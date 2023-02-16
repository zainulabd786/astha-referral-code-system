<?php
add_shortcode('as_signup_form', 'as_signup_form');
function as_signup_form($atts){

    global $post;
    
    ob_start();
    $atts = shortcode_atts(array(), $atts, 'signup_form');
    if (!empty($_GET['message']) && !empty($_GET["status"]) ) { ?>
        <div class="alert alert-<?= $_GET['status'] ?>" role="alert">
            <?= $_GET['message'] ?>
        </div><?php
    } else{
        write_log(" GET['message'] or  GET['status'] is not supplied ");
    } ?>
    <div class="wrap as__form__wrap">
        <form action="<?= admin_url('admin-post.php') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="as_user_registration">
            <input type="hidden" name="redirect_url" value="<?php echo get_permalink($post->ID)  ?>" />
            <?php wp_nonce_field("as_user_registration_verify"); ?>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
            </div>
            
            <div class="mb-3">
                <label for="mobile">Mobile number</label>
                <div class="input-group">
                    <select class="form-select as-country-code-dropdown" name="country_code" aria-label="Country Code" required>
                        <option data-countryCode="IN" value="91">India (+91)</option>                        <option data-countryCode="US" value="1" selected>USA (+1)</option>
                        <option data-countryCode="US" value="1" selected>USA (+1)</option>
                    </select>
                    <input id="mobile" type="text" name="mobile_number" class="form-control" aria-label="Mobile Number" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="confirm_password" name="confirm_password" class="form-control" id="confirm_password" required>
            </div>
            <div id="password_alert" class="alert alert-danger" role="alert"></div>

            <div class="mb-3">
                <button type="submit" id="as_register" class="btn btn-primary">Signup</button>
            </div>
            
        </form>
    </div><?php
    return ob_get_clean();
}


add_action("admin_post_nopriv_as_user_registration", "as_user_registration");
add_action("admin_post_as_user_registration", "as_user_registration");
function as_user_registration(){
    check_admin_referer("as_user_registration_verify");
    global $signup_email_template;
    $email = $_POST["email"];
    $country_code = $_POST["country_code"];
    $mobile_number = $_POST["mobile_number"];
    $password = $_POST["password"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    $args = [
        "user_email" => $email,
        "user_pass" => $password,
        "user_login" => $email,
        "first_name" => $first_name,
        "last_name" => $last_name,
        "user_registered" => date("Y-m-d H:i:s"),
        'role' => 'subscriber',
    ];
    $result = wp_insert_user($args);

    if(gettype($result) === "integer"){ // wp_insert_user returns integer user id on success and object on failure
        add_user_meta($result, "country_code", $country_code, false);
        add_user_meta($result, "mobile_number", $mobile_number, false);
        add_user_meta($result, "status", "Pending", false);
        add_user_meta($result, USER_EARNINGS_META_KEY, maybe_serialize([]), false);

        $ambassadors = get_users( array( 'role' => AMBASSADOR_ADMIN_ROLE ) );
        $admin_emails = [];
        foreach ( $ambassadors as $ambassador ) { 
            array_push($admin_emails, $ambassador->user_email);
        }

        $subject = "New User registration on " . get_bloginfo("name");
        $body =
            "Email: " .
            $email .
            "<br/>" .
            "Mobile: +" .
            $country_code .
            " " .
            $mobile_number .
            "<br/>" .
            "View more details at " .
            get_admin_edit_user_link($result);
        $headers = ["Content-Type: text/html; charset=UTF-8"];
        $admin_email_response = wp_mail($admin_emails, $subject, $body, $headers);
        $user_mail_subject = str_replace("{ambassador_name}", $first_name." ".$last_name, $signup_email_template['subject']);
        $user_mail_body = str_replace("{ambassador_email}", $email, $signup_email_template['body'] );
        $user_mail_body = str_replace("{ambassador_password}", $password, $user_mail_body);

       $user_email_response = wp_mail(
            $email,
            $user_mail_subject,
            $user_mail_body,
            $headers
        );
        wp_redirect($_POST["redirect_url"]. '?message=Successfully Registered!&status=success');
    } else{
        write_log($result);
        wp_redirect($_POST["redirect_url"]. '?message='. $result->errors['existing_user_login'][0] .'&status=danger');
    }

    
    
}

add_shortcode('as_login_form', 'as_login_form');
function as_login_form($atts){

    ob_start();
    $atts = shortcode_atts(array(), $atts, 'login_form');
    $args = array(
        "echo" => false, 
        'redirect' => get_edit_profile_url(),
        "label_username" => "Email"
    );
    return wp_login_form($args);
}
