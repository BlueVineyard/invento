<?php
/**
 * Plugin Name: Invento
 * Description: Product catalog + request-a-quote system.
 * Version: 0.1.0
 * Author: Lime Advertising
 * Text Domain: invento
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'INVENTO_VERSION', '0.1.0' );
define( 'INVENTO_PLUGIN_FILE', __FILE__ );
define( 'INVENTO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'INVENTO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'INVENTO_TEXT_DOMAIN', 'invento' );

spl_autoload_register( function ( $class ) {
    if ( 0 !== strpos( $class, 'Invento\\' ) ) {
        return;
    }

    $base_dir = INVENTO_PLUGIN_DIR . 'includes/';
    $relative_class = substr( $class, strlen( 'Invento\\' ) );
    $relative_class = str_replace( '\\', '/', $relative_class );
    $file = $base_dir . $relative_class . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
} );

function invento_bootstrap() {
    $plugin = new Invento\Core\Plugin( INVENTO_PLUGIN_DIR, INVENTO_PLUGIN_URL, INVENTO_PLUGIN_FILE );
    $plugin->run();
}

register_activation_hook( __FILE__, function () {
    $cpt = new Invento\PostTypes\Product_Post_Type();
    $cpt->register();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

invento_bootstrap();
