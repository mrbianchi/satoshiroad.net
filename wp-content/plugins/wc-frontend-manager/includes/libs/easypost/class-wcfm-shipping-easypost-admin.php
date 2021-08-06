<?php

class WCFM_Shipping_Easypost_Admin {

    private $easypost_services;
    private $signature_options = array(
        0 => '',
        1 => 'NO_SIGNATURE',
        2 => 'ADULT_SIGNATURE'
    );

    public function __construct() {
        
        $this->wf_init();

        if (!class_exists('WF_Easypost')) {
            include_once 'class-wf-shipping-easypost.php';
        }
        include_once ( 'class-wcfm-tracking-admin.php' );

        $this->easypost_services = include( 'data-wf-services.php' );

        //Print Shipping Label.
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'wf_add_easypost_metabox'), 15);
            add_action('admin_notices', array($this, 'wf_admin_notice'), 15);
        }
		
        if (isset($_GET['wf_easypost_shipment_confirm'])) {
            add_action('init', array($this, 'wf_easypost_shipment_confirm'), 14);
        } else if (isset($_GET['wf_easypost_add_funds'])) {
            add_action('init', array($this, 'wf_easypost_add_funds'), 14);
        } else if (isset($_GET['wf_easypost_void_shipment'])) {
            add_action('init', array($this, 'wf_easypost_void_shipment'), 14);
        } else if (isset($_GET['wf_easypost_generate_packages'])) {
            add_action('init', array($this, 'wf_easypost_generate_packages'), 14);
        }
    }

    function wf_admin_notice() {
        global $pagenow;
        global $post;

        if (!isset($_GET["wfeasypostmsg"]) && empty($_GET["wfeasypostmsg"])) {
            return;
        }

        $wfeasypostmsg = $_GET["wfeasypostmsg"];

        switch ($wfeasypostmsg) {
            case "0":
                echo '<div class="error"><p>' . __('Easypost.com: Sorry, An unexpected error occurred.', 'wf-easypost') . '</p></div>';
                break;
            case "1":
                echo '<div class="updated"><p>Easypost.com: Create Shipment is complete. You can proceed with print label.</p></div>';
                break;
            case "2":
                $wfeasypostmsg = get_post_meta($post->ID, 'wfeasypostmsg', true);
                echo '<div class="error"><p>Easypost.com: ' . $wfeasypostmsg . '</p></div>';
                break;
            case "3":
            case "4":
                echo '<div class="updated"><p>' . __('Easypost.com: Shipment cancelled successfully. Strongly recommend to double check it by login in to your Easypost.com account.', 'wf-easypost') . '</p></div>';
                break;
            case "5":
                echo '<div class="updated"><p>' . __('Easypost.com: Client side reset of labels and shipment completed. You can re-initiate shipment now.', 'wf-easypost') . '</p></div>';
                break;
            case "6":
                $wfeasypostmsg = get_post_meta($post->ID, 'wfeasypostmsg', true);
                echo '<div class="updated"><p>' . $wfeasypostmsg . '</p></div>';
                break;
            default:
                break;
        }
    }

    private function wf_init() {
        global $post;

        $shipmentconfirm_requests = array();
        // Load Easypost.com Settings.
        $this->settings = get_option('woocommerce_' . WF_EASYPOST_ID . '_settings', null);
        //$this->easypost_admin_enabled = isset($this->settings['easypost_admin_enabled']) ? $this->settings['easypost_admin_enabled'] : $this->easypost_admin_enabled;
        //Print Label Settings.
        $this->packing_method = isset($this->settings['packing_method']) ? $this->settings['packing_method'] : 'per_item';
        $this->disble_shipment_tracking = isset($this->settings['disble_shipment_tracking']) ? $this->settings['disble_shipment_tracking'] : 'TrueForCustomer';
        
        $this->wf_debug = isset($this->settings['debug_mode'])  && $this->settings['debug_mode'] == 'yes' ? true : false;
        $this->wf_insure = isset($this->settings['insurance'])  && $this->settings['insurance'] == 'yes' ? true : false;

		// Units
		$this->weight_unit = 'LBS';
		$this->weight_ounce_unit = 'OZ';
		$this->dim_unit = 'IN';
    }

    function wf_add_easypost_metabox() {
        global $post;

        if (!$post)
            return;
        
        if ( ! in_array( $post->post_type, array('shop_order') ) ) return;

        $order = $this->wf_load_order($post->ID);
        if (!$order)
            return;

        $shipping_service_data = $this->wf_get_shipping_service_data($order);

        // Shipping method is available. 
        if ($shipping_service_data) {
            add_meta_box('WF_Easypost_metabox', __('Easypost.com Shipment Label', 'wf-easypost'), array($this, 'wf_easypost_metabox_content'), 'shop_order', 'advanced', 'default');
            /*if ('yes' == $this->easypost_admin_enabled) {
                //add_meta_box( 'WF_Easypost_Account_metabox', __( 'Easypost.com Account', 'wf-easypost' ), array( $this, 'wf_easypost_account_metabox_content' ), 'shop_order', 'side', 'default' );
            }*/
        }
    }

    function wf_easypost_account_metabox_content() {
        global $post;
        //get_easypost_shipment_create
        // Authenticate with Easypost.com.
        $wf_easypost = new WF_Easypost();

        $easypost_authenticator = '';
        try {
            //$response_obj			= $wf_usps_easypost->get_easypost_authenticate_response();
        } catch (Exception $e) {
            echo __('Unable to get Auth information at this point of time. ', 'wf-easypost');
            echo $e->getMessage();
            return;
        }

        if (isset($response_obj->Authenticator)) {
            $easypost_authenticator = $response_obj->Authenticator;
        } else {
            echo '<strong>Unknown error - Please cross check settings.</strong>';
            return;
        }

        $request = array();
        $request['Authenticator'] = $easypost_authenticator;

        try {
            // It seems there is an issue verifying peers over ssl on a bsd server. I verified there was no problem connecting to the host over http (I'm running this this on my own server). It seems a BSD servers root cert chain is too restrictive. I'll most likely go ahead with the purchase of the pro plugin once I sort out the root chain cert issue and don't have to resort to a hack. Just wanted to let you know about this for future reference in case anyone else runs into this problem.
            // 'stream_context'=> stream_context_create(array('ssl'=> array('verify_peer'=>false,'verify_peer_name'=>false)))
            $plugin_dir = ABSPATH . 'wp-content/plugins/easypost-woocommerce-shipping/';
            $client = new SoapClient($plugin_dir . $wf_easypost->get_easypost_endpoint(), array('trace' => 1));
            $response_obj = $client->GetAccountInfo($request);
        } catch (Exception $e) {
            echo __('Unable to get information at this point of time. ', 'wf-easypost');
            echo $e->getMessage();
            return;
        }

        if (isset($response_obj->Authenticator)) {
            echo '<strong>Welcome ' . (string) $response_obj->Address->FirstName . '!</strong><br/>';
            $easypost_bal = (string) $response_obj->AccountInfo->PostageBalance->AvailablePostage;
            echo '<strong>Your Balance: $' . number_format_i18n($easypost_bal, 2) . '</strong><br/>';
            echo '<ul><li class="wide">$<select class="select" id="wf_easypost_topup_amount">';
            echo '<option value="" >0</option>';
            echo '<option value="10" >10</option>';
            echo '<option value="50" >50</option>';
            echo '<option value="100" >100</option>';
            echo '<option value="200" >200</option>';
            echo '<option value="500" >500</option>';
            echo '<option value="1000" >1000</option>';
            echo '</select>';
            
            $download_url = add_query_arg( 'wf_easypost_add_funds', base64_encode($post->ID . '|' . $response_obj->Authenticator . '|' . $response_obj->AccountInfo->PostageBalance->ControlTotal), get_wcfm_view_order_url( $post_id ) );
            ?>
            <a class="button button-primary tips easypost_add_funds" href="<?php echo $download_url; ?>" data-tip="<?php _e('Confirm Shipments', 'wf-easypost'); ?>"><?php _e('Add Funds', 'wf-easypost'); ?></a><hr style="border-color:#0074a2"></li></ul>

            <script type="text/javascript">
                jQuery("a.easypost_add_funds").on("click", function () {
                    location.href = this.href + '&wf_easypost_topup_amount=' + jQuery('#wf_easypost_topup_amount').val();
                    return false;
                });
            </script>
            <?php
        } else {
            echo '<strong>Unknown error - Please cross check settings.</strong>';
            return;
        }
    }

    function wf_easypost_add_funds() {
        //if (!$this->wf_user_check()) {
            //echo "You don't have admin privileges to view this page.";
            //exit;
        //}

        $query_string = explode('|', base64_decode($_GET['wf_easypost_add_funds']));
        $post_id = $query_string[0];
        $easypost_authenticator = $query_string[1];
        $control_total = $query_string[2];
        $easypost_topup_amount = $_GET['wf_easypost_topup_amount'] ? $_GET['wf_easypost_topup_amount'] : '';
        if ('' == $easypost_topup_amount) {
            $wfeasypostmsg = 2;
            update_post_meta($post_id, 'wfeasypostmsg', __('Please select the amount before proceeding with Add Funds.', 'wf-easypost'));
            
            wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
            exit;
        }

        $wf_easypost = new WF_Easypost();

        $request = array();
        $request['Authenticator'] = $easypost_authenticator;
        $request['PurchaseAmount'] = $easypost_topup_amount;
        $request['ControlTotal'] = $control_total;

        $message = '';
        try {
        	  $plugin_dir = ABSPATH . 'wp-content/plugins/easypost-woocommerce-shipping/';
            $client = new SoapClient($plugin_dir . $wf_easypost->get_easypost_endpoint(), array('trace' => 1));
            $response_obj = $client->PurchasePostage($request);
        } catch (Exception $e) {
            $message .= __('Unable to get information at this point of time. ', 'wf-easypost');
            $message .= $e->getMessage() . ' ';
        }

        if (isset($response_obj->PurchaseStatus)) {
            //$rejection_reason = isset( $response_obj->RejectionReason ) ?  $response_obj->RejectionReason : '';
            $message = __('Your Add Funds status is ', 'wf-easypost') . $response_obj->PurchaseStatus . '. ';
        } else {
            $wfeasypostmsg = 2;
            update_post_meta($post_id, 'wfeasypostmsg', __('Unknown error while Purchase Postage- Please cross check settings.', 'wf-easypost'));
            wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
            exit;
        }

        $wfeasypostmsg = 6;
        update_post_meta($post_id, 'wfeasypostmsg', $message);
        wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
        exit;
    }

    function wf_easypost_metabox_content( $order_id ) {
        global $post;
        $shipmentId = '';

        $order = $this->wf_load_order($order_id);
        $shipping_service_data = $this->wf_get_shipping_service_data($order);
        $default_service_type = $shipping_service_data['shipping_service'];

        $easypost_labels = get_post_meta($order_id, 'wf_easypost_labels', true);

        if (empty($easypost_labels)) {
        	  
            $download_url = add_query_arg( 'wf_easypost_shipment_confirm', base64_encode($shipmentId . '|' . $order_id), get_wcfm_view_order_url( $order_id ) );
			
			$stored_packages	=	get_post_meta( $order_id, '_wf_easypost_stored_packages', true );
			
			$wf_easypost_generate_packages = add_query_arg( 'wf_easypost_generate_packages', base64_encode($order_id), get_wcfm_view_order_url( $order_id ) );
			if(empty($stored_packages)	&&	!is_array($stored_packages)){
				echo '<strong>'.__('Auto generate packages.', 'wf-easypost').'</strong></br>';?>
                <a class="button button-primary tips easypost_generate_packages" href="<?php echo $wf_easypost_generate_packages; ?>" data-tip="<?php _e('Generate Packages', 'wf-easypost'); ?>">
                    <?php _e('Generate Packages', 'wf-easypost'); ?>
                </a><?php
			}else{?>


                <a class="easypost_generate_packages button" href="<?php echo $wf_easypost_generate_packages; ?>" data-tip="<?php _e('Generate Packages', 'wf-easypost'); ?>" style="float: right;overflow: hidden;">
                    <span class="dashicons dashicons-update help_tip" data-tip="<?php _e('Re-generate the Packages', 'wf-easypost')?> "  style="padding-top: 2px;"></span>
                </a><?php

				echo '<strong>'.__('Initiate your shipment.', 'wf-easypost').'</strong></br>';
				echo __('Select Preferred Service', 'wf-easypost').':';
				echo '<img class="help_tip" style="float:none;" data-tip="'.__('Contact Easypost.com for more info on these services.', 'wf-easypost').'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" />';
				
				echo '<ul>';
                    $enabled_services = array();
                    foreach( array_keys($this->easypost_services)  as $key => $carrier_name) {
                        if( !empty($this->settings['easypost_carrier']) 
                            && in_array($carrier_name,$this->settings['easypost_carrier']) 
                            && isset($this->settings['services'][$carrier_name]) 
                            && is_array($this->settings['services'][$carrier_name]) ) {
                            foreach ($this->settings['services'][$carrier_name] as $service_code => $service_data) {
                                if( $service_data['enabled'] !=1 ) {
                                    continue;
                                }
                                

                                $service_name = !empty( $service_data['name'] ) 
                                ? $service_data['name'] 
                                : $this->easypost_services[$carrier_name]['services'][$service_code];

                                $enabled_services[$service_code] = $service_name;

                            }
                        }
                    }
                    $available_services = array_keys($enabled_services);
                    if( empty( $available_services ) ){
                        echo "<span style='color: #f00'>Please enable some service from plugin settings page</span>";
                        return;
                    }else{
    					echo '<li class="wide"><select class="select" id="easypost_manual_service">';
                        foreach ($enabled_services as $service_code => $service_name) {
        					echo '<option value="' . $service_code . '" ' . selected($default_service_type, $service_code) . ' >' . $service_name . '</option>';
                        }
                        echo '</select></li>';
                    }
                    
                    $flat_rate_boxes = include( 'data-wf-flat-rate-boxes.php' );
                    $selected_flat_rate_boxes = isset($this->settings['selected_flat_rate_boxes'])?$this->settings['selected_flat_rate_boxes']:array();
                    $wf_easypost = new WF_Easypost();
                    $selected_flat_rate_boxes = $wf_easypost->selected_flat_rate_box_format_data($selected_flat_rate_boxes);

                    if( !empty($selected_flat_rate_boxes) ){
                    $default_shipping_method = '';
                        $shipping_methods = $order->get_shipping_methods();
                        if ( !empty($shipping_methods) ) {
                            $shipping_method = array_shift($shipping_methods);
                            $default_shipping_method = $shipping_method['method_id'];
                        }

                        echo __('Flat rate box', 'wf-easypost').':';
                        echo '<img class="help_tip" style="float:none;" data-tip="'.__('Select default to proceed with non-flat rate package(s).', 'wf-easypost').'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" />';
                        
                        echo '<li class="wide"><select class="select" id="easypost_flatrate_box">';
                        foreach($selected_flat_rate_boxes as $carrier =>  $boxes){
                            echo '<option value="">No flat rate boxes (default)</option>';
                            foreach ($boxes as $box_no => $box) {
                                echo '<option value="'.$box.'" '.selected($default_shipping_method, WF_EASYPOST_ID.':'.$carrier.':'.$box).'>'.print_r($flat_rate_boxes[$carrier][$box]['name'],1).'</option>';
                            }
                        }
                        echo '</select></li>';
                    }

					echo '<li>';
						echo '<h4>'.__( 'Package(s)' , 'wf-easypost').': </h4>';
						echo '<table id="wf_easypost_package_list" class="wf-shipment-package-table">';
							echo '<tr>';
								echo '<th>'.__('Wt.', 'wf-easypost').'</br>('.$this->weight_ounce_unit.')</th>';
								echo '<th>'.__('L', 'wf-easypost').'</br>('.$this->dim_unit.')</th>';
								echo '<th>'.__('W', 'wf-easypost').'</br>('.$this->dim_unit.')</th>';
								echo '<th>'.__('H', 'wf-easypost').'</br>('.$this->dim_unit.')</th>';
                                if($this->wf_insure){
								    echo '<th>'.__('Insur.', 'wf-easypost').'</th>';
                                }
								echo '<th>&nbsp;</th>';
							echo '</tr>';
							foreach($stored_packages as $stored_package_key	=>	$stored_package){
                                for ($i=1; $i <= $stored_package['BoxCount'] ; $i++) {
                                    $dimensions =   $this->get_dimension_from_package($stored_package);
                                    if(is_array($dimensions)){
                                        ?>
                                        <tr>
                                            <td style="text-align: center;"><input type="text" id="easypost_manual_weight" name="easypost_manual_weight[]" size="2" value="<?php echo $dimensions['WeightOz'];?>" /></td>     
                                            <td style="text-align: center;"><input type="text" id="easypost_manual_length" name="easypost_manual_length[]" size="2" value="<?php echo $dimensions['Length'];?>" /></td>
                                            <td style="text-align: center;"><input type="text" id="easypost_manual_width" name="easypost_manual_width[]" size="2" value="<?php echo $dimensions['Width'];?>" /></td>
                                            <td style="text-align: center;"><input type="text" id="easypost_manual_height" name="easypost_manual_height[]" size="2" value="<?php echo $dimensions['Height'];?>" /></td>
                                            <?php
                                            if( $this->wf_insure ){?>
                                                <td style="text-align: center;"><input type="text" id="easypost_manual_insurance" name="easypost_manual_insurance[]" size="2" value="<?php echo $dimensions['InsuredValue'];?>" /></td><?php
                                            }?>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?php
                                    }
                                }
							}
						echo '</table>';
						echo '<a class="wf-action-button wf-add-button" style="font-size: 12px;" id="wf_easypost_add_package">Add Package</a>';
					echo '</li>';				
				echo '</ul>';
				
				?>
				<a class="button button-primary tips easypost_create_shipment" href="<?php echo $download_url; ?>" data-tip="<?php _e('Create Shipment', 'wf-easypost'); ?>"><?php _e('Create Shipment', 'wf-easypost'); ?></a><hr style="border-color:#0074a2">
				<script type="text/javascript">
					jQuery(document).ready(function(){
                        insurance = '<?php echo $this->wf_insure;?>';
						jQuery('#wf_easypost_add_package').on("click", function(){
							var new_row = '<tr>';
								new_row 	+= '<td><input type="text" id="easypost_manual_weight" name="easypost_manual_weight[]" size="2" value="0"></td>';
								new_row 	+= '<td><input type="text" id="easypost_manual_length" name="easypost_manual_length[]" size="2" value="0"></td>';								
								new_row 	+= '<td><input type="text" id="easypost_manual_width" name="easypost_manual_width[]" size="2" value="0"></td>';
								new_row 	+= '<td><input type="text" id="easypost_manual_height" name="easypost_manual_height[]" size="2" value="0"></td>';
                                if( insurance ){
								    new_row 	+= '<td><input type="text" id="easypost_manual_insurance" name="easypost_manual_insurance[]" size="2" value="0"></td>';
                                }
								new_row 	+= '<td><a class="wf_easypost_package_line_remove">&#x26D4;</a></td>';
							new_row 	+= '</tr>';
							
							jQuery('#wf_easypost_package_list tr:last').after(new_row);
						});
						
						jQuery(document).on('click', '.wf_easypost_package_line_remove', function(){
							jQuery(this).closest('tr').remove();
						});
					});
					
					jQuery("a.easypost_create_shipment").on("click", function() {
						var manual_weight_arr 	= 	jQuery("input[id='easypost_manual_weight']").map(function(){return jQuery(this).val();}).get();
						var manual_weight 		=	JSON.stringify(manual_weight_arr);
						
						var manual_height_arr 	= 	jQuery("input[id='easypost_manual_height']").map(function(){return jQuery(this).val();}).get();
						var manual_height 		=	JSON.stringify(manual_height_arr);
						
						var manual_width_arr 	= 	jQuery("input[id='easypost_manual_width']").map(function(){return jQuery(this).val();}).get();
						var manual_width 		=	JSON.stringify(manual_width_arr);
						
						var manual_length_arr 	= 	jQuery("input[id='easypost_manual_length']").map(function(){return jQuery(this).val();}).get();
						var manual_length 		=	JSON.stringify(manual_length_arr);
						
						var manual_insurance_arr 	= 	jQuery("input[id='easypost_manual_insurance']").map(function(){return jQuery(this).val();}).get();
						var manual_insurance 		=	JSON.stringify(manual_insurance_arr);
						
					    var flatrate_box = jQuery('#easypost_flatrate_box').length ? jQuery('#easypost_flatrate_box').val() : '';

					   location.href = this.href + '&weight=' + manual_weight +
						'&length=' + manual_length
						+ '&width=' + manual_width
						+ '&height=' + manual_height
						+ '&insurance=' + manual_insurance
                        + '&wf_easypost_flatrate_box=' + flatrate_box
						+ '&wf_easypost_service=' + jQuery('#easypost_manual_service').val();
					   return false;
					});
				</script>
				<?php
			}?>
            <?php
        } else {
            foreach ($easypost_labels as $easypost_label) {
                ?>
                <strong>Tracking No: </strong><?php echo $easypost_label['tracking_number']; ?><br/>
                <a href="<?php echo esc_attr($easypost_label['url']); ?>" target="_blank" class="button button-primary tips" data-tip="<?php _e('Print Label ', 'wf-easypost'); ?>">
                    <?php if (strstr($easypost_label['url'], '.png') || strstr($easypost_label['url'], '.pdf') || strstr($easypost_label['url'], '.zpl')) : ?> <?php _e('Print Label', 'wf-easypost'); ?>
                    <?php else : ?>
                        <?php _e('View Label', 'wf-easypost'); ?>
                    <?php endif; ?>
                </a><hr style="border-color:#0074a2"><br/>
                <?php
            }

            $download_url = add_query_arg( 'wf_easypost_void_shipment', base64_encode($orderid), get_wcfm_view_order_url( $orderid ) );
            ?>
            <strong>Cancel The Shipment Created</strong><br>
            <a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Cancel Label(s)', 'wf-easypost'); ?>">Cancel Shipment</a><hr style="border-color:#0074a2">
            <?php
        }
    }

    private function wf_get_shipment_description($order) {
        $shipment_description = '';
        $order_items = $order->get_items();

        foreach ($order_items as $order_item) {
            $product_data = wc_get_product($order_item['variation_id'] ? $order_item['variation_id'] : $order_item['product_id'] );
            $title = $product_data->get_title();
            $shipment_description .= $title . ', ';
        }

        if ('' == $shipment_description) {
            $shipment_description = 'Package/customer supplied.';
        }

        return $shipment_description;
    }

    function wf_get_package_data($order) {
        $easypost_settings = get_option('woocommerce_' . WF_EASYPOST_ID . '_settings', null);
        $easypost_packing_method = isset($easypost_settings['packing_method']) ? $easypost_settings['packing_method'] : 'per_item';
        $package = $this->wf_create_package($order);
        $wf_easypost = new WF_Easypost();
        $package_data_array = $wf_easypost->wf_get_api_rate_box_data($package, $easypost_packing_method);

        return $package_data_array;
    }

    function wf_create_package($order) {
        $orderItems = $order->get_items();

        foreach ($orderItems as $orderItem) {
            $item_id = $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'];
            $product_data = wc_get_product($item_id);
            $items[$item_id] = array('data' => $product_data, 'quantity' => $orderItem['qty']);
        }
        $package['contents'] = $items;
        $package['destination'] = array(
            'country' => $order->shipping_country,
            'state' => $order->shipping_state,
            'zip' => $order->shipping_postcode,
            'city' => $order->shipping_city,
            'street1' => $order->shipping_address_1,
            'street2' => $order->shipping_address_2);

        return $package;
    }
	
    private function get_special_rates_eligibility($service){
        if( $service == 'MediaMail' ){
            $special_rates = 'USPS.MEDIAMAIL';

        }elseif ($service == 'LibraryMail') {
            $special_rates = 'USPS.LIBRARYMAIL';

        }else{
            $special_rates = false;
        }
        return $special_rates;
    }

    private function get_package_signature($order){
        $order_items = $order->get_items();
        $higher_signature_option = 0;
        foreach ($order_items as $order_item) {
            $signature = get_post_meta( $order_item['product_id'], '_wf_easypost_signature', 1);

            if( empty($signature) || !is_numeric ( $signature )){
                $signature = 0;
            }
            if( $signature > $higher_signature_option ){
                $higher_signature_option = $signature;
            }
        }
        return $this->signature_options[$higher_signature_option];
    }

    function wf_easypost_shipment_confirm() {

        //if (!$this->wf_user_check()) {
            //echo "You don't have admin privileges to view this page.";
            //exit;
        //}

        $wfeasypostmsg = '';
        // Load Easypost.com Settings.
        $easypost_settings = get_option('woocommerce_' . WF_EASYPOST_ID . '_settings', null);

        $api_mode = isset($easypost_settings['api_mode']) ? $easypost_settings['api_mode'] : 'Live';

        $query_string = explode('|', base64_decode($_GET['wf_easypost_shipment_confirm']));
        $post_id = $query_string[1];
        $wf_easypost_selected_service = isset($_GET['wf_easypost_service']) ? $_GET['wf_easypost_service'] : '';
        update_post_meta($post_id, 'wf_easypost_selected_service', $wf_easypost_selected_service);

        $selected_flatrate_box = isset($_GET['wf_easypost_flatrate_box']) ? $_GET['wf_easypost_flatrate_box'] : '';

        $order = $this->wf_load_order($post_id);
        if (!$order)
            return;
		
		$package_data_array = $this->wf_get_package_data($order);
        $package_data_array =   $this->manual_packages($package_data_array); // Filter data with manual packages

        if (empty($package_data_array)) {
            return false;
        }

        $easypost_printLabelType = isset($easypost_settings['printLabelType']) ? $easypost_settings['printLabelType'] : 'PNG';
        $easypost_packing_method = isset($easypost_settings['packing_method']) ? $easypost_settings['packing_method'] : 'per_item';

        $message = '';
        $shipment_details = array();

        $shipping_service_data = $this->wf_get_shipping_service_data($order);
        $default_service_type  = $shipping_service_data['shipping_service'];

        $shipment_details['options']['print_custom_1']  = $order->id;
        $shipment_details['options']['label_format']    =   $easypost_printLabelType;
       
        $signature_option = $this->get_package_signature( $order );
        if( !empty($signature_option) ){
            $shipment_details['options']['delivery_confirmation']   =  $signature_option; 
        }


        $specialrate = $this->get_special_rates_eligibility( $default_service_type );
        if( !empty($specialrate) ){
            $shipment_details['options']['special_rates_eligibility'] = $specialrate;
             
        }
        
        if( wcfm_is_vendor() ) {
        	$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
        	$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
        	$the_user = get_user_by( 'id', $vendor_id );
        	$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
        	$store_name     = empty( $store_name ) ? $the_user->display_name : $store_name;

        	$shipment_details['from_address']['name']    = $store_name;
					$shipment_details['from_address']['company'] = $store_name;;
					$shipment_details['from_address']['street1'] = isset( $vendor_data['address']['street_1'] ) ? $vendor_data['address']['street_1'] : '';
					$shipment_details['from_address']['street2'] = isset( $vendor_data['address']['street_2'] ) ? $vendor_data['address']['street_2'] : '';
					$shipment_details['from_address']['city']    = isset( $vendor_data['address']['city'] ) ? $vendor_data['address']['city'] : '';
					$shipment_details['from_address']['state']   = isset( $vendor_data['address']['state'] ) ? $vendor_data['address']['state'] : '';
					$shipment_details['from_address']['zip']     = isset( $vendor_data['address']['zip'] ) ? $vendor_data['address']['zip'] : '';
					$shipment_details['from_address']['email']   = $the_user->user_email;
					$shipment_details['from_address']['phone']   = isset( $vendor_data['phone'] ) ? esc_attr( $vendor_data['phone'] ) : '';
					$shipment_details['from_address']['country'] = isset( $vendor_data['address']['country'] ) ? $vendor_data['address']['country'] : '';
					//$shipment_details['from_address']['country'] = WC()->countries->get_base_country() ? WC()->countries->get_base_country() : '';
        } else {
					$shipment_details['from_address']['name']    = isset($easypost_settings['name']) ? $easypost_settings['name'] : '';
					$shipment_details['from_address']['company'] = isset($easypost_settings['company']) ? $easypost_settings['company'] : '';
					$shipment_details['from_address']['street1'] = isset($easypost_settings['street1']) ? $easypost_settings['street1'] : '';
					$shipment_details['from_address']['street2'] = isset($easypost_settings['street2']) ? $easypost_settings['street2'] : '';
					$shipment_details['from_address']['city']    = isset($easypost_settings['city']) ? $easypost_settings['city'] : '';
					$shipment_details['from_address']['state']   = isset($easypost_settings['state']) ? $easypost_settings['state'] : '';
					$shipment_details['from_address']['zip']     = isset($easypost_settings['zip']) ? $easypost_settings['zip'] : '';
					$shipment_details['from_address']['email']   = isset($easypost_settings['email']) ? $easypost_settings['email'] : '';
					$shipment_details['from_address']['phone']   = isset($easypost_settings['phone']) ? $easypost_settings['phone'] : '';
					$shipment_details['from_address']['country']   = isset($easypost_settings['country']) ? $easypost_settings['country'] : '';
					//$shipment_details['from_address']['country'] = WC()->countries->get_base_country() ? WC()->countries->get_base_country() : '';
				}

        $shipping_first_name = $order->shipping_first_name;
        $shipping_last_name = $order->shipping_last_name;
        $shipping_full_name = $shipping_first_name . ' ' . $shipping_last_name;

    
        $shipment_details['to_address']['name'] = isset($shipping_full_name) ? $shipping_full_name : '';
        $shipment_details['to_address']['street1'] = isset($order->shipping_address_1) ? $order->shipping_address_1 : '';
        $shipment_details['to_address']['street2'] = isset($order->shipping_address_2) ? $order->shipping_address_2 : '';
        $shipment_details['to_address']['city'] = isset($order->shipping_city) ? $order->shipping_city : '';
        $shipment_details['to_address']['company'] = isset($order->shipping_company) ? $order->shipping_company : '';
        $shipment_details['to_address']['state'] = isset($order->shipping_state) ? $order->shipping_state : '';
        $shipment_details['to_address']['zip'] = isset($order->shipping_postcode) ? $order->shipping_postcode : '';
        $shipment_details['to_address']['email'] = isset($order->billing_email) ? $order->billing_email : '';
        $shipment_details['to_address']['phone'] = isset($order->billing_phone) ? $order->billing_phone : '';
        $shipment_details['to_address']['country'] = isset($order->shipping_country) ? $order->shipping_country : '';
        $shipment_details['to_address']['residential'] = isset($easypost_settings['show_rates']) && $easypost_settings['show_rates'] == 'residential' ? true : '';
        
        if(isset($easypost_settings['return_address']) && $easypost_settings['return_address']=='yes')
        {
        $shipment_details['return_address']['name']    = isset($easypost_settings['return_name']) ? $easypost_settings['return_name'] : '';
        $shipment_details['return_address']['company'] = isset($easypost_settings['return_company']) ? $easypost_settings['return_company'] : '';
        $shipment_details['return_address']['street1'] = isset($easypost_settings['return_street1']) ? $easypost_settings['return_street1'] : '';
        $shipment_details['return_address']['street2'] = isset($easypost_settings['return_street2']) ? $easypost_settings['return_street2'] : '';
        $shipment_details['return_address']['city']    = isset($easypost_settings['return_city']) ? $easypost_settings['return_city'] : '';
        $shipment_details['return_address']['state']   = isset($easypost_settings['return_state']) ? $easypost_settings['return_state'] : '';
        $shipment_details['return_address']['zip']     = isset($easypost_settings['return_zip']) ? $easypost_settings['return_zip'] : '';
        $shipment_details['return_address']['email']   = isset($easypost_settings['return_email']) ? $easypost_settings['return_email'] : '';
        $shipment_details['return_address']['phone']   = isset($easypost_settings['return_phone']) ? $easypost_settings['return_phone'] : '';
        $shipment_details['return_address']['country']   = isset($easypost_settings['return_country']) ? $easypost_settings['return_country'] : '';
        }
        //  need to find some solution for intnat
        $international = FALSE;
        $eligible_for_customs_details = $this->is_eligible_for_customs_details($shipment_details['from_address']['country'],$shipment_details['to_address']['country'],$shipment_details['to_address']['city']);
        if($eligible_for_customs_details){
            $international = TRUE;
        
            $order_items = $order->get_items();
            $custom_line_array = array();
            foreach ($order_items as $order_item) {
                for ($i=0; $i < $order_item['qty']; $i++) { 
                    $product_data = wc_get_product($order_item['variation_id'] ? $order_item['variation_id'] : $order_item['product_id'] );
                    $title = $product_data->get_title();
                    if(WC()->version < '3.0'){
                       $weight = woocommerce_get_weight($product_data->get_weight(), 'lbs'); 
                    }else{
                        $weight = wc_get_weight($product_data->get_weight(), 'lbs');
                    }
                    $shipment_description = $title;
                    if (!empty($easypost_settings['customs_description'])) $shipment_description = $easypost_settings['customs_description'];
                    $shipment_description = ( strlen($shipment_description) >= 50 ) ? substr($shipment_description, 0, 45) . '...' : $shipment_description;
                    $quantity = $order_item['qty'];
                    $value = $order_item['line_subtotal'];


                    $custom_line = array();
                    $custom_line['description'] = $shipment_description;
                    $custom_line['quantity'] = 1;
                    $custom_line['value'] = $value/$quantity;
                    $custom_line['weight'] = (string) ($weight);
                    $custom_line['origin_country'] = $shipment_details['from_address']['country'];
                   
                    $wf_hs_code = get_post_meta( $order_item['product_id'], '_wf_hs_code', 1);
                    if( !empty( $wf_hs_code ) ) {
                        $custom_line['hs_tariff_number'] = $wf_hs_code;
                    }
                   
                    $custom_line_array[] = $custom_line;
                }
            }
            //for International shipping only
            $shipment_details['customs_info']['customs_certify'] = true;
            $shipment_details['customs_info']['customs_signer'] = 'Customs Signer';
            $shipment_details['customs_info']['contents_type'] = 'merchandise';
            $shipment_details['customs_info']['contents_explanation'] = '';
            $shipment_details['customs_info']['restriction_type'] = 'none';
            $shipment_details['customs_info']['eel_pfc'] = 'NOEEI 30.37(a)';
        }

        if(!class_exists('EasyPost\EasyPost')){
        	  $plugin_dir = ABSPATH . 'wp-content/plugins/easypost-woocommerce-shipping/';
            require_once($plugin_dir . "easypost.php");
        }
        \EasyPost\EasyPost::setApiKey($easypost_settings['api_key']);

        $easypost_labels = array();
        
        $package_count=0;
        foreach ($package_data_array as $package_data) {
            $tx_id = uniqid('wf_' . $order->id . '_');
            update_post_meta($order->id, 'wf_last_label_tx_id', $tx_id);

            if(!empty($selected_flatrate_box)){
                $shipment_details['parcel']['predefined_package'] = strpos($selected_flatrate_box, "-") ? substr($selected_flatrate_box, 0, strpos($selected_flatrate_box, "-")) : $selected_flatrate_box;;
            }
            else{
                $shipment_details['parcel']['length'] = $package_data['Length'];
                $shipment_details['parcel']['width'] = $package_data['Width'];
                $shipment_details['parcel']['height'] = $package_data['Height'];
            }
            $shipment_details['parcel']['weight'] = $package_data['WeightOz'];

            // below lines for International shipping - + customs info
            if ($international) {
                $m = 0;
                $shipment_details['customs_info']['customs_items'] = array();
                if (!empty($package_data['PackedItem'])) {
                    for ($m = 0; $m < sizeof($package_data['PackedItem']); $m++) {
                        //In box packing algorithm the individual product details are stored in object named 'meta'
                        $item = isset( $package_data['PackedItem'][$m]->meta ) ? $package_data['PackedItem'][$m]->meta : $package_data['PackedItem'][$m];
                        $item = $this->wf_load_product($item);

                        if (!empty($easypost_settings['customs_description'])){
                            $prod_title = $easypost_settings['customs_description'];
                        }
                        else{
                            $prod_title = $item->get_title();
                        }

                        $shipment_desc = ( strlen($prod_title) >= 50 ) ? substr($prod_title, 0, 45) . '...' : $prod_title;
                        $shipment_details['customs_info']['customs_items'][$m]['description'] = $shipment_desc;
                        $shipment_details['customs_info']['customs_items'][$m]['quantity'] = 1; //$quantity;
                        $shipment_details['customs_info']['customs_items'][$m]['value'] = $item->get_price();
                        $wf_hs_code = get_post_meta( $item->id, '_wf_hs_code', 1);

                        if( !empty( $wf_hs_code ) ) {
                            $shipment_details['customs_info']['customs_items'][$m]['hs_tariff_number'] = $wf_hs_code;
                        }
                        if(WC()->version < '3.0'){
                           $weight_to_send = woocommerce_get_weight( $item->weight, 'Oz' );
                       }else{
                           $weight_to_send = wc_get_weight($item->weight, 'Oz');
                       }
                        $shipment_details['customs_info']['customs_items'][$m]['weight'] = $weight_to_send;
                        $shipment_details['customs_info']['customs_items'][$m]['origin_country'] = $shipment_details['from_address']['country'];
                    }
                }else{ //if($this->packing_method == 'per_item'){ // PackedItem will be empty , also each item will be shipped separately
                    $shipment_details['customs_info']['customs_items'][0] = $custom_line_array[$package_count];
                }
            }
            try {
                try{						
                    $shipment = \EasyPost\Shipment::create($shipment_details);                    
                    $this->wf_debug("<h3>Debug mode is Enabled. Please Disable it in the <a href='".get_site_url()."/wp-admin/admin.php?page=wc-settings&tab=shipping&section=wf_easypost' tartget='_blank'>settings  page</a> if you do not want to see this.</h3>");
                    $this->wf_debug( 'Easypost CREATE SHIPMENT REQUEST: <pre style="background: rgba(158, 158, 158, 0.30);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment_details, true) . '</pre>' );
                    
                    $this->wf_debug( 'Easypost CREATE SHIPMENT OBJECT: <pre style="background: rgba(158, 158, 158, 0.30);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment, true) . '</pre>' );
                }catch (Exception $e) {
                    $message .= __('Create shipment failed. ', 'wf-easypost');
                    $message .= $e->getMessage() . ' ';
                    $wfeasypostmsg = 6;
                    update_post_meta($post_id, 'wfeasypostmsg', $message);

                    $this->wf_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
                    echo $message;
                    exit;
                }

                $srvc = $default_service_type;
				
                try {
                    
                    $shipment_obj_array = array(
                        'rate'      => $shipment->lowest_rate( array_keys($this->easypost_services), array($srvc) ),
                    );
                    if( $this->wf_insure && (float)$package_data['InsuredValue'] > 0 ){
                        $shipment_obj_array['insurance'] = $package_data['InsuredValue'];
                    }
                    $this->wf_debug( '<br><br>Easypost REQUEST (Buy-shipment): <pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment_obj_array, true) . "</pre>");

                    $response_obj = $shipment->buy($shipment_obj_array);

                }catch(Exception $e){
                    $carrier_services = include('data-wf-services.php');
                    $carrier;
                    $carrier_name;			
                    foreach($carrier_services as $service => $code) {
                        if(array_key_exists( $wf_easypost_selected_service, $code['services'])) {
                            $carrier_name = $service;
                        }
                    }
                    $message .= __('Something went wrong. ', 'wf-easypost');
                    $message .= $e->getMessage() . ' ';
                    
                    //This error occurs while generating shipping label.
                    if($e->ecode == 'SHIPMENT.POSTAGE.FAILURE' && $carrier_name == 'UPS')
                    {
                         $message .=__('<br>The UPS account tied to the Shipper Number you are using is not yet fully set up. Please contact UPS.','wf-easypost');
                    }
                    $wfeasypostmsg = 6;
                    update_post_meta($post_id, 'wfeasypostmsg', $message);
                    
                    $this->wf_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
                    echo $message;
                    exit;
                }
            } catch (Exception $e) {
                $message .= __('Unable to get information at this point of time. ', 'wf-easypost');
                $message .= $e->getMessage() . ' ';
            }
            if (isset($response_obj)) {
                
                $this->wf_debug( '<br><br>Easypost RESPONSE OBJECT(Buy-shipment): <pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($response_obj, true) . "</pre>");

                //$easypost_authenticator   = ( string ) $response_obj->Authenticator;
                $label_url = (string) $response_obj->postage_label->label_url;
                if (!empty($label_url)) {
                    $easypost_label = array();
                    $easypost_label['url'] = $label_url;
                    $easypost_label['tracking_number'] = (string) $response_obj->tracking_code;
                    $easypost_label['integrator_txn_id'] = isset($shipment_details['IntegratorTxID']) ? $shipment_details['IntegratorTxID'] : ''; //(string) $response_obj->reference;
                    $easypost_label['easypost_tx_id'] = (string) $response_obj->tracker->id;
                    $easypost_label['shipment_id'] = $shipment->id;

                    $easypost_labels[] = $easypost_label;
                }
            } else {
                $message .= __('Sorry. Something went wrong:', 'wf-easypost') . '<br/>';
            }
            $package_count++;
        }
		$carrier_services = include('data-wf-services.php');
		$carrier;
		$carrier_name;			
		foreach($carrier_services as $service => $code) {
			if(array_key_exists( $wf_easypost_selected_service, $code['services'])) {
				$carrier_name = $service;
			}
		}
		switch($carrier_name) {
			case 'UPS': {
                $carrier = 'ups';
                break;
            }
			case 'USPS': {
				$carrier = 'united-states-postal-service-usps';
				break;
			}
			case 'FedEx': {
				$carrier = 'fedex';
				break;
			}
		}
        if (isset($easypost_labels)) {
            // Update post 
            update_post_meta($post_id, 'wf_easypost_labels', $easypost_labels);

            // Auto fill tracking info.
            $shipment_id_cs = '';
            foreach ($easypost_labels as $easypost_label) {
                $shipment_id_cs .= $easypost_label['tracking_number'] . ',';
            }

            // Shipment Tracking (Auto)
            $admin_notice = '';
            try {
                    $admin_notice = WfTrackingUtil::update_tracking_data( $post_id, $shipment_id_cs, $carrier, WF_Tracking_Admin_EasyPost::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_EasyPost::SHIPMENT_RESULT_KEY );
            } catch ( Exception $e ) {
                    $admin_notice = '';
                    // Do nothing.
            }

            // Shipment Tracking (Auto)
            if( '' != $admin_notice && !$this->wf_debug ) {
                    WF_Tracking_Admin_EasyPost::display_admin_notification_message( $post_id, $admin_notice );
            }
            else {
                //Do your plugin's desired redirect.
                $wfeasypostmsg = 2;
                update_post_meta($post_id, 'wfeasypostmsg', $message);
                $this->wf_redirect( add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ) );
                exit;
             }
		
        } else {
            delete_post_meta($post_id, 'wf_easypost_labels');
        }

        if ('' != $message) {
            $wfeasypostmsg = 2;
            update_post_meta($post_id, 'wfeasypostmsg', $message);
            $this->wf_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
            exit;
        }

        // Shipment Tracking (Auto)
        if ('' != $admin_notice) {
            WF_Tracking_Admin_Easypost::display_admin_notification_message($post_id, $admin_notice);
        } else {
            $wfeasypostmsg = 1;
            $this->wf_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));
            exit;
        }
    }
	
	function wf_easypost_generate_packages(){
		
		//if (!$this->wf_user_check()) {
            //echo "You don't have admin privileges to view this page.";
            //exit;
        //}
		
		$post_id = base64_decode($_GET['wf_easypost_generate_packages']);
		
		$order = $this->wf_load_order($post_id);
        if (!$order)
            return;
		
		$package_data_array = $this->wf_get_package_data($order);
		update_post_meta( $post_id, '_wf_easypost_stored_packages', $package_data_array );
		wp_redirect( get_wcfm_view_order_url( $post_id ) );
		exit;
	}

    private function wf_redirect($url){
        if(!$this->wf_debug){
            wp_redirect($url);
        }
    }
    
    function get_client_side_reset_warning_message() {
        $current_page_uri = $_SERVER['REQUEST_URI'];
        $href_url = $current_page_uri . '&client_reset';
        $message = '</br>' . __('You can remove the labels manually by login to <a href="http://www.easypost.com/wooforce" target="_blank">easypost.com</a> and only then, proceed with clicking on ', 'wf-easypost') . '<a href="' . $href_url . '" class="button button-primary" >' . __('client side label reset.', 'wf-easypost') . '</a>.';
        return $message;
    }
    
    function is_eligible_for_customs_details($from_country, $to_country, $to_city) {
        $eligible_cities  = array("APO","FPO","DPO");
        if (($from_country != $to_country) || (in_array(strtoupper($to_city), $eligible_cities))) {
            return true;
        }
        else {
            return false;
        }
    } 

    function wf_easypost_void_shipment() {
        //if (!$this->wf_user_check()) {
            //echo "You don't have admin privileges to view this page.";
            //exit;
        //}

        $post_id = base64_decode($_GET['wf_easypost_void_shipment']);

        if (isset($_GET['client_reset'])) {
            delete_post_meta($post_id, 'wf_easypost_labels');

            $wfeasypostmsg = 6;
            $message = __('Client side reset is complete', 'wf-easypost');
            update_post_meta($post_id, 'wfeasypostmsg', $message);
            wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));

            exit;
        }

        $easypost_labels = get_post_meta($post_id, 'wf_easypost_labels', true);

        if (empty($easypost_labels)) {
            $wfeasypostmsg = 2;
            $message = __('Unable to reset label(s)', 'wf-easypost');
            $message .= $this->get_client_side_reset_warning_message();
            update_post_meta($post_id, 'wfeasypostmsg', $message);
            wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));

            exit;
        }

        $wf_easypost = new WF_Easypost();
        $message = '';
        foreach ($easypost_labels as $easypost_label) {
            $request = array();
            $easypost_settings = get_option('woocommerce_' . WF_EASYPOST_ID . '_settings', null);
            if(!class_exists('EasyPost\EasyPost')){
            	  $plugin_dir = ABSPATH . 'wp-content/plugins/easypost-woocommerce-shipping/';
                require_once($plugin_dir . "easypost.php");
            }
            \EasyPost\EasyPost::setApiKey($easypost_settings['api_key']);
            $shipment = \EasyPost\Shipment::retrieve($easypost_label['shipment_id']);

            $request['EasypostTxID'] = $easypost_label['easypost_tx_id'];
            try {
                $response_obj = $shipment->refund();
            } catch (Exception $e) {
                $message .= __('Unable to get information at this point of time. ', 'wf-easypost');
                $message .= $e->getMessage() . ' ';
            }
            if (isset($response_obj)) {
                // Success.
            } else {
                $message .= __('Unknown error while Cancel Indicium - Please cross check settings.', 'wf-easypost');
            }
        }

        if ('' != $message) {
            $wfeasypostmsg = 2;
            $message = __('There were errors while cancelling labels. Please remove those labels manually by login to easypost.com.', 'wf-easypost') . '<br/> Error: ' . $message;
            $message .= $this->get_client_side_reset_warning_message();
            update_post_meta($post_id, 'wfeasypostmsg', $message);
            wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));

            exit;
        }

        delete_post_meta($post_id, 'wf_easypost_labels');
        $wfeasypostmsg = 4;
        wp_redirect(add_query_arg( 'wfeasypostmsg', $wfeasypostmsg, get_wcfm_view_order_url( $post_id ) ));

        exit;
    }

    function wf_load_order($orderId) {
        if (!class_exists('WC_Order')) {
            return false;
        }
        return ( WC()->version < '2.7.0' ) ? new WC_Order( $orderId ) : new wf_order( $orderId );    
    }

    function wf_user_check() {
        if (is_admin()) {
            return true;
        }
        return false;
    }

    function wf_get_shipping_service_data($order) {

        $shipping_methods = $order->get_shipping_methods();

        if (!$shipping_methods) {
            return false;
        }

        $shipping_method = array_shift($shipping_methods);
        $shipping_service_tmp_data = explode(':', $shipping_method['method_id']);
        
        if(WC()->version < '3.4.0') {
            $shipping_service = $shipping_service_tmp_data[1];
        }
        else
            $shipping_service = $shipping_method['instance_id'];
        
        $wf_easypost_selected_service = '';

        $wf_easypost_selected_service = get_post_meta($order->id, 'wf_easypost_selected_service', true);

        if ('' != $wf_easypost_selected_service) {
            $shipping_service_data['shipping_method'] = WF_EASYPOST_ID;
            $shipping_service_data['shipping_service'] = $wf_easypost_selected_service;
            $shipping_service_data['shipping_service_name'] = isset($easypost_services[$wf_easypost_selected_service]) ? $easypost_services[$wf_easypost_selected_service] : '';
        } else if (!isset($shipping_service_tmp_data[0]) ||
                ( isset($shipping_service_tmp_data[0]) && $shipping_service_tmp_data[0] != WF_EASYPOST_ID )) {
            $shipping_service_data['shipping_method'] = WF_EASYPOST_ID;
            $shipping_service_data['shipping_service'] = '';
            $shipping_service_data['shipping_service_name'] = '';
        } else {
            $shipping_service_data['shipping_method'] = $shipping_service_tmp_data[0];
            $shipping_service_data['shipping_service'] = $shipping_service;
            $shipping_service_data['shipping_service_name'] = $shipping_method['name'];
        }
        return $shipping_service_data;
    }
	
	private function get_dimension_from_package($package){
		$dimensions	=	array(
			'Length'		=>	0,
			'Width'			=>	0,
			'Height'		=>	0,
			'WeightOz'		=>	0,
			'InsuredValue'	=>	0,
		);
		if(!is_array($package) || !isset($package['WeightOz'])){
			return $dimensions;
		}
		$dimensions['WeightOz']		=	$package['WeightOz'];
		$dimensions['Length']		=	isset($package['Length'])?$package['Length']:0;
		$dimensions['Width']		=	isset($package['Width'])?$package['Width']:0;
		$dimensions['Height']		=	isset($package['Height'])?$package['Height']:0;
		$dimensions['InsuredValue']	=	isset($package['InsuredValue'])?$package['InsuredValue']:0;
		return  $dimensions;
	}
	
	function manual_packages($packages){
		
		//If manual values not provided
		if(!isset($_GET['weight'])){
			return $packages;
		}
		
		// Get manual values
		$length_arr		=	json_decode(stripslashes(html_entity_decode($_GET["length"])));
		$width_arr		=	json_decode(stripslashes(html_entity_decode($_GET["width"])));
		$height_arr		=	json_decode(stripslashes(html_entity_decode($_GET["height"])));
		$weight_arr		=	json_decode(stripslashes(html_entity_decode($_GET["weight"])));		
		$insurance_arr	=	json_decode(stripslashes(html_entity_decode($_GET["insurance"])));
		
		//If extra values provided, then add it with the package list
		
		$no_of_package_entered	=	count($weight_arr);
		$no_of_packages			=	count($packages);
		
		if($no_of_package_entered > $no_of_packages){ 
			$package_clone	=	current($packages);
			
			if(isset($package_clone['PackedItem'])){ // Everything clone except packed items
				unset($package_clone['PackedItem']);
			}
			
			for($i=$no_of_packages; $i<$no_of_package_entered; $i++){
				$packages[$i]	=	$package_clone;
			}
		}
		
		// Overridding package values
		foreach($packages as $key => $package){
			$packages[$key]['WeightOz']      =	$weight_arr[$key];
			$packages[$key]['Length']        =	$length_arr[$key];
			$packages[$key]['Width']         =	$width_arr[$key];
			$packages[$key]['Height']        =	$height_arr[$key];
			$packages[$key]['InsuredValue']  =	isset($insurance_arr[$key]) ? $insurance_arr[$key] : '';
		}
		
		return $packages;
	}
	
    private function wf_debug( $message ) {
        if ( $this->wf_debug ) {
            echo($message);         
        }
        return;
    }

    private function wf_load_product( $product ){
        if( !$product ){
            return false;
        }
        if( !class_exists('wf_product') ){
            include_once('class-wf-legacy.php');
        }
        if($product instanceof wf_product){
            return $product;
        }
        return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
    }
}

new WCFM_Shipping_Easypost_Admin();