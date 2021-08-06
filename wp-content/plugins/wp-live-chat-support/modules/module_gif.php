<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_wplc_search_gif', 'wplc_search_gif' );
add_action( 'wp_ajax_nopriv_my_action', 'wplc_search_gif');




/* admin chat - legacy chatbox */
add_action('wplc_hook_admin_chatbox_javascript','wplc_gif_localize_data');
/*legacy dashboard */
add_action('wplc_hook_push_js_to_front','wplc_gif_localize_data');
/* default dashboard */
add_action('wplc_admin_remoter_dashboard_scripts_localizer','wplc_gif_localize_data');

/**
 * Add localized data for Gif integration
 * 
 */
function wplc_gif_localize_data() {
	$wplc_settings = get_option("WPLC_SETTINGS");

	// Localize variables for the GIF Integration
	if( isset($wplc_settings['wplc_is_gif_integration_enabled']) && $wplc_settings['wplc_is_gif_integration_enabled'] == '1' ) {
		$wplc_is_gif_integration_enabled = true;
	} else {
		$wplc_is_gif_integration_enabled = false;
	}

	if ( $wplc_is_gif_integration_enabled ) {



		wp_register_script('my-wplc-u-admin-gif-integration', plugins_url('../js/wplc_u_admin_gif_integration.js', __FILE__), array('jquery'), WPLC_PLUGIN_VERSION, true);
		wp_enqueue_script('my-wplc-u-admin-gif-integration');



		if( isset($wplc_settings['wplc_preferred_gif_provider']) ) {
			$wplc_selected_gif_provider_idx = $wplc_settings['wplc_preferred_gif_provider'];
		} else {
			$wplc_selected_gif_provider_idx = '0';
		}

		$wplc_gif_integration_details = array(
			'is_gif_integration_enabled' => $wplc_is_gif_integration_enabled,
			'preferred_gif_provider' => $wplc_selected_gif_provider_idx,
			'available_gif_providers' => array(
				"1" => "https://api.giphy.com",
				"2" => "https://api.tenor.com"
			)
		);

		wp_localize_script('my-wplc-u-admin-gif-integration', "wplc_gif_integration_details", $wplc_gif_integration_details); 

	}

}


/**
 * Searches for a gif in the selected GIF provider
 */
function wplc_search_gif() {
    // Clean the input coming from the client side
    $search_term = sanitize_text_field($_POST["search_term"]);

    // Get the necessary parameters to build the url
    $wplc_settings = get_option("WPLC_SETTINGS");

    if (isset($wplc_settings['wplc_preferred_gif_provider'])) {
        $wplc_selected_gif_provider_idx = $wplc_settings['wplc_preferred_gif_provider'];
    } else {
        $wplc_selected_gif_provider_idx = '1';
    }


    

    switch ($wplc_selected_gif_provider_idx) {

        // Giphy
        case '1':

            if (isset($wplc_settings['wplc_giphy_api_key'])) {
                $gif_provider_url = "https://api.giphy.com/v1/gifs/search";
                $wplc_selected_gif_provider_key = $wplc_settings['wplc_giphy_api_key'];

                $params = array(
                    'api_key'=> $wplc_selected_gif_provider_key,
                    'q' => $search_term,
                    'limit' => 10,
                    'offset' => 0,
                    'rating' => 'G',
                    'lan' => 'en'
                );

                $gif_provider_url = add_query_arg($params, esc_url_raw($gif_provider_url));
            }

            break;

        // Tenor
        case '2':

    		if (isset($wplc_settings['wplc_tenor_api_key'])) {
                $gif_provider_url = "https://api.tenor.com/v1/search";
                $wplc_selected_gif_provider_key = $wplc_settings['wplc_tenor_api_key'];

                $params = array(
                    'q' => $search_term,
                    'key'=> $wplc_selected_gif_provider_key,
                    'limit' => 10,
                    'anon_id' => '3a76e56901d740da9e59ffb22b988242'
                );

                $gif_provider_url = add_query_arg($params, esc_url_raw($gif_provider_url));
            }

    		break;
    }

    $response = wp_remote_get(esc_url_raw($gif_provider_url));
    $response_code = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (is_wp_error($response)) {
        die($response_message);
        exit;
    } else {
        $body = wp_remote_retrieve_body($response);
        die($body);
        exit;
    }
}

add_action('wplc_hook_admin_settings_chat_box_settings_after','wplc_gif_settings_page', 1);
function wplc_gif_settings_page() {
	$wplc_settings = get_option("WPLC_SETTINGS");
	if (isset($wplc_settings["wplc_preferred_gif_provider"])) { $wplc_preferred_gif_provider[intval($wplc_settings["wplc_preferred_gif_provider"])] = "SELECTED"; }
	?>
	<!-- GIF Integration -->
	  <h3><?php _e("GIF Integration", 'wplivechat') ?></h3>
	  <hr>
	  <table class='wp-list-table wplc_list_table widefat fixed striped pages'>
	      <tbody>
	          <tr>
	              <td width='300' valign='top'>
	                  <?php _e("Enable GIF Integration", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("If you enable GIF Integration you're going to be able to send GIFs by typing commands such as '/gif happy' when writing a message.", "wplivechat") ?>"></i>
	              </td>
	              <td>
	                  <input id="wplc_is_gif_integration_enabled" name="wplc_is_gif_integration_enabled" type="checkbox" value="1" <?php echo (isset($wplc_settings['wplc_is_gif_integration_enabled']) && $wplc_settings['wplc_is_gif_integration_enabled'] == 1 ? "checked" : "" ); ?> />
	              </td>
	          </tr>
	          <tr>
	              <td width='300' valign='top'>
	                  <?php _e("Select a GIF provider", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("A GIF provider is the source of the GIFs that are going to show when you type a command like '/gif happy' on a message.", "wplivechat") ?>"></i>
	              </td>
	              <td>
	                  <select id='wplc_preferred_gif_provider' name='wplc_preferred_gif_provider'>
	                      <option value="1" <?php if (isset($wplc_preferred_gif_provider[1])) { echo $wplc_preferred_gif_provider[1]; } ?>><?php _e("Giphy","wplivechat"); ?></option>
	                      <option value="2" <?php if (isset($wplc_preferred_gif_provider[2])) { echo $wplc_preferred_gif_provider[2]; } ?>><?php _e("Tenor","wplivechat"); ?></option>
	                  </select>
	              </td>
	          </tr>
	          <tr>
	              <td width='300' valign='top'>
	                  <?php _e("Giphy API key", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("The API key created for you when you register an app on Giphy", "wplivechat") ?>"></i>
	              </td>
	              <td>
	                  <input id='wplc_giphy_api_key' name="wplc_giphy_api_key" type="text" value="<?php echo (isset($wplc_settings['wplc_giphy_api_key']) ?  urldecode($wplc_settings['wplc_giphy_api_key']) : '' ); ?>" placeholder="<?php _e('Giphy API key', 'wplivechat'); ?>" />
	              </td>
	          </tr>
	          <tr>
	              <td width='300' valign='top'>
	                  <?php _e("Tenor API key", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("The API key created for you when you register an app on Tenor", "wplivechat") ?>"></i>
	              </td>
	              <td>
	                  <input id='wplc_tenor_api_key' name="wplc_tenor_api_key" type="text" value="<?php echo (isset($wplc_settings['wplc_tenor_api_key']) ?  urldecode($wplc_settings['wplc_tenor_api_key']) : '' ); ?>" placeholder="<?php _e('Tenor API key', 'wplivechat'); ?>" />
	              </td>
	          </tr>
	      </tbody>
	  </table>
	  <!-- ./GIF Integration -->
	  <?php

}