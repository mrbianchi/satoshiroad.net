<?php
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	add_action( 'wp_enqueue_scripts', function() {
		wp_enqueue_script('satoshiroad',get_stylesheet_directory_uri() . '/satoshiroad.js',array( 'jquery' ));
	} );

    // og:image on products
    add_image_size( "og_image", 1200, 600, true );

    // Bitcoin on withdraws
    add_filter( 'wcfm_marketplace_withdrwal_payment_methods', function( $payment_methods ) {
        $payment_methods['bitcoin_for_vendors'] = 'Bitcoin';
        return $payment_methods;
    });


    add_filter( 'wcfm_marketplace_settings_fields_withdrawal_charges', function( $withdrawal_charges, $wcfm_withdrawal_options, $withdrawal_charge ) {
        $gateway_slug = 'bitcoin_for_vendors';
        $withdrawal_charge_bitcoin_for_vendors = isset( $withdrawal_charge[$gateway_slug] ) ? $withdrawal_charge[$gateway_slug] : array();
        $payment_withdrawal_charges = array( 
            "withdrawal_charge_".$gateway_slug => array(
                'label' => __('Bitcoin Charge', 'wc-multivendor-marketplace'),
                'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge]['.$gateway_slug.']',
                'class' => 'withdraw_charge_block withdraw_charge_'.$gateway_slug,
                'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_'.$gateway_slug,
                'value' => $withdrawal_charge_bitcoin_for_vendors,
                'custom_attributes' => array( 'limit' => 1 ),
                'options' => array(
                    "percent" => array(
                        'label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),
                        'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed',
                        'attributes' => array( 'min' => '0.1', 'step' => '0.1')
                    ),
                    "fixed" => array(
                        'label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 
                        'type' => 'number', 
                        'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 
                        'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 
                        'attributes' => array( 'min' => '0.1', 'step' => '0.1') 
                    ),
                    "tax" => array(
                        'label' => __('Charge Tax', 'wc-multivendor-marketplace'), 
                        'type' => 'number', 
                        'class' => 'wcfm-text wcfm_ele', 
                        'label_class' => 'wcfm_title wcfm_ele', 
                        'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 
                        'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' )
                    ),
                )
            )
        );
        $withdrawal_charges = array_merge( $withdrawal_charges, $payment_withdrawal_charges );
        return $withdrawal_charges;
    }, 50, 3);

    add_filter( 'wcfm_marketplace_settings_fields_billing', function( $vendor_billing_fileds, $vendor_id ) {
        $gateway_slug = 'bitcoin_for_vendors';
        $vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
        if( !$vendor_data ) $vendor_data = array();
        $bitcoin_for_vendors = isset( $vendor_data['payment'][$gateway_slug]['Address'] ) ? esc_attr( $vendor_data['payment'][$gateway_slug]['Address'] ) : '' ;
        $vendor_bitcoin_for_vendors_billing_fileds = array(
        $gateway_slug => array('label' => __('Bitcoin Address', 'wc-frontend-manager'), 'name' => 'payment['.$gateway_slug.'][Address]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_'.$gateway_slug, 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_'.$gateway_slug, 'value' => $bitcoin_for_vendors ),
        );
        $vendor_billing_fileds = array_merge( $vendor_billing_fileds, $vendor_bitcoin_for_vendors_billing_fileds );
        return $vendor_billing_fileds;
    }, 50, 2);


    /**
     * Disable sidebar on product pages in Storefront.
     *
     * @param bool $is_active_sidebar
     * @param int|string $index
     *
     * @return bool
     */
    function iconic_remove_sidebar( $is_active_sidebar, $index ) {
        if( $index !== "sidebar-1" ) {
            return $is_active_sidebar;
		}
		
		if( is_search() ) {
			return $is_active_sidebar;
		} else return false;

        if( ! is_product() ) {
            return $is_active_sidebar;
        }



        return false;
    }

    add_filter( 'is_active_sidebar', 'iconic_remove_sidebar', 10, 2 );


    // Function to change email address
    
    function wpb_sender_email( $original_email_address ) {
        return 'no-reply@satoshiroad.net';
    }
    
    // Function to change sender name
    function wpb_sender_name( $original_email_from ) {
        return 'Satoshi Road';
    }
    
    // Hooking up our functions to WordPress filters 
    add_filter( 'wp_mail_from', 'wpb_sender_email' );
    add_filter( 'wp_mail_from_name', 'wpb_sender_name' );


	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
    function storefront_credit() {
		?>
		<div class="site-info">
			<strong><?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?></strong>
		</div><!-- .site-info -->
		<?php
    }
    

	/**
	 * Display the page header without the featured image
	 *
	 * @since 1.0.0
	 */
    function storefront_homepage_header() {
		edit_post_link( __( 'Edit this section', 'storefront' ), '', '', '', 'button storefront-hero__button-edit' );
		ob_start();
		?>
		<header class="entry-header">
			<?php
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
		if(!is_page()) ob_end_flush();else ob_end_clean();
    }
    

	/**
	 * Display the page header
	 *
	 * @since 1.0.0
	 */
    function storefront_page_header() {
		ob_start();
		?>
		<header class="entry-header">
			<?php
			storefront_post_thumbnail( 'full' );
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
		if(!is_page()) ob_end_flush();else ob_end_clean();
    }
    

    /**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function storefront_post_header() {
		ob_start();
		?>
		<header class="entry-header">
		<?php
		if ( is_single() ) {
			storefront_post_meta();
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			if ( 'post' === get_post_type() ) {
				storefront_post_meta();
			}

			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}
		?>
		</header><!-- .entry-header -->
		<?php
		if(!is_page()) ob_end_flush();else ob_end_clean();
    }
    

    	/**
	 * The Storefront WooCommerce Integration class
	 */
	class Storefront_WooCommerce {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_filter( 'body_class', array( $this, 'woocommerce_body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_scripts' ), 20 );
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
			add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'thumbnail_columns' ) );
			add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimiter' ) );

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.5', '<' ) ) {
				add_action( 'wp_footer', array( $this, 'star_rating_script' ) );
			}

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
				add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ) );
			}

			// Integrations.
			add_action( 'storefront_woocommerce_setup', array( $this, 'setup_integrations' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_integrations_scripts' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 140 );
		}

		/**
		 * Sets up theme defaults and registers support for various WooCommerce features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 2.4.0
		 * @return void
		 */
		public function setup() {
			add_theme_support(
				'woocommerce', apply_filters(
					'storefront_woocommerce_args', array(
						'single_image_width'    => 416,
						'thumbnail_image_width' => 324,
						'product_grid'          => array(
							'default_columns' => 3,
							'default_rows'    => 4,
							'min_columns'     => 1,
							'max_columns'     => 6,
							'min_rows'        => 1,
						),
					)
				)
			);

			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

			/**
			 * Add 'storefront_woocommerce_setup' action.
			 *
			 * @since  2.4.0
			 */
			do_action( 'storefront_woocommerce_setup' );
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 * If the Customizer is active pull in the raw css. Otherwise pull in the prepared theme_mods if they exist.
		 *
		 * @since 2.1.0
		 * @return void
		 */
		public function add_customizer_css() {
			wp_add_inline_style( 'storefront-woocommerce-style', $this->get_woocommerce_extension_css() );
		}

		/**
		 * Assign styles to individual theme mod.
		 *
		 * @deprecated 2.3.1
		 * @since 2.1.0
		 * @return void
		 */
		public function set_storefront_style_theme_mods() {
			if ( function_exists( 'wc_deprecated_function' ) ) {
				wc_deprecated_function( __FUNCTION__, '2.3.1' );
			} else {
				_deprecated_function( __FUNCTION__, '2.3.1' );
			}
		}

		/**
		 * Add WooCommerce specific classes to the body tag
		 *
		 * @param  array $classes css classes applied to the body tag.
		 * @return array $classes modified to include 'woocommerce-active' class
		 */
		public function woocommerce_body_class( $classes ) {
			$classes[] = 'woocommerce-active';

			// Remove `no-wc-breadcrumb` body class.
			$key = array_search( 'no-wc-breadcrumb', $classes, true );

			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}

			return $classes;
		}

		/**
		 * WooCommerce specific scripts & stylesheets
		 *
		 * @since 1.0.0
		 */
		public function woocommerce_scripts() {
			global $storefront_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'storefront-woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce/woocommerce.css', array(), $storefront_version );
			wp_style_add_data( 'storefront-woocommerce-style', 'rtl', 'replace' );

			wp_register_script( 'storefront-header-cart', get_template_directory_uri() . '/assets/js/woocommerce/header-cart' . $suffix . '.js', array(), $storefront_version, true );
			wp_enqueue_script( 'storefront-header-cart' );

			if ( ! class_exists( 'Storefront_Sticky_Add_to_Cart' ) && is_product() ) {
				wp_register_script( 'storefront-sticky-add-to-cart', get_template_directory_uri() . '/assets/js/sticky-add-to-cart' . $suffix . '.js', array(), $storefront_version, true );
			}

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-legacy', get_template_directory_uri() . '/assets/css/woocommerce/woocommerce-legacy.css', array(), $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-legacy', 'rtl', 'replace' );
			}
		}

		/**
		 * Star rating backwards compatibility script (WooCommerce <2.5).
		 *
		 * @since 1.6.0
		 */
		public function star_rating_script() {
			if ( is_product() ) {
				?>
			<script type="text/javascript">
				var starsEl = document.querySelector( '#respond p.stars' );
				if ( starsEl ) {
					starsEl.addEventListener( 'click', function( event ) {
						if ( event.target.tagName === 'A' ) {
							starsEl.classList.add( 'selected' );
						}
					} );
				}
			</script>
				<?php
			}
		}

		/**
		 * Related Products Args
		 *
		 * @param  array $args related products args.
		 * @since 1.0.0
		 * @return  array $args related products args
		 */
		public function related_products_args( $args ) {
			$args = apply_filters(
				'storefront_related_products_args', array(
					'posts_per_page' => 3,
					'columns'        => 3,
				)
			);

			return $args;
		}

		/**
		 * Product gallery thumbnail columns
		 *
		 * @return integer number of columns
		 * @since  1.0.0
		 */
		public function thumbnail_columns() {
			$columns = 4;

			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$columns = 5;
			}

			return intval( apply_filters( 'storefront_product_thumbnail_columns', $columns ) );
		}

		/**
		 * Products per page
		 *
		 * @return integer number of products
		 * @since  1.0.0
		 */
		public function products_per_page() {
			return intval( apply_filters( 'storefront_products_per_page', 12 ) );
		}

		/**
		 * Query WooCommerce Extension Activation.
		 *
		 * @param string $extension Extension class name.
		 * @return boolean
		 */
		public function is_woocommerce_extension_activated( $extension = 'WC_Bookings' ) {
			return class_exists( $extension ) ? true : false;
		}

		/**
		 * Remove the breadcrumb delimiter
		 *
		 * @param  array $defaults The breadcrumb defaults.
		 * @return array           The breadcrumb defaults.
		 * @since 2.2.0
		 */
		public function change_breadcrumb_delimiter( $defaults ) {
			$defaults['delimiter']   = '<span class="breadcrumb-separator"> / </span>';
			$defaults['wrap_before'] = '<div class="storefront-breadcrumb"><div class="col-full"><nav class="woocommerce-breadcrumb">';
			$defaults['wrap_after']  = '</nav></div></div>';
			$defaults['home']  = 'SatoshiRoad.net';
			return $defaults;
		}

		/**
		 * Integration Styles & Scripts
		 *
		 * @return void
		 */
		public function woocommerce_integrations_scripts() {
			global $storefront_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			/**
			 * Bookings
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Bookings' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-bookings-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/bookings.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-bookings-style', 'rtl', 'replace' );
			}

			/**
			 * Brands
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Brands' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-brands-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/brands.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-brands-style', 'rtl', 'replace' );

				wp_enqueue_script( 'storefront-woocommerce-brands', get_template_directory_uri() . '/assets/js/woocommerce/extensions/brands' . $suffix . '.js', array(), $storefront_version, true );
			}

			/**
			 * Wishlists
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Wishlists_Wishlist' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-wishlists-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/wishlists.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-wishlists-style', 'rtl', 'replace' );
			}

			/**
			 * AJAX Layered Nav
			 */
			if ( $this->is_woocommerce_extension_activated( 'SOD_Widget_Ajax_Layered_Nav' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-ajax-layered-nav-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/ajax-layered-nav.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-ajax-layered-nav-style', 'rtl', 'replace' );
			}

			/**
			 * Variation Swatches
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_SwatchesPlugin' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-variation-swatches-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/variation-swatches.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-variation-swatches-style', 'rtl', 'replace' );
			}

			/**
			 * Composite Products
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-composite-products-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/composite-products.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-composite-products-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Photography
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Photography' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-photography-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/photography.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-photography-style', 'rtl', 'replace' );
			}

			/**
			 * Product Reviews Pro
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-product-reviews-pro-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/product-reviews-pro.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-product-reviews-pro-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Smart Coupons
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-smart-coupons-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/smart-coupons.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-smart-coupons-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Deposits
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Deposits' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-deposits-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/deposits.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-deposits-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Product Bundles
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Bundles' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-bundles-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/bundles.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-bundles-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Multiple Shipping Addresses
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Ship_Multiple' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-sma-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/ship-multiple-addresses.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-sma-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Advanced Product Labels
			 */
			if ( $this->is_woocommerce_extension_activated( 'Woocommerce_Advanced_Product_Labels' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-apl-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/advanced-product-labels.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-apl-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Mix and Match
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Mix_and_Match' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-mix-and-match-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/mix-and-match.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-mix-and-match-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Memberships
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Memberships' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-memberships-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/memberships.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-memberships-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Quick View
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Quick_View' ) ) {
				wp_enqueue_style( 'storefront-woocommerce-quick-view-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/quick-view.css', 'storefront-woocommerce-style', $storefront_version );
				wp_style_add_data( 'storefront-woocommerce-quick-view-style', 'rtl', 'replace' );
			}

			/**
			 * Checkout Add Ons
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Checkout_Add_Ons' ) ) {
				add_filter( 'storefront_sticky_order_review', '__return_false' );
			}
		}

		/**
		 * Get extension css.
		 *
		 * @see get_storefront_theme_mods()
		 * @return array $styles the css
		 */
		public function get_woocommerce_extension_css() {
			global $storefront;

			$storefront_theme_mods = $storefront->customizer->get_storefront_theme_mods();

			$woocommerce_extension_style = '';

			if ( $this->is_woocommerce_extension_activated( 'WC_Bookings' ) ) {
				$woocommerce_extension_style .= '
				.wc-bookings-date-picker .ui-datepicker td.bookable a {
					background-color: ' . $storefront_theme_mods['accent_color'] . ' !important;
				}

				.wc-bookings-date-picker .ui-datepicker td.bookable a.ui-state-default {
					background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['accent_color'], -10 ) . ' !important;
				}

				.wc-bookings-date-picker .ui-datepicker td.bookable a.ui-state-active {
					background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['accent_color'], -50 ) . ' !important;
				}
				';
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				$woocommerce_extension_style .= '
				.woocommerce #reviews .product-rating .product-rating-details table td.rating-graph .bar,
				.woocommerce-page #reviews .product-rating .product-rating-details table td.rating-graph .bar {
					background-color: ' . $storefront_theme_mods['text_color'] . ' !important;
				}

				.woocommerce #reviews .contribution-actions .feedback,
				.woocommerce-page #reviews .contribution-actions .feedback,
				.star-rating-selector:not(:checked) label.checkbox {
					color: ' . $storefront_theme_mods['text_color'] . ';
				}

				.woocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.star-rating-selector:not(:checked) input:checked ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover,
				.woocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before,
				.woocommerce-page #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before {
					color: ' . $storefront_theme_mods['accent_color'] . ' !important;
				}';
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				$woocommerce_extension_style .= '
				.coupon-container {
					background-color: ' . $storefront_theme_mods['button_background_color'] . ' !important;
				}

				.coupon-content {
					border-color: ' . $storefront_theme_mods['button_text_color'] . ' !important;
					color: ' . $storefront_theme_mods['button_text_color'] . ';
				}

				.sd-buttons-transparent.woocommerce .coupon-content,
				.sd-buttons-transparent.woocommerce-page .coupon-content {
					border-color: ' . $storefront_theme_mods['button_background_color'] . ' !important;
				}';
			}

			return apply_filters( 'storefront_customizer_woocommerce_extension_css', $woocommerce_extension_style );
		}

		/*
		|--------------------------------------------------------------------------
		| Integrations.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Sets up integrations.
		 *
		 * @since  2.3.4
		 *
		 * @return void
		 */
		public function setup_integrations() {

			if ( $this->is_woocommerce_extension_activated( 'WC_Bundles' ) ) {
				add_filter( 'woocommerce_bundled_table_item_js_enqueued', '__return_true' );
				add_filter( 'woocommerce_bundles_group_mode_options_data', array( $this, 'bundles_group_mode_options_data' ) );
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				add_filter( 'woocommerce_composited_table_item_js_enqueued', '__return_true' );
				add_filter( 'woocommerce_display_composite_container_cart_item_data', '__return_true' );
			}
		}

		/**
		 * Add "Includes" meta to parent cart items.
		 * Displayed only on handheld/mobile screens.
		 *
		 * @since  2.3.4
		 *
		 * @param  array $group_mode_data Group mode data.
		 * @return array
		 */
		public function bundles_group_mode_options_data( $group_mode_data ) {
			$group_mode_data['parent']['features'][] = 'parent_cart_item_meta';

			return $group_mode_data;
		}
	}


	function storefront_handheld_footer_bar_menu_categories() {
		echo '<a href="#"></a>';
	}

	// hide when page is personal store
	function storefront_handheld_footer_bar() {
		$links = array(
			'menu-categories'     => array(
				'priority' => 20,
				'callback' => 'storefront_handheld_footer_bar_menu_categories',
			),
			'cart'       => array(
				'priority' => 30,
				'callback' => 'storefront_handheld_footer_bar_cart_link',
			),
			'my-account' => array(
				'priority' => 10,
				'callback' => 'storefront_handheld_footer_bar_account_link',
			),
		);

		if ( wc_get_page_id( 'myaccount' ) === -1 ) {
			unset( $links['my-account'] );
		}

		if ( wc_get_page_id( 'cart' ) === -1 ) {
			unset( $links['cart'] );
		}

		if ( wc_get_page_id( 'personal-store' ) === -1 ) {
			unset( $links['personal-store'] );
		}

		$links = apply_filters( 'storefront_handheld_footer_bar_links', $links );
		if(is_page("personal-store")) return;

		?>
		<div class="storefront-handheld-footer-bar">
			<ul class="columns-<?php echo count( $links ); ?>">
				<?php foreach ( $links as $key => $link ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>">
						<?php
						if ( $link['callback'] ) {
							call_user_func( $link['callback'], $key, $link );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}


	
	class WCFMmp_Gateway_Bitcoin_for_vendors {
		public $id;
		public $message = array();
		public $gateway_title;
		public $payment_gateway;
		public $withdrawal_id;
		public $vendor_id;
		public $withdraw_amount = 0;
		public $currency;
		public $transaction_mode;
		private $reciver_email;
		public $test_mode = false;
		public $client_id;
		public $client_secret;
		public function __construct() {
		$this->id = 'bitcoin_for_vendors';
		$this->gateway_title = __('Bitcoin', 'wc-multivendor-marketplace');
		$this->payment_gateway = $this->id;
		}
		public function gateway_logo() { global $WCFMmp; return $WCFMmp->plugin_url . 'assets/images/'.$this->id.'.png'; }
		public function process_payment( $withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, $transaction_mode = 'auto' ) {
		global $WCFM, $WCFMmp;
		$this->withdrawal_id = $withdrawal_id;
		$this->vendor_id = $vendor_id;
		$this->withdraw_amount = $withdraw_amount;
		$this->currency = get_woocommerce_currency();
		$this->transaction_mode = $transaction_mode;
		$this->reciver_email = $WCFMmp->wcfmmp_vendor->get_vendor_payment_account( $this->vendor_id, $this->id );
		$withdrawal_test_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['test_mode'] ) ? 'yes' : 'no';
		$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_client_id'] : '';
		$this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_key'] : '';
		if ( $withdrawal_test_mode == 'yes') {
		$this->test_mode = true;
		$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_client_id'] : '';
		$this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] : '';
		}
		if ( $this->validate_request() ) {
		// Updating withdrawal meta
		$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'withdraw_amount', $this->withdraw_amount );
		$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'currency', $this->currency );
		$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'reciver_email', $this->reciver_email );
		return array( 'status' => true, 'message' => __('New transaction has been initiated', 'wc-multivendor-marketplace') );
		} else {
		return $this->message;
		}
		}
		public function validate_request() {
		global $WCFMmp;
		return true;
		}
	}


	/** Sidebar: move it from bottom to top on mobile  */
	function storefront_after_content() {
		?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php
	}
	function storefront_before_content() {
		do_action( 'storefront_sidebar' );
		?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
		<?php
	}
	
	
	// custom widget: after logo/search

	function custom_widgets_init() {

		register_sidebar( array(
			'name'          => 'Translations',
			'id'            => 'translations',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		) );
	
	}
	add_action( 'widgets_init', 'custom_widgets_init' );


	// widget: advanced search
	class SRSearch extends WP_Widget {

		function __construct() {
			// Instantiate the parent object
			parent::__construct( false, 'SR Advanced Search' );
		}
	
		function widget( $args, $instance ) {
			//if(is_page("personal-store")) return;
			// Widget output
?>
<?php
		}
	
		function update( $new_instance, $old_instance ) {
			// Save widget options
		}
	
		function form( $instance ) {
			// Output admin widget options form
		}
	}


	// widget: advanced search filters
	class SRSearch_Filters extends WP_Widget {

		function __construct() {
			// Instantiate the parent object
			parent::__construct( false, 'SR Advanced Search: Filters' );
		}
	
		function widget( $args, $instance ) {
			//if(!is_search()) return;
			// Widget output
?>
<form role="search" method="get" class="widget woocommerce-product-search sr-advanced-search-filters" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
<input type="hidden" name="post_type" value="product" />
<input type="hidden" class="search-field" placeholder="<?php echo esc_html__("Product Search", "woocommerce"); ?>" value="<?php echo get_search_query(); ?>" name="s" />
<?php
	global $WCFM, $WCFMmp, $post;
	$paged  = max( 1, get_query_var( 'paged' ) );
	
	$search_country = '';
	$search_state   = '';
	
	// GEO Locate Support
	if( apply_filters( 'wcfmmp_is_allow_store_list_by_user_location', true ) ) {
		if( is_user_logged_in() && !$search_country ) {
			$user_location = get_user_meta( get_current_user_id(), 'wcfm_user_location', true );
			if( $user_location ) {
				$search_country = $user_location['country'];
				$search_state   = $user_location['state'];
			}
		}
				
		if( apply_filters( 'wcfm_is_allow_wc_geolocate', true ) && class_exists( 'WC_Geolocation' ) && !$search_country ) {
			$user_location = WC_Geolocation::geolocate_ip();
			$search_country = $user_location['country'];
			$search_state   = $user_location['state'];
		}
	}
	
	$search_query   = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
	$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : '';
	$search_country = isset( $_GET['wcfmmp_store_country'] ) ? sanitize_text_field( $_GET['wcfmmp_store_country'] ) : $search_country;
	$search_state   = isset( $_GET['wcfmmp_store_state'] ) ? sanitize_text_field( $_GET['wcfmmp_store_state'] ) : $search_state;
	
	// Country -> States
	$country_obj   = new WC_Countries();
	$countries     = $country_obj->countries;
	$states        = $country_obj->states;
	$state_options = array();
	if( $state && isset( $states[$search_country] ) && is_array( $states[$search_country] ) )
		$state_options = $states[$search_country];
	if( $search_state && empty( $state_options ) ) $state_options[$search_state] = $search_state;
	echo '<span class="gamma widget-title">Envíos a</span>';
	$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_country" => array( 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'value' => isset($_GET["wcfmmp_store_country"]) ? $search_country : false ) ) );
	//$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_state" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'options' => $state_options, 'value' => $search_state ) ) );
	echo "<br>";
	if(isset($_REQUEST['product_cat']) && !empty($_REQUEST['product_cat']))
		$optsetlect=$_REQUEST['product_cat'];
	else
		$optsetlect=0;
		$args = array(
					'show_option_all' => esc_html__( 'All Categories', 'woocommerce' ),
					'hierarchical' => 1,
					'class' => 'cat',
					'echo' => 1,
					'value_field' => 'slug',
					'selected' => $optsetlect
				);
		$args['taxonomy'] = 'product_cat';
		$args['name'] = 'product_cat';              
		$args['class'] = 'cate-dropdown hidden-xs';
		echo '<span class="gamma widget-title">Categoría</span>';
		wp_dropdown_categories($args);
?>
<input type="submit" style="display:none;" value="Filter" />
</form>
<?php
		}
	
		function update( $new_instance, $old_instance ) {
			// Save widget options
		}
	
		function form( $instance ) {
			// Output admin widget options form
		}
	}

	// widget: advanced search filters
	class SRSearch_FiltersDeptos extends WP_Widget {

		function __construct() {
			// Instantiate the parent object
			parent::__construct( false, 'SR Advanced Search: Filters' );
		}
	
		function widget( $args, $instance ) {
			//if(!is_search()) return;
			// Widget output
?>
<?php
		}
	
		function update( $new_instance, $old_instance ) {
			// Save widget options
		}
	
		function form( $instance ) {
			// Output admin widget options form
		}
	}
	

	add_action( 'widgets_init', function() {
		register_widget( 'SRSearch' );
		register_widget( 'SRSearch_Filters' );
		register_widget( 'SRSearch_FiltersDeptos' );
	} );


	function get_request_parameter( $key, $default = '' ) {
		// If not request set
		if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
			return $default;
		}
	
		// Set so process it
		return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
	}


	add_filter( 'the_posts', 'process_search_results', 10, 2 );
	function process_search_results($posts, $query=false) {
		$wcfmmp_store_country = get_request_parameter("wcfmmp_store_country");
		$country = empty($wcfmmp_store_country) ? $_COOKIE["customer_location_satoshiroad"] : $wcfmmp_store_country;
		if ( ( !$query || !$query->is_search() || !$query->is_main_query() || empty($country) ) && $query->query_vars["post_type"] != "product" )
			return $posts;
			
		$relevants = array();
		foreach($posts as &$post) {
			$_wcfmmp_state_rates = get_user_meta($post->post_author,"_wcfmmp_state_rates",true);
			if( array_key_exists($country,$_wcfmmp_state_rates) ||
			array_key_exists("everywhere",$_wcfmmp_state_rates) ||
			(empty($wcfmmp_store_country) && $_COOKIE["customer_location_satoshiroad"] == "none"))
				$relevants[] = $post;
		}
		return $relevants;
	}


	function storefront_primary_navigation() {
		/*
		?>

		<?php
		*/
	}


	function storefront_product_search() {
		global $woocommerce, $post;
		
		if ( storefront_is_woocommerce_activated() ) {
			/*<div class="site-search">*/
			?>
				
<div id="category-list">
	<?php the_widget("WC_Widget_Product_Categories", array("hide_empty" => true)); ?>
</div>
<?php dynamic_sidebar( 'translations' ); ?>
<nav id="site-navigation" class="main-navigation" role="navigation">
<button class="site-menu menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span></span></button>
<div class="primary-navigation">
	<ul class="menu nav-menu left-menu" aria-expanded="false">
		<li class="menu-item menu-item-type-post_type menu-item-object-page location">
<?php
// Country -> States
$countries_obj   = new WC_Countries();
$countries     = $countries_obj->__get('countries');
$states        = $countries_obj->states;

?>
<form method="post">
<?php

ob_start();
woocommerce_form_field('location_1578', array(
'type'       => 'select',
'options'    => array_merge(array("none" => "Sin especificar", "everywhere" => "Todo el mundo"),$countries)
), $_COOKIE["customer_location_satoshiroad"]
);
$select  = ob_get_clean();
$replace = "<select$1 onchange='return this.form.submit()'>";
$select  = preg_replace( '#<select([^>]*)>#', $replace, $select );
echo $select;
?></form>
<select id="width_tmp_select">
  <option id="width_tmp_option"></option>
</select>
<?php

//$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_country" => array( 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'value' => $search_country ) ) );
?>
		</li>
		<li class="menu-item menu-item-type-post_type menu-item-object-page categories">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
			<?php
	//$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_state" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'options' => $state_options, 'value' => $search_state ) ) );
	if(isset($_REQUEST['product_cat']) && !empty($_REQUEST['product_cat']))
		$optsetlect=$_REQUEST['product_cat'];
	else
		$optsetlect=0;
		$args = array(
			'show_option_none' => "Categorías",
			'show_option_all' => "Todas las categorías",
			'hierarchical' => 1,
			'class' => 'cat',
			'echo' => 0,
			'value_field' => 'slug',
			'selected' => $optsetlect,
			'tab_index' => 1,
			'option_none_value' => 0,
			'depth' => 1
		);
		$args['taxonomy'] = 'product_cat';
		$args['name'] = 'product_cat';              
		$args['class'] = 'cate-dropdown hidden-xs';
		$select  = trim(wp_dropdown_categories($args));
		$replace = "<select$1 onchange='return this.form.submit()'>";
		$select  = preg_replace( '#<select([^>]*)>#', $replace, $select );
		echo $select;
			?>
			<input type="hidden" name="post_type" value="product" />
			<input type="hidden" class="search-field" placeholder="<?php echo esc_html__("Product Search", "woocommerce"); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			</form>
		</li>
		<li id="" class="menu-item menu-item-type-post_type menu-item-object-page store-map focus">
			<a href="https://www.satoshiroad.net/store-map">Mapa de Tiendas</a>
		</li>
	</ul>
	<?php
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
	<ul id="site-header-cart" class="site-header-cart menu">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php storefront_cart_link(); ?>
		</li>
		<li>
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</li>
	</ul>
		<?php
	}
	?>
	<ul id="menu-menu" class="menu nav-menu right-menu" aria-expanded="false">
	<?php
	$user_id = get_current_user_id();
	if($user_id) {
		$user_id = apply_filters( 'wcfm_current_vendor_id',  $user_id);
		$the_user = get_user_by( 'id', $user_id );
		$vendor_data = get_user_meta( $user_id, 'wcfmmp_profile_settings', true );
		$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
		$store_name     = empty( $store_name ) ? $the_user->display_name : $store_name;
		$store_slug     = $the_user->user_nicename;
		?>
		<li id="" class="menu-item menu-item-type-post_type menu-item-object-page store">
			<?php /* <a href="https://www.satoshiroad.net/store/<?php echo $store_slug; ?>/"><?php echo $store_name; ?></a> */ ?>
			<a href="https://www.satoshiroad.net/personal-store/"><?php echo $store_name; ?></a>
		</li>
		<?php
	}
	?>
		<li id="" class="menu-item menu-item-type-post_type menu-item-object-page account">
			<a href="https://www.satoshiroad.net/my-account/">Cuenta</a>
		</li>
	</ul>
</div>

<style>
.gt_black_overlay {display:none;position:fixed;top:0%;left:0%;width:100%;height:100%;background-color:black;z-index:2017;-moz-opacity:0.8;opacity:.80;filter:alpha(opacity=80);}
.gt_white_content {display:none;position:fixed;top:50%;left:50%;width:300px;height:375px;margin:-187.5px 0 0 -150px;padding:6px 16px;border-radius:5px;background-color:white;color:black;z-index:19881205;overflow:auto;text-align:left;}
.gt_white_content a {display:block;padding:5px 0;border-bottom:1px solid #e7e7e7;white-space:nowrap;}
.gt_white_content a:last-of-type {border-bottom:none;}
.gt_white_content a.selected {background-color:#ffc;}
.gt_white_content .gt_languages {column-count:2;column-gap:10px;}
.switcher-popup span { display: none; }
#masthead a.glink[onclick="openGTPopup(this)"] span:nth-of-type(2) { display: inline; }
@media (min-width: 768px) {
    .gt_white_content {display:none;position:fixed;top:50%;left:50%;width:980px;height:375px;margin:-187.5px 0 0 -490px;padding:6px 16px;border-radius:5px;background-color:white;color:black;z-index:19881205;overflow:auto;text-align:left;}
    .gt_white_content .gt_languages {column-count:5;column-gap:10px;}
	.switcher-popup span { display: inline; }
}
.gt_white_content::-webkit-scrollbar-track{-webkit-box-shadow:inset 0 0 3px rgba(0,0,0,0.3);border-radius:5px;background-color:#F5F5F5;}
.gt_white_content::-webkit-scrollbar {width:5px;}
.gt_white_content::-webkit-scrollbar-thumb {border-radius:5px;-webkit-box-shadow: inset 0 0 3px rgba(0,0,0,.3);background-color:#888;}
#goog-gt-tt {display:none !important;}
.goog-te-banner-frame {display:none !important;}
.goog-te-menu-value:hover {text-decoration:none !important;}
.goog-text-highlight {background-color:transparent !important;box-shadow:none !important;}
body {top:0 !important;}
#google_translate_element2 {display:none!important;}
</style>
<div class="handheld-navigation">
	<?php ob_start(); ?>
	<div class="handheld-user-info">
		<div class="guest-icon">
			<img class="bi x0 y0 w1 h1" alt="" src="data:image/png;base64,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">
		</div>
		<h3>Bienvenido</h3>
		<p>Ingresa a tu cuenta para ver tus compras, tus ventas, etc.</p>
		<div class="guest-buttons">
			<a class="button button-filled" href="/my-account/">Ingresa</a>
			<a class="button" href="/my-account/">Creá tu cuenta</a>
		</div>
	</div>
	<?php if(is_user_logged_in()) ob_end_clean(); else ob_end_flush(); ?>
	<ul id="menu-mobile" class="menu nav-menu" aria-expanded="true">
		<li class="<?php echo "home" == $post->post_name ? "page-selected" : ""; ?>">
			<a href="/"><i class="fas fa-home"></i>Inicio</a>
		</li>
		<?php
		if($user_id) {
			$user_id = apply_filters( 'wcfm_current_vendor_id',  $user_id);
			$the_user = get_user_by( 'id', $user_id );
			$vendor_data = get_user_meta( $user_id, 'wcfmmp_profile_settings', true );
			$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
			$store_name     = empty( $store_name ) ? $the_user->display_name : $store_name;
			$store_slug     = $the_user->user_nicename;
			?>
		<li class="">
			<?php /* <a href="/store/<?php echo $store_slug; ?>/"><i class="fas fa-store"></i><?php echo $store_name; ?></a> */ ?>
			<a href="/personal-store/"><i class="fas fa-store"></i>Mi tienda</a>
		</li>
			<?php
		}
		?>
		<li class="<?php echo "store-map" == $post->post_name ? "page-selected" : ""; ?>">
			<a href="/store-map"><i class="fas fa-map-marked-alt"></i>Mapa de Tiendas</a>
		</li>
		<li class="menu-categories">
			<a href="#"><i class="fas fa-list"></i>Categorías</a>
		</li>
		<?php if(is_user_logged_in()) foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			if($endpoint == "dashboard" ||
			$endpoint == "edit-address" ||
			$endpoint == "personal-store") continue;
			?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
				<?php switch($endpoint) {
					case "orders":
					echo '<i class="fas fa-shopping-bag"></i>';
					break;
					case "inquiry":
					echo '<i class="fas fa-comments"></i>';
					break;
					case "edit-account":
					echo '<i class="fas fa-user"></i>';
					break;
					case "customer-logout":
					echo '<i class="fas fa-sign-out-alt"></i>';
					break;
				}
				echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
		<li class="<?php echo "help" == $post->post_name ? "page-selected" : ""; ?>">
			<a href="/help/"><i class="fas fa-question-circle"></i>Ayuda</a>
		</li>
	</ul>
</div>
<?php
			?>
		</nav><!-- #site-navigation -->
<form role="search" method="get" class="woocommerce-product-search sr-advanced-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
<input type="hidden" name="post_type" value="product" />
<div class="sr-advanced-search-input-container">
<input type="search" class="search-field" placeholder="<?php echo esc_html__("Product Search", "woocommerce"); ?>" value="<?php echo get_search_query(); ?>" name="s" />
</div>
</form>
			<?php
			/*</div>*/
		}
	}

	add_filter( 'woocommerce_is_purchasable', 'filter_woocommerce_is_purchasable', 10, 2 ); 
	function filter_woocommerce_is_purchasable($is_purchasable, $product) {
		//$country_rates = get_user_meta(get_post($product->get_id())->post_author,"_wcfmmp_country_rates",true);
		$state_rates_per_country = get_user_meta($post->post_author,"_wcfmmp_state_rates",true);
		if(is_array($state_rates_per_country))
			return count( array_filter(array_keys( $state_rates_per_country ))) > 0 ? true : false;
		return $is_purchasable;
	}

	

	function woocommerce_page_title( $echo = true ) {

		if ( is_search() ) {
			
			$page_title = ""; //sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'woocommerce' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {

			$page_title = single_term_title( '', false );

		} else {

			$shop_page_id = wc_get_page_id( 'shop' );
			$page_title   = get_the_title( $shop_page_id );

		}

		$page_title = apply_filters( 'woocommerce_page_title', $page_title );

		if ( $echo ) {
			echo $page_title; // WPCS: XSS ok.
		} else {
			return $page_title;
		}
	}



	/* agrega endpoints a la seccion mi-cuenta
	add_filter( 'woocommerce_account_menu_items', function ( $items ) {
		$my_items = array(
			'personal-store' => 'Mi Tienda'
		);

		$my_items = array_slice( $items, 0, 1, true ) +
			$my_items +
			array_slice( $items, 1, count( $items ), true );

		return $my_items;
	}, 99, 1 );
	*/

	add_filter( 'woocommerce_price_trim_zeros', 'wc_hide_trailing_zeros', 10, 1 );
function wc_hide_trailing_zeros( $trim ) {
    // set to false to show trailing zeros
    return true;
}



/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', 'deliver_to' );
function deliver_to( $tabs ) {
	
	// Adds the new tab
	
	$tabs['shipping_zone'] = array(
		'title' 	=> "Envíos",
		'priority' 	=> 50,
		'callback' 	=> 'shpping_zone'
	);

	return $tabs;

}
function shpping_zone() {
	global $post;
	$country_rates = get_user_meta($post->post_author,"_wcfmmp_country_rates",true);
	$state_rates_per_country = get_user_meta($post->post_author,"_wcfmmp_state_rates",true);
	$shipping_base_price_info = get_user_meta($post->post_author,"_wcfmmp_shipping_by_country",true);
	if(! count( array_filter(array_keys($country_rates))) ) {
		echo '<p class="woocommerce-noreviews">No hay información de envío disponible. Consulte al vendedor.</p>';
		return;
	}
	echo '<h4>Precios básicos</h4>';
	echo '<strong>País de origen:</strong> '.WC()->countries->countries[ $shipping_base_price_info["_wcfmmp_form_location"] ];
	echo $shipping_base_price_info["_wcfmmp_shipping_type_price"] ? '<br><strong>Precio de envío base por unidad:</strong> US$ '.$shipping_base_price_info["_wcfmmp_shipping_type_price"] : "";
	echo $shipping_base_price_info["_wcfmmp_additional_product"] ? '<br><strong>Precio por productos adicionales del mismo vendedor:</strong>  US$ '.$shipping_base_price_info["_wcfmmp_additional_product"] : "";
	echo $shipping_base_price_info["_wcfmmp_additional_qty"] ? '<br><strong>Precio por cantidad adicional de éste producto:</strong>  US$ '.$shipping_base_price_info["_wcfmmp_additional_qty"] : "";
	echo '<br><br><h4>Destinos</h4>';
	echo '<ul>';
	foreach($country_rates as $country_code => $country_rate) {
		//foreach($state_rates_per_country as $country_code => $state_rates) {
		if($country_code != "everywhere") {
			$states_names = WC()->countries->get_states($country_code);
			echo "<li><strong>".WC()->countries->countries[ $country_code ]."</strong></li>";
			echo "<ul>";
			if(isset($state_rates_per_country[$country_code]))
				foreach($state_rates_per_country[$country_code] as $state_code => $rate)
					if($state_code != "everywhere")
						echo "<li>".$states_names[$state_code]." US$ ".(float) $rate."</li>";
					else
						if(count($state_rates_per_country[$country_code]) == 1) 
							echo "<li>Cualquier ciudad de ".WC()->countries->countries[ $country_code ]." US$ ".(float) $rate["everywhere"]."</li>";
						else
							echo "<li>".__( 'Everywhere Else', 'wc-frontend-manager' )." US$ ".(float) $rate["everywhere"]."</li>";
			else
				echo "<li>Cualquier ciudad de ".WC()->countries->countries[ $country_code ]." US$ ".(float) $country_rate."</li>";
			echo "</ul>";
		} else
			echo "<li>".__( 'Everywhere Else', 'wc-frontend-manager' )." ".$country_rates["everywhere"]."</li>";
	}
	echo '</ul>';
	
}

//google it
add_filter("hide_country_shiping_default_zero_cost", "__return_true");


//disable yoast seo tab
add_filter( 'wcfm_is_allow_seo', '__return_false' );


/* Forces all new registrations to your site on the My Account page to be a Vendor, rather than the Customer role */
add_filter( 'woocommerce_new_customer_data', function ( $data ){
    $data['role'] = 'wcfm_vendor'; // the new default role
    return $data;
} );


add_filter( 'wcfm_is_allow_store_setup', '__return_false' );

add_action('wcfmmp_new_store_created', function($vendor_id,$wcfm_vendor_form_data) {
	$wcfmmp_shipping = array ( '_wcfmmp_user_shipping_enable' => 'yes', '_wcfmmp_user_shipping_type' => 'by_country' );
	update_user_meta( $vendor_id, '_wcfmmp_shipping', $wcfmmp_shipping );
}, 2);

add_action( 'init', 'process_post', 1 );

function process_post() {
	$countries_obj   = new WC_Countries();
	$countries     = $countries_obj->__get('countries');
	$states        = $countries_obj->states;
	$countries = array_merge(array("none" => "Sin especificar", "everywhere" => "Todo el mundo"),$countries);


	if(isset($_POST["location_1578"]) && !empty($_POST["location_1578"]) && array_key_exists($_POST["location_1578"], $countries))
	{
		$country = sanitize_text_field( $_POST["location_1578"] );
		setcookie("customer_location_satoshiroad",$country,0,"/");
	}else{
		if(!isset($_COOKIE["customer_location_satoshiroad"]))
		{
			if( is_user_logged_in() && !isset( $_COOKIE["customer_location_satoshiroad"] ) ) {
				$user_location = get_user_meta( get_current_user_id(), 'wcfm_user_location', true );
				if( $user_location ) {
					$country = $user_location['country'];
				}
			}

			if( empty($country) ) {
				$user_location = WC_Geolocation::geolocate_ip('', true, true, true);
				$country = $user_location['country'];
			}
			if( empty($country) ) {
				$country = "none";
			}
			$country = "none";
			setcookie("customer_location_satoshiroad",$country,0,"/");
		}else
			$country = $_COOKIE["customer_location_satoshiroad"];
	}
	$_COOKIE["customer_location_satoshiroad"] = $country;
}