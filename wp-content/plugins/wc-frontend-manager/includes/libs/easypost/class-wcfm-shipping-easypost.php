<?php

/**
 * WF_USPS_Easypost class.
 *
 * @extends WC_Shipping_Method
 */
class WF_Easypost extends WC_Shipping_Method {

    private $domestic = array("US");
    private $found_rates;
    private $carrier_list;

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = WF_EASYPOST_ID;
        $this->method_title = __('EasyPost', 'wf-easypost');
        $this->method_description = __('The <strong>Easypost.com USPS</strong> plugin obtains rates dynamically from the Easypost.com API during cart/checkout.', 'wf-easypost');
        $this->services = include( 'data-wf-services.php' );
        $this->flat_rate_boxes = include( 'data-wf-flat-rate-boxes.php' );
        $this->flat_rate_pricing = include( 'data-wf-flat-rate-box-pricing.php' );
        $this->set_carrier_list();
        $this->init();
    }

    /**
     * init function.
     *
     * @access public
     * @return void
     */
    private function init() {
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : $this->enabled;
        $this->title = !empty($this->settings['title']) ? $this->settings['title'] : $this->method_title;
        $this->availability = isset($this->settings['availability']) ? $this->settings['availability'] : 'all';
        $this->countries = isset($this->settings['countries']) ? $this->settings['countries'] : array();
        $this->zip = isset($this->settings['zip']) ? $this->settings['zip'] : '';

        $this->disbleShipmentTracking = isset($this->settings['disbleShipmentTracking']) ? $this->settings['disbleShipmentTracking'] : 'TrueForCustomer';
        $this->fillShipmentTracking = isset($this->settings['fillShipmentTracking']) ? $this->settings['fillShipmentTracking'] : 'Manual';
        $this->disblePrintLabel = isset($this->settings['disblePrintLabel']) ? $this->settings['disblePrintLabel'] : '';
        $this->easypost_hidden_postage = isset($this->settings['easypost_hidden_postage']) ? $this->settings['easypost_hidden_postage'] : 'no';
        $this->defaultPrintService = isset($this->settings['defaultPrintService']) ? $this->settings['defaultPrintService'] : 'None';
        $this->printLabelSize = isset($this->settings['printLabelSize']) ? $this->settings['printLabelSize'] : 'Default';
        $this->printLabelType = isset($this->settings['printLabelType']) ? $this->settings['printLabelType'] : 'Png';
        $this->resid = isset($this->settings['show_rates']) && $this->settings['show_rates'] == 'residential' ? true : '';
        $this->senderName = isset($this->settings['name']) ? $this->settings['name'] : '';
        $this->senderCompanyName = isset($this->settings['company']) ? $this->settings['company'] : '';
        $this->senderAddressLine1 = isset($this->settings['street1']) ? $this->settings['street1'] : '';
        $this->senderAddressLine2 = isset($this->settings['street2']) ? $this->settings['street2'] : '';
        $this->senderCity = isset($this->settings['city']) ? $this->settings['city'] : '';
        $this->senderState = isset($this->settings['state']) ? $this->settings['state'] : '';
        //adding country
        $this->senderCountry = isset($this->settings['country']) ? $this->settings['country'] : '';
        $this->senderEmail = isset($this->settings['email']) ? $this->settings['email'] : '';
        $this->senderPhone = isset($this->settings['phone']) ? $this->settings['phone'] : '';
        $this->api_key = isset($this->settings['api_key']) ? $this->settings['api_key'] : WF_USPS_EASYPOST_ACCESS_KEY;
        $this->packing_method = isset($this->settings['packing_method']) ? $this->settings['packing_method'] : 'per_item';
        $this->boxes = isset($this->settings['boxes']) ? $this->settings['boxes'] : array();
        $this->custom_services = isset($this->settings['services']) ? $this->settings['services'] : array();
        $this->offer_rates = isset($this->settings['offer_rates']) ? $this->settings['offer_rates'] : 'all';
        $this->fallback = !empty($this->settings['fallback']) ? $this->settings['fallback'] : '';
        $this->mediamail_restriction = isset($this->settings['mediamail_restriction']) ? $this->settings['mediamail_restriction'] : array();
        $this->mediamail_restriction = array_filter((array) $this->mediamail_restriction);
        $this->unpacked_item_handling = !empty($this->settings['unpacked_item_handling']) ? $this->settings['unpacked_item_handling'] : '';
        $this->enable_standard_services = true;
        $this->debug = isset($this->settings['debug_mode']) && $this->settings['debug_mode'] == 'yes' ? true : false;
        $this->api_mode = isset($this->settings['api_mode']) ? $this->settings['api_mode'] : 'Live';
        $this->carrier = (isset($this->settings['easypost_carrier']) && !empty($this->settings['easypost_carrier'])) ? $this->settings['easypost_carrier'] : array();

        $this->flat_rate_fee = !empty($this->settings['flat_rate_fee']) ? $this->settings['flat_rate_fee'] : '';
        $this->selected_flat_rate_boxes = isset($this->settings['selected_flat_rate_boxes']) ? $this->settings['selected_flat_rate_boxes'] : array();
        
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'clear_transients'));
    }

    private function set_carrier_list() {
        foreach (array_keys($this->services) as $key => $carrier) {
            $carrier_list[$carrier] = $carrier;
        }
        $this->carrier_list = $carrier_list;
    }

    /**
     * environment_check function.
     *
     * @access public
     * @return void
     */
    private function environment_check() {
        global $woocommerce;

        $admin_page = version_compare(WOOCOMMERCE_VERSION, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';

        if (!$this->zip && $this->enabled == 'yes') {
            echo '<div class="error">
                <p>' . __('Easypost.com is enabled, but the zip code has not been set.', 'wf-easypost') . '</p>
            </div>';
        }

        $error_message = '';

        // Check for Easypost.com APIKEY
        if (!$this->api_key && $this->enabled == 'yes') {
            $error_message .= '<p>' . __('Easypost.com is enabled, but the Easypost.com API KEY has not been set.', 'wf-easypost') . '</p>';
        }

        if (!$error_message == '') {
            echo '<div class="error">';
            echo $error_message;
            echo '</div>';
        }
    }

    /**
     * admin_options function.
     *
     * @access public
     * @return void
     */
    public function admin_options() {
        // Check users environment supports this method
        $this->environment_check();

        // Show settings
        parent::admin_options();
    }

    /**
     * generate_services_html function.
     */
    public function generate_services_html() {
        ob_start();
        include( 'html-wf-services.php' );
        return ob_get_clean();
    }

    /**
     * generate_box_packing_html function.
     */
    public function generate_box_packing_html() {
        ob_start();
        include( 'html-wf-box-packing.php' );
        return ob_get_clean();
    }

    /**
     * validate_box_packing_field function.
     *
     * @access public
     * @param mixed $key
     * @return void
     */
    public function validate_box_packing_field($key) {
        $boxes = array();

        if (isset($_POST['boxes_outer_length'])) {
            $boxes_name = isset($_POST['boxes_name']) ? $_POST['boxes_name'] : array();
            $boxes_outer_length = $_POST['boxes_outer_length'];
            $boxes_outer_width = $_POST['boxes_outer_width'];
            $boxes_outer_height = $_POST['boxes_outer_height'];
            $boxes_inner_length = $_POST['boxes_inner_length'];
            $boxes_inner_width = $_POST['boxes_inner_width'];
            $boxes_inner_height = $_POST['boxes_inner_height'];
            $boxes_box_weight = $_POST['boxes_box_weight'];
            $boxes_max_weight = $_POST['boxes_max_weight'];
            $boxes_is_letter = isset($_POST['boxes_is_letter']) ? $_POST['boxes_is_letter'] : array();

            for ($i = 0; $i < sizeof($boxes_outer_length); $i ++) {

                if ($boxes_outer_length[$i] && $boxes_outer_width[$i] && $boxes_outer_height[$i] && $boxes_inner_length[$i] && $boxes_inner_width[$i] && $boxes_inner_height[$i]) {

                    $boxes[] = array(
                        'name' => wc_clean($boxes_name[$i]),
                        'outer_length' => floatval($boxes_outer_length[$i]),
                        'outer_width' => floatval($boxes_outer_width[$i]),
                        'outer_height' => floatval($boxes_outer_height[$i]),
                        'inner_length' => floatval($boxes_inner_length[$i]),
                        'inner_width' => floatval($boxes_inner_width[$i]),
                        'inner_height' => floatval($boxes_inner_height[$i]),
                        'box_weight' => floatval($boxes_box_weight[$i]),
                        'max_weight' => floatval($boxes_max_weight[$i]),
                        'is_letter' => isset($boxes_is_letter[$i]) ? true : false
                    );
                }
            }
        }

        return $boxes;
    }

    /**
     * validate_services_field function.
     *
     * @access public
     * @param mixed $key
     * @return void
     */
    public function validate_services_field($key) {
        $services = array();
        $posted_services = isset($_POST['easypost_service']) ? $_POST['easypost_service'] : array();

        foreach ($posted_services as $code => $settings) {

            foreach ($this->services[$code]['services'] as $key => $name) {

                $services[$code][$key]['enabled'] = isset($settings[$key]['enabled']) ? true : false;
                $services[$code][$key]['adjustment'] = wc_clean($settings[$key]['adjustment']);
                $services[$code][$key]['adjustment_percent'] = wc_clean($settings[$key]['adjustment_percent']);
                $services[$code][$key]['name'] = wc_clean($settings[$key]['name']);
                $services[$code][$key]['order'] = wc_clean($settings[$key]['order']);
            }
        }

        return $services;
    }

    /**
     * clear_transients function.
     *
     * @access public
     * @return void
     */
    public function clear_transients() {
        global $wpdb;

        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_easypost_quote_%') OR `option_name` LIKE ('_transient_timeout_easypost_quote_%')");
    }

    public function generate_activate_box_html() {
        ob_start();
        $plugin_name = 'easypost';
        include( 'wf_api_manager/html/html-wf-activation-window.php' );
        return ob_get_clean();
    }
    
    
    public function generate_easypost_tabs_html()
        {
            $current_tab = (!empty($_GET['subtab'])) ? esc_attr($_GET['subtab']) : 'general';

                echo '
                <div class="wrap">
                    <script>
                    jQuery(function($){
                    show_selected_tab($(".tab_general"),"general");
                    $(".tab_general").on("click",function(){
                                                return show_selected_tab($(this),"general");
                                        });
                    $(".tab_rates").on("click",function(){
                                                return show_selected_tab($(this),"rates");
                                        });
                    $(".tab_labels").on("click",function(){
                                                return show_selected_tab($(this),"label");
                                        });
                    $(".tab_packing").on("click",function(){
                                                return show_selected_tab($(this),"package");
                                        });
                    $(".tab_licence").on("click",function(){                                                
                                                return show_selected_tab($(this),"license");
                                        });
                    function show_selected_tab($element,$tab)
                    {
                        $(".nav-tab").removeClass("nav-tab-active");
                        $element.addClass("nav-tab-active");                   
                        $(".rates_tab_field").closest("tr,h3").hide();
                        $(".rates_tab_field").next("p").hide();

                        $(".general_tab_field").closest("tr,h3").hide();
                        $(".general_tab_field").next("p").hide();

                        $(".label_tab_field").closest("tr,h3").hide();
                        $(".label_tab_field").next("p").hide();

                        $(".package_tab_field").closest("tr,h3").hide();
                        $(".package_tab_field").next("p").hide();

                        $(".license_tab_field").closest("tr,h3").hide();
                        $(".license_tab_field").next("p").hide();
                        if($tab=="license")
                        {   
                            $(".activation_window").show();
                        }else{
                            $(".activation_window").hide();
                        }
                        $("#woocommerce_wf_easypost_availability").trigger("change"); 
                        $("."+$tab+"_tab_field").closest("tr,h3").show();
                        $("."+$tab+"_tab_field").next("p").show();
                        
                       
                       
                        if($tab=="label")
                        {
                        jQuery("#woocommerce_wf_easypost_id_return_address").trigger("change");
                        }
                        if($tab=="rates")
                        {
                        	if(document.getElementById("woocommerce_wf_easypost_id_availability").value=="specific")
							{
					            $("#woocommerce_wf_easypost_id_countries").closest("tr").show();
							}	
							else
							{
					            $("#woocommerce_wf_easypost_id_countries").closest("tr").hide();
							}
                        }
                        else
                        {
                        	$("#woocommerce_wf_easypost_id_countries").closest("tr").hide();
                        }
                        
                        if($tab=="package")
                        {
                            $("#woocommerce_wf_easypost_packing_method").change();
                            jQuery("#woocommerce_wf_easypost_id_packing_method").trigger("change"); 
                        }
                        if($tab=="license")
                        {
                            $(".woocommerce-save-button").hide();
                        }else
                        {
                            $(".woocommerce-save-button").show();
                        }
                        return false;
                    }   

                    });
                    </script>
                    <style>
                    .wrap {
                                min-height: 800px;
                            }
                    a.nav-tab{
                                cursor: default;
                    }
                    </style>
                    <hr class="wp-header-end">';
                    $tabs = array(
                        'general' => __("General", 'wf-easypost'),
                        'rates' => __("Rates & Services", 'wf-easypost'),
                        'labels' => __("Label Generation", 'wf-easypost'),
                        'packing' => __("Packaging", 'wf-easypost'),
                        'licence' => __("License", 'wf-easypost')
                    );
                    $html = '<h2 class="nav-tab-wrapper">';
                    foreach ($tabs as $stab => $name) {
                        $class = ($stab == $current_tab) ? 'nav-tab-active' : '';
                        $style = ($stab == $current_tab) ? 'border-bottom: 1px solid transparent !important;' : '';
                        $html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class." tab_".$stab . '" >' . $name . '</a>';
                    }
                    $html .= '</h2>';
                    echo $html;

        }
    
  
    

    /**
     * init_form_fields function.
     *
     * @access public
     * @return void
     */
    public function init_form_fields() {
        global $woocommerce;

        $shipping_classes = array();
        $classes = ( $classes = get_terms('product_shipping_class', array('hide_empty' => '0')) ) ? $classes : array();

        foreach ($classes as $class)
            $shipping_classes[$class->term_id] = $class->name;

        if (WF_EASYPOST_ADV_DEBUG_MODE == "on") { // Test mode is only for development purpose.
            $api_mode_options = array(
                'Live' => __('Live', 'wf-easypost'),
                'Test' => __('Test', 'wf-easypost'),
            );
        } else {
            $api_mode_options = array(
                'Live' => __('Live', 'wf-easypost'),
            );
        }

        $carrier_boxes = include( 'data-wf-flat-rate-boxes.php' );
        $rate_box_names = array();
        foreach ($carrier_boxes as $carrier => $rate_boxes) {
            foreach ($rate_boxes as $rate_box_code => $rate_box) {
                $rate_box_names[$carrier . ':' . $rate_box_code] = $carrier . ' : ' . $rate_box['name'];
            }
        }



        $this->form_fields = array(
           
            'easypost_wrapper'=>array(
                            'type'=>'easypost_tabs'
                        ),
           
            'licence' => array(
                'type' => 'activate_box',
                'class' => 'general_tab_field'
            ),
            'enabled' => array(
                'title' => __('Realtime Rates', 'wf-easypost'),
                'type' => 'checkbox',
                'label' => __('Enable', 'wf-easypost'),
                'description' => __('Enable realtime rates on Cart/Checkout page.', 'wf-easypost'),
                'default' => 'no',
                'desc_tip' => true,
                'class' => 'general_tab_field',
            ),
            'title' => array(
                'title' => __('Method Title', 'wf-easypost'),
                'type' => 'text',
                'description' => __('This controls the title for fall-back rate which the user sees during Cart/Checkout.', 'wf-easypost'),
                'default' => __($this->method_title, 'wf-easypost'),
                'placeholder' => __($this->method_title, 'wf-easypost'),
                'desc_tip' => true,
                'class' => 'rates_tab_field',
            ),
            'availability' => array(
                'title' => __('Method Available to', 'wf-easypost'),
                'type' => 'select',
                'css'  => 'padding: 0px;',
                'default' => 'all',
                'description' => __('Select the countries','wf-easypost'),
                'desc_tip' => true,
                'class' => 'availability rates_tab_field',
                'options' => array(
                    'all' => __('All Countries', 'wf-easypost'),
                    'specific' => __('Specific Countries', 'wf-easypost'),
                ),
            ),
            'countries' => array(
                'title' => __('Specific Countries', 'wf-easypost'),
                'type' => 'multiselect',
                'class' => 'chosen_select rates_tab_field',
                'description' => __('Select specific countries','wf-easypost'),
                'desc_tip' => true,
                'css' => 'width: 450px;',
                'default' => '',
                'options' => $woocommerce->countries->get_allowed_countries(),
            ),
            
            
            'debug_mode' => array(
                'title' => __('Debug Mode', 'wf-easypost'),
                'label' => __('Enable', 'wf-easypost'),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => __('Enable debug mode to show debugging information on your cart/checkout. Not recommended to enable this in live site with traffic.', 'wf-easypost'),
                'desc_tip' => true,
                'class'=> 'general_tab_field',
            ),
            'api' => array(
                'title' => __('Generic API Settings', 'wf-easypost'),
                'type' => 'title',
                'description' => __('To obtain a Easypost.com API Key, Signup & Login to the ', 'wf-easypost'). '<a href="http://www.easypost.com" target="_blank">' . __('EasyPost.com', 'wf-easypost') . '</a>'.__(' and then go to the ', 'wf-easypost'). '<a href="https://www.easypost.com/account/api-keys" target="_blank">'.__('API Keys section.', 'wf-easypost'). '</a></br>'.__('You will find different API Keys for Live and Test mode.', 'wf-easypost'),
                'class'=> 'general_tab_field',
            ),
            'api_mode' => array(
                'title' => __('API Mode', 'wf-easypost'),
                'type' => 'select',
                'css'  => 'padding: 0px;',
                'default' => 'Live',
                'options' => $api_mode_options,
                'description' => __('Live mode is the strict choice for Customers as Test mode is strictly restricted for development purpose by Easypost.com.', 'wf-easypost'),
                'desc_tip' => true,
                'class' => 'general_tab_field',
            ),
            'api_key' => array(
                'title' => __('API-KEY', 'wf-easypost'),
                'type' => 'password',
                'description' => __('Live and Test mode APIs keys are different. Make sure to enter the right key based on API Mode.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'custom_attributes' => array(
                    'autocomplete' => 'off'
                ),
                'class'=> 'general_tab_field',
            ),
            'label-settings' => array(
                'title' => __('Label Printing API Settings', 'wf-easypost'),
                'type' => 'title',
                'class' =>'label_tab_field',
            ),
            'printLabelType' => array(
                'title' => __('Print Label Type', 'wf-easypost'),
                'type' => 'select',
                'css'  => 'padding: 0px;',
                'default' => 'yes',
                'options' => array(
                    'PNG' => __('PNG', 'wf-easypost'),
                    'PDF' => __('PDF', 'wf-easypost'),
                ),
                'description' => __('Set print label file type.', 'wf-easypost'),
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            
            'insurance' => array(
                'title' => __('Insurance', 'wf-easypost'),
                'label' => __('Enable', 'wf-easypost'),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => __('Enable this option to insure the parcel. EasyPost charge 1% of the value, with a $1 minimum, and handle all the claims', 'wf-easypost'),
                'desc_tip' => true,
                'class' => 'label_tab_field',
            ),
            
            'origin_address' => array(
                'title' => __('Origin Address', 'wf-easypost'),
                'type' => 'title',
                'class' =>'label_tab_field',
            ),
            
            'name' => array(
                'title' => __('Sender Name', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter your name.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' => 'label_tab_field',
                
            ),
            'company' => array(
                'title' => __('Sender Company Name', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter your company name.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'street1' => array(
                'title' => __('Sender Address Line1', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter your address line 1.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'street2' => array(
                'title' => __('Sender Address Line2', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter your address line 2.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'city' => array(
                'title' => __('Sender City', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter your city.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'state' => array(
                'title' => __('Sender State Code', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter state short code (Eg: CA).', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'email' => array(
                'title' => __('Sender Email', 'wf-easypost'),
                'type' => 'email',
                'description' => __('Enter sender email', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'zip' => array(
                'title' => __('Zip Code', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter the postcode for the sender', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'rates_tab_field',
            ),
            //adding country
            'country' => array(
                'title' => __('Sender Country', 'wf-easypost'),
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'width: 450px;',
                'description' => __('Select the sender country', 'wf-easypost'),
                'desc_tip' => true,
                'default' => 'US',
                'options' => $woocommerce->countries->get_allowed_countries(),
                'class' =>'rates_tab_field',
            ),
            'phone' => array(
                'title' => __('Sender Phone', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter sender phone', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            
            'customs_description' => array(
                'title' => __('Customs Description', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Product description for International shipping.', 'wf-easypost'),
                'default' => '',
                'css' => 'width:50%;',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            'selected_flat_rate_boxes' => array(
                'title' => __('Flat Rate Boxes', 'wf-easypost'),
                'type' => 'multiselect',
                'class' => 'multiselect chosen_select selected_flat_rate_boxes rates_tab_field',
                'default' => '',
                'options' => $rate_box_names,
                'description' => __('Select flat rate boxes to make available.', 'wf-easypost'),
                'desc_tip' => false,
            ),
            'flat_rate_fee' => array(
                'title' => __('Flat Rate Fee', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Fee per-box excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'rates_tab_field',
            ),
            'fallback' => array(
                'title' => __('Fallback', 'wf-easypost'),
                'type' => 'text',
                'description' => __('If Easypost.com returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' => 'rates_tab_field',
            ),
            'show_rates' => array(
                'title' => __('Rates Type', 'wf-easypost'),
                'type' => 'select',
                'css'  => 'padding: 0px;',
                'default' => 'commercial',
                'options' => array(
                    'residential' => __('Residential', 'wf-easypost'),
                    'commercial' => __('Commercial', 'wf-easypost'),
                ),
                'description' => __('Rates will be fetched based on the address type that you choose here. Please note this functionality will be available only for supported carriers.', 'wf-easypost'),
                'desc_tip' => true,
                'class' =>'rates_tab_field',
            ),
            'packing_method' => array(
                'title' => __('Parcel Packing', 'wf-easypost'),
                'type' => 'select',
                'css'  => 'padding: 0px;',
                'default' => '',
                'class' => 'packing_method package_tab_field',
                'options' => array(
                    'per_item' => __('Default: Pack items individually', 'wf-easypost'),
                    'box_packing' => __('Recommended: Pack into boxes with weights and dimensions', 'wf-easypost'),
                ),
                'desc_tip' => true,
                'description' => __('Determine how items are packed before being sent to EasyPost.', 'wf-shipping-fedex'),
                
            ),
            'boxes' => array(
                'type' => 'box_packing',
                'class' =>'package_tab_field',
            ),
            'easypost_carrier' => array(
                'title' => __('Easypost Carrier(s)', 'wf-easypost'),
                'type' => 'multiselect',
                'description' => __('Select your Easypost Carriers.', 'wf-easypost'),
                'default' => array('USPS'),
                'css' => 'width: 450px;',
                'class' => 'ups_packaging chosen_select rates_tab_field',
                'options' => $this->carrier_list,
                'desc_tip' => true,
                'custom_attributes' => array(
                    'autocomplete' => 'off'
                ),
                
            ),
            'services' => array(
                'type' => 'services',
                'class' =>'rates_tab_field'
            ),
            
            
            'return_header' => array(
                'title' => __('Return Address', 'wf-easypost'),
                'type' => 'title',
                'class' =>'label_tab_field',
                ),
            
            'return_address'=> array(
               'title'=>__('Return Address', 'wf-easypost'),
                'label'=>__('Enable','wf-easypost'),
                'description'=>__('Enable this option if you want to input another address, which will be picked up as the return address while generating return labels. If not enabled, returns will be sent to the origin address mentioned above.','wf-easypost'),
                'desc_tip' => true,
               'type'=>'checkbox',
               'default'=>'no',
               'class' =>'label_tab_field',
               
           ),
            'return_name'=> array(
               
               'title'=>__('Name', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter name.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_company'=> array(
               
               'title'=>__('Company Name', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter Company name.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_street1'=> array(
               
               'title'=>__('Address Line1', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter Address Line1.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_street2'=> array(
               
               'title'=>__('Address Line2', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter Address Line2.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_city'=> array(
               
               'title'=>__('City', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter City.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_state'=> array(
               
               'title'=>__('State Code', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter state short code (Eg: CA)', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_zip' => array(
                'title' => __('Zip Code', 'wf-easypost'),
                'type' => 'text',
                'description' => __('Enter Postcode.', 'wf-easypost'),
                'desc_tip' => true,
                'default' => '',
                'class' =>'label_tab_field',
            ),
            'return_country' => array(
                'title' => __('Country', 'wf-easypost'),
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'width: 450px;',
                'description' => __('Select the country.', 'wf-easypost'),
                'desc_tip' => true,
                'default' => 'US',
                'options' => $woocommerce->countries->get_allowed_countries(),
                'class' =>'label_tab_field',
            ),
            'return_phone'=> array(
               
               'title'=>__('Phone', 'wf-easypost'),
               'type'=>'text',
                'description' => __('Enter phone number.', 'wf-easypost'),
                'desc_tip' => true,
                'default'=>'',
                'class' =>'label_tab_field',
               
           ),
            'return_email' => array(
                'title' => __('Email', 'wf-easypost'),
                'type' => 'email',
                'description' => __('Enter email', 'wf-easypost'),
                'default' => '',
                'desc_tip' => true,
                'class' =>'label_tab_field',
            ),
            
            
        );
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping( $package = array() ) {
        global $woocommerce;
        $this->rates = array();
        $this->unpacked_item_costs = 0;
        $domestic = in_array($package['destination']['country'], $this->domestic) ? true : false;

        $this->debug(__('Easypost.com debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-easypost'));

        if ( $this->enable_standard_services ) {
            // Get cart package details and proceed with GetRates.
            $package_requests = $this->get_package_requests($package);
            libxml_use_internal_errors(true);
            if ($package_requests) {

                if(!class_exists('EasyPost\EasyPost')){
                    require_once(plugin_dir_path(dirname(__FILE__)) . "/easypost.php");
                }
                \EasyPost\EasyPost::setApiKey($this->settings['api_key']);

                $responses = array();
                foreach ($package_requests as $key => $package_request) {
                    $responses[] = $this->get_result($package_request);
                }
                if (!$responses) {
                    return false;
                }

                $found_rates = array();

                foreach ($responses as $response_ele) {
                    $response_obj = $response_ele['response'];

                    if ( isset( $response_obj->rates ) && !empty( $response_obj->rates ) ) {
                        foreach ( $this->carrier as $carrier_name ) {
                            $flag_currency_convertion = false;
                            foreach ( $response_obj->rates as $easypost_rate ) {
                                if ( $carrier_name == $easypost_rate->carrier ) {
                                    if( $flag_currency_convertion  == false) {
                                        $from_currency = $easypost_rate->currency;
                                        $to_currency = get_woocommerce_currency();
                                        $converted_currency = $this->xa_currency_converter($from_currency, $to_currency, $easypost_rate->carrier);
                                        $flag_currency_convertion = true;
                                    }
                                    
                                    if( !$converted_currency ) {
                                        break;
                                    }
                                    
                                    $service_type = (string) $easypost_rate->service;
                                    $service_name = (string) ( isset($this->custom_services[$carrier_name][$service_type]['name']) && !empty($this->custom_services[$carrier_name][$service_type]['name']) ) ? $this->custom_services[$carrier_name][$service_type]['name'] : $this->services[$carrier_name]['services'][$service_type];
                                    $total_amount = $response_ele['quantity'] * $easypost_rate->rate;
                                    $total_amount = $total_amount * $converted_currency;
                                    // Sort
                                    if ( isset( $this->custom_services[$carrier_name][$service_type]['order'] ) ) {
                                        $sort = $this->custom_services[$carrier_name][$service_type]['order'];
                                    } else {
                                        $sort = 999;
                                    }

                                    if (isset($found_rates[$service_type])) {
                                        $found_rates[$service_type]['cost'] = $found_rates[$service_type]['cost'] + $total_amount;
                                    } else {
                                        $found_rates[$service_type]['label'] = $service_name;
                                        $found_rates[$service_type]['cost'] = $total_amount;
                                        $found_rates[$service_type]['carrier'] = $easypost_rate->carrier;
                                        $found_rates[$service_type]['sort'] = $sort;
                                    }
                                }
                            }
                        }
                    } else {
                        $this->debug(__('Easypost.com - No rated returned from API.', 'wf-easypost'));
                        // return;
                    }
                }
                $rate_added = 0;
                if ($found_rates) {
                    uasort($found_rates, array($this, 'sort_rates'));
                    foreach ($this->carrier as $carrier_name) {
                        foreach ($found_rates as $service_type => $found_rate) {
                            // Enabled check
                            if ($carrier_name == $found_rate['carrier']) {
                                if (isset($this->custom_services[$carrier_name][$service_type]) && empty($this->custom_services[$carrier_name][$service_type]['enabled'])) {
                                    continue;
                                }
                                $total_amount = $found_rate['cost'];
                                // Cost adjustment %
                                if (!empty($this->custom_services[$carrier_name][$service_type]['adjustment_percent'])) {
                                    $total_amount = $total_amount + ( $total_amount * ( floatval($this->custom_services[$carrier_name][$service_type]['adjustment_percent']) / 100 ) );
                                }
                                // Cost adjustment
                                if (!empty($this->custom_services[$carrier_name][$service_type]['adjustment'])) {
                                    $total_amount = $total_amount + floatval($this->custom_services[$carrier_name][$service_type]['adjustment']);
                                }
                                $labelName = !empty($this->settings['services'][$carrier_name][$service_type]['name']) ? $this->settings['services'][$carrier_name][$service_type]['name'] : $this->services[$carrier_name]['services'][$service_type];
                                $rate = array(
                                    'id' => (string) $this->id . ':' . $service_type,
                                    'label' => (string) $labelName,
                                    'cost' => (string) $total_amount,
                                    'calc_tax' => 'per_item',
                                );
                                // Register the rate
                                $this->add_rate($rate);
                                $rate_added++;
                            }
                        }
                    }
                }
            }
        }
        if ($domestic) {
            $flat_rate = $this->calculate_flat_rate_box_rate($package);

            if ($flat_rate) {
                $found_flat_rates[$flat_rate['id']] = $flat_rate;
            }

            if (isset($found_flat_rates) && is_array($found_flat_rates)) {
                foreach ($found_flat_rates as $found_flat_rate) {
                    $this->add_rate($found_flat_rate);
                    $rate_added++;
                }
            }
        }

    }
    
    function xa_currency_converter( $from_currency, $to_currency, $carrier ) {
        if($from_currency == $to_currency) {
             return 1;
        }
        else {
            $from_currency = urlencode($from_currency);
            $to_currency = urlencode($to_currency);
            try {
            $result = @file_get_contents("https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency=$from_currency&to_currency=$to_currency&apikey=G1QF4V7WM07HNOB2");
            if ( $result === FALSE ) {
                throw new Exception("Unable to receive currency conversion response from Alpha Vantage API call ( https://www.alphavantage.co ). Skipping the shipping rates for the carrier $carrier as shop currency and the currency returned by Rates API differs.");
              }
            }
            catch(Exception $e) {
                 $this->debug(__($e->getMessage(),'wf-easypost'));
                 return 0;
            }
           $result = json_decode($result,true);
            $converted_currency = $result['Realtime Currency Exchange Rate']['5. Exchange Rate'];
            return $converted_currency;
        }
    }
    

    private function get_result($package_request, $predefined_package = '') {
        // Get rates.
        try {
            $payload = array();
            $payload['from_address'] = array(
                "name" => $this->senderName,
                "company" => $this->senderCompanyName,
                "street1" => $this->senderAddressLine1,
                "street2" => $this->senderAddressLine2,
                "city" => $this->senderCity,
                "state" => $this->senderState,
                "zip" => $this->zip,
                //adding country
                "country"=>$this->senderCountry,
                "phone" => $this->senderPhone
            );
           
            $payload['to_address'] = array(
                // Name and Street1 are required fields for getting rates.
                // But, at this point, these details are not available.
                "name" => "-",
                "street1" => "-",
                "residential" => $this->resid,
                "zip" => $package_request['request']['Rate']['ToZIPCode'],
                "country" => $package_request['request']['Rate']['ToCountry']
            );
            
            
            
            if (!empty($package_request['request']['Rate']['WeightLb']) && $package_request['request']['Rate']['WeightOz'] == 0.00) {
                $package_request['request']['Rate']['WeightOz'] = number_format($package_request['request']['Rate']['WeightLb'] * 16, 0, '.', '');
            }

            $payload['parcel'] = array(
                'length' => $package_request['request']['Rate']['Length'],
                'width' => $package_request['request']['Rate']['Width'],
                'height' => $package_request['request']['Rate']['Height'],
                'weight' => $package_request['request']['Rate']['WeightOz'],
            );
            if (!empty($predefined_package)) {
                $payload['parcel']['predefined_package'] = strpos($predefined_package, "-") ? substr($predefined_package, 0, strpos($predefined_package, "-")) : $predefined_package;
            }

            $payload['options'] = array(
                "special_rates_eligibility" => 'USPS.LIBRARYMAIL,USPS.MEDIAMAIL',
                
            );
            
            
            

            $this->debug('EASYPOST REQUEST: <pre>' . print_r($payload, true) . '</pre>');
            $shipment = \EasyPost\Shipment::create($payload);
            $response = json_decode($shipment);
            $this->debug('EASYPOST RESPONSE: <pre>' . print_r($response, true) . '</pre>');
            $response_ele = array();

            $response_ele['response'] = $response;
            $response_ele['quantity'] = $package_request['quantity'];
        } catch (Exception $e) {
            
            if(strpos($e->getMessage(), 'Could not connect to EasyPost') !== false)
            {
                if ($this->fallback) {
            $this->debug(__('Easypost.com - Calculating fall back rates', 'wf-easypost'));
            $rate = array(
                'id' => (string) $this->id . ':_fallback',
                'label' => (string) $this->title,
                'cost' => $this->fallback,
                'calc_tax' => 'per_item',
            );
            // Register the rate
            $this->add_rate($rate);
                }
            }
            
            $this->debug(__('Easypost.com - Unable to Get Rates: ', 'wf-easypost') . $e->getMessage());
            if (WF_EASYPOST_ADV_DEBUG_MODE == "on") {
                $this->debug(print_r($e, true));
            }
            return false;
        }
        return $response_ele;
    }

    /**
     * prepare_rate function.
     *
     * @access private
     * @param mixed $rate_code
     * @param mixed $rate_id
     * @param mixed $rate_name
     * @param mixed $rate_cost
     * @return void
     */
    private function prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost) {

        // Name adjustment
        if (!empty($this->custom_services[$rate_code]['name']))
            $rate_name = $this->custom_services[$rate_code]['name'];
        // Merging
        if (isset($this->found_rates[$rate_id])) {
            $rate_cost = $rate_cost + $this->found_rates[$rate_id]['cost'];
            $packages = 1 + $this->found_rates[$rate_id]['packages'];
        } else {
            $packages = 1;
        }

        // Sort
        if (isset($this->custom_services[$rate_code]['order'])) {
            $sort = $this->custom_services[$rate_code]['order'];
        } else {
            $sort = 999;
        }

        $this->found_rates[$rate_id] = array(
            'id' => $rate_id,
            'label' => $rate_name,
            'cost' => $rate_cost,
            'sort' => $sort,
            'packages' => $packages
        );
    }

    /**
     * sort_rates function.
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @return void
     */
    public function sort_rates($a, $b) {
        if ($a['sort'] == $b['sort'])
            return 0;
        return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
    }

    /**
     * get_request function.
     *
     * @access private
     * @return void
     */
    // WF - Changing function to public.
    public function get_package_requests($package) {

        // Choose selected packing
        switch ($this->packing_method) {
            case 'box_packing' :
                $requests = $this->box_shipping($package);
                break;
            case 'per_item' :
            default :
                $requests = $this->per_item_shipping($package);
                break;
        }

        return $requests;
    }

    /**
     * per_item_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
     */
    private function per_item_shipping($package) {
        global $woocommerce;

        $requests = array();
        $domestic = in_array($package['destination']['country'], $this->domestic) ? true : false;

        // Get weight of order
        foreach ($package['contents'] as $item_id => $values) {
            $values['data'] = $this->wf_load_product($values['data']);

            if (!$values['data']->needs_shipping()) {
                $this->debug(sprintf(__('Product # is virtual. Skipping.', 'wf-easypost'), $item_id));
                continue;
            }

            if (!$values['data']->get_weight()) {
                $this->debug(sprintf(__('Product # is missing weight. Using 1lb.', 'wf-easypost'), $item_id));

                $weight = 1;
                $weightoz = 1; // added for default
            } else {
                $weight = wc_get_weight($values['data']->get_weight(), 'lbs');
                $weightoz = wc_get_weight($values['data']->get_weight(), 'oz');
            }

            $size = 'REGULAR';

            if ($values['data']->length && $values['data']->height && $values['data']->width) {

                $dimensions = array(wc_get_dimension($values['data']->length, 'in'), wc_get_dimension($values['data']->height, 'in'), wc_get_dimension($values['data']->width, 'in'));

                sort($dimensions);

                if (max($dimensions) > 12) {
                    $size = 'LARGE';
                }

                $girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];
            } else {
                $dimensions = array(0, 0, 0);
                $girth = 0;
            }

            $quantity = $values['quantity'];

            if ('LARGE' === $size) {
                $rectangular_shaped = 'true';
            } else {
                $rectangular_shaped = 'false';
            }

            $dest_postal_code = !empty($package['destination']['postcode']) ? $package['destination']['postcode'] : (isset($package['destination']['zip']) ? $package['destination']['zip'] : '');
            if ($domestic) {
                $request['Rate'] = array(
                    'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                    'ToZIPCode' => strtoupper(substr($dest_postal_code, 0, 5)),
                    'ToCountry' => $package['destination']['country'],
                    'WeightLb' => floor($weight),
                    'WeightOz' => number_format($weightoz, 0, '.', ''),
                    'PackageType' => 'Package',
                    'Length' => $dimensions[2],
                    'Width' => $dimensions[1],
                    'Height' => $dimensions[0],
                    'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                    'InsuredValue' => $values['data']->get_price(),
                    'RectangularShaped' => $rectangular_shaped
                );
            } else {
                $request['Rate'] = array(
                    'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                    'ToZIPCode' => $dest_postal_code,
                    'ToCountry' => $package['destination']['country'],
                    'Amount' => $values['data']->get_price(),
                    'WeightLb' => floor($weight),
                    'WeightOz' => number_format($weightoz, 0, '.', ''),
                    'PackageType' => 'Package',
                    'Length' => $dimensions[2],
                    'Width' => $dimensions[1],
                    'Height' => $dimensions[0],
                    'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                    'InsuredValue' => $values['data']->get_price(),
                    'RectangularShaped' => $rectangular_shaped
                );
            }

            $request['unpacked'] = array();
            $request['packed'] = array($values['data']);

            $request_ele = array();
            $request_ele['request'] = $request;
            $request_ele['quantity'] = $quantity;

            $requests[] = $request_ele;
        }
        return $requests;
    }

    /**
     * Generate a package ID for the request
     *
     * Contains qty and dimension info so we can look at it again later when it comes back from USPS if needed
     *
     * @return string
     */
    public function generate_package_id($id, $qty, $length, $width, $height, $weight) {
        return implode(':', array($id, $qty, $length, $width, $height, $weight));
    }

    /**
     * box_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
     */
    private function box_shipping($package) {
        global $woocommerce;

        $requests = array();
        $domestic = in_array($package['destination']['country'], $this->domestic) ? true : false;

        if (!class_exists('WF_Boxpack')) {
            include_once 'class-wf-packing.php';
        }

        $boxpack = new WF_Boxpack();

        // Define boxes
        foreach ($this->boxes as $key => $box) {

            $newbox = $boxpack->add_box($box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight']);

            $newbox->set_id(isset($box['name']) ? $box['name'] : $key );
            $newbox->set_inner_dimensions($box['inner_length'], $box['inner_width'], $box['inner_height']);

            if ($box['max_weight']) {
                $newbox->set_max_weight($box['max_weight']);
            }
        }

        // Add items
        foreach ($package['contents'] as $item_id => $values) {
            $values['data'] = $this->wf_load_product($values['data']);

            if (!$values['data']->needs_shipping()) {
                continue;
            }

            if ($values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight) {

                $dimensions = array($values['data']->length, $values['data']->height, $values['data']->width);
            } else {
                $this->debug(sprintf(__('Product #%d is missing dimensions. Using 1x1x1.', 'wf-easypost'), $item_id), 'error');

                $dimensions = array(1, 1, 1);
            }

            for ($i = 0; $i < $values['quantity']; $i ++) {

                $wtLB = wc_get_weight($values['data']->get_weight(), 'lbs');

                $boxpack->add_item(
                        wc_get_dimension($dimensions[2], 'in'), wc_get_dimension($dimensions[1], 'in'), wc_get_dimension($dimensions[0], 'in'), $wtLB, $values['data']->get_price(), $item_id //WF: Adding Item Id and Quantity as meta.
                );
            }
        }

        // Pack it
        $boxpack->pack();

        // Get packages
        $box_packages = $boxpack->get_packages();

        foreach ($box_packages as $key => $box_package) {
            if (!empty($box_package->unpacked)) {
                $this->debug('Unpacked Item');

                switch ($this->unpacked_item_handling) {
                    case 'fallback' :
                        // No request, just a fallback
                        $this->unpacked_item_costs += $this->fallback;
                        continue;
                        break;
                    case 'ignore' :
                        // No request
                        continue;
                        break;
                    case 'abort' :
                        // No requests!
                        return false;
                        break;
                }
            } else {
                $this->debug('Packed ' . $box_package->id);
            }

            $weight = number_format($box_package->weight * 16, 2, '.', '');
            $size = 'REGULAR';
            $dimensions = array($box_package->length, $box_package->width, $box_package->height);

            sort($dimensions);

            if (max($dimensions) > 12) {
                $size = 'LARGE';
            }

            $girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];

            if ('LARGE' === $size) {
                $rectangular_shaped = 'true';
            } else {
                $rectangular_shaped = 'false';
            }
            $request = array();

            $dest_postal_code = !empty($package['destination']['postcode']) ? $package['destination']['postcode'] : (isset($package['destination']['zip']) ? $package['destination']['zip'] : '');
            if ($domestic) {
                $request['Rate'] = array(
                    'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                    'ToZIPCode' => strtoupper(substr($dest_postal_code, 0, 5)),
                    'ToCountry' => $package['destination']['country'],
                    'Amount' => $values['data']->get_price(),
                    'WeightOz' => number_format($weight, 0, '.', ''),
                    'PackageType' => 'Package',
                    'Length' => $dimensions[2],
                    'Width' => $dimensions[1],
                    'Height' => $dimensions[0],
                    'Value' => $box_package->value,
                    'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                    'InsuredValue' => $box_package->value,
                    'RectangularShaped' => $rectangular_shaped
                );
            } else {
                $request['Rate'] = array(
                    'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                    'ToZIPCode' => strtoupper($dest_postal_code),
                    'ToCountry' => $package['destination']['country'],
                    'Amount' => $values['data']->get_price(),
                    'WeightOz' => number_format($weight, 0, '.', ''),
                    'PackageType' => 'Package',
                    'Length' => $dimensions[2],
                    'Width' => $dimensions[1],
                    'Height' => $dimensions[0],
                    'Value' => $box_package->value,
                    'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                    'InsuredValue' => $box_package->value,
                    'RectangularShaped' => $rectangular_shaped
                );
            }
            $request['unpacked'] = (isset($box_package->unpacked) && !empty($box_package->unpacked)) ? $box_package->unpacked : array();
            $request['packed'] = (isset($box_package->packed) && !empty($box_package->packed)) ? $box_package->packed : array();

            $request_ele = array();
            $request_ele['request'] = $request;
            $request_ele['quantity'] = 1;

            $requests[] = $request_ele;
        }

        return $requests;
    }

    public function debug($message, $type = 'notice') {
        if ($this->debug && !is_admin()) { //WF: is_admin check added.
            if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
                wc_add_notice($message, $type);
            } else {
                global $woocommerce;
                $woocommerce->add_message($message);
            }
        }
    }

    /**
     * wf_get_api_rate_box_data function.
     *
     * @access public
     */
    public function wf_get_api_rate_box_data($package, $packing_method) {
        $this->packing_method = $packing_method;
        $requests = $this->get_package_requests($package);
        $package_data_array = array();
        if ($requests) {
            foreach ($requests as $key => $request) {

                $package_data = array();
                $request_data = $request['request']['Rate'];

                // PS: Some of PHP versions doesn't allow to combining below two line of code as one. 
                // id_array must have value at this point. Force setting it to 1 if it is not.
                $package_data['BoxCount'] = isset($request['quantity']) ? $request['quantity'] : 1;
                $package_data['WeightOz'] = isset($request_data['WeightOz']) ? $request_data['WeightOz'] : '';
                $package_data['FromZIPCode'] = isset($request_data['FromZIPCode']) ? $request_data['FromZIPCode'] : '';
                $package_data['ToZIPCode'] = isset($request_data['ToZIPCode']) ? $request_data['ToZIPCode'] : '';
                $package_data['ToCountry'] = isset($request_data['ToCountry']) ? $request_data['ToCountry'] : '';
                $package_data['RectangularShaped'] = isset($request_data['RectangularShaped']) ? $request_data['RectangularShaped'] : '';
                $package_data['InsuredValue'] = isset($request_data['InsuredValue']) ? $request_data['InsuredValue'] : '';
                $package_data['ShipDate'] = isset($request_data['ShipDate']) ? $request_data['ShipDate'] : '';
                $package_data['Width'] = isset($request_data['Width']) ? $request_data['Width'] : '';
                $package_data['Length'] = isset($request_data['Length']) ? $request_data['Length'] : '';
                $package_data['Height'] = isset($request_data['Height']) ? $request_data['Height'] : '';
                $package_data['Value'] = isset($request_data['Value']) ? $request_data['Value'] : '';
                $package_data['Girth'] = isset($request_data['Girth']) ? $request_data['Girth'] : '';
                $package_data['PackedItem'] = !empty($request['request']['packed']) ? $request['request']['packed'] : '';

                $package_data_array[] = $package_data;
            }
        }

        return $package_data_array;
    }

    /*
      Calculate flat rate box function
     */

    private function calculate_flat_rate_box_rate($package) {
        global $woocommerce;

        $cost = 0;

        if (!class_exists('WF_Boxpack'))
            include_once 'class-wf-packing.php';

        $boxpack = new WF_Boxpack();
        $added = array();

        $selected_flat_rate_boxes = $this->selected_flat_rate_box_format_data($this->selected_flat_rate_boxes);
        $flat_rate_boxes = $this->flat_rate_boxes;

        // Add Boxes
        foreach ($selected_flat_rate_boxes as $carrier => $boxes) {
            foreach ($boxes as $box_code) {
                if (!isset($flat_rate_boxes[$carrier][$box_code])) { // Can't load the box
                    continue;
                }

                $box = $flat_rate_boxes[$carrier][$box_code];

                $service_code = $carrier . ':' . $box_code;
                $newbox = $boxpack->add_box($box['length'], $box['width'], $box['height']);
                $newbox->set_max_weight($box['weight']);
                $newbox->set_id($service_code);

                if (isset($box['volume']) && method_exists($newbox, 'set_volume')) {
                    $newbox->set_volume($box['volume']);
                }

                if (isset($box['type']) && method_exists($newbox, 'set_type')) {
                    $newbox->set_type($box['type']);
                }

                $added[] = $service_code . ' - ' . $box['name'] . ' (' . $box['length'] . 'x' . $box['width'] . 'x' . $box['height'] . ')';
            }
        }

        $this->debug('Calculating EasyPost Flat Rate with boxes: ' . implode(', ', $added));

        // Add items
        foreach ($package['contents'] as $item_id => $values) {
            $values['data'] = $this->wf_load_product($values['data']);

            if (!$values['data']->needs_shipping())
                continue;

            $skip_product = apply_filters('wf_shipping_skip_product', false, $values, $package['contents']);
            if ($skip_product) {
                continue;
            }

            if ($values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight) {

                $dimensions = array(wc_get_dimension($values['data']->length, 'in'), wc_get_dimension($values['data']->height, 'in'), wc_get_dimension($values['data']->width, 'in'));
            } else {
                $this->debug(sprintf(__('Product #%d is missing dimensions! Using 1x1x1.', 'wf-usps-woocommerce-shipping'), $item_id), 'error');

                $dimensions = array(1, 1, 1);
            }

            for ($i = 0; $i < $values['quantity']; $i ++) {
                $boxpack->add_item(
                        wc_get_dimension($dimensions[2], 'in'), wc_get_dimension($dimensions[1], 'in'), wc_get_dimension($dimensions[0], 'in'), wc_get_weight($values['data']->get_weight(), 'lbs'), $values['data']->get_price(), $item_id //WF: Adding Item Id and Quantity as meta.
                );
            }
        }

        // Pack it
        $boxpack->pack();

        // Get packages
        $flat_packages = $boxpack->get_packages();

        if ($flat_packages) {
            foreach ($flat_packages as $flat_package) {

                $flat_package_id = explode(':', $flat_package->id);
                $carrier = isset($flat_package_id[0]) ? $flat_package_id[0] : '';
                $box_code = isset($flat_package_id[1]) ? $flat_package_id[1] : '';

                if (isset($this->flat_rate_boxes[$carrier][$box_code])) {
                    $this->debug('Packed ' . $flat_package->id . ' - ' . $this->flat_rate_boxes[$carrier][$box_code]['name']);
                    $box_cost = isset($this->flat_rate_pricing[$carrier][$box_code]['commercial']) ? $this->flat_rate_pricing[$carrier][$box_code]['commercial'] : '';
                    if (empty($box_cost)) {
                        $request = $this->get_flate_rate_requests($flat_package, $package);
                        $flatrate_costs = $this->process_flat_rate_result($this->get_result($request, $box_code));
                        
                        if (is_array($flatrate_costs)) {
                            foreach ($flatrate_costs as $flatrate_cost) {
                                $temp_cost = isset($flatrate_cost['cost']) && $flatrate_cost['carrier'] == $carrier ? $flatrate_cost['cost'] : false;
                                if ((float) $temp_cost > 0) {
                                    if (empty($box_cost)) {
                                        $box_cost = $temp_cost;
                                    } else {
                                        $box_cost = (float) $temp_cost < $box_cost ? $temp_cost : $box_cost; //Take cheapest coat among multiple options.
                                    }
                                }
                            }
                        }
                    }
                    if (!$box_cost) {
                        continue;
                    }
                    // Fees
                    if (!empty($this->flat_rate_fee)) {
                        $sym = substr($this->flat_rate_fee, 0, 1);
                        $fee = $sym == '-' ? substr($this->flat_rate_fee, 1) : $this->flat_rate_fee;

                        if (strstr($fee, '%')) {
                            $fee = str_replace('%', '', $fee);

                            if ($sym == '-')
                                $box_cost = $box_cost - ( $box_cost * ( floatval($fee) / 100 ) );
                            else
                                $box_cost = $box_cost + ( $box_cost * ( floatval($fee) / 100 ) );
                        } else {
                            if ($sym == '-')
                                $box_cost = $box_cost - $fee;
                            else
                                $box_cost += $fee;
                        }

                        if ($box_cost < 0)
                            $box_cost = 0;
                    }

                    $cost += $box_cost;
                }else {
                    return; // No match found
                }
            }

            $label = 'EasyPost Flat Rate';
            
            if( $cost != 0 )
            return array(
                'id' => $this->id . ':' . $carrier . ':' . $box_code,
                'label' => $label,
                'cost' => $cost
            );
        }
    }

    private function get_flate_rate_requests($box_package, $package) {

        $dimensions = array($box_package->length, $box_package->width, $box_package->height);
        $rectangular_shaped = max($dimensions) > 12 ? 'true' : 'false';
        $weight = number_format($box_package->weight * 16, 2, '.', '');

        $domestic = in_array($package['destination']['country'], $this->domestic) ? true : false;

        $dest_postal_code = !empty($package['destination']['postcode']) ? $package['destination']['postcode'] : (isset($package['destination']['zip']) ? $package['destination']['zip'] : '');
        if ($domestic) {
            $request['Rate'] = array(
                'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                'ToZIPCode' => strtoupper(substr($dest_postal_code, 0, 5)),
                'ToCountry' => $package['destination']['country'],
                'Amount' => $box_package->value,
                'WeightOz' => number_format($weight, 0, '.', ''),
                'PackageType' => 'Package',
                'Length' => $dimensions[2],
                'Width' => $dimensions[1],
                'Height' => $dimensions[0],
                'Value' => $box_package->value,
                'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                'InsuredValue' => $box_package->value,
                'RectangularShaped' => $rectangular_shaped
            );
        } else {
            $request['Rate'] = array(
                'FromZIPCode' => str_replace(' ', '', strtoupper($this->settings['zip'])),
                'ToZIPCode' => strtoupper($dest_postal_code),
                'ToCountry' => $package['destination']['country'],
                'Amount' => $box_package->value,
                'WeightOz' => number_format($weight, 0, '.', ''),
                'PackageType' => 'Package',
                'Length' => $dimensions[2],
                'Width' => $dimensions[1],
                'Height' => $dimensions[0],
                'Value' => $box_package->value,
                'ShipDate' => date("Y-m-d", ( current_time('timestamp') + (60 * 60 * 24))),
                'InsuredValue' => $box_package->value,
                'RectangularShaped' => $rectangular_shaped
            );

            $request['unpacked'] = (isset($box_package->unpacked) && !empty($box_package->unpacked)) ? $box_package->unpacked : array();
            $request['packed'] = (isset($box_package->packed) && !empty($box_package->packed)) ? $box_package->packed : array();
        }

        $request_ele = array();
        $request_ele['request'] = $request;
        $request_ele['quantity'] = 1;

        return $request_ele;
    }

    private function process_flat_rate_result($response_ele) {
        if (empty($response_ele))
            return false;
        $response_obj = $response_ele['response'];
        if (isset($response_obj->rates) && !empty($response_obj->rates)) {
            foreach ($this->carrier as $carrier_name) {
                foreach ($response_obj->rates as $easypost_rate) {
                    if ($carrier_name == $easypost_rate->carrier) {
                        $service_type = (string) $easypost_rate->service;
                        $service_name = (string) ( isset($this->custom_services[$carrier_name][$service_type]['name']) && !empty($this->custom_services[$carrier_name][$service_type]['name']) ) ? $this->custom_services[$carrier_name][$service_type]['name'] : $this->services[$carrier_name]['services'][$service_type];
                        $total_amount = $response_ele['quantity'] * $easypost_rate->rate;
                        // Sort
                        if (isset($this->custom_services[$carrier_name][$service_type]['order'])) {
                            $sort = $this->custom_services[$carrier_name][$service_type]['order'];
                        } else {
                            $sort = 999;
                        }

                        if (isset($found_rates[$service_type])) {
                            $found_rates[$service_type]['cost'] = $found_rates[$service_type]['cost'] + $total_amount;
                        } else {
                            $found_rates[$service_type]['label'] = $service_name;
                            $found_rates[$service_type]['cost'] = $total_amount;
                            $found_rates[$service_type]['carrier'] = $easypost_rate->carrier;
                            $found_rates[$service_type]['sort'] = $sort;
                        }
                    }
                }
            }
        } else {
            $this->debug(__('Easypost.com - Unknown error while processing flat Rates.', 'wf-easypost'));
            return;
        }
        return $found_rates;
    }

    public function selected_flat_rate_box_format_data($selected_flat_rate_boxes) {
        $boxes = array();
        if (is_array($selected_flat_rate_boxes)) {
            foreach ($selected_flat_rate_boxes as $selected_flat_rate_box) {
                list($carrier, $box_code) = explode(':', $selected_flat_rate_box);
                $boxes[$carrier][] = $box_code;
            }
        }
        return $boxes;
    }

    private function wf_load_product($product) {
        if (!$product) {
            return false;
        }
        if (!class_exists('wf_product')) {
            include_once('class-wf-legacy.php');
        }
        if ($product instanceof wf_product) {
            return $product;
        }
        return ( WC()->version < '2.7.0' ) ? $product : new wf_product($product);
    }

}
