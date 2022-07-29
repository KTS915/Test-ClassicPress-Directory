<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Software Submit Form
 * Plugin URI: https://directory.classicpress.net/
 * Description: Form to enable submission of a ClassicPress plugin, theme, or code snippet
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1
 */
 
function kts_get_plugin_data( $file_data ) {
	$default_headers = array(
		'Name' => 'Plugin Name',
		'PluginURI' => 'Plugin URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Network' => 'Network',
		'RequiresWP'  => 'Requires at least',
		'RequiresPHP' => 'Requires PHP',
	);
	$plugin_data = kts_get_file_data( $file_data, $default_headers );
	$plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
	return $plugin_data;
}

function kts_get_file_data( $file_data, $all_headers ) {
	$file_data = str_replace( "\r", "\n", $file_data );
	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] )
			$all_headers[ $field ] = _cleanup_header_comment( $match[1] );
		else
			$all_headers[ $field ] = '';
	}
	return $all_headers;
}

function kts_render_software_submit_form() {
	$user_id = get_current_user_id();
	$two_fa = kts_2fa_enabled( $user_id );
	ob_start();

	if ( ! is_user_logged_in() ) {
		_e( '<p>You need to <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">log in</a> to be able to use the form on this page.</p>', 'classicpress' );
	}

	elseif ( ! current_user_can( 'edit_posts' ) ) { // Not a Contributor or above
		if ( $two_fa === false ) {
			_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
			_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( get_current_user_id() ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>.</p>', 'classicpress' );
			_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
		}
		else {
			$two_fa_method = get_user_meta( $user_id, '_two_factor_provider', true );
			if ( $two_fa_method === 'Two_Factor_Dummy' ) {
				_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
				_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( get_current_user_id() ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>. Enabling the dummy method is not acceptable for this purpose.</p>', 'classicpress' );
				_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
			}
			else {
				_e( '<p>Your account is pending review. You will receive an email when you have been approved.</p>', 'classicpress' );
			}
		}
	}

	else { // Contributor role or above
		_e( '<p>Please use the form below to upload your plugin, theme, or code snippet. All fields are required.</p>', 'classicpress' );
		_e( '<p>We recommend that, before submitting your software, you run your code through the <a href="https://wpseek.com/pluginfilecheck/">Plugin Doctor</a>.</p>', 'classicpress' );
		_e( '<p>Once your software has been approved, details will be made public in this directory.</p>', 'classicpress' );

		$cp_nonce = cp_set_nonce( 'software_nonce' );
		$categories = get_categories( array( 
			'taxonomy'		=> 'category',
			'hide_empty'	=> false,
			'exclude'		=> array( get_cat_ID( 'Uncategorized' ) ),
		) );

		# Error Messages
		if ( isset( $_GET['notification'] ) ) {
			if ( $_GET['notification'] === 'nonce-wrong' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You have already submitted this form.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-software-type' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify the type of software that you are submitting!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-software-type' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The software type you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-name' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a name for your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-slug' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify a suitable slug for your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-excerpt' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a brief description of your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-excerpt' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The brief description of your software is longer than 100 characters!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-description' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a description of your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-categories' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify at least one category for your plugin!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-categories' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The categories you have specified are not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-tags' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify at least one tag for your code snippet!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-current-version' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify the current version of your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-git-provider' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify your Git provider!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-git-provider' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The Git provider you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-cp-version' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify the minimum version of ClassicPress that your software works with!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-cp-version' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The minimum version of ClassicPress you have specified is unrecognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-download-link' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-download-link' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a valid URL for the download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'temp-file-error' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'There was a problem creating a temporary file.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'file-error' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'There was a problem while parsing your zip file.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-github' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a URL for a GitHub repository!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'not-sent' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'There was a problem submitting the form. Your message has not been sent.', 'classicpress' ) . '</p></div>';
			}
		}
		?>

		<h2><?php _e( 'Software Information', 'classicpress' ); ?></h2>

		<noscript><div class="error-message" role="alert"><p><?php _e( 'This form will not work without JavaScript turned on.', 'classicpress' ); ?></p></div></noscript>

		<form id="submit-cp-code-form" method="post" autocomplete="off">

			<fieldset>
				<legend><?php _e( 'Select Type of Software', 'classicpress' ); ?></legend>
				<div class="clear"></div>
				<label for="plugin">
					<input id="plugin" class="mgr-lg" name="software_type" type="radio" value="plugin" required>
					Plugin
				</label>
				<br>
				<label for="theme">
					<input id="theme" class="mgr-lg" name="software_type" type="radio" value="theme" required>
					Theme
				</label>
				<br>
				<label for="snippet">
					<input id="snippet" class="mgr-lg" name="software_type" type="radio" value="snippet" required>
					Code Snippet
				</label>
			</fieldset>

			<label for="name"><?php _e( 'Name of Software', 'classicpress' ); ?></label>
			<input id="name" name="name" type="text" required>

			<label for="excerpt"><?php _e( 'Brief Description of Software (not more than 150 characters)', 'classicpress' ); ?></label>
			<input id="excerpt" name="excerpt" type="text" maxlength="150" required>

			<label for="description"><?php _e( 'Full Description of Software (you might like to paste the contents of your readme.txt file here)', 'classicpress' ); ?></label>
			<textarea id="description" name="description" required></textarea>
		
			<fieldset id="category" hidden>
				<legend id="cats"><?php _e( 'Specify to which of the following categories your plugin relates. (You must choose at least one.)', 'classicpress' ); ?></legend>
				<div class="clear"></div>

				<?php
				foreach( $categories as $category ) {
					echo '<input id="cat-' . $category->slug . '" class="mgr-lg" name="categories[]" type="checkbox" value="' . $category->cat_ID . '"  disabled>';
					echo '<label for="cat-' . $category->slug . '">' . $category->name . '</label>';
					echo '<br>';
				}
				?>

			</fieldset>

			<div id="tags-div" hidden>
				<label for="tags"><?php _e( 'Tags (you may specify up to three, separated by commas)', 'classicpress' ); ?></label>
				<span id="max" class="error-message" role="alert" hidden><?php _e( 'You have specified more than three tags!', 'classicpress' ); ?></span>
				<input id="tags" name="tags" type="text" disabled>
			</div>

			<label for="cp_version"><?php _e( 'Minimum Version of ClassicPress', 'classicpress' ); ?></label>
			<input id="cp_version" name="cp_version" type="number" step="0.1" min="1.0" required>

			<fieldset>
				<legend><?php _e( 'Select Git Provider (currently only GitHub)', 'classicpress' ); ?></legend>
				<div class="clear"></div>
				<label for="github">
					<input id="github" class="mgr-lg" name="git_provider" type="radio" value="github" required>
					GitHub
				</label>
				<br>
				<label for="gitlab">
					<input id="gitlab" class="mgr-lg" name="git_provider" type="radio" value="gitlab" disabled>
					GitLab
				</label>
			</fieldset>

			<label for="download_link"><?php _e( 'Software Download Link (full URL including https://)', 'classicpress' ); ?></label>
			<input id="download_link" name="download_link" type="url" required>

			<input type="hidden" name="cp-nonce-name" value="<?php echo $cp_nonce['name']; ?>">
			<input type="hidden" name="cp-nonce-value" value="<?php echo $cp_nonce['value']; ?>">
			<button id="submit-btn" type="submit" enterkeyhint="send">Submit</button>
			<button type="reset" enterkeyhint="go">Clear</button>

		</form>

	<?php
	}
	echo ob_get_clean();
}


/* PROCESS SUBMISSION FORM */
function kts_software_submit_form_redirect() {

	# Check that user has the capability to submit software for review
	if ( ! current_user_can( 'edit_posts' ) ) { // Contributor role or above
		return;
	}

	# Check for nonce
	if ( empty( $_POST['cp-nonce-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['cp-nonce-name'], $_POST['cp-nonce-value'] );
	$referer = remove_query_arg( 'notification', wp_get_referer() );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong' ) );
		exit;
	}

	# Get correct type of software
	$post_type = sanitize_text_field( wp_unslash( $_POST['software_type'] ) );

	# Check that the type of software has been specified
	if ( empty( $post_type ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-software-type' ) );
		exit;
	}

	# Check that the type of software specified actually exists
	$software_types = array( 'plugin', 'theme', 'snippet' );
	if ( ! in_array( $post_type, $software_types ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-software-type' ) );
		exit;
	}

	# If the software is a plugin, check that at least one category has been specified
	$category_ids = isset( $_POST['categories'] ) ? array_map( 'absint', wp_unslash( $_POST['categories'] ) ) : [];
	if ( $post_type === 'plugin' ) {
		if ( empty( $category_ids ) ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=no-categories' ) );
			exit;
		}
		else { // and check each category is recognized
			$cat_ids = get_categories( array( 
				'taxonomy'		=> 'category',
				'hide_empty'	=> false,
				'exclude'		=> array( get_cat_ID( 'Uncategorized' ) ),
				'fields'		=> 'ids',
			) );
			foreach( $category_ids as $cat_id ) {
				if ( ! in_array( $cat_id, $cat_ids ) ) {
					wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-categories' ) );
					exit;
				}
			}
		}
	}

	# Check that at least one tag has been specified for a code snippet	
	$tags = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : '';
	if ( $post_type === 'snippet' ) {
		if ( empty( $tags ) ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=no-tags' ) );
			exit;
		} // and check no more than three provided
		else {
			$tags = str_replace( '#', '', strtolower( $tags ) );
			$tags_array = explode( $tags );
			if ( count( $tags ) > 3 ) {
				wp_safe_redirect( esc_url_raw( $referer . '?notification=too-many-tags' ) );
				exit;
			}				
		}
	}

	# Check that name of software has been provided
	if ( empty( $_POST['name'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-name' ) );
		exit;
	}

	# Check that brief description of software has been provided
	if ( empty( $_POST['excerpt'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-excerpt' ) );
		exit;
	}

	# Check that brief description of software does not exceed 150 characters
	if ( strlen( $_POST['excerpt'] ) > 150 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=excerpt-too-long' ) );
		exit;
	}

	# Check that description of software has been provided
	if ( empty( $_POST['description'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-description' ) );
		exit;
	}

	# Check that Git provider has been provided
	if ( empty( $_POST['git_provider'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-git-provider' ) );
		exit;
	}

	# Check that Git provider specified actually exists
	$git_providers = array( 'github' );
	if ( ! in_array( $_POST['git_provider'], $git_providers ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-git-provider' ) );
		exit;
	}

	# Check that minimum version of CP has been provided
	if ( empty( $_POST['cp_version'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-cp-version' ) );
		exit;
	}

	# Check that minimum version of CP is a float
	if ( filter_var( $_POST['cp_version'], FILTER_VALIDATE_FLOAT ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-cp-version' ) );
		exit;
	}

	# Check that URL to download software has been provided
	if ( empty( $_POST['download_link'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-download_link' ) );
		exit;
	}

	# Check that the download link is a valid URL
	if ( filter_var( $_POST['download_link'], FILTER_VALIDATE_URL ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-download-link' ) );
		exit;
	}

	# Check that the download link points to GitHub
	if ( strpos( $_POST['download_link'], 'https://github.com/' ) !== 0 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-github' ) );
		exit;
	}
	
	# Enable the download_url() and wp_handle_sideload() functions
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	# Get download link
	$download_link = esc_url_raw( wp_unslash( $_POST['download_link'] ) );

	# Download (upload?) file to temp dir
	$temp_file = download_url( $download_link, 5 ); // 5 secs before timeout
	if ( is_wp_error( $temp_file ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=temp-file-error' ) );
		exit;
	}

	# Array based on $_FILE as seen in PHP file uploads
	$file = array(
		'name'     => basename( $download_link ),
		'type'     => 'application/zip',
		'tmp_name' => $temp_file,
		'error'    => 0,
		'size'     => filesize( $temp_file ),
	);

	# Find slug from top level folder name
	$zip = new ZipArchive();
	$zip->open( $file['tmp_name'] );
	$slug = strstr( $zip->getNameIndex(0), '/', true );
	$main_plugin_file = '';

	# Deal with themes and code snippets
	if ( in_array( $post_type, ['theme', 'snippet'] ) ) {
		$headers = [];
	}
	else {
		# Check if most common location for main file contain headers
		$guessed_main_file = $slug . '/' . $slug . '.php';
		$file_data = $zip->getFromName( $guessed_main_file, 8192 );
		$headers = kts_get_plugin_data( $file_data );

		if ( empty( $headers['Name'] ) ) {
			# Parse other files for headers
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				if ( ! preg_match( '~\.php$~', $zip->getNameIndex( $i ) ) || substr_count( $zip->getNameIndex( $i ), '/' ) !== 1 ) {
					# Only check PHP and don't recourse into subdirs
					continue;
				}
				$file_data = $zip->getFromIndex( $i, 8192 );
				$headers = kts_get_plugin_data( $file_data );
				if ( ! empty( $headers['Name'] ) ) {
					# We have the headers
					$main_plugin_file = $zip->getNameIndex( $i );
					break;
				}
			}
		}
		else {
			$main_plugin_file = $guessed_main_file;
		}
	}

trigger_error(print_r($headers, true));
trigger_error(print_r($main_plugin_file, true));
	# Delete temporary file
	wp_delete_file( $file['tmp_name'] );

	# Prevent form title being all upper case
	$title = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	if ( strtoupper( $title ) === $title ) { // all upper case
		$title = ucwords( strtolower( $title ) ); // convert to title case
	}

	# Get brief description of software
	$excerpt = sanitize_text_field( wp_unslash( $_POST['excerpt'] ) );

	# Get software description
	$description = sanitize_textarea_field( wp_unslash( $_POST['description'] ) );

	# Get current version of software
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $matches );
	$current_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $matches[0] );

	# Get git provider
	$git_provider = sanitize_text_field( wp_unslash( $_POST['git_provider'] ) );

	# Get minimum version of CP
	$cp_version = sanitize_text_field( wp_unslash( $_POST['cp_version'] ) );

	# Submit form as a post type
	$post_info = array(
		'post_title'	=> $title,
		'post_excerpt'	=> $excerpt,
		'post_content'	=> $description,
		'post_type'		=> $post_type,
		'post_status'	=> 'draft',
		'post_author'	=> get_current_user_id(),
		'comment_status'=> 'closed',
	);

	# Add category if software is a plugin
	if ( $post_type === 'plugin' ) {
		$post_info['post_category'] = $category_ids;
	}
	elseif ( $post_type === 'snippet' ) {
		$post_info['tags_input'] = array_map( 'trim', $tags_array );
	}

	# Save post
	$post_id = wp_insert_post( $post_info );

	# Generate an error message if there is a problem with submitting the form
	if ( $post_id === 0 || is_wp_error( $post_id ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=not-sent' ) );
		exit;
	}

	add_post_meta( $post_id, 'slug', $slug );
	add_post_meta( $post_id, 'current_version', $current_version );
	add_post_meta( $post_id, 'git_provider', $git_provider );
	add_post_meta( $post_id, 'cp_version', $cp_version );
	add_post_meta( $post_id, 'download_link', $download_link );

	# Redirect to post where published
	wp_safe_redirect( esc_url_raw( get_permalink( $post_id ) ) );
	exit;
}
add_action( 'template_redirect', 'kts_software_submit_form_redirect' );


/* EMAIL ALL SITE ADMINISTRATORS WHEN SOFTWARE SUBMITTED */
function kts_email_on_software_submitted( $post_id, $meta_key, $_meta_value ) {

	# Bail if not relevant CPT
	$post = get_post( $post_id );
	if ( ! in_array( $post->post_type, ['plugin', 'theme', 'snippet'] ) ) {
		return;
	}

	# Bail if not download link metadata
	if ( $meta_key !== 'download_link' ) {
		return;
	}

	# Get details of message
	$subject = __( 'A new ' . esc_html( $post->post_type ) . ' has been submitted for review', 'classicpress' );

	$post_admin_url = admin_url( 'post.php?post=' . $post->ID ) . '&action=edit';

	$message = __( 'The ' . esc_html( $post->post_type ) . '  is called ' . esc_html( $post->post_title ) . '. You will find the details on <a href="' . esc_url( $post_admin_url ) . '">this admin page</a>. The download link is ' . make_clickable( esc_url( $_meta_value ) ) . '.', 'classicpress' );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	# Get email addresses of administrators and send
	$users = get_users( array( 'role' => 'administrator' ) );
	foreach( $users as $user ) {
		wp_mail( $user->user_email, $subject, $message, $headers );
	}
}
add_action( 'add_post_meta', 'kts_email_on_software_submitted', 10, 3 );
