<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(class_exists("WP_REST_Request")){
	//The request class was found, move one
	include_once "wplc-api-routes.php";
	include_once "wplc-api-functions.php";
	
}else{
	//No Rest Request class
}

/*
 * Checks if a secret key has been created. 
 * If not create one for use in the API
*/
add_action("wplc_activate_hook", "wplc_api_s_key_check", 10);
add_action("wplc_update_hook", "wplc_api_s_key_check", 10);
function wplc_api_s_key_check(){
	if (!get_option("wplc_api_secret_token")) {
		$user_token = wplc_api_s_key_create();
        add_option("wplc_api_secret_token", $user_token);
    }
}


/*
 * Generates a new Secret Token
*/
function wplc_api_s_key_create(){
	$the_code = rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000);
	$the_time = time();
	$token = md5($the_code . $the_time);
	return $token;
}

/*
 * Adds 'Rest API' tab to settings area
*/
add_filter("wplc_filter_setting_tabs","wplc_api_settings_tab_heading");
function wplc_api_settings_tab_heading($tab_array) {
    $tab_array['api'] = array(
      "href" => "#tabs-api",
      "icon" => 'fa fa-plug',
      "label" => __("REST API","wplivechat")
    );
    return $tab_array;
}

/*
 * Adds 'Rest API' content to settings area
*/
add_action("wplc_hook_settings_page_more_tabs","wplc_api_settings_tab_content");
function wplc_api_settings_tab_content() {
	wplc_api_settings_head();
    ?>
		<div id="tabs-api">
	<?php

	if(!class_exists("WP_REST_Request")){
		?>
		 	<div class="update-nag">
		 		<?php _e("To make use of the REST API, please ensure you are using a version of WordPress with the REST API included.", "wplivechat");?>
		 		<br><br>
		 		<?php _e("Alternatively, please install the official Rest API plugin from WordPress.", "wplivechat");?>
		 	</div>
		<?php
	} else {

		$secret_token = get_option("wplc_api_secret_token"); //Checks for token
		?>
			<h3><?php _e("REST API", "wplivechat") ?></h3>
			<table class=" form-table wp-list-table wplc_list_table widefat fixed striped pages">
				<tbody>
					<tr>
						<td width='200'>
							<?php _e("Secret Token", "wplivechat") ?>
						</td>
						<td>
							<input style="max-width:60%; width:100%" type="text" value="<?php echo ($secret_token === false ? __('No secret token found', 'wplivechat') : $secret_token) ?>" readonly>
							<a class="button-secondary" href="?page=wplivechat-menu-settings&wplc_action=new_secret_key"><?php _e("Generate New", "wplivechat") ?></a>
						</td>
					</tr>
					<tr>
						<td width='200'>
							<?php _e("Supported API Calls", "wplivechat") ?>:
						</td>
						<td>
							<code>wp_live_chat_support/v1/accept_chat</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/accept_chat" wplcTerms="chat_id,agent_id" wplcVals="1,0"><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>
					<tr>
						<td width='200'>
						</td>
						<td>
							<code>wp_live_chat_support/v1/end_chat</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/end_chat" wplcTerms="chat_id,agent_id" wplcVals="1,0"><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>wp_live_chat_support/v1/send_message</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/send_message" wplcTerms="chat_id,message" wplcVals="1,Test Message"><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>wp_live_chat_support/v1/get_status</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/get_status" wplcTerms="chat_id" wplcVals="1"><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>wp_live_chat_support/v1/get_messages</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/get_messages" wplcTerms="chat_id,limit,offset" wplcVals="1,50,0"><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>

					<tr>
						<td>
						</td>
						<td>
							<code>wp_live_chat_support/v1/get_sessions</code> <code>GET, POST</code> 
							<code><a href="#" class="rest_test_button" wplcRest="wp_live_chat_support/v1/get_sessions" wplcTerms="" wplcVals=""><?php _e("Try", "wplivechat") ?></a></code>
						</td>
					</tr>

					<?php do_action("wplc_api_reference_hook"); ?>

					<tr>
						<td>
							<?php _e("API Response Codes", "wplivechat") ?>:
						</td>
						<td>
							<code>200</code> <code>Success</code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>400</code> <code>Bad Request</code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>401</code> <code>Unauthorized</code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>403</code> <code>Forbidden</code>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<code>404</code> <code>Content Not Found</code>
						</td>
					</tr>

					<?php do_action("wplc_api_response_ref_hook"); ?>
				</tbody>
			</table>
			<br>

			<?php do_action("wplc_api_below_table_hook"); ?>

		</div>
		
		<?php
	}

}

/*
 * Handles API Settings -> Generate new Token
*/
function wplc_api_settings_head(){
	if(isset($_GET)){
		if(isset($_GET["wplc_action"])){
			if($_GET["wplc_action"] === "new_secret_key"){
				$user_token = wplc_api_s_key_create();
       			update_option("wplc_api_secret_token", $user_token);
			}
		}
	}
}

/*
 * Add Rest Test Component
*/
add_action("wplc_api_below_table_hook", "wplc_api_test_component", 10);
function wplc_api_test_component(){
	$site_url = rest_url();
	$secret_token = get_option("wplc_api_secret_token");

	?>
		<script>
			jQuery(function(){
				jQuery(function(){
					jQuery(".rest_test_button").click(function (){
						var route = jQuery(this).attr('wplcRest');
						var terms = jQuery(this).attr('wplcTerms').split(",");
						var vals = jQuery(this).attr('wplcVals').split(",");
						wplc_rest_console_setup(route,terms,vals);
						wplc_rest_console_show();
					});

					jQuery("body").on("click", "#wplc_rest_console_button", function (){
						wplc_rest_ajax();
					});

					function wplc_rest_console_setup(route,terms,values){
						var url = "<?php echo $site_url; ?>";

						url += route + "?token=" + "<?php echo $secret_token; ?>";

						for(var i = 0; i < terms.length; i++){
							url += "&" + terms[i] + "=" + values[ (i < values.length ? i : values.length-1) ]
						}

						jQuery("#wplc_rest_console_input").val(encodeURI(url));
					}

					function wplc_rest_console_show(){
						jQuery(".wplc_rest_consol").fadeIn();
					}

					function wplc_rest_ajax(){
						var url = jQuery("#wplc_rest_console_input").val();
						jQuery.get(url, function(response){
							var returned_data = wplcParseResponse(response);
							jQuery("#wplc_rest_console_response").text("Success:\n--------\n" + returned_data);
						}).fail(function(e){
							var errors = "";
							errors = wplcParseResponse(e.responseText);
							jQuery("#wplc_rest_console_response").text("Error:\n--------\n" + errors);
						});
					}

					function wplcParseResponse(content){
						try{
							if(typeof content !== "object"){
						    	content = JSON.parse(content);
						    }
						}catch(e){
						    content = e.toString();
						}
						if (typeof e === "undefined") {
							var new_content ="";
							jQuery.each(content, function(i, val) {
								if(typeof val === "object"){
									new_content += wplcParseResponse(val);
								}else{
							  		new_content += "\n"+ i + ": "+ val;										
								}
							});
							content = new_content;
						}
						return content;
					}
				});
			});
			
		</script>
		<table class="wp-list-table wplc_list_table widefat fixed striped pages wplc_rest_consol" style="display:none">
			<thead>
				<tr>
					<th><?php _e("Rest Console ", "wplivechat") ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" value="<?php echo $site_url ?>" style="max-width: 600px; width:80%" id="wplc_rest_console_input">
						<a href="javascript:void(0)"  class="button" style="max-width:120px" id="wplc_rest_console_button"><?php _e("Try it!", "wplivechat"); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<textarea style="max-width: 600px; width:80%; min-height:250px" id="wplc_rest_console_response">

						</textarea>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
}