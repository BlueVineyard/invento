<?php
namespace Invento\Frontend;

use Invento\Core\Service_Interface;
use Invento\PostTypes\Product_Post_Type;

class Frontend_Loader implements Service_Interface {
    protected Template_Loader $templates;

    public function __construct( string $plugin_dir ) {
        $this->templates = new Template_Loader( $plugin_dir );
    }

    public function register(): void {
        add_filter( 'template_include', [ $this, 'template_include' ] );
    }

    public function template_include( string $template ): string {
        if ( is_singular( Product_Post_Type::POST_TYPE ) ) {
            $custom = $this->templates->locate_template( 'single-product.php' );
            return $custom ? $custom : $template;
        }

        if ( is_post_type_archive( Product_Post_Type::POST_TYPE ) ) {
            $custom = $this->templates->locate_template( 'archive-products.php' );
            return $custom ? $custom : $template;
        }

        return $template;
    }
}
