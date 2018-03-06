<?php
/**
 * Checkout terms and conditions area.
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( 'yes' === get_option( 'woocommerce_checkout_privacy_checkbox', 'no' ) ) {
	do_action( 'woocommerce_checkout_before_privacy_policy' );

	?>
	<div class="woocommerce-privacy-policy">
		<div class="woocommerce-privacy-policy-text">
			<?php woocommerce_output_privacy_policy_text(); ?>
		</div>

		<p class="form-row validate-required">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="privacy" <?php checked( isset( $_POST['privacy'] ), true ); ?> id="privacy" />
				<span><?php esc_html_e( 'I agree to the storage and handling of my information by this website.', 'woocommerce' ); ?></span> <span class="required">*</span>
			</label>
			<input type="hidden" name="privacy-field" value="1" />
		</p>
	</div>
	<?php

	do_action( 'woocommerce_checkout_after_privacy_policy' );
}

$terms_page = wc_get_page_id( 'terms' ) > 0 ? get_post( wc_get_page_id( 'terms' ) ) : false;

if ( $terms_page && 'publish' === $terms_page->post_status && apply_filters( 'woocommerce_checkout_show_terms', true ) ) {
	do_action( 'woocommerce_checkout_before_terms_and_conditions' );

	if ( $terms_page->post_content && ! has_shortcode( $terms_page->post_content, 'woocommerce_checkout' ) ) {
		?>
		<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">
			<?php echo wc_format_content( $terms_page->post_content ); ?>
		</div>
		<?php
	}

	?>
	<p class="form-row terms wc-terms-and-conditions validate-required">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
			<span><?php echo wp_kses_post( sprintf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank" class="woocommerce-terms-and-conditions-link">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ) ); ?></span> <span class="required">*</span>
		</label>
		<input type="hidden" name="terms-field" value="1" />
	</p>
	<?php

	do_action( 'woocommerce_checkout_after_terms_and_conditions' );
}
