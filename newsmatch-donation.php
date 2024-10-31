<?php
/*
Plugin Name: News Match Donation Shortcode
Description:  Provides methods to integrate with the donation application hosted at checkout.voiceofsandiego.org
Version: 0.1.2
Author: INN Labs
Author URI: https://labs.inn.org/
*/

// Plugin directory normalization.
define( 'NMD_PLUGIN_FILE', __FILE__ );

/**
 * Set up the plugin
 *
 * @package NewsMatchDonation
 */
class NewsMatchDonation {
	/**
	 * Set up the plugin
	 */
	public function __construct() {
		require_once( __DIR__ . '/classes/class-newsmatchdonation-settings.php' );
		new NewsMatchDonation_Settings();
		require_once( __DIR__ . '/classes/class-newsmatchdonation-shortcode.php' );
		new NewsMatchDonation_Shortcode();
	}
}

new NewsMatchDonation();
