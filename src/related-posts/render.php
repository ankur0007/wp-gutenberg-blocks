<?php

$postID = get_the_ID();
$postsToShow = $attributes['postsToShow'] ?? 3;
$postType = $attributes['postType'] ?? 'post';
$taxonomy = $attributes['taxonomy'] ?? 'category';
$terms = $attributes['terms'] ?? [];
$order = $attributes['order'] ?? 'DESC';
$orderby = $attributes['orderby'] ?? 'date';

$posts = get_posts([
    'post_type' => $postType,
    'posts_per_page' => $postsToShow,
    'orderby' => $orderby,
    'order' => $order,
    'post__not_in' => [$postID],
    
]);

?>
<div class="avcl-related-posts">
    <div class="avcl-related-posts__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $postsToShow ); ?>, 1fr);">

    <?php if(!empty($posts)):

        foreach ($posts as $post):
            
            $image_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
           
            $image_url = $image_url ?: plugins_url(
                'assets/images/placeholder.jpg',
                dirname( __FILE__, 3 ) . '/avcl-blocks.php'
            );

            
        ?>
        <article class="avcl-related-posts__card">
            <a class="avcl-related-posts__image-link" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                <img
                    class="avcl-related-posts__image"
                    src="<?php echo esc_url($image_url); ?>"
                    alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>"
                >
            </a>

            <div class="avcl-related-posts__content">
                <h3 class="avcl-related-posts__title">
                    <a href="#"><?php echo esc_html($post->post_title);?></a>
                </h3>

                <p class="avcl-related-posts__excerpt">
                    <?php echo esc_html(get_the_excerpt($post));?>
                </p>

                <a class="avcl-related-posts__link" href="<?php echo get_permalink($post->ID);?>">
                    <?php esc_html_e( 'Read More', 'avcl-blocks' ); ?>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </article>
            <?php endforeach; endif;?>
        <!-- Repeat the article for each related post -->

    </div>
