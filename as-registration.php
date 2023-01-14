<?php

/**
 * Plugin Name: Astha ambassador management
 * Description: Astha ambassador registation and login
 * Version: 1.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Zainul Abideen
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: as-domain
 * Domain Path: /languages
 */

define('REFERRAL_CODE_META_KEY', 'referral_code');

include (plugin_dir_path(__FILE__) . 'utils.php');
include (plugin_dir_path(__FILE__) . 'includes/bulk_code_assignment.php');
include (plugin_dir_path(__FILE__) . 'includes/user_signup_login.php');
include (plugin_dir_path(__FILE__) . 'includes/user_profile_customizations.php');

// Changes Subscriber name to Ambassador
add_action('init', 'change_role_name');
function change_role_name() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

    $wp_roles->roles['subscriber']['name'] = 'Ambassador';
    $wp_roles->role_names['subscriber'] = 'Ambassador';           
}

//Adds status and Referral Code column to Users table
add_filter( 'manage_users_columns', 'new_modify_user_table' );
function new_modify_user_table( $column ) {
    $column['status'] = 'Status';
    $column[REFERRAL_CODE_META_KEY] = 'Referral Code';
    return $column;
}

add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );
function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'status' :
            return get_user_meta( $user_id, 'status', true);
        case REFERRAL_CODE_META_KEY:
            return get_user_meta( $user_id, REFERRAL_CODE_META_KEY, true);
        default:
    }
    return $val;
}

// Extend users search to consider user metadata while performing search
add_action( 'pre_user_query', 'project_pre_user_search'  );
function project_pre_user_search( $query ) {
    global $wpdb;
    global $pagenow;

    if (is_admin() && 'users.php' == $pagenow) {
        if( empty($_REQUEST['s']) ){return;}
        $query->query_fields = 'DISTINCT '.$query->query_fields;
        $query->query_from .= ' LEFT JOIN '.$wpdb->usermeta.' ON '.$wpdb->usermeta.'.user_id = '.$wpdb->users.'.ID';
        $query->query_where = "WHERE 1=1 AND (user_login LIKE '%".$_REQUEST['s']."%' OR ID = '".$_REQUEST['s']."' OR (meta_value LIKE '%".$_REQUEST['s']."%' AND meta_key = 'status'))";
    }
    return $query;
}

//Adds Approve Users action to bulk actions dropdown
add_filter('bulk_actions-users', function($bulk_actions) {
	$bulk_actions['approve-users'] = __('Approve Users', 'as-domain');
	$bulk_actions['reject-users'] = __('Reject Users', 'as-domain');
	return $bulk_actions;
});
add_filter('handle_bulk_actions-users', function($redirect_url, $action, $user_ids) {
	if ($action == 'approve-users') {
		foreach ($user_ids as $user_id) {
            update_user_meta($user_id, 'status', 'Approved');
		}
		$redirect_url = add_query_arg('approve-users', count($user_ids), $redirect_url);
	} elseif ($action == 'reject-users') {
		foreach ($user_ids as $user_id) {
            update_user_meta($user_id, 'status', 'Rejecteed');
		}
		$redirect_url = add_query_arg('reject-users', count($user_ids), $redirect_url);
	}
	return $redirect_url;
}, 10, 3);
 
// Show success message on custom bulk options
add_action('admin_notices', function() {
	if (!empty($_REQUEST['approve-users'])) {
		$num_changed = (int) $_REQUEST['approve-users'];
		printf('<div id="message" class="updated notice is-dismissable"><p>' . __('%d Users Approved.', 'as-domain') . '</p></div>', $num_changed);
	}
});

add_action('wp_enqueue_scripts', 'enqueue_scripts');
add_action('admin_enqueue_scripts', 'enqueue_scripts');
function enqueue_scripts(){
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js', array(''), '3.6.1', true);
    wp_enqueue_script('as-js', plugins_url('/js/script.js', __FILE__), array('jquery'));
    wp_enqueue_style('style-css', plugins_url('/css/style.css', __FILE__));
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
}

