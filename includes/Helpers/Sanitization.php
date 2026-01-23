<?php
namespace Invento\Helpers;

class Sanitization {
    public static function sanitize_json_array( $value ): string {
        if ( empty( $value ) ) {
            return wp_json_encode( [] );
        }

        if ( is_string( $value ) ) {
            $decoded = json_decode( wp_unslash( $value ), true );
            $value = is_array( $decoded ) ? $decoded : [];
        }

        if ( ! is_array( $value ) ) {
            $value = [];
        }

        $value = self::sanitize_text_field_deep( $value );

        return wp_json_encode( array_values( $value ) );
    }

    public static function sanitize_icon_string( string $icon ): string {
        $icon = wp_strip_all_tags( $icon );
        return sanitize_text_field( $icon );
    }

    public static function sanitize_icon_rows( $value ): string {
        if ( empty( $value ) ) {
            return wp_json_encode( [] );
        }

        if ( is_string( $value ) ) {
            $decoded = json_decode( wp_unslash( $value ), true );
            $value = is_array( $decoded ) ? $decoded : [];
        }

        if ( ! is_array( $value ) ) {
            $value = [];
        }

        $allowed = self::allowed_svg_html();
        $rows = [];

        foreach ( $value as $row ) {
            if ( ! is_array( $row ) ) {
                continue;
            }
            $rows[] = [
                'icon_type'   => isset( $row['icon_type'] ) ? wp_kses( $row['icon_type'], $allowed ) : '',
                'title'       => isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '',
                'description' => isset( $row['description'] ) ? sanitize_text_field( $row['description'] ) : '',
            ];
        }

        return wp_json_encode( array_values( $rows ) );
    }

    public static function allowed_svg_html(): array {
        return [
            'svg'     => [
                'class'       => true,
                'xmlns'       => true,
                'width'       => true,
                'height'      => true,
                'viewbox'     => true,
                'aria-hidden' => true,
                'role'        => true,
            ],
            'path'    => [
                'd'    => true,
                'fill' => true,
            ],
            'g'       => [ 'fill' => true ],
            'circle'  => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ],
            'rect'    => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true ],
            'line'    => [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true ],
            'polyline'=> [ 'points' => true, 'stroke' => true, 'fill' => true ],
            'polygon' => [ 'points' => true, 'stroke' => true, 'fill' => true ],
        ];
    }

    public static function sanitize_text_field_deep( $data ) {
        if ( is_array( $data ) ) {
            $sanitized = [];
            foreach ( $data as $key => $value ) {
                if ( is_int( $key ) ) {
                    $sanitized[ $key ] = self::sanitize_text_field_deep( $value );
                } else {
                    $sanitized[ sanitize_key( $key ) ] = self::sanitize_text_field_deep( $value );
                }
            }
            return $sanitized;
        }

        if ( is_string( $data ) ) {
            return sanitize_text_field( $data );
        }

        if ( is_numeric( $data ) ) {
            return $data;
        }

        return '';
    }
}
