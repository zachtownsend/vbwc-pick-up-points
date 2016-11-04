<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://verbbrands.com/
 * @since      1.0.0
 *
 * @package    Vbwc_Pick_Up_Points
 * @subpackage Vbwc_Pick_Up_Points/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Vbwc_Pick_Up_Points
 * @subpackage Vbwc_Pick_Up_Points/includes
 * @author     Zach Townsend <zach@verbbrands.com>
 */
class Vbwc_Pick_Up_Points_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'vbwc-pick-up-points',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
