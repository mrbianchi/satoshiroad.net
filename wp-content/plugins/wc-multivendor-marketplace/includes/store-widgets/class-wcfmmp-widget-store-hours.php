<?php

/**
 * WCFM Marketplace Store Hours Widget
 *
 * @since 1.0.0
 *
 */
class WCFMmp_Store_Hours_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$widget_ops = array( 'classname' => 'wcfmmp-store-hours-widget', 'description' => __( 'Store Hours', 'wc-multivendor-marketplace' ) );
		parent::__construct( 'wcfmmp-store-hours-widget', __( 'Vendor Store: Opening/Closing Hours', 'wc-multivendor-marketplace' ), $widget_ops );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array  An array of standard parameters for widgets in this theme
	 * @param array  An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance ) {
		global $WCFM, $WCFMmp, $post;
		
		if( !apply_filters( 'wcfm_is_pref_store_hours', true ) ) return;

		if ( ! wcfmmp_is_store_page() && !is_product() ) {
				return;
		}

		extract( $args, EXTR_SKIP );

		$title        = apply_filters( 'widget_title', $instance['title'] );
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		
		$is_store_offline = get_user_meta( $store_id, '_wcfm_store_offline', true );
		if ( $is_store_offline ) {
			return;
		}
		
		$is_disable_vendor = get_user_meta( $store_id, '_disable_vendor', true );
		if ( $is_disable_vendor ) return;
		
		if( !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'store_hours' ) ) return;
		
		//if( !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'hours_setting' ) ) return;
		
		$wcfm_vendor_store_hours = get_user_meta( $store_id, 'wcfm_vendor_store_hours', true );
		if( !$wcfm_vendor_store_hours ) {
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
			return;
		}
		
		$wcfm_store_hours_enable = isset( $wcfm_vendor_store_hours['enable'] ) ? 'yes' : 'no';
		if( $wcfm_store_hours_enable != 'yes' ) return;
		
		$wcfm_store_hours_off_days  = isset( $wcfm_vendor_store_hours['off_days'] ) ? $wcfm_vendor_store_hours['off_days'] : array();
		$wcfm_store_hours_day_times = isset( $wcfm_vendor_store_hours['day_times'] ) ? $wcfm_vendor_store_hours['day_times'] : array();
		if( empty( $wcfm_store_hours_day_times ) ) return;
		
		// Old Store Hours Migrating
		$wcfm_vendor_store_hours_migrated = get_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', true );
		if( !empty( array_filter( $wcfm_vendor_store_hours ) ) && !$wcfm_vendor_store_hours_migrated ) {
			$wcfm_store_hours_mon_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[0]['start'] ) ? $wcfm_store_hours_day_times[0]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[0]['end'] ) ? $wcfm_store_hours_day_times[0]['end'] : '' ) );
			$wcfm_store_hours_tue_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[1]['start'] ) ? $wcfm_store_hours_day_times[1]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[1]['end'] ) ? $wcfm_store_hours_day_times[1]['end'] : '' ) );
			$wcfm_store_hours_wed_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[2]['start'] ) ? $wcfm_store_hours_day_times[2]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[2]['end'] ) ? $wcfm_store_hours_day_times[2]['end'] : '' ) );
			$wcfm_store_hours_thu_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[3]['start'] ) ? $wcfm_store_hours_day_times[3]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[3]['end'] ) ? $wcfm_store_hours_day_times[3]['end'] : '' ) );
			$wcfm_store_hours_fri_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[4]['start'] ) ? $wcfm_store_hours_day_times[4]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[4]['end'] ) ? $wcfm_store_hours_day_times[4]['end'] : '' ) );
			$wcfm_store_hours_sat_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[5]['start'] ) ? $wcfm_store_hours_day_times[5]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[5]['end'] ) ? $wcfm_store_hours_day_times[5]['end'] : '' ) );
			$wcfm_store_hours_sun_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[6]['start'] ) ? $wcfm_store_hours_day_times[6]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[6]['end'] ) ? $wcfm_store_hours_day_times[6]['end'] : '' ) );
			
			$wcfm_store_hours_day_times = array( 0 => $wcfm_store_hours_mon_times,
																					 1 => $wcfm_store_hours_tue_times,
																					 2 => $wcfm_store_hours_wed_times,
																					 3 => $wcfm_store_hours_thu_times,
																					 4 => $wcfm_store_hours_fri_times,
																					 5 => $wcfm_store_hours_sat_times,
																					 6 => $wcfm_store_hours_sun_times
																					);
			
			$wcfm_vendor_store_hours['day_times'] = $wcfm_store_hours_day_times;
			update_user_meta( $store_id, 'wcfm_vendor_store_hours', $wcfm_vendor_store_hours );
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
		} else {
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
		}
		
		$weekdays = array( 0 => __( 'Monday', 'wc-multivendor-marketplace' ), 1 => __( 'Tuesday', 'wc-multivendor-marketplace' ), 2 => __( 'Wednesday', 'wc-multivendor-marketplace' ), 3 => __( 'Thursday', 'wc-multivendor-marketplace' ), 4 => __( 'Friday', 'wc-multivendor-marketplace' ), 5 => __( 'Saturday', 'wc-multivendor-marketplace' ), 6 => __( 'Sunday', 'wc-multivendor-marketplace') );
		
		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . '<span class="wcfmfa fa-clock"></span>&nbsp;' . $title . $args['after_title'];
		}
		
		do_action( 'wcfmmp_store_before_sidebar_store_hours', $store_id );
		
		echo '<div class="wcfmmp_store_hours">';
		
		$WCFMmp->template->get_template( 'store/widgets/wcfmmp-view-store-hours.php', array( 
			                                             'wcfm_store_hours_day_times' => $wcfm_store_hours_day_times, 
			                                             'wcfm_store_hours_off_days'  => $wcfm_store_hours_off_days,
			                                             'weekdays' => $weekdays,
			                                             'store_id' => $store_id,
			                                             ) );
		
		echo '</div>';

		do_action( 'wcfmmp_store_after_sidebar_store_hours', $store_id );

		echo $after_widget;
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 *
	 * @param array  An array of new settings as submitted by the admin
	 * @param array  An array of the previous settings
	 *
	 * @return array The validated and (if necessary) amended settings
	 */
	function update( $new_instance, $old_instance ) {

			// update logic goes here
			$updated_instance = $new_instance;
			return $updated_instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array  An array of the current settings for this widget
	 *
	 * @return void Echoes it's output
	 */
	function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array(
					'title' => __( 'Store Hours', 'wc-multivendor-marketplace' ),
			) );

			$title = $instance['title'];
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wc-multivendor-marketplace' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<?php
	}
}
