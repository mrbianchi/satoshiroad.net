<tr valign="top" id="packing_options" class ="package_tab_field">
	<td class="forminp" colspan="2" style="padding-left:0px">
		<strong><?php _e( 'Box Dimensions', 'wf-easypost' ); ?></strong><br/>
		<style type="text/css">
			.easypost_boxes td, .easypost_services td, .easypost_boxes th, .easypost_services th {
				vertical-align: middle;
				padding: 4px 7px;
			}
			.easypost_boxes td input {
				margin-right: 4px;
			}
			.easypost_boxes .check-column {
				vertical-align: middle;
				text-align: left;
				padding: 0 7px;
			}
			.easypost_services th.sort {
				width: 16px;
			}
			.easypost_services td.sort {
				cursor: move;
				width: 16px;
				padding: 0;
				cursor: move;
				background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;					}
		</style>
		<table class="easypost_boxes widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th><?php _e( 'Name', 'wf-easypost' ); ?></th>
					<th><?php _e( 'Length', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Width', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Height', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Inner Length', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Inner Width', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Inner Height', 'wf-easypost' ); ?> (in)</th>
					<th><?php _e( 'Box Weight', 'wf-easypost' ); ?> (lbs)</th>
					<th><?php _e( 'Max Weight', 'wf-easypost' ); ?> (lbs)</th>
					<th><?php _e( 'Letter', 'wf-easypost' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="3">
						<a href="#" class="button plus insert"><?php _e( 'Add Box', 'wf-easypost' ); ?></a>
						<a href="#" class="button minus remove"><?php _e( 'Remove selected box(es)', 'wf-easypost' ); ?></a>
					</th>
					<th colspan="8">
						<small class="description"><?php _e( 'Items will be packed into these boxes based on item dimensions and volume. Outer dimensions will be passed to Easypost - USPS, CanadaPost, UPS and FedEx, whereas inner dimensions will be used for packing. Items not fitting into boxes will be packed individually. Letter check box is in beta testing mode and currently not supported.', 'wf-easypost' ); ?></small>
					</th>
				</tr>
			</tfoot>
			<tbody id="rates">
				<?php
					if ( $this->boxes ) {
						foreach ( $this->boxes as $key => $box ) {
							?>
							<tr>
								<td class="check-column"><input type="checkbox" /></td>
								<td><input type="text" size="10" name="boxes_name[<?php echo $key; ?>]" value="<?php echo isset( $box['name'] ) ? esc_attr( $box['name'] ) : ''; ?>" /></td>
								<td><input type="text" size="5" name="boxes_outer_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_length'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_outer_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_width'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_outer_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_height'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_inner_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_length'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_inner_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_width'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_inner_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_height'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_box_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['box_weight'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_max_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['max_weight'] ); ?>" /></td>
								<td><input type="checkbox" name="boxes_is_letter[<?php echo $key; ?>]" <?php checked( isset( $box['is_letter'] ) && $box['is_letter'] == true, true ); ?> /></td>
							</tr>
							<?php
						}
					}
				?>
			</tbody>
		</table>
		<script type="text/javascript">

			jQuery(window).load(function(){

				jQuery('.easypost_boxes .insert').click( function() {
					var $tbody = jQuery('.easypost_boxes').find('tbody');
					var size = $tbody.find('tr').size();
					var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<td><input type="text" size="10" name="boxes_name[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_outer_length[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_outer_width[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_outer_height[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_inner_length[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_inner_width[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_inner_height[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" />lbs</td>\
							<td><input type="text" size="5" name="boxes_max_weight[' + size + ']" />lbs</td>\
							<td><input type="checkbox" name="boxes_is_letter[' + size + ']" /></td>\
						</tr>';

					$tbody.append( code );

					return false;
				} );

				jQuery('.easypost_boxes .remove').click(function() {
					var $tbody = jQuery('.easypost_boxes').find('tbody');

					$tbody.find('.check-column input:checked').each(function() {
						jQuery(this).closest('tr').hide().find('input').val('');
					});

					return false;
				});

				// Ordering
				jQuery('.easypost_services tbody').sortable({
					items:'tr',
					cursor:'move',
					axis:'y',
					handle: '.sort',
					scrollSensitivity:40,
					forcePlaceholderSize: true,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'wc-metabox-sortable-placeholder',
					start:function(event,ui){
						ui.item.css('baclbsround-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
						easypost_services_row_indexes();
					}
				});

				function easypost_services_row_indexes() {
					jQuery('.easypost_services tbody tr').each(function(index, el){
						jQuery('input.order', el).val( parseInt( jQuery(el).index('.easypost_services tr') ) );
					});
				};

			});

		</script>
	</td>
</tr>