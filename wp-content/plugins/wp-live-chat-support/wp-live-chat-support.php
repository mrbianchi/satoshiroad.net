<?php
/*
  Plugin Name: WP Live Chat Support
  Plugin URI: http://www.wp-livechat.com
  Description: The easiest to use website live chat plugin. Let your visitors chat with you and increase sales conversion rates with WP Live Chat Support.
  Version: 8.0.34
  Author: WP-LiveChat
  Author URI: http://www.wp-livechat.com
  Text Domain: wplivechat
  Domain Path: /languages
*/

/*
 * 8.0.34 - 2019-06-03 - High priority
 * Fixed issue with activation function
 * Fixed issue with node server ping when chatbox is offline
 * Fixed bug where embedded chatboard was not showing in admin pages
 * Refactored Bootstrap Styles to prevent them from affecting WordPress Styles
 * Replaced all chat servers with Google Cloud servers.
 * 
 * 8.0.33 - 2019-05-30 - High priority
 * Adds additional security hardening to the REST API (Reported by Jonny Milliken - Active Intelligence)
 * Fixed issue where chat rel was not being respected by the converter function
 * Fixed DDOS Vector on the End Chat button by hiding and disabling the end chat functionality once clicked
 * Fixed DDOS Vector which allowed more than 2000 characters to be send as a user message. Checks in place to prevent this.
 *
 * 8.0.32 - 2019-05-28 - High priority
 * Removed references to esc_attr within PO file configuration
 * Remove Custom Scripts area (including JS and CSS)
 * Remove handlers for Custom Scripts
 * Added back the AES and Crypto Helper classes to support backwards compat encryption/decryption of messages
 * Added supporting encryption/decryption functions
 * Added a dismissable notice explaining the deprecation of local encryption and local server usage
 * Restored the ability to enable/disable encryption
 * Restored the ability to enable/disable the use of local servers
 * Fixed bug where Ace library was still trying to initialize custom JS and CSS boxes
 *
 * 8.0.31 - 2019-05-27 - High priority
 * Additional sanitization and security cleanup
 * Added upgrade check to clear custom JS prior to version 8.0.31
 * Updated readme to document use of external services
 * Fixed a bug in departments transfer where name is not defined
 * Fixed issue with GUID not being generated after re-activation
 * Fixed issue with escape loop in settings area
 * Removed setting to disable remote servers
 * Removed local encryption functionality, every chat message is using HTTPS secure connection
 * Removed AES and CryptoHelpers as these are no longer used
 * Removed manual inclusion of SMTP and PHPMailer 
 * Removed mail type setting, along with SMTP options
 *
 * 8.0.30 - 2019-05-20 - High priority 
 * Security revision, code updated with latest security best practices
 * Removed all external dependencies
 * Nonce fields and URLs added to actions within admin area
 * Implemented WP Prepared Queries to increase security
 * Fixed issues with REST API Test system
 * Changed all non-service remote file references to local references
 * Ensured all asset files are loaded dynamically, and paths are never assumed
 * Implemented realpath and proper sanitization on all file handlers
 * Removed API Keys area as this is no longer in use
 * Updated Bootstrap to V4.3.1
 * Updated modern dashboard styles to support updated Bootstrap version
 * Added local upgrade variables to modern dashboard render function
 * Fixed bug in wplc_choose.js where duplicate Switchery toggles were generated
 * Changed position/style of Online/Offline toggle
 * Changed loading of wplc_node.js file on the frontend to use wp_enqueue_script
 * Deprecated 'wplc_submit_find_us' handler as this is no longer in use 
 * Removed any reference to old deprecated Pro version
 * Replaced all CURL requests with WordPress HTTP API requests
 * Removed hardocded media.tenor image reference (loading graphic in GIF integration)
 * Replaced all 'esc_' calls with respective WordPress sanitization calls 
 * Added sanitization to all $_GET and $_POST variable to prevent any injection or storage of unsafe values 
 * Deprecated 'wplc_api_call_to_server_visitor' REST endpoint as it was not in use and made use of session data
 * Removed AJAX use of 'ob_start' to improve performance
 * Added checks to prevent direct file access.
 *
 * 8.0.29 - 2019-05-17 - High priority
 * Security fix in Custom JS configuration
 * 
 * 8.0.28 - 2019-05-17 - High priority
 * Security fix in GDPR configuration
 * 
 * 8.0.27 - 2019-05-15 - High priority
 * Free and PRO plugins are now merged
 * All previously "PRO" features are now unlocked
 * Legacy Code cleanup for version < 8
 * Minor changes in the admin menu names/slugs
 * Fixed styling issue in Triggers content replacement editor
 * Fixed various PHP warnings/notices
 * Removed the option to download/delete chats from the front-end 
 * Fixed an issue where Document Suggestions would appear twice
 * Updated/synchronised translation files
 * Improved security for js configuration
 * Fixed XSS attack in GDPR page
 * Security Patch: Added save settings nonce field and processing to prevent remote access. Resolves potential Cross Site Scripting attacks. Thanks to John Castro (Sucuri.net)
 * Improved file upload extension validation
 * 
 * 8.0.26 - 2019-04-04 - Medium priority
 * Added 16 more servers
 * Improved server selection code
 * Minor fixes to administration console
 * Deprecation of mobile and desktop apps
 * 
 * 8.0.25 - 2019-03-19 - Medium priority
 * Modified server integration code
 * Modified remote file sources
 * Added update notice to ensure stability
 * 
 * 8.0.24 - 2019-03-04 - Medium priority
 * Added styling settings hook.
 * Added Slovenian translation files. Thanks to Zarko Germek
 * Fixed bug where transcript message would be empty if settings were cleared (defaults)
 * Fixed bug where slashes were repeatedly added to GDPR company name
 * Fixed issues with history styling when using our servers
 * Fixed a bug where wp_localize_script would localize an empty value, causing WP Core errors appearing on the frontent (Survey Module)
 * Fixed placement issue when using classic theme
 * Modified cookie lifespan for dashboard redirect
 * Modified remote dashboard URL to load directly from API
 * Modified all Gravatar links to default to Mystery Man
 * Improved styling in settings area
 * Updated Bulgarian translation file. Thanks to Pavel Valkov.
 * Tested on WordPress 5.1.0
 *
 * 8.0.23 - 2019-02-05 - Low priority
 * Fixed access to new dashboard for non admin agents
 * Fixed documentation suggestions not working (Pro)
 *
 * 8.0.22 - 2019-02-04 - Low priority
 * Introduced a new dashboard that showcases latest blog posts, the latest podcast episode and the system status
 * Moved GDPR warnings for first time users to the settings page only
 * Moved the warning regarding desktop notifications to the settings page only
 * 
 * 8.0.21 - 2018-12-18 - Low priority
 * Readme Update: Coming soon features
 * Readme Update: Features list
 * Tested on WordPress 5.0.1
 * Updated ZH_CN translation files. Thanks to Air
 *
 * 8.0.20 - 2018-11-29 - Medium priority
 * Fixed issue where relay server requests were made via a shortpoll. (Performance Update)
 * Re-enabled automatic server location selection for new insttalations
 * GDPR Compliance is now disabled by default for new installations
 * 
 * 8.0.19 - 2018-11-19 - Medium priority
 * Tested on WordPress 5 Beta
 * Tested Basic Gutenberg Block Support
 *
 * 8.0.18 - 2018-11-01 - High Priority
 * Fixed XSS vulnerability within the GDPR search system (Thanks to Tim Coen)
 * Fixed Self-XSS vulnerability within the message input field on both dashboard and chat box (Thanks to Tim Coen)
 *
 * 8.0.17 - 2018-10-19 - Low priority
 * Removes WP User Avatar option from settings page. This was incorrectly included in the last release.
 *
 * 8.0.16 - 2018-10-18 - Low priority
 * Fixed undefined 'wplc_user_avatars' not defined error on frontend
 * 
 * 8.0.15 - 2018-10-11 - High priority
 * Added WP User Avatar integration
 * Added jQuery 3 compatibility as per WordPress.org guidelines
 * Added auto hide of chat ended prompt on close or restart
 * Fixed a possible injection issue within the notification control (Thanks to Nico from https://serhack.me)
 * Fixed Gutenberg and Yoast compatibility issue
 * Fixed minor issue with rest storage module
 * Fixed minor styling issue with popup dashbaord
 * Fixed some core issues which influenced custom fields (Pro)
 * Fixed issue with Android Browser image missing in dashboard
 * Fixed delete chat history not deleting messages
 * Fixed nonce checking for non-logged in users failing on cached pages for AJAX and WP API
 * Updated NL translation with GDPR strings (Google Translate)
 * Updated EL translation files. Thanks to Ioannis Barkouzos
 * Updated FR translation files. Thanks to Freitas Junior
 * Updated IT translation files. Thanks to Carlo Piazzano
 * Updated AR translation files. 
 *
 * 8.0.14 - 2018-07-19 - Low priority
 * Removed 'let' from wplc_server.js file (Adds Safari compatibility)
 * Fixed issues with Google Analytics integration when using our servers
 * Fixed issues with chat box styling with classic theme and GDPR module enabled
 * Fixed issues with Contact From Ready integration styling with modern theme
 * Fixed issues with Slack integration extension
 * Bulgarian Translation Added (Thank you Emil Genchev!)
 * Fixed erroneous display of set_time_limit and safe_mode warnings
 * Fixed a big that lead to the deletion of sessions and not messages when a chat was marked for deletion
 * Improved security in the chat history code
 * Added better styling support for smaller width devices (<500px)
 * Updated Swedish translation files
 * Added Arabic translation files
 * Fixed the duplicate message history loading in the history area
 * Fixed core framework issues with Voice Note system
 * Fixed an issue where invalid query selectors would break the 'Open chat via' functionality
 * Fixed an issue with username encoding
 * Fixed issue with surveys showing after chat end
 * Fixed an issue with classic theme display when anchored to left/right positions
 * Added auto transcript mailing to frontend end chat button, and REST API
 * Added an 'incomplete' class when GDPR checkbox is not ticket, to draw attention to this input field
 * Tested Multi-Site compatibility
 * Updated all PO/MO files with updated sources
 * Added default GDPR translations for DE, FR, ES, and IT languages (Using Google Translate)
 * 
 * 8.0.13 - 2018-06-06 - Medium priority
 * Fix chat delay not working for first visit and offline
 * Optimize images
 * Mootools compatibility
 * Fix new chat request email notifications when Pro is active
 * 
 * 8.0.12 - 2018-05-29 - High priority
 * Fixed a bug which caused the chat box not to display on some sites
 * Fixed minor styling issues
 *
 * 8.0.11 - 2018-05-28 - High priority
 * Fixed a bug that caused a fatal error on PHP 5.3 and below
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wplc_p_version;
global $wplc_tblname;
global $wpdb;
global $wplc_tblname_chats;
global $wplc_tblname_msgs;
global $wplc_tblname_offline_msgs;
global $current_chat_id; 
global $wplc_tblname_chat_ratings; 
global $wplc_tblname_chat_triggers; 


/**
 * This stores the admin chat data once so that we do not need to keep sourcing it via the WP DB or Cloud DB
 */
global $admin_chat_data;
$admin_chat_data = false;

$wplc_tblname_offline_msgs = $wpdb->prefix . "wplc_offline_messages";
$wplc_tblname_chats = $wpdb->prefix . "wplc_chat_sessions";
$wplc_tblname_msgs = $wpdb->prefix . "wplc_chat_msgs";
$wplc_tblname_chat_ratings = $wpdb->prefix . "wplc_chat_ratings";
$wplc_tblname_chat_triggers = $wpdb->prefix . "wplc_chat_triggers";

// Load Config
require_once (plugin_dir_path(__FILE__) . "config.php");
require_once (plugin_dir_path(__FILE__) . "ajax.php");
require_once (plugin_dir_path(__FILE__) . "functions.php");

// Check if PRO plugin is active
function is_pro_active( $plugin ) {

	// Check if it's a WP network
	if ( ! is_multisite() ) {
		$active_plugins = get_option( 'active_plugins', array() );		
		return in_array( $plugin, (array) $active_plugins );
	}

	$plugins = get_site_option( 'active_sitewide_plugins' );
	if ( isset( $plugins[ $plugin ] ) ) {
		return true;
	}else {
		return false;
	}
	
}

// Check if PRO plugin is present
function is_pro_present() {

	if ( is_readable( WP_PLUGIN_DIR . "/wp-live-chat-support-pro/wp-live-chat-support-pro.php" ) ) {
		return true;
	} else {
		return false;
	}
}

		
/*
 * Load Includes
 */
	
require_once (plugin_dir_path(__FILE__) . "includes/surveys.php");
require_once (plugin_dir_path(__FILE__) . "includes/notification_control.php");
require_once (plugin_dir_path(__FILE__) . "includes/modal_control.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_data_triggers.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_roi.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_departments.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_transfer_chats.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_custom_fields.php");
require_once (plugin_dir_path(__FILE__) . "includes/wplc_agent_data.php");


/*
 * Load Modules
 */
require_once (plugin_dir_path(__FILE__) . "modules/documentation_suggestions.php");
require_once (plugin_dir_path(__FILE__) . "modules/offline_messages_custom_fields.php");
require_once (plugin_dir_path(__FILE__) . "modules/google_analytics.php");
require_once (plugin_dir_path(__FILE__) . "modules/api/wplc-api.php");
require_once (plugin_dir_path(__FILE__) . "modules/advanced_features.php");
require_once (plugin_dir_path(__FILE__) . "modules/node_server.php");
require_once (plugin_dir_path(__FILE__) . "modules/module_gif.php");
require_once (plugin_dir_path(__FILE__) . "modules/webhooks_manager.php");
require_once (plugin_dir_path(__FILE__) . "modules/privacy.php");
require_once (plugin_dir_path(__FILE__) . "modules/api/wplc-api.php");
require_once (plugin_dir_path(__FILE__) . "modules/cta_animations.php"); 
require_once (plugin_dir_path(__FILE__) . "modules/advanced_tools.php"); 

/*
 * Added back for backwards compat decrypt
*/
if (class_exists("AES")) { } else { require( 'includes/aes_fast.php'); }
if (class_exists("cryptoHelpers")) { } else { require( 'includes/cryptoHelpers.php'); }

// Gutenberg Blocks
require_once (plugin_dir_path(__FILE__) . "includes/blocks/wplc-chat-box/index.php");
require_once (plugin_dir_path(__FILE__) . "includes/blocks/wplc-inline-chat-box/index.php");

// Shortcodes
require_once (plugin_dir_path(__FILE__) . "includes/shortcodes.php");

add_action('admin_init', 'detect_old_pro_plugin');
add_action("wp_login",'wplc_check_guid');
add_action('init', 'wplc_version_control');
add_action('init', 'wplc_init');
add_action('init', 'wplc_mrg_create_macro_post_type',100); 
add_action('init', 'wplc_first_run_check');
add_action('admin_init', 'wplc_head');
add_action('wp_ajax_wplc_admin_set_transient', 'wplc_action_callback');
add_action('wp_ajax_wplc_admin_remove_transient', 'wplc_action_callback');
add_action('wp_ajax_wplc_hide_ftt', 'wplc_action_callback');
add_action('wp_ajax_nopriv_wplc_user_send_offline_message', 'wplc_action_callback');
add_action('wp_ajax_wplc_user_send_offline_message', 'wplc_action_callback');
add_action('wp_ajax_delete_offline_message', 'wplc_action_callback');
add_action('wp_ajax_wplc_a2a_dismiss', 'wplc_action_callback');
add_action('activated_plugin', 'wplc_redirect_on_activate');
add_action('wp_ajax_wplc_choose_accepting','wplc_action_callback');
add_action('wp_ajax_wplc_choose_not_accepting','wplc_action_callback'); 
add_action('wplc_hook_action_callback','wplc_choose_hook_control_action_callback'); 
add_action('wplc_hook_admin_menu_layout_display_top','wplc_ma_hook_control_admin_meny_layout_display_top'); 
add_action('edit_user_profile_update', 'wplc_maa_set_user_as_agent'); 
add_action('personal_options_update', 'wplc_maa_set_user_as_agent'); 
add_action('edit_user_profile', 'wplc_maa_custom_user_profile_fields');
add_action('show_user_profile', 'wplc_maa_custom_user_profile_fields');
add_action('admin_enqueue_scripts', 'wplc_control_admin_javascript');
add_action('wp_ajax_wplc_add_agent', 'wplc_action_callback'); 
add_action('wp_ajax_wplc_remove_agent', 'wplc_action_callback'); 
add_action('wp_ajax_wplc_macro', 'wplc_action_callback'); 
add_action('wp_ajax_wplc_typing', 'wplc_action_callback'); 
add_action('wp_ajax_nopriv_wplc_typing', 'wplc_action_callback');
add_action('wp_ajax_wplc_upload_file', 'wplc_action_callback'); 
add_action('wp_ajax_nopriv_wplc_upload_file', 'wplc_action_callback'); 
add_action('wp_ajax_wplc_record_chat_rating', 'wplc_action_callback'); 
add_action('wp_ajax_nopriv_wplc_record_chat_rating', 'wplc_action_callback');
add_action('wp_enqueue_scripts', 'wplc_add_user_stylesheet');
add_action('admin_enqueue_scripts', 'wplc_add_admin_stylesheet');
add_action('admin_menu', 'wplc_admin_menu', 4);
add_action("wplc_hook_admin_chatbox_javascript","wplc_features_admin_js");
add_action('admin_head', 'wplc_superadmin_javascript');
add_action( 'admin_notices', 'deprecated_standalone_pro' );

// Activation Hook
register_activation_hook(__FILE__, 'wplc_activate');

// Load languages
function wplc_init() {
    $plugin_dir = basename(dirname(__FILE__)) . "/languages/";
    load_plugin_textdomain('wplivechat', false, $plugin_dir);
}

// Show notice if PRO plugin is present in versions < 8.0.27
function deprecated_standalone_pro() {
	if (is_pro_present()) {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'The additional WP Live Chat Support <b>PRO</b> plugin which you have installed is no longer needed. <br> Please keep it disabled or consider removing it.', 'wplivechat' ); ?></p>
    </div>
    <?php
	}
}

// If an old PRO plugin is detected disable it as it is not needed anymore.
function detect_old_pro_plugin() {
    if (is_pro_active("wp-live-chat-support-pro/wp-live-chat-support-pro.php")) {
        deactivate_plugins("wp-live-chat-support-pro/wp-live-chat-support-pro.php");
    }
}

/**
 * Redirect the user to the welcome page on plugin activate
 * @param  string $plugin
 * @return void
 */
function wplc_redirect_on_activate( $plugin ) {

	if( $plugin == plugin_basename( __FILE__ ) ) {
		if (get_option("WPLC_V8_FIRST_TIME") == true) {
	    	update_option("WPLC_V8_FIRST_TIME",false);
	    	// clean the output header and redirect the user
	    	@ob_flush();
			@ob_end_flush();
			@ob_end_clean();
	    	exit( wp_redirect( admin_url( 'admin.php?page=wplivechat-menu&action=welcome' ) ) );
	    }
	}
}


add_action( 'admin_init', 'wplc_redirect_on_activate' );



function wplc_version_control() {

    $current_version = get_option("wplc_current_version");
    if (!isset($current_version) || $current_version != WPLC_PLUGIN_VERSION) {

    	$wplc_settings = get_option( 'WPLC_SETTINGS' );

    	/**
    	 * Are we updating from a version before version 8?
    	 * If yes, set NODE to enabled
    	 */
    	if ( isset( $current_version ) ) {
    		$main_ver = intval( $current_version[0] );
    		if ( $main_ver < 8 ) {
    			if ( $wplc_settings ) {
    				$wplc_settings['wplc_use_node_server'] = 1;

    				update_option( "WPLC_V8_FIRST_TIME", true );

    			}
    		}

    	}

    	/** 
    	 * Added for security cleanup prior to version 8.0.31
    	*/
    	if( isset( $current_version )){
    		if(intval(str_replace('.', '', $current_version)) < 8031 || intval(str_replace('.', '', $current_version)) < 8032){
    			// Remove all custom JS if previous version was less than 8.0.31 or 8.0.32
    			update_option( "WPLC_CUSTOM_JS", '');
    			update_option( "WPLC_CUSTOM_CSS", '');
    		}
    	}


        $admins = get_role('administrator');
        if( $admins !== null ) {
        	$admins->add_cap('wplc_ma_agent');
        }

        $uid = get_current_user_id();
        update_user_meta($uid, 'wplc_ma_agent', 1);
        update_user_meta($uid, "wplc_chat_agent_online", time());



        /* add caps to admin */
        if (current_user_can('manage_options')) {
            global $user_ID;
            $user = new WP_User($user_ID);
            foreach ($user->roles as $urole) {
                if ($urole == "administrator") {
                    $admins = get_role('administrator');
                    $admins->add_cap('edit_wplc_quick_response');
                    $admins->add_cap('edit_wplc_quick_response');
                    $admins->add_cap('edit_other_wplc_quick_response');
                    $admins->add_cap('publish_wplc_quick_response');
                    $admins->add_cap('read_wplc_quick_response');
                    $admins->add_cap('read_private_wplc_quick_response');
                    $admins->add_cap('delete_wplc_quick_response');
                }
            }
        }



	    if (!isset($wplc_settings['wplc_pro_na'])) { $wplc_settings["wplc_pro_na"] = __("Chat offline. Leave a message", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_intro'])) { $wplc_settings["wplc_pro_intro"] = __("Hello. Please input your details so that I may help you.", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_offline1'])) { $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_offline2'])) { $wplc_settings["wplc_pro_offline2"] =  __("Sending message...", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_offline3'])) { $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_offline_btn']) || (isset($wplc_settings['wplc_pro_offline_btn']) && $wplc_settings['wplc_pro_offline_btn'] == "")) { $wplc_settings["wplc_pro_offline_btn"] = __("Leave a message", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_offline_btn_send']) || (isset($wplc_settings['wplc_pro_offline_btn_send']) && $wplc_settings['wplc_pro_offline_btn_send'] == "")) { $wplc_settings["wplc_pro_offline_btn_send"] = __("Send message", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_fst1'])) { $wplc_settings["wplc_pro_fst1"] = __("Questions?", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_fst2'])) { $wplc_settings["wplc_pro_fst2"] = __("Chat with us", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_fst3'])) { $wplc_settings["wplc_pro_fst3"] = __("Start live chat", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_sst1'])) { $wplc_settings["wplc_pro_sst1"] = __("Start Chat", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_sst1_survey'])) { $wplc_settings["wplc_pro_sst1_survey"] = __("Or chat to an agent now", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_sst1e_survey'])) { $wplc_settings["wplc_pro_sst1e_survey"] = __("Chat ended", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_sst2'])) { $wplc_settings["wplc_pro_sst2"] = __("Connecting. Please be patient...", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_pro_tst1'])) { $wplc_settings["wplc_pro_tst1"] = __("Reactivating your previous chat...", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_user_welcome_chat'])) { $wplc_settings["wplc_user_welcome_chat"] = __("Welcome. How may I help you?", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_welcome_msg'])) { $wplc_settings['wplc_welcome_msg'] = __("Please standby for an agent. While you wait for the agent you may type your message.","wplivechat"); }
	    if (!isset($wplc_settings['wplc_user_enter'])) { $wplc_settings["wplc_user_enter"] = __("Press ENTER to send your message", "wplivechat"); }
	    if (!isset($wplc_settings['wplc_close_btn_text'])) { $wplc_settings["wplc_close_btn_text"] = __("close", "wplivechat"); }


        if (!isset($wplc_settings['wplc_powered_by_link'])) { $wplc_settings["wplc_powered_by_link"] = "0"; }



        /* users who are updating will stay on the  existing theme */
        if (get_option("WPLC_V8_FIRST_TIME")) {} else {
          if (!isset($wplc_settings['wplc_newtheme'])) { $wplc_settings["wplc_newtheme"] = "theme-2"; }
        }


        if (!isset($wplc_settings['wplc_settings_color1'])) { $wplc_settings["wplc_settings_color1"] = "ED832F"; }
        if (!isset($wplc_settings['wplc_settings_color2'])) { $wplc_settings["wplc_settings_color2"] = "FFFFFF"; }
        if (!isset($wplc_settings['wplc_settings_color3'])) { $wplc_settings["wplc_settings_color3"] = "EEEEEE"; }
        if (!isset($wplc_settings['wplc_settings_color4'])) { $wplc_settings["wplc_settings_color4"] = "666666"; }



        if (!isset($wplc_settings['wplc_settings_align'])) { $wplc_settings["wplc_settings_align"] = 2; }

        if (!isset($wplc_settings['wplc_settings_enabled'])) { $wplc_settings["wplc_settings_enabled"] = 1; }

        if (!isset($wplc_settings['wplc_settings_fill'])) { $wplc_settings["wplc_settings_fill"] = "ed832f"; }

        if (!isset($wplc_settings['wplc_settings_font'])) { $wplc_settings["wplc_settings_font"] = "FFFFFF"; }

        if (!isset($wplc_settings['wplc_preferred_gif_provider'])) { $wplc_settings["wplc_preferred_gif_provider"] = 1; }
        if (!isset($wplc_settings['wplc_giphy_api_key'])) { $wplc_settings["wplc_giphy_api_key"] = ""; }
        if (!isset($wplc_settings['wplc_tenor_api_key'])) { $wplc_settings["wplc_tenor_api_key"] = ""; }

        wplc_handle_db();
        update_option("wplc_current_version", WPLC_PLUGIN_VERSION);


        if (!isset($wplc_settings['wplc_require_user_info'])) { $wplc_settings['wplc_require_user_info'] = "1"; }
	    if (!isset($wplc_settings['wplc_user_default_visitor_name'])) {
		    $wplc_default_visitor_name = __( "Guest", "wplivechat" );
		    $wplc_settings['wplc_user_default_visitor_name'] = $wplc_default_visitor_name;
	    }
        if (!isset($wplc_settings['wplc_loggedin_user_info'])) { $wplc_settings['wplc_loggedin_user_info'] = "1"; }
        if (!isset($wplc_settings['wplc_user_alternative_text'])) {
            $wplc_alt_text = __("Please click \'Start Chat\' to initiate a chat with an agent", "wplivechat");
            $wplc_settings['wplc_user_alternative_text'] = $wplc_alt_text;
        }
        if (!isset($wplc_settings['wplc_enabled_on_mobile'])) { $wplc_settings['wplc_enabled_on_mobile'] = "1"; }
        if(!isset($wplc_settings['wplc_record_ip_address'])){ $wplc_settings['wplc_record_ip_address'] = "0"; }
        if(!isset($wplc_settings['wplc_enable_msg_sound'])){ $wplc_settings['wplc_enable_msg_sound'] = "1"; }
	    if(!isset($wplc_settings['wplc_enable_font_awesome'])){ $wplc_settings['wplc_enable_font_awesome'] = "1"; }
	    if(!isset($wplc_settings['wplc_using_localization_plugin'])){ $wplc_settings['wplc_using_localization_plugin'] = 0; }

	    $wplc_settings = apply_filters('wplc_update_settings_between_versions_hook', $wplc_settings); //Added in 8.0.09

        update_option("WPLC_SETTINGS", $wplc_settings);

        do_action("wplc_update_hook");
    }



}

add_action("wplc_hook_set_transient","wplc_hook_control_set_transient",10);
function wplc_hook_control_set_transient() {
  $should_set_transient = apply_filters("wplc_filter_control_set_transient",true);
  if ($should_set_transient) {
    set_transient("wplc_is_admin_logged_in", "1", 70);
  }
}

add_action("wplc_hook_remove_transient","wplc_hook_control_remove_transient",10);
function wplc_hook_control_remove_transient() {
  delete_transient('wplc_is_admin_logged_in');
}

function wplc_check_guid() {
  $guid=get_option('WPLC_GUID');
  $guid_fqdn=get_option('WPLC_GUID_URL');
  $guid_lastcheck=intval(get_option('WPLC_GUID_CHECK'));
  if (empty($guid_lastcheck) || time()-$guid_lastcheck>86400) { // check at least once per day to ensure guid is updated properly
    $guid='';
  }
  if (empty($guid) || $guid_fqdn!=get_option('siteurl')) { // guid not assigned or fqdn is changed since last assignment
    $wplc_settings = get_option("WPLC_SETTINGS");
    $server=0;
    if (isset($wplc_settings['wplc_use_node_server'])){
      $server=intval($wplc_settings['wplc_use_node_server']);
      if ($server!=0){
        $server=1;
      }
    }
    $data_array = array(
      'method' => 'POST',
      'body' => array(
        'method' => 'get_guid',
        'url' => get_option('siteurl'),
        'server' => $server
      )
    );
    $response = wp_remote_post(WPLC_ACTIVATION_SERVER.'/api/v1', $data_array);
    if (is_array($response)) {
      if ( $response['response']['code'] == "200" ) {
        $data = json_decode($response['body'],true);
        if ($data && isset($data['guid'])){
          update_option('WPLC_GUID', sanitize_text_field($data["guid"]));
          update_option('WPLC_GUID_URL', get_option('siteurl'));
          update_option('WPLC_GUID_CHECK', time());
        }
      }
    }       
  }
}

function wplc_action_callback() {
  global $wpdb;
  $check = check_ajax_referer('wplc', 'security');

  if ($check == 1) {
    if ($_POST['action'] == 'wplc_a2a_dismiss') {
      $uid = get_current_user_id();
      update_user_meta($uid, 'wplc_a2a_upsell', 1);
    } else if ($_POST['action'] == 'delete_offline_message') {
      global $wplc_tblname_offline_msgs;
      $mid = intval( $_POST['mid'] );
      $sql = "DELETE FROM `$wplc_tblname_offline_msgs` WHERE `id` = '%d'";
      $sql = $wpdb->prepare($sql, $mid);
      $query = $wpdb->Query($sql);
      if( $query ){
        echo 1;
      }
    } else if ($_POST['action'] == "wplc_user_send_offline_message") {
      $cid=intval($_POST['cid']);
      $name=sanitize_text_field($_POST['name']);
      $email=sanitize_text_field($_POST['email']);
      $msg=sanitize_text_field($_POST['msg']);
      if (function_exists('wplc_send_offline_msg')){ wplc_send_offline_msg($name, $email, $msg, $cid); }
      if (function_exists('wplc_store_offline_message')){ wplc_store_offline_message($name, $email, $msg); }
      do_action("wplc_hook_offline_message",array(
        "cid"=>$cid,
        "name"=>$name,
        "email"=>$email,
        "url"=>get_site_url(),
        "msg"=>$msg
      ));
    } else if ($_POST['action'] == "wplc_admin_set_transient") {
      do_action("wplc_hook_set_transient");
    } else if ($_POST['action'] == "wplc_admin_remove_transient") {
      do_action("wplc_hook_remove_transient");
    } else if ($_POST['action'] == 'wplc_hide_ftt') {
      update_option("WPLC_FIRST_TIME_TUTORIAL",true);
    }

    do_action("wplc_hook_action_callback");
  }
  die(); // this is required to return a proper result
}


/**
* Check if this is the first time the user has run the plugin. If yes, set the default settings
* @since       	1.0.0
* @param       
* @return		void
* @author       Nick Duncan <nick@wp-livechat.com> 
*/
if (!function_exists("wplc_first_run_check")) { 
	function wplc_first_run_check() {
		
		/* set the default settings */
		
		if (!get_option("WPLC_IC_FIRST_RUN")) {
	        $wplc_freshdesk_data['wplc_ic_enable'] = 1;
	        update_option('WPLC_IC_SETTINGS', $wplc_freshdesk_data);
	        update_option("WPLC_IC_FIRST_RUN",true);
		}
		
		if (!get_option("WPLC_ma_FIRST_RUN")) {
	        $wplc_freshdesk_data['wplc_ma_enable'] = 1;
	        wplc_ma_first_time_install();
	        update_option('WPLC_ma_SETTINGS', $wplc_freshdesk_data);
	        update_option("WPLC_ma_FIRST_RUN",true);
		}
		
	    if (!get_option("WPLC_CHOOSE_FIRST_RUN")) {
	        /* set the default of true for "accepting chats" */
	        $wplc_choose_data['wplc_auto_online'] = true;
	        update_option("WPLC_CHOOSE_SETTINGS",$wplc_choose_data);

	        $choose_array = get_option("WPLC_CHOOSE_ACCEPTING");
	        $user_id = get_current_user_id();
	        $choose_array[$user_id] = true;
	        update_option("WPLC_CHOOSE_ACCEPTING",$choose_array);
	        update_option("WPLC_CHOOSE_FIRST_RUN",true);
	    }

	    if (!get_option("WPLC_ENCRYPT_FIRST_RUN")) {
	        $wplc_encrypt_data['wplc_enable_encryption'] = 0;
	        update_option('WPLC_ENCRYPT_SETTINGS', $wplc_encrypt_data);
	        update_option("WPLC_ENCRYPT_FIRST_RUN",true);
	    }
		
	    if (!get_option("WPLC_INEX_FIRST_RUN")) {
	        $wplc_inex_data['wplc_exclude_from_pages'] = "";
	        $wplc_inex_data['wplc_include_on_pages'] = "";
	        update_option('WPLC_INEX_SETTINGS', $wplc_inex_data);
	        update_option("WPLC_INEX_FIRST_RUN",true);
	    }
		
	}
}



/**
 * Decide who gets to see the various main menus (left navigation)
 * @return array
 * @since  6.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
add_filter("wplc_ma_filter_menu_control","wplc_filter_control_menu_control",10,1);
function wplc_filter_control_menu_control() {
	    if (current_user_can('wplc_ma_agent')) {
	        $array = array(
	          0 => 'wplc_ma_agent', /* main menu */
	          1 => 'manage_options', /* settings */
	          2 => 'wplc_ma_agent', /* history */
	          3 => 'wplc_ma_agent', /* missed chats */
	          4 => 'wplc_ma_agent', /* offline messages */
	          5 => 'manage_options', /* feedback */
			  6 => 'manage_options', /* Surveys */
	          );
	    } else if (current_user_can('manage_options')) {
	        $array = array(
	          0 => 'manage_options', /* main menu */
	          1 => 'manage_options', /* settings */
	          2 => 'manage_options', /* history */
	          3 => 'manage_options', /* missed chats */
	          4 => 'manage_options', /* offline messages */
	          5 => 'manage_options', /* feedback */
			  6 => 'manage_options' /* Surveys */
	          );
	    } else {
	    	$array = array(
	          0 => 'read', /* main menu */
	          1 => 'read', /* settings */
	          2 => 'read', /* history */
	          3 => 'read', /* missed chats */
	          4 => 'read', /* offline messages */
	          5 => 'read', /* feedback */
			  6 => 'read' /* Surveys */
	          );
	    }
    return $array;
}

add_action('admin_init', 'wplc_metric_dashboard_redirect');
function wplc_metric_dashboard_redirect(){
    try{
        $cap = apply_filters("wplc_ma_filter_menu_control",array());
        if(current_user_can($cap[1])){
            if (isset($_GET['page'])) {
                if ($_GET['page'] === 'wplivechat-menu') {
                    // check if we are overriding this redirect because the user pressed the "Chat now" button in the dashboard
                    if (isset($_GET['subaction']) && $_GET['subaction'] == 'override') { } else {
                        if(!isset($_COOKIE['wplcfirstsession'])) {
                            @setcookie("wplcfirstsession", true, time() + (60 * 60 * 24)); // 60s * 60m * 24h (Life span of cookie)
                            @Header("Location: ./admin.php?page=wplivechat-menu-dashboard");
                            exit();
                        }
                    }

                }
            }
        }
    } catch (Exception $ex){

    }
}

function wplc_admin_menu() {

    $cap = apply_filters("wplc_ma_filter_menu_control",array());
    if ( get_option("wplc_seen_surveys") ) { $survey_new = ""; } else { $survey_new = ' <span class="update-plugins"><span class="plugin-count">New</span></span>'; }

    $wplc_current_user = get_current_user_id();

    /* If user is either an agent or an admin, access the page. */
    if( get_user_meta( $wplc_current_user, 'wplc_ma_agent', true ) || current_user_can("wplc_ma_agent")){
      $wplc_mainpage = add_menu_page('WP Live Chat', __('Live Chat', 'wplivechat'), $cap[0], 'wplivechat-menu', 'wplc_admin_menu_layout', 'dashicons-format-chat');
      add_submenu_page('wplivechat-menu', __('Dashboard', 'wplivechat'), __('Dashboard', 'wplivechat'), $cap[1], 'wplivechat-menu-dashboard', 'wplc_admin_dashboard_layout');
      add_submenu_page('wplivechat-menu', __('Settings', 'wplivechat'), __('Settings', 'wplivechat'), $cap[1], 'wplivechat-menu-settings', 'wplc_admin_settings_layout');
      add_submenu_page('wplivechat-menu', __('Surveys', 'wplivechat'), __('Surveys', 'wplivechat'). $survey_new, $cap[2], 'wplivechat-menu-survey', 'wplc_admin_survey_layout');
    }


    /* only if user is both an agent and an admin that has the cap assigned, can they access these pages */
    if( get_user_meta( $wplc_current_user, 'wplc_ma_agent', true ) && current_user_can("wplc_ma_agent")){

      add_submenu_page('wplivechat-menu', __('History', 'wplivechat'), __('History', 'wplivechat'), $cap[2], 'wplivechat-menu-history', 'wplc_admin_history_layout');
      add_submenu_page('wplivechat-menu', __('Missed Chats', 'wplivechat'), __('Missed Chats', 'wplivechat'), $cap[3], 'wplivechat-menu-missed-chats', 'wplc_admin_missed_chats');

     
      add_submenu_page('wplivechat-menu', __('Offline Messages', 'wplivechat'), __('Offline Messages', 'wplivechat'), $cap[4], 'wplivechat-menu-offline-messages', 'wplc_admin_offline_messages');

      do_action("wplc_hook_menu_mid",$cap);


      add_submenu_page('wplivechat-menu', __('Support', 'wplivechat'), __('Support', 'wplivechat'), 'manage_options', 'wplivechat-menu-support-page', 'wplc_support_menu');
    }
    do_action("wplc_hook_menu");
}


add_action("wplc_hook_menu","wplc_hook_control_menu");
function wplc_hook_control_menu() {
  $check = apply_filters("wplc_filter_menu_api",0);
}

/**
 * Allow agent to access the menu
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
add_action("wplc_hook_menu_mid","wplc_mid_hook_control_menu",10,1);
function wplc_mid_hook_control_menu($cap) {
	add_submenu_page('wplivechat-menu', __('Reporting', 'wplivechat'), __('Reporting', 'edit_posts'), $cap[0], 'wplivechat-menu-reporting', 'wplc_reporting_page');
	add_submenu_page('wplivechat-menu', __('Triggers', 'wplivechat'), __('Triggers', 'edit_posts'), $cap[1], 'wplivechat-menu-triggers', 'wplc_triggers_page');
	add_submenu_page('wplivechat-menu', __('Custom Fields', 'wplivechat'), __('Custom Fields', 'edit_posts'), $cap[0], 'wplivechat-menu-custom-fields', 'wplc_custom_fields_page');
}


add_action("wp_head","wplc_load_user_js",0);


function wplc_load_user_js () {
    if (!is_admin()) {
        if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
             return false;
        }


        $display_contents = wplc_display_chat_contents();

        if(function_exists('wplc_is_user_banned')){
            $user_banned = wplc_is_user_banned();
        } else {
            $user_banned = 0;
        }
        $display_contents = apply_filters("wplc_filter_display_contents",$display_contents);

        if($display_contents && $user_banned == 0){

            $wplc_settings = get_option("WPLC_SETTINGS");
            if (!class_exists('Mobile_Detect')) {
                require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
            }
            $wplc_detect_device = new Mobile_Detect;
            $wplc_is_mobile = $wplc_detect_device->isMobile();

            if ($wplc_is_mobile && isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] == '0') {
                return;
            }

            wplc_push_js_to_front();
        }
    }
}

function wplc_push_js_to_front() {
    global $wplc_is_mobile;

	wp_register_script('wplc-user-jquery-cookie', plugins_url('/js/jquery-cookie.js', __FILE__), array('jquery'),false, false);
	wp_enqueue_script('wplc-user-jquery-cookie');

    wp_enqueue_script('jquery');

    wplc_register_common_node();

    $wplc_settings = get_option("WPLC_SETTINGS");
	$wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
	$wplc_ga_enabled = get_option("WPLC_GA_SETTINGS");

    if (isset($wplc_settings['wplc_display_to_loggedin_only']) && $wplc_settings['wplc_display_to_loggedin_only'] == 1) {
        /* Only show to users that are logged in */
        if (!is_user_logged_in()) {
            return;
        }
    }

    /* is the chat enabled? */
    if ($wplc_settings["wplc_settings_enabled"] == 2) { return; }

    wp_register_script('wplc-md5', plugins_url('/js/md5.js', __FILE__),array('wplc-user-script'),WPLC_PLUGIN_VERSION);
    wp_enqueue_script('wplc-md5');
    if (isset($wplc_settings['wplc_display_name']) && $wplc_settings['wplc_display_name'] == 1) {
        $wplc_display = 'display';
    } else {
        $wplc_display = 'hide';
    }


    if (isset($wplc_settings['wplc_enable_msg_sound']) && intval($wplc_settings['wplc_enable_msg_sound']) == 1) { $wplc_ding = '1'; } else { $wplc_ding = '0'; }

    $ajax_nonce = wp_create_nonce("wplc");
    $ajaxurl = admin_url('admin-ajax.php');
    $wplc_ajaxurl = $ajaxurl;


	wp_register_script('wplc-server-script', plugins_url('/js/wplc_server.js', __FILE__), array('jquery'), WPLC_PLUGIN_VERSION);
 	wp_enqueue_script('wplc-server-script');

 	wp_localize_script( 'wplc-server-script', 'wplc_datetime_format', array(
 		'date_format' => get_option( 'date_format' ),
		'time_format' => get_option( 'time_format' ),
	) );

 	if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1){
    wp_localize_script('wplc-server-script', 'wplc_use_node_server', "true");

    $wplc_node_token = get_option("wplc_node_server_secret_token");
    if(!$wplc_node_token){
	    if(function_exists("wplc_node_server_token_regenerate")){
		      wplc_node_server_token_regenerate();
		      $wplc_node_token = get_option("wplc_node_server_secret_token");
		  }
		}
    wp_localize_script('wplc-server-script', 'bleeper_api_key', $wplc_node_token);



		wp_register_script('wplc-node-server-script', WPLC_PLUGIN_URL."/js/vendor/sockets.io/socket.io.slim.js", array('jquery'), WPLC_PLUGIN_VERSION);
 		wp_enqueue_script('wplc-node-server-script');
 		wp_register_script('wplc-user-events-script', plugins_url('/js/wplc_u_node_events.js', __FILE__),array('jquery', 'wplc-server-script'),WPLC_PLUGIN_VERSION);

 		wp_localize_script('wplc-server-script', 'bleeper_override_upload_url', rest_url( 'wp_live_chat_support/v1/remote_upload' ) );

    //For node verification

    wp_localize_script('wplc-server-script', 'wplc_guid', get_option('WPLC_GUID', ''));


 		//Emoji Libs
		if(empty($wplc_settings['wplc_disable_emojis'])) {

			wp_register_script('wplc-user-js-emoji-concat', WPLC_PLUGIN_URL."/js/vendor/wdt-emoji/wdt-emoji-concat.min.js", array("wplc-server-script", "wplc-server-script"), WPLC_PLUGIN_VERSION, false);
			wp_enqueue_script('wplc-user-js-emoji-concat');

			wp_register_style( 'wplc-admin-style-emoji', WPLC_PLUGIN_URL."/js/vendor/wdt-emoji/wdt-emoji-bundle.css", false, WPLC_PLUGIN_VERSION );
			wp_enqueue_style( 'wplc-admin-style-emoji' );
		}

    wp_register_script('wplc-user-node-node-primary', plugins_url('/js/wplc_node.js', __FILE__),array('jquery', 'wplc-server-script', 'wplc-user-script'), WPLC_PLUGIN_VERSION);
    wp_enqueue_script('wplc-user-node-node-primary');

  } else {
  	/* not using the node server, load traditional event handler JS */
  	wp_register_script('wplc-user-events-script', plugins_url('/js/wplc_u_events.js', __FILE__),array('jquery', 'wplc-server-script'),WPLC_PLUGIN_VERSION);
  }

  wp_register_script('wplc-user-script', plugins_url('/js/wplc_u.js', __FILE__),array('jquery', 'wplc-server-script'),WPLC_PLUGIN_VERSION);

  wp_enqueue_script('wplc-user-script');
  wp_enqueue_script('wplc-user-events-script');

  if (isset($wplc_settings['wplc_newtheme'])) { $wplc_newtheme = $wplc_settings['wplc_newtheme']; } else { $wplc_newtheme = "theme-2"; }
  
  if (isset($wplc_newtheme)) {
    if($wplc_newtheme == 'theme-1') {
      wp_register_script('wplc-theme-classic', plugins_url('/js/themes/classic.js', __FILE__),array('wplc-user-script'),WPLC_PLUGIN_VERSION);
      wp_enqueue_script('wplc-theme-classic');
      $avatars = wplc_all_avatars();
      wp_localize_script('wplc-theme-classic', 'wplc_user_avatars', $avatars);

    } else if($wplc_newtheme == 'theme-2') {
      wp_register_script('wplc-theme-modern', plugins_url('/js/themes/modern.js', __FILE__),array('wplc-user-script'),WPLC_PLUGIN_VERSION);
      wp_enqueue_script('wplc-theme-modern');
      $avatars = wplc_all_avatars();
      wp_localize_script('wplc-theme-modern', 'wplc_user_avatars', $avatars);
    }
  } else {
      wp_register_script('wplc-theme-classic', plugins_url('/js/themes/classic.js', __FILE__),array('wplc-user-script'),WPLC_PLUGIN_VERSION);
      wp_enqueue_script('wplc-theme-classic');
      $avatars = wplc_all_avatars();
      wp_localize_script('wplc-theme-classic', 'wplc_user_avatars', $avatars);
  }

  $ajax_url = admin_url('admin-ajax.php');
  $home_ajax_url = $ajax_url;

  $wplc_ajax_url = apply_filters("wplc_filter_ajax_url",$ajax_url);
  wp_localize_script('wplc-admin-chat-js', 'wplc_ajaxurl', $wplc_ajax_url);
  wp_localize_script('wplc-ma-js', 'wplc_home_ajaxurl', $home_ajax_url);

  //Added rest security nonces
  if(class_exists("WP_REST_Request")) {
      wp_localize_script('wplc-user-script', 'wplc_restapi_enabled', '1');
      wp_localize_script('wplc-user-script', 'wplc_restapi_token', get_option('wplc_api_secret_token'));
      wp_localize_script('wplc-user-script', 'wplc_restapi_endpoint', rest_url('wp_live_chat_support/v1'));
      wp_localize_script('wplc-user-script', 'wplc_restapi_nonce', wp_create_nonce( 'wp_rest' ));
  } else {
      wp_localize_script('wplc-user-script', 'wplc_restapi_enabled', '0');
      wp_localize_script('wplc-user-script', 'wplc_restapi_nonce', "false");
  }


  if (isset($wplc_ga_enabled['wplc_enable_ga']) && $wplc_ga_enabled['wplc_enable_ga'] == '1') {
  	  wp_localize_script('wplc-user-script', 'wplc_enable_ga', '1');
  }

	$wplc_ding_file = apply_filters( 'wplc_filter_message_sound', '' );
  if ( ! empty( $wplc_ding_file ) ) {
	 wp_localize_script( 'wplc-user-script', 'bleeper_message_override', $wplc_ding_file );
  }

  $wplc_detect_device = new Mobile_Detect;
  $wplc_is_mobile = $wplc_detect_device->isMobile() ? 'true' : 'false';
  wp_localize_script('wplc-user-script', 'wplc_is_mobile', $wplc_is_mobile);


  wp_localize_script('wplc-user-script', 'wplc_ajaxurl', $wplc_ajax_url);
  wp_localize_script('wplc-user-script', 'wplc_ajaxurl_site', admin_url('admin-ajax.php'));
  wp_localize_script('wplc-user-script', 'wplc_nonce', $ajax_nonce);
  wp_localize_script('wplc-user-script', 'wplc_plugin_url', WPLC_PLUGIN_URL);

  $wplc_display = false;

  $wplc_images = apply_filters( 'wplc_get_images_to_preload', array(), $wplc_acbc_data );
  wp_localize_script( 'wplc-user-script', 'wplc_preload_images', $wplc_images );




	if( isset($wplc_settings['wplc_show_name']) && $wplc_settings['wplc_show_name'] == '1' ){ $wplc_show_name = true; } else { $wplc_show_name = false; }
  if( isset($wplc_settings['wplc_show_avatar']) && $wplc_settings['wplc_show_avatar'] ){ $wplc_show_avatar = true; } else { $wplc_show_avatar = false; }
	if( isset($wplc_settings['wplc_show_date']) && $wplc_settings['wplc_show_date'] == '1' ){ $wplc_show_date = true; } else { $wplc_show_date = false; }
	if( isset($wplc_settings['wplc_show_time']) && $wplc_settings['wplc_show_time'] == '1' ){ $wplc_show_time = true; } else { $wplc_show_time = false; }

 	$wplc_chat_detail = array( 'name' => $wplc_show_name, 'avatar' => $wplc_show_avatar, 'date' => $wplc_show_date, 'time' => $wplc_show_time );



	if( $wplc_display !== FALSE && $wplc_display !== 'hide'  ){
		wp_localize_script('wplc-user-script', 'wplc_display_name', $wplc_display);
	} else {
		wp_localize_script( 'wplc-user-script', 'wplc_show_chat_detail', $wplc_chat_detail );
	}



    /**
     * Create a JS object for all Agent ID's and Gravatar MD5's
     */
    $user_array = get_users(array(
        'meta_key' => 'wplc_ma_agent',
    ));

    $a_array = array();
    if ($user_array) {
        foreach ($user_array as $user) {
        	$a_array[$user->ID] = array();
        	$a_array[$user->ID]['name'] = apply_filters( "wplc_decide_agents_name", $user->display_name, $wplc_acbc_data );
        	$a_array[$user->ID]['md5'] = md5( $user->user_email );
        }
    }
	wp_localize_script('wplc-user-script', 'wplc_agent_data', $a_array);


	$wplc_error_messages = array(
      'valid_name'     => __( "Please enter your name", "wplivechat" ),
      'valid_email'     => __( "Please enter your email address", "wplivechat" ),
      'server_connection_lost' => __("Connection to server lost. Please reload this page. Error: ", "wplivechat"),
      'chat_ended_by_operator' => ( empty( $wplc_settings['wplc_text_chat_ended'] ) ) ? __("The chat has been ended by the operator.", "wplivechat") : sanitize_text_field( $wplc_settings['wplc_text_chat_ended'] ) ,
      'empty_message' => __( "Please enter a message", "wplivechat" ),
      'disconnected_message' => __("Disconnected, attempting to reconnect...", "wplivechat"),
  );

  $wplc_error_messages = apply_filters( "wplc_user_error_messages_filter", $wplc_error_messages );

  wp_localize_script('wplc-user-script', 'wplc_error_messages', $wplc_error_messages);
  wp_localize_script('wplc-user-script', 'wplc_enable_ding', $wplc_ding);
  $wplc_run_override = "0";
  $wplc_run_override = apply_filters("wplc_filter_run_override",$wplc_run_override);
  wp_localize_script('wplc-user-script', 'wplc_filter_run_override', $wplc_run_override);

  if (!isset($wplc_settings['wplc_pro_offline1'])) { $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat"); }
  if (!isset($wplc_settings['wplc_pro_offline2'])) { $wplc_settings["wplc_pro_offline2"] =  __("Sending message...", "wplivechat"); }
  if (!isset($wplc_settings['wplc_pro_offline3'])) { $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat"); }


  wp_localize_script('wplc-user-script', 'wplc_offline_msg', __(stripslashes($wplc_settings['wplc_pro_offline2']), 'wplivechat'));
  wp_localize_script('wplc-user-script', 'wplc_offline_msg3',__(stripslashes($wplc_settings['wplc_pro_offline3']), 'wplivechat'));
  wp_localize_script('wplc-user-script', 'wplc_welcome_msg', __(stripslashes($wplc_settings['wplc_welcome_msg']), 'wplivechat'));
 	wp_localize_script('wplc-user-script', 'wplc_pro_sst1', __(stripslashes($wplc_settings['wplc_pro_sst1']), 'wplivechat') );
 	wp_localize_script('wplc-user-script', 'wplc_pro_offline_btn_send', __(stripslashes($wplc_settings['wplc_pro_offline_btn_send']), 'wplivechat') );
 	wp_localize_script('wplc-user-script', 'wplc_user_default_visitor_name', __(stripslashes($wplc_settings['wplc_user_default_visitor_name']), 'wplivechat') );

  if( isset( $wplc_acbc_data['wplc_use_wp_name'] ) && $wplc_acbc_data['wplc_use_wp_name'] == '1' ){
  	if( isset( $_COOKIE['wplc_cid'] ) ){
  		$chat_data = wplc_get_chat_data( $_COOKIE['wplc_cid'] );
        if ( isset($chat_data->agent_id ) ) {
	        $user_info = get_userdata( intval( $chat_data->agent_id ) );
        	if( $user_info ){
	        	$agent = $user_info->display_name;
			} else {
	        	$agent = "agent";
	        }
	    } else {
	    	$agent = 'agent';
	    }
  	} else {
  		$agent = 'agent';
  	}

  } else {
      if (!empty($wplc_acbc_data['wplc_chat_name'])) {
          $agent = $wplc_acbc_data['wplc_chat_name'];
      } else {
          $agent = 'agent';
      }
  }
  wp_localize_script('wplc-user-script', 'wplc_localized_string_is_typing', $agent . __(" is typing...","wplivechat"));
  wp_localize_script('wplc-user-script', 'wplc_localized_string_is_typing_single', __(" is typing...","wplivechat"));

  $bleeper_string_array = array(
  	__(" has joined.","wplivechat"),
  	__(" has left.","wplivechat"),
  	__(" has ended the chat.", "wplivechat"),
  	__(" has disconnected.", "wplivechat"),
  	__("(edited)", "wplivechat"),
  	__("Type here","wplivechat")
  );

	wp_localize_script('wplc-user-script', 'bleeper_localized_strings', $bleeper_string_array );

  if( isset( $wplc_settings['wplc_elem_trigger_id'] ) && trim( $wplc_settings['wplc_elem_trigger_id'] ) !== "" ) {
  	if( isset( $wplc_settings['wplc_elem_trigger_action'] ) ){
  		wp_localize_script( 'wplc-user-script', 'wplc_elem_trigger_action', stripslashes( $wplc_settings['wplc_elem_trigger_action'] ) );
  	}
  	if( isset( $wplc_settings['wplc_elem_trigger_type'] ) ){
  		wp_localize_script( 'wplc-user-script', 'wplc_elem_trigger_type', stripslashes( $wplc_settings['wplc_elem_trigger_type'] ) );
  	}
  	wp_localize_script( 'wplc-user-script', 'wplc_elem_trigger_id', stripslashes( $wplc_settings['wplc_elem_trigger_id'] ) );
  }

  $extra_data_array = array("object_switch" => true);
  $extra_data_array = apply_filters("wplc_filter_front_js_extra_data",$extra_data_array);
  wp_localize_script('wplc-user-script', 'wplc_extra_data',$extra_data_array);


  if (isset($_COOKIE['wplc_email']) && $_COOKIE['wplc_email'] != "") { $wplc_user_gravatar = sanitize_text_field(md5(strtolower(trim($_COOKIE['wplc_email'])))); } else {$wplc_user_gravatar = ""; }

  if ($wplc_user_gravatar != "") { $wplc_grav_image = "<img src='//www.gravatar.com/avatar/$wplc_user_gravatar?s=30&d=mm' class='wplc-user-message-avatar' />";} else { $wplc_grav_image = "";}

	if ( ! empty( $wplc_grav_image ) ) {
		wp_localize_script('wplc-user-script', 'wplc_gravatar_image', $wplc_grav_image);
  }

  $wplc_hide_chat = "";
  if (get_option('WPLC_HIDE_CHAT') == TRUE) { $wplc_hide_chat = "yes"; } else { $wplc_hide_chat = null; }
  wp_localize_script('wplc-user-script', 'wplc_hide_chat', $wplc_hide_chat);

  if(isset($wplc_settings['wplc_redirect_to_thank_you_page']) && isset($wplc_settings['wplc_redirect_thank_you_url']) && $wplc_settings['wplc_redirect_thank_you_url'] !== "" && $wplc_settings['wplc_redirect_thank_you_url'] !== " "){
    	wp_localize_script('wplc-user-script', 'wplc_redirect_thank_you', urldecode($wplc_settings['wplc_redirect_thank_you_url']));
  }

  wp_enqueue_script('jquery-ui-core',false,array('wplc-user-script'),false,false);
  wp_enqueue_script('jquery-ui-draggable',false,array('wplc-user-script'),false,false);

  do_action("wplc_hook_push_js_to_front");

}

add_action('wp_head', 'wplc_user_top_js');




/**
 * Add to the array to determine which images need to be preloaded via JS on the front end.
 *
 * @param  array $images Array of images to be preloaded
 * @return array
 */
add_filter( "wplc_get_images_to_preload", "wplc_filter_control_get_images_to_preload", 10, 2 );
function wplc_filter_control_get_images_to_preload( $images, $wplc_acbc_data ) {
	$icon = plugins_url('images/iconRetina.png', __FILE__);
	$close_icon = plugins_url('images/iconCloseRetina.png', __FILE__);
	array_push( $images, $icon );
	array_push( $images, $close_icon );
	return $images;
}



function wplc_user_top_js() {

    $display_contents = wplc_display_chat_contents();

    if($display_contents >= 1){

        $ajax_nonce = wp_create_nonce("wplc");
        $wplc_settings = get_option("WPLC_SETTINGS");
        $ajax_url = admin_url('admin-ajax.php');
        $wplc_ajax_url = apply_filters("wplc_filter_ajax_url",$ajax_url);
        ?>

        <script type="text/javascript">
            var wplc_ajaxurl = '<?php echo $wplc_ajax_url; ?>';
            var wplc_nonce = '<?php echo $ajax_nonce; ?>';
        </script>




        <?php

        $wplc_settings = get_option('WPLC_SETTINGS');
        if (isset($wplc_settings['wplc_theme'])) { $wplc_theme = $wplc_settings['wplc_theme']; } else { $wplc_theme = "theme-default"; }
        if (isset($wplc_theme)) {

          if($wplc_theme == 'theme-6') {
            /* custom */

            if (isset($wplc_settings["wplc_settings_color1"])) { $wplc_settings_color1 = sanitize_text_field($wplc_settings["wplc_settings_color1"]); } else { $wplc_settings_color1 = "ED832F"; }
            if (isset($wplc_settings["wplc_settings_color2"])) { $wplc_settings_color2 = sanitize_text_field($wplc_settings["wplc_settings_color2"]); } else { $wplc_settings_color2 = "FFFFFF"; }
            if (isset($wplc_settings["wplc_settings_color3"])) { $wplc_settings_color3 = sanitize_text_field($wplc_settings["wplc_settings_color3"]); } else { $wplc_settings_color3 = "EEEEEE"; }
            if (isset($wplc_settings["wplc_settings_color4"])) { $wplc_settings_color4 = sanitize_text_field($wplc_settings["wplc_settings_color4"]); } else { $wplc_settings_color4 = "666666"; }


            ?>
            <style>
              .wplc-color-1 { color: #<?php echo $wplc_settings_color1; ?> !important; }
              .wplc-color-2 { color: #<?php echo $wplc_settings_color2; ?> !important; }
              .wplc-color-3 { color: #<?php echo $wplc_settings_color3; ?> !important; }
              .wplc-color-4 { color: #<?php echo $wplc_settings_color4; ?> !important; }
              .wplc-color-bg-1 { background-color: #<?php echo $wplc_settings_color1; ?> !important; }
              .wplc-color-bg-2 { background-color: #<?php echo $wplc_settings_color2; ?> !important; }
              .wplc-color-bg-3 { background-color: #<?php echo $wplc_settings_color3; ?> !important; }
              .wplc-color-bg-4 { background-color: #<?php echo $wplc_settings_color4; ?> !important; }
              .wplc-color-border-1 { border-color: #<?php echo $wplc_settings_color1; ?> !important; }
              .wplc-color-border-2 { border-color: #<?php echo $wplc_settings_color2; ?> !important; }
              .wplc-color-border-3 { border-color: #<?php echo $wplc_settings_color3; ?> !important; }
              .wplc-color-border-4 { border-color: #<?php echo $wplc_settings_color4; ?> !important; }
              .wplc-color-border-1:before { border-color: transparent #<?php echo $wplc_settings_color1; ?> !important; }
              .wplc-color-border-2:before { border-color: transparent #<?php echo $wplc_settings_color2; ?> !important; }
              .wplc-color-border-3:before { border-color: transparent #<?php echo $wplc_settings_color3; ?> !important; }
              .wplc-color-border-4:before { border-color: transparent #<?php echo $wplc_settings_color4; ?> !important; }
            </style>

            <?php


          }
        }


    }
}




/**
 * Detect if the user is using blocked in the live chat settings 'blocked IP' section
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_hook_control_banned_users() {
    if (function_exists('wplc_is_user_banned')){
        $user_banned = wplc_is_user_banned();
    } else {
        $user_banned = 0;
    }
    if ($user_banned) {
      remove_action("wplc_hook_output_box_body","wplc_hook_control_show_chat_box");
    }
}

/**
 * Detect if the user is using a mobile phone or not and decides to show the chat box depending on the admins settings
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_hook_control_check_mobile() {
  $wplc_settings = get_option("WPLC_SETTINGS");

  $draw_box = false;

  if (!class_exists('Mobile_Detect')) {
      require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
  }

  $wplc_detect_device = new Mobile_Detect;
  $wplc_is_mobile = $wplc_detect_device->isMobile();

  if ($wplc_is_mobile && !isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] != 1) {
      return "";
  }

  if (function_exists('wplc_hide_chat_when_offline')) {
      $wplc_hide_chat = wplc_hide_chat_when_offline();
      if (!$wplc_hide_chat) {
          $draw_box = true;
      }
  } else {
      $draw_box = true;
  }
  if (!$draw_box) {
      remove_action("wplc_hook_output_box_body","wplc_hook_control_show_chat_box");
  }

}


/**
 * Decides whether or not to show the chat box based on the main setting in the settings page
 * @return void
 * @since  6.0.00
 */
function wplc_hook_control_is_chat_enabled() {
  $wplc_settings = get_option("WPLC_SETTINGS");
  if ($wplc_settings["wplc_settings_enabled"] == 2) {
      remove_action("wplc_hook_output_box_body","wplc_hook_control_show_chat_box");
  }
}

/**
 * Backwards compatibility for the control of the chat box
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_hook_control_show_chat_box($cid) {
      echo wplc_output_box_ajax($cid);
}

/* basic */
add_action("wplc_hook_output_box_header","wplc_hook_control_banned_users");
add_action("wplc_hook_output_box_header","wplc_hook_control_check_mobile");
add_action("wplc_hook_output_box_header","wplc_hook_control_is_chat_enabled");

add_action("wplc_hook_output_box_body","wplc_hook_control_show_chat_box",10,1);

/**
 * Build the chat box
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_output_box_5100($cid = null) {
   wplc_string_check();
   do_action("wplc_hook_output_box_header",$cid);
   do_action("wplc_hook_output_box_body",$cid);
   do_action("wplc_hook_output_box_footer",$cid);
}



/**
 * Filter to control the top MAIN DIV of the chat box
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings,$logged_in,$wplc_using_locale) {
    $ret_msg = "";
   $wplc_class = "";

    if ($wplc_settings["wplc_settings_align"] == 1) {
        $original_pos = "bottom_left";
    } else if ($wplc_settings["wplc_settings_align"] == 2) {
        $original_pos = "bottom_right";
    } else if ($wplc_settings["wplc_settings_align"] == 3) {
        $original_pos = "left";
        $wplc_class = "wplc_left";
    } else if ($wplc_settings["wplc_settings_align"] == 4) {
        $original_pos = "right";
        $wplc_class = "wplc_right";
    }


    $animations = wplc_return_animations();
    if ($animations) {
      isset($animations['animation']) ? $wplc_animation = $animations['animation'] : $wplc_animation = 'animation-4';
      isset($animations['starting_point']) ? $wplc_starting_point = $animations['starting_point'] : $wplc_starting_point = 'display: none;';
      isset($animations['box_align']) ? $wplc_box_align = $animations['box_align'] : $wplc_box_align = '';
    }
    else {

      if ($wplc_settings["wplc_settings_align"] == 1) {
          $original_pos = "bottom_left";
          $wplc_box_align = "left:20px; bottom:0px;";
      } else if ($wplc_settings["wplc_settings_align"] == 2) {
          $original_pos = "bottom_right";
          $wplc_box_align = "right:20px; bottom:0px;";
      } else if ($wplc_settings["wplc_settings_align"] == 3) {
          $original_pos = "left";
          $wplc_box_align = "left:0; bottom:100px;";
          $wplc_class = "wplc_left";
      } else if ($wplc_settings["wplc_settings_align"] == 4) {
          $original_pos = "right";
          $wplc_box_align = "right:0; bottom:100px;";
          $wplc_class = "wplc_right";
      }
    }


  $wplc_extra_attr = apply_filters("wplc_filter_chat_header_extra_attr","");
  if (isset($wplc_settings['wplc_newtheme'])) { $wplc_newtheme = $wplc_settings['wplc_newtheme']; } else { $wplc_newtheme = "theme-2"; }
  if (isset($wplc_newtheme)) {
    if($wplc_newtheme == 'theme-1') { $wplc_theme_type = "classic"; }
    else if($wplc_newtheme == 'theme-2') { $wplc_theme_type = "modern"; }
    else { $wplc_theme_type = "modern"; }
  }

  if(!isset($wplc_settings['wplc_newtheme'])){ $wplc_settings['wplc_newtheme'] = "theme-2"; }
  if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
  	$hovercard_content = "<div class='wplc_hovercard_content_left'>".apply_filters("wplc_filter_modern_theme_hovercard_content_left","")."</div><div class='wplc_hovercard_content_right'>".apply_filters("wplc_filter_live_chat_box_html_1st_layer",wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings,$logged_in,$wplc_using_locale,'wplc-color-4'))."</div>";
  	$hovercard_content = apply_filters("wplc_filter_hovercard_content", $hovercard_content);

    $ret_msg .= "<div id='wplc_hovercard' style='display:none' class='".$wplc_theme_type."'>";
    $ret_msg .= "<div id='wplc_hovercard_content'>".apply_filters("wplc_filter_live_chat_box_pre_layer1","").$hovercard_content."</div>";
    $ret_msg .= "<div id='wplc_hovercard_bottom'>".apply_filters("wplc_filter_hovercard_bottom_before","").apply_filters("wplc_filter_live_chat_box_hover_html_start_chat_button","",$wplc_settings,$logged_in,$wplc_using_locale)."</div>";
    $ret_msg .= "</div>";

  } else if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-1"){
  	$hovercard_content = "<div class='wplc_hovercard_content_right'>".apply_filters("wplc_filter_live_chat_box_html_1st_layer",wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings,$logged_in,$wplc_using_locale, "wplc-color-2"))."</div>";
  	$hovercard_content = apply_filters("wplc_filter_hovercard_content", $hovercard_content);

    $ret_msg .= "<div id='wplc_hovercard' style='display:none' class='".$wplc_theme_type."'>";
    $ret_msg .= "<div id='wplc_hovercard_content'>".apply_filters("wplc_filter_live_chat_box_pre_layer1","").$hovercard_content."</div>";
    $ret_msg .= "<div id='wplc_hovercard_bottom'>".apply_filters("wplc_filter_hovercard_bottom_before","").apply_filters("wplc_filter_live_chat_box_hover_html_start_chat_button","",$wplc_settings,$logged_in,$wplc_using_locale)."</div>";
    $ret_msg .= "</div>";
  }

  $ret_msg .= "<div id=\"wp-live-chat\" wplc_animation=\"".$wplc_animation."\" style=\"".$wplc_starting_point." ".$wplc_box_align.";\" class=\"".$wplc_theme_type." ".$wplc_class." wplc_close\" original_pos=\"".$original_pos."\" ".$wplc_extra_attr." > ";
  return $ret_msg;
}



add_filter("wplc_filter_modern_theme_hovercard_content_left","wplc_filter_control_modern_theme_hovercard_content_left",10,1);
function wplc_filter_control_modern_theme_hovercard_content_left($msg) {

  $msg .= "<div class='wplc_left_logo' style='background:url(".plugins_url('images/iconmicro.png', __FILE__).") no-repeat; background-size: cover;'></div>";
  $msg = apply_filters("wplc_filter_microicon",$msg);
  return $msg;
}

/**
 * Filter to control the top HEADER DIV of the chat box
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings) {

  $ret_msg = "";

  $current_theme = isset($wplc_settings['wplc_newtheme']) ? $wplc_settings['wplc_newtheme'] : "theme-2";
  if($current_theme === "theme-1"){
  	$ret_msg .= apply_filters("wplc_filter_chat_header_above","", $wplc_settings); //Ratings/Social Icon Filter
  }

  $ret_msg .= "<div id=\"wp-live-chat-header\" class='wplc-color-bg-1 wplc-color-2'>";
  $ret_msg .= apply_filters("wplc_filter_chat_header_under","",$wplc_settings);
  return $ret_msg;
}


add_filter("wplc_filter_chat_header_under","wplc_filter_control_chat_header_under",1,2);
function wplc_filter_control_chat_header_under($ret_msg,$wplc_settings) {
	$current_theme = isset($wplc_settings['wplc_newtheme']) ? $wplc_settings['wplc_newtheme'] : "theme-2";
	if($current_theme === "theme-2"){

		if (function_exists("wplc_acbc_filter_control_chat_header_under")) {
		  remove_filter("wplc_filter_chat_header_under","wplc_acbc_filter_control_chat_header_under");
		}
	}

	return $ret_msg;

}



/**
 * Filter to control the user details section - custom fields coming soon
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_ask_user_detail($wplc_settings) {
  $ret_msg = "";
  if (isset($wplc_settings['wplc_loggedin_user_info']) && $wplc_settings['wplc_loggedin_user_info'] == 1) {
      $wplc_use_loggedin_user_details = 1;
  } else {
      $wplc_use_loggedin_user_details = 0;
  }

	if (isset($wplc_settings['wplc_require_user_info']) && ( $wplc_settings['wplc_require_user_info'] == 1 || $wplc_settings['wplc_require_user_info'] == 'name' )) {
		$wplc_ask_user_details = 1;
	} else {
		$wplc_ask_user_details = 0;
	}

  $wplc_loggedin_user_name = "";
  $wplc_loggedin_user_email = "";

  if ($wplc_use_loggedin_user_details == 1 && is_user_logged_in()) {
      global $current_user;

      if ($current_user->data != null) {
          //Logged in. Get name and email
          $wplc_loggedin_user_name = $current_user->user_nicename;
          $wplc_loggedin_user_email = $current_user->user_email;
      }
  } else {
	  if ( $wplc_ask_user_details == 0 ) {
		  $wplc_loggedin_user_name = stripslashes( $wplc_settings['wplc_user_default_visitor_name'] );
	  }
  }

  if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 1) {
	  //Ask the user to enter name and email

	  $ret_msg .= "<input type=\"text\" name=\"wplc_name\" id=\"wplc_name\" value='" . $wplc_loggedin_user_name . "' placeholder=\"" . __( "Name", "wplivechat" ) . "\" />";
	  $ret_msg .= "<input type=\"text\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"0\" value=\"" . $wplc_loggedin_user_email . "\" placeholder=\"" . __( "Email", "wplivechat" ) . "\"  />";
	  $ret_msg .= apply_filters( "wplc_start_chat_user_form_after_filter", "" );

  } elseif (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'email') {

	  $wplc_random_user_number = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
	  if ($wplc_loggedin_user_name != '') { $wplc_lin = $wplc_loggedin_user_name; } else {  $wplc_lin = 'user' . $wplc_random_user_number; }
	  $ret_msg .= "<input type=\"hidden\" name=\"wplc_name\" id=\"wplc_name\" value=\"".$wplc_lin."\" />";
	  $ret_msg .= "<input type=\"text\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"0\" value=\"" . $wplc_loggedin_user_email . "\" placeholder=\"" . __( "Email", "wplivechat" ) . "\"  />";
	  $ret_msg .= apply_filters("wplc_start_chat_user_form_after_filter", "");
  } elseif (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'name') {

	  $wplc_random_user_number = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
	  if ($wplc_loggedin_user_email != '' && $wplc_loggedin_user_email != null) { $wplc_lie = $wplc_loggedin_user_email; } else { $wplc_lie = $wplc_random_user_number . '@' . $wplc_random_user_number . '.com'; }
	  $ret_msg .= "<input type=\"text\" name=\"wplc_name\" id=\"wplc_name\" value='" . $wplc_loggedin_user_name . "' placeholder=\"" . __( "Name", "wplivechat" ) . "\" />";
	  $ret_msg .= "<input type=\"hidden\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"1\" value=\"".$wplc_lie."\" />";
	  $ret_msg .= apply_filters("wplc_start_chat_user_form_after_filter", "");
  } else {
      //Dont ask the user

      $ret_msg .= "<div style=\"padding: 7px; text-align: center;\">";
      if (isset($wplc_settings['wplc_user_alternative_text'])) {
          $ret_msg .= html_entity_decode( stripslashes($wplc_settings['wplc_user_alternative_text']) );
      }
      $ret_msg .= '</div>';

      $wplc_random_user_number = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
      if ($wplc_loggedin_user_name != '') { $wplc_lin = $wplc_loggedin_user_name; } else {  $wplc_lin = 'user' . $wplc_random_user_number; }
      if ($wplc_loggedin_user_email != '' && $wplc_loggedin_user_email != null) { $wplc_lie = $wplc_loggedin_user_email; } else { $wplc_lie = $wplc_random_user_number . '@' . $wplc_random_user_number . '.com'; }
      $ret_msg .= "<input type=\"hidden\" name=\"wplc_name\" id=\"wplc_name\" value=\"".$wplc_lin."\" />";
      $ret_msg .= "<input type=\"hidden\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"1\" value=\"".$wplc_lie."\" />";
      $ret_msg .= apply_filters("wplc_start_chat_user_form_after_filter", "");
  }
  return $ret_msg;
}


/**
 * Filter to control the start chat button
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_start_chat_button($wplc_settings,$wplc_using_locale ) {
    $wplc_sst_1 = __('Start chat', 'wplivechat');
    if (!isset($wplc_settings['wplc_pro_sst1']) || $wplc_settings['wplc_pro_sst1'] == "") { $wplc_settings['wplc_pro_sst1'] = $wplc_sst_1; }
    $text = ($wplc_using_locale ? $wplc_sst_1 : stripslashes($wplc_settings['wplc_pro_sst1']));
    $custom_attr = apply_filters('wplc_start_button_custom_attributes_filter', "", $wplc_settings);
  	return "<button id=\"wplc_start_chat_btn\" type=\"button\" class='wplc-color-bg-1 wplc-color-2' $custom_attr>$text</button>";
}




/**
 * Filter to control the hover card start chat button
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.1.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
add_filter("wplc_filter_live_chat_box_hover_html_start_chat_button","wplc_filter_control_live_chat_box_html_hovercard_chat_button",10,4);
function wplc_filter_control_live_chat_box_html_hovercard_chat_button($content,$wplc_settings,$logged_in,$wplc_using_locale ) {
    if ($logged_in) {
      $wplc_sst_1 = __('Start chat', 'wplivechat');

      if (!isset($wplc_settings['wplc_pro_sst1']) || $wplc_settings['wplc_pro_sst1'] == "") { $wplc_settings['wplc_pro_sst1'] = $wplc_sst_1; }
      $text = ($wplc_using_locale ? $wplc_sst_1 : stripslashes($wplc_settings['wplc_pro_sst1']));
      return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$text</button>";
    } else {
      $wplc_sst_1 = stripslashes($wplc_settings['wplc_pro_offline_btn']);
      return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$wplc_sst_1</button>";

    }
}

/**
 * Filter to control the offline message button
 * @param  array   $wplc_settings Live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_send_offline_message_button($wplc_settings) {
$wplc_settings = get_option('WPLC_SETTINGS');
if (isset($wplc_settings['wplc_theme'])) { $wplc_theme = $wplc_settings['wplc_theme']; } else { }

  if (isset($wplc_theme)) {
    if($wplc_theme == 'theme-1') {
        $wplc_settings_fill = "#DB0000";
        $wplc_settings_font = "#FFFFFF";
    } else if ($wplc_theme == 'theme-2'){
        $wplc_settings_fill = "#000000";
        $wplc_settings_font = "#FFFFFF";
    } else if ($wplc_theme == 'theme-3'){
        $wplc_settings_fill = "#DB30B3";
        $wplc_settings_font = "#FFFFFF";
    } else if ($wplc_theme == 'theme-4'){
        $wplc_settings_fill = "#1A14DB";
        $wplc_settings_font = "#F7FF0F";
    } else if ($wplc_theme == 'theme-5'){
        $wplc_settings_fill = "#3DCC13";
        $wplc_settings_font = "#FF0808";
    } else if ($wplc_theme == 'theme-6'){
        if ($wplc_settings["wplc_settings_fill"]) {
            $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
        } else {
            $wplc_settings_fill = "#ec832d";
        }
        if ($wplc_settings["wplc_settings_font"]) {
            $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
        } else {
            $wplc_settings_font = "#FFFFFF";
        }
    } else {
        if ($wplc_settings["wplc_settings_fill"]) {
            $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
        } else {
            $wplc_settings_fill = "#ec832d";
        }
        if ($wplc_settings["wplc_settings_font"]) {
            $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
        } else {
            $wplc_settings_font = "#FFFFFF";
        }
    }
    } else {
    if ($wplc_settings["wplc_settings_fill"]) {
        $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
    } else {
        $wplc_settings_fill = "#ec832d";
    }
    if ($wplc_settings["wplc_settings_font"]) {
        $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
    } else {
        $wplc_settings_font = "#FFFFFF";
    }
  }
  $custom_attr = apply_filters('wplc_offline_message_button_custom_attributes_filter', "", $wplc_settings);
  $ret_msg = "<input id=\"wplc_na_msg_btn\" type=\"button\" value=\"".stripslashes($wplc_settings['wplc_pro_offline_btn_send'])."\" style=\"background: ".$wplc_settings_fill." !important; background-color: ".$wplc_settings_fill." !important; color: ".$wplc_settings_font." !important;\" $custom_attr/>";
  return $ret_msg;
}



/**
 * Filter to control the 2nd layer of the chat window (online/offline)
 * @param  array   $wplc_settings Live chat settings array
 * @param  bool    $logged_in     Is the user logged in or not
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings,$logged_in,$wplc_using_locale, $cid) {

    if ($logged_in) {
      $wplc_intro = __('Hello. Please input your details so that I may help you.', 'wplivechat');
      if (!isset($wplc_settings['wplc_pro_intro']) || $wplc_settings['wplc_pro_intro'] == "") { $wplc_settings['wplc_pro_intro'] = $wplc_intro; }
      $text = ($wplc_using_locale ? $wplc_intro : stripslashes($wplc_settings['wplc_pro_intro']));

      $ret_msg = "<div id=\"wp-live-chat-2-inner\">";
      $ret_msg .= " <div id=\"wp-live-chat-2-info\" class='wplc-color-4'>";
      $ret_msg .= apply_filters("wplc_filter_intro_text_heading", $text, $wplc_settings);
      $ret_msg .= " </div>";
      $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_ask_user_details",wplc_filter_control_live_chat_box_html_ask_user_detail($wplc_settings));
      $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_start_chat_button",wplc_filter_control_live_chat_box_html_start_chat_button($wplc_settings,$wplc_using_locale ), $cid);
      $ret_msg .= "</div>";
    } else {
	    if ( isset( $wplc_settings['wplc_loggedin_user_info'] ) && $wplc_settings['wplc_loggedin_user_info'] == 1 ) {
		    $wplc_use_loggedin_user_details = 1;
	    } else {
		    $wplc_use_loggedin_user_details = 0;
	    }

	    $wplc_loggedin_user_name  = '';
	    $wplc_loggedin_user_email = '';

	    if ( $wplc_use_loggedin_user_details == 1 ) {
		    global $current_user;

		    if ( $current_user->data != null ) {
			    if ( is_user_logged_in() ) {
				    //Logged in. Get name and email
				    $wplc_loggedin_user_name = $current_user->user_nicename;
				    $wplc_loggedin_user_email = $current_user->user_email;
			    } else {
				    $wplc_loggedin_user_name = stripslashes( $wplc_settings['wplc_user_default_visitor_name'] );
			    }
		    }
	    } else {
		    $wplc_loggedin_user_name  = '';
		    $wplc_loggedin_user_email = '';
	    }

      /* admin not logged in, show offline messages */
      $wplc_offline = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat");
      $text = ($wplc_using_locale ? $wplc_offline : stripslashes($wplc_settings['wplc_pro_offline1']));

      $ret_msg = "<div id=\"wp-live-chat-2-info\" class=\"wplc-color-bg-1 wplc-color-2\">";
      $ret_msg .= $text;
      $ret_msg .= "</div>";
      $ret_msg .= "<div id=\"wplc_message_div\">";
      $ret_msg .= "<input type=\"text\" name=\"wplc_name\" id=\"wplc_name\" value=\"$wplc_loggedin_user_name\" placeholder=\"".__("Name", "wplivechat")."\" />";
      $ret_msg .= "<input type=\"text\" name=\"wplc_email\" id=\"wplc_email\" value=\"$wplc_loggedin_user_email\"  placeholder=\"".__("Email", "wplivechat")."\" />";
      $ret_msg .= "<textarea name=\"wplc_message\" id=\"wplc_message\" placeholder=\"".__("Message", "wplivechat")."\"></textarea>";

      if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
          $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
          $ip_address = $_SERVER['REMOTE_ADDR'];
      }

 
      $offline_ip_address = "";

      $ret_msg .= "<input type=\"hidden\" name=\"wplc_ip_address\" id=\"wplc_ip_address\" value=\"".$offline_ip_address."\" />";
      $ret_msg .= "<input type=\"hidden\" name=\"wplc_domain_offline\" id=\"wplc_domain_offline\" value=\"".site_url()."\" />";
      $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_send_offline_message_button",wplc_filter_control_live_chat_box_html_send_offline_message_button($wplc_settings));
      $ret_msg .= "</div>";



    }
    $data = array(
    	'ret_msg' => $ret_msg,
    	'wplc_settings' => $wplc_settings,
    	'logged_in' => $logged_in,
    	'wplc_using_locale' => $wplc_using_locale
	);


    $ret_msg = apply_filters( "wplc_filter_2nd_layer_modify" , $data );
	if( is_array( $ret_msg ) ){
		/* if nothing uses this filter is comes back as an array, so return the original message in that array */
        return $ret_msg['ret_msg'];
    } else {
        return $ret_msg;
    }
}

/**
 * Filter to control the 3rd layer of the chat window
 * @param  array $wplc_settings live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings,$wplc_using_locale) {

  $wplc_sst_2 = __('Connecting. Please be patient...', 'wplivechat');
  if (!isset($wplc_settings['wplc_pro_sst2']) || $wplc_settings['wplc_pro_sst2'] == "") { $wplc_settings['wplc_pro_sst2'] = $wplc_sst_2; }
  $text = ($wplc_using_locale ? $wplc_sst_2 : stripslashes($wplc_settings['wplc_pro_sst2']));

  $ret_msg = "<p class=''wplc-color-4>".$text."</p>";
  return $ret_msg;
}

add_filter("wplc_filter_intro_text_heading", "wplc_filter_control_intro_text_heading", 10, 2);
/**
 * Filters intro text
*/
function wplc_filter_control_intro_text_heading($content, $wplc_settings){

	if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 1) {

  	} elseif (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'email') {

  	} elseif (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'name') {

  	} else {
     	$content = "";
  	}

	return $content;
}


add_filter("wplc_filter_live_chat_box_above_main_div","wplc_filter_control_live_chat_box_above_main_div",10,3);
function wplc_filter_control_live_chat_box_above_main_div( $msg, $wplc_settings, $cid ) {
  if(!isset($wplc_settings['wplc_newtheme'])){ $wplc_settings['wplc_newtheme'] = "theme-2"; }
  if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {

  	$agent_info = '';
  	$cbox_header_bg = '';
  	$agent_tagline = '';
  	$agent_bio = '';

  	$a_twitter = '';
  	$a_linkedin = '';
  	$a_facebook = '';
  	$a_website = '';
  	$social_links = '';
  	$agent_string = '';

  	if(isset($wplc_settings['wplc_use_node_server']) && intval($wplc_settings['wplc_use_node_server']) == 1) {} else {
	  	if ( $cid ) {
	  		$cid = wplc_return_chat_id_by_rel($cid);
			$chat_data = wplc_get_chat_data( $cid );

			if ( isset( $chat_data->agent_id ) ) {
				$agent_id = intval( $chat_data->agent_id );
			} else {
				$agent_id = get_current_user_id();
			}

			if ( $agent_id ) {

		        $wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
		        $user_info = get_userdata( $agent_id );


		        if( isset( $wplc_acbc_data['wplc_use_wp_name'] ) && $wplc_acbc_data['wplc_use_wp_name'] == '1' ){

		            $agent = $user_info->display_name;
		        } else {
		            if (!empty($wplc_acbc_data['wplc_chat_name'])) {
		                $agent = $wplc_acbc_data['wplc_chat_name'];
		            } else {
		                $agent = 'Admin';
		            }
		        }
		        $cbox_header_bg = "style='background-image:url(https://www.gravatar.com/avatar/".md5($user_info->user_email)."?s=380&d=mm); no-repeat; cover;'";

				$extra = apply_filters( "wplc_filter_further_live_chat_box_above_main_div", '', $wplc_settings, $cid, $chat_data, $agent );

				$agent_string = '
				<p style="text-align:center;">
					<img class="img-thumbnail img-circle wplc_thumb32 wplc_agent_involved" style="max-width:inherit;" id="agent_grav_'.$agent_id.'" title="'.$agent.'" src="https://www.gravatar.com/avatar/'.md5($user_info->user_email).'?s=60&d=mm" /><br />
					<span class="wplc_agent_name wplc-color-2">'.$agent.'</span>
					'.$extra.'
					<span class="bleeper_pullup down"><i class="fa fa-angle-up"></i></span>
				</p>';

			}
	  	}

	}

    $msg .= "<div id='wplc_chatbox_header_bg' ".$cbox_header_bg."><div id='wplc_chatbox_header' class='wplc-color-bg-1 wplc-color-4'><div class='wplc_agent_info'>".$agent_string."</div></div></div>";

  }
  return $msg;
}

/**
 * Filter to control the 4th layer of the chat window
 * @param  array $wplc_settings live chat settings array
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings,$wplc_using_locale, $cid ) {
  $wplc_enter = __('Connecting. Please be patient...', 'wplivechat');
  if (!isset($wplc_settings['wplc_user_enter']) || $wplc_settings['wplc_user_enter'] == "") { $wplc_settings['wplc_pro_sst2'] = $wplc_enter; }
  $text = ($wplc_using_locale ? $wplc_enter : stripslashes($wplc_settings['wplc_user_enter']));

  $wplc_welcome = __('Welcome. How may I help you?', 'wplivechat');
  if (!isset($wplc_settings['wplc_user_welcome_chat']) || $wplc_settings['wplc_user_welcome_chat'] == "") { $wplc_settings['wplc_user_welcome_chat'] = $wplc_welcome; }
  $text2 = ($wplc_using_locale ? $wplc_welcome : stripslashes($wplc_settings['wplc_user_welcome_chat']));





  $ret_msg = "";
  if(!isset($wplc_settings['wplc_newtheme'])){ $wplc_settings['wplc_newtheme'] = "theme-2"; }
  if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-1') {
  	$ret_msg .= apply_filters("wplc_filter_typing_control_div","");
  }

  $ret_msg .= apply_filters("wplc_filter_inner_live_chat_box_4th_layer","", $wplc_settings);

  $ret_msg .= "<div id=\"wplc_sound_update\" style=\"height:0; width:0; display:none; border:0;\"></div>";

  $ret_msg .= apply_filters("wplc_filter_live_chat_box_above_main_div","",$wplc_settings, $cid);


  $ret_msg .= "<div id=\"wplc_chatbox\">";
  $ret_msg .= "</div>";

  $ret_msg .= "<div id='bleeper_chat_ended' style='display:none;'></div>";
  $ret_msg .= "<div id='wplc_user_message_div'>";

  $ret_msg .= "<p id='wplc_msg_notice'>".$text."</p>";

  //Editor Controls
  $ret_msg .= apply_filters("wplc_filter_chat_text_editor","");

  $ret_msg .= "<p>";
  $placeholder = __('Type here','wplivechat');
  $ret_msg .= "<textarea type=\"text\" name=\"wplc_chatmsg\" id=\"wplc_chatmsg\" placeholder=\"".$placeholder."\" class='wdt-emoji-bundle-enabled' maxlength='2000'></textarea>";
  if(!isset($wplc_settings['wplc_newtheme'])){ $wplc_settings['wplc_newtheme'] = "theme-2"; }
  if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-2') {
  	$ret_msg .= apply_filters("wplc_filter_typing_control_div_theme_2","");
  }

  //Upload Controls
  $ret_msg .= apply_filters("wplc_filter_chat_upload","");

  $ret_msg .= "<input type=\"hidden\" name=\"wplc_cid\" id=\"wplc_cid\" value=\"\" />";
  $ret_msg .= "<input id=\"wplc_send_msg\" type=\"button\" value=\"".__("Send", "wplivechat")."\" style=\"display:none;\" />";
  $ret_msg .= "</p>";

  $ret_msg .= function_exists("wplc_emoji_selector_div") ? wplc_emoji_selector_div() : "";

  $current_theme = isset($wplc_settings['wplc_newtheme']) ? $wplc_settings['wplc_newtheme'] : "theme-2";
  if($current_theme === "theme-2"){
  	$ret_msg .= apply_filters("wplc_filter_chat_4th_layer_below_input","", $wplc_settings); //Ratings/Social Icon Filter
  }

  $ret_msg .= "</div>";
  $ret_msg .= "</div>";
  return $ret_msg;
}

/**
 * Filter to control the 1st layer of the chat window
 * @param  array $wplc_settings        live chat settings array
 * @param  bool  $logged_in            Is the admin logged in or not
 * @param  bool  $wplc_using_locale    Are they using a localization plugin
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 */
function wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings,$logged_in,$wplc_using_locale, $class_override = false) {

  $ret_msg = "<div id='wplc_first_message'>";

  if(!isset($wplc_settings['wplc_newtheme'])){ $wplc_settings['wplc_newtheme'] = "theme-2"; }

  if ($logged_in) {
    $wplc_fst_1 = __('Questions?', 'wplivechat');
    $wplc_fst_2 = __('Chat with us', 'wplivechat');
    if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
      $coltheme = "wplc-color-4";
    } else {
      $coltheme = "wplc-color-2";
    }

    if($class_override){
    	//Override color class
    	$coltheme = $class_override;
    }

    $wplc_tl_msg = "<div class='$coltheme'><strong>" . ($wplc_using_locale ? $wplc_fst_1 : stripslashes($wplc_settings['wplc_pro_fst1'])) . "</strong> " . ( $wplc_using_locale ? $wplc_fst_2 : stripslashes($wplc_settings['wplc_pro_fst2'])) ."</div>";

    $ret_msg .= $wplc_tl_msg;
  } else {


    $wplc_na = __('Chat offline. Leave a message', 'wplivechat');
    $wplc_tl_msg = "<span class='wplc_offline'>" . ($wplc_using_locale ? $wplc_na : stripslashes($wplc_settings['wplc_pro_na'])) . "</span>";
    $ret_msg .= $wplc_tl_msg;
  }
  $ret_msg .= "</div>";



  return $ret_msg;

}



add_filter("wplc_loggedin_filter","wplc_filter_control_loggedin",10,1);
function wplc_filter_control_loggedin($logged_in) {
    $wplc_is_admin_logged_in = get_transient("wplc_is_admin_logged_in");

    if ($wplc_is_admin_logged_in != 1) {
        $logged_in = false;
    } else {
        $logged_in = true;
    }

    $logged_in_checks = apply_filters("wplc_filter_is_admin_logged_in",array());

    /* if we are logged in ANYWHERE, set this to true */
    foreach($logged_in_checks as $key => $val) {
      if ($val) { $logged_in = true; break; }
    }
    $logged_in_via_app = false;
    if (isset($logged_in_checks['app']) && $logged_in_checks['app'] == true) { $logged_in_via_app = true; }

    $logged_in = apply_filters("wplc_final_loggedin_control",$logged_in,$logged_in_via_app);

    /* admin is using the basic version and is logged in */
    if ($wplc_is_admin_logged_in) { $logged_in = true; }

    return $logged_in;
}

function wplc_shortenurl($url) {
	if ( strlen($url) > 45) {
		return substr($url, 0, 30)."[...]".substr($url, -15);
	} else {
		return $url;
	}
}


/**
 * The function that builds the chat box
 * @since  6.0.00
 * @author  Nick Duncan <nick@wp-livechat.com>
 * @return JSON encoded HTML
 */
function wplc_output_box_ajax($cid = null) {


        $ret_msg = array();
        $logged_in = false;

        $wplc_settings = get_option("WPLC_SETTINGS");

        if(isset($wplc_settings['wplc_using_localization_plugin']) && $wplc_settings['wplc_using_localization_plugin'] == 1){ $wplc_using_locale = true; } else { $wplc_using_locale = false; }


        $logged_in = apply_filters("wplc_loggedin_filter",false);

        $ret_msg['cbox'] = apply_filters("wplc_theme_control",$wplc_settings,$logged_in,$wplc_using_locale,$cid);

        $ret_msg['online'] = $logged_in;


        if ($cid !== null && $cid !== '') {
        	$ret_msg['cid'] = $cid;
        	$chat_data = wplc_get_chat_data( $cid );
        	wplc_record_chat_notification('user_loaded',$cid,array('uri' => $_SERVER['HTTP_REFERER'], 'chat_data' => $chat_data ));



	        if ( !isset($chat_data) || !$chat_data->agent_id ) {
	            $ret_msg['type'] = 'new';
	        } else {
	        	$ret_msg['type'] = 'returning';

	        	if(isset($wplc_settings['wplc_use_node_server']) && intval($wplc_settings['wplc_use_node_server']) == 1) {
	        		//This is using node, we shouldn't generate the header data as part of the chat box.
	        		//We will do this dynamically on the front end

	        	} else {
		        	/* build the AGENT DATA array */
			        $wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
			        $user_info = get_userdata( intval( $chat_data->agent_id ) );

			        $agent_tagline = '';
			        $agent_bio = '';
			        $a_twitter = '';
			        $a_linkedin = '';
			        $a_facebook = '';
			        $a_website = '';
			        $social_links = '';

			        if( isset( $wplc_acbc_data['wplc_use_wp_name'] ) && $wplc_acbc_data['wplc_use_wp_name'] == '1' ){

			            $agent = $user_info->display_name;
			        } else {
			            if (!empty($wplc_acbc_data['wplc_chat_name'])) {
			                $agent = $wplc_acbc_data['wplc_chat_name'];
			            } else {
			                $agent = 'Admin';
			            }
			        }

			        if( !isset( $data ) ){ $data = false; }

			        $agent_tagline = apply_filters( "wplc_filter_agent_data_agent_tagline", $agent_tagline, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data );
	                $agent_bio = apply_filters( "wplc_filter_agent_data_agent_bio", $agent_bio, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data );
	                $social_links = apply_filters( "wplc_filter_agent_data_social_links", $social_links, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data);

		        	$ret_msg['agent_data'] = array(
			                'email' => md5($user_info->user_email),
			                'name' => $agent,
			                'aid' => $user_info->ID,
			                'agent_tagline' => $agent_tagline,
			                'agent_bio' => $agent_bio,
			                "social_links" => $social_links
			            );
		        }

	        }


        } else {
        	$ret_msg['type'] = 'new';
        }
        
        return json_encode($ret_msg);


}
function wplc_return_default_theme($wplc_settings,$logged_in,$wplc_using_locale,$cid) {
	$ret_msg = apply_filters("wplc_filter_live_chat_box_html_main_div_top",wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings,$logged_in,$wplc_using_locale));
    $ret_msg .= "<div class=\"wp-live-chat-wraper\">";
    $ret_msg .= 	"<div id='bleeper_bell' class='wplc-color-bg-1 wplc-color-2' style='display:none;'><i class='fa fa-bell'></i></div>";
    $ret_msg .=   apply_filters("wplc_filter_live_chat_box_html_header_div_top",wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings));
    $ret_msg .= " <i id=\"wp-live-chat-minimize\" class=\"fa fa-minus wplc-color-bg-2 wplc-color-1\" style=\"display:none;\"></i>";
    $ret_msg .= " <i id=\"wp-live-chat-close\" class=\"fa fa-times\" style=\"display:none;\" ></i>";
    $ret_msg .= " <div id=\"wp-live-chat-1\" >";
    $ret_msg .=     apply_filters("wplc_filter_live_chat_box_html_1st_layer",wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings,$logged_in,$wplc_using_locale,'wplc-color-2'));
    $ret_msg .= " </div>";
	$ret_msg .= '<div id="wplc-chat-alert" class="wplc-chat-alert wplc-chat-alert--' . $wplc_settings["wplc_theme"] . '"></div>';
	$ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-2\" style=\"display:none;\">";
    $ret_msg .= 	apply_filters("wplc_filter_live_chat_box_survey","");
    $ret_msg .=     apply_filters("wplc_filter_live_chat_box_html_2nd_layer",wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings,$logged_in,$wplc_using_locale,$cid), $cid);
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-3\" style=\"display:none;\">";
    $ret_msg .=     apply_filters("wplc_filter_live_chat_box_html_3rd_layer",wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings,$wplc_using_locale));
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-react\" style=\"display:none;\">";
    $ret_msg .= "   <p>".__("Reactivating your previous chat...", "wplivechat")."</p>";
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wplc-extra-div\" style=\"display:none;\">";
    $ret_msg .=     apply_filters("wplc_filter_wplc_extra_div","",$wplc_settings,$wplc_using_locale);
    $ret_msg .= "</div>";
    $ret_msg .= "</div>";
    $ret_msg .= " <div id=\"wp-live-chat-4\" style=\"display:none;\">";
    $ret_msg .=     apply_filters("wplc_filter_live_chat_box_html_4th_layer",wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings,$wplc_using_locale, $cid));
    $ret_msg .= "</div>";
    return $ret_msg;
}

add_filter("wplc_theme_control","wplc_theme_control_function",10,4);

function wplc_theme_control_function($wplc_settings,$logged_in,$wplc_using_locale, $cid) {
  $cid=intval($cid);
  if (!$wplc_settings) { return ""; }
  if (isset($wplc_settings['wplc_newtheme'])) { $wplc_newtheme = $wplc_settings['wplc_newtheme']; } else { $wplc_newtheme = "theme-2"; }
  $default_theme = wplc_return_default_theme($wplc_settings,$logged_in,$wplc_using_locale, $cid);
  if (isset($wplc_newtheme)) {
    if($wplc_newtheme == 'theme-1') {
      $ret_msg = $default_theme;
    } else if ($wplc_newtheme == 'theme-2') {
      $ret_msg = apply_filters("wplc_filter_live_chat_box_html_main_div_top",wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings,$logged_in,$wplc_using_locale));
      $ret_msg .= "<div class=\"wp-live-chat-wraper\">";
      $ret_msg .= 	"<div id='bleeper_bell' class='wplc-color-bg-1  wplc-color-2' style='display:none;'><i class='fa fa-bell'></i></div>";
      $ret_msg .=   apply_filters("wplc_filter_live_chat_box_html_header_div_top",wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings));
      $ret_msg .= " </div>";
      $ret_msg .= '<div id="wplc-chat-alert" class="wplc-chat-alert wplc-chat-alert--' . $wplc_settings["wplc_theme"] . '"></div>';
      $ret_msg .= " <div id=\"wp-live-chat-2\" style=\"display:none;\">";
      $ret_msg .= " 	<i id=\"wp-live-chat-minimize\" class=\"fa fa-minus wplc-color-bg-2 wplc-color-1\" style=\"display:none;\" ></i>";
      $ret_msg .= " 	<i id=\"wp-live-chat-close\" class=\"fa fa-times\" style=\"display:none;\" ></i>";
      $ret_msg .= " 	<div id=\"wplc-extra-div\" style=\"display:none;\">";
      $ret_msg .=     	apply_filters("wplc_filter_wplc_extra_div","",$wplc_settings,$wplc_using_locale);
      $ret_msg .= "	</div>";
      $ret_msg .= " 	<div id='wp-live-chat-inner-container'>";
      $ret_msg .= " 		<div id='wp-live-chat-inner'>";
      $ret_msg .= "   		<div id=\"wp-live-chat-1\" class=\"wplc-color-2 wplc-color-bg-1\" >";
      $ret_msg .=       			apply_filters("wplc_filter_live_chat_box_html_1st_layer",wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings,$logged_in,$wplc_using_locale,'wplc-color-2'));
      $ret_msg .= "   		</div>";
      $ret_msg .=     		apply_filters("wplc_filter_live_chat_box_html_2nd_layer",wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings,$logged_in,$wplc_using_locale, $cid), $cid);
      $ret_msg .= " 		</div>";
      $ret_msg .= " 		<div id=\"wp-live-chat-react\" style=\"display:none;\">";
      $ret_msg .= "   		<p>".__("Reactivating your previous chat...", "wplivechat")."</p>";
      $ret_msg .= " 		</div>";
      $ret_msg .= " 	</div>";
      $ret_msg .= "   <div id=\"wp-live-chat-3\" style=\"display:none;\">";
      $ret_msg .=     	apply_filters("wplc_filter_live_chat_box_html_3rd_layer",wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings,$wplc_using_locale));
      $ret_msg .= "   </div>";
      $ret_msg .= " </div>";
      $ret_msg .= "   <div id=\"wp-live-chat-4\" style=\"display:none;\">";
      $ret_msg .=       apply_filters("wplc_filter_live_chat_box_html_4th_layer",wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings,$wplc_using_locale, $cid));
      $ret_msg .= "   </div>";
      $ret_msg .= "</div>";
    } else {
      $ret_msg = $default_theme;
    }
  } else {
    $ret_msg = $default_theme;
  }
  return $ret_msg;
}


function wplc_admin_accept_chat($cid) {
    $user_ID = get_current_user_id();
    wplc_change_chat_status(sanitize_text_field($cid), 3,$user_ID);
    return true;
}

add_action('admin_head', 'wplc_update_chat_statuses');


function wplc_superadmin_javascript() {
	
    $wplc_settings = get_option("WPLC_SETTINGS");

    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1 && (!isset($_GET['action']) || $_GET['action'] !== "history") ){

    	//Using node, remote scripts please
		if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {
			/* Run admin JS on all admin pages */
			if ( isset( $_GET['action'] ) && $_GET['action'] == "history" ) {  }else {
           		wplc_admin_remote_dashboard_scripts($wplc_settings);
			}
		} else {
			/* Only run admin JS on the chat dashboard page */
			if ( isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu' && !isset( $_GET['action'] ) ) {
            	wplc_admin_remote_dashboard_scripts($wplc_settings);
			}
		}

        if( isset( $_GET['page'] ) && $_GET['page'] === "wplivechat-menu-offline-messages"){
           wplc_admin_output_js();
        }
    } else {

	    do_action("wplc_hook_superadmin_head");

		if ( isset( $_GET['page'] ) && isset( $_GET['action'] ) && $_GET['page'] == "wplivechat-menu" && ( $_GET['action'] != 'welcome' && $_GET['action'] != 'credits'  ) ) {
            /* admin chat box page */
            
	        	/** set the global chat data here so we dont need to keep getting it from the DB or Cloud server */
	       	global $admin_chat_data;
	       	$admin_chat_data = wplc_get_chat_data($_GET['cid'], __LINE__);
	        	
            wplc_return_admin_chat_javascript(sanitize_text_field($_GET['cid']));
            
	        do_action("wplc_hook_admin_javascript_chat");
	 	} else {
			/* load this on every other admin page */
	        wplc_admin_javascript();
	        do_action("wplc_hook_admin_javascript");
	 	}
		

	    ?>
	    <script type="text/javascript">


	        function wplc_desktop_notification() {
	            if (typeof Notification !== 'undefined') {
	                if (!Notification) {
	                    return;
	                }
	                if (Notification.permission !== "granted")
	                    Notification.requestPermission();

	                var wplc_desktop_notification = new Notification('<?php _e('New chat received', 'wplivechat'); ?>', {
	                    icon: wplc_notification_icon_url,
	                    body: "<?php _e("A new chat has been received. Please go the 'Live Chat' page to accept the chat", "wplivechat"); ?>"
	                });
	                //Notification.close()
	            }
	        }

	    </script>
    	<?php

    }
	
	if(isset($_GET['page']) && $_GET['page'] === 'wplivechat-menu-settings') {
	      wp_enqueue_script('wplc-admin-js-settings', plugins_url('js/wplc_u_admin_settings.js', __FILE__), false, WPLC_PLUGIN_VERSION, false);
	}
	
}


/**
 * Admin JS set up
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
function wplc_admin_javascript() {
	$wplc_settings = get_option("WPLC_SETTINGS");
	
	if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {
		/* Run admin JS on all admin pages */
		if ( isset( $_GET['action'] ) && $_GET['action'] == "history" ) { return; }
		else {
			wplc_admin_output_js();
		}
	} else {
		/* Only run admin JS on the chat dashboard page */
		if ( isset( $_GET['page'] ) && ($_GET['page'] === 'wplivechat-menu') && !isset( $_GET['action'] ) ) {
			wplc_admin_output_js();
		}
	}

}

/**
 * Outputs the admin JS on to the relevant pages, controlled by wplc_admin_javascript();
 *
 * @return void
 * @since  7.1.00
 * @author Nick Duncan
 */
function wplc_admin_output_js() {

	$ajax_nonce = wp_create_nonce("wplc");

	    $wplc_current_user = get_current_user_id();
	    if( get_user_meta( $wplc_current_user, 'wplc_ma_agent', true )) {

	      wp_register_script('wplc-admin-js', plugins_url('js/wplc_u_admin.js', __FILE__), false, WPLC_PLUGIN_VERSION, false);
	      wp_enqueue_script('wplc-admin-js');
	      $not_icon = plugins_url('/images/wplc_notification_icon.png', __FILE__);

	      $wplc_wav_file = plugins_url('/includes/sounds/general/ring.wav', __FILE__);
	      $wplc_wav_file = apply_filters("wplc_filter_wav_file",$wplc_wav_file);
	      wp_localize_script('wplc-admin-js', 'wplc_wav_file', $wplc_wav_file);

	      wp_localize_script('wplc-admin-js', 'wplc_ajax_nonce', $ajax_nonce);

          wp_localize_script('wplc-admin-js', 'wplc_notification_icon', $not_icon);

          wp_localize_script('wplc-admin-js', 'bleeper_favico_noti', WPLC_PLUGIN_URL . '/images/48px_n.png');
	      wp_localize_script('wplc-admin-js', 'bleeper_favico', WPLC_PLUGIN_URL . '/images/48px.png');

	      $extra_data = apply_filters("wplc_filter_admin_javascript",array());
	      wp_localize_script('wplc-admin-js', 'wplc_extra_data', $extra_data);

	      $ajax_url = admin_url('admin-ajax.php');
	      $wplc_ajax_url = apply_filters("wplc_filter_ajax_url",$ajax_url);
	      wp_localize_script('wplc-admin-js', 'wplc_ajaxurl', $wplc_ajax_url);
	      wp_localize_script('wplc-admin-js', 'wplc_ajaxurl_home', admin_url( 'admin-ajax.php' ) );

	      $wpc_ma_js_strings = array(
	          'remove_agent' => __('Remove', 'wplivechat'),
	          'nonce' => wp_create_nonce("wplc"),
	          'user_id' => get_current_user_id(),
	          'typing_string' => __('Typing...', 'wplivechat')

	      );
	      wp_localize_script('wplc-admin-js', 'wplc_admin_strings', $wpc_ma_js_strings);

	    }
	
}

function wplc_admin_menu_layout() {

	    do_action("wplc_hook_admin_menu_layout");

	    update_option('WPLC_V8_FIRST_TIME', false);
	    $wplc_settings = get_option("WPLC_SETTINGS");
	    if ( isset( $wplc_settings['wplc_use_node_server'] ) && $wplc_settings['wplc_use_node_server'] == 1 ) {
	        //Node in use, load remote dashboard
	        if ( $_GET['page'] === 'wplivechat-menu') {
	           wplc_admin_dashboard_layout_node('dashboard');

	           if( isset($_GET['action']) ){
	              wplc_admin_menu_layout_display();
	           }
	        } else {
	                // we'll control this in admin_footer
	            }

	   } else {
	              wplc_admin_menu_layout_display();
	  }
	    
}



function wplc_first_time_tutorial() {
    if (!get_option('WPLC_FIRST_TIME_TUTORIAL')) {
        ?>

        <div id="wplcftt" style='margin-top:30px; margin-bottom:20px; width: 65%; background-color: #FFF; box-shadow: 1px 1px 3px #ccc; display:block; padding:10px; text-align:center; margin-left:auto; margin-right:auto;'>
            <img src='<?php echo WPLC_PLUGIN_URL; ?>/images/wplc_notification_icon.png' width="130" align="center" />
            <h1 style='font-weight: 300; font-size: 50px; line-height: 50px;'><strong style="color: #ec822c;"><?php _e("Congratulations","wplivechat"); ?></strong></h1>
            <h2><strong><?php _e("You are now accepting live chat requests on your site.","wplivechat"); ?></strong></h2>
            <p><?php _e("The live chat box has automatically been enabled on your website.","wplivechat"); ?></p>
            <p><?php _e("Chat notifications will start appearing once visitors send a request.","wplivechat"); ?></p>
            <p><?php _e("You may <a href='?page=wplivechat-menu-settings' target='_BLANK'>modify your chat box settings here.","wplivechat"); ?></a></p>
            <p><?php _e("Experiencing issues?","wplivechat"); ?> <a href="https://wp-livechat.com/documentation/troubleshooting/" target="_BLANK" title=""><?php _e("Visit our troubleshooting section.","wplivechat"); ?></a></p>

            <p><button id="wplc_close_ftt" class="button button-secondary"><?php _e("Hide","wplivechat"); ?></button></p>

        </div>

    <?php }
}


/**
 * Control the content below the visitor count
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
add_filter("wplc_filter_chat_dahsboard_visitors_online_bottom","wplc_filter_control_chat_dashboard_visitors_online_bottom",10);
function wplc_filter_control_chat_dashboard_visitors_online_bottom($text) {
  $text = "<hr />";
  $text .= "<p class='wplc-agent-info' id='wplc-agent-info'>";
  $text .= "  <span class='wplc_agents_online'>1</span>";
  $text .= "  <a href='javascript:void(0);'>".__("Agent(s) online","wplivechat")."</a>";
  $text .= "</p>";
  return $text;
}



function wplc_admin_menu_layout_display() {

    $wplc_current_user = get_current_user_id();

    if( get_user_meta( $wplc_current_user, 'wplc_ma_agent', true ) ){

        do_action("wplc_hook_admin_menu_layout_display_top");

        wplc_stats("chat_dashboard");

        if (!isset($_GET['action'])) {
            ?>
        <style>
          #wplc-support-tabs a.wplc-support-link {
            background: url('<?php echo WPLC_PLUGIN_URL; ?>/images/support.png');
            right: 0px;
            top: 250px;
            height: 108px;
            width: 45px;
            margin: 0;
            padding: 0;
            position: fixed;
            z-index: 9999;
            display:block;
          }
        </style>
        	<div class='wplc_network_issue' style='display:none;'>

        	</div>

            <div id="wplc-support-tabs">
                <a class="wplc-support-link wplc-rotate" href="?page=wplivechat-menu-support-page"></a>
            </div>
            <div class='wplc_page_title'>
            	<img src='<?php echo WPLC_PLUGIN_URL; ?>images/wplc-logo.png' class='wplc-logo' />
                <?php wplc_first_time_tutorial(); ?>
				<?php do_action("wplc_hook_chat_dashboard_above"); ?>

                <p><?php _e("Please note: This window must be open in order to receive new chat notifications.", "wplivechat"); ?></p>
            </div>


            <div id="wplc_sound"></div>

            <div id="wplc_admin_chat_holder">
                <div id='wplc_admin_chat_info_new'>
                    <div class='wplc_chat_vis_count_box'>
                        <?php do_action("wplc_hook_chat_dahsboard_visitors_online_top"); ?>
                        <span class='wplc_vis_online'>0</span>
                        <span style='text-transform:uppercase;'>
                            <?php _e("Visitors online","wplivechat"); ?>
                        </span>
                        <?php echo apply_filters("wplc_filter_chat_dahsboard_visitors_online_bottom",""); ?>


                    </div>

                    <?php do_action("wplc_after_chat_visitor_count_hook"); ?>

                </div>

                <div id="wplc_admin_chat_area_new">
                    <div style="display:block;padding:10px;">
                    <ul class='wplc_chat_ul_header'>
                        <li><span class='wplc_header_vh wplc_headerspan_v'><?php _e("Visitor","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_t'><?php _e("Time","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_nr'><?php _e("Type","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_dev'><?php _e("Device","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_d'><?php _e("Data","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_s'><?php _e("Status","wplivechat"); ?></span></li>
                        <li><span class='wplc_header_vh wplc_headerspan_a'><?php _e("Action","wplivechat"); ?></span></li>
                    </ul>
                    <ul id='wplc_chat_ul'>
                    </ul>
                    <br />
                    <hr />
                    <?php //do_action("wplc_hook_chat_dashboard_bottom"); ?>

                    </div>

                </div>
            </div>





            <?php
        } else {
            if (isset($_GET['aid'])) { $aid = intval($_GET['aid']); } else { $aid = null; }

            if (!is_null($aid)) {
                do_action("wplc_hook_update_agent_id", intval($_GET['cid']), $aid);
            }

            if ($_GET['action'] == 'ac') {
                do_action('wplc_hook_accept_chat',$_GET, $aid);
            }
            do_action("wplc_hook_admin_menu_layout_display", $_GET['action'], intval($_GET['cid']), $aid);
        }
    } else {
      ?>

      <h1><?php _e("Chat Dashboard","wplivechat"); ?></h1>
      <div id="welcome-panel" class="welcome-panel">
        <div class="welcome-panel-content">
          <h2><?php _e("Oh no!","wplivechat"); ?></h2>
          <p class="about-description">
            <?php _e(
            "You do not have access to this page as <strong>you are not a chat agent</strong>.",
            "wplivechat"
            ); ?>
          </p>
          <p>Users with access to this page are as follows:</p>
          <p>
            <?php
             $user_array = get_users(array(
                'meta_key' => 'wplc_ma_agent',
            ));
             echo "<ol>";
            if ($user_array) {
              foreach ($user_array as $user) {
                echo "<li> ".$user->display_name . " (ID: ".$user->ID.")</li>";
              }
            }
             echo "</ol>";
            ?>
          </p>
        </div>
      </div>
      <?php
    }
}

add_action("wplc_hook_change_status_on_answer","wplc_hook_control_change_status_on_answer",10,2);
function wplc_hook_control_change_status_on_answer($get_data, $chat_data = false) {

  $user_ID = get_current_user_id();
  wplc_change_chat_status(sanitize_text_field($get_data['cid']), 3,$user_ID );
  wplc_record_chat_notification("joined",$get_data['cid'],array("aid"=>$user_ID));
}


add_action('wplc_hook_accept_chat','wplc_hook_control_accept_chat',10,2);
function wplc_hook_control_accept_chat($get_data,$aid) {

	global $admin_chat_data;

	if (!$admin_chat_data) {
		$chat_data = wplc_get_chat_data($get_data['cid'], __LINE__);
	} else {
		if (isset($admin_chat_data->id) && intval($admin_chat_data->id) === intval($get_data['cid'])) {
			$chat_data = $admin_chat_data;
		} else {
			/* chat ID's dont match, get the data for the correct chat ID */
			$chat_data = wplc_get_chat_data($get_data['cid'], __LINE__);
		}
	}


	do_action("wplc_hook_accept_chat_url",$get_data, $chat_data);
  	do_action("wplc_hook_change_status_on_answer",$get_data, $chat_data);
	do_action("wplc_hook_draw_chat_area",$get_data, $chat_data);


}

/**
* Hook to accept chat
*
* @since        7.1.00
* @param
* @return       void
* @author       Nick Duncan <nick@wp-livechat.com>
*
*/
add_action( 'wplc_hook_accept_chat_url' , 'wplc_b_hook_control_accept_chat_url', 5, 2);
function wplc_b_hook_control_accept_chat_url($get_data, $chat_data = false) {
    if (!isset($get_data['agent_id'])) {
    	/* free version */
        wplc_b_update_agent_id(sanitize_text_field($get_data['cid']), get_current_user_id());
    } else {
        wplc_b_update_agent_id(sanitize_text_field($get_data['cid']), sanitize_text_field($get_data['agent_id']));
    }

}

/**
* Assign the chat to the agent
*
* Replaces the same function of a different name in the older pro version
*
* @since        7.1.00
* @param
* @return       void
* @author       Nick Duncan <nick@wp-livechat.com>
*
*/
function wplc_b_update_agent_id($cid, $aid){
    global $wpdb;
    global $wplc_tblname_chats;
    $sql = "SELECT * FROM `$wplc_tblname_chats` WHERE `id` = '%d' LIMIT 1";
    $sql = $wpdb->prepare($sql, $cid);
    $result = $wpdb->get_row($sql);
    if ($result) {
	    if(intval($result->status) != 3){
	        $update = "UPDATE `$wplc_tblname_chats` SET `agent_id` = '%d' WHERE `id` = '%d' LIMIT 1";
            $update = $wpdb->prepare($update, $aid, $cid);
	        $wpdb->query($update);
	    }
	}
}


add_action("wplc_hook_draw_chat_area","wplc_hook_control_draw_chat_area",10,2);
function wplc_hook_control_draw_chat_area($get_data, $chat_data = false) {

 wplc_draw_chat_area(sanitize_text_field($get_data['cid']), $chat_data);
}

function wplc_draw_chat_area($cid, $chat_data = false) {

    global $wpdb;
    global $wplc_tblname_chats;

    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_chats
        WHERE `id` = '$cid'
        LIMIT 1
        "
    );

    if ($results) { } else {  $results[0] = null; } /* if chat ID doesnt exist, create the variable anyway to avoid an error. Hopefully the Chat ID exists on the server..! */

    $result = apply_filters("wplc_filter_chat_area_data", $results[0], $cid);

    ?>
    <style>

        .wplc-clear-float-message{
            clear: both;
        }

        .rating{
            background-color: lightgray;
            width: 80px;
            padding: 2px;
            border-radius: 4px;
            text-align: center;
            color: white;
            display: inline-block;
            float: right;
        }

        .rating-bad {
            background-color: #AF0B0B !important;
        }

        .rating-good {
            background-color: #368437 !important;
        }


    </style>
    <?php

      $user_data = maybe_unserialize($result->ip);
      $user_ip = isset($user_data['ip']) ? $user_data['ip'] : 'Unknown';
      $browser = isset($user_data['user_agent']) ? wplc_return_browser_string($user_data['user_agent']) : "Unknown";
      $browser_image = wplc_return_browser_image($browser, "16");

      if ($result->status == 1) {
          $status = __("Previous", "wplivechat");
      } else {
          $status = __("Active", "wplivechat");
      }

      if($user_ip == ""){
          $user_ip = __('IP Address not recorded', 'wplivechat');
      } else {
          $user_ip = "<a href='http://www.ip-adress.com/ip_tracer/" . $user_ip . "' title='".__('Whois for' ,'wplivechat')." ".$user_ip."' target='_BLANK'>".$user_ip."</a>";
      }

	echo "<h2>$status " . __( 'Chat with', 'wplivechat' ) . " " . sanitize_text_field($result->name) . "</h2>";
	if ( isset( $_GET['action'] ) && 'history' === $_GET['action'] ) {
		echo "<span class='wplc-history__date'><strong>" . __( 'Starting Time:', 'wplivechat' ) . "</strong>" . date( 'Y-m-d H:i:s', current_time( strtotime( $result->timestamp ) ) ) . "</span>";
		echo "<span class='wplc-history__date wplc-history__date-end'><strong>" . __( 'Ending Time:', 'wplivechat' ) . "</strong>" . date( 'Y-m-d H:i:s', current_time( strtotime( $result->last_active_timestamp ) ) ) . "</span>";
	}
      echo "<style>#adminmenuwrap { display:none; } #adminmenuback { display:none; } #wpadminbar { display:none; } #wpfooter { display:none; } .update-nag { display:none; }</style>";

      echo "<div class=\"end_chat_div\">";

      do_action("wplc_admin_chat_area_before_end_chat_button", $result);

      echo "<a href=\"javascript:void(0);\" class=\"wplc_admin_close_chat button\" id=\"wplc_admin_close_chat\">" . __("End chat", "wplivechat") . "</a>";

      do_action("wplc_admin_chat_area_after_end_chat_button", $result);

      echo "</div>";

      do_action("wplc_add_js_admin_chat_area", $cid, $chat_data);

      echo "<div id='admin_chat_box'>";

      $result->continue = true;

      do_action("wplc_hook_wplc_draw_chat_area",$result);

      if (!$result->continue) { return; }

      echo"<div class='admin_chat_box'><div class='admin_chat_box_inner' id='admin_chat_box_area_" . intval($result->id) . "'>".apply_filters( "wplc_chat_box_draw_chat_box_inner", "", $cid)."</div><div class='admin_chat_box_inner_bottom'>" . wplc_return_chat_response_box($cid, $result) . "</div>";


      echo "</div>";
      echo "<div class='admin_visitor_info'>";
      do_action("wplc_hook_admin_visitor_info_display_before",$cid);


      echo "  <div style='float:left; width:100px;'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "?d=mm\" class=\"admin_chat_img\" /></div>";
      echo "  <div style='float:left;'>";

      echo "      <div class='admin_visitor_info_box1'>";
      echo "          <span class='admin_chat_name'>" . sanitize_text_field($result->name) . "</span>";
      echo "          <span class='admin_chat_email'>" . sanitize_text_field($result->email) . "</span>";
      echo "      </div>";
      echo "  </div>";

      echo "  <div class='admin_visitor_advanced_info'>";
      echo "      <strong>" . __("Site Info", "wplivechat") . "</strong>";
      echo "      <hr />";
      echo "      <span class='part1'>" . __("Chat initiated on:", "wplivechat") . "</span> <span class='part2'>" . esc_url($result->url) . "</span>";
      echo "  </div>";

      echo "  <div class='admin_visitor_advanced_info'>";
      echo "      <strong>" . __("Advanced Info", "wplivechat") . "</strong>";
      echo "      <hr />";
      echo "      <span class='part1'>" . __("Browser:", "wplivechat") . "</span><span class='part2'> $browser <img src='" . WPLC_PLUGIN_URL . "/images/$browser_image' alt='$browser' title='$browser' /><br />";
      echo "      <span class='part1'>" . __("IP Address:", "wplivechat") . "</span><span class='part2'> ".sanitize_text_field($user_ip);
      echo "  </div>";
	  echo "<hr />";

      echo (apply_filters("wplc_filter_advanced_info","", sanitize_text_field($result->id), sanitize_text_field($result->name), $result));

      echo "  <div id=\"wplc_sound_update\"></div>";

      echo "<div class='wplc_bottom_chat_info_container'>";
      do_action("wplc_hook_admin_visitor_info_display_after",$cid);
      echo "</div>";

      echo "</div>";

      if ($result->status != 1) {

          do_action("wplc_hook_admin_below_chat_box",$result);
      }

}


function wplc_return_chat_response_box($cid, $chat_data = false) {

    $ret = "<div class=\"chat_response_box\">";
    $ret .= apply_filters("wplc_filter_typing_control_div","");
    $ret .= "<input type='text' name='wplc_admin_chatmsg' id='wplc_admin_chatmsg' value='' placeholder='" . __("type here...", "wplivechat") . "' />";

    $ret .= apply_filters("wplc_filter_chat_upload","");
    $ret .= apply_filters("wplc_filter_chat_text_editor","");

    $ret .= "<input id='wplc_admin_cid' type='hidden' value='$cid' />";
    $ret .= "<input id='wplc_admin_send_msg' type='button' value='" . __("Send", "wplivechat") . "' style=\"display:none;\" />";
    $ret .= "</div>";
    return $ret;
}

function wplc_return_admin_chat_javascript($cid) {
    $ajax_nonce = wp_create_nonce("wplc");
    $wplc_settings = get_option("WPLC_SETTINGS");

    wp_register_script('wplc-admin-chat-server', plugins_url('js/wplc_server.js', __FILE__), false, WPLC_PLUGIN_VERSION, false);
    wp_enqueue_script('wplc-admin-chat-server');

	wp_localize_script( 'wplc-admin-chat-server', 'wplc_datetime_format', array(
		'date_format' => get_option( 'date_format' ),
		'time_format' => get_option( 'time_format' ),
	) );

    global $admin_chat_data;
    if (!$admin_chat_data) {
    	$cdata = wplc_get_chat_data($cid, __LINE__);
    } else {
    	/* copy the stored admin chat data variable - more efficient */
    	$cdata = $admin_chat_data;
    }

	$other = maybe_unserialize($cdata->other);

    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1){
		if (isset($other['socket']) && ($other['socket'] == true || $other['socket'] == "true")) {
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'history') {
				wp_localize_script('wplc-admin-chat-server', 'wplc_use_node_server', "false");
			} else {
				wp_localize_script('wplc-admin-chat-server', 'wplc_use_node_server', "true");
				wp_localize_script('wplc-admin-chat-server', 'wplc_localized_string_is_typing_single', __(" is typing...","wplivechat"));
			}

    		$wplc_node_token = get_option("wplc_node_server_secret_token");
	    	if(!$wplc_node_token){
		    	if(function_exists("wplc_node_server_token_regenerate")){
			        wplc_node_server_token_regenerate();
			        $wplc_node_token = get_option("wplc_node_server_secret_token");
			    }
			}
	    	wp_localize_script('wplc-admin-chat-server', 'bleeper_api_key', $wplc_node_token);
		}
    }

    /**
     * Create a JS object for all Agent ID's and Gravatar MD5's
     */
    $user_array = get_users(array(
        'meta_key' => 'wplc_ma_agent',
    ));

    $a_array = array();
    if ($user_array) {
        foreach ($user_array as $user) {
        	$a_array[$user->ID] = array();
        	$a_array[$user->ID]['name'] = $user->display_name;
        	$a_array[$user->ID]['md5'] = md5( $user->user_email );
        }
    }
	wp_localize_script('wplc-admin-chat-server', 'wplc_agent_data', $a_array);

    /**
     * Get the CURRENT agent's data
     */
    if(isset($_GET['aid'])){
    	$agent_data = get_user_by('ID', intval($_GET['aid']));
    	wp_localize_script('wplc-admin-chat-server', 'wplc_admin_agent_name', $agent_data->display_name);
    	wp_localize_script('wplc-admin-chat-server', 'wplc_admin_agent_email', md5($agent_data->user_email));
 	} else {
 		$agent_data = get_user_by('ID', intval(get_current_user_id()));
    	wp_localize_script('wplc-admin-chat-server', 'wplc_admin_agent_name', $agent_data->display_name);
    	wp_localize_script('wplc-admin-chat-server', 'wplc_admin_agent_email', md5($agent_data->user_email));
 	}


	wp_register_script('wplc-admin-chat-js', plugins_url('js/wplc_u_admin_chat.js', __FILE__), array('wplc-admin-chat-server'), WPLC_PLUGIN_VERSION, false);
	wp_enqueue_script('wplc-admin-chat-js');


	
    if (isset($wplc_settings['wplc_theme'])) { $wplc_theme = $wplc_settings['wplc_theme']; } else { $wplc_theme = "theme-default"; }

      if($wplc_theme == 'theme-default') {
        wp_register_style('wplc-theme-palette-default', plugins_url('/css/themes/theme-default.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-default');

      }
      else if($wplc_theme == 'theme-1') {
        wp_register_style('wplc-theme-palette-1', plugins_url('/css/themes/theme-1.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-1');

      }
      else if($wplc_theme == 'theme-2') {
        wp_register_style('wplc-theme-palette-2', plugins_url('/css/themes/theme-2.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-2');

      }
      else if($wplc_theme == 'theme-3') {
        wp_register_style('wplc-theme-palette-3', plugins_url('/css/themes/theme-3.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-3');

      }
      else if($wplc_theme == 'theme-4') {
        wp_register_style('wplc-theme-palette-4', plugins_url('/css/themes/theme-4.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-4');

      }
      else if($wplc_theme == 'theme-5') {
        wp_register_style('wplc-theme-palette-5', plugins_url('/css/themes/theme-5.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-theme-palette-5');

      }
      else if($wplc_theme == 'theme-6') {
        /* custom */
        /* handled elsewhere */



      }
  	
	$wplc_settings = get_option("WPLC_SETTINGS");
	$wplc_user_data = get_user_by( 'id', get_current_user_id() );

	if (isset($cdata->agent_id)) {
		$wplc_agent_data = get_user_by( 'id', intval( $cdata->agent_id ) );
	}


	if( isset($wplc_settings['wplc_show_name']) && $wplc_settings['wplc_show_name'] == '1' ){ $wplc_show_name = true; } else { $wplc_show_name = false; }
    if( isset($wplc_settings['wplc_show_avatar']) && $wplc_settings['wplc_show_avatar'] ){ $wplc_show_avatar = true; } else { $wplc_show_avatar = false; }
	if( isset($wplc_settings['wplc_show_date']) && $wplc_settings['wplc_show_date'] == '1' ){ $wplc_show_date = true; } else { $wplc_show_date = false; }
	if( isset($wplc_settings['wplc_show_time']) && $wplc_settings['wplc_show_time'] == '1' ){ $wplc_show_time = true; } else { $wplc_show_time = false; }

 	$wplc_chat_detail = array( 'name' => $wplc_show_name, 'avatar' => $wplc_show_avatar, 'date' => $wplc_show_date, 'time' => $wplc_show_time );


	wp_enqueue_script('wplc-admin-chat-js');
	wp_localize_script( 'wplc-admin-chat-js', 'wplc_show_chat_detail', $wplc_chat_detail );



	if (!empty( $wplc_agent_data ) ) {
		wp_localize_script( 'wplc-admin-chat-js', 'wplc_agent_name', $wplc_agent_data->display_name );
        wp_localize_script( 'wplc-admin-chat-js', 'wplc_agent_email', md5( $wplc_agent_data->user_email ) );
	} else {
		wp_localize_script( 'wplc-admin-chat-js', 'wplc_agent_name', ' ' );
		wp_localize_script( 'wplc-admin-chat-js', 'wplc_agent_email', ' ' );
	}

    wp_localize_script('wplc-admin-chat-js', 'wplc_chat_name', $cdata->name);
    wp_localize_script('wplc-admin-chat-js', 'wplc_chat_email', md5($cdata->email));

    if(class_exists("WP_REST_Request")) {
	    wp_localize_script('wplc-admin-chat-js', 'wplc_restapi_enabled', '1');
	    wp_localize_script('wplc-admin-chat-js', 'wplc_restapi_token', get_option('wplc_api_secret_token'));
		wp_localize_script('wplc-admin-chat-js', 'wplc_restapi_endpoint', rest_url('wp_live_chat_support/v1'));
        } else {
    	wp_localize_script('wplc-admin-chat-js', 'wplc_restapi_enabled', '0');
    }


    $src = wplc_get_admin_picture();
    if ($src) {
        $image = "<img src=" . $src . " width='20px' id='wp-live-chat-2-img'/>";
    } else {
      $image = " ";
    }
 
    $admin_pic = $image;
    wp_localize_script('wplc-admin-chat-js', 'wplc_localized_string_is_typing', __("is typing...","wplivechat"));
    wp_localize_script('wplc-user-script', 'wplc_localized_string_admin_name', apply_filters( 'wplc_filter_admin_name', 'Admin' ) );
    wp_localize_script('wplc-admin-chat-js', 'wplc_ajax_nonce', $ajax_nonce);
    wp_localize_script('wplc-admin-chat-js', 'admin_pic', $admin_pic);

    $wplc_ding_file = plugins_url('/includes/sounds/general/ding.mp3', __FILE__);
    $wplc_ding_file = apply_filters( 'wplc_filter_message_sound', $wplc_ding_file );
    wp_localize_script('wplc-admin-chat-js', 'wplc_ding_file', $wplc_ding_file);

    $extra_data = apply_filters("wplc_filter_admin_javascript",array());
    wp_localize_script('wplc-admin-chat-js', 'wplc_extra_data', $extra_data);

    if (isset($wplc_settings['wplc_display_name']) && $wplc_settings['wplc_display_name'] == 1) {
        $display_name = 'display';
    } else {
        $display_name = 'hide';
    }
    if (isset($wplc_settings['wplc_enable_msg_sound']) && intval($wplc_settings['wplc_enable_msg_sound']) == 1) {
        $enable_ding = '1';
    } else {
        $enable_ding = '0';
    }
    if (isset($_COOKIE['wplc_email']) && $_COOKIE['wplc_email'] != "") {
        $wplc_user_email_address = sanitize_text_field($_COOKIE['wplc_email']);
    } else {
        $wplc_user_email_address = " ";
    }

    wp_localize_script('wplc-admin-chat-js', 'wplc_name', $display_name);
    wp_localize_script('wplc-admin-chat-js', 'wplc_enable_ding', $enable_ding);

    $ajax_url = admin_url('admin-ajax.php');
	$home_ajax_url = $ajax_url;

    $wplc_ajax_url = apply_filters("wplc_filter_ajax_url",$ajax_url);
    wp_localize_script('wplc-admin-chat-js', 'wplc_ajaxurl', $wplc_ajax_url);
    wp_localize_script('wplc-admin-chat-js', 'wplc_home_ajaxurl', $home_ajax_url);



    $wplc_url = admin_url('admin.php?page=wplivechat-menu&action=ac&cid=' . $cid);
    wp_localize_script('wplc-admin-chat-js', 'wplc_url', $wplc_url);


    $wplc_string1 = __("User has opened the chat window", "wplivechat");
    $wplc_string2 = __("User has minimized the chat window", "wplivechat");
    $wplc_string3 = __("User has maximized the chat window", "wplivechat");
    $wplc_string4 = __("The chat has been ended", "wplivechat");
    wp_localize_script('wplc-admin-chat-js', 'wplc_string1', $wplc_string1);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string2', $wplc_string2);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string3', $wplc_string3);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string4', $wplc_string4);
    wp_localize_script('wplc-admin-chat-js', 'wplc_cid', $cid);


    do_action("wplc_hook_admin_chatbox_javascript");

}



function wplc_activate() {
	wplc_check_guid();
    wplc_handle_db();
    
    if (!get_option("WPLC_SETTINGS")) {
        $wplc_alt_text = __("Please click \'Start Chat\' to initiate a chat with an agent", "wplivechat");
	    $wplc_default_visitor_name = __( "Guest", "wplivechat" );
	    $wplc_admin_email = get_option('admin_email');

        $wplc_default_settings_array = array(
            "wplc_settings_align" => "2",
            "wplc_settings_enabled" => "1",
            "wplc_powered_by" => "1",
            "wplc_settings_fill" => "ed832f",
            "wplc_settings_font" => "FFFFFF",
            "wplc_settings_color1" => "ED832F",
            "wplc_settings_color2" => "FFFFFF",
            "wplc_settings_color3" => "EEEEEE",
            "wplc_settings_color4" => "666666",
            "wplc_theme" => "theme-default",
            "wplc_newtheme" => "theme-2",
            "wplc_require_user_info" => '1',
            "wplc_loggedin_user_info" => '1',
            "wplc_user_alternative_text" => $wplc_alt_text,
            "wplc_user_default_visitor_name" => $wplc_default_visitor_name,
            "wplc_enabled_on_mobile" => '1',
            "wplc_display_name" => '1',
            "wplc_record_ip_address" => '0',
            "wplc_pro_chat_email_address" => $wplc_admin_email,
            "wplc_pro_fst1" => __("Questions?", "wplivechat"),
            "wplc_pro_fst2" => __("Chat with us", "wplivechat"),
            "wplc_pro_fst3" => __("Start live chat", "wplivechat"),
            "wplc_pro_sst1" => __("Start Chat", "wplivechat"),
            "wplc_pro_sst1_survey" => __("Or chat to an agent now", "wplivechat"),
            "wplc_pro_sst1e_survey" => __("Chat ended", "wplivechat"),
            "wplc_pro_sst2" => __("Connecting. Please be patient...", "wplivechat"),
            "wplc_pro_tst1" => __("Reactivating your previous chat...", "wplivechat"),
            "wplc_pro_na" => __("Chat offline. Leave a message", "wplivechat"),
            "wplc_pro_intro" => __("Hello. Please input your details so that I may help you.", "wplivechat"),
            "wplc_pro_offline1" => __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat"),
            "wplc_pro_offline2" => __("Sending message...", "wplivechat"),
            "wplc_pro_offline3" => __("Thank you for your message. We will be in contact soon.", "wplivechat"),
            "wplc_pro_offline_btn" => __("Leave a message", "wplivechat"),
            "wplc_pro_offline_btn_send" => __("Send message", "wplivechat"),
            "wplc_user_enter" => __("Press ENTER to send your message", "wplivechat"),
            "wplc_text_chat_ended" => __("The chat has been ended by the operator.", "wplivechat"),
            "wplc_close_btn_text" => __("close", "wplivechat"),
            "wplc_user_welcome_chat" => __("Welcome. How may I help you?", "wplivechat"),
            'wplc_welcome_msg' => __("Please standby for an agent. While you wait for the agent you may type your message.","wplivechat"),
            'wplc_typing_enabled' => '1', 
			'wplc_show_avatar' => '1', 
			'wplc_ux_editor' => '1', 
			'wplc_ux_file_share' => '1', 
			'wplc_ux_exp_rating' => '1', 
			'wplc_default_department' => '-1',
			'wplc_enable_font_awesome' => '1',
        );

		//Added V8: Default Settings array filter
		$wplc_default_settings_array = apply_filters("wplc_activate_default_settings_array", $wplc_default_settings_array);

        add_option('WPLC_SETTINGS', $wplc_default_settings_array);
				

    if (current_user_can('manage_options')) {
        global $user_ID;
        $user = new WP_User($user_ID);
        foreach ($user->roles as $urole) {
            if ($urole == "administrator") {
                $admins = get_role('administrator');
                $admins->add_cap('edit_wplc_quick_response');
                $admins->add_cap('edit_wplc_quick_response');
                $admins->add_cap('edit_other_wplc_quick_response');
                $admins->add_cap('publish_wplc_quick_response');
                $admins->add_cap('read_wplc_quick_response');
                $admins->add_cap('read_private_wplc_quick_response');
                $admins->add_cap('delete_wplc_quick_response');
            }
        }
    } 

    }

	$settings = get_option( 'WPLC_ACBC_SETTINGS' );

	$wplc_acbc_defaults = array(
			'wplc_chat_name'              => __( 'Admin', 'wplivechat' ),
			'wplc_chat_pic'               => WPLC_PLUGIN_URL . 'images/picture-for-chat-box.jpg',
			'wplc_chat_logo'              => '',
			'wplc_chat_icon'              => WPLC_PLUGIN_URL . 'images/chaticon.png',
			'wplc_chat_delay'             => '2',
			'wplc_pro_chat_notification'  => 'no',
			'wplc_pro_chat_email_address' => get_option( 'admin_email' ),
			'wplc_use_wp_name'            => '1',
	);
		
    if ( ! is_array( $settings ) ) {

		$settings = $wplc_acbc_defaults;

	} else {

		$settings = array_merge( $wplc_acbc_defaults, $settings );

		if ( strpos( $settings['wplc_chat_icon'], plugins_url() ) !== false ) {

			$settings['wplc_chat_icon'] = WPLC_PLUGIN_URL . 'images/chaticon.png';

		}

		if ( strpos( $settings['wplc_chat_pic'], plugins_url() ) !== false ) {

			$settings['wplc_chat_pic'] = WPLC_PLUGIN_URL . 'images/picture-for-chat-box.jpg';

		}
	}

	update_option( 'WPLC_ACBC_SETTINGS', $settings );
	

    $admins = get_role('administrator');
    if( $admins !== null ) { $admins->add_cap('wplc_ma_agent'); }


    $uid = get_current_user_id();
    update_user_meta($uid, 'wplc_ma_agent', 1);
    update_user_meta($uid, "wplc_chat_agent_online", time());

    add_option("WPLC_HIDE_CHAT", "true");

    do_action("wplc_activate_hook");
}


/**
 * Activation of the plugin - set the accepting chat variable to true
 * @return void
 * @since  1.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
if (!function_exists("wplc_choose_activate")) { 
	register_activation_hook(__FILE__, 'wplc_choose_activate');
	function wplc_choose_activate( $networkwide ) {
	    $choose_array = get_option("WPLC_CHOOSE_ACCEPTING");
	    $user_id = get_current_user_id();
	    $choose_array[$user_id] = true;
	    wplc_mrg_update_db( $networkwide ); //Run update db
	    update_option("WPLC_CHOOSE_ACCEPTING",$choose_array);
	}
}

/**
 * Deactivate of the plugin - set the accepting chat variable to false
 * @return void
 * @since  1.0.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
if (!function_exists("wplc_choose_deactivate")) { 
	register_deactivation_hook(__FILE__, 'wplc_choose_deactivate');
	function wplc_choose_deactivate() {
	    $choose_array = get_option("WPLC_CHOOSE_ACCEPTING");
	    $user_id = get_current_user_id();
	    $choose_array[$user_id] = true;    
	    update_option("WPLC_CHOOSE_ACCEPTING",$choose_array);

	}
}


function wplc_handle_db() {
    global $wpdb;
    global $wplc_tblname_chats;
    global $wplc_tblname_msgs;
    global $wplc_tblname_offline_msgs;

    $sql = "
        CREATE TABLE " . $wplc_tblname_chats . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          timestamp datetime NOT NULL,
          name varchar(700) NOT NULL,
          email varchar(700) NOT NULL,
          ip varchar(700) NOT NULL,
          status int(11) NOT NULL,
          session varchar(100) NOT NULL,
          url varchar(700) NOT NULL,
          last_active_timestamp datetime NOT NULL,
          agent_id INT(11) NOT NULL,
          other LONGTEXT NOT NULL,
          rel varchar(40) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);




    $sql = '
        CREATE TABLE ' . $wplc_tblname_msgs . ' (
          id int(11) NOT NULL AUTO_INCREMENT,
          chat_sess_id int(11) NOT NULL,
          msgfrom varchar(150) CHARACTER SET utf8 NOT NULL,
          msg LONGTEXT CHARACTER SET utf8 NOT NULL,
          timestamp datetime NOT NULL,
          status INT(3) NOT NULL,
          originates INT(3) NOT NULL,
          other LONGTEXT NOT NULL,
          rel varchar(40) NOT NULL,
          afrom INT(10) NOT NULL,
          ato INT(10) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    @dbDelta($sql);

    /* check for previous versions containing 'from' instead of 'msgfrom' */
    $results = $wpdb->get_results("DESC $wplc_tblname_msgs");
    $founded = 0;
    foreach ($results as $row ) {
        if ($row->Field == "from") {
            $founded++;
        }
    }

    if ($founded>0) { $wpdb->query("ALTER TABLE ".$wplc_tblname_msgs." CHANGE `from` `msgfrom` varchar(150)"); }


    $sql2 = "
        CREATE TABLE " . $wplc_tblname_offline_msgs . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          timestamp datetime NOT NULL,
          name varchar(700) NOT NULL,
          email varchar(700) NOT NULL,
          message varchar(700) NOT NULL,
          ip varchar(700) NOT NULL,
          user_agent varchar(700) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    @dbDelta($sql2);

    add_option("wplc_db_version", WPLC_PLUGIN_VERSION);
    update_option("wplc_db_version", WPLC_PLUGIN_VERSION);
}

function wplc_add_user_stylesheet() {
    $show_chat_contents = wplc_display_chat_contents();

    if($show_chat_contents >= 1){
	    $wplc_settings = get_option( 'WPLC_SETTINGS' );
	    if ( isset( $wplc_settings ) && isset( $wplc_settings['wplc_enable_font_awesome'] ) && '1' === $wplc_settings['wplc_enable_font_awesome'] ) {
		    wp_register_style( 'wplc-font-awesome', plugins_url( '/css/font-awesome.min.css', __FILE__ ) );

		    wp_enqueue_style( 'wplc-font-awesome' );
	    }
        wp_register_style('wplc-style', plugins_url('/css/wplcstyle.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
        wp_enqueue_style('wplc-style');

        if( isset( $wplc_settings['wplc_elem_trigger_id'] ) && trim( $wplc_settings['wplc_elem_trigger_id'] ) !== "" ) {
	    	if( isset( $wplc_settings['wplc_elem_trigger_type'] ) ){
	    		if ( $wplc_settings['wplc_elem_trigger_type'] === "0" ) {
	    			/* class */
	    			$wplc_elem_style_prefix = ".";
	    		} else {
	    			/* ID */
	    			$wplc_elem_style_prefix = "#";
	    		}
	    	}

	        $wplc_elem_inline_style = $wplc_elem_style_prefix.stripslashes( $wplc_settings['wplc_elem_trigger_id'] ).":hover { cursor:pointer; }";
	        wp_add_inline_style( 'wplc-style', stripslashes( $wplc_elem_inline_style ) );
	    }

		// Serve the icon up over HTTPS if needs be
		$icon = plugins_url('images/iconRetina.png', __FILE__);
		$close_icon = plugins_url('images/iconCloseRetina.png', __FILE__);


		if ( isset( $wplc_settings['wplc_settings_bg'] ) ) {
			if ( $wplc_settings['wplc_settings_bg']  == "0" ) { $bg = false; } else { $bg = sanitize_text_field( $wplc_settings['wplc_settings_bg'] ); }
		} else { $bg = "cloudy.jpg"; }
		if ($bg) {
			$bg = plugins_url('images/bg/'.$bg, __FILE__);
			$bg_string = "#wp-live-chat-4 { background:url('$bg') repeat; background-size: cover; }";
		} else { $bg_string = "#wp-live-chat-4 { background-color: #fff; }"; }

		if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ){ $icon = preg_replace('/^http:\/\//', 'https:\/\/', $icon); }
		if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ){ $close_icon = preg_replace('/^http:\/\//', 'https:\/\/', $close_icon); }
		$icon = apply_filters("wplc_filter_chaticon",$icon);

		$close_icon = apply_filters("wplc_filter_chaticon_close",$close_icon);

		$wplc_elem_inline_style = "#wp-live-chat-header { background:url('$icon') no-repeat; background-size: cover; }  #wp-live-chat-header.active { background:url('$close_icon') no-repeat; background-size: cover; } $bg_string";
		wp_add_inline_style( 'wplc-style', stripslashes( $wplc_elem_inline_style ) );



        $wplc_settings = get_option('WPLC_SETTINGS');
        if (isset($wplc_settings['wplc_theme'])) { $wplc_theme = $wplc_settings['wplc_theme']; } else { $wplc_theme = "theme-default"; }


          if($wplc_theme == 'theme-default') {
            wp_register_style('wplc-theme-palette-default', plugins_url('/css/themes/theme-default.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-default');

          }
          else if($wplc_theme == 'theme-1') {
            wp_register_style('wplc-theme-palette-1', plugins_url('/css/themes/theme-1.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-1');

          }
          else if($wplc_theme == 'theme-2') {
            wp_register_style('wplc-theme-palette-2', plugins_url('/css/themes/theme-2.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-2');

          }
          else if($wplc_theme == 'theme-3') {
            wp_register_style('wplc-theme-palette-3', plugins_url('/css/themes/theme-3.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-3');

          }
          else if($wplc_theme == 'theme-4') {
            wp_register_style('wplc-theme-palette-4', plugins_url('/css/themes/theme-4.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-4');

          }
          else if($wplc_theme == 'theme-5') {
            wp_register_style('wplc-theme-palette-5', plugins_url('/css/themes/theme-5.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
            wp_enqueue_style('wplc-theme-palette-5');

          }
          else if($wplc_theme == 'theme-6') {
            /* custom */
            /* handled elsewhere */



          }





          if (isset($wplc_settings['wplc_newtheme'])) { $wplc_newtheme = $wplc_settings['wplc_newtheme']; } else { $wplc_newtheme = "theme-2"; }
          if (isset($wplc_newtheme)) {
            if($wplc_newtheme == 'theme-1') {
              wp_register_style('wplc-theme-classic', plugins_url('/css/themes/classic.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-classic');

            }
            else if($wplc_newtheme == 'theme-2') {
              wp_register_style('wplc-theme-modern', plugins_url('/css/themes/modern.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-modern');

            }
          }

          if ($wplc_settings["wplc_settings_align"] == 1) {
              wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-left.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-position');
          } else if ($wplc_settings["wplc_settings_align"] == 2) {
              wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-right.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-position');
          } else if ($wplc_settings["wplc_settings_align"] == 3) {
              wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-left.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-position');
          } else if ($wplc_settings["wplc_settings_align"] == 4) {
              wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-right.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-position');
          } else {
              wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-right.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
              wp_enqueue_style('wplc-theme-position');
          }

        

        // Gutenberg template styles - user
        wp_register_style( 'wplc-gutenberg-template-styles-user', plugins_url( '/includes/blocks/wplc-chat-box/wplc_gutenberg_template_styles.css', __FILE__ ) );
        wp_enqueue_style( 'wplc-gutenberg-template-styles-user' );

        // GIF integration styles - user
        wp_register_style( 'wplc-gif-integration-user', plugins_url( '/css/wplc_gif_integration.css', __FILE__ ) );
        wp_enqueue_style( 'wplc-gif-integration-user' );
    }
    if(function_exists('wplc_ce_activate')){
        if(function_exists('wplc_ce_load_user_styles')){
            wplc_ce_load_user_styles();
        }
    }
}


add_action( 'init', 'wplc_online_check_script', 10 );
/**
 * Load the JS that allows us to integrate into the WP Heartbeat
 * @return void
 */
function wplc_online_check_script() {
    if (sanitize_text_field( get_the_author_meta( 'wplc_ma_agent', get_current_user_id() ) ) == "1"){
    	$ajax_nonce = wp_create_nonce("wplc");
	    wp_register_script( 'wplc-heartbeat', plugins_url( 'js/wplc_heartbeat.js', __FILE__ ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	    wp_enqueue_script( 'wplc-heartbeat' );
		wp_localize_script( 'wplc-heartbeat', 'wplc_transient_nonce', $ajax_nonce );

		$wplc_ajax_url = apply_filters("wplc_filter_ajax_url", admin_url('admin-ajax.php'));
		wp_localize_script('wplc-heartbeat', 'wplc_ajaxurl', $wplc_ajax_url);
	}
}

/**
 * Heartbeat integrations
 *
 */
add_filter( 'heartbeat_received', 'wplc_heartbeat_receive', 10, 2 );
add_filter( 'heartbeat_nopriv_received', 'wplc_heartbeat_receive', 10, 2 );
function wplc_heartbeat_receive( $response, $data ) {
	if ( array_key_exists('client',$data) && $data['client'] == 'wplc_heartbeat' ) {
	    if (sanitize_text_field( get_the_author_meta( 'wplc_ma_agent', get_current_user_id() ) ) == "1"){
	        update_user_meta(get_current_user_id(), "wplc_chat_agent_online", time());
	        wplc_hook_control_set_transient();
	    }
	}
	return $response;
}



/**
 * Loads the admin stylesheets for the chat dashboard and settings pages
 * @return void
 */
function wplc_add_admin_stylesheet() {
	wp_register_style( 'wplc-ace-styles', plugins_url( '/css/ace.min.css', __FILE__ ) );
	wp_enqueue_style( 'wplc-ace-styles' );

	wp_register_style( 'wplc-fontawesome-iconpicker', plugins_url( '/css/fontawesome-iconpicker.min.css', __FILE__ ) );
	wp_enqueue_style( 'wplc-fontawesome-iconpicker' );
	
    
	$wplc_settings = get_option("WPLC_SETTINGS");
    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1 && (!isset($_GET['action']) || $_GET['action'] !== "history") ){
	    	//Using node, remote styles please
	    	//Using node, remote scripts please
			if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {
				/* Run admin JS on all admin pages */
           		wplc_admin_remote_dashboard_styles();
			} else {
				/* Only run admin JS on the chat dashboard page */
				if ( isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu' && !isset( $_GET['action'] ) ) {
	    			wplc_admin_remote_dashboard_styles();
				}
			}

	    	wp_register_style( 'wplc-admin-remote-addition-styles', plugins_url( '/css/remote_dash_styles.css', __FILE__ ), array(), WPLC_PLUGIN_VERSION );
			wp_enqueue_style( 'wplc-admin-remote-addition-styles' );

    }
	//Special new check to see if we need to add the node history styling
    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1 && isset($_GET['action']) && $_GET['action'] == 'history'){
        wp_register_style( 'wplc-admin-node-history-styles', plugins_url( '/css/node_history_styles.css', __FILE__ ) );
        wp_enqueue_style( 'wplc-admin-node-history-styles' );
    }

    if (isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu' && isset($_GET['action']) && ($_GET['action'] == "ac" || $_GET['action'] == "history" ) ) {
        wp_register_style('wplc-admin-chat-box-style', plugins_url('/css/admin-chat-box-style.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style('wplc-admin-chat-box-style');
    }

    wp_register_style( 'wplc-font-awesome', plugins_url('css/font-awesome.min.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
    wp_enqueue_style( 'wplc-font-awesome' );

	if (isset($_GET['page']) && ($_GET['page'] == 'wplivechat-menu' ||  $_GET['page'] == 'wplivechat-menu-api-keys-page' || $_GET['page'] == 'wplivechat-menu-settings' || $_GET['page'] == 'wplivechat-menu-offline-messages' || $_GET['page'] == 'wplivechat-menu-history' || $_GET['page'] == 'wplivechat-menu-missed-chats' || $_GET['page'] == 'wplivechat-menu-dashboard')) {
        wp_register_style( 'wplc-jquery-ui', plugins_url( '/css/jquery-ui.min.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style( 'wplc-jquery-ui' );

        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-effects-core' );

        // Gutenberg template styles - admin
        wp_register_style( 'wplc-gutenberg-template-styles', plugins_url( '/includes/blocks/wplc-chat-box/wplc_gutenberg_template_styles.css', __FILE__ ) );
        wp_enqueue_style( 'wplc-gutenberg-template-styles' );

        wp_register_style( 'wplc-admin-styles', plugins_url( '/css/admin_styles.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style( 'wplc-admin-styles' );

        // Old admin chat style
		if ((!isset($wplc_settings['wplc_use_node_server'])) || ($wplc_settings['wplc_use_node_server'] != 1)) {
            wp_register_style( 'wplc-chat-style', plugins_url( '/css/chat-style.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
            wp_enqueue_style( 'wplc-chat-style' );

        // New admin chat style
        } else if (isset($wplc_settings['wplc_use_node_server']) && ($wplc_settings['wplc_use_node_server'] == 1)) {
            wp_register_style( 'wplc-admin-chat-style', plugins_url( '/css/admin-chat-style.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
            wp_enqueue_style( 'wplc-admin-chat-style' );
        }
    }

    // Gif Integration styles - admin
    wp_register_style( 'wplc-gif-integration', plugins_url( '/css/wplc_gif_integration.css', __FILE__ ) );
    wp_enqueue_style( 'wplc-gif-integration' );

    // This loads the chat styling on all admin pages as we are using the popout dashboard
    if ( isset( $wplc_settings['wplc_use_node_server'] ) && ( $wplc_settings['wplc_use_node_server'] == 1 ) && ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1') ) {

        wp_register_style( 'wplc-admin-chat-style', plugins_url( '/css/admin-chat-style.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style( 'wplc-admin-chat-style' );
    }

    if (isset($_GET['page']) && $_GET['page'] == "wplivechat-menu-support-page") {
        wp_register_style('fontawesome', plugins_url('css/font-awesome.min.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style('fontawesome');
        wp_register_style('wplc-support-page-css', plugins_url('css/support-css.css', __FILE__ ), false, WPLC_PLUGIN_VERSION );
        wp_enqueue_style('wplc-support-page-css');
    }

    if(isset($_GET['immersive_mode'])){
		wp_add_inline_style( 'wplc-admin-style', "#wpcontent { margin-left: 0px !important;} #wpadminbar, #wpfooter, #adminmenumain {display: none !important;}" );
	}
}

if (isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu-settings') {
    add_action('admin_print_scripts', 'wplc_admin_scripts');
}

/**
 * Loads the admin scripts for the chat dashboard and settings pages
 * @return void
 */
function wplc_admin_scripts() {

$gutenberg_default_html = '<!-- Default HTML -->
<div class="wplc_block">
	<span class="wplc_block_logo">{wplc_logo}</span>
	<span class="wplc_block_text">{wplc_text}</span>
	<span class="wplc_block_icon">{wplc_icon}</span>
</div>';
    
    if (isset($_GET['page']) && $_GET['page'] == "wplivechat-menu-settings") {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_register_script('my-wplc-color', plugins_url('js/jscolor.js', __FILE__), false, '1.4.5', false);
        wp_enqueue_script('my-wplc-color');
        wp_enqueue_script('jquery-ui-tabs');
        wp_register_script('my-wplc-tabs', plugins_url('js/wplc_tabs.js', __FILE__), array('jquery-ui-core'), '', true);
        wp_enqueue_script('my-wplc-tabs');
        wp_enqueue_media();
        wp_register_script('wplc-fontawesome-iconpicker', plugins_url('js/fontawesome-iconpicker.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('wplc-fontawesome-iconpicker');
        wp_register_script('wplc-gutenberg', plugins_url('js/wplc_gutenberg.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('wplc-gutenberg');
        wp_localize_script( 'wplc-gutenberg', 'default_html', $gutenberg_default_html );
    }
}

/**
 * Loads basic version's settings page
 * @return void
 */
function wplc_admin_settings_layout() {
    wplc_settings_page();
}

/**
 * Loads the dashboard page
 * @return void
 */
function wplc_admin_dashboard_layout() {
    include 'includes/dashboard_page.php';
}

add_action("wplc_hook_history_draw_area","wplc_hook_control_history_draw_area",10,1);
/**
 * Display normal history page
 * @param  int   $cid Chat ID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
function wplc_hook_control_history_draw_area($cid) {
    wplc_draw_chat_area($cid);
}

/**
 * What to display for the chat history
 * @param  int   $cid Chat ID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
function wplc_admin_view_chat_history($cid) {
  do_action("wplc_hook_history_draw_area",$cid);
}


add_action( 'wplc_hook_admin_menu_layout_display' , 'wplc_hook_control_history_get_control', 1, 3);
/**
 * Control history GET calls
 * @param  string $action The GET action
 * @param  int    $cid    The chat id
 * @param  int    $aid    AID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@wp-livechat.com>
 */
function wplc_hook_control_history_get_control($action,$cid,$aid) {

  if ($action == 'history') {
      if (!isset($_GET['wplc_history_nonce']) || !wp_verify_nonce($_GET['wplc_history_nonce'], 'wplc_history_nonce')){
          wp_die(__("You do not have permission do perform this action", "wplivechat"));
      }
      wplc_admin_view_chat_history(sanitize_text_field($cid));
  } else if ($action == 'download_history'){
    
  }


}


add_action("wplc_hook_chat_history","wplc_hook_control_chat_history");
/**
 * Renders the chat history content
 * @return string
 */
function wplc_hook_control_chat_history() {

	if (is_admin()) {

	    global $wpdb;
	    global $wplc_tblname_chats;
	    global $wplc_tblname_msgs;

	    if(isset($_GET['wplc_action']) && $_GET['wplc_action'] == 'remove_cid'){
	        if(isset($_GET['cid'])){
	            if(isset($_GET['wplc_confirm'])){
	                //Confirmed - delete

                    if (!isset($_GET['wplc_history_nonce']) || !wp_verify_nonce($_GET['wplc_history_nonce'], 'wplc_history_nonce')){
                          wp_die(__("You do not have permission do perform this action", "wplivechat"));
                    }

	                $delete_sql = "DELETE FROM $wplc_tblname_chats WHERE `id` = '%d'";
                    $delete_sql = $wpdb->prepare($delete_sql, intval($_GET['cid']));

					$delete_messages = "DELETE FROM $wplc_tblname_msgs WHERE `chat_sess_id` = '%d'";
                    $delete_messages = $wpdb->prepare($delete_messages, intval($_GET['cid']));

	                $wplc_was_error = false;

	                $wpdb->query($delete_sql);
	                if ($wpdb->last_error) { $wplc_was_error = true; }
	                $wpdb->query($delete_messages);
	                if ($wpdb->last_error) { $wplc_was_error = true; }

	                if ($wplc_was_error) {
	                    echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>
	                        ".__("Error: Could not delete chat", "wplivechat")."<br>
	                      </div>";
	                } else {
	                     echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'>
	                        ".__("Chat Deleted", "wplivechat")."<br>
	                      </div>";
	                }

	            } else {
	                //Prompt
                    $hist_nonce = wp_create_nonce('wplc_history_nonce');

	                echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>
	                        ".__("Are you sure you would like to delete this chat?", "wplivechat")."<br>
	                        <a class='button' href='?page=wplivechat-menu-history&wplc_action=remove_cid&cid=".sanitize_text_field( $_GET['cid'] )."&wplc_confirm=1&wplc_history_nonce=" . $hist_nonce . "' >".__("Yes", "wplivechat")."</a> <a class='button' href='?page=wplivechat-menu-history'>".__("No", "wplivechat")."</a>
	                      </div>";
	            }
	        }
	    }

		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		$limit = 20; // number of rows in page
		$offset = ( $pagenum - 1 ) * $limit;
		$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM $wplc_tblname_chats" );
		$num_of_pages = ceil( $total / $limit );

		$results = $wpdb->get_results(
	            "
	        SELECT *
	        FROM $wplc_tblname_chats
	        WHERE `name` NOT LIKE 'agent-to-agent chat'
	        ORDER BY `timestamp` DESC
	        LIMIT $limit OFFSET $offset
	      "
	    );
	    echo "
	       <form method=\"post\" >
	        <input type=\"submit\" value=\"".__('Delete History', 'wplivechat')."\" class='button' name=\"wplc-delete-chat-history\" /><br /><br />
	       </form>

	      <table class=\"wp-list-table wplc_list_table widefat fixed \" cellspacing=\"0\">
	  <thead>
	  <tr>
	    <th scope='col' id='wplc_id_colum' class='manage-column column-id sortable desc'  style=''><span>" . __("Date", "wplivechat") . "</span></th>
	                <th scope='col' id='wplc_name_colum' class='manage-column column-name_title sortable desc'  style=''><span>" . __("Name", "wplivechat") . "</span></th>
	                <th scope='col' id='wplc_email_colum' class='manage-column column-email' style=\"\">" . __("Email", "wplivechat") . "</th>
	                <th scope='col' id='wplc_url_colum' class='manage-column column-url' style=\"\">" . __("URL", "wplivechat") . "</th>
	                <th scope='col' id='wplc_status_colum' class='manage-column column-status'  style=\"\">" . __("Status", "wplivechat") . "</th>
	                <th scope='col' id='wplc_action_colum' class='manage-column column-action sortable desc'  style=\"\"><span>" . __("Action", "wplivechat") . "</span></th>
	        </tr>
	  </thead>
	        <tbody id=\"the-list\" class='list:wp_list_text_link'>
	        ";
	    if (!$results) {
	        echo "<tr><td></td><td>" . __("No chats available at the moment", "wplivechat") . "</td></tr>";
	    } else {
	        foreach ($results as $result) {
	            unset($trstyle);
	            unset($actions);

	            $tcid = sanitize_text_field( $result->id );
                $hist_nonce = wp_create_nonce('wplc_history_nonce');


	            $url = admin_url('admin.php?page=wplivechat-menu&action=history&cid=' . $tcid . "&wplc_history_nonce=" . $hist_nonce);
	            $url2 = admin_url('admin.php?page=wplivechat-menu&action=download_history&type=csv&cid=' . $tcid . "&wplc_history_nonce=" . $hist_nonce);
	            $url3 = "?page=wplivechat-menu-history&wplc_action=remove_cid&cid=" . $tcid;
	            $actions = "
	                <a href='$url' class='button' title='".__('View Chat History', 'wplivechat')."' target='_BLANK' id=''><i class='fa fa-eye'></i></a> <a href='$url2' class='button' title='".__('Download Chat History', 'wplivechat')."' target='_BLANK' id=''><i class='fa fa-download'></i></a> <a href='$url3' class='button'><i class='fa fa-trash-o'></i></a>      
	                ";
	            $trstyle = "style='height:30px;'";

	            echo "<tr id=\"record_" . $tcid . "\" $trstyle>";
	            echo "<td class='chat_id column-chat_d'>" . date("Y-m-d H:i:s", current_time( strtotime( $result->timestamp ) ) ) . "</td>";
	            echo "<td class='chat_name column_chat_name' id='chat_name_" . $tcid . "'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "?s=40&d=mm\" /> " . sanitize_text_field($result->name) . "</td>";
	            echo "<td class='chat_email column_chat_email' id='chat_email_" . $tcid . "'><a href='mailto:" . sanitize_text_field($result->email) . "' title='Email " . ".$result->email." . "'>" . sanitize_text_field ($result->email) . "</a></td>";
	            echo "<td class='chat_name column_chat_url' id='chat_url_" . $tcid . "'>" . esc_url($result->url) . "</td>";
	            echo "<td class='chat_status column_chat_status' id='chat_status_" . $tcid . "'><strong>" . wplc_return_status($result->status) . "</strong></td>";
	            echo "<td class='chat_action column-chat_action' id='chat_action_" . $tcid . "'>$actions</td>";
	            echo "</tr>";
	        }
	    }
	    echo "</table>";

		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'wplivechat' ),
			'next_text' => __( '&raquo;', 'wplivechat' ),
			'total' => $num_of_pages,
			'current' => $pagenum
		) );

		if ( $page_links ) {
			echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0;float:none;text-align:center;">' . $page_links . '</div></div>';
		}
	}


}

/**
 * Loads the chat history layout
 * @return string
 */
function wplc_admin_history_layout() {
    wplc_stats("history");
    echo"<div class=\"wrap wplc_wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat History", "wplivechat") . "</h2>";

    do_action("wplc_before_history_table_hook");

    if(function_exists("wplc_ce_activate")){
        wplc_ce_admin_display_history();
    }  else {
      do_action("wplc_hook_chat_history");
    }
}


add_action("wplc_hook_chat_missed","wplc_hook_control_missed_chats",10);
/**
 * Loads missed chats contents
 * @return string
 */
function wplc_hook_control_missed_chats() {
  if (function_exists('wplc_admin_display_missed_chats')) { wplc_admin_display_missed_chats(); }
}

/**
 * Loads the missed chats page wrapper
 * @return string
 */
function wplc_admin_missed_chats() {
    wplc_stats("missed");
    echo "<div class=\"wrap wplc_wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat Missed Chats", "wplivechat") . "</h2>";
    do_action("wplc_hook_chat_missed");
}

add_action("wplc_hook_offline_messages_display","wplc_hook_control_offline_messages_display",10);
/**
 * Loads the offline messages page contents
 * @return string
 */
function wplc_hook_control_offline_messages_display() {
   wplc_admin_display_offline_messages(); 
}

/**
 * Control who should see the offline messages
 * @return void
 */
function wplc_admin_offline_messages() {
    wplc_stats("offline_messages");
    echo"<div class=\"wrap wplc_wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat Offline Messages", "wplivechat") . "</h2>";
    do_action("wplc_hook_offline_messages_display");
}

/**
 * Output the offline messages in an HTML table
 * @return void
 */
function wplc_admin_display_offline_messages() {

    global $wpdb;
    global $wplc_tblname_offline_msgs;

    echo "
        <table class=\"wp-list-table wplc_list_table widefat \" cellspacing=\"0\">
            <thead>
                <tr>
                    <th class='manage-column column-id'><span>" . __("Date", "wplivechat") . "</span></th>
                    <th scope='col' id='wplc_name_colum' class='manage-column column-id'><span>" . __("Name", "wplivechat") . "</span></th>
                    <th scope='col' id='wplc_email_colum' class='manage-column column-id'>" . __("Email", "wplivechat") . "</th>
                    <th scope='col' id='wplc_message_colum' class='manage-column column-id'>" . __("Message", "wplivechat") . "</th>
                    <th scope='col' id='wplc_message_colum' class='manage-column column-id'>" . __("Actions", "wplivechat") . "</th>
                </tr>
            </thead>
            <tbody id=\"the-list\" class='list:wp_list_text_link'>";

	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM $wplc_tblname_offline_msgs" );
	$num_of_pages = ceil( $total / $limit );

    $sql = "SELECT * FROM $wplc_tblname_offline_msgs ORDER BY `timestamp` DESC LIMIT %d OFFSET %d";
    $sql = $wpdb->prepare($sql, $limit, $offset);

    $results = $wpdb->get_results($sql);

    if (!$results) {
        echo "<tr><td></td><td>" . __("You have not received any offline messages.", "wplivechat") . "</td></tr>";
    } else {
        foreach ($results as $result) {
            echo "<tr id=\"record_" . intval($result->id) . "\">";
            echo "<td class='chat_id column-chat_d'>" . sanitize_text_field($result->timestamp) . "</td>";
            echo "<td class='chat_name column_chat_name' id='chat_name_" . intval($result->id) . "'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "?s=30&d=mm\" /> " . sanitize_text_field($result->name) . "</td>";
            echo "<td class='chat_email column_chat_email' id='chat_email_" . intval($result->id) . "'><a href='mailto:" . sanitize_email($result->email) . "' title='Email " . ".$result->email." . "'>" . sanitize_email($result->email) . "</a></td>";
            echo "<td class='chat_name column_chat_url' id='chat_url_" . intval($result->id) . "'>" . nl2br(sanitize_text_field($result->message)) . "</td>";
            echo "<td class='chat_name column_chat_delete'><button class='button wplc_delete_message' title='".__('Delete Message', 'wplivechat')."' class='wplc_delete_message' mid='".intval($result->id)."'><i class='fa fa-times'></i></button></td>";
            echo "</tr>";
        }
    }

    echo "
            </tbody>
        </table>";

	$page_links = paginate_links( array(
		'base' => add_query_arg( 'pagenum', '%#%' ),
		'format' => '',
		'prev_text' => __( '&laquo;', 'wplivechat' ),
		'next_text' => __( '&raquo;', 'wplivechat' ),
		'total' => $num_of_pages,
		'current' => $pagenum
	) );

	if ( $page_links ) {
		echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0;float:none;text-align:center;">' . $page_links . '</div></div>';
	}
}

/**
 * Loads the settings pages
 * @return string
 */
function wplc_settings_page() {
    include 'includes/settings_page.php';
}

/**
 * Updates chat statistics
 * @param  string $sec Specify which array key of the stats you'd like access to
 * @return void
 */
function wplc_stats($sec) {
    $wplc_stats = get_option("wplc_stats");
    if ($wplc_stats) {
        if (isset($wplc_stats[$sec]["views"])) {
            $wplc_stats[$sec]["views"] = $wplc_stats[$sec]["views"] + 1;
            $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        } else {
            $wplc_stats[$sec]["views"] = 1;
            $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
            $wplc_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");
        }


    } else {

        $wplc_stats[$sec]["views"] = 1;
        $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        $wplc_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");


    }
    update_option("wplc_stats",$wplc_stats);

}


add_action("wplc_hook_head","wplc_hook_control_head");
/**
 * Deletes the chat history on submission of POST
 * @return bool
 */
function wplc_hook_control_head() {
    if (isset($_POST['wplc-delete-chat-history'])) {
        wplc_del_history();
    }
}

/**
 * Deletes all chat history
 * @return bool
 */
function wplc_del_history(){
    global $wpdb;
    global $wplc_tblname_chats;
    global $wplc_tblname_msgs;
    $wpdb->query("TRUNCATE TABLE $wplc_tblname_chats");
    $wpdb->query("TRUNCATE TABLE $wplc_tblname_msgs");
}

add_filter("wplc_filter_chat_header_extra_attr","wplc_filter_control_chat_header_extra_attr",10,1);
/**
 * Controls if the chat window should popup or not
 * @param  array $wplc_extra_attr Extra chat data passed
 * @return string
 */
function wplc_filter_control_chat_header_extra_attr($wplc_extra_attr) {
    $wplc_acbc_data = get_option("WPLC_SETTINGS");
    if (isset($wplc_acbc_data['wplc_auto_pop_up'])) { $extr_string = $wplc_acbc_data['wplc_auto_pop_up']; $wplc_extra_attr .= " wplc-auto-pop-up=\"".$extr_string."\""; }

    return $wplc_extra_attr;
}

/**
 * Admin side headers used to save settings
 * @return string
 */
function wplc_head() {
    global $wpdb;

    do_action("wplc_hook_head");


    if (isset($_POST['wplc_save_settings'])) {
		
        if (!isset($_POST['wplc_save_settings_nonce']) || !wp_verify_nonce($_POST['wplc_save_settings_nonce'], 'wplc_save_settings')){
            ?>
            <div class='notice notice-warning wplc_settings_save_notice'>
                <?php _e("You do not have permission to save settings.", "wplivechat"); ?>
            </div>
            <?php

            return false;
        } 
		

        do_action("wplc_hook_admin_settings_save");
		
		$wplc_choose_data = get_option("WPLC_CHOOSE_SETTINGS");
	    if(isset($_POST['wplc_auto_online'])){ $wplc_choose_data['wplc_auto_online'] = sanitize_text_field($_POST['wplc_auto_online']);} else { $wplc_choose_data['wplc_auto_online'] = 0; }
		
		if (isset($_POST['wplc_enable_encryption'])) {$wplc_encrypt_data['wplc_enable_encryption'] = intval($_POST['wplc_enable_encryption']);} else {$wplc_encrypt_data['wplc_enable_encryption'] = 0;}
		
	    if (isset($_POST['wplc_include_on_pages'])) { $wplc_inex_data['wplc_include_on_pages'] = sanitize_text_field($_POST['wplc_include_on_pages']); }
        if (isset($_POST['wplc_exclude_from_pages'])) { $wplc_inex_data['wplc_exclude_from_pages'] = sanitize_text_field($_POST['wplc_exclude_from_pages']); }
        if (isset($_POST['wplc_exclude_post_types']) && ! empty($_POST['wplc_exclude_post_types'])) {foreach ( $_POST['wplc_exclude_post_types'] as $post_type ) { $wplc_inex_data['wplc_exclude_post_types'][] = sanitize_text_field($post_type); } }
        if (isset($_POST['wplc_exclude_home'])) { $wplc_inex_data['wplc_exclude_home'] = sanitize_text_field($_POST['wplc_exclude_home']); }
        if (isset($_POST['wplc_exclude_archive'])) { $wplc_inex_data['wplc_exclude_archive'] = sanitize_text_field($_POST['wplc_exclude_archive']); }
		
        if (isset($_POST['wplc_make_agent'])) { $wplc_inex_data['wplc_make_agent'] = sanitize_text_field($_POST['wplc_make_agent']); }
				
		if (isset($_POST['wplc_quick_response_orderby'])) { $wplc_data['wplc_quick_response_orderby'] = sanitize_text_field($_POST['wplc_quick_response_orderby']); }
		if (isset($_POST['wplc_quick_response_order'])) { $wplc_data['wplc_quick_response_order'] = sanitize_text_field($_POST['wplc_quick_response_order']); }
		
		if ( isset( $_POST['wplc_enable_transcripts'] ) ) {	$wplc_et_data['wplc_enable_transcripts'] = sanitize_text_field( $_POST['wplc_enable_transcripts'] );} else {$wplc_et_data['wplc_enable_transcripts'] = 0;}
		if ( isset( $_POST['wplc_send_transcripts_to'] ) ) {$wplc_et_data['wplc_send_transcripts_to'] = sanitize_text_field( $_POST['wplc_send_transcripts_to'] );	} else {$wplc_et_data['wplc_send_transcripts_to'] = 'user';	}
		if ( isset( $_POST['wplc_send_transcripts_when_chat_ends'] ) ) {$wplc_et_data['wplc_send_transcripts_when_chat_ends'] = sanitize_text_field( $_POST['wplc_send_transcripts_when_chat_ends'] );	} else {$wplc_et_data['wplc_send_transcripts_when_chat_ends'] = 0;}
		
		if ( isset( $_POST['wplc_et_email_header'] ) ) {$wplc_et_data['wplc_et_email_header'] = sanitize_text_field( $_POST['wplc_et_email_header'] );	}
		if ( isset( $_POST['wplc_et_email_footer'] ) ) {$wplc_et_data['wplc_et_email_footer'] = sanitize_text_field( $_POST['wplc_et_email_footer'] );	}
		if ( isset( $_POST['wplc_et_email_body'] ) ) {$wplc_et_data['wplc_et_email_body'] = wp_filter_post_kses( $_POST['wplc_et_email_body'] );}
		
		if ( isset( $_POST['wplc_enable_voice_notes_on_admin'] ) ) {$wplc_data['wplc_enable_voice_notes_on_admin'] = sanitize_text_field( $_POST['wplc_enable_voice_notes_on_admin'] );
		} else {$wplc_data['wplc_enable_voice_notes_on_admin'] = "0";}
		if ( isset( $_POST['wplc_enable_voice_notes_on_visitor'] ) ) {	$wplc_data['wplc_enable_voice_notes_on_visitor'] = sanitize_text_field( $_POST['wplc_enable_voice_notes_on_visitor'] );} else { $wplc_data['wplc_enable_voice_notes_on_visitor'] = "0";}
		
        if (isset($_POST['wplc_settings_align'])) { $wplc_data['wplc_settings_align'] = sanitize_text_field($_POST['wplc_settings_align']); }
        if (isset($_POST['wplc_settings_bg'])) { $wplc_data['wplc_settings_bg'] = sanitize_text_field($_POST['wplc_settings_bg']); }
		if (isset($_POST['wplc_environment'])) { $wplc_data['wplc_environment'] = sanitize_text_field($_POST['wplc_environment']); }
        if (isset($_POST['wplc_settings_fill'])) { $wplc_data['wplc_settings_fill'] = sanitize_text_field($_POST['wplc_settings_fill']); }
        if (isset($_POST['wplc_settings_font'])) { $wplc_data['wplc_settings_font'] = sanitize_text_field($_POST['wplc_settings_font']); }
        if (isset($_POST['wplc_settings_color1'])) { $wplc_data['wplc_settings_color1'] = sanitize_text_field($_POST['wplc_settings_color1']); /* backwards compatibility for pro */ $wplc_data['wplc_settings_fill'] = sanitize_text_field($_POST['wplc_settings_color1']); }
        if (isset($_POST['wplc_settings_color2'])) { $wplc_data['wplc_settings_color2'] = sanitize_text_field($_POST['wplc_settings_color2']); /* backwards compatibility for pro */ $wplc_data['wplc_settings_font'] = sanitize_text_field($_POST['wplc_settings_color2']); }
        if (isset($_POST['wplc_settings_color3'])) { $wplc_data['wplc_settings_color3'] = sanitize_text_field($_POST['wplc_settings_color3']); }
        if (isset($_POST['wplc_settings_color4'])) { $wplc_data['wplc_settings_color4'] = sanitize_text_field($_POST['wplc_settings_color4']); }

        if (isset($_POST['wplc_settings_enabled'])) { $wplc_data['wplc_settings_enabled'] = sanitize_text_field($_POST['wplc_settings_enabled']); }
        if (isset($_POST['wplc_powered_by_link'])) { $wplc_data['wplc_powered_by_link'] = sanitize_text_field($_POST['wplc_powered_by_link']); }
        if (isset($_POST['wplc_auto_pop_up'])) { $wplc_data['wplc_auto_pop_up'] = sanitize_text_field($_POST['wplc_auto_pop_up']); }
        if (isset($_POST['wplc_require_user_info'])) { $wplc_data['wplc_require_user_info'] = sanitize_text_field($_POST['wplc_require_user_info']); } else { $wplc_data['wplc_require_user_info'] = "0";  }
	    if (isset($_POST['wplc_user_default_visitor_name']) && $_POST['wplc_user_default_visitor_name'] != '') { $wplc_data['wplc_user_default_visitor_name'] = sanitize_text_field($_POST['wplc_user_default_visitor_name']); } else { $wplc_data['wplc_user_default_visitor_name'] = __("Guest", "wplivechat"); }
	    if (isset($_POST['wplc_loggedin_user_info'])) { $wplc_data['wplc_loggedin_user_info'] = sanitize_text_field($_POST['wplc_loggedin_user_info']); } else {  $wplc_data['wplc_loggedin_user_info'] = "0"; }
        if (isset($_POST['wplc_user_alternative_text']) && $_POST['wplc_user_alternative_text'] != '') { $wplc_data['wplc_user_alternative_text'] = sanitize_text_field($_POST['wplc_user_alternative_text']); } else { $wplc_data['wplc_user_alternative_text'] = __("Please click 'Start Chat' to initiate a chat with an agent", "wplivechat"); }
        if (isset($_POST['wplc_enabled_on_mobile'])) { $wplc_data['wplc_enabled_on_mobile'] = sanitize_text_field($_POST['wplc_enabled_on_mobile']); } else {  $wplc_data['wplc_enabled_on_mobile'] = "0"; }
        if (isset($_POST['wplc_display_name'])) { $wplc_data['wplc_display_name'] = sanitize_text_field($_POST['wplc_display_name']); }
        if (isset($_POST['wplc_display_to_loggedin_only'])) { $wplc_data['wplc_display_to_loggedin_only'] = sanitize_text_field($_POST['wplc_display_to_loggedin_only']); }
        if (isset($_POST['wplc_redirect_to_thank_you_page'])) { $wplc_data['wplc_redirect_to_thank_you_page'] = sanitize_text_field($_POST['wplc_redirect_to_thank_you_page']); }
        if (isset($_POST['wplc_redirect_thank_you_url'])) { $wplc_data['wplc_redirect_thank_you_url'] = esc_url(str_replace("https:", "", str_replace("http:", "", $_POST['wplc_redirect_thank_you_url']) ) ); }
        if (isset($_POST['wplc_is_gif_integration_enabled'] )){ $wplc_data['wplc_is_gif_integration_enabled'] = sanitize_text_field($_POST['wplc_is_gif_integration_enabled']); }
        if (isset($_POST['wplc_preferred_gif_provider'])) { $wplc_data['wplc_preferred_gif_provider'] = sanitize_text_field($_POST['wplc_preferred_gif_provider']); }
        if (isset($_POST['wplc_giphy_api_key'])) { $wplc_data['wplc_giphy_api_key'] = sanitize_text_field($_POST['wplc_giphy_api_key']); }
        if (isset($_POST['wplc_tenor_api_key'])) { $wplc_data['wplc_tenor_api_key'] = sanitize_text_field($_POST['wplc_tenor_api_key']); }
		$wplc_data['wplc_disable_emojis'] = !empty($_POST['wplc_disable_emojis']);
        $wplc_data['wplc_record_ip_address'] = "0";
        if(isset($_POST['wplc_enable_msg_sound'])){ $wplc_data['wplc_enable_msg_sound'] = sanitize_text_field($_POST['wplc_enable_msg_sound']); } else { $wplc_data['wplc_enable_msg_sound'] = "0"; }
        if(isset($_POST['wplc_enable_visitor_sound'])){ $wplc_data['wplc_enable_visitor_sound'] = sanitize_text_field($_POST['wplc_enable_visitor_sound']); } else { $wplc_data['wplc_enable_visitor_sound'] = "0"; }
	    if(isset($_POST['wplc_enable_font_awesome'])){ $wplc_data['wplc_enable_font_awesome'] = sanitize_text_field($_POST['wplc_enable_font_awesome']); } else { $wplc_data['wplc_enable_font_awesome'] = "0"; }
	    if(isset($_POST['wplc_enable_all_admin_pages'])){ $wplc_data['wplc_enable_all_admin_pages'] = sanitize_text_field($_POST['wplc_enable_all_admin_pages']); } else { $wplc_data['wplc_enable_all_admin_pages'] = "0"; }
        if (isset($_POST['wplc_pro_na'])) { $wplc_data['wplc_pro_na'] = sanitize_text_field($_POST['wplc_pro_na']); }
        if (isset($_POST['wplc_hide_when_offline'])) { $wplc_data['wplc_hide_when_offline'] = sanitize_text_field($_POST['wplc_hide_when_offline']); }
        if (isset($_POST['wplc_pro_chat_email_address'])) { $wplc_data['wplc_pro_chat_email_address'] = sanitize_text_field($_POST['wplc_pro_chat_email_address']); }
        if (isset($_POST['wplc_pro_chat_email_offline_subject'])) { $wplc_data['wplc_pro_chat_email_offline_subject'] = sanitize_text_field($_POST['wplc_pro_chat_email_offline_subject']); }
        if (isset($_POST['wplc_pro_offline1'])) { $wplc_data['wplc_pro_offline1'] = sanitize_text_field($_POST['wplc_pro_offline1']); }
        if (isset($_POST['wplc_pro_offline2'])) { $wplc_data['wplc_pro_offline2'] = sanitize_text_field($_POST['wplc_pro_offline2']); }
        if (isset($_POST['wplc_pro_offline3'])) { $wplc_data['wplc_pro_offline3'] = sanitize_text_field($_POST['wplc_pro_offline3']); }
	    if (isset($_POST['wplc_pro_offline_btn'])) { $wplc_data['wplc_pro_offline_btn'] = sanitize_text_field($_POST['wplc_pro_offline_btn']); }
	    if (isset($_POST['wplc_pro_offline_btn_send'])) { $wplc_data['wplc_pro_offline_btn_send'] = sanitize_text_field($_POST['wplc_pro_offline_btn_send']); }
	    if (isset($_POST['wplc_using_localization_plugin'])){ $wplc_data['wplc_using_localization_plugin'] = sanitize_text_field($_POST['wplc_using_localization_plugin']); }
        if (isset($_POST['wplc_pro_fst1'])) { $wplc_data['wplc_pro_fst1'] = sanitize_text_field($_POST['wplc_pro_fst1']); }
        if (isset($_POST['wplc_pro_fst2'])) { $wplc_data['wplc_pro_fst2'] = sanitize_text_field($_POST['wplc_pro_fst2']); }
        if (isset($_POST['wplc_pro_fst3'])) { $wplc_data['wplc_pro_fst3'] = sanitize_text_field($_POST['wplc_pro_fst3']); }
        if (isset($_POST['wplc_pro_sst1'])) { $wplc_data['wplc_pro_sst1'] = sanitize_text_field($_POST['wplc_pro_sst1']); }
        if (isset($_POST['wplc_pro_sst1_survey'])) { $wplc_data['wplc_pro_sst1_survey'] = sanitize_text_field($_POST['wplc_pro_sst1_survey']); }
        if (isset($_POST['wplc_pro_sst1e_survey'])) { $wplc_data['wplc_pro_sst1e_survey'] = sanitize_text_field($_POST['wplc_pro_sst1e_survey']); }
        if (isset($_POST['wplc_pro_sst2'])) { $wplc_data['wplc_pro_sst2'] = sanitize_text_field($_POST['wplc_pro_sst2']); }
        if (isset($_POST['wplc_pro_tst1'])) { $wplc_data['wplc_pro_tst1'] = sanitize_text_field($_POST['wplc_pro_tst1']); }
        if (isset($_POST['wplc_pro_intro'])) { $wplc_data['wplc_pro_intro'] = sanitize_text_field($_POST['wplc_pro_intro']); }
        if (isset($_POST['wplc_user_enter'])) { $wplc_data['wplc_user_enter'] = sanitize_text_field($_POST['wplc_user_enter']); }
        if (isset($_POST['wplc_text_chat_ended'])) { $wplc_data['wplc_text_chat_ended'] = sanitize_text_field($_POST['wplc_text_chat_ended']); }
        if (isset($_POST['wplc_close_btn_text'])) { $wplc_data['wplc_close_btn_text'] = sanitize_text_field($_POST['wplc_close_btn_text']); }
        if (isset($_POST['wplc_user_welcome_chat'])) { $wplc_data['wplc_user_welcome_chat'] = sanitize_text_field($_POST['wplc_user_welcome_chat']); }
        if (isset($_POST['wplc_welcome_msg'])) { $wplc_data['wplc_welcome_msg'] = sanitize_text_field($_POST['wplc_welcome_msg']); }
		if (isset($_POST['wplc_typing_enabled']) && $_POST['wplc_typing_enabled'] == "1") { $wplc_data['wplc_typing_enabled'] = sanitize_text_field($_POST['wplc_typing_enabled']); } else { $wplc_data['wplc_typing_enabled'] = "0"; }
		if (isset($_POST['wplc_ux_editor'])) { $wplc_data['wplc_ux_editor'] = sanitize_text_field($_POST['wplc_ux_editor']); } else { $wplc_data['wplc_ux_editor'] = "0"; }
		if (isset($_POST['wplc_ux_file_share'])) { $wplc_data['wplc_ux_file_share'] = sanitize_text_field($_POST['wplc_ux_file_share']); } else { $wplc_data['wplc_ux_file_share'] = "0"; }
		if (isset($_POST['wplc_ux_exp_rating'])) { $wplc_data['wplc_ux_exp_rating'] = sanitize_text_field($_POST['wplc_ux_exp_rating']); } else { $wplc_data['wplc_ux_exp_rating'] = "0"; }
		if (isset($_POST['wplc_disable_initiate_chat']) && $_POST['wplc_disable_initiate_chat'] == "1") { $wplc_data['wplc_disable_initiate_chat'] = sanitize_text_field($_POST['wplc_disable_initiate_chat']); } else { $wplc_data['wplc_disable_initiate_chat'] = "0"; }		
		if (isset($_POST['wplc_pro_name'])) { $wplc_acbc_data['wplc_chat_name'] = sanitize_text_field($_POST['wplc_pro_name']); }
	    if (isset($_POST['wplc_use_wp_name'])) { $wplc_acbc_data['wplc_use_wp_name'] = sanitize_text_field($_POST['wplc_use_wp_name']); } else { $wplc_acbc_data['wplc_use_wp_name'] = "0"; }
	    if (isset($_POST['wplc_upload_pic'])) { $wplc_acbc_data['wplc_chat_pic'] = esc_url(base64_decode($_POST['wplc_upload_pic'])); }
	    if (isset($_POST['wplc_upload_logo'])) { $wplc_acbc_data['wplc_chat_logo'] = esc_url(base64_decode($_POST['wplc_upload_logo'])); }                
	    if (isset($_POST['wplc_upload_icon'])) { $wplc_acbc_data['wplc_chat_icon'] = esc_url(base64_decode($_POST['wplc_upload_icon'])); }
	    if (isset($_POST['wplc_pro_delay'])) { $wplc_acbc_data['wplc_chat_delay'] = sanitize_text_field($_POST['wplc_pro_delay']); }
	    if (isset($_POST['wplc_pro_chat_notification'])) { $wplc_acbc_data['wplc_pro_chat_notification'] = sanitize_text_field($_POST['wplc_pro_chat_notification']); }
	    if (isset($_POST['wplc_pro_chat_email_address'])) { $wplc_acbc_data['wplc_pro_chat_email_address'] = sanitize_text_field($_POST['wplc_pro_chat_email_address']); }
	    if (isset($_POST['wplc_social_fb'])) { $wplc_acbc_data['wplc_social_fb'] = str_replace("https:", "", esc_url($_POST['wplc_social_fb']) ); }
    	if (isset($_POST['wplc_social_tw'])) { $wplc_acbc_data['wplc_social_tw'] = str_replace("https:", "", esc_url($_POST['wplc_social_tw']) ); }
	    if (isset($_POST['wplc_ringtone'])) { $wplc_data['wplc_ringtone'] = str_replace("https:", "", sanitize_text_field($_POST['wplc_ringtone']) ); }
	    if (isset($_POST['wplc_messagetone'])) { $wplc_data['wplc_messagetone'] = str_replace("https:", "", sanitize_text_field($_POST['wplc_messagetone']) ); }
        if(isset($_POST['wplc_animation'])){ $wplc_data['wplc_animation'] = sanitize_text_field($_POST['wplc_animation']); }
        if(isset($_POST['wplc_theme'])){ $wplc_data['wplc_theme'] = sanitize_text_field($_POST['wplc_theme']); }
        if(isset($_POST['wplc_newtheme'])){ $wplc_data['wplc_newtheme'] = sanitize_text_field($_POST['wplc_newtheme']); }
        if(isset($_POST['wplc_elem_trigger_action'])){ $wplc_data['wplc_elem_trigger_action'] = sanitize_text_field($_POST['wplc_elem_trigger_action']); } else{ $wplc_data['wplc_elem_trigger_action'] = "0"; }
        if(isset($_POST['wplc_elem_trigger_type'])){ $wplc_data['wplc_elem_trigger_type'] = sanitize_text_field($_POST['wplc_elem_trigger_type']); } else { $wplc_data['wplc_elem_trigger_type'] = "0";}
        if(isset($_POST['wplc_elem_trigger_id'])){ $wplc_data['wplc_elem_trigger_id'] = sanitize_text_field($_POST['wplc_elem_trigger_id']); } else { $wplc_data['wplc_elem_trigger_id'] = ""; }
		
		if (isset($_POST['wplc_node_disable_typing_preview'])) { 
        	$wplc_data['wplc_node_disable_typing_preview'] = sanitize_text_field($_POST['wplc_node_disable_typing_preview']); 
    	} else {
			$wplc_data['wplc_node_disable_typing_preview'] = '0';
    	}

        if(isset($_POST['wplc_agent_select']) && $_POST['wplc_agent_select'] != "") {
            $user_array = get_users(array(
                'meta_key' => 'wplc_ma_agent',
            ));
            if ($user_array) {
                foreach ($user_array as $user) {
                    $uid = $user->ID;
                    $wplc_ma_user = new WP_User( $uid );
                    $wplc_ma_user->remove_cap( 'wplc_ma_agent' );
                    delete_user_meta($uid, "wplc_ma_agent");
                    delete_user_meta($uid, "wplc_chat_agent_online");
                }
            }


            $uid = intval($_POST['wplc_agent_select']);
            $wplc_ma_user = new WP_User( $uid );
            $wplc_ma_user->add_cap( 'wplc_ma_agent' );
            update_user_meta($uid, "wplc_ma_agent", 1);
            update_user_meta($uid, "wplc_chat_agent_online", time());

        }


        if(isset($_POST['wplc_ban_users_ip'])){
            $wplc_banned_ip_addresses = explode('<br />', nl2br(sanitize_text_field($_POST['wplc_ban_users_ip'])));
            foreach($wplc_banned_ip_addresses as $key => $value) {
                $data[$key] = trim($value);
            }
            $wplc_banned_ip_addresses = maybe_serialize($data);

            update_option('WPLC_BANNED_IP_ADDRESSES', $wplc_banned_ip_addresses);
        }

        if( isset( $_POST['wplc_show_date'] ) ){ $wplc_data['wplc_show_date'] = '1'; } else { $wplc_data['wplc_show_date'] = '0'; }
        if( isset( $_POST['wplc_show_time'] ) ){ $wplc_data['wplc_show_time'] = '1'; } else { $wplc_data['wplc_show_time'] = '0'; }

        if( isset( $_POST['wplc_show_name'] ) ){ $wplc_data['wplc_show_name'] = '1'; } else { $wplc_data['wplc_show_name'] = '0'; }
        if( isset( $_POST['wplc_show_avatar'] ) ){ $wplc_data['wplc_show_avatar'] = '1'; } else { $wplc_data['wplc_show_avatar'] = '0'; }
        $wplc_data = apply_filters("wplc_settings_save_filter_hook", $wplc_data);

        if (isset($_POST['wplc_user_no_answer'])) { $wplc_data["wplc_user_no_answer"] = sanitize_text_field($_POST['wplc_user_no_answer']); } else { $wplc_data["wplc_user_no_answer"] = __("There is No Answer. Please Try Again Later.", "wplivechat"); }
		
		if(isset($_POST['wplc_pro_auto_first_response_chat_msg'])){
			$wplc_data['wplc_pro_auto_first_response_chat_msg'] = sanitize_text_field($_POST['wplc_pro_auto_first_response_chat_msg']);
		} else {
			$wplc_data['wplc_pro_auto_first_response_chat_msg'] = "";
		}
		
		$wplc_data = apply_filters("wplc_pro_setting_save_filter", $wplc_data);
		
		update_option('WPLC_ACBC_SETTINGS', $wplc_acbc_data);
        update_option('WPLC_SETTINGS', $wplc_data);
		update_option('WPLC_CHOOSE_SETTINGS', $wplc_choose_data);
		update_option('WPLC_ENCRYPT_SETTINGS', $wplc_encrypt_data);
		update_option('WPLC_INEX_SETTINGS', $wplc_inex_data);
		update_option('WPLC_ET_SETTINGS', $wplc_et_data );
		
		
		
        if (isset($_POST['wplc_hide_chat'])) {
            update_option("WPLC_HIDE_CHAT", true);
        } else {
          update_option("WPLC_HIDE_CHAT", false);
        }


        $wplc_advanced_settings = array();
        if (isset($_POST['wplc_iterations'])) { $wplc_advanced_settings['wplc_iterations'] = sanitize_text_field($_POST['wplc_iterations']); }
		if (isset($_POST['wplc_delay_between_loops'])) { $wplc_advanced_settings['wplc_delay_between_loops'] = sanitize_text_field($_POST['wplc_delay_between_loops']); }
		update_option("wplc_advanced_settings",$wplc_advanced_settings);

        add_action( 'admin_notices', 'wplc_save_settings_action' );
    }
    

    if( isset( $_GET['override'] ) && $_GET['override'] == '1' ){
    	update_option( "WPLC_V8_FIRST_TIME", false);
    }
}

function wplc_save_settings_action() { ?>
    <div class='notice notice-success updated wplc_settings_save_notice'>
		<?php _e("Your settings have been saved.", "wplivechat"); ?>
    </div>
<?php }

add_action('wp_logout', 'wplc_logout');
/**
 * Deletes the chat transient when a user logs out
 * @return bool
 */
function wplc_logout() {
    delete_transient('wplc_is_admin_logged_in');
}



/**
 * Error checks used to ensure the user's resources meet the plugin's requirements
 */
if(isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu-settings'){
    if(is_admin()){
    	
    	// Only show these warning messages to Legacy users as they will be affected, not Node users.
    	$wplc_settings = get_option("WPLC_SETTINGS");
    	if (isset( $wplc_settings['wplc_use_node_server'] ) && intval( $wplc_settings['wplc_use_node_server'] ) == 1 ) { } else {

	        $wplc_error_count = 0;
	        $wplc_admin_warnings = "<div class='error'>";
	        if(!function_exists('set_time_limit')){
	            $wplc_admin_warnings .= "
	                <p>".__("WPLC: set_time_limit() is not enabled on this server. You may experience issues while using WP Live Chat Support as a result of this. Please get in contact your host to get this function enabled.", "wplivechat")."</p>
	            ";
	            $wplc_error_count++;
	        }
	        if(ini_get('safe_mode')){
	            $wplc_admin_warnings .= "
	                <p>".__("WPLC: Safe mode is enabled on this server. You may experience issues while using WP Live Chat Support as a result of this. Please contact your host to get safe mode disabled.", "wplivechat")."</p>
	            ";
	            $wplc_error_count++;
	        }
	        $wplc_admin_warnings .= "</div>";
	        if($wplc_error_count > 0){
	            echo $wplc_admin_warnings;
	        }
	    }
    }
}

/**
 * Loads the contents of the support menu item
 * @return string
 */
function wplc_support_menu() {
        wplc_stats("support");
?>
    <h1><?php _e("WP Live Chat Support","wplivechat"); ?></h1>
    <div class="wplc_row">
        <div class='wplc_row_col' style='background-color:#FFF;'>
            <h2><i class="fa fa-exclamation-circle fa-2x"></i> <?php _e("Troubleshooting","wplivechat"); ?></h2>
            <hr />
            <p><?php _e("WP Live Chat Support  has a diverse and wide range of features which may, from time to time, run into conflicts with the thousands of themes and other plugins on the market.", "wplivechat"); ?></p>
            <p><strong><?php _e("Common issues:","wplivechat"); ?></strong></p>
            <ul>
                <li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/my-api-key-says-invalid/' target='_BLANK' title='<?php _e("My API Key says ‘Invalid’","wplivechat"); ?>'><?php _e("My API Key says ‘Invalid’","wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/my-chat-box-is-not-showing/' target='_BLANK' title='<?php _e("My Chat Box is Not Showing","wplivechat"); ?>'><?php _e("My Chat Box is Not Showing","wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/not-receiving-notifications-of-new-chats/' target='_BLANK' title='<?php _e("Not Receiving Notifications of New Chats","wplivechat"); ?>'><?php _e("Not Receiving Notifications of New Chats","wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/messages-only-show-when-i-refresh/' target='_BLANK' title='<?php _e("Messages Only Show When I Refresh","wplivechat"); ?>'><?php _e("Messages Only Show When I Refresh","wplivechat"); ?></a></li>
				<li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/the-chat-box-never-goes-offline/' target='_BLANK' title='<?php _e("The Chat Box Never Goes Offline","wplivechat"); ?>'><?php _e("The Chat Box Never Goes Offline","wplivechat"); ?></a></li>
				<li><a href='https://wp-livechat.com/docs/troubleshooting/quickview/error-chat-has-already-been-answered/' target='_BLANK' title='<?php _e("Error: Chat has already been Answered","wplivechat"); ?>'><?php _e("Error: Chat has already been Answered","wplivechat"); ?></a></li>
            </ul>
        </div>
        <div class='wplc_row_col' style='background-color:#FFF;'>
            <h2><i class="fa fa-bullhorn fa-2x"></i> <?php _e("Support","wplivechat"); ?></h2>
            <hr />
            <p><?php _e("Still need help? Use one of these links below.","wplivechat"); ?></p>
            <ul>
                <li><a href='https://wp-livechat.com/docs/' target='_BLANK' title='<?php _e("Documentation","wplivechat"); ?>'><?php _e("Documentation","wplivechat"); ?></a></li>
            </ul>
        </div>
    </div>
<?php
}
if (!function_exists("wplc_ic_initiate_chat_button")) {
    add_action('admin_enqueue_scripts', 'wp_button_pointers_load_scripts');
}
/**
 * Displays the pointers on the live chat dashboard for the initiate chat functionality
 * @param  string $hook returns the page name we're on
 * @return string       contents ofthe pointers and their scripts
 */
function wp_button_pointers_load_scripts($hook) {
	$wplcrun = false;

	$wplc_settings = get_option("WPLC_SETTINGS");
	if ( isset( $wplc_settings['wplc_enable_all_admin_pages'] ) && $wplc_settings['wplc_enable_all_admin_pages'] === '1' ) {
		/* Run admin JS on all admin pages */
		$wplcrun = true;


	} else {

		if( $hook === 'toplevel_page_wplivechat-menu') { $wplcrun = true; } // stop if we are not on the right page
	}


	if ( $wplcrun ) {




	    $pointer_localize_strings = array(
	    "initiate" => "<h3>".__("Initiate Chats","wplivechat")."</h3>",
	    "chats" => "<h3>".__("Multiple Chats","wplivechat")."</h3>",
	    "agent_info" => "<h3>".__("Add unlimited agents","wplivechat")."</h3>",
	    "transfer" => "<h3>".__("Transfer Chats","wplivechat")."</h3>",
	    "direct_to_page" => "<h3>".__("Direct User To Page","wplivechat")."</h3>"
	    );


	    wp_enqueue_style( 'wp-pointer' );
	    wp_enqueue_script( 'wp-pointer' );
	    wp_register_script('wplc-user-admin-pointer', plugins_url('/js/wplc-admin-pointers.js', __FILE__), array('wp-pointer'), WPLC_PLUGIN_VERSION);
	    wp_enqueue_script('wplc-user-admin-pointer');
	    wp_localize_script('wplc-user-admin-pointer', 'pointer_localize_strings', $pointer_localize_strings);
	}

}

add_filter( 'admin_footer_text', 'wplc_footer_mod' );
/**
 * Adds the WP Live Chat Support footer contents to the relevant pages
 * @param  string $footer_text current footer text available to us
 * @return string              footer contents with our branding in it
 */
function wplc_footer_mod( $footer_text ) {
    if (isset($_GET['page']) && ($_GET['page'] == 'wplivechat-menu' || $_GET['page'] == 'wplivechat-menu-settings' || $_GET['page'] == 'wplivechat-menu-offline-messages' || $_GET['page'] == 'wplivechat-menu-history')) {
        $footer_text_mod = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">WP Live Chat Support</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'wplivechat' ),
            'https://wp-livechat.com/?utm_source=plugin&utm_medium=link&utm_campaign=footer',
            'https://wordpress.org/support/view/plugin-reviews/wp-live-chat-support?filter=5#postform'
        );

        return str_replace( '</span>', '', $footer_text ) . ' | ' . $footer_text_mod;
    } else {
        return $footer_text;
    }

}

add_filter("wplc_filter_admin_long_poll_chat_loop_iteration","wplc_filter_control_wplc_admin_long_poll_chat_iteration", 1, 3);
/**
 * Alters the admin's long poll chat iteration
 * @param  array $array     current chat data available to us
 * @param  array $post_data current post data available to us
 * @param  int 	 $i         count for each chat available
 * @return array            additional contents added to the chat data
 */
function wplc_filter_control_wplc_admin_long_poll_chat_iteration($array,$post_data,$i) {
  if(isset($post_data['action_2']) && $post_data['action_2'] == "wplc_long_poll_check_user_opened_chat"){
      $chat_status = wplc_return_chat_status(sanitize_text_field($post_data['cid']));
      if(intval($chat_status) == 3){
          $array['action'] = "wplc_user_open_chat";
      }
  } else {

  	  if ($post_data['first_run'] === "true") {
  	  	/* get the chat messages for the first run */
  	  	$array['chat_history'] = wplc_return_chat_messages($post_data['cid'], false, true, false, false, 'array', false);
  	  	$array['action'] = "wplc_chat_history";

  	  } else {

	      $new_chat_status = wplc_return_chat_status(sanitize_text_field($post_data['cid']));
	      if($new_chat_status != $post_data['chat_status']){
	          $array['chat_status'] = $new_chat_status;
	          $array['action'] = "wplc_update_chat_status";
	      }
	      $new_chat_message = wplc_return_admin_chat_messages(sanitize_text_field($post_data['cid']));

	      if($new_chat_message){

	          $array['chat_message'] = $new_chat_message;
	          $array['action'] = "wplc_new_chat_message";
	      }
	  }
  }

  return $array;
}



/**
 * Returns chat data specific to a chat ID
 * @param  int 		$cid  Chat ID
 * @param  string 	$line Line number the function is called on
 * @return array    	  Contents of the chat based on the ID provided
 */
function wplc_get_chat_data($cid,$line = false) {
	$result = apply_filters("wplc_filter_get_chat_data",null,$cid);
	return $result;
}

add_filter( "wplc_filter_get_chat_data", "wplc_get_local_chat_data", 10, 2 );
function wplc_get_local_chat_data($result, $cid) {
  	global $wpdb;
  	global $wplc_tblname_chats;

  	$sql = "SELECT * FROM $wplc_tblname_chats WHERE `id` = '%d' LIMIT 1";
    $sql = $wpdb->prepare($sql, intval($cid));
  	$results = $wpdb->get_results($sql);
  	if (isset($results[0])) { $result = $results[0]; } else {  $result = null; }

  	return $result;

}

/**
 * Returns chat messages specific to a chat ID
 * @param  int 		$cid  Chat ID
 * @return array 		  Chat messages based on the ID provided
 */
function wplc_get_chat_messages($cid, $only_read_messages = false, $wplc_settings = false) {
  global $wpdb;
  global $wplc_tblname_msgs;

  if (!$wplc_settings) {
      $wplc_settings = get_option("WPLC_SETTINGS");
  }

  /**
   * Identify if the user is using the node server and if they are, display all messages. Otherwise display read only messages (non-node users)
   */
  if (isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == '1') {

          $sql = "
            SELECT * FROM (
                SELECT *
                FROM $wplc_tblname_msgs
                WHERE `chat_sess_id` = '$cid'
                ORDER BY `timestamp` DESC LIMIT 100
            ) sub 
            ORDER BY `timestamp` ASC
            ";
    } else {
        if ($only_read_messages) {
        // only show read messages
              $sql =
                "
                SELECT * FROM (
                    SELECT *
                    FROM $wplc_tblname_msgs
                    WHERE `chat_sess_id` = '$cid' AND `status` = 1
                    ORDER BY `timestamp` DESC LIMIT 100
                ) sub 
                ORDER BY `timestamp` ASC
                ";
        } else {
            $sql =
                "
                SELECT * FROM (
                    SELECT *
                    FROM $wplc_tblname_msgs
                    WHERE `chat_sess_id` = '$cid'
                    ORDER BY `timestamp` DESC LIMIT 100
                ) sub 
                ORDER BY `timestamp` ASC
                ";
        }

    }
    $results = $wpdb->get_results($sql);

  if (isset($results[0])) {  } else {  $results = null; }
  $results = apply_filters("wplc_filter_get_chat_messages",$results,$cid);

  if ($results == "null") {
    return false;
  } else {
    return $results;
  }
}

add_action('admin_init', 'wplc_admin_download_chat_history');
/**
 * Downloads the chat history and adds it to a CSV file
 * @return file
 */
function wplc_admin_download_chat_history(){
	if(!is_user_logged_in() || !get_user_meta(get_current_user_id(), 'wplc_ma_agent', true) ){
	    return;
	}

	if (isset($_GET['action']) && $_GET['action'] == "download_history") {

        global $wpdb;   

        if (!isset($_GET['wplc_history_nonce']) || !wp_verify_nonce($_GET['wplc_history_nonce'], 'wplc_history_nonce')){
              wp_die(__("You do not have permission do perform this action", "wplivechat"));
        }

        $chat_id = sanitize_text_field( $_GET['cid'] );
        $fileName = 'live_chat_history_'.$chat_id.'.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        global $wpdb;
	    global $wplc_tblname_msgs;

	    $results = $wpdb->get_results(
	        "
	        SELECT *
	        FROM $wplc_tblname_msgs
	        WHERE `chat_sess_id` = '$chat_id'
	        ORDER BY `timestamp` ASC
	        LIMIT 0, 100
	        "
	    );

	    $fields[] = array(
	        'id' => __('Chat ID', 'wplivechat'),
	        'msgfrom' => __('From', 'wplivechat'),
	        'msg' => __('Message', 'wplivechat'),
	        'time' => __('Timestamp', 'wplivechat'),
	        'orig' => __('Origin', 'wplivechat'),
	    );

	    foreach ($results as $result => $key) {
	        if($key->originates == 2){
	            $user = __('user', 'wplivechat');
	        } else {
	            $user = __('agent', 'wplivechat');
	        }

	        $fields[] = array(
	            'id' => $key->chat_sess_id,
	            'msgfrom' => $key->msgfrom,
	            'msg' => apply_filters("wplc_filter_message_control_out",$key->msg),
	            'time' => $key->timestamp,
	            'orig' => $user,
	        );
	    }

        foreach($fields as $field){
	    	fputcsv($fh, $field, ",", '"');
	    }
        // Close the file
        fclose($fh);
        // Make sure nothing else is sent, our file is done
        exit;

    }
}

/**
 * Retrieves the data to start downloadling the chat history
 * @param  string $type Chat history output type
 * @param  string $cid  Chat ID
 * @return void
 */
function wplc_admin_download_history($type, $cid){
  	if(!is_user_logged_in() || !get_user_meta(get_current_user_id(), 'wplc_ma_agent', true) ){
	    return;
	}

    global $wpdb;
    global $wplc_tblname_msgs;

    $results = $wpdb->get_results($wpdb->prepare(
        "
        SELECT *
        FROM $wplc_tblname_msgs
        WHERE `chat_sess_id` = '%d'
        ORDER BY `timestamp` ASC
        LIMIT 0, 100
        "
    	, intval($cid))
    );

    $fields[] = array(
        'id' => __('Chat ID', 'wplivechat'),
        'msgfrom' => __('From', 'wplivechat'),
        'msg' => __('Message', 'wplivechat'),
        'time' => __('Timestamp', 'wplivechat'),
        'orig' => __('Origin', 'wplivechat'),
    );

    foreach ($results as $key) {
        if($key->originates == 2){
            $user = __('user', 'wplivechat');
        } else {
            $user = __('agent', 'wplivechat');
        }

        $fields[] = array(
            'id' => $key->chat_sess_id,
            'msgfrom' => $key->msgfrom,
            'msg' => apply_filters("wplc_filter_message_control_out",$key->msg),
            'time' => $key->timestamp,
            'orig' => $user,
        );
    }

    ob_end_clean();

    wplc_convert_to_csv($fields, 'live_chat_history_'.$cid.'.csv', ',');

    exit();
}

/**
 * Converts contents into a CSV file
 * @param  string $in  Contents of file, array of arrays
 * @param  string $out Output of file
 * @param  string $del Delimiter for content
 * @return void
 */
function wplc_convert_to_csv($in, $out, $del) {
  $f = fopen('php://memory', 'w');

  foreach ($in as $arr) {
      wplc_fputcsv_eol($f, $arr, $del, "\r\n");
  }
  fseek($f, 0);
  header('Content-Type: application/csv');
  header('Content-Disposition: attachement; filename="' . $out . '";');
  fpassthru($f);
}

/**
 * Parses content to add to a CSV file
 * @param  string $fp    The open file
 * @param  array $array The content to be added to the file
 * @param  string $del   Delimiter to use in the file
 * @param  string $eol   Content to be written to the file
 * @return void
 */
function wplc_fputcsv_eol($fp, $array, $del, $eol) {
  fputcsv($fp, $array, $del);
  if("\n\r" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
    fwrite($fp, $eol);
  }
}

/**
 * Adds an API key notice in the plugin's page
 * @return string
 */
function wplc_plugin_row_invalid_api() {
  echo '<tr class="active"><td>&nbsp;</td><td colspan="2" style="color:red;">
    &nbsp; &nbsp; '.__('Your API Key is Invalid. You are not eligible for future updates. Please enter your API key <a href="admin.php?page=wplivechat-menu-api-keys-page">here</a>.','wplivechat').'
    </td></tr>';
}

/**
 * Hides the chat when offline
 * @return int Incremented number if any agents have logged in
 */
function wplc_hide_chat_when_offline(){
    $wplc_settings = get_option("WPLC_SETTINGS");

    $hide_chat = 0;
    if (isset($wplc_settings['wplc_hide_when_offline']) && $wplc_settings['wplc_hide_when_offline'] == 1) {
        /* Hide the window when its offline */
        $logged_in = apply_filters("wplc_loggedin_filter",false);
        if (!$logged_in) {
            $hide_chat++;
        }
    } else {
        $hide_chat = 0;
    }
    return $hide_chat;
}

/**
 * Checks all strings that are added to the chat window
 * @return void
 */
function wplc_string_check() {
  $wplc_settings = get_option("WPLC_SETTINGS");

	if (!isset($wplc_settings['wplc_pro_na'])) { $wplc_settings["wplc_pro_na"] = __("Chat offline. Leave a message", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_intro'])) { $wplc_settings["wplc_pro_intro"] = __("Hello. Please input your details so that I may help you.", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_offline1'])) { $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_offline2'])) { $wplc_settings["wplc_pro_offline2"] =  __("Sending message...", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_offline3'])) { $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_offline_btn']) || (isset($wplc_settings['wplc_pro_offline_btn']) && $wplc_settings['wplc_pro_offline_btn'] == "")) { $wplc_settings["wplc_pro_offline_btn"] = __("Leave a message", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_offline_btn_send']) || (isset($wplc_settings['wplc_pro_offline_btn_send']) && $wplc_settings['wplc_pro_offline_btn_send'] == "")) { $wplc_settings["wplc_pro_offline_btn_send"] = __("Send message", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_fst1'])) { $wplc_settings["wplc_pro_fst1"] = __("Questions?", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_fst2'])) { $wplc_settings["wplc_pro_fst2"] = __("Chat with us", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_fst3'])) { $wplc_settings["wplc_pro_fst3"] = __("Start live chat", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_sst1'])) { $wplc_settings["wplc_pro_sst1"] = __("Start Chat", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_sst1_survey'])) { $wplc_settings["wplc_pro_sst1_survey"] = __("Or chat to an agent now", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_sst1e_survey'])) { $wplc_settings["wplc_pro_sst1e_survey"] = __("Chat ended", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_sst2'])) { $wplc_settings["wplc_pro_sst2"] = __("Connecting. Please be patient...", "wplivechat"); }
	if (!isset($wplc_settings['wplc_pro_tst1'])) { $wplc_settings["wplc_pro_tst1"] = __("Reactivating your previous chat...", "wplivechat"); }
	if (!isset($wplc_settings['wplc_user_welcome_chat'])) { $wplc_settings["wplc_user_welcome_chat"] = __("Welcome. How may I help you?", "wplivechat"); }
	if (!isset($wplc_settings['wplc_welcome_msg'])) { $wplc_settings['wplc_welcome_msg'] = __("Please standby for an agent. While you wait for the agent you may type your message.","wplivechat"); }
	if (!isset($wplc_settings['wplc_user_enter'])) { $wplc_settings["wplc_user_enter"] = __("Press ENTER to send your message", "wplivechat"); }
	if (!isset($wplc_settings['wplc_close_btn_text'])) { $wplc_settings["wplc_close_btn_text"] = __("close", "wplivechat"); }

  update_option("WPLC_SETTINGS",$wplc_settings);

}

add_filter("wplc_filter_typing_control_div","wplc_filter_control_return_chat_response_box_before",2,1);
function wplc_filter_control_return_chat_response_box_before($string) {
    $string = $string. "<div class='typing_indicator wplc-color-2 wplc-color-bg-1'></div>";

    return $string;
}
add_filter("wplc_filter_typing_control_div_theme_2","wplc_filter_control_return_chat_response_box_before_theme2",2,1);
function wplc_filter_control_return_chat_response_box_before_theme2($string) {
    $string = $string. "<div class='typing_indicator wplc-color-2 wplc-color-bg-1'></div>";

    return $string;
}


add_action("wplc_hook_admin_settings_main_settings_after","wplc_hook_control_admin_settings_chat_box_settings_after",2);
/**
 * Adds the settings to allow the user to change their server environment variables
 * @return sring */
function wplc_hook_control_admin_settings_chat_box_settings_after() {
	$wplc_settings = get_option("WPLC_SETTINGS");
	if (isset($wplc_settings["wplc_environment"])) { $wplc_environment[intval($wplc_settings["wplc_environment"])] = "SELECTED"; }
	?>
	<h4><?php _e("Advanced settings", "wplivechat") ?></h4>
	<table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
          <?php do_action("wplc_advanced_settings_above_performance", $wplc_settings); ?>
      </table>
	<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
          <tr>
          	<td colspan='2'>
          		<p><em><small><?php _e("Only change these settings if you are experiencing performance issues.","wplivechat"); ?></small></em></p>
          	</td>
          </tr>
          </tr>
          <?php do_action("wplc_advanced_settings_settings"); ?>
          <tr>
              <td valign='top'>
                  <?php _e("What type of environment are you on?","wplivechat"); ?>
              </td>
              <td valign='top'>
                  <select name='wplc_environment' id='wplc_environment'>
                    <option value='1' <?php if (isset($wplc_environment[1])) { echo $wplc_environment[1]; } ?>><?php _e("Shared hosting - low level plan","wplivechat"); ?></option>
                    <option value='2' <?php if (isset($wplc_environment[2])) { echo $wplc_environment[2]; } ?>><?php _e("Shared hosting - normal plan","wplivechat"); ?></option>
                    <option value='3' <?php if (isset($wplc_environment[3])) { echo $wplc_environment[3]; } ?>><?php _e("VPS","wplivechat"); ?></option>
                    <option value='4' <?php if (isset($wplc_environment[4])) { echo $wplc_environment[4]; } ?>><?php _e("Dedicated server","wplivechat"); ?></option>
                  </select>
              </td>
          </tr>
          <tr>
              <td valign='top'>
                  <?php _e("Long poll setup","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Only change these if you are an experienced developer or if you have received these figures from the WP Live Chat Support team.", "wplivechat") ?>"></i>
              </td>
              <td valign='top'>
                <?php $wplc_advanced_settings = get_option("wplc_advanced_settings");?>
                  <table>
                    <tr>
                      <td><?php _e("Iterations","wplivechat"); ?></td>
                      <td><input id="wplc_iterations" name="wplc_iterations" type="number" max='200' min='10'  value="<?php if (isset($wplc_advanced_settings['wplc_iterations'])) { echo $wplc_advanced_settings['wplc_iterations']; } else { echo '55'; } ?>" /></td>
                    </tr>
                    <tr>
                      <td><?php _e("Sleep between iterations","wplivechat"); ?></td>
                      <td>
                        <input id="wplc_delay_between_loops" name="wplc_delay_between_loops" type="number" max='1000000' min='25000'  value="<?php if (isset($wplc_advanced_settings['wplc_delay_between_loops'])) { echo $wplc_advanced_settings['wplc_delay_between_loops']; } else { echo '500000'; } ?>" />
                        <small><em><?php _e("microseconds","wplivechat"); ?></em></small>
                      </td>
                    </tr>
                  </table>

              </td>
          </tr>

      </table>
  <?php
}

add_action('wplc_hook_admin_settings_main_settings_after','wplc_powered_by_link_settings_page',2);
/**
 * Adds the necessary checkbox to enable/disable the 'Powered by' link
 * @return string
 */
function wplc_powered_by_link_settings_page() {
    $wplc_powered_by = get_option("WPLC_POWERED_BY");
  ?>
    <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
        <tr>
            <td width='350' valign='top'>
                <?php _e("Display a 'Powered by' link in the chat box", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Checking this will display a 'Powered by WP Live Chat Support' caption at the bottom of your chatbox.", 'wplivechat'); ?>"></i>
            </td>
            <td>
                <input type="checkbox" value="1" name="wplc_powered_by" <?php if ( $wplc_powered_by && $wplc_powered_by == 1 ) { echo "checked"; } ?> />
            </td>
        </tr>
    </table>
    <hr>
  <?php
}

add_action( "wplc_hook_head", "wplc_powered_by_link_save_settings" );
/**
 * Saves the 'Powered by' link settings
 * @return void
 */
function wplc_powered_by_link_save_settings(){

	if( isset( $_POST['wplc_save_settings'] ) ){

			if( isset( $_POST['wplc_powered_by'] ) && $_POST['wplc_powered_by'] == '1' ){
				update_option( "WPLC_POWERED_BY", 1 );
			} else {
				update_option( "WPLC_POWERED_BY", 0 );
			}

	}

}

add_filter( "wplc_start_chat_user_form_after_filter", "wplc_powered_by_link_in_chat", 12, 1 );
/**
 * Appends the 'Powered by' link to the chat window
 * @param  string 	$string the current contents of the chat box
 * @param  int 		$cid    the current chat ID
 * @return string         	the chat contents, with the 'Powered by' link appended to it
 */
function wplc_powered_by_link_in_chat( $string ){

	$show_powered_by = get_option( "WPLC_POWERED_BY" );

	if( $show_powered_by == 1){

		$ret = "<i style='text-align: center; display: block; padding: 5px 0; font-size: 10px;'><a href='https://wp-livechat.com/?utm_source=poweredby&utm_medium=click&utm_campaign=".wp_filter_post_kses(site_url())."'' target='_BLANK' rel='nofollow'>".__("Powered by WP Live Chat Support", "wplivechat")."</a></i>";

	} else {

		$ret = "";

	}

	return $string . $ret;

}

add_action( "admin_enqueue_scripts", "wplc_custom_scripts_scripts" );
/**
 * Loads the Ace.js editor for the custom scripts
 * @return void
 */
function wplc_custom_scripts_scripts(){

	if ( isset( $_GET['page'] ) ) {
		if ( $_GET['page'] == 'wplivechat-menu-settings' ) {
			wp_enqueue_script( "wplc-custom-script-tab-ace", WPLC_PLUGIN_URL.'/js/vendor/ace/ace.js', array('jquery'),WPLC_PLUGIN_VERSION );
		} else if ( $_GET['page'] == 'wplivechat-menu-dashboard' ) {
      wplc_register_common_node();
			wp_enqueue_script( 'wplc-custom-script-dashboard', WPLC_PLUGIN_URL.'/js/wplc_dashboard.js', array('jquery'), WPLC_PLUGIN_VERSION, true );
		}
		
	}

}

add_filter( "wplc_offline_message_subject_filter", "wplc_change_offline_message", 10, 1 );
/**
 * Adds a filter to change the email address to the user's preference
 * @param  string $subject The default subject
 * @return string
 */
function wplc_change_offline_message( $subject ){

	$wplc_settings = get_option( "WPLC_SETTINGS");

	if( isset( $wplc_settings['wplc_pro_chat_email_offline_subject'] ) ){
		$subject = stripslashes( $wplc_settings['wplc_pro_chat_email_offline_subject'] );
	}

	return $subject;

}


add_filter( 'wplc_filter_active_chat_box_notification', 'wplc_active_chat_box_notice' );

if ( ! function_exists( "wplc_active_chat_box_notices" ) ) {
	add_action( "wplc_hook_chat_dashboard_above", "wplc_active_chat_box_notices" );
	function wplc_active_chat_box_notices() {
		$wplc_settings   = get_option( "WPLC_SETTINGS" );
		if ( $wplc_settings["wplc_settings_enabled"] == 2 ) { ?>
            <div class="wplc-chat-box-notification wplc-chat-box-notification--disabled">
                <p><?php echo sprintf( __( 'The Live Chat box is currently disabled on your website due to : <a href="%s">General Settings</a>', 'wp-livechat' ), admin_url( 'admin.php?page=wplivechat-menu-settings#tabs-1' ) ) ?></p>
            </div>
			<?php
		} else {
			$notice = '';
			$notice = apply_filters( 'wplc_filter_active_chat_box_notice', $notice );
			echo $notice;
		}

	}
}


add_filter("wplc_filter_chat_4th_layer_below_input","nifty_chat_ratings_div_backwards_compat",2,2);
/**
 * This adds backwards compatibility to the new look and feel of the modern chat box
 *
 * The ' | ' is removed from the rating icons to fit the new look and feel
 *
 */
function nifty_chat_ratings_div_backwards_compat($msg, $wplc_setting){
	$msg = str_replace(" | ","", $msg);
    return $msg;

}


/*
 * Returns the WDT emoji selector
*/
function wplc_emoji_selector_div(){
	$wplc_settings = get_option("WPLC_SETTINGS");

	// NB: This causes Failed to initVars ReferenceError: wplc_show_date is not defined when uncommented and enabled
	if(!empty($wplc_settings['wplc_disable_emojis']))
		return;

	$emoji_container = "";
    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1){
	   $emoji_container = '<div class="wdt-emoji-popup">
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
	                      </div>';
	}

    return $emoji_container;

}


add_action( 'admin_notices', 'wplc_browser_notifications_admin_warning' );
/**
 * Displays browser notifications warning.
 *
 * Only displays if site is insecure (no SSL).
 */
function wplc_browser_notifications_admin_warning() {

    if ( ! is_ssl() && isset( $_GET['page'] ) && $_GET['page'] === 'wplivechat-menu-settings' ) {

        if ( isset( $_GET['wplc_dismiss_notice_bn'] ) && 'true' === $_GET['wplc_dismiss_notice_bn'] ) {

            update_option( 'wplc_dismiss_notice_bn', 'true' );

        }

        if ( get_option( 'wplc_dismiss_notice_bn') !== 'true' ) {

            ?>
            <div class="notice notice-warning is-dismissible">
                <p><img src="<?php echo sanitize_text_field( plugins_url( 'images/wplc-logo.png', __FILE__ ) ); ?>" style="width:260px;height:auto;max-width:100%;"></p>
                <p><strong><?php _e( 'Browser notifications will no longer function on insecure (non-SSL) sites.', 'wplivechat' ); ?></strong></p>
                <p><?php _e( 'Please add an SSL certificate to your site to continue receiving chat notifications in your browser.', 'wplivechat' ); ?></p>
                <p><a href="?page=<?php echo sanitize_text_field( $_GET['page'] ); ?>&wplc_dismiss_notice_bn=true" id="wplc_dismiss_notice_bn" class="button"><?php _e( "Don't Show This Again", 'wplivechat' ); ?></a></p>
            </div>
            <?php

        }
    }
}

if ( function_exists( 'wplc_et_first_run_check' ) ) {
	add_action( 'admin_notices', 'wplc_transcript_admin_notice' );
}
function wplc_transcript_admin_notice() {
	printf( '<div class="notice notice-info">%1$s</div>', __( 'Please deactivate WP Live Chat Suport - Email Transcript plugin. Since WP Live Chat Support 8.0.05 there is build in support for Email Transcript.', 'wplivechat' ) );
}

add_action( 'init', 'wplc_transcript_first_run_check' );
function wplc_transcript_first_run_check() {
	if ( ! get_option( "WPLC_ET_FIRST_RUN" ) ) {
		/* set the default settings */
		$wplc_et_data['wplc_enable_transcripts']  = 1;
		$wplc_et_data['wplc_send_transcripts_to'] = 'user';
		$wplc_et_data['wplc_send_transcripts_when_chat_ends']  = 0;
		$wplc_et_data['wplc_et_email_body']       = wplc_transcript_return_default_email_body();
		$wplc_et_data['wplc_et_email_header']     = '<a title="' . get_bloginfo( 'name' ) . '" href="' . get_bloginfo( 'url' ) . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #FFF; font-weight: bold; text-decoration: underline;">' . get_bloginfo( 'name' ) . '</a>';

		$default_footer_text = sprintf( __( 'Thank you for chatting with us. If you have any questions, please <a href="%1$s" target="_blank" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #FFF; font-weight: bold; text-decoration: underline;">contact us</a>', 'wplivechat' ),
			'mailto:' . get_bloginfo( 'admin_email' )
		);

		$wplc_et_data['wplc_et_email_footer'] = "<span style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #FFF; font-weight: normal;'>" . $default_footer_text . "</span>";


		update_option( 'WPLC_ET_SETTINGS', $wplc_et_data );
		update_option( "WPLC_ET_FIRST_RUN", true );
	}
}

add_action( 'wplc_hook_admin_visitor_info_display_after', 'wplc_transcript_add_admin_button' );
function wplc_transcript_add_admin_button( $cid ) {
	$wplc_et_settings        = get_option( "WPLC_ET_SETTINGS" );
	$wplc_enable_transcripts = $wplc_et_settings['wplc_enable_transcripts'];
	if ( isset( $wplc_enable_transcripts ) && $wplc_enable_transcripts == 1 ) {
		echo "<p><a href=\"javascript:void(0);\" cid='" . sanitize_text_field( $cid ) . "' class=\"wplc_admin_email_transcript button button-secondary\" id=\"wplc_admin_email_transcript\">" . __( "Email transcript to user", "wplivechat" ) . "</a></p>";
	}
}

add_action( 'wplc_hook_admin_javascript_chat', 'wplc_transcript_admin_javascript' );
function wplc_transcript_admin_javascript() {
	$wplc_et_ajax_nonce = wp_create_nonce( "wplc_et_nonce" );
	wp_register_script( 'wplc_transcript_admin', plugins_url( '/js/wplc_transcript.js', __FILE__ ), null, '', true );
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
}

add_action( 'wp_ajax_wplc_et_admin_email_transcript', 'wplc_transcript_callback' );
function wplc_transcript_callback() {
	$check = check_ajax_referer( 'wplc_et_nonce', 'security' );
	if ( $check == 1 ) {

		if ( isset( $_POST['el'] ) && $_POST['el'] === 'endChat' ) {
			$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );
			$transcript_chat_ends = $wplc_et_settings['wplc_send_transcripts_when_chat_ends'];
			if ( $transcript_chat_ends === 0 ) {
				wp_die();
			}
		}

		if ( $_POST['action'] == "wplc_et_admin_email_transcript" ) {
			if ( isset( $_POST['cid'] ) ) {
				$cid = wplc_return_chat_id_by_rel( intval($_POST['cid']) );
				echo json_encode( wplc_send_transcript( sanitize_text_field( $cid ) ) );
			} else {
				echo json_encode( array( "error" => "no CID" ) );
			}
			wp_die();
		}

		wp_die();
	}
	wp_die();
}

function wplc_send_transcript( $cid ) {
	if ( ! $cid ) {
		return array( "error" => "no CID", "cid" => $cid );
	}
	
	if( ! filter_var($cid, FILTER_VALIDATE_INT) ) {
        /*  We need to identify if this CID is a node CID, and if so, return the WP CID */
        $cid = wplc_return_chat_id_by_rel($cid);
    }

	$email = false;
	$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );

	$wplc_enable_transcripts = $wplc_et_settings['wplc_enable_transcripts'];
	if ( isset( $wplc_enable_transcripts ) && $wplc_enable_transcripts == 1 ) {

		if ( function_exists( "wplc_get_chat_data" ) ) {
			$cdata = wplc_get_chat_data( $cid );
			if ( $cdata ) {
				if ( $wplc_et_settings['wplc_send_transcripts_to'] === 'admin' ) {
				    $user = wp_get_current_user();
					$email = $user->user_email;
			    } else {
					$email = $cdata->email;
				}
				if ( ! $email ) {
					return array( "error" => "no email" );
				}
			} else {
				return array( "error" => "no chat data" );
			}
		} else {
			return array( "error" => "basic funtion missing" );
		}

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$subject = sprintf( __( 'Your chat transcript from %1$s', 'wplivechat' ),
			get_bloginfo( 'url' )
		);
		wp_mail( $email, $subject, wplc_transcript_return_chat_messages( $cid ), $headers );

	}

	return array( "success" => 1 );

}
add_action('wplc_send_transcript_hook', 'wplc_send_transcript', 10, 1);

function wplc_transcript_return_chat_messages( $cid ) {
	global $current_chat_id;
	$current_chat_id  = $cid;
	$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );
	$body             = html_entity_decode( stripslashes( $wplc_et_settings['wplc_et_email_body'] ) );

	if (!$body || empty($body)) {
		$body = do_shortcode( wplc_transcript_return_default_email_body() );
	} else {
		$body = do_shortcode( $body );
	}

	return $body;
}

function wplc_transcript_return_default_email_body() {
	$body = '
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">		
	<html>
	
	<body>



		<table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ec822c;">
	    <tbody>
	      <tr>
	        <td width="100%" style="padding: 30px 20px 100px 20px;">
	          <table align="center" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; max-width:600px;">
	            <tbody>
	              <tr>
	                <td style="text-align: center; padding-bottom: 20px;">
	                  
	                  <p>[wplc_et_transcript_header_text]</p>
	                </td>
	              </tr>
	            </tbody>
	          </table>

	          <table id="" align="center" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; max-width: 600px; font-family: Georgia, serif; font-size: 12px; color: rgb(51, 62, 72); border: 0px solid rgb(255, 255, 255); border-radius: 10px; background-color: rgb(255, 255, 255);">
	          <tbody>
	              <tr>
	                <td class="sortable-list ui-sortable" style="padding:20px;">
	                    [wplc_et_transcript]
	                </td>
	              </tr>
	            </tbody>
	          </table>

	          <table align="center" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; max-width:100%;">
	            <tbody>
	              <tr>
	                <td style="padding:20px;">
	                  <table border="0" cellpadding="0" cellspacing="0" class="" width="100%">
	                    <tbody>
	                      <tr>
	                        <td id="" align="center">
	                         <p>[wplc_et_transcript_footer_text]</p>
	                        </td>
	                      </tr>
	                    </tbody>
	                  </table>
	                </td>
	              </tr>
	            </tbody>
	          </table>
	        </td>
	      </tr>
	    </tbody>
	  </table>


		
		</div>
	</body>
</html>
			';

	return $body;
}


add_action( 'wplc_hook_admin_settings_main_settings_after', 'wplc_hook_admin_transcript_settings' );
function wplc_hook_admin_transcript_settings() {
	$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );
	echo "<hr />";
	echo "<h3>" . __( "Chat Transcript Settings", 'wplivechat' ) . "</h3>";
	echo "<table class='form-table wp-list-table widefat fixed striped pages' width='700'>";
	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Enable chat transcripts:", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<input type=\"checkbox\" value=\"1\" name=\"wplc_enable_transcripts\" ";
	if ( isset( $wplc_et_settings['wplc_enable_transcripts'] ) && $wplc_et_settings['wplc_enable_transcripts'] == 1 ) {
		echo "checked";
	}
	echo " />";
	echo "		</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Send transcripts to:", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<select name=\"wplc_send_transcripts_to\">";
	echo "			    <option value=\"user\" ";
	if ( isset( $wplc_et_settings['wplc_send_transcripts_to'] ) && $wplc_et_settings['wplc_send_transcripts_to'] == 'user' ) {
	    echo "selected";
    }
    echo ">" . __( "User", "wplivechat" ) . "</option>";
	echo "			    <option value=\"admin\" ";
	if ( isset( $wplc_et_settings['wplc_send_transcripts_to'] ) && $wplc_et_settings['wplc_send_transcripts_to'] == 'admin' ) {
		echo "selected";
	}
	echo ">" . __( "Admin", "wplivechat" ) . "</option>";
	echo "          </select>";
	echo "		</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Send transcripts when chat ends:", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<input type=\"checkbox\" value=\"1\" name=\"wplc_send_transcripts_when_chat_ends\" ";
	if ( isset( $wplc_et_settings['wplc_send_transcripts_when_chat_ends'] ) && $wplc_et_settings['wplc_send_transcripts_when_chat_ends'] == 1 ) {
		echo "checked";
	}
	echo " />";
	echo "		</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Email body", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<textarea cols='85' rows='15' name=\"wplc_et_email_body\">";
	if ( isset( $wplc_et_settings['wplc_et_email_body'] ) ) {
		echo trim(html_entity_decode( stripslashes( $wplc_et_settings['wplc_et_email_body'] ) ));
	}
	echo " </textarea>";
	echo "		</td>";
	echo "	</tr>";


	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Email header", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<textarea cols='85' rows='5' name=\"wplc_et_email_header\">";
	if ( isset( $wplc_et_settings['wplc_et_email_header'] ) ) {
		echo trim(stripslashes( $wplc_et_settings['wplc_et_email_header'] ));
	}
	echo " </textarea>";
	echo "		</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td width='400' valign='top'>" . __( "Email footer", "wplivechat" ) . "</td>";
	echo "		<td>";
	echo "			<textarea cols='85' rows='5' name=\"wplc_et_email_footer\">";
	if ( isset( $wplc_et_settings['wplc_et_email_footer'] ) ) {
		echo trim(stripslashes( $wplc_et_settings['wplc_et_email_footer'] ));
	}
	echo " </textarea>";
	echo "		</td>";
	echo "	</tr>";


	echo "</table>";
}

add_shortcode('wplc_et_transcript', 'wplc_transcript_get_transcript');
function wplc_transcript_get_transcript() {
	global $current_chat_id;
	$cid = $current_chat_id;

	if ( intval( $cid ) > 0 ) {
		return wplc_return_chat_messages( intval( $cid ), true );
	} else {
		return "0";
	}
}

add_shortcode( 'wplc_et_transcript_footer_text', 'wplc_transcript_get_footer_text' );
function wplc_transcript_get_footer_text() {
	$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );
	$wplc_et_footer   = html_entity_decode( stripslashes( $wplc_et_settings['wplc_et_email_footer'] ) );
	if ( $wplc_et_footer ) {
		return $wplc_et_footer;
	} else {
		return "";
	}
}

add_shortcode( 'wplc_et_transcript_header_text', 'wplc_transcript_get_header_text' );
function wplc_transcript_get_header_text() {
	$wplc_et_settings = get_option( "WPLC_ET_SETTINGS" );

	global $current_chat_id;
	$cid = $current_chat_id;

	$from_email = "Unknown@unknown.com";
	$from_name = "User";
	if ( intval( $cid ) > 0 ) {
		$chat_data = wplc_get_chat_data( $cid );
		if ( isset( $chat_data->email ) ) {
			$from_email = $chat_data->email;
		}
		if ( isset( $chat_data->name ) ) {
			$from_name = $chat_data->name;
		}
	}

	$wplc_et_header   = "<h3>".$from_name." (".$from_email.")"."</h3>".html_entity_decode( stripslashes( $wplc_et_settings['wplc_et_email_header'] ) );
	if ( $wplc_et_header ) {
		return $wplc_et_header;
	} else {
		return "";
	}
}

function wplc_features_admin_js() {
	wp_register_script('wplc-admin-features', plugins_url('/js/wplc_admin_pro_features.js', __FILE__), array('wplc-admin-chat-js'),false, false);
	wp_enqueue_script('wplc-admin-features');
}

add_action('admin_notices', 'wplc_encryption_deprecated_notice');
/**
 * Notice of doom
*/
function wplc_encryption_deprecated_notice(){
  if(isset($_GET['wplc_encryption_dismiss_notice'])){
  	if(wp_verify_nonce($_GET['wplc_dismiss_nonce'], 'wplc_encrypt_note_nonce')){
    	update_option('WPLC_ENCRYPT_DEPREC_NOTICE_DISMISSED', 'true');
    }
  }

  if(isset($_GET['page'])){
  	if($_GET['page'] === 'wplivechat-menu-settings'){
        $encrypt_deprec_notice_dismissed = get_option('WPLC_ENCRYPT_DEPREC_NOTICE_DISMISSED', false);

        if($encrypt_deprec_notice_dismissed === false || $encrypt_deprec_notice_dismissed === 'false'){
          $dismiss_nonce = wp_create_nonce('wplc_encrypt_note_nonce');
          $encrypt_note = __('Please note, local message encryption and local server options will be deprecated in the next major release. All encryption and message delivery will handled by our external servers in future.', 'wplivechat');

          $output = "<div class='update-nag' style='margin-bottom: 5px;'>";
          $output .=     "<strong>" . __("Deprecation Notice - Message Encryption & Local Server", "wplivechat") . "</strong><br>";
          $output .=     "<p>" . $encrypt_note . "</p>";
          $output .=     "<a class='button' href='?page=" . htmlspecialchars(sanitize_text_field($_GET['page'])) ."&wplc_encryption_dismiss_notice=true&wplc_dismiss_nonce=$dismiss_nonce'>" . __("Dismiss", "wplivechat") . "</a>";
          $output .= "</div>";
          echo $output;
        }
    }
  }
}