<?php
/**
 * Oxygen Builder CLI Commands.
 *
 * @package Oxygen CLI
 */
class OxygenSignShortcode extends WP_CLI_Command {

	private function get_oxygen_version($plugin_file = 'oxygen/functions.php') {
		// Define the full path to the plugin file
		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
	
		// Check if the file exists
		if (!file_exists($plugin_path)) {
			return false;
		}
	
		// Read the file content
		$file_content = file_get_contents($plugin_path);
	
		// Use regex to extract the version from the plugin header
		if (preg_match('/^.*Version:\s*([^\s]+).*$/mi', $file_content, $matches)) {
			return $matches[1];
		}
	
		return false;
	}
	/**
	 * Shortcode Signing.
	 *
	 * ## OPTIONS
	 *
	 * <post-type>...
	 * : Specify the post types to sign the oxygen's shortcode.
	 * 
	 * ## EXAMPLES
	 * 
	 *    wp oxygen sign-shortcode page
	 *    wp oxygen sign-shortcode page ct_template
	 *
	 * @when wp_loaded
	 * @subcommand sign-shortcode
	*/
	function sign_shortcode($args, $assoc_args) {
		global $oxygen_signature;
		$oxygen_signature = new OXYGEN_VSB_Signature();
	
		foreach ( $args as $type ) {
			$pages = get_posts(
				array(
					'post_type' => array( $type ),
					'numberposts' => -1,
					'orderby' => 'ID',
					'order' => 'ASC',
					'meta_key' => $this->get_oxygen_version() >= '4.8.3' ? '_ct_builder_shortcodes' : 'ct_builder_shortcodes',
				)
			);

			if ( !is_array( $pages ) or count( $pages ) == 0 ) {
				WP_CLI::warning( "No posts found of type $type" );
				continue;
			}

			$page_ids = array_map( function ( $page ) {
				return $page->ID;
			}, $pages );
	
			WP_CLI::line( sprintf( "Found %d post(s) of type %s", count( $page_ids ), $type ) );
	
			$_REQUEST['page_ids'] = $page_ids;
			$_REQUEST['index'] = NULL;
			do {
				$response = oxygen_vsb_sign_shortcodes( $type );
				foreach ( $response['messages'] as $message ) {
					WP_CLI::line( $message );
				}
				if ( ! array_key_exists( 'index', $response ) ) {
					break;
				}
				$_REQUEST['index'] = $response['index'];
			} while (true);
		}
	}
}
WP_CLI::add_command( 'oxygen', 'OxygenSignShortcode' );
