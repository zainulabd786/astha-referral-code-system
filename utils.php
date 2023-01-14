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


