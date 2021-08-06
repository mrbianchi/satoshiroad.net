<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Adds 'Google Analytics' content to settings area
 */
add_action("wplc_hook_admin_settings_main_settings_after","wplc_hook_settings_page_ga_integration",2);
function wplc_hook_settings_page_ga_integration() {

	$wplc_ga_data = get_option("WPLC_GA_SETTINGS"); 
    ?>
	<h4><?php _e("Google Analytics Integration", "wplivechat") ?></h4>
	<table class="wp-list-table widefat wplc_list_table fixed striped pages">
		<tbody>
			<tr>
				<td width="350" valign="top">
				  <label for="wplc_enable_ga"><?php _e("Enable Google Analytics Integration","wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="When enabled we will send custom events to your Google Analytics account for events such as when a user starts a chat, sends an offline message, closes a chat, etc."></i></label>
				</td>
				<td valign="top">
				  <input type="checkbox" value="1" name="wplc_enable_ga" <?php if (isset($wplc_ga_data['wplc_enable_ga']) && $wplc_ga_data['wplc_enable_ga'] == '1') { echo "checked"; } ?>> 
					</td>
          	</tr>
		</tbody>
	</table>
	<br>

	
	<?php
	

}

/**
* Latch onto the default POST handling when saving live chat settings
*/
add_action('wplc_hook_admin_settings_save','wplc_ga_integraton_save_settings');
function wplc_ga_integraton_save_settings() {
    if (isset($_POST['wplc_save_settings'])) {

    	$wplc_ga_data = array();
        if (isset($_POST['wplc_enable_ga'])) { $wplc_ga_data['wplc_enable_ga'] = sanitize_text_field($_POST['wplc_enable_ga']); }

        update_option('WPLC_GA_SETTINGS', $wplc_ga_data);

    }
}


