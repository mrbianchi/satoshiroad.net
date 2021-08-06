<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'edit_user_profile', 'wplc_user_profile_fields_mrg' );
add_action( 'show_user_profile', 'wplc_user_profile_fields_mrg' );
function wplc_user_profile_fields_mrg( $user ){
    $ret = "";
    $ret .= "<a name='wplc-user-fields'></a><h2>".__( 'WP Live Chat Support - User Fields', 'wplivechat' )."</h2>";
    $ret .= "<table class='form-table'>";
    $ret .= "<tr>";
    $ret .= "<th>";
    $ret .= "<label for='wplc_user_tagline'>".__('User tagline', 'wplivechat')."</label>";
    $ret .= "</th>";
    $ret .= "<td>";
    $ret .= "<label for='wplc_user_tagline'>";
    if ( get_the_author_meta( 'wplc_user_tagline', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_tagline', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<textarea name='wplc_user_tagline' id='wplc_user_tagline' rows='6'>$predefined</textarea><br/>";
    $ret .= "<small>".__( 'This will show up at the top of the chatbox - Leave blank to disable.', 'wplivechat' )."</small>";
    $ret .= "</label>";
    $ret .= "</td>";
    $ret .= "</tr>";

    $ret .= "<tr>";
    $ret .= "<th>";
    $ret .= "<label for='wplc_user_bio'>".__('User bio', 'wplivechat')."</label>";
    $ret .= "</th>";
    $ret .= "<td>";
    $ret .= "<label for='wplc_user_bio'>";
    if ( get_the_author_meta( 'wplc_user_bio', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_bio', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<textarea name='wplc_user_bio' id='wplc_user_bio' rows='6'>$predefined</textarea><br/>";
    $ret .= "<small>".__( 'This will show up at the top of the chatbox - Leave blank to disable.', 'wplivechat' )."</small>";
    $ret .= "</label>";
    $ret .= "</td>";
    $ret .= "</tr>";

    $ret .= "<tr>";
    $ret .= "<th>";
    $ret .= "<label for='wplc_user_social_accounts'>".__('User social accounts', 'wplivechat')."</label>";
    $ret .= "</th>";
    $ret .= "<td>";
    $ret .= "<label for='wplc_user_twitter'>";
    if ( get_the_author_meta( 'wplc_user_twitter', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_twitter', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<input name='wplc_user_twitter' id='wplc_user_twitter' value='$predefined' /> <em>".__("Twitter URL","wplivechat")."</em><br/>";
    $ret .= "</label>";
    $ret .= "<label for='wplc_user_linkedin'>";
    if ( get_the_author_meta( 'wplc_user_linkedin', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_linkedin', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<input name='wplc_user_linkedin' id='wplc_user_linkedin' value='$predefined' /> <em>".__("LinkedIn URL","wplivechat")."</em><br/>";
    $ret .= "</label>";
    $ret .= "<label for='wplc_user_facebook'>";
    if ( get_the_author_meta( 'wplc_user_facebook', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_facebook', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<input name='wplc_user_facebook' id='wplc_user_facebook' value='$predefined' /> <em>".__("Facebook URL","wplivechat")."</em><br/>";
    $ret .= "</label>";
    $ret .= "<label for='wplc_user_website'>";
    if ( get_the_author_meta( 'wplc_user_website', $user->ID ) != "" ) { $predefined = sanitize_text_field( get_the_author_meta( 'wplc_user_website', $user->ID ) ); } else { $predefined = ""; }
    $ret .= "<input name='wplc_user_website' id='wplc_user_website' value='$predefined' /> <em>".__("Website URL","wplivechat")."</em><br/>";
    $ret .= "</label>";
    $ret .= "<small>".__( "This will show up at the top of the chatbox, in the agent's description - Leave each item blank to disable it.", 'wplivechat' )."</small>";
    $ret .= "</label>";
    $ret .= "</td>";
    $ret .= "</tr>";

    $ret .= "</table>";
    echo $ret;
}
add_action('edit_user_profile_update', 'wplc_save_user_profile_data_mrg'); 
add_action('personal_options_update', 'wplc_save_user_profile_data_mrg');
function wplc_save_user_profile_data_mrg( $user_id ){
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }    
    if( isset( $_POST['wplc_user_tagline'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_tagline'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_tagline', $predefined_response );
    }    
    if( isset( $_POST['wplc_user_bio'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_bio'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_bio', $predefined_response );
    }    
    if( isset( $_POST['wplc_user_twitter'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_twitter'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_twitter', $predefined_response );
    } 
    if( isset( $_POST['wplc_user_linkedin'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_linkedin'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_linkedin', $predefined_response );
    } 
    if( isset( $_POST['wplc_user_facebook'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_facebook'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_facebook', $predefined_response );
    } 
    if( isset( $_POST['wplc_user_website'] ) ){
        $predefined_response = wp_kses( nl2br( $_POST['wplc_user_website'] ), array( 'br' ) );
        update_user_meta( $user_id, 'wplc_user_website', $predefined_response );
    } 

}



add_filter("wplc_filter_further_live_chat_box_above_main_div","wplc_mrg_filter_control_live_chat_box_above_main_div",10,5);
function wplc_mrg_filter_control_live_chat_box_above_main_div( $msg, $wplc_settings, $cid, $chat_data, $agent ) {
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
  	if ( $cid ) {
		
		
		if ( isset( $chat_data->agent_id ) ) {
			$agent_id = intval( $chat_data->agent_id );
		} else { 
			$agent_id = get_current_user_id(); 
		}

		if ( $agent_id ) {


		 	$tagline = get_user_meta( $agent_id, 'wplc_user_tagline', true );
		    if( $tagline !== "" ){
		        $agent_tagline = $tagline;
		        $agent_tagline = '<span class="wplc_agent_infosection wplc_agent_tagline wplc-color-2">'.$agent_tagline.'</span>';
		    }
		 	$bio = get_user_meta( $agent_id, 'wplc_user_bio', true );
		    if( $bio !== "" ){
		        $agent_bio = $bio;
		        $agent_bio = '<span class="wplc_agent_infosection wplc_agent_bio wplc-color-2">'.$agent_bio.'</span>';
		    }

		 	$a_twitter = get_user_meta( $agent_id, 'wplc_user_twitter', true );
		 	$a_linkedin = get_user_meta( $agent_id, 'wplc_user_linkedin', true );
		 	$a_facebook = get_user_meta( $agent_id, 'wplc_user_facebook', true );
		 	$a_website = get_user_meta( $agent_id, 'wplc_user_website', true );



		    if ($a_twitter === '' && $a_linkedin === '' && $a_facebook === '' && $a_website === '') { 
		    	$social_links = '';
		    } else {
		    	$social_links = '<span class="wplc_agent_infosection wplc_agent_social_links wplc-color-2">';
		    	if ($a_twitter !== '') {
		    		$social_links .= '<a href="'.$a_twitter.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/twitter.png',dirname(__FILE__)).'" title="'.$agent.' - Twitter" border="0" /></a> &nbsp; ';
		    	}
		    	if ($a_linkedin !== '') {
		    		$social_links .= '<a href="'.$a_linkedin.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/linkedin.png',dirname(__FILE__)).'" title="'.$agent.' - LinkedIn" border="0" /></a> &nbsp; ';
		    	}
		    	if ($a_facebook !== '') {
		    		$social_links .= '<a href="'.$a_facebook.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/facebook.png',dirname(__FILE__)).'" title="'.$agent.' - Facebook" border="0" /></a> &nbsp; ';
		    	}
		    	if ($a_website !== '') {
		    		$social_links .= '<a href="'.$a_website.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/website.png',dirname(__FILE__)).'" title="'.$agent.' - Website" border="0" /></a> &nbsp; ';
		    	}
		    	$social_links .= '</span>';
		    }

			$msg = $agent_tagline.''.$agent_bio.''.$social_links;

		}
  	}

    

  }
  return $msg;
}

add_filter( "wplc_filter_agent_data_social_links", "wplc_mrg_filter_control_agent_data_social_links", 10, 7 );
function wplc_mrg_filter_control_agent_data_social_links(  $social_links, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data ) {
    if( !isset( $data ) ){ $data = false; }
    if (!isset($data) || !isset($data['aid'])) {
        $data = array();
        if (isset($chat_data) && is_object($chat_data) && isset($chat_data->agent_id)) {
            $data['aid'] = $chat_data->agent_id;
        }
    }


	$a_twitter = get_user_meta( intval($data['aid']), 'wplc_user_twitter', true );
    $a_linkedin = get_user_meta( intval($data['aid']), 'wplc_user_linkedin', true );
    $a_facebook = get_user_meta( intval($data['aid']), 'wplc_user_facebook', true );
    $a_website = get_user_meta( intval($data['aid']), 'wplc_user_website', true );

    if ($a_twitter === '' && $a_linkedin === '' && $a_facebook === '' && $a_website === '') { 
        $social_links = '';
    } else {
        $social_links = '<span class="wplc_agent_infosection wplc_agent_social_links wplc-color-2">';
        if ($a_twitter !== '') {
            $social_links .= '<a href="'.$a_twitter.'" title="'.$agent.'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/twitter.png',dirname(__FILE__)).'" title="'.$agent.' - Twitter" border="0" /></a> &nbsp; ';
        }
        if ($a_linkedin !== '') {
            $social_links .= '<a href="'.$a_linkedin.'" title="'.$agent.'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/linkedin.png',dirname(__FILE__)).'" title="'.$agent.' - LinkedIn" border="0" /></a> &nbsp; ';
        }
        if ($a_facebook !== '') {
            $social_links .= '<a href="'.$a_facebook.'" title="'.$agent.'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/facebook.png',dirname(__FILE__)).'" title="'.$agent.' - Facebook" border="0" /></a> &nbsp; ';
        }
        if ($a_website !== '') {
            $social_links .= '<a href="'.$a_website.'" title="'.$agent.'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/website.png',dirname(__FILE__)).'" title="'.$agent.' - Website" border="0" /></a> &nbsp; ';
        }
        $social_links .= '</span>';
    } 
    return $social_links;
}

add_filter( "wplc_filter_agent_data_agent_bio", "wplc_mrg_filter_control_agent_data_agent_bio", 10, 7 );
function wplc_mrg_filter_control_agent_data_agent_bio(  $agent_bio, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data ) {
    if( !isset( $data ) ){ $data = false; }
    $bio = get_user_meta( intval($chat_data->agent_id), 'wplc_user_bio', true );
    if( $bio !== "" ){
        $agent_bio = $bio;
        $agent_bio = '<span class="wplc_agent_infosection wplc_agent_bio wplc-color-2">'.$agent_bio.'</span>';
    }
    return $agent_bio;
}

add_filter( "wplc_filter_agent_data_agent_tagline", "wplc_mrg_filter_control_agent_data_agent_tagline", 10, 7 );
function wplc_mrg_filter_control_agent_data_agent_tagline(  $agent_tagline, $cid, $chat_data, $agent, $wplc_acbc_data, $user_info, $data ) {
    if( !isset( $data ) ){ $data = false; }
    $tagline = get_user_meta( intval($chat_data->agent_id), 'wplc_user_tagline', true );
    if( $tagline !== "" ){
        $agent_tagline = $tagline;
        $agent_tagline = '<span class="wplc_agent_infosection wplc_agent_tagline wplc-color-2">'.$agent_tagline.'</span>';
    }
    return $agent_tagline;
}






add_filter( "wplc_filter_simple_agent_data_agent_bio", "wplc_mrg_filter_simple_control_agent_data_agent_bio", 10, 2 );
function wplc_mrg_filter_simple_control_agent_data_agent_bio(  $agent_bio, $agent_id ) {
    if( !isset( $data ) ){ $data = false; }
    $bio = get_user_meta( intval($agent_id), 'wplc_user_bio', true );
    if( $bio !== "" ){
        $agent_bio = $bio;
        $agent_bio = '<span class="wplc_agent_infosection wplc_agent_bio wplc-color-2">'.$agent_bio.'</span>';
    }
    return $agent_bio;
}

add_filter( "wplc_filter_simple_agent_data_agent_tagline", "wplc_mrg_filter_simple_control_agent_data_agent_tagline", 10, 2 );
function wplc_mrg_filter_simple_control_agent_data_agent_tagline(  $agent_tagline, $agent_id ) {
    if( !isset( $data ) ){ $data = false; }
    $tagline = get_user_meta( intval($agent_id), 'wplc_user_tagline', true );
    if( $tagline !== "" ){
        $agent_tagline = $tagline;
        $agent_tagline = '<span class="wplc_agent_infosection wplc_agent_tagline wplc-color-2">'.$agent_tagline.'</span>';
    }
    return $agent_tagline;
}




add_filter( "wplc_filter_simple_agent_data_social_links", "wplc_mrg_filter_simple_control_agent_data_social_links", 10, 2 );
function wplc_mrg_filter_simple_control_agent_data_social_links(  $social_links, $agent_id ) {


    $a_twitter = get_user_meta( intval($agent_id), 'wplc_user_twitter', true );
    $a_linkedin = get_user_meta( intval($agent_id), 'wplc_user_linkedin', true );
    $a_facebook = get_user_meta( intval($agent_id), 'wplc_user_facebook', true );
    $a_website = get_user_meta( intval($agent_id), 'wplc_user_website', true );

    if ($a_twitter === '' && $a_linkedin === '' && $a_facebook === '' && $a_website === '') { 
        $social_links = '';
    } else {
        $social_links = '<span class="wplc_agent_infosection wplc_agent_social_links wplc-color-2">';
        if ($a_twitter !== '') {
            $social_links .= '<a href="'.$a_twitter.'" title="'.__("Twitter","wplivechat").'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/twitter.png',dirname(__FILE__)).'" title="'.__("Twitter","wplivechat").'" border="0" /></a> &nbsp; ';
        }
        if ($a_linkedin !== '') {
            $social_links .= '<a href="'.$a_linkedin.'" title="'.__("LinkedIn","wplivechat").'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/linkedin.png',dirname(__FILE__)).'" title="'.__("LinkedIn","wplivechat").'" border="0" /></a> &nbsp; ';
        }
        if ($a_facebook !== '') {
            $social_links .= '<a href="'.$a_facebook.'" title="'.__("Facebook","wplivechat").'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/facebook.png',dirname(__FILE__)).'" title="'.__("Facebook","wplivechat").'" border="0" /></a> &nbsp; ';
        }
        if ($a_website !== '') {
            $social_links .= '<a href="'.$a_website.'" title="'.__("Website","wplivechat").'" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/website.png',dirname(__FILE__)).'" title="'.__("Website","wplivechat").'" - Website" border="0" /></a> &nbsp; ';
        }
        $social_links .= '</span>';
    } 
    return $social_links;
}

