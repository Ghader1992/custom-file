<?php
/**
 * WooCommerce Extra Specifications Class
 *
 * @package WooCommerce_Extra_Specifications
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Extra_Specifications class.
 */
class WC_Extra_Specifications {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_product_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_metabox_data' ) ); // Use save_post_{post_type} for specificity
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tab' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the metabox to the product edit page.
	 */
	public function add_product_metabox() {
		add_meta_box(
			'wc_extra_specifications_metabox',
			__( 'Extra Specifications', 'woocommerce-extra-specifications' ),
			array( $this, 'render_metabox_content' ),
			'product',
			'normal',
			'default'
		);
	}

	/**
	 * Render the metabox content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox_content( $post ) {
		wp_nonce_field( 'wc_extra_specifications_save_metabox_data', 'wc_extra_specifications_metabox_nonce' );

		$specifications = get_post_meta( $post->ID, '_wc_extra_specifications', true );
		?>
		<div id="wc_extra_specifications_wrapper">
			<table id="wc_extra_specifications_table" class="widefat">
				<thead>
					<tr>
						<th class="spec-key"><?php esc_html_e( 'Key', 'woocommerce-extra-specifications' ); ?></th>
						<th class="spec-value"><?php esc_html_e( 'Value', 'woocommerce-extra-specifications' ); ?></th>
						<th class="spec-actions"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( ! empty( $specifications ) && is_array( $specifications ) ) {
						foreach ( $specifications as $index => $spec ) {
							// Ensure spec is an array and keys exist before accessing
							$key_value = isset($spec['key']) ? $spec['key'] : '';
							$value_value = isset($spec['value']) ? $spec['value'] : '';
							?>
							<tr class="wc-extra-spec-row">
								<td><input type="text" name="wc_extra_specifications[<?php echo esc_attr( $index ); ?>][key]" value="<?php echo esc_attr( $key_value ); ?>" class="widefat" /></td>
								<td><input type="text" name="wc_extra_specifications[<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $value_value ); ?>" class="widefat" /></td>
								<td><button type="button" class="button wc-remove-spec-row"><?php esc_html_e( 'Remove', 'woocommerce-extra-specifications' ); ?></button></td>
							</tr>
							<?php
						}
					}
					// No blank row is added here by PHP if $specifications is empty.
					// The JavaScript will add the initial row if the tbody is empty.
					?>
				</tbody>
			</table>
			<button type="button" class="button button-primary wc-add-spec-row"><?php esc_html_e( 'Add Specification', 'woocommerce-extra-specifications' ); ?></button>
		</div>
		<?php
		// Inline script is intentionally removed and handled by the enqueued JS file.
	}

	/**
	 * Save the metabox data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_metabox_data( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['wc_extra_specifications_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_key( $_POST['wc_extra_specifications_metabox_nonce'] ), 'wc_extra_specifications_save_metabox_data' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['wc_extra_specifications'] ) && is_array( $_POST['wc_extra_specifications'] ) ) {
			$specifications_data = array();
			foreach ( $_POST['wc_extra_specifications'] as $spec_item ) {
				// Ensure $spec_item is an array and keys exist before accessing
				if ( is_array($spec_item) && ! empty( $spec_item['key'] ) && isset( $spec_item['value'] ) ) { // Allow empty value
					$specifications_data[] = array(
						'key'   => sanitize_text_field( wp_unslash( $spec_item['key'] ) ),
						'value' => sanitize_text_field( wp_unslash( $spec_item['value'] ) ),
					);
				}
			}

			if ( ! empty( $specifications_data ) ) {
				update_post_meta( $post_id, '_wc_extra_specifications', $specifications_data );
			} else {
				delete_post_meta( $post_id, '_wc_extra_specifications' );
			}
		} else {
			// If no data is submitted (e.g., all rows removed and 'wc_extra_specifications' is not in POST), delete the meta.
			delete_post_meta( $post_id, '_wc_extra_specifications' );
		}
	}

	/**
	 * Add the custom product tab.
	 *
	 * @param array $tabs Existing product tabs.
	 * @return array Modified product tabs.
	 */
	public function add_product_tab( $tabs ) {
		global $product;

		if ( ! $product ) {
			return $tabs;
		}

		$specifications = get_post_meta( $product->get_id(), '_wc_extra_specifications', true );

		if ( ! empty( $specifications ) && is_array( $specifications ) ) {
            // Check if there's at least one valid specification
            $has_valid_spec = false;
            foreach ($specifications as $spec) {
                if (is_array($spec) && !empty($spec['key']) && isset($spec['value'])) {
                    $has_valid_spec = true;
                    break;
                }
            }
            if ($has_valid_spec) {
                $tabs['extra_specifications_tab'] = array(
                    'title'    => __( 'Extra Specifications', 'woocommerce-extra-specifications' ),
                    'priority' => 50,
                    'callback' => array( $this, 'render_product_tab_content' ),
                );
            }
		}
		return $tabs;
	}

	/**
	 * Render the content for the custom product tab.
	 */
	public function render_product_tab_content() {
		global $product;

		if ( ! $product ) {
			return;
		}
		$specifications = get_post_meta( $product->get_id(), '_wc_extra_specifications', true );

		if ( ! empty( $specifications ) && is_array( $specifications ) ) {
			echo '<h2>' . esc_html__( 'Extra Specifications', 'woocommerce-extra-specifications' ) . '</h2>';
			echo '<table class="shop_attributes shop_attributes_extra_specs">';
			$has_content = false;
			foreach ( $specifications as $spec ) {
				if ( is_array($spec) && ! empty( $spec['key'] ) && isset( $spec['value'] ) ) {
					echo '<tr>';
					echo '<th>' . esc_html( $spec['key'] ) . '</th>';
					echo '<td><p>' . esc_html( $spec['value'] ) . '</p></td>';
					echo '</tr>';
					$has_content = true;
				}
			}
			echo '</table>';
			if (!$has_content) {
				echo '<p>' . esc_html__( 'No extra specifications available for this product.', 'woocommerce-extra-specifications' ) . '</p>';
			}
		} else {
			echo '<p>' . esc_html__( 'No extra specifications available for this product.', 'woocommerce-extra-specifications' ) . '</p>';
		}
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		// Get current screen info
		$screen = get_current_screen();

		// Only enqueue on the product edit screen.
		// Check if $screen is an object and has an id property.
		if ( $screen && ( 'product' === $screen->id && 'post' === $screen->base ) && ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) {
			wp_enqueue_script(
				'wc-extra-specifications-admin-js',
				WC_EXTRA_SPECS_URL . 'admin/js/wc-extra-specifications-admin.js',
				array( 'jquery' ),
				'1.0.1', // Incremented version
				true
			);

			$localized_data = array(
				'remove_text'    => esc_html__( 'Remove', 'woocommerce-extra-specifications' ),
				'last_row_alert' => esc_html__( 'You must have at least one specification row.', 'woocommerce-extra-specifications' ),
			);
			wp_localize_script( 'wc-extra-specifications-admin-js', 'wc_extra_specs_admin', $localized_data );
		}
	}
}
