<?php
/**
 * Oxygen Builder CLI Commands.
 *
*/

class OxyCommand extends WP_CLI_Command {

	/**
	 * Shortcode Signing.
	 *
	 * ## OPTIONS
	 *
	 * <post-type>...
	 * : Specify the post types to sign the oxygen's shorcode.
	 * 
	 * ## EXAMPLES
	 * 
	 *    wp artifact oxygen-builder sign-shortcode page
	 *    wp artifact oxygen-builder sign-shortcode page ct_template
	 *
	 * @when wp_loaded
	 * @subcommand sign-shortcode
	*/
	function sign_shortcode($args, $assoc_args) {
		global $oxygen_signature;
		$oxygen_signature = new OXYGEN_VSB_Signature();
	
		$posts = get_posts(
			[
				'post_type' => $args,
				'numberposts' => -1,
				'orderby' => 'ID',
				'order' => 'ASC',
				'meta_key' => 'ct_builder_shortcodes',
			]
		);
	
		WP_CLI::line(sprintf("Found %d post(s)", count($posts)));
	
		foreach ($posts as $post) {
			$shortcodes = get_post_meta($post->ID, 'ct_builder_shortcodes', true);
	
			if (!$shortcodes) {
				WP_CLI::warning("No shortcodes found on post of type \"{$post->post_type}\" with ID = {$post->ID}", false);
			} else {
				$not_registered_shortcodes = oxygen_has_not_registered_shortcodes($shortcodes);
				if ($not_registered_shortcodes) {
					$message = 'Inactive Shortcodes Present: "' . implode(', ', $not_registered_shortcodes) . "\" on post type \"{$post->post_type}\" with ID = {$post->ID} - Activate Add-Ons Before Re-Signing.";
					WP_CLI::warning($message, false);
				} else {
					WP_CLI::line("Signing shortcodes on post type \"{$post->post_type}\" with ID = {$post->ID}");
	
					// parse without verifying signature, as these might not have any signature
					$shortcodes = parse_shortcodes($shortcodes, false, false);
	
					//save again and re-sign in the process
					$shortcodes = parse_components_tree($shortcodes['content']);
	
					update_post_meta($post->ID, 'ct_builder_shortcodes', $shortcodes);
				}
			}
		}
	}

	/**
	 * Regenerate CSS Cache.
	 *
	 * @when wp_loaded
	 * @subcommand css-cache
	 */
	public function css_cache($args, $assocArgs)
	{
		global $oxygen_signature;
		$oxygen_signature = new OXYGEN_VSB_Signature();

		$posts = [];

		$query = new WP_Query([
			'posts_per_page' => -1,
			'post_type' => 'any',
			'meta_query' => [
				[
					'key'     => 'ct_builder_shortcodes',
					'value'   => '',
					'compare' => '!=',
				],
			],
		]);

		foreach ($query->posts as $post) {
			$posts[] = $post->ID;
		}

		$query = new WP_Query([
			'posts_per_page' => -1,
			'post_type' => ['ct_template'],
			'meta_query' => [
				[
					'key'     => 'ct_builder_shortcodes',
					'value'   => '',
					'compare' => '!=',
				],
			],
		]);

		foreach ($query->posts as $post) {
			$posts[] = $post->ID;
		};

		if (oxygen_vsb_cache_universal_css()) {
			WP_CLI::line("Universal CSS cache generated successfully.");
		} else {
			WP_CLI::warning("Universal CSS cache not generated.");
		}

		foreach ($posts as $post) {
			if (oxygen_vsb_cache_page_css($post)) {
				WP_CLI::line("CSS cache generated successfully. Post ID: {$post} - " . get_the_title($post));
			} else {
				WP_CLI::warning("CSS cache not generated.");
			}
		}
	}
}
WP_CLI::add_command( 'oxygen', 'OxyCommand' );