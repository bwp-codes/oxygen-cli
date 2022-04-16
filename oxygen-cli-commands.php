<?php
/**
 * Plugin Name:  Oxygen WP CLI Commands
 * Plugin URI:   https://github.com/artifact-modules/command
 * Description:  Collection of WP-CLI commands.
 * Version:      0.0.1
 * Author:       dPlugins
 * Author URI:   https://dplugins.com/
 * License:      MIT License
 */

function is_cli_running() {
    return defined( 'WP_CLI' ) && WP_CLI;
}

if ( is_cli_running() ) {
    require_once plugin_dir_path( __FILE__ ) . '/commands/commands.php';
}
