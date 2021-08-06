<?php
/**
 * GDPR Compliance Module
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_filter("wplc_activate_default_settings_array", "wplc_gdpr_set_default_settings", 10, 1);
/*
 * Sets the default GDPR options
*/
function wplc_gdpr_set_default_settings($wplc_default_settings_array){
    return $wplc_default_settings_array;
}

add_action("wplc_hook_privacy_options_content", "wplc_gdpr_settings_content", 10, 1);
/** 
 * Adds the GDPR sepcific settings to the Privacy tab
*/
function wplc_gdpr_settings_content($wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }

  ?>
  <table class="wp-list-table wplc_list_table widefat fixed striped pages">
       <tbody>
         <tr>
           <td width="250" valign="top">
             <label for="wplc_gdpr_enabled"><?php _e("Enabled GDPR Compliance", "wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Disabling will disable all GDPR related options, this is not advised.', 'wplivechat'); ?>"></i></label>
           </td>
           <td> 
            <input type="checkbox" name="wplc_gdpr_enabled" value="1" <?php echo(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1' ? 'checked' : ''); ?> > 
            <a href="https://www.eugdpr.org/" target="_blank"><?php _e("Importance of GDPR Compliance", "wplivechat"); ?></a>
           </td>
         </tr>

         <tr>
           <td width="250" valign="top">
             <label for="wplc_gdpr_notice_company"><?php _e("Company Name", "wplivechat"); ?></label>
           </td>
           <td> 
            <input type="text" name="wplc_gdpr_notice_company" value="<?php echo(isset($wplc_settings['wplc_gdpr_notice_company']) ? stripslashes($wplc_settings['wplc_gdpr_notice_company']) : get_bloginfo('name')); ?>" > 
           </td>
         </tr>

         <tr>
           <td width="250" valign="top">
             <label for="wplc_gdpr_notice_retention_purpose"><?php _e("Retention Purpose", "wplivechat"); ?></label>
           </td>
           <td> 
            <input type="text" name="wplc_gdpr_notice_retention_purpose" value="<?php echo(isset($wplc_settings['wplc_gdpr_notice_retention_purpose']) ? $wplc_settings['wplc_gdpr_notice_retention_purpose'] : __('Chat/Support', 'wplivechat')); ?>" > 
           </td>
         </tr>

         <tr>
           <td width="250" valign="top">
             <label for="wplc_gdpr_notice_retention_period"><?php _e("Retention Period", "wplivechat"); ?></label>
           </td>
           <td> 
            <input type="number" name="wplc_gdpr_notice_retention_period" value="<?php echo(isset($wplc_settings['wplc_gdpr_notice_retention_period']) ? intval($wplc_settings['wplc_gdpr_notice_retention_period']) < 1 ? 1 : intval($wplc_settings['wplc_gdpr_notice_retention_period']) : 30); ?>" > <?php _e('days', 'wplivechat'); ?> 
           </td>
         </tr>

         <tr>
          <td width="250" valign="top">
             <label><?php _e("GDPR Notice", "wplivechat"); ?> 
              <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Users will be asked to accept the notice shown here, in the form of a check box.', 'wplivechat'); ?>"></i>
             </label>
            </td>
            <td>
              <span>
                <?php
                  echo wplc_gdpr_generate_retention_agreement_notice($wplc_settings);     
                  echo "<br><br>"; 
                  echo apply_filters('wplc_gdpr_create_opt_in_checkbox_filter', "");            
                ?>
              </span>
            </td>
         </tr> 

        
        </tbody>
    </table>
  <?php
}

add_filter("wplc_settings_save_filter_hook", "wplc_gdpr_settings_save_hooked", 10, 1);
/**
 * Save the settings for GDPR
*/
function wplc_gdpr_settings_save_hooked($wplc_data){
  
  if (isset($_POST['wplc_gdpr_enabled'])) { 
    $wplc_data['wplc_gdpr_enabled'] = sanitize_text_field($_POST['wplc_gdpr_enabled']); 
    do_action('wplc_gdpr_reg_cron_hook');

    update_option('WPLC_GDPR_DISABLED_WARNING_DISMISSED', 'false');
  } else {
    do_action('wplc_gdpr_de_reg_cron_hook');
  }

  if (isset($_POST['wplc_gdpr_notice_company'])) { $wplc_data['wplc_gdpr_notice_company'] = sanitize_text_field($_POST['wplc_gdpr_notice_company']); }
  if (isset($_POST['wplc_gdpr_notice_retention_purpose'])) { $wplc_data['wplc_gdpr_notice_retention_purpose'] = sanitize_text_field($_POST['wplc_gdpr_notice_retention_purpose']); }
  if (isset($_POST['wplc_gdpr_notice_retention_period'])) { $wplc_data['wplc_gdpr_notice_retention_period'] = sanitize_text_field($_POST['wplc_gdpr_notice_retention_period']); }
  
  return $wplc_data;
}


add_action("wplc_hook_menu", "wplc_gdpr_add_menu");
/**
 * Adds a menu specifically dedicated to GDPR
*/
function wplc_gdpr_add_menu(){
  $wplc_settings = get_option("WPLC_SETTINGS");

  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
      add_submenu_page('wplivechat-menu', __('GDPR', 'wplivechat'), __('GDPR', 'wplivechat'), 'manage_options', 'wplivechat-menu-gdpr-page', 'wplc_gdpr_page_layout');
    }
}

/**
 * Handles the layout for the GDPR page
*/
function wplc_gdpr_page_layout(){

  $current_nonce = wp_create_nonce('wplc_gdpr_page_nonce');
  ?>
  <h2><?php _e('WP Live Chat Support - GDPR', 'wplivechat'); ?></h2>

  <small><em><?php _e("Search is performed on chat sessions, messages, and offline messages. Data will also be deleted automatically per your retention policy.", "wplivechat"); ?></em></small>
  <?php do_action('wplc_gdpr_page_before_table_hook'); ?>
  <table class="wp-list-table wplc_list_table widefat fixed striped pages">
    <thead>
        <tr>
          <th></th>
          <th>
          </th>
          <th>
            <form method="GET" action="">
              <input type="hidden" name="wplc_gdpr_page_nonce" value="<?php echo $current_nonce; ?>">
              <input type="hidden" name="page" value='wplivechat-menu-gdpr-page'>
              <input name='term' type="text" value='<?php echo(isset($_GET['term']) ? htmlspecialchars(sanitize_text_field($_GET['term'])) : ''); ?>' placeholder="<?php _e('Name, Email, Message', 'wplivechat'); ?>" style='height:30px; width: 70%'>
              
              <?php do_action('wplc_gdpr_page_search_form_before_submit_hook'); ?>

              <input type='submit' class='button' value="<?php _e("Search", "wplivechat"); ?>" >
            </form>
          </th>
        </tr>
    </thead>
   <tbody>
      <?php
        if(isset($_GET['term'])){
          $results = wplc_gdpr_return_chat_session_search_results(htmlspecialchars(sanitize_text_field($_GET['term'])));

          foreach ($results as $heading => $sub_results) {
              $original_heading = $heading;
              $heading = ucwords(str_replace("_", " ", $heading));
              $heading = str_replace("%%TABLE%%", $heading, __('Search Results in %%TABLE%%', 'wplivechat'));
              ?>
                <tr>
                  <td><strong><?php echo $heading; ?></strong></td>
                  <td></td>
                  <td style="text-align: right"><em><?php echo count($sub_results); ?></em></td>
                </tr>
              <?php

              /**Setup Defaults*/
              $cid_identidier = 'id';
              $action_action_filter = 'chat_session';
              $show_fields = array('name', 'email');
              switch ($original_heading) {
                case 'chat_messages':
                  $cid_identidier = 'chat_sess_id';
                  $show_fields = array('msg');
                  break;
                case 'offline_messages':
                  $action_action_filter = 'offline_message';
                  $show_fields = array('name', 'email', 'message');
                  break;
              }

              $action_action_filter = htmlspecialchars($action_action_filter);

              foreach ($sub_results as $key => $value) {
                  $cid = isset($value[$cid_identidier]) ? $value[$cid_identidier] : 'false';
                  $delete_button_text = str_replace("%%CID%%", $cid, __("Delete Chat (%%CID%%)", "wplivechat"));
                  $download_button_text = str_replace("%%CID%%", $cid, __("Download Chat (%%CID%%)", "wplivechat"));

                 ?>
                  <tr>
                    <td><?php echo(__('Chat ID', 'wplivechat') . ": " . sanitize_text_field($cid) ); ?></td>
                    <td>
                      <?php 
                        foreach ($value as $subkey => $sub_val) {
                          if(in_array($subkey, $show_fields)){
                            echo( sanitize_text_field($subkey) . ": " . str_replace("%%BREAK%%", "<br>", sanitize_text_field($sub_val) ) . "<br>");
                          }
                        }
                      ?>
                    </td>
                    <td>
                      <a class='button' href='?page=wplivechat-menu-gdpr-page&term=<?php echo(htmlspecialchars(sanitize_text_field($_GET["term"]))); ?>&action=delete&filter=<?php echo $action_action_filter; ?>&id=<?php echo htmlspecialchars($cid); ?>&wplc_gdpr_page_nonce=<?php echo $current_nonce; ?>'><?php echo $delete_button_text; ?></a>
                      <a class='button button-primary' href='?page=wplivechat-menu-gdpr-page&term=<?php echo(htmlspecialchars(sanitize_text_field($_GET["term"]))); ?>&action=download&filter=<?php echo $action_action_filter; ?>&id=<?php echo htmlspecialchars($cid); ?>&wplc_gdpr_page_nonce=<?php echo $current_nonce; ?>'><?php echo $download_button_text; ?></a>
                    </td>
                  </tr>
                 <?php
              }
          }
        } else {
          ?>
            <tr>
              <td><strong><?php _e('Please perform a search using the input field above', 'wplivechat'); ?></strong></td>
              <td></td>
              <td></td>
            </tr>
          <?php
        }
      ?>
    </tbody>
  </table>

  <?php do_action('wplc_gdpr_page_after_table_hook'); ?>

  <?php
}

add_action('admin_init', 'wplc_gdpr_admin_init', 1);
/**
 * Runs on admin init, if we are on the GDPR page, we run the check action hook
 * This will allow us to alter the header if needed for JSON files
*/
function wplc_gdpr_admin_init(){
  wplc_gdpr_check_for_cron();

  if(isset($_GET['page']) && $_GET['page'] === 'wplivechat-menu-gdpr-page'){
    do_action('wplc_gdpr_page_process_actions_hook');
  }
}


add_action('wplc_gdpr_page_process_actions_hook', 'wplc_gdpr_page_process_actions');
/**
 * Handles the magic processing of the GDPR page
*/
function wplc_gdpr_page_process_actions(){

    

    if (current_user_can('export')) {
      if(isset($_GET['action']) && isset($_GET['filter']) && isset($_GET['id'])){
        if (!isset($_GET['wplc_gdpr_page_nonce']) || !wp_verify_nonce($_GET['wplc_gdpr_page_nonce'], 'wplc_gdpr_page_nonce')){
          wp_die(__("You do not have permission do perform this action", "wplivechat"));
        } 
        
        $action = sanitize_text_field($_GET['action']);
        $filter = sanitize_text_field($_GET['filter']);
        $id = sanitize_text_field($_GET['id']);
    
        if($action === 'delete'){
          wplc_gdpr_delete_chat($filter, $id);
        } else if($action === 'download'){
          wplc_gdpr_download_chat($filter, $id);
        }
      }
    } else {
        wp_die(__("You do not have permission do perform this action", "wplivechat"));
    }
}

/**
 * Delete a chat
*/
function wplc_gdpr_delete_chat($filter_type, $cid, $output = true){
  global $wpdb, $wplc_tblname_offline_msgs, $wplc_tblname_chats, $wplc_tblname_msgs;
  if($filter_type === 'chat_session'){
    $wpdb->delete($wplc_tblname_chats, array('id' => $cid));
    $wpdb->delete($wplc_tblname_msgs, array('chat_sess_id' => $cid));
  } else if ($filter_type === 'offline_message'){
    $wpdb->delete($wplc_tblname_offline_msgs, array('id' => $cid));
  }

  do_action('wplc_gdpr_delete_chat_extend_hook', $filter_type, $cid);

  if($output){
    $output = "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>";
    $output .=     "<strong>" . __("Data Deleted", "wplivechat") . "(" . $cid . ")" . "</strong><br>";
    $output .= "</div>";
    echo $output;
  }
}

/**
 * Download a chat
*/
function wplc_gdpr_download_chat($filter_type, $cid){
  global $wpdb, $wplc_tblname_offline_msgs, $wplc_tblname_chats, $wplc_tblname_msgs;
  if($filter_type === 'chat_session'){
    $result_chat_session = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `id` = '$cid' LIMIT 1", ARRAY_A);
    $result_chat_messages = $wpdb->get_results("SELECT * FROM $wplc_tblname_msgs WHERE `chat_sess_id` = '$cid'", ARRAY_A);

    if(count($result_chat_session) > 0){
      $chat_session = $result_chat_session[0];

      $chat_session['messages'] = array();
      foreach ($result_chat_messages as $key => $value) {
        $chat_session['messages'][] = $value;
      }

      $chat_session = apply_filters('wplc_gdpr_download_chat_extender_hook', $chat_session, $cid);

      //var_dump(json_encode($chat_session));

      header('Content-disposition: attachment; filename=chat_export_' . $cid . '.json');
      header('Content-type: application/json');
      echo json_encode($chat_session);
      die(); //Let's stop any further data capture please and thank you
    }
  } else if ($filter_type === 'offline_message'){

  }
}

/**
 * Searches the db for all relevant chat sessions based on the search term
*/
function wplc_gdpr_return_chat_session_search_results($term){
  global $wpdb, $wplc_tblname_offline_msgs, $wplc_tblname_chats, $wplc_tblname_msgs;

  $term = sanitize_text_field($term);
  
  $results_chats = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `name` LIKE '%$term%' OR `email` LIKE '%$term%'", ARRAY_A);
  $results_message = $wpdb->get_results("SELECT * FROM $wplc_tblname_msgs WHERE `msg` LIKE '%$term%'", ARRAY_A);
  $results_offline = $wpdb->get_results("SELECT * FROM $wplc_tblname_offline_msgs WHERE `name` LIKE '%$term%' OR `email` LIKE '%$term%' OR `message` LIKE '%$term%'", ARRAY_A);

  $formatted_messages = array();
  foreach ($results_message as $key => $value) {
    $cid = isset($value['chat_sess_id']) ? $value['chat_sess_id'] : false;
    if($cid !== false){
      $msg = maybe_unserialize($value['msg']);
      $msg = is_array($msg) ? $msg['m'] : $msg;
      if(!isset($formatted_messages[$cid])){
        $formatted_messages[$cid]['chat_sess_id'] = $cid;
        $formatted_messages[$cid]['msg'] = $msg . "%%BREAK%%";
      } else {
        $formatted_messages[$cid]['msg'] .= $msg . "%%BREAK%%";
      }
    }
  }

  $return_results = array(
    'chat_sessions' => $results_chats,
    'chat_messages' => $formatted_messages,
    'offline_messages' => $results_offline
  );

  return $return_results;

}

/**
 * Generates a localized retention notice message
*/
function wplc_gdpr_generate_retention_agreement_notice($wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }

  $localized_notice = __("I agree for my personal data to be processed and for the use of cookies in order to engage in a chat processed by %%COMPANY%%, for the purpose of %%PURPOSE%%, for the time of %%PERIOD%% day(s) as per the GDPR.", "wplivechat");
    $company_replacement = isset($wplc_settings['wplc_gdpr_notice_company']) ? stripslashes($wplc_settings['wplc_gdpr_notice_company']) : get_bloginfo('name');
    $purpose_replacement = isset($wplc_settings['wplc_gdpr_notice_retention_purpose']) ? $wplc_settings['wplc_gdpr_notice_retention_purpose'] : __('Chat/Support', 'wplivechat');
    $period_replacement = isset($wplc_settings['wplc_gdpr_notice_retention_period']) ? intval($wplc_settings['wplc_gdpr_notice_retention_period']) : 30;

    if($period_replacement < 1){ $period_replacement = 1; }

    $localized_notice = str_replace("%%COMPANY%%", $company_replacement, $localized_notice);
    $localized_notice = str_replace("%%PURPOSE%%", $purpose_replacement, $localized_notice);
    $localized_notice = str_replace("%%PERIOD%%", $period_replacement, $localized_notice);

    $localized_notice = apply_filters('wplc_gdpr_retention_agreement_notice_filter', $localized_notice);

    return $localized_notice;
}

add_filter('wplc_gdpr_create_opt_in_checkbox_filter', 'wplc_gdpr_add_wplc_privacy_notice', 10, 1);
/**
 * WPLC Compliance notice and link to policy
*/
function wplc_gdpr_add_wplc_privacy_notice($content){
  $link = '<a href="https://wp-livechat.com/privacy-policy/" target="_blank">' . __('Privacy Policy', 'wplivechat') . '</a>';
  $localized_content = __('We use WP Live Chat Support as our live chat platform. By clicking below to submit this form, you acknowledge that the information you provide now and during the chat will be transferred to WP Live Chat Support for processing in accordance with their %%POLICY_LINK%%.', 'wplivechat');

  $localized_content = str_replace("%%POLICY_LINK%%", $link, $localized_content);
  $html = "<div class='wplc_gdpr_privacy_notice'>$localized_content</div>";

  return $content . $html;
}

add_action("wplc_before_history_table_hook", "wplc_gdpr_retention_cron_notice");
add_action("wplc_hook_chat_missed", "wplc_gdpr_retention_cron_notice");
add_action("wplc_hook_offline_messages_display", "wplc_gdpr_retention_cron_notice", 1);
/**
 * Shows a notice which notifies the admin that all messages older than the retention period will be removed
*/
function wplc_gdpr_retention_cron_notice(){
  $wplc_settings = get_option("WPLC_SETTINGS");

  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    $period_replacement = isset($wplc_settings['wplc_gdpr_notice_retention_period']) ? intval($wplc_settings['wplc_gdpr_notice_retention_period']) : 30;
    
    if($period_replacement < 1){ $period_replacement = 1; }

    $retention_period_message = __("Please note as per the GDPR settings you have selected, all chat data will be retained for %%PERIOD%% day(s).", "wplivechat");
    $retention_period_message = str_replace("%%PERIOD%%", $period_replacement, $retention_period_message);

    $retention_period_message_alt = __("After this period of time, all chat data older than %%PERIOD%% day(s), will be permanently removed from your server. ", "wplivechat");
    $retention_period_message_alt = str_replace("%%PERIOD%%", $period_replacement, $retention_period_message_alt);

    $output = "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>";
    $output .=     "<strong>" . __("GDPR - Data Retention", "wplivechat") . "</strong><br>";
    $output .=     "<p>" . $retention_period_message . "</p>";
    $output .=     "<p>" . $retention_period_message_alt . "</p>";

    $output .= apply_filters('wplc_gdpr_retention_cron_notice_extender_hook', "");

    $output .=     "<a class='button' href='?page=wplivechat-menu-settings#tabs-privacy' >" . __("Privacy Settings", "wplivechat") . "</a>";
    $output .= "</div>";
    echo $output;

  }
}

add_filter('cron_schedules','wplc_gdpr_custom_cron_schedules', 10, 1);
/** 
 * Setup a cron schedule
*/
function wplc_gdpr_custom_cron_schedules($schedules){
    if(!isset($schedules["wplc_6_hour"])){
        $schedules["wplc_6_hour"] = array(
            'interval' => 6*60*60,
            'display' => __('Once every 6 hours'));
    }

    return $schedules;
}

/**
 * Checks if cron is still registered
*/
function wplc_gdpr_check_for_cron(){
  $cron_jobs = get_option( 'cron' );
  $cron_found = false;
  foreach ($cron_jobs as $cron_key => $cron_data) {
    if(is_array($cron_data)){
      foreach ($cron_data as $cron_inner_key => $cron_inner_data) {
        if($cron_inner_key === "wplc_gdpr_cron_hook"){
          $cron_found = true;
        }
      }
    }
  }

  if(!$cron_found){
    do_action('wplc_gdpr_reg_cron_hook'); //The cron was unregistered at some point. Lets fix that
  }
}

add_action('wplc_gdpr_reg_cron_hook', 'wplc_gdpr_register_cron');
/**
 * Cron Register
*/
function wplc_gdpr_register_cron(){
  $wplc_settings = get_option('WPLC_SETTINGS');
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    wp_schedule_event( time(), 'wplc_6_hour', 'wplc_gdpr_cron_hook' );
  } 

}

add_action('wplc_gdpr_de_reg_cron_hook', 'wplc_gdpr_de_register_cron');
/**
 * Cron De-Register
*/
function wplc_gdpr_de_register_cron(){
  wp_clear_scheduled_hook('wplc_gdpr_cron_hook');
}
  

add_action('wplc_gdpr_cron_hook', 'wplc_gdpr_cron_delete_chats');
/**
 * GDPR Cron for deleting chats that are old school
*/
function wplc_gdpr_cron_delete_chats(){
  global $wpdb, $wplc_tblname_chats, $wplc_tblname_msgs, $wplc_tblname_offline_msgs;
  $wplc_settings = get_option("WPLC_SETTINGS");
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    $period_replacement = isset($wplc_settings['wplc_gdpr_notice_retention_period']) ? intval($wplc_settings['wplc_gdpr_notice_retention_period']) : 30;

    if($period_replacement < 1){ $period_replacement = 1; }

    $days_ago = date('Y-m-d', strtotime('-' . $period_replacement . ' days', time()));

    $results_chats = $wpdb->get_results("DELETE FROM $wplc_tblname_chats WHERE `timestamp` < '$days_ago'", ARRAY_A);
    $results_messages = $wpdb->get_results("DELETE FROM $wplc_tblname_msgs WHERE `timestamp` < '$days_ago'", ARRAY_A);
    $results_offline = $wpdb->get_results("DELETE FROM $wplc_tblname_offline_msgs WHERE `timestamp` < '$days_ago'", ARRAY_A);

    do_action('wplc_cron_delete_chats_extender', $days_ago);
  }
}

add_filter('wplc_filter_live_chat_box_html_start_chat_button', 'wplc_gdpr_create_opt_in_checkbox_in_chatbox', 10, 2);
add_filter('wplc_filter_live_chat_box_html_send_offline_message_button', 'wplc_gdpr_create_opt_in_checkbox_in_chatbox', 10, 2);
/**
 * Checkbox opt in please
*/
function wplc_gdpr_create_opt_in_checkbox_in_chatbox($filter_content, $wplc_cid = false){
  $wplc_settings = get_option("WPLC_SETTINGS");
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    $checkbox = "<input type='checkbox' id='wplc_chat_gdpr_opt_in'> <label for='wplc_chat_gdpr_opt_in'>" . wplc_gdpr_generate_retention_agreement_notice($wplc_settings) . "</label>";

    $checkbox = apply_filters('wplc_gdpr_create_opt_in_checkbox_filter', $checkbox);

    $filter_content = $checkbox . $filter_content;

  }

  return $filter_content;
}

add_filter('wplc_start_button_custom_attributes_filter', 'wplc_gdpr_chat_box_opt_in_custom_attributes', 10, 2);
add_filter('wplc_offline_message_button_custom_attributes_filter', 'wplc_gdpr_chat_box_opt_in_custom_attributes', 10, 2);
add_filter('wplc_end_button_custom_attributes_filter', 'wplc_gdpr_chat_box_opt_in_custom_attributes', 10, 2);
/**
 * Adds custom attributes to the start chat, and offline messages buttons to prevent click without opt in
*/
function wplc_gdpr_chat_box_opt_in_custom_attributes($content, $wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }

  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    $content .= " data-wplc-gdpr-enabled='true' ";
  }

  return $content;
}

add_filter("wplc_filter_inner_live_chat_box_4th_layer", "wplc_gdpr_end_chat_action_prompt", 10, 2);
/**
 * Creates the GDPR end chat notice/prompt
*/
function wplc_gdpr_end_chat_action_prompt($content, $wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }

  $notice_html = "";
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    $notice_html = "<div class='wplc_in_chat_notice' id='wplc_gdpr_end_chat_notice_container' style='display:none;'>";
    $notice_html .=   "<div class='wplc_in_chat_notice_heading'>" . __("Chat Ended", "wplivechat") . "</div>";
    $notice_html .= "</div>";  
  }

  return $notice_html . $content;

}


add_action('admin_notices', 'wplc_gdpr_disabled_warning');
/**
 * Notice of doom
*/
function wplc_gdpr_disabled_warning(){

  if(isset($_GET['wplc_gdpr_dismiss_notice'])){
    update_option('WPLC_GDPR_DISABLED_WARNING_DISMISSED', 'true');
  }

  if(isset($_GET['page'])){
    if($_GET['page'] === 'wplivechat-menu-settings'){
      $wplc_settings = get_option("WPLC_SETTINGS");
      if(!isset($wplc_settings['wplc_gdpr_enabled']) || $wplc_settings['wplc_gdpr_enabled'] != '1'){
        $gdpr_disabled_warning_dismissed = get_option('WPLC_GDPR_DISABLED_WARNING_DISMISSED', false);
        if($gdpr_disabled_warning_dismissed === false || $gdpr_disabled_warning_dismissed === 'false'){
          $implication_warning = __('GDPR compliance has been disabled, read more about the implications of this here', 'wplivechat');
          $privacy_warning = __('Additionally please take a look at WP Live Chat Support', 'wplivechat');
          $final_warning = __('It is highly recommended that you enable GDPR compliance to ensure your user data is regulated.', 'wplivechat');

          $output = "<div class='update-nag' style='margin-bottom: 5px; border-color:#DD0000'>";
          $output .=     "<strong>" . __("Warning - GDPR Compliance Disabled - Action Required", "wplivechat") . "</strong><br>";
          $output .=     "<p>" . $implication_warning . ": <a href='https://www.eugdpr.org/' target='_blank'>" . __('EU GDPR', 'wplivechat') . "</a></p>";
          $output .=     "<p>" . $privacy_warning . " <a href='https://wp-livechat.com/privacy-policy/' target='_blank'>" . __('Privacy Policy', 'wplivechat') . "</a></p>";
          $output .=     "<p>" . $final_warning . "</p>";
          $output .=     "<a class='button' href='?page=wplivechat-menu-settings#tabs-privacy' >" . __("Privacy Settings", "wplivechat") . "</a> ";
          $output .=     "<a class='button' href='?page=" . htmlspecialchars(sanitize_text_field($_GET['page'])) ."&wplc_gdpr_dismiss_notice=true' style='color: #fff;background-color: #bb0000;border-color: #c70000;'>" . __("Dismiss & Accept Responsibility", "wplivechat") . "</a>";
          $output .= "</div>";
          echo $output;
        }
      }
    }
  }
}


add_filter( 'admin_footer_text', 'wplc_gdpr_footer_mod', 99, 1);
/**
 * Adds the data privacy notices
 */
function wplc_gdpr_footer_mod( $footer_text ) {
    if(isset($_GET['page'])){
      if(strpos($_GET['page'], 'wplivechat') !== FALSE){
        $footer_text_addition =  __( 'Please refer to our %%PRIVACY_LINK%% for information on Data Processing', 'wplivechat' );
        $footer_text_addition = str_replace("%%PRIVACY_LINK%%", "<a href='https://wp-livechat.com/privacy-policy/' target='_blank'>" . __(
          "Privacy Policy", "wplivechat") . "</a>", $footer_text_addition);

        return str_replace( '</span>', '', $footer_text ) . ' | ' . $footer_text_addition . '</span>';
      }
    } 
    
    return $footer_text;

}

add_filter( 'wplc_node_server_us_options_enabled', 'wplc_gdpr_allow_us_servers_check',10 ,2);
/**
 * Checks if GDPR option is enabled so we can force users into the EU
*/
function wplc_gdpr_allow_us_servers_check($is_allowed, $wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }

  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    return false;
  }

  return $is_allowed;
}


add_filter( 'wplc_node_server_default_selection_override', 'wplc_gdpr_node_server_eu_default_override',10,2);
/**
 * Checks if GDPR option is enabled so we can force users into the EU
*/
function wplc_gdpr_node_server_eu_default_override($selected, $wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
    if($selected === 'auto' || substr($selected,0,2) == 'us') {
      //This is not allowed in the GDPR
      $selected = 'eu'.rand(1,10);
    }
  }
  return $selected;
}


add_action('wplc_node_server_selection_notices', 'wplc_gdpr_node_server_override_notice',10,1);
/**
 * Will add a very simple server override if GDPR is enabled
*/ 
function wplc_gdpr_node_server_override_notice($wplc_settings = false){
  if($wplc_settings === false){ $wplc_settings = get_option("WPLC_SETTINGS"); }
  if(isset($wplc_settings['wplc_gdpr_enabled']) && $wplc_settings['wplc_gdpr_enabled'] == '1'){
      echo "<small><strong><em>" . __("GDPR Compliance Enabled - Server Selection Limited to EU", "wplivechat") . "</em></strong></small>";
  }
}

add_filter('wplc_update_settings_between_versions_hook', 'wplc_gdpr_update_settings_between_versions',10,1);
/**
 * This will handle the auto update magic. Although we have a default in place this is far superior as it is a hard data set
*/
function wplc_gdpr_update_settings_between_versions($wplc_settings){
  if(is_array($wplc_settings)){
    $gdpr_enabled_atleast_once_before = get_option('WPLC_GDPR_ENABLED_AT_LEAST_ONCE', false);
    if($gdpr_enabled_atleast_once_before === false){
      //Only fire if this user has never had GDPR enabled before
      update_option('WPLC_GDPR_ENABLED_AT_LEAST_ONCE', 'true');
      $wplc_settings['wplc_gdpr_enabled'] = '0';
    }
  }

  return $wplc_settings;
}



/**
 * Extends the GDPR functionality
*/

add_action('wplc_gdpr_page_before_table_hook', 'wplc_gdpr_mrg_page_before_table_notice');
/**
 * Adds a small notice stating that the pro is active, and ratings will also be removed as/where needed
*/
function wplc_gdpr_mrg_page_before_table_notice(){
	?>
	<br><small><em>* <?php _e("Pro is active - Deleting a chat will resilt in user experience ratings being deleted along with chat data", 'wplivechat'); ?></em></small>
	<?php
}

add_filter('wplc_gdpr_retention_cron_notice_extender_hook', 'wplc_gdpr_mrg_cron_notice_extender', 10, 1);
/**
 * Adds additional info to the cron notice about data being removed in the cron
*/
function wplc_gdpr_mrg_cron_notice_extender($content){
	$content .= "<p>" . __('Pro data will also be removed as a part of this automatic process.', 'wplivechat') . "</p>";
	return $content;
}

add_action('wplc_cron_delete_chats_extender', 'wplc_gdpr_mrg_cron_extended_delete', 10, 1);
/**
 * Extends the cron in the GDPR module, to delete ratings
*/
function wplc_gdpr_mrg_cron_extended_delete($days_ago){
	global $wpdb, $wplc_tblname_chat_ratings;
	$results_ratings = $wpdb->get_results("DELETE FROM $wplc_tblname_chat_ratings WHERE `timestamp` < '$days_ago'", ARRAY_A);
}

add_action('wplc_gdpr_delete_chat_extend_hook', 'wplc_gdpr_mrg_delete_chat_data', 10, 2);
/**
 * Handles manual deleting of chat data
*/
function wplc_gdpr_mrg_delete_chat_data($filter_type, $cid){
	global $wpdb, $wplc_tblname_chat_ratings;
	if($filter_type === 'chat_session'){
		$wpdb->delete($wplc_tblname_chat_ratings, array('cid' => $cid));
	}
}

add_filter('wplc_gdpr_download_chat_extender_hook', 'wplc_gdpr_mrg_download_chat_data', 10, 2);
/**
 * Handles added the rating data to the download JSON array
*/
function wplc_gdpr_mrg_download_chat_data($chat_session, $cid){
	global $wpdb, $wplc_tblname_chat_ratings;

	if(is_array($chat_session)){
		$result_chat_rating = $wpdb->get_results("SELECT * FROM $wplc_tblname_chat_ratings WHERE `cid` = '$cid'", ARRAY_A);
		if(count($result_chat_rating) > 0){
		  $chat_session['rating'] = $result_chat_rating[0];
		}
	}

	return $chat_session;
}