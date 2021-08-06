<?php
/**
 * The Template for displaying store sidebar category.
 *
 * @package WCfM Markeplace Views Store List Loop
 *
 * For edit coping this to yourtheme/wcfm/store-lists
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp;

if ( empty( $stores )  ) return;

$args = array(
		'stores'          => $stores,
		'per_row'         => $per_row,
		'limit'           => $limit,
		'offset'          => $offset,
		'category'        => $category,
		'country'         => $country,
		'state'           => $state,
		'search_category' => $search_category,
		'has_product'     => $has_product,
		'theme'           => $theme
);

$carousel_id = 'wcfmmp-stores-carousel-' . uniqid();
$carousel_args 	= apply_filters( 'wcfmmp_stores_carousel_args', array(
	'items'				      => $per_row,
	'margin'            => 10,
	'loop'              => true,
  'autoplay'          => true,
  'autoplayTimeout'   => 2000,
	'autoplayHoverPause'=> true,
	'nav'				      => true,
	'dots'				    => false,
	//'slideTransition' => 'linear',
	'rtl'				      => is_rtl() ? true : false,
	'paginationSpeed'	=> 400,
	'navText'			    => is_rtl() ? array( '<i class="wcfmfa fa-chevron-right"></i>', '<i class="wcfmfa fa-chevron-left"></i>' ) : array( '<i class="wcfmfa fa-chevron-left"></i>', '<i class="wcfmfa fa-chevron-right"></i>' ),
	'margin'			    => 0,
	'touchDrag'			  => false,
	'responsive'		=> array(
					'0'		=> array( 'items'	=> 1 ),
					'480'	=> array( 'items'	=> 2 ),
					'768'	=> array( 'items'	=> 2 ),
					'992'	=> array( 'items'	=> 3 ),
					'1200'	=> array( 'items' => 4 ),
				)
) );

?>

<div id="wcfmmp-stores-wrap">
	<div id="<?php echo esc_attr( $carousel_id );?>" class="wcfmmp-stores-content">
		
		<ul class="wcfmmp-store-wrap owl-carousel">
			<?php
			foreach ( $stores as $store_id => $store_name ) {
				$args['store_id'] = $store_id;
				$WCFMmp->template->get_template( 'store-lists/wcfmmp-view-store-lists-card.php', $args );
			}
			?>
		</ul> <!-- .wcfmmp-store-wrap -->
		
		<?php	wp_enqueue_script( 'wcfmmp_stores_carousel_js', $WCFMmp->library->js_lib_url . 'carousel/wcfmmp-script-owl-carousel.min.js', array('jquery'), $WCFMmp->version, true );
		wp_enqueue_style( 'wcfmmp_store_carousel_css',  $WCFMmp->library->css_lib_url . 'carousel/wcfmmp-style-owl-carousel.min.css', array(), $WCFMmp->version );
		wp_enqueue_style( 'wcfmmp_store_list_css',  $WCFMmp->library->css_lib_url_min . 'store-lists/wcfmmp-style-stores-list.css', array(), $WCFMmp->version );
		
		if( $theme == 'classic' ) {
			wp_enqueue_style( 'wcfmmp_store_list_classic_css',  $WCFMmp->library->css_lib_url_min . 'store-lists/wcfmmp-style-stores-list-classic.css', array( 'wcfmmp_store_list_css' ), $WCFMmp->version );
		}
				
		if( is_rtl() ) {
			wp_enqueue_style( 'wcfmmp_store_list_rtl_css',  $WCFMmp->library->css_lib_url_min . 'store-lists/wcfmmp-style-stores-list-rtl.css', array('wcfmmp_store_list_css'), $WCFMmp->version );
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$( '#<?php echo esc_attr( $carousel_id ); ?> .owl-carousel').owlCarousel(<?php echo json_encode( $carousel_args ); ?>);
			});
		</script>
		<style>
		#wcfmmp-stores-wrap ul.owl-carousel li {width: 100% !important;}
		.wcfmmp-stores-content .owl-carousel .owl-nav{display:block;}
		</style>
	</div>
</div>