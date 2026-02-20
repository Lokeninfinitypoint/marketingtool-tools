<?php
/**
 * Header
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="pxl-header">
    <div class="container">
        <div class="logo">
            <a href="<?php echo home_url('/'); ?>">
                <span class="logo-text">MarketingTool Pro</span>
            </a>
        </div>
        <nav>
            <a href="<?php echo home_url('/'); ?>">Home</a>
            <a href="<?php echo home_url('/services'); ?>">Services</a>
            <a href="#" class="dropdown-toggle">Resources</a>
            <a href="#" class="dropdown-toggle">Company</a>
            <a href="<?php echo home_url('/blog'); ?>">Blog</a>
        </nav>
        <div class="header-buttons">
            <a href="<?php echo home_url('/signin'); ?>" class="btn-signin">Sign In</a>
            <a href="<?php echo home_url('/trial'); ?>" class="btn-trial">Free Trial</a>
        </div>
    </div>
</header>
