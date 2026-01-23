<?php
use Invento\Helpers\Formatting;

if ( ! isset( $post_id ) ) {
    $post_id = get_the_ID();
}

$post = get_post( $post_id );
if ( ! $post ) {
    return;
}

$from_shortcode = isset( $from_shortcode ) && $from_shortcode;

$short_description = get_post_meta( $post_id, '_invento_short_description', true );
if ( ! $short_description ) {
    $short_description = get_the_excerpt( $post_id );
}

$stock_mode     = get_post_meta( $post_id, '_invento_stock_mode', true );
$stock_quantity = (int) get_post_meta( $post_id, '_invento_stock_quantity', true );
$stock_label    = get_post_meta( $post_id, '_invento_stock_label', true );

$gallery_ids = json_decode( (string) get_post_meta( $post_id, '_invento_gallery_ids', true ), true );
$gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : [];

$variations = json_decode( (string) get_post_meta( $post_id, '_invento_variations', true ), true );
$variations = is_array( $variations ) ? $variations : [];

$icon_rows = json_decode( (string) get_post_meta( $post_id, '_invento_icon_text_rows', true ), true );
$icon_rows = is_array( $icon_rows ) ? $icon_rows : [];

$features = json_decode( (string) get_post_meta( $post_id, '_invento_main_features', true ), true );
$features = is_array( $features ) ? $features : [];

if ( ! $from_shortcode ) {
    get_header();
}
?>
<div class="invento-single-product">
    <header class="invento-product-header">
        <h1><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
        <?php if ( $short_description ) : ?>
            <p class="invento-short-description"><?php echo esc_html( $short_description ); ?></p>
        <?php endif; ?>
    </header>

    <div class="invento-product-media">
        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <div class="invento-featured-image">
                <?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $gallery_ids ) ) : ?>
            <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-gallery.php', [ 'gallery_ids' => $gallery_ids ] ); ?>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $icon_rows ) ) : ?>
        <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-features.php', [ 'rows' => $icon_rows, 'type' => 'icon_text' ] ); ?>
    <?php endif; ?>

    <?php if ( ! empty( $features ) ) : ?>
        <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-features.php', [ 'rows' => $features, 'type' => 'features' ] ); ?>
    <?php endif; ?>

    <?php if ( ! empty( $variations ) ) : ?>
        <section class="invento-variations">
            <h2><?php echo esc_html__( 'Variations', 'invento' ); ?></h2>
            <?php foreach ( $variations as $variation ) : ?>
                <?php
                $name = isset( $variation['name'] ) ? $variation['name'] : '';
                $options = isset( $variation['options'] ) && is_array( $variation['options'] ) ? $variation['options'] : [];
                ?>
                <?php if ( $name && $options ) : ?>
                    <div class="invento-variation-group">
                        <strong><?php echo esc_html( $name ); ?>:</strong>
                        <span><?php echo esc_html( implode( ', ', $options ) ); ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="invento-long-description">
        <?php echo wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); ?>
    </section>

    <?php
    $stock_output = Formatting::format_stock_status( $stock_mode, $stock_quantity, $stock_label );
    if ( $stock_output ) :
    ?>
        <p class="invento-stock-status"><?php echo $stock_output; ?></p>
    <?php endif; ?>

    <div class="invento-quote-button-wrap">
        <?php echo Formatting::render_quote_button( $post_id ); ?>
    </div>
</div>
<?php
if ( ! $from_shortcode ) {
    get_footer();
}
