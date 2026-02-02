<?php
namespace Invento\Core;

class Assets implements Service_Interface {
    protected string $plugin_url;

    public function __construct( string $plugin_url ) {
        $this->plugin_url = $plugin_url;
    }

    public function register(): void {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend' ] );
    }

    public function enqueue_admin( string $hook ): void {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $is_product_screen = ( 'invento_product' === $screen->post_type );
        $is_settings = ( 'toplevel_page_invento-settings' === $screen->id || 0 === strpos( $screen->id, 'invento_page_' ) );

        if ( ! $is_product_screen && ! $is_settings ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'invento-admin-css',
            $this->plugin_url . 'assets/css/admin.css',
            [],
            INVENTO_VERSION
        );

        wp_enqueue_script(
            'invento-admin-js',
            $this->plugin_url . 'assets/js/admin.js',
            [ 'jquery', 'wp-util', 'media-editor', 'jquery-ui-sortable' ],
            INVENTO_VERSION,
            true
        );

        wp_localize_script(
            'invento-admin-js',
            'InventoAdmin',
            [
                'nonce'   => wp_create_nonce( 'invento_admin_nonce' ),
                'strings' => [
                    'addRow'     => __( 'Add Row', 'invento' ),
                    'removeRow'  => __( 'Remove', 'invento' ),
                    'selectImgs' => __( 'Select Images', 'invento' ),
                ],
            ]
        );
    }

    public function enqueue_frontend(): void {
        if ( is_admin() ) {
            return;
        }

        wp_enqueue_style(
            'invento-frontend-css',
            $this->plugin_url . 'assets/css/frontend.css',
            [],
            INVENTO_VERSION
        );

        wp_enqueue_style( 'dashicons' );

        wp_enqueue_script(
            'invento-frontend-js',
            $this->plugin_url . 'assets/js/frontend.js',
            [ 'jquery' ],
            INVENTO_VERSION,
            true
        );

        $css = $this->get_style_variables();
        if ( $css ) {
            wp_add_inline_style( 'invento-frontend-css', $css );
        }
    }

    protected function get_style_variables(): string {
        $styles = wp_parse_args( get_option( 'invento_style_settings', [] ), \Invento\Admin\Settings_Page::get_style_defaults() );

        $vars = [
            '--invento-container-width'      => $styles['container_width'],
            '--invento-column-gap'           => $styles['column_gap'],
            '--invento-divider-color'        => $styles['divider_color'],
            '--invento-title-size'           => $styles['title_size'],
            '--invento-title-weight'         => $styles['title_weight'],
            '--invento-title-color'          => $styles['title_color'],
            '--invento-short-size'           => $styles['short_desc_size'],
            '--invento-short-color'          => $styles['short_desc_color'],
            '--invento-media-max-height'     => $styles['media_max_height'],
            '--invento-media-radius'         => $styles['media_radius'],
            '--invento-thumb-height'         => $styles['thumb_height'],
            '--invento-thumb-radius'         => $styles['thumb_radius'],
            '--invento-thumb-bg'             => $styles['thumb_bg'],
            '--invento-thumb-active-border'  => $styles['thumb_active_border'],
            '--invento-thumb-count'          => $styles['thumb_count'],
            '--invento-features-box-width'   => $styles['features_box_width'],
            '--invento-features-box-radius'  => $styles['features_box_radius'],
            '--invento-features-box-border'  => $styles['features_box_border'],
            '--invento-features-box-padding' => $styles['features_box_padding'],
            '--invento-features-title-size'  => $styles['features_title_size'],
            '--invento-features-title-weight'=> $styles['features_title_weight'],
            '--invento-specs-box-bg'         => $styles['specs_box_bg'],
            '--invento-specs-box-radius'     => $styles['specs_box_radius'],
            '--invento-specs-box-padding'    => $styles['specs_box_padding'],
            '--invento-specs-box-gap'        => $styles['specs_box_gap'],
            '--invento-specs-card-bg'        => $styles['specs_card_bg'],
            '--invento-specs-card-radius'    => $styles['specs_card_radius'],
            '--invento-quote-bg'             => $styles['quote_bg'],
            '--invento-quote-color'          => $styles['quote_color'],
            '--invento-quote-radius'         => $styles['quote_radius'],
            '--invento-qty-size'             => $styles['qty_size'],
            '--invento-qty-radius'           => $styles['qty_radius'],
            '--invento-qty-border'           => $styles['qty_border'],
            '--invento-qty-bg'               => $styles['qty_bg'],
            '--invento-qty-text-size'        => $styles['qty_text_size'],
            '--invento-qty-text-color'       => $styles['qty_text_color'],
            '--invento-grid-columns-min'     => $styles['grid_columns_min'],
            '--invento-grid-gap'             => $styles['grid_gap'],
            '--invento-grid-card-bg'         => $styles['grid_card_bg'],
            '--invento-grid-card-border'     => $styles['grid_card_border'],
            '--invento-grid-card-radius'     => $styles['grid_card_radius'],
            '--invento-grid-title-size'      => $styles['grid_title_size'],
            '--invento-grid-title-color'     => $styles['grid_title_color'],
            '--invento-grid-excerpt-size'    => $styles['grid_excerpt_size'],
            '--invento-grid-excerpt-color'   => $styles['grid_excerpt_color'],
            '--invento-grid-thumb-radius'    => $styles['grid_thumb_radius'],
            '--invento-grid-thumb-width'     => $styles['grid_thumb_width'],
            '--invento-grid-thumb-height'    => $styles['grid_thumb_height'],
            '--invento-grid-card-padding'    => $styles['grid_card_padding'],
            '--invento-grid-section-gap'     => $styles['grid_section_gap'],
            '--invento-popup-max-width'      => $styles['popup_max_width'],
            '--invento-popup-bg'             => $styles['popup_bg'],
            '--invento-popup-radius'         => $styles['popup_radius'],
            '--invento-popup-padding'        => $styles['popup_padding'],
            '--invento-popup-border'         => $styles['popup_border'],
            '--invento-popup-overlay-bg'     => $styles['popup_overlay_bg'],
            '--invento-popup-title-size'     => $styles['popup_title_size'],
            '--invento-popup-title-weight'   => $styles['popup_title_weight'],
            '--invento-popup-title-color'    => $styles['popup_title_color'],
            '--invento-popup-close-size'     => $styles['popup_close_size'],
            '--invento-popup-close-color'    => $styles['popup_close_color'],
        ];

        $lines = [];
        foreach ( $vars as $key => $value ) {
            if ( '' !== (string) $value ) {
                $lines[] = $key . ':' . $value . ';';
            }
        }

        if ( empty( $lines ) ) {
            return '';
        }

        return ':root{' . implode( '', $lines ) . '}';
    }
}
