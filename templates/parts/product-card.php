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
        if ( has_post_thumbnail( $post_id ) ) {
            echo '<a class="invento-product-link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
            echo '<div class="invento-card-thumb">' . get_the_post_thumbnail( $post_id, 'medium' ) . '</div>';
            echo '</a>';
        }
    };

    $rendered['title'] = function () use ( $post_id ) {
        echo '<a class="invento-product-link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
        echo '<h3>' . esc_html( get_the_title( $post_id ) ) . '</h3>';
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
