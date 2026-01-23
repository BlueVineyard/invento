<?php
namespace Invento\PostTypes;

use Invento\Core\Service_Interface;

class Product_Post_Type implements Service_Interface {
    public const POST_TYPE = 'invento_product';

    public function register(): void {
        add_action( 'init', [ $this, 'register_post_type' ] );
    }

    public function register_post_type(): void {
        $labels = [
            'name'               => __( 'Products', 'invento' ),
            'singular_name'      => __( 'Product', 'invento' ),
            'add_new'            => __( 'Add New', 'invento' ),
            'add_new_item'       => __( 'Add New Product', 'invento' ),
            'edit_item'          => __( 'Edit Product', 'invento' ),
            'new_item'           => __( 'New Product', 'invento' ),
            'view_item'          => __( 'View Product', 'invento' ),
            'search_items'       => __( 'Search Products', 'invento' ),
            'not_found'          => __( 'No products found', 'invento' ),
            'not_found_in_trash' => __( 'No products found in Trash', 'invento' ),
            'all_items'          => __( 'All Products', 'invento' ),
            'menu_name'          => __( 'Products', 'invento' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => [ 'slug' => 'products' ],
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-archive',
            'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
        ];

        register_post_type( self::POST_TYPE, $args );
    }
}
