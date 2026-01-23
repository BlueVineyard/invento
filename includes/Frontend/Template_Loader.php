<?php
namespace Invento\Frontend;

use Invento\Helpers\View;

class Template_Loader {
    protected string $plugin_dir;

    public function __construct( string $plugin_dir ) {
        $this->plugin_dir = $plugin_dir;
    }

    public function locate_template( string $template_name ): string {
        $template_name = ltrim( $template_name, '/' );

        $child = trailingslashit( get_stylesheet_directory() ) . 'invento/' . $template_name;
        if ( file_exists( $child ) ) {
            return $child;
        }

        $parent = trailingslashit( get_template_directory() ) . 'invento/' . $template_name;
        if ( file_exists( $parent ) ) {
            return $parent;
        }

        $plugin = $this->plugin_dir . 'templates/' . $template_name;
        if ( file_exists( $plugin ) ) {
            return $plugin;
        }

        return '';
    }

    public function get_template( string $template_name, array $args = [] ): void {
        $path = $this->locate_template( $template_name );
        if ( $path ) {
            View::render( $path, $args );
        }
    }
}
