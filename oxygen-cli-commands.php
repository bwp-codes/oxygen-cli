<?php
/**
 * Plugin Name:  Oxygen CLI
 * Plugin URI:   https://github.com/bwp-codes/oxygen-cli
 * Description:  Collection of WP-CLI commands for Oxygen Builder (https://oxygenbuilder.com/).
 * Version:      1.2.0
 * Author:       BWP Codes
 * Contributors: https://github.com/bwp-codes/oxygen-cli/graphs/contributors
 * Author URI:   https://bwp-codes.de/
 * License:      MIT License
 *
 * @package Oxygen CLI
 */

/**
 * Check if WP CLI is running.
 */
function is_cli_running() {
	return defined( 'WP_CLI' ) && WP_CLI;
}

if ( is_cli_running() ) {
	// Include command: wp oxygen css-cache.
	require_once plugin_dir_path( __FILE__ ) . '/commands/class-oxygenregeneratecsscache.php';
	// Include command: wp oxygen sign-shortcode.
	require_once plugin_dir_path( __FILE__ ) . '/commands/class-oxygensignshortcode.php';
}
