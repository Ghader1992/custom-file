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
		add_action( 'save_post_product', array( $this, 'save_metabox_data' ) );
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
							?>
							<tr class="wc-extra-spec-row">
								<td><input type="text" name="wc_extra_specifications[<?php echo esc_attr( $index ); ?>][key]" value="<?php echo esc_attr( $spec['key'] ); ?>" class="widefat" /></td>
								<td><input type="text" name="wc_extra_specifications[<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $spec['value'] ); ?>" class="widefat" /></td>
								<td><button type="button" class="button wc-remove-spec-row"><?php esc_html_e( 'Remove', 'woocommerce-extra-specifications' ); ?></button></td>
							</tr>
							<?php
						}
					} else {
						// Add a blank row if no data exists
						?>
						<tr class="wc-extra-spec-row">
							<td><input type="text" name="wc_extra_specifications[0][key]" value="" class="widefat" /></td>
							<td><input type="text" name="wc_extra_specifications[0][value]" value="" class="widefat" /></td>
							<td><button type="button" class="button wc-remove-spec-row"><?php esc_html_e( 'Remove', 'woocommerce-extra-specifications' ); ?></button></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<button type="button" class="button button-primary wc-add-spec-row"><?php esc_html_e( 'Add Specification', 'woocommerce-extra-specifications' ); ?></button>
		</div>
		<script type="text/javascript">
			// Basic JavaScript for adding and removing rows will be added in a later step.
			// For now, this is a placeholder.
			jQuery(document).ready(function($) {
				var wrapper = $('#wc_extra_specifications_wrapper');
				var tableBody = wrapper.find('tbody');
				var rowIndex = tableBody.find('tr').length > 0 ? tableBody.find('tr').length : 0;

				wrapper.on('click', '.wc-add-spec-row', function() {
					rowIndex++;
					var newRow = '<tr class="wc-extra-spec-row">' +
						'<td><input type="text" name="wc_extra_specifications[' + rowIndex + '][key]" value="" class="widefat" /></td>' +
						'<td><input type="text" name="wc_extra_specifications[' + rowIndex + '][value]" value="" class="widefat" /></td>' +
						'<td><button type="button" class="button wc-remove-spec-row"><?php esc_html_e( 'Remove', 'woocommerce-extra-specifications' ); ?></button></td>' +
						'</tr>';
					tableBody.append(newRow);
				});

				wrapper.on('click', '.wc-remove-spec-row', function() {
					if (tableBody.find('tr.wc-extra-spec-row').length > 1) {
						$(this).closest('tr.wc-extra-spec-row').remove();
					} else {
						// Optionally, clear the fields if it's the last row instead of removing it
						$(this).closest('tr.wc-extra-spec-row').find('input[type="text"]').val('');
						// alert('You must have at least one specification row.'); // Replaced by localized string
					}
				});
			});
		// Script moved to admin/js/wc-extra-specifications-admin.js and enqueued via enqueue_scripts()
		// </script>
		<?php
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
			// Sanitize and structure the data.
			foreach ( $_POST['wc_extra_specifications'] as $spec_item ) {
				if ( ! empty( $spec_item['key'] ) && ! empty( $spec_item['value'] ) ) {
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
			// If no data is submitted (e.g., all rows removed), delete the meta.
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

		// Check if the product has extra specifications.
		$specifications = get_post_meta( $product->get_id(), '_wc_extra_specifications', true );

		if ( ! empty( $specifications ) ) {
			$tabs['extra_specifications_tab'] = array(
				'title'    => __( 'Extra Specifications', 'woocommerce-extra-specifications' ),
				'priority' => 50,
				'callback' => array( $this, 'render_product_tab_content' ),
			);
		}
		return $tabs;
	}

	/**
	 * Render the content for the custom product tab.
	 */
	public function render_product_tab_content() {
		global $product;
		$specifications = get_post_meta( $product->get_id(), '_wc_extra_specifications', true );

		if ( ! empty( $specifications ) && is_array( $specifications ) ) {
			echo '<h2>' . esc_html__( 'Extra Specifications', 'woocommerce-extra-specifications' ) . '</h2>';
			echo '<table class="shop_attributes shop_attributes_extra_specs">';
			foreach ( $specifications as $spec ) {
				if ( ! empty( $spec['key'] ) && ! empty( $spec['value'] ) ) {
					echo '<tr>';
					echo '<th>' . esc_html( $spec['key'] ) . '</th>';
					echo '<td><p>' . esc_html( $spec['value'] ) . '</p></td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		} else {
			// This message should ideally not be shown if the tab visibility logic in add_product_tab is working correctly.
			echo '<p>' . esc_html__( 'No extra specifications available for this product.', 'woocommerce-extra-specifications' ) . '</p>';
		}
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		global $post_type;

		// Only enqueue on the product edit screen.
		if ( ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) && 'product' === $post_type ) {
			wp_enqueue_script(
				'wc-extra-specifications-admin-js',
				WC_EXTRA_SPECS_URL . 'admin/js/wc-extra-specifications-admin.js',
				array( 'jquery' ),
				'1.0.0', // Version number
				true // Load in footer
			);

			// Localize script with translatable strings.
			$localized_data = array(
				'remove_text'    => esc_html__( 'Remove', 'woocommerce-extra-specifications' ),
				'last_row_alert' => esc_html__( 'You must have at least one specification row.', 'woocommerce-extra-specifications' ),
			);
			wp_localize_script( 'wc-extra-specifications-admin-js', 'wc_extra_specs_admin', $localized_data );
		}
	}
}
