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

$video_type = get_post_meta( $post_id, '_invento_featured_video_type', true );
$video_url  = get_post_meta( $post_id, '_invento_featured_video_url', true );

$gallery_ids = json_decode( (string) get_post_meta( $post_id, '_invento_gallery_ids', true ), true );
$gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : [];

$featured_image_id  = get_post_thumbnail_id( $post_id );
$featured_image_url = $featured_image_id ? wp_get_attachment_image_url( $featured_image_id, 'large' ) : '';
$featured_thumb_url = $featured_image_id ? wp_get_attachment_image_url( $featured_image_id, 'thumbnail' ) : '';

$variations = json_decode( (string) get_post_meta( $post_id, '_invento_variations', true ), true );
$variations = is_array( $variations ) ? $variations : [];

$icon_rows = json_decode( (string) get_post_meta( $post_id, '_invento_icon_text_rows', true ), true );
$icon_rows = is_array( $icon_rows ) ? $icon_rows : [];

$features = json_decode( (string) get_post_meta( $post_id, '_invento_main_features', true ), true );
$features = is_array( $features ) ? $features : [];

$media_items = [];
if ( $video_type && 'none' !== $video_type && $video_url ) {
    $media_items[] = [
        'type'   => 'video',
        'url'    => $video_url,
        'poster' => $featured_image_url,
        'thumb'  => $featured_thumb_url,
        'video_type' => $video_type,
    ];
}

if ( $featured_image_url ) {
    $media_items[] = [
        'type'  => 'image',
        'url'   => $featured_image_url,
        'thumb' => $featured_thumb_url ? $featured_thumb_url : $featured_image_url,
    ];
}

if ( ! empty( $gallery_ids ) ) {
    foreach ( $gallery_ids as $attachment_id ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) {
            continue;
        }
        $full  = wp_get_attachment_image_url( $attachment_id, 'large' );
        $thumb = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
        if ( $full ) {
            $media_items[] = [
                'type'  => 'image',
                'url'   => $full,
                'thumb' => $thumb ? $thumb : $full,
            ];
        }
    }
}

$default_image_url = '';
foreach ( $media_items as $item ) {
    if ( isset( $item['type'] ) && 'image' === $item['type'] && ! empty( $item['url'] ) ) {
        $default_image_url = $item['url'];
        break;
    }
}

if ( ! $from_shortcode ) {
    get_header();
}
?>
<div class="invento-single-product invento-product-layout">
    <div class="invento-product-media">
        <div class="invento-media-display" data-has-video="<?php echo esc_attr( ( $video_type && 'none' !== $video_type && $video_url ) ? '1' : '0' ); ?>">
            <?php if ( $video_type && 'none' !== $video_type && $video_url ) : ?>
                <div class="invento-featured-video" data-video-type="<?php echo esc_attr( $video_type ); ?>" data-video-url="<?php echo esc_url( $video_url ); ?>">
                    <div class="invento-video-poster">
                        <?php if ( $featured_image_url ) : ?>
                            <img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" />
                        <?php else : ?>
                            <div class="invento-video-placeholder"></div>
                        <?php endif; ?>
                        <button type="button" class="invento-video-play" aria-label="<?php echo esc_attr__( 'Play video', 'invento' ); ?>"></button>
                    </div>
                </div>
            <?php elseif ( $default_image_url ) : ?>
                <div class="invento-featured-image">
                    <img src="<?php echo esc_url( $default_image_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" />
                </div>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $media_items ) ) : ?>
            <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-gallery.php', [ 'media_items' => $media_items ] ); ?>
        <?php endif; ?>
    </div>

    <div class="invento-product-details">
        <header class="invento-product-header">
            <h1><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
            <?php if ( $short_description ) : ?>
                <p class="invento-short-description"><?php echo esc_html( $short_description ); ?></p>
            <?php endif; ?>
        </header>

        <hr class="invento-divider" />

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
            <hr class="invento-divider" />
        <?php endif; ?>

        <div class="invento-qty-control" data-product-id="<?php echo esc_attr( (string) $post_id ); ?>">
            <button type="button" class="invento-qty-btn invento-qty-minus" aria-label="<?php echo esc_attr__( 'Decrease quantity', 'invento' ); ?>">-</button>
            <input type="number" class="invento-qty-input" min="1" value="1" />
            <button type="button" class="invento-qty-btn invento-qty-plus" aria-label="<?php echo esc_attr__( 'Increase quantity', 'invento' ); ?>">+</button>
        </div>

        <hr class="invento-divider" />

        <?php if ( ! empty( $features ) ) : ?>
            <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-features.php', [ 'rows' => $features, 'type' => 'features' ] ); ?>
        <?php endif; ?>

        <?php if ( ! empty( $icon_rows ) ) : ?>
            <?php Invento\Helpers\View::render( __DIR__ . '/parts/product-features.php', [ 'rows' => $icon_rows, 'type' => 'icon_text' ] ); ?>
        <?php endif; ?>

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
</div>

<section class="invento-long-description">
    <?php echo wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); ?>
</section>

<?php if ( $video_type && 'none' !== $video_type && $video_url ) : ?>
    <div class="invento-video-overlay" aria-hidden="true">
        <div class="invento-video-modal" role="dialog" aria-modal="true">
            <button type="button" class="invento-video-close" aria-label="<?php echo esc_attr__( 'Close video', 'invento' ); ?>">&times;</button>
            <div class="invento-video-player"></div>
        </div>
    </div>
<?php endif; ?>

<div class="invento-image-lightbox" aria-hidden="true">
    <div class="invento-image-lightbox-inner" role="dialog" aria-modal="true">
        <button type="button" class="invento-image-lightbox-close" aria-label="<?php echo esc_attr__( 'Close image', 'invento' ); ?>">&times;</button>
        <img src="" alt="" />
    </div>
</div>
<?php
if ( ! $from_shortcode ) {
    get_footer();
}
