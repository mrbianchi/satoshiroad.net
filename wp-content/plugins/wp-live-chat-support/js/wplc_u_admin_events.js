
var wplc_upsell_events_array = [];
var wplc_new_chat_ringer_dismissed = false;

/**
 * This helps us keep track of which agent has left or joined a chat so we do not show the notification more than once.
 * @type {Array}
 */
var wplc_agent_chat_statuses = [];

/**
 * Added this to track the whether connection is open or closed
 * Used in the disconnect notice to determine if a connection has truly been lost
*/
var wplc_node_socket_connected = false;

jQuery("#toolbar-item-open-bleeper").hide();


jQuery(function(){
  
  if ( typeof bleeper_in_dashboard !== "undefined" && bleeper_in_dashboard === "0") {
    jQuery(".nifty_top_wrapper").hide();
    jQuery(".nifty_top_wrapper").addClass("bleeper-out-dash");
  }

  node_uri = WPLC_SOCKET_URI;
  if (window.console) { console.log("[WPLC] Connecting to "+node_uri); }
  jQuery.event.trigger({type: "bleeper_dom_ready"});        
  
  if ( typeof bleeper_in_dashboard !== "undefined" && bleeper_in_dashboard === "0") {

    jQuery(".nifty_top_wrapper").hide();
    jQuery(".nifty_top_wrapper").addClass("bleeper-out-dash");
    jQuery("#toolbar-item-open-bleeper").show();

  }
});

var agent_dash_open = false;
jQuery(document).on("click", "#toolbar-item-open-bleeper", function() {
  if (agent_dash_open) {
    agent_dash_open = false;
    jQuery(this).css("background-image","url("+wplc_url+"/images/48px.png)");
    jQuery(this).css("background-color","#333");
    jQuery(".bleeper-out-dash").fadeOut("1000");
  } else {
    agent_dash_open = true;
    jQuery(this).css("background-image","url("+wplc_url+"/images/iconCloseRetina.png)");
    jQuery(this).css("background-color","#333");
    jQuery(".bleeper-out-dash").fadeIn("1000");
  }
});

jQuery(document).on("bleeper_connect", function(e) {
  if(typeof wplc_disabled !== "undefined" && wplc_disabled === "true"){
    var wplc_disabled_content = (typeof wplc_disabled_html !== "undefined" ? wplc_disabled_html : "Chat has been disabled in the settings area.");
    jQuery("#nifty_bg_holder_text_inner").html(wplc_disabled_content);
    jQuery("#nifty_bg_holder_text_inner").css("font-size", "19px");
  } else {
    if (typeof wplc_choose_accept_chats !== "undefined" && wplc_choose_accept_chats === "0") {
      jQuery(".nifty_bg_holder_text").html("");
      jQuery(".nifty_bg_holder").fadeOut();
      jQuery(".nifty_bg_holder_text").html("You are not accepting chats");
      jQuery(".nifty_bg_holder").fadeIn();  
      jQuery(".nifty_bg_holder").css('left','25%');  

    } else {
      socket = io.connect(node_uri, { secure:true, query : e.ndata.query_string, transports: ['websocket'] } );
      wplc_node_socket_connected = true;
      bleeper_delegates();
      bleeper_update_agent_unread();

    }
  }
});

//Chat Notification
jQuery(document).on("bleeper_user_chat_notification", function(e){
  if(typeof e.ndata !== "undefined"){
    if(typeof e.ndata.chatid !== "undefined" && typeof e.ndata.notification_text !== "undefined"){
      if(e.ndata.chatid === active_chatid){
        the_message = {};
        the_message.msg = (typeof wplc_user_chat_notification_prefix !== "undefined" ? "<strong><em>" + wplc_user_chat_notification_prefix + "</strong></em><br>" : "") + e.ndata.notification_text;
        the_message.originates = 0;
        the_message.other = {};
        wplc_push_message_to_chatbox(the_message, agent_id, function(){
        });
      }
    }
  }
}); 

//jQuery(function(){
jQuery(document).on("bleeper_dom_ready", function(e) {


    if (typeof wplc_upsell_a2a !== "undefined" && wplc_upsell_a2a === '1') {
      jQuery("#agent_list").hide();
      jQuery(".userListBox_Wrapper").css('bottom','0px');
    }

    jQuery("body").on("click", ".wplc_a2a_dismiss", function() {
        jQuery('#agent_list').hide();
        jQuery(".userListBox_Wrapper").css('bottom','0px');

        var data = {
            action: 'wplc_a2a_dismiss',
            security: wplc_nonce
        };
    
        jQuery.post(ajaxurl, data, function(response) {

            
        });
    });



    if (jQuery(".online_agent_list").length && typeof wplc_is_pro === "undefined") {

      jQuery('<span/>', {
        'class' : 'wplc_a2a_dismiss',
        html: 'Dismiss'

      }).appendTo("#agent_list");

      jQuery('<li/>', {
        'class': 'upsell',
        'id': '',
        'aid': '',
        html: agent_to_agent_chat_upsell
      }).appendTo(".online_agent_list");  


      jQuery('<div/>', {
        'class': 'initiate_upsell',
        html: initiate_chat_upsell
      }).appendTo(".infoArea"); 
    }

    jQuery("#page-wrapper").css("height", (jQuery(window).height() - 100) + "px");

    jQuery(window).on("resize", function(){
        jQuery("#page-wrapper").css("height", (jQuery(window).height() - 100) + "px");
    });

    jQuery("#toolbar-item-fullscreen-wp").click(function(){
        jQuery("#collapse-button").click();
    });

    jQuery(document).on("bleeper_new_chat", function(e) {
        wplc_new_chat_notifications();
        wplc_desktop_notification();
        wplc_new_chat_ringer_dismissed = false; //Ensure it is reset here
        wplc_repeat_new_chat_notification(1);
    });

    jQuery(document).on("bleeper_new_message_for_cid", function(e) {
      if(typeof e.cid !== "undefined"){
          jQuery("#"+e.cid).removeClass('inactive');
      }
    });

    jQuery(document).on("bleeper_send_message", function(e) {
        var the_message = {}
        the_message.originates = 1;
        the_message.msg = typeof e.message !== "undefined" ? e.message : false;
        the_message.other = {};
        
        var wplc_d = new Date();
        the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
        the_message.other.agent_id = agent_id;

        if(the_message.msg !== false){
            if (typeof message_type === 'undefined' || message_type === 'u') {
              /* this message is going to a user */
              var api_endpoint = 'send_message';
              var ato = '';
            } else {
              /* this message is going to another agent */
              var api_endpoint = 'send_agent_message';
              var ato = message_type;
            }
            var data = {
                    relay_action: 'wplc_admin_send_msg',
                    agent_id: agent_id,
                    security: wplc_nonce,
                    chat_id: wplc_cid,
                    message: the_message.msg,
                    ato: ato,
                    msg_id: e.msg_id

            };
            wplc_rest_api(api_endpoint, data, 12000, null);   
        }
    });

    jQuery(document).on("bleeper_add_message_chatbox", function(e) {


        if(typeof e.message !== "object"){
            var the_message = {}
            the_message.originates = 1; //Assume from admin
            the_message.msg = typeof e.message !== "undefined" ? e.message : false;
            the_message.mid = typeof e.msgID !== "undefined" ? e.msgID : undefined;
            the_message.other = {};
            
            var wplc_d = new Date();
            the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
            the_message.other.agent_id = agent_id;

            if(the_message.msg !== false){
                wplc_push_message_to_chatbox(the_message, agent_id, function(){

                });
            }
        } else {
            //message is already an object
            var the_message = e.message;
            the_message.mid = typeof e.message.msgID !== "undefined" ? e.message.msgID : undefined;
            the_message.originates = the_message.aoru === "u" ? 2 : 1; //Assume from admin

            if(typeof the_message.msg === "undefined"){
                the_message.msg = typeof the_message.message !== "undefined" ? the_message.message : false;
            }
            if (the_message.aoru !== 'u') {
              if (typeof the_message.other !== 'object') { the_message.other = {}; }
              the_message.other.aid = parseInt(the_message.aoru);
            }

            if (the_message.aoru === 'u') {
              the_message.other = {};
              var wplc_d = new Date();
              the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
            }

            if(the_message.msg !== false){
                wplc_push_message_to_chatbox(the_message, agent_id, function(){

                });
            }
        }

        jQuery.event.trigger({type: "bleeper_scroll_bottom"});
    });



    /**
     * Triggered when an agent clicks the "accept chat" button
     *
     * We need to send the AID to a WP REST API endpoint so that we can save the AID in the table
     * 
     * @return void
     */
    jQuery(document).on("bleeper_agent_accepted_chat", function(e){
        var data = {
            relay_action: 'wplc_admin_send_msg',
            agent_id: e.ndata.agent_id,
            token: wplc_restapi_token,
            chat_id: e.ndata.chat_id
        
        };
        wplc_rest_api('accept_chat', data, 12000, null);  

    });

    jQuery(document).on("bleeper_open_chat", function(e) {
        var new_chat_id = typeof e.cid !== "undefined" ? e.cid : false;
        var new_username = typeof e.username !== "undefined" ? e.username : false;
        if(new_username !== false){
            wplc_chat_name = new_username;
        }

        if(new_chat_id !== false){
            wplc_cid = new_chat_id;
            jQuery.event.trigger({type: "bleeper_get_history"});
        }

        if (typeof wplc_is_pro === 'undefined' && typeof wplc_upsell_events_array[new_chat_id] === 'undefined') {
          wplc_upsell_events_array[new_chat_id] = true;
          bleeper_save_event(new_chat_id, 'fa-lightbulb-o', wplc_event_upsell);
        }

        wplc_new_chat_ringer_dismissed = true; //Dismiss it now regardless

 
    });

    jQuery(document).on("bleeper_get_history", function(e) {
        

        /**
        * Pull from sessionStorage if we can
        */
        /*
        if (bleeper_test_sessionStorage()) {

          if (sessionStorage.getItem(wplc_cid+"_m") !== null) {
            var msg_obj = JSON.parse(sessionStorage.getItem(wplc_cid+"_m"));
            for (var key in msg_obj) {
              var the_message = msg_obj[key];
              the_message.mid = key;

              if(typeof the_message.afrom !== "undefined"){
                if(parseInt(the_message.afrom) !== parseInt(agent_id)){
                  //From a different agent
                  if(typeof the_message.other === "undefined"){ the_message.other = {}; }
                  the_message.other.from_an_agent = true;
                }
              }
              
              wplc_push_message_to_chatbox(the_message, agent_id, function() {
                  jQuery.event.trigger({type: "bleeper_scroll_bottom"});
              });
            }
  

          }
        }
        */
        bleeper_sort_and_send_messages(wplc_cid);

        var data = {
            relay_action: 'wplc_get_messages',
            security: wplc_nonce,
            chat_id: wplc_cid, 
            limit:50,
            offset:0,
            received_via: agent_id
        };

        wplc_rest_api('get_messages', data, 12000, function(message_history) {
            if (typeof message_history !== "undefined" && message_history && typeof message_history.data !== "undefined" && typeof message_history.data.messages !== "undefined" && typeof message_history.data.messages === "object") {
                message_history = message_history.data.messages;
                for (var key in message_history) {
                    var wplc_push_to_chatbox = true;
                    
                    /* first check if we have this in sessionStorage */
                    if (bleeper_test_sessionStorage()) {
                      if (sessionStorage.getItem(wplc_cid+"_m") !== null) {
                          var msg_obj = JSON.parse(sessionStorage.getItem(wplc_cid+"_m"));
                          if (typeof msg_obj[key] !== "undefined") {
                            //Check if message has changed - if so, live update the element
                            if(typeof msg_obj[key].msg !== "undefined" && typeof message_history[key].msg !== "undefined"){
                              if(msg_obj[key].msg !== message_history[key].msg){
                                //Message has changed, most likely an edit
                                wplc_update_message_element_by_mid(key, message_history[key].msg, "messageBody");
                              }
                            }

                            /* we've already got this message but lets update it anyway incase it was edited along the way and we never caught the edit */
                            msg_obj[key] = message_history[key];
                            wplc_push_to_chatbox = false;

                          } else {
                            msg_obj[key] = message_history[key];
                          }
                          sessionStorage.setItem(wplc_cid+"_m", JSON.stringify(msg_obj));
                      } else {
                        var msg_obj = {};
                        msg_obj[key] = message_history[key];
                        sessionStorage.setItem(wplc_cid+"_m", JSON.stringify(msg_obj));
                      }

                    } else {
                      /* we arent using sessionStorage or it is disabled so push message to window */
                      wplc_push_to_chatbox = true; 
                    }

                    /*
                    if (wplc_push_to_chatbox) {
                      var the_message = message_history[key];
                      the_message.mid = key;

                      
                      if(typeof the_message.afrom !== "undefined"){
                        if(parseInt(the_message.afrom) !== parseInt(agent_id)){
                          //From a different agent
                          if(typeof the_message.other === "undefined"){ the_message.other = {}; }
                          the_message.other.from_an_agent = true;
                        }
                      }

                      wplc_push_message_to_chatbox(the_message, agent_id, function() {
                          jQuery.event.trigger({type: "bleeper_scroll_bottom"});
                      });
                    }*/
                }

                bleeper_sort_and_send_messages(wplc_cid);
                

                
            }

            jQuery.event.trigger({type: "bleeper_messages_added", ndata:{cid:wplc_cid}});
        });
    });

    jQuery(document).on("bleeper_scroll_bottom", function(e) {
        jQuery("#messages").scrollTop(jQuery("#messages")[0].scrollHeight);
    });

    jQuery(document).on("bleeper_add_initiate_button_no_col", function(e) {
      
        if(typeof wplc_integration_pro_active === "undefined"){
            var tmp_chatid = typeof e.cid !== "undefined" ? e.cid : false;
        
            if(tmp_chatid !== false){
                jQuery('<div/>', {
                  'class': '',
                  html: "<a href='javascript:void(0);' id='wplc_initiate_chat' class='wplc_initiate_chat btn btn-info pull-right' cid='"+ tmp_chatid +"'>Initiate Chat</a>"
                }).appendTo('#vis'+e.cid + ' .visActionCol');



            }
        }
       
    });

    jQuery(document).on("bleeper_remove_initiate_button", function(e) {
        if(typeof wplc_integration_pro_active === "undefined"){
            var tmp_chatid = typeof e.cid !== "undefined" ? e.cid : false;
            if(tmp_chatid !== false){
                jQuery('#vis'+ tmp_chatid + " #wplc_initiate_chat").remove();  
            }
        }
       
    });

    if(typeof wplc_tip_header !== "undefined" && typeof wplc_tip_array !== "undefined"){
        
        jQuery(".bleeper_tips_hints").fadeOut(500, function(){
            var tip_content = wplc_tip_header + "<br><br>" + wplc_tip_array[0];
            jQuery(".bleeper_tips_hints").html(tip_content).fadeIn();
        });
    
        var wplc_tip_index = 1;
        setInterval(function(){ 
            jQuery(".bleeper_tips_hints").fadeOut(500, function(){
                var tip_content = wplc_tip_header + "<hr>" + wplc_tip_array[wplc_tip_index];
                jQuery(".bleeper_tips_hints").html(tip_content).fadeIn();
            });
            

            if(wplc_tip_index + 1 >= wplc_tip_array.length){
                wplc_tip_index = 0;
            } else {
                wplc_tip_index ++;
            }
        }, 30000);
    }

    /**
     * Handles the "add agent" event.
     *
     * Takes over the 'add agent' event and adds our own data to it. In this case we add the agent's data so that it can be displayed in the header section of the live chat box on the front end
     *
     * @return void
     */
    jQuery(document).on("bleeper_add_agent", function(e) {

        var custom_header_data = wplc_head_data;
        if  (typeof bleeper_agent_name !== 'undefined'){
            custom_header_data.name = bleeper_agent_name;
        }

        socket.emit('add agent', e.ndata);
        socket.emit('custom data',{action:'send_custom_header',chatid:e.ndata.chatid,agentid:e.ndata.agent,ndata:custom_header_data});


    });

    /**
     * Handles the "agent left" event.
     *
     * @return void
     */
    jQuery(document).on("bleeper_agent_left", function(e) {
      wplc_agent_chat_statuses[active_chatid] = 0;
      if (active_chatid === e.ndata.chatid) {
        the_message = wplc_generate_system_notification_object(e.ndata.agent_name+" left.", {}, 0);
        wplc_push_message_to_chatbox(the_message,'a', function() {
            //jQuery.event.trigger({type: "bleeper_scroll_bottom"});
        });        
      }
    });

    /**
     * Handles the "agent joined" event.
     *
     * @return void
     */
    jQuery(document).on("bleeper_agent_joined", function(e) {
      if (active_chatid === e.ndata.chatid) {
        if (typeof wplc_agent_chat_statuses[active_chatid] === "undefined" || wplc_agent_chat_statuses[active_chatid] === 0) {
          
          /* set this to 1 so we know this agent has joined the chat already */
          wplc_agent_chat_statuses[active_chatid] = 1;


          the_message = wplc_generate_system_notification_object(e.ndata.agent_name+" joined.", {}, 0);
          wplc_push_message_to_chatbox(the_message,'a', function() {
              //jQuery.event.trigger({type: "bleeper_scroll_bottom"});
          });
        }        
      }
    });

    /**
     * Add the agent to the chat list items
     * 
     * @return void
     */
     jQuery(document).on('mouseenter', '.agent_involved', function() {
      jQuery(this).addClass('agent_selected');
     });
     jQuery(document).on('mouseleave', '.agent_involved', function() {
      jQuery(this).removeClass('agent_selected');
     });


    jQuery(document).on("bleeper_add_agent_involved", function(e) {
        if(typeof wplc_agent_data !== "undefined"){
            
            
            if(typeof wplc_agent_data[e.agentid] !== "undefined" && typeof wplc_agent_data[e.agentid].md5 !== "undefined" && typeof wplc_agent_data[e.agentid].name !== "undefined"){
                
                var num_age = jQuery(".agent_involved_"+e.chatid).length;
                if (num_age > 2) { 
                  var marg_right = num_age * -1.5; 
                  jQuery(".agent_involved_"+e.chatid).addClass('morethan2');
                } 
                else { 
                  var marg_right = 2; 
                  jQuery(".agent_involved_"+e.chatid).removeClass('morethan2');
                }
                if (marg_right < -12) { marg_right = 12; }

                agent_involved = wplc_agent_data[e.agentid];
                agent_involved_email = agent_involved.md5;
                agent_involved_name = agent_involved.name;
                jQuery(".agent_involved_"+e.chatid).css('margin-right',marg_right+'px');

                if ( ! jQuery( '#agent_grav_'+e.chatid+'_'+e.agentid ).length ) {
                    jQuery('<img/>', {
                      'style': 'max-width:inherit; margin-right:'+marg_right+'px',
                      'id' : 'agent_grav_'+e.chatid+'_'+e.agentid,
                      'title' : agent_involved_name,
                      'alt' : agent_involved_name,
                      'src' : 'https://www.gravatar.com/avatar/'+agent_involved_email+'?s=32&d=mm',
                      'class' : "img-thumbnail img-circle thumb16 agent_involved agent_involved_"+e.chatid
                    }).appendTo('#'+e.chatid+ " .agents_involved");
                }
            }
        }
    });    
    /**
     * Add the involved agent's gravatar to the visitor list, under the CHAT STATUS column
     */
    jQuery(document).on("bleeper_add_agent_involved_visitor_list", function(e) {
      
        if(typeof wplc_agent_data !== "undefined"){
            
            
            if(typeof wplc_agent_data[e.agentid] !== "undefined" && typeof wplc_agent_data[e.agentid].md5 !== "undefined" && typeof wplc_agent_data[e.agentid].name !== "undefined"){
                
                var num_age = jQuery(".agent_involved_"+e.chatid).length;
                if (num_age > 2) { 
                  var marg_right = num_age * -1.5;
					jQuery(".agent_involved_"+e.chatid).addClass('morethan2');
                } 
                else { 
                  var marg_right = 2;
					jQuery(".agent_involved_"+e.chatid).removeClass('morethan2');
                }
                if (marg_right < -12) { marg_right = 12; }

                agent_involved = wplc_agent_data[e.agentid];
                agent_involved_email = agent_involved.md5;
                agent_involved_name = agent_involved.name;
				jQuery(".agent_involved_"+e.chatid).css('margin-right',marg_right+'px');

                if ( ! jQuery( '#agent_grav_visitor_'+e.chatid+'_'+e.agentid ).length ) {
					jQuery('<img/>', {
                      'style': 'max-width:inherit; margin-right:'+marg_right+'px',
                      'id' : 'agent_grav_visitor_'+e.chatid+'_'+e.agentid,
                      'title' : agent_involved_name,
                      'alt' : agent_involved_name,
                      'src' : 'https://www.gravatar.com/avatar/'+agent_involved_email+'?s=32&d=mm',
                      'class' : "img-thumbnail img-circle thumb16 agent_involved agent_involved_"+e.chatid
                    }).appendTo('#vis'+e.chatid+ " .agents_involved_visitor");
                }
            }
        }
    }); 
    jQuery(document).on('bleeper_disconnected', function(e) {
        if (connection_lost_type == '' || typeof connection_lost_type === "undefined") {
          wplc_node_socket_connected = false;
          setTimeout(function(){
            if(wplc_node_socket_connected === false){
              jQuery(".nifty_bg_holder_text").html("Connection to the server lost.. please be patient while we try restore the connection...");
              jQuery(".nifty_bg_holder").fadeIn();    
              jQuery(".nifty_bg_holder").css('left','0');  
            }
          }, 2000);
        } else if (connection_lost_type === 'offline_status') {
          jQuery(".nifty_bg_holder_text").html("You are not accepting chats");
          jQuery(".nifty_bg_holder").fadeIn();  
          jQuery(".nifty_bg_holder").css('left','25%');  
        }
    });


    /**
     * User has toggled their status
     *
     * This will either disconnect the socket connection or re-establish it.
     * 
     * @return void
     */
    jQuery(document).on("wplc_switchery_changed", function(e) {
        if (typeof e.ndata !== "undefined" && typeof e.ndata.action !== "undefined" && e.ndata.action === "wplc_choose_not_accepting") {
            /* drop the connection */
            if (socket.connected) {
                socket.disconnect();
            }
        } else if (e.ndata.action === "wplc_choose_accepting") {
            if (typeof socket === "undefined" || socket.connected === false) {
                socket = io.connect(node_uri, { secure:true, query : query_string, transports: ['websocket'] } );
                bleeper_delegates();
                jQuery(".nifty_bg_holder").fadeOut();  
                
            }
        }
    })


    /**
     * Handles the "get chat history" event.
     *
     * Attaching an object called "other" to the parent object will allow this object to traverse through all socket events from this point
     *
     * Example:
     *     e.ndata.other = {};
     *     e.ndata.other.my_unique_variable = 'This is extra data'
     * 
     * @return void
     */
    jQuery(document).on("bleeper_send_get_chat_history", function(e) {

        
        socket.emit('get chat history',e.ndata);

        wplc_head_data.aid = agent_id;
        wplc_head_data.name = wplc_agent_data[agent_id].name;
        wplc_head_data.email = wplc_agent_data[agent_id].md5;

        //Removing this as we should not be sending the custom header unless the agent is joining the chat
        //socket.emit('custom data',{action:'send_custom_header',chatid:e.ndata.chatid,agentid:e.ndata.agentid,ndata:wplc_head_data});
    });


    /**
     * We have received our socketID and instanceID
     * 
     * @return void
     */
    jQuery(document).on("bleeper_socketid_received", function(e) {
        jQuery(".online_agent_"+agent_id).attr("socket",e.ndata.socketid);
        jQuery(".bleeper_instance").html(" <em>[Instance: "+e.ndata.instanceid+"]</em> ");



        
        
    });

    /**
     * Chat was ended by another agent, lets handle the events.
     *
     * @return void
     */
    jQuery(document).on("bleeper_chat_ended_by_other_agent", function(e) {
      if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {

        the_message = wplc_generate_system_notification_object(e.ndata.agent_name+" ended the chat.", {}, 0);
        
        bleeper_add_message_to_sessionStorage(e.ndata.chatid, the_message);

        if (e.ndata.chatid === active_chatid) {
          wplc_push_message_to_chatbox(the_message,'a', function() {
              jQuery.event.trigger({type: "bleeper_scroll_bottom"});
          });
        }

      }
    })


    /**
     * What to do when a visitor has left or a chat has disconnected
     * 
     * @return void
     */
    jQuery(document).on("bleeper_remove_visitor", function(e) {
      if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {

        if (typeof visitor_list[e.ndata.chatid] !== "undefined" && visitor_list[e.ndata.chatid].state === 'active') {
          the_message = wplc_generate_system_notification_object(e.ndata.username+" may have left (disconnected).", {}, 0);
          
          bleeper_add_message_to_sessionStorage(e.ndata.chatid, the_message);

          if (e.ndata.chatid === active_chatid) {
            wplc_push_message_to_chatbox(the_message,'a', function() {
                jQuery.event.trigger({type: "bleeper_scroll_bottom"});
            });
          }
        }
        jQuery("#"+e.ndata.chatid).removeClass('active');
        jQuery("#"+e.ndata.chatid).addClass('inactive');
        jQuery(".bleeper_close_item_"+e.ndata.chatid).show();
        jQuery("#vis"+e.ndata.chatid).remove();
      }

    });


    /**
     * New visitor
     * 
     * @return void
     */
    jQuery(document).on("bleeper_new_visitor", function(e) {
      if (typeof bleeper_enable_visitor_sound !== "undefined" && bleeper_enable_visitor_sound === '1') {
        bleeper_ping.play();
      }

    });


    /**
     * Custom data trigger
     *
     * If custom data is sent through the socket, this is where you would want to handle it
     *
     * @return void
     */
    /*
    jQuery(document).on("bleeper_custom_data_received", function(e) {
        if (typeof e.ndata !== "undefined") {

            if (e.ndata.action === "send_user_click_data") {
              if (typeof e.ndata.ndata.elem_class !== "undefined") { var elem_class = e.ndata.ndata.elem_class; } else { var elem_class = '-'; }
              if (typeof e.ndata.ndata.elem_id !== "undefined") { var elem_id = e.ndata.ndata.elem_id; } else { var elem_id = '-'; }
                the_message = wplc_generate_system_notification_object("User clicked on an element (Class: "+elem_class+", ID: "+elem_id+")", {}, 0);
                wplc_push_message_to_chatbox(the_message,'a', function() {
                    jQuery.event.trigger({type: "bleeper_scroll_bottom"});
                });

            }

        }
    });
*/


    /**
     * bleeper_online_agent_packet_received
     *
     * A packet of information has been received from the socket that contains data about an online agent. 
     *
     * This is run iteratively from 'the information received' socket event
     * 
     * @return void
     */
    jQuery(document).on("bleeper_online_agent_packet_received", function(e) {
        if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {
            t_ag_sock = e.ndata.from_socket;
            t_ag_name = e.ndata.username;
            t_ag_id = e.ndata.agent_id;

            t_ag_cid = nifty_api_key+''+Math.min(parseInt(agent_id), parseInt(t_ag_id))+''+Math.max(parseInt(agent_id), parseInt(t_ag_id));
            t_ag_cid = md5(t_ag_cid);



			jQuery(".online_agent_"+t_ag_id+" .online_offline").removeClass('offline');
			jQuery(".online_agent_"+t_ag_id+" .online_offline").addClass('online');
			jQuery(".online_agent_"+t_ag_id+"").attr('socket',t_ag_sock);
			jQuery(".online_agent_"+t_ag_id+"").attr('id',t_ag_cid);

            /**
             * Add each agent as an 'involved agent' to the chat ID
             */
            //socket.emit('chat accepted',{chatid: t_ag_cid,agent_id:t_ag_id,agent_name:wplc_agent_data[t_ag_id].name});
        }
    });    
    
});

/**
 * Scrolls the chat box to the bottom
 * 
 */
var wplc_scroll_to_bottom = function() {
    var height = jQuery('#messages')[0].scrollHeight;
    jQuery('#messages').scrollTop(height);
}

/**
 * Recursive Ringer
*/
function wplc_repeat_new_chat_notification(count){
 var limit = 5; //Default
 if(typeof bleeper_ringer_count !== "undefined"){
   limit = parseInt(bleeper_ringer_count);
 }

 count ++;
  if(count <= limit){
    if(wplc_new_chat_ringer_dismissed !== true){
      setTimeout(function(){
        if(wplc_new_chat_ringer_dismissed !== true){
          wplc_new_chat_notifications();
        }
        wplc_repeat_new_chat_notification(count);
      }, 3000);
    }
  } else {
    wplc_new_chat_ringer_dismissed = true; //Set it to dismissed now
  }
}

/** 
 * New Chat Notification Controller
*/
function wplc_new_chat_notifications(){
  bleeper_ring.play();
  bleeper_change_favico(bleeper_favico_noti);
}

/**
 * Desktop notification
*/
function wplc_desktop_notification() {
    if (typeof Notification !== 'undefined') {
        if (!Notification) {
            return;
        }
        if (Notification.permission !== "granted")
            Notification.requestPermission();

        var wplc_desktop_notification = new Notification(bleeper_new_chat_notification_title, {
            icon: bleeper_new_chat_notification_icon,
            body: bleeper_new_chat_notification_text
        });
    }
}