<?php


//removes unncessary options from edit profile page
add_action('admin_head','remove_personal_options');
function remove_personal_options(){
    echo '<script type="text/javascript">jQuery(document).ready(function($) {
$(\'form#your-profile > h2:first\').remove(); // remove the "Personal Options" title
$(\'form#your-profile tr.user-rich-editing-wrap\').remove(); // remove the "Visual Editor" field
$(\'form#your-profile tr.user-admin-color-wrap\').remove(); // remove the "Admin Color Scheme" field
$(\'form#your-profile tr.user-comment-shortcuts-wrap\').remove(); // remove the "Keyboard Shortcuts" field
$(\'form#your-profile tr.user-admin-bar-front-wrap\').remove(); // remove the "Toolbar" field
$(\'form#your-profile tr.user-language-wrap\').remove(); // remove the "Language" field
$(\'form#your-profile tr.user-first-name-wrap\').remove(); // remove the "First Name" field
$(\'form#your-profile tr.user-last-name-wrap\').remove(); // remove the "Last Name" field
$(\'form#your-profile tr.user-nickname-wrap\').hide(); // Hide the "nickname" field
$(\'table.form-table tr.user-display-name-wrap\').remove(); // remove the “Display name publicly as” field
$(\'table.form-table tr.user-url-wrap\').remove();// remove the "Website" field in the "Contact Info" section
$(\'h2:contains("About Yourself"), h2:contains("About the user")\').remove(); // remove the "About Yourself" and "About the user" titles
$(\'form#your-profile tr.user-description-wrap\').remove(); // remove the "Biographical Info" field
$(\'form#your-profile tr.user-profile-picture\').remove(); // remove the "Profile Picture" field
$(\'table.form-table tr.user-aim-wrap\').remove();// remove the "AIM" field in the "Contact Info" section
$(\'table.form-table tr.user-yim-wrap\').remove();// remove the "Yahoo IM" field in the "Contact Info" section
$(\'table.form-table tr.user-jabber-wrap\').remove();// remove the "Jabber / Google Talk" field in the "Contact Info" section
$(\'.application-passwords\').remove();// remove the "Jabber / Google Talk" field in the "Contact Info" section
});</script>';
  
}
  

add_action( 'edit_user_profile', 'as_custom_user_profile_fields' );
function as_custom_user_profile_fields( $user ){
    $udata = get_userdata($user->ID);
    $signup_date = date("d M Y", strtotime($udata->user_registered));
    $user_earnings = get_user_meta($user->ID, USER_EARNINGS_META_KEY, true);
    $current_user_status = get_user_meta($user->ID, STATUS_META_KEY, true);
    $referral_code = get_user_meta($user->ID, REFERRAL_CODE_META_KEY, true);
    echo '<h3 class="heading">Additional user information</h3>'; ?>
    
    <table class="form-table">
        <tr>
            <th>Registration Date</th>
            <td><?= $signup_date ?></td>
        </tr>
        <tr>
            <th>Mobile number</th>
            <td>+<?= get_user_meta($user->ID, 'country_code', true) ?> <?= get_user_meta($user->ID, 'mobile_number', true) ?></td>
        </tr>
        <tr>
            <th><label for="<?= STATUS_META_KEY ?>">Status</label></th>
            <td>
                <select name="<?= STATUS_META_KEY ?>" id="<?= STATUS_META_KEY ?>">
                    <option value="">Select User Status</option>
                    <option value="<?= STATUS_APPROVED ?>" <?= $current_user_status === STATUS_APPROVED ? "selected" : "" ?>><?= STATUS_APPROVED ?></option>
                    <option value="<?= STATUS_REJECTED ?>" <?= $current_user_status === STATUS_REJECTED ? "selected" : "" ?>><?= STATUS_REJECTED ?></option>
                    <option value="<?= STATUS_PENDING ?>" <?= $current_user_status === STATUS_PENDING ? "selected" : "" ?>><?= STATUS_PENDING ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="<?= REFERRAL_CODE_META_KEY ?>">Referral code</label></th>
            <td>
                <input type="text" name="<?= REFERRAL_CODE_META_KEY ?>" id="<?= REFERRAL_CODE_META_KEY ?>" value="<?= $referral_code ?>" placeholder="Referral Code" <?= $current_user_status !== STATUS_APPROVED ? 'disabled':'' ?>/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= PARTICULARS ?>">User earnings</label>
            </th>
            <td>
                <h6>Add Earnings</h6>
                <input type="text" name="<?= PARTICULARS ?>" id="<?= PARTICULARS ?>"  placeholder="Particulars" <?= empty($referral_code) ? "disabled" : '' ?> />
                <input type="text" name="<?= AMOUNT ?>" id="<?= AMOUNT ?>"  placeholder="Amount" <?= empty($referral_code) ? "disabled" : '' ?> />
                <?php get_user_earnings_table($user->ID) ?>
            </td>
        </tr>
    </table><?php
}

add_action( 'edit_user_profile_update', 'as_save_custom_user_profile_fields' );
function as_save_custom_user_profile_fields( $user_id ){
    if(!empty($_POST[PARTICULARS]) && !empty($_POST[AMOUNT])){
        $serialized_earnings = get_user_meta( $user_id, USER_EARNINGS_META_KEY, true);
        $user_earnings = $serialized_earnings ? maybe_unserialize($serialized_earnings) : [];
        array_unshift($user_earnings, array(
            'date' => current_time('d M Y'),
            PARTICULARS => $_POST[PARTICULARS],
            AMOUNT => $_POST[AMOUNT]
        ));
        update_user_meta( $user_id, USER_EARNINGS_META_KEY, maybe_serialize($user_earnings) );
    }
    if(!empty($_POST[REFERRAL_CODE_META_KEY])){
        update_user_meta( $user_id, REFERRAL_CODE_META_KEY, $_POST[REFERRAL_CODE_META_KEY] );
    }
    if(!empty($_POST[STATUS_META_KEY])){
        update_user_meta( $user_id, STATUS_META_KEY, $_POST[STATUS_META_KEY] );
    }
    
}

