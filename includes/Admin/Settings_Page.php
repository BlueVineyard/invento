<?php
namespace Invento\Admin;

use Invento\Core\Service_Interface;

class Settings_Page implements Service_Interface {
    public function register(): void {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_menu(): void {
        add_menu_page(
            __( 'Invento', 'invento' ),
            __( 'Invento', 'invento' ),
            'manage_options',
            'invento-settings',
            [ $this, 'render_page' ],
            'dashicons-archive',
            58
        );
    }

    public function register_settings(): void {
        register_setting(
            'invento_settings_group',
            'invento_settings',
            [ $this, 'sanitize_settings' ]
        );

        add_settings_section(
            'invento_general',
            __( 'General Settings', 'invento' ),
            '__return_false',
            'invento-settings'
        );

        add_settings_field(
            'quote_button_global_url',
            __( 'Quote Button URL', 'invento' ),
            [ $this, 'render_quote_url_field' ],
            'invento-settings',
            'invento_general'
        );

        add_settings_field(
            'quote_button_global_label',
            __( 'Quote Button Label', 'invento' ),
            [ $this, 'render_quote_label_field' ],
            'invento-settings',
            'invento_general'
        );

        add_settings_field(
            'catalog_layout',
            __( 'Catalog Layout', 'invento' ),
            [ $this, 'render_catalog_layout_field' ],
            'invento-settings',
            'invento_general'
        );

        add_settings_field(
            'products_per_page',
            __( 'Products Per Page', 'invento' ),
            [ $this, 'render_products_per_page_field' ],
            'invento-settings',
            'invento_general'
        );
    }

    public function sanitize_settings( array $settings ): array {
        $sanitized = [];
        $sanitized['quote_button_global_url']   = isset( $settings['quote_button_global_url'] ) ? esc_url_raw( $settings['quote_button_global_url'] ) : '';
        $sanitized['quote_button_global_label'] = isset( $settings['quote_button_global_label'] ) ? sanitize_text_field( $settings['quote_button_global_label'] ) : '';
        $sanitized['catalog_layout']            = isset( $settings['catalog_layout'] ) && in_array( $settings['catalog_layout'], [ 'grid', 'list' ], true ) ? $settings['catalog_layout'] : 'grid';
        $sanitized['products_per_page']         = isset( $settings['products_per_page'] ) ? max( 1, absint( $settings['products_per_page'] ) ) : 12;

        return $sanitized;
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        echo '<div class="wrap invento-settings">';
        echo '<h1>' . esc_html__( 'Invento Settings', 'invento' ) . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'invento_settings_group' );
        do_settings_sections( 'invento-settings' );
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function render_quote_url_field(): void {
        $settings = get_option( 'invento_settings', [] );
        $value    = isset( $settings['quote_button_global_url'] ) ? $settings['quote_button_global_url'] : '/request-quote/';
        echo '<input type="url" class="regular-text" name="invento_settings[quote_button_global_url]" value="' . esc_attr( $value ) . '" />';
    }

    public function render_quote_label_field(): void {
        $settings = get_option( 'invento_settings', [] );
        $value    = isset( $settings['quote_button_global_label'] ) ? $settings['quote_button_global_label'] : __( 'Request a Quote', 'invento' );
        echo '<input type="text" class="regular-text" name="invento_settings[quote_button_global_label]" value="' . esc_attr( $value ) . '" />';
    }

    public function render_catalog_layout_field(): void {
        $settings = get_option( 'invento_settings', [] );
        $value    = isset( $settings['catalog_layout'] ) ? $settings['catalog_layout'] : 'grid';
        echo '<select name="invento_settings[catalog_layout]">';
        echo '<option value="grid" ' . selected( $value, 'grid', false ) . '>' . esc_html__( 'Grid', 'invento' ) . '</option>';
        echo '<option value="list" ' . selected( $value, 'list', false ) . '>' . esc_html__( 'List', 'invento' ) . '</option>';
        echo '</select>';
    }

    public function render_products_per_page_field(): void {
        $settings = get_option( 'invento_settings', [] );
        $value    = isset( $settings['products_per_page'] ) ? (int) $settings['products_per_page'] : 12;
        echo '<input type="number" min="1" class="small-text" name="invento_settings[products_per_page]" value="' . esc_attr( (string) $value ) . '" />';
    }
}
