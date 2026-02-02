<?php
namespace Invento\Admin;

use Invento\Core\Service_Interface;
use Invento\Helpers\Sanitization;
use Invento\PostTypes\Product_Post_Type;

class Product_Meta_Boxes implements Service_Interface {
    public function register(): void {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_' . Product_Post_Type::POST_TYPE, [ $this, 'save_meta' ] );
    }

    public function add_meta_boxes(): void {
        add_meta_box(
            'invento-product-details',
            __( 'Product Details', 'invento' ),
            [ $this, 'render_product_details' ],
            Product_Post_Type::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'invento-product-gallery',
            __( 'Gallery & Media', 'invento' ),
            [ $this, 'render_gallery' ],
            Product_Post_Type::POST_TYPE,
            'normal',
            'default'
        );

        $settings = get_option( 'invento_settings', [] );
        $variations_enabled = isset( $settings['enable_variations'] ) ? $settings['enable_variations'] : '1';
        if ( '1' === $variations_enabled ) {
            add_meta_box(
                'invento-product-variations',
                __( 'Variations', 'invento' ),
                [ $this, 'render_variations' ],
                Product_Post_Type::POST_TYPE,
                'normal',
                'default'
            );
        }

        add_meta_box(
            'invento-product-specs',
            __( 'Specs & Features', 'invento' ),
            [ $this, 'render_specs_features' ],
            Product_Post_Type::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'invento-product-description',
            __( 'Product Description', 'invento' ),
            [ $this, 'render_description' ],
            Product_Post_Type::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'invento-product-quote',
            __( 'Request a Quote', 'invento' ),
            [ $this, 'render_quote' ],
            Product_Post_Type::POST_TYPE,
            'side',
            'default'
        );
    }

    public function render_product_details( \WP_Post $post ): void {
        wp_nonce_field( 'invento_save_meta', 'invento_meta_nonce' );

        $short_description = get_post_meta( $post->ID, '_invento_short_description', true );
        $stock_mode        = get_post_meta( $post->ID, '_invento_stock_mode', true );
        $stock_quantity    = get_post_meta( $post->ID, '_invento_stock_quantity', true );
        $stock_label       = get_post_meta( $post->ID, '_invento_stock_label', true );
        $stock_mode        = $stock_mode ? $stock_mode : 'none';
        $video_type        = get_post_meta( $post->ID, '_invento_featured_video_type', true );
        $video_url         = get_post_meta( $post->ID, '_invento_featured_video_url', true );
        $video_type        = $video_type ? $video_type : 'none';

        echo '<p><label for="invento_short_description"><strong>' . esc_html__( 'Short Description', 'invento' ) . '</strong></label></p>';
        echo '<textarea id="invento_short_description" name="invento_short_description" rows="4" class="widefat">' . esc_textarea( $short_description ) . '</textarea>';

        echo '<hr />';
        echo '<p><strong>' . esc_html__( 'Stock Management', 'invento' ) . '</strong></p>';
        echo '<p><label for="invento_stock_mode">' . esc_html__( 'Mode', 'invento' ) . '</label> ';
        echo '<select id="invento_stock_mode" name="invento_stock_mode">';
        echo '<option value="none" ' . selected( $stock_mode, 'none', false ) . '>' . esc_html__( 'None', 'invento' ) . '</option>';
        echo '<option value="simple" ' . selected( $stock_mode, 'simple', false ) . '>' . esc_html__( 'Simple Quantity', 'invento' ) . '</option>';
        echo '<option value="label" ' . selected( $stock_mode, 'label', false ) . '>' . esc_html__( 'Custom Label', 'invento' ) . '</option>';
        echo '</select></p>';

        echo '<p class="invento-stock-field invento-stock-quantity">';
        echo '<label for="invento_stock_quantity">' . esc_html__( 'Quantity', 'invento' ) . '</label> ';
        echo '<input type="number" id="invento_stock_quantity" name="invento_stock_quantity" class="small-text" min="0" value="' . esc_attr( (string) $stock_quantity ) . '" />';
        echo '</p>';

        echo '<p class="invento-stock-field invento-stock-label">';
        echo '<label for="invento_stock_label">' . esc_html__( 'Label', 'invento' ) . '</label> ';
        echo '<input type="text" id="invento_stock_label" name="invento_stock_label" class="regular-text" value="' . esc_attr( $stock_label ) . '" />';
        echo '</p>';

        echo '<hr />';
        echo '<p><strong>' . esc_html__( 'Featured Video', 'invento' ) . '</strong></p>';
        echo '<p><label for="invento_featured_video_type">' . esc_html__( 'Video Type', 'invento' ) . '</label> ';
        echo '<select id="invento_featured_video_type" name="invento_featured_video_type">';
        echo '<option value="none" ' . selected( $video_type, 'none', false ) . '>' . esc_html__( 'None', 'invento' ) . '</option>';
        echo '<option value="self_hosted" ' . selected( $video_type, 'self_hosted', false ) . '>' . esc_html__( 'Self Hosted', 'invento' ) . '</option>';
        echo '<option value="youtube" ' . selected( $video_type, 'youtube', false ) . '>' . esc_html__( 'YouTube', 'invento' ) . '</option>';
        echo '<option value="vimeo" ' . selected( $video_type, 'vimeo', false ) . '>' . esc_html__( 'Vimeo', 'invento' ) . '</option>';
        echo '<option value="other" ' . selected( $video_type, 'other', false ) . '>' . esc_html__( 'Other / Embed URL', 'invento' ) . '</option>';
        echo '</select></p>';

        echo '<p class="invento-video-field">';
        echo '<label for="invento_featured_video_url">' . esc_html__( 'Video URL', 'invento' ) . '</label> ';
        echo '<input type="url" id="invento_featured_video_url" name="invento_featured_video_url" class="regular-text" value="' . esc_attr( $video_url ) . '" />';
        echo '<button type="button" class="button invento-video-select" data-target="#invento_featured_video_url">' . esc_html__( 'Select from Media', 'invento' ) . '</button>';
        echo '<span class="description">' . esc_html__( 'Poster uses the Featured Image.', 'invento' ) . '</span>';
        echo '</p>';
    }

    public function render_gallery( \WP_Post $post ): void {
        $gallery_ids = get_post_meta( $post->ID, '_invento_gallery_ids', true );
        if ( empty( $gallery_ids ) ) {
            $gallery_ids = wp_json_encode( [] );
        }

        echo '<div class="invento-gallery-wrapper">';
        echo '<input type="hidden" id="invento_gallery_ids" name="invento_gallery_ids" value="' . esc_attr( $gallery_ids ) . '" />';
        echo '<button type="button" class="button invento-gallery-select">' . esc_html__( 'Select Images', 'invento' ) . '</button>';
        echo '<div class="invento-gallery-preview"></div>';
        echo '</div>';
    }

    public function render_variations( \WP_Post $post ): void {
        $variations = get_post_meta( $post->ID, '_invento_variations', true );
        if ( empty( $variations ) ) {
            $variations = wp_json_encode( [] );
        }

        echo '<div class="invento-repeater" data-repeater="variations">';
        echo '<input type="hidden" name="invento_variations" class="invento-repeater-input" value="' . esc_attr( $variations ) . '" />';
        echo '<div class="invento-repeater-rows"></div>';
        echo '<button type="button" class="button invento-repeater-add" data-template="variation">' . esc_html__( 'Add Variation Group', 'invento' ) . '</button>';
        echo '</div>';

        echo '<script type="text/template" id="invento-variation-template">';
        echo '<div class="invento-repeater-row">';
        echo '<p><label>' . esc_html__( 'Name', 'invento' ) . '</label> <input type="text" class="widefat" data-field="name" /></p>';
        echo '<p><label>' . esc_html__( 'Options (comma separated)', 'invento' ) . '</label> <input type="text" class="widefat" data-field="options" /></p>';
        echo '<button type="button" class="button-link invento-repeater-remove">' . esc_html__( 'Remove', 'invento' ) . '</button>';
        echo '</div>';
        echo '</script>';
    }

    public function render_specs_features( \WP_Post $post ): void {
        $icon_text = get_post_meta( $post->ID, '_invento_icon_text_rows', true );
        $features  = get_post_meta( $post->ID, '_invento_main_features', true );
        if ( empty( $icon_text ) ) {
            $icon_text = wp_json_encode( [] );
        }
        if ( empty( $features ) ) {
            $features = wp_json_encode( [] );
        }

        echo '<h4>' . esc_html__( 'Icon + Text Rows', 'invento' ) . '</h4>';
        echo '<div class="invento-repeater" data-repeater="icon_text">';
        echo '<input type="hidden" name="invento_icon_text_rows" class="invento-repeater-input" value="' . esc_attr( $icon_text ) . '" />';
        echo '<div class="invento-repeater-rows"></div>';
        echo '<button type="button" class="button invento-repeater-add" data-template="icon-text">' . esc_html__( 'Add Row', 'invento' ) . '</button>';
        echo '</div>';

        echo '<script type="text/template" id="invento-icon-text-template">';
        echo '<div class="invento-repeater-row">';
        echo '<p><label>' . esc_html__( 'Icon Image', 'invento' ) . '</label></p>';
        echo '<div class="invento-icon-upload">';
        echo '<input type="hidden" data-field="icon_type" />';
        echo '<img class="invento-icon-preview" src="" alt="" style="max-width:48px;max-height:48px;display:none;" />';
        echo '<button type="button" class="button invento-icon-select">' . esc_html__( 'Select Icon', 'invento' ) . '</button> ';
        echo '<button type="button" class="button-link invento-icon-remove" style="display:none;color:#b32d2e;">' . esc_html__( 'Remove Icon', 'invento' ) . '</button>';
        echo '</div>';
        echo '<p><label>' . esc_html__( 'Title', 'invento' ) . '</label> <input type="text" class="widefat" data-field="title" /></p>';
        echo '<p><label>' . esc_html__( 'Description', 'invento' ) . '</label> <textarea class="widefat" rows="3" data-field="description"></textarea></p>';
        echo '<button type="button" class="button-link invento-repeater-remove">' . esc_html__( 'Remove', 'invento' ) . '</button>';
        echo '</div>';
        echo '</script>';

        echo '<hr />';
        echo '<h4>' . esc_html__( 'Main Features', 'invento' ) . '</h4>';
        echo '<div class="invento-repeater" data-repeater="main_features">';
        echo '<input type="hidden" name="invento_main_features" class="invento-repeater-input" value="' . esc_attr( $features ) . '" />';
        echo '<div class="invento-repeater-rows"></div>';
        echo '<button type="button" class="button invento-repeater-add" data-template="feature">' . esc_html__( 'Add Feature', 'invento' ) . '</button>';
        echo '</div>';

        echo '<script type="text/template" id="invento-feature-template">';
        echo '<div class="invento-repeater-row">';
        echo '<p><label>' . esc_html__( 'Title', 'invento' ) . '</label> <input type="text" class="widefat" data-field="title" /></p>';
        echo '<p><label>' . esc_html__( 'Description', 'invento' ) . '</label> <textarea class="widefat" rows="3" data-field="description"></textarea></p>';
        echo '<button type="button" class="button-link invento-repeater-remove">' . esc_html__( 'Remove', 'invento' ) . '</button>';
        echo '</div>';
        echo '</script>';
    }

    public function render_description( \WP_Post $post ): void {
        $content = get_post_meta( $post->ID, '_invento_description', true );
        wp_editor( (string) $content, 'invento_description', [
            'textarea_name' => 'invento_description',
            'textarea_rows' => 10,
            'media_buttons' => true,
            'teeny'         => false,
        ] );
    }

    public function render_quote( \WP_Post $post ): void {
        $mode  = get_post_meta( $post->ID, '_invento_quote_button_mode', true );
        $label = get_post_meta( $post->ID, '_invento_quote_button_label', true );
        $url   = get_post_meta( $post->ID, '_invento_quote_button_url', true );
        $mode  = $mode ? $mode : 'global';

        echo '<p><label for="invento_quote_button_mode">' . esc_html__( 'Mode', 'invento' ) . '</label></p>';
        echo '<select id="invento_quote_button_mode" name="invento_quote_button_mode" class="widefat">';
        echo '<option value="global" ' . selected( $mode, 'global', false ) . '>' . esc_html__( 'Use Global', 'invento' ) . '</option>';
        echo '<option value="product" ' . selected( $mode, 'product', false ) . '>' . esc_html__( 'Product Specific', 'invento' ) . '</option>';
        echo '<option value="disabled" ' . selected( $mode, 'disabled', false ) . '>' . esc_html__( 'Disabled', 'invento' ) . '</option>';
        echo '</select>';

        echo '<p><label for="invento_quote_button_label">' . esc_html__( 'Label', 'invento' ) . '</label></p>';
        echo '<input type="text" id="invento_quote_button_label" name="invento_quote_button_label" class="widefat" value="' . esc_attr( $label ) . '" />';

        echo '<p><label for="invento_quote_button_url">' . esc_html__( 'URL', 'invento' ) . '</label></p>';
        echo '<input type="url" id="invento_quote_button_url" name="invento_quote_button_url" class="widefat" value="' . esc_attr( $url ) . '" />';
    }

    public function save_meta( int $post_id ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $nonce = isset( $_POST['invento_meta_nonce'] ) ? wp_unslash( $_POST['invento_meta_nonce'] ) : '';
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'invento_save_meta' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $short_description = isset( $_POST['invento_short_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['invento_short_description'] ) ) : '';
        update_post_meta( $post_id, '_invento_short_description', $short_description );

        $stock_mode = isset( $_POST['invento_stock_mode'] ) ? sanitize_key( wp_unslash( $_POST['invento_stock_mode'] ) ) : 'none';
        if ( ! in_array( $stock_mode, [ 'none', 'simple', 'label' ], true ) ) {
            $stock_mode = 'none';
        }
        update_post_meta( $post_id, '_invento_stock_mode', $stock_mode );

        $stock_quantity = isset( $_POST['invento_stock_quantity'] ) ? absint( $_POST['invento_stock_quantity'] ) : 0;
        update_post_meta( $post_id, '_invento_stock_quantity', $stock_quantity );

        $stock_label = isset( $_POST['invento_stock_label'] ) ? sanitize_text_field( wp_unslash( $_POST['invento_stock_label'] ) ) : '';
        update_post_meta( $post_id, '_invento_stock_label', $stock_label );

        $gallery = isset( $_POST['invento_gallery_ids'] ) ? Sanitization::sanitize_json_array( $_POST['invento_gallery_ids'] ) : wp_json_encode( [] );
        update_post_meta( $post_id, '_invento_gallery_ids', $gallery );

        $variations = isset( $_POST['invento_variations'] ) ? Sanitization::sanitize_json_array( $_POST['invento_variations'] ) : wp_json_encode( [] );
        update_post_meta( $post_id, '_invento_variations', $variations );

        $icon_text = isset( $_POST['invento_icon_text_rows'] ) ? Sanitization::sanitize_icon_rows( $_POST['invento_icon_text_rows'] ) : wp_json_encode( [] );
        update_post_meta( $post_id, '_invento_icon_text_rows', $icon_text );

        $features = isset( $_POST['invento_main_features'] ) ? Sanitization::sanitize_json_array( $_POST['invento_main_features'] ) : wp_json_encode( [] );
        update_post_meta( $post_id, '_invento_main_features', $features );

        $quote_mode = isset( $_POST['invento_quote_button_mode'] ) ? sanitize_key( wp_unslash( $_POST['invento_quote_button_mode'] ) ) : 'global';
        if ( ! in_array( $quote_mode, [ 'global', 'product', 'disabled' ], true ) ) {
            $quote_mode = 'global';
        }
        update_post_meta( $post_id, '_invento_quote_button_mode', $quote_mode );

        $quote_label = isset( $_POST['invento_quote_button_label'] ) ? sanitize_text_field( wp_unslash( $_POST['invento_quote_button_label'] ) ) : '';
        update_post_meta( $post_id, '_invento_quote_button_label', $quote_label );

        $quote_url = isset( $_POST['invento_quote_button_url'] ) ? esc_url_raw( wp_unslash( $_POST['invento_quote_button_url'] ) ) : '';
        update_post_meta( $post_id, '_invento_quote_button_url', $quote_url );

        $description = isset( $_POST['invento_description'] ) ? wp_kses_post( wp_unslash( $_POST['invento_description'] ) ) : '';
        update_post_meta( $post_id, '_invento_description', $description );

        $video_type = isset( $_POST['invento_featured_video_type'] ) ? sanitize_key( wp_unslash( $_POST['invento_featured_video_type'] ) ) : 'none';
        if ( ! in_array( $video_type, [ 'none', 'self_hosted', 'youtube', 'vimeo', 'other' ], true ) ) {
            $video_type = 'none';
        }
        update_post_meta( $post_id, '_invento_featured_video_type', $video_type );

        $video_url = isset( $_POST['invento_featured_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['invento_featured_video_url'] ) ) : '';
        update_post_meta( $post_id, '_invento_featured_video_url', $video_url );
    }
}
