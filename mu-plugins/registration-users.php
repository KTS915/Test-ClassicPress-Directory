<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Registration Form and User Enhancements
 * Plugin URI: https://directory.classicpress.net/
 * Description: Adds fields to default registration form
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/

/* ADD CUSTOM FIELDS TO REGISTRATION FORM */
function kts_registration_form_fields() { ?>

	<p>
		<label for="first_name"><?php _e( 'First Name', 'classicpress' ); ?></label>
		<br>
		<input id="first_name" name="first_name" type="text" required>
	</p>

	<p>
		<label for="last_name"><?php _e( 'Last Name', 'classicpress' ); ?></label>
		<br>
		<input id="last_name" name="last_name" type="text" required>
	</p>

	<p>
		<label for="repo_url"><?php _e( 'Developer Repository URL', 'classicpress' ) ?></label>
		<div id="repo_explain"><?php _e( '(full URL beginning https://github.com/)', 'classicpress' ); ?></div>
		<input id="repo_url" name="repo_url" type="url" aria-describedby="repo_explain" required>
	</p>

<?php
}
add_action( 'register_form', 'kts_registration_form_fields' );


/* ENABLE ERROR MESSAGES FOR CUSTOM FIELDS */
function kts_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'classicpress' ) );
	}

	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'last_name_error', __( '<strong>ERROR</strong>: Please enter your last name.', 'classicpress' ) );
	}

	if ( empty( $_POST['repo_url'] ) ) {
		$errors->add( 'repo_url_error', __( '<strong>ERROR</strong>: Please enter your GitHub repository URL.', 'classicpress' ) );
	}
	elseif ( filter_var( $_POST['repo_url'], FILTER_VALIDATE_URL ) === false ) {
		$errors->add( 'invalid_url_error', __( '<strong>ERROR</strong>: Your GitHub repository URL must be a valid URL.', 'classicpress' ) );
	}
	elseif ( strpos( $_POST['repo_url'], 'https://github.com/' ) !== 0 ) {
		$errors->add( 'invalid_url_error', __( '<strong>ERROR</strong>: You must specify a URL to a GitHub repository.', 'classicpress' ) );
	}

	return $errors;
}
add_filter( 'registration_errors', 'kts_registration_errors', 10, 3 );


/* PROCESS CUSTOM FIELDS */
function kts_register_custom_fields( $user_id ) {

	# Require first and last name
	if ( empty( $_POST['first_name'] ) ) {
		return;
	}

	if ( empty( $_POST['last_name'] ) ) {
		return;
	}

	# Require Github Repo URL if not an editor or administrator
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_pages' ) ) {

		if ( empty( $_POST['repo_url'] ) ) {
			return;
		}

		if ( filter_var( $_POST['repo_url'], FILTER_VALIDATE_URL ) === false ) {
			return;
		}

		if ( strpos( $_POST['repo_url'], 'https://github.com/' ) !== 0 ) {
			return;
		}
	}

	# OK to update custom fields
	$first_name = sanitize_text_field( wp_unslash( $_POST['first_name'] ) );
	update_user_meta( $user_id, 'first_name', $first_name );

	$last_name = sanitize_text_field( wp_unslash( $_POST['last_name'] ) );
	update_user_meta( $user_id, 'last_name', $last_name );

	$repo_url = esc_url_raw( wp_unslash( $_POST['repo_url'] ) );
	update_user_meta( $user_id, 'repo_url', $repo_url );
}
add_action( 'user_register', 'kts_register_custom_fields' );
add_action( 'edit_user_created_user', 'kts_register_custom_fields' ); // backend
add_action( 'personal_options_update', 'kts_register_custom_fields' );
add_action( 'edit_user_profile_update', 'kts_register_custom_fields' );


/* BACK-END USER REGISTRATION CUSTOM FIELDS */
function kts_user_admin_register_custom_fields( $user ) { ?>
	
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row">
				<label for="repo_url"><?php _e( 'Developer Repository URL', 'classicpress' ) ?></label> <span class="description"><?php _e( '(required)', 'classicpress' ); ?></span>
			</th>

			<td>
				<input id="repo_url" name="repo_url" type="url" class="code" aria-describedby="repo_explain" value="<?php echo esc_url( get_user_meta( $user->ID, 'repo_url', true ) ); ?>" required>
				<br>
				<div id="repo_explain"><?php _e( 'Full URL beginning https://github.com/', 'classicpress' ); ?></div>
			</td>
		</tr>
	</table>

<?php
}
add_action( 'user_new_form', 'kts_user_admin_register_custom_fields' );
add_action( 'show_user_profile', 'kts_user_admin_register_custom_fields' );
add_action( 'edit_user_profile', 'kts_user_admin_register_custom_fields' );


/* BACKEND CUSTOM FIELDS ERRORS */
function kts_user_profile_update_errors( $errors, $update, $user ) {

	# Require first and last name
	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: Please enter a first name.', 'classicpress' ) );
	}

	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'last_name_error', __( '<strong>ERROR</strong>: Please enter a last name.', 'classicpress' ) );
	}

	# Require Github Repo URL if not an editor or administrator
	if ( ! current_user_can( 'edit_pages' ) ) {
		if ( empty( $_POST['repo_url'] ) ) {
			$errors->add( 'repo_url_error', __( '<strong>ERROR</strong>: Please enter a GitHub repository URL.', 'classicpress' ) );
		}
		elseif ( filter_var( $_POST['repo_url'], FILTER_VALIDATE_URL ) === false ) {
			$errors->add( 'invalid_url_error', __( '<strong>ERROR</strong>: The GitHub repository URL must be a valid URL.', 'classicpress' ) );
		}
		elseif ( strpos( $_POST['repo_url'], 'https://github.com/' ) !== 0 ) {
			$errors->add( 'invalid_url_error', __( '<strong>ERROR</strong>: You must specify a URL to a GitHub repository.', 'classicpress' ) );
		}
	}
}
add_action( 'user_profile_update_errors', 'kts_user_profile_update_errors', 10, 3 );


/* DISPLAY EXTRA INFO ON USERS LIST ADMIN PAGE */
function kts_add_user_admin_columns( $columns ) {
	$columns['plugin'] = __( 'Plugins', 'classicpress' );
	$columns['theme'] = __( 'Themes', 'classicpress' );
	$columns['snippet'] = __( 'Snippets', 'classicpress' );
	$columns['repo_url'] = __( 'Repo URL', 'classicpress' );
	$columns['last_login'] = __( 'Last Login', 'classicpress' );
	unset( $columns['posts'] );
	return $columns;
}
add_filter( 'manage_users_columns', 'kts_add_user_admin_columns' );

function kts_user_meta_columns( $custom_column, $column_name, $user_id ) {	
	$timezone = get_option( 'timezone_string' );

	switch( $column_name ) {
		case 'plugin':
			return count_user_posts( $user_id, 'plugin' );
		break;
		case 'theme':
			return count_user_posts( $user_id, 'theme' );
		break;
		case 'snippet':
			return count_user_posts( $user_id, 'snippet' );
		break;
		case 'repo_url':
			return get_user_meta( $user_id, 'repo_url', true );
		break;
		case 'last_login':
			$last_login = (int) get_user_meta( $user_id, 'last_login', true );
			return $last_login ? kts_ts2time( $last_login, $timezone ) : '';
		break;
	}
}
add_action( 'manage_users_custom_column', 'kts_user_meta_columns', 9, 3 );


/* RECORD LAST SUCCESSFUL LOGIN */
function kts_user_last_login( $user_login, $user ) {

	# Set time
    update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'kts_user_last_login', 9, 2 );


/* CHANGE AUTHOR BASE TO DEVELOPER */
function kts_change_author_base() {
	global $wp_rewrite;
	$wp_rewrite->author_base = 'developers';
}
add_action( 'init', 'kts_change_author_base' );


/* REMOVE TITLE PREFIX ON ARCHIVE PAGES */
function kts_change_author_archive_base( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    }
    elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    }
    elseif ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    }
    elseif ( is_tax() ) {
        $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
    }
    elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'kts_change_author_archive_base' );


/* KEEP THOSE OTHER THAN EDITORS AND ADMINISTRATORS ON PROFILE PAGE IN ADMIN */
function kts_restrict_admin_access() {
	global $pagenow;

	if ( ! current_user_can( 'edit_pages' ) ) {
		if ( $pagenow !== 'profile.php' ) {
			wp_redirect( esc_url_raw( admin_url( 'profile.php' ) ) );
			exit;
		}
	}
}
add_action( 'admin_init', 'kts_restrict_admin_access' );


/* ADD MESSAGE TO TOP OF EACH SUBSCRIBER'S PROFILE PAGE */
function kts_profile_message() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		_e( '<div class="notice is-dismissible"><p id="two-fa"><strong>You need to activate <a href="#two-factor-options">Two Factor Authentication</a> before you can be approved to submit software to the Directory.</strong></p></div>', 'classicpress' );
	} 
}
add_action( 'admin_notices', 'kts_profile_message' );


/* REMOVE ADMIN MENU ITEMS FOR THOSE OTHER THAN EDITORS AND ADMINISTRATORS */
function kts_remove_admin_menu_items() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=plugin' );
		remove_menu_page( 'edit.php?post_type=theme' );
		remove_menu_page( 'edit.php?post_type=snippet' );
		remove_menu_page( 'edit.php?post_type=review' );
		remove_menu_page( 'edit.php?post_type=message' );
		remove_menu_page( 'edit.php?post_type=page' );
		remove_menu_page( 'upload.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'plugins.php' );
	}
}
add_action( 'admin_menu', 'kts_remove_admin_menu_items' );


/* ADD LINKS TO THE ADMIN BAR */
function kts_add_toolbar_links( $admin_bar ) {
	$user_id = get_current_user_id();

	$admin_bar->add_menu( array(
		'id'    => 'my-software',
		'title' => __( 'Software Links' ),
		'href'  => '#',
		'meta'  => array(
		'title' => __( 'Software Links' ),            
		),
	) );
	$admin_bar->add_menu( array(
		'id'    => 'my-plugins',
		'parent' => 'my-software',
		'title' => __( 'My Plugins' ),
		'href'  => esc_url( get_author_posts_url( $user_id ) . '#ui-id-1' ),
		'meta'  => array(
			'title' => __( 'My Plugins' ),
			'target' => '_blank',
			'class' => 'my_menu_item_class'
		),
	) );
	$admin_bar->add_menu( array(
		'id'    => 'my-themes',
		'parent' => 'my-software',
		'title' => __( 'My Themes' ),
		'href'  => esc_url( get_author_posts_url( $user_id ) . '#ui-id-2' ),
		'meta'  => array(
			'title' => __( 'My Themes' ),
			'target' => '_blank',
			'class' => 'my_menu_item_class'
		),
	) );
	$admin_bar->add_menu( array(
		'id'    => 'my-snippets',
		'parent' => 'my-software',
		'title' => __( 'My Snippets' ),
		'href'  => esc_url( get_author_posts_url( $user_id ) . '#ui-id-3' ),
		'meta'  => array(
			'title' => __( 'My Snippets' ),
			'target' => '_blank',
			'class' => 'my_menu_item_class'
		),
	) );
}
add_action( 'admin_bar_menu', 'kts_add_toolbar_links', 100 );


/* EMAIL DEVELOPERS WHEN ROLE CHANGED TO CONTRIBUTOR */
function kts_email_on_role_to_contributor( $user_id, $new_role, $old_roles ) {
		 	
	# Get role info
	$user = get_userdata( $user_id );
	
	# Send email when membership is first approved
	if ( in_array( 'subscriber', $old_roles ) && $new_role === 'contributor' ) {
		$to = $user->user_email;
		$subject = __( 'ClassicPress Directory Approval', 'classicpress' );
		$message = __( 'You have been approved to submit software to be listed in the <a href="' . esc_url( home_url( '/' ) ) . '">ClassicPress Directory</a>.', 'classicpress' );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $message, $headers );
	}	
}
add_action( 'set_user_role', 'kts_email_on_role_to_contributor', 10, 3 );
