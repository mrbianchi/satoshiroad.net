<?php
/*
Plugin Name: WooCommerce Product Carousel Slider
Plugin URI:  https://aazztech.com/product/woocommerce-product-carousel-slider-pro
Description: This plugin allows you to easily create WooCommerce product carousel slider. It is fully responsive and mobile friendly carousel slider which comes with lots of features.
Version:     3.3.2
Author:      AazzTech
Author URI:  https://aazztech.com
License:     GPL2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages/
Text Domain: woocommerce-product-carousel-slider
WC requires at least: 3.0
WC tested up to: 3.5.1
*/

/**
 * Protect direct access
 */

 defined( 'ABSPATH' ) || die( 'Cheating, huh? Direct access to this file is not allowed !!!!' );

 
 class WooCommerce_Product_Carousel_Slider {
     public function __construct()
     {
         // at first let's define all the necessary constant
         $this->_define_constant();
         // lets include all the required files
         $this->_include_required_files();
         // fire up objects
         /**
          * Check if WooCommerce is not active
          */
         if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
             add_action( 'admin_notices', array($this, 'admin_notice') );
         }
         // fire up objects
         new WPCS_Custom_Post;
         // Modify the text of the feature image meta box on our custom post page
         // Instantiate carousel shortcode
         new WPCS_Shortcode;

         //  lets fire up all admin hook
         add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_styles') );

         // lets fire up all front end hook, notes. template_redirect is the best hook for adding scripts and style for plugin-- Brad William, author of the professional WordPress Plugin Development
         add_action( 'template_redirect', array($this, 'enqueue_front_scripts_styles') );

         // do other miscellaneous stuff from here
         //#TODO; Add woocommerce widget feature after completing the plugin
         //add_action( 'widgets_init', array($this, 'register_widget'));

         //Enables shortcode for Widget
         add_filter('widget_text', 'do_shortcode');


         add_action('plugins_loaded', array($this, 'load_textdomain'));
         add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'pro_version_link') );
         add_action('admin_menu', array($this, 'upgrade_support_submenu_pages'));



     }

     private function _define_constant()
     {
         /**
          * Defining constants
          */
         if( ! defined( 'WPCS_PLUGIN_DIR' ) ) define( 'WPCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
         if( ! defined( 'WPCS_PLUGIN_URI' ) ) define( 'WPCS_PLUGIN_URI', plugins_url( '', __FILE__ ) );
         if( ! defined( 'WPCS_TEXTDOMAIN' ) ) define( 'WPCS_TEXTDOMAIN', 'woocommerce-product-carousel-slider' );

     }


     public function enqueue_admin_scripts_styles()
     {
         global $typenow;
         if ( ($typenow == 'woocarousel') ) {
             wp_enqueue_style( 'wpcs_custom_wp_admin_css', WPCS_PLUGIN_URI . '/css/wpcs-admin-styles.css' );
             wp_enqueue_style( 'wpcs_meta_fields_css', WPCS_PLUGIN_URI . '/css/cmb2.min.css' );
             wp_enqueue_style( 'wp-color-picker' );
             wp_enqueue_script( 'wpcs_custom_wp_admin_js', WPCS_PLUGIN_URI . '/js/wpcs-admin-script.js', array('jquery','wp-color-picker'), false, true  );

         }
     }

     public function enqueue_front_scripts_styles()
     {
         wp_register_style( 'wpcs-owl-carousel-style', WPCS_PLUGIN_URI . '/css/owl.carousel.min.css', false, '2.2.1' );
         wp_register_style( 'wpcs-owl-theme-style', WPCS_PLUGIN_URI . '/css/owl.theme.default.css', false, '2.2.1' );
         wp_register_style( 'wpcs-font-awesome', WPCS_PLUGIN_URI . '/css/font-awesome.min.css' );
         wp_register_style( 'wpcs-custom-style', WPCS_PLUGIN_URI . '/css/wpcs-styles.css' );
         wp_register_script( 'wpcs-owl-carousel-js', WPCS_PLUGIN_URI . '/js/owl.carousel.min.js', array('jquery'),'2.2.1', true );
         wp_register_script( 'wpcs-custom-js', WPCS_PLUGIN_URI . '/js/custom.js', array('jquery'),'3.0', true );
     }

     public function register_widget()
     {
         
     }

     private function _include_required_files()
     {


         require_once WPCS_PLUGIN_DIR . 'wpcs-custom-post.php';
         require_once WPCS_PLUGIN_DIR . 'wpcs-img-resizer.php';
         require_once WPCS_PLUGIN_DIR . 'wpcs-shortcodes.php';
     }
     function admin_notice() { ?>
         <div class="error">
             <p>
                 <?php
                 printf('%s <strong>%s</strong>', esc_html__('WooCommerce plugin is not activated. Please install and activate it to use', WPCS_TEXTDOMAIN), esc_html__('WooCommerce Product Carousel Slider Plugin', WPCS_TEXTDOMAIN) );
                 ?>
             </p>
         </div>
     <?php }
     /**
      * Load plugin textdomain
      */
     function load_textdomain() {
         load_plugin_textdomain( WPCS_TEXTDOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
     }
      /**
      * Pro Version link
      */
     public function pro_version_link( $links ) {
         $links[] = '<a href="http://aazztech.com/product/woocommerce-product-carousel-slider-pro" target="_blank">Pro Version</a>';
         return $links;
     }

     /**
      * Upgrade & Support submenu pages
      */
     function upgrade_support_submenu_pages() {
         add_submenu_page( 'edit.php?post_type=woocarousel', esc_html__('Support', WPCS_TEXTDOMAIN), esc_html__('Usage & Support', WPCS_TEXTDOMAIN), 'manage_options', 'support', array( $this, 'support_view' ) );
     }

     function support_view() {
         include('wpcs-support.php');
     }

     /**
      * It will serialize and then encode the string using base64_encode() and return the encoded data
      * @param $data
      * @return string
      */
     public static function serialize_and_encode24($data)
     {
         return base64_encode(serialize($data));
     }

     /**
      * It will decode the data using base64_decode() and then unserialize the data and return it
      * @param string $data Encoded strings that should be decoded and then unserialize
      * @return mixed
      */
     public static function unserialize_and_decode24($data){
         return unserialize(base64_decode($data));
     }

 }

 new WooCommerce_Product_Carousel_Slider();
 

