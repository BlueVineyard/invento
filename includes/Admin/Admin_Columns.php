<?php
namespace Invento\Admin;

use Invento\Core\Service_Interface;
use Invento\PostTypes\Product_Post_Type;

class Admin_Columns implements Service_Interface {
    public function register(): void {
        add_filter( 'manage_' . Product_Post_Type::POST_TYPE . '_posts_columns', [ $this, 'add_columns' ] );
        add_action( 'manage_' . Product_Post_Type::POST_TYPE . '_posts_custom_column', [ $this, 'render_columns' ], 10, 2 );
        add_filter( 'manage_edit-' . Product_Post_Type::POST_TYPE . '_sortable_columns', [ $this, 'sortable_columns' ] );
        add_action( 'pre_get_posts', [ $this, 'handle_sorting' ] );
    }

    public function add_columns( array $columns ): array {
        $new_columns = [];
        foreach ( $columns as $key => $label ) {
            if ( 'title' === $key ) {
                $new_columns['thumbnail'] = __( 'Thumbnail', 'invento' );
            }
            $new_columns[ $key ] = $label;
        }

        $new_columns['short_description'] = __( 'Short Description', 'invento' );
        $new_columns['stock']             = __( 'Stock', 'invento' );
        $new_columns['quote_mode']        = __( 'Quote Button', 'invento' );

        return $new_columns;
    }

    public function render_columns( string $column, int $post_id ): void {
        if ( 'thumbnail' === $column ) {
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, [ 50, 50 ] );
            } else {
                echo '&mdash;';
            }
            return;
        }

        if ( 'short_description' === $column ) {
            $short = get_post_meta( $post_id, '_invento_short_description', true );
            if ( ! $short ) {
                $short = get_the_excerpt( $post_id );
            }
            echo esc_html( wp_trim_words( $short, 12 ) );
            return;
        }

        if ( 'stock' === $column ) {
            $mode     = get_post_meta( $post_id, '_invento_stock_mode', true );
            $quantity = (int) get_post_meta( $post_id, '_invento_stock_quantity', true );
            $label    = get_post_meta( $post_id, '_invento_stock_label', true );
            if ( 'simple' === $mode ) {
                echo esc_html( $quantity );
            } elseif ( 'label' === $mode && $label ) {
                echo esc_html( $label );
            } else {
                echo '&mdash;';
            }
            return;
        }

        if ( 'quote_mode' === $column ) {
            $mode = get_post_meta( $post_id, '_invento_quote_button_mode', true );
            if ( ! $mode ) {
                $mode = 'global';
            }
            echo esc_html( ucfirst( $mode ) );
        }
    }

    public function sortable_columns( array $columns ): array {
        $columns['stock'] = 'invento_stock_quantity';
        return $columns;
    }

    public function handle_sorting( \WP_Query $query ): void {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || Product_Post_Type::POST_TYPE !== $screen->post_type ) {
            return;
        }

        if ( 'invento_stock_quantity' === $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', '_invento_stock_quantity' );
            $query->set( 'orderby', 'meta_value_num' );
        }
    }
}
