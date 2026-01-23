<?php
namespace Invento\Core;

class I18n implements Service_Interface {
    public function register(): void {
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
    }

    public function load_textdomain(): void {
        load_plugin_textdomain( 'invento', false, dirname( plugin_basename( INVENTO_PLUGIN_FILE ) ) . '/languages' );
    }
}
