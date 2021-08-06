
/**
 * Create an object so that we can track if we've displayed 'agent has joined' notices
 * @type {Object}
 */
var agent_joined = {};

jQuery(function() {
    jQuery(document).on("wplc_open_chat", function(e) {
        jQuery("#bleeper_chat_ended").html('').hide();
        nc_status = Cookies.get("nc_status");
        if (typeof nc_status === "undefined" || nc_status === "browsing") {
            /* show the part that requests name, email and department, etc */
            jQuery("#wp-live-chat-2").show();    
            jQuery("#wp-live-chat-2-inner").show();         
            var wplc_visitor_name = Cookies.get('wplc_name');           
            if(Cookies.get('wplc_email') !== "no email set" && typeof wplc_visitor_name !== "undefined"){
                jQuery("#wplc_name").val(Cookies.get('wplc_name'));
                jQuery("#wplc_email").val(Cookies.get('wplc_email'));
            }
            jQuery("#wp-live-chat-header").addClass("active");
        } else if (nc_status === "active") {
            /* go straight into chat */
            Cookies.set('wplc_minimize', "", { expires: 1, path: '/' });
            jQuery.event.trigger({type: "wplc_open_chat_2", wplc_online: wplc_online});
            

        }
        
    });

    //Chat Notification
    jQuery(document).on("bleeper_user_chat_notification", function(e){
      if(typeof e.ndata !== "undefined"){
        if(typeof e.ndata.chatid !== "undefined" && typeof e.ndata.notification_text !== "undefined"){
          if(e.ndata.chatid === wplc_cid){
            the_message = {};
            the_message.msg = e.ndata.notification_text;
            the_message.originates = 0;
            the_message.other = {};
            wplc_push_message_to_chatbox(the_message, "u", function(){
            });
          }
        }
      }
    });

    jQuery(document).on("bleeper_socket_connected", function(e) {
        if (e.status === "active") {
            jQuery.event.trigger({type: "wplc_open_chat_2", wplc_online: wplc_online});      
        }
        wplc_clear_system_notification();
    });

    /**
     * Handle the disconnect notice
     * 
     * @return void
     */
    jQuery(document).on("bleeper_disconnected", function() {
        console.log('disconnected');
        if(typeof wplc_error_messages !== "undefined" && typeof wplc_error_messages['disconnected_message'] !== "undefined"){
            var the_message = wplc_generate_system_notification_object(wplc_error_messages['disconnected_message'], {}, 0);
            wplc_display_system_notification(the_message);
        }
    });

    jQuery(document).on("nifty_trigger_open_chat_2", function( e ) { 
      
      


    });    

    /**
     * Get chat messages from WP storage once the chat box init has been completed.
     * 
     */
    jQuery(document).on("wplc_init_complete", function(e) {
        if (wplc_online && Cookies.get('bleeper_received_msg') === "true") {
            var data = {
                relay_action: 'wplc_get_messages',
                security: wplc_nonce,
                chat_id: wplc_cid,
                limit:50,
                offset:0,
                received_via: 'u',
                wplc_extra_data:wplc_extra_data
            };
            wplc_rest_api('get_messages', data, 12000, function(message_history) {
                if (typeof message_history !== "undefined" && message_history && typeof message_history.data !== "undefined" && typeof message_history.data.messages !== "undefined" && typeof message_history.data.messages === "object") {
                    message_history = message_history.data.messages;
                    for (var key in message_history) {
                        var the_message = message_history[key];
                        the_message.mid = key;
                        wplc_push_message_to_chatbox(the_message,'u', function() {
                            wplc_scroll_to_bottom();    
                        });
                    }
                }
            });
        }

        var wplc_node_status_from_cookie = Cookies.get("nc_status");
        if(typeof wplc_node_status_from_cookie !== "undefined" &&  wplc_node_status_from_cookie === "active"){
            if(typeof wplc_cbox_animation !== "undefined"){
                wplc_cbox_animation();
            }
        }

    });

    /**
     * New message received from socket, add it to the DOM
     * @return void
     */
    jQuery(document).on("bleeper_new_message", function(e) {
        
        /**
         * set the bleeper_received_msg = true so that we can know when to /get_messages in the future 
         */
        Cookies.set('bleeper_received_msg', true, { expires: 1, path: '/' });

        if (typeof e.ndata === "object") {
            if (typeof e.ndata.message === "undefined") { e.ndata.message = ''; }
            
            the_message = {};
            the_message.other = {};
            
            if (e.ndata.aoru === 'u') {
                the_message.originates = 2;
            } else {
                the_message.originates = 1;
                the_message.other.aid = e.ndata.aoru;

            }
            
            the_message.msg = wplc_strip(e.ndata.message);
            the_message.mid = e.ndata.msgID;
            
            var wplc_d = new Date();
            the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
            wplc_push_message_to_chatbox(the_message,'u', function() {
                wplc_scroll_to_bottom();    
            });

            if (Cookies.get("wplc_minimize") === 'yes' && jQuery("#bleeper_bell").length > 0) {
                jQuery("#bleeper_bell").show();
            }
        }
    });



    jQuery(document).on("bleeper_send_message", function(e) {
        var wplc_chat = wplc_strip(e.message);

        /*
        the_message = {};
        the_message.originates = 2;
        the_message.msg = wplc_chat;
        the_message.other = {};
        var wplc_d = new Date();
        the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
        wplc_push_message_to_chatbox(the_message,'u', function() {
            wplc_scroll_to_bottom();    
        });
        */
         var data = {
                relay_action: 'wplc_user_send_msg',
                security: wplc_nonce,
                chat_id: wplc_cid,
                message: wplc_chat,
                msg_id: e.msg_id,
                wplc_extra_data:wplc_extra_data
        };

        if(typeof wplc_chat === "string" && wplc_chat.trim() !== ""){
            /* 
             * Will only send message if this is not empty string
             * This will resolve some issues with rest storage
            */
            wplc_rest_api('send_message', data, 12000, null);
            jQuery("#wplc_chatmsg").val('');

            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'User Send Message'
                    });
                }
            }
        }

        

        jQuery.event.trigger({type: "wplc_update_gdpr_last_chat_id"});

    });


    /**
     * Handle the chat history object returned from the socket
     * 
     * @return void
     */
    jQuery(document).on("bleeper_chat_history", function(e) {
        if (typeof e.ndata !== "undefined" && e.ndata !== null) {
            
        }
    });

    /**
     * Handle the agent joined event
     * 
     * @return void
     */
    jQuery(document).on("bleeper_agent_joined", function(e) {
        if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {
            if (typeof agent_joined[parseInt(e.ndata.agent)] === "undefined") {
                the_message = wplc_generate_system_notification_object(e.ndata.agent_name+bleeper_localized_strings[0], {}, 0);
                wplc_push_message_to_chatbox(the_message,'u', function() {
                    wplc_scroll_to_bottom();    
                });
                agent_joined[parseInt(e.ndata.agent)] = true;
            }

        }
    });

    /**
     * Handle the agent left event
     * 
     * @return void
     */
    jQuery(document).on("bleeper_agent_left", function(e) {
        if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {
            the_message = wplc_generate_system_notification_object(e.ndata.agent_name+bleeper_localized_strings[1], {}, 0);
            if (typeof agent_joined[parseInt(e.ndata.agent)] !== "undefined" && agent_joined[parseInt(e.ndata.agent)] === true) {
                agent_joined[parseInt(e.ndata.agent)] = undefined;
                wplc_push_message_to_chatbox(the_message,'u', function() {
                    wplc_scroll_to_bottom();    
                });
            }

            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'Agent left the chat'
                    });
                }
            }

        }
    });


    jQuery(document).on("bleeper_chat_ended_notification", function(e) {
        if (typeof e.ndata !== "undefined" && typeof e.ndata === "object") {
            the_message = wplc_generate_system_notification_object(e.ndata.agent_name+bleeper_localized_strings[2], {}, 0);
            wplc_push_message_to_chatbox(the_message,'u', function() {
                wplc_scroll_to_bottom();    
            });
        }

        if(typeof wplc_redirect_thank_you !== "undefined" && wplc_redirect_thank_you !== null && wplc_redirect_thank_you !== ""){
            window.location = wplc_redirect_thank_you;
        }

        if(jQuery('#wplc_gdpr_end_chat_notice_container').length > 0){
            jQuery("#wplc_gdpr_end_chat_notice_container").fadeIn('fast');
        }

        if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
            if (typeof ga !== "undefined") {
                ga('send', {
                  hitType: 'event',
                  eventCategory: 'WP_Live_Chat_Support',
                  eventAction: 'Event',
                  eventLabel: 'Chat Ended By Agent'
                });
            }
        }
    });

    /**
     * Handle the agent disconnected event
     * 
     * @return void
     */
    jQuery(document).on("bleeper_agent_disconnected", function(e) {
        the_message = wplc_generate_system_notification_object(e.ndata.agent_name+bleeper_localized_strings[3], {}, 0);
            if (typeof agent_joined[parseInt(e.ndata.aid)] !== "undefined" && agent_joined[parseInt(e.ndata.aid)] === true) {
                agent_joined[parseInt(e.ndata.aid)] = undefined;
                wplc_push_message_to_chatbox(the_message,'u', function() {
                    wplc_scroll_to_bottom();    
                });
            }
        
    });

    /**
     * Custom data trigger
     *
     * If custom data is sent through the socket, this is where you would want to handle it
     *
     * @return void
     */
    jQuery(document).on("bleeper_custom_data_received", function(e) {
        if (typeof e.ndata !== "undefined") {
            if (typeof e.ndata.action !== "undefined") {
                if (e.ndata.action === "send_custom_header") {

                    var temail = '';
                    var tname = '';
                    var taid = '';
                    var ta_tagline = '';
                    var ta_bio = '';
                    var ta_social_links = '';
                    if (typeof e.ndata.ndata.email !== "undefined") { temail = e.ndata.ndata.email; }
                    if (typeof e.ndata.ndata.name !== "undefined") { tname = e.ndata.ndata.name; }
                    if (typeof e.ndata.ndata.aid !== "undefined") { taid = e.ndata.ndata.aid; }
                    if (typeof e.ndata.ndata.tagline !== "undefined") { ta_tagline = e.ndata.ndata.tagline; }
                    if (typeof e.ndata.ndata.bio !== "undefined") { ta_bio = e.ndata.ndata.bio; }
                    if (typeof e.ndata.ndata.social !== "undefined") { ta_social_links = e.ndata.ndata.social; }



                    var data = {
                        social_links: ta_social_links,
                        agent_bio: ta_bio,
                        agent_tagline: ta_tagline,
                        aid: taid,
                        email: temail,
                        name: tname
                    }
                    

                    jQuery.event.trigger({type: "wplc_agent_joined", ndata:{other:data}}); 

                    if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                        if (typeof ga !== "undefined") {
                            ga('send', {
                              hitType: 'event',
                              eventCategory: 'WP_Live_Chat_Support',
                              eventAction: 'Event',
                              eventLabel: 'Agent joined chat'
                            });
                        }
                    }
                } else if(e.ndata.action === "send_user_direct_to_page"){
                    if(typeof e.ndata.direction !== "undefined"){
                        if(typeof e.ndata.pretty_name !== "undefined"){
                            var link_url = e.ndata.pretty_name + " - " + e.ndata.direction;
                            
                            var notice = "<span> ";
                            notice += (typeof e.ndata.agent_name !== "undefined" ? e.ndata.agent_name : "Agent") + " "; 
                            notice += (typeof bleeper_direct_to_page_localized_notice !== "undefined" ? bleeper_direct_to_page_localized_notice : "would like to direct you to the following page: ");
                            notice += link_url;
                            notice += " </span>";

                            the_message = {};
                            the_message.msg = notice;
                            the_message.originates = 0;
                            the_message.other = {};
                            wplc_push_message_to_chatbox(the_message, "u", function(){
                            });
                        }
                    }
                }
            }
            
        }

    });


    jQuery(document).on("bleeper_init_chat_box", function() {
        
        /* find out if we are actively involved in a chat, if yes, then open the chat window, unless the user has minimized it.. */
        
        nifty_chat_status_temp = Cookies.get("nc_status");
        if (typeof nifty_chat_status_temp !== "undefined" && nifty_chat_status_temp === "active" && !nifty_is_minimized) {
            open_chat(1);
            jQuery.event.trigger({type: "wplc_open_chat_2", wplc_online: wplc_online});
        } else {
            wplc_init_chat_box(false, false);
        }

        
    });

    jQuery(document).on("nifty_trigger_start_chat", function(e) {
        wplc_cookie_name = Cookies.get('wplc_name');
        wplc_cookie_email = Cookies.get('wplc_email');

        if( typeof wplc_cookie_name == 'undefined' ){ var wplc_cookie_name =  jQuery( "#wp-live-chat" ).find( "#wplc_name" ).val(); }
        if( typeof wplc_cookie_email == 'undefined' ){  var wplc_cookie_email =  jQuery( "#wp-live-chat" ).find( "#wplc_email" ).val(); }


        wplc_send_welcome_message();

        var start_data = {
                relay_action: 'wplc_new_chat',
                security: wplc_nonce,
                cid: wplc_cid,
                wplc_name: wplc_cookie_name,
                wplc_email: wplc_cookie_email,
                wplc_extra_data: {},
                url : window.location.href,
                session : wplc_session_variable
        };
        


        if(typeof wplc_start_chat_pro_custom_fields_filter !== "undefined" && typeof wplc_start_chat_pro_custom_fields_filter === "function"){
            wplc_start_chat_pro_custom_fields_filter(wplc_extra_data, start_data, function(passed_extra_data, passed_action_data){
                //Callback after processing custom field
                wplc_rest_api('start_chat', passed_action_data, 12000, null);

            });
        } else {
            start_data["wplc_extra_data"] = wplc_extra_data;
            wplc_rest_api('start_chat', start_data, 12000, null);
        }

        jQuery.event.trigger({type: "wplc_open_chat_2", wplc_online: wplc_online});

        if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
            if (typeof ga !== "undefined") {
                ga('send', {
                  hitType: 'event',
                  eventCategory: 'WP_Live_Chat_Support',
                  eventAction: 'Event',
                  eventLabel: 'Start Chat'
                });
            }
        }
         
    });

    if(typeof wplc_elem_trigger_id !== "undefined" && wplc_elem_trigger_id !== ""){
        var wplc_click_or_hover = 0;
        var wplc_class_or_id = 0;

        if(typeof wplc_elem_trigger_action !== "undefined" && wplc_elem_trigger_action !== ""){ wplc_click_or_hover = parseInt(wplc_elem_trigger_action); }
        if(typeof wplc_elem_trigger_type !== "undefined" && wplc_elem_trigger_type !== ""){ wplc_class_or_id = parseInt(wplc_elem_trigger_type); }
        
        try{
            jQuery( (wplc_class_or_id === 1 ? "#" : ".") + wplc_elem_trigger_id).on( (wplc_click_or_hover === 1 ? "mouseenter" : "click"), function(){
                open_chat(0);
            });
        } catch (e){
            console.log("WPLC Error: \"" + (wplc_class_or_id === 1 ? "#" : ".") + wplc_elem_trigger_id + "\" is not a valid selector");
        }
    }

    
    /** End Chat from User Side */
    jQuery(document).on("wplc_end_chat_as_user", function(e){
        if(typeof socket !== "undefined"){
            socket.emit('end chat', {chatid:chatid,agent:false,agent_name:'User', visitor_socket: socket.id});
        }

        data = { 'agent_name' : 'User'};

        jQuery.event.trigger({type: "bleeper_chat_ended_notification", ndata:data});
        
        jQuery('#bleeper_chat_ended').show();
        jQuery('.bleeper_restart_chat').remove();
        bleeper_end_chat_div_create();


        if (typeof user_heartbeat !== "undefined"){
            clearInterval(user_heartbeat);
        }
        
        user_heartbeat = undefined;
        
        socket.disconnect({test:'test'});
        
        niftyUpdateStatusCookie('browsing');
        // restart connection as a visitor
        if (typeof io !== "undefined") {
          socket = io.connect(WPLC_SOCKET_URI, { query : query_string, transports: ['websocket'] } );
          wplc_chat_delegates();
        }

        if(typeof Cookies !== "undefined"){
          Cookies.remove("wplc_cid");
        }

        if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
            if (typeof ga !== "undefined") {
                ga('send', {
                  hitType: 'event',
                  eventCategory: 'WP_Live_Chat_Support',
                  eventAction: 'Event',
                  eventLabel: 'User End Chat'
                });
            }
        }
    });
});

