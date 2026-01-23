<?php
if ( empty( $media_items ) || ! is_array( $media_items ) ) {
    return;
}
?>
<div class="invento-gallery">
    <div class="invento-thumb-viewport">
        <div class="invento-thumb-track">
            <?php foreach ( $media_items as $item ) : ?>
                <?php
                $type  = isset( $item['type'] ) ? $item['type'] : 'image';
                $url   = isset( $item['url'] ) ? $item['url'] : '';
                $thumb = isset( $item['thumb'] ) ? $item['thumb'] : '';
                $poster = isset( $item['poster'] ) ? $item['poster'] : '';
                if ( ! $url && 'video' !== $type ) {
                    continue;
                }
                ?>
                <button
                    type="button"
                    class="invento-thumb"
                    data-media-type="<?php echo esc_attr( $type ); ?>"
                    data-media-url="<?php echo esc_url( $url ); ?>"
                    data-media-poster="<?php echo esc_url( $poster ); ?>"
                    data-video-type="<?php echo esc_attr( $type === 'video' ? ( isset( $item['video_type'] ) ? $item['video_type'] : 'youtube' ) : '' ); ?>"
                >
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="" />
                    <?php else : ?>
                        <span class="invento-thumb-placeholder"></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="invento-thumb-nav-row">
        <button type="button" class="invento-thumb-nav invento-thumb-prev" aria-label="<?php echo esc_attr__( 'Previous images', 'invento' ); ?>">&#10094;</button>
        <button type="button" class="invento-thumb-nav invento-thumb-next" aria-label="<?php echo esc_attr__( 'Next images', 'invento' ); ?>">&#10095;</button>
    </div>
</div>
