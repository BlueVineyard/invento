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
    <?php
    $style_settings = wp_parse_args( get_option( 'invento_style_settings', [] ), Invento\Admin\Settings_Page::get_style_defaults() );
    $sections = json_decode( (string) $style_settings['grid_sections'], true );
    $sections = is_array( $sections ) ? $sections : [];

    $rendered = [];

    $rendered['thumb'] = function () use ( $post_id ) {
        $gallery_ids = json_decode( (string) get_post_meta( $post_id, '_invento_gallery_ids', true ), true );
        $gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : [];

        // Build gallery image URLs
        $images = [];
        foreach ( $gallery_ids as $att_id ) {
            $url = wp_get_attachment_image_url( (int) $att_id, 'full' );
            if ( $url ) {
                $images[] = $url;
            }
        }

        // Fallback to featured image
        if ( empty( $images ) && has_post_thumbnail( $post_id ) ) {
            $featured_url = get_the_post_thumbnail_url( $post_id, 'full' );
            if ( $featured_url ) {
                $images[] = $featured_url;
            }
        }

        // Fallback to placeholder
        if ( empty( $images ) ) {
            $settings = get_option( 'invento_settings', [] );
            $placeholder_id = isset( $settings['placeholder_image_id'] ) ? (int) $settings['placeholder_image_id'] : 0;
            if ( $placeholder_id ) {
                $placeholder_url = wp_get_attachment_image_url( $placeholder_id, 'full' );
                if ( $placeholder_url ) {
                    $images[] = $placeholder_url;
                }
            }
        }

        if ( empty( $images ) ) {
            return;
        }

        $permalink = esc_url( get_permalink( $post_id ) );
        $data_attr = count( $images ) > 1 ? " data-gallery='" . wp_json_encode( $images ) . "'" : '';

        echo '<a class="invento-product-link" href="' . $permalink . '">';
        echo '<div class="invento-card-thumb"' . $data_attr . '>';
        echo '<img src="' . esc_url( $images[0] ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '" />';
        echo '</div>';
        echo '</a>';
    };

    $rendered['title'] = function () use ( $post_id ) {
        $sku = get_post_meta( $post_id, '_invento_sku', true );
        $title = ( $sku && '' !== trim( $sku ) ) ? $sku : get_the_title( $post_id );
        echo '<a class="invento-product-link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
        echo '<h3>' . esc_html( $title ) . '</h3>';
        echo '</a>';
    };

    $rendered['excerpt'] = function () use ( $short_description ) {
        if ( $short_description ) {
            echo '<p class="invento-card-excerpt">' . esc_html( wp_trim_words( $short_description, 20 ) ) . '</p>';
        }
    };

    $rendered['quantity'] = function () use ( $post_id ) {
        echo '<div class="invento-qty-control" data-product-id="' . esc_attr( (string) $post_id ) . '">';
        echo '<button type="button" class="invento-qty-btn invento-qty-minus" aria-label="' . esc_attr__( 'Decrease quantity', 'invento' ) . '">-</button>';
        echo '<input type="number" class="invento-qty-input" min="1" value="1" />';
        echo '<button type="button" class="invento-qty-btn invento-qty-plus" aria-label="' . esc_attr__( 'Increase quantity', 'invento' ) . '">+</button>';
        echo '</div>';
    };

    $rendered['pricing'] = function () use ( $post_id ) {
        echo Formatting::render_price_block( $post_id );
    };

    $rendered['quote'] = function () use ( $post_id ) {
        echo Formatting::render_quote_button( $post_id );
    };

    $enabled_sections = [];
    foreach ( $sections as $section ) {
        if ( ! empty( $section['enabled'] ) && ! empty( $section['key'] ) ) {
            $enabled_sections[] = $section['key'];
        }
    }

    foreach ( $enabled_sections as $key ) {
        if ( isset( $rendered[ $key ] ) && is_callable( $rendered[ $key ] ) ) {
            $rendered[ $key ]();
        }
    }
    ?>
</article>
