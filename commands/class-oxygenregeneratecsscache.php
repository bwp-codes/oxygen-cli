<?php
/**
 * Oxygen Builder CLI Command to regenerate css cache.
 *
 * @package Oxygen CLI
 */
class OxygenRegenerateCssCache extends WP_CLI_Command {

  
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
   * WP CLI entry method.
   *
   * @when wp_loaded
   * @subcommand css-cache
   */
  public function css_cache( $args, $assocArgs ) {
    // global $oxygen_signature is required
    // the css caching method check signatures and need the global object initialized
    global $oxygen_signature;
    $oxygen_signature = new OXYGEN_VSB_Signature();

    // If a numeric argument is given, clear the page of the ID given
    if (!empty($args) && is_numeric($args[0])) {
      return $this->regenerateCSSCacheForPost($args[0]);
    }

    $this->regenerateEverything();
  }

  /**
   * @return void
   */
  private function regenerateEverything():void {
    global $ct_ignore_post_types;
    $postTypes = get_post_types();
    $ignore_post_types = $ct_ignore_post_types;
    $ct_template_key = array_search('ct_template', $ignore_post_types);
    if($ct_template_key !== false) {
      unset($ignore_post_types[$ct_template_key]);
    }
    if(is_array($ignore_post_types) && is_array($postTypes)) {
      $postTypes = array_diff($postTypes, $ignore_post_types);
    }
    // Append ct_template manually as not returned by get_post_types
    // and need refresh
    $postTypes[] = 'ct_template';

    $query = new WP_Query([
      'posts_per_page' => -1,
      'fields' => 'ids',
      'post_type' => $postTypes,
      'post_status' => ['publish', 'acf-disabled', 'future', 'draft', 'pending', 'private'],
      'meta_query' => [
        'relation' => 'OR',
        [
          'key'     => $this->get_oxygen_version() >= '4.8.3' ? '_ct_builder_shortcodes' : 'ct_builder_shortcodes',
          'value'   => '',
          'compare' => '!=',
        ],
        [
          'key'     => $this->get_oxygen_version() >= '4.8.3' ? '_ct_builder_json' : 'ct_builder_json',
          'value'   => '',
          'compare' => '!=',
        ],
      ],
    ]);

    if (oxygen_vsb_cache_universal_css()) {
      WP_CLI::line("Universal CSS cache generated successfully.");
    } else {
      WP_CLI::warning("Universal CSS cache not generated.");
    }

    // Relaunch a new command instead of calling the regenerateCSSCacheForPost method
    // This ensure that everything is reset between each calls and mimics the same
    // behaviour of back-office oxygen CSS regeneration through ajax calls.
    foreach ($query->posts  as $post) {
      WP_CLI::runcommand('oxygen css-cache ' . $post);
    }
  }

  /**
   * @param $id
   * @return bool
   */
  private function regenerateCSSCacheForPost($id) : bool {

    // global is required to retrieve correct page settings
    // @see ct_get_page_settings function
    global $oxy_ajax_post_id;
    $oxy_ajax_post_id = $id;

    if (oxygen_vsb_cache_page_css($id)) {
      WP_CLI::line("CSS cache generated successfully. Post ID: {$id} - " . get_post_type($id) . ' - ' . get_the_title($id));
      return true;
    } else {
      WP_CLI::warning("CSS cache not generated.");
      return false;
    }
  }
}

WP_CLI::add_command( 'oxygen', 'OxygenRegenerateCssCache' );
