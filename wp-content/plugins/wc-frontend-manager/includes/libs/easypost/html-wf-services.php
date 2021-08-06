<tr valign="top" id="service_options" class="rates_tab_field">
	<td class="forminp" colspan="2" style="padding-left:0px">
	<strong><?php _e( 'Services', 'wf-easypost' ); ?></strong><br/>
		<table class="easypost_services widefat">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php _e( 'Service(s)', 'wf-easypost' ); ?></th>
				<th><?php _e( 'Name', 'wf-easypost' ); ?></th>
				<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'wf-easypost' ), get_woocommerce_currency_symbol() ); ?></th>
				<th><?php _e( 'Price Adjustment (%)', 'wf-easypost' ); ?></th>
			</thead>
			<tbody>
				<?php
					$sort = 0;
					$this->ordered_services = array();
					
					foreach ( $this->services as $code => $values ) {
						$ordered_services = array();
						// if( in_array($code, $this->carrier) ){
							foreach ($values['services'] as $key => $value) {
								if ( is_array($this->custom_services) && isset( $this->custom_services[ $code ][ $key ]['order'] ) && !empty( $this->custom_services[ $code ][ $key ]['order'] ) ){
									$sort = $this->custom_services[ $code ] [ $key ] ['order'];
								}

								while ( isset( $this->ordered_services[ $sort ] ) ) {
                                    $sort++;
                                }
									
								if( !empty( $this->custom_services ) && array_key_exists( $code, $this->custom_services ) ){
									$ordered_services[ $sort ] = array( $key, $this->custom_services[ $code ][ $key ] );
								}
								else{
									$ordered_services[ $sort ] = array( 
										$key, array(
											$code => array(
												$key=> array(
													'enalbled' => true,
													'adjustment' => '',
													'adjustment_percent' => '',
													'name' => '',
													'order' =>'' 
												)
											)
										)
									);
								}

								$sort++;
							}
						// }
						$this->ordered_services[ $code ] = $ordered_services;
					}


					foreach ($this->ordered_services as $key => $value) {
						ksort( $this->ordered_services[$key] );
					}
					foreach ( $this->ordered_services as $code => $value ) {
						if ( !empty($this->custom_services) &&  !isset( $this->custom_services[$code] ) )
                            {
                                $this->custom_services[$code] = array();
                            }
												
						foreach ( $value as $order => $values ){
							$key   = $values[0];
							
							?>
							<tr class="services" carrier="<?php echo $code; ?>">
								<td class="sort">
									<input type="hidden" class="order" name="easypost_service[<?php echo $code; ?>][<?php echo $key; ?>][order]" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['order'] ) ? $this->custom_services[ $code ][ $key ]['order'] : ''; ?>" />
								</td>
								<td>
									<label>
										<input type="checkbox" name="easypost_service[<?php echo $code; ?>][<?php echo $key; ?>][enabled]" <?php checked( ( ! isset( $this->custom_services[ $code ][ $key ]['enabled'] ) || ! empty( $this->custom_services[ $code ][ $key ]['enabled'] ) ), true ); ?> />
										<?php echo $key; ?>
									</label>
								</td>
								<td>	
									<input type="text" name="easypost_service[<?php echo $code; ?>][<?php echo $key; ?>][name]" placeholder="<?php echo (string)$this->services[$code]['services'][$key] ?>" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['name'] ) ? $this->custom_services[ $code ][ $key ]['name'] : ''; ?>" size="30" />
								</td>
								<td>
									<?php echo get_woocommerce_currency_symbol(); ?><input type="text" name="easypost_service[<?php echo $code; ?>][<?php echo $key; ?>][adjustment]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['adjustment'] ) ? $this->custom_services[ $code ][ $key ]['adjustment'] : ''; ?>" size="4" />
								</td>
								<td>
									<input type="text" name="easypost_service[<?php echo $code; ?>][<?php echo $key; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['adjustment_percent'] ) ? $this->custom_services[ $code ][ $key ]['adjustment_percent'] : ''; ?>" size="4" />%
								</td>
							</tr>
							<?php
						}
					}
				?>
			</tbody>
		</table>
	</td>
</tr>
