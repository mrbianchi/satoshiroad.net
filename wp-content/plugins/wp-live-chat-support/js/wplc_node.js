var nc_sid;
var nc_name;
var wplc_cid;
var socket;
var FADE_TIME = 150; // ms
var TYPING_TIMER_LENGTH = 1000; // ms
var username = 'Guest';
var connected = false;
var typing = false;
var lastTypingTime;
var nifty_is_chat_open = false;
var nifty_chat_status = "browsing";
var bleeper_show_drag_zone = false;

/* helps us keep track of which messages made it to the server */
var bleeper_msg_confirmations = {};

/**
 * Keep track of recent agents
 * @type {array}
 */
var bleeper_recent_agents = undefined;
var bleeper_recent_agents_data = undefined;

/**
 * Setup Query String, customer ID, and fingerprint
 */
var query_string = "";
var bleeper_customerID = null;
var bleeper_fingerprint = null;

/**
 * Set allowed file types
 */
var bleeper_file_suffix_check = [
  "zip", "pdf", "txt", "mp3", "mpa", "ogg", "wav", "wma", "7z", "rar",
  "db", "xml", "csv", "sql", "apk", "exe", "jar", "otf", "ttf", "fon",
  "fnt", "ai", "psd", "svg", "tif", "tiff", "ps", "msi", "doc", "docx",
];

var wplc_chat_delegates;
var wplc_connect;
var bleeper_ping = new Audio(wplc_baseurl + "/audio/ding.mp3");
var bleeper_inactive = false;
var bleeper_inactive_timeout;
var bleeper_timeout_duration = 300000;
var bleeperAverageResponseTime = undefined;

/**
 * An array to keep track of agent disconnects
 * @type {array}
 */
var agent_disc_timer = [];

/**
 * Set the default agent_joined variable
 * @type {array}
 */
var agent_joined = [];

/**
 * Used to identify the heartbeat timer
 */
var user_hearbeat;

/**
 * variable to check if the agent is online or not - this is set after the first run to the server
 * @type {Boolean}
 */
var wplc_online = false;

/**
 * Everytime the user clicks the minimize button this is set to true
 * @type {Boolean}
 */
var nifty_is_minimized = false;

/**
 * Used as a set up variables for the text editor
 */
var selectedIndexStart;
var selectedIndexEnd;
var checkSelection = true;

/**
 * Set up the global variable for the function
 */
var wplcFormatParser;

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
 * Sets default for identifying if the welcome message has been sent yet, or not
 * @type {Boolean}
 */
var nifty_welcome_message_sent = false;

var $inputMessage = '';
var $messages = '';
var bleeper_first;
var bleeper_current;
var bleeper_location_info = false;

/* Regex for inline links */
var bleeper_link_match_regex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!,.;<>]*[-A-Z0-9+&@#\/%=~_|<>])/ig;

/* OS Holder */
var bleeper_user_current_os = false; //When false the 'bleeper_get_operating_system' function will run all actions to identify the OS

/**
 * Generate a unique ID for the visitor
 *
 * @return {string} guid
 */
function wplc_jsguid() {
  var nav = window.navigator;
  var screen = window.screen;
  var guid = nav.mimeTypes.length;
  guid += nav.userAgent.replace(/\D+/g, '');
  guid += nav.plugins.length;
  guid += screen.height || '';
  guid += screen.width || '';
  guid += screen.pixelDepth || '';
  return guid;
};

/**
 * Setup the socket query variable, which is appended to the socket connection whenever the soket connects to the node servers
 */
function wplc_set_up_query_string() {
  if (typeof wplc_guid !== "undefined") {
    query_string += "&guid=" + wplc_guid;
  }

  if (typeof bleeper_user_ip_address !== "undefined") {
    query_string += "&user_ip=" + bleeper_user_ip_address;
  }

  if (typeof bleeper_location_info !== "undefined" && bleeper_location_info !== false) {
    if (typeof bleeper_location_info.code !== "undefined" && typeof bleeper_location_info.name !== "undefined") {
      query_string += "&location_code=" + bleeper_location_info.code + "&location_name=" + bleeper_location_info.name;
    }
  }

  bleeper_customerID = wplc_getCookie('bleeper_customerID');
  if (typeof bleeper_customerID !== "undefined" && bleeper_customerID !== '' && bleeper_customerID !== null) {
    query_string += "&customer_id=" + bleeper_customerID;
  }

  bleeper_fingerprint = wplc_jsguid();
  if (typeof bleeper_fingerprint !== "undefined" && bleeper_fingerprint !== '' && bleeper_fingerprint !== null) {
    query_string += "&bleeper_fingerprint=" + bleeper_fingerprint;
  }

  if (typeof window !== "undefined" && typeof window.location !== "undefined" && typeof window.location.href !== "undefined") {
    query_string += "&referer=" + window.location.href;
  }

  query_string = wplc_query_cleanup(query_string);
}

jQuery(document).on('wplc_sockets_ready', function () {
  console.log('here');

  /**
   * Run Query setup function
   */
  wplc_set_up_query_string();

  wplc_powered_by();

  /**
   * Setup an inactive timer
   */
  bleeper_inactive_timeout = setTimeout(function () {
      bleeper_inactive = true;
    }, bleeper_timeout_duration);

  if (typeof bleeper_message_override !== "undefined") {
    bleeper_ping = new Audio(bleeper_message_override);
  }

  /*Find nifty object and check if online */
  if (wplc_test_localStorage()) {

    var wplc_d = new Date();
    wplc_d.toUTCString();
    var cdatetime = Math.floor(wplc_d.getTime() / 1000);

    if (localStorage.getItem('bleeper_first') === null) {
      localStorage.setItem('bleeper_first', cdatetime);
      bleeper_first = cdatetime;
    } else {
      bleeper_first = localStorage.getItem('bleeper_first');
    }

    localStorage.setItem('bleeper_current', cdatetime);
    bleeper_current = cdatetime;

  }

  if (typeof ns_obj === 'undefined') {
    //Nifty Chat Object not created yet
  } else {
    if (ns_obj.o === '1') {
      wplc_online = true;
    } else {
      wplc_run = false;
      wplc_online = false;
    }
  }

  wplc_check_minimize_cookie = Cookies.get('nifty_minimize');

  var dragTimeout = -1;
  jQuery("html").bind("dragenter", function () {
    jQuery("#chat_drag_zone").fadeIn();
    bleeper_show_drag_zone = true;
  });

  jQuery("html").bind("dragover", function () {
    bleeper_show_drag_zone = true;
  });

  jQuery("html").bind("dragleave", function () {
    bleeper_show_drag_zone = false;
    clearTimeout(dragTimeout);
    dragTimeout = setTimeout(function () {
        if (!bleeper_show_drag_zone) {
          jQuery("#chat_drag_zone").fadeOut();
        }
      }, 200);
  });

  jQuery("#chat_drag_zone").on("dragover", function (event) {
    event.preventDefault();
    event.stopPropagation();
  });
  jQuery("#chat_drag_zone").on("dragleave", function (event) {
    event.preventDefault();
    event.stopPropagation();
  });
  jQuery("#chat_drag_zone").on("drop", function (event) {
    event.preventDefault();
    event.stopPropagation();
    if (jQuery('#nifty_add_media:checked').length > 0) {
      //Do nothing its open
    } else {
      jQuery("#nifty_add_media").click();
    }

    var fileInput = document.getElementById('nifty_file_input');
    fileInput.files = event.originalEvent.dataTransfer.files;
    jQuery("#chat_drag_zone").fadeOut();
  });

  /**
   * Builds the socket delegates. This needs to be called everytime a connection is made (i.e. moving from a short poll to a long poll)
   */
  wplc_chat_delegates = function (keepalive) {
    nifty_chat_status_temp = nc_getCookie("nc_status");
    if (typeof nifty_chat_status_temp !== "undefined" && nifty_chat_status_temp === "active") {
      /* leave the cookie untouched as we are already in ACTIVE state and should continue in this state until changed. */
    } else {
      if (keepalive) {
        niftyUpdateStatusCookie("active");
      } else {
        niftyUpdateStatusCookie("browsing");
      }
    }

    nifty_username_temp = nc_getCookie("nc_username");
    if (typeof nifty_username_temp !== "undefined") {
      username = nifty_username_temp;
    }

    // Socket events
    socket.on('connect', function (data) {
      nc_add_user(socket, data);

      nifty_chat_status_temp = nc_getCookie("nc_status");
      if (typeof nifty_chat_status_temp !== "undefined" && nifty_chat_status_temp === "active") {
        if (typeof user_hearbeat === "undefined") {
          user_hearbeat = setInterval(function () {
              if (socket.connected)
                socket.emit('heartbeat');
            }, 5000);
        }
      }
      jQuery.event.trigger({
        type: 'bleeper_socket_connected',
        status: nifty_chat_status_temp
      });

    });

    socket.on("force_disconnect", function (data) {

      socket.disconnect({
        test: 'test'
      });

      if (typeof user_heartbeat !== "undefined")
        clearInterval(user_hearbeat);
      user_heartbeat = undefined;
      /* reconnect this socket in 7 seconds to check for a forced chat on the agents end */
      setTimeout(function () {
        wplc_connect(false);
      }, 12000);
      /* its important that this number is less than the TTL of the variable in redis */
    });

    socket.on("blacklisted", function (data) {

      jQuery.event.trigger({
        type: "bleeper_blacklisted",
        ndata: data
      });

    });

    socket.on("user blocked", function (data) {
      socket.disconnect({
        blocked: 'blocked'
      });
      CookieDate.setFullYear(CookieDate.getFullYear() + 1);
      Cookies.set('bleeper_b', '1', {
        expires: CookieDate,
        path: '/'
      });
      jQuery("#wp-live-chat-4").remove();
      jQuery("#wp-live-chat-wraper").remove();
      keepalive = false;
    });

    socket.on("customerID", function (data) {
      var CookieDate = new Date;
      CookieDate.setFullYear(CookieDate.getFullYear() + 1);
      Cookies.set('bleeper_customerID', data.customerID, {
        expires: CookieDate,
        path: '/'
      });

    });

    socket.on("agent initiate", function (data) {
      if (typeof user_hearbeat === "undefined") {
        socket.emit('initiate received', {
          chatid: wplc_cid
        });
        user_hearbeat = setInterval(function () {
            if (socket.connected) {
              socket.emit('heartbeat');

            }
          }, 5000);
      }
      niftyUpdateStatusCookie('active');
      jQuery.event.trigger({
        type: "bleeper_agent_initiated_chat",
        ndata: data
      });

    });

    /* Confirm that a message was saved to the db */
    socket.on('message received', function (data) {
      if (typeof data !== 'undefined') {
        if (typeof data.msgID !== 'undefined' && typeof data.outcome !== 'undefined') {
          bleeper_msg_confirmations[data.msgID] = data.outcome;
        }
      }

    });

    socket.on('message read received', function (data) {
      jQuery.event.trigger({
        type: "bleeper_message_read_received",
        ndata: data
      });
    });

    socket.on('agent to participant ping', function (data) {
      socket.emit('agent to participant ping received', {
        fromsocket: socket.id,
        intendedsocket: data.fromsocket,
        chatid: data.chatid
      });
    });

    socket.on("chat ended", function (data) {
      jQuery.event.trigger({
        type: "bleeper_chat_ended_notification",
        ndata: data
      });

      jQuery("#bleeper_chat_ended").show();
      bleeper_end_chat_div_create();
      //$("#wplc_user_message_div").hide();

      if (typeof user_heartbeat !== "undefined")
        clearInterval(user_heartbeat);
      user_heartbeat = undefined;
      socket.disconnect({
        test: 'test'
      });
      niftyUpdateStatusCookie('browsing');
      // restart connection as a visitor
      if (typeof io !== "undefined") {
        wplc_set_up_query_string();
        socket = io.connect(WPLC_SOCKET_URI, {
            query: query_string,
            transports: ['websocket']
          });
        wplc_chat_delegates();
      }

      if (typeof Cookies !== "undefined") {
        Cookies.remove("wplc_cid");
      }
    });

    socket.on("averageResponse", function (data) {
      jQuery.event.trigger({
        type: "bleeper_average_response",
        ndata: data
      });

    });

    socket.on("recent_agents", function (data) {
      if (typeof data !== "undefined" && typeof data.agents !== "undefined") {
        bleeper_recent_agents = data.agents;
      }
    });

    socket.on("agent_data", function (data) {
      if ((typeof data !== "undefined" && data !== null) && (typeof data.ndata !== "undefined" && data.ndata !== null) && (typeof data.ndata.aid !== 'undefined' && data.ndata.aid !== null)) {
        if (typeof bleeper_recent_agents_data === "undefined") {
          bleeper_recent_agents_data = {};
          bleeper_recent_agents_data[data.ndata.aid] = data.ndata;
        } else {
          bleeper_recent_agents_data[data.ndata.aid] = data.ndata;
        }
      }
    });

    socket.on("transfer chat", function (data) {
      addNotice({
        message: 'You are being transferred to another agent. Please be patient.'
      });
    });

    socket.on("location found", function (data) {
      bleeper_location_info = data; //Set the data
    });

    socket.on('chat history', function (data) {
      jQuery.event.trigger({
        type: "bleeper_chat_history",
        ndata: data
      });

    });

    // Whenever the server emits 'login', log the login message
    socket.on('login', function (data) {

      connected = true;
      // Display the welcome message

      /**
       * Only show if this is the keepalive session (i.e. we are wanting to chat now)
       */
      if (keepalive) {
        var message = "Connection established";
      }

    });

    // Whenever the server emits 'new message', update the chat body
    socket.on('new message', function (data) {
      socket.emit('message read', data);
      jQuery.event.trigger({
        type: "bleeper_new_message",
        ndata: data
      });
      if (typeof wplc_enable_ding !== 'undefined' && '1' === wplc_enable_ding) {
        bleeper_ping.play();
      }
    });

    // Whenever the server emits 'new message', update the chat body
    socket.on('edit message', function (data) {
      jQuery.event.trigger({
        type: "bleeper_edit_message",
        ndata: data
      });
    });

    socket.on('user chat notification', function (data) {
      jQuery.event.trigger({
        type: "bleeper_user_chat_notification",
        ndata: data
      });
    });

    socket.on('custom data received', function (data) {
      jQuery.event.trigger({
        type: "bleeper_custom_data_received",
        ndata: data
      });
    });

    // Whenever the server emits 'new message', update the chat body
    socket.on('socketid', function (socketid) {
      document.cookie = "nc_sid=" + socketid.socketid;
      if (!wplc_online) {}
    });

    socket.on('agent joined', function (data) {
      clearTimeout(agent_disc_timer[data.agent]);
      jQuery.event.trigger({
        type: "bleeper_agent_joined",
        ndata: data
      });

    });

    socket.on('new_socket', function (socketid) {});

    socket.on('agent left', function (data) {
      jQuery.event.trigger({
        type: "bleeper_agent_left",
        ndata: data
      });

    });

    socket.on('agent connected', function (data) {
      clearTimeout(agent_disc_timer[data.aid]);
    })

    socket.on('agent disconnected', function (data) {

      agent_disc_timer[data.aid] = setTimeout(function () {
          jQuery.event.trigger({
            type: "bleeper_agent_disconnected",
            ndata: data
          });
          removeChatTyping(data);
        }, 8000);

    });

    // Whenever the server emits 'typing', show the typing message
    socket.on('typing', function (data) {
      jQuery.event.trigger({
        type: "bleeper_typing",
        ndata: data
      });

    });

    // Whenever the server emits 'stop typing', kill the typing message
    socket.on('stop typing', function (data) {
      jQuery.event.trigger({
        type: "bleeper_stop_typing",
        ndata: data
      });
    });

    // Receive CHAT ID from server
    socket.on('chatID', function (data) {
      Cookies.set('wplc_cid', data.chatid, {
        expires: 1,
        path: '/'
      });

      wplc_cid = data.chatid;

      /* is chat box open? */
      if (!nifty_is_chat_open) {
        nifty_init_chat_box_check(data.chatid);
      }
    });

    socket.on("involved check returned", function (data) {
      jQuery.event.trigger({
        type: 'bleeper_build_involved_agents_header',
        ndata: data
      });
    });

    socket.on('disconnect', function () {
      if (typeof user_heartbeat !== "undefined")
        clearInterval(user_heartbeat);
      user_heartbeat = undefined;
      /**
       * Only show if this was part of the keepalive session (i.e. an active chat)
       */
      if (keepalive) {
        //log('you have been disconnected'); //TODO: Add Bleeper handler as well
        jQuery.event.trigger({
          type: "bleeper_disconnected"
        });
      }
    });

    socket.on('reconnect', function () {
      /**
       * Only show if this was part of the keepalive session (i.e. an active chat)
       */
      if (keepalive) {
        //log('you have been reconnected'); //TODO: Addd bleeper handler
        jQuery.event.trigger({
          type: "bleeper_reconnect"
        });
      }
      nc_add_user(socket, 'what the shizz');
    });

    socket.on('reconnect_error', function () {
      //log('attempt to reconnect has failed'); //TODO: Addd bleepet handler
      jQuery.event.trigger({
        type: "bleeper_reconnect_error"
      });
    });

    socket.on('a2vping', function (data) {
      socket.emit('a2vping return', {
        fromsocket: socket.id,
        intendedsocket: data.returnsocket,
        chatid: data.chatid
      });
    })
  }

  $messages = jQuery('#wplc_chatbox'); // Messages area
  $inputMessage = jQuery('#wplc_chatmsg'); // Input message input box

  jQuery("#nifty_file_input").on("change", function () {
    var file = this.files[0]; //Last file in array
    wplcShareFile(file, '#nifty_attach_fail_icon', '#nifty_attach_success_icon', '#nifty_attach_uploading_icon', "#nifty_select_file");
    jQuery("#chat_drag_zone").fadeOut();
  });

  /** Image pasting functionality */
  try {
    document.getElementById('wplc_chatmsg').onpaste = function (event) {
      // use event.originalEvent.clipboard for newer chrome versions
      var items = (event.clipboardData || event.originalEvent.clipboardData).items;
      // find pasted image among pasted items
      var blob = null;
      for (var i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("image") === 0) {
          blob = items[i].getAsFile();
        }
      }
      // load image if there is a pasted image
      if (blob !== null) {
        var reader = new FileReader();
        reader.onload = function (event) {
          document.getElementById("wplc_chatmsg").value = "####" + event.target.result + "####";

          jQuery("#wplc_send_msg").click();
          jQuery("#bleeper_send_msg").click();

        };
        reader.readAsDataURL(blob);
      }
    }
  } catch (ex) {}

  jQuery("#nifty_tedit_b").click(function () {
    niftyTextEdit("b");
  });
  jQuery("#nifty_tedit_i").click(function () {
    niftyTextEdit("i");
  });
  jQuery("#nifty_tedit_u").click(function () {
    niftyTextEdit("u");
  });
  jQuery("#nifty_tedit_strike").click(function () {
    niftyTextEdit("strike");
  });
  jQuery("#nifty_tedit_mark").click(function () {
    niftyTextEdit("mark");
  });
  jQuery("#nifty_tedit_sub").click(function () {
    niftyTextEdit("sub");
  });
  jQuery("#nifty_tedit_sup").click(function () {
    niftyTextEdit("sup");
  });
  jQuery("#nifty_tedit_link").click(function () {
    niftyTextEdit("link");
  });
  setInterval(function () {
    getText(document.getElementById("wplc_chatmsg"));
  }, 1000);
  /**
   * End of rich text functionality
   */

  /* find out if we have had a chat with this visitor before */
  sid = nc_getCookie("wplc_sid");
  nifty_chat_status_temp = nc_getCookie("nc_status");
  if (nifty_chat_status_temp !== "undefined") {
    nifty_chat_status = nifty_chat_status_temp;
  }
  chatid = nc_getCookie("wplc_cid");
  if (chatid !== "undefined") {
    wplc_cid = chatid;
    nc_name = nc_getCookie("nc_username");
  }

  if (window.console) {
    console.log("[WPLC] Connecting to " + WPLC_SOCKET_URI);
  }

  /* is socket.io ready yet? */

  /* blocked? */
  var bleeper_b = wplc_getCookie('bleeper_b');
  if (typeof bleeper_b !== "undefined" && bleeper_b === '1') {
    console.log("[WPLC] You have been blocked from using WP Live Chat Support");
    return;
  } else {
    if (typeof io !== "undefined") {
      wplc_set_up_query_string();
      socket = io.connect(WPLC_SOCKET_URI, {
          query: query_string,
          transports: ['websocket']
        });

    } else {
      var socketchecker = setInterval(function () {
          if (typeof io !== "undefined") {
            clearInterval(socketchecker);
            wplc_set_up_query_string();
            socket = io.connect(WPLC_SOCKET_URI, {
                query: query_string,
                transports: ['websocket']
              });

          }
        }, 1000);
    }

    wplc_chat_delegates();
  }

  /**
   * Connect the node socket
   *
   * @param {bool} keepalive Keep this connection alive?
   */
  wplc_connect = function (keepalive) {

    if (bleeper_inactive === false) {
      if (typeof socket !== "undefined") {
        if (socket.connected) {

          /* already connected */
        } else {
          //opening socket connection
          wplc_set_up_query_string();
          socket = io.connect(WPLC_SOCKET_URI, {
              query: query_string,
              transports: ['websocket']
            });

          wplc_chat_delegates(keepalive);
        }
      } else {
        //opening socket connection2
        wplc_set_up_query_string();
        socket = io.connect(WPLC_SOCKET_URI, {
            query: query_string,
            transports: ['websocket']
          });

        wplc_chat_delegates(keepalive);
      }
    } else {

      /* try again in 7 seconds */
      setTimeout(function () {
        if (socket.connected) {}
        else {
          wplc_connect(false);
        }
      }, 7000);
    }

  }

  // Initialize variables
  var $window = jQuery(window);

  var message_preview_currently_being_typifcationed;

  /**
   * Detect if the user is active or inactive.
   *
   * This manipulates the shortpoll connection to the server
   *
   * i.e. an inactive user will not send shortpolls.
   */
  jQuery(document).on('mousemove', function () {

    clearTimeout(bleeper_inactive_timeout);
    bleeper_inactive = false;
    bleeper_inactive_timeout = setTimeout(function () {
        bleeper_inactive = true;
      }, bleeper_timeout_duration);
  });

  document.addEventListener('bleeper_send_message', function (e) {

    if (typeof wplc_online !== 'undefined' && wplc_online === true) {
      socket.emit('stop typing', {
        chatid: wplc_cid
      });
    }
    // reset the typing variable
    typing = false;
    // reset the niftyIsEditing variable
    niftyIsEditing = false;
  }, false);

  jQuery(document).on("bleeper_send_message", function (e) {
    //sendMessage(e.message);
    if (typeof wplc_online !== 'undefined' && wplc_online === true) {
      socket.emit('stop typing', {
        chatid: wplc_cid
      });
    }
    // reset the typing variable
    typing = false;
    // reset the niftyIsEditing variable
    niftyIsEditing = false;
  });

  // Keyboard events

  jQuery(document).on("keydown", "#wplc_chatmsg", function (event) {
    // When the client hits ENTER on their keyboard
    if (event.which === 13 && !event.shiftKey) {

      if (jQuery(this).val().trim() !== '') {
        event.preventDefault();
        jQuery("#wplc_send_msg").click();
        jQuery("#bleeper_send_msg").click();
      }

    } else if (event.which === 38 && !event.shiftKey) {
      if (typeof lastmessagesent !== "undefined") {
        if (typeof wplc_integration_pro_active !== "undefined" && wplc_integration_pro_active === "true") {
          var mid = lastmessagesent;
          var mdiv = jQuery('.message_' + mid + " .messageBody").html();
          jQuery("#wplc_chatmsg").val(mdiv);
          // set the niftyIsEditing variable to the msgID so we can identify if we are in the process of editing a message
          niftyIsEditing = mid;
        }
      }

    } else if (event.which === 27 && !event.shiftKey) {
      jQuery("#wplc_chatmsg").val('');
      niftyIsEditing = false;
    }

  });

  $inputMessage.keyup(function (event) {
    // When the client hits ENTER on their keyboard

    if (event.which === 13 && !event.shiftKey) {}
    else {
      if (typeof wplc_online !== 'undefined' && wplc_online === true) {
        var typing_preview_tmp = cleanInput($inputMessage.val());
        socket.emit('typing_preview', {
          chatid: wplc_cid,
          tempmessage: typing_preview_tmp
        });
      }
    }
  });

  $inputMessage.on('input', function () {
    updateTyping();
  });

  // Click events

  // Focus input when clicking on the message input's border
  $inputMessage.click(function () {
    $inputMessage.focus();
  });

  jQuery(document).on("mouseleave", ".message", function () {
    var tmid = jQuery(this).attr('mid');
    jQuery(".message_" + tmid + " .bleeper-edit-message").hide();
  });

  jQuery(document).on("mouseenter", ".message", function () {
    var tmid = jQuery(this).attr('mid');
    jQuery(".message_" + tmid + " .bleeper-edit-message").show();
  });

  jQuery(document).on("click", ".bleeper_restart_chat", function () {
    jQuery("#wp-live-chat-header").click();
    setTimeout(function () {
      jQuery("#wp-live-chat-header").click();
    }, 50);

    jQuery('#wplc_end_chat_button').show();
    jQuery('#wplc_end_chat_button').removeAttr('wplc_disable');
  });

  jQuery(document).on("click", "#wplc_send_msg", function () {
    var message = $inputMessage.val();
    if(message.length > 2000){
      message = message.substring(0, 2000);
    }
    sendMessage(message);
  });

  jQuery(document).on("click", ".bleeper-edit-message", function () {
    var mid = jQuery(this).parent().attr('mid');
    var mdiv = jQuery(this).siblings('.messageBody').attr("data-message");
    jQuery("#wplc_chatmsg").val(mdiv);

    // set the niftyIsEditing variable to the msgID so we can identify if we are in the process of editing a message
    niftyIsEditing = mid;
  });


jQuery(document).on("nifty_trigger_open_chat", function (event) {
  open_chat();
  jQuery("#bleeper_chat_ended").hide();
});

jQuery(document).on("bleeper_socket_connected", function (e) {
  if (typeof socket !== "undefined" && typeof nifty_chat_status !== "undefined") {
    if (nifty_chat_status === "active") {
      socket.emit('check involved agents', {
        chatid: chatid
      });
    }
  }
});

jQuery(document).on("wplc_animation_done", function (event) {
  if (typeof wdtEmojiBundle !== "undefined") {
    wdtEmojiBundle.defaults.emojiSheets = {
      'apple'    : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_apple_64_indexed_128.png',
      'google'   : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_google_64_indexed_128.png',
      'twitter'  : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_twitter_64_indexed_128.png',
      'emojione' : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_emojione_64_indexed_128.png',
      'facebook' : wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_facebook_64_indexed_128.png',
      'messenger': wplc_baseurl + '/js/vendor/wdt-emoji/sheets/sheet_messenger_64_indexed_128.png'
    };
    bleeper_attempt_emoji_input_init(0);
  }
});

/* Minimize chat window */
jQuery("#wp-live-chat-minimize").on("click", function () {
  jQuery.event.trigger({
    type: "nifty_minimize_chat"
  });
  Cookies.set('nifty_minimize', "yes", {
    expires: 1,
    path: '/'
  });
  nifty_is_minimized = true;
});

/**
 * Click handler for the start chat button
 */
jQuery("#wplc_start_chat_btn").on("click", function () {
  var wplc_is_gdpr_enabled = jQuery(this).attr('data-wplc-gdpr-enabled');
  if (typeof wplc_is_gdpr_enabled !== "undefined" && (wplc_is_gdpr_enabled === 'true')) {
    var wplc_gdpr_opt_in_checked = jQuery("#wplc_chat_gdpr_opt_in").is(':checked');
    if (typeof wplc_gdpr_opt_in_checked === "undefined" || wplc_gdpr_opt_in_checked === false) {
      /* GDPR requirements not met */
      jQuery("#wplc_chat_gdpr_opt_in").addClass('incomplete');
      return false;
    }
    jQuery("#wplc_chat_gdpr_opt_in").removeClass('incomplete');
  }

  var wplc_name = jQuery("#wplc_name").val();
  var wplc_email = jQuery("#wplc_email").val();

  if (wplc_name.length <= 0) {
    alert("Please enter your name");
    return false;
  }
  if (wplc_email.length <= 0) {
    alert("Please enter your email address");
    return false;
  }

  if (jQuery("#wplc_email").attr('wplc_hide') !== "1") {
    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,6}$/i;
    if (!testEmail.test(wplc_email)) {
      alert("Please Enter a Valid Email Address");
      return false;
    }
  }

  jQuery.event.trigger({
    type: "nifty_trigger_start_chat"
  });

  var date = new Date();
  date.setTime(date.getTime() + (2 * 60 * 1000));

  niftyUpdateUserDataCookies(wplc_name, wplc_email);
  niftyUpdateGravCookie(md5(wplc_email));
  niftyUpdateStatusCookie("active");

  wplc_connect(true);
  var request_chat_checker = setInterval(function () {
      if (typeof socket !== "undefined" && typeof socket.connected !== "undefined" && socket.connected === true) {
        clearInterval(request_chat_checker);
        socket.emit("request chat", {
          chatid: wplc_cid,
          name: wplc_name,
          email: wplc_email
        });
      } else {
        //still not connected, trying again
      }
    }, 300);
  });
}); // document.ready

/**
 * Add a log to the chat box
 *
 * @param {string} message Log message string
 * @param {object} options Options for the message being added (fade, prepend)
 */
function log(message, options) {
  var $el = jQuery('<li>').addClass('log').text(message);
  addMessageElement($el, options);
}

/**
 * Add a notice to the chat box
 *
 * @param {object} data Chat message data packet
 * @param {object} options Options for the message being added (fade, prepend)
 */
function addNotice(data, options) {
  options = options || {};
  var new_item = "";
  if (options.is_admin) {
    new_item += "<li class='message wplc-admin-notice' />";
  } else {
    new_item += "<li class='message wplc-user-notice' />";
  }

  var $messageBodyDiv = jQuery('<span class="noticeBody">')
    .html(wplcFormatParser(data.message));

  var $messageDiv = jQuery(new_item)
    .append($messageBodyDiv)

    addMessageElement($messageDiv, options);
}

/**
 * Remove any remaining 'typing messages'
 *
 * @param {object} data Data to check
 */
function removeChatTyping(data) {
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
function addMessageElement(el, options) {
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
  $messages[0].scrollTop = $messages[0].scrollHeight;
}

/**
 * Update the typing statu on the socket
 */
function updateTyping() {
  if (connected) {
    if (!niftyIsEditing) {
      if (!typing) {
        typing = true;
        socket.emit('typing', {
          chatid: wplc_cid
        });
      }
      lastTypingTime = (new Date()).getTime();

      setTimeout(function () {
        var typingTimer = (new Date()).getTime();
        var timeDiff = typingTimer - lastTypingTime;
        if (timeDiff >= TYPING_TIMER_LENGTH && typing) {
          if (typeof wplc_online !== 'undefined' && wplc_online === true) {
            socket.emit('stop typing', {
              chatid: wplc_cid
            });
          }
          typing = false;
        }
      }, TYPING_TIMER_LENGTH);
    }
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
function cleanInput(input) {
  var input_cleaned = input;
  if (typeof input_cleaned !== 'string') {
    return input_cleaned;
  }
  return input_cleaned.replace(/<(?:.|\n)*?>/gm, '');
}

/**
 * Get the username of the person who is typing (Example: Agent is typing)
 *
 * @param {object} data Packet to check
 */
function getTypingMessages(data) {
  return jQuery('.typing.message').filter(function (i) {
    return jQuery(this).data('username') === data.username;
  });
}

/**
 * Send a chat message using the socket. Also checks if this is a new message, or an edit to an existing message.
 *
 * @param {string} message Message to be sent
 */
function sendMessage(message) {
  // Prevent markup from being injected into the message
  message = cleanInput(message);

  if (typeof bleeper_convert_colon_to_uni !== "undefined") {
    message = bleeper_convert_colon_to_uni(message);
  }

  if (niftyIsEditing !== false) {
    // we edited a message
    msgID = parseInt(niftyIsEditing);
    jQuery(".message_" + msgID + " .messageBody").attr("data-message", message);
    jQuery(".message_" + msgID + " .messageBody").html(wplcFormatParser(message));
    socket.emit('edit message', {
      message: message,
      chatid: wplc_cid,
      msgID: msgID
    });

    jQuery.event.trigger({
      type: "bleeper_edited_message",
      ndata: {
        message: message,
        chatid: wplc_cid,
        msgID: msgID
      }
    });

    jQuery("#wplc_chatmsg").val("");
    niftyIsEditing = false;

  } else {
    var randomNum = Math.floor((Math.random() * 100) + 1);

    var msgID = Date.now() + randomNum;
    lastmessagesent = msgID;

    var ndata = {
      username: username,
      message: message,
      aoru: 'u',
      msgID: msgID,
      is_admin: false
    }

    jQuery.event.trigger({
      type: "bleeper_send_message",
      message: message,
      msg_id: msgID
    });
    jQuery.event.trigger({
      type: "bleeper_new_message",
      ndata: ndata,
      msgID: msgID
    });

    // tell server to execute 'new message' and send along one parameter
    var msgObject = {
      message: message,
      chatid: wplc_cid,
      msgID: msgID,
      aoru: 'u'
    };
    socket.emit('new message', msgObject);

    /* run timer to check if message was delivered! */
    bleeperConfirmDelivery(msgID, msgObject);

  }
}

/**
 * Update the chat status cookie
 *
 * @param {string} new_status The status you would like to store
 */
function niftyUpdateStatusCookie(new_status) {
  Cookies.set('nc_status', new_status, {
    expires: 1,
    path: '/'
  });
}

/**
 * Update the visitors gravatar cookie
 *
 * @param {string} grav_hash Gravatar Hash (MD5 of email address)
 */
function niftyUpdateGravCookie(grav_hash) {
  Cookies.set('wplc_grav_hash', grav_hash, {
    expires: 1,
    path: '/'
  });

  wplc_cookie_grav_hash = grav_hash;
}

/**
 * Update the name and email cookies
 *
 * @param {string} name Name of the visitor
 * @param {string} email Email of the visitor
 */
function niftyUpdateUserDataCookies(name, email) {
  Cookies.set('wplc_name', name, {
    expires: 1,
    path: '/'
  });
  Cookies.set('wplc_email', email, {
    expires: 1,
    path: '/'
  });

  wplc_cookie_name = name;
  wplc_cookie_email = email;
}

/**
 * Open the chat box
 *
 * @param {bool} force Force open regardless of state
 */
var open_chat = function (force) {
  var tmp_cookie_val = nc_getCookie('nifty_minimize');
  nifty_is_minimized = tmp_cookie_val == '' || tmp_cookie_val == 'false' || tmp_cookie_val == false ? false : true;

  nifty_chat_status_temp = nc_getCookie("nc_status");
  wplc_chat_status_temp = nc_getCookie("wplc_chat_status");

  if (nifty_chat_status_temp === "active") {
    niftyUpdateStatusCookie("active");
    wplc_connect(true);

    if (!nifty_is_minimized) {
      jQuery.event.trigger({
        type: "nifty_trigger_open_chat_2",
        wplc_online: wplc_online
      });
      nifty_is_chat_open = true;
    }
  } else if (nifty_chat_status_temp === "browsing" || wplc_chat_status_temp === "5") { //Added 11 here for usability
    if (jQuery("#wp-live-chat-2").is(":visible") === false && jQuery("#wp-live-chat-4").is(":visible") === false) {
      jQuery("#wp-live-chat-2").show();
      jQuery("#wp-live-chat-header").addClass("active");
    }
  }
}

/**
 * Get the selection range on the current element
 *
 * @param {element} elem The element you would like to check
 */
function getText(elem) {
  if (checkSelection) {
    if (selectedIndexStart !== elem.selectionStart) {
      selectedIndexStart = elem.selectionStart;
    }
    if (selectedIndexEnd !== elem.selectionEnd) {
      selectedIndexEnd = elem.selectionEnd;
    }
  }

}

/**
 * Legacy code for the hidden text editor which autmatically adds tags like 'link:' or 'mark:'
 *
 * Depracated, but supported for legacy users
 *
 * @param {string} insertContent Tag to insert
 */
function niftyTextEdit(insertContent) {
  if (typeof selectedIndexStart !== "undefined" && typeof selectedIndexEnd !== "undefined") {
    checkSelection = false;
    /*Text editor Code here*/

    jQuery("#wplc_chatmsg").focus();

    var current = jQuery("#wplc_chatmsg").val();

    var pre = current.substr(0, (selectedIndexStart > 0) ? selectedIndexStart : 0);
    var selection = current.substr(selectedIndexStart, selectedIndexEnd - selectedIndexStart);
    var post = current.substr(((selectedIndexEnd < current.length) ? selectedIndexEnd : current.length), current.length);

    current = pre + insertContent + ":" + selection + ":" + insertContent + post;
    jQuery("#wplc_chatmsg").val(current);

    checkSelection = true;
  }
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
function wplcShareFile(fileToUpload, failedID, successID, uploadingID, originalID) {
  var formData = new FormData();
  formData.append('file', fileToUpload);
  formData.append('timestamp', Date.now());
  jQuery(uploadingID).show();
  jQuery(originalID).hide();
  jQuery(successID).hide();
  jQuery(failedID).hide();

  var uploadUrl = '';
  uploadUrl = (typeof bleeper_override_upload_url !== "undefined" && bleeper_override_upload_url !== "") ? bleeper_override_upload_url : uploadUrl;

  if (fileToUpload.name.indexOf(".php") === -1 && fileToUpload.name.indexOf(".html") === -1 && fileToUpload.name.indexOf(".asp") === -1 && fileToUpload.name.indexOf(".svg") === -1) {
    //Files allowed - continue
    if (fileToUpload.size < 8000000) {
      jQuery.ajax({
        url: uploadUrl,
        type: 'POST',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
          if (parseInt(data) !== 0) {
            jQuery(uploadingID).hide();
            jQuery(successID).show();
            setTimeout(function () {
              jQuery(successID).hide();
              jQuery(originalID).show();
            }, 2000);

            //All good post the link to file
            var fileLinkUrl = false;
            if (!bleeperIsJson(data)) {
              //This is not a parsable JSON string
              if (typeof data !== "object") {
                fileLinkUrl = data;
              } else {
                if (typeof data.response !== "undefined") {
                  //Our url is in response index
                  fileLinkUrl = data.response;
                } else {
                  fileLinkUrl = data;
                }
              }

            } else {
              //This is a parsable JSON string which will now be converted into an object
              var dataPacket = JSON.parse(data);
              if (typeof dataPacket.response !== "undefined") {
                //Our url is in response index
                fileLinkUrl = dataPacket.response;
              } else {
                fileLinkUrl = data;
              }
            }

            if (fileLinkUrl !== false) {
              var tag = (fileLinkUrl.indexOf(".png") !== -1 || fileLinkUrl.indexOf(".PNG") !== -1 || fileLinkUrl.indexOf(".jpg") !== -1 || fileLinkUrl.indexOf(".JPG") !== -1 || fileLinkUrl.indexOf(".jpeg") !== -1 || fileLinkUrl.indexOf(".JPEG") !== -1 || fileLinkUrl.indexOf(".gif") !== -1 || fileLinkUrl.indexOf(".GIF") !== -1 || fileLinkUrl.indexOf(".bmp") !== -1 || fileLinkUrl.indexOf(".BMP") !== -1) ? "img" : "link";

              if (tag !== "img") {
                tag = (fileLinkUrl.indexOf(".mp4") !== -1 || fileLinkUrl.indexOf(".mpeg4") !== -1 || fileLinkUrl.indexOf(".webm") !== -1 || fileLinkUrl.indexOf(".oog") !== -1) ? "video" : "link"; //video now
              }
              jQuery("#wplc_chatmsg").val(tag + ":" + fileLinkUrl + ":" + tag); //Add to input field
              jQuery("#wplc_send_msg").trigger("click"); //Send message
              jQuery("#bleeper_send_msg").trigger("click"); //Send message
              setTimeout(function () {
                $messages[0].scrollTop = $messages[0].scrollHeight;
              }, 1000);
            }
          } else {
            jQuery(uploadingID).hide();
            jQuery(failedID).show();
            setTimeout(function () {
              jQuery(failedID).hide();
              jQuery(originalID).show();
            }, 2000);

          }
        },
        error: function () {
          jQuery(uploadingID).hide();
          jQuery(failedID).show();
          setTimeout(function () {
            jQuery(failedID).hide();
            jQuery(originalID).show();
          }, 2000);

        }
      });
    } else {
      alert("File limit is 8mb");
      jQuery(uploadingID).hide();
      jQuery(failedID).show();
      setTimeout(function () {
        jQuery(failedID).hide();
        jQuery(originalID).show();
      }, 2000);
    }
  } else {
    alert("File type not supported");
    jQuery(uploadingID).hide();
    jQuery(failedID).show();
    setTimeout(function () {
      jQuery(failedID).hide();
      jQuery(originalID).show();
    }, 2000);
  }
}

/**
 * Process all inline formatting (For example: bold, italic, preformatted, etc) within a message
 *
 * @param {string} msg The chat message
 * @return {string} The formatted message
 */
var wplcFormatParser = function (msg) {
  var bypass_inline_links = false;
  // This is now handled easily with an inline link handler
  msg = msg.replace(/link:(.+?):link/g, "$1");

  if (msg.indexOf("video:") !== -1) {
    msg = msg.replace(/video:/g, "<video style='background-color: black; max-width:100%;' src='");
    bypass_inline_links = true; //There is a video being processed, let's leave it alone
  }
  if (msg.indexOf(":video") !== -1) {
    msg = msg.replace(/:video/g, "' controls></video>");
    bypass_inline_links = true; //There is a video being processed, let's leave it alone
  }

  if (msg.indexOf('wplc-ds-wrapper') !== -1) {
    bypass_inline_links = true;
  }

  if (msg.indexOf("mark:") !== -1) {
    msg = msg.replace(/mark:/g, "<mark>");
  }
  if (msg.indexOf(":mark") !== -1) {
    msg = msg.replace(/:mark/g, "</mark>");
  }

  if (msg.indexOf("strike:") !== -1) {
    msg = msg.replace(/strike:/g, "<del>");
  }
  if (msg.indexOf(":strike") !== -1) {
    msg = msg.replace(/:strike/g, "</del>");
  }

  if (msg.indexOf("sub:") !== -1) {
    msg = msg.replace(/sub:/g, "<sub>");
  }
  if (msg.indexOf(":sub") !== -1) {
    msg = msg.replace(/:sub/g, "</sub>");
  }
  if (msg.indexOf("sup:") !== -1) {
    msg = msg.replace(/sup:/g, "<sup>");
  }
  if (msg.indexOf(":sup") !== -1) {
    msg = msg.replace(/:sup/g, "</sup>");
  }
  //run singles last

  // Fix double emojis
  if (msg.search(/\:(\S+)(\:)(\S+)\:/g) !== -1) {
    msg = msg.replace(/\:(\S+)(\:)(\S+)\:/g, function (match, p1, p2, p3) {
        return [":", p1, "::", p3, ":"].join('');
      });
  }

  msg = wplc_emoji_render(msg); //First render the emojis - Preventing <em>'s from breaking the content


  var italics_match = msg.match(/_([^*]*?)_/g);
  if (italics_match !== null) {
    for (var i = 0, len = italics_match.length; i < len; i++) {
      var to_find = italics_match[i];
      var to_replace = to_find.substring(1, to_find.length - 1); // remove the starting _ and ending _
      msg = msg.replace(to_find, "<em>" + to_replace + "</em>");

    }
  }

  /* New IMG processor */
  var image_match = msg.match(/img:([^*]*?):img/g);
  if (image_match !== null) {
    for (var i = 0, len = image_match.length; i < len; i++) {
      var to_find = image_match[i];
      var to_replace = to_find.substring(4, to_find.length - 4); // remove the starting #### and ending ####
      msg = msg.replace(to_find, "<img style='max-width:100%;' src='" + bleeper_url_path_em_stripper(to_replace.replace("::", ":")) + "' />");
      bypass_inline_links = true;

    }
  }

  var image_match = msg.match(/####([^*]*?)####/g);
  if (image_match !== null) {
    for (var i = 0, len = image_match.length; i < len; i++) {
      var to_find = image_match[i];
      var to_replace = to_find.substring(4, to_find.length - 4); // remove the starting #### and ending ####
      msg = msg.replace(to_find, "<img src='" + bleeper_url_path_em_stripper(to_replace) + "' />");
      bypass_inline_links = true; //There is an image being processed, let's leave it alone

    }
  }

  var bold_match = msg.match(/\*\s*([^*]*?)\s*\*/g);
  if (bold_match !== null) {
    for (var i = 0, len = bold_match.length; i < len; i++) {
      var to_find = bold_match[i];
      var to_replace = to_find.substring(1, to_find.length - 1); // remove the starting * and ending *
      msg = msg.replace(to_find, "<strong>" + to_replace + "</strong>");

    }
  }

  var pre_match = msg.match(/```([^*]*?)```/g);
  if (pre_match !== null) {
    for (var i = 0, len = pre_match.length; i < len; i++) {
      var to_find = pre_match[i];
      var to_replace = to_find.substring(3, to_find.length - 3); // remove the starting ``` and ending ```
      msg = msg.replace(to_find, "<pre>" + to_replace + "</pre>");

    }
  }

  var code_match = msg.match(/`([^*]*?)`/g);
  if (code_match !== null) {
    for (var i = 0, len = code_match.length; i < len; i++) {
      var to_find = code_match[i];
      var to_replace = to_find.substring(1, to_find.length - 1); // remove the starting ` and ending `
      msg = msg.replace(to_find, "<code>" + to_replace + "</code>");

    }
  }

  msg = msg.replace(/\n/g, "<br />");

  if (bypass_inline_links === false) {
    msg = bleeper_inline_link_generator(msg);
  }

  return msg;

}

/**
 * Fire off needed events to confirm message delivry event
 *
 * @param {string} msgID Message ID
 * @param {object} msgObject Message object
 */
function bleeperConfirmDelivery(msgID, msgObject) {
  jQuery.event.trigger({
    type: 'bleeper_trigger_check_message_received',
    msgID: msgID,
    msgObject: msgObject
  });
}

/**
 * Create the end chat div, which holds the restart chat button
 */
function bleeper_end_chat_div_create() {
  jQuery('<a/>', {
    'class': 'bleeper_restart_chat',
    href: "javascript:void(0);",
    title: "Restart chat",
    html: "Restart chat",
  }).appendTo('#bleeper_chat_ended');

}

/**
 * Render emojis within a message string
 *
 * @param {string} msg The chat message
 * @return {string} The chat message with emojis
 */
function wplc_emoji_render(msg) {
  if (typeof wdtEmojiBundle !== "undefined") {
    msg = wdtEmojiBundle.render(msg);
  }
  return msg;
}

/**
 * Add the user/socket as a user by sending all needed data to the server
 *
 * @param {socket} socket The current users socket
 * @param {data} data The visitor data packet
 */
function nc_add_user(socket, data) {
  var data = {};
  /* recurring visitor */
  /* find out if we have had a chat with this visitor before */
  chatid = nc_getCookie("wplc_cid");
  if (typeof chatid !== "undefined") {
    wplc_cid = chatid;
    nc_name = nc_getCookie("nc_username");
    wplc_name = nc_getCookie("wplc_name");
  }

  var bleeper_customerID = wplc_getCookie('bleeper_customerID');
  if (typeof bleeper_customerID !== "undefined" && bleeper_customerID !== '' && bleeper_customerID !== null) {
    data.customerID = bleeper_customerID;
  }

  /* blocked? */
  var bleeper_b = wplc_getCookie('bleeper_b');
  if (typeof bleeper_b !== "undefined" && bleeper_b === '1') {
    console.log("[WPLC] You have been blocked from using WP Live Chat Support");
    return;

  } else {

    if (typeof chatid !== "undefined") {
      if (typeof nc_name !== "undefined") {
        data.username = nc_name;
      } else {
        if (typeof wplc_name !== 'undefined') {
          data.username = wplc_name;
        } else {
          data.username = 'Guest';
        }
      }
      data.api_key = bleeper_api_key;
      data.wplc_cid = chatid;
    } else {
      /* first time user */
      data.username = 'Guest';
      data.api_key = bleeper_api_key;
      data.wplc_cid = null;
    }

    data.date_first = bleeper_first;
    data.date_current = bleeper_current;

    data.timezoneUTC = bleeper_get_timezone();
    data.device_in_use = bleeper_get_device_in_use();
    data.operating_system = bleeper_get_operating_system();

    if (typeof bleeper_location_info !== "undefined" && bleeper_location_info !== false) {
      data.location_info = bleeper_location_info;
    }

    if (typeof wplc_extra_data !== 'undefined' && typeof wplc_extra_data['wplc_user_selected_department'] !== 'undefined') {
      data.department = wplc_extra_data['wplc_user_selected_department'];
    }

    /**
     * Let's identify if this user is a visitor. If they are, lets set the connection type to "SHORT" so that the connection can be dropped immediately once data is received.
     * This negates the need for having the socket stay open for visitors.
     */
    nc_status = nc_getCookie("nc_status");

    if (typeof nc_status === "undefined" || nc_status === "browsing") {
      data.connectiontype = "short";
    }

    socket.emit('add user', data);
  }

}

/**
 * Get a specific cookie's value
 *
 * @param  {string} name Cookie key
 * @return {string} Cookie value
 */
function nc_getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2)
    return parts.pop().split(";").shift();
}

/**
 * Checks to see if the init chat box function has been loaded (via another JS file). If not, it will recursively keep trying until it has been loaded
 *
 * @param   {int} cid Chat ID
 * @return  {void}
 */
function nifty_init_chat_box_check(cid) {
  if (typeof wplc_init_chat_box === "function") {
    wplc_init_chat_box(cid);
  } else {
    if (typeof wplc_init_chat_box !== "undefined" && wplc_init_chat_box !== false) {
      setTimeout(function () {
        /* keep checking every 500ms to see if that function exists */
        nifty_init_chat_box_check(cid);
      }, 500);
    }
  }
}

/**
 * Check if string is JSON object
 *
 * @param {string} str String to check
 * @return {bool} True if string is JSON
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
 * Test to make sure localStorage exists and is enabled
 *
 * @return {bool} True if locale storage is available, false if not
 */
function wplc_test_localStorage() {
  if (typeof localStorage !== 'undefined') {
    try {
      localStorage.setItem('bleeper_test', 'yes');
      if (localStorage.getItem('bleeper_test') === 'yes') {
        localStorage.removeItem('bleeper_test');
        return true;
        // localStorage is enabled
      } else {
        return false;
        // localStorage is disabled
      }
    } catch (e) {
      return false;
      // localStorage is disabled
    }
  } else {
    return false;
    // localStorage is not available
  }
}

/*
 * Returns a user readable timezone difference from UTC (ex: +2 which is UTC+2)
 *
 * @return {string} The best guess of the users timezone.
 */
function bleeper_get_timezone() {
  var offsetFromUTC = new Date().getTimezoneOffset();
  var offsetInHours = Math.floor(offsetFromUTC / 60);
  if (offsetInHours > 0) {
    //before standard UTD (-)
    return "-" + offsetInHours;
  } else if (offsetInHours < 0) {
    //Negative amount so this is after UTC (+)
    return offsetInHours.toString().replace("-", "+");
  } else {
    //is a zero
    return "0";
  }
}

/*
 * Returns a users estimated device (Desktop or Mobile) based on screen width
 *
 * @return {string} Device type (mobile|desktop)
 */
function bleeper_get_device_in_use() {
  if (jQuery(window).width() < 900) {
    //Width is less than 900
    return "mobile";
  }
  return "desktop";
}

/*
 * Returns the users OS - will only run once as we have a check in place to see if the variable has been set.
 * This is done to prevent Regular Expression from being performed more often than we need it
 */
function bleeper_get_operating_system() {
  if (bleeper_user_current_os === false && typeof navigator !== "undefined" && navigator.userAgent !== "undefined") {
    var current_user_agent = navigator.userAgent;
    var possibleOsList = [{
        s: 'Windows 10',
        r: /(Windows 10.0|Windows NT 10.0)/
      }, {
        s: 'Windows 8.1',
        r: /(Windows 8.1|Windows NT 6.3)/
      }, {
        s: 'Windows 8',
        r: /(Windows 8|Windows NT 6.2)/
      }, {
        s: 'Windows 7',
        r: /(Windows 7|Windows NT 6.1)/
      }, {
        s: 'Windows Vista',
        r: /Windows NT 6.0/
      }, {
        s: 'Windows Server 2003',
        r: /Windows NT 5.2/
      }, {
        s: 'Windows XP',
        r: /(Windows NT 5.1|Windows XP)/
      }, {
        s: 'Windows 2000',
        r: /(Windows NT 5.0|Windows 2000)/
      }, {
        s: 'Windows ME',
        r: /(Win 9x 4.90|Windows ME)/
      }, {
        s: 'Windows 98',
        r: /(Windows 98|Win98)/
      }, {
        s: 'Windows 95',
        r: /(Windows 95|Win95|Windows_95)/
      }, {
        s: 'Windows NT 4.0',
        r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/
      }, {
        s: 'Windows CE',
        r: /Windows CE/
      }, {
        s: 'Windows 3.11',
        r: /Win16/
      }, {
        s: 'Android',
        r: /Android/
      }, {
        s: 'Open BSD',
        r: /OpenBSD/
      }, {
        s: 'Sun OS',
        r: /SunOS/
      }, {
        s: 'Linux',
        r: /(Linux|X11)/
      }, {
        s: 'iOS',
        r: /(iPhone|iPad|iPod)/
      }, {
        s: 'Mac OS X',
        r: /Mac OS X/
      }, {
        s: 'Mac OS',
        r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/
      }, {
        s: 'QNX',
        r: /QNX/
      }, {
        s: 'UNIX',
        r: /UNIX/
      }, {
        s: 'BeOS',
        r: /BeOS/
      }, {
        s: 'OS/2',
        r: /OS\/2/
      }, {
        s: 'Search Bot',
        r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/
      }
    ];

    for (var id in possibleOsList) {
      var current_os = possibleOsList[id];
      if (current_os.r.test(current_user_agent)) {
        bleeper_user_current_os = current_os.s;
        return bleeper_user_current_os; //Return and kill loop as we have a match
      }
    }

    //Made it past our loop - This shouldn't happen. But if it does the OS is unknown
    bleeper_user_current_os = "Unknown"; //Prevent loop from running again
    return bleeper_user_current_os;

  } else {
    return bleeper_user_current_os; //Just return the OS
  }
}

/**
 * Use Regular Exp seen above to parse any inline links automatically
 * Variable defined outside of function scope to reserve resources as seen here: https://stackoverflow.com/questions/1500260/detect-urls-in-text-with-javascript
 *
 * @param {string} content Content to filter
 * @return {string} Fitlered content
 */
function bleeper_inline_link_generator(content) {
  return content.replace(bleeper_link_match_regex, function (url) {
    if (url.indexOf("wdt-emoji") === -1) {
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
function bleeper_url_path_em_stripper(path_url) {
  if (path_url.indexOf("<em>") !== -1) {
    path_url = path_url.replace(/<em>/g, "_");
  }
  if (path_url.indexOf("</em>") !== -1) {
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
function bleeper_attachment_label_filter(content) {
  var fileExt = content.split('.').pop();
  fileExt = fileExt.toLowerCase();
  for (var i in bleeper_file_suffix_check) {
    if (fileExt === bleeper_file_suffix_check[i]) {
      return "Attachment";
    }
  }

  return content;
}

/**
 * Test to make sure sessionStorage exists and is enabled
 *
 * @return {bool} True on success, and false on fail
 */
function wplc_test_sessionStorage() {
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
    } catch (e) {
      return false;
      // sessionStorage is disabled
    }
  } else {
    return false;
    // sessionStorage is not available
  }
}

/**
 * Recusively initialize the input field for emoji support. If an error occurs it will attempt again.
 *
 * Will try to initialize up to 5 times.
 * @param {int} attempt Attempt number
 */
function bleeper_attempt_emoji_input_init(attempt) {
  try {
    wdtEmojiBundle.init('.wdt-emoji-bundle-enabled');
  } catch (err) {
    if (attempt < 5) {
      attempt++;
      setTimeout(function () {
        bleeper_attempt_emoji_input_init(attempt);
      }, 1000);
    }
  }
}

/**
 * Clean up the query string
 *
 * @param {string} current_query Current Query String
 * @return {string} Modified Query String
 */
function wplc_query_cleanup(current_query) {
  if (current_query.charAt(0) === "&") {
    current_query = current_query.substring(1);
  }

  return current_query;
}

/**
 * Powered By Link for WPLC, which is appended to the chat box content
 */
function wplc_powered_by() {
  if (typeof wplc_integration_pro_active === "undefined" || (typeof bleeper_force_powered_by !== 'undefined' && bleeper_force_powered_by === true)) {
    var html = '<span class="wplc_powered_by"><i title="Powered by" class="fa fa-bolt"></i> <a title="Powered by WP Live Chat Support" href="https://wp-livechat.com/?utm_source=powered&utm_medium=poweredby&utm_campaign=' + window.location.hostname + '" target="_BLANK" rel="nofollow" class="wplc-color-1">WP Live Chat Support</a><span></span></span>';

    if (jQuery("#wp-live-chat-4").length) {
      jQuery("#wp-live-chat-4").append(html);
    }

    if (jQuery("#wp-live-chat-2").length) {
      jQuery("#wp-live-chat-2").append(html);
    }

    jQuery(".wplc_powered_by").css('position', 'absolute');
    jQuery(".wplc_powered_by").css('padding-left', '10px');
    jQuery(".wplc_powered_by").css('bottom', '-82px');
    jQuery(".wplc_powered_by").css('font-size', '10px');
    jQuery(".wplc_powered_by").css('font-family', 'Roboto, sans-serif');
    jQuery(".wplc_powered_by a").css('color', '#adadad');

    jQuery(".wplc_powered_by a").css('font-weight', '700');
    jQuery(".wplc_powered_by a").css('color', '#989898');

    /* If this is classic theme */
    jQuery(".classic .wplc_powered_by").css('bottom', '0px');
    jQuery(".classic #wplc_user_message_div").css('margin-bottom', '10px');

    jQuery("#wp-live-chat-2 .wplc_powered_by").css('bottom', '0px');

    jQuery(".wplc_powered_by").css('left', '0px');
  }

}

/**
 * Get a specific cookie by name

 * @param {string} name Name of the cookie
 * @return {string} Value of store cookie
 */
function wplc_getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2)
    return parts.pop().split(";").shift();
}
