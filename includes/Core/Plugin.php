<?php
namespace Invento\Core;

use Invento\Admin\Admin_Columns;
use Invento\Admin\Product_Meta_Boxes;
use Invento\Admin\Settings_Page;
use Invento\Admin\Taxonomy_Image;
use Invento\Frontend\Frontend_Loader;
use Invento\Frontend\Shortcodes;
use Invento\PostTypes\Product_Post_Type;
use Invento\Quote\Quote_Form;

class Plugin {
    protected string $plugin_dir;
    protected string $plugin_url;
    protected string $plugin_file;

    public function __construct( string $plugin_dir, string $plugin_url, string $plugin_file ) {
        $this->plugin_dir  = $plugin_dir;
        $this->plugin_url  = $plugin_url;
        $this->plugin_file = $plugin_file;
    }

    public function run(): void {
        foreach ( $this->get_services() as $service ) {
            if ( $service instanceof Service_Interface ) {
                $service->register();
            }
        }
    }

    /**
     * @return Service_Interface[]
     */
    protected function get_services(): array {
        return [
            new I18n(),
            new Product_Post_Type(),
            new Assets( $this->plugin_url ),
            new Product_Meta_Boxes(),
            new Settings_Page(),
            new Admin_Columns(),
            new Taxonomy_Image(),
            new Frontend_Loader( $this->plugin_dir ),
            new Shortcodes( $this->plugin_dir ),
            new Quote_Form(),
        ];
    }
}
