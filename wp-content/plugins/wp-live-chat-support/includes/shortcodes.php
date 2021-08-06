<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
add_filter("init","wplc_add_shortcode",10,4);

function wplc_add_shortcode() {
	add_shortcode('wplc_live_chat', 'wplc_live_chat_box_shortcode');
}

function wplc_live_chat_box_shortcode( $atts, $content = null ) {
  $wplc_settings = get_option("WPLC_SETTINGS");
  $logged_in = apply_filters("wplc_loggedin_filter",false);
  $wplc_using_locale = (isset($wplc_settings['wplc_using_localization_plugin']) && $wplc_settings['wplc_using_localization_plugin'] == 1) ? true : false;
  $cid = intval($_COOKIE['wplc_cid']);
  $wplc_chat_box_content = wplc_theme_control_function($wplc_settings, $logged_in, $wplc_using_locale, $cid);

  // get attributes
  $atts = shortcode_atts( 
    array(
      'style' => 'normal'
    ),
    $atts,
    'wplc_live_chat'
  );

  $output = '<div class="wplc_live_chat_support_shortcode wplc_' . $atts['style'] . '">';
  $output  .= $wplc_chat_box_content;
  $output .= '</div">';
  return $output;
}