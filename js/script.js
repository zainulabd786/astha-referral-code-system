jQuery(document).ready(function ($) {
    // Confirm password validation
    $("#password_alert").hide();
    $("#confirm_password").on('keyup', function(){
     var password = $("#password").val();
     var confirmPassword = $("#confirm_password").val();
     if (password != confirmPassword){
        $("#password_alert").show().html("Password does not match !");
        $("#as_register").attr('disabled', true)
     } else {
        $("#password_alert").hide();
        $("#as_register").attr('disabled', false)
     }
         
    });
 });