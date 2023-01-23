<?php 
add_action('admin_menu', 'as_register_bulk_assignment_submenu_page');
function as_register_bulk_assignment_submenu_page() {
    add_submenu_page( 
		'users.php',
        'Upload CSV',
        'Bulk code assignment',
        'edit_users',
        'bulk_assignment',
        'bulk_assignment_page_callback',
    );
}

function bulk_assignment_page_callback() {
    if (!current_user_can('edit_users')) wp_die(__('You do not have sufficient permissions to access this page.'));
    $updated_users = !empty($_GET['updated_users']) ? $_GET['updated_users'] : ''; ?>
    <h3><?= get_admin_page_title() ?></h3><?php
    if ($updated_users > 0){ ?>
        <div class="update-nag notice notice-success" role="alert">
            CSV successfully imported! <b><?= $updated_users ?></b> users updated successfully!
        </div><?php
    } elseif(isset($_GET['updated_users'])) { ?>
        <div class="update-nag notice notice-warning" role="alert">
            No records updated!
        </div><?php
    } ?>
    <div class="wrap">
        <form action="admin-post.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="as_start_csv_import">
            <?php wp_nonce_field("as_import_csv_form_verify"); ?>
            <input type="file" name="csv_file" accept=".csv" />
            <button type="submit" class="button button-primary button-large">Start Import</button>
        </form>
    </div><?php
}

add_action("admin_post_as_start_csv_import", "as_start_csv_import");
function as_start_csv_import(){
    if (!current_user_can('manage_options')) wp_die('You are not allowed to be on this page');
    check_admin_referer("as_import_csv_form_verify");
    $tmp_name = $_FILES['csv_file']['tmp_name'];
    $rows = convert_csv_to_arr($tmp_name);
    $upload_statuses = [];
    foreach ($rows as $row){
        $user = get_user_by( 'email', $row['email'] );
        $update_status = assign_code_to_user($user->ID, $row['code'], STATUS_APPROVED);
        array_push($upload_statuses, $update_status);
    }
    $number_of_updated_users = array_filter($upload_statuses, function($i) {
        return $i === 'success';
    });

    wp_redirect(admin_url('admin.php?page=bulk_assignment&updated_users=' . count($number_of_updated_users)));
}
