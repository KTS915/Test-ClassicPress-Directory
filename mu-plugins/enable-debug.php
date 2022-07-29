<?php if ( ! defined( 'ABSPATH' ) ) { die(); }
/*
 * Plugin Name: Turn on logs
 * Plugin URI: https://software.gieffeedizioni.it
 * Description: Turn on logs
 * Version: 1.0
 * Author: Simone Fioravanti
 * License: MIT
*/

# Get or set up log file name
$error_log_file = get_option( 'directory-error-log-file', false);
if ( $error_log_file === false ) {
	require_once ABSPATH . 'wp-includes/pluggable.php';
	$error_log_file = WP_CONTENT_DIR . '/' . wp_generate_password( 12, false, false ) . '-error.log';
	update_option( 'directory-error-log-file', $error_log_file );
}

# Enable logging
ini_set('error_log', $error_log_file);
error_reporting(E_ALL);
