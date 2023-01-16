<?php 
    if(!is_user_logged_in()){
        wp_die('You are not allowed to access this page');
    }

    get_header();   
?>

<div class="p-4">
    <nav id="site-navigation" class="main-navigation" aria-label="Top Menu">
    <div class="menu-main-menu-container">
        <ul id="top-menu" class="menu">
            <li class="menu-item menu-item-type-post_type menu-item-object-page">
                <a href="<?php echo get_edit_profile_url( ); ?>">My Account</a>
            </li>
            <li class="menu-item menu-item-type-post_type menu-item-object-page">
                <a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a>
            </li>
        </ul>
    </div>
    </nav>

    <?= do_shortcode('[as_earnings_table]') ?>
</div>

<?php get_footer(); ?>