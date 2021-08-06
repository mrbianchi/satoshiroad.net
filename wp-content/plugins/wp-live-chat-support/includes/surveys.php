<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter("wplc_filter_setting_tabs","wplc_survey_filter_control_setting_tabs");
function wplc_survey_filter_control_setting_tabs($tab_array) {
    $tab_array['survey'] = array(
      "href" => "#tabs-survey",
      "icon" => 'fa fa-comment',
      "label" => __("Surveys & Lead Forms","wplivechat")
    );
    return $tab_array;
}


add_action("wplc_hook_settings_page_more_tabs","wplc_survey_hook_control_settings_page_more_tabs");
function wplc_survey_hook_control_settings_page_more_tabs() {
    $wplc_survey_data = get_option("WPLC_SURVEY_SETTINGS");
    $wplc_settings = get_option('WPLC_SETTINGS');
  ?>
 <div id="tabs-survey">            
    <h3><?php _e("Surveys & Lead Forms", "wplivechat") ?></h3>
    <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
        <tr>
            <td width='300' valign='top'><?php _e("Enable Surveys & Lead Forms", "wplivechat") ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Enable surveys and lead forms within your live chat box, either before or after a live chat session.', 'wplivechat'); ?>"></i></td> 
            <td>
                <input type="checkbox" name="wplc_enable_surveys" id="wplc_enable_surveys" value="1" <?php if(isset($wplc_survey_data['wplc_enable_surveys']) && $wplc_survey_data['wplc_enable_surveys'] == 1){ echo 'checked'; } ?>/>
            </td>
        </tr>
        <tr>
            <td>Select a survey: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Choosing a survey will reset the lead form selection: the items are alternatives, if you select a survey no lead form can be selected.', 'wplivechat'); ?>"></i></td>
            <td><?php
                $surveys = wplc_return_nimble_surveys_forms('return_surveys');
                $link1 = sprintf( __( 'No surveys created. Please <a href="%s" target="_BLANK" title="NimbleSquirrel">create a survey and then refresh this page.</a>', 'wplivechat' ),
                    'http://app.nimblesquirrel.com/?utm_source=wplc&utm_medium=click&utm_campaign=no_surveys'
                );
                if( $surveys ){
                    $surveys = json_decode( $surveys );
                    if( $surveys ){
                        echo "<select name='nimble_survey' id='nimble_survey'>";
                        echo "<option value='0'>".__('Select a survey', 'nimble-squirrel')."</option>";                                             
                        $cnt = 0;
                        foreach( $surveys as $survey ){
                            $nimble_user_id = $survey->uid;
                            if (isset($survey->name)) {
                                $cnt++;
                                if( isset( $wplc_survey_data['survey'] ) && $wplc_survey_data['survey'] == $survey->id ){ $sel = 'selected'; } else { $sel = ''; }
                                echo "<option value='".$survey->id."' $sel>".$survey->name."</option>";
                            }
                        }
                        echo "</select>";
                        if ($cnt == 0) { echo "<p>".$link1."</p>";   }
                    } else {
                        echo "<p>".$link1."</p>";
                    }
                } else {
                    echo "<p>".$link1."</p>";
                }

                if (isset($nimble_user_id)) {
                    echo "<input name='survey_user' type='hidden' value='".$nimble_user_id."' />";
                }

            ?></td>
        </tr>
        <tr>
            <td>Select a lead form: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Choosing a lead form will reset the survey selection: the items are alternatives, if you select a lead form no survey can be selected.', 'wplivechat'); ?>"></i></td>
            <td><?php
                $lead_forms = wplc_return_nimble_surveys_forms('return_leads');
                $link1 = sprintf( __( 'No lead forms created. Please <a href="%s" target="_BLANK" title="NimbleSquirrel">create a lead form and then refresh this page.</a>', 'wplivechat' ),
                    'http://app.nimblesquirrel.com/?utm_source=wplc&utm_medium=click&utm_campaign=no_surveys'
                );
                if( $lead_forms ){
                    $lead_forms = json_decode( $lead_forms );
                    if( $lead_forms ){
                        echo "<select name='nimble_lead_form' id='nimble_lead_form'>";
                        echo "<option value='0'>".__('Select a lead form', 'nimble-squirrel')."</option>";                                             
                        $cnt = 0;
                        foreach( $lead_forms as $lead_form ){
                            $nimble_user_id = $lead_form->uid;
                            if (isset($lead_form->name)) {
                                $cnt++;
                                if( isset( $wplc_survey_data['lead_form'] ) && intval($wplc_survey_data['lead_form']) == intval($lead_form->id) ){ $sel = 'selected'; } else { $sel = ''; }
                                echo "<option value='".intval($lead_form->id)."' $sel>".sanitize_text_field($lead_form->name)."</option>";
                            }
                        }
                        echo "</select>";
                        if ($cnt == 0) { echo "<p>".$link1."</p>";   }
                    } else {
                        echo "<p>".$link1."</p>";
                    }
                } else {
                    echo "<p>".$link1."</p>";
                }
        ?>      
            </td>
        </tr>
        <tr>
            <td><?php _e("Display survey/form","wplivechat"); ?></td>
            <td>
                <select name='survey_display'>
                    <option value='1' <?php if (isset($wplc_survey_data['survey_display']) && $wplc_survey_data['survey_display'] == "1") { echo 'selected'; }?>><?php _e("Before chat","wplivechat"); ?></option>
                    <option value='2' <?php if (isset($wplc_survey_data['survey_display']) && $wplc_survey_data['survey_display'] == "2") { echo 'selected'; }?>><?php _e("After chat","wplivechat"); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e("Chat button text","wplivechat"); ?></td>
            <td>
                <input type='text' name='wplc_pro_sst1_survey' value='<?php echo $wplc_settings['wplc_pro_sst1_survey']; ?>' />
            </td>
        </tr>
        <tr>
            <td><?php _e("Change title of chat box when chat ended to","wplivechat"); ?></td>
            <td>
                <input type='text' name='wplc_pro_sst1e_survey' value='<?php echo $wplc_settings['wplc_pro_sst1e_survey']; ?>' />
            </td>
        </tr>
    </table>
</div>

<?php

    wp_register_script('wplc-lead-form-script', plugins_url('../js/wplc_lead_forms.js', __FILE__),array('jquery'),WPLC_PLUGIN_VERSION);
    wp_enqueue_script('wplc-lead-form-script');
}


add_action('wplc_hook_admin_settings_save','wplc_survey_save_settings');
function wplc_survey_save_settings() {
    if (isset($_POST['wplc_save_settings'])) {
        
        if (isset($_POST['wplc_enable_surveys'])) {
            $wplc_survey_data['wplc_enable_surveys'] = intval($_POST['wplc_enable_surveys']);
        } else {
            $wplc_survey_data['wplc_enable_surveys'] = 0;
        }
        if (isset($_POST['nimble_survey']) && $_POST['nimble_survey'] !== '0') {
            $wplc_survey_data['survey'] = sanitize_text_field($_POST['nimble_survey']);
        } else {
            $wplc_survey_data['survey'] = null;
        }
        if (isset($_POST['nimble_lead_form']) && $_POST['nimble_lead_form'] !== '0') {
            $wplc_survey_data['lead_form'] = sanitize_text_field($_POST['nimble_lead_form']);
        } else {
            $wplc_survey_data['lead_form'] = null;
        }
        if (isset($_POST['survey_user']) && $_POST['survey_user'] !== '0') {
            $wplc_survey_data['survey_user'] = sanitize_text_field($_POST['survey_user']);
        } else {
            $wplc_survey_data['survey_user'] = null;
        }
        if (isset($_POST['survey_display']) && $_POST['survey_display'] !== '0') {
            $wplc_survey_data['survey_display'] = sanitize_text_field($_POST['survey_display']);
        } else {
            $wplc_survey_data['survey_display'] = "1";
        }

        
        update_option('WPLC_SURVEY_SETTINGS', $wplc_survey_data);

    }
}


function wplc_return_nimble_surveys_forms($action) {
    $nimble_api = 'http://nimblesquirrel.com/api/wordpress.php';

    $response = wp_remote_post( $nimble_api, 
        array(
            'method' => 'POST',
            'body' => array( 
                'wordpress_plugin' => 1,
                'action' => $action,
                'siteurl' => site_url()
            )
        )
    );

    if ( is_wp_error( $response ) ) {

       $error_message = $response->get_error_message();
       echo $error_message;
    } else {
       return $response['body'];
    }
}


add_filter("wplc_filter_live_chat_box_pre_layer1","wplc_filter_control_live_chat_box_pre_layer1",10,1);
function wplc_filter_control_live_chat_box_pre_layer1($content) {
     $settings = get_option('WPLC_SURVEY_SETTINGS');
    if( isset( $settings['wplc_enable_surveys'] ) && intval($settings['wplc_enable_surveys']) == 1 && !isset($_COOKIE['ns_participated'])){

        if( isset( $settings['survey_display']) && $settings['survey_display'] == '1' ) {
            $content .= "<img src='".plugins_url('../images/loader2.gif', __FILE__)."' style='display:block; margin-left:auto; margin-right:auto;' / >";
            return $content;
        }
    }
}



add_action('wp_enqueue_scripts', 'wplc_nimble_load_scripts' , 99 );
function wplc_nimble_load_scripts() {
    $settings = get_option('WPLC_SURVEY_SETTINGS');
    $wplc_settings = get_option('WPLC_SETTINGS');
    
    if( isset( $settings['wplc_enable_surveys'] ) && intval($settings['wplc_enable_surveys']) == 1 && !isset($_COOKIE['ns_participated'])){



        if( isset( $settings['survey_user'] ) ){ $ns_id = $settings['survey_user']; } else { $ns_id = 'false'; }
        if( isset( $settings['survey'] ) ){ $ns_sid = $settings['survey']; } else { $ns_sid = 'false'; }
        if( isset( $settings['lead_form'] ) ){ $ns_lfid = $settings['lead_form']; } else { $ns_lfid = 'false'; }

        //wp_enqueue_script( 'nimble-squirrel-user-script', '//nimblesquirrel.com/api/nimblesquirrel.js', array(), '1.0.0', true );
        //wp_enqueue_script( 'nimble-squirrel-user-script', 'https://nimblesquirrel.com/api/v2.0/nimblesquirrel.js', array(), '1.0.0', true );
        wp_enqueue_script( 'nimble-squirrel-user-script', plugins_url('../js/nimblesquirrel.js', __FILE__), array(), '1.0.0', true );
        wp_localize_script( 'nimble-squirrel-user-script', 'ns_id', $ns_id );


        if (isset($wplc_settings['wplc_newtheme'])) { $wplc_newtheme = $wplc_settings['wplc_newtheme']; } else { $wplc_newtheme == 'theme-2'; }
        if (isset($wplc_newtheme)) {
            if($wplc_newtheme == 'theme-1') {
                if( isset( $settings['survey_display']) && $settings['survey_display'] == '1' ) {
                    $div = "wp-live-chat-2";
                    $clear_div = '0';
                    $hide_div = 'wp-live-chat-2-inner';
                    wp_localize_script( 'nimble-squirrel-user-script', 'ns_hide_div', $hide_div );
                } else if (isset( $settings['survey_display']) && $settings['survey_display'] == '2' ) {
                    $div = "wplc-extra-div";
                    $clear_div = '1';
                    wp_localize_script( 'nimble-squirrel-user-script', 'wplc_extra_div_enabled', "1" );
                } else {
                    $div = "wp-live-chat-2";
                    $clear_div = '0';
                    $hide_div = 'wp-live-chat-2-inner';
                    wp_localize_script( 'nimble-squirrel-user-script', 'ns_hide_div', $hide_div );
                }
            }
            else if($wplc_newtheme == 'theme-2') {
                if( isset( $settings['survey_display']) && $settings['survey_display'] == '1' ) {
                    $div = 'wplc_hovercard_content';
                    $clear_div = '1';
                } else if (isset( $settings['survey_display']) && $settings['survey_display'] == '2' ) {
                    $div = "wplc-extra-div";
                    $clear_div = '1';
                    wp_localize_script( 'nimble-squirrel-user-script', 'wplc_extra_div_enabled', "1" );
                } else {
                    $div = 'wplc_hovercard_content';
                    $clear_div = '1';

                }
                
            }
        }

        wp_localize_script( 'nimble-squirrel-user-script', 'ns_div', $div );
        

        wp_localize_script( 'nimble-squirrel-user-script', 'ns_hide_min', '1'  );

        if ( '' != $ns_sid ) {
            wp_localize_script( 'nimble-squirrel-user-script', 'ns_sid', $ns_sid );
        }
        else {
            wp_localize_script( 'nimble-squirrel-user-script', 'ns_lfid', $ns_lfid );
        }

        wp_localize_script( 'nimble-squirrel-user-script', 'ns_clear_div', $clear_div );
        

        wp_register_style('wplc-survey-style', plugins_url('../css/wplc-survey-style.css', __FILE__));
        wp_enqueue_style('wplc-survey-style');
        if( isset( $settings['survey_display']) && $settings['survey_display'] == '1' ) {
            wp_register_style('wplc-survey-before-style', plugins_url('../css/wplc-survey-style-before.css', __FILE__));
            wp_enqueue_style('wplc-survey-before-style');
        }
    }
}



add_filter("wplc_filter_live_chat_box_hover_html_start_chat_button","wplc_filter_survey_control_live_chat_box_html_hovercard_chat_button",1,3);
function wplc_filter_survey_control_live_chat_box_html_hovercard_chat_button($wplc_settings,$logged_in,$wplc_using_locale ) {

    $settings = get_option('WPLC_SURVEY_SETTINGS');

    if( isset( $settings['wplc_enable_surveys'] ) && intval($settings['wplc_enable_surveys']) == 1 && !isset($_COOKIE['ns_participated'])){
        if( isset( $settings['survey_display']) && $settings['survey_display'] == '1' ) {
            remove_filter("wplc_filter_live_chat_box_hover_html_start_chat_button","wplc_filter_control_live_chat_box_html_hovercard_chat_button");
            if ($logged_in) {
              $wplc_sst_1 = __('Or chat to an agent now', 'wplivechat');
              if (!isset($settings['wplc_pro_sst1_survey']) || $settings['wplc_pro_sst1_survey'] == "") { $settings['wplc_pro_sst1_survey'] = $wplc_sst_1; }
              $text = ($wplc_using_locale ? $wplc_sst_1 : stripslashes($settings['wplc_pro_sst1_survey']));
              return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$text</button>";
            } else {
              $wplc_sst_1 = __('Leave a message', 'wplivechat');
              return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$wplc_sst_1</button>";

            }
        }
    }
}


add_action("wplc_hook_push_js_to_front","wplc_hook_survey_push_js_to_front",10,1);
function wplc_hook_survey_push_js_to_front() {
    $settings = get_option('WPLC_SURVEY_SETTINGS');

    if( isset( $settings['wplc_enable_surveys'] ) && intval($settings['wplc_enable_surveys']) == 1 ){
     
        $wplc_settings = get_option('WPLC_SETTINGS');
        wp_register_script('wplc-survey-script', plugins_url('../js/wplc_surveys.js', __FILE__),array('jquery'),WPLC_PLUGIN_VERSION);
        wp_enqueue_script('wplc-survey-script');
        wp_localize_script( 'wplc-survey-script', 'wplc_end_chat_string', $wplc_settings['wplc_pro_sst1e_survey'] );
        wp_localize_script( 'wplc-survey-script', 'wplc_button_string', $wplc_settings['wplc_pro_sst1_survey'] );
    }


}



function wplc_admin_survey_layout() {
    wplc_stats("surveys");
    update_option("wplc_seen_surveys",true);
    $settings = get_option('WPLC_SURVEY_SETTINGS');

    echo"<div class=\"wrap wplc_wrap\">";
    echo "<h1>" . __("WP Live Chat Surveys & Lead Forms with Nimble Squirrel", "wplivechat") . "</h1>";
    echo "<div style='width:100%; display:block; overflow:auto; background-color:#FFF; padding:10px; border-radius:10px;'>";


    if( isset( $settings['wplc_enable_surveys'] ) && intval($settings['wplc_enable_surveys']) == 1 ){
        echo "<h2>".__("Surveys & Lead Forms","wplivechat")."</h2>";
        echo "<p>".__("To view your responses, click the button below and log in to your NimbleSquirrel account.","wplivechat")."</p>";
        echo "<a href='http://app.nimblesquirrel.com/?utm_source=wplc&utm_medium=click&utm_campaign=view_responses' target='_BLANK' class='button button-primary'>View your responses</a>";
        echo "<p>&nbsp;</p>";
        echo "";
        echo "";
        echo "";
        echo "";
        echo "";

    } 
    else {

    $link1 = sprintf( __( 'Register on <a href="%s" target="_BLANK" title="NimbleSquirrel">NimbleSquirrel</a> (It\'s free)', 'wplivechat' ),
        'http://app.nimblesquirrel.com/join.php?email='.get_option("admin_email")."&site=".site_url().'&utm_source=wplc&utm_medium=click&utm_campaign=plugin_register'
    );
    $link2 = sprintf( __( '<a href="%s" target="_BLANK" title="Create a survey">Create a survey</a>.', 'wplivechat' ),
        'http://app.nimblesquirrel.com/my_account.php?a=add_survey_wizard&utm_source=wplc&utm_medium=click&utm_campaign=add_survey'
    );


        echo "  <div style='display:block; width:50%; float:left; overflow:auto;'>";
        echo "      <div style='display:block; padding:10px;'>";
        echo "          <h2>".__("Add a Survey to your live chat box","wplivechat")."</h2>";
        echo "          <p>".__("Three simple steps:","wplivechat")."</p>";
        echo "          <ol>";
        echo "              <li>".$link1."</li>";
        echo "              <li>".$link2."</li>";
        echo "              <li>".__("Enable surveys in your live chat <a href='admin.php?page=wplivechat-menu-settings#tabs-survey'>settings page</a>.","wplivechat")."</li>";
        echo "          </ol>";
        echo "";
        echo "";
        echo "      </div>";
        echo "  </div>";
        echo "  <div style='display:block; width:50%; float:left; overflow:auto;'>";
        echo "      <div style='display:block; padding:10px;'>";
        echo "";
        echo "";
        echo "";
        echo "";
        echo "          <img src='http://nimblesquirrel.com/app/img/Survey_Box_Standard.jpg' style='max-width:100%;' />";
        echo "";
        echo "";
        echo "";
        echo "";
        echo "";
        echo "      </div>";
        echo "  </div>";

    }
    echo "</div>";
    echo "</div>";

}
