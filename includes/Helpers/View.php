<?php
namespace Invento\Helpers;

class View {
    public static function render( string $template_path, array $args = [] ): void {
        if ( ! file_exists( $template_path ) ) {
            return;
        }

        if ( ! empty( $args ) ) {
            extract( $args, EXTR_SKIP );
        }

        include $template_path;
    }
}
