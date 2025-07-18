<?php
/**
 * Template for the payment types setting.
 *
 * @package Clip
 */

$pre_key      = $args['key'];
$settings     = $args['settings'];
$payment_type = $args['payment_type'];

$payment_cards_enabled         = isset( $payment_type['cards'] ) ? $payment_type['cards'] : 'no';
$payment_credit_enabled        = isset( $payment_type['credit'] ) ? $payment_type['credit'] : 'no';
$payment_debit_enabled         = isset( $payment_type['debit'] ) ? $payment_type['debit'] : 'no';
$payment_cash_enabled          = isset( $payment_type['cash'] ) ? $payment_type['cash'] : 'no';
$payment_bank_transfer_enabled = isset( $payment_type['bank_transfer'] ) ? $payment_type['bank_transfer'] : 'no';

$labels = array(
	'cards'         => __( 'Cards', 'clip' ),
	'cash'          => __( 'Cash', 'clip' ),
	'bank_transfer' => __( 'Spei', 'clip' ),
	'credit'        => __( 'Credit', 'clip' ),
	'debit'         => __( 'Debit', 'clip' ),
);
?>
<tr valign="top" id="wc_clip_payment_type_container">
<th scope="row" class="titledesc">
		<label for="woocommerce_wc_clip_wc_clip_payment"><?php echo esc_html( $settings['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset class="payment_types">
			<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cards]">
				<strong><?php echo esc_html( $labels['cards'] ); ?></strong>
				<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cards]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cards]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $payment_cards_enabled ? 'checked' : '' ); ?> /> 
			</label>
			<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cash]">
				<strong><?php echo esc_html( $labels['cash'] ); ?></strong>
				<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cash]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cash]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $payment_cash_enabled ? 'checked' : '' ); ?> /> 
			</label>
			<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[bank_transfer]">
				<strong><?php echo esc_html( $labels['bank_transfer'] ); ?></strong>
				<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[bank_transfer]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[bank_transfer]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $payment_bank_transfer_enabled ? 'checked' : '' ); ?> /> 
			</label>                      
		</fieldset>
			<p>
				<?php echo esc_html__( 'Select the available payment types.', 'clip' ); ?>
			</p> 
		<fieldset class="cards-options">
			<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[credit]">
				<strong><?php echo esc_html( $labels['credit'] ); ?></strong>
				<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[credit]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[credit]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $payment_credit_enabled ? 'checked' : '' ); ?> /> 
			</label>
			<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[debit]">
				<strong><?php echo esc_html( $labels['debit'] ); ?></strong>
				<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[debit]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[debit]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $payment_debit_enabled ? 'checked' : '' ); ?> /> 
			</label>                       
		</fieldset>
		<p class="cards-options-label">
			<?php echo esc_html__( 'Select the available payment brands.', 'clip' ); ?>
		</p>  
	</td>
</tr>
