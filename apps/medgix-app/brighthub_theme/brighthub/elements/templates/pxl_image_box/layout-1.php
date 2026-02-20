<div class="pxl-image-box pxl-image-box--layout-1 <?php echo esc_attr($settings['pxl_animate']); ?>" data-wow-delay="<?php echo esc_attr($settings['pxl_animate_delay']); ?>ms" >
    <div class="pxl-image-box__image">
        <div class="pxl-image-box__star">
            <?php \Elementor\Icons_Manager::render_icon( $settings['star_icon'], [ 'aria-hidden' => 'true' ] ); ?>
            <?php echo esc_html($settings['star_number']); ?>
        </div>
        <?php $image_size = 'full';
            if(!empty($settings['image']['id'])) : $img_id = $settings['image']['id']; endif;
            $img  = pxl_get_image_by_size( array(
                'attach_id'  => $img_id,
                'thumb_size' => $image_size,
                'class' => ''
            ) );
            $thumbnail    = $img['thumbnail'];
            $thumbnail_url    = $img['url']; ?>
        <?php if ( ! empty( $img_id ) ) { echo wp_kses_post($thumbnail); } ?>
    </div>
    <div class="pxl-image-box__content">
        <div class="pxl-image-box__title"><?php echo esc_html($settings['title']); ?></div>
        <div class="pxl-image-box__position"><?php echo esc_html($settings['position']); ?></div>
    </div>
</div>