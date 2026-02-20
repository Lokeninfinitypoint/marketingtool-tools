<div class="pxl-step pxl-step__style-3 <?php echo esc_attr($settings['auto_play_l3'] ? 'auto-play' : ''); ?> <?php echo esc_attr($settings['pxl_animate']); ?>" data-wow-delay="<?php echo esc_attr($settings['pxl_animate_delay']); ?>ms" >
    <ul class="pxl-step__feature">
        <?php
            foreach ($settings['step_l3'] as $key => $item):
                $title = $widget->parse_text_editor( $item['l3_title'] ); 
                $desc = $widget->parse_text_editor( $item['l3_desc'] ); ?>
                <li class="pxl-step__feature-item <?php echo esc_attr($desc ? 'has-content' : ''); ?> <?php echo esc_attr($key === 0 ? 'active' : ''); ?>">
                    <?php if($title || $desc) : ?>
                        <span class="pxl-step__feature-content">
                            <?php if($title) : ?>
                                <span class="pxl-step__feature-title"><?php echo pxl_print_html($title); ?></span>
                            <?php endif; ?>
                            <?php if($desc) : ?>
                                <span class="pxl-step__feature-desc"><?php echo pxl_print_html($desc); ?></span>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </li>
        <?php endforeach; ?>
    </ul>
</div>