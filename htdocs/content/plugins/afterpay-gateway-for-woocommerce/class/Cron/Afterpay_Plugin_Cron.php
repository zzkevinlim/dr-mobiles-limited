<?php
/**
* Afterpay Plugin CRON Handler Class
*/
class Afterpay_Plugin_Cron
{
	/**
	 * Create a new WP-Cron job scheduling interval so jobs can run "Every 15 minutes".
	 *
	 * Note:	Hooked onto the "cron_schedules" Filter.
	 *
	 * @since	2.0.0
	 * @param	array	$schedules	The current array of cron schedules.
	 * @return	array				Array of cron schedules with 15 minutes added.
	 **/
	public static function edit_cron_schedules($schedules) {
		$schedules['15min'] = array(
			'interval' => 15 * 60,
			'display' => __( 'Every 15 minutes', 'woo_afterpay' )
		);
		return $schedules;
	}

	/**
	 * Schedule the WP-Cron job for Afterpay.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::activate_plugin()
	 * @uses	wp_next_scheduled()
	 * @uses	wp_schedule_event()
	 **/
	public static function create_jobs() {
		$timestamp = wp_next_scheduled( 'afterpay_do_cron_jobs' );
		if ($timestamp == false) {
			wp_schedule_event( time(), '15min', 'afterpay_do_cron_jobs' );
		}
	}

	/**
	 * Delete the Afterpay WP-Cron job.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::deactivate_plugin()
	 * @uses	wp_clear_scheduled_hook()
	 **/
	public static function delete_jobs() {
		wp_clear_scheduled_hook( 'afterpay_do_cron_jobs' );
	}

	/**
	 * Fire the Afterpay WP-Cron job.
	 *
	 * Note:	Hooked onto the "afterpay_do_cron_jobs" Action, which exists
	 *			because we scheduled a cron under that key when the plugin was activated.
	 *
	 * Note:	Hooked onto the "woocommerce_update_options_payment_gateways_afterpay"
	 *			Action as well.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::__construct()	For hook attachment.
	 * @see		self::create_jobs()				For initial scheduling (on plugin activation).
	 * @uses	is_admin()
	 * @uses	WC_Gateway_Afterpay::log()
	 * @uses	self::update_payment_limits()
	 */
	public static function fire_jobs() {
		if (defined('DOING_CRON') && DOING_CRON === true) {
			$fired_by = 'schedule';
		} elseif (is_admin()) {
			$fired_by = 'admin';
		} else {
			$fired_by = 'unknown';
		}
		WC_Gateway_Afterpay::log("Firing cron by {$fired_by}...");

		self::update_payment_limits();
	}

	/**
	 * Load this merchant's payment limits from the API.
	 *
	 * @since	2.0.0
	 * @uses	WC_Gateway_Afterpay::log()
	 * @uses	WC_Gateway_Afterpay::get_configuration()
	 * @used-by	self::fire_jobs()
	 */
	private static function update_payment_limits() {
		$gateway = WC_Gateway_Afterpay::getInstance();
		$settings = $gateway->getSettings();

		if ($settings['enabled'] != 'yes') {
			# Don't hit any API when the gateway is not Enabled.
			return false;
		}

		if (is_admin()) {
			$gateway->init_merchant_account();
		} else {
			if ($settings['testmode'] == 'production') {
				if (empty($settings['prod-id']) || empty($settings['prod-secret-key'])) {
					# Don't hit the Production API without Production creds.
					return false;
				}
			} elseif ($settings['testmode'] == 'sandbox') {
				if (empty($settings['test-id']) || empty($settings['test-secret-key'])) {
					# Don't hit the Sandbox API without Sandbox creds.
					return false;
				}
			}
		}

		$settings_changed = false;
		$configuration = $gateway->get_configuration();

		if (is_object($configuration)) {
			if (property_exists($configuration, 'errorCode')) {
				# Only change the values if getting 401
				if ($configuration->errorCode == 'unauthorized') {
					$settings_changed = true;
					$settings['pay-over-time-limit-min'] = 'N/A';
					$settings['pay-over-time-limit-max'] = 'N/A';
					$settings['settlement-currency'] = '';
					$settings['cbt-countries'] = 'N/A';
				}
			}
			else {
				$old_min = floatval($settings['pay-over-time-limit-min']);
				$old_max = floatval($settings['pay-over-time-limit-max']);
				$new_min = property_exists($configuration, 'minimumAmount') ? $configuration->minimumAmount->amount : '0.00';
				$new_max = property_exists($configuration, 'maximumAmount') ? $configuration->maximumAmount->amount : '0.00';
				if ($new_min != $old_min) {
					$settings_changed = true;
					$gateway::log("Cron changing payment limit MIN from '{$old_min}' to '{$new_min}'.");
					$settings['pay-over-time-limit-min'] = $new_min;
				}
				if ($new_max != $old_max) {
					$settings_changed = true;
					$gateway::log("Cron changing payment limit MAX from '{$old_max}' to '{$new_max}'.");
					$settings['pay-over-time-limit-max'] = $new_max;
				}

				$old_currency = isset($settings['settlement-currency']) ? $settings['settlement-currency'] : '';
				$new_currency = $configuration->maximumAmount->currency;
				if ($new_currency != $old_currency) {
					$settings_changed = true;
					$gateway::log("Cron changing settlement currency from '{$old_currency}' to '{$new_currency}'.");
					$settings['settlement-currency'] = $new_currency;
				}

				$old_cbt = isset($settings['cbt-countries']) ? $settings['cbt-countries'] : '';
				$new_cbt = 'N/A';
				if (property_exists($configuration, 'CBT') && $configuration->CBT->enabled) {
					if (is_array($configuration->CBT->countries)) {
						sort($configuration->CBT->countries);
						$new_cbt = implode('|', $configuration->CBT->countries);
					}
				}
				if ($new_cbt != $old_cbt) {
					$settings_changed = true;
					$gateway::log("Cron changing cbt countries from '{$old_cbt}' to '{$new_cbt}'.");
					$settings['cbt-countries'] = $new_cbt;
				}
			}
		}

		if ($settings_changed) {
			update_option( $gateway->get_option_key(), $settings );
		}
	}
}
