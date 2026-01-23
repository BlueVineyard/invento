<?php
if ( empty( $gallery_ids ) || ! is_array( $gallery_ids ) ) {
    return;
}
?>
<div class="invento-gallery">
    <?php foreach ( $gallery_ids as $attachment_id ) : ?>
        <?php
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) {
            continue;
        }
        ?>
        <div class="invento-gallery-item">
            <?php echo wp_get_attachment_image( $attachment_id, 'large' ); ?>
        </div>
    <?php endforeach; ?>
</div>
