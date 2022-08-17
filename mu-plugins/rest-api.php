<?php if ( ! defined( 'ABSPATH' ) ) { die(); }

/**
 * Plugin Name: REST API Mods for Plugins, Themes, and Snippets
 * Description: Modifies the REST API for the above CPTs' endpoints
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1.0
 */

/* REGISTER META FIELDS WITH REST API */
function kts_register_meta_with_rest_api() {
	$plugin_args1 = array(
		'type'			=> 'string',
		'description'	=> 'Current Version of Software',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'current_version', $plugin_args1 );

	$plugin_args2 = array(
		'type'			=> 'string',
		'description'	=> 'Git Provider',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'git_provider', $plugin_args2 );

	$plugin_args3 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of ClassicPress',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'cp_version', $plugin_args3 );

	$plugin_args4 = array(
		'type'			=> 'string',
		'description'	=> 'Download Link URL',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'download_link', $plugin_args4 );

	$plugin_args5 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of PHP',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_php', $plugin_args5 );

	$plugin_args6 = array(
		'type'			=> 'string',
		'description'	=> 'Plugin slug',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'slug', $plugin_args6 );

	$plugin_args7 = array(
		'type'			=> 'string',
		'description'	=> 'Developer name',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'developer_name', $plugin_args7 );

	$plugin_args8 = array(
		'type'			=> 'string',
		'description'	=> 'Category names',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'category_names', $plugin_args8 );

	$plugin_args9 = array(
		'type'			=> 'string',
		'description'	=> 'Category slugs',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'category_slugs', $plugin_args9 );

	$theme_args1 = array(
		'type'			=> 'string',
		'description'	=> 'Current Version of Software',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'current_version', $theme_args1 );

	$theme_args2 = array(
		'type'			=> 'string',
		'description'	=> 'Git Provider',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'git_provider', $theme_args2 );

	$theme_args3 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of ClassicPress',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'cp_version', $theme_args3 );

	$theme_args4 = array(
		'type'			=> 'string',
		'description'	=> 'Download Link URL',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'download_link', $theme_args4 );

	$theme_args5 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of PHP',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_php', $theme_args5 );

	$theme_args6 = array(
		'type'			=> 'string',
		'description'	=> 'Theme slug',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'slug', $theme_args6 );

	$theme_args7 = array(
		'type'			=> 'string',
		'description'	=> 'Developer name',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'developer_name', $theme_args7 );

	$snippet_args1 = array(
		'type'			=> 'string',
		'description'	=> 'Current Version of Software',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'current_version', $snippet_args1 );

	$snippet_args2 = array(
		'type'			=> 'string',
		'description'	=> 'Git Provider',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'git_provider', $snippet_args2 );

	$snippet_args3 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of ClassicPress',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'cp_version', $snippet_args3 );

	$snippet_args4 = array(
		'type'			=> 'string',
		'description'	=> 'Download Link URL',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'download_link', $snippet_args4 );

	$snippet_args5 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of PHP',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_php', $snippet_args5 );

	$snippet_args6 = array(
		'type'			=> 'string',
		'description'	=> 'Snippet slug',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'slug', $snippet_args6 );

	$snippet_args7 = array(
		'type'			=> 'string',
		'description'	=> 'Developer name',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'developer_name', $snippet_args7 );

	$snippet_args8 = array(
		'type'			=> 'string',
		'description'	=> 'Tags',
		'single'		=> true,
		'object_subtype'=> 'snippet',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'tags', $snippet_args8 );
}
add_action( 'init', 'kts_register_meta_with_rest_api' );


/* REST API SECURITY */
function kts_modify_rest_software_routes( $response, $handler, $request ) {

	# Prevent editing and deletion of plugin, theme, and snippet CPTs
	$routes = array(
        '/wp/v2/plugins',
        '/wp/v2/themes',
        '/wp/v2/snippets',
	);

	if ( in_array( $request->get_route(), $routes ) ) {		

		$methods = array( 'POST', 'PUT', 'PATCH', 'DELETE' );
		if ( in_array( $request->get_method(), $methods ) ) {

			return new WP_Error(
				'rest_cpts_cannot_1',
				__( 'Sorry, you are not allowed to edit or delete posts.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
	}
}
add_filter( 'rest_request_before_callbacks', 'kts_modify_rest_software_routes', 10, 3 );
