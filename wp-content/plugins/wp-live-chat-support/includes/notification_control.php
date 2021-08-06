<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wplc_record_chat_notification($type,$cid,$data) {
  if ($cid) {
    do_action("wplc_hook_chat_notification",$type,$cid,$data);
  }
  return true;
}

add_action("wplc_hook_chat_notification","wplc_filter_control_chat_notification_user_loaded",10,3);
function wplc_filter_control_chat_notification_user_loaded($type,$cid,$data) {
    if ($type == "user_loaded") {
        /**
         * Only run if the chat status is not 1 or 5 (complete or browsing)
         * 1 is questionable here, we may have to remove it at a later stage.
         *  - Nick
         */
        if (isset($data['chat_data']) && isset($data['chat_data']->status) && (intval($data['chat_data']->status) != 5 || intval($data['chat_data']->status) != 5)) {

            global $wpdb;
            global $wplc_tblname_msgs;


            $msg = sprintf(__("User is browsing <small><a href='%s' target='_BLANK'>%s</a></small>","wplivechat"),wp_filter_post_kses($data['uri']),wp_filter_post_kses(wplc_shortenurl($data['uri'])));

            $wpdb->insert( 
                $wplc_tblname_msgs, 
                array( 
                        'chat_sess_id' => $cid, 
                        'timestamp' => current_time('mysql'),
                        'msgfrom' => __('System notification',"wplivechat"),
                        'msg' => $msg,
                        'status' => 0,
                        'originates' => 3
                ), 
                array( 
                        '%s', 
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d'
                ) 
            );



        }
    }
    return $type;
} 


add_action("wplc_hook_chat_notification","wplc_filter_control_chat_notification_await_agent",10,3);
function wplc_filter_control_chat_notification_await_agent($type,$cid,$data) {
    $wplc_settings = get_option("WPLC_SETTINGS");
    if(isset($wplc_settings['wplc_use_node_server']) && intval($wplc_settings['wplc_use_node_server']) == 1){ } else {

        if ($type == "await_agent") {

  

            global $wpdb;
            global $wplc_tblname_msgs;
            $wpdb->insert( 
                $wplc_tblname_msgs, 
                array( 
                        'chat_sess_id' => $cid, 
                        'timestamp' => current_time('mysql'),
                        'msgfrom' => __('System notification',"wplivechat"),
                        'msg' => $data['msg'],
                        'status' => 0,
                        'originates' => 0
                ), 
                array( 
                        '%s', 
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d'
                ) 
            );
        }
    }
    return $type;
} 


add_action("wplc_hook_chat_notification","wplc_filter_control_chat_notification_agent_joined",10,3);
function wplc_filter_control_chat_notification_agent_joined($type,$cid,$data) {


    if ($type == "joined") {

        global $wpdb;
        global $wplc_tblname_msgs;
        
        $chat_data = wplc_get_chat_data( $cid );  
        
        $wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
        $user_info = get_userdata(intval($data['aid']));

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


        $agent_tagline = apply_filters( "wplc_filter_agent_data_agent_tagline", $agent_tagline, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data );          
        $agent_bio = apply_filters( "wplc_filter_agent_data_agent_bio", $agent_bio, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data );          
        $social_links = apply_filters( "wplc_filter_agent_data_social_links", $social_links, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data);          

        $msg = $agent . " ". __("has joined the chat.","wplivechat");

        $data_array = array( 
            'chat_sess_id' => $cid, 
            'timestamp' => current_time('mysql'),
            'msgfrom' => __('System notification',"wplivechat"),
            'msg' => $msg,
            'status' => 0,
            'originates' => 0,
            'other' => maybe_serialize(array(
                'ntype' => 'joined',
                'email' => md5($user_info->user_email),
                'name' => $agent,
                'aid' => $user_info->ID,
                'agent_tagline' => $agent_tagline,
                'agent_bio' => $agent_bio,
                "social_links" => $social_links
            ))
        );
        $type_array = array( 
            '%s', 
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%s'
        );

        do_action( "wplc_store_agent_joined_notification", $data_array, $type_array );

        
    }
    return $type;
} 


add_action( "wplc_store_agent_joined_notification", "wplc_hook_control_store_agent_joined_notification", 10, 2 );
function wplc_hook_control_store_agent_joined_notification($data_array, $type_array) {
    global $wpdb;
    global $wplc_tblname_msgs;
    
    $wpdb->insert( 
        $wplc_tblname_msgs, 
        $data_array, 
        $type_array 
    );
    return;
}