<?php
if ( empty( $rows ) || ! is_array( $rows ) ) {
    return;
}

$type = isset( $type ) ? $type : 'features';
?>
<section class="invento-features invento-features-<?php echo esc_attr( $type ); ?>">
    <?php if ( 'features' === $type ) : ?>
        <h3 class="invento-features-title"><?php echo esc_html__( 'Main Features:', 'invento' ); ?></h3>
        <ol class="invento-features-list">
            <?php foreach ( $rows as $row ) : ?>
                <?php
                $title = isset( $row['title'] ) ? $row['title'] : '';
                $description = isset( $row['description'] ) ? $row['description'] : '';
                $text = trim( $title . ( $description ? ' ' . $description : '' ) );
                ?>
                <?php if ( $text ) : ?>
                    <li class="invento-feature-item">
                        <span class="invento-feature-text"><?php echo esc_html( $text ); ?></span>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    <?php else : ?>
        <div class="invento-features-grid">
            <?php foreach ( $rows as $row ) : ?>
                <?php
                $title = isset( $row['title'] ) ? $row['title'] : '';
                $description = isset( $row['description'] ) ? $row['description'] : '';
                $icon = isset( $row['icon_type'] ) ? $row['icon_type'] : '';
                ?>
                <div class="invento-feature-item">
                    <?php if ( $icon && 'icon_text' === $type ) : ?>
                        <?php if ( false !== strpos( $icon, '<' ) ) : ?>
                            <span class="invento-feature-icon"><?php echo wp_kses( $icon, Invento\Helpers\Sanitization::allowed_svg_html() ); ?></span>
                        <?php else : ?>
                            <span class="invento-feature-icon <?php echo esc_attr( $icon ); ?>"></span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <h4><?php echo esc_html( $title ); ?></h4>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <p><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
