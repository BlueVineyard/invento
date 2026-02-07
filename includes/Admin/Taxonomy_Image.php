<?php
namespace Invento\Admin;

use Invento\Core\Service_Interface;

class Taxonomy_Image implements Service_Interface {
    private const TAXONOMY = 'invento_product_category';
    private const META_KEY = '_invento_category_image_id';

    public function register(): void {
        add_action( self::TAXONOMY . '_add_form_fields', [ $this, 'add_form_fields' ] );
        add_action( self::TAXONOMY . '_edit_form_fields', [ $this, 'edit_form_fields' ] );
        add_action( 'created_' . self::TAXONOMY, [ $this, 'save_term_meta' ] );
        add_action( 'edited_' . self::TAXONOMY, [ $this, 'save_term_meta' ] );
    }

    public function add_form_fields(): void {
        ?>
        <div class="form-field invento-term-image-wrap">
            <label><?php esc_html_e( 'Category Image', 'invento' ); ?></label>
            <div class="invento-term-image-preview"></div>
            <input type="hidden" name="invento_category_image_id" class="invento-term-image-id" value="" />
            <button type="button" class="button invento-term-image-upload"><?php esc_html_e( 'Upload Image', 'invento' ); ?></button>
            <button type="button" class="button invento-term-image-remove" style="display:none;"><?php esc_html_e( 'Remove Image', 'invento' ); ?></button>
        </div>
        <?php
    }

    public function edit_form_fields( $term ): void {
        $image_id = (int) get_term_meta( $term->term_id, self::META_KEY, true );
        $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
        ?>
        <tr class="form-field invento-term-image-wrap">
            <th scope="row"><label><?php esc_html_e( 'Category Image', 'invento' ); ?></label></th>
            <td>
                <div class="invento-term-image-preview">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="" />
                    <?php endif; ?>
                </div>
                <input type="hidden" name="invento_category_image_id" class="invento-term-image-id" value="<?php echo esc_attr( $image_id ); ?>" />
                <button type="button" class="button invento-term-image-upload"><?php esc_html_e( 'Upload Image', 'invento' ); ?></button>
                <button type="button" class="button invento-term-image-remove" <?php echo $image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove Image', 'invento' ); ?></button>
            </td>
        </tr>
        <?php
    }

    public function save_term_meta( int $term_id ): void {
        if ( ! isset( $_POST['invento_category_image_id'] ) ) {
            return;
        }

        $image_id = absint( $_POST['invento_category_image_id'] );

        if ( $image_id ) {
            update_term_meta( $term_id, self::META_KEY, $image_id );
        } else {
            delete_term_meta( $term_id, self::META_KEY );
        }
    }

    public static function get_image_url( int $term_id, string $size = 'thumbnail' ): string {
        $image_id = (int) get_term_meta( $term_id, self::META_KEY, true );
        if ( ! $image_id ) {
            return '';
        }

        return (string) wp_get_attachment_image_url( $image_id, $size );
    }
}
