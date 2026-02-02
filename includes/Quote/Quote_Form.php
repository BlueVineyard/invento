<?php
namespace Invento\Quote;

use Invento\Core\Service_Interface;

class Quote_Form implements Service_Interface {
    public function register(): void {
        add_action( 'wp_footer', [ $this, 'render_modal' ] );
    }

    public static function get_form_settings(): array {
        return wp_parse_args(
            get_option( 'invento_quote_form_settings', [] ),
            [
                'fluent_form_id'       => 0,
                'form_title'           => __( 'Request a Quote', 'invento' ),
                'field_name_product'   => 'interested_product',
                'field_name_quantity'  => 'numeric_field',
            ]
        );
    }

    public function render_modal(): void {
        if ( ! wp_script_is( 'invento-frontend-js', 'enqueued' ) ) {
            return;
        }

        $settings = self::get_form_settings();
        $form_id  = absint( $settings['fluent_form_id'] );

        if ( ! $form_id || ! shortcode_exists( 'fluentform' ) ) {
            return;
        }

        $product_id = is_singular( 'invento_product' ) ? get_queried_object_id() : 0;

        echo '<div class="invento-quote-overlay" aria-hidden="true" data-product-id="' . esc_attr( $product_id ) . '" data-field-product="' . esc_attr( $settings['field_name_product'] ) . '" data-field-quantity="' . esc_attr( $settings['field_name_quantity'] ) . '">';
        echo '<div class="invento-quote-modal" role="dialog" aria-modal="true">';
        echo '<button type="button" class="invento-quote-close" aria-label="' . esc_attr__( 'Close', 'invento' ) . '">&times;</button>';
        echo '<h2 class="invento-quote-title">' . esc_html( $settings['form_title'] ) . '</h2>';
        echo '<div class="invento-quote-form-wrap">';
        echo do_shortcode( '[fluentform id="' . $form_id . '"]' );
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
