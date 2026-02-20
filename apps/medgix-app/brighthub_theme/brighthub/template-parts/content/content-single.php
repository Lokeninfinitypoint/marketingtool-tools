<?php
/**
 * Template part for displaying posts in loop
 *
 * @package Case-Themes
 */
$post_tag = brighthub()->get_theme_opt( 'post_tag', true );
$post_navigation = brighthub()->get_theme_opt( 'post_navigation', false );
$post_social_share = brighthub()->get_theme_opt( 'post_social_share', false );
$tags_list = get_the_tag_list();
$sg_post_title = brighthub()->get_theme_opt('sg_post_title', 'default');
$title_custom = brighthub()->get_page_opt( 'title_custom', '' ); 
$titles = brighthub()->page->get_title();
?>
<article id="pxl-post-<?php the_ID(); ?>" <?php post_class('pxl-post'); ?>>
    <?php if(sidebar_active('blog')) brighthub()->blog->get_post_nav(true, true); ?>
    <?php if(sidebar_active('blog')) : ?>
        <h1 class="pxl-ptit__text"><?php echo brighthub_html(!empty($title_custom) ? $title_custom : $titles['title']); ?></h1>
    <?php endif; ?>
    <?php if(sidebar_active('blog')){
        $sg_featured_img_size = brighthub()->get_theme_opt('sg_featured_img_size', '1280x717');
        brighthub()->blog->pxl_get_post_meta();
        if (has_post_thumbnail()) {
            $img  = pxl_get_image_by_size( array(
                'attach_id'  => get_post_thumbnail_id(get_the_ID()),
                'thumb_size' => $sg_featured_img_size,
            ) );
            $thumbnail    = $img['thumbnail']; ?>
            <div class="pxl-post__image">
                <?php echo wp_kses_post($thumbnail); ?>
            </div>
        <?php } 
    } ?>
    <div class="pxl-post__content">
        <?php
            the_content();
            wp_link_pages( array(
                'before'      => '<div class="page-links">',
                'after'       => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
            ) );
        ?>
    </div>
    <?php if($post_tag && $tags_list ) : 
        brighthub()->blog->get_tagged_in();
    endif; ?>   
    <?php if($post_navigation || $post_social_share) :  ?>
        <div class="pxl-post__footer">
            <?php 
                brighthub()->blog->get_post_nav(); 
                brighthub()->blog->get_socials_share();
            ?>
        </div>
    <?php endif; ?>
</article>