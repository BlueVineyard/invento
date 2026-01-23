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

        add_submenu_page(
            'invento-settings',
            __( 'General Settings', 'invento' ),
            __( 'General', 'invento' ),
            'manage_options',
            'invento-settings',
            [ $this, 'render_page' ]
        );

        add_submenu_page(
            'invento-settings',
            __( 'Styles', 'invento' ),
            __( 'Styles', 'invento' ),
            'manage_options',
            'invento-styles',
            [ $this, 'render_styles_page' ]
        );

        add_submenu_page(
            'invento-settings',
            __( 'Instructions', 'invento' ),
            __( 'Instructions', 'invento' ),
            'manage_options',
            'invento-instructions',
            [ $this, 'render_instructions_page' ]
        );
    }

    public function register_settings(): void {
        register_setting(
            'invento_settings_group',
            'invento_settings',
            [ $this, 'sanitize_settings' ]
        );

        register_setting(
            'invento_style_settings_group',
            'invento_style_settings',
            [ $this, 'sanitize_style_settings' ]
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

    public static function get_style_defaults(): array {
        return [
            'container_width'        => '1320px',
            'column_gap'             => '32px',
            'divider_color'          => '#E9E9E9',
            'title_size'             => '40px',
            'title_weight'           => '600',
            'title_color'            => '#121212',
            'short_desc_size'         => '18px',
            'short_desc_color'        => '#535353',
            'media_max_height'        => '671px',
            'media_radius'            => '12px',
            'thumb_height'            => '164px',
            'thumb_radius'            => '10px',
            'thumb_bg'                => '#F4F4F4',
            'thumb_active_border'     => '#016CB2',
            'thumb_count'             => '4',
            'features_box_width'      => '602px',
            'features_box_radius'     => '8px',
            'features_box_border'     => '#DCDCDC',
            'features_box_padding'    => '24px',
            'features_title_size'     => '24px',
            'features_title_weight'   => '500',
            'specs_box_bg'            => '#F4F4F4',
            'specs_box_radius'        => '12px',
            'specs_box_padding'       => '18px 24px',
            'specs_box_gap'           => '10px',
            'specs_card_radius'       => '6px',
            'specs_card_bg'           => '#FFFFFF',
            'quote_bg'                => '#111111',
            'quote_color'             => '#FFFFFF',
            'quote_radius'            => '0px',
            'qty_size'                => '43px',
            'qty_radius'              => '8px',
            'qty_border'              => '#BFBFBF',
            'qty_bg'                  => '#FFFFFF',
            'qty_text_size'           => '16px',
            'qty_text_color'          => '#535353',
        ];
    }

    public function sanitize_style_settings( array $settings ): array {
        $defaults = self::get_style_defaults();
        $sanitized = [];

        $keys = array_keys( $defaults );
        foreach ( $keys as $key ) {
            $value = isset( $settings[ $key ] ) ? wp_unslash( $settings[ $key ] ) : $defaults[ $key ];
            $value = is_string( $value ) ? trim( $value ) : $value;

            if ( in_array( $key, [ 'thumb_count' ], true ) ) {
                $count = absint( $value );
                $sanitized[ $key ] = (string) max( 1, min( 6, $count ) );
                continue;
            }

            if ( false !== strpos( $key, 'color' ) || in_array( $key, [ 'divider_color', 'thumb_bg', 'thumb_active_border', 'features_box_border', 'specs_box_bg', 'specs_card_bg', 'quote_bg', 'quote_color', 'qty_border', 'qty_bg', 'qty_text_color' ], true ) ) {
                $sanitized[ $key ] = sanitize_hex_color( $value ) ? sanitize_hex_color( $value ) : $defaults[ $key ];
                continue;
            }

            if ( in_array( $key, [ 'features_box_padding', 'specs_box_padding' ], true ) ) {
                $sanitized[ $key ] = preg_replace( '/[^0-9\\s.%a-z]/i', '', $value );
                continue;
            }

            if ( in_array( $key, [ 'title_weight', 'features_title_weight' ], true ) ) {
                $sanitized[ $key ] = preg_replace( '/[^0-9]/', '', (string) $value );
                continue;
            }

            $sanitized[ $key ] = preg_replace( '/[^0-9.%a-z]/i', '', (string) $value );
        }

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

    public function render_styles_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'product-grid';
        if ( ! in_array( $tab, [ 'product-grid', 'product-template' ], true ) ) {
            $tab = 'product-grid';
        }

        echo '<div class="wrap invento-settings">';
        echo '<h1>' . esc_html__( 'Invento Styles', 'invento' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a class="nav-tab ' . ( 'product-grid' === $tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=invento-styles&tab=product-grid' ) ) . '">' . esc_html__( 'Product Grid', 'invento' ) . '</a>';
        echo '<a class="nav-tab ' . ( 'product-template' === $tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=invento-styles&tab=product-template' ) ) . '">' . esc_html__( 'Product Template', 'invento' ) . '</a>';
        echo '</h2>';

        if ( 'product-template' === $tab ) {
            $styles = wp_parse_args( get_option( 'invento_style_settings', [] ), self::get_style_defaults() );
            echo '<form method="post" action="options.php">';
            settings_fields( 'invento_style_settings_group' );

            echo '<div class="invento-settings-grid">';

            $this->render_style_card( __( 'Layout', 'invento' ), [
                $this->style_field( 'container_width', __( 'Container Width', 'invento' ), $styles['container_width'] ),
                $this->style_field( 'column_gap', __( 'Column Gap', 'invento' ), $styles['column_gap'] ),
                $this->style_field( 'divider_color', __( 'Divider Color', 'invento' ), $styles['divider_color'], 'color' ),
            ] );

            $this->render_style_card( __( 'Typography', 'invento' ), [
                $this->style_field( 'title_size', __( 'Title Size', 'invento' ), $styles['title_size'] ),
                $this->style_field( 'title_weight', __( 'Title Weight', 'invento' ), $styles['title_weight'] ),
                $this->style_field( 'title_color', __( 'Title Color', 'invento' ), $styles['title_color'], 'color' ),
                $this->style_field( 'short_desc_size', __( 'Short Description Size', 'invento' ), $styles['short_desc_size'] ),
                $this->style_field( 'short_desc_color', __( 'Short Description Color', 'invento' ), $styles['short_desc_color'], 'color' ),
            ] );

            $this->render_style_card( __( 'Media', 'invento' ), [
                $this->style_field( 'media_max_height', __( 'Media Max Height', 'invento' ), $styles['media_max_height'] ),
                $this->style_field( 'media_radius', __( 'Media Radius', 'invento' ), $styles['media_radius'] ),
                $this->style_field( 'thumb_height', __( 'Thumb Height', 'invento' ), $styles['thumb_height'] ),
                $this->style_field( 'thumb_radius', __( 'Thumb Radius', 'invento' ), $styles['thumb_radius'] ),
                $this->style_field( 'thumb_bg', __( 'Thumb Background', 'invento' ), $styles['thumb_bg'], 'color' ),
                $this->style_field( 'thumb_active_border', __( 'Active Thumb Border', 'invento' ), $styles['thumb_active_border'], 'color' ),
                $this->style_field( 'thumb_count', __( 'Thumbs Per View', 'invento' ), $styles['thumb_count'], 'number' ),
            ] );

            $this->render_style_card( __( 'Main Features', 'invento' ), [
                $this->style_field( 'features_box_width', __( 'Box Width', 'invento' ), $styles['features_box_width'] ),
                $this->style_field( 'features_box_radius', __( 'Box Radius', 'invento' ), $styles['features_box_radius'] ),
                $this->style_field( 'features_box_border', __( 'Box Border Color', 'invento' ), $styles['features_box_border'], 'color' ),
                $this->style_field( 'features_box_padding', __( 'Box Padding', 'invento' ), $styles['features_box_padding'] ),
                $this->style_field( 'features_title_size', __( 'Title Size', 'invento' ), $styles['features_title_size'] ),
                $this->style_field( 'features_title_weight', __( 'Title Weight', 'invento' ), $styles['features_title_weight'] ),
            ] );

            $this->render_style_card( __( 'Specs (Icon Rows)', 'invento' ), [
                $this->style_field( 'specs_box_bg', __( 'Container Background', 'invento' ), $styles['specs_box_bg'], 'color' ),
                $this->style_field( 'specs_box_radius', __( 'Container Radius', 'invento' ), $styles['specs_box_radius'] ),
                $this->style_field( 'specs_box_padding', __( 'Container Padding', 'invento' ), $styles['specs_box_padding'] ),
                $this->style_field( 'specs_box_gap', __( 'Container Gap', 'invento' ), $styles['specs_box_gap'] ),
                $this->style_field( 'specs_card_bg', __( 'Card Background', 'invento' ), $styles['specs_card_bg'], 'color' ),
                $this->style_field( 'specs_card_radius', __( 'Card Radius', 'invento' ), $styles['specs_card_radius'] ),
            ] );

            $this->render_style_card( __( 'Buttons', 'invento' ), [
                $this->style_field( 'quote_bg', __( 'Quote Button Background', 'invento' ), $styles['quote_bg'], 'color' ),
                $this->style_field( 'quote_color', __( 'Quote Button Text', 'invento' ), $styles['quote_color'], 'color' ),
                $this->style_field( 'quote_radius', __( 'Quote Button Radius', 'invento' ), $styles['quote_radius'] ),
            ] );

            $this->render_style_card( __( 'Quantity UI', 'invento' ), [
                $this->style_field( 'qty_size', __( 'Control Size', 'invento' ), $styles['qty_size'] ),
                $this->style_field( 'qty_radius', __( 'Control Radius', 'invento' ), $styles['qty_radius'] ),
                $this->style_field( 'qty_border', __( 'Border Color', 'invento' ), $styles['qty_border'], 'color' ),
                $this->style_field( 'qty_bg', __( 'Background', 'invento' ), $styles['qty_bg'], 'color' ),
                $this->style_field( 'qty_text_size', __( 'Text Size', 'invento' ), $styles['qty_text_size'] ),
                $this->style_field( 'qty_text_color', __( 'Text Color', 'invento' ), $styles['qty_text_color'], 'color' ),
            ] );

            echo '</div>';
            submit_button();
            echo '</form>';
        } else {
            echo '<p>' . esc_html__( 'Product grid style settings will be added here.', 'invento' ) . '</p>';
        }
        echo '</div>';
    }

    protected function render_style_card( string $title, array $fields ): void {
        echo '<div class="invento-card">';
        echo '<h3>' . esc_html( $title ) . '</h3>';
        echo '<div class="invento-card-fields">';
        foreach ( $fields as $field ) {
            echo $field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        echo '</div>';
    }

    protected function style_field( string $key, string $label, string $value, string $type = 'text' ): string {
        $name = 'invento_style_settings[' . $key . ']';
        $input = '';

        if ( 'color' === $type ) {
            $input = '<input type="color" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
        } elseif ( 'number' === $type ) {
            $input = '<input type="number" min="1" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
        } else {
            $input = '<input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
        }

        return '<div class="invento-field"><label>' . esc_html( $label ) . '</label>' . $input . '</div>';
    }

    public function render_instructions_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        echo '<div class="wrap invento-settings">';
        echo '<h1>' . esc_html__( 'Invento Instructions', 'invento' ) . '</h1>';
        echo '<p>' . esc_html__( 'Instructions and usage guidance will be added here.', 'invento' ) . '</p>';
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
