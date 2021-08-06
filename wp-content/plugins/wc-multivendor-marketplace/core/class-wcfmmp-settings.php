<?php
/**
 * WCFMmp plugin core
 *
 * WCfMmp Settings
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Settings {

	public function __construct() {
		global $WCFM;
		
		// Marketplace Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_marketplace_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_marketplace_settings_update' ), 14 );
		
		
		// Commission Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_commission_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_commission_settings_update' ), 14 );
		
		// Withdrawal Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_withdrawal_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_withdrawal_settings_update' ), 14 );
		
		// Shipping Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_shipping_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_shipping_settings_update' ), 14 );
		
		// Refund Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_refund_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_refund_settings_update' ), 14 );
		
		// Review Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_review_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_review_settings_update' ), 14 );
		
		// Vendor Registration Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_vendor_registration_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_vendor_registration_settings_update' ), 14 );
		
		// Store Style Setting
		add_action( 'begin_wcfm_settings_form_style', array( &$this, 'wcfm_store_style_settings' ), 14 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_store_style_settings_update' ), 14 );
	}
	
	function wcfm_marketplace_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_marketplace_manage', true ) ) return; 
		
		$wcfm_marketplace_options = wcfm_get_option( 'wcfm_marketplace_options', array() );
    
		$wcfm_store_url           = isset( $wcfm_marketplace_options['wcfm_store_url'] ) ? $wcfm_marketplace_options['wcfm_store_url'] : 'store';
		$vendor_sold_by           = isset( $wcfm_marketplace_options['vendor_sold_by'] ) ? $wcfm_marketplace_options['vendor_sold_by'] : 'yes';
		$sold_by_label            = isset( $wcfm_marketplace_options['sold_by_label'] ) ? $wcfm_marketplace_options['sold_by_label'] : __('Store', 'wc-multivendor-marketplace');
		$vendor_sold_by_template  = isset( $wcfm_marketplace_options['vendor_sold_by_template'] ) ? $wcfm_marketplace_options['vendor_sold_by_template'] : 'advanced';
		$vendor_sold_by_position  = isset( $wcfm_marketplace_options['vendor_sold_by_position'] ) ? $wcfm_marketplace_options['vendor_sold_by_position'] : 'bellow_atc';
		$store_name_position      = isset( $wcfm_marketplace_options['store_name_position'] ) ? $wcfm_marketplace_options['store_name_position'] : 'on_banner';
		$store_list_sidebar       = isset( $wcfm_marketplace_options['store_list_sidebar'] ) ? $wcfm_marketplace_options['store_list_sidebar'] : 'yes';
		$store_sidebar            = isset( $wcfm_marketplace_options['store_sidebar'] ) ? $wcfm_marketplace_options['store_sidebar'] : 'yes';
		$store_sidebar_pos        = isset( $wcfm_marketplace_options['store_sidebar_pos'] ) ? $wcfm_marketplace_options['store_sidebar_pos'] : 'left';
		$store_related_products   =  isset( $wcfm_marketplace_options['store_related_products'] ) ? $wcfm_marketplace_options['store_related_products'] : 'default';
		$store_ppp                =  isset( $wcfm_marketplace_options['store_ppp'] ) ? $wcfm_marketplace_options['store_ppp'] : get_option('posts_per_page');
		$order_sync               = isset( $wcfm_marketplace_options['order_sync'] ) ? $wcfm_marketplace_options['order_sync'] : 'no';
		//$product_mulivendor       = isset( $wcfm_marketplace_options['product_mulivendor'] ) ? $wcfm_marketplace_options['product_mulivendor'] : 'yes';
		$wcfm_google_map_api      = isset( $wcfm_marketplace_options['wcfm_google_map_api'] ) ? $wcfm_marketplace_options['wcfm_google_map_api'] : '';
		
		$store_default_logo   = !empty( $wcfm_marketplace_options['store_default_logo'] ) ? $wcfm_marketplace_options['store_default_logo'] : $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png';
		$store_default_banner = !empty( $wcfm_marketplace_options['store_default_banner'] ) ? $wcfm_marketplace_options['store_default_banner'] : $WCFMmp->plugin_url . 'assets/images/default_banner.jpg';
		$store_list_default_banner = !empty( $wcfm_marketplace_options['store_list_default_banner'] ) ? $wcfm_marketplace_options['store_list_default_banner'] : $WCFMmp->plugin_url . 'assets/images/default_banner.jpg';
		$store_banner_width   = isset( $wcfm_marketplace_options['store_banner_width'] ) ? $wcfm_marketplace_options['store_banner_width'] : '1650';
		$store_banner_height  = isset( $wcfm_marketplace_options['store_banner_height'] ) ? $wcfm_marketplace_options['store_banner_height'] : '350';
		$store_banner_mwidth  = isset( $wcfm_marketplace_options['store_banner_mwidth'] ) ? $wcfm_marketplace_options['store_banner_mwidth'] : '520';
		$store_banner_mheight = isset( $wcfm_marketplace_options['store_banner_mheight'] ) ? $wcfm_marketplace_options['store_banner_mheight'] : '250';
		
		$disable_wcfm_store_setup = isset( $wcfm_marketplace_options['disable_wcfm_store_setup'] ) ? $wcfm_marketplace_options['disable_wcfm_store_setup'] : 'no';
		
		$enable_wcfm_geo_locate = isset( $wcfm_marketplace_options['enable_wcfm_geo_locate'] ) ? $wcfm_marketplace_options['enable_wcfm_geo_locate'] : 'no';
		
		$delete_data_on_uninstall = isset( $wcfm_marketplace_options['delete_data_on_uninstall'] ) ? $wcfm_marketplace_options['delete_data_on_uninstall'] : 'no';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_marketplace_head">
			<label class="wcfmfa fa-home"></label>
			<?php _e('Marketplace Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_marketplace_expander" class="wcfm-content">
			  <h2><?php _e('Marketplace Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://www.youtube.com/embed/ZKGD1PkdgYI' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_store', array(
					                                                                        "vendor_store_url" => array('label' => __('Vendor Store URL', 'wc-multivendor-marketplace') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'desc_class' => 'wcfm_page_options_desc', 'value' => $wcfm_store_url, 'desc' => sprintf( __( 'Define the seller store URL  (%s/[this-text]/[seller-name])', 'wc-multivendor-marketplace' ), get_site_url() ), 'custom_attributes' => array( 'required' => true )  ),
					                                                                        "vendor_sold_by" => array('label' => __('Visible Sold By', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $vendor_sold_by, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Uncheck this to disable Sold By display for products.', 'wc-multivendor-marketplace' ) ),
					                                                                        "sold_by_label" => array('label' => __('Sold By Label', 'wc-multivendor-marketplace'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $sold_by_label, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Sold By label along with store name under product archive pages.', 'wc-multivendor-marketplace' ) ),
					                                                                        "vendor_sold_by_template" => array('label' => __('Sold By Template', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'simple' => __( 'Simple', 'wc-multivendor-marketplace' ), 'advanced' => __( 'Advanced', 'wc-multivendor-marketplace' ), 'tab' => __( 'As Tab', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $vendor_sold_by_template, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Single product page Sold By template.', 'wc-multivendor-marketplace' ) ),
					                                                                        "sold_by_template_simple" => array( 'type' => 'html', 'class' => 'vendor_sold_by_type vendor_sold_by_type_simple', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '<img src="'.$WCFMmp->plugin_url.'assets/images/sold_by_simple.png" />', 'attributes' => array( 'style' => 'margin-left:35%;border: 1px dotted #ccc;margin-bottom:15px;' ) ),
					                                                                        "sold_by_template_advanced" => array( 'type' => 'html', 'class' => 'vendor_sold_by_type vendor_sold_by_type_advanced', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '<img src="'.$WCFMmp->plugin_url.'assets/images/sold_by_advanced.png" />', 'attributes' => array( 'style' => 'margin-left:35%;border: 1px dotted #ccc;margin-bottom:15px;' ) ),
					                                                                        "sold_by_template_tab" => array( 'type' => 'html', 'class' => 'vendor_sold_by_type vendor_sold_by_type_tab', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '<img src="'.$WCFMmp->plugin_url.'assets/images/sold_by_tab.png" />', 'attributes' => array( 'style' => 'margin-left:35%;border: 1px dotted #ccc;margin-bottom:15px;' ) ),
					                                                                        "vendor_sold_by_position" => array( 'label' => __('Sold By Position', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'bellow_price' => __( 'Below Price', 'wc-multivendor-marketplace' ), 'bellow_sc' => __( 'Below Short Description', 'wc-multivendor-marketplace' ), 'bellow_atc' => __( 'Below Add to Cart', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $vendor_sold_by_position, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Sold by display position at Single Product Page.', 'wc-multivendor-marketplace' ) ),
					                                                                        "store_name_position" => array( 'label' => __('Store Name Position', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'on_banner' => __( 'On Banner', 'wc-multivendor-marketplace' ), 'on_header' => __( 'At Header', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_name_position, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Store name position at Vendor Store Page.', 'wc-multivendor-marketplace' ) ),
					                                                                        
					                                                                        "store_list_sidebar" => array( 'label' => __('Store List Sidebar', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $store_list_sidebar, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Uncheck this to disable store list sidebar.', 'wc-multivendor-marketplace' ) ),
					                                                                        "store_sidebar" => array( 'label' => __('Store Sidebar', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $store_sidebar, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Uncheck this to disable vendor store sidebar.', 'wc-multivendor-marketplace' ) ),
					                                                                        "store_sidebar_pos" => array( 'label' => __('Store Sidebar Position', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'left' => __( 'At Left', 'wc-multivendor-marketplace' ), 'right' => __( 'At Right', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_sidebar_pos ),
					                                                                        "store_related_products" => array( 'label' => __('Store Related Products', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'default' => __( 'As per WC Default Rule', 'wc-multivendor-marketplace' ), 'store' => __( 'Only same Store Products', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_related_products, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Single product page related products rule.', 'wc-frontend-manager' ) ),
					                                                                        "store_ppp" => array( 'label' => __('Products per page', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_ppp, 'attributes' => array( 'min'=> 1, 'step' => 1 ), 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'No of products at Store per Page.', 'wc-frontend-manager' ) ),
					                                                                        
					                                                                        "order_sync" => array('label' => __('Order Sync', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $order_sync, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Enable this to sync WC main order status when vendors update their order status.', 'wc-multivendor-marketplace' ) ),
					                                                                        //"product_mulivendor" => array('label' => __('Product Multi-vendor', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $product_mulivendor, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Enable this to allow vendors to sell other vendor products, single product multiple seller.', 'wc-multivendor-marketplace' ) ),
																																									"wcfm_google_map_api" => array('label' => __('Google Map API Key', 'wc-multivendor-marketplace') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'desc_class' => 'wcfm_page_options_desc', 'value' => $wcfm_google_map_api, 'desc' => sprintf( __( '%sAPI Key%s is needed to display map on store page', 'wc-multivendor-marketplace' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/">', '</a>' ) ),
																																									
																																									"store_default_logo" => array('label' => __('Store Default Logo', 'wc-multivendor-marketplace') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele wcfm-logo-uploads', 'label_class' => 'wcfm_title', 'prwidth' => 75, 'value' => $store_default_logo ),
																																									"store_default_banner" => array('label' => __('Store Default Banner', 'wc-multivendor-marketplace') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele wcfm-banner-uploads', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $store_default_banner ),
																																									"store_list_default_banner" => array('label' => __('Store List Default Banner', 'wc-multivendor-marketplace') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele wcfm-banner-uploads', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $store_list_default_banner ),
																																									"store_banner_dimension" => array( 'label' => __('Banner Dimension(s)', 'wc-multivendor-marketplace'), 'type' => 'html', 'label_class' => 'wcfm_title wcfm_full_ele_title', 'value' => '' ),
					                                                                        "store_banner_width" => array( 'label' => __('Width', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'value' => $store_banner_width, 'hints' => __( 'Store banner preferred width in pixels.', 'wc-multivendor-marketplace' ), 'attributes' => array( 'min' => 1, 'step' => 1 ) ),
					                                                                        "store_banner_height" => array( 'label' => __('Height', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'value' => $store_banner_height, 'hints' => __( 'Store banner preferred height in pixels.', 'wc-multivendor-marketplace' ), 'attributes' => array( 'min' => 1, 'step' => 1 ) ),
					                                                                        "store_banner_mwidth" => array( 'label' => __('Width (Mob)', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'value' => $store_banner_mwidth, 'hints' => __( 'Store banner preferred width for mobile in pixels.', 'wc-multivendor-marketplace' ), 'attributes' => array( 'min' => 1, 'step' => 1 ) ),
					                                                                        "store_banner_mheight" => array( 'label' => __('Height (Mob)', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'value' => $store_banner_mheight, 'hints' => __( 'Store banner preferred heightfor mobile in pixels.', 'wc-multivendor-marketplace' ), 'attributes' => array( 'min' => 1, 'step' => 1 ) ),
					                                                                        "store_banner_dimension2" => array( 'type' => 'html', 'value' => '<div class="wcfm_clearfix"></div>' ),
					                                                                        
					                                                                        "disable_wcfm_store_setup" => array('label' => __('Disable Store Setup Widget', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $disable_wcfm_store_setup ),
					                                                                        
					                                                                        "enable_wcfm_geo_locate"  => array('label' => __('Enable GEO Locate', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $enable_wcfm_geo_locate, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Check this to enable store list auto-filter by user\'s location.', 'wc-multivendor-marketplace' ) ),
																																									
					                                                                        "delete_data_on_uninstall" => array('label' => __('On Uninstall', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $delete_data_on_uninstall, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Delete all marketplace data on uninstall. Be careful, there is no way to retrieve those data if once deleted!', 'wc-multivendor-marketplace' ) ),
																																									) ) );
			  ?>
			  
			  <div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h2><?php _e( 'Store List Page', 'wc-multivendor-marketplace' ); ?></h2></div>
				<?php wcfm_video_tutorial( 'https://docs.wclovers.com/store-list/' ); ?>
				<div class="wcfm_clearfix"></div>
				<div class="store_address store_address_wrap">
				  <p>    
				    <?php
							printf( __( "You just have to create a page using short code – %swcfm_stores%s
							You may specify “per_row” attribute to specify number of store in one row, by default it’s “2”.%s
							Also specify “per_page” attribute to set how many stores you want to show in a page. Default value is 10.%s
							You may also specify “excludes” attribute (comma separated store ids) to excludes some store from list.", 'wc-multivendor-marketplace' ),
							'<b>', '</b><br/>', '<br/>', '<br/>');
					  ?>
					 </p>
					 <img src="https://docs.wclovers.com/wp-content/uploads/2019/03/wcfmmp-storelist.png" />
				</div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_marketplace_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_marketplace_manage', true ) ) return; 
		
		$wcfm_marketplace_options = wcfm_get_option( 'wcfm_marketplace_options', array() );
		
		if( isset( $wcfm_settings_form['vendor_store_url'] ) ) {
			$wcfm_marketplace_options['wcfm_store_url'] = sanitize_title( $wcfm_settings_form['vendor_store_url'] );
			update_option( 'wcfm_store_url', sanitize_title( $wcfm_settings_form['vendor_store_url'] ) );
		}
		
		if( isset( $wcfm_settings_form['vendor_sold_by'] ) ) {
			$wcfm_marketplace_options['vendor_sold_by'] = 'yes';
		} else {
			$wcfm_marketplace_options['vendor_sold_by'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['sold_by_label'] ) ) {
			$wcfm_marketplace_options['sold_by_label'] = $wcfm_settings_form['sold_by_label'];
		}
		
		if( isset( $wcfm_settings_form['vendor_sold_by_template'] ) ) {
			$wcfm_marketplace_options['vendor_sold_by_template'] = $wcfm_settings_form['vendor_sold_by_template'];
		}
		
		if( isset( $wcfm_settings_form['vendor_sold_by_position'] ) ) {
			$wcfm_marketplace_options['vendor_sold_by_position'] = $wcfm_settings_form['vendor_sold_by_position'];
		}
		
		if( isset( $wcfm_settings_form['store_name_position'] ) ) {
			$wcfm_marketplace_options['store_name_position'] = $wcfm_settings_form['store_name_position'];
		}
		
		if( isset( $wcfm_settings_form['store_list_sidebar'] ) ) {
			$wcfm_marketplace_options['store_list_sidebar'] = 'yes';
		} else {
			$wcfm_marketplace_options['store_list_sidebar'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['store_sidebar'] ) ) {
			$wcfm_marketplace_options['store_sidebar'] = 'yes';
		} else {
			$wcfm_marketplace_options['store_sidebar'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['store_related_products'] ) ) {
			$wcfm_marketplace_options['store_related_products'] = $wcfm_settings_form['store_related_products'];
		}
		
		if( isset( $wcfm_settings_form['store_sidebar_pos'] ) ) {
			$wcfm_marketplace_options['store_sidebar_pos'] = $wcfm_settings_form['store_sidebar_pos'];
		}
		
		if( isset( $wcfm_settings_form['store_default_logo'] ) ) {
			$wcfm_marketplace_options['store_default_logo'] = $wcfm_settings_form['store_default_logo'];
		}
		
		if( isset( $wcfm_settings_form['store_default_banner'] ) ) {
			$wcfm_marketplace_options['store_default_banner'] = $wcfm_settings_form['store_default_banner'];
		}
		
		if( isset( $wcfm_settings_form['store_list_default_banner'] ) ) {
			$wcfm_marketplace_options['store_list_default_banner'] = $wcfm_settings_form['store_list_default_banner'];
		}
		
		if( isset( $wcfm_settings_form['store_banner_width'] ) ) {
			$wcfm_marketplace_options['store_banner_width'] = $wcfm_settings_form['store_banner_width'];
		}
		
		if( isset( $wcfm_settings_form['store_banner_height'] ) ) {
			$wcfm_marketplace_options['store_banner_height'] = $wcfm_settings_form['store_banner_height'];
		}
		
		if( isset( $wcfm_settings_form['store_banner_mwidth'] ) ) {
			$wcfm_marketplace_options['store_banner_mwidth'] = $wcfm_settings_form['store_banner_mwidth'];
		}
		
		if( isset( $wcfm_settings_form['store_banner_mheight'] ) ) {
			$wcfm_marketplace_options['store_banner_mheight'] = $wcfm_settings_form['store_banner_mheight'];
		}
		
		if( isset( $wcfm_settings_form['store_ppp'] ) ) {
			$wcfm_marketplace_options['store_ppp'] = $wcfm_settings_form['store_ppp'];
		}
		
		//if( isset( $wcfm_settings_form['product_mulivendor'] ) ) {
			//$wcfm_marketplace_options['product_mulivendor'] = 'yes';
		//} else {
			$wcfm_marketplace_options['product_mulivendor'] = 'no';
		//}
		
		if( isset( $wcfm_settings_form['order_sync'] ) ) {
			$wcfm_marketplace_options['order_sync'] = 'yes';
		} else {
			$wcfm_marketplace_options['order_sync'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['wcfm_google_map_api'] ) ) {
			$wcfm_marketplace_options['wcfm_google_map_api'] = $wcfm_settings_form['wcfm_google_map_api'];
		}
		
		if( isset( $wcfm_settings_form['disable_wcfm_store_setup'] ) ) {
			$wcfm_marketplace_options['disable_wcfm_store_setup'] = 'yes';
		} else {
			$wcfm_marketplace_options['disable_wcfm_store_setup'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['enable_wcfm_geo_locate'] ) ) {
			$wcfm_marketplace_options['enable_wcfm_geo_locate'] = 'yes';
		} else {
			$wcfm_marketplace_options['enable_wcfm_geo_locate'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['delete_data_on_uninstall'] ) ) {
			$wcfm_marketplace_options['delete_data_on_uninstall'] = 'yes';
		} else {
			$wcfm_marketplace_options['delete_data_on_uninstall'] = 'no';
		}
		
		wcfm_update_option( 'wcfm_marketplace_options', $wcfm_marketplace_options );
	}
	
	function wcfm_commission_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_commission_manage', true ) ) return; 
		
		$wcfm_commission_options = get_option( 'wcfm_commission_options', array() );
		
		$wcfm_commission_types = get_wcfm_marketplace_commission_types();
		
		$vendor_commission_for         = isset( $wcfm_commission_options['commission_for'] ) ? $wcfm_commission_options['commission_for'] : 'vendor';
		$vendor_commission_mode        = isset( $wcfm_commission_options['commission_mode'] ) ? $wcfm_commission_options['commission_mode'] : 'percent';
		$vendor_commission_fixed       = isset( $wcfm_commission_options['commission_fixed'] ) ? $wcfm_commission_options['commission_fixed'] : '';
		$vendor_commission_percent     = isset( $wcfm_commission_options['commission_percent'] ) ? $wcfm_commission_options['commission_percent'] : '90';
		$vendor_commission_by_sales    = isset( $wcfm_commission_options['commission_by_sales'] ) ? $wcfm_commission_options['commission_by_sales'] : array();
		$vendor_commission_by_products = isset( $wcfm_commission_options['commission_by_products'] ) ? $wcfm_commission_options['commission_by_products'] : array();
		$vendor_commission_by_quantity = isset( $wcfm_commission_options['commission_by_quantity'] ) ? $wcfm_commission_options['commission_by_quantity'] : array();
		$vendor_get_shipping           = isset( $wcfm_commission_options['get_shipping'] ) ? $wcfm_commission_options['get_shipping'] : 'yes';
		$vendor_get_tax                = isset( $wcfm_commission_options['get_tax'] ) ? $wcfm_commission_options['get_tax'] : 'yes';
		$vendor_coupon_deduct          = isset( $wcfm_commission_options['coupon_deduct'] ) ? $wcfm_commission_options['coupon_deduct'] : 'yes';
		$admin_coupon_deduct           = isset( $wcfm_commission_options['admin_coupon_deduct'] ) ? $wcfm_commission_options['admin_coupon_deduct'] : 'yes';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_commission_head">
			<label class="wcfmfa fa-percent"></label>
			<?php _e('Commission Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_commission_expander" class="wcfm-content">
			  <h2><?php _e('Marketplace Commission Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-commission/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_commission', array(
																																									"vendor_commission_for" => array('label' => __('Commission For', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'vendor' => __( 'Vendor', 'wc-multivendor-marketplace' ), 'admin' => __( 'Admin', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_commission_for ),
					                                                                        "vendor_commission_mode" => array('label' => __('Commission Mode', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => $wcfm_commission_types, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_commission_mode ),
					                                                                        "vendor_commission_percent" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'value' => $vendor_commission_percent, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'value' => $vendor_commission_fixed, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_by_sales" => array('label' => __('Commission By Sales Rule(s)', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_sales', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_sales', 'desc_class' => 'commission_mode_field commission_mode_by_sales instructions', 'value' => $vendor_commission_by_sales, 'desc' => sprintf( __( 'Commission rules depending upon vendors total sales. e.g 50&#37; commission when sales < %s1000, 75&#37; commission when sales > %s1000 but < %s2000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"sales" => array('label' => __('Sales', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input ', 'label_class' => 'wcfm_title wcfm_non_negative_input wcfm_ele', 'attributes' => array( 'min' => '1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater' => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed' => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
					                                                                        "vendor_commission_by_products" => array('label' => __('Commission By Product Price', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_products', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_products', 'desc_class' => 'commission_mode_field commission_mode_by_products instructions', 'value' => $vendor_commission_by_products, 'desc' => sprintf( __( 'Commission rules depending upon product price. e.g 80&#37; commission when product cost < %s1000, %s100 fixed commission when product cost > %s1000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"cost" => array('label' => __('Product Cost', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
					                                                                        "vendor_commission_by_quantity" => array('label' => __('Commission By Purchase Quantity', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_quantity', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_quantity', 'desc_class' => 'commission_mode_field commission_mode_by_quantity instructions', 'value' => $vendor_commission_by_quantity, 'desc' => __( 'Commission rules depending upon purchased product quantity. e.g 80&#37; commission when purchase quantity 2, 80&#37; commission when purchase quantity > 2 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ),  'options' => array( 
					                                                                        																			"quantity" => array('label' => __('Purchase Quantity', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '1', 'step' => '1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
																																									"vendor_get_shipping" => array('label' => __('Shipping cost goes to vendor?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $vendor_get_shipping ),
																																									"vendor_get_tax" => array('label' => __('Tax goes to vendor?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $vendor_get_tax ),
																																									"vendor_coupon_deduct" => array('label' => __('Commission after consider Vendor Coupon?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $vendor_coupon_deduct, 'hints' => __( 'Generate vendor commission after deduct Vendor Coupon discounts.', 'wc-multivendor-marketplace' ) ),
																																									"admin_coupon_deduct" => array('label' => __('Commission after consider Admin Coupon?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $admin_coupon_deduct, 'hints' => __( 'Generate vendor commission after deduct Admin Coupon discounts.', 'wc-multivendor-marketplace' ) ),
																																									) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_commission_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_commission_manage', true ) ) return; 
		
		$wcfm_commission_options = get_option( 'wcfm_commission_options', array() );
		
		if( isset( $wcfm_settings_form['vendor_commission_for'] ) ) {
			$wcfm_commission_options['commission_for'] = $wcfm_settings_form['vendor_commission_for'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_mode'] ) ) {
			$wcfm_commission_options['commission_mode'] = $wcfm_settings_form['vendor_commission_mode'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_fixed'] ) ) {
			$wcfm_commission_options['commission_fixed'] = $wcfm_settings_form['vendor_commission_fixed'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_percent'] ) ) {
			$wcfm_commission_options['commission_percent'] = $wcfm_settings_form['vendor_commission_percent'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_by_sales'] ) ) {
			$wcfm_commission_options['commission_by_sales'] = $wcfm_settings_form['vendor_commission_by_sales'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_by_products'] ) ) {
			$wcfm_commission_options['commission_by_products'] = $wcfm_settings_form['vendor_commission_by_products'];
		}
		
		if( isset( $wcfm_settings_form['vendor_commission_by_quantity'] ) ) {
			$wcfm_commission_options['commission_by_quantity'] = $wcfm_settings_form['vendor_commission_by_quantity'];
		}
		
		if( isset( $wcfm_settings_form['vendor_get_shipping'] ) ) {
			$wcfm_commission_options['get_shipping'] = 'yes';
		} else {
			$wcfm_commission_options['get_shipping'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['vendor_get_tax'] ) ) {
			$wcfm_commission_options['get_tax'] = 'yes';
		} else {
			$wcfm_commission_options['get_tax'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['vendor_coupon_deduct'] ) ) {
			$wcfm_commission_options['coupon_deduct'] = 'yes';
		} else {
			$wcfm_commission_options['coupon_deduct'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['admin_coupon_deduct'] ) ) {
			$wcfm_commission_options['admin_coupon_deduct'] = 'yes';
		} else {
			$wcfm_commission_options['admin_coupon_deduct'] = 'no';
		}
		
		
		update_option( 'wcfm_commission_options', $wcfm_commission_options );
	}
	
	function wcfm_withdrawal_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_withdrawal_manage', true ) ) return; 
		
		$wcfm_withdrawal_options = get_option( 'wcfm_withdrawal_options', array() );
		
		$wcfm_marketplace_withdrwal_payment_methods = get_wcfm_marketplace_withdrwal_payment_methods();
		$wcfm_marketplace_withdrawal_order_status   = get_wcfm_marketplace_withdrwal_order_status();
		$wcfm_marketplace_disallow_order_payment_methods = get_wcfm_marketplace_disallow_order_payment_methods();
		
		$request_auto_approve                 = isset( $wcfm_withdrawal_options['request_auto_approve'] ) ? $wcfm_withdrawal_options['request_auto_approve'] : 'no';
		
		$generate_auto_withdrawal             = isset( $wcfm_withdrawal_options['generate_auto_withdrawal'] ) ? $wcfm_withdrawal_options['generate_auto_withdrawal'] : 'no';
		$auto_withdrawal_status               = isset( $wcfm_withdrawal_options['auto_withdrawal_status'] ) ? $wcfm_withdrawal_options['auto_withdrawal_status'] : 'wc-processing';
		
		$order_status                         = isset( $wcfm_withdrawal_options['order_status'] ) ? $wcfm_withdrawal_options['order_status'] : array( 'wc-completed', 'wc-processing' );
		
		$withdrawal_schedule                  = isset( $wcfm_withdrawal_options['withdrawal_schedule'] ) ? $wcfm_withdrawal_options['withdrawal_schedule'] : 'week';
		
		$withdrawal_limit                     = isset( $wcfm_withdrawal_options['withdrawal_limit'] ) ? $wcfm_withdrawal_options['withdrawal_limit'] : '';
		$withdrawal_thresold                  = isset( $wcfm_withdrawal_options['withdrawal_thresold'] ) ? $wcfm_withdrawal_options['withdrawal_thresold'] : '';
		
		if( isset( $wcfm_withdrawal_options['withdrawal_mode'] ) ) {
			$withdrawal_mode                    = isset( $wcfm_withdrawal_options['withdrawal_mode'] ) ? $wcfm_withdrawal_options['withdrawal_mode'] : '';
		} elseif( $generate_auto_withdrawal == 'yes' ) {
			$withdrawal_mode                      = 'by_order_status';
		} else {
			$withdrawal_mode                      = 'by_manual';
		}
		
		$payment_methods                      = isset( $wcfm_withdrawal_options['payment_methods'] ) ? $wcfm_withdrawal_options['payment_methods'] : array( 'paypal', 'bank_transfer' );
		$withdrawal_test_mode                 = isset( $wcfm_withdrawal_options['test_mode'] ) ? 'yes' : 'no';
		
		$withdrawal_paypal_client_id          = isset( $wcfm_withdrawal_options['paypal_client_id'] ) ? $wcfm_withdrawal_options['paypal_client_id'] : '';
		$withdrawal_paypal_secret_key         = isset( $wcfm_withdrawal_options['paypal_secret_key'] ) ? $wcfm_withdrawal_options['paypal_secret_key'] : '';
		$withdrawal_stripe_client_id          = isset( $wcfm_withdrawal_options['stripe_client_id'] ) ? $wcfm_withdrawal_options['stripe_client_id'] : '';
		$withdrawal_stripe_published_key      = isset( $wcfm_withdrawal_options['stripe_published_key'] ) ? $wcfm_withdrawal_options['stripe_published_key'] : '';
		$withdrawal_stripe_secret_key         = isset( $wcfm_withdrawal_options['stripe_secret_key'] ) ? $wcfm_withdrawal_options['stripe_secret_key'] : '';
		
		$withdrawal_paypal_test_client_id     = isset( $wcfm_withdrawal_options['paypal_test_client_id'] ) ? $wcfm_withdrawal_options['paypal_test_client_id'] : '';
		$withdrawal_paypal_test_secret_key    = isset( $wcfm_withdrawal_options['paypal_test_secret_key'] ) ? $wcfm_withdrawal_options['paypal_test_secret_key'] : '';
		$withdrawal_stripe_test_client_id     = isset( $wcfm_withdrawal_options['stripe_test_client_id'] ) ? $wcfm_withdrawal_options['stripe_test_client_id'] : '';
		$withdrawal_stripe_test_published_key = isset( $wcfm_withdrawal_options['stripe_test_published_key'] ) ? $wcfm_withdrawal_options['stripe_test_published_key'] : '';
		$withdrawal_stripe_test_secret_key    = isset( $wcfm_withdrawal_options['stripe_test_secret_key'] ) ? $wcfm_withdrawal_options['stripe_test_secret_key'] : '';
		
		$withdrawal_stripe_split_pay_mode     = isset( $wcfm_withdrawal_options['stripe_split_pay_mode'] ) ? $wcfm_withdrawal_options['stripe_split_pay_mode'] : 'direct_charges';
		
		
		$withdrawal_charge_type               = isset( $wcfm_withdrawal_options['withdrawal_charge_type'] ) ? $wcfm_withdrawal_options['withdrawal_charge_type'] : 'no';
		
		$withdrawal_charge                    = isset( $wcfm_withdrawal_options['withdrawal_charge'] ) ? $wcfm_withdrawal_options['withdrawal_charge'] : array();
		$withdrawal_charge_paypal             = isset( $withdrawal_charge['paypal'] ) ? $withdrawal_charge['paypal'] : array();
		$withdrawal_charge_stripe             = isset( $withdrawal_charge['stripe'] ) ? $withdrawal_charge['stripe'] : array();
		$withdrawal_charge_skrill             = isset( $withdrawal_charge['skrill'] ) ? $withdrawal_charge['skrill'] : array();
		$withdrawal_charge_bank_transfer      = isset( $withdrawal_charge['bank_transfer'] ) ? $withdrawal_charge['bank_transfer'] : array();
		
		$disallow_order_payment_methods       = get_wcfm_marketplace_disallow_active_order_payment_methods(); //isset( $wcfm_withdrawal_options['disallow_order_payment_methods'] ) ? $wcfm_withdrawal_options['disallow_order_payment_methods'] : array();
		$withdrawal_reverse                   = isset( $wcfm_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : 'no';
		$withdrawal_reverse_limit             = isset( $wcfm_withdrawal_options['withdrawal_reverse_limit'] ) ? $wcfm_withdrawal_options['withdrawal_reverse_limit'] : '';
		
		if( !empty( $disallow_order_payment_methods ) ) $disallow_order_payment_methods = array_keys( $disallow_order_payment_methods );
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_withdrawal_head">
			<label class="wcfmfa" style="font-size:18px;"><?php echo get_woocommerce_currency_symbol(); ?></label>
			<?php _e('Withdrawal Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_withdrawal_expander" class="wcfm-content">
			  <h2><?php _e('Marketplace Withdrawal Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-withdrawal/' ); ?>
				<div class="wcfm_clearfix"></div>
				<div class="store_address">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal', array(
																																										"withdrawal_request_auto_approve"     => array('label' => __('Request auto-approve?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_withdrawal_options[request_auto_approve]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $request_auto_approve, 'desc_class' => 'instructions', 'desc' => __( 'Check this to automatically disburse payments to vendors on request, no admin approval required. Auto disbursement only works for auto-payment gateways, e.g. PayPal, Stripe etc. Bank Transfer or other non-autopay mode always requires approval, as these are manual transactions.', 'wc-multivendor-marketplace' ) ),
																																										
																																										"withdrawal_mode"   => array( 'label' => __( 'Withdrawal Mode', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[withdrawal_mode]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele withdrawal_mode', 'label_class' => 'wcfm_title withdrawal_mode', 'options' => apply_filters( 'wcfm_withdrawal_modes', array( 'by_manual' => __( 'Manual Withdrawal', 'wc-multivendor-marketplace' ), 'by_schedule' => __( 'Periodic Withdrawal', 'wc-multivendor-marketplace' ), 'by_order_status' => __( 'By Order Status', 'wc-multivendor-marketplace' ) ) ), 'value' => $withdrawal_mode  ),
																																										
																																										//"withdrawal_generate_auto_withdrawal" => array('label' => __('Generate auto-withdrawal?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_withdrawal_options[generate_auto_withdrawal]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $generate_auto_withdrawal, 'desc_class' => 'instructions', 'desc' => __( 'Check this to generate withdrawal request automatically when order status reach at certain status.', 'wc-multivendor-marketplace' ) ),
																																										"withdrawal_auto_withdrawal_status"   => array( 'label' => __( 'Order Status', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[auto_withdrawal_status]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele auto_withdrawal_order_status', 'label_class' => 'wcfm_title auto_withdrawal_order_status', 'options' => $wcfm_marketplace_withdrawal_order_status, 'value' => $auto_withdrawal_status, 'desc_class' => 'wcfm_page_options_desc auto_withdrawal_order_status', 'desc' => __( 'Order status for generate withdrawal request automatically.', 'wc-multivendor-marketplace' ),  ),
																																										
																																										"withdrawal_schedule"   => array( 'label' => __( 'Schedule Interval', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[withdrawal_schedule]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele schedule_withdrawal_threshold_ele', 'label_class' => 'wcfm_title schedule_withdrawal_threshold_ele', 'options' => apply_filters( 'wcfm_withdrawal_schedule_periods', array( 'day' => __( 'Every Day', 'wc-multivendor-marketplace' ), 'week' => __( 'Every 7 Days (Every Week - Monday)', 'wc-multivendor-marketplace' ), '2weeks' => __( 'Every 15 Days (Every 2 Weeks - Monday)', 'wc-multivendor-marketplace' ), 'month' => __( 'Every 30 Days (Every Month - 1st)', 'wc-multivendor-marketplace' ), '2months' => __( 'Every 60 Days (Every 2 Months - 1st)', 'wc-multivendor-marketplace' ), 'quarter' => __( 'Every 90 Days (Every 3 Months - 1st)', 'wc-multivendor-marketplace' ) ) ), 'value' => $withdrawal_schedule  ),
																																										
																																										"withdrawal_order_status"             => array( 'label' => __( 'Allowed Order Status for Withdrawal', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[order_status]', 'type' => 'checklist', 'wrapper_class' => 'manual_withdrawal_ele', 'class' => 'wcfm-checkbox wcfm_ele payment_options', 'label_class' => 'wcfm_title wcfm_full_title manual_withdrawal_ele', 'options' => $wcfm_marketplace_withdrawal_order_status, 'value' => $order_status, 'desc_class' => 'instructions manual_withdrawal_ele', 'desc' => __( 'Allowed order statuses for which vendor may request for withdrawal.', 'wc-multivendor-marketplace' )  ),
																																										), $wcfm_withdrawal_options ) );
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal_rules', array(
																																										"withdrawal_limit" => array('label' => __('Minimum Withdraw Limit', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[withdrawal_limit]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdrawal_threshold_ele', 'label_class' => 'wcfm_title withdrawal_threshold_ele', 'desc_class'=> 'wcfm_page_options_desc withdrawal_threshold_ele', 'value' => $withdrawal_limit, 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'desc' => __( 'Minimum balance required to make a withdraw request. Leave blank to set no minimum limits.', 'wc-multivendor-marketplace') ),
																																										"withdrawal_thresold" => array('label' => __('Withdraw Threshold', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[withdrawal_thresold]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdrawal_threshold_ele', 'label_class' => 'wcfm_title wcfm_ele withdrawal_threshold_ele', 'value' => $withdrawal_thresold, 'desc_class' => 'wcfm_page_options_desc withdrawal_threshold_ele', 'attributes' => array( 'min' => '1', 'step' => '1'), 'desc' => __('Withdraw Threshold Days, (Make order matured to make a withdraw request). Leave empty to inactive this option.', 'wc-multivendor-marketplace') ),
																																										), $wcfm_withdrawal_options ) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div><br/>
				<h2><?php _e('Payment Setup', 'wc-multivendor-marketplace'); ?></h2>
				<div class="wcfm_clearfix"></div>
				<div class="store_address">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal_payment', array(
																																										"withdrawal_payment_methods" => array( 'label' => __( 'Withdraw Payment Methods', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[payment_methods]', 'type' => 'checklist', 'class' => 'wcfm-checkbox wcfm_ele payment_options', 'label_class' => 'wcfm_title wcfm_full_title', 'options' => $wcfm_marketplace_withdrwal_payment_methods, 'value' => $payment_methods  ),
																																										"withdrawal_setting_break_1" => array( 'type' => 'html', 'value' => '<div style="height: 15px;"></div>' ),
																																										"withdrawal_stripe_split_pay_mode" => array('label' => __('Stripe Split Pay Mode', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_split_pay_mode]', 'type' => 'select', 'options' => array( 'direct_charges' => __( 'Direct Charges',  'wc-multivendor-marketplace' ), 'destination_charges' => __( 'Destination Charges',  'wc-multivendor-marketplace' ), 'transfers_charges' => __( 'Transfer Charges',  'wc-multivendor-marketplace' )  ), 'class' => 'wcfm-select wcfm_ele withdrawal_mode withdrawal_mode_stripe_split', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe_split', 'value' => $withdrawal_stripe_split_pay_mode, 'desc_class' => 'withdrawal_mode withdrawal_mode_stripe_split wcfm_page_options_desc', 'desc' => __( 'Set your preferred Stripe Split pay mode.', 'wc-multivendor-marketplace' ) ),
																																										"withdrawal_test_mode" => array('label' => __('Enable Test Mode', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_withdrawal_options[test_mode]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $withdrawal_test_mode ),
																																										), $wcfm_withdrawal_options ) );
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal_payment_keys', array(
																																										"withdrawal_paypal_client_id" => array('label' => __('PayPal Client ID', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[paypal_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_paypal withdrawal_mode_live', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_paypal withdrawal_mode_live', 'value' => $withdrawal_paypal_client_id ),
																																										"withdrawal_paypal_secret_key" => array('label' => __('PayPal Secret Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[paypal_secret_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_paypal withdrawal_mode_live', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_paypal withdrawal_mode_live', 'value' => $withdrawal_paypal_secret_key ),
																																										"withdrawal_stripe_client_id" => array('label' => __('Stripe Client ID', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'value' => $withdrawal_stripe_client_id, 'desc_class' => 'withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live wcfm_page_options_desc', 'desc' => sprintf( __( 'Set redirect URL: %s', 'wc-multivendor-marketplace' ), get_wcfm_settings_url() ) ),
																																										"withdrawal_stripe_published_key" => array('label' => __('Stripe Publish Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_published_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'value' => $withdrawal_stripe_published_key ),
																																										"withdrawal_stripe_secret_key" => array('label' => __('Stripe Secret Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_secret_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_live', 'value' => $withdrawal_stripe_secret_key ),
																																									 ), $wcfm_withdrawal_options ) );
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal_payment_test_keys', array(
																																										"withdrawal_paypal_test_client_id" => array('label' => __('PayPal Client ID', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[paypal_test_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_paypal withdrawal_mode_test', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_paypal withdrawal_mode_test', 'value' => $withdrawal_paypal_test_client_id ),
																																										"withdrawal_paypal_test_secret_key" => array('label' => __('PayPal Secret Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[paypal_test_secret_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_paypal withdrawal_mode_test', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_paypal withdrawal_mode_test', 'value' => $withdrawal_paypal_test_secret_key ),
																																										"withdrawal_stripe_test_client_id" => array('label' => __('Stripe Client ID', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_test_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'value' => $withdrawal_stripe_test_client_id, 'desc_class' => 'withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test wcfm_page_options_desc', 'desc' => sprintf( __( 'Set redirect URL: %s', 'wc-multivendor-marketplace' ), get_wcfm_settings_url() ) ),
																																										"withdrawal_stripe_test_published_key" => array('label' => __('Stripe Publish Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_test_published_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'value' => $withdrawal_stripe_test_published_key ),
																																										"withdrawal_stripe_test_secret_key" => array('label' => __('Stripe Secret Key', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[stripe_test_secret_key]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_stripe withdrawal_mode_stripe_split withdrawal_mode_test', 'value' => $withdrawal_stripe_test_secret_key ),
																																										), $wcfm_withdrawal_options ) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div><br/>
				<h2><?php _e('Withdrawal Charges', 'wc-multivendor-marketplace'); ?></h2>
				<div class="wcfm_clearfix"></div>
				<div class="store_address">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_withdrawal_charges', array(
																																										"withdrawal_charge_type" => array('label' => __( 'Charge Type', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[withdrawal_charge_type]', 'type' => 'select', 'options' => array( 'no' => __( 'No Charge', 'wc-multivendor-marketplace' ), 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'desc_class' => 'wcfm_page_options_desc', 'value' => $withdrawal_charge_type , 'desc' => __('Charges applicable for each withdarwal.', 'wc-multivendor-marketplace') ),
																																										"withdrawal_setting_break_4" => array( 'type' => 'html', 'value' => '<div style="height: 15px;"></div>' ),
																																										
																																										"withdrawal_charge_paypal" => array( 'label' => __('PayPal Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge][paypal]', 'class' => 'withdraw_charge_block withdraw_charge_paypal', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_paypal', 'value' => $withdrawal_charge_paypal, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																												"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input ', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																												) ),
																																										"withdrawal_charge_stripe" => array( 'label' => __('Stripe Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge][stripe]', 'class' => 'withdraw_charge_block withdraw_charge_stripe', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_stripe', 'value' => $withdrawal_charge_stripe, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																												"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input ', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																												) ),
																																										"withdrawal_charge_skrill" => array( 'label' => __('Skrill Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge][skrill]', 'class' => 'withdraw_charge_block withdraw_charge_skrill', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_skrill', 'value' => $withdrawal_charge_skrill, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																												"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input ', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																												) ),
																																										"withdrawal_charge_bank_transfer" => array( 'label' => __('Bank Transfer Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge][bank_transfer]', 'class' => 'withdraw_charge_block withdraw_charge_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_bank_transfer', 'value' => $withdrawal_charge_bank_transfer, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																												"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																												"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input ', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																												) ),
																																										), $wcfm_withdrawal_options, $withdrawal_charge ) );
					
					?>
				</div>
				
				<div class="wcfm_clearfix"></div><br/>
				<h2><?php _e('Marketplace Reverse Withdrawal Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-reverse-withdrawal/' ); ?>
				<div class="wcfm_clearfix"></div>
				<div class="store_address">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_reverse_withdrawal', array(
																																										"withdrawal_reverse" => array('label' => __('Reverse Withdrawal', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_withdrawal_options[withdrawal_reverse]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $withdrawal_reverse, 'desc_class' => 'instructions', 'desc' => __( 'Enable this to keep track reverse withdrawals. In case vendor receive full payment (e.g. COD) from customer then they have to reverse-pay admin commission. This is only applicable for reverse-withdrawal payment methods.', 'wc-multivendor-marketplace' ) ),
																																										"withdrawal_order_payment_methods" => array( 'label' => __( 'Reverse or No Withdrawal Payment Methods', 'wc-multivendor-marketplace' ), 'name' => 'wcfm_withdrawal_options[disallow_order_payment_methods]', 'type' => 'checklist', 'wrapper_class' => 'reverse_withdrawal_ele', 'class' => 'wcfm-checkbox wcfm_ele reverse_withdrawal_ele', 'label_class' => 'wcfm_title wcfm_full_title reverse_withdrawal_ele', 'options' => $wcfm_marketplace_disallow_order_payment_methods, 'value' => $disallow_order_payment_methods, 'desc_class' => 'reverse_withdrawal_ele', 'desc' => __( 'Order Payment Methods which are not applicable for vendor withdrawal request. e.g Order payment method COD and vendor receiving that amount directly from customers. So, no more require withdrawal request. You may also enable Reverse Withdrawal to track reverse pending payments for such payment options.', 'wc-multivendor-marketplace' )  ),
																																										"withdrawal_setting_break_3" => array( 'type' => 'html', 'class' => "reverse_withdrawal_ele", 'value' => '<div style="height: 15px;"></div>' ),
																																										
																																										"withdrawal_reverse_limit" => array('label' => __('Reverse Withdraw Limit', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options[withdrawal_reverse_limit]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele reverse_withdrawal_ele', 'label_class' => 'wcfm_title wcfm_ele reverse_withdrawal_ele', 'value' => $withdrawal_reverse_limit, 'desc_class' => 'instructions reverse_withdrawal_ele', 'attributes' => array( 'min' => '1', 'step' => '1'), 'desc' => __('Set reverse withdrawal threshold limit, if reverse-pay balance reach this limit then vendor will not allow to withdrawal anymore. Leave empty to inactive this option.', 'wc-multivendor-marketplace') ),
																																										), $wcfm_withdrawal_options ) );
					?>
				</div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_withdrawal_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_withdrawal_manage', true ) ) return; 
		
		if( isset( $wcfm_settings_form['wcfm_withdrawal_options'] ) ) {
			
			// Periodic Schedule Reset - Only if any change happend
			$wcfm_withdrawal_options = get_option( 'wcfm_withdrawal_options', array() );
		
			$withdrawal_mode         = isset( $wcfm_withdrawal_options['withdrawal_mode'] ) ? $wcfm_withdrawal_options['withdrawal_mode'] : '';
			$withdrawal_schedule     = isset( $wcfm_withdrawal_options['withdrawal_schedule'] ) ? $wcfm_withdrawal_options['withdrawal_schedule'] : 'week';
			if( $withdrawal_mode && ( $withdrawal_mode == 'by_schedule' ) ) {
				$new_withdrawal_mode         = isset( $wcfm_settings_form['wcfm_withdrawal_options'] ) ? $wcfm_settings_form['wcfm_withdrawal_options'] : '';
				$new_withdrawal_schedule     = isset( $wcfm_settings_form['wcfm_withdrawal_options'] ) ? $wcfm_settings_form['wcfm_withdrawal_options'] : 'week';
				
				if( !$new_withdrawal_mode || ( $new_withdrawal_mode != 'by_schedule' ) ) {
					$next = WC()->queue()->get_next( 'wcfmmp_withdrawal_periodic_scheduler' );
					if ( $next ) {
						WC()->queue()->cancel_all( 'wcfmmp_withdrawal_periodic_scheduler' );
					}
				} elseif( $new_withdrawal_mode && ( $new_withdrawal_mode != $withdrawal_mode ) ) {
					$next = WC()->queue()->get_next( 'wcfmmp_withdrawal_periodic_scheduler' );
					if ( $next ) {
						WC()->queue()->cancel_all( 'wcfmmp_withdrawal_periodic_scheduler' );
					}
				}
			}
			
			
			
			update_option( 'wcfm_withdrawal_options', $wcfm_settings_form['wcfm_withdrawal_options'] );
		}
	}
	
	function wcfm_shipping_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_store_shipping', true ) ) return; 
		
		$wcfm_shipping_options = get_option( 'wcfm_shipping_options', array() );
		$wcfmmp_store_shipping_enabled = isset( $wcfm_shipping_options['enable_store_shipping'] ) ? $wcfm_shipping_options['enable_store_shipping'] : 'yes';
		
		$wcfmmp_marketplace_shipping_zone_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_zone_settings', array() );
    $wcfmmp_marketplace_shipping_zone_enabled = ( !empty($wcfmmp_marketplace_shipping_zone_options) && !empty($wcfmmp_marketplace_shipping_zone_options['enabled']) ) ? $wcfmmp_marketplace_shipping_zone_options['enabled'] : 'yes';
		
		$wcfmmp_marketplace_shipping_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_country_settings', array() );
    $wcfmmp_marketplace_shipping_enabled = ( !empty($wcfmmp_marketplace_shipping_options) && !empty($wcfmmp_marketplace_shipping_options['enabled']) ) ? $wcfmmp_marketplace_shipping_options['enabled'] : 'yes';
    
    $wcfmmp_marketplace_shipping_by_weight_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_weight_settings', array() );
    $wcfmmp_marketplace_shipping_by_weight_enabled = ( !empty($wcfmmp_marketplace_shipping_by_weight_options) && !empty($wcfmmp_marketplace_shipping_by_weight_options['enabled']) ) ? $wcfmmp_marketplace_shipping_by_weight_options['enabled'] : 'yes';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_shipping_head">
			<label class="wcfmfa fa-truck"></label>
			<?php _e('Shipping Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_shipping_expander" class="wcfm-content">
			  <h2><?php _e('Store Shipping Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-store-shipping/' ); ?>
				<div class="wcfm_clearfix"></div>
				<div class="store_address">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_shipping', array(
																																										"enable_store_shipping" => array('label' => __('Store Shipping', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $wcfmmp_store_shipping_enabled, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Uncheck this to disable vendor wise store shipping options.', 'wc-multivendor-marketplace' ) ),
																																			) ) );
					?>
				</div>
				
				<div id="wcfm_settings_form_shipping_by_zone_expander" class="wcfm_store_shipping_fields" style="margin-top:50px;">
				  <div class="wcfm_vendor_settings_heading"><h2><?php _e('Shipping By Zone', 'wc-multivendor-marketplace'); ?></h2></div>
				  <div class="wcfm_clearfix"></div>
					<div class="store_address">
				  
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_shipping_country', array(
																																						"enable_marketplace_shipping_zone" => array('label' => __('Enable', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_store_shipping_fields', 'label_class' => 'wcfm_title checkbox_title wcfm_store_shipping_fields', 'value' => 'yes', 'dfvalue' => $wcfmmp_marketplace_shipping_zone_enabled, 'desc_class' => 'wcfm_page_options_desc wcfm_store_shipping_fields', 'desc' => __( 'Uncheck this to disable zone wise shipping options.', 'wc-multivendor-marketplace' ) ),
                                                                        ) ) );
            ?>
          </div>
        </div>
				
				<div id="wcfm_settings_form_shipping_by_country_expander" class="wcfm_store_shipping_fields" style="margin-top:50px;">
				  <div class="wcfm_vendor_settings_heading"><h2><?php _e('Shipping By Country', 'wc-multivendor-marketplace'); ?></h2></div>
				  <div class="wcfm_clearfix"></div>
					<div class="store_address">
				  
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_shipping_country', array(
																																						"enable_marketplace_shipping" => array('label' => __('Enable', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_store_shipping_fields', 'label_class' => 'wcfm_title checkbox_title wcfm_store_shipping_fields', 'value' => 'yes', 'dfvalue' => $wcfmmp_marketplace_shipping_enabled, 'desc_class' => 'wcfm_page_options_desc wcfm_store_shipping_fields', 'desc' => __( 'Uncheck this to disable country wise shipping options.', 'wc-multivendor-marketplace' ) ),
																																						"store_shipping_break_1" => array( 'type' => 'html', 'value' => '<div style="padding-bottom:30px;" class="wcfm_clearfix"></div>' ),
                                                                        ) ) );
            ?>
            
            <div class="wcfm_store_shipping_country_fields">
              <?php
							$wcfmmp_shipping_by_country = get_option( '_wcfmmp_shipping_by_country', array() );
	
							$wcfmmp_country_rates       = get_option( '_wcfmmp_country_rates', array() );
							$wcfmmp_state_rates         = get_option( '_wcfmmp_state_rates', array() );
							
							$WCFM->wcfm_fields->wcfm_generate_form_field (
										apply_filters( 'wcfmmp_settings_fields_shipping_by_country', array(
											"wcfmmp_shipping_type_price" => array('label' => __('Default Shipping Price', 'wc-multivendor-marketplace'), 'name' => 'wcfmmp_shipping_by_country[_wcfmmp_shipping_type_price]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => isset($wcfmmp_shipping_by_country['_wcfmmp_shipping_type_price']) ? $wcfmmp_shipping_by_country['_wcfmmp_shipping_type_price'] : '', 'hints' => __('This is the base price and will be the starting shipping price for each product', 'wc-multivendor-marketplace') ),
											"wcfmmp_additional_product" => array('label' => __('Per Product Additional Price', 'wc-multivendor-marketplace'), 'name' => 'wcfmmp_shipping_by_country[_wcfmmp_additional_product]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => isset($wcfmmp_shipping_by_country['_wcfmmp_additional_product']) ? $wcfmmp_shipping_by_country['_wcfmmp_additional_product'] : '', 'hints' => __('If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'wc-multivendor-marketplace') ),
											"wcfmmp_additional_qty" => array('label' => __('Per Qty Additional Price', 'wc-multivendor-marketplace'), 'name' => 'wcfmmp_shipping_by_country[_wcfmmp_additional_qty]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => isset($wcfmmp_shipping_by_country['_wcfmmp_additional_qty']) ? $wcfmmp_shipping_by_country['_wcfmmp_additional_qty'] : '', 'hints' => __('Every second product of same type will be charged with this price', 'wc-multivendor-marketplace') ),
											"wcfmmp_byc_free_shipping_amount" => array('label' => __('Free Shipping Minimum Order Amount', 'wc-multivendor-marketplace'), 'name' => 'wcfmmp_shipping_by_country[_free_shipping_amount]', 'placeholder' => __( 'NO Free Shipping', 'wc-multivendor-marketplace'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => isset($wcfmmp_shipping_by_country['_free_shipping_amount']) ? $wcfmmp_shipping_by_country['_free_shipping_amount'] : '', 'hints' => __('Free shipping will be available if order amount more than this. Leave empty to disable Free Shipping.', 'wc-multivendor-marketplace') ),
											"wcfmmp_form_location" => array('label' => __('Ships from:', 'wc-multivendor-marketplace'), 'name' => 'wcfmmp_shipping_by_country[_wcfmmp_form_location]','type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => isset($wcfmmp_shipping_by_country['_wcfmmp_form_location']) ? $wcfmmp_shipping_by_country['_wcfmmp_form_location'] : '', 'hints' => __( 'Location from where the products are shipped for delivery. Usually it is same as the store.', 'wc-multivendor-marketplace' ) ),
											) )
									);
						
								$wcfmmp_shipping_rates = array();
								$state_options = array();
								if ( $wcfmmp_country_rates ) {
									foreach ( $wcfmmp_country_rates as $country => $country_rate ) {
										$wcfmmp_shipping_state_rates = array();
										$state_options = array();
										if ( !empty( $wcfmmp_state_rates ) && isset( $wcfmmp_state_rates[$country] ) ) {
											foreach ( $wcfmmp_state_rates[$country] as $state => $state_rate ) {
												$state_options[$state] = $state;
												$wcfmmp_shipping_state_rates[] = array( 
														'wcfmmp_state_to' => $state, 
														'wcfmmp_state_to_price' => $state_rate, 
														'option_values' => $state_options 
													);
											}
										}
										$wcfmmp_shipping_rates[] = array( 
												'wcfmmp_country_to' => $country, 
												'wcfmmp_country_to_price' => $country_rate, 
												'wcfmmp_shipping_state_rates' => $wcfmmp_shipping_state_rates 
											);
									}   
								}
								
								$WCFM->wcfm_fields->wcfm_generate_form_field( 
									apply_filters( 'wcfmmp_settings_fields_shipping_rates_by_country', array( 
										"wcfmmp_shipping_rates" => array(
												'label' => __('Shipping Rates by Country', 'wc-multivendor-marketplace') , 
												'type' => 'multiinput', 
												'label_class' => 'wcfm_title wcfm_full_title', 
												'value' => $wcfmmp_shipping_rates, 
												'desc' => __( 'Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries, there is an option Everywhere Else, you can use that.', 'wc-multivendor-marketplace' ),
												'desc_class' => 'instructions', 
												'options' => array(
														"wcfmmp_country_to" => array(
																'label' => __('Country', 'wc-multivendor-marketplace'), 
																'type' => 'country',
																'wcfmmp_shipping_country' => 1, 
																'class' => 'wcfm-select wcfmmp_country_to_select', 
																'label_class' => 'wcfm_title'
														),
														"wcfmmp_country_to_price" => array( 
																'label' => __('Cost', 'wc-multivendor-marketplace') . '('.get_woocommerce_currency_symbol().')', 
																'type' => 'number',
																'dfvalue' => 0,
																'placeholder' => '0.00',
																'class' => 'wcfm-text', 
																'label_class' => 'wcfm_title' 
														),
														"wcfmmp_shipping_state_rates" => array(
																'label' => __('State Shipping Rates', 'wc-multivendor-marketplace'), 
																'type' => 'multiinput', 
																'label_class' => 'wcfm_title wcfmmp_shipping_state_rates_label', 
																'options' => array(
																		"wcfmmp_state_to" => array( 
																				'label' => __('State', 'wc-multivendor-marketplace'), 
																				'type' => 'select', 'class' => 'wcfm-select wcfmmp_state_to_select', 
																				'label_class' => 'wcfm_title', 
																				'options' => $state_options 
																		),
																		"wcfmmp_state_to_price" => array( 
																				'label' => __('Cost', 'wc-multivendor-marketplace') . '('.get_woocommerce_currency_symbol().')', 
																				'type' => 'number', 
																				'dfvalue' => 0,
																				'placeholder' => '0.00 (' . __('Free Shipping', 'wc-multivendor-marketplace') . ')', 
																				'class' => 'wcfm-text', 
																				'label_class' => 'wcfm_title' 
																		),
						
																) 
														)   
												) 
										)
									) ) 
								);
							?>
						</div>
          </div>
        </div>
        
        
				<div id="wcfm_settings_form_shipping_by_country_expander" class="wcfm_store_shipping_fields" style="margin-top:50px;">
				  <div class="wcfm_vendor_settings_heading"><h2><?php _e('Shipping By Weight', 'wc-multivendor-marketplace'); ?></h2></div>
				  <div class="wcfm_clearfix"></div>
					<div class="store_address">
				  
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_shipping_weight', array(																																					
																																									"enable_marketplace_shipping_by_weight" => array('label' => __('Enable', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_store_shipping_fields', 'label_class' => 'wcfm_title checkbox_title wcfm_store_shipping_fields', 'value' => 'yes', 'dfvalue' => $wcfmmp_marketplace_shipping_by_weight_enabled, 'desc_class' => 'wcfm_page_options_desc wcfm_store_shipping_fields', 'desc' => __( 'Uncheck this to disable weight based shipping options.', 'wc-multivendor-marketplace' ) ),
																																									"store_shipping_break_2" => array( 'type' => 'html', 'value' => '<div style="padding-bottom:30px;" class="wcfm_clearfix"></div>' ),
																																				) ) );
						?>
						
						<div class="wcfm_store_shipping_weight_fields">
						  <?php
								$weight_unit = strtolower( get_option( 'woocommerce_weight_unit' ) );
								$wcfmmp_country_weight_rates       = get_option( '_wcfmmp_country_weight_rates', array() );
								$wcfmmp_country_weight_default_costs  = get_option( '_wcfmmp_country_weight_default_costs', array() );
								$wcfmmp_country_weight_shipping_value = array();
								if( $wcfmmp_country_weight_rates ) {
									foreach ($wcfmmp_country_weight_rates as $country => $each_rate ) {
										$wcfmmp_country_weight_shipping_value[] = array(
												'wcfmmp_weightwise_country_to' => $country,
												'wcfmmp_weightwise_country_default_cost' => isset( $wcfmmp_country_weight_default_costs[$country]) ? $wcfmmp_country_weight_default_costs[$country] : 0,
												'wcfmmp_shipping_country_weight_settings' => $each_rate
										);
									}
								}
								
								$WCFM->wcfm_fields->wcfm_generate_form_field( 
											apply_filters( 'wcfmmp_settings_fields_shipping_rates_by_weight', array( 
												"wcfmmp_shipping_rates_by_weight" => array(
														'label' => __('Country and Weight wise Shipping Rate Calculation', 'wc-multivendor-marketplace') , 
														'type' => 'multiinput', 
														'label_class' => 'wcfm_title wcfm_full_title', 
														'value' => $wcfmmp_country_weight_shipping_value, 
														'desc' => __( 'Add the countries you deliver your products to and specify rates for weight range. If the shipping price is same except some countries/states, there is an option Everywhere Else, you can use that.', 'wc-multivendor-marketplace' ),
														'desc_class' => 'instructions',
														'options' => array(
																"wcfmmp_weightwise_country_to" => array(
																		'label' => __('Country', 'wc-multivendor-marketplace'), 
																		'type' => 'country',
																		'wcfmmp_shipping_country' => 1, 
																		'class' => 'wcfm-select wcfmmp_weightwise_country_to_select', 
																		'label_class' => 'wcfm_title'
																),
																"wcfmmp_weightwise_country_default_cost" => array(
																		'label' => __('Country default cost if no matching rule', 'wc-multivendor-marketplace') . ' ('.get_woocommerce_currency_symbol().')', 
																		'type' => 'number', 
																		'dfvalue' => 0,
																		'placeholder' => '0.00',
																		'class' => 'wcfm-text wcfm_ele wcfmmp_weightwise_country_default_weight_text', 
																		'label_class' => 'wcfm_title',
																),
																"wcfmmp_shipping_country_weight_settings" => array(
																		'label' => __('Weight-Cost Rules', 'wc-multivendor-marketplace'), 
																		'type' => 'multiinput', 
																		'label_class' => 'wcfm_title wcfmmp_shipping_weight_rates_label', 
																		'options' => array(
																				"wcfmmp_weight_rule" => array( 
																						'label' => __('Weight Rule', 'wc-multivendor-marketplace'), 
																						'type' => 'select', 
																						'class' => 'wcfm-select wcfmmp_weight_rule_select', 
																						'label_class' => 'wcfm_title', 
																						'options' => array(
																							'up_to' => __('Weight up to', 'wc-multivendor-marketplace'),
																							'more_than' => __('Weight more than', 'wc-multivendor-marketplace')
																						) 
																				),
																				"wcfmmp_weight_unit" => array( 
																						'label' => __('Weight', 'wc-multivendor-marketplace') . ' ('.$weight_unit.')', 
																						'type' => 'number', 
																						'placeholder' => '0.00 (' . __('Free Shipping', 'wc-multivendor-marketplace') . ')', 
																						'class' => 'wcfm-text', 
																						'label_class' => 'wcfm_title' 
																				),
																				"wcfmmp_weight_price" => array( 
																						'label' => __('Cost', 'wc-multivendor-marketplace') . ' ('.get_woocommerce_currency_symbol().')', 
																						'type' => 'number', 
																						'placeholder' => '0.00 (' . __('Free Shipping', 'wc-multivendor-marketplace') . ')',
																						'class' => 'wcfm-text', 
																						'label_class' => 'wcfm_title' 
																				),
																				
																		)
																)
														)
												)
											))
									);
								?>
						 </div>
          </div>
        </div>					
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_shipping_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_store_shipping', true ) ) return; 
		
		if( isset( $wcfm_settings_form['enable_store_shipping'] ) ) {
			$enable_store_shipping = 'yes';
		} else {
			$enable_store_shipping = 'no';
		}
		$wcfm_shipping_options = get_option( 'wcfm_shipping_options', array() );
		$wcfm_shipping_options['enable_store_shipping'] = $enable_store_shipping;
    update_option( 'wcfm_shipping_options', $wcfm_shipping_options );
    
    // Shipping by Zone
		if( isset( $wcfm_settings_form['enable_marketplace_shipping_zone'] ) ) {
			$enable_marketplace_shipping_zone = 'yes';
		} else {
			$enable_marketplace_shipping_zone = 'no';
		}
		$wcfmmp_marketplace_shipping_zone_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_zone_settings', array() );
    $wcfmmp_marketplace_shipping_zone_options['enabled'] = $enable_marketplace_shipping_zone;
    update_option( 'woocommerce_wcfmmp_product_shipping_by_zone_settings', $wcfmmp_marketplace_shipping_zone_options );
		
    // Shipping by Country
		if( isset( $wcfm_settings_form['enable_marketplace_shipping'] ) ) {
			$enable_marketplace_shipping = 'yes';
		} else {
			$enable_marketplace_shipping = 'no';
		}
		$wcfmmp_marketplace_shipping_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_country_settings', array() );
    $wcfmmp_marketplace_shipping_options['enabled'] = $enable_marketplace_shipping;
    update_option( 'woocommerce_wcfmmp_product_shipping_by_country_settings', $wcfmmp_marketplace_shipping_options );
    
    if( isset( $wcfm_settings_form['wcfmmp_shipping_by_country'] ) ) {
			update_option( '_wcfmmp_shipping_by_country', $wcfm_settings_form['wcfmmp_shipping_by_country'] );
		}
		
		if(isset($wcfm_settings_form['wcfmmp_shipping_rates']) && !empty($wcfm_settings_form['wcfmmp_shipping_rates'])) {
			$wcfmmp_country_rates = array();
			$wcfmmp_state_rates   = array(); 
			foreach( $wcfm_settings_form['wcfmmp_shipping_rates'] as $wcfmmp_shipping_rates ) {
				if( $wcfmmp_shipping_rates['wcfmmp_country_to'] ) {
					if( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] && !empty( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] ) ) {
						foreach( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] as $wcfmmp_shipping_state_rates ) {
							if( $wcfmmp_shipping_state_rates['wcfmmp_state_to'] ) {
								$wcfmmp_state_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']][$wcfmmp_shipping_state_rates['wcfmmp_state_to']] = $wcfmmp_shipping_state_rates['wcfmmp_state_to_price'];
							}
						}
					}
					$wcfmmp_country_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']] = $wcfmmp_shipping_rates['wcfmmp_country_to_price'];
				}
			}
			update_option( '_wcfmmp_country_rates', $wcfmmp_country_rates );
			update_option( '_wcfmmp_state_rates', $wcfmmp_state_rates );
		}

		// Shipping by Weight
    if( isset( $wcfm_settings_form['enable_marketplace_shipping_by_weight'] ) ) {
			$enable_marketplace_shipping_by_weight = 'yes';
		} else {
			$enable_marketplace_shipping_by_weight = 'no';
		}
    
    $wcfmmp_marketplace_shipping_by_weight_options = get_option( 'woocommerce_wcfmmp_product_shipping_by_weight_settings', array() );
    $wcfmmp_marketplace_shipping_by_weight_options['enabled'] = $enable_marketplace_shipping_by_weight;
    update_option( 'woocommerce_wcfmmp_product_shipping_by_weight_settings', $wcfmmp_marketplace_shipping_by_weight_options );
    
    $wcfmmp_country_weight_rates   = array(); 
		$wcfmmp_country_weight_default_costs = array();
		foreach( $wcfm_settings_form['wcfmmp_shipping_rates_by_weight'] as $wcfmmp_shipping_rates_by_weight ) {
			if( $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to'] ) {
				if( $wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'] && !empty( $wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'] ) ) {
					$wcfmmp_country_weight_rates[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = $wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'];
					$wcfmmp_country_weight_default_costs[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = ( $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] || $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] != "" ) ? $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] : 0;
				}
			}
		}
		
		update_option( '_wcfmmp_country_weight_rates', $wcfmmp_country_weight_rates );
		update_option( '_wcfmmp_country_weight_default_costs', $wcfmmp_country_weight_default_costs );
	}
	
	function wcfm_refund_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_refund_requests', true ) ) return; 
		
		$wcfm_refund_options = get_option( 'wcfm_refund_options', array() );
		
		$refund_auto_approve = isset( $wcfm_refund_options['refund_auto_approve'] ) ? $wcfm_refund_options['refund_auto_approve'] : 'no';
		$refund_by_customer = isset( $wcfm_refund_options['refund_by_customer'] ) ? $wcfm_refund_options['refund_by_customer'] : 'no';
		$refund_threshold = isset( $wcfm_refund_options['refund_threshold'] ) ? $wcfm_refund_options['refund_threshold'] : '';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_refund_head">
			<label class="wcfmfa fa-retweet"></label>
			<?php _e('Refund Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_refund_expander" class="wcfm-content">
			  <h2><?php _e('Store Refund Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-refund/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_refund', array(
																																									"refund_auto_approve" => array('label' => __('Refund auto-approve?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_refund_options[refund_auto_approve]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $refund_auto_approve ),
																																									"refund_by_customer" => array('label' => __('Refund by Customer?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_refund_options[refund_by_customer]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $refund_by_customer, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __('Enable this to allow customers make refund requests. Customers refund requests never auto-approve, admin always has to manually approve this.', 'wc-multivendor-marketplace') ),
					                                                                        "refund_threshold" => array('label' => __('Refund Threshold', 'wc-multivendor-marketplace'), 'type' => 'number', 'name' => 'wcfm_refund_options[refund_threshold]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $refund_threshold, 'desc_class' => 'wcfm_page_options_desc', 'attributes' => array( 'min' => '1', 'step' => '1'), 'desc' => __('Refund Threshold Days, (Allow an order available to make a refund request). Leave empty to inactive this option.', 'wc-multivendor-marketplace') ),
																																									) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_refund_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_refund_requests', true ) ) return; 
		
		if( isset( $wcfm_settings_form['wcfm_refund_options'] ) ) {
			update_option( 'wcfm_refund_options', $wcfm_settings_form['wcfm_refund_options'] );
		}
	}
	
	function wcfm_review_settings( $wcfm_options ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_reviews', true ) ) return; 
		
		$wcfm_review_options = get_option( 'wcfm_review_options', array() );
		
		$wcfm_default_review_categories = get_wcfm_marketplace_default_review_categories();
		
		$review_auto_approve    = isset( $wcfm_review_options['review_auto_approve'] ) ? $wcfm_review_options['review_auto_approve'] : 'no';
		$review_only_store_user = isset( $wcfm_review_options['review_only_store_user'] ) ? $wcfm_review_options['review_only_store_user'] : 'no';
		$product_review_sync    = isset( $wcfm_review_options['product_review_sync'] ) ? $wcfm_review_options['product_review_sync'] : 'no';
		$wcfm_review_categories = isset( $wcfm_review_options['review_categories'] ) ? $wcfm_review_options['review_categories'] : $wcfm_default_review_categories;
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_review_head">
			<label class="wcfmfa fa-comment-alt"></label>
			<?php _e('Review Settings', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_review_expander" class="wcfm-content">
			  <h2><?php _e('Store Review Settings', 'wc-multivendor-marketplace'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-reviews/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_review', array(
																																									"review_auto_approve"    => array('label' => __('Review auto-approve?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_review_options[review_auto_approve]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $review_auto_approve ),
																																									"review_only_store_user" => array('label' => __('Review only store users?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_review_options[review_only_store_user]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $review_only_store_user, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Enable this to allow only users to review the store who already purchased something from this store.', 'wc-multivendor-marketplace' ) ),
																																									"product_review_sync"    => array('label' => __('Product review sync?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'wcfm_review_options[product_review_sync]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $product_review_sync, 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'Enable this to allow vendor\'s products review consider as store review.', 'wc-multivendor-marketplace' ) ),
					                                                                        "wcfm_review_categories" => array('label' => __('Review Categories', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'wcfm_review_options[review_categories]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title', 'value' => $wcfm_review_categories, 'options' => array( 
					                                                                        																			"category" => array('label' => __('Category', 'wc-multivendor-marketplace'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele' ),
					                                                                        																			) ),
																																									) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfm_review_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		if( !apply_filters( 'wcfm_is_allow_reviews', true ) ) return; 
		
		if( isset( $wcfm_settings_form['wcfm_review_options'] ) ) {
			update_option( 'wcfm_review_options', $wcfm_settings_form['wcfm_review_options'] );
		}
	}
	
	function wcfm_vendor_registration_settings() {
		global $WCFM, $WCFMmp, $_POST;
		
		$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
		
		$membership_reject_rules = array();
		if( isset( $wcfm_membership_options['membership_reject_rules'] ) ) $membership_reject_rules = $wcfm_membership_options['membership_reject_rules'];
		$required_approval = isset( $membership_reject_rules['required_approval'] ) ? $membership_reject_rules['required_approval'] : 'no';
		
		$membership_type_settings = array();
		if( isset( $wcfm_membership_options['membership_type_settings'] ) ) $membership_type_settings = $wcfm_membership_options['membership_type_settings'];
		$email_verification = isset( $membership_type_settings['email_verification'] ) ? 'yes' : 'no';
		$sms_verification = isset( $membership_type_settings['sms_verification'] ) ? 'yes' : 'no';
		
		$wcfmvm_registration_static_fields = get_option( 'wcfmvm_registration_static_fields', array() );
		$enable_address = isset( $wcfmvm_registration_static_fields['address'] ) ? 'yes' : '';
		
		$field_types = apply_filters( 'wcfm_product_custom_filed_types', array( 'text' => 'Text', 'number' => 'Number', 'textarea' => 'textarea', 'datepicker' => 'Date Picker', 'timepicker' => 'Time Picker', 'checkbox' => 'Check Box', 'select' => 'Select', 'upload' => 'File/Image' ) );
		$wcfmvm_registration_custom_fields = get_option( 'wcfmvm_registration_custom_fields', array() );
		
		?>
		<div class="page_collapsible" id="membership_settings_form_custom_field_head">
			<label class="wcfmfa fa-user"></label>
			<?php _e('Vendor Registration', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_custom_field_expander" class="wcfm-content">
				<h2><?php _e( 'Vendor Registration Settings', 'wc-multivendor-marketplace' ); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-marketplace-vendor-registration/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'membership_setting_approval_fields', array(  
																																		"wcfmvm_required_approval" => array( 'label' => __( 'Required Approval', 'wc-multivendor-marketplace' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $required_approval, 'hints' => __( 'Whether user required Admin Approval to become vendor or not!', 'wc-multivendor-marketplace' ) ),
																																		"wcfmvm_email_verification" => array( 'label' => __( 'Email Verification', 'wc-multivendor-marketplace' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $email_verification ),
																																		) ) );
				
				if( WCFMmp_Dependencies::wcfm_sms_alert_plugin_active_check() || WCFMmp_Dependencies::wcfm_twilio_plugin_active_check() || WCFMmp_Dependencies::wcfm_msg91_plugin_active_check() ) {
					$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																															"wcfmvm_sms_verification" => array( 'label' => __( 'SMS (via OTP) Verification', 'wc-multivendor-marketplace' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $sms_verification ),
																															) );
				}
				
				?>
				<h2><?php _e( 'Registration Form Fields', 'wc-multivendor-marketplace' ); ?></h2>
				<div class="wcfm_clearfix"></div>
				<?php
				$pages = get_pages(); 
				$pages_array = array( '' => __( '-- Choose Terms Page --', 'wc-multivendor-marketplace' ) );
				$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
				foreach ( $pages as $page ) {
					if(!in_array($page->ID, $woocommerce_pages)) {
						$pages_array[$page->ID] = $page->post_title;
					}
				}
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmvm_registration_static_fields', array(
					                                                                                                    "first_name"  => array( 'label' => __( 'First Name', 'wc-frontend-manager' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[first_name]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['first_name'] ) ? 'yes' : '' ),
							                                                                                                "last_name"   => array( 'label' => __( 'Last Name', 'wc-frontend-manager' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[last_name]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['last_name'] ) ? 'yes' : '' ),
							                                                                                                "user_name"   => array( 'label' => __( 'User Name', 'wc-multivendor-marketplace' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[user_name]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['user_name'] ) ? 'yes' : '' ),
																																																							"address"     => array( 'label' => __( 'Store Address', 'wc-frontend-manager' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[address]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['address'] ) ? 'yes' : '' ),
																																																							"phone"       => array( 'label' => __( 'Store Phone', 'wc-frontend-manager' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[phone]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['phone'] ) ? 'yes' : '' ),
																																																							"terms"       => array( 'label' => __( 'Terms & Conditions', 'wc-multivendor-marketplace' ), 'type' => 'checkbox', 'name' => 'wcfmvm_registration_static_fields[terms]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => isset( $wcfmvm_registration_static_fields['terms'] ) ? 'yes' : '' ),
																																																							"terms_page"  => array( 'label' => __( 'Terms Page', 'wc-multivendor-marketplace' ), 'type' => 'select', 'name' => 'wcfmvm_registration_static_fields[terms_page]', 'options' => $pages_array, 'class' => 'wcfm-select wcfm_ele terms_page_ele', 'label_class' => 'wcfm_title terms_page_ele', 'value' => isset( $wcfmvm_registration_static_fields['terms_page'] ) ? $wcfmvm_registration_static_fields['terms_page'] : '' )
																																																							)
																																		) );
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_membership_registration_custom_fields', array(
																																														"wcfmvm_registration_custom_fields" => array('label' => __( 'Registration Form Custom Fields', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_full_title', 'value' => $wcfmvm_registration_custom_fields, 'options' => array(
																																																						"enable"   => array('label' => __('Enable', 'wc-frontend-manager'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes'),
																																																						"type" => array( 'label' => __('Field Type', 'wc-frontend-manager'), 'type' => 'select', 'options' => $field_types, 'class' => 'wcfm-select wcfm_ele field_type_options', 'label_class' => 'wcfm_title'),           
																																																						"label" => array( 'label' => __('Label', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title'),
																																																						"options" => array( 'label' => __('Options', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele field_type_select_options', 'label_class' => 'wcfm_title field_type_select_options', 'placeholder' => __( 'Insert option values | separated', 'wc-frontend-manager' ) ),
																																																						"help_text" => array( 'label' => __('Help Content', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title' ),
																																																						"required" => array( 'label' => __('Required?', 'wc-frontend-manager'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes' ),
																																															) )
																																											) ) );
				?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		<?php
	}
	
	function wcfm_vendor_registration_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
		$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
		
		if( isset( $wcfm_settings_form['wcfmvm_required_approval'] ) ) {
			$wcfm_membership_options['membership_reject_rules']['required_approval'] = 'yes';
		} else {
			$wcfm_membership_options['membership_reject_rules']['required_approval'] = 'no';
		}
		
		if( isset( $wcfm_settings_form['wcfmvm_email_verification'] ) ) {
			$wcfm_membership_options['membership_type_settings']['email_verification'] = 'yes';
		} else {
			$wcfm_membership_options['membership_type_settings']['email_verification'] = 'no';
			unset( $wcfm_membership_options['membership_type_settings']['email_verification'] );
		}
		
		if( isset( $wcfm_settings_form['wcfmvm_sms_verification'] ) ) {
			$wcfm_membership_options['membership_type_settings']['sms_verification'] = 'yes';
		} else {
			$wcfm_membership_options['membership_type_settings']['sms_verification'] = 'no';
			unset( $wcfm_membership_options['membership_type_settings']['sms_verification'] );
		}
		
	  update_option( 'wcfm_membership_options', $wcfm_membership_options );
		
		if( isset( $wcfm_settings_form['wcfmvm_registration_static_fields'] ) ) {
	  	update_option( 'wcfmvm_registration_static_fields', $wcfm_settings_form['wcfmvm_registration_static_fields'] );
	  } else {
	  	update_option( 'wcfmvm_registration_static_fields', array() );
	  }
	  
	  if( isset( $wcfm_settings_form['wcfmvm_registration_custom_fields'] ) ) {
	  	update_option( 'wcfmvm_registration_custom_fields', $wcfm_settings_form['wcfmvm_registration_custom_fields'] );
	  }
	}
	
	function wcfmmp_store_color_setting_options() {
		global $WCFM;
		
		$color_options = apply_filters( 'wcfmmp_store_color_setting_options', array( 
																																				 'wcfmmp_store_name_color' => array( 'label' => __( 'Store Name', 'wc-multivendor-marketplace' ), 'name' => 'store_name', 'default' => '#ffffff', 'element' => '#wcfmmp-store .banner_text h1, #wcfmmp-store .video_text h1, #wcfmmp-store .slider_text h1, #wcfmmp-store h1.wcfm_store_title, #wcfmmp-store .address h1.wcfm_store_title', 'style' => 'color', 'element2' => '#wcfmmp-store .banner_text h1:after, .banner_text h1:before', 'style2' => 'background' ),
																																				 'wcfmmp_header_background_color' => array( 'label' => __( 'Header Background', 'wc-multivendor-marketplace' ), 'name' => 'header_background', 'default' => '#2f2f2f', 'element' => '#wcfmmp-store #wcfm_store_header', 'style' => 'background' ),
																																				 'wcfmmp_header_social_background_color' => array( 'label' => __( 'Header Social Background', 'wc-multivendor-marketplace' ), 'name' => 'header_social_background', 'default' => '#212121', 'element' => '#wcfmmp-store .social_area', 'style' => 'background' ),
																																				 'wcfmmp_header_text_color' => array( 'label' => __( 'Header Text', 'wc-multivendor-marketplace' ), 'name' => 'header_text', 'default' => '#ffffff', 'element' => '#wcfmmp-store .address span, #wcfmmp-store .address a, #wcfmmp-store .address h1, #wcfmmp-store .address h2, #wcfmmp-store .social_area ul li:hover i', 'style' => 'color', 'element2' => '#wcfmmp-store .social_area ul li', 'style2' => 'background' ),
																																				 'wcfmmp_header_icon_color' => array( 'label' => __( 'Header Icon', 'wc-multivendor-marketplace' ), 'name' => 'header_icon', 'default' => '#17a2b8', 'element' => '#wcfmmp-store .address i, #wcfmmp-store .bd_icon i, #wcfmmp-store .social_area ul li, #wcfmmp-store .social_area ul li a i', 'style' => 'color', 'element2' => '#wcfmmp-store .social_area ul li:hover', 'style2' => 'background' ),
																																				 'wcfmmp_sidebar_background_color' => array( 'label' => __( 'Sidebar Background', 'wc-multivendor-marketplace' ), 'name' => 'sidebar_background', 'default' => '#efefef', 'element' => '#wcfmmp-store .left_sidebar, #wcfmmp-stores-lists .left_sidebar', 'style' => 'background' ),
																																				 'wcfmmp_sidebar_heading_color' => array( 'label' => __( 'Sidebar Heading', 'wc-multivendor-marketplace' ), 'name' => 'sidebar_heading', 'default' => '#525252', 'element' => '#wcfmmp-store .sidebar_heading h4, #wcfmmp-store .reviews_heading, #wcfmmp-store h2, #wcfmmp-store .user_name', 'style' => 'color', 'element2' => '.store-data-container .store-phone i', 'style2' => 'background' ),
																																				 'wcfmmp_sidebar_text_color' => array( 'label' => __( 'Sidebar Text', 'wc-multivendor-marketplace' ), 'name' => 'sidebar_text', 'default' => '#9f9e9e', 'element' => '#wcfmmp-store .categories_list ul li a, .store-data-container .store-address, .store-data-container .store-phone', 'style' => 'color' ),
																																				 'wcfmmp_tabs_text_color' => array( 'label' => __( 'Tabs Text', 'wc-multivendor-marketplace' ), 'name' => 'tabs_text', 'default' => '#999999', 'element' => '#wcfmmp-store .tab_area .tab_links li a', 'style' => 'color' ),
																																				 'wcfmmp_tabs_active_text_color' => array( 'label' => __( 'Tabs Active Text', 'wc-multivendor-marketplace' ), 'name' => 'tabs_active_text', 'default' => '#17a2b8', 'element' => '#wcfmmp-store .tab_area .tab_links li:hover a, #wcfmmp-store .tab_area .tab_links li.active a', 'style' => 'color', 'element2' => '#wcfmmp-store .tab_area .tab_links li:after', 'style2' => 'background', 'element3' => '#wcfmmp-store .tab_area .tab_links li.active', 'style3' => 'border-top-color' ),
																																				 'wcfmmp_store_card_heighlight' => array( 'label' => __( 'Store Card Highlight Color', 'wc-multivendor-marketplace' ), 'name' => 'ctore_card_highlight', 'default' => '#17a2b8', 'element' => '#wcfmmp-stores-wrap ul.wcfmmp-store-wrap li.coloum-4 .store-data-container .store-data .store-phone i, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li.coloum-5 .store-data-container .store-data .store-phone i', 'style' => 'color', 'element2' => '#wcfmmp-stores-wrap .paginations ul li span.current, #wcfmmp-stores-wrap .paginations ul li a:hover', 'style2' => 'background', 'element3' => '#wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-wrapper .store-content', 'style3' => 'border-bottom-color' ),
																																				 'wcfmmp_store_card_color' => array( 'label' => __( 'Store Card Text Color', 'wc-multivendor-marketplace' ), 'name' => 'ctore_card_text', 'default' => '#ffffff', 'element' => '#wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data h2 a, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data-container .store-address, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data-container .store-phone', 'style' => 'color'),
																																				 'wcfmmp_button_bg_color' => array( 'label' => __( 'Button Background', 'wc-multivendor-marketplace' ), 'name' => 'button_bg', 'default' => '#17a2b8', 'element' => '#wcfmmp-store .add_review button, #wcfmmp-store .user_rated, #wcfmmp-store .bd_icon_box .follow, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry, #wcfmmp-store .bd_icon_box .wcfm_store_chatnow, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store', 'style' => 'background', 'element2' => '#wcfmmp-store .reviews_heading a, #wcfmmp-store .reviews_count a', 'style2' => 'color', 'element3' => '#wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-content, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store', 'style3' => 'border-bottom-color' ),
																																				 'wcfmmp_button_text_color' => array( 'label' => __( 'Button Text', 'wc-multivendor-marketplace' ), 'name' => 'button_text', 'default' => '#ffffff', 'element' => '#wcfmmp-store .add_review button, #wcfmmp-store .user_rated, #wcfmmp-store .bd_icon_box .follow, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry, #wcfmmp-store .bd_icon_box .wcfm_store_chatnow, #wcfmmp-store .bd_icon_box .follow span, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry span, #wcfmmp-store .bd_icon_box .wcfm_store_chatnow span, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store', 'style' => 'color' ),
																																				 'wcfmmp_button_active_bg_color' => array( 'label' => __( 'Button Hover Background', 'wc-multivendor-marketplace' ), 'name' => 'button_active_bg', 'default' => '#000000', 'element' => '#wcfmmp-store .add_review button:hover, #wcfmmp-store .bd_icon_box .follow:hover, #wcfmmp-store .bd_icon_box:hover .wcfm_store_enquiry:hover, #wcfmmp-store .bd_icon_box .wcfm_store_chatnow:hover, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry:hover, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store:hover', 'style' => 'background', 'element2' => '#wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry:hover, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store:hover', 'style2' => 'border-bottom-color' ),
																																				 'wcfmmp_button_active_text_color' => array( 'label' => __( 'Button Hover Text', 'wc-multivendor-marketplace' ), 'name' => 'button_active_text', 'default' => '#ffffff', 'element' => '#wcfmmp-store .add_review button:hover, #wcfmmp-store .bd_icon_box a.follow:hover, #wcfmmp-store .bd_icon_box a.wcfm_store_enquiry:hover, #wcfmmp-store .bd_icon_box a.wcfm_store_chatnow:hover, #wcfmmp-store .bd_icon_box a.follow:hover span, #wcfmmp-store .bd_icon_box a.wcfm_store_enquiry:hover span, #wcfmmp-store .bd_icon_box a.wcfm_store_chatnow:hover span, #wcfmmp-store .bd_icon_box a.follow:hover i, #wcfmmp-store .bd_icon_box a.wcfm_store_enquiry:hover i, #wcfmmp-store .bd_icon_box a.wcfm_store_chatnow:hover i, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry:hover, #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-data p.store-enquiry a.wcfm_catalog_enquiry:hover span, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store:hover, #wcfmmp-stores-wrap .store-footer a.wcfmmp-visit-store:hover span', 'style' => 'color' ),
																																				 'wcfmmp_star_rating_color' => array( 'label' => __( 'Star Rating', 'wc-multivendor-marketplace' ), 'name' => 'start_rating', 'default' => '#FF912C', 'element' => '#wcfmmp-store .rating_box i.selected, #wcfmmp-store .rating-stars ul > li.star.selected > i.wcfmfa, .store-data-container .star-rating span::before, .logo_area_after .wcfmmp-store-rating span:before', 'style' => 'color' ),
																																			) );
		
		return $color_options;
	}
	
	function wcfm_store_style_settings() {
		global $WCFM, $WCFMmp;
		
		$wcfm_store_color_settings = get_option( 'wcfm_store_color_settings', array() );
		?>
		<div class="page_collapsible" id="wcfmmp_settings_form_store_style_head">
			<label class="wcfmfa fa-image"></label>
			<?php _e('Store Style', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfmmp_settings_form_store_style_expander" class="wcfm-content">
				<h2><?php _e('Store Display Setting', 'wc-multivendor-marketplace'); ?></h2>
				<div class="wcfm_clearfix"></div>
				<?php
					$color_options = $this->wcfmmp_store_color_setting_options();
					$color_options_array = array();
	
					foreach( $color_options as $color_option_key => $color_option ) {
						$color_options_array[$color_option['name']] = array( 'label' => $color_option['label'] , 'type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'label_class' => 'wcfm_title wcfm_ele', 'name' => 'wcfm_store_color_settings['.$color_option['name'].']', 'value' => ( isset($wcfm_store_color_settings[$color_option['name']]) ) ? $wcfm_store_color_settings[$color_option['name']] : $color_option['default'] );
					}
					$WCFM->wcfm_fields->wcfm_generate_form_field( $color_options_array );
				?>
				<div class="wcfm_clearfix"></div>
				<input type="submit" name="reset-store-color-settings" value="<?php _e( 'Reset to Default', 'wc-frontend-manager' ); ?>" id="wcfm_store_color_setting_reset_button" class="wcfm_submit_button" />
				<div class="wcfm_clearfix"></div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	function wcfm_store_style_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMmp, $_POST;
		
	  if( isset( $wcfm_settings_form['wcfm_store_color_settings'] ) ) {
	  	update_option( 'wcfm_store_color_settings', $wcfm_settings_form['wcfm_store_color_settings'] );
	  	$this->wcfmmp_create_store_css();
	  }
	}
	
	/**
	 * Create WCFMmp custom Store CSS
	 */
	function wcfmmp_create_store_css() {
		global $WCFM, $WCFMmp;
		
		$wcfm_store_color_settings = get_option( 'wcfm_store_color_settings', array() );
		$color_options = $this->wcfmmp_store_color_setting_options();
		$custom_color_data = '';
		$custom_button_color_data = '';
		foreach( $color_options as $color_option_key => $color_option ) {
		  $custom_color_data .= $color_option['element'] . '{ ' . "\n";
			$custom_color_data .= "\t" . $color_option['style'] . ': ';
			if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
			$custom_color_data .= '!important;' . "\n";
			$custom_color_data .= '}' . "\n\n";
			
			if( in_array( $color_option_key, array( 'wcfmvm_field_button_color', 'wcfmvm_field_button_hover_color', 'wcfmvm_field_button_bg_color' ) ) ) {
				$custom_button_color_data .= $color_option['element'] . '{ ' . "\n";
				$custom_button_color_data .= "\t" . $color_option['style'] . ': ';
				if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_button_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_button_color_data .= $color_option['default']; }
				$custom_button_color_data .= '!important;' . "\n";
				$custom_button_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element2'] ) && isset( $color_option['style2'] ) ) {
				$custom_color_data .= $color_option['element2'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style2'] . ': ';
				if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default2']; }
				$custom_color_data .= '!important;' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element3'] ) && isset( $color_option['style3'] ) ) {
				$custom_color_data .= $color_option['element3'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style3'] . ': ';
				if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= '!important;' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element4'] ) && isset( $color_option['style4'] ) ) {
				$custom_color_data .= $color_option['element4'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style4'] . ': ';
				if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= '!important;' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element5'] ) && isset( $color_option['style5'] ) ) {
				$custom_color_data .= $color_option['element5'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style5'] . ': ';
				if( isset( $wcfm_store_color_settings[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_store_color_settings[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= '!important;' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
		}
		
		$upload_dir      = wp_upload_dir();

		$files = array(
			array(
				'key'     => 'wcfmmp_style_custom',
				'base' 		=> $upload_dir['basedir'] . '/wcfm',
				'file' 		=> 'wcfmmp-style-custom-' . time() . '.css',
				'content' => $custom_color_data,
			)
		);

		$wcfmmp_style_custom = get_option( 'wcfmmp_style_custom' );
		if(  $wcfmmp_style_custom && file_exists( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfmmp_style_custom ) ) {
			unlink( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfmmp_style_custom );
		}
		
		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					$wcfmvm_style_custom = $file['file'];
					update_option( $file['key'], $file['file'] );
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
		return $wcfmmp_style_custom;
	}

}