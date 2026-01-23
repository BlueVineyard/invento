<?php
use Invento\Helpers\Formatting;

if ( ! isset( $post_id ) ) {
    $post_id = get_the_ID();
}

$short_description = get_post_meta( $post_id, '_invento_short_description', true );
if ( ! $short_description ) {
    $short_description = get_the_excerpt( $post_id );
}
?>
<article class="invento-product-card">
    <a class="invento-product-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <div class="invento-card-thumb">
                <?php echo get_the_post_thumbnail( $post_id, 'medium' ); ?>
            </div>
        <?php endif; ?>
        <h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
    </a>
    <?php if ( $short_description ) : ?>
        <p class="invento-card-excerpt"><?php echo esc_html( wp_trim_words( $short_description, 20 ) ); ?></p>
    <?php endif; ?>
    <div class="invento-card-actions">
        <div class="invento-qty-control" data-product-id="<?php echo esc_attr( (string) $post_id ); ?>">
            <button type="button" class="invento-qty-btn invento-qty-minus" aria-label="<?php echo esc_attr__( 'Decrease quantity', 'invento' ); ?>">-</button>
            <input type="number" class="invento-qty-input" min="1" value="1" />
            <button type="button" class="invento-qty-btn invento-qty-plus" aria-label="<?php echo esc_attr__( 'Increase quantity', 'invento' ); ?>">+</button>
        </div>
        <?php echo Formatting::render_quote_button( $post_id ); ?>
    </div>
</article>
