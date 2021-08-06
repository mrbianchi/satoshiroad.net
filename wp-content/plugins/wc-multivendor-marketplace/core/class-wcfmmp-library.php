<?php

/**
 * WCFMmp plugin library
 *
 * Plugin intiate library
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Library {
	
	public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $js_lib_path;
  
  public $js_lib_url;
  
  public $css_lib_path;
  
  public $css_lib_url;
  
  public $views_path;
	
	public function __construct() {
    global $WCFMmp;
		
	  $this->lib_path = $WCFMmp->plugin_path . 'assets/';

    $this->lib_url = $WCFMmp->plugin_url . 'assets/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->js_lib_path = $this->lib_path . 'js/';
    
    $this->js_lib_url = $this->lib_url . 'js/';
    
    $this->js_lib_url_min = $this->js_lib_url . 'min/';
    
    $this->css_lib_path = $this->lib_path . 'css/';
    
    $this->css_lib_url = $this->lib_url . 'css/';
    
    $this->css_lib_url_min = $this->css_lib_url . 'min/';
    
    $this->views_path = $WCFMmp->plugin_path . 'views/';
    
    // Load wcfmmp Scripts
    add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ) );
    add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ) );
    
    // Load wcfmmp Styles
    add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ) );
    add_action( 'after_wcfm_load_styles', array( &$this, 'load_styles' ) );
    
    // Load wcfmmp views
    add_action( 'wcfm_load_views', array( &$this, 'load_views' ) );
  }
  
  public function load_scripts( $end_point ) {
	  global $WCFM, $WCFMmp;
    
	  switch( $end_point ) {
	  	
	    case 'wcfm-dashboard':
	    	
	  	break;
	  	
	  	case 'wcfm-products-manage':
	  		if( !wcfm_is_vendor() ) {
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  		}
      break;
	  	
	  	case 'wcfm-settings':
	  		if( !wcfm_is_vendor() ) {
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  			
	  			$wcfm_store_color_setting_options = $WCFMmp->wcfmmp_settings->wcfmmp_store_color_setting_options();
					wp_localize_script( 'wcfmmp_settings_js', 'wcfm_store_color_setting_options', $wcfm_store_color_setting_options );
	  		}
      break;
      
      case 'wcfm-memberships-manage':
      case 'wcfm-vendors-new': 
	  		if( !wcfm_is_vendor() ) {
	  			$WCFM->library->load_multiinput_lib();
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  		}
      break;
      
      case 'wcfm-vendors-manage':  
      	if( !wcfm_is_vendor() ) {
	  			$WCFM->library->load_multiinput_lib();
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  			
	  			$WCFM->library->load_datatable_lib();
	  			$WCFM->library->load_daterangepicker_lib();
	  			$WCFM->library->load_datatable_download_lib();
	  			wp_enqueue_script( 'wcfmmp_vendors_manager_js', $this->js_lib_url . 'vendors/wcfmmp-script-vendors-manage.js', array('jquery', 'dataTables_js'), $WCFMmp->version, true );
	  			
	  			// Order Columns Defs
					$wcfm_datatable_column_defs = '[{ "targets": 0, "orderable" : false }, { "targets": 1, "orderable" : false }, { "targets": 2, "orderable" : false }, { "targets": 3, "orderable" : false }, { "targets": 4, "orderable" : false },{ "targets": 5, "orderable" : false },{ "targets": 6, "orderable" : false },{ "targets": 7, "orderable" : false },{ "targets": 8, "orderable" : false },{ "targets": 9, "orderable" : false },{ "targets": 10, "orderable" : false },{ "targets": 11, "orderable" : false },{ "targets": 12, "orderable" : false }]';
																		
					$wcfm_datatable_column_defs = apply_filters( 'wcfm_datatable_column_defs', $wcfm_datatable_column_defs, 'order' );
					
					// Order Columns Priority
					$wcfm_datatable_column_priority = '[{ "responsivePriority": 2 },{ "responsivePriority": 1 },{ "responsivePriority": 4 },{ "responsivePriority": 10 },{ "responsivePriority": 6 },{ "responsivePriority": 5 },{ "responsivePriority": 7 },{ "responsivePriority": 11 },{ "responsivePriority": 3 },{ "responsivePriority": 12 },{ "responsivePriority": 8 },{ "responsivePriority": 9 },{ "responsivePriority": 1 }]';
					$wcfm_datatable_column_priority = apply_filters( 'wcfm_datatable_column_priority', $wcfm_datatable_column_priority, 'order' );
					
					wp_localize_script( 'dataTables_js', 'wcfm_datatable_columns', array( 'defs' => $wcfm_datatable_column_defs, 'priority' => $wcfm_datatable_column_priority ) );
					
					// Screen manager
					$wcfm_screen_manager = get_option( 'wcfm_screen_manager', array() );
					$wcfm_screen_manager_data = array();
					if( isset( $wcfm_screen_manager['order'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['order'];
					if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
						$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
						$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
					}
					
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
					if( !$WCFM->is_marketplace || !apply_filters( 'wcfm_is_allow_view_commission', true ) ) {
						$wcfm_screen_manager_data[8] = 'yes';
					}
					if( apply_filters( 'wcfm_orders_additonal_data_hidden', true ) ) {
						$wcfm_screen_manager_data[10] = 'yes';
					}
					if( WCFM_Dependencies::wcfmd_plugin_active_check() ) {
						//$wcfm_screen_manager_data[13] = 'yes';
					} else {
						//$wcfm_screen_manager_data[12] = 'yes';
					}
					$wcfm_screen_manager_data    = apply_filters( 'wcfm_screen_manager_data_columns', $wcfm_screen_manager_data );
					wp_localize_script( 'wcfmmp_vendors_manager_js', 'wcfm_orders_screen_manage', $wcfm_screen_manager_data );
					
					$wcfm_screen_manager_hidden_data = array();
					$wcfm_screen_manager_hidden_data[3] = 'yes';
					$wcfm_screen_manager_hidden_data[7] = 'yes';
					$wcfm_screen_manager_hidden_data[9] = 'yes';
					$wcfm_screen_manager_hidden_data    = apply_filters( 'wcfm_screen_manager_hidden_columns', $wcfm_screen_manager_hidden_data );
					wp_localize_script( 'wcfmmp_vendors_manager_js', 'wcfm_orders_screen_manage_hidden', $wcfm_screen_manager_hidden_data );
					
					wp_localize_script( 'wcfmmp_vendors_manager_js', 'wcfm_orders_auto_refresher', array( 'is_allow' => apply_filters( 'wcfm_orders_is_allow_auto_refresher', false ) ) );
	  		}
      break;
	  	
    }
  }
  
  public function load_styles( $end_point ) {
	  global $WCFM, $WCFMmp;
		
	  switch( $end_point ) {
	  	
	  	case 'wcfm-memberships-manage':
	    	// wp_enqueue_style( 'wcfm_settings_css',  $WCFM->library->css_lib_url . 'settings/wcfm-style-settings.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-vendors-manage':  
		    wp_enqueue_style( 'wcfm_orders_css',  $WCFM->library->css_lib_url . 'orders/wcfm-style-orders.css', array(), $WCFM->version );
		  break;
		}
	}
	
	public function load_views( $end_point ) {
	  global $WCFM, $WCFMmp;
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-analytics':
        //$WCFMmp->template->get_template( 'wcfmmp-view-analytics.php' );
      break;
    }
  }
}