<?php
/**
 * The Template for displaying store list.
 *
 * @package WCfM Markeplace Views Store Lists
 *
 * For edit coping this to yourtheme/wcfm/store/store-lists
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp, $post;

$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
if ( !$api_key ) return;
if( !apply_filters( 'wcfmmp_is_allow_store_list_map', true ) ) return;

?>

<div id="wcfmmp-store-list-map" class="wcfmmp-store-list-map"></div>

<script>
  $map_zoom    = <?php echo absint($map_zoom); ?>;
  $auto_zoom   = '<?php echo $auto_zoom; ?>';
  $per_row     = '<?php echo $per_row; ?>';
	$per_page    = '<?php echo $limit; ?>';
	$includes    = '<?php echo implode(",", $includes ); ?>';
	$excludes    = '<?php echo $excludes; ?>';
	$has_product = '<?php echo $has_product; ?>';
	$sidebar     = '<?php echo $sidebar; ?>';
	$has_orderby = '<?php echo $has_orderby; ?>';
	$theme       = '<?php echo $theme; ?>';
</script>