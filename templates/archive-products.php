<?php
use Invento\Helpers\View;

get_header();

$settings = get_option( 'invento_settings', [] );
$layout   = isset( $settings['catalog_layout'] ) ? $settings['catalog_layout'] : 'grid';
?>
<div class="invento-archive-products invento-catalog-<?php echo esc_attr( $layout ); ?>">
    <header class="invento-archive-header">
        <h1><?php post_type_archive_title(); ?></h1>
    </header>

    <div class="invento-archive-grid">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php View::render( __DIR__ . '/parts/product-card.php', [ 'post_id' => get_the_ID(), 'layout' => $layout ] ); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php echo esc_html__( 'No products found.', 'invento' ); ?></p>
        <?php endif; ?>
    </div>

    <div class="invento-pagination">
        <?php the_posts_pagination(); ?>
    </div>
</div>
<?php
get_footer();
