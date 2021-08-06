<?php
/** Settings page */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

?>
  <style>
  .ui-tabs-vertical {  }
  .ui-tabs-vertical .ui-tabs-nav {
      padding: .2em .1em .2em .2em;
      float: left;
      max-width: 20%;
      min-width: 190px;
  }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel {
      float: left;
      min-width: 67%;
      max-width: 67%;
  }
  textarea, input[type='text'], input[type='email'], input[type='password']{ width: 100% !important; }
  </style>

  <?php
  /**
   * Removes the ajax loader and forces the settings page to load as is after 3 seconds.
   * 
   * This has been put here to counter any PHP fatal warnings that may be experienced on the settings page.
   *
   * Putting this in the wplc_tabs.js file will not work as that file is not loaded if there is a PHP fatal error
   */
  ?>
  <script>
     setTimeout( function() {
        jQuery("#wplc_settings_page_loader").remove();
        jQuery(".wrap").css('display','block');
        jQuery(".wplc_settings_save_notice").css('display','block');
   },3000);
 </script>

<?php wplc_stats("settings");


if (function_exists("wplc_string_check")) { wplc_string_check(); }
$wplc_settings = get_option("WPLC_SETTINGS");

 ?>

<?php
if (get_option("WPLC_HIDE_CHAT") == true) {
    $wplc_hide_chat = "checked";
} else {
    $wplc_hide_chat = "";
};

?>
<img src='<?php echo WPLC_PLUGIN_URL.'images/ajax-loader.gif'; ?>' id='wplc_settings_page_loader' style='display: block; margin: 20px auto;' />
<div class="wrap wplc_wrap" style='display: none;'>

    <style>
        .wplc_light_grey{
            color: #666;
        }
    </style>
    <div id="icon-edit" class="icon32 icon32-posts-post">
        <br>
    </div>
    <h2><?php _e("WP Live Chat Support Settings","wplivechat")?></h2>
    <?php
        if (isset($wplc_settings["wplc_settings_align"])) { $wplc_settings_align[intval($wplc_settings["wplc_settings_align"])] = "SELECTED"; }
        if (isset($wplc_settings["wplc_settings_enabled"])) { $wplc_settings_enabled[intval($wplc_settings["wplc_settings_enabled"])] = "SELECTED"; }
        if (isset($wplc_settings["wplc_settings_fill"])) { $wplc_settings_fill = $wplc_settings["wplc_settings_fill"]; } else { $wplc_settings_fill = "ed832f"; }
        if (isset($wplc_settings["wplc_settings_font"])) { $wplc_settings_font = $wplc_settings["wplc_settings_font"]; } else { $wplc_settings_font = "FFFFFF"; }
        if (isset($wplc_settings["wplc_settings_color1"])) { $wplc_settings_color1 = $wplc_settings["wplc_settings_color1"]; } else { $wplc_settings_color1 = "ED832F"; }
        if (isset($wplc_settings["wplc_settings_color2"])) { $wplc_settings_color2 = $wplc_settings["wplc_settings_color2"]; } else { $wplc_settings_color2 = "FFFFFF"; }
        if (isset($wplc_settings["wplc_settings_color3"])) { $wplc_settings_color3 = $wplc_settings["wplc_settings_color3"]; } else { $wplc_settings_color3 = "EEEEEE"; }
        if (isset($wplc_settings["wplc_settings_color4"])) { $wplc_settings_color4 = $wplc_settings["wplc_settings_color4"]; } else { $wplc_settings_color4 = "666666"; }
        
        
        if(get_option("WPLC_HIDE_CHAT") == true) { $wplc_hide_chat = "checked"; } else { $wplc_hide_chat = ""; };
        $wplc_choose_data = get_option("WPLC_CHOOSE_SETTINGS");
		$wplc_inex_data = get_option("WPLC_INEX_SETTINGS");
		$wplc_auto_responder_settings = get_option( "WPLC_AUTO_RESPONDER_SETTINGS" );
		$wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
		$wplc_bh_settings = get_option( "wplc_bh_settings" );
    $wplc_encrypt_data = get_option("WPLC_ENCRYPT_SETTINGS");
		
		$wplc_quick_response_order_by = isset( $wplc_settings['wplc_quick_response_orderby'] ) ? sanitize_text_field( $wplc_settings['wplc_quick_response_orderby'] ) : 'title';
		$wplc_quick_response_order = isset( $wplc_settings['wplc_quick_response_order'] ) ? sanitize_text_field( $wplc_settings['wplc_quick_response_order'] ) : 'DESC'; 
		
 		$wplc_pro_auto_first_response_chat_msg = isset($wplc_settings['wplc_pro_auto_first_response_chat_msg']) ? $wplc_settings['wplc_pro_auto_first_response_chat_msg'] : '';
     ?>
    <form action='' name='wplc_settings' method='POST' id='wplc_settings'>
    <?php wp_nonce_field( 'wplc_save_settings', 'wplc_save_settings_nonce' ); ?>
    <div id="wplc_tabs">
      <ul>
        <?php 
          $tab_array = array(
            0 => array(
              "href" => "#tabs-1",
              "icon" => 'fa fa-gear',
              "label" => __("General Settings","wplivechat")
            ),
            1 => array(
              "href" => "#tabs-2",
              "icon" => 'fa fa-envelope',
              "label" => __("Chat Box","wplivechat")
            ),
            2 => array(
              "href" => "#tabs-3",
              "icon" => 'fa fa-book',
              "label" => __("Offline Messages","wplivechat")
            ),
            3 => array(
              "href" => "#tabs-4",
              "icon" => 'fa fa-pencil',
              "label" => __("Styling","wplivechat")
            ),
            4 => array(
              "href" => "#tabs-5",
              "icon" => 'fa fa-users',
              "label" => __("Agents","wplivechat")
            ),
            5 => array(
              "href" => "#tabs-7",
              "icon" => 'fa fa-gavel',
              "label" => __("Blocked Visitors","wplivechat")
            )
          );
          $tabs_top = apply_filters("wplc_filter_setting_tabs",$tab_array);

          foreach ($tabs_top as $tab) {
            echo "<li><a href=\"".$tab['href']."\"><i class=\"".$tab['icon']."\"></i> ".$tab['label']."</a></li>";
          }

        ?>
       
      </ul>
      <div id="tabs-1">
          <h3><?php _e("General Settings",'wplivechat')?></h3>
          <table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
              <tr>
                  <td width='350' valign='top'><?php _e("Chat enabled","wplivechat")?>: </td>
                  <td>
                      <select id='wplc_settings_enabled' name='wplc_settings_enabled'>
                          <option value="1" <?php if (isset($wplc_settings_enabled[1])) { echo $wplc_settings_enabled[1]; } ?>><?php _e("Yes","wplivechat"); ?></option>
                          <option value="2" <?php if (isset($wplc_settings_enabled[2])) { echo $wplc_settings_enabled[2]; }?>><?php _e("No","wplivechat"); ?></option>
                      </select>
                  </td>
              </tr>


                  <tr>
                  <td width='300' valign='top'>
                      <?php _e("Required Chat Box Fields","wplivechat")?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Set default fields that will be displayed when users starting a chat", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <div class="wplc-require-user-info__item">
                          <input type="radio" value="1" name="wplc_require_user_info" id="wplc_require_user_info_both" <?php if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == '1') { echo "checked"; } ?> />
                          <label for="wplc_require_user_info_both"><?php _e( 'Name and email', 'wplivechat' ); ?></label>
                      </div>
                      <div class="wplc-require-user-info__item">
                          <input type="radio" value="email" name="wplc_require_user_info" id="wplc_require_user_info_email" <?php if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'email') { echo "checked"; } ?> />
                          <label for="wplc_require_user_info_email"><?php _e( 'Email', 'wplivechat' ); ?></label>
                      </div>
                      <div class="wplc-require-user-info__item">
                          <input type="radio" value="name" name="wplc_require_user_info" id="wplc_require_user_info_name" <?php if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 'name') { echo "checked"; } ?> />
                          <label for="wplc_require_user_info_name"><?php _e( 'Name', 'wplivechat' ); ?></label>
                      </div>
                      <div class="wplc-require-user-info__item">
                          <input type="radio" value="0" name="wplc_require_user_info" id="wplc_require_user_info_none" <?php if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == '0') { echo "checked"; } ?> />
                          <label for="wplc_require_user_info_none"><?php _e( 'No fields', 'wplivechat' ); ?></label>
                      </div>
                  </td>
              </tr>
              <tr class="wplc-user-default-visitor-name__row">
                  <td width='300' valign='top'>
		              <?php _e("Default visitor name","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("This name will be displayed for all not logged in visitors", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <input type="text" name="wplc_user_default_visitor_name" id="wplc_user_default_visitor_name" value="<?php if ( isset( $wplc_settings['wplc_user_default_visitor_name'] ) ) { echo stripslashes( $wplc_settings['wplc_user_default_visitor_name'] ); } else { echo __( "Guest", "wplivechat" ); } ?>" />
                  </td>
              </tr>
              <tr>
                  <td width='300' valign='top'>
                      <?php _e("Input Field Replacement Text","wplivechat")?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("This is the text that will show in place of the Name And Email fields", "wplivechat") ?>"></i>                      
                  </td>
                  <td valign='top'>
                      <textarea cols="45" rows="5" name="wplc_user_alternative_text" ><?php if(isset($wplc_settings['wplc_user_alternative_text'])) { echo stripslashes($wplc_settings['wplc_user_alternative_text']); } ?></textarea>
                </td>
              </tr>
              <tr>
                  <td width='300' valign='top'>
                      <?php _e("Use Logged In User Details","wplivechat")?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("A user's Name and Email Address will be used by default if they are logged in.", "wplivechat") ?>"></i>                      
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_loggedin_user_info" <?php if(isset($wplc_settings['wplc_loggedin_user_info'])  && intval($wplc_settings['wplc_loggedin_user_info']) == 1 ) { echo "checked"; } ?> />                      
                  </td>
              </tr>
              <tr>
                  <td width='200' valign='top'>
                      <?php _e("Enable On Mobile Devices","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Disabling this will mean that the Chat Box will not be displayed on mobile devices. (Smartphones and Tablets)", "wplivechat") ?>"></i>                      
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_enabled_on_mobile" <?php if(isset($wplc_settings['wplc_enabled_on_mobile'])  && intval($wplc_settings['wplc_enabled_on_mobile']) == 1 ) { echo "checked"; } ?> />                      
                  </td>
              </tr>

              <?php if( isset( $wplc_settings['wplc_use_node_server'] ) && intval( $wplc_settings['wplc_use_node_server'] ) == 1 ){ ?>
              <tr>
                  <td width='300' valign='top'>
                      <?php _e("Play a sound when there is a new visitor","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Disable this to mute the sound that is played when a new visitor arrives", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_enable_visitor_sound" <?php if(isset($wplc_settings['wplc_enable_visitor_sound'])  && intval($wplc_settings['wplc_enable_visitor_sound']) == 1 ) { echo "checked"; } ?> />                      
                  </td>
              </tr>
              <?php } ?>
              <tr>
                  <td width='300' valign='top'>
                      <?php _e("Play a sound when a new message is received","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Disable this to mute the sound that is played when a new chat message is received", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_enable_msg_sound" <?php if(isset($wplc_settings['wplc_enable_msg_sound'])  && intval($wplc_settings['wplc_enable_msg_sound']) == 1 ) { echo "checked"; } ?> />                      
                  </td>
              </tr>
              
              <tr>
                  <td width='300' valign='top'>
    			          <?php _e("Enable Font Awesome set","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Disable this if you have Font Awesome set included with your theme", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_enable_font_awesome" <?php if(isset($wplc_settings['wplc_enable_font_awesome']) && intval($wplc_settings['wplc_enable_font_awesome']) == 1 ) { echo "checked"; } ?> />
                  </td>
              </tr>
              <tr>
                  <td width='300' valign='top'>
                    <?php _e("Enable chat dashboard and notifications on all admin pages","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("This will load the chat dashboard on every admin page.", "wplivechat") ?>"></i>
                  </td>
                  <td valign='top'>
                      <input type="checkbox" value="1" name="wplc_enable_all_admin_pages" <?php if(isset($wplc_settings['wplc_enable_all_admin_pages']) && intval($wplc_settings['wplc_enable_all_admin_pages']) == 1 ) { echo "checked"; } ?> />
                  </td>
              </tr>    
            </table>
		  
		  	<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
	        <tr>
	            <td width='350' valign='top'>
	                <?php _e("Choose when I want to be online", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Checking this will allow you to change your status to Online or Offline on the Live Chat page.', 'wplivechat'); ?>"></i>                     
	            </td>  
	            <td>
	                <input type="checkbox" value="1" name="wplc_auto_online" <?php if (isset($wplc_choose_data['wplc_auto_online']) && intval($wplc_choose_data['wplc_auto_online']) == 1) { echo "checked"; } ?> />                    
	            </td>
	        </tr>
	    </table>
	    <hr>
		
		  	    <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
	    	<tr>
	            <td width='350' valign='top'>
	                <?php _e("Exclude chat from 'Home' page:", "wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Leaving this unchecked will allow the chat window to display on your home page.", "wplivechat") ?>"></i>
	            </td>
	            <td valign='top'>
	                <input type="checkbox" name="wplc_exclude_home" <?php if (isset($wplc_inex_data['wplc_exclude_home'])) { echo 'checked'; } ?> value='1' />                      
	            </td>
            </tr>
            <tr>
	            <td width='350' valign='top'>
	                <?php _e("Exclude chat from 'Archive' pages:", "wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Leaving this unchecked will allow the chat window to display on your archive pages.", "wplivechat") ?>"></i>
	            </td>
	            <td valign='top'>
	                <input type="checkbox" name="wplc_exclude_archive" <?php if (isset($wplc_inex_data['wplc_exclude_archive'])) { echo 'checked'; } ?> value='1' />                      
	            </td>
            </tr>
	        <tr>
	            <td width='350' valign='top'>
	                <?php _e("Include chat window on the following pages:", "wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Show the chat window on the following pages. Leave blank to show on all. (Use comma-separated Page ID's)", "wplivechat") ?>"></i>
	            </td>
	            <td valign='top'>
	                <input type="text" name="wplc_include_on_pages" value="<?php if (isset($wplc_inex_data['wplc_include_on_pages'])) { echo $wplc_inex_data['wplc_include_on_pages']; } ?>" />                      
	            </td>
            </tr>
            <tr>
	            <td width='350' valign='top'>
	                <?php _e("Exclude chat window on the following pages:", "wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Do not show the chat window on the following pages. Leave blank to show on all. (Use comma-separated Page ID's)", "wplivechat") ?>"></i>                      
	            </td>
	            <td valign='top'>
	                <input type="text" name="wplc_exclude_from_pages" value="<?php if (isset($wplc_inex_data['wplc_exclude_from_pages'])) { echo $wplc_inex_data['wplc_exclude_from_pages']; } ?>" />                      
	            </td>
	        </tr>
            <tr class="wplc-exclude-post-types__row">
                <td width='200' valign='top'>
				    <?php _e("Exclude chat window on selected post types","wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Do not show the chat window on the following post types pages.", "wplivechat") ?>"></i>
                </td>
                <td valign='top'><?php
                    $wplc_inex_settings = get_option('WPLC_INEX_SETTINGS');
				    $wplc_posts_types = get_post_types(
					    array(
                            '_builtin' => false,
                            'public' => true
                        ),
                        'objects'
				    );
				    if ( ! empty( $wplc_posts_types ) ) {
					    foreach ( $wplc_posts_types as $posts_type ) { ?>
                            <div class="wplc-exclude-post-types__item">
                                <input type="checkbox" value="<?php echo $posts_type->name; ?>" id="wplc_exclude_post_types_<?php echo $posts_type->name; ?>" name="wplc_exclude_post_types[]" <?php echo ( ! empty( $wplc_inex_settings['wplc_exclude_post_types'] ) && in_array( $posts_type->name, $wplc_inex_settings['wplc_exclude_post_types'] ) ) ? 'checked' : ''; ?> />
                                <label for="wplc_exclude_post_types_<?php echo $posts_type->name; ?>"><?php _e( $posts_type->label, "wplivechat" ) ?></label>
                            </div>
					    <?php
					    }
				    } else {
					    _e( 'No post types found.', 'wplivechat' );
                    } ?>
                </td>
            </tr>
	        <tr>
                <td width='300' valign='top'>
            		<?php _e("Allow any user to make themselves a chat agent", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Checking this will allow any of your users to make themselves a chat agent when editing their profile.', 'wplivechat'); ?>"></i>
                </td>
                <td>
                    <input type="checkbox" value="1" name="wplc_make_agent" <?php if (isset($wplc_inex_data['wplc_make_agent']) && intval($wplc_inex_data['wplc_make_agent']) == 1) { echo "checked"; } ?> />                                          
                </td>
            </tr>

	    </table>
	    <hr>
		  
		<h4><?php _e( "Quick Response", "wplivechat" ); ?></h4>
        <table class="wp-list-table wplc_list_table widefat fixed striped pages">
            <tbody>
            <tr>
                <td width="350" valign="top">
                    <label for="wplc_quick_response_orderby"><?php _e( "Order by", "wplivechat" ); ?></label>
                </td>
                <td valign="top">
                    <select id='wplc_quick_response_orderby' name='wplc_quick_response_orderby'>
                        <option value="title" <?php selected( $wplc_quick_response_order_by, 'title' ) ?>><?php _e( "Title", "wplivechat" ); ?></option>
                        <option value="date" <?php selected( $wplc_quick_response_order_by, 'date' ) ?>><?php _e( "Date", "wplivechat" ); ?></option>
                        <option value="number" <?php selected( $wplc_quick_response_order_by, 'number' ) ?>><?php _e( "Number", "wplivechat" ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="350" valign="top">
                    <label for="wplc_quick_response_order"><?php _e( "Order", "wplivechat" ); ?></label>
                </td>
                <td valign="top">
                    <select id='wplc_quick_response_order' name='wplc_quick_response_order'>
                        <option value="DESC" <?php selected( $wplc_quick_response_order, 'DESC' ) ?>><?php _e( "Descending", "wplivechat" ); ?></option>
                        <option value="ASC" <?php selected( $wplc_quick_response_order, 'ASC' ) ?>><?php _e( "Ascending", "wplivechat" ); ?></option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <br>  
		  
		   <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
            <tr>
                <td width='350' valign='top'>
					<?php _e( "Enable Voice Notes on admin side", "wplivechat" ); ?>: <i
                            class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                            title="<?php _e( "Enabling this will allow you to record the voice during the chat and send it to visitor once you hold on CTRL + SPACEBAR in main chat window", "wplivechat" ) ?>"></i>
                </td>
                <td valign='top'>
                    <input type="checkbox" value="1"
                           name="wplc_enable_voice_notes_on_admin" <?php if ( isset( $wplc_settings['wplc_enable_voice_notes_on_admin'] ) && intval($wplc_settings['wplc_enable_voice_notes_on_admin']) == 1 ) {
						echo "checked";
					} ?> />
                </td>
            </tr>
            <tr>
                <td width='350' valign='top'>
					<?php _e( "Enable Voice Notes on visitor side", "wplivechat" ); ?>: <i
                            class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                            title="<?php _e( "Enabling this will allow the visitors to record the voice during the chat and send it to agent once they hold on CTRL + SPACEBAR", "wplivechat" ) ?>"></i>
                </td>
                <td valign='top'>
                    <input type="checkbox" value="1"
                           name="wplc_enable_voice_notes_on_visitor" <?php if ( isset( $wplc_settings['wplc_enable_voice_notes_on_visitor'] ) && intval($wplc_settings['wplc_enable_voice_notes_on_visitor']) == 1 ) {
						echo "checked";
					} ?> />
                </td>
            </tr>
        </table>  
		  
            <?php do_action('wplc_hook_admin_settings_main_settings_after'); ?>
            
          
      </div>
      <div id="tabs-2">
          <h3><?php _e("Chat Window Settings",'wplivechat')?></h3>
          <table class='wp-list-table wplc_list_table widefat fixed striped pages'>
              <tr>
                  <td width='300' valign='top'><?php _e("Chat box alignment","wplivechat")?>:</td>
                  <td>
                      <select id='wplc_settings_align' name='wplc_settings_align'>
                          <option value="1" <?php if (isset($wplc_settings_align[1])) { echo $wplc_settings_align[1]; } ?>><?php _e("Bottom left","wplivechat"); ?></option>
                          <option value="2" <?php if (isset($wplc_settings_align[2])) { echo $wplc_settings_align[2]; } ?>><?php _e("Bottom right","wplivechat"); ?></option>
                          <option value="3" <?php if (isset($wplc_settings_align[3])) { echo $wplc_settings_align[3]; } ?>><?php _e("Left","wplivechat"); ?></option>
                          <option value="4" <?php if (isset($wplc_settings_align[4])) { echo $wplc_settings_align[4]; } ?>><?php _e("Right","wplivechat"); ?></option>
                      </select>
                  </td>
              </tr>
              <tr>
                  <td width='300'>
                      <?php _e("Auto Pop-up","wplivechat") ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Expand the chat box automatically (prompts the user to enter their name and email address).","wplivechat") ?>"></i>
                  </td>
                  <td>
                      <input type="checkbox" name="wplc_auto_pop_up" value="1" <?php if(isset($wplc_settings['wplc_auto_pop_up'])  && intval($wplc_settings['wplc_auto_pop_up']) == 1 ) { echo "checked"; } ?>/>
                  </td>
              </tr>
             
              <tr>
                  <td>
                      <?php _e("Display details in chat message", "wplivechat") ?>
                  </td>
                  <td> 
                      <?php if (isset($wplc_settings['wplc_show_name']) && intval($wplc_settings['wplc_show_name']) == 1) { $checked = "checked"; } else { $checked = ''; } ?>
                      <input type="checkbox" name="wplc_show_name" value="1" <?php echo $checked; ?>/> <label><?php _e("Show Name", "wplivechat"); ?></label><br/>
                      <?php if (isset($wplc_settings['wplc_show_avatar']) && intval($wplc_settings['wplc_show_avatar']) == 1) { $checked = "checked"; } else { $checked = ''; } ?>
                      <input type="checkbox" name="wplc_show_avatar" value="1" <?php echo $checked; ?>/> <label><?php _e("Show Avatar", "wplivechat"); ?></label>
                  </td>
              </tr>
              <tr>
                  <td>
                      <?php _e("Only show the chat window to users that are logged in", "wplivechat") ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("By checking this, only users that are logged in will be able to chat with you.", "wplivechat") ?>"></i>
                  </td>
                  <td>
                      <input type="checkbox" name="wplc_display_to_loggedin_only" value="1" <?php
                      if (isset($wplc_settings['wplc_display_to_loggedin_only']) && $wplc_settings['wplc_display_to_loggedin_only'] == 1) {
                          echo "checked";
                      }
                      ?>/>
                  </td>
              </tr>   
              <tr>
                  <td>
                      <?php _e("Display a timestamp in the chat window", "wplivechat") ?>
                  </td>
                  <td>  
                      <?php if (isset($wplc_settings['wplc_show_date']) && intval($wplc_settings['wplc_show_date']) == 1) { $checked = "checked"; } else { $checked = ''; } ?>
                      <input type="checkbox" name="wplc_show_date" value="1" <?php echo $checked; ?>/> <label><?php _e("Show Date", "wplivechat"); ?></label><br/>
                      <?php if (isset($wplc_settings['wplc_show_time']) && intval($wplc_settings['wplc_show_time']) == 1) { $checked = "checked"; } else { $checked = ''; } ?>
                      <input type="checkbox" name="wplc_show_time" value="1" <?php echo $checked; ?>/> <label><?php _e("Show Time", "wplivechat"); ?></label>
                  </td>
              </tr>  
              <tr>
                  <td>
                     <?php _e("Redirect user to thank you page when chat is ended", "wplivechat") ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("By checking this, users will be redirected to your thank you page when a chat is completed.", "wplivechat") ?>"></i>
                  </td>
                  <td>
                      <input type="checkbox" name="wplc_redirect_to_thank_you_page" value="1" <?php echo (isset($wplc_settings['wplc_redirect_to_thank_you_page']) && intval($wplc_settings['wplc_redirect_to_thank_you_page']) == 1 ? "checked" : "" ); ?> />
                      <input type="text" name="wplc_redirect_thank_you_url" value="<?php echo (isset($wplc_settings['wplc_redirect_thank_you_url']) ?  urldecode($wplc_settings['wplc_redirect_thank_you_url']) : '' ); ?>" placeholder="<?php _e('Thank You Page URL', 'wplivechat'); ?>" />
                  </td>
              </tr> 
				<?php
				if(defined('WPLC_PLUGIN'))
				{
					?>
					<tr>
						<td>
							<?php
							_e('Disable Emojis', 'wplivechat');
							?>
						</td>
						<td>
						<input type="checkbox" name="wplc_disable_emojis"
							<?php
							if(!empty($wplc_settings['wplc_disable_emojis']))
								echo 'checked="checked"';
							?>
							/>
					</tr>
					<?php
				}
				?>
          </table>     
		  
		<?php
		if (isset($wplc_acbc_data['wplc_pro_chat_notification']) && $wplc_acbc_data['wplc_pro_chat_notification'] == "yes") { $wplc_pro_chat_notification = "CHECKED"; } else { }

	    echo "<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>";
		echo "	<tr>";
		echo "  	<td width='300'>";
		echo "      	".__("Display typing indicator", "wplivechat")." <i class='fa fa-question-circle wplc_light_grey wplc_settings_tooltip' title='".__("Display the \"typing...\" animation in the chat window as soon as an agent or visitor is typing.", "wplivechat")."'></i>";
		echo "  	</td>";
		echo "  	<td>";    
		echo "      	<input type=\"checkbox\" name=\"wplc_typing_enabled\" value=\"1\"";
		if (isset($wplc_settings['wplc_typing_enabled']) && intval($wplc_settings['wplc_typing_enabled']) == 1) { echo "checked"; }
		echo "/>";
		echo " 			<small><em>".__("For non-cloud server users, please note that this will increase the amount of server resources required.", "wplivechat")." </em></small>";
		echo "  	</td>";
		echo "</tr>";


	  ?>
	                    
	        <tr>
	            <td width='420' valign='top'>
	                <?php _e("Name ", "wplivechat") ?>:
	            </td>
	            <td>
	                <input id='wplc_pro_name' name='wplc_pro_name' type='text' size='50' maxlength='50' class='regular-text' value='<?php echo isset( $wplc_acbc_data['wplc_chat_name'] ) ? stripslashes($wplc_acbc_data['wplc_chat_name']) : ''; ?>' />
	            </td>
	        </tr>
	        <tr>
	            <td width='420' valign='top'>
	                - <?php _e("Use WordPress name instead", "wplivechat") ?>:
	            </td>
	            <td>
	                <input id='wplc_use_wp_name' name='wplc_use_wp_name' value='1' type='checkbox' <?php echo (isset($wplc_acbc_data['wplc_use_wp_name']) && intval($wplc_acbc_data['wplc_use_wp_name']) == 1 ? "checked" : "")  ?>/> <small><em><?php _e("Note: 'Name' field will be ignored", "wplivechat")?></em></small>
	            </td>
	        </tr>

	        <?php 

	        	if (isset($wplc_settings['wplc_ringtone'])) {
	        		$wplc_ringtone_selected =  str_replace("http:", "", $wplc_settings['wplc_ringtone'] );
	        	} else {
	        		$wplc_ringtone_selected = WPLC_PLUGIN_URL.'includes/sounds/general/ring.wav';
	        	}


	        ?>


	        <tr>
	            <td width='420' valign='top'>
	                <?php _e("Incoming chat ring tone", "wplivechat") ?>:
	            </td>
	            <td>
	            	<select name='wplc_ringtone' id='wplc_ringtone'>
	            	<?php $wplc_ringtone = str_replace("http:", "", WPLC_PLUGIN_URL.'includes/sounds/general/ring.wav' ); ?>
	            		<option <?php if ($wplc_ringtone_selected == WPLC_PLUGIN_URL.'includes/sounds/general/ring.wav') { echo "selected"; } ?> value='<?php echo $wplc_ringtone; ?>'><?php _e("Default","wplivechat"); ?></option>
	                <?php

	                	/* Thank you to the following authors of the sound files used in WP Live Chat Support

						(1) Mike Koenig Sound - Store door chime, Mario jump, ringing phone, Rooster, ship bell
						(2) Freesfx.com
						(3) Corsica - Elevator ding sound, Air plane ding
						(4) Brian Rocca - Pin dropping
						(5) Marianne Gagnon - Robot blip
						(6) Caroline Ford - Drop metal thing
						(7) bennstir - Doorbell

	                	 */
	                	$path = WPLC_PLUGIN_DIR."/includes/sounds/";

						$files = scandir($path);

						foreach ($files as &$value) {

							if ( is_dir( $path . $value ) ) {
								continue;
							}

							$available_tones = plugin_dir_url( $path.$value ).$value;							
							$http_stripped_tone = str_replace( "http:", "", $available_tones );
							$wplc_new_sanitized_tone = str_replace( "https:", "", $http_stripped_tone );
							
							if ($value == "." || $value == "..") { } else {
								$fname = $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value);
								$fname = str_replace("_"," ",$fname);
								if ($wplc_ringtone_selected == $wplc_new_sanitized_tone) { $sel = "selected"; } else { $sel = ""; }
								$new_ringtone = str_replace("http:", "", plugin_dir_url( $path.$value ).$value );
						    	echo "<option value='".$new_ringtone."' $sel>".$fname."</option>";
						    }
						}
	                ?>
	            </select>
	            <button type='button' id='wplc_sample_ring_tone'><i class='fa fa-play'></i></button>
	            </td>
            </tr>

			<?php
	
			if (isset($wplc_settings['wplc_messagetone'])) {
				$wplc_messagetone_selected =  str_replace("http:", "", $wplc_settings['wplc_messagetone'] );
			} else {
				$wplc_messagetone_selected = WPLC_PLUGIN_URL.'includes/sounds/general/ring.wav';
			}
	
	
			?>
	
	
			<tr>
				<td width='420' valign='top'>
					<?php _e("Incoming message tone", "wplivechat") ?>:
				</td>
				<td>
					<select name='wplc_messagetone' id='wplc_messagetone'>
						<?php $wplc_messagetone = str_replace("http:", "", realpath(WPLC_PLUGIN_URL.'includes/sounds/general/ding.mp3') ); ?>
						<option <?php if ($wplc_messagetone_selected == WPLC_PLUGIN_URL.'includes/sounds/general/ding.mp3') { echo "selected"; } ?> value='<?php echo $wplc_messagetone; ?>'><?php _e("Default","wplivechat"); ?></option>
						<?php
						$path = WPLC_PLUGIN_DIR."/includes/sounds/message/";
						$files = scandir($path);
	
						foreach ($files as &$value) {

							if ( is_dir( $path . $value ) ) {
								continue;
							}

							$available_tones = plugin_dir_url( $path.$value ).$value;
							$http_stripped_tone = str_replace( "http:", "", $available_tones );
							$wplc_new_sanitized_tone = str_replace( "https:", "", $http_stripped_tone );
	
							if ($value == "." || $value == "..") { } else {
								$fname = $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value);
								$fname = str_replace("_"," ",$fname);
								if ($wplc_messagetone_selected == $wplc_new_sanitized_tone) { $sel = "selected"; } else { $sel = ""; }
								$new_messagetone = str_replace("http:", "", plugin_dir_url( $path.$value ).$value );
								echo "<option value='".$new_messagetone."' $sel>".$fname."</option>";
							}
						}
						?>
					</select>
					<button type='button' id='wplc_sample_message_tone'><i class='fa fa-play'></i></button>
				</td>
			</tr>
	                    
	        <?php
	        $wplc_current_user = wp_get_current_user();
	        $wplc_current_picture = isset( $wplc_acbc_data['wplc_chat_pic'] ) ? urldecode($wplc_acbc_data['wplc_chat_pic']) : '';
	        $wplc_current_logo = isset( $wplc_acbc_data['wplc_chat_logo'] ) ? urldecode($wplc_acbc_data['wplc_chat_logo']) : '';
	        $wplc_current_icon = isset( $wplc_acbc_data['wplc_chat_icon'] ) ? urldecode($wplc_acbc_data['wplc_chat_icon']) : '';
	        ?>


        <!-- Chat Icon-->
        <tr class='wplc-icon-area'>
            <td width='300' valign='top'>
				<?php _e("Icon", "wplivechat") ?>:
            </td>
            <td>
                    <span id="wplc_icon_area">
                        <img src="<?php echo trim($wplc_current_icon); ?>" width="50px"/>
                    </span>
                <input id="wplc_upload_icon" name="wplc_upload_icon" type="hidden" size="35" class="regular-text" maxlength="700" value=" <?php echo base64_encode($wplc_current_icon) ?>"/>
                <br/>
                <input id="wplc_btn_upload_icon" name="wplc_btn_upload_icon" type="button" value="<?php _e("Upload Icon", "wplivechat") ?>" />
                <input id="wplc_btn_select_default_icon" name="wplc_btn_select_default_icon" type="button" value="<?php _e("Select Default Icon", "wplivechat") ?>" />
                <br/>
                <input id="wplc_btn_remove_icon" name="wplc_btn_remove_icon" type="button" value="<?php _e("Remove Icon", "wplivechat") ?>" />
				<?php _e("Recomended Size 50px x 50px", "wplivechat") ?>
				
				<div id="wplc_default_chat_icons" style="display: none">
					<strong><?php _e("Select Default Icon", "wplivechat"); ?></strong>
					<img class="wplc_default_chat_icon_selector" src="<?php echo WPLC_PLUGIN_URL . '/images/chaticon.png'; ?>">	
					<img class="wplc_default_chat_icon_selector" src="<?php echo WPLC_PLUGIN_URL . '/images/default_icon_1.png'; ?>">	
					<img class="wplc_default_chat_icon_selector" src="<?php echo WPLC_PLUGIN_URL . '/images/default_icon_2.png'; ?>">
					<img class="wplc_default_chat_icon_selector" src="<?php echo WPLC_PLUGIN_URL . '/images/default_icon_3.png'; ?>">
				</div>

				<style type="text/css">
					#wplc_default_chat_icons {
					    margin-top: 10px;
					    padding: 5px;
					    border: 1px solid #eee;
					    border-radius: 5px; 
					}
					#wplc_default_chat_icons strong {
						display: block;
						margin-bottom: 5px;
					}
					.wplc_default_chat_icon_selector {
						background-color: #ccc;
						display: inline-block;
						vertical-align: top;
						margin-right: 5px;
						max-width: 50px;
						border-radius: 100px;
					}
					.wplc_default_chat_icon_selector:hover {
						cursor: pointer;
						background-color: #bbb;
					}
				</style>
            </td>
        </tr>

	        <tr class='wplc-pic-area'>
	            <td width='300' valign='top'>
	                <?php _e("Picture", "wplivechat") ?>:
	            </td>
	            <td>
	                <span id="wplc_pic_area">
	                    <img src="<?php echo $wplc_current_picture ?>" width="60px"/>
	                </span>
	                <input id="wplc_upload_pic" name="wplc_upload_pic" type="hidden" size="35" class="regular-text" maxlength="700" value="<?php echo base64_encode($wplc_current_picture) ?>"/> 
	                <br/>
	                <input id="wplc_btn_upload_pic" name="wplc_btn_upload_pic" type="button" value="<?php _e("Upload Image", "wplivechat") ?>" />
	                <br/>
	                <input id="wplc_btn_remove_pic" name="wplc_btn_remove_pic" type="button" value="<?php _e("Remove Image", "wplivechat") ?>" />
	                <?php _e("Recomended Size 60px x 60px", "wplivechat") ?>
	            </td>
            </tr>

            <!-- Chat Logo-->
            <tr class='wplc-logo-area'>
                <td width='300' valign='top'>
                    <?php _e("Logo", "wplivechat") ?>:
                </td>
                <td>
                    <span id="wplc_logo_area">
                        <img src="<?php echo trim($wplc_current_logo); ?>" width="100px"/>
                    </span> 
                    <input id="wplc_upload_logo" name="wplc_upload_logo" type="hidden" size="35" class="regular-text" maxlength="700" value=" <?php echo base64_encode($wplc_current_logo) ?>"/> 
                    <br/>
                    <input id="wplc_btn_upload_logo" name="wplc_btn_upload_logo" type="button" value="<?php _e("Upload Logo", "wplivechat") ?>" />
                    <br/>
                    <input id="wplc_btn_remove_logo" name="wplc_btn_remove_logo" type="button" value="<?php _e("Remove Logo", "wplivechat") ?>" />
                    <?php _e("Recomended Size 250px x 40px", "wplivechat") ?>
                </td>
            </tr>
	                    
            <tr>
                <td width='300' valign='top'>
                    <?php _e("Chat delay (seconds)", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("How long it takes for your chat window to pop up", "wplivechat") ?>"></i>
                </td>
                <td>
                    <input id="wplc_pro_delay" name="wplc_pro_delay" type="text" size="6" maxlength="4" value="<?php echo isset( $wplc_acbc_data['wplc_chat_delay'] ) ? stripslashes($wplc_acbc_data['wplc_chat_delay']) : ''; ?>" />
                </td>
            </tr>
            <!-- Chat Notification if want to chat -->
            <tr>
                <td width='300' valign='top'>
                    <?php _e("Chat notifications", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Alert me via email as soon as someone wants to chat (while online only)", "wplivechat"); ?>"></i>
                </td>
                <td>
                    <input id="wplc_pro_chat_notification" name="wplc_pro_chat_notification" type="checkbox" value="yes" <?php if (isset($wplc_pro_chat_notification)) { echo $wplc_pro_chat_notification; } ?>/>                    
                </td>
            </tr>

        </table>

        <h3><?php _e("User Experience", 'wplivechat') ?></h3>
        <hr>
        <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
            <tbody>
                <tr>
                    <td width='300' valign='top'><?php _e("Enable Text Editor", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Adds advanced text editor features, such as links, text styling, and more!", "wplivechat") ?>"></i></td> 
                    <td><input id='wplc_ux_editor' name='wplc_ux_editor' type='checkbox' <?php echo((isset($wplc_settings['wplc_ux_editor']) && $wplc_settings['wplc_ux_editor'] != "0") ? "checked" : "") ?> /> </td>    
                </tr>
                <tr>
                    <td width='300' valign='top'><?php _e("Enable File Sharing", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Adds file sharing to your chat box!", "wplivechat") ?>"></i></td> 
                    <td><input id='wplc_ux_file_share' name='wplc_ux_file_share' type='checkbox' <?php echo((isset($wplc_settings['wplc_ux_file_share']) && $wplc_settings['wplc_ux_file_share'] != "0") ? "checked" : "") ?> />  </td>   
                </tr>
                <tr>
                    <td width='300' valign='top'><?php _e("Enable Experience Ratings", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Allows users to rate the chat experience with an agent.", "wplivechat") ?>"></i></td> 
                    <td><input id='wplc_ux_exp_rating' name='wplc_ux_exp_rating' type='checkbox' <?php echo((isset($wplc_settings['wplc_ux_exp_rating']) && $wplc_settings['wplc_ux_exp_rating'] != "0") ? "checked" : "") ?> />  </td>   
                </tr>
            </tbody>
        </table>

	        <h3><?php _e("Social", 'wplivechat') ?></h3>
	        <hr>

	        <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
	            <tbody>
	                <tr>
	                    <td width='300' valign='top'><?php _e("Facebook URL", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Link your Facebook page here. Leave blank to hide", "wplivechat") ?>"></i></td> 
	                    <td><input id='wplc_social_fb' name='wplc_social_fb' placeholder="<?php _e("Facebook URL...", "wplivechat") ?>" type='text' value="<?php echo (isset($wplc_acbc_data['wplc_social_fb']) ? urldecode($wplc_acbc_data['wplc_social_fb']) : "") ?>" /> </td>    
	                </tr>
	                <tr>
	                    <td width='300' valign='top'><?php _e("Twitter URL", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Link your Twitter page here. Leave blank to hide", "wplivechat") ?>"></i></td> 
	                    <td><input id='wplc_social_tw' name='wplc_social_tw' placeholder="<?php _e("Twitter URL...", "wplivechat") ?>" type='text' value="<?php echo (isset($wplc_acbc_data['wplc_social_tw']) ? urldecode($wplc_acbc_data['wplc_social_tw']) : "") ?>" />  </td>   

	                </tr>
	            </tbody>
	        </table>
		  

          <?php do_action('wplc_hook_admin_settings_chat_box_settings_after'); ?>

      </div>
                  <div id="tabs-3">
                <h3><?php _e("Offline Messages", 'wplivechat') ?></h3> 
                <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
                    <tr>
                        <td width='300'>
<?php _e("Do not allow users to send offline messages", "wplivechat") ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("The chat window will be hidden when it is offline. Users will not be able to send offline messages to you", "wplivechat") ?>"></i>
                        </td>
                        <td>
                            <input type="checkbox" name="wplc_hide_when_offline" value="1" <?php
if (isset($wplc_settings['wplc_hide_when_offline']) && intval($wplc_settings['wplc_hide_when_offline']) == 1) {
    echo "checked";
}
?>/>
                        </td>
                    </tr>
                    <tr>
                        <td width="300" valign="top"><?php _e("Offline Chat Box Title", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_na" name="wplc_pro_na" type="text" size="50" maxlength="50" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_na'])) { echo stripslashes($wplc_settings['wplc_pro_na']); } ?>" /> <br />


                        </td>
                    </tr>
                    <tr>
                        <td width="300" valign="top"><?php _e("Offline Text Fields", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_offline1" name="wplc_pro_offline1" type="text" size="50" maxlength="150" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_offline1'])) { echo stripslashes($wplc_settings['wplc_pro_offline1']); } ?>" /> <br />
                            <input id="wplc_pro_offline2" name="wplc_pro_offline2" type="text" size="50" maxlength="50" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_offline2'])) { echo stripslashes($wplc_settings['wplc_pro_offline2']); } ?>" /> <br />
                            <input id="wplc_pro_offline3" name="wplc_pro_offline3" type="text" size="50" maxlength="150" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_offline3'])) { echo stripslashes($wplc_settings['wplc_pro_offline3']); } ?>" /> <br />


                        </td>
                    </tr>
                    <tr>
                        <td width="300" valign="top"><?php _e("Offline Button Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_offline_btn" name="wplc_pro_offline_btn" type="text" size="50" maxlength="50" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_offline_btn'])) { echo stripslashes($wplc_settings['wplc_pro_offline_btn']); } ?>" /> <br />
                        </td>
                    </tr>
                    <tr>
                        <td width="300" valign="top"><?php _e("Offline Send Button Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_offline_btn_send" name="wplc_pro_offline_btn_send" type="text" size="50" maxlength="50" class="regular-text" value="<?php if (isset($wplc_settings['wplc_pro_offline_btn_send'])) { echo stripslashes($wplc_settings['wplc_pro_offline_btn_send']); } ?>" /> <br />
                        </td>
                    </tr>
                    <tr>
                        <td width="300" valign="top"><?php _e("Custom fields", "wplivechat") ?>:</td>
                        <td>
                            <?php do_action( "wplc_hook_offline_custom_fields_integration_settings" ); ?>
                        </td>
                    </tr>

                </table>

                <h4><?php _e("Email settings", 'wplivechat') ?></h4> 


                <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
                    <tr>
                        <td width='300' valign='top'>
<?php _e("Email Address", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Email address where offline messages are delivered to. Use comma separated email addresses to send to more than one email address", "wplivechat") ?>"></i>
                        </td>
                        <td>
                            <input id="wplc_pro_chat_email_address" name="wplc_pro_chat_email_address" class="regular-text" type="text" value="<?php if (isset($wplc_settings['wplc_pro_chat_email_address'])) {
    echo $wplc_settings['wplc_pro_chat_email_address']; } ?>" />
                        </td>
                    </tr>

                     <tr>
                        <td width='300' valign='top'>
                            <?php _e("Subject", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("User name will be appended to the end of the subject.", "wplivechat") ?>"></i>
                        </td>
                        <td>
                            <input id="wplc_pro_chat_email_offline_subject" name="wplc_pro_chat_email_offline_subject" class="regular-text" type="text" value="<?php echo(isset($wplc_settings['wplc_pro_chat_email_offline_subject']) ? $wplc_settings['wplc_pro_chat_email_offline_subject'] : ""); ?>" placeholder="<?php echo __("WP Live Chat Support - Offline Message from ", "wplivechat"); ?>"/>
                        </td>
                    </tr>

                </table>

                
					  
				<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
					<tr>
						<td width="300" valign="top"><?php _e("Enable an Auto Responder", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Send your visitors an email as soon as they send you an offline message", "wplivechat") ?>"></i></td>
						<td>
							<input id="wplc_ar_enable" name="wplc_ar_enable" type="checkbox" value="1" <?php if( isset( $wplc_auto_responder_settings['wplc_ar_enable'] ) ) { echo "checked"; } ?> /> <br />
						</td>
					</tr>
					<tr>
						<td width="300" valign="top"><?php _e("Auto Responder 'From' Name", "wplivechat") ?>: </td>
						<td>
							<input type="text" name="wplc_ar_from_name" id="wplc_ar_from_name" class="regular-text" value="<?php if( isset( $wplc_auto_responder_settings['wplc_ar_from_name'] ) ) { echo stripslashes($wplc_auto_responder_settings['wplc_ar_from_name']); } ?>" />
						</td>
					</tr>
					<tr>
						<td width="300" valign="top"><?php _e("Auto Responder 'From' Email Address", "wplivechat") ?>: </td>
						<td>
							<input type="text" name="wplc_ar_from_email" id="wplc_ar_from_email" class="regular-text" value="<?php if( isset( $wplc_auto_responder_settings['wplc_ar_from_email'] ) ) { echo $wplc_auto_responder_settings['wplc_ar_from_email']; } ?>" /> 
						</td>
					</tr>
					<tr>
						<td width="300" valign="top"><?php _e("Auto Responder Subject Line", "wplivechat") ?>: </td>
						<td>
							<input type="text" name="wplc_ar_subject" id="wplc_ar_subject" class="regular-text" value="<?php if( isset( $wplc_auto_responder_settings['wplc_ar_subject'] ) ) { echo $wplc_auto_responder_settings['wplc_ar_subject']; } ?>" />
						</td>
					</tr>
					<tr>
						<td width="300" valign="top"><?php _e("Auto Responder Body", "wplivechat") ?>: <br/></td>
						<td>
							<textarea name="wplc_ar_body" id="wplc_ar_body" rows="6" style="width:50%;"><?php if( isset( $wplc_auto_responder_settings['wplc_ar_body'] ) ) { echo stripslashes( $wplc_auto_responder_settings['wplc_ar_body'] ); } ?></textarea>
							<p class="description"><small><?php _e("HTML and the following shortcodes can be used", "wplivechat"); ?>: <?php _e("User's name", "wplivechat"); ?>: {wplc-user-name} <?php _e("User's email address", "wplivechat"); ?>: {wplc-email-address}</small></p>
						</td>
					</tr>
				</table> 
					  
            </div>

      
      
      <div id="tabs-4">
                <style>
                    .wplc_theme_block img{
                        border: 1px solid #CCC;
                        border-radius: 5px;
                        padding: 5px;
                        margin: 5px;
                    }         
                    .wplc_theme_single{
                        width: 162px;
                        height: 162px;
                        text-align: center;
                        display: inline-block;
                        vertical-align: top;
                        margin: 5px;
                    }
                                            .wplc_animation_block div{
                            display: inline-block;
                            width: 150px;
                            height: 150px;
                            border: 1px solid #CCC;
                            border-radius: 5px;
                            text-align: center;  
                            margin: 10px;
                        }
                        .wplc_animation_block i{
                            font-size: 3em;
                            line-height: 150px;
                        }
                        .wplc_animation_block .wplc_red{
                            color: #E31230;
                        }
                        .wplc_animation_block .wplc_orange{
                            color: #EB832C;
                        }
                        .wplc_animation_active, .wplc_theme_active{
                            box-shadow: 2px 2px 2px #666666;
                        }
                </style>
                <style>
                  .wplc_animation_block div{
                      display: inline-block;
                      width: 150px;
                      height: 150px;
                      border: 1px solid #CCC;
                      border-radius: 5px;
                      text-align: center;  
                      margin: 10px;
                  }
                  .wplc_animation_block i{
                      font-size: 3em;
                      line-height: 150px;
                  }
                  .wplc_animation_block .wplc_red{
                      color: #E31230;
                  }
                  .wplc_animation_block .wplc_orange{
                      color: #EB832C;
                  }
                  .wplc_animation_active{
                      box-shadow: 2px 2px 2px #CCC;
                  }
              </style>
          <h3><?php _e("Styling",'wplivechat')?></h3>
          <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
              

              <tr style='margin-bottom: 10px;'>
                <td width='200'><label for=""><?php _e('Choose a theme', 'wplivechat'); ?></label></td>
                <td>    
                    <div class='wplc_theme_block'>
                        <div class='wplc_theme_image' id=''>
                            <div class='wplc_theme_single'>
                                <img style='width:162px;' src='<?php echo WPLC_PLUGIN_URL.'images/themes/newtheme-1.jpg'; ?>' title="<?php _e('Classic', 'wplivechat'); ?>" alt="<?php _e('Classic', 'wplivechat'); ?>" class='<?php if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-1') { echo 'wplc_theme_active'; } ?>' id='wplc_newtheme_1'/>
                                <?php _e('Classic', 'wplivechat'); ?>
                            </div>
                            <div class='wplc_theme_single'>
                                <img style='width:162px;'  src='<?php echo WPLC_PLUGIN_URL.'images/themes/newtheme-2.jpg'; ?>' title="<?php _e('Modern', 'wplivechat'); ?>" alt="<?php _e('Modern', 'wplivechat'); ?>" class='<?php if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-2') { echo 'wplc_theme_active'; } ?>' id='wplc_newtheme_2'/>
                                <?php _e('Modern', 'wplivechat'); ?>
                            </div>

                        </div>
                    </div>
                    <input type="radio" name="wplc_newtheme" value="theme-1" class="wplc_hide_input" id="wplc_new_rb_theme_1" <?php if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-1') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_newtheme" value="theme-2" class="wplc_hide_input" id="wplc_new_rb_theme_2" <?php if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == 'theme-2') { echo 'checked'; } ?>/>

                </td>
              </tr>
              <tr height="30">
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
              </tr> 

              <tr style='margin-bottom: 10px;'>
                <td><label for=""><?php _e('Color Scheme', 'wplivechat'); ?></label></td>
                <td>    
                    <div class='wplc_theme_block'>
                        <div class='wplc_palette'>
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-default') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_default'>
                                  <div class='wplc-palette-top' style='background-color:#ED832F;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#EEE;'></div>
                                  <div class='wplc-palette-top' style='background-color:#666;'></div>
                                </div>
                            </div>

                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-1') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_1'>
                                  <div class='wplc-palette-top' style='background-color:#DB0000;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#000;'></div>
                                  <div class='wplc-palette-top' style='background-color:#666;'></div>
                                </div>
                            </div>
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-2') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_2'>
                                  <div class='wplc-palette-top' style='background-color:#000;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#888;'></div>
                                  <div class='wplc-palette-top' style='background-color:#666;'></div>
                                </div>
                            </div>
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-3') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_3'>
                                  <div class='wplc-palette-top' style='background-color:#B97B9D;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#EEE;'></div>
                                  <div class='wplc-palette-top' style='background-color:#5A0031;'></div>
                                </div>
                            </div>
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-4') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_4'>
                                  <div class='wplc-palette-top' style='background-color:#1A14DB;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FDFDFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#7F7FB3;'></div>
                                  <div class='wplc-palette-top' style='background-color:#666;'></div>
                                </div>
                            </div>
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-5') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_5'>
                                  <div class='wplc-palette-top' style='background-color:#3DCC13;'></div>
                                  <div class='wplc-palette-top' style='background-color:#FDFDFF;'></div>
                                  <div class='wplc-palette-top' style='background-color:#EEE;'></div>
                                  <div class='wplc-palette-top' style='background-color:#666;'></div>
                                </div>
                            </div>                            
                            <div class='wplc_palette_single'>
                                <div class='wplc-palette-selection <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-6') { echo 'wplc_theme_active'; } ?>' id='wplc_theme_6'>
                                  <div class='wplc-palette-top' style='padding-top:3px'><?php _e("Choose","wplivechat"); ?></div>
                                  <div class='wplc-palette-top' style='padding-top:3px'><?php _e("Your","wplivechat"); ?></div>
                                  <div class='wplc-palette-top' style='padding-top:3px'><?php _e("Colors","wplivechat"); ?></div>
                                  <div class='wplc-palette-top' style='padding-top:3px'><?php _e("Below","wplivechat"); ?></div>
                                </div>
                            </div> 


                        </div>
                    </div>
                    <input type="radio" name="wplc_theme" value="theme-default" class="wplc_hide_input" id="wplc_rb_theme_default" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-default') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-1" class="wplc_hide_input" id="wplc_rb_theme_1" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-1') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-2" class="wplc_hide_input" id="wplc_rb_theme_2" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-2') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-3" class="wplc_hide_input" id="wplc_rb_theme_3" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-3') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-4" class="wplc_hide_input" id="wplc_rb_theme_4" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-4') { echo 'checked';  } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-5" class="wplc_hide_input" id="wplc_rb_theme_5" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-5') { echo 'checked'; } ?>/>
                    <input type="radio" name="wplc_theme" value="theme-6" class="wplc_hide_input" id="wplc_rb_theme_6" <?php if (isset($wplc_settings['wplc_theme']) && $wplc_settings['wplc_theme'] == 'theme-6') { echo 'checked'; } ?>/>

                </td>
              </tr>

              <tr>
                  <td width='300' valign='top'><?php _e("Chat background","wplivechat")?>:</td>
                  <td>
                      
                      <select id='wplc_settings_bg' name='wplc_settings_bg'>
                          <option value="cloudy.jpg" <?php if (!isset($wplc_settings['wplc_settings_bg']) || ($wplc_settings['wplc_settings_bg'] == "cloudy.jpg") ) { echo "selected"; } ?>><?php _e("Cloudy","wplivechat"); ?></option>
                          <option value="geometry.jpg" <?php if (isset($wplc_settings['wplc_settings_bg']) && $wplc_settings['wplc_settings_bg'] == "geometry.jpg") { echo "selected"; } ?>><?php _e("Geometry","wplivechat"); ?></option>
                          <option value="tech.jpg" <?php if (isset($wplc_settings['wplc_settings_bg']) && $wplc_settings['wplc_settings_bg'] == "tech.jpg") { echo "selected"; } ?>><?php _e("Tech","wplivechat"); ?></option>
                          <option value="social.jpg" <?php if (isset($wplc_settings['wplc_settings_bg']) && $wplc_settings['wplc_settings_bg'] == "social.jpg") { echo "selected"; } ?>><?php _e("Social","wplivechat"); ?></option>
                          <option value="0" <?php if (isset($wplc_settings['wplc_settings_bg']) && $wplc_settings['wplc_settings_bg'] == "0") { echo "selected"; } ?>><?php _e("None","wplivechat"); ?></option>
                      </select>
                  </td>
              </tr>              
              <tr>
                  <td width='200' valign='top'><?php _e("Palette Color 1","wplivechat")?>:</td>
                  <td>
                      <input id="wplc_settings_color1" name="wplc_settings_color1" type="text" class="color" value="<?php if (isset($wplc_settings_color1)) { echo $wplc_settings_color1; } else { echo 'ED832F'; } ?>" />
                  </td>
              </tr>
              <tr>
                  <td width='200' valign='top'><?php _e("Palette Color 2","wplivechat")?>:</td>
                  <td>
                      <input id="wplc_settings_color2" name="wplc_settings_color2" type="text" class="color" value="<?php if (isset($wplc_settings_color2)) { echo $wplc_settings_color2; } else { echo 'FFFFFF'; } ?>" />
                  </td>
              </tr>
              <tr>
                  <td width='200' valign='top'><?php _e("Palette Color 3","wplivechat")?>:</td>
                  <td>
                      <input id="wplc_settings_color3" name="wplc_settings_color3" type="text" class="color" value="<?php if (isset($wplc_settings_color3)) { echo $wplc_settings_color3; } else { echo 'EEEEEE'; } ?>" />
                  </td>
              </tr>
              <tr>
                  <td width='200' valign='top'><?php _e("Palette Color 4","wplivechat")?>:</td>
                  <td>
                      <input id="wplc_settings_color4" name="wplc_settings_color4" type="text" class="color" value="<?php if (isset($wplc_settings_color4)) { echo $wplc_settings_color4; } else { echo '666666'; } ?>" />
                  </td>
              </tr>

                    <tr>
                        <td width="200" valign="top"><?php _e("I'm using a localization plugin", "wplivechat") ?></td>
                        <td>
                            <input type="checkbox" name="wplc_using_localization_plugin" id="wplc_using_localization_plugin" value="1" <?php if (isset($wplc_settings['wplc_using_localization_plugin']) && $wplc_settings['wplc_using_localization_plugin'] == 1) { echo 'checked'; } ?>/>
                            <br/><small><?php echo sprintf( __("Enable this if you are using a localization plugin. Should you wish to change the below strings with this option enabled, please visit the documentation %s", "wplivechat"), "<a href='https://wp-livechat.com/docs/features/quickview/plugin-features/localization/' target='_BLANK'>".__("here", "wplivechat") ); ?></small>
                        </td>
                    </tr>

                  <tr style='height:30px;'><td></td><td></td></tr>
                                <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("First Section Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_fst1" name="wplc_pro_fst1" type="text" size="50" maxlength="50" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_fst1']) ?>" /> <br />
                            <input id="wplc_pro_fst2" name="wplc_pro_fst2" type="text" size="50" maxlength="50" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_fst2']) ?>" /> <br />
                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("Intro Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_intro" name="wplc_pro_intro" type="text" size="50" maxlength="150" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_intro']) ?>" /> <br />
                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("Second Section Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_sst1" name="wplc_pro_sst1" type="text" size="50" maxlength="30" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_sst1']) ?>" /> <br />
                            <input id="wplc_pro_sst2" name="wplc_pro_sst2" type="text" size="50" maxlength="70" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_sst2']) ?>" /> <br />
                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("Reactivate Chat Section Text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_pro_tst1" name="wplc_pro_tst1" type="text" size="50" maxlength="50" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_pro_tst1']) ?>" /> <br />


                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("Welcome message", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_welcome_msg" name="wplc_welcome_msg" type="text" size="50" maxlength="350" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_welcome_msg']) ?>" /> <span class='description'><?php _e('This text is shown as soon as a user starts a chat and waits for an agent to join', 'wplivechat'); ?></span><br />
                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("No answer", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_user_no_answer" name="wplc_user_no_answer" type="text" size="50" maxlength="150" class="regular-text" value="<?php echo (isset($wplc_settings['wplc_user_no_answer']) ? stripslashes($wplc_settings['wplc_user_no_answer']) : __("There is No Answer. Please Try Again Later.","wplivechat")); ?>" /> <span class='description'><?php _e('This text is shown to the user when an agent has failed to answer a chat ', 'wplivechat'); ?></span><br />
                        </td>
                    </tr>
                    <tr class="wplc_localization_strings">
                        <td width="200" valign="top"><?php _e("Other text", "wplivechat") ?>:</td>
                        <td>
                            <input id="wplc_user_enter" name="wplc_user_enter" type="text" size="50" maxlength="150" class="regular-text" value="<?php echo stripslashes($wplc_settings['wplc_user_enter']) ?>" /><br />
                            <input id="wplc_text_chat_ended" name="wplc_text_chat_ended" type="text" size="50" maxlength="150" class="regular-text" value="<?php echo ( empty( $wplc_settings['wplc_text_chat_ended'] ) ) ? stripslashes(__("The chat has been ended by the operator.", "wplivechat")) : stripslashes( $wplc_settings['wplc_text_chat_ended'] ) ?>" /> <br />
                        </td>
                    </tr>
                        
                    <tr>
                        <td><label for=""><?php _e('Choose an animation', 'wplivechat'); ?></label></td>

                        <td>    
                            <div class='wplc_animation_block'>
                                <div class='wplc_animation_image <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-1') {
                    echo 'wplc_animation_active';
                } ?>' id='wplc_animation_1'>
                                    <i class="fa fa-arrow-circle-up wplc_orange"></i>
                                    <p><?php _e('Slide Up', 'wplivechat'); ?></p>
                                </div>
                                <div class='wplc_animation_image <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-2') {
                    echo 'wplc_animation_active';
                } ?>' id='wplc_animation_2'>
                                    <i class="fa fa-arrows-h wplc_red"></i>
                                    <p><?php _e('Slide From The Side', 'wplivechat'); ?></p>
                                </div>
                                <div class='wplc_animation_image <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-3') {
                    echo 'wplc_animation_active';
                } ?>' id='wplc_animation_3'>
                                    <i class="fa fa-arrows-alt wplc_orange"></i>
                                    <p><?php _e('Fade In', 'wplivechat'); ?></p>
                                </div>
                                <div class='wplc_animation_image <?php if ((isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-4') || !isset($wplc_settings['wplc_animation'])) {
                    echo 'wplc_animation_active';
                } ?>' id='wplc_animation_4'>
                                    <i class="fa fa-thumb-tack wplc_red"></i>
                                    <p><?php _e('No Animation', 'wplivechat'); ?></p>
                                </div>
                            </div>
                            <input type="radio" name="wplc_animation" value="animation-1" class="wplc_hide_input" id="wplc_rb_animation_1" class='wplc_hide_input' <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-1') {
                    echo 'checked';
                } ?>/>
                            <input type="radio" name="wplc_animation" value="animation-2" class="wplc_hide_input" id="wplc_rb_animation_2" class='wplc_hide_input' <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-2') {
                    echo 'checked';
                } ?>/>
                            <input type="radio" name="wplc_animation" value="animation-3" class="wplc_hide_input" id="wplc_rb_animation_3" class='wplc_hide_input' <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-3') {
                    echo 'checked';
                } ?>/>
                            <input type="radio" name="wplc_animation" value="animation-4" class="wplc_hide_input" id="wplc_rb_animation_4" class='wplc_hide_input' <?php if (isset($wplc_settings['wplc_animation']) && $wplc_settings['wplc_animation'] == 'animation-4') {
                    echo 'checked';
                } ?>/>
                        </td>
                    </tr>
					<tr>
						<td width='300' valign='top'><?php _e("Auto First Response Message","wplivechat")?>:</td>
						<td>  
							<input type="text" name="wplc_pro_auto_first_response_chat_msg" value="<?php echo $wplc_pro_auto_first_response_chat_msg; ?>">
							<span class='description'><?php _e('This message will be sent automatically after the first message is sent from the user side. Leave empty to disable.', 'wplivechat'); ?></span>
						</td>   
					</tr>

          </table>
      </div>
        <div id="tabs-5">


        <?php
			
				    $user_array = get_users(array(
	        'meta_key' => 'wplc_ma_agent',
	    ));

	    echo "<h3>".__('Current Users that are Chat Agents', 'wplivechat')."</h3>";

	    $wplc_agents = "<div class='wplc_agent_container'><ul>";

	    if ($user_array) {
	        foreach ($user_array as $user) {

	            $wplc_agents .= "<li id=\"wplc_agent_li_".$user->ID."\">";
	            $wplc_agents .= "<p><img src=\"//www.gravatar.com/avatar/" . md5($user->user_email) . "?s=80&d=mm\" /></p>";
	             $check = get_user_meta($user->ID,"wplc_chat_agent_online");
	            if ($check) {
	                $wplc_agents .= "<span class='wplc_status_box wplc_type_returning'>".__("Online","wplivechat")."</span>";
	            }
	            $wplc_agents .= "<h3>" . $user->display_name . "</h3>";
	           
	            $wplc_agents .= "<small>" . $user->user_email . "</small>";

	            $wplc_agents .= apply_filters("wplc_pro_agent_list_before_button_filter", "", $user);

	            if (get_current_user_id() == $user->ID) {
	            } else { 
	                $wplc_agents .= "<p><button class='button button-secondary wplc_remove_agent' id='wplc_remove_agent_".$user->ID."' uid='".$user->ID."'>".__("Remove","wplivechat")."</button></p>";
	            }
	            $wplc_agents .= "</li>";
	        }
	    }
	    echo $wplc_agents;
	    ?>
	    <li style='width:150px;' id='wplc_add_new_agent_box'>
	        <p><i class='fa fa-plus-circle fa-4x' style='color:#ccc;' ></i></p>
	        <h3><?php _e("Add New Agent","wplivechat"); ?></h3>
	        <select id='wplc_agent_select'>
	            <option value=''><?php _e("Select","wplivechat"); ?></option>
	        <?php 
	            $blogusers = get_users( array( 'role' => 'administrator', 'fields' => array( 'display_name','ID','user_email' ) ) );
	            // Array of stdClass objects.
	            foreach ( $blogusers as $user ) {
	                $is_agent = get_user_meta(intval( $user->ID ), 'wplc_ma_agent', true);            
	                if(!$is_agent){ echo '<option id="wplc_selected_agent_'. intval( $user->ID ) .'" em="' . md5(sanitize_email( $user->user_email )) . '" uid="' . intval( $user->ID ) . '" em2="' . sanitize_email( $user->user_email ) . '"  name="' . sanitize_text_field( $user->display_name ) . '" value="' . intval( $user->ID ) . '">' . sanitize_text_field( $user->display_name ) . ' ('.__('Administrator','wplivechat').')</option>'; }
	            }
	            $blogusers = get_users( array( 'role' => 'editor', 'fields' => array( 'display_name','ID','user_email' ) ) );
	            // Array of stdClass objects.
	            foreach ( $blogusers as $user ) {
	                $is_agent = get_user_meta(intval( $user->ID ), 'wplc_ma_agent', true);            
	                if(!$is_agent){ echo '<option id="wplc_selected_agent_'. intval( $user->ID ) .'" em="' . md5(sanitize_email( $user->user_email )) . '" uid="' . intval( $user->ID ) . '" em2="' . sanitize_email( $user->user_email ) . '"  name="' . sanitize_text_field( $user->display_name ) . '" value="' . intval( $user->ID ) . '">' . sanitize_text_field( $user->display_name ) . ' ('.__('Editor','wplivechat').')</option>'; }
	            }
	            $blogusers = get_users( array( 'role' => 'author', 'fields' => array( 'display_name','ID','user_email' ) ) );
	            // Array of stdClass objects.
	            foreach ( $blogusers as $user ) {
	                $is_agent = get_user_meta(intval( $user->ID ), 'wplc_ma_agent', true);            
	                if(!$is_agent){ echo '<option id="wplc_selected_agent_'. intval( $user->ID ) .'" em="' . md5(sanitize_email( $user->user_email )) . '" uid="' . intval( $user->ID ) . '" em2="' . sanitize_email( $user->user_email ) . '"  name="' . sanitize_text_field( $user->display_name ) . '" value="' . intval( $user->ID ) . '">' . sanitize_text_field( $user->display_name ) . ' ('.__('Author','wplivechat').')</option>'; }
	            }
	        ?>
	        </select>
	        <p><button class='button button-secondary' id='wplc_add_agent'><?php _e("Add Agent","wplivechat"); ?></button></p>
	    </li>
	</ul>
	</div>
	    
	                <hr/>
	                <p class="description"><?php _e("Should you wish to add a user that has a role less than 'Author', please go to the <a href='./users.php'>Users</a> page, select the relevant user, click Edit and scroll to the bottom of the page and enable the 'Chat Agent' checkbox.", "wplivechat"); ?></p>
	                <p class="description"><?php _e("If there are no chat agents online, the chat will show as offline", "wplivechat"); ?></p>

            
        </div>
        <div id="tabs-7">           
            <h3><?php _e("Blocked Visitors - Based on IP Address", "wplivechat") ?></h3>
            <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>                       
              <tr>
                <td>
                  <textarea name="wplc_ban_users_ip" style="width: 50%; min-height: 200px;" placeholder="<?php _e('Enter each IP Address you would like to block on a new line', 'wplivechat'); ?>" autocomplete="false"><?php
                      $ip_addresses = get_option('WPLC_BANNED_IP_ADDRESSES'); 
                      if($ip_addresses){
                          $ip_addresses = maybe_unserialize($ip_addresses);
                          if ($ip_addresses && is_array($ip_addresses)) {
                              foreach($ip_addresses as $ip){
                                  echo $ip."\n";
                              }
                          }
                      }
                  ?></textarea>  
                  <p class="description"><?php _e('Blocking a user\'s IP Address here will hide the chat window from them, preventing them from chatting with you. Each IP Address must be on a new line', 'wplivechat'); ?></p>
                </td>
              </tr>
            </table>
        </div>

		<?php

	$content = "";

	$content .= "<div id='wplc-business-hours'>";

	$content .= "<h2>".__("Business Hours", "wplivechat")."</h2>";
	$content .= "<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' >";

	$content .= "<tr>";
	$content .= "<td width='300'>".__("Enable Business Hours", "wplivechat")."</td>";
	if( isset( $wplc_bh_settings['enabled'] ) && $wplc_bh_settings['enabled'] === true ) { $checked = 'checked'; } else { $checked = ''; }
	$content .= "<td><input type='checkbox' name='wplc_bh_enable' id='wplc_bh_enable' value='1' $checked /></td>";
	$content .= "</tr>";

	$content .= "<tr>";
	$content .= "<td width='300'>".__("Display Interval", "wplivechat")."</td>";
	$content .= "<td>";
	$content .= "<select name='wplc_bh_interval' id='wplc_bh_interval'>";
	$wplc_intervals = array(
		'0' => __("Daily", "wplivechat"),
		'1' => __("Week Days", "wplivechat"),
		'2' => __("Weekends", "wplivechat")
	);
	$wplc_intervals = apply_filters( "wplc_bg_intervals_array", $wplc_intervals );
	if( $wplc_intervals ){

		foreach( $wplc_intervals as $key => $val ){
			if( isset( $wplc_bh_settings['interval'] ) && $wplc_bh_settings['interval'] == $key ){ $sel = 'selected'; } else { $sel = ''; }
			$content .= "<option value='$key' $sel>$val</option>";
		}

	}
	$content .= "</select><br/>";

	$wplc_times_array = wplc_return_times_array_mrg();

	$content .= __(" Between ", "wplivechat")."<select name='wplc_bh_hours_start' id='wplc_bh_hours_start'>";
	if( $wplc_times_array ){					
		$hours = $wplc_times_array['hours'];		
		foreach( $hours as $hour ){
			if( (isset( $wplc_bh_settings['start'] ) ) && ( isset( $wplc_bh_settings['start']['h'] ) && $wplc_bh_settings['start']['h'] == $hour ) ){ $sel = 'selected'; } else { $sel = ''; }
			$content .= "<option value='$hour' $sel>$hour</option>";
		}
	}
	$content .= "</select>:";
	$content .= "<select name='wplc_bh_minutes_start' id='wplc_bh_minutes_start'>";
	if( $wplc_times_array ){					
		$minutes = $wplc_times_array['minutes'];		
		foreach( $minutes as $minute ){
			if( (isset( $wplc_bh_settings['start'] ) ) && ( isset( $wplc_bh_settings['start']['m'] ) && $wplc_bh_settings['start']['m'] == $minute ) ){ $sel = 'selected'; } else { $sel = ''; }
			$content .= "<option value='$minute' $sel>$minute</option>";
		}
	}
	$content .= "</select>".__(" and ", "wplivechat");
	$content .= "<select name='wplc_bh_hours_end' id='wplc_bh_hours_end'>";
	if( $wplc_times_array ){					
		$hours = $wplc_times_array['hours'];		
		foreach( $hours as $hour ){
			if( (isset( $wplc_bh_settings['end'] ) ) && ( isset( $wplc_bh_settings['end']['h'] ) && $wplc_bh_settings['end']['h'] == $hour ) ){ $sel = 'selected'; } else { $sel = ''; }
			$content .= "<option value='$hour' $sel>$hour</option>";
		}
	}
	$content .= "</select>";
	$content .= "<select name='wplc_bh_minutes_end' id='wplc_bh_minutes_end'>";
	if( $wplc_times_array ){					
		$minutes = $wplc_times_array['minutes'];		
		foreach( $minutes as $minute ){
			if( (isset( $wplc_bh_settings['end'] ) ) && ( isset( $wplc_bh_settings['end']['m'] ) && $wplc_bh_settings['end']['m'] == $minute ) ){ $sel = 'selected'; } else { $sel = ''; }
			$content .= "<option value='$minute' $sel>$minute</option>";
		}
	}
	$content .= "</select>";
	$content .= "</td>";
	$content .= "</tr>";

	$content .= "<tr>";
	$content .= "<td width='300'>".__("Current Site Time", "wplivechat")."</td>";
	$content .= "<td>";
	$content .= $current_time = current_time('mysql');
	$content .= "</td>";
	$content .= "</tr>";

	$content .= "</table>";
	$content .= "</div>";

	echo $content;
		?>

    <div id="tabs-9">            
      <h3><?php _e("Chat Encryption", "wplivechat") ?></h3>
      <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
          <tr>
              <td width='300' valign='top'><?php _e("Enable Encryption", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('All messages will be encrypted when being sent to and from the user and agent.', 'wplivechat'); ?>"></i></td> 
              <td>
                  <input type="checkbox" name="wplc_enable_encryption" id="wplc_enable_encryption" value="1" <?php if(isset($wplc_encrypt_data['wplc_enable_encryption']) && $wplc_encrypt_data['wplc_enable_encryption'] == 1){ echo 'checked'; } ?>/>
              </td>
          </tr>
          <tr>
              <td width='300'></td>
              <td>
                  <p class='notice notice-error'>
                      <?php _e('Please note: Chat messages will only be encrypted and decrypted if you have inserted your WP Live Chat Support API Key with a previous version of our Pro add-on as this key was required.', 'wplivechat'); ?>
                      <?php _e('Once enabled, all messages sent will be encrypted. This cannot be undone.', 'wplivechat'); ?>
                  </p>

                  <p class='update-nag'>
                      <?php _e('Please note: Message encryption will be deprecated in our next major release.', 'wplivechat'); ?>
                  </p>
              </td>
          </tr>
      </table>
  </div>

        <?php do_action("wplc_hook_settings_page_more_tabs"); ?>
        
    </div>
    <p class='submit'><input type='submit' name='wplc_save_settings' class='button-primary' value='<?php _e("Save Settings","wplivechat")?>' /></p>
    </form>
    
    </div>


