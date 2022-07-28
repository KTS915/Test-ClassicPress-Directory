<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Download Links
 * Plugin URI: https://directory.classicpress.net/
 * Description: Keeps download links up to date
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
/* RENDER MANUAL UPDATE DOWNLOAD LINK FORM */
function kts_render_software_update_link_form( $post ) {

	# Bail if not logged in
	if ( ! is_user_logged_in() ) {
		return;
	}

	# Bail if not the user's own archive page
	if ( get_current_user_id() !== get_queried_object_id() ) {
		return;
	}

	$update_nonce = cp_set_nonce( 'update_nonce' );
			
	if ( isset( $_GET['notification'] ) ) {
		if ( $_GET['notification'] === 'nonce-wrong-' . absint( $post->ID ) ) {
			echo '<div class="error-message" role="alert"><p>' . __( 'You have already submitted this.', 'classicpress' ) . '</p></div>';
		}
		elseif ( $_GET['notification'] === 'github-api-wrong-' . absint( $post->ID ) ) {
			echo '<div class="error-message" role="alert"><p>' . __( 'Something went wrong with GitHub API.', 'classicpress' ) . '</p></div>';
		}
		elseif ( $_GET['notification'] === 'success-' . absint( $post->ID ) ) {
			echo '<div class="success-message" role="polite"><p>' . __( 'The link has been updated.', 'classicpress' ) . '</p></div>';
		}
	}

	echo '<form id="update-form" class="update-form" method="POST" autocomplete="off">';
	echo '<input type="hidden" name="software-id" value="' . absint( $post->ID ) . '">';
	echo '<input type="hidden" name="update-nonce-name" value="' . $update_nonce['name'] . '">';
	echo '<input type="hidden" name="update-nonce-value" value="' . $update_nonce['value'] . '">';
	echo '<button type="submit" class="aligncenter">' . __( 'Update Download Link', 'classicpress' ) . '</button>';
	echo '</form>';
}


/* PROCESS UPDATE DOWNLOAD LINK FORM */
function kts_software_update_link_redirect() {

	# Check for nonce
	if ( empty( $_POST['update-nonce-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['update-nonce-name'], $_POST['update-nonce-value'] );
	$referer = remove_query_arg( 'notification', wp_get_referer() );	
	$software_id = absint( $_POST['software-id'] );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong-' . $software_id ) );
		exit;
	}

	# Construct the URL to the GitHub API
	$download_link = get_post_meta( $software_id, 'download_link', true );
	$tidy_url = preg_replace( '~releases\/[\s\S]+?\.zip~', '', $download_link );
	$repo_url = str_replace( 'https://github.com/', 'https://api.github.com/repos/', $tidy_url );
	$github_url = $repo_url . 'releases/latest';

	# Make GET request to GitHub API to retrieve latest software download link
	# To use a GitHub API token put define('GITHUB_API_TOKEN', 'YOURTOKENVALU'); in wp-config.php
	# To create a token, go to https://github.com/settings/tokens and generate a new token.
	# When generting the new token don't select any scope.
	if ( defined ( 'GITHUB_API_TOKEN' ) ) {
		$auth = [
			'headers' => [
				'Authorization' => 'token ' . GITHUB_API_TOKEN,
			],
		];
	} else {
		$auth = [];
	}

	$result = json_decode( wp_remote_retrieve_body( wp_safe_remote_get( esc_url_raw( $github_url ), $auth ) ) );

	if ( isset ( $result->message ) ) {
		trigger_error ( 'Something went wrong with GitHub API on item ' . $software_id . ': ' . esc_html( $result->message ) );
		wp_safe_redirect( esc_url_raw( $referer . '?notification=github-api-wrong-' . $software_id ) );
		exit;
	}

	$new_link = ( $result && $result->assets ) ? $result->assets[0]->browser_download_url : '';

	# Check that URL to download software is to a later release	
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $orig_matches );
	$orig_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $orig_matches[0] );

	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $new_link, $new_matches );
	$new_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $new_matches[0] );

	# Update download link and current version if newer
	if ( version_compare( $new_version, $orig_version ) === 1 ) {
		update_post_meta( $software_id, 'download_link', $new_link );
		update_post_meta( $software_id, 'current_version', $new_version );
	}

	# Generate success message
	wp_safe_redirect( esc_url_raw( $referer . '?notification=success-' . $software_id ) );
	exit;

}
add_action( 'template_redirect', 'kts_software_update_link_redirect' );

 
/* UPDATE DOWNLOAD LINKS VIA DAILY CRONJOB */
function kts_cron_update_download_links() {

	# Get all plugins, themes, and snippets
	$args = array(
		'numberposts'	=> -1,
		'post_type'		=> array( 'plugin', 'theme', 'snippet' ),
		'post_status'	=> 'publish',
	);
	$posts = get_posts( $args );

	foreach( $posts as $key => $post ) {

		# Get software ID and construct the URL to the GitHub API
		$download_link = get_post_meta( $post->ID, 'download_link', true );
		$tidy_url = preg_replace( '~releases\/[\s\S]+?\.zip~', '', $download_link );
		$repo_url = str_replace( 'https://github.com/', 'https://api.github.com/repos/', $tidy_url );
		$github_url = $repo_url . 'releases/latest';

		# Make GET request to GitHub API to retrieve latest software download link
		if ( defined ( 'GITHUB_API_TOKEN' ) ) {
			$auth = [
				'headers' => [
					'Authorization' => 'token ' . GITHUB_API_TOKEN,
				],
			];
		} else {
			$auth = [];
		}
		$result = json_decode( wp_remote_retrieve_body( wp_safe_remote_get( esc_url_raw( $github_url ), $auth ) ) );
		if ( isset ( $result->message ) ) {
			trigger_error ( 'Something went wrong with GitHub API on item ' . $post->ID . ': ' . esc_html( $result->message ) );
			continue;
		}
		$new_link = '';
		if ( ! empty( $result ) && ! empty( $result->assets ) ) {
			$new_link = $result->assets[0]->browser_download_url;
		}

		if ( ! empty( $new_link ) ) {

			# Check that URL to download software is to a later release	
			preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $orig_matches );
			$orig_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $orig_matches[0] );

			preg_match( '~releases\/download\/v?[\s\S]+?\/~', $new_link, $new_matches );
			$new_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $new_matches[0] );

			# Update download link and current version if newer
			if ( version_compare( $new_version, $orig_version ) === 1 ) {
				update_post_meta( $post->ID, 'download_link', $new_link );
				update_post_meta( $post->ID, 'current_version', $new_version );
			}

			# Break into groups of 10 to keep manageable
			if ( $key > 0 && $key % 10 == 0 ) { // modulo operator
				sleep( 5 ); // wait for 5 seconds
			}
		}
	}
}
add_action( 'daily_cron_hook', 'kts_cron_update_download_links' );


/* CRONJOB SCHEDULE */
function kts_cp_directory_cronjobs() {
	if ( ! wp_next_scheduled( 'daily_cron_hook' ) ) {
		wp_schedule_event( time(), 'daily', 'daily_cron_hook' );
	}
}
add_action( 'init', 'kts_cp_directory_cronjobs' );
