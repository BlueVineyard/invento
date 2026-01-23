<?php
namespace Invento\Frontend;

use Invento\Core\Service_Interface;
use Invento\PostTypes\Product_Post_Type;

class Shortcodes implements Service_Interface {
    protected Template_Loader $templates;

    public function __construct( string $plugin_dir ) {
        $this->templates = new Template_Loader( $plugin_dir );
    }

    public function register(): void {
        add_shortcode( 'invento_catalog', [ $this, 'catalog_shortcode' ] );
        add_shortcode( 'invento_product', [ $this, 'product_shortcode' ] );
    }

    public function catalog_shortcode( array $atts ): string {
        $settings = get_option( 'invento_settings', [] );
        $atts = shortcode_atts(
            [
                'layout'   => isset( $settings['catalog_layout'] ) ? $settings['catalog_layout'] : 'grid',
                'per_page' => isset( $settings['products_per_page'] ) ? (int) $settings['products_per_page'] : 12,
                'taxonomy' => '',
                'category' => '',
            ],
            $atts,
            'invento_catalog'
        );

        $paged = max( 1, get_query_var( 'paged' ) );

        $query = new \WP_Query(
            [
                'post_type'      => Product_Post_Type::POST_TYPE,
                'posts_per_page' => (int) $atts['per_page'],
                'paged'          => $paged,
            ]
        );

        ob_start();
        echo '<div class="invento-catalog invento-catalog-' . esc_attr( $atts['layout'] ) . '">';
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $this->templates->get_template( 'parts/product-card.php', [ 'post_id' => get_the_ID(), 'layout' => $atts['layout'] ] );
            }
        } else {
            echo '<p>' . esc_html__( 'No products found.', 'invento' ) . '</p>';
        }
        echo '</div>';

        $big = 999999999;
        echo '<div class="invento-pagination">';
        echo paginate_links(
            [
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $query->max_num_pages,
                'type'      => 'list',
            ]
        );
        echo '</div>';

        wp_reset_postdata();

        return ob_get_clean();
    }

    public function product_shortcode( array $atts ): string {
        $atts = shortcode_atts(
            [
                'id' => 0,
            ],
            $atts,
            'invento_product'
        );

        $post_id = absint( $atts['id'] );
        if ( ! $post_id ) {
            return '';
        }

        $post = get_post( $post_id );
        if ( ! $post || Product_Post_Type::POST_TYPE !== $post->post_type ) {
            return '';
        }

        ob_start();
        $this->templates->get_template( 'single-product.php', [ 'post_id' => $post_id, 'from_shortcode' => true ] );
        return ob_get_clean();
    }
}
