/**
 * Custom data trigger
 *
 * If custom data is sent through the socket, this is where you would want to handle it
 *
 * @return void
 */
jQuery(document).on("bleeper_custom_data_received", function(e) {
    if (typeof e.ndata !== "undefined") {
      
        if (e.ndata.action === "send_user_canvas") {
          if (active_chatid === e.ndata.chatid) {
            the_message = wplc_generate_system_notification_object("<img style='max-width:400px;' src='"+e.ndata.ndata+"' />", {}, 0);
            wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
          }
        }
        if (e.ndata.action === "input_field_change") {
          if (typeof e.ndata.ndata.fields !== "undefined") { var fields = e.ndata.ndata.fields; } else { var fields = false; }
          if( fields ){
            bleeper_save_event(e.ndata.chatid, 'fa-edit', "User has selected a form input field. ", e.ndata);

          }

        }
        if (e.ndata.action === "send_link_click") {
          if (typeof e.ndata.ndata.link_click !== "undefined") { var link_click = e.ndata.ndata.link_click; } else { var link_click = false; }
          if( link_click ){
            bleeper_save_event(e.ndata.chatid, 'fa-link', "User has clicked a link ("+e.ndata.ndata.text+"). ", e.ndata);

          }

        }

        if(e.ndata.action === "wplc_minimized") {
          bleeper_save_event(e.ndata.chatid, "fa-caret-square-o-down", "User minimized the chat box", e.ndata);
          the_message = wplc_generate_system_notification_object("User minimized the chat box", {}, 0);
          bleeper_add_message_to_sessionStorage(e.ndata.chatid, the_message);
        }

        if(e.ndata.action === "wplc_maximized") {
          bleeper_save_event(e.ndata.chatid, "fa-caret-square-o-up ", "User maximized the chat box", e.ndata);
          the_message = wplc_generate_system_notification_object("User maximized the chat box", {}, 0);
          bleeper_add_message_to_sessionStorage(e.ndata.chatid, the_message);
        }        


        if (e.ndata.action === "send_user_click_data") {
          if (typeof e.ndata.ndata.elem_class !== "undefined") { var elem_class = "."+e.ndata.ndata.elem_class; } else { var elem_class = ''; }
          if (typeof e.ndata.ndata.elem_id !== "undefined") { var elem_id = " #"+e.ndata.ndata.elem_id; } else { var elem_id = ''; }
          var elem_msg = '';
          if (elem_class === "" && elem_id === "") {
            elem_msg = "(no class or ID).";
          } else {
            elem_msg = "("+elem_class+""+elem_id+").";
          }

          bleeper_save_event(e.ndata.chatid, 'fa-mouse-pointer', "User clicked on an element "+elem_msg, e.ndata);

          //if (active_chatid === e.ndata.chatid) {
            //the_message = wplc_generate_system_notification_object("User clicked on an element (Class: "+elem_class+", ID: "+elem_id+")", {}, 0);
            //wplc_push_message_to_chatbox(the_message,'a', function() {
                //wplc_scroll_to_bottom();  
            //});
          //}

        }

        if (e.ndata.action === "send_user_mouse_idling") {
          if (typeof e.ndata.ndata.idle !== "undefined") { var idling = e.ndata.ndata.idle; } else { var idling = false; }
          if( idling ){
            bleeper_save_event(e.ndata.chatid, 'fa-minus', "User is now idling. ", e.ndata);
            
            /*if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User is now idling", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_right_clicked") {
          if (typeof e.ndata.ndata.right_clicked !== "undefined") { var right_clicked = e.ndata.ndata.right_clicked; } else { var right_clicked = false; }
          if( right_clicked ){
            bleeper_save_event(e.ndata.chatid, 'fa-list', "User has right clicked on the page. ", e.ndata);
            /*if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has right clicked on the page", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_ctrl_f") {
          if (typeof e.ndata.ndata.ctrl_f !== "undefined") { var ctrl_f = e.ndata.ndata.ctrl_f; } else { var ctrl_f = false; }
          if( ctrl_f ){
            bleeper_save_event(e.ndata.chatid, 'fa-search', "User has pressed CTRL + F", e.ndata);
            /*
            if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has pressed CTRL + F", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_ctrl_p") {
          if (typeof e.ndata.ndata.ctrl_p !== "undefined") { var ctrl_p = e.ndata.ndata.ctrl_p; } else { var ctrl_p = false; }
          if( ctrl_p ){
            bleeper_save_event(e.ndata.chatid, 'fa-print', "User has pressed CTRL + P", e.ndata);
            /*if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has pressed CTRL + P", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_ctrl_c") {
          if (typeof e.ndata.ndata.ctrl_c !== "undefined") { var ctrl_c = e.ndata.ndata.ctrl_c; } else { var ctrl_c = false; }
          if( ctrl_c ){
            bleeper_save_event(e.ndata.chatid, 'fa-copy', "User has pressed CTRL + C", e.ndata);
            /*if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has pressed CTRL + C", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_ctrl_v") {
          if (typeof e.ndata.ndata.ctrl_v !== "undefined") { var ctrl_v = e.ndata.ndata.ctrl_v; } else { var ctrl_v = false; }
          if( ctrl_v ){
            bleeper_save_event(e.ndata.chatid, 'fa-paste', "User has pressed CTRL + V", e.ndata);
            /*
            if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has pressed CTRL + V", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_console_opened") {
          if (typeof e.ndata.ndata.console_log_opened !== "undefined") { var console_log_opened = e.ndata.ndata.console_log_opened; } else { var console_log_opened = false; }
          if( console_log_opened ){
            bleeper_save_event(e.ndata.chatid, 'fa-bug', "User has opened the window inspector", e.ndata);
            /*
            if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has opened the window inspector", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "send_user_section_selected") {
          if (typeof e.ndata.ndata.html_element !== "undefined") { var html_element = e.ndata.ndata.html_element; } else { var html_element = false; }
          if( html_element ){
            bleeper_save_event(e.ndata.chatid, 'fa-text-width', "User has selected the following element: "+html_element, e.ndata);

            /*
            if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has selected the following element: "+html_element, {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if (e.ndata.action === "user_scrolling") {
          if (typeof e.ndata.ndata.scrolling !== "undefined") { var scrolling = e.ndata.ndata.scrolling; } else { var scrolling = false; }
          if( scrolling ){
            bleeper_save_event(e.ndata.chatid, 'fa-arrows-v', "User has scrolled on the page", e.ndata);

            /*
            if (active_chatid === e.ndata.chatid) {
              the_message = wplc_generate_system_notification_object("User has scrolled on the page", {}, 0);
              wplc_push_message_to_chatbox(the_message,'a', function() {
                  wplc_scroll_to_bottom();  
              });
            }*/
          }

        }

        if(e.ndata.action === "wplc_send_live_rating") {
          if(typeof e.ndata.rating_data !== "undefined"){
            //Rating data received 
            if(typeof e.ndata.rating_data.score !== "undefined"){
              var rating_score = parseInt(e.ndata.rating_data.score);
              var rating_comment = typeof e.ndata.rating_data.comment !== "undefined" ? e.ndata.rating_data.comment : "No Comment...";

              var rating_icon = rating_score === 0 ? "fa-thumbs-o-down" : "fa-thumbs-o-up";
              bleeper_save_event(e.ndata.chatid, rating_icon, "User left a rating: " + rating_comment, e.ndata);

              the_message = wplc_generate_system_notification_object("User left a " + (rating_score === 0 ? "negative" : "positive")  + " rating: " + rating_comment, {}, 0);
        
              bleeper_add_message_to_sessionStorage(e.ndata.chatid, the_message);

              if (e.ndata.chatid === active_chatid) {
                wplc_push_message_to_chatbox(the_message,'a', function() {
                    jQuery.event.trigger({type: "bleeper_scroll_bottom"});
                });
              }
            }
          }
        }


    }
});