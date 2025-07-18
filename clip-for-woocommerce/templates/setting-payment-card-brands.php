<?php
/**
 * Template for the payment card brands setting.
 *
 * @package Ecomerciar\Clip
 */

use Ecomerciar\Clip\Helper\Helper;

$pre_key             = $args['key'];
$settings            = $args['settings'];
$payment_card_brands = $args['payment_card_brands'];
$brands              = $args['brands'];
?>

<tr valign="top" id="wc_clip_payment_brands_container">
	<th scope="row" class="titledesc">
		<label for="woocommerce_wc_clip_wc_clip_payment"><?php echo esc_html( $settings['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset class="payment_card_brands">
			<?php foreach ( $brands as $brand ) : ?>
				<?php $checked = isset( $payment_card_brands[ $brand['value'] ] ) ? $payment_card_brands[ $brand['value'] ] : 'no'; ?>
				<label for="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[cards]">                    
					<input name="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[<?php echo esc_attr( $brand['value'] ); ?>]" id="woocommerce_wc_clip_<?php echo esc_attr( $pre_key ); ?>[<?php echo esc_attr( $brand['value'] ); ?>]" type="checkbox" value="yes" class="checkbox" <?php echo esc_attr( 'yes' === $checked ? 'checked' : '' ); ?> /> 
					<img class="brand_icon" src="<?php echo esc_url( $brand['icon'] ); ?>" alt="<?php echo esc_attr( $brand['label'] ); ?>" />					                    
				</label>
			<?php endforeach; ?>
		</fieldset>
		<p>
			<?php echo esc_html__( 'Select the available cards.', 'clip' ); ?>
		</p>  
	</td>
</tr>
