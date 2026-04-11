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

    public static function render_price_block( int $product_id ): string {
        $regular = get_post_meta( $product_id, '_invento_regular_price', true );
        $sale    = get_post_meta( $product_id, '_invento_sale_price', true );

        if ( '' === trim( (string) $regular ) && '' === trim( (string) $sale ) ) {
            return '';
        }

        $html = '<div class="invento-price-block">';

        if ( $sale && '' !== trim( $sale ) ) {
            $html .= '<span class="invento-price-sale">$' . esc_html( $sale ) . '</span>';
            if ( $regular && '' !== trim( $regular ) ) {
                $html .= '<span class="invento-price-regular invento-price-strikethrough">$' . esc_html( $regular ) . '</span>';
                $regular_num = (float) preg_replace( '/[^0-9.]/', '', $regular );
                $sale_num    = (float) preg_replace( '/[^0-9.]/', '', $sale );
                if ( $regular_num > 0 && $sale_num < $regular_num ) {
                    $discount = round( ( ( $regular_num - $sale_num ) / $regular_num ) * 100 );
                    $html .= '<span class="invento-price-discount">' . esc_html( $discount ) . '% ' . esc_html__( 'OFF', 'invento' ) . '</span>';
                }
            }
        } else {
            $html .= '<span class="invento-price-sale">$' . esc_html( $regular ) . '</span>';
        }

        $html .= '</div>';

        return $html;
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

        $product_name = get_the_title( $product_id );
        $data_attrs   = sprintf( ' data-product-name="%s"', esc_attr( $product_name ) );

        $arrow_svg = '<div class="invento-quote-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"></path></svg></div>';

        if ( 'product' === $mode && ! empty( $url ) ) {
            $final_url   = esc_url( $url );
            $final_label = $label ? $label : $global_label;
            return sprintf( '<a class="invento-quote-button" href="%s"%s><span>%s</span>%s</a>', $final_url, $data_attrs, esc_html( $final_label ), $arrow_svg );
        }

        if ( empty( $global_url ) ) {
            $global_url = get_permalink( $product_id );
        }

        return sprintf( '<a class="invento-quote-button" href="%s"%s><span>%s</span>%s</a>', esc_url( $global_url ), $data_attrs, esc_html( $global_label ), $arrow_svg );
    }

    public static function render_specs_table( int $product_id ): string {
        $raw = get_post_meta( $product_id, '_invento_specifications', true );
        $rows = json_decode( (string) $raw, true );
        $rows = is_array( $rows ) ? $rows : [];

        if ( empty( $rows ) ) {
            return '';
        }

        // Filter out empty rows.
        $specs = [];
        foreach ( $rows as $row ) {
            $label = isset( $row['label'] ) ? trim( $row['label'] ) : '';
            $value = isset( $row['value'] ) ? trim( $row['value'] ) : '';
            if ( '' !== $label || '' !== $value ) {
                $specs[] = [ 'label' => $label, 'value' => $value ];
            }
        }

        if ( empty( $specs ) ) {
            return '';
        }

        $html = '<table class="invento-specs-table">';
        $html .= '<tbody>';

        $allowed = [
            'br'     => [],
            'strong' => [],
            'em'     => [],
            'sub'    => [],
            'sup'    => [],
        ];

        $count = count( $specs );
        for ( $i = 0; $i < $count; $i += 2 ) {
            $html .= '<tr>';
            $html .= '<th>' . wp_kses( $specs[ $i ]['label'], $allowed ) . '</th>';
            $html .= '<td>' . wp_kses( $specs[ $i ]['value'], $allowed ) . '</td>';
            if ( isset( $specs[ $i + 1 ] ) ) {
                $html .= '<th>' . wp_kses( $specs[ $i + 1 ]['label'], $allowed ) . '</th>';
                $html .= '<td>' . wp_kses( $specs[ $i + 1 ]['value'], $allowed ) . '</td>';
            } else {
                $html .= '<th></th><td></td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}
