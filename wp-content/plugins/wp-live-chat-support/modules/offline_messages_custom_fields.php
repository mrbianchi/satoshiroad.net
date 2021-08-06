<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( "wplc_hook_offline_custom_fields_integration_settings", "wplc_hook_control_offline_custom_fields_integration_settings", 10 );

function wplc_hook_control_offline_custom_fields_integration_settings() {
	?>
		<p><?php echo sprintf(__("Use the <a href='%s'>Contact Form Ready</a> plugin (free) in order to customise the input fields of the offline message form.</a>","wplivechat"),admin_url('plugin-install.php?s=contact+form+ready&tab=search&type=term') ); ?></p>

	<?php
}