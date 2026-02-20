<?php
$p_menu = brighthub()->get_page_opt('p_menu');
if (!empty($p_menu)) {
    ?>
    <div class="pxl-menu <?php echo esc_attr($settings['menu_mega_type'].' '.'pxl-menu__'.$settings['menu_type'].' '.$settings['sub_show_effect'].' '.$settings['hover_active_style'].' '.$settings['pxl_animate']); ?>" data-wow-delay="<?php echo esc_attr($settings['pxl_animate_delay']); ?>ms" >
        <?php
        wp_nav_menu(array(
            'menu'           => $p_menu,
            'theme_location' => 'primary',
            'menu_class'     => 'pxl-menu__primary clearfix',
            'link_before'    => '<span class="pxl-menu__text">',
            'link_after'     => '</span><i class="caseicon-angle-arrow-down pxl-hide"></i></span>',
            'walker'         => class_exists( 'PXL_Mega_Menu_Walker' ) ? new PXL_Mega_Menu_Walker : '',
        ));
        ?>
        <?php if($settings['hover_active_style'] == 'divider-move') : ?>
            <div class="pxl-menu__divider"></div>
        <?php endif; ?>
    </div>
<?php
} elseif ( has_nav_menu( 'primary' ) ) { ?>
    <div class="pxl-menu <?php echo esc_attr($settings['menu_mega_type'].' pxl-menu__'.$settings['menu_type'].' '.$settings['hover_active_style'].' '.$settings['sub_show_effect']); ?>">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class'     => 'pxl-menu__primary clearfix',
            'link_before'    => '<span class="pxl-menu__text">',
            'link_after'     => '</span><i class="caseicon-angle-arrow-down pxl-hide"></i></span>',
            'walker'         => class_exists( 'PXL_Mega_Menu_Walker' ) ? new PXL_Mega_Menu_Walker : '',
        ));
        ?>
        <?php if($settings['hover_active_style'] == 'divider-move') : ?>
            <div class="pxl-menu__divider"></div>
        <?php endif; ?>
    </div>
    
<?php } ?>
