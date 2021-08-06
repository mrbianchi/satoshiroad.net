<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>
<style>
	div#content {
		margin-top: 0;
	}
	nav.woocommerce-MyAccount-navigation {
    	background-color: #1e1e1e;
    	padding-left: 15px;
	}
	.woocommerce-MyAccount-navigation ul li a:before {
		float: left;
		margin-left: unset;
		margin-right: .5407911001em;
	}
	.woocommerce-MyAccount-navigation ul li a:hover:after {
    	right: 0;
    	border: 12px solid transparent;
    	content: " ";
    	height: 0;
    	width: 0;
    	position: absolute;
    	pointer-events: none;
    	border-right-color: #d37f0b;
		top: 50%;
    	margin-top: -15px;
	}

	.woocommerce-MyAccount-navigation ul li a {
		color: white;
		transition: color .5s;
	}
	
	.woocommerce-MyAccount-navigation ul li a:hover {
		color: #d37f0b;
		transition: color .5s;
	}
	main#main, main#main article {
		margin-bottom: 0;
	}
	.page-template-template-fullwidth-php .woocommerce-MyAccount-content {
		margin-top: 40px;
	}
	.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--edit-inquiry a:before,
	.woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--inquiry a:before{
		content: "\f15c";
	}

	.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--wcfm-store-manager a:before {
		content: "\f54e";
	}
	.woocommerce-MyAccount-navigation ul {
			display: none;
	}
	@media (min-width: 768px) {
		#woocommerce-MyAccount-navigation_mobile_button {
			display: none;
		}
		.woocommerce-MyAccount-navigation ul {
			display: block;
		}
	}
	div#woocommerce-MyAccount-navigation_mobile_button button::before {
    	content: '\f03a';
    	font-family: 'Font Awesome 5 Free';
    	font-weight: 900;
    	margin-right: 10px;
    	font-size: 13px;
	}
	.page-template-template-fullwidth-php .woocommerce-MyAccount-content {
    	margin-top: 20px;
	}
</style>
<nav class="woocommerce-MyAccount-navigation">
	<div id="woocommerce-MyAccount-navigation_mobile_button">
		<button>Secciones</button>
	</div>
	<script>
		(function($) {
			$("div#woocommerce-MyAccount-navigation_mobile_button button").click(function(){
				$("nav.woocommerce-MyAccount-navigation ul").toggle('slow');
			});
		})(jQuery);
	</script>
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
