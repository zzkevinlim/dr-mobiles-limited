<?php
/**
 * Afterpay Checkout Instalments Display
 * @var WC_Gateway_Afterpay $this
 */

if ($this->settings['testmode'] != 'production') {
    ?><p class="afterpay-test-mode-warning-text"><?php _e( 'TEST MODE ENABLED', 'woo_afterpay' ); ?></p><?php
}

if ($this->get_js_locale() != 'fr_CA') {
?>
    <div id="afterpay-widget-container"></div>
    <script>
    new AfterPay.Widgets.PaymentSchedule({
        target: '#afterpay-widget-container',
        locale: '<?php echo $locale; ?>',
        amount: {
            amount: "<?php echo $order_total; ?>",
            currency: "<?php echo $currency; ?>"
        },
    });
    </script>
<?php
} else {
?>
    <div class="instalment-info-container" id="afterpay-checkout-instalment-info-container">
        <p class="header-text">
            <?php _e( 'Four interest-free payments totalling', 'woo_afterpay' ); ?>
            <strong><?php echo wc_price($order_total); ?></strong>
        </p>
        <div class="instalment-wrapper">
            <afterpay-price-table
                data-amount="<?php echo $order_total; ?>"
                data-locale="<?php echo $this->get_js_locale(); ?>"
                data-currency="<?php echo $currency; ?>"
                data-price-table-theme="white"
            ></afterpay-price-table>
        </div>
    </div>
<?php
    wp_enqueue_script('afterpay_js_lib');
}
