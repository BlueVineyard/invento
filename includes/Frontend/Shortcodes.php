<?php

namespace Invento\Frontend;

use Invento\Core\Service_Interface;
use Invento\PostTypes\Product_Post_Type;

class Shortcodes implements Service_Interface
{
    protected Template_Loader $templates;

    public function __construct(string $plugin_dir)
    {
        $this->templates = new Template_Loader($plugin_dir);
    }

    protected static array $field_map = [
        'short_description' => ['key' => '_invento_short_description', 'type' => 'text'],
        'stock_mode'        => ['key' => '_invento_stock_mode', 'type' => 'text'],
        'stock_quantity'    => ['key' => '_invento_stock_quantity', 'type' => 'int'],
        'stock_label'       => ['key' => '_invento_stock_label', 'type' => 'text'],
        'video_type'        => ['key' => '_invento_featured_video_type', 'type' => 'text'],
        'video_url'         => ['key' => '_invento_featured_video_url', 'type' => 'url'],
        'gallery'           => ['key' => '_invento_gallery_ids', 'type' => 'gallery'],
        'variations'        => ['key' => '_invento_variations', 'type' => 'variations'],
        'icon_specs'        => ['key' => '_invento_icon_text_rows', 'type' => 'icon_specs'],
        'features'          => ['key' => '_invento_main_features', 'type' => 'features'],
        'quote_mode'        => ['key' => '_invento_quote_button_mode', 'type' => 'text'],
        'quote_label'       => ['key' => '_invento_quote_button_label', 'type' => 'text'],
        'quote_url'         => ['key' => '_invento_quote_button_url', 'type' => 'url'],
        'gallery_first'     => ['key' => '_invento_gallery_ids', 'type' => 'gallery_first'],
        'description'       => ['key' => '_invento_description', 'type' => 'wysiwyg'],
    ];

    public function register(): void
    {
        add_shortcode('invento_catalog', [$this, 'catalog_shortcode']);
        add_shortcode('invento_product', [$this, 'product_shortcode']);
        add_shortcode('invento_field', [$this, 'field_shortcode']);
        add_shortcode('invento_qty', [$this, 'qty_shortcode']);
    }

    public function catalog_shortcode(array $atts): string
    {
        $settings = get_option('invento_settings', []);
        $atts = shortcode_atts(
            [
                'layout'   => isset($settings['catalog_layout']) ? $settings['catalog_layout'] : 'grid',
                'per_page' => isset($settings['products_per_page']) ? (int) $settings['products_per_page'] : 12,
                'taxonomy' => '',
                'category' => '',
            ],
            $atts,
            'invento_catalog'
        );

        $paged = max(1, get_query_var('paged'));

        $query = new \WP_Query(
            [
                'post_type'      => Product_Post_Type::POST_TYPE,
                'posts_per_page' => (int) $atts['per_page'],
                'paged'          => $paged,
            ]
        );

        ob_start();
        echo '<div class="invento-catalog invento-catalog-' . esc_attr($atts['layout']) . '">';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $this->templates->get_template('parts/product-card.php', ['post_id' => get_the_ID(), 'layout' => $atts['layout']]);
            }
        } else {
            echo '<p>' . esc_html__('No products found.', 'invento') . '</p>';
        }
        echo '</div>';

        $big = 999999999;
        echo '<div class="invento-pagination">';
        echo paginate_links(
            [
                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
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

    public function product_shortcode(array $atts): string
    {
        $atts = shortcode_atts(
            [
                'id' => 0,
            ],
            $atts,
            'invento_product'
        );

        $post_id = absint($atts['id']);
        if (! $post_id) {
            return '';
        }

        $post = get_post($post_id);
        if (! $post || Product_Post_Type::POST_TYPE !== $post->post_type) {
            return '';
        }

        ob_start();
        $this->templates->get_template('single-product.php', ['post_id' => $post_id, 'from_shortcode' => true]);
        return ob_get_clean();
    }

    public function qty_shortcode($atts): string
    {
        $atts = shortcode_atts(
            [
                'value' => '1',
                'min'   => '1',
            ],
            $atts,
            'invento_qty'
        );

        $value = max(1, (int) $atts['value']);
        $min   = max(1, (int) $atts['min']);

        $html  = '<div class="invento-qty-control" data-min="' . esc_attr($min) . '">';
        $html .= '<button type="button" class="invento-qty-btn invento-qty-minus">-</button>';
        $html .= '<input type="number" class="invento-qty-input counter" min="' . esc_attr($min) . '" value="' . esc_attr($value) . '" />';
        $html .= '<button type="button" class="invento-qty-btn invento-qty-plus">+</button>';
        $html .= '</div>';

        return $html;
    }

    public function field_shortcode($atts): string
    {
        $atts = shortcode_atts(
            [
                'field' => '',
                'id' => 0,
            ],
            $atts,
            'invento_field'
        );

        $field_name = sanitize_key($atts['field']);
        if (! isset(self::$field_map[$field_name])) {
            return '';
        }

        $post_id = absint($atts['id']);
        if (! $post_id) {
            $post_id = get_the_ID();
        }
        if (! $post_id) {
            return '';
        }

        $meta_key = self::$field_map[$field_name]['key'];
        $type = self::$field_map[$field_name]['type'];
        $raw_value = get_post_meta($post_id, $meta_key, true);

        switch ($type) {
            case 'int':
                return esc_html((string) (int) $raw_value);

            case 'url':
                return esc_url((string) $raw_value);

            case 'gallery_first':
                $ids = json_decode((string) $raw_value, true);
                $ids = is_array($ids) ? $ids : [];
                if (empty($ids)) {
                    return '';
                }
                return wp_get_attachment_image((int) $ids[0], 'medium');

            case 'gallery':
                $ids = json_decode((string) $raw_value, true);
                $ids = is_array($ids) ? $ids : [];
                if (empty($ids)) {
                    return '';
                }

                $first_url = wp_get_attachment_image_url((int) $ids[0], 'large');
                $html = '<div class="invento-sc-gallery" data-gallery-id="' . esc_attr((string) $post_id) . '">';

                // Main image display
                $html .= '<div class="invento-sc-gallery-main">';
                if (count($ids) > 1) {
                    $html .= '<button type="button" class="invento-sc-gallery-prev"
            aria-label="' . esc_attr__('Previous image', 'invento') . '">&lsaquo;</button>';
                }
                $html .= '<img class="invento-sc-gallery-image" src="' . esc_url($first_url) . '" alt="" />';
                if (count($ids) > 1) {
                    $html .= '<button type="button" class="invento-sc-gallery-next"
            aria-label="' . esc_attr__('Next image', 'invento') . '">&rsaquo;</button>';
                }
                $html .= '</div>';

                // Thumbnail strip
                if (count($ids) > 1) {
                    $html .= '<div class="invento-sc-gallery-thumbs">';
                    foreach ($ids as $index => $attachment_id) {
                        $thumb_url = wp_get_attachment_image_url((int) $attachment_id, 'thumbnail');
                        $large_url = wp_get_attachment_image_url((int) $attachment_id, 'large');
                        $active = 0 === $index ? ' is-active' : '';
                        $html .= '<button type="button" class="invento-sc-gallery-thumb' . $active . '"
            data-full="' . esc_url($large_url) . '">';
                        $html .= '<img src="' . esc_url($thumb_url) . '" alt="" />';
                        $html .= '</button>';
                    }
                    $html .= '</div>';
                }

                $html .= '</div>';
                return $html;

            case 'variations':
                $items = json_decode((string) $raw_value, true);
                $items = is_array($items) ? $items : [];
                if (empty($items)) {
                    return '';
                }
                $html = '<div class="invento-field-variations">';
                foreach ($items as $variation) {
                    $name = isset($variation['name']) ? $variation['name'] : '';
                    $options = isset($variation['options']) && is_array($variation['options']) ? $variation['options'] : [];
                    if ($name && $options) {
                        $html .= '<div class="invento-field-variation">';
                        $html .= '<strong>' . esc_html($name) . ':</strong> ';
                        $html .= esc_html(implode(', ', $options));
                        $html .= '</div>';
                    }
                }
                $html .= '</div>';
                return $html;

            case 'icon_specs':
                $rows = json_decode((string) $raw_value, true);
                $rows = is_array($rows) ? $rows : [];
                if (empty($rows)) {
                    return '';
                }
                $html = '<div class="invento-field-specs">';
                foreach ($rows as $row) {
                    $icon = isset($row['icon_type']) ? $row['icon_type'] : '';
                    $title = isset($row['title']) ? $row['title'] : '';
                    $desc = isset($row['description']) ? $row['description'] : '';
                    $html .= '<div class="invento-field-spec">';
                    if ($icon) {
                        if (is_numeric($icon)) {
                            $html .= wp_get_attachment_image((int) $icon, 'thumbnail', false, ['class' => 'invento-spec-icon']) . ' ';
                        } else {
                            $html .= '<i class="' . esc_attr($icon) . '"></i> ';
                        }
                    }
                    $html .= '<strong>' . esc_html($title) . '</strong>';
                    if ($desc) {
                        $html .= ': ' . esc_html($desc);
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
                return $html;

            case 'features':
                $items = json_decode((string) $raw_value, true);
                $items = is_array($items) ? $items : [];
                if (empty($items)) {
                    return '';
                }
                $html = '<div class="invento-field-features">';
                foreach ($items as $feature) {
                    $title = isset($feature['title']) ? $feature['title'] : '';
                    $desc = isset($feature['description']) ? $feature['description'] : '';
                    $html .= '<div class="invento-field-feature">';
                    if ($title) {
                        $html .= '<h4>' . esc_html($title) . '</h4>';
                    }
                    if ($desc) {
                        $html .= '<p>' . esc_html($desc) . '</p>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
                return $html;

            case 'wysiwyg':
                $content = (string) $raw_value;
                if ('' === trim($content)) {
                    return '';
                }
                return wp_kses_post(wpautop($content));

            default:
                return esc_html((string) $raw_value);
        }
    }
}
