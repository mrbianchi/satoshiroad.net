<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<script>
  var nifty_api_key = '<?php echo get_option("wplc_node_server_secret_token"); ?>';
</script>

<?php
  $user = wp_get_current_user();

  global $wpdb;
  global $wplc_tblname_chats;
  $sql = "SELECT COUNT(id) as total_chats FROM `$wplc_tblname_chats` WHERE `agent_id` <> 0";
  $results = $wpdb->get_row( $sql );
  if ($results) {
    $total_count = intval($results->total_chats);
  } else {
    $total_count = 0;
  }
?>

<div class="wrap wplc_wrap">
  <h2 id="wplc_dashboard_page_title"><?php _e( 'WP Live Chat Support Dashboard', 'wplivechat' ) ?></h2>
  <div class="wplc_dashboard_container">
    <div class="wplc_dashboard_row">
      <div class="wplc_panel_col wplc_col_6">
        <div class="wplc_panel">
          <h3 class="wplc_panel_title"><?php printf( __( 'Welcome, %s', 'wplivechat' ), $user->display_name ); ?></h3>
        </div>
      </div>
      <div class="wplc_panel_col wplc_col_6">
        <div class="wplc_panel pull-right">
        </div>
      </div>
    </div>

    <div class="wplc_dashboard_row">
      <div class="wplc_panel_col wplc_col_12">
        <div class="wplc_panel">
          <div class="wplc_material_panel">
            <div class="wplc_panel_col wplc_col_4 wplc-center">
              <h4><?php _e("Actions","wplivechat"); ?></h4>
              <p><a href='admin.php?page=wplivechat-menu&subaction=override' class='button-primary'><?php echo __("Chat with Visitors","wplivechat"); ?></a></p>
              <p><a href='admin.php?page=wplivechat-menu-settings' class='button-secondary'><?php echo __("Settings","wplivechat"); ?></a></p>
            </div>
            <div class="wplc_panel_col wplc_col_4 wplc-center">
              <h4><?php _e("Active Visitors","wplivechat"); ?><br />&nbsp;</h4>
              <span class='wplc-stat' id='totalVisitors'>...</span>
              <p><a href='admin.php?page=wplivechat-menu&subaction=override' class='button-secondary'><?php echo __("Chat now","wplivechat"); ?></a></p>
            </div>
            <div class="wplc_panel_col wplc_col_4 wplc-center">
              <h4><?php _e("Conversations","wplivechat"); ?><br /><span class='smaller'><?php _e("Last 90 days","wplivechat"); ?></span></h4>
              <span class='wplc-stat'><?php echo $total_count; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="wplc_dashboard_row">
      <div class="wplc_panel_col wplc_col_12">
        <div class="wplc_panel">
          <div id="wplc_blog_posts"></div>
        </div>
      </div>
    </div>
    
  </div>
</div>