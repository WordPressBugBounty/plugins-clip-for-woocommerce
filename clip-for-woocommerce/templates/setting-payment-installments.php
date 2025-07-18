<?php
/**
 * Template for the payment installments setting.
 *
 * @package Ecomerciar\Clip
 */

$pre_key              = $args['key'];
$settings             = $args['settings'];
$payment_installments = $args['payment_installments'];
$labels               = $args['labels'];
?>

<tr valign="top" id="wc_clip_payment_installments_container">
	<th scope="row" class="titledesc">
		<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>"><?php echo esc_html( $settings['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset class="payment_installments">
			<table>
					<thead>
						<tr>
							<th><?php echo esc_html__( 'Installment', 'clip' ); ?></th>
							<th style="text-align: center;"><?php echo esc_html__( 'Enabled', 'clip' ); ?></th>
							<th style="text-align: center;"><?php echo esc_html__( 'Min Amount', 'clip' ); ?></th>
						</tr>
					</thead>
			<?php foreach ( $labels as $key => $label ) : ?>
				<?php $enabled = isset( $payment_installments[ $key ]['enabled'] ) ? $payment_installments[ $key ]['enabled'] : 'no'; ?>
				<?php $min_amount = isset( $payment_installments[ $key ]['value'] ) ? $payment_installments[ $key ]['value'] : 0; ?>
				<?php $installment = isset( $payment_installments[ $key ]['installment'] ) ? $payment_installments[ $key ]['installment'] : 0; ?>
				<input type="hidden" name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[<?php echo esc_attr( $key ); ?>][installment]" value="<?php echo esc_attr( $installment ); ?>">
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<td style="text-align: center;">
							<input type="checkbox" 
								name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[<?php echo esc_attr( $key ); ?>][enabled]" 
								value="yes" 
								class="installment-checkbox"
								data-id="<?php echo esc_attr( $key ); ?>"
								<?php checked( $enabled, 'yes' ); ?> />
						</td>
						<td>
							<input type="number" 
								name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[<?php echo esc_attr( $key ); ?>][value]"
								value="<?php echo esc_attr( $min_amount ); ?>"
								step=".01"
								min="0"	
								class="min-amount-input"
								id="min_amount_<?php echo esc_attr( $key ); ?>" />
								
						</td>
					</tr>				
			<?php endforeach; ?>
			</table>
		</fieldset>
	</td>
</tr>
