/**
 * WPLC - Agent JavaScript (Main)
 *
 * Copyright (c) 2017 Code Cabin Inc.
 *
 * Questions? 
 * Email nick@wp-livechat.com
 * Version 1.0
 * Released August 2017
 * 
 *
 * 
*/


/**
 * General variables
 */
var socket;
var active_socket;
var active_chatid;
var active_customerID = null;
var remove_visitor_timer = [];
var visitor_list = {};
var unread_count = [];
var involved_list = {};
var last_chat_messages = {};
var bleeper_emoji_converter;
var bleeper_delegates;
var connection_lost_type;
var information_received_pointer = false;
var latency_dead_check;
var bleepblop_running = false;
var ping_list = {};
var old_version_detected = false;

/**
 * Setup supported file suffix types
*/
var bleeper_file_suffix_check = [
  "zip", "pdf", "txt", "mp3", "mpa", "ogg", "wav", "wma", "7z", "rar", 
  "db", "xml", "csv", "sql", "apk", "exe", "jar", "otf", "ttf", "fon", 
  "fnt", "ai", "psd", "tif", "tiff", "ps", "msi", "doc", "docx", 
];

/**
 * Variable to help identfy if we are editing a message
 * @type {Boolean}
 */
var niftyIsEditing = false;

/**
 * Identify the last message sent, so when we press UP we can edit it
 */
var lastmessagesent;

/**
 * Visitor data timer
 */
var get_visitor_timer = undefined;

/** Prompt and cancel callback reference */
var nifty_prompt_callback = null;
var nifty_cancel_callback = null;

var nifty_my_socket = null;

var bleeper_ping = new Audio(wplc_baseurl + "/audio/ding.mp3");
var bleeper_ring = new Audio(wplc_baseurl +"/audio/ring.wav");

var bleeper_dash_fullscreen_minimal = false;
var bleeper_show_drag_zone = false;
var bleeper_showing_quick_responses = false;

var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

var message_type = 'u';

var bleeper_limited = true;

$ = typeof $ !== "undefined" ? $ : jQuery;

var $messages;
var $inputMessage;

var FADE_TIME = 150; // ms
var TYPING_TIMER_LENGTH = 400; // ms

var query_string;
var username =  bleeper_agent_name;
var connected = false;
var typing = false;
var lastTypingTime;

var nc_sid;
var nc_name;
var nc_chatid;


var bleeper_favico_noti = '../wp-content/plugins/wp-live-chat-support/images/48px_n.png';
var bleeper_favico = '../wp-content/plugins/wp-live-chat-support/images/48px.png';

// used to validate visitors so we can double-verify their existence
var visitor_validator = {};
var is_domain_filter_active = false;

var node_uri = "";

var bleeper_active_filter = false;
var bleeper_visitor_filters = {
  new_visitors : 0,
  active_chats : 1,
  referer      : 2
};

/* Regex for inline links */
var bleeper_link_match_regex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!,.;<>]*[-A-Z0-9+&@#\/%=~_|<>])/ig;

(function($) {
  $(document).on("bleeper_dom_ready", function(e) {
    if (typeof bleeper_ring_override !== "undefined") {
      bleeper_ring = new Audio(bleeper_ring_override);
    }
    if (typeof bleeper_message_override !== "undefined") {
      bleeper_ping = new Audio(bleeper_message_override);
    }

    // Initialize variables
    var $window = $(window);
    $messages = $('.messages'); // Messages area
    $inputMessage = $('.inputMessage'); // Input message input box

    /* find out if we have had a chat with this visitor before */
    sid = nc_getCookie("nc_sid");
    chatid = nc_getCookie("nc_chatid");

    if (typeof override_chatid !== "undefined") {
      chatid = override_chatid;
    }

    query_string = "nc_api_key="+nifty_api_key+"&nc_agent_id="+agent_id;

    if(typeof wplc_guid !== "undefined"){
      query_string += "&guid=" + wplc_guid;
    }

    if(typeof bleeper_agent_verification_end_point !== "undefined"){
      query_string += "&agent_verification_end_point=" + bleeper_agent_verification_end_point;
    }

    if(typeof agent_id !== "undefined"){
      query_string += "&agent_id=" + agent_id;
    }

    if(typeof window !== "undefined" && typeof window.location !== "undefined" && typeof window.location.href !== "undefined"){
      query_string += "&referer=" + window.location.href; 
    }


    jQuery.event.trigger({type: "bleeper_connect", ndata:{query_string:query_string}});

    // Keyboard events
    $inputMessage.keydown(function (event) {
      // When the client hits ENTER on their keyboard
      if (event.which === 13) {
        event.preventDefault();
        jQuery("#wplc_send_msg").click();
      } else if (event.which === 38 && !event.shiftKey) {
        if (typeof lastmessagesent !== "undefined") {
          if(typeof wplc_integration_pro_active !== "undefined" && wplc_integration_pro_active === "true"){
            var mid = lastmessagesent;
            var mdiv = jQuery('.message_'+mid+ " .messageBody").html();
            jQuery("#inputMessage").val(mdiv);
            // set the niftyIsEditing variable to the msgID so we can identify if we are in the process of editing a message
            niftyIsEditing = mid;
          }
        }

      } else if  (event.which === 27 && !event.shiftKey) {
        jQuery("#wplc_chatmsg").val('');
        niftyIsEditing = false;
      }
    });

    $inputMessage.keyup(function (event) {
      // When the client hits ENTER on their keyboard
      if (event.which === 13) {
      } else {
        //socket.emit('typing_preview', {tempmessage: $inputMessage.val()});
      }
    });

    $inputMessage.on('input', function() {
      t_ag_cid = nifty_api_key+''+parseInt(agent_id)+''+parseInt(agent_id);
      t_ag_cid = md5(t_ag_cid);

      if (active_chatid === t_ag_cid) {
        /* do nothing as we are chatting with ourselves */
      } else {
        updateTyping();
      }
    });

    $(document).on("click","#wplc_send_msg", function() {
      sendMessage();
      socket.emit('stop typing',{chatid:active_chatid});
      typing = false;
    });


    $(document).on("mouseout",".bleeper-edit-message", function() {
      $(this).hide();

    });
    $(document).on("mouseenter",".bleeper-edit-message", function() {
      $(this).show();
    });
    $(document).on("mouseleave",".message", function() {
      var tmid = $(this).attr('mid');
      $(".message_"+tmid+ " .bleeper-edit-message").hide();
    });
    $(document).on("mouseenter",".message", function() {
      var tmid = $(this).attr('mid');
      $(".message_"+tmid+ " .bleeper-edit-message").show();
    });
    $(document).on("click", ".bleeper-edit-message", function() {
      var mid = $(this).parent().attr('mid');
      var mdiv = $(this).siblings('.messageBody').attr("data-message");
      $("#inputMessage").val(mdiv);
      // set the niftyIsEditing variable to the msgID so we can identify if we are in the process of editing a message
      niftyIsEditing = mid;

    });  

    // Click events
    // Focus input when clicking on the message input's border
    $inputMessage.click(function () {
      $inputMessage.focus();
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('open_chat')) {
            var tcid = e.target.getAttribute('cid');
            nc_add_user_to_list(visitor_list[tcid], function() {
              $("#"+tcid).click();
            });
        } 
    }, false);

    $("body").on("click", ".init_chat", function() {
      var tcid = $(this).attr('cid');
      jQuery.event.trigger({type: "bleeper_initiate_chat", ndata:{chatid:tcid, agent:agent_id}});
      socket.emit('initiate chat',{chatid: tcid});
      nc_add_user_to_list(visitor_list[tcid], function() {
        $("#"+tcid).click();
        setTimeout(function() {
          $(".bleeper_join_chat_btn").click();
        },100)

      });
    });

    $("body").on("click", ".bleeper_close_item", function() {
      var tcid = $(this).attr('cid');
      $("#"+tcid).remove();
      bleeper_remove_chatid_from_ls(tcid);
      bleeper_remove_events(tcid);

      delete visitor_list[tcid];

      setTimeout(function() {
        $("#user_count").click();
      },100);
    });

    $("body").on("click", ".accept_chat", function() {
      var tcid = $(this).attr('cid');

      involved_list[tcid] = true;
      
      $(this).remove();
      socket.emit('chat accepted',{chatid: tcid,agent_id:agent_id,agent_name:bleeper_agent_name});
      jQuery.event.trigger({type: "bleeper_add_agent", ndata:{chatid:tcid, agent:agent_id}});

      nc_add_user_to_list(visitor_list[tcid], function() {
        $("#"+tcid).click();

      });
      nifty_chat_add_agent_involved(tcid, agent_id);
      nifty_chat_add_agent_involved_visitor_list(tcid, agent_id);

      jQuery.event.trigger({type: "bleeper_agent_accepted_chat", ndata:{chat_id:tcid,agent_id:agent_id}});    
    });

    
    $("body").on("click", "#user_count", function() {
        $('.infoArea').show();
        $('.chatArea').hide();
        $('.messages').empty();
        
        active_chatid = undefined;
        
        if (typeof visitor_list === "undefined" || Object.keys(visitor_list).length === 0) {
          nc_no_visitors();
        }

        /* remove the active state of any agent chat item */
        $(".online_agent").each(function() {
          jQuery(this).removeClass('active');
        });

        /* remove the active state of any user chat item */
        $(".userList").each(function() {
          jQuery(this).removeClass('active');
        });

        
        
        
    });

    $("body").on("click", ".chatTransfer", function() {
      var tmp_chatid_transfer = $(this).attr('cid');
      message = '<p class="chat_agent_list">Please select an agent:</p>';
      message += '<div class="t_agent_list"></div>';


      params = {
        token: 'zf6fe1399sdfgsdfg02ad09ab6a8cb7345s',
        api_key: nifty_api_key
      },


      jQuery.ajax({
         url : nc_buildUrl(node_uri+'/api/v1/list_online_agents/', params),
         type : 'GET',
         cache: false,
         processData: false, 
         contentType: false, 
         success : function(data) {
             information_received_pointer = 'transfer';
             online_ag_array = JSON.parse(data);
             var atleast_one_added = false;
             for (l in online_ag_array) {
                if (online_ag_array[l] !== nifty_my_socket) {
                  atleast_one_added = true;
                  socket.emit('request information',{socketid: online_ag_array[l]});
                  $('<div/>', {
                    'class': 'active_agent_in_list',
                    html: '<input type="radio" value="" name="ag_t_selection" cid="'+tmp_chatid_transfer+'" id="ag_t_'+online_ag_array[l]+'" /> <span class="ag_t_span_'+online_ag_array[l]+'">getting information...</span> <br/>'
                  }).appendTo(".t_agent_list");
                }
             }

             if(!atleast_one_added){
                   $('<div/>', {
                    'class': 'no_active_agents_notice',
                    html: '<span class="no_active_agents_notice_inner">No other agents online...</span> <br/>'
                  }).appendTo(".t_agent_list");
             }
            
         },
         error : function (){

        }   
      });


      niftyShowDialog("Transfer chat", message, 'Transfer', 'Cancel', function() {
          var ag_selection = $('input[name=ag_t_selection]:checked', '.t_agent_list').val();
          var transfer_chat_id = $('input[name=ag_t_selection]:checked', '.t_agent_list').attr('cid');
          if (typeof ag_selection !== "undefined") {
            socket.emit('transfer chat',{socketid: ag_selection,chatid:transfer_chat_id,from_agent:bleeper_agent_name});
          }

      },null);

    });

    $("body").on("click", ".chatTransferDepartment", function() {
      var tmp_chatid_transfer = active_chatid;
      message = '<p class="chat_department_list">Please select a department:</p>';
      message += '<div class="t_department_list">';
      if(typeof bleeper_departments !== "undefined"){
        for(var index in bleeper_departments){
          message += "<input type='radio' name='dp_t_selection' cid='"+tmp_chatid_transfer+"' value='"+index+"' /><span>" + bleeper_departments[index] + "</span> <br/>"; 
        }
      }
      message += '</div>';


      niftyShowDialog("Transfer chat", message, 'Transfer', 'Cancel', function() {
          var dp_selection = $('input[name=dp_t_selection]:checked', '.t_department_list').val();
          var transfer_chat_id = $('input[name=dp_t_selection]:checked', '.t_department_list').attr('cid');
          if (typeof dp_selection !== "undefined") {
            socket.emit('transfer chat department',{socketid: dp_selection,department:dp_selection,chatid:transfer_chat_id,from_agent:bleeper_agent_name});
          }

      },null);

    });


    //Direct to page handler
    $("body").on("click", ".chatDirectUserToPagePrompt", function() {
      if (typeof active_chatid !== "undefined") {
        if(typeof bleeper_valid_direct_to_page_array !== "undefined"){

          var select_html_for_modal = bleeper_generate_dropdown("bleeper_direct_to_page_select", bleeper_valid_direct_to_page_array);

          niftyShowDialog("Direct User To Page", select_html_for_modal , 'Direct User To Selected Page', 'Cancel', function() {
              //What to do wena
              var selected_page_url = jQuery("#bleeper_direct_to_page_select").val();

              socket.emit('custom data',{action:'send_user_direct_to_page',chatid:active_chatid,agent_name:bleeper_agent_name, direction: selected_page_url, pretty_name: bleeper_valid_direct_to_page_array[selected_page_url]});
          },null);

          jQuery.event.trigger({type: "bleeper_direct_user_to_page_prompt"});
        }
      }
    });
    $("body").on("click", ".chatClose", function() {
        var tmp_chatid_close = $(this).attr('cid');
        

        involved_list[tmp_chatid_close] = false;
        $(this).attr('cid',null);
        $(this).hide();
        $("#user_count").click();
        socket.emit('agent left',{chatid:tmp_chatid_close,agent:agent_id,agent_name:username});
        nifty_chat_remove_agent_involved(tmp_chatid_close, agent_id);
        if (typeof active_chatid !== "undefined") {
          active_chatid = undefined;
        }
        $("#"+tmp_chatid_close).remove();
        //bleeper_remove_chatid_from_ls(tmp_chatid_close);
        bleeper_remove_events(tmp_chatid_close);
    });
    $("body").on("click", ".endChat", function() {
        var tmp_chatid_close = $(this).attr('cid');
        
        involved_list[tmp_chatid_close] = false;
        $(this).attr('cid',null);
        $(this).hide();
        $("#user_count").click();
        socket.emit('agent left', {chatid:tmp_chatid_close,agent:agent_id,agent_name:username});
        socket.emit('end chat', {chatid:tmp_chatid_close,agent:agent_id,agent_name:username, visitor_socket: active_socket});
        nifty_chat_remove_agent_involved(tmp_chatid_close, agent_id);
        if (typeof active_chatid !== "undefined") {
          active_chatid = undefined;
        }

        $("#"+tmp_chatid_close).remove();
        //bleeper_remove_chatid_from_ls(tmp_chatid_close);
        bleeper_remove_events(tmp_chatid_close);
        visitor_list[tmp_chatid_close].state = 'browsing';
        visitor_list[tmp_chatid_close].involved = undefined;
        jQuery.event.trigger({type: "bleeper_add_initiate_button_no_col", cid:tmp_chatid_close});  
        nc_remove_open_chat_visitor_row(tmp_chatid_close);

        setTimeout(function() {
          $("#user_count").click();
        },100);

    });
    $("body").on("click", ".minChat", function() {
        var tmp_chatid_close = $(this).attr('cid');
        
        $(this).attr('cid',null);
        $(this).hide();
        $("#user_count").click();
        if (typeof active_chatid !== "undefined") {
          active_chatid = undefined;
        }

        jQuery.event.trigger({type: "bleeper_min_chat"});

        jQuery('.eventbox-close').click();
    });

    $("body").on("click", ".userList", function() {
        $(".nifty_bg_holder").fadeOut();      
        message_type = 'u';

        $('.inchat-menu').show();
        $('.infoArea').hide();
        $('.chatArea').show();
        $('.messages').empty();
        //nc_log('Fetching messages...',"fetchinglog",{prepend: true});
        active_socket = $(this).attr('socket');
        active_customerID = $(this).attr('customerID');
        var tmp_chatid = $(this).attr('id');
        active_chatid = tmp_chatid;
        
        //involved_list[tmp_chatid] = true;
        
        unread_count[tmp_chatid] = 0;
        bleeper_check_if_all_read();
        nc_update_unread_count(tmp_chatid);

        /* remove the active state of any agent chat item */
        $(".online_agent").each(function() {
          jQuery(this).removeClass('active');
        });

        $(".userList").removeClass("active");
        $(this).removeClass("newmessage");
        $(".chatClose").show();
        $(".endChat").show();
        $(".minChat").show();
        $(".chatClose").attr("cid",active_chatid);
        $(".endChat").attr("cid",active_chatid);
        $(".chatTransfer").attr("cid",active_chatid);


        $(".eventbox").show();
        $(".events-ul").empty();

        jQuery.event.trigger({type: "bleeper_send_get_chat_history", ndata:{chatid:tmp_chatid,agent_name:username,agent_id:agent_id}});
        jQuery.event.trigger({type: "bleeper_clear_typing_preview", cid: tmp_chatid});

        bleeper_update_event_box(active_chatid);


        bleeper_add_socket_to_ping(active_socket,active_chatid);


        $(".chatInfoArea-Name").html("");
        $(".chatInfoArea-Email").html("");
        $(".chatInfoArea-Info1").html("");
        $(".user_gravatar").html("");
        if (typeof visitor_list[active_chatid] !== "undefined") {
          if(typeof visitor_list[active_chatid].username !== "undefined"){
            $(".chatInfoArea-Name").html( bleeper_strip_tags(visitor_list[active_chatid].username) );
          } else {
            $(".chatInfoArea-Name").html("Guest");
          }

          $(".chatInfoArea-Info1").html("from: <a href='"+bleeper_strip_tags(visitor_list[active_chatid].referer)+"' target='_BLANK'>"+bleeper_string_limiter(bleeper_strip_tags(visitor_list[active_chatid].referer),45)+"</a>");
          
          if(typeof visitor_list[active_chatid].email !== "undefined"){
            $(".user_gravatar").html("<img src='https://www.gravatar.com/avatar/"+md5(visitor_list[active_chatid].email)+"?s=100&d=mm' />");
            $(".chatInfoArea-Email").html('<a href="mailto:'+ bleeper_strip_tags( visitor_list[active_chatid].email )+'" target="_BLANK">'+ bleeper_strip_tags( visitor_list[active_chatid].email )+'</a>');
          } else {
            $(".user_gravatar").html("<img src='https://www.gravatar.com/avatar/unknown?s=100&d=mm' />");
            $(".chatInfoArea-Email").html('Email Unknown');
          }
          
          jQuery.event.trigger({type: "bleeper_open_chat", cid:active_chatid, username: visitor_list[active_chatid].username});
        }
        

        /* removed - only triggered now when you click on the "Join chat" button */
        //nifty_chat_add_agent_involved(active_chatid, agent_id);

        if (typeof involved_list[active_chatid] === "undefined" || involved_list[active_chatid] === false) {
          $("#inputMessage").prop('disabled',true);
          $(".bleeper_join_chat_div").show();

        } else {
          $(".bleeper_join_chat_div").hide();
          $("#inputMessage").prop('disabled',false);
          $("#inputMessage").focus();


        }


        if ($(this).hasClass('inactive')) {} else {
          $(this).addClass('active');
        }

        

        setTimeout(function() {
          $("#fetchinglog").fadeOut("slow");
        },2000);

        
        /**
         * This makes sure that "agent joined" gets sent back to the customer (if the agent previously disconnected)
         * 
         */
        if (typeof involved_list[active_chatid] !== "undefined" && involved_list[active_chatid] === true) {
          
          jQuery.event.trigger({type: "bleeper_add_agent", ndata:{chatid:active_chatid, agent:agent_id}});
          nifty_chat_add_agent_involved(active_chatid, agent_id);
          nifty_chat_add_agent_involved_visitor_list(active_chatid, agent_id);
          socket.emit('chat accepted',{
            chatid: active_chatid,
            agent_id:agent_id,
            agent_name:bleeper_agent_name
          });
        }

       
    });
    $("body").on("click", ".bleeper_join_chat_btn", function(e) {
      involved_list[active_chatid] = true;
      socket.emit('chat accepted',{chatid: active_chatid,agent_id:agent_id,agent_name:bleeper_agent_name});
      jQuery.event.trigger({type: "bleeper_add_agent", ndata:{chatid:active_chatid, agent:agent_id}});
      nifty_chat_add_agent_involved(active_chatid, agent_id);
      nifty_chat_add_agent_involved_visitor_list(active_chatid, agent_id);
      $(".bleeper_join_chat_div").hide();
      $("#inputMessage").prop('disabled',false);
      $("#inputMessage").focus();

    });
    $("body").on("click", ".online_agent", function() {

        $(".eventbox").hide();
        $(".eventbox-wrapper").hide();

        /* remove the active state of any agent chat item */
        $(".online_agent").each(function() {
          jQuery(this).removeClass('active');
        });

        /* remove the active state of any user chat item */
        $(".userList").each(function() {
          jQuery(this).removeClass('active');
        });

        $('.inchat-menu').hide();
        $('.nifty_bg_holder').hide();
        $('.infoArea').hide();
        $('.chatArea').show();
        $('.messages').empty();
        $(this).removeClass("newmessage");
        $(".chatClose").show();
        $(".endChat").show();
        $(".minChat").show();
        $(".chatClose").attr("cid",active_chatid);
        $(".endChat").attr("cid",active_chatid);

        var selected_agentid = $(this).attr('aid');
        message_type = parseInt(selected_agentid);



        //nc_log('Fetching messages...',"fetchinglog",{prepend: true});
        active_socket = $(this).attr('socket');


        var tmp_chatid = $(this).attr('id');
        active_chatid = tmp_chatid;
        involved_list[tmp_chatid] = true;
        unread_count[tmp_chatid] = 0;
        nc_update_unread_count(tmp_chatid);

        jQuery.event.trigger({type: "bleeper_send_get_chat_history", ndata:{chatid:tmp_chatid,agent_name:username,agent_id:agent_id}});
        
        $(".chatInfoArea-Name").html("");
        $(".chatInfoArea-Info1").html("");
        $(".user_gravatar").html("");


        $(".chatInfoArea-Name").html( bleeper_strip_tags(wplc_agent_data[selected_agentid].name) );
        $(".user_gravatar").html("<img src='https://www.gravatar.com/avatar/"+wplc_agent_data[selected_agentid].md5+"?s=100&d=mm' />");

        if ($(this).hasClass('inactive')) {} else {
          $(this).addClass('active');
        }
        setTimeout(function() {
          $("#fetchinglog").fadeOut("slow");
        },2000);

       jQuery.event.trigger({type: "bleeper_open_chat", cid:active_chatid, username: wplc_agent_data[selected_agentid].name});
    });








    

    $("#nifty_file_input").on("change", function(){
        var file = this.files[0]; //Last file in array
        wplcShareFile(file,'#nifty_attach_fail_icon', '#nifty_attach_success_icon', '#nifty_attach_uploading_icon',  "#nifty_select_file");
        jQuery("#chat_drag_zone").fadeOut();
        
    });



    /*Add in filter click handlers */
    $("body").on("click", ".filter-new-visitors", function(){
      bleeper_active_filter = bleeper_visitor_filters.new_visitors;
      bleeper_refresh_visitor_list();
    });

    $("body").on("click", ".filter-active-chats", function(){
      bleeper_active_filter = bleeper_visitor_filters.active_chats;
      bleeper_refresh_visitor_list();
    });

    $('body').on('click', '.filter-referer', function(){
      bleeper_active_filter = bleeper_visitor_filters.referer;
      bleeper_refresh_visitor_list();
    });

    $('body').on('click', '#nifty_referer_contains', function(){
	  bleeper_refresh_visitor_list();
    });

    $('body').on('change keyup paste mouseup', '#nifty_referer_url', function(){
      var last_val = $('#nifty_referer_url').attr('last-val');
      var cur_val = $('#nifty_referer_url').val();
      if(typeof last_val === 'undefined' || last_val !== cur_val){
        $('#nifty_referer_url').attr('last-val',cur_val);
        bleeper_refresh_visitor_list();
      }
    });

    $("body").on("click", ".filter-clear", function(){
	  $('#nifty_referer_options').fadeOut('slow');
      bleeper_active_filter = false;
      bleeper_refresh_visitor_list();
    });

    $("body").on("click", ".filter-menu .dropdown-menu li a[class!='filter-clear']", function(){
      //Not trying to clear the filter persay
      var current_filter = jQuery(this).text();
      current_filter = bleeper_string_limiter(current_filter, 12);
	  $('#nifty_referer_options').fadeOut('slow');
      jQuery(".filter-active-tag-container").fadeOut("slow", function(){
        jQuery(".filter-active-tag-inner").text(current_filter);
		if(bleeper_active_filter === bleeper_visitor_filters.referer) {
		  $('#nifty_referer_options').fadeIn('slow');
		}
        jQuery(".filter-active-tag-container").fadeIn("slow");
      });
    });

/* socket delegates were here */
  });
})(jQuery);

/* MOBILE CONTROL */
jQuery(function(){
  jQuery(document).on("bleeper_open_chat", function(e){
    if(jQuery(window).width() < 700){
      jQuery("label[for=user_list_mobile_control]").click();
    }
  });

  jQuery(document).on("bleeper_min_chat", function(e){
    if(jQuery(window).width() < 700){
      jQuery("label[for=user_list_mobile_control]").click();
    }
  });
});
/* END MOBILE CONTROL */

jQuery(document).on("bleeper_dom_ready", function(e) {

  
  if (document.getElementById('inputMessage') !== null) { 

    /** Image pasting functionality */
    document.getElementById('inputMessage').onpaste = function (event) {
      jQuery.event.trigger({type: "bleeper_input_paste", event: event});
    }
  }
});


jQuery(document).on("bleeper_dom_ready", function(e) {

  jQuery("#nifty_admin_chat_prompt_confirm").click(function(){
      niftyExecuteDialogCallback();
  });

  jQuery("#nifty_admin_chat_prompt_cancel").click(function(){
      niftyCloseDialog();
  });

  jQuery("#quick_response_drawer_handle").click(function(){

    if(bleeper_showing_quick_responses){
      jQuery("#quick_response_drawer_handle i").removeClass("fa-times").addClass("fa-bolt");
      jQuery("#quick_response_drawer_container").fadeOut("fast");
    } else {
      jQuery("#quick_response_drawer_handle i").removeClass("fa-bolt").addClass("fa-times");
      jQuery("#quick_response_drawer_container").fadeIn("fast");
    }

    bleeper_showing_quick_responses = !bleeper_showing_quick_responses;

  });

 jQuery("#nifty_wrapper").click(function(e){
      if(bleeper_showing_quick_responses){
        if(typeof e !== "undefined" && typeof e.target !== "undefined" && typeof e.target.id !== "undefined"){
          if(e.target.id !== "quick_response_drawer_handle" && (e.target.nodeName !== "I" && e.target.nodeName !== "I")) {
            jQuery("#quick_response_drawer_handle").click();
          }
        }
      }
  });
  jQuery("body").on("click", ".eventbox", function() {
    jQuery(".eventbox-wrapper").show();
  })
  jQuery("body").on("click", ".eventbox-close", function() {
    jQuery(".eventbox-wrapper").hide();
  })

  jQuery("body").on("click", ".quick_response_item", function(){
    var response = jQuery(this).text();
    var current_text = jQuery("#inputMessage").val();
    
    jQuery("#inputMessage").val(current_text + response);
    jQuery("#quick_response_drawer_handle").click();
  });

  jQuery("#toolbar-item-fullscreen").click(function(){
    if(bleeper_dash_fullscreen_minimal){
      //Show bars again
      jQuery("section").removeClass("strip_margins");
      jQuery("footer").removeClass("strip_margins");
      jQuery(".topnavbar[role=navigation]").fadeIn();
      jQuery(".aside[role=navigation]").fadeIn();
    } else {
      //Hide them now 
      jQuery(".topnavbar[role=navigation]").fadeOut("fast");
      jQuery(".aside[role=navigation]").fadeOut("fast");

      setTimeout(function(){
        jQuery("section").addClass("strip_margins");
        jQuery("footer").addClass("strip_margins");
      }, 300);
    }
    bleeper_dash_fullscreen_minimal = !bleeper_dash_fullscreen_minimal; //Flips this state
  });

  var dragTimeout = -1;
  jQuery("html").bind("dragenter", function(){
    if(typeof chatid !== "undefined" && chatid !== false){
      jQuery("#chat_drag_zone").fadeIn();
      bleeper_show_drag_zone = true;
    }

  });

  jQuery("html").bind("dragover", function(){
    if(typeof chatid !== "undefined" && chatid !== false){
      bleeper_show_drag_zone = true;
    }
  });
  jQuery("html").bind("dragleave", function(){
      bleeper_show_drag_zone = false;
      clearTimeout(dragTimeout);
      dragTimeout = setTimeout(function(){
        if(!bleeper_show_drag_zone){ jQuery("#chat_drag_zone").fadeOut(); }
      }, 200);
  });

  jQuery("#chat_drag_zone").on("dragover", function(event) { event.preventDefault();  event.stopPropagation(); });
  jQuery("#chat_drag_zone").on("dragleave", function(event) { event.preventDefault();  event.stopPropagation(); });

  jQuery("#chat_drag_zone").on("drop", function(event) {
      event.preventDefault();  
      event.stopPropagation();
      if(jQuery('#nifty_add_media:checked').length > 0){
      //Do nothing its open
    } else {
      jQuery("#nifty_add_media").click(); 
    }

    var fileInput = document.getElementById('nifty_file_input');
    fileInput.files = event.originalEvent.dataTransfer.files;
  });

  if (document.getElementById('messages') !== null) {
    bleeper_mutation_observer_init();
    bleeper_init_emoji_picker();
    bleeper_init_emoji_converter(0);
  }
});

/**
 * Initialize the mutation observer
*/
function bleeper_mutation_observer_init(){
  if (document.getElementById('messages') !== null) {
    var target = document.getElementById('messages');
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(bleeper_mutation_observer_callback);    
    });
     
    var config = { attributes: true, childList: true, characterData: true };


    observer.observe(target, config);
  }
}

/**
 * Mutation observer for attachments
 *
 * @param {object} mutation The mutation object
*/
function bleeper_mutation_observer_callback(mutation){
  if (mutation.type === 'childList') {
    //Now try find 
    jQuery(".bleeper_in_chat_card a").each(function() {
      var last_dynamic_link = jQuery(this);
      if(typeof last_dynamic_link !== "undefined"){
        //We have a dynamic link.  Let's do dis now
        var dynamic_href = last_dynamic_link.attr("href");
        var breakdown_href = dynamic_href;
        var generate_simple_title = false;
        if(dynamic_href.indexOf(".") !== -1){
          //There is a full stop, so we have an extension type here. (This is probably a .com though, so we will end up here no matter what in most cases)
          var extension = dynamic_href.substring(dynamic_href.lastIndexOf("."));
          if(extension !== ".zip" && extension !== ".exe" && extension !== ".bin" && extension !== ".rar" && extension !== ".7zip" && extension !== ".ai" &&
             extension !== ".psd" && extension !== ".psd" && extension !== ".esp" && extension !== ".eps" && extension !== ".txt" && extension !== ".js" &&
             extension !== ".cdr" && extension !== ".apk"){
            //common web file
            if(dynamic_href.indexOf("//") !== -1){
              breakdown_href = dynamic_href.substring(dynamic_href.indexOf("//") + 2);
              breakdown_href = breakdown_href.substring(0, breakdown_href.indexOf("/"));
              generate_simple_title = true;
            } else {
              breakdown_href = dynamic_href;
            }
          } else {
            //Probably a file, and not the end of the world if its not
            breakdown_href = dynamic_href.substring(dynamic_href.lastIndexOf("/"));
            generate_simple_title = true;
          }
        } else {
          //Just assumy this is a link.
          if(dynamic_href.indexOf("//") !== -1){
            breakdown_href = dynamic_href.substring(dynamic_href.indexOf("//") + 2);
            breakdown_href = breakdown_href.substring(0, breakdown_href.indexOf("/"));
            generate_simple_title = true;
          } else {
            breakdown_href = dynamic_href;
          }
        }

        if(generate_simple_title){
          var the_title = breakdown_href;
          if(the_title.indexOf(".") !== -1){
            the_title = the_title.substring(0, the_title.lastIndexOf("."));
          }
          the_title = the_title.replace(new RegExp("[0-9]", "g"), ""); //Remove all numbers
          the_title = the_title.replace(/-/g, " ");
          the_title = the_title.replace("http://", "");
          the_title = the_title.replace("https://", "");
          the_title = the_title.replace("www.", "");
          the_title = the_title.replace("/", "");
          the_title = the_title.trim();

          if(the_title === ""){
            the_title = "Link";
          }

          if(breakdown_href === ""){
            breakdown_href = dynamic_href;
          }

          breakdown_href = "<small><strong>" + the_title + "</strong> (" + breakdown_href + ")</small>";

        }

        last_dynamic_link.html(breakdown_href);

      }

    });
  }
}

/**
 * Event which gets all visitor data from the socket
*/
function bleeper_get_visitor_data() {
  if (typeof socket.connected !== "undefined" && socket.connected) {
    socket.emit('get visitor list',{api_key: nifty_api_key});
  }

}

/**
 * Initialize the emoji picket
*/
function bleeper_init_emoji_picker(){
  if(typeof wdtEmojiBundle !== "undefined"){
    wdtEmojiBundle.defaults.emojiSheets = {
        'apple'    : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_apple_64_indexed_128.png',
        'google'   : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_google_64_indexed_128.png',
        'twitter'  : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_twitter_64_indexed_128.png',
        'emojione' : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_emojione_64_indexed_128.png',
        'facebook' : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_facebook_64_indexed_128.png',
        'messenger': wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_messenger_64_indexed_128.png'
    };

    wdtEmojiBundle.init('.wdt-emoji-bundle-enabled');
  }
}

/**
 * Recusively initialize the input field for emoji support. If an error occurs it will attempt again.
 *
 * Will try to initialize up to 5 times.
 * @param {int} attempt Attempt number
*/
function bleeper_init_emoji_converter(attempt){
    attempt = typeof attempt === "undefined" ? 0 : parseInt(attempt);
    if(typeof EmojiConvertor !== "undefined"){
        bleeper_emoji_converter = new EmojiConvertor(); //Used for converting colon codes to unicodes
    } else {
        //Dom not ready yet
        if(attempt < 5){
            setTimeout(function(){
                bleeper_init_emoji_converter(attempt++);
            }, 1000);
        }
    }
}

/**
 * Converts colons to unified
 *
 * @param {string} msg The message string
 * @return {string} The formatted message
*/
function bleeper_convert_colon_to_uni(msg){
    if(typeof EmojiConvertor !== "undefined" && typeof bleeper_emoji_converter !== "undefined"){
        bleeper_emoji_converter.init_env(); // else auto-detection will trigger when we first convert
        bleeper_emoji_converter.replace_mode = 'unified';
        bleeper_emoji_converter.allow_native = true;
        return bleeper_emoji_converter.replace_colons(msg);
    } else {
        return msg;
    }
}

/**
 * Render emojis within a message string
 *
 * @param {string} msg The chat message
 * @return {string} The chat message with emojis
*/
function bleeper_emoji_render(msg){
  if(typeof wdtEmojiBundle !== "undefined"){
    msg = wdtEmojiBundle.render(msg);
  }
  return msg;
}

/**
 * Add the agent to the involved list for a specific chat on the server (event)
 *
 * @param {string} chatid The chat ID
 * @param {int} agentid The agent ID
*/
function nifty_chat_add_agent_involved(chatid, agentid) {
  jQuery.event.trigger({type: "bleeper_add_agent_involved", chatid:chatid, agentid:agentid});
}

/**
 * Add the agent to the involved list for a specific chat
 *
 * @param {string} chatid The chat ID
 * @param {int} agentid The agent ID
*/
function nifty_chat_add_agent_involved_visitor_list(chatid, agentid) {
  jQuery.event.trigger({type: "bleeper_add_agent_involved_visitor_list", chatid:chatid, agentid:agentid});
}

/**
 * Remove the agent rom the involved list for a specific chat
 *
 * @param {string} chatid The chat ID
 * @param {int} agentid The agent ID
*/
function nifty_chat_remove_agent_involved(chatid, agentid) {
  if(typeof all_agents !== "undefined"){
    if(typeof all_agents !== "object"){
      var check_all_agents = JSON.parse(all_agents);
    } else { 
      var check_all_agents = all_agents;
    }
    if (agentid === agent_id) {
      
      involved_list[chatid] = false;
    }
    agent_involved = check_all_agents[agentid];
    jQuery('#agent_grav_'+chatid+'_'+agentid).remove();
    jQuery('#agent_grav_visitor_'+chatid+'_'+agentid).remove();
  }

}

/**
 * Show a popup dialog within the chat dashboard
 *
 * @param {string} title The dialog title
 * @param {string} message The dialog message
 * @param {string} accept_btn The accept/confirm button label
 * @param {string} cancel_btn The cancle button label
 * @param {function} callback The accept callback function
 * @param {function} cancel_callback The cancel callback function
*/
function niftyShowDialog(title, message, accept_btn, cancel_btn, callback, cancel_callback){
    if(title !== null){
        jQuery(".nifty_admin_chat_prompt_title").text(title);        
    } else {
        jQuery(".nifty_admin_chat_prompt_title").text("Please Confirm");
    }
    if(message !== null){
        jQuery(".nifty_admin_chat_prompt_message").html(message);
    }

    if(accept_btn !== null){
        jQuery("#nifty_admin_chat_prompt_confirm").text(accept_btn);        
    } else {
        jQuery("#nifty_admin_chat_prompt_confirm").text("Confirm");
    }

    if(cancel_btn !== null){
        jQuery("#nifty_admin_chat_prompt_cancel").text(cancel_btn);        
    } else {
        jQuery("#nifty_admin_chat_prompt_cancel").text("Cancel");
    }



    niftyRegisterDialogCallback(callback);
    niftyRegisterDialogCancelCallback(cancel_callback);
    jQuery(".nifty_admin_overlay").fadeIn();
    jQuery(".nifty_admin_chat_prompt").fadeIn();
}

/**
 * Registed the confirm dialog callback 
 *
 * @param {function} callback The function to execute when confirmed
*/
function niftyRegisterDialogCallback(callback){
    nifty_prompt_callback = callback;
}

/**
 * Registed the cancel dialog callback 
 *
 * @param {function} callback The function to execute when cancelled
*/
function niftyRegisterDialogCancelCallback(callback){
    nifty_cancel_callback = callback;
}

/**
 * Execute the accept/confirm dialog callback
*/
function niftyExecuteDialogCallback(){
    if(typeof nifty_prompt_callback !== "undefined" && typeof nifty_prompt_callback === "function"){
        nifty_prompt_callback();
    }
    jQuery(".nifty_admin_chat_prompt").hide();
    jQuery(".nifty_admin_overlay").hide();
}

/**
 * Execute the cancel dialog callback
*/
function niftyExecuteDialogCancelCallback(){
    if(typeof nifty_cancel_callback !== "undefined" && typeof nifty_cancel_callback === "function"){
        nifty_cancel_callback();
    }
    jQuery(".nifty_admin_chat_prompt").hide();
    jQuery(".nifty_admin_overlay").hide();
}

/**
 * Close the active dialog
*/
function niftyCloseDialog(){
    niftyRegisterDialogCallback(null); //Just incase
    niftyExecuteDialogCancelCallback(nifty_cancel_callback);
}

/**
 * Send a chat message using the socket. Also checks if this is a new message, or an edit to an existing message.
*/
function sendMessage () {
  var message = $inputMessage.val();
  // Prevent markup from being injected into the message
  message = cleanInput(message);
  // Fix double emojis
  if(message.search(/\:(\S+)(\:)(\S+)\:/g) !== -1){
        message = message.replace(/\:(\S+)(\:)(\S+)\:/g, function (match, p1, p2, p3) {
            return [":", p1, "::", p3, ":"].join('');
        });
  }
  message = bleeper_convert_colon_to_uni(message);


  if (niftyIsEditing !== false) {
    // we edited a message 
    msgID = parseInt(niftyIsEditing);
    jQuery(".message_"+msgID+ " .messageBody").attr("data-message", message);
    jQuery(".message_"+msgID+ " .messageBody").html(wplcFormatParser(message));
    socket.emit('edit message', { message:message, chatid:active_chatid, msgID:msgID });

    jQuery.event.trigger({type: "bleeper_edited_message", ndata:{ message:message, chatid:active_chatid, msgID:msgID } });   
    
    jQuery("#inputMessage").val('');
    niftyIsEditing = false;

  } else {

    // this is new message
    var randomNum = Math.floor((Math.random() * 100) + 1);
    var msgID = Date.now()+randomNum;
    lastmessagesent = msgID;

    // if there is a non-empty message and a socket connection
    if (message && connected) {
      $inputMessage.val('');
      var msg_obj = {
        username: username,
        message: message,
        timestamp: Date.now(),
        msgID: msgID,
        aoru: agent_id
      };


      addChatMessage(msg_obj,
      {is_admin: false});

      jQuery.event.trigger({type: "bleeper_send_message", message:message, msg_id:msgID});

      // tell server to execute 'new message' and send along one parameter
      
      if (message_type === 'u') {
        bleeper_append_message_to_ls(active_chatid, msg_obj);
        socket.emit('new message', { message:message, chatid:active_chatid, msgID:msgID, aoru: agent_id });
      } else {
        /* get agent socket id */
        var to_sock_id = jQuery(".online_agent_"+message_type).attr('socket');
        bleeper_append_message_to_ls(active_chatid, msg_obj);
        socket.emit('new admin message', { message:message, chatid:active_chatid, msgID:msgID, from: agent_id, from_socket:socket.id, to: message_type, to_socket:to_sock_id });
      }

      jQuery.event.trigger({type: "bleeper_add_message_chatbox", message:message, msgID:msgID});
    }
  }
}

/**
 * Deprecated Send Message function
 *
 * @deprecated
*/
function old_sendMessage () {
  var message = $inputMessage.val();
  // Prevent markup from being injected into the message
  message = cleanInput(message);
  // if there is a non-empty message and a socket connection
  if (message && connected) {
    $inputMessage.val('');
    addChatMessage({
      username: username,
      message: message,
      timestamp: Date.now()

    });
    // tell server to execute 'new message' and send along one parameter
    var randomNum = Math.floor((Math.random() * 100) + 1);
    var msgID = Date.now()+randomNum;
    socket.emit('new message', {message:message,chatid:active_chatid, msgID:msgID});
  }
}

/** 
 * Add a log to the chat box
 *
 * @param {string} message Log message string
 * @param {object} options Options for the message being added (fade, prepend)
*/
function log (message, options) {
  var $el = jQuery('<li>').addClass('log').text(message);
  addMessageElement($el, options);
}

/** 
 * Add a log to the chat box
 *
 * @param {string} message Log message string
 * @param {int} id ID for the list item
 * @param {object} options Options for the message being added (fade, prepend)
*/
function nc_log (message, id, options) {
  var $el = jQuery('<li id="'+id+'">').addClass('log').text(message);
  addMessageElement($el, options);
}  

/**
 * Build a url with paramaters
 *
 * @param {string} url The current URL
 * @param {object} parameters Object of key value pairs to be added
 * @return {string} URL with params attached
*/
function nc_buildUrl(url, parameters){
  var qs = "";
  for(var key in parameters) {
    var value = parameters[key];
    qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
  }
  if (qs.length > 0){
    qs = qs.substring(0, qs.length-1); //chop off last "&"
    url = url + "?" + qs;
  }
  return url;
}

/**
 * Handles uploading a file within the chat
 *
 * @param {file} fileToUpload The file to upload
 * @param {string} failedID The id of the div to show when upload fails
 * @param {string} successID The id of the div to show when upload succeeds
 * @param {string} uploadingID The id of the div to show when upload is in progress
 * @param {string} originalID The id of the div to show when upload final div to show after evething is complete
*/
function wplcShareFile(fileToUpload, failedID, successID, uploadingID, originalID){
    var formData = new FormData();
    formData.append('file', fileToUpload);
    formData.append('timestamp', Date.now());
    jQuery(uploadingID).show();
    jQuery(originalID).hide();
    jQuery(successID).hide();
    jQuery(failedID).hide();

    var uploadUrl = '';
    uploadUrl = (typeof bleeper_override_upload_url !== "undefined" && bleeper_override_upload_url !== "") ? bleeper_override_upload_url : uploadUrl;

    if(fileToUpload.name.indexOf(".php") === -1 && fileToUpload.name.indexOf(".html") === -1 && fileToUpload.name.indexOf(".asp") === -1 && fileToUpload.name.indexOf(".svg") === -1){
        //Files allowed - continue
        if(fileToUpload.size < 8000000){
            jQuery.ajax({
               url : uploadUrl,
               type : 'POST',
               data : formData,
               cache: false,
               processData: false, 
               contentType: false, 
               success : function(data) {
                   if(parseInt(data) !== 0){
                       jQuery(uploadingID).hide();
                       jQuery(successID).show();
                       setTimeout(function(){
                          jQuery(successID).hide();
                          jQuery(originalID).show(); 
                       }, 2000);

                       //All good post the link to file
                       var fileLinkUrl = false;
                       if(!bleeperIsJson(data)){
                           //This is not a parsable JSON string
                           if(typeof data !== "object"){
                            fileLinkUrl = data;
                           } else {
                              if(typeof data.response !== "undefined"){
                                //Our url is in response index
                                fileLinkUrl = data.response;
                              } else {
                                fileLinkUrl = data;
                              }
                           }

                       } else { 
                          //This is a parsable JSON string which will now be converted into an object
                          var dataPacket = JSON.parse(data);
                          if(typeof dataPacket.response !== "undefined"){
                            //Our url is in response index
                            fileLinkUrl = dataPacket.response;
                          } else {
                            fileLinkUrl = data;
                          }
                       }

                       if(fileLinkUrl !== false){
                          var tag = (fileLinkUrl.indexOf(".png") !== -1 || fileLinkUrl.indexOf(".PNG") !== -1 || fileLinkUrl.indexOf(".jpg") !== -1  || fileLinkUrl.indexOf(".JPG") !== -1 || fileLinkUrl.indexOf(".jpeg") !== -1 || fileLinkUrl.indexOf(".JPEG") !== -1 || fileLinkUrl.indexOf(".gif") !== -1 || fileLinkUrl.indexOf(".GIF") !== -1 || fileLinkUrl.indexOf(".bmp")!== -1 || fileLinkUrl.indexOf(".BMP") !== -1 ) ? "img" : "link";
                           
                          if(tag !== "img"){
                              tag = (fileLinkUrl.indexOf(".mp4") !== -1 || fileLinkUrl.indexOf(".mpeg4") !== -1 || fileLinkUrl.indexOf(".webm") !== -1 || fileLinkUrl.indexOf(".oog") !== -1 ) ? "video" : "link"; //video now
                          }
                          addChatMessage({
                            username: username,
                            message: (tag + ":" + fileLinkUrl + ":" + tag)
                          });
                          socket.emit("new message", {message:(tag + ":" + fileLinkUrl + ":" + tag),chatid:active_chatid});

                          jQuery.event.trigger({type: "bleeper_add_message_chatbox", message: (tag + ":" + fileLinkUrl + ":" + tag)});
                          jQuery.event.trigger({type: "bleeper_send_message", message: (tag + ":" + fileLinkUrl + ":" + tag)});
                       }
                   }
                   else{
                       jQuery(uploadingID).hide();
                       jQuery(failedID).show();
                       setTimeout(function(){
                          jQuery(failedID).hide();
                          jQuery(originalID).show(); 
                       }, 2000);

                   }
               },
               error : function (){
                    jQuery(uploadingID).hide();
                    jQuery(failedID).show();
                    setTimeout(function(){
                       jQuery(failedID).hide();
                       jQuery(originalID).show(); 
                    }, 2000);


               }
            });
        }else{
            alert("File limit is 4mb");
            jQuery(uploadingID).hide();
            jQuery(failedID).show();
            setTimeout(function(){
               jQuery(failedID).hide();
               jQuery(originalID).show(); 
            }, 2000);
        }
    } else{
        alert("File type not supported");
        jQuery(uploadingID).hide();
        jQuery(failedID).show();
        setTimeout(function(){
           jQuery(failedID).hide();
           jQuery(originalID).show(); 
        }, 2000);
    }
}

/** 
 * Add a notice to the chat box
 *
 * @param {object} data Chat message data packet
 * @param {object} options Options for the message being added (fade, prepend)
*/
var addNotice = function(data, options) {
  options = options || {};
  var new_item = "";
  if(options.is_admin){
      new_item += "<li class='message wplc-admin-notice wplc-color-bg-3 wplc-color-2 wplc-color-border-3' />";
  } else {
       new_item += "<li class='message wplc-user-notice wplc-color-bg-1 wplc-color-2 wplc-color-border-1' />"; 
  }

  var $messageBodyDiv = jQuery('<span class="noticeBody">')
    .html(wplcFormatParser(data.message));

  var $messageDiv = jQuery(new_item)
    
    .append($messageBodyDiv)

  addMessageElement($messageDiv, options);
}

/**
 * Add a chat message to the chat box container
 *
 * @param {object} data The message data
 * @param {object} options The options for the message
*/
function addChatMessage (data, options) {
  if (typeof bleeper_disable_add_message !== "undefined") {
    /* do nothing as this is being controlled by an integration */
  } else {

    if(typeof data.type !=='undefined' && data.type === 'event'){
      return; //CRM EVENT >> Let's bail
    }

    // Don't fade the message in if there is an 'X was typing'
    var $typingMessages = getTypingMessages(data);
    options = options || {};
    if ($typingMessages.length !== 0) {
      options.fade = false;
      $typingMessages.remove();
    }
    var new_item = "";
    if(data.aoru === 'u'){
        new_item += "<li class='message wplc-user-message wplc-color-bg-1 wplc-color-2 wplc-color-border-1 message_"+data.msgID+"' mid='"+data.msgID+"' />"; 
    } else {
        new_item += "<li class='message wplc-admin-message wplc-color-bg-3 wplc-color-2 wplc-color-border-3 message_"+data.msgID+"' mid='"+data.msgID+"' />";
    }


    if (typeof data.timestamp !== "undefined") {
      var tdate = new Date(parseInt(data.timestamp));
      var hours = tdate.getHours();
      var minutes = tdate.getMinutes();
      var ampm = hours >= 12 ? 'pm' : 'am';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
      var strTime = hours + ':' + minutes + ' ' + ampm;
      data.timestamp = strTime;

    } else {
      data.timestamp = "";
    }

    var $usernameDiv = jQuery('<span class="username"/>: ')
      .text(bleeper_strip_tags(data.username));

    var $timeDiv = jQuery('<span class="timestamp"/>: ')
      .text(data.timestamp);


    var edit_item = "";
    if(data.aoru !== 'u'){
       var $editDiv = jQuery('<span class="bleeper-edit-message" style="display:none"/>: ').text('edit');
    } else {
       var $editDiv = jQuery('<span class=""/>: ').text('');
    }

    var $messageBodyDiv = jQuery('<span class="messageBody" data-message="' + data.message + '">')
      .html(wplcFormatParser(data.message));

    if(data.aoru !== 'u'){
      var $messageReadBody = jQuery('<span class="messageReadReceipt" id="read_receipt_'+data.msgID+'">').html('Sent');
    } else {
      var $messageReadBody = '';
    }

    var typingClass = data.typing ? 'typing' : '';
    var $is_typing_preview = data.typing ? jQuery("<span id='typing_preview'>") : '';
    var $messageDiv = jQuery(new_item)
      .data('username', bleeper_strip_tags(data.username))
      .addClass(typingClass)
      .append($usernameDiv, $timeDiv, $messageReadBody, $editDiv, $messageBodyDiv )
      .append(" ")
      .append($is_typing_preview);

    addMessageElement($messageDiv, options);
    jQuery.event.trigger({type: "bleeper_check_for_read_receipt_on_adding_message", ndata: data });
    
    socket.emit('message read',data);          

  }

}

/**
 * Add is typing to chat box
 *
 * @param {object} data Data to check
*/
function addChatTyping (data) {
  data.typing = true;
  data.message = 'is typing';
  addChatMessage(data);
}

/**
 * Remove any remaining 'typing messages'
 *
 * @param {object} data Data to check
*/
function removeChatTyping (data) {
  getTypingMessages(data).fadeOut(function () {
    jQuery(this).remove();
  });
}

/**
 * Add a message elemtn to the document. Mostly used for events as WPLC will handle message appending
 *
 * @param {element} el The element to add to the chat box
 * @param {object} options Options for the message being added (fade, prepend)
*/
function addMessageElement (el, options) {
  try{
    var $el = jQuery(el);
    // Setup default options
    if (!options) {
      options = {};
    }
    if (typeof options.fade === 'undefined') {
      options.fade = true;
    }
    if (typeof options.prepend === 'undefined') {
      options.prepend = false;
    }

    // Apply options
    if (options.fade) {
      $el.hide().fadeIn(FADE_TIME);
    }
    if (options.prepend) {
      $messages.prepend($el);
    } else {
      $messages.append($el);
    }
    if(typeof $messages !== "undefined" && typeof $messages[0] !== "undefined" && typeof $messages[0].scrollHeight !== "undefined"){
      $messages[0].scrollTop = $messages[0].scrollHeight;
    }
  } catch (ex){
    
  }
}

/**
 * JS Based cleanup to prevent injected code snippets from ever making it to the server. 
 * 
 * The server will also handle some cleanup in this regard
 *
 * @param {string} input The content to clean
 * @return {string} The clean content 
*/
function cleanInput (input) {
  var input_cleaned = input;
  if(typeof input_cleaned !== 'string'){
    return input_cleaned;
  }
  return input_cleaned.replace(/<(?:.|\n)*?>/gm, '');
}
  
/**
 * Update the typing statu on the socket
*/
function updateTyping () {
  if (connected) {
    if (!niftyIsEditing) {
      if (!typing) {
        typing = true;
        if (message_type === 'u') {
          socket.emit('typing',{chatid:active_chatid});
        } else {
          socket.emit('typing',{chatid:active_chatid,agent:active_socket});
        }
        
      }
      lastTypingTime = (new Date()).getTime();

      setTimeout(function () {
        var typingTimer = (new Date()).getTime();
        var timeDiff = typingTimer - lastTypingTime;
        if (timeDiff >= TYPING_TIMER_LENGTH && typing) {
          
          if (message_type === 'u') {
            socket.emit('stop typing',{chatid:active_chatid});
          } else {
            socket.emit('stop typing',{chatid:active_chatid,agent:active_socket});
          }
          
          typing = false;
        }
      }, TYPING_TIMER_LENGTH);
    }
  }
}

/**
 * Remove any remaining 'typing messages'
 *
 * @param {object} data Data to check
*/
function getTypingMessages (data) {
  return jQuery('.typing.message').filter(function (i) {
    return jQuery(this).data('username') === data.username;
  });
}


/**
 * Add the agent
 * 
 * @param {socket} socket The socket object
 * @param {object} data The agent data packet
*/
var nc_add_agent = function(socket, data) {
  var data = {};
  /* recurring visitor */
  if (typeof nc_sid !== "undefined") {
    if (typeof nc_name !== "undefined") { data.username = nc_name; } else { data.username = 'Agent'; }
    data.api_key = nifty_api_key;
    data.sid = null;
  } else {
    /* first time user */
    data.username = bleeper_agent_name;
    data.api_key = nifty_api_key;
    data.agent_id = agent_id;
    data.sid = null;
  }
  jQuery.event.trigger({type: "bleeper_add_agent", ndata:data});
}

/**
 * Get a specific cookie by name
 *
 * @param {string} name The name of the cookie
 * @return {string} The value of the cookie
*/
function nc_getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

var bleeper_random_offline_image_count = Math.floor((Math.random() * 9) + 1);
/**
 * Show the no visitors image
*/
function nc_no_visitors() {
  var random_img = "<img id='bleeper_offline_image' src='" + wplc_baseurl + "/images/random_images/offline_images/" + bleeper_random_offline_image_count + ".jpg' />";

  jQuery("#active_count").html("0");
  jQuery(".nifty_bg_holder_text").html(random_img + "There are no visitors on your site at the moment");
  jQuery(".nifty_bg_holder").css('left','25%');

  /* only fade in if were not chatting with an agent, or if we do not have a completed chat open */
  if (typeof active_chatid === "undefined") {
    jQuery(".nifty_bg_holder").fadeIn();
  }

  


}

/**
 * Update the visitor counter
 * 
 * @param {int} count The new visitor count
*/
function nc_update_visitor_count(count) {
  jQuery("#active_count").html(document.querySelectorAll('.visitorList').length);
}

/**
 * Add the accept chat button to the visitor row
 *
 * @param {string} chatid The Chat ID
*/
function nc_add_accept_chat_visitor_row(chatid) {
  if(typeof bleeper_io_is_origin !== "undefined" && bleeper_io_is_origin === true){
    nc_add_open_chat_visitor_row(chatid);
  } else {
    if ( jQuery("#vis"+chatid+" .visActionCol .accept_chat").length === 0 ) {
      jQuery('<div/>', {
        'class': '',
        html: "<a href='javascript:void(0);' class='accept_chat btn btn-danger pull-right' cid='"+chatid+"'>Accept Chat</a>"
      }).appendTo('#vis'+chatid+" .visActionCol");
      nc_remove_init_chat_visitor_row(chatid);
    }
  }
}

/**
 * Add the open chat button to the visitor row
 *
 * @param {string} chatid The Chat ID
*/
function nc_add_open_chat_visitor_row(chatid) {
  jQuery(".nifty_bg_holder_text").html("");
  jQuery(".nifty_bg_holder").fadeOut();

  if ( jQuery(".open_chat_"+chatid).length > 0 ) { } else {
    jQuery('<div/>', {
      'class': '',
      html: "<a href='javascript:void(0);' class='open_chat open_chat_"+chatid+"  btn btn-success pull-right' cid='"+chatid+"'>Open Chat</a>"
    }).appendTo('#vis'+chatid + " .visActionCol");
  }
}
/**
 * Remove the open chat button from the visitor row
 *
 * @param {string} chatid The Chat ID
*/
function nc_remove_open_chat_visitor_row(chatid) { jQuery("#vis"+chatid+" .open_chat").remove(); }

/**
 * Remove the accept chat button from the visitor row
 *
 * @param {string} chatid The Chat ID
*/
function nc_remove_accept_chat_visitor_row(chatid) { 
  jQuery("#vis"+chatid+" .accept_chat").remove();
  bleeper_ring.pause();
  if(!isNaN(bleeper_ring.duration)){
  	bleeper_ring.currentTime = 0;
  }

  wplc_new_chat_ringer_dismissed = true;
 }

/**
 * Remove the init chat button from the visitor row
 *
 * @param {string} chatid The Chat ID
*/
function nc_remove_init_chat_visitor_row(chatid) { 
  jQuery("#vis"+chatid+" .init_chat").remove();  
  jQuery.event.trigger({type: "bleeper_remove_initiate_button", cid:chatid});
}

/**
 * Clears the action column for the specific chat ID (visitor)
 *
 * @param {string} chatid The Chat ID
*/
function nc_clear_action_col_visitor_row(chatid) { 
  jQuery("#vis"+chatid+" .visActionCol").html('');
  
  /**
   * TO DO
   * Check if there are any OTHER visitors that are wanting to chat (i.e. use jQuery to run through all buttons).
   * If there are still .accept_chat's present, then dont stop the bleeper_ring.
   */
  bleeper_ring.pause();
  if(!isNaN(bleeper_ring.duration)){
  	bleeper_ring.currentTime = 0;
  }
  wplc_new_chat_ringer_dismissed = true;
  
}

/**
 * Add user to the user list
 *
 * @param {object} data The user data
 * @param {function} next Callback to fire after this has completed
*/
function nc_add_user_to_list(data, next) {
  if (typeof data === "undefined")
    return;


  var date = new Date();
  var minute = date.getMinutes();
  var hour = date.getHours();
  
  /* update the visitor table */
  jQuery("#vis"+data.chatid+" .session-state-label").html(bleeper_pretty_chat_status(data.state));
  
  nc_clear_action_col_visitor_row(data.chatid);
  nc_add_open_chat_visitor_row(data.chatid);

  /* add to the userList */
  if (jQuery("#"+data.chatid).length) {
    nc_update_user_to_list(data);
  } else {

    jQuery('<div/>', {
      id: data.chatid,
      'class': 'userList',
    })
    .css('display','none')
    .attr('socket', data.socketid)
    .attr('customerID', data.customerID)
    .appendTo('.userListBox');
 
    var t_em = typeof data.email !== "undefined" ? md5(data.email) : '';

    jQuery('<img/>', {
      'src' : 'https://www.gravatar.com/avatar/'+t_em+'?s=24&d=mm',
      'class': 'bleeper_userlist_gravatar bleeper_userlist_gravatar_'+data.chatid
    }).appendTo('#'+data.chatid);

    jQuery('<h3/>', {
      html: "<span class='userName'>"+bleeper_strip_tags( (typeof data.username !== "undefined" ? data.username : "Guest") )+'</span>'
    }).appendTo('#'+data.chatid);
    jQuery('<span/>', {
      text: 'x',
      "class": 'bleeper_close_item bleeper_close_item_'+data.chatid,
      "cid": data.chatid
    }).appendTo('#'+data.chatid);
    
    jQuery('<p/>', {
      text: hour + ":" + minute,
      "class" : "userInfo"
    }).appendTo('#'+data.chatid);  

    jQuery('<p/>', {
      text: "",
      "class" : "userItemLastMessage"
    })
    .css('display','block')
    .appendTo('#'+data.chatid);  

    jQuery('<p/>', {
      text: "",
      "class" : "unread_count"
    })
    .css('display','none')
    .appendTo('#'+data.chatid);  

    jQuery('<div/>', {
      text: "",
      "class" : "agents_involved"
    })
    .css('display','block')
    .appendTo('#'+data.chatid);  

    if (typeof bleeper_departments !== "undefined") {
      if (typeof data.department !== 'undefined') {
        if (data.department !== 'any') {
          jQuery('<div/>', {
            text: (typeof data.department !== "undefined" ? bleeper_departments[data.department] : bleeper_default_department_tag),
            "class" : "department_assigned"
          })
          .css('display','block')
          .appendTo('#'+data.chatid);  
        }
      }
    }

    jQuery("#"+data.chatid).fadeIn("slow");

  }
  
  next(data);
}

/**
 * Update a user in the user list
 *
 * @param {object} data The user data
*/
function nc_update_user_to_list(data) {
  var date = new Date();
  var minute = date.getMinutes();
  var hour = date.getHours();
  
   jQuery("#"+data.chatid+ " .userList").attr("socket",data.socketid);
   jQuery("#"+data.chatid+ " .userInfo").html(hour + ":" + minute);

}

/**
 * Update a visitor in the visitor list
 *
 * @param {object} data The visitor data
*/
function nc_update_visitor_to_list(data) {
  var date = new Date();
  var minute = date.getMinutes();
  var hour = date.getHours();
  
  jQuery("#"+data.chatid+ " .visitorList").attr("socket",data.socketid);
  jQuery("#"+data.chatid+ " .visitorInfo").html(hour + ":" + minute);
}

/**
 * Add visitor to the visitor list
 * 
 * @param {object} data The visitor data
*/
function nc_add_visitor_to_list(data) {
  jQuery(".nifty_bg_holder_text").html("");
  jQuery(".nifty_bg_holder").fadeOut();
  if (jQuery("#vis"+data.chatid).length) {
    nc_update_visitor_to_list(data);
  } else {

    jQuery('<div/>', {
      id: "vis"+data.chatid,
      'class': 'visitorList',
    })
    .css('display','none')
    .attr('socket', data.socketid)
    .appendTo('.visitorListBox');

    jQuery('<div/>', {
      'class': 'vcol visCol',
      html: "<span class='userName'>"+bleeper_strip_tags((typeof data.username !== "undefined" ? data.username : "Guest"))+"</span>"
    }).appendTo('#vis'+data.chatid);
    
    jQuery('<div/>', {
      'class': 'vcol visStatusCol',
      html: "<span class='info_chat' cid='"+data.chatid+"' >" + nc_return_browser_details(data) + " " + nc_return_location_details(data) + " " + nc_return_device_details(data) + bleeper_return_visitor_os_data(data) + "</span>"
    }).appendTo('#vis'+data.chatid);  

    jQuery('<div/>', {
      'class': 'vcol visPageCol',
      html: "<a href='"+bleeper_strip_tags(data.referer)+"' class='referer-link truncate' target='_BLANK'>"+bleeper_strip_tags(data.referer)+"</a>"+bleeper_return_visitor_lifetime_data(data)+"<br/>"+bleeper_return_visitor_timezone_data(data)+bleeper_return_campaign_data(bleeper_strip_tags(data.referer))+"</em>"
    }).appendTo('#vis'+data.chatid);  

    jQuery('<div/>', {
      'class': 'vcol visChatStatusCol',
      html: "<label class='label-default session-state-label'>" + bleeper_pretty_chat_status(data.state) + "</label><div class='agents_involved_visitor'></div>"
    }).appendTo('#vis'+data.chatid); 

    if(typeof bleeper_departments !== "undefined"){
      jQuery('<div/>', {
        'class': 'vcol visChatDepCol',
        html: "<label class='label-default'>" + (typeof data.department !== "undefined" && typeof bleeper_departments[ data.department ] !== "undefined" ? bleeper_departments[ data.department ]  : bleeper_default_department_tag) + "</label>"
      }).appendTo('#vis'+data.chatid); 
    }   

    jQuery('<div/>', {
      'class': 'vcol visActionCol',
      html: ""
    }).appendTo('#vis'+data.chatid); 
    
    jQuery.event.trigger({type: "bleeper_add_initiate_button_no_col", cid:data.chatid});  

    var show_based_on_filter = !bleeper_hide_visitor_by_filter(data); //Set it to the oposite filter
    var show_based_on_domain_filter = !bleeper_hide_visitor_by_domain_filter(data);


    if(show_based_on_filter && show_based_on_domain_filter){
        jQuery.event.trigger({type: "bleeper_new_visitor", cid:data.chatid});  
        jQuery("#vis"+data.chatid).fadeIn("slow");
    }
  }
}

/**
 * Update the visitor list 
 *
 * @param {object} data The visitor list object
*/
function nc_update_visitor_to_list(data) {
  var date = new Date();
  var minute = date.getMinutes();
  var hour = date.getHours();
  jQuery("#vis"+data.chatid+ " .visitorList").attr("socket",data.socketid);
  jQuery("#vis"+data.chatid+ " .session-state-label").html(bleeper_pretty_chat_status(data.state));
  jQuery("#vis"+data.chatid+ " .visPageCol").html("<a href='"+bleeper_strip_tags(data.referer)+"' class='referer-link truncate' target='_BLANK'>"+bleeper_strip_tags(data.referer)+"</a>"+bleeper_return_visitor_lifetime_data(data)+"<br/>"+bleeper_return_visitor_timezone_data(data)+bleeper_return_campaign_data(bleeper_strip_tags(data.referer))+"</em>");

  jQuery("#vis"+data.chatid+ " .visitorInfo").html(hour + ":" + minute);

  if(typeof bleeper_departments !== "undefined"){
     jQuery("#vis"+data.chatid+ " .visChatDepCol").html("<label class='label-default'>" + (typeof data.department !== "undefined" && typeof bleeper_departments[ data.department ] !== "undefined" ? bleeper_departments[ data.department ]  : bleeper_default_department_tag) + "</label>");
  } 

  var hide_based_on_filter = bleeper_hide_visitor_by_filter(data); //Set it to the oposite filter
  var hide_based_on_domain_filter = bleeper_hide_visitor_by_domain_filter(data); //Set it to the oposite filter

  var accept_chat_present = false;

  if (typeof data.state !== "undefined" && data.state === 'browsing' ) {
      
      if ( jQuery("#vis"+data.chatid+" .visActionCol .accept_chat").length > 0 ) {
        accept_chat_present = true;
      }
      
      if (accept_chat_present) {
        jQuery("#vis"+data.chatid+" .visActionCol").html('');
        nc_add_accept_chat_visitor_row(data.chatid);
      } else {
        bleeper_ring.pause();
        if(!isNaN(bleeper_ring.duration)){
        	bleeper_ring.currentTime = 0;
        }
        wplc_new_chat_ringer_dismissed = true;
        

        /* Add the INIT button if it is NOT there */
        if ( jQuery("#vis"+data.chatid+" .visActionCol .init_chat").length > 0 || jQuery("#vis"+data.chatid+" .visActionCol .wplc_initiate_chat").length > 0  ) { } else {
          jQuery("#vis"+data.chatid+" .visActionCol").html('');
          jQuery.event.trigger({type: "bleeper_add_initiate_button_no_col", cid:data.chatid});  
        }
      }
  }

  var show_based_on_filter = !bleeper_hide_visitor_by_filter(data); //Set it to the oposite filter
  var show_based_on_domain_filter = !bleeper_hide_visitor_by_domain_filter(data);
  
  if(show_based_on_filter && show_based_on_domain_filter){
    jQuery("#vis"+data.chatid).fadeIn("slow");
  } else {
    jQuery("#vis"+data.chatid).fadeOut("slow");
  }
}

/**
 * Hide visitors based on a domain
 *
 * @param {object} data The visitor data package
*/
function bleeper_hide_visitor_by_domain_filter(data) {
    /* are we filtering domains? */
    var passed_domain = jQuery(".bleeper_domain_selector").val();
    
    var vis_id = data.chatid;
    if (typeof passed_domain !== 'undefined' && passed_domain !== undefined && passed_domain !== 'false') {
      if(typeof visitor_list !=='undefined' && typeof visitor_list[vis_id] !== 'undefined'){
        if(typeof visitor_list[vis_id]['referer'] !== 'undefined'){
          var ref = visitor_list[vis_id]['referer'];
          if(ref.indexOf(passed_domain) !== -1){
            return false;
          } else {
            return true;
          }
        } else {
          return true;
        }
      } else {
        return true;
      }
    } else {
      return false;
    }
}

/**
 * Update the unread count for a specific chat ID
 *
 * @param {string} chatid The chat ID
*/
function nc_update_unread_count(chatid) {
  if (typeof unread_count[chatid] === "undefined" || unread_count[chatid] < 1) {
    jQuery("#"+chatid+ " .unread_count").hide();
  } else { 
    jQuery("#"+chatid+ " .unread_count").show();
    jQuery("#"+chatid+ " .unread_count").html(unread_count[chatid]);
  }
  bleeper_check_if_all_read();
}

/**
 * Check if all unread messages have been read
*/
function bleeper_check_if_all_read() {
  var tot_unread = 0;
  for (k in unread_count) {
    if (parseInt(unread_count[k]) > 0) {
      tot_unread = tot_unread + parseInt(unread_count[k]);
    }
  }
  if (tot_unread > 0) {
    bleeper_change_favico(bleeper_favico_noti);
  }
}

/**
 * Update the last message from the visitor in the list
 *
 * @param {string} chatid The Chat ID
 * @param {object} message The message data packet
 * @param {bool} update_user_list Should we update the user list...
*/
function nc_update_last_message(chatid, message, update_user_list) {
  if (typeof message.date !== "undefined") {
    var date = new Date(message.date);
  } else {
    var date = new Date();
  }
  var minute = date.getMinutes();
  var hour = date.getHours();
  jQuery("#"+chatid+ " .visitorInfo").html(hour + ":" + minute);
  jQuery("#vis"+chatid+" .session-state-label").html("active");
  jQuery("#"+chatid+ " .userItemLastMessage").html(wplcFormatParser(message.message));
  last_chat_messages[chatid] = message.message;

  /* add this message to sessionStorage */
  bleeper_append_message_to_ls(chatid, message);

  
  /* set this chat as active if it isnt already ... */
  if (update_user_list) {
    /* first check if this agent is involved in the chat, else do not add to the left chat list */
    if (typeof involved_list[chatid] !== "undefined" && involved_list[chatid] === true) {
      nc_add_user_to_list(visitor_list[chatid], function(data){

      });
    }
  }
}

/**
 * Test to make sure sessionStorage exists and is enabled
 *
 * @return {bool} True if session storage is available
*/
function bleeper_test_sessionStorage() {
  if (typeof sessionStorage !== 'undefined') {
    try {
        sessionStorage.setItem('bleeper_test', 'yes');
        if (sessionStorage.getItem('bleeper_test') === 'yes') {
            sessionStorage.removeItem('bleeper_test');
            return true;
            // sessionStorage is enabled
        } else {
            return false;
            // sessionStorage is disabled
        }
    } catch(e) {
        return false;
        // sessionStorage is disabled
    }
  } else {
    return false;
    // sessionStorage is not available
  }
}

/**
 * Update the event box for the current chat ID
 *
 * @param {string} chatid The Chat ID
*/
function bleeper_update_event_box(chatid) {
  if (bleeper_test_sessionStorage()) {
    if (sessionStorage.getItem(chatid+"_e") !== null) {
      var msg_obj = JSON.parse(sessionStorage.getItem(chatid+"_e"));
      for (key in msg_obj) {
        bleeper_add_event_to_eventbox(msg_obj[key].icon, msg_obj[key].desc, [key]);
      }
    }

  }
}

/**
 * Add a message to session storage
 *
 * @param {string} chatid The chat ID
 * @param {object} message_obj The message object
*/
function bleeper_add_message_to_sessionStorage(chatid, message_obj) {
    if (bleeper_test_sessionStorage()) {

    var wplc_d = new Date();
    var cdatetime = wplc_d.getTime();

    if (sessionStorage.getItem(chatid+"_m") !== null) {
      var msg_obj = JSON.parse(sessionStorage.getItem(chatid+"_m"));
      msg_obj[cdatetime] = message_obj;
      sessionStorage.setItem(chatid+"_m", JSON.stringify(msg_obj));

    } else {
      var msg_obj = {};
      msg_obj[cdatetime] = message_obj;
      sessionStorage.setItem(chatid+"_m", JSON.stringify(msg_obj));
    }

  }
}

/**
 * Save and event to local storage and add it to the event box
 *
 * @param {string} chatid The chat ID
 * @param {string} icon The font awesome icon to be used
 * @param {string} desc Label for the event to be shown in the panel
 * @param {object} data The chat data packet
 */
function bleeper_save_event(chatid, icon, desc, data) {
  if (bleeper_test_sessionStorage()) {

    var wplc_d = new Date();
    var cdatetime = Math.round( wplc_d.getTime() / 1000 );

    if (sessionStorage.getItem(chatid+"_e") !== null) {
      var msg_obj = JSON.parse(sessionStorage.getItem(chatid+"_e"));
      msg_obj[cdatetime] = {};
      msg_obj[cdatetime].desc = desc;
      msg_obj[cdatetime].icon = icon;
      msg_obj[cdatetime].data = data;
      sessionStorage.setItem(chatid+"_e", JSON.stringify(msg_obj));

    } else {
      var msg_obj = {};
      msg_obj[cdatetime] = {};
      msg_obj[cdatetime].desc = desc;
      msg_obj[cdatetime].icon = icon;
      msg_obj[cdatetime].data = data;
      sessionStorage.setItem(chatid+"_e", JSON.stringify(msg_obj));
    }

    if (active_chatid === chatid) {
      bleeper_add_event_to_eventbox(icon, desc, cdatetime);
    }

  }
}

/**
 * Remove events from the session storage, for a specific Chat ID
 *
 * @param {string} chatid The Chat ID
*/
function bleeper_remove_events(chatid) {
  if (bleeper_test_sessionStorage()) {
    sessionStorage.removeItem(chatid+"_e");
  }
}

/**
 * Add an event element to the events panel
 *
 * @param {string} icon The font awesome icon to be used
 * @param {string} desc Label for the event to be shown in the panel
 * @param {string} datetime Date/Time of the event
*/
function bleeper_add_event_to_eventbox(icon, desc, datetime) {
  jQuery('<li/>', {
      id: "event-"+datetime,
    })
    .prependTo('.events-ul');

    jQuery('<div/>', {
      'class': 'event-icon',
      html: "<i class='fa "+icon+"'></i>"
    }).appendTo('#event-'+datetime);

    jQuery('<div/>', {
      'class': 'event-desc',
      html: desc
    }).appendTo('#event-'+datetime);

    jQuery('<div/>', {
      'class': 'event-meta',
      html: bleeper_date_converter(datetime)
    }).appendTo('#event-'+datetime);    

}

/**
 * Remove all messages for specific chat from local storage
 *
 * @param {string} chatid The chat ID
*/
function bleeper_remove_chatid_from_ls(chatid) {
  if (bleeper_test_sessionStorage()) {
    sessionStorage.removeItem(chatid+"_m");
  }
}

/**
 * Add message to local storage
 *
 * @param {string} chatid The chat ID
 * @param {string} lmessage The message data object
*/
function bleeper_append_message_to_ls(chatid, lmessage) {
  if (bleeper_test_sessionStorage()) {

    var new_msg = {};
    if(typeof lmessage !== "undefined" && typeof lmessage.msgID !== "undefined"){
      new_msg.mid = lmessage.msgID.toString();
    }
    new_msg.originates = lmessage.aoru === "u" ? "2" : "1";
    new_msg.msg = lmessage.message;
    new_msg.other = {};
    if (new_msg.originates === "1") {
      if (typeof lmessage.aoru !== "undefined") {
        new_msg.other.aid = lmessage.aoru;  
      }
      if (typeof lmessage.aid !== "undefined") {
        new_msg.other.aid = lmessage.aid;  
      }

    }
    
    var wplc_d = new Date();
    new_msg.other.datetime = Math.round( wplc_d.getTime() / 1000 );

    if (sessionStorage.getItem(chatid+"_m") !== null) {
      var msg_obj = JSON.parse(sessionStorage.getItem(chatid+"_m"));
      msg_obj[lmessage.msgID] = new_msg;
      sessionStorage.setItem(chatid+"_m", JSON.stringify(msg_obj));

    } else {
      var msg_obj = {};
      msg_obj[lmessage.msgID] = new_msg;
      sessionStorage.setItem(chatid+"_m", JSON.stringify(msg_obj));
    }
  }
}

/**
 * Creates a pretty-print date from a UNIX timestamp
 *
 * @param {date} UNIX_timestamp The UNIX timestamp
 * @return {string} Pretty printed date
*/
function bleeper_date_converter(UNIX_timestamp) {
  var a = new Date(UNIX_timestamp * 1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var month = months[a.getMonth()];
  var date = a.getDate();
  var hour = a.getHours();
  var min = a.getMinutes() < 10 ? '0' + a.getMinutes() : a.getMinutes();
  var sec = sec = a.getSeconds() < 10 ? '0' + a.getSeconds() : a.getSeconds();
  var time = date + ' ' + month + ' ' + hour + ':' + min + ':' + sec ;
  return time;
}

/**
 * Updates the user typing flag
 *
 * @param {string} chatid The chat ID
*/
function nc_update_user_typing(chatid) {
  last_chat_messages[chatid] = jQuery("#vis"+chatid+ " .session-state-label").html();
  jQuery("#"+chatid+ " .userItemLastMessage").html('<label class="label-default session-state-label">'+wplc_admin_strings.typing_string+'</label>');
}

/**
 * Removes the user typing content
 *
 * @param {string} chatid The chat ID 
*/
function nc_update_user_stop_typing(chatid) {
  if (typeof last_chat_messages[chatid] !== "undefined") {
    jQuery("#"+chatid+ " .userItemLastMessage").html(wplcFormatParser(last_chat_messages[chatid])); 
  }
}

/**
 * Process all inline formatting (For example: bold, italic, preformatted, etc) within a message
 * 
 * @param {string} msg The chat message
 * @return {string} The formatted message
*/
wplcFormatParser = function(msg){
  var bypass_inline_links = false;

  //Handled by inline link generator
    if(msg.indexOf("link:") !== -1){
        msg = msg.replace(/link:/g, "");
    }
    if(msg.indexOf(":link") !== -1){
        msg = msg.replace(/:link/g, "");
    }

     if(msg.indexOf("video:") !== -1){
         msg = msg.replace(/video:/g, "<video style='background-color: black; max-width:100%;' src='");
         bypass_inline_links = true;
    }
    if(msg.indexOf(":video") !== -1){
        msg = msg.replace(/:video/g, "' controls></video>");
        bypass_inline_links = true;
    }

     if(msg.indexOf("vid:") !== -1){
         msg = msg.replace(/vid:/g, "<video style='background-color: black; max-width:100%;' src='");
         bypass_inline_links = true;
    }
    if(msg.indexOf(":vid") !== -1){
        msg = msg.replace(/:vid/g, "' controls></video>");
        bypass_inline_links = true;
    }
    
    if(msg.indexOf("mark:") !== -1){
         msg = msg.replace(/mark:/g, "<mark>");
    }
    if(msg.indexOf(":mark") !== -1){
        msg = msg.replace(/:mark/g, "</mark>");
    }
    
    if(msg.indexOf("strike:") !== -1){
         msg = msg.replace(/strike:/g, "<del>");
    }
    if(msg.indexOf(":strike") !== -1){
        msg = msg.replace(/:strike/g, "</del>");
    }
    
    if(msg.indexOf("sub:") !== -1){
         msg = msg.replace(/sub:/g, "<sub>");
    }
    if(msg.indexOf(":sub") !== -1){
        msg = msg.replace(/:sub/g, "</sub>");
    }
    if(msg.indexOf("sup:") !== -1){
         msg = msg.replace(/sup:/g, "<sup>");
    }
    if(msg.indexOf(":sup") !== -1){
        msg = msg.replace(/:sup/g, "</sup>");
    }

    // Fix double emojis
    if(msg.search(/\:(\S+)(\:)(\S+)\:/g) !== -1){
        msg = msg.replace(/\:(\S+)(\:)(\S+)\:/g, function (match, p1, p2, p3) {
            return [":", p1, "::", p3, ":"].join('');
        });
    }

    msg = bleeper_emoji_render(msg); //First render emojis before processing italics
    var italics_match = msg.match(/_([^*]*?)_/g);
    if (italics_match !== null) {
      for (var i = 0, len = italics_match.length; i < len; i++) {
        var to_find = italics_match[i];
        var to_replace = to_find.substring(1, to_find.length-1); // remove the starting _ and ending _
        msg = msg.replace(to_find, "<em>"+to_replace+"</em>");
        
      }
    } 

    /* New IMG processor */
    var image_match = msg.match(/img:([^*]*?):img/g);
    if (image_match !== null) {
      for (var i = 0, len = image_match.length; i < len; i++) {
        var to_find = image_match[i];
        var to_replace = to_find.substring(4, to_find.length-4); // remove the starting #### and ending ####
        msg = msg.replace(to_find, "<img style='max-width:100%;' src='"+bleeper_url_path_em_stripper(to_replace.replace("::", ":"))+"' />");
        bypass_inline_links = true;
        
      }
    }   

    var image_match = msg.match(/####([^*]*?)####/g);
    if (image_match !== null) {
      for (var i = 0, len = image_match.length; i < len; i++) {
        var to_find = image_match[i];
        var to_replace = to_find.substring(4, to_find.length-4); // remove the starting #### and ending ####
        msg = msg.replace(to_find, "<img style='max-width:100%;' src='"+bleeper_url_path_em_stripper(to_replace)+"' />");
        bypass_inline_links = true;
        
      }
    }    

    var bold_match = msg.match(/\*\s*([^*]*?)\s*\*/g);
    if (bold_match !== null) {
      for (var i = 0, len = bold_match.length; i < len; i++) {
        var to_find = bold_match[i];
        var to_replace = to_find.substring(1, to_find.length-1); // remove the starting * and ending *
        msg = msg.replace(to_find, "<strong>"+to_replace+"</strong>");
        
      }
    }       

    var pre_match = msg.match(/```([^*]*?)```/g);
    if (pre_match !== null) {
      for (var i = 0, len = pre_match.length; i < len; i++) {
        var to_find = pre_match[i];
        var to_replace = to_find.substring(3, to_find.length-3); // remove the starting ``` and ending ```
        msg = msg.replace(to_find, "<pre>"+to_replace+"</pre>");
        
      }
    } 

    var code_match = msg.match(/`([^*]*?)`/g);
    if (code_match !== null) {
      for (var i = 0, len = code_match.length; i < len; i++) {
        var to_find = code_match[i];
        var to_replace = to_find.substring(1, to_find.length-1); // remove the starting ` and ending `
        msg = msg.replace(to_find, "<code>"+to_replace+"</code>");
        
      }
    } 

    msg = msg.replace(/\n/g, "<br />");
    if(bypass_inline_links === false){
      msg = bleeper_inline_link_generator(msg);
    }

    return msg;
}

/**
 * Get the visitors browser icon
 *
 * @param {object} data The visitor data packet
 * @return {string} HTML showing the browser icon
*/
function nc_return_browser_details(data) {
  if (typeof data === "undefined") {
    return "";
  }

  if (typeof data.browserName !== "undefined") { browserName = data.browserName; } else { browserName = "Unknown"; }
  if (typeof data.browserVersion !== "undefined") { browserVer = data.browserVersion; } else { browserVer = "Unknown"; }

  origbrowserName = browserName;
  browserName = browserName.toLowerCase();

  return "<img src='" + wplc_baseurl + "/images/browsers/"+browserName+"_16x16.png' alt='"+origbrowserName+" (v"+browserVer+")' title='"+origbrowserName+" (v"+browserVer+")' />";
}

/**
 * Get the visitors location icon - (REQUIRE MAXIND TO BE ENABLED)
 *
 * @param {object} data The visitor packet data
 * @return {string} HTML showing the country flag 
*/
function nc_return_location_details(data){
  if(typeof data !== "undefined" && typeof data.location_info !== "undefined"){
    if (typeof data.location_info === 'string') {
      if (bleeperIsJson(data.location_info)) {
          data.location_info = JSON.parse(data.location_info);
      } else {
        data.location_info = undefined;
      }
    }
    if(typeof data.location_info !== 'undefined' && typeof data.location_info.code !== "undefined"){
      var location_code = data.location_info.code;
      var location_name = (typeof data.location_info.name !== "undefined" ? data.location_info.name : "");
      
      return "<img src='" + wplc_baseurl + "/images/flags/"+location_code.toLowerCase()+".png' alt='"+location_name+"' title='"+location_name+"' />";
    }    
  }

  return "";
}

/**
 * Get the device image for this visitor
 * 
 * @param {object} data The visitor packet data
 * @return {string} HTML of the image for the relevant device
*/
function nc_return_device_details(data){
  if(typeof data !== "undefined" && typeof data.device_in_use !== "undefined"){
      var device_in_use = data.device_in_use;
      return "<img src='" + wplc_baseurl + "/images/devices/"+device_in_use+".png' alt='"+device_in_use+"' title='"+device_in_use.charAt(0).toUpperCase() + device_in_use.slice(1)+"' />";
  }
  return "";
}


/**
 * Adds the visitor that just left to a unique timer and then removes that user if they havent come back in x seconds
 * 
 * @param  {object} data The visitor data packet
 */
function nc_remove_visitor_after_time(data) {
  var visitor_timeout_seconds = 12000; /* one second less than the a2vping */
  clearTimeout(remove_visitor_timer[data.chatid]);

  remove_visitor_timer[data.chatid] = setTimeout(function() {
    jQuery.event.trigger({type: "bleeper_remove_visitor", ndata:data});
    
    clearTimeout(ping_list[data.socketid]);
  },visitor_timeout_seconds);
  
}


var bleeper_update_agent_unread = function() {
  
}

/**
 * Check the latency of the connection
*/
var bleeper_latency_check = function() {
    bleepblop_running = true;
    startTime = Date.now();
    socket.emit('bleep');

    latency_dead_check = setTimeout(function() {
      bleepblop_running = false;
      jQuery(".bleeper_stats").html(" [<em><span style='color:red'>Latency: You are offline.</span></em>] ");
    },3100);
}


/**
 * WPLC SOCKET delegate functions
 * 
 * @return void
 */
bleeper_delegates = function() {
  // Socket events
  socket.on('connect', function(data) {
    connected = true;
    nc_add_agent(socket, data);

    if (get_visitor_timer === undefined) {
      var get_visitor_timer = setInterval(function() {
        bleeper_get_visitor_data();
      },30000);
    }

    /**
     * Latency checks
     */
    if (bleepblop_running === false) {
      bleeper_latency_check();
    }

    socket.emit('get online agents', {api_key:nifty_api_key});  

    jQuery.event.trigger({type: "bleeper_agent_socket_connected", ndata:data});

    /* are we running this on the CRM page? If yes, trigger the events */
    if (typeof bleeper_crm_page !== "undefined" && bleeper_crm_page === true) {
      jQuery.event.trigger({type: "bleeper_crm_page_loaded", ndata:data});
    }
    /* are we running this on the CRM page? If yes, trigger the events */
    if (typeof bleeper_contact_page !== "undefined" && bleeper_contact_page === true) {
      jQuery.event.trigger({type: "bleeper_contact_page_loaded", ndata:data});
    }

    jQuery(".nifty_bg_holder_text").html("");
    jQuery(".nifty_bg_holder").fadeOut();

  });

  socket.on('blop', function() {
    clearTimeout(latency_dead_check);

    latency = Date.now() - startTime;
    jQuery(".bleeper_stats").html(" [<em>Latency: "+latency+"ms</em>] ");
    
    setTimeout(function() {
      bleeper_latency_check();
    },3000);
    
  });


  socket.on('blip', function(data) {
    if (typeof blip_trigger !== "undefined") { blip_trigger(data,'before'); }
  });
  socket.on('blips', function(data) {
    if (typeof blip_sort_queue !== "undefined") { blip_sort_queue(data); }
  });

  socket.on('a2vping returned', function(data) {
    if (typeof remove_visitor_timer[data.chatid] !== "undefined") {
      clearTimeout(remove_visitor_timer[data.chatid]);
      
    
    }
    clearTimeout(ping_list[data.fromsocket]);
    bleeper_add_socket_to_ping(data.fromsocket, data.chatid);
  })

  socket.on('is recording', function(data) {
      jQuery.event.trigger({type: "bleeper_is_recording", ndata:data});
  });

  socket.on('chat history', function(data) {
    /* TO DO - compare against sessionStorage */
    if (typeof data !== "undefined" && typeof data.history !== "undefined") {
      if (data.history.length > 0) { 
        for (var i = 0, len = data.history.length; i < len; i++) {
         addChatMessage({username: bleeper_strip_tags(data.history[i].username), message: data.history[i].message, timestamp: data.history[i].date, msgID: data.history[i].id, aoru: data.history[i].aoru, type: data.history[i].type, read: data.history[i].read, chatid: data.chatid  });
         
         jQuery.event.trigger({type: "bleeper_add_message_chatbox", message:data.history[i].message});

        }
        nc_update_last_message(data.chatid,data.history[data.history.length-1], false);
      } else {
        log('No chat messages');
      }
    } else {
      log('No chat messages');
    }
    jQuery("#fetchinglog").remove();

  });

  socket.on('blacklisted', function(data) {
    jQuery.event.trigger({type: "bleeper_blacklisted", ndata:data});

  });

  socket.on('online agents', function(data) {
      if (data !== null) {
        for (l in data) {
            if (data[l] !== nifty_my_socket) {
              information_received_pointer = 'online_agents';
              socket.emit('request information',{socketid: data[l]});
            }
        }
      }
  });

  // Whenever the server emits 'login', log the login message
  socket.on('login', function (data) {
    connected = true;
    // Display the welcome message
    var message = "Connection established";
    log(message, {
      prepend: true
    });
  });

  socket.on('request chat', function(data) {
    if(typeof data !== "undefined" && typeof data.data === 'undefined'){
        data.data = {};
    }

    if (typeof data !== 'undefined' && typeof data.data !== 'undefined' && typeof data.data.name === "undefined") {
        data.data.name = "Guest";
    }
    if (typeof data !== 'undefined' && typeof data.data !== 'undefined' && typeof data.data.email === "undefined") {
        data.data.email = "No Email";
    }

    if(typeof visitor_list[data.chatid] === 'undefined'){
      visitor_list[data.chatid] = {};
    }

    visitor_list[data.chatid].state = 'active';
    visitor_list[data.chatid].username = bleeper_strip_tags(data.data.name);
    visitor_list[data.chatid].email = bleeper_strip_tags(data.data.email);
    jQuery("#vis"+data.chatid+" .userName").html(bleeper_strip_tags(data.data.name));
    var orig_elm = jQuery("#vis"+data.chatid);
    jQuery("#vis"+data.chatid).remove();
    jQuery(orig_elm).prependTo(".visitorListBox");    
    jQuery("#"+data.chatid+" .userName").html(bleeper_strip_tags(data.data.name));
    nc_add_accept_chat_visitor_row(data.chatid);
    jQuery.event.trigger({type: "bleeper_new_chat", ndata:data});   
  })

  socket.on('get information', function(data) {
    /* we have a request to send information to the server */

    var info_obj = {};
    info_obj.username = bleeper_agent_name;
    info_obj.agent_id = agent_id;
    socket.emit('send information', { intended_socket:data.socketid, data: info_obj });        

  })

  socket.on('transferring chat', function (data) {
    if (typeof data.chatid !== "undefined") {

      var message = '<h2>'+data.from_agent+' has transferred a chat to you!</h2>';
      
      niftyShowDialog("Incoming Transfer chat", message, 'Accept', 'Reject', function() {
        /* accept */
        socket.emit('accept transfer', {socketid:data.socketid,chatid:data.chatid,from_agent:bleeper_agent_name})  
        /* add the user to the active chats */
        nc_add_user_to_list(visitor_list[data.chatid], function(data){
          /* open the chat */
          jQuery("#"+data.chatid).click();
        });
      }, function() {
        /* reject */
        socket.emit('reject transfer', {socketid:data.socketid,chatid:data.chatid,from_agent:bleeper_agent_name})

      });
    }
  })


  socket.on('transferring chat department', function (data) {
    if (typeof data.chatid !== "undefined" && typeof data.department !== "undefined") {
      if(data.department === bleeper_agent_department){
        //This agent is a part of the desired department
        var message = '<h2>Transfer for your department!</h2>';

        niftyShowDialog("Incoming Department Transfer", message, 'Accept', 'Reject', function() {
          /* accept */
          socket.emit('accept department transfer', {socketid:data.socketid,chatid:data.chatid,from_agent:bleeper_agent_name})  
          /* add the user to the active chats */
          nc_add_user_to_list(visitor_list[data.chatid], function(data){
            /* open the chat */
            jQuery("#"+data.chatid).click();
          });
        }, function() {
          /* reject */
          socket.emit('reject department transfer', {socketid:data.socketid,chatid:data.chatid,from_agent:bleeper_agent_name})

        });
      }
    }
  })

  socket.on('username changed', function(data) {
    jQuery.event.trigger({type: "bleeper_username_changes", ndata:data});
  });

  socket.on('email changed', function(data) {
    jQuery.event.trigger({type: "bleeper_email_changes", ndata:data});
  });


  socket.on('new admin message', function (data) {
    nc_update_last_message(data.chatid,data,false);
    jQuery("#"+data.chatid).addClass('newmessage');

    if (active_chatid === data.chatid) {
      addChatMessage(data);
      data.other = {};
      data.other.aid = data.aid;
      data.aoru = data.aid;
      data.other.from_an_agent = true;
      jQuery.event.trigger({type: "bleeper_add_message_chatbox", message:data});
      jQuery.event.trigger({type:'bleeper_stop_typing', ndata: data, chatid: data.chatid});

    } else {
      if (typeof unread_count[data.chatid] === "undefined") { unread_count[data.chatid] = 1; }
      else { unread_count[data.chatid] = unread_count[data.chatid] + 1; }
      
      beeper_notify_new_message(data.chatid);  
    }

    nc_update_user_stop_typing(data.chatid);
  });

  socket.on('message read received', function(data) {
    jQuery.event.trigger({type: "bleeper_message_read_received", ndata:data});
  });


  // Whenever the server emits 'new message', update the chat body
  socket.on('new message', function (data) {
    if (typeof visitor_list[data.chatid] === "undefined") {
      // the agent possibly ended the chat but the user sent another message, re-add this user obj into the visitor_list array
      visitor_list[data.chatid] = data.user_obj;
    }

    visitor_list[data.chatid].state = 'active';

    /* remove the visitor timeout - perhaps this visitor just moved to a different page, if that is the case we dont want the agent to think that they have left us. if they have left though, they will timeout after x seconds - we are just canceling that timeout here. */
    if (typeof remove_visitor_timer[data.chatid] !== "undefined") {
      clearTimeout(remove_visitor_timer[data.chatid])
    }
    
    nc_remove_init_chat_visitor_row(data.chatid);
    nc_add_open_chat_visitor_row(data.chatid);

    /**
     * Clear the typing preview for this specific chat ID
     */
    jQuery.event.trigger({type: "bleeper_clear_typing_preview", cid: data.chatid});
    jQuery.event.trigger({type: "bleeper_new_message_for_cid", cid: data.chatid});
    

    nc_update_last_message(data.chatid,data,false);


    jQuery("#"+data.chatid).addClass('newmessage');
    bleeper_notify_new_message(data.chatid);
    
    if (typeof involved_list[data.chatid] !== "undefined") {
        if (active_chatid === data.chatid) {
          addChatMessage(data);
          jQuery.event.trigger({type: "bleeper_add_message_chatbox", message:data});
          // let the server know we have 'read' the message
          socket.emit('message read',data);          
        } else {
          if (typeof unread_count[data.chatid] === "undefined") { unread_count[data.chatid] = 1; }
          else { unread_count[data.chatid] = unread_count[data.chatid] + 1; }
          nc_update_unread_count(data.chatid);
          bleeper_change_favico(bleeper_favico_noti);
        }
    }

    jQuery.event.trigger({type: "bleeper_new_message_extender", message:data});
  });



  socket.on('edit message', function (data) {
    visitor_list[data.chatid].state = 'active';

    /* hide the initiate chat button */
    jQuery("#vis"+data.chatid+" .init_chat").hide();


    /**
     * Clear the typing preview for this specific chat ID
     */
    jQuery.event.trigger({type: "bleeper_clear_typing_preview", cid: data.chatid});
    

    if (active_chatid === data.chatid) {
      jQuery(".message_"+data.msgID+" .messageBody").html(wplcFormatParser(data.message) + " <em><small>(edited)</small></em>");
    } else {
      if (typeof unread_count[data.chatid] === "undefined") { unread_count[data.chatid] = 1; }
      else { unread_count[data.chatid] = unread_count[data.chatid] + 1; }
      nc_update_unread_count(data.chatid);
    }
    nc_update_last_message(data.chatid,data,true);
    jQuery("#"+data.chatid).addClass('newmessage');
  });


  socket.on('chat ended', function(data) {
        var tmp_chatid_close = data.chatid;

        involved_list[tmp_chatid_close] = false;
        jQuery.event.trigger({type: "bleeper_chat_ended_by_other_agent", ndata:data});  

        if (active_chatid === data.chatid) {
          /* current agent was also chatting and had the window active */
          /* disable the input field and simulate a user disconnect */
          active_chatid = undefined;
        }
       
        jQuery("#"+tmp_chatid_close).removeClass('active');
        jQuery("#"+tmp_chatid_close).addClass('inactive');
        jQuery(".bleeper_close_item_"+tmp_chatid_close).show();

        nifty_chat_remove_agent_involved(tmp_chatid_close, agent_id);
        bleeper_remove_events(tmp_chatid_close);

        if(typeof visitor_list[tmp_chatid_close] === 'undefined'){
          visitor_list[tmp_chatid_close] = {};
        }
        
        visitor_list[tmp_chatid_close].state = 'browsing';
        visitor_list[tmp_chatid_close].involved = undefined;
        jQuery.event.trigger({type: "bleeper_add_initiate_button_no_col", cid:tmp_chatid_close});  


  });


  // Whenever the server emits 'new message', update the chat body
  socket.on('socketid', function (socketid) {
    nifty_my_socket = socketid.socketid;
    document.cookie = "nc_sid="+socketid.socketid;
    jQuery.event.trigger({type: "bleeper_socketid_received", ndata:{socketid:socketid.socketid,instanceid:socketid.instanceid}});
  });

  socket.on('limited', function (socketinfo) {
    if (typeof socketinfo !== "undefined" && typeof socketinfo.limited !== "undefined") {
      bleeper_limited = socketinfo.limited;
      jQuery.event.trigger({ type: "bleeper_limited", ndata:{limited:socketinfo.limited} });
    }
  });

  socket.on('invalid_agent', function (socketinfo) {
    jQuery.event.trigger({ type: "bleeper_invalid_agent", socketdata: socketinfo });
  });

  socket.on('visitor list', function(data) {
    if (typeof data === "undefined" || data === null || (typeof data === "object" && data.total_visitors === 0 )) {
      nc_no_visitors();
      return;
    }

    if (data !== null) {
      if (typeof data !== "undefined" && typeof data.visitor_list !== "undefined") {
        // run through the visitor list and identify if we are actually ONLINE for this domain
        data.visitor_list = bleeper_update_visitor_data_list_per_online(data.visitor_list);

        if (typeof data.total_visitors !== "undefined") {
          nc_update_visitor_count(data.total_visitors);
        }

        for (k in data.visitor_list) {
          visitor_list[data.visitor_list[k].chatid] = data.visitor_list[k];
          
          if (typeof data.visitor_list[k].chatid !== 'undefined') {
            if (typeof visitor_validator[data.visitor_list[k].chatid] !== 'undefined') {
              clearInterval(visitor_validator[data.visitor_list[k].chatid]);
            }
            var tmp_cid = data.visitor_list[k].chatid;
            visitor_validator[data.visitor_list[k].chatid] = setInterval(function() {
              socket.emit('validate visitor', {chatid: tmp_cid});    
            }, 35000);
          }

          nc_add_visitor_to_list(data.visitor_list[k]);
          if (data.visitor_list[k].state === "active") {
            
            jQuery("#vis"+data.visitor_list[k].chatid+ " .agents_involved_visitor").html('');
            if (typeof data.visitor_list[k].involved !== "undefined" && data.visitor_list[k].state !== "browsing") {
              if (data.visitor_list[k].involved.length > 0) {
                /* set involved to FALSE as default */
                involved_list[data.visitor_list[k].chatid] = false;

                for (l in data.visitor_list[k].involved) {
                    
                    nifty_chat_add_agent_involved_visitor_list(data.visitor_list[k].chatid, data.visitor_list[k].involved[l]);

                    if (parseInt(data.visitor_list[k].involved[l]) === parseInt(agent_id)) {
                      /* this is the current agent, add it to their local variable */
                      nc_add_user_to_list(data.visitor_list[k], function(ret_data) {
                        for (q in data.visitor_list[k].involved) {
                          nifty_chat_add_agent_involved(data.visitor_list[k].chatid, data.visitor_list[k].involved[q]);
                        }
                      });
                      involved_list[data.visitor_list[k].chatid] = true;
                    } else {
                     
                    }

                }
                nc_clear_action_col_visitor_row(data.visitor_list[k].chatid);
                nc_add_open_chat_visitor_row(data.visitor_list[k].chatid);
                
              } else {
                nc_add_accept_chat_visitor_row(data.visitor_list[k].chatid);
              }
            }


          } else {
            /* visitor is browsing */
            jQuery("#vis"+data.visitor_list[k].chatid+" .init_chat").show();

          }
        }
      } else {
        nc_no_visitors();
      }
    }

    jQuery.event.trigger({type: "bleeper_after_update_visitor_list"});

  });

  socket.on('new visitor', function(data) {
    var istrack_elm =  document.getElementById('bleeper-no-tracking');
    if (typeof(istrack_elm) != 'undefined' && istrack_elm != null) { 
      istrack_elm.parentNode.removeChild(istrack_elm);
    }

    if (typeof visitor_validator[data.chatid] !== 'undefined') {
      clearInterval(visitor_validator[data.chatid]);
    }
    visitor_validator[data.chatid] = setInterval(function() {
      socket.emit('validate visitor', {chatid: data.chatid});
    }, 35000);

    if (bleeper_check_visitor_as_per_online(data)) {
      // this visitor matches a domain from a domain that we have set to ONLINE
      visitor_list[data.chatid] = data;
      var current_visitor_socketid = jQuery("#"+data.chatid).attr('socket');
      
      nc_update_visitor_count(data.total_visitors);
      nc_add_visitor_to_list(data);

      /* was this chat open and active with an agent? if yes, update the agent as to what page the user is now browsing */
      if (typeof data.state !== "undefined" && data.state === "active") {
        jQuery("#"+data.chatid).removeClass('inactive');
        jQuery("#"+data.chatid).addClass('active');
        
        bleeper_save_event(data.chatid, 'fa-window-maximize', bleeper_strip_tags(data.username)+" is now browsing "+bleeper_strip_tags(data.referer), data);
        if (data.chatid === active_chatid) {
          jQuery(".chatInfoArea-Info1").html("from: <a href='"+bleeper_strip_tags(visitor_list[active_chatid].referer)+"' target='_BLANK'>"+bleeper_string_limiter(bleeper_strip_tags(visitor_list[active_chatid].referer),45)+"</a>");
          
        }

        jQuery("#vis"+data.chatid+" .init_chat").hide();
      }

      /* remove the visitor timeout - perhaps this visitor just moved to a different page, if that is the case we dont want the agent to think that they have left us. if they have left though, they will timeout after x seconds - we are just canceling that timeout here. */
      if (typeof remove_visitor_timer[data.chatid] !== "undefined") {
        clearTimeout(remove_visitor_timer[data.chatid])
      }

      jQuery.event.trigger({type: "bleeper_after_update_visitor_list"});
    }
    
  });
  
  socket.on('new chat', function(data) {

    if(typeof data !== 'undefined' && typeof data.data === "undefined"){
      data.data = {};
    }

    if(typeof data !== 'undefined' && typeof data.data !== 'undefined' && typeof data.data.name === "undefined") {
      data.data.name = "Guest";
    }

    if(typeof data !== 'undefined' && typeof data.data !== 'undefined' && typeof data.data.email === "undefined") {
      data.data.email = "No Email";
    }

    visitor_list[data.chatid].state = 'active';
    visitor_list[data.chatid].username = bleeper_strip_tags(data.data.name);
    visitor_list[data.chatid].email = bleeper_strip_tags(data.data.email);
    jQuery("#vis"+data.chatid+" .userName").html(bleeper_strip_tags(data.data.name));
    jQuery("#"+data.chatid+" .userName").html(bleeper_strip_tags(data.data.name));
    nc_add_accept_chat_visitor_row(data.chatid);
    jQuery.event.trigger({type: "bleeper_new_chat", ndata:data});   

  });
  
  socket.on('visitor left', function(data) {
    if (typeof visitor_validator[data.chatid] !== 'undefined') {
      clearInterval(visitor_validator[data.chatid]);
      delete visitor_validator[data.chatid];
    }
    jQuery.event.trigger({type: "bleeper_remove_visitor", ndata:data});
  });  

  socket.on('agent joined', function (data) {
    jQuery.event.trigger({type: "bleeper_agent_joined", ndata:data});
    data.apikey = nifty_api_key;
  });

  socket.on('custom data received', function (data) {
    jQuery.event.trigger({type: "bleeper_custom_data_received", ndata:data});    
  });

  /**
   * This tells us when the user has received the intiate chat request
   *
   * We can now send custom data (like the agent header data) to the user
   * 
   * @return void
   */
  socket.on('initiate received admin', function (data) {
    jQuery.event.trigger({type: "bleeper_add_agent", ndata:{chatid:data.chatid, agent:agent_id}});
    jQuery.event.trigger({type: "bleeper_notify_agent_initiate_received", ndata:{chatid:data.chatid, agent:agent_id}});
  });


  socket.on('agent connected', function(data) {
    var t_c_aid = data.aid;
    var t_c_sid = data.socketid;
    jQuery(".online_agent_"+t_c_aid).attr('socket',t_c_sid);
    jQuery(".online_agent_"+t_c_aid+ ' .online_offline').removeClass('offline');
    jQuery(".online_agent_"+t_c_aid+ ' .online_offline').addClass('online');

    jQuery.event.trigger({type: "bleeper_agent_connected", ndata: data});
  });


  socket.on('agent disconnected', function(data) {
    var t_c_aid = data.aid;
    jQuery(".online_agent_"+t_c_aid).attr('socket',null);
    jQuery(".online_agent_"+t_c_aid+ ' .online_offline').removeClass('online');
    jQuery(".online_agent_"+t_c_aid+ ' .online_offline').addClass('offline');

    jQuery.event.trigger({type: "bleeper_agent_disconnected", ndata: data});
  });

  socket.on('agent involved', function (data) {
    nifty_chat_add_agent_involved(data.chatid, data.agentid);
    nifty_chat_add_agent_involved_visitor_list(data.chatid, data.agentid);
    nc_clear_action_col_visitor_row(data.chatid);
    nc_add_open_chat_visitor_row(data.chatid);
  });

  socket.on('information received', function(data) {
    if (!information_received_pointer) {
      /* default */
    } else if (information_received_pointer === "transfer") {
        t_ag_sock = data.from_socket;
        t_ag_name = data.username;
        t_ag_id = data.agent_id;

        var check_all_agents = all_agents;
        if(typeof all_agents !== "object"){
          check_all_agents = JSON.parse(all_agents);
        }
        
        agent_involved = check_all_agents[t_ag_id];
        agent_involved_email = agent_involved.email;
        agent_involved_name = agent_involved.display_name;

        var thing = '<img class="img-thumbnail img-circle thumb32" style="max-width:inherit;" id="agent_grav_'+t_ag_id+'" title="'+agent_involved_name+'" src="https://www.gravatar.com/avatar/'+agent_involved_email+'?s=32&d=mm" />'
        


        jQuery(".ag_t_span_"+t_ag_sock).html(thing + ' ' +data.username);
        jQuery("#ag_t_"+t_ag_sock).attr('value',t_ag_sock);

    } else if (information_received_pointer === "online_agents") {
        jQuery.event.trigger({type: "bleeper_online_agent_packet_received", ndata:data});   
        
    }
  });

  socket.on('agent left', function (data) {
    jQuery.event.trigger({type: "bleeper_agent_left", ndata:data});
    removeChatTyping(data);
    nifty_chat_remove_agent_involved(data.chatid, data.agent);
  });

  // Whenever the server emits 'typing', show the typing message
  socket.on('typing', function (data) {
    if (active_chatid === data.chatid) {
      jQuery.event.trigger({type:'bleeper_typing', ndata: data, chatid: data.chatid});
    }
    //bleeper_save_event(data.chatid, 'fa-keyboard-o', data.username+" was typing", data);    
    nc_update_user_typing(data.chatid);
  });

  // Whenever the server emits 'stop typing', kill the typing message
  socket.on('stop typing', function (data) {
    if (active_chatid === data.chatid) {
        jQuery.event.trigger({type:'bleeper_stop_typing', ndata: data, chatid: data.chatid});
    }
    nc_update_user_stop_typing(data.chatid);
  });

  socket.on('typing_preview_received', function(data) {
    if(typeof data !== "undefined" && typeof data.preview !== "undefined" && typeof data.preview.tempmessage !== "undefined"){
      data.preview.tempmessage = cleanInput(data.preview.tempmessage);
    }
    jQuery.event.trigger({type: 'bleeper_typing_preview_received', ndata:data});
  });   

  // Receive CHAT ID from server
  socket.on('chatID', function (data) {
    document.cookie = "nc_chatid="+data.chatid;
  });

  socket.on('disconnect', function () {
    clearTimeout(get_visitor_timer);
    get_visitor_timer = undefined;
    jQuery.event.trigger({type: 'bleeper_disconnected'})
  });

  socket.on('reconnect', function () {
    log('you have been reconnected');
    nc_add_agent(socket, null);
    bleeper_get_visitor_data();
  });

  socket.on('reconnect_error', function () {
    log('attempt to reconnect has failed');
    jQuery(".nifty_bg_holder_text").html("Connection to the server lost.. please be patient while we try restore the connection...");
    jQuery(".nifty_bg_holder").fadeIn();
  });

  socket.on('user chat notification', function(data){
    //Notice has been sent to the user
    jQuery.event.trigger({type: "bleeper_user_chat_notification", ndata:data});
  }); 

  socket.on("your browser", function(data) {
  });

  jQuery.event.trigger({type: "bleeper_socket_extender"});

}

/**
 * Check if the visitor is online
 * 
 * @param {object} visitor_data The visitor data packet
 * @return {bool} True if should show
*/
function bleeper_check_visitor_as_per_online(visitor_data) {
  
  if (typeof onlineSwitch !== 'undefined') {
    if (typeof onlineSwitch.domainNameURIList !== 'undefined') {

      if (typeof onlineSwitch.onlineDomains !== 'undefined') {

        for (k in onlineSwitch.onlineDomains) {
          // let's run through the first online domain and compare against all visitors
          var current_domain_to_check = onlineSwitch.domainNameURIList[onlineSwitch.onlineDomains[k]];
          
          if (visitor_data.referer.includes(current_domain_to_check)) {
            return true;

          } else {
            
            
          }
          
          
        }

      } else { 
        return false;
      }

    } else {
      return false;
    }
  } else {
    return false;  
  }

  

  return false;

}

/**
 * Update the visitor list
 * 
 * @param {object} total_visitors The Visitor list packet
 * @return {object} New formatted visitor list
*/
function bleeper_update_visitor_data_list_per_online(total_visitors) {
  
  if (typeof onlineSwitch !== 'undefined') {
    if (typeof onlineSwitch.domainNameURIList !== 'undefined') {

      if (typeof onlineSwitch.onlineDomains !== 'undefined') {
        if (Object.keys(onlineSwitch.onlineDomains).length === 0) { return {}; }
        var new_visitor_list = {};

        var cnt = 0;
        var maxcnt = Object.keys(onlineSwitch.onlineDomains).length;
        

        for (k in onlineSwitch.onlineDomains) {
          // let's run through the first online domain and compare against all visitors
          var current_domain_to_check = onlineSwitch.domainNameURIList[onlineSwitch.onlineDomains[k]];
          for (l in total_visitors) {
            
            if (total_visitors[l].referer.includes(current_domain_to_check)) {
              
              new_visitor_list[total_visitors[l].chatid] = total_visitors[l];
            } 
            
          }
          cnt++;

          // are we done here?
          if (cnt >= maxcnt) {
            // compare new visitors to old visitors so we can remove visitors that shouldnt be there.
            for (m in total_visitors) {
              if (typeof new_visitor_list[total_visitors[m].chatid] !== 'undefined') {
                // this visitor must stay..
              } else {
                jQuery.event.trigger({type: "bleeper_remove_visitor", ndata:total_visitors[m]});
                delete total_visitors[m];
              }
            }
            return new_visitor_list;
          }
        }


      } else { 
        //not online for anything
        return {};
        
      }

    } else {
      
      return total_visitors;
    }
  } else {
    
    return total_visitors;  
  }
  return total_visitors;
}

/**
 * Get specific URL params
 * 
 * @param {string} url The URL to be parsed
 * @param {string} name The name of the param you are looking for
 * @return {string} The value of the param (or null)
*/
function bleeper_getURLParameter(url, name) {
  if(typeof url !== "undefined"){

    var check = url.match(name+'=([^&]*)')

    if (typeof check !== "undefined" && check !== null && typeof check[1] !== "undefined") { return check[1]; } else { return null; }
  } else {
    return null;
  }
  
}

/**
 * Parse GA UTM Campaign information
 *
 * @param {string} url The URL to be parsed
 * @return {string} HTML with the campaign data in order
*/
function bleeper_return_campaign_data(url) {
  bleeper_utmsource = bleeper_getURLParameter(url,'utm_source') !== null ? "<label class='label-warning session-state-label'>"+bleeper_getURLParameter(url,'utm_source')+"</label>" : "";
  bleeper_utmcampaign = bleeper_getURLParameter(url,'utm_campaign') !== null ? "<label class='label-success session-state-label'>"+bleeper_getURLParameter(url,'utm_campaign')+"</label>" : "";
  bleeper_utmmedium = bleeper_getURLParameter(url,'utm_medium') !== null ? "<label class='label-info session-state-label'>"+bleeper_getURLParameter(url,'utm_medium')+"</label>" : "";

  return bleeper_utmsource+" "+bleeper_utmcampaign+" "+bleeper_utmmedium;
}

/**
 * Return liftime, total lifetime, and referrer
 *
 * @param {object} data The visitor data packet
 * @return {string} HTML showing lifetime and referrer
*/
function bleeper_return_visitor_lifetime_data(data) {
    var wplc_d = new Date();
    wplc_d.toUTCString();
    var cdatetime = Math.floor(wplc_d.getTime()/ 1000);

  if (typeof data.date_first !== "undefined" && data.date_first !== "0") {
    bleeper_date_first = cdatetime - parseInt(data.date_first);
    bleeper_date_first = wplc_timeSince(bleeper_date_first) + "";
  } else {
    bleeper_date_first = '';
  }
  if (typeof data.date_current !== "undefined" && data.date_current !== "0") {
    bleeper_date_current = cdatetime - parseInt(data.date_current);
    bleeper_date_current = wplc_timeSince(bleeper_date_current) + "";
  } else {
    bleeper_date_current = '';
  }
  return "Lifetime: <label class='label-default time-state-label' title='Total lifetime' alt='Total lifetime'>"+bleeper_date_first+"</label> / <label class='label-default time-state-label' title='Current page' alt='Current page'>"+bleeper_date_current+"</label>";

}

/**
 * Return the users timezone
 *
 * @param {object} data The visitor data packet
 * @return {string} HTML containing the timezone
*/
function bleeper_return_visitor_timezone_data(data){
  var timezomeDiff = "";
  if (typeof data.timezoneUTC !== "undefined"){
    if(data.timezoneUTC !== "0") {
      timezomeDiff = " " + data.timezoneUTC;
    }
  }

  return "Timezone: <label class='label-default time-state-label' title='UTC" + timezomeDiff + "' alt='UTC" + timezomeDiff + "'>UTC" + timezomeDiff + "</label><br>";
}

/**
 * Find and return the users OS icon
 *
 * @param {object} data The visitor data packet
 * @return {string} A font awesome icon
*/
function bleeper_return_visitor_os_data(data){
  var user_os = "Unknown";
  if (typeof data.operating_system !== "undefined"){
    user_os = data.operating_system;
  }
  
  if(user_os !== "Unknown"){
    var font_awesome_icon = "";
    if(user_os.indexOf("Windows") !== -1){
      font_awesome_icon = "fa-windows";
    } else if (user_os.indexOf("Mac") !== -1){
      font_awesome_icon = "fa-apple";
    } else if (user_os.indexOf("Android") !== -1){
      font_awesome_icon = "fa-android";
    } else if (user_os.indexOf("iOS") !== -1){
      font_awesome_icon = "fa-apple";
    } else if (user_os.indexOf("Linux") !== -1){
      font_awesome_icon = "fa-linux";
    } else if (user_os.indexOf("Search Bot") !== -1){
      font_awesome_icon = "fa-bug";
    } else {
      //Some other os
      font_awesome_icon = "fa-server";
    }
    return " <i class='fa " + font_awesome_icon + "' title='OS: " + user_os + "'></i>";
  }
  return "";

}

/**
 * Change the favicon image if this is the chat dashboard
 *
 * @param {string} src Source (url) of the new favicon
*/
function bleeper_change_favico(src) {
  //Check if this is an allowed 'page' location - Are we allowed to change favicons? 
  if( (window.location.href.indexOf("page=wplivechat-menu") !== -1) ){
    var link = document.createElement('link'),
         oldLink = document.getElementById('dynamic-favicon');
    link.id = 'dynamic-favicon';
    link.rel = 'shortcut icon';
    link.href = src;
    if (oldLink) {
     document.head.removeChild(oldLink);
    }
    document.head.appendChild(link);
  }
}

/**
 * Checks if string is JSON
 * 
 * @param {string} str String to be checked
 * @return {bool} True if JSON object
*/
function bleeperIsJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/**
 * Create a substring
 *
 * @param {string} str The string to be sliced
 * @param {int} limit The maximum amount of chars
 * @return {string} The substring
*/
function bleeper_string_limiter(str, limit){
   var char_count = str.length;
   if(char_count > limit){
        var first_half_cutoff = parseInt(limit / 2) - 2;
           var second_half_cutoff = parseInt(limit / 2) + 1;

        var first_half = str.substring(0, first_half_cutoff);
        var second_half = str.substring(char_count - second_half_cutoff);
        str = first_half + "..." + second_half;
   }
   return str;
}

/**
 * Time passed function
 *
 * @param {int} seconds Time in seconds
 * @return {string} Pretty print of the time in seconds
*/
function wplc_timeSince(seconds) {
  var interval = Math.floor(seconds / 31536000);
  if (interval > 1) {
    return interval + " yrs";
  }
  interval = Math.floor(seconds / 2592000);
  if (interval > 1) {
    return interval + " mo";
  }
  interval = Math.floor(seconds / 86400);
  if (interval > 1) {
    return interval + " d";
  }
  interval = Math.floor(seconds / 3600);
  if (interval > 1) {
    return interval + " h";
  }
  interval = Math.floor(seconds / 60);
  if (interval > 1) {
    return interval + " m";
  }
  return Math.floor(seconds) + " seconds";
}

/**
 * Lifespan function
 *
 * @param {int} from From time in seconds
 * @param {int} to To time in secods
 * @return {string} Pretty print of the time ago
*/
function bleeper_timeAgo(from, to) {
  var seconds = Math.floor((from - to) / 1000);
  var interval = Math.floor(seconds / 31536000);
  if (interval >= 1) {
    if (interval === 1) {
      return interval + " year";
    } else {
      return interval + " years";
    }
  }
  interval = Math.floor(seconds / 2592000);
  if (interval >= 1) {
    if (interval === 1) {
      return interval + " month";
    } else {
      return interval + " months";
    }
  }
  interval = Math.floor(seconds / 86400);
  if (interval >= 1) {
    
    if (interval === 1) {
      return interval + " day";
    } else {
      return interval + " days";
    }
  }
  interval = Math.floor(seconds / 3600);
  if (interval >= 1) {
    if (interval === 1) {
      return interval + " hour";
    } else {
      return interval + " hours";
    }
  }
  interval = Math.floor(seconds / 60);
  if (interval >= 1) {
    if (interval === 1) { 
      return interval + " minute";
    } else {
      return interval + " minutes";
    }
  }

  if (seconds < 60) {
    return "moments";
  }

  return Math.floor(seconds) + " seconds";

}

/**
 * Message stack and queue functionality
 *
 * @param {string} chat_id The Chat ID
*/
function bleeper_sort_and_send_messages(chat_id) {
  if (bleeper_test_sessionStorage()) {
    jQuery("#messages").html('');

    if (sessionStorage.getItem(chat_id+"_m") !== null) {
      var msg_obj = JSON.parse(sessionStorage.getItem(chat_id+"_m"));

      var new_msg_list = {};
      Object.keys(msg_obj).sort().forEach(function(key) {
        new_msg_list[key] = msg_obj[key];
      });

      for (var key in new_msg_list) {
        var the_message = new_msg_list[key];
        
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
}

/**
 * Determines whether the currently selected user filter affects the current visitor items visibility. 
 * If conditions for the filters are met, we will return true to hide the div
 * 
 * @param {object} data The visitor data
 * @return {bool} True if the visitor should be hidden
*/
function bleeper_hide_visitor_by_filter(data){
  if(typeof bleeper_active_filter !== "undefined" && bleeper_active_filter !== false){
    if(bleeper_active_filter === bleeper_visitor_filters.new_visitors){
      //New visitors only, 3 minutes or less
      var wplc_d = new Date();
      wplc_d.toUTCString();
      var cdatetime = Math.floor(wplc_d.getTime()/ 1000);

      if (typeof data.date_current !== "undefined" && data.date_current !== "0") {
        bleeper_date_current = cdatetime - parseInt(data.date_current);
        var interval = Math.floor(bleeper_date_current / 60);

        if (interval > 3) {
          return true; //Less than 3 minutes passed - Show chat
        }
      }
    }

    if(bleeper_active_filter === bleeper_visitor_filters.active_chats){
      if(typeof data.state !== "undefined" && data.state !== "active"){
        return true; // Hides it
      }
    }

    if (bleeper_active_filter === bleeper_visitor_filters.referer){
      var filter_referer = jQuery('#nifty_referer_url').val().split(/[?#]/)[0].replace(/\/$/, '').toLowerCase();
      if(typeof data.referer !== 'undefined' && data.referer.length > 0 && filter_referer.length > 0){
        var referer = data.referer.split(/[?#]/)[0].replace(/\/$/, '').toLowerCase();
		    if(jQuery('#nifty_referer_contains').prop('checked')){
		      if (-1 === referer.indexOf(filter_referer)){
		        return true; // Hides it
		      }
		    } else {
		      if (referer !== filter_referer){
		        return true; // Hides it
		      }
		    }
	    }
    }

  }
  return false;
}

/**
 * Loops through the visitor data object and adds each item to the container div. This essentially forces a refresh
 * This is nice for use in filters
*/
function bleeper_refresh_visitor_list(){
  if(typeof bleeper_active_filter !== "undefined" && bleeper_active_filter !== false){
    jQuery(".filter-menu .dropdown-toggle").addClass('filter-active');    //It is active
  } else {
    jQuery(".filter-menu .dropdown-toggle").removeClass('filter-active');
    jQuery(".filter-active-tag-container").fadeOut("slow");
  }

  visitor_list = bleeper_update_visitor_data_list_per_online(visitor_list);

  for(var i in visitor_list){
    nc_add_visitor_to_list(visitor_list[i]);
  }

  jQuery.event.trigger({type: "bleeper_after_update_visitor_list"});
}

/**
 * Generates and returns a dropdown menu (html), based on an input array utm_sourc
 * 
 * @param {string} id_name ID Name
 * @param {array} data_array The data arrat to loop through
 * @return {string} Dropdown HTML
*/
function bleeper_generate_dropdown(id_name, data_array){
  var dropdown_html = "<select name='" + id_name + "' id='" + id_name + "' >";
  for(var i in data_array){
    dropdown_html += "<option value='" + i + "'>" + bleeper_string_limiter(data_array[i], 45) + "</option>";
  }
  dropdown_html += "</select>";
  return dropdown_html;
}

/**
 * Use Regular Exp seen above to parse any inline links automatically
 * Variable defined outside of function scope to reserve resources as seen here: https://stackoverflow.com/questions/1500260/detect-urls-in-text-with-javascript
 *
 * @param {string} content Content to filter
 * @return {string} Fitlered content
*/
function bleeper_inline_link_generator(content){
  return content.replace(bleeper_link_match_regex, function(url) {
        if(url.indexOf("wdt-emoji") === -1){
          url = bleeper_url_path_em_stripper(url);
          return '<a href="' + url + '" target="_BLANK">' + bleeper_attachment_label_filter(url) + '</a>';
        } else {
          url = bleeper_url_path_em_stripper(url);
          return url;
        }
  });
}

/**
 * Removes any instances of "<em>" or "</em>" from a path. Used in fixes of the image uploader, and link processing
 *
 * @param {string} path_url Path to cleanup
 * @return {string} Clean path URL
*/
function bleeper_url_path_em_stripper(path_url){
    if(path_url.indexOf("<em>") !== -1){
         path_url = path_url.replace(/<em>/g, "_");
    }
    if(path_url.indexOf("</em>") !== -1){
        path_url = path_url.replace(/<\/em>/g, "_");
    }

    return path_url;
}

/**
 * Check if string contains any file suffixes
 * If so return 'Attachment' - Else Return self
 *
 * @param {string} content Content to filter
 * @return {string} Fitlered content
*/
function bleeper_attachment_label_filter(content){
  var fileExt = content.split('.').pop();
  fileExt = fileExt.toLowerCase();
  for(var i in bleeper_file_suffix_check){
    if(fileExt === bleeper_file_suffix_check[i]){
      return "Attachment";
    }
  }

  return content;
}

/**
 * Notify the agent that there is a new message and display the enabled 
 * notification options.
 *
 * @param {string} chatid The Chat ID
 */
function bleeper_notify_new_message(chatid) {
  if(typeof current_user_is_online !== 'undefined'){
    //This is bleeper specific 
    if(current_user_is_online == false){
      return; //User is offline, just return now and kill the function
    }
  }

  if (typeof involved_list[chatid] !== "undefined" && involved_list[chatid] === true) {
    nc_update_unread_count(chatid);

    if (typeof bleeper_ping_sound_notification_enabled !== "undefined") {
      if (bleeper_ping_sound_notification_enabled === "true") {
        bleeper_ping.play();
      }
    }

    bleeper_change_favico(bleeper_favico_noti);
  }
}

/**
 * Strip tags with JS
 *
 * @param {string} input_string The Input string
 * @return {string} The clean value
*/
function bleeper_strip_tags(input_string){
  var stripped_string = input_string;
  if(typeof stripped_string === "string"){
    stripped_string = stripped_string.replace(/(<([^\/\/]+)\/\/)/ig,"");
    stripped_string = stripped_string.replace(/(<([^\/>]+)\/>)/ig,"");
    stripped_string = stripped_string.replace(/(<([^>]+)>)/ig,"");
    stripped_string = stripped_string.replace(/(<([^]+))/ig,"");
    stripped_string = stripped_string.replace(/%20/ig, " ");
  }
  return stripped_string;
}

/**
 * Handles aliases for chat statuses
 *
 * @param {string} status The current status
 * @return {string} The formatted status
*/
function bleeper_pretty_chat_status(status){
  var status_aliases = {
    'ticket_new'     : 'active',
    'ticket_pending' : 'active',
    'ticket_open'    : 'active',
    'ticket_closed'  : 'active',
  };

  if(typeof status_aliases[status] !== 'undefined'){
    return status_aliases[status];
  }
  return status;
}


/**
 * Adds a socket to the ping list
 * This will check if the user is still around
 *
 * @param {string} socketid The socket ID to be pinged
 * @param {string} chatid The chat ID
*/ 
function bleeper_add_socket_to_ping(socketid, chatid) {
  ping_list[socketid] = setTimeout(function() {
    socket.emit('a2vping',{socketid:socketid, returnsocket: nifty_my_socket, chatid: chatid});
    var tmpusername = 'The visitor';
    if (typeof visitor_list[chatid] !== 'undefined' && visitor_list[chatid].username !== 'undefined') { 
      tmpusername = visitor_list[chatid].username;
    }

    nc_remove_visitor_after_time({socketid: socketid, chatid: chatid, username: tmpusername})
  },9000);
}