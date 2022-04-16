<?php
/**
 * Plugin Name:  Oxygen CLI
 * Plugin URI:   https://github.com/artifact-modules/command
 * Description:  Collection of WP-CLI commands for Oxygen Builder (https://oxygenbuilder.com/).
 * Version:      0.0.3
 * Author:       BWP Codes
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
	require_once plugin_dir_path( __FILE__ ) . '/commands/commands.php';
}
