<div class="pxl-marquee">
    <ul class="pxl-marquee__list pxl-marquee__list--testimonial style-<?php echo esc_attr($settings['style']); ?> pxl-marquee__<?php echo esc_attr($settings['direction']); ?> pxl-marquee__mb-<?php echo esc_attr($settings['direction_mobile']); ?>">
        <?php foreach ($settings['items'] as $item) : ?>
            <li class="pxl-marquee__item">
                <?php if (!empty($item['star'])) : ?>
                    <span class="pxl-marquee__star">
                        <span class="pxl-marquee__star-wrap">
                            <?php for ($i = 0; $i < $item['star']; $i++) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M6 0L7.41068 4.05836L11.7063 4.1459L8.28254 6.74164L9.52671 10.8541L6 8.4L2.47329 10.8541L3.71746 6.74164L0.293661 4.1459L4.58932 4.05836L6 0Z" fill="white"/>
                                </svg>
                            <?php endfor; ?>
                        </span>
                        <span class="pxl-marquee__star-value"><?php echo esc_html($item['star']); ?>/5</span>
                    </span>
                <?php endif; ?>
                <?php if($settings['style'] == '2') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18" fill="none">
                        <path d="M13.548 17.1096V8.80802L16.6152 1.38601C16.8264 1.01159 17.2152 0.771592 17.6448 0.75L20.3184 0.75C20.712 0.75 20.892 1.03081 20.712 1.38601L17.664 8.80802H22.6872C23.0808 8.80802 23.4 9.12721 23.4 9.52081V17.1096C23.4 17.5032 23.0808 17.8224 22.6872 17.8224H14.2608C13.8672 17.8224 13.548 17.5032 13.548 17.1096Z" fill="white"/>
                        <path d="M0.599976 17.1096L0.599976 8.80802L3.66478 1.38601C3.87836 1.01159 4.26718 0.771592 4.69679 0.75L7.36797 0.750001C7.76397 0.750001 7.9416 1.03081 7.76397 1.38601L4.71358 8.80802H9.7392C10.1328 8.80802 10.452 9.12721 10.452 9.52081L10.452 17.1096C10.452 17.5032 10.1328 17.8224 9.73919 17.8224H1.31277C0.919194 17.8224 0.599976 17.5032 0.599976 17.1096Z" fill="white"/>
                    </svg>
                    <?php endif; ?>
                <span class="pxl-marquee__item-content">
                    <?php echo esc_html($item['content']); ?>
                </span>
                <span class="pxl-marquee__item-author">
                    <?php if (!empty($item['avatar'])) : ?>
                        <?php if(!empty($item['avatar']['id'])) { 
                            $img = pxl_get_image_by_size( array(
                                'attach_id'  => $item['avatar']['id'],
                                'thumb_size' => '40x40',
                                'class' => '',
                            ));
                            $thumbnail = $img['thumbnail'];?>
                            <span class="pxl-marquee__item-avatar">
                                <?php echo wp_kses_post($thumbnail); ?>
                            </span>
                        <?php } ?>
                    <?php endif; ?>
                    <?php if ($item['name']) : ?>
                        <span class="pxl-marquee__item-name"><?php echo esc_html($item['name']); ?></span>
                    <?php endif; ?>
                    <?php if ($item['position']) : ?>
                        <span class="pxl-marquee__item-pos"><?php echo esc_html($item['position']); ?></span>
                    <?php endif; ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>