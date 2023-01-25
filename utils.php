<?php

if (!function_exists('write_log')) {

    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

function convert_csv_to_arr($csv)
{
    $array = $fields = array();
    $i = 0;
    $handle = @fopen($csv, "r");
    if ($handle) {
        while (($row = fgetcsv($handle, 4096)) !== false) {
            if (empty($fields)) {
                $fields = $row;
                continue;
            }
            foreach ($row as $k => $value) {
                $array[$i][$fields[$k]] = $value;
            }
            $i++;
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    return $array;
}


function get_admin_edit_user_link($user_id){
    if (get_current_user_id() == $user_id)
        $edit_link = get_edit_profile_url($user_id);
    else
        $edit_link = add_query_arg('user_id', $user_id, self_admin_url('user-edit.php'));
        
    return $edit_link;
}

function get_user_earnings_table($user_id){
    $earnings = maybe_unserialize(get_user_meta($user_id, USER_EARNINGS_META_KEY, true)); ?>
    <!-- <div class="date-filters-wrap">
        <label>Show records</label>
        <div class="inputs">
            <div>
                <input type="text" id="min" name="min" placeholder="From date">
            </div>
            <div>
                <input type="text" id="max" name="max" placeholder="To date">
            </div>
        </div>
    </div> -->
    <table id="earnings_table">
        <thead>
            <th>Date Duration</th>
            <th>No. of Referrals</th>
        </thead>
        <tbody><?php
            if($earnings){
                foreach($earnings as $earning){ ?>
                    <tr>
                        <td><?= isset($earning[PARTICULARS]) ? $earning[PARTICULARS] : '' ?></td>
                        <td><?= isset($earning[AMOUNT]) ? $earning[AMOUNT] : '' ?></td>
                    </tr><?php
                }
                
            } ?>
        </tbody>
    </table><?php
}


function assign_code_to_user($user_id, $code, $status){
    $code = $status === STATUS_APPROVED ? $code : "";
    $status_update = update_user_meta($user_id, STATUS_META_KEY, $status);
    $code_update = update_user_meta($user_id, REFERRAL_CODE_META_KEY, $code);
    if($code && $code_update === true){
        $user = get_user_by( 'ID', $user_id );
        $user_email = $user->data->user_email;
        $subject = "Account status updated on " . get_bloginfo("name");
        $body = "Your account status updated to <b> " . $status . " </b> on " .
        get_bloginfo("name") . ". Your Referral Code is <b>". $code ."<b/>";
        $headers = array('Content-Type: text/html; charset=UTF-8');
        write_log($user_email);
        wp_mail(
            $user_email,
            $subject,
            $body,
            $headers
        );
        return "success";
    } else return "failed";
}