<?php
namespace Invento\Helpers;

class Formatting {
    public static function format_stock_status( string $mode, int $quantity, string $label ): string {
        if ( 'label' === $mode && $label !== '' ) {
            return esc_html( $label );
        }

        if ( 'simple' === $mode ) {
            if ( $quantity > 0 ) {
                return esc_html( sprintf( __( 'In stock: %d units', 'invento' ), $quantity ) );
            }
            return esc_html__( 'Out of stock', 'invento' );
        }

        return '';
    }

    public static function render_quote_button( int $product_id ): string {
        $settings = get_option( 'invento_settings', [] );
        $mode     = get_post_meta( $product_id, '_invento_quote_button_mode', true );
        $label    = get_post_meta( $product_id, '_invento_quote_button_label', true );
        $url      = get_post_meta( $product_id, '_invento_quote_button_url', true );

        $mode = $mode ? $mode : 'global';

        if ( 'disabled' === $mode ) {
            return '';
        }

        $global_url   = isset( $settings['quote_button_global_url'] ) ? $settings['quote_button_global_url'] : '/request-quote/';
        $global_label = isset( $settings['quote_button_global_label'] ) ? $settings['quote_button_global_label'] : __( 'Request a Quote', 'invento' );
        if ( empty( $global_label ) ) {
            $global_label = __( 'Request a Quote', 'invento' );
        }

        if ( 'product' === $mode && ! empty( $url ) ) {
            $final_url   = esc_url( $url );
            $final_label = $label ? $label : $global_label;
            return sprintf( '<a class="invento-quote-button" href="%s">%s</a>', $final_url, esc_html( $final_label ) );
        }

        if ( empty( $global_url ) ) {
            return '';
        }

        return sprintf( '<a class="invento-quote-button" href="%s">%s</a>', esc_url( $global_url ), esc_html( $global_label ) );
    }
}
