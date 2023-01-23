<?php

function remove_dashboard_meta() {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); //Removes the 'incoming links' widget
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); //Removes the 'plugins' widget
    remove_meta_box('dashboard_primary', 'dashboard', 'normal'); //Removes the 'WordPress News' widget
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal'); //Removes the secondary widget
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); //Removes the 'Quick Draft' widget
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); //Removes the 'Recent Drafts' widget
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); //Removes the 'Activity' widget
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); //Removes the 'At a Glance' widget
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); //Removes the 'Activity' widget (since 3.8)
}
add_action('admin_init', 'remove_dashboard_meta');

add_action( 'wp_dashboard_setup', 'register_my_dashboard_widget' );
function register_my_dashboard_widget() {
	wp_add_dashboard_widget(
		'my_dashboard_widget',
		'Ambassador Insights',
		'my_dashboard_widget_display'
	);

}

function my_dashboard_widget_display() {
    $role = 'subscriber';
    $ambassadors = get_users( array( 'role' => $role ) );
    $approved_ambassadors = get_users( array(
        'meta_key' => STATUS_META_KEY,
        'meta_value' => STATUS_APPROVED
    ) );
    $disapproved_ambassadors = get_users( array(
        'role' => $role,
        'meta_key' => STATUS_META_KEY,
        'meta_value' => STATUS_REJECTED
    ) );
    $pending_ambassadors = get_users( array(
        'role' => $role,
        'meta_key' => STATUS_META_KEY,
        'meta_value' => STATUS_PENDING
    ) );
    $code_assigned_users = get_users(
        array(
            'role' => $role,
            'meta_query' => array(
                array(
                    'key' => REFERRAL_CODE_META_KEY,
                    'value'   => array(''),
                    'compare' => 'NOT IN'
                )
            )
        )
    ); ?>
    <table class="ambassador-insights-table">
        <tr>
            <td><b>Total Amabassadors:</b></td>
            <td><?= count($ambassadors) ?></td>
        </tr>
        <tr>
            <td><b>Approved Amabassadors:</b></td>
            <td><?= count($approved_ambassadors) ?></td>
        </tr>
        <tr>
            <td><b>Disapproved Amabassadors:</b></td>
            <td><?= count($disapproved_ambassadors) ?></td>
        </tr>
        <tr>
            <td><b>Pending Approveal:</b></td>
            <td><?= count($pending_ambassadors) ?></td>
        </tr>
        <tr>
            <td><b>Total Refferal Code Assigned:</b></td>
            <td><?= count($code_assigned_users) ?></td>
        </tr>
    </table><?php
}