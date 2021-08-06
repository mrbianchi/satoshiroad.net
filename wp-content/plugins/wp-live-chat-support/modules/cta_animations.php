<?php
/**
 * Handles all call to action animations
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
add_action('wplc_hook_styling_setting_bottom', 'wplc_mrg_call_to_action_settings_area');
/**
* Renders settings area for CTA animations
*/
function wplc_mrg_call_to_action_settings_area(){
 	$wplc_settings = get_option("WPLC_SETTINGS");
 	$animations = wplc_mrg_call_to_action_get_animations();
 	$current_animations = isset($wplc_settings['wplc_pro_cta_anim']) ? $wplc_settings['wplc_pro_cta_anim'] : 'false';
 	?>	
 	<tr>
        <td width='300' valign='top'><?php _e("Call To Action Animation","wplivechat")?>:</td>
        <td>  
        	<select name='wplc_pro_cta_anim'>
        		<option value='false' <?php echo ($current_animations === 'false' ? 'selected=selected' : ''); ?> >No Animation</option>
        		<?php
        		if(is_array($animations) && count($animations) > 0){
        			foreach ($animations as $key => $value) {
        				echo "<option value='$key' " . ($current_animations === $key ? 'selected=selected' : '') . ">$value</option>";
        			}
        		}
        		?>
        	</select>
        </td>   
    </tr>
 	<?php
}

/**
 * Gets all CSS files in css/cta_animations and creates an array accordingly
*/
function wplc_mrg_call_to_action_get_animations(){
 	$animation_list = array();
 	try{
 		$animation_stylesheet = @scandir(__DIR__ . '\..\css\cta_animations');
 		if(!empty($animation_stylesheet)){
 			foreach ($animation_stylesheet as $value) {
 				if($value !== '.' && $value !== '..'){
 					$ext_index = strpos($value, '.css');
 					if($ext_index !== FALSE){
 						$animation_list[$value] = ucwords(substr($value, 0, $ext_index));
 					}
 				}
 			}
 		}
 	} catch (Exception $ex){

 	}

 	return $animation_list;
}

add_filter('wplc_pro_setting_save_filter', 'wplc_mrg_call_to_action_anim_save');
/**
 * Saves pro call to action settings
*/
function wplc_mrg_call_to_action_anim_save( $settings ) {
	if(isset($_POST['wplc_save_settings'])){
		if(isset($_POST['wplc_pro_cta_anim'])){
			$settings['wplc_pro_cta_anim'] = sanitize_text_field($_POST['wplc_pro_cta_anim']);
		} else {
			$settings['wplc_pro_cta_anim'] = "false";
		}
		return $settings;
	}
}

add_action('wplc_hook_push_js_to_front', 'wplc_mrg_call_to_action_anim_styles');
/**
 * Loads selected animation on the front end
*/
function wplc_mrg_call_to_action_anim_styles() {
	$wplc_settings = get_option( "WPLC_SETTINGS" );
	if (!empty($wplc_settings['wplc_pro_cta_anim']) && $wplc_settings['wplc_pro_cta_anim'] !== 'false' && $wplc_settings['wplc_pro_cta_anim'] !== false ) {
		wp_enqueue_style('wplc-pro-cta-animation', plugins_url('../css/cta_animations/' . trim($wplc_settings['wplc_pro_cta_anim']) , __FILE__ ));
	}
}
