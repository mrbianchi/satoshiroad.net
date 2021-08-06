<?php
/**
 * WCFM plugin core
 *
 * WCFM Ledger core
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Ledger {

	public function __construct() {
		global $WCFM, $WCFMmp;
		
		if( !wcfm_is_vendor() ) return;
		
		if( !apply_filters( 'wcfm_is_allow_ledger', true ) ) return;
		
		// WCFM Ledger Query Var Filter
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_ledger_query_vars' ), 10 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_ledger_endpoint_title' ), 10, 2 );
		add_action( 'init', array( &$this, 'wcfm_ledger_init' ), 120 );
		
		// WCFMu Ledger Load WCFMu Scripts
		add_action( 'wcfm_load_scripts', array( &$this, 'wcfm_ledger_load_scripts' ), 10 );
		add_action( 'after_wcfm_load_scripts', array( &$this, 'wcfm_ledger_load_scripts' ), 10 );
		
		// WCFMu Ledger Load WCFMu Styles
		add_action( 'wcfm_load_styles', array( &$this, 'wcfm_ledger_load_styles' ), 10 );
		add_action( 'after_wcfm_load_styles', array( &$this, 'wcfm_ledger_load_styles' ), 10 );
		
		// WCFMu Ledger Load WCFMu views
		add_action( 'wcfm_load_views', array( &$this, 'wcfm_ledger_load_views' ), 10 );
		
		// WCFMu Ledger Ajax Controller
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcfm_ledger_ajax_controller' ) );
		
		// Ledger menu on WCfM dashboard
		if( apply_filters( 'wcfm_is_allow_ledger', true ) ) {
			add_filter( 'wcfm_menus', array( &$this, 'wcfm_ledger_menus' ), 30 );
		}
	}
	
	/**
   * WCfM Ledger Query Var
   */
  function wcfm_ledger_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_wcfm_vars = array(
			'wcfm-ledger'        => ! empty( $wcfm_modified_endpoints['wcfm-ledger'] ) ? $wcfm_modified_endpoints['wcfm-ledger'] : 'ledger',
		);
		$query_vars = array_merge( $query_vars, $query_wcfm_vars );
		
		return $query_vars;
  }
  
  /**
   * WCfM Ledger End Point Title
   */
  function wcfm_ledger_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
			case 'wcfm-ledger' :
				$title = __( 'Ledger Book', 'wc-multivendor-marketplace' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCfM Ledger Endpoint Intialize
   */
  function wcfm_ledger_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		//if( !get_option( 'wcfm_updated_end_point_payment' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_ledger', 1 );
		//}
  }
  
	/**
   * WCfM Ledger Ledger Menu
   */
  function wcfm_ledger_menus( $menus ) {
  	global $WCFM;
  		
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-ledger' => array( 'label'  => __( 'Ledger Book', 'wc-multivendor-marketplace' ),
																										 'url'        => wcfm_ledger_url(),
																										 'icon'       => 'money-bill-alt',
																										 'menu_for'   => 'vendor',
																										 'priority'   => 69
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
  	return $menus;
  }
  
	/**
   * WCfM Ledger Scripts
   */
  public function wcfm_ledger_load_scripts( $end_point ) {
	  global $WCFM, $WCFMmp;
    
	  switch( $end_point ) {
      case 'wcfm-ledger':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_datatable_scroll_lib();
      	wp_enqueue_script( 'wcfm_ledger_js', $WCFMmp->library->js_lib_url . 'ledger/wcfmmp-script-ledger.js', array('jquery'), $WCFMmp->version, true );
      break;
	  }
	}
	
	/**
   * WCfM Ledger Styles
   */
	public function wcfm_ledger_load_styles( $end_point ) {
	  global $WCFM, $WCFMmp;
		
	  switch( $end_point ) {
		  case 'wcfm-ledger':
				wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
				wp_enqueue_style( 'wcfm_dashboard_css',  $WCFM->library->css_lib_url . 'dashboard/wcfm-style-dashboard.css', array(), $WCFM->version );
				wp_enqueue_style( 'wcfm_ledger_css',  $WCFMmp->library->css_lib_url . 'ledger/wcfmmp-style-ledger.css', array( 'wcfm_dashboard_css' ), $WCFMmp->version );
		  break;
	  }
	}
	
	/**
   * WCfM Ledger Views
   */
  public function wcfm_ledger_load_views( $end_point ) {
	  global $WCFM, $WCFMmp;
	  
	  switch( $end_point ) {
      case 'wcfm-ledger':
      	$WCFMmp->template->get_template( 'ledger/wcfmmp-view-ledger.php' );
      break;
	  }
	}
	
	/**
   * WCfM Ledger Ajax Controllers
   */
  public function wcfm_ledger_ajax_controller() {
  	global $WCFM, $WCFMmp;
  	
  	$controllers_path = $WCFMmp->plugin_path . 'controllers/ledger/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = sanitize_text_field( $_POST['controller'] );
  		switch( $controller ) {
  			case 'wcfm-ledger':
					include_once( $controllers_path . 'wcfmmp-controller-ledger.php' );
					new WCFMmp_Ledger_Controller();
  			break;
  		}
  	}
  }
  
  /**
   * Reference Name
   */
  function wcfmmp_vendor_ledger_reference_name( $reference ) {
  	$ledger_references = apply_filters( 'wcfmmp_ledger_references', 
  																		 array(
  																		 	 			'order'               => __( 'Order', 'wc-multivendor-marketplace' ),
  																		 	 			'withdraw'            => __( 'Withdrawal', 'wc-multivendor-marketplace' ),
  																		 	 			'refund'              => __( 'Refunded', 'wc-multivendor-marketplace' ),
  																		 	 			'partial-refund'      => __( 'Partial Refunded', 'wc-multivendor-marketplace' ),
  																		 	 			'withdraw-charges'    => __( 'Charges', 'wc-multivendor-marketplace' )
  																		       ) );
  	
  	$reference_name = __( ucfirst( str_replace( '-', ' ', $reference ) ), 'wc-multivendor-marketplace' );
  	if( isset( $ledger_references[$reference] ) ) $reference_name = $ledger_references[$reference];
  	return $reference_name;
  }
  
}