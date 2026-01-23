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
        $is_settings = ( 'toplevel_page_invento-settings' === $screen->id );

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
            [ 'jquery', 'wp-util', 'media-editor' ],
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
        $should_load = is_singular( 'invento_product' ) || is_post_type_archive( 'invento_product' );

        if ( ! $should_load ) {
            $post_id = get_queried_object_id();
            if ( $post_id ) {
                $content = get_post_field( 'post_content', $post_id );
                $should_load = has_shortcode( $content, 'invento_catalog' ) || has_shortcode( $content, 'invento_product' );
            }
        }

        if ( ! $should_load ) {
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
    }
}
