<?php if ( !empty($settings['textbox']) && count($settings['textbox']) > 0 ) : ?>
    <div class="pxl-marquee">
        <ul class="pxl-marquee__list pxl-marquee__list--textbox pxl-marquee__list--scrollbar-<?php echo esc_attr($settings['show_scrollbar']); ?> pxl-marquee__<?php echo esc_attr($settings['direction']); ?> pxl-marquee__mb-<?php echo esc_attr($settings['direction_mobile']); ?>">
            <?php foreach ($settings['textbox'] as $key => $item) : ?>
                <li class="pxl-marquee__item">
                    <div class="pxl-marquee__item-title">
                        <?php echo esc_html($item['title']); ?>
                    </div>
                    <div class="pxl-marquee__item-description">
                        <?php echo esc_html($item['description']); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>