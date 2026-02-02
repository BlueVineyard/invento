<?php
namespace Invento\Admin;

use Invento\Core\Service_Interface;
use Invento\Quote\Quote_Form;

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
            __( 'Quote Form', 'invento' ),
            __( 'Quote Form', 'invento' ),
            'manage_options',
            'invento-quote-form',
            [ $this, 'render_quote_form_page' ]
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

        register_setting(
            'invento_quote_form_group',
            'invento_quote_form_settings',
            [ $this, 'sanitize_quote_form_settings' ]
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

        add_settings_field(
            'enable_variations',
            __( 'Enable Variations', 'invento' ),
            [ $this, 'render_enable_variations_field' ],
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
        $sanitized['enable_variations']         = ! empty( $settings['enable_variations'] ) ? '1' : '0';

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
            'grid_columns_min'        => '240px',
            'grid_gap'                => '24px',
            'grid_card_bg'            => '#FFFFFF',
            'grid_card_border'        => '#E2E2E2',
            'grid_card_radius'        => '0px',
            'grid_title_size'         => '18px',
            'grid_title_color'        => '#121212',
            'grid_excerpt_size'       => '14px',
            'grid_excerpt_color'      => '#535353',
            'grid_thumb_radius'       => '0px',
            'grid_thumb_width'        => '100%',
            'grid_thumb_height'       => 'auto',
            'grid_card_padding'       => '16px',
            'grid_section_gap'        => '10px',
            'grid_sections'           => wp_json_encode( [
                [ 'key' => 'thumb', 'enabled' => true ],
                [ 'key' => 'title', 'enabled' => true ],
                [ 'key' => 'excerpt', 'enabled' => true ],
                [ 'key' => 'quantity', 'enabled' => true ],
                [ 'key' => 'quote', 'enabled' => true ],
            ] ),
            'popup_max_width'         => '520px',
            'popup_bg'                => '#FFFFFF',
            'popup_radius'            => '12px',
            'popup_padding'           => '32px',
            'popup_border'            => 'none',
            'popup_overlay_bg'        => 'rgba(0,0,0,0.6)',
            'popup_title_size'        => '22px',
            'popup_title_weight'      => '600',
            'popup_title_color'       => '#121212',
            'popup_close_size'        => '28px',
            'popup_close_color'       => '#666666',
            'template_sections'       => wp_json_encode( [
                [ 'key' => 'title', 'enabled' => true ],
                [ 'key' => 'variations', 'enabled' => true ],
                [ 'key' => 'quantity', 'enabled' => true ],
                [ 'key' => 'features', 'enabled' => true ],
                [ 'key' => 'specs', 'enabled' => true ],
                [ 'key' => 'description', 'enabled' => true ],
                [ 'key' => 'stock', 'enabled' => true ],
                [ 'key' => 'quote', 'enabled' => true ],
            ] ),
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

            if ( false !== strpos( $key, 'color' ) || in_array( $key, [ 'divider_color', 'thumb_bg', 'thumb_active_border', 'features_box_border', 'specs_box_bg', 'specs_card_bg', 'quote_bg', 'quote_color', 'qty_border', 'qty_bg', 'qty_text_color', 'popup_bg' ], true ) ) {
                $sanitized[ $key ] = sanitize_hex_color( $value ) ? sanitize_hex_color( $value ) : $defaults[ $key ];
                continue;
            }

            if ( in_array( $key, [ 'features_box_padding', 'specs_box_padding', 'popup_padding' ], true ) ) {
                $sanitized[ $key ] = preg_replace( '/[^0-9\\s.%a-z]/i', '', $value );
                continue;
            }

            if ( in_array( $key, [ 'popup_overlay_bg', 'popup_border' ], true ) ) {
                $sanitized[ $key ] = preg_replace( '/[^0-9\\s.%a-z(),#]/i', '', $value );
                continue;
            }

            if ( in_array( $key, [ 'title_weight', 'features_title_weight', 'popup_title_weight' ], true ) ) {
                $sanitized[ $key ] = preg_replace( '/[^0-9]/', '', (string) $value );
                continue;
            }

            if ( 'template_sections' === $key ) {
                $sanitized[ $key ] = $this->sanitize_template_sections( $value );
                continue;
            }
            if ( 'grid_sections' === $key ) {
                $sanitized[ $key ] = $this->sanitize_grid_sections( $value );
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

            $this->render_style_card( __( 'Template Order', 'invento' ), [
                $this->template_order_field( $styles['template_sections'] ),
            ] );

            $this->render_style_card( __( 'Quote Popup', 'invento' ), [
                $this->style_field( 'popup_max_width', __( 'Max Width', 'invento' ), $styles['popup_max_width'] ),
                $this->style_field( 'popup_bg', __( 'Background', 'invento' ), $styles['popup_bg'], 'color' ),
                $this->style_field( 'popup_radius', __( 'Border Radius', 'invento' ), $styles['popup_radius'] ),
                $this->style_field( 'popup_padding', __( 'Padding', 'invento' ), $styles['popup_padding'] ),
                $this->style_field( 'popup_border', __( 'Border', 'invento' ), $styles['popup_border'] ),
                $this->style_field( 'popup_overlay_bg', __( 'Overlay Background', 'invento' ), $styles['popup_overlay_bg'] ),
                $this->style_field( 'popup_title_size', __( 'Title Size', 'invento' ), $styles['popup_title_size'] ),
                $this->style_field( 'popup_title_weight', __( 'Title Weight', 'invento' ), $styles['popup_title_weight'] ),
                $this->style_field( 'popup_title_color', __( 'Title Color', 'invento' ), $styles['popup_title_color'], 'color' ),
                $this->style_field( 'popup_close_size', __( 'Close Button Size', 'invento' ), $styles['popup_close_size'] ),
                $this->style_field( 'popup_close_color', __( 'Close Button Color', 'invento' ), $styles['popup_close_color'], 'color' ),
            ] );

            echo '</div>';
            submit_button();
            echo '</form>';
        } else {
            $styles = wp_parse_args( get_option( 'invento_style_settings', [] ), self::get_style_defaults() );
            echo '<form method="post" action="options.php">';
            settings_fields( 'invento_style_settings_group' );

            echo '<div class="invento-settings-grid">';

            $this->render_style_card( __( 'Grid Layout', 'invento' ), [
                $this->style_field( 'grid_columns_min', __( 'Min Column Width', 'invento' ), $styles['grid_columns_min'] ),
                $this->style_field( 'grid_gap', __( 'Grid Gap', 'invento' ), $styles['grid_gap'] ),
            ] );

            $this->render_style_card( __( 'Card', 'invento' ), [
                $this->style_field( 'grid_card_bg', __( 'Card Background', 'invento' ), $styles['grid_card_bg'], 'color' ),
                $this->style_field( 'grid_card_border', __( 'Card Border', 'invento' ), $styles['grid_card_border'], 'color' ),
                $this->style_field( 'grid_card_radius', __( 'Card Radius', 'invento' ), $styles['grid_card_radius'] ),
                $this->style_field( 'grid_card_padding', __( 'Card Padding', 'invento' ), $styles['grid_card_padding'] ),
                $this->style_field( 'grid_section_gap', __( 'Section Spacing', 'invento' ), $styles['grid_section_gap'] ),
            ] );

            $this->render_style_card( __( 'Typography', 'invento' ), [
                $this->style_field( 'grid_title_size', __( 'Title Size', 'invento' ), $styles['grid_title_size'] ),
                $this->style_field( 'grid_title_color', __( 'Title Color', 'invento' ), $styles['grid_title_color'], 'color' ),
                $this->style_field( 'grid_excerpt_size', __( 'Excerpt Size', 'invento' ), $styles['grid_excerpt_size'] ),
                $this->style_field( 'grid_excerpt_color', __( 'Excerpt Color', 'invento' ), $styles['grid_excerpt_color'], 'color' ),
            ] );

            $this->render_style_card( __( 'Thumbnail', 'invento' ), [
                $this->style_field( 'grid_thumb_radius', __( 'Thumb Radius', 'invento' ), $styles['grid_thumb_radius'] ),
                $this->style_field( 'grid_thumb_width', __( 'Thumb Width', 'invento' ), $styles['grid_thumb_width'] ),
                $this->style_field( 'grid_thumb_height', __( 'Thumb Height', 'invento' ), $styles['grid_thumb_height'] ),
            ] );

            $this->render_style_card( __( 'Grid Order', 'invento' ), [
                $this->grid_order_field( $styles['grid_sections'] ),
            ] );

            echo '</div>';
            submit_button();
            echo '</form>';
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

    protected function template_order_field( string $value ): string {
        $sections = $this->decode_template_sections( $value );
        $labels = $this->get_template_section_labels();

        $html  = '<div class="invento-template-order">';
        $html .= '<input type="hidden" name="invento_style_settings[template_sections]" class="invento-template-order-input" value="' . esc_attr( wp_json_encode( $sections ) ) . '" />';
        $html .= '<ul class="invento-template-order-list">';
        foreach ( $sections as $section ) {
            $key = $section['key'];
            $enabled = (bool) $section['enabled'];
            $label = isset( $labels[ $key ] ) ? $labels[ $key ] : $key;
            $html .= '<li class="invento-template-order-item" data-key="' . esc_attr( $key ) . '">';
            $html .= '<span class="invento-drag-handle">≡</span>';
            $html .= '<span class="invento-template-label">' . esc_html( $label ) . '</span>';
            $html .= '<label class="invento-switch"><input type="checkbox" ' . checked( $enabled, true, false ) . ' /></label>';
            $html .= '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

    protected function sanitize_template_sections( $value ): string {
        $decoded = $this->decode_template_sections( $value );
        return wp_json_encode( $decoded );
    }

    protected function decode_template_sections( $value ): array {
        $allowed = array_keys( $this->get_template_section_labels() );
        $data = [];

        if ( is_string( $value ) ) {
            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) ) {
                $data = $decoded;
            }
        } elseif ( is_array( $value ) ) {
            $data = $value;
        }

        $normalized = [];
        $seen = [];
        foreach ( $data as $item ) {
            if ( ! is_array( $item ) || empty( $item['key'] ) ) {
                continue;
            }
            $key = sanitize_key( $item['key'] );
            if ( in_array( $key, $allowed, true ) && ! isset( $seen[ $key ] ) ) {
                $normalized[] = [
                    'key'     => $key,
                    'enabled' => ! empty( $item['enabled'] ),
                ];
                $seen[ $key ] = true;
            }
        }

        foreach ( $allowed as $key ) {
            if ( ! isset( $seen[ $key ] ) ) {
                $normalized[] = [ 'key' => $key, 'enabled' => true ];
            }
        }

        return $normalized;
    }

    protected function get_template_section_labels(): array {
        return [
            'title'      => __( 'Title & Short Description', 'invento' ),
            'variations' => __( 'Variations', 'invento' ),
            'quantity'   => __( 'Quantity Selector', 'invento' ),
            'features'   => __( 'Main Features', 'invento' ),
            'specs'      => __( 'Icon Specs', 'invento' ),
            'stock'      => __( 'Stock Status', 'invento' ),
            'quote'      => __( 'Quote Button', 'invento' ),
        ];
    }

    protected function grid_order_field( string $value ): string {
        $sections = $this->decode_grid_sections( $value );
        $labels = $this->get_grid_section_labels();

        $html  = '<div class="invento-template-order invento-grid-order">';
        $html .= '<input type="hidden" name="invento_style_settings[grid_sections]" class="invento-template-order-input invento-grid-order-input" value="' . esc_attr( wp_json_encode( $sections ) ) . '" />';
        $html .= '<ul class="invento-template-order-list invento-grid-order-list">';
        foreach ( $sections as $section ) {
            $key = $section['key'];
            $enabled = (bool) $section['enabled'];
            $label = isset( $labels[ $key ] ) ? $labels[ $key ] : $key;
            $html .= '<li class="invento-template-order-item invento-grid-order-item" data-key="' . esc_attr( $key ) . '">';
            $html .= '<span class="invento-drag-handle">≡</span>';
            $html .= '<span class="invento-template-label">' . esc_html( $label ) . '</span>';
            $html .= '<label class="invento-switch"><input type="checkbox" ' . checked( $enabled, true, false ) . ' /></label>';
            $html .= '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

    protected function sanitize_grid_sections( $value ): string {
        $decoded = $this->decode_grid_sections( $value );
        return wp_json_encode( $decoded );
    }

    protected function decode_grid_sections( $value ): array {
        $allowed = array_keys( $this->get_grid_section_labels() );
        $data = [];

        if ( is_string( $value ) ) {
            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) ) {
                $data = $decoded;
            }
        } elseif ( is_array( $value ) ) {
            $data = $value;
        }

        $normalized = [];
        $seen = [];
        foreach ( $data as $item ) {
            if ( ! is_array( $item ) || empty( $item['key'] ) ) {
                continue;
            }
            $key = sanitize_key( $item['key'] );
            if ( in_array( $key, $allowed, true ) && ! isset( $seen[ $key ] ) ) {
                $normalized[] = [
                    'key'     => $key,
                    'enabled' => ! empty( $item['enabled'] ),
                ];
                $seen[ $key ] = true;
            }
        }

        foreach ( $allowed as $key ) {
            if ( ! isset( $seen[ $key ] ) ) {
                $normalized[] = [ 'key' => $key, 'enabled' => true ];
            }
        }

        return $normalized;
    }

    protected function get_grid_section_labels(): array {
        return [
            'thumb'    => __( 'Thumbnail', 'invento' ),
            'title'    => __( 'Title', 'invento' ),
            'excerpt'  => __( 'Short Description', 'invento' ),
            'quantity' => __( 'Quantity Selector', 'invento' ),
            'quote'    => __( 'Quote Button', 'invento' ),
        ];
    }


    public function render_instructions_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'custom-fields';
        if ( ! in_array( $tab, [ 'custom-fields', 'shortcodes' ], true ) ) {
            $tab = 'custom-fields';
        }

        echo '<div class="wrap invento-settings">';
        echo '<h1>' . esc_html__( 'Invento Instructions', 'invento' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a class="nav-tab ' . ( 'custom-fields' === $tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=invento-instructions&tab=custom-fields' ) ) . '">' . esc_html__( 'Custom Fields Info', 'invento' ) . '</a>';
        echo '<a class="nav-tab ' . ( 'shortcodes' === $tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=invento-instructions&tab=shortcodes' ) ) . '">' . esc_html__( 'Shortcodes Info', 'invento' ) . '</a>';
        echo '</h2>';

        if ( 'custom-fields' === $tab ) {
            $this->render_custom_fields_info();
        } elseif ( 'shortcodes' === $tab ) {
            $this->render_shortcodes_info();
        }

        echo '</div>';
    }

    protected function render_custom_fields_info(): void {
        $fields = [
            [
                'title' => __( 'Short Description', 'invento' ),
                'key'   => '_invento_short_description',
                'type'  => 'string',
                'code'  => '$short_description = get_post_meta( get_the_ID(), \'_invento_short_description\', true );
echo esc_html( $short_description );',
            ],
            [
                'title' => __( 'Stock Mode', 'invento' ),
                'key'   => '_invento_stock_mode',
                'type'  => 'string (none | simple | label)',
                'code'  => '$stock_mode = get_post_meta( get_the_ID(), \'_invento_stock_mode\', true );
// Returns: "none", "simple", or "label"
echo esc_html( $stock_mode );',
            ],
            [
                'title' => __( 'Stock Quantity', 'invento' ),
                'key'   => '_invento_stock_quantity',
                'type'  => 'integer',
                'code'  => '$stock_qty = (int) get_post_meta( get_the_ID(), \'_invento_stock_quantity\', true );
echo esc_html( $stock_qty );',
            ],
            [
                'title' => __( 'Stock Label', 'invento' ),
                'key'   => '_invento_stock_label',
                'type'  => 'string',
                'code'  => '$stock_label = get_post_meta( get_the_ID(), \'_invento_stock_label\', true );
echo esc_html( $stock_label );',
            ],
            [
                'title' => __( 'Featured Video Type', 'invento' ),
                'key'   => '_invento_featured_video_type',
                'type'  => 'string (none | self_hosted | youtube | vimeo | other)',
                'code'  => '$video_type = get_post_meta( get_the_ID(), \'_invento_featured_video_type\', true );
echo esc_html( $video_type );',
            ],
            [
                'title' => __( 'Featured Video URL', 'invento' ),
                'key'   => '_invento_featured_video_url',
                'type'  => 'URL string',
                'code'  => '$video_url = get_post_meta( get_the_ID(), \'_invento_featured_video_url\', true );
echo esc_url( $video_url );',
            ],
            [
                'title' => __( 'Gallery Image IDs', 'invento' ),
                'key'   => '_invento_gallery_ids',
                'type'  => 'JSON array of attachment IDs',
                'code'  => '$gallery_ids = json_decode( (string) get_post_meta( get_the_ID(), \'_invento_gallery_ids\', true ), true );
$gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : [];

foreach ( $gallery_ids as $attachment_id ) {
    echo wp_get_attachment_image( (int) $attachment_id, \'medium\' );
}',
            ],
            [
                'title' => __( 'Variations', 'invento' ),
                'key'   => '_invento_variations',
                'type'  => 'JSON array of objects [{name, options[]}, ...]',
                'code'  => '$variations = json_decode( (string) get_post_meta( get_the_ID(), \'_invento_variations\', true ), true );
$variations = is_array( $variations ) ? $variations : [];

foreach ( $variations as $variation ) {
    $name    = isset( $variation[\'name\'] ) ? $variation[\'name\'] : \'\';
    $options = isset( $variation[\'options\'] ) && is_array( $variation[\'options\'] ) ? $variation[\'options\'] : [];

    echo \'<strong>\' . esc_html( $name ) . \':</strong> \';
    echo esc_html( implode( \', \', $options ) );
    echo \'<br>\';
}',
            ],
            [
                'title' => __( 'Icon Specs (Icon + Text Rows)', 'invento' ),
                'key'   => '_invento_icon_text_rows',
                'type'  => 'JSON array of objects [{icon_type, title, description}, ...]',
                'code'  => '$icon_rows = json_decode( (string) get_post_meta( get_the_ID(), \'_invento_icon_text_rows\', true ), true );
$icon_rows = is_array( $icon_rows ) ? $icon_rows : [];

foreach ( $icon_rows as $row ) {
    $icon  = isset( $row[\'icon_type\'] ) ? $row[\'icon_type\'] : \'\';
    $title = isset( $row[\'title\'] ) ? $row[\'title\'] : \'\';
    $desc  = isset( $row[\'description\'] ) ? $row[\'description\'] : \'\';

    echo \'<i class="\' . esc_attr( $icon ) . \'"></i> \';
    echo \'<strong>\' . esc_html( $title ) . \'</strong>: \';
    echo esc_html( $desc ) . \'<br>\';
}',
            ],
            [
                'title' => __( 'Main Features', 'invento' ),
                'key'   => '_invento_main_features',
                'type'  => 'JSON array of objects [{title, description}, ...]',
                'code'  => '$features = json_decode( (string) get_post_meta( get_the_ID(), \'_invento_main_features\', true ), true );
$features = is_array( $features ) ? $features : [];

foreach ( $features as $feature ) {
    $title = isset( $feature[\'title\'] ) ? $feature[\'title\'] : \'\';
    $desc  = isset( $feature[\'description\'] ) ? $feature[\'description\'] : \'\';

    echo \'<h4>\' . esc_html( $title ) . \'</h4>\';
    echo \'<p>\' . esc_html( $desc ) . \'</p>\';
}',
            ],
            [
                'title' => __( 'Quote Button Mode', 'invento' ),
                'key'   => '_invento_quote_button_mode',
                'type'  => 'string (global | product | disabled)',
                'code'  => '$quote_mode = get_post_meta( get_the_ID(), \'_invento_quote_button_mode\', true );
// Returns: "global", "product", or "disabled"
echo esc_html( $quote_mode );',
            ],
            [
                'title' => __( 'Quote Button Label', 'invento' ),
                'key'   => '_invento_quote_button_label',
                'type'  => 'string',
                'code'  => '$quote_label = get_post_meta( get_the_ID(), \'_invento_quote_button_label\', true );
echo esc_html( $quote_label );',
            ],
            [
                'title' => __( 'Quote Button URL', 'invento' ),
                'key'   => '_invento_quote_button_url',
                'type'  => 'URL string',
                'code'  => '$quote_url = get_post_meta( get_the_ID(), \'_invento_quote_button_url\', true );
echo esc_url( $quote_url );',
            ],
            [
                'title' => __( 'Product Description', 'invento' ),
                'key'   => '_invento_description',
                'type'  => 'HTML string (WYSIWYG rich text)',
                'code'  => '$description = get_post_meta( get_the_ID(), \'_invento_description\', true );
if ( $description && \'\' !== trim( $description ) ) {
    echo wp_kses_post( wpautop( $description ) );
}',
            ],
        ];

        echo '<p>' . esc_html__( 'Use these snippets inside any WordPress loop (e.g. WP_Query, get_posts) to display product data. All snippets use get_the_ID() so they work in any query context.', 'invento' ) . '</p>';

        echo '<div class="invento-settings-grid">';
        foreach ( $fields as $field ) {
            echo '<div class="invento-card">';
            echo '<h3>' . esc_html( $field['title'] ) . '</h3>';
            echo '<div class="invento-card-fields">';
            echo '<div class="invento-field"><label>' . esc_html__( 'Meta Key', 'invento' ) . '</label>';
            echo '<code>' . esc_html( $field['key'] ) . '</code></div>';
            echo '<div class="invento-field"><label>' . esc_html__( 'Value Type', 'invento' ) . '</label>';
            echo '<code>' . esc_html( $field['type'] ) . '</code></div>';
            echo '<div class="invento-field"><label>' . esc_html__( 'PHP Snippet', 'invento' ) . '</label>';
            echo '<pre class="invento-code-block"><code>' . esc_html( $field['code'] ) . '</code></pre></div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    protected function render_shortcodes_info(): void {
        $shortcodes = [
            [
                'title'       => __( 'Product Catalog', 'invento' ),
                'shortcode'   => '[invento_catalog]',
                'description' => __( 'Displays a paginated grid or list of all products. Respects the global layout and per-page settings from the General Settings page.', 'invento' ),
                'attributes'  => [
                    'layout'   => __( 'Display layout. Values: "grid" or "list". Default: value from General Settings.', 'invento' ),
                    'per_page' => __( 'Number of products per page. Default: value from General Settings.', 'invento' ),
                    'taxonomy' => __( 'Taxonomy slug to filter by. Default: empty (all products).', 'invento' ),
                    'category' => __( 'Category term slug to filter by. Default: empty (all categories).', 'invento' ),
                ],
                'examples'    => [
                    '[invento_catalog]',
                    '[invento_catalog layout="list" per_page="6"]',
                    '[invento_catalog category="electronics"]',
                ],
            ],
            [
                'title'       => __( 'Single Product', 'invento' ),
                'shortcode'   => '[invento_product]',
                'description' => __( 'Embeds a single product detail view anywhere on your site. Requires the product ID.', 'invento' ),
                'attributes'  => [
                    'id' => __( 'The product post ID. Required.', 'invento' ),
                ],
                'examples'    => [
                    '[invento_product id="123"]',
                    '[invento_product id="456"]',
                ],
            ],
            [
                'title'       => __( 'Product Field', 'invento' ),
                'shortcode'   => '[invento_field]',
                'description' => __( 'Outputs a single meta field value for a product. Works inside any WordPress loop automatically, or accepts an explicit product ID. For simple fields (text, number, URL) the raw value is returned. For complex fields (gallery, variations, specs, features) structured HTML is returned.', 'invento' ),
                'attributes'  => [
                    'field' => __( 'The field name. Required. See accepted values below.', 'invento' ),
                    'id'    => __( 'The product post ID. Optional — defaults to the current post in the loop.', 'invento' ),
                ],
                'examples'    => [
                    '[invento_field field="short_description"]',
                    '[invento_field field="stock_quantity" id="123"]',
                    '[invento_field field="gallery" id="456"]',
                    '[invento_field field="variations"]',
                    '[invento_field field="gallery_first"]',
                    '[invento_field field="features" id="789"]',
                    '[invento_field field="description"]',
                ],
                'field_values' => [
                    'short_description' => __( 'Product short description text.', 'invento' ),
                    'stock_mode'        => __( 'Stock mode: "none", "simple", or "label".', 'invento' ),
                    'stock_quantity'    => __( 'Stock quantity number.', 'invento' ),
                    'stock_label'       => __( 'Custom stock label text.', 'invento' ),
                    'video_type'        => __( 'Video type: "none", "self_hosted", "youtube", "vimeo", or "other".', 'invento' ),
                    'video_url'         => __( 'Video URL.', 'invento' ),
                    'gallery'           => __( 'All gallery images rendered as &lt;img&gt; tags.', 'invento' ),
                    'gallery_first'     => __( 'First gallery image only, rendered as a single &lt;img&gt; tag.', 'invento' ),
                    'variations'        => __( 'Variations rendered as name: options pairs.', 'invento' ),
                    'icon_specs'        => __( 'Icon spec rows rendered with icon, title, and description.', 'invento' ),
                    'features'          => __( 'Main features rendered with title and description.', 'invento' ),
                    'quote_mode'        => __( 'Quote button mode: "global", "product", or "disabled".', 'invento' ),
                    'quote_label'       => __( 'Quote button label text.', 'invento' ),
                    'quote_url'         => __( 'Quote button URL.', 'invento' ),
                    'description'       => __( 'Product description (WYSIWYG rich text content).', 'invento' ),
                ],
            ],
        ];

        echo '<p>' . esc_html__( 'Use these shortcodes in any page, post, or widget area to display Invento products.', 'invento' ) . '</p>';

        echo '<div class="invento-settings-grid">';
        foreach ( $shortcodes as $sc ) {
            echo '<div class="invento-card">';
            echo '<h3>' . esc_html( $sc['title'] ) . '</h3>';
            echo '<div class="invento-card-fields">';

            echo '<div class="invento-field"><label>' . esc_html__( 'Shortcode', 'invento' ) . '</label>';
            echo '<pre class="invento-code-block"><code>' . esc_html( $sc['shortcode'] ) . '</code></pre></div>';

            echo '<div class="invento-field"><label>' . esc_html__( 'Description', 'invento' ) . '</label>';
            echo '<p style="margin:0">' . esc_html( $sc['description'] ) . '</p></div>';

            echo '<div class="invento-field"><label>' . esc_html__( 'Attributes', 'invento' ) . '</label>';
            echo '<table class="widefat striped" style="border-radius:8px;overflow:hidden"><thead><tr>';
            echo '<th>' . esc_html__( 'Attribute', 'invento' ) . '</th>';
            echo '<th>' . esc_html__( 'Description', 'invento' ) . '</th>';
            echo '</tr></thead><tbody>';
            foreach ( $sc['attributes'] as $attr_name => $attr_desc ) {
                echo '<tr><td><code>' . esc_html( $attr_name ) . '</code></td>';
                echo '<td>' . esc_html( $attr_desc ) . '</td></tr>';
            }
            echo '</tbody></table></div>';

            if ( ! empty( $sc['field_values'] ) ) {
                echo '<div class="invento-field"><label>' . esc_html__( 'Accepted Field Values', 'invento' ) . '</label>';
                echo '<table class="widefat striped" style="border-radius:8px;overflow:hidden"><thead><tr>';
                echo '<th>' . esc_html__( 'Field Name', 'invento' ) . '</th>';
                echo '<th>' . esc_html__( 'Output', 'invento' ) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ( $sc['field_values'] as $fv_name => $fv_desc ) {
                    echo '<tr><td><code>' . esc_html( $fv_name ) . '</code></td>';
                    echo '<td>' . esc_html( $fv_desc ) . '</td></tr>';
                }
                echo '</tbody></table></div>';
            }

            echo '<div class="invento-field"><label>' . esc_html__( 'Examples', 'invento' ) . '</label>';
            echo '<pre class="invento-code-block"><code>' . esc_html( implode( "\n", $sc['examples'] ) ) . '</code></pre></div>';

            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    public function render_enable_variations_field(): void {
        $settings = get_option( 'invento_settings', [] );
        $value    = isset( $settings['enable_variations'] ) ? $settings['enable_variations'] : '1';
        echo '<label><input type="checkbox" name="invento_settings[enable_variations]" value="1" ' . checked( $value, '1', false ) . ' /> '
            . esc_html__( 'Show product variations on the frontend', 'invento' ) . '</label>';
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

    public function render_quote_form_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settings = Quote_Form::get_form_settings();

        echo '<div class="wrap invento-settings">';
        echo '<h1>' . esc_html__( 'Quote Form', 'invento' ) . '</h1>';

        if ( ! shortcode_exists( 'fluentform' ) ) {
            echo '<div class="notice notice-warning"><p>' . esc_html__( 'Fluent Forms plugin is required for the quote form popup. Please install and activate it.', 'invento' ) . '</p></div>';
        }

        echo '<form method="post" action="options.php">';
        settings_fields( 'invento_quote_form_group' );

        echo '<table class="form-table"><tbody>';
        echo '<tr><th>' . esc_html__( 'Fluent Form ID', 'invento' ) . '</th>';
        echo '<td><input type="number" min="0" class="small-text" name="invento_quote_form_settings[fluent_form_id]" value="' . esc_attr( $settings['fluent_form_id'] ) . '" />';
        echo '<p class="description">' . esc_html__( 'Enter the ID of the Fluent Form to display in the quote popup. Set to 0 to disable.', 'invento' ) . '</p></td></tr>';
        echo '<tr><th>' . esc_html__( 'Popup Title', 'invento' ) . '</th>';
        echo '<td><input type="text" class="regular-text" name="invento_quote_form_settings[form_title]" value="' . esc_attr( $settings['form_title'] ) . '" /></td></tr>';
        echo '<tr><th>' . esc_html__( 'Product Field Name', 'invento' ) . '</th>';
        echo '<td><input type="text" class="regular-text" name="invento_quote_form_settings[field_name_product]" value="' . esc_attr( $settings['field_name_product'] ) . '" />';
        echo '<p class="description">' . esc_html__( 'The data-name of the Fluent Form field to auto-populate with the product (e.g. interested_product).', 'invento' ) . '</p></td></tr>';
        echo '<tr><th>' . esc_html__( 'Quantity Field Name', 'invento' ) . '</th>';
        echo '<td><input type="text" class="regular-text" name="invento_quote_form_settings[field_name_quantity]" value="' . esc_attr( $settings['field_name_quantity'] ) . '" />';
        echo '<p class="description">' . esc_html__( 'The data-name of the Fluent Form field to auto-populate with the quantity (e.g. numeric_field).', 'invento' ) . '</p></td></tr>';
        echo '</tbody></table>';

        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function sanitize_quote_form_settings( $value ): array {
        if ( ! is_array( $value ) ) {
            return [];
        }

        return [
            'fluent_form_id'       => isset( $value['fluent_form_id'] ) ? absint( $value['fluent_form_id'] ) : 0,
            'form_title'           => isset( $value['form_title'] ) ? sanitize_text_field( $value['form_title'] ) : __( 'Request a Quote', 'invento' ),
            'field_name_product'   => isset( $value['field_name_product'] ) ? sanitize_key( $value['field_name_product'] ) : 'interested_product',
            'field_name_quantity'  => isset( $value['field_name_quantity'] ) ? sanitize_key( $value['field_name_quantity'] ) : 'numeric_field',
        ];
    }
}
