<?php
/**
 * Oxygen Builder CLI Commands.
 *
 * @package Oxygen CLI
 */
class OxygenRegenerateCssCache extends WP_CLI_Command {

	/**
	 * Regenerate CSS Cache.
	 *
	 * @when wp_loaded
	 * @subcommand css-cache
	 */
	public function css_cache( $args, $assocArgs ) {
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

		/***
		 * This is a fix to prevent partial CSS cache generation.
		 * WP_CLI generates all page caches in a row while the official
		 * way uses a new request through ajax each time.
		 * Oxygen implements a mechanism that prevents default CSS of
		 * elements to be included multiple times.
		 * This fix ensure that default css are included in each page cache.
		 */
		global $oxygen_vsb_components;
		foreach ($oxygen_vsb_components as $key => $component) {
			add_filter('oxygen_id_styles_filter-'.$key, function($styles, $states, $selector ) use ($key) {
				ob_start();
				do_action("oxygen_id_styles_output-".$key);
				$page_css = ob_get_clean();
				if (strpos($styles, $page_css) !== FALSE) {
					return $styles;
				}
				return $page_css . $styles;
			}, 1, 3);
		}

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
WP_CLI::add_command( 'oxygen', 'OxygenRegenerateCssCache' );
