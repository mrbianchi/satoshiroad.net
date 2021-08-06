<?php
if(!function_exists('wcfmvm_woocommerce_inactive_notice')) {
	function wcfmvm_woocommerce_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCFM - Membership is inactive.%s The %sWooCommerce plugin%s must be active for the WCFM - Membership to work. Please %sinstall & activate WooCommerce%s', WCFMvm_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=woocommerce' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wcfmvm_wcfm_inactive_notice')) {
	function wcfmvm_wcfm_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCFM - Membership is inactive.%s The %sWooCommerce Frontend Manager%s must be active for the WCFM - Membership to work. Please %sinstall & activate WooCommerce Frontend Manager%s', WCFMvm_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/wc-frontend-manager/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=wc+frontend+manager' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wcfm_allowed_membership_user_roles')) {
	function wcfm_allowed_membership_user_roles() { 
		$allowed_membership_user_roles = apply_filters( 'wcfm_allowed_membership_user_roles',  array( 'disable_vendor', 'vendor', 'dc_vendor', 'seller', 'wcfm_vendor', 'wc_product_vendors_admin_vendor', 'customer', 'subscriber', 'editor', 'contributor', 'author' ) );
		return $allowed_membership_user_roles;
	}
}

if(!function_exists('wcfm_is_allowed_membership')) {
	function wcfm_is_allowed_membership() { 
		global $_SESSION;
		
		if( !is_user_logged_in() ) return true;
		
		$allowed_membership_user_roles = wcfm_allowed_membership_user_roles();
		$user = wp_get_current_user();
		if ( array_intersect( $allowed_membership_user_roles, (array) $user->roles ) )  {
			if( WC()->session ) {
				do_action( 'woocommerce_set_cart_cookies', true );
				WC()->session->set( 'wcfm_membership_mode', 'new' );
			}
			//$_SESSION['wcfm_membership']['mode'] = 'new';
			if( !wcfm_has_membership() ) { 
				return true;
			} else {
				if( WC()->session ) {
					do_action( 'woocommerce_set_cart_cookies', true );
					WC()->session->set( 'wcfm_membership_mode', 'upgrade' );
				}
				//$_SESSION['wcfm_membership']['mode'] = 'upgrade';
				return true;
			}
		}
		
		return apply_filters( 'wcfm_is_allowed_membership', false );
	}
}

if(!function_exists('wcfm_is_valid_membership')) {
	function wcfm_is_valid_membership( $wcfm_membership ) { 
		$wcfm_membership = absint( $wcfm_membership );
		if( !$wcfm_membership ) return false;
		if ( FALSE === get_post_status( $wcfm_membership ) ) return false;
		return true;
	}
}

if(!function_exists('wcfm_has_membership')) {
	function wcfm_has_membership() { 
		if( !is_user_logged_in() ) return false;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$wcfm_membership = get_user_meta( $user_id, 'wcfm_membership', true );
		if( !$wcfm_membership ) return false;
		if( !wcfm_is_valid_membership( $wcfm_membership ) ) return false;
		return apply_filters( 'wcfm_has_membership', $wcfm_membership );
	}
}

if(!function_exists('wcfm_get_membership')) {
	function wcfm_get_membership() { 
		$wcfm_membership = wcfm_has_membership();
		if( !$wcfm_membership ) return false;
		return apply_filters( 'wcfm_get_membership', $wcfm_membership );
	}
}

if(!function_exists('is_wcfm_membership_page')) {
	function is_wcfm_membership_page() {   
		//return wc_post_content_has_shortcode( 'wcfm_vendor_membership' );
		$pages = get_option("wcfm_page_options", array());
		if( isset( $pages['wcfm_vendor_membership_page_id'] ) && $pages['wcfm_vendor_membership_page_id'] ) {
			return is_page( $pages['wcfm_vendor_membership_page_id'] ) || wc_post_content_has_shortcode( 'wcfm_vendor_membership' );
		}
		return false;
	}
}

if(!function_exists('get_wcfm_membership_page')) {
	function get_wcfm_membership_page() {
		$pages = get_option("wcfm_page_options", array());
		if( isset( $pages['wcfm_vendor_membership_page_id'] ) && $pages['wcfm_vendor_membership_page_id'] ) {
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
				global $sitepress;
				$language_code = $sitepress->get_current_language();
				
				$membership_page = get_permalink( icl_object_id( $pages['wcfm_vendor_membership_page_id'], 'page', true, $language_code ) );
				$membership_page = apply_filters( 'wpml_permalink', $membership_page, $language_code );
				
				return $membership_page;
			} else {
				return get_permalink( $pages['wcfm_vendor_membership_page_id'] );
			}
		}
		return false;
	}
}

if(!function_exists('is_wcfm_registration_page')) {
	function is_wcfm_registration_page() {  
		//return wc_post_content_has_shortcode( 'wcfm_vendor_registration' );
		$pages = get_option("wcfm_page_options", array());
		if( isset( $pages['wcfm_vendor_registration_page_id'] ) && $pages['wcfm_vendor_registration_page_id'] ) {
			return is_page( $pages['wcfm_vendor_registration_page_id'] ) || wc_post_content_has_shortcode( 'wcfm_vendor_registration' );
		}
		return false;
	}
}

if(!function_exists('get_wcfm_registration_page')) {
	function get_wcfm_registration_page() {
		$pages = get_option("wcfm_page_options", array());
		if( isset( $pages['wcfm_vendor_registration_page_id'] ) && $pages['wcfm_vendor_registration_page_id'] ) {
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
				global $sitepress;
				$language_code = $sitepress->get_current_language();
				
				$registration_page = get_permalink( icl_object_id( $pages['wcfm_vendor_registration_page_id'], 'page', true, $language_code ) );
				$registration_page = apply_filters( 'wpml_permalink', $registration_page, $language_code );
				
				return $registration_page;
			} else {
				return get_permalink( $pages['wcfm_vendor_registration_page_id'] );
			}
		}
		return false;
	}
}

if(!function_exists('get_wcfm_membership_url')) {
	function get_wcfm_membership_url() {
		return apply_filters( 'wcfm_membership_home', get_wcfm_membership_page() );
	}
}

if(!function_exists('get_wcfm_membership_payment_methods')) {
	function get_wcfm_membership_payment_methods() {
		$wcfm_membership_payment_methods = array( 
			                                      'paypal'        => __( 'PayPal', 'wc-multivendor-membership' ), 
			                                      'stripe'        => __( 'Stripe', 'wc-multivendor-membership' ), 
			                                      'bank_transfer' => __( 'Bank Transfer', 'wc-multivendor-membership' ) 
			                                      );
		return apply_filters( 'wcfm_membership_payment_methods', $wcfm_membership_payment_methods );
	}
}

if(!function_exists('get_wcfm_memberships')) {
	function get_wcfm_memberships() {
		$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'wcfm_memberships',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('publish'),
							'suppress_filters' => true 
						);
		$args = apply_filters( 'wcfm_vendor_memberships_args', $args );
		$wcfm_memberships = get_posts( $args );
		
		$wcfm_active_memberships = array();
		if( !empty( $wcfm_memberships ) ) {
			foreach( $wcfm_memberships as $wcfm_membership ) {
				$is_disable = get_post_meta( $wcfm_membership->ID, 'is_wcfm_membership_disable', true );
				if( !$is_disable ) { 
					$wcfm_active_memberships[] = $wcfm_membership;
				}
			}
		}
		
		return apply_filters( 'wcfm_memberships', $wcfm_active_memberships );
	}
}

if(!function_exists('wcfm_membership_registration_steps')) {
	function wcfm_membership_registration_steps() {
		$wcfm_membership_registration_steps = array(  'choose_membership' => __( 'Plans', 'wc-multivendor-membership' ),
																									'registration'      => __( 'Registration', 'wc-multivendor-membership' ),
																									'payment'           => __( 'Confirmation', 'wc-multivendor-membership' ),
																									'thankyou'          => __( 'Thank You', 'wc-multivendor-membership' ),
																									//'cancel'            => __( 'Cancel', 'wc-multivendor-membership' )
																								);
		
		$membership_id = '';
		if( WC()->session && WC()->session->get( 'wcfm_membership' ) ) {
			$membership_id = absint( WC()->session->get( 'wcfm_membership' ) );
		}
		
		if( apply_filters( 'wcfmvm_is_allow_registration_first', false, $membership_id ) ) {
			$wcfm_membership_registration_steps = array(  
																										'registration'      => __( 'Registration', 'wc-multivendor-membership' ),
																										'choose_membership' => __( 'Plans', 'wc-multivendor-membership' ),
																										'payment'           => __( 'Confirmation', 'wc-multivendor-membership' ),
																										'thankyou'          => __( 'Thank You', 'wc-multivendor-membership' ),
																										//'cancel'            => __( 'Cancel', 'wc-multivendor-membership' )
																									);
		}
		
		if( $membership_id ) {
		  $subscription = (array) get_post_meta( $membership_id, 'subscription', true );
			$is_free = isset( $subscription['is_free'] ) ? 'yes' : 'no';
			if( $is_free != 'yes' ) {
				$wcfm_membership_registration_steps['payment'] = __( 'Payment', 'wc-multivendor-membership' );
			}
		}
		
		if( is_user_logged_in() ) {
			$wcfm_membership_registration_steps['registration'] = __( 'Profile', 'wc-multivendor-membership' );
		}
		
		return apply_filters( 'wcfm_membership_registration_steps', $wcfm_membership_registration_steps );
	}
}

if(!function_exists('wcfm_membership_registration_current_step')) {
	function wcfm_membership_registration_current_step() {
		if( apply_filters( 'wcfmvm_is_allow_registration_first', false ) ) {
			$current_step = 'registration';
		} else {
			$current_step = 'choose_membership';
		}
		//if( isset( $_SESSION['wcfm_membership'] ) && isset( $_SESSION['wcfm_membership']['membership'] ) && $_SESSION['wcfm_membership']['membership'] ) {
			if( isset( $_REQUEST['vmstep'] ) ) {
				$current_step = $_REQUEST['vmstep'];
			}
			if( isset( $_REQUEST['auth'] )  ) {
				$current_step = 'thankyou';
			}
		//}
		
		if( is_user_logged_in() ) {
			$member_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			$application_status = get_user_meta( $member_id, 'wcfm_membership_application_status', true );
			if( $application_status && ( $application_status == 'pending' ) ) {
				$current_step = 'thankyou';
			}
		}
		
		return apply_filters( 'wcfm_membership_registration_current_step', $current_step );
	}
}

if(!function_exists('get_wcfm_memberships_url')) {
	function get_wcfm_memberships_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_memberships_url = wcfm_get_endpoint_url( 'wcfm-memberships', '', $wcfm_page );
		return $wcfm_memberships_url;
	}
}

if(!function_exists('get_wcfm_memberships_manage_url')) {
	function get_wcfm_memberships_manage_url( $membership_id = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_membership_manage_url = wcfm_get_endpoint_url( 'wcfm-memberships-manage', $membership_id, $wcfm_page );
		return $wcfm_membership_manage_url;
	}
}

if(!function_exists('get_wcfm_memberships_settings_url')) {
	function get_wcfm_memberships_settings_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_membership_settings_url = wcfm_get_endpoint_url( 'wcfm-memberships-settings', '', $wcfm_page );
		return $wcfm_membership_settings_url;
	}
}

if(!function_exists('get_wcfmvm_membership_manage_messages')) {
	function get_wcfmvm_membership_manage_messages() {
		global $WCFMvm;
		
		$messages = array(
											'no_title'          => __( 'Please insert Membership Name before submit.', 'wc-multivendor-membership' ),
											'membership_failed' => __( 'Membership Saving Failed.', 'wc-multivendor-membership' ),
											'membership_saved'  => __( 'Membership Successfully Saved.', 'wc-multivendor-membership' ),
											);
		
		return $messages;
	}
}

if(!function_exists('get_wcfmvm_membership_registration_messages')) {
	function get_wcfmvm_membership_registration_messages() {
		global $WCFMu;
		
		$messages = array(
											'no_username'          => __( 'Please insert Username before submit.', 'wc-multivendor-membership' ),
											'no_email'             => __( 'Please insert Email before submit.', 'wc-multivendor-membership' ),
											'username_exists'      => __( 'This Username already exists. Please login to the site and apply as vendor.', 'wc-multivendor-membership' ),
											'email_exists'         => __( 'This Email already exists. Please login to the site and apply as vendor.', 'wc-multivendor-membership' ),
											'email_invalid_code'   => __( 'Email verification code invalid.', 'wc-multivendor-membership' ),
											'sms_invalid_code'     => __( 'Phone verification code (OTP) invalid.', 'wc-multivendor-membership' ),
											'registration_failed'  => __( 'Registration Failed.', 'wc-multivendor-membership' ),
											'registration_success' => __( 'Registration Successfully Completed.', 'wc-multivendor-membership' ),
											);
		
		return $messages;
	}
}

if(!function_exists('get_wcfmvm_membership_payment_messages')) {
	function get_wcfmvm_membership_payment_messages() {
		global $WCFMu;
		
		$messages = array(
											'no_memberid'          => __( 'No Membership ID found, please try again.', 'wc-multivendor-membership' ),
											'subscription_failed'  => __( 'Subscription Failed.', 'wc-multivendor-membership' ),
											'subscription_success' => __( 'Subscription Successfully Completed.', 'wc-multivendor-membership' ),
											);
		
		return $messages;
	}
}

if(!function_exists('get_wcfm_free_membership')) {
	function get_wcfm_free_membership() {
		$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
			
		$membership_type_settings = array();
		if( isset( $wcfm_membership_options['membership_type_settings'] ) ) $membership_type_settings = $wcfm_membership_options['membership_type_settings'];
		$free_membership = isset( $membership_type_settings['free_membership'] ) ? $membership_type_settings['free_membership'] : '';
	
		if( !$free_membership ) $free_membership = 0;
		
		return $free_membership;
	}
}

if(!function_exists('get_wcfm_basic_membership')) {
	function get_wcfm_basic_membership() {
		return get_wcfm_free_membership();
	}
}

if(!function_exists('wcfmvm_membership_table_tax_display')) {
	function wcfmvm_membership_table_tax_display( $html_handler = 'div' ) {
		if( !apply_filters( 'wcfm_is_allow_membership_table_tax_display', true ) ) return;
		
		$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
		$membership_tax_settings = array();
		if( isset( $wcfm_membership_options['membership_tax_settings'] ) ) $membership_tax_settings = $wcfm_membership_options['membership_tax_settings'];
		$tax_enable  = isset( $membership_tax_settings['enable'] ) ? 'yes' : 'no';
		$tax_name    = isset( $membership_tax_settings['name'] ) ? $membership_tax_settings['name'] : __( 'Tax', 'wc-multivendor-membership' );
		$tax_percent = isset( $membership_tax_settings['percent'] ) ? $membership_tax_settings['percent'] : '';
		
		if( ( $tax_enable == 'yes' ) && $tax_percent ) {
			$text = __( 'will be applied', 'wc-multivendor-membership' );
			if( $html_handler == 'span' ) $text = __( 'applied', 'wc-multivendor-membership' );
			echo '<' . $html_handler . ' class="wcfm_membership_price_description">' . $tax_percent . '% ' . $tax_name . ' ' . $text . '</' . $html_handler . '>';
		}
	}
}

if(!function_exists('wcfmvm_membership_tax_price')) {
	function wcfmvm_membership_tax_price( $price ) {
		$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
		$membership_tax_settings = array();
		if( isset( $wcfm_membership_options['membership_tax_settings'] ) ) $membership_tax_settings = $wcfm_membership_options['membership_tax_settings'];
		$tax_enable  = isset( $membership_tax_settings['enable'] ) ? 'yes' : 'no';
		$tax_name    = isset( $membership_tax_settings['name'] ) ? $membership_tax_settings['name'] : __( 'Tax', 'wc-multivendor-membership' );
		$tax_percent = isset( $membership_tax_settings['percent'] ) ? $membership_tax_settings['percent'] : '';
		
		if( ( $tax_enable == 'yes' ) && $tax_percent ) {
			$price += wc_format_decimal( $price * ($tax_percent/100) );
		}
		return apply_filters( 'wcfmvm_membership_tax_price', $price );
	}
}

if(!function_exists('wcfmvm_create_log')) {
	function wcfmvm_create_log( $info ) {
		//if(  defined('DOING_AJAX') ) return;
		
		$upload_dir      = wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/wcfm',
				'file' 		=> 'subscription_ipn.log',
				'content' 	=> $info . "\r\n",
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'a' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}
}
?>