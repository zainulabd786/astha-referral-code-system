<?php 
    if(!is_user_logged_in()){
        wp_die('You are not allowed to access this page');
    }

    get_header();   
?>

<div class="p-4">
    <?= do_shortcode('[as_earnings_table]') ?>
</div>

<?php get_footer(); ?>