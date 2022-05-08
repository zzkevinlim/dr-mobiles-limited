<?php
$get_afterpay_assets = function($country)
{
	// These are assets values in the Afterpay - WooCommerce plugin
	$global_assets = array(
		"cart_page_express_button"					=>	'<tr><td colspan="2" style="text-align: center;"><button id="afterpay_express_button" class="btn-afterpay_express btn-afterpay_express_cart" type="button" disabled><img src="https://static.afterpay.com/button/checkout-with-afterpay/[THEME].svg" alt="Checkout with Afterpay" /></button></td></tr>',
	);

	$assets = array(
		"US" => array(
			"cs_number"		=>	'855 289 6014',
			"retailer_url"	=>	'https://www.afterpay.com/for-retailers',
		),
	    "CA" => array(
			"cs_number"		=>	'833 386 0210',
			"retailer_url"	=>	'https://www.afterpay.com/en-CA/for-retailers',
		),
		"AU" => array(
			"cs_number"		=>	'1300 100 729',
			"retailer_url"	=>	'https://www.afterpay.com/en-AU/business',
		),
		"NZ" => array(
			"cs_number"		=>	'0800 461 268',
			"retailer_url"	=>	'https://www.afterpay.com/en-NZ/business',
		),
	);

	$region_assets = array_key_exists($country, $assets) ? $assets[$country] : $assets['AU'];

	return array_merge($global_assets, $region_assets);
};

return $get_afterpay_assets($this->get_country_code());
?>
