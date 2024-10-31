<?php if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<td class="titledesc" colspan="2" style="padding-left:0px">
	<strong><u><?php _e( 'Shipping Services & Price Adjustments', 'ma_pu_ship' ); ?></u></strong><br><br>
		<table class="widefat" style="background:unset;">
			<thead>
				<th><?php _e( 'Service Code', 'ma_pu_ship' ); ?></th>
				<th><?php _e( 'Name', 'ma_pu_ship' ); ?></th>
				<th><?php _e( 'Enabled', 'ma_pu_ship' ); ?></th>
				<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'ma_pu_ship' ), get_woocommerce_currency_symbol() ); ?></th>
				<th><?php _e( 'Price Adjustment (%)', 'ma_pu_ship' ); ?></th>
			</thead>
			<tbody>
				<?php
					$sort = 0;
					$this->ordered_services = array();

					foreach ( $this->services as $code => $name ) {

						if ( isset( $this->custom_services[ $code ]['order'] ) ) {
							$sort = $this->custom_services[ $code ]['order'];
						}

						while ( isset( $this->ordered_services[ $sort ] ) )
							$sort++;

						$this->ordered_services[ $sort ] = array( $code, $name );

						$sort++;
					}

					ksort( $this->ordered_services );

					foreach ( $this->ordered_services as $value ) {
						$code = $value[0];
						$name = $value[1];
						?>
						<tr>
							<td><strong><?php echo $code; ?></strong></td>
							<td><input type="text" name="pu_services[<?php echo $code; ?>][name]" placeholder="<?php echo $name; ?>" value="<?php echo isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : ''; ?>" size="50" /></td>
							<td><input type="checkbox" name="pu_services[<?php echo $code; ?>][enabled]" <?php checked( ( (isset( $this->custom_services[ $code ]['enabled'] ) && $this->custom_services[ $code ]['enabled'] == true) || (!isset($this->custom_services[ $code ]['name']) )), true ); ?> /></td>
							<td><input type="text" name="pu_services[<?php echo $code; ?>][adjustment]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : ''; ?>" size="10" /></td>
							<td><input type="text" name="pu_services[<?php echo $code; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : ''; ?>" size="10" /></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</td>
</tr>