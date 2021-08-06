<?php
/*
 * Node Code -> Insert pun here
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define("BLEEPER_REMOTE_DASH_ROUTE", "remote_dashboard.php");
define("BLEEPER_NODE_SERVER_URL", "https://livechat-001.us-3.evennode.com");

define("BLEEPER_NODE_END_POINTS_ROUTE", "api/v1/");
define("BLEEPER_NODE_END_POINT_TOKEN", "zf6fe1399sdfgsdfg02ad09ab6a8cb7345s");


add_action("wplc_activate_hook", "wplc_node_server_token_check", 10);
add_action("wplc_update_hook", "wplc_node_server_token_check", 10);

/**
 * Checks if a secret key has been created.
 * If not create one for use in the API
 *
 * @return void
*/
function wplc_node_server_token_check(){
	if (!get_option("wplc_node_server_secret_token")) {
		$user_token = wplc_node_server_token_create();
        add_option("wplc_node_server_secret_token", $user_token);
    }
}

add_action("wplc_admin_dashboard_render", "wplc_admin_dashboard");
/*
 * Adds a simple thank you message to the footer of the dashboard
*/
function wplc_admin_dashboard() {

  $wplc_node_token = get_option("wplc_node_server_secret_token");
  if(!$wplc_node_token){
      if(function_exists("wplc_node_server_token_regenerate")){
          wplc_node_server_token_regenerate();
          $wplc_node_token = get_option("wplc_node_server_secret_token");
      }
  }

  $variables = array("node_token" => $wplc_node_token, "action" => "wordpress");
  $variables = apply_filters("wplc_admin_dashboard_layout_node_request_variable_filter", $variables); //Filter for pro to add a few request variables to the mix for additional structure

?>
<div class='nifty_top_wrapper'>
  <div class="wrap">
    <div class='floating-right-toolbar'>
      <label for="user_list_mobile_control" style="margin-bottom: 0;"><i id="toolbar-item-user_list" class="fa fa-bars fa-fw" title="Toggle user list."></i></label>
      <?php if (isset($variables['pro']) && isset($variables['include_filters'])){ ?>
      <!--<i id="toolbar-item-filter-wp" class="fa fa-filter fa-fw"></i>-->
      <?php } ?>
      <i id="toolbar-item-fullscreen-wp" class="fa fa-clone fa-fw" title="Toggle WordPress Menu for a full screen experience."></i>
    </div>
    <div id="page-wrapper" style='position:relative;'>
      <div class='nifty_bg_holder'>
        <div class='nifty_bg_holder_text'><img src='<?php echo WPLC_PLUGIN_URL;?>/images/wplc_loading.png' width='50' /><br /><br /><div id='nifty_bg_holder_text_inner'>Connecting...</div></div>
        <div class='bleeper_tips_hints'></div>
      </div>
                    
        <div class="nifty_admin_overlay" style="display:none">

        </div>
        
       <div class="nifty_admin_chat_prompt" style="display:none">
            <div class="nifty_admin_chat_prompt_title" id='nifty_admin_chat_prompt_title'>Please Confirm</div>
            <div class="nifty_admin_chat_prompt_message"></div>
            <div class="nifty_admin_chat_prompt_actions">
                <button class="btn btn-info" id="nifty_admin_chat_prompt_confirm">Confirm</button>
                <button class="btn btn-secondary" id="nifty_admin_chat_prompt_cancel">Cancel</button>
             </div>
        </div>

        <div id='nifty_wrapper'>
          <input type="checkbox" id="user_list_mobile_control" name="user_list_mobile_control" checked="checked">
          <div id='user_list' class='col-md-3'>
              <?php if (!empty($variables['choose']) && !empty($variables['aid'])) { ?>
              <div id='choose_online'>
                  <div id="wplc_agent_status_text" style="display: inline-block; padding-left: 10px;"></div>
                  <input type="checkbox" class="wplc_switchery" name="wplc_agent_status" id="wplc_agent_status" <?php if (isset($variables['choose'][$variables['aid']]) && $variables['choose'][$variables['aid']] === "true")  { echo 'checked'; } ?> />
              </div>
              <?php } ?>
              <div id='user_count'>
                  <span id='active_count'>...</span>
                  <span id='active_count_string'> Active visitors</span>
              </div>
              <?php if (!isset($variables['pro'])){ ?>
                <span class="agent_count">1 <a class="wplc-agent-info" id='wplc-agent-info' href="javascript:void(0);">Agent(s) Online</a></span>
              <?php } else { ?> 
                <span class="agent_count"><?php echo (isset($variables['agent_count']) ? intval($variables['agent_count']) : "1"); ?> Agent(s) Online</span>
              <?php } ?>
                <span class='history_link'><a href='./admin.php?page=wplivechat-menu-history' target='_BLANK' id='wplc_history_link'>Chat History</a></span>
              <div class='userListBox_Wrapper'>
                <div class='userListBox'></div>
              </div>
              <div id='agent_list'>
                  <h4 id='nifty_agent_heading'>Agents</h4>
                  <ul class='online_agent_list'>
                  </ul>
              </div>



          </div>

          <div id='chat_area' class='col-md-9'>
            <?php if (isset($variables['pro']) && isset($variables['include_media_sharing'])){ ?>
            <div id="chat_drag_zone" style="display:none;"><div id="chat_drag_zone_inner"><span id='drag_zone_inner_text'>Drag Files Here</span></div></div>
            <?php } ?>
            <div class="chatArea" style='display:none;'>
              <div class="chatInfoArea">
                <div class="dropdown pull-right"><span class="minChat btn" id="bleeper_min_chat" title="Minimize Chat">X</span></div>
                <div class="pull-right"><span class="eventbox btn"><i class='fa fa-ellipsis-h'></i> Show Events</span></div>


                <div class="btn-group inchat-menu pull-right">
                  <button class="btn dropdown-toggle" type="button" id="inchat_drop_down" data-toggle="dropdown">
                    <em class="fa fa-navicon"></em>
                    <span class="caret"></span>
                  </button>
                  <div class="dropdown-menu" aria-labelledby='inchat_drop_down'>
                    <?php if (isset($variables['pro']) && isset($variables['include_transfers'])){ ?>
                    <a href="javascript:void(0);" class='dropdown-item chatTransfer' id='chatTransferLink'>Transfer</a>
                    <a href="javascript:void(0);" class='dropdown-item chatTransferDepartment' id='chatTransferDepLink'>Department Transfer</a>
                    <a href="javascript:void(0);" class='dropdown-item chatDirectUserToPagePrompt' id='chatDirectUserToPageLink'>Direct User To Page</a>
                    <?php } ?>
                    <a href="javascript:void(0);" class='dropdown-item chatTranscript' id='chatTranscriptTitle' style='display:none;'>Transcript</a>
                    <a href="javascript:void(0);" class='dropdown-item chatClose' id='chatCloseTitle'>Leave chat</a>
                    <a href="javascript:void(0);" class='dropdown-item endChat' id='chatEndTitle'>End chat</a>
                    <?php if (!isset($variables['pro'])){ ?>
                    <a href="javascript:void(0);" class='dropdown-item chatTransferUps' id="chatTransferUps">Transfer</a>
                    <a href="javascript:void(0);" class='dropdown-item chatDirectUserToPageUps' id="chatDirectUserToPageUps">Direct User To Page</a>
                    <?php } ?>
                  </div>
                </div>


                <div class='user_header_wrapper_img'>
                  <div class='user_gravatar'></div>
                </div>
                <div class='user_header_wrapper_info'>
                  <h3><span class='chatInfoArea-Name'>Name</span></h3>
                  <h4><span class='chatInfoArea-Email'>Email</span></h4>
                  <p><span class='chatInfoArea-Info1'>Something</span></p>
                </div>
                
              </div>
              <div class='eventbox-wrapper'>
                <h3 id='nifty_event_heading'>Events</h3>
                <a class='eventbox-close' href='#' title='Close the event box'><i class='fa fa-times'></i></a>
                <hr />
                <ul class='events-ul'>
                  <li>
                    <div class='event-icon'><i class='fa fa-info'></i></div>
                    <div class='event-desc'>User is now browsing http://test.com and clicked on something or the other so that this description is long.</div>
                    <div class='event-meta'>2017-08-15 10:16am</div>
                  </li>
                  <li>
                    <div class='event-icon'><i class='fa fa-keyboard-o'></i></div>
                    <div class='event-desc'>User was typing.</div>
                    <div class='event-meta'>2017-08-15 10:15am</div>
                  </li>
                </ul> 
              </div>
              <ul class="messages" id="messages"></ul>
              
              <?php if (isset($variables['pro']) && isset($variables['include_quick_responses'])){ ?>
              <div id="quick_response_drawer_handle"><i class="fa fa-bolt" title="Quick Responses"></i></div>
              <!--<div id="quick_response_drawer_container" style="display:none;">
                <h5>Quick Response</h5> 
                <hr> -->
                <!--<div class="quick_response_item">Some Sample Text</div>-->
                <?php //echo $quick_responses; ?>
              <!--</div>-->
              <?php } ?>
              <div class='typing_preview_wplc' style='display:none;'></div>
              <div class='bleeper_join_chat_div'><button class='bleeper_join_chat_btn btn btn-success' id='nifty_join_chat_button'>Join chat</button></div>
              <input class="inputMessage wdt-emoji-bundle-enabled" id="inputMessage" placeholder="Type here..."/>
              <span class="editing_hints"><strong>*bold*</strong> <em>_italics_</em> <code>`code`</code> <code>```preformatted```</code></span>
              <img id="wplc_send_msg" class='nifty_send_arrow' style="display:none;" src='<?php echo WPLC_PLUGIN_URL;?>/images/arrow.png' />

                <?php if (isset($variables['pro']) && isset($variables['include_media_sharing'])){ ?>
                  <label for='nifty_add_media' class="nifty_add_media_button"><i class="fa fa-plus"></i></label>
                  <input type="checkbox" id="nifty_add_media" />
                  <ul class='nifty_media_prompt'>
                    <li>
                      <label for="nifty_file_input">
                        <i class="nifty_tedit_icon fa fa-paperclip" id="nifty_attach" ></i>
                        <i class="nifty_attach_icon fa fa-circle-o-notch fa-spin" id="nifty_attach_uploading_icon" style="display:none;"></i>
                        <i class="nifty_attach_icon fa fa-check-circle" id="nifty_attach_success_icon" style="display:none;"></i>
                        <i class="nifty_attach_icon fa fa-minus-circle" id="nifty_attach_fail_icon" style="display:none;"></i>
                      </label>
                    </li>
                  </ul>
                  <input type="file" id="nifty_file_input" name="nifty_file_input" style="display:none">
                <?php } else { ?>
                  <label for='nifty_add_media' class="nifty_add_media_button"><i class="fa fa-plus"></i></label>
                  <input type="checkbox" id="nifty_add_media" />
                <?php } ?>

            </div>
            <div class="infoArea">
              <div class="dropdown filter-menu pull-right">
                  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" style='margin-right:25px;' title="Filter the user list based on activity." id='nifty_filter_button'>
                    Filters 
                    <span class="caret"></span>
                  </button>
                  <div class="dropdown-menu">
                    <a href="javascript:void(0);" class='dropdown-item filter-new-visitors' id='nifty_new_visitor_item'>New Visitors (3 Min)</a>
                    <a href="javascript:void(0);" class='dropdown-item filter-active-chats' id='nifty_active_chats_item'>Active Chats</a>
					          <a href="javascript:void(0);" class="dropdown-item filter-referer" id="nifty_referer_item">Page URL</a>
                    <a href="javascript:void(0);" class='dropdown-item filter-clear' id='nifty_clear_filters_item'>Clear Filters</a>
                  </div>
                  <div class='filter-active-tag-container' style='display:none;'>
                    <i class='fa fa-times-circle filter-clear' style="cursor:pointer;"></i>
                    <span class='filter-active-tag-inner'></span>
                  </div>
              </div>

			  <div id="nifty_referer_options" style="display:none;float:right;margin-right:10px;">
				  <input placeholder="Page URL" type="text" id="nifty_referer_url" style="width:100%;float:right;">
				  <label style="font-weight:normal;">
					<input type="checkbox" id="nifty_referer_contains" style="margin:0;"> Contains
				  </label>
			  </div>

              <h2 id='nifty_active_chats_heading'>Active visitors</h2>
              
              <div class='visitorListBoxHeader'>
                <div class='vcol visCol' id='nifty_vis_col_heading'>Visitor</div>
                <div class='vcol visStatusCol' id='nifty_vis_info_heading'>Info</div>
                <div class='vcol visPageCol' id='nifty_vis_page_heading'>Page</div>
                <div class='vcol visChatStatusCol' id='nifty_vis_status_heading'>Chat Status</div>
                <?php if (isset($variables['pro']) && isset($variables['include_departments'])){ ?>
                <div class='vcol visChatDepCol' id='nifty_vis_dep_heading'>Department</div>
                <?php } ?>
                <div class='vcol visActionCol'></div>
              </div>
              <div class='visitorListBox'>
                  
              </div>
              
            </div>
          </div>
    </div>
    <div class="wdt-emoji-popup">
      <a href="#" class="wdt-emoji-popup-mobile-closer"> &times; </a>
      <div class="wdt-emoji-menu-content">
        <div id="wdt-emoji-menu-header">
          <a class="wdt-emoji-tab" data-group-name="People"></a>
          <a class="wdt-emoji-tab" data-group-name="Nature"></a>
          <a class="wdt-emoji-tab" data-group-name="Foods"></a>
          <a class="wdt-emoji-tab" data-group-name="Activity"></a>
          <a class="wdt-emoji-tab" data-group-name="Places"></a>
          <a class="wdt-emoji-tab" data-group-name="Objects"></a>
          <a class="wdt-emoji-tab" data-group-name="Symbols"></a>
          <a class="wdt-emoji-tab" data-group-name="Flags"></a>
        </div>
        <div class="wdt-emoji-scroll-wrapper">
          <div id="wdt-emoji-menu-items">
            <input id="wdt-emoji-search" type="text" placeholder="Search">
            <h3 id="wdt-emoji-search-result-title">Search Results</h3>
            <div class="wdt-emoji-sections"></div>
            <div id="wdt-emoji-no-result">No emoji found</div>
          </div>
        </div>
        <div id="wdt-emoji-footer">
          <div id="wdt-emoji-preview">
            <span id="wdt-emoji-preview-img"></span>
            <div id="wdt-emoji-preview-text">
              <span id="wdt-emoji-preview-name"></span><br>
              <span id="wdt-emoji-preview-aliases"></span>
            </div>
          </div>
          <div id="wdt-emoji-preview-bundle">
            <span></span>
          </div>
          <span class="wdt-credit">WDT Emoji Bundle</span>
        </div>
      </div>
    </div>

    <script>
      jQuery(document).ready(function(){
        jQuery("#wplc_footer_loading_icon").hide();
        jQuery("#wplc_footer_message").fadeIn();
      });
    </script>
  </div>
</div>
<?php
}

/**
 * Generates a new Secret Token
 *
 * @return string
*/
function wplc_node_server_token_create(){
	$the_code = rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000);
	$the_time = time();
	$token = md5($the_code . $the_time);
	return $token;
}

/**
 * Re-Generates the token
 * @return void
*/
function wplc_node_server_token_regenerate(){
	$wplc_node_token_new = wplc_node_server_token_create();
    update_option("wplc_node_server_secret_token", $wplc_node_token_new);
}

/**
 * Post to the NODE server -
 *
 * @param string $route Route you would like to use for the node server
 * @param string $form_data data to send
 * @return string (or false on fail)
*/
function wplc_node_server_post($route, $form_data){

    $url = trailingslashit(BLEEPER_NODE_SERVER_URL) . trailingslashit(BLEEPER_NODE_END_POINTS_ROUTE) . $route;

    if(!isset($form_data['token'])){
        $form_data['token'] = BLEEPER_NODE_END_POINT_TOKEN; //Add the security token
    }

	if(!isset($form_data['api_key'])){
        $wplc_node_token = get_option("wplc_node_server_secret_token");
        if(!$wplc_node_token){
            if(function_exists("wplc_node_server_token_regenerate")){
                wplc_node_server_token_regenerate();
                $wplc_node_token = get_option("wplc_node_server_secret_token");
            }
        }

        $form_data['api_key'] = $wplc_node_token; //Add the security token
    }

    if(!isset($form_data['origin_url'])){
        $ajax_url = admin_url('admin-ajax.php');
        $origin_url = str_replace("/wp-admin/admin-ajax.php", "", $ajax_url);
        $form_data['origin_url'] = $origin_url; //Add the security token
    }

    $options = array();
    $context  = @stream_context_create($options);
    $result = @file_get_contents($url . "?" . http_build_query($form_data), false, $context);

    if ($result === FALSE) {
        return false;
    } else {
        return $result;
    }
}

add_action("wplc_hook_chat_notification","wplc_filter_notification_hook_node",20,3);
/**
 * Send a system notification to the node server
 *
 * @return void
*/
function wplc_filter_notification_hook_node($type,$cid,$data){

    $wplc_settings = get_option("WPLC_SETTINGS");
    if(isset($wplc_settings['wplc_use_node_server']) && intval($wplc_settings['wplc_use_node_server']) == 1) {

    	if(filter_var($cid, FILTER_VALIDATE_INT) ) {
	        //Let's reverse the CID back to it's rel counterpart
		    $cid = wplc_return_chat_rel_by_id($cid);
		}

		$msg = false;
		switch($type){
			case "doc_suggestion":
				$msg = $data['formatted_msg'];
				break;
			default:
				break;
		}

		if(isset($cid)){
			if($msg !== false){
	            $form_data = array(
	                    'chat_id' => $cid,
	                    'notification_text' => $msg,
              );
              
              error_log("Using deprecated wplc_node_server_post() for user_chat_notification");
	          $user_request = wplc_node_server_post("user_chat_notification", $form_data);

			}
		}
	}

	return;
}

add_action("wplc_api_route_hook", "wplc_api_node_routes");
/**
 * Add a REST API routes for the node server
 *
 * @return void
*/
function wplc_api_node_routes(){
	register_rest_route('wp_live_chat_support/v1','/async_storage', array(
						'methods' => 'POST',
						'callback' => 'wplc_node_async_storage_rest'
	));
}

/**
 * Handles Async storage REST -> Params are processed within the request
 * Required POST variables:
 * - Chat ID
 * - Security Key
 * - Message (JSON encoded array)
 * - Action
 *
 * @param WP_REST_Request $request Request Data
 * @return void
*/
function wplc_node_async_storage_rest(WP_REST_Request $request){
	$return_array = array();
	$return_array['request_status'] = false; //Default to be returned if something goes wrong
	if(isset($request)){
		if(isset($request['security'])){
			$stored_token = get_option("wplc_node_server_secret_token");
			$check = $_POST['server_token'] == $stored_token ? true : false;
			if ($check) {
				if(isset($request['chat_id'])){
					if(isset($request['messages'])){
						if(isset($request['relay_action'])){
							$chat_id = sanitize_text_field($request['chat_id']);
							$message_data = json_decode($request['messages']);
							$chat_session = wplc_return_chat_session_variable($chat_id);
							$action = $request['relay_action'];
							if($message_data !== NULL){
								if($action == "wplc_user_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("1", $chat_id, $message);
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								} else if ($action == "wplc_admin_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("2", $chat_id, $message, true);
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								}
							} else {
								$return_array['request_information'] = __("Message data is corrupt", "wplivechat");
							}
						} else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['request_information'] = __("Message data array not set", "wplivechat");
					}
				} else {
					$return_array['request_information'] = __("Chat ID is not set", "wplivechat");
				}
			}
		} else {
			$return_array['request_information'] = __("No security nonce found", "wplivechat");
		}
	}

	return $return_array;

}

add_action("wp_ajax_wplc_node_async_storage_ajax", "wplc_node_async_storage_ajax");
add_action("wp_ajax_nopriv_wplc_node_async_storage_ajax", "wplc_node_async_storage_ajax");
/**
 * Handles Async storage AJAX (Fallback for if REST is not present) -> Params are processed within the request
 * Required POST variables:
 * - Chat ID
 * - Security Key
 * - Message (JSON encoded array)
 * - Action
 *
 * @return void
*/
function wplc_node_async_storage_ajax(){
	$return_array = array();
	$return_array['request_status'] = false; //Default to be returned if something goes wrong
	if(isset($_POST)){
		if(isset($_POST['server_token'])){
			$stored_token = get_option("wplc_node_server_secret_token");
			$check = $_POST['server_token'] == $stored_token ? true : false;
			if ($check) {
				if(isset($_POST['chat_id'])){
					if(isset($_POST['messages'])){
						if(isset($_POST['relay_action'])){
							$chat_id = sanitize_text_field($_POST['chat_id']);
							$message_data = json_decode($_POST['messages']);
							$chat_session = wplc_return_chat_session_variable($chat_id);
							$action = sanitize_text_field($_POST['relay_action']);
							if($message_data !== NULL){
								if($action == "wplc_user_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("1", $chat_id, $message);
										wplc_update_active_timestamp($chat_id);

									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								} else if ($action == "wplc_admin_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("2", $chat_id, $message);
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								}
							} else {
								$return_array['request_information'] = __("Message data is corrupt", "wplivechat");
							}
						} else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['request_information'] = __("Message data array not set", "wplivechat");
					}
				} else {
					$return_array['request_information'] = __("Chat ID is not set", "wplivechat");
				}
			}
		} else {
			$return_array['request_information'] = __("No security nonce found", "wplivechat");
		}
	}

	return $return_array;

}

/**
 * Loads remote dashboard
 *
 * @return void
*/
function wplc_admin_dashboard_layout_node( $location = 'dashboard' ){
	if ( $location === 'dashboard') {

		if( ! get_user_meta( get_current_user_id(), 'wplc_ma_agent', true ) ){
			echo "<div class='error below-h1'>";
		    echo "<h2>".__("Error", "wplivechat")."</h2>";
		    echo "<p>".__("Only chat agents can access this page.", "wplivechat")."</p>";
		    echo "</div>";
			return;
		}

		do_action("wplc_admin_remote_dashboard_above");

		echo "<div id='bleeper_content_wrapper'></div>";

		if ( ! isset( $_GET['action'] ) || 'history' !== $_GET['action'] ) {

            echo "<div class='wplc_remote_dash_below_contianer'>";
            do_action("wplc_admin_dashboard_render");
            do_action("wplc_admin_remote_dashboard_below");
            echo "</div>";

        }
   } else {
		do_action("wplc_admin_remote_dashboard_above");

		echo "<div id='bleeper_content_wrapper'></div>";

		  if ( ! empty( $_GET['page'] ) && 'wplivechat-menu' === $_GET['page'] ) { // This div is also hidden by js under the same conditions

            echo "<div class='wplc_remote_dash_below_contianer'>";
            do_action("wplc_admin_remote_dashboard_below");
            do_action("wplc_admin_dashboard_render");
            echo "</div>";

      } else {
        $wplc_settings = get_option("WPLC_SETTINGS");
        if (isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1) {
          if ( isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu') {

          } else {
            if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {
              echo "<div class='wplc_remote_dash_below_contianer'>";
              do_action("wplc_admin_dashboard_render");
              echo "</div>";
            }
          }
        }
      }
   }
}

add_action('admin_enqueue_scripts', 'wplc_enqueue_dashboard_popup_scripts');
/**
 * Enqueues the scripts for the admin dashboard popup icon and chat box
 * @return void
 */
function wplc_enqueue_dashboard_popup_scripts() {
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('wplc-admin-popup', WPLC_PLUGIN_URL.'/js/wplc_admin_popup.js', array('jquery-ui-draggable'), WPLC_PLUGIN_VERSION);
	
	wp_button_pointers_load_scripts('toplevel_page_wplivechat-menu');
}

add_action( "admin_footer", "wplc_dashboard_display_decide" );
/**
 * Decide whether or not to display the dashboard layout on an admin page
 * @return  void
 */
function wplc_dashboard_display_decide() {
	$wplc_settings = get_option("WPLC_SETTINGS");
    if ( isset( $wplc_settings['wplc_use_node_server'] ) && $wplc_settings['wplc_use_node_server'] == 1 ) {
    	//Node in use, load remote dashboard
    	if ( isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu') {

    	} else {

			/**
			 * Check to see if we have enabled "Enable chat dashboard and notifications on all admin pages"
			 */
			
			if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {


	    		wplc_admin_dashboard_layout_node('other');

	    		echo '<div class="floating-right-toolbar">';
	      		echo '<label for="user_list_bleeper_control" style="margin-bottom: 0; display:none;"></label>';

	            echo '<i id="toolbar-item-open-bleeper" class="fa fa-fw" style="background:url(\''.plugins_url('../images/48px.png', __FILE__).'\') no-repeat; background-size: cover;"></i>';
	    		echo '</div>';
	    	}

    	}
    }



}


/**
 * Loads remote dashboard scripts and styles
 *
 * @return void
*/
function wplc_admin_remote_dashboard_scripts($wplc_settings){
	$wplc_current_user = get_current_user_id();
	if( get_user_meta( $wplc_current_user, 'wplc_ma_agent', true )) {

		$user_info = get_userdata(intval($wplc_current_user));

		$user_array = get_users(array(
	        'meta_key' => 'wplc_ma_agent',
	    ));

		$a_array = array();
		if ($user_array) {
            foreach ($user_array as $user) {
            	$current_user_name = apply_filters("wplc_agent_display_name_filter", $user->display_name);
                $a_array[$user->ID] = array();
                $a_array[$user->ID]['name'] = $user->display_name;
                $a_array[$user->ID]['display_name'] = $user->display_name;
                $a_array[$user->ID]['md5'] = md5( $user->user_email );
                $a_array[$user->ID]['email'] = md5( $user->user_email );
            }
        }


		if( isset($wplc_settings['wplc_show_name']) && $wplc_settings['wplc_show_name'] == '1' ){ $wplc_show_name = true; } else { $wplc_show_name = false; }
   		if( isset($wplc_settings['wplc_show_avatar']) && $wplc_settings['wplc_show_avatar'] ){ $wplc_show_avatar = true; } else { $wplc_show_avatar = false; }
		if( isset($wplc_settings['wplc_show_date']) && $wplc_settings['wplc_show_date'] == '1' ){ $wplc_show_date = true; } else { $wplc_show_date = false; }
		if( isset($wplc_settings['wplc_show_time']) && $wplc_settings['wplc_show_time'] == '1' ){ $wplc_show_time = true; } else { $wplc_show_time = false; }

	 	$wplc_chat_detail = array( 'name' => $wplc_show_name, 'avatar' => $wplc_show_avatar, 'date' => $wplc_show_date, 'time' => $wplc_show_time );

		wp_register_script('wplc-admin-js-sockets', WPLC_PLUGIN_URL."/js/vendor/sockets.io/socket.io.slim.js", false, WPLC_PLUGIN_VERSION, false);
		wp_enqueue_script('wplc-admin-js-sockets');

		wp_register_script('wplc-admin-js-bootstrap', WPLC_PLUGIN_URL."/js/vendor/bootstrap/dist/js/bootstrap.js", array("wplc-admin-js-sockets"), WPLC_PLUGIN_VERSION, false);
    wp_enqueue_script('wplc-admin-js-bootstrap');
    
    wplc_register_common_node();
    
		// NB: This causes Failed to initVars ReferenceError: wplc_show_date is not defined when uncommented and enabled
		if(empty($wplc_settings['wplc_disable_emojis']))
		{
			wp_register_script('wplc-admin-js-emoji', WPLC_PLUGIN_URL."/js/vendor/wdt-emoji/emoji.min.js", array("wplc-admin-js-sockets"), WPLC_PLUGIN_VERSION, false);
			wp_enqueue_script('wplc-admin-js-emoji');

			wp_register_script('wplc-admin-js-emoji-bundle', WPLC_PLUGIN_URL."/js/vendor/wdt-emoji/wdt-emoji-bundle.min.js", array("wplc-admin-js-emoji"), WPLC_PLUGIN_VERSION, false);
			wp_enqueue_script('wplc-admin-js-emoji-bundle');
		}

		wp_register_script('md5', WPLC_PLUGIN_URL.'/js/md5.js', array("wplc-admin-js-sockets"), false, false);
    	wp_enqueue_script('md5');

		$dependencies = array();
		if(empty($wplc_settings['wplc_disable_emojis']))
			$dependencies[] = "wplc-admin-js-emoji-bundle";
      	wp_register_script('wplc-admin-js-agent', WPLC_PLUGIN_URL.'/js/wplc_agent_node.js', $dependencies, WPLC_PLUGIN_VERSION, false);

		wp_localize_script('wplc-admin-js-agent', 'bleeper_remote_enabled', "true");

		if (isset($wplc_settings['wplc_enable_msg_sound'])) { 

			if (intval($wplc_settings['wplc_enable_msg_sound']) == 1) {
				wp_localize_script('wplc-admin-js-agent', "bleeper_ping_sound_notification_enabled", "true");
			} else {
				wp_localize_script('wplc-admin-js-agent', "bleeper_ping_sound_notification_enabled", "false");
			}

		}

		wp_register_script('my-wplc-admin-chatbox-ui-events', WPLC_PLUGIN_URL.'/js/wplc_u_admin_chatbox_ui_events.js', array('jquery'), WPLC_PLUGIN_VERSION, true);
		wp_enqueue_script('my-wplc-admin-chatbox-ui-events'); 
		


		$wplc_et_ajax_nonce = wp_create_nonce( "wplc_et_nonce" );
		wp_register_script( 'wplc_transcript_admin', WPLC_PLUGIN_URL.'/js/wplc_transcript.js', null, '', true );
		$wplc_transcript_localizations = array(
			'ajax_nonce'          => $wplc_et_ajax_nonce,
			'string_loading'      => __( "Sending transcript...", "wplivechat" ),
			'string_title'        => __( "Sending Transcript", "wplivechat" ),
			'string_close'        => __( "Close", "wplivechat" ),
			'string_chat_emailed' => __( "The chat transcript has been emailed.", "wplivechat" ),
			'string_error1'       => __( "There was a problem emailing the chat.", "wplivechat" )
		);
		wp_localize_script( 'wplc_transcript_admin', 'wplc_transcript_nonce', $wplc_transcript_localizations );
		wp_enqueue_script( 'wplc_transcript_admin' );

		$wplc_node_token = get_option("wplc_node_server_secret_token");
	    if(!$wplc_node_token){
	        if(function_exists("wplc_node_server_token_regenerate")){
	            wplc_node_server_token_regenerate();
	            $wplc_node_token = get_option("wplc_node_server_secret_token");
	        }
	    }

	    $form_data = array("node_token" => $wplc_node_token, "action" => "wordpress");
	    $form_data = apply_filters("wplc_admin_dashboard_layout_node_request_variable_filter", $form_data); //Filter for pro to add a few request variables to the mix for additional structure


	    wp_localize_script('wplc-admin-js-agent', 'bleeper_remote_form_data_array', $form_data);
      wp_localize_script('wplc-admin-js-agent', 'bleeper_remote_form_route', 'dashboard.php');


	    if ( isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu' ) {
			  wp_localize_script('wplc-admin-js-agent', 'bleeper_in_dashboard', '1');
	    } else {
	    	wp_localize_script('wplc-admin-js-agent', 'bleeper_in_dashboard', '0');
	    }

	    $inline_error_message = "<div class='error below-h1' style='display:none;' id='bleeper_inline_connection_error'>
	                				<p>" . __("Connection Error", "wplivechat") . "<br /></p>
	                				<p>" . __("We are having some trouble contacting the server. Please try again later.", "wplivechat") . "</p>
	            				</div>";
	    wp_localize_script('wplc-admin-js-agent', 'bleeper_remote_form_error', $inline_error_message);





	    if (isset($wplc_settings['wplc_enable_visitor_sound']) && intval($wplc_settings['wplc_enable_visitor_sound']) == 1) {
	        wp_localize_script('wplc-admin-js-agent', 'bleeper_enable_visitor_sound', '1');
	    } else {
	        wp_localize_script('wplc-admin-js-agent', 'bleeper_enable_visitor_sound', '0');
	    }




		$agent_display_name = $user_info->display_name;

		wp_localize_script('wplc-admin-js-agent', 'agent_id', "" . $wplc_current_user);
		wp_localize_script('wplc-admin-js-agent', 'bleeper_agent_name', apply_filters("wplc_agent_display_name_filter", $agent_display_name) );
		wp_localize_script('wplc-admin-js-agent', 'nifty_api_key', get_option("wplc_node_server_secret_token"));

    //For node verification
    wplc_check_guid();

    wp_localize_script('wplc-admin-js-agent', 'wplc_guid', get_option('WPLC_GUID', ''));
    wp_localize_script('wplc-admin-js-agent', 'bleeper_agent_verification_end_point', rest_url('wp_live_chat_support/v1/validate_agent'));
		wp_localize_script('wplc-admin-js-agent', 'bleeper_disable_mongo', "true");
		wp_localize_script('wplc-admin-js-agent', 'bleeper_disable_add_message', "true");
		wp_localize_script('wplc-admin-js-agent', 'wplc_nonce', wp_create_nonce("wplc"));
		wp_localize_script('wplc-admin-js-agent', 'wplc_cid', "null");
		wp_localize_script('wplc-admin-js-agent', 'wplc_chat_name', "null");

		wp_localize_script( 'wplc-admin-js-agent', 'wplc_show_chat_detail', $wplc_chat_detail );

		wp_localize_script('wplc-admin-js-agent', 'wplc_agent_data', $a_array);
		wp_localize_script('wplc-admin-js-agent', 'all_agents', $a_array);

		wp_localize_script('wplc-admin-js-agent', 'wplc_url', plugins_url( '', dirname( __FILE__ ) ) );

		if( isset($wplc_settings['wplc_settings_enabled']) && intval($wplc_settings["wplc_settings_enabled"]) == 2) {

            $wplc_disabled_html = __("Chat is disabled in settings area, re-enable", "wplivechat");
            $wplc_disabled_html .= " <a href='?page=wplivechat-menu-settings'>" . __("here", "wplivechat") . "</a>";

            wp_localize_script('wplc-admin-js-agent', 'wplc_disabled', 'true');
            wp_localize_script('wplc-admin-js-agent', 'wplc_disabled_html',  $wplc_disabled_html);
        }


		//Added rest nonces
        if(class_exists("WP_REST_Request")) {
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_enabled', '1');
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_token', get_option('wplc_api_secret_token'));
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_endpoint', rest_url('wp_live_chat_support/v1'));
            wp_localize_script('wplc-admin-js-agent', 'bleeper_override_upload_url', rest_url('wp_live_chat_support/v1/remote_upload'));
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_nonce', wp_create_nonce( 'wp_rest' ));

        } else {
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_enabled', '0');
            wp_localize_script('wplc-admin-js-agent', 'wplc_restapi_nonce', "false");
        }

	    $agent_tagline = apply_filters( "wplc_filter_simple_agent_data_agent_tagline", '', get_current_user_id() );
        $agent_bio = apply_filters( "wplc_filter_simple_agent_data_agent_bio", '', '', get_current_user_id() );
        $social_links = apply_filters( "wplc_filter_simple_agent_data_social_links", '', get_current_user_id() );
        $head_data = array(
            'tagline' => $agent_tagline,
            'bio' => $agent_bio,
            'social' => $social_links
        );
        wp_localize_script( 'wplc-admin-js-agent', 'wplc_head_data', $head_data );
        wp_localize_script( 'wplc-admin-js-agent', 'wplc_user_chat_notification_prefix', __("User received notification:", "wplivechat") );

        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_valid_direct_to_page_array', wplc_node_pages_posts_array() );

        if( isset($wplc_settings['wplc_new_chat_ringer_count']) && $wplc_settings['wplc_new_chat_ringer_count'] !== "") {
            $wplc_ringer_count = intval($wplc_settings['wplc_new_chat_ringer_count']);
            wp_localize_script( 'wplc-admin-js-agent', 'bleeper_ringer_count', "" . $wplc_ringer_count );
        }

        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_new_chat_notification_title', __('New chat received', 'wplivechat') );
        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_new_chat_notification_text', __("A new chat has been received. Please go the 'Live Chat' page to accept the chat", "wplivechat") );

		$wplc_notification_icon = plugin_dir_url(dirname(__FILE__)) . 'images/wplc_notification_icon.png';
        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_new_chat_notification_icon', $wplc_notification_icon );

		do_action("wplc_admin_remoter_dashboard_scripts_localizer");  //For pro localization of agents list, and departments

		wp_enqueue_script('wplc-admin-js-agent');

		wp_register_script('wplc-admin-chat-server', WPLC_PLUGIN_URL.'/js/wplc_server.js', array("wplc-admin-js-agent", "wplc-admin-js-sockets"), WPLC_PLUGIN_VERSION, false); //Added this for async storage calls
    	wp_enqueue_script('wplc-admin-chat-server');

		wp_localize_script( 'wplc-admin-chat-server', 'wplc_datetime_format', array(
			'date_format' => get_option( 'date_format' ),
			'time_format' => get_option( 'time_format' ),
    ) );
		
		wp_register_script('wplc-admin-chat-events', WPLC_PLUGIN_URL.'/js/wplc_u_admin_events.js', array("wplc-admin-js-agent", "wplc-admin-js-sockets", "wplc-admin-chat-server"), WPLC_PLUGIN_VERSION, false); //Added this for async storage calls
		wp_enqueue_script('wplc-admin-chat-events');
		
		if (isset($wplc_settings['wplc_show_date']) && $wplc_settings["wplc_show_date"] == '1') {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_date', 'true');
        } else {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_date', 'false');
		}

		if (isset($wplc_settings['wplc_show_time']) && $wplc_settings["wplc_show_time"] == '1') {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_time', 'true');
        } else {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_time', 'false');
		}

		if (isset($wplc_settings['wplc_show_name']) && $wplc_settings["wplc_show_name"] == '1') {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_name', 'true');
        } else {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_name', 'false');
		}

		if (isset($wplc_settings['wplc_show_avatar']) && $wplc_settings["wplc_show_avatar"] == '1') {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_avatar', 'true');
        } else {
			wp_localize_script('wplc-admin-chat-server', 'wplc_show_avatar', 'false');
		}
	}
}


/**
 * Loads remote dashboard styles
 *
 * @return void
*/
function wplc_admin_remote_dashboard_styles(){
    $wplc_settings = get_option("WPLC_SETTINGS");

    wp_register_style( 'wplc-admin-style', WPLC_PLUGIN_URL."/css/chat_dashboard/admin_style.css", false, WPLC_PLUGIN_VERSION );
    wp_enqueue_style( 'wplc-admin-style' );

    if (!isset($wplc_settings['wplc_show_avatar']) || (isset($wplc_settings['wplc_show_avatar']) && intval($wplc_settings['wplc_show_avatar']) == 0) ) {
        wp_add_inline_style( 'wplc-admin-style', ".wplc-user-message, .wplc-admin-message { padding-left: 0 !important; }" );

    } else if( !isset($wplc_settings['wplc_show_name']) || (isset($wplc_settings['wplc_show_name']) && intval($wplc_settings['wplc_show_name']) == 0) ){
    	//User has enabled the gravatar, but has chosen to hide the user name.
    	//This causes some issues with admin display so let's just add some different styling to get around this
    	$inline_identity_css =
            "
            .wplc-admin-message-avatar, .wplc-user-message-avatar {
			    max-width:28px !important;
			    max-height:28px !important;
			}

			.wplc-admin-message, .wplc-user-message{
			    padding-left:25px !important;
			}

			.wplc-admin-message::before, .wplc-user-message::before  {
			    content: ' ';
			    width: 7px;
			    height: 7px;
			    background:#343434;
			    position: absolute;
			    left: 12px;
			    border-radius: 2px;
			    z-index: 1;
			}

			.wplc-user-message::before {
			    background:#2b97d2;
			}
            ";

        wp_add_inline_style( 'wplc-admin-style', $inline_identity_css );
    }

    wp_register_style( 'wplc-admin-style-bootstrap', WPLC_PLUGIN_URL."/css/bootstrap.css", false, WPLC_PLUGIN_VERSION );
    wp_enqueue_style( 'wplc-admin-style-bootstrap' );

	if(empty($wplc_settings['wplc_disable_emojis']))
	{
		wp_register_style( 'wplc-admin-style-emoji', WPLC_PLUGIN_URL."/js/vendor/wdt-emoji/wdt-emoji-bundle.css", false, WPLC_PLUGIN_VERSION );
		wp_enqueue_style( 'wplc-admin-style-emoji' );
	}
	
	do_action("wplc_admin_remote_dashboard_styles_hook");
}

/*
 * Add action for notice checks
*/
if ( ! function_exists( "wplc_active_chat_box_notices" ) ) {
	if(isset($_GET['page']) && $_GET['page'] === "wplivechat-menu"){
		add_action( "wplc_admin_remote_dashboard_above", "wplc_active_chat_box_notices" );
	}
}

add_action("admin_notices", "wplc_node_v8_plus_notice_dismissable");
/*
 * Displays an admin notice (which can be dismissed), to notify any V8+ users of the node option (if not already checked)
*/
function wplc_node_v8_plus_notice_dismissable() {
  $page='';
  if (isset($_GET['page'])){
    $page=preg_replace('/[^a-z0-9-]/', '', sanitize_text_field($_GET['page']));
  }
  if (!empty($page) && strpos($page, 'wplivechat') === 0) { // only if it begins with wplivechat
    if (isset($_GET['wplc_dismiss_notice_v8']) && $_GET['wplc_dismiss_notice_v8'] === "true") {
      update_option("wplc_node_v8_plus_notice_dismissed", 'true');
    }
    
    $wplc_settings = get_option("WPLC_SETTINGS");
    if (isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1) {
      //Do Nothing
    } else {
      //User is not on node, let's check if they have seen this notice before, if not, let's show a notice
      $wplc_has_notice_been_dismissed = get_option("wplc_node_v8_plus_notice_dismissed", false);
      if ($wplc_has_notice_been_dismissed === false) {
        //Has not been dismissed
        $output = "<div class='notice notice-warning' style='border-color: #0180bc;'>";
        $output .=     "<p><strong>" . __( 'Welcome to V8 of WP Live Chat Support', 'wplivechat' ) . "</strong></p>";
        $output .=  "<p>" . __('Did you know, this version features high speed message delivery, agent to agent chat, and a single window layout?', 'wplivechat') . "</p>";
        $output .=  "<p>" . __('To activate this functionality please navigate to Live Chat -> Settings -> Advanced Features -> And enable our Chat Server option.', 'wplivechat') . "</p>";

        $output .=  "<p>";
        $output .=    "<a href='?page=wplivechat-menu-settings#tabs-beta' class='button button-primary'>" . __("Show me!", "wplivechat") . "</a> ";
        $output .=    "<a href='?page=".$page."&wplc_dismiss_notice_v8=true' id='wplc_v8_dismiss_node_notice' class='button'>" . __("Don't Show This Again", "wplivechat") . "</a>";
        $output .=  "</p>";
        $output .= "</div>";
        echo $output;
      }
    }
  }
}

add_filter("wplc_activate_default_settings_array", "wplc_node_set_default_settings", 10, 1);
/*
 * Adds default node setting to the default settings array
*/
function wplc_node_set_default_settings($wplc_default_settings_array){
    if(is_array($wplc_default_settings_array)){
        if(!isset($wplc_default_settings_array['wplc_use_node_server'])){
            //Is not set already
            $wplc_default_settings_array['wplc_use_node_server'] = 1;
        }
    }
    return $wplc_default_settings_array;
}

add_filter( 'rest_url', 'wplc_node_rest_url_ssl_fix');
/**
 * Changes the REST URL to include the SSL version if we are using SSL
 * See https://core.trac.wordpress.org/ticket/36451
 */
function wplc_node_rest_url_ssl_fix($url){
        if (is_ssl()){
            $url = set_url_scheme( $url, 'https' );
            return $url;
        }
        return $url;
}

/**
 * Returns an array of pages/posts available on the site
*/
function wplc_node_pages_posts_array(){
    $r = array(
        'depth' 	=> 0,
        'child_of' 	=> 0,
        'echo' 		=> false,
        'id' 		=> '',
        'class' 	=> '',
        'show_option_none' 		=> '',
        'show_option_no_change' => '',
        'option_none_value' 	=> '',
        'value_field' 			=> 'ID',
    );

 	$pages = get_pages($r);
 	$posts = get_posts(array('posts_per_page' => -1));

 	$posts_pages = array_merge($pages,$posts);

 	$return_array = array();

 	foreach ($posts_pages as $key => $value) {
 		$post_page_id = $value->ID;
 		$post_page_title = $value->post_title;

 		$return_array[get_permalink($post_page_id)] = $post_page_title;
 	}

 	return $return_array;
 }



add_action("wplc_admin_remoter_dashboard_scripts_localizer", "wplc_admin_remote_dashboard_dynamic_translation_handler");
/*
 * Localizes an array of strings and ids in the dashboard which need to be replaced
 * Loads the custom JS file responsible for replacing the content dynamically.
*/
function wplc_admin_remote_dashboard_dynamic_translation_handler(){

    wp_register_script('wplc-admin-dynamic-translation', WPLC_PLUGIN_URL.'/js/wplc_admin_dynamic_translations.js', array("wplc-admin-js-agent", "wplc-admin-js-sockets", "jquery"), WPLC_PLUGIN_VERSION, false); //Added this for async storage calls

    $wplc_dynamic_translation_array = array(
      'nifty_bg_holder_text_inner' => __('Connecting...', 'wplivechat'),
      'nifty_admin_chat_prompt_title' => __('Please Confirm', 'wplivechat'),
      'nifty_admin_chat_prompt_confirm' => __('Confirm', 'wplivechat'),
      'nifty_admin_chat_prompt_cancel' => __('Cancel', 'wplivechat'),
      'active_count_string' => __(' Active visitors', 'wplivechat'),
      'wplc-agent-info' => __('Agent(s) Online', 'wplivechat'),
      'wplc_history_link' => __('Chat History', 'wplivechat'),
      'nifty_agent_heading' => __('Agents', 'wplivechat'),
      'drag_zone_inner_text' => __('Drag Files Here', 'wplivechat'),
      'chatTransferLink' => __('Transfer', 'wplivechat'),
      'chatTransferDepLink' => __('Department Transfer', 'wplivechat'),
      'chatDirectUserToPageLink' => __('Direct User To Page', 'wplivechat'),
      'chatCloseTitle' => __('Leave chat', 'wplivechat'),
      'chatEndTitle' => __('End chat', 'wplivechat'),
      'chatTransferUps' => __('Transfer', 'wplivechat'),
      'chatDirectUserToPageUps' => __('Direct User To Page', 'wplivechat'),
      'nifty_event_heading' => __('Events', 'wplivechat'),
      'nifty_join_chat_button' => __('Join chat', 'wplivechat'),
      'nifty_filter_button' => __('Filters', 'wplivechat'),
      'nifty_new_visitor_item' => __('New Visitors (3 Min)', 'wplivechat'),
      'nifty_active_chats_item' => __('Active Chats', 'wplivechat'),
      'nifty_clear_filters_item' => __('Clear Filters', 'wplivechat'),
      'nifty_active_chats_heading' => __('Active visitors', 'wplivechat'),
      'nifty_vis_col_heading' => __('Visitor', 'wplivechat'),
      'nifty_vis_info_heading' => __('Info', 'wplivechat'),
      'nifty_vis_page_heading' => __('Page', 'wplivechat'),
      'nifty_vis_status_heading' => __('Chat Status', 'wplivechat'),
      'nifty_vis_dep_heading' => __('Department', 'wplivechat'),
      'wdt-emoji-search-result-title' => __('Search Results', 'wplivechat'),
      'wdt-emoji-no-result' => __('No emoji found', 'wplivechat')
    );

    apply_filters("wplc_adming_dynamic_translation_filter", $wplc_dynamic_translation_array);


    wp_localize_script('wplc-admin-dynamic-translation', 'wplc_dynamic_translation_array', $wplc_dynamic_translation_array);
    wp_enqueue_script('wplc-admin-dynamic-translation');

}




add_action("wplc_admin_remoter_dashboard_scripts_localizer", "wplc_admin_remote_dashboard_localize_variables");
/*
 * Localizes all the admin variables
*/
function wplc_admin_remote_dashboard_localize_variables(){
	$wplc_settings = get_option("WPLC_SETTINGS");

	$user_id = get_current_user_id();
	$user_department = get_user_meta($user_id ,"wplc_user_department", true);

	$department_array = array();
	$departments = wplc_get_all_deparments_mrg();
	if($departments){
		foreach($departments as $dep){
			$department_array[$dep->id] = $dep->name;
		}
	}  

	$departments['any'] = __("None", "wplivechat"); 
   	$default_department = isset($wplc_settings['wplc_default_department']) ? $wplc_settings['wplc_default_department'] : 0;

	

	if ( !empty( $department_array ) ) {   
        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_departments', $department_array );

        if(intval($default_department) > 0){
          wp_localize_script( 'wplc-admin-js-agent', 'bleeper_default_department_tag', $department_array[$default_department] );   
        } else {
          wp_localize_script( 'wplc-admin-js-agent', 'bleeper_default_department_tag', $departments['any'] );
        }
    } else {

        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_default_department_tag', 'any' );   
    }
	 wp_localize_script( 'wplc-admin-js-agent', 'wplc_integration_pro_active', "true");

    if ( !empty( $user_department ) ) {
	     wp_localize_script( 'wplc-admin-js-agent', 'bleeper_agent_department', $user_department);
    }

    if (isset($wplc_settings['wplc_node_disable_typing_preview']) && $wplc_settings['wplc_node_disable_typing_preview'] == '1') {
        wp_localize_script( 'wplc-admin-js-agent', 'bleeper_disable_typing_preview', 'true');
    }   
    if (isset($wplc_settings['wplc_ringtone'])) {
        $wplc_ringtone_selected =  str_replace("http:", "", $wplc_settings['wplc_ringtone'] );
    } else {
        $wplc_ringtone_selected = WPLC_PLUGIN_URL.'includes/sounds/general/ring.wav';
    }

	if (!empty($wplc_settings['wplc_messagetone'])) {
		$wplc_messagetone_selected =  str_replace("http:", "", $wplc_settings['wplc_messagetone'] );
	} else {
		$wplc_messagetone_selected = WPLC_PLUGIN_URL.'includes/sounds/general/ding.mp3';
	}

  wp_localize_script( 'wplc-admin-js-agent', 'bleeper_ring_override', $wplc_ringtone_selected);
	wp_localize_script( 'wplc-admin-js-agent', 'bleeper_message_override', $wplc_messagetone_selected);

	wp_register_script('wplc-admin-chat-events-pro', WPLC_PLUGIN_URL . 'js/wplc_admin_pro_events.js', array("wplc-admin-js-agent", "wplc-admin-chat-events", "wplc-admin-chat-server"), WPLC_PLUGIN_VERSION, false); //Added this for async storage calls
    wp_enqueue_script('wplc-admin-chat-events-pro');

	wp_register_script('wplc-admin-bleeper-event-tracking-pro', WPLC_PLUGIN_URL . 'js/wplc_bleeper_admin_events.js', array("wplc-admin-js-agent", "wplc-admin-chat-events", "wplc-admin-chat-server"), WPLC_PLUGIN_VERSION, false); //Added this for async storage calls
    wp_enqueue_script('wplc-admin-bleeper-event-tracking-pro');




}


add_filter("wplc_admin_dashboard_layout_node_request_variable_filter", "wplc_admin_dashboard_layout_node_request_add_mrg_variables", 10, 1);
/*
 * Adds the Pro request variables to our node request
*/
function wplc_admin_dashboard_layout_node_request_add_mrg_variables($variables){
	if(is_array($variables)){
		$variables['pro'] = "true";
		$variables['include_filters'] = "true";
		$variables['include_transfers'] = "true";
		$variables['include_media_sharing'] = "true";
		$variables['include_quick_responses'] = "true";
		$variables['include_departments'] = "true";
        $variables['aid'] = get_current_user_id();

		$variables['agent_count'] = wplc_maa_total_agents_online();
	} 
	return $variables;
}



add_filter("wplc_admin_remote_dashboard_localize_tips_array", "wplc_admin_remote_dashboard_localize_tips_array_mrg_handler", 1, 1);
/*
 * Overrides the tip array in the Pro add-on
*/
function wplc_admin_remote_dashboard_localize_tips_array_mrg_handler($tip_array){
	$tip_array = array(
      "0" => "<p>" . __("You can transfer chats from within a chat by clicking on the in chat menu, and selecting Transfer Chat or Transfer Department", "wplivechat") . "</p>",
      "1" => "<p>" . __("You can share files quickly when in a chat, by simply dragging a file into the chat window!", "wplivechat") . "</p>",
  	  "2" => "<p>" . __("You can now move between chats without ending/closing an open chat", "wplivechat") . "</p>"
    );

    return $tip_array;

}


/**
 * Return a count of unread messages for a specific agent from a specific agent
 *
 * @param  [intval] $ato   Agent ID 
 * @param  [intval] $afrom Agent ID
 * @return [intval]        Count
 */
function wplc_return_unread_agent_messages_mrg( $ato = 0, $afrom = 0 ) {
    global $wpdb;
    global $wplc_tblname_msgs;
    $count = $wpdb->get_var( $wpdb->prepare("SELECT count(id) FROM $wplc_tblname_msgs WHERE ato = %d AND afrom = %d AND status = 0",$ato,$afrom) );
    return $count;
}

add_action("wplc_admin_remote_dashboard_above", "wplc_admin_remote_dashboard_quick_responses_container_mrg");
/*
 * Adds the quick response container
*/
function wplc_admin_remote_dashboard_quick_responses_container_mrg(){

    $wplc_settings = get_option( 'WPLC_SETTINGS' );
    $wplc_quick_response_order_by = isset( $wplc_settings['wplc_quick_response_orderby'] ) ? sanitize_text_field( $wplc_settings['wplc_quick_response_orderby'] ) : 'title';
    $wplc_quick_response_order = isset( $wplc_settings['wplc_quick_response_order'] ) ? sanitize_text_field( $wplc_settings['wplc_quick_response_order'] ) : 'DESC';
    $args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        'category' => '',
        'order' => $wplc_quick_response_order,
        'orderby' => $wplc_quick_response_order_by != 'number' ? $wplc_quick_response_order_by : 'meta_value_num',
        'include' => '',
        'exclude' => '',
        'meta_key' => $wplc_quick_response_order_by != 'number' ? '' : 'wplc_quick_response_number',
        'meta_value' => '',
        'post_type' => 'wplc_quick_response',
        'post_mime_type' => '',
        'post_parent' => '',
        'post_status' => 'publish',
        'suppress_filters' => true);
    $posts_array = get_posts($args);
    echo '<div id="quick_response_drawer_container" style="display:none;">';
    echo     '<h5>'.__("Quick Responses", "wplivechat").'</h5>';
    echo    '<hr>';         
     //Add quick responses
    if($posts_array){
        foreach ($posts_array as $post) {
            echo '<div class="quick_response_item">'.$post->post_content.'</div>';
        }
    } else {
        echo "<div style='position: absolute; top: 23px; bottom: 0; left: 10px; right: 0; margin: auto; height: 20px;'>";
        echo __("No quick responses found", "wplivechat") . " - <a target='_blank' href='" . admin_url('post-new.php?post_type=wplc_quick_response') . "'>" . __("Add New Quick Response", "wplivechat") . "</a>"; 
        echo "</div>";
    }
    echo '</div>';
}


add_filter("wplc_agent_display_name_filter", "wplc_agent_display_name_filter_control_mrg", 10, 1);
/*
 * Filters the agent display name 
*/
function wplc_agent_display_name_filter_control_mrg($wplc_display_name){
    $settings = get_option("WPLC_ACBC_SETTINGS");
    
    if( isset( $settings['wplc_use_wp_name'] ) && $settings['wplc_use_wp_name'] == '1' ){
        return $wplc_display_name;
        
    } else {
        if (!empty($settings['wplc_chat_name'])) {
            $wplc_display_name = $settings['wplc_chat_name'];
        }
    }

    return $wplc_display_name;

}

add_action("wplc_admin_remote_dashboard_styles_hook", "wplc_admin_remote_dashboard_styles_mrg");
/**
 * Loads remote dashboard styles
 *
 * @return void
*/
function wplc_admin_remote_dashboard_styles_mrg(){
    $wplc_settings = get_option("WPLC_SETTINGS");

    if (isset($wplc_settings['wplc_disable_initiate_chat'])  && $wplc_settings['wplc_disable_initiate_chat'] == '1' ) { 
        $initiate_chat_inline_styles = 
            "
            .init_chat {
                display:none !important;
            }
            ";

        wp_add_inline_style( 'wplc-admin-style', $initiate_chat_inline_styles );
    } 

    $wplc_choose_settings = get_option("WPLC_CHOOSE_SETTINGS");
    if(!isset($wplc_choose_settings['wplc_auto_online']) || $wplc_choose_settings['wplc_auto_online'] != 1) {
        //Hide the selectionator
        $choose_online_chat_inline_styles = 
            "
            #choose_online { display: none; }
            ";

        wp_add_inline_style( 'wplc-admin-style', $choose_online_chat_inline_styles );
    }

}
