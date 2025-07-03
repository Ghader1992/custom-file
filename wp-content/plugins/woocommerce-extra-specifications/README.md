# WooCommerce Extra Specifications

**Contributors:** Jules
**Requires at least:** 5.0
**Tested up to:** 6.4 (Please update with the latest WordPress version you test with)
**Requires PHP:** 7.2
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that enhances WooCommerce products by adding a dedicated "Extra Specifications" tab and an easy-to-use metabox in the product admin area for managing these specifications.

## Description

The WooCommerce Extra Specifications plugin allows store owners to add detailed, custom key/value specifications to their products. This information is then displayed neatly in a separate tab on the single product page, providing customers with more comprehensive product details.

The plugin is designed to be lightweight and integrates seamlessly with the WooCommerce interface.

## Functionality Details

### 1. Extra Specifications Metabox (Product Edit Page)

When you edit a WooCommerce product, you will find a new metabox titled "Extra Specifications". This interface allows you to:

*   **Add Specifications**: Click the "Add Specification" button to add a new row for a key/value pair.
*   **Enter Data**: For each specification, you can enter a "Key" (e.g., "Material", "Dimensions", "Made in") and a corresponding "Value".
*   **Remove Specifications**: Each row has a "Remove" button to delete a specific key/value pair.
*   **Dynamic Rows**: You can add or remove as many specification pairs as needed. The interface ensures at least one row is always present for ease of use.
*   **Data Saving**: Specifications are saved when you "Publish" or "Update" the product. The data is stored securely as post metadata.

### 2. "Extra Specifications" Tab (Frontend Product Page)

On the frontend, if a product has any specifications defined, a new tab titled "Extra Specifications" will automatically appear on the single product page.

*   **Display**: This tab lists all the saved key/value pairs in a clear, table format, similar to WooCommerce's default "Additional Information" tab.
*   **Conditional Visibility**: The "Extra Specifications" tab is only displayed if there are actual specifications saved for the product. This keeps the product page clean if no extra details are provided.

## Plugin Structure

The plugin is organized as follows:

*   `woocommerce-extra-specifications/` (Root plugin directory)
    *   `woocommerce-extra-specifications.php`: The main plugin file. It handles the plugin header, defines constants, and initializes the core plugin class.
    *   `README.md`: This file.
    *   `includes/`: This directory contains the core PHP class.
        *   `class-wc-extra-specifications.php`: This class encapsulates all the plugin's functionality, including metabox creation, data saving, tab registration, content rendering, and script enqueuing.
    *   `admin/`: This directory contains admin-specific assets.
        *   `js/`: Contains JavaScript files for the admin area.
            *   `wc-extra-specifications-admin.js`: Handles the dynamic behavior (add/remove rows) of the "Extra Specifications" metabox.

## Key Hooks Used

The plugin utilizes several standard WordPress and WooCommerce hooks:

*   **WordPress Hooks**:
    *   `plugins_loaded`: To initialize the main plugin class.
    *   `add_meta_boxes`: To add the custom metabox to the product edit screen.
    *   `save_post_product`: To save the metabox data when a product is saved (specifically for the 'product' post type).
    *   `admin_enqueue_scripts`: To load the JavaScript for the admin metabox.
*   **WooCommerce Hooks**:
    *   `woocommerce_product_tabs`: To add the custom "Extra Specifications" tab to the product page.

## How Data is Stored

All key/value specifications are stored in the product's post meta using the meta key: `_wc_extra_specifications`. The data is stored as a serialized array of key/value pairs.

## Installation

1.  Download the `woocommerce-extra-specifications.zip` file (if applicable) or clone the repository.
2.  Upload the entire `woocommerce-extra-specifications` folder to the `/wp-content/plugins/` directory on your WordPress installation.
3.  Activate the plugin through the 'Plugins' menu in WordPress.
4.  Once activated, the "Extra Specifications" metabox will be available on product edit pages, and the tab will appear on the frontend for products with specifications.

## Future Enhancements (Potential Ideas)

*   Support for different field types in the metabox (e.g., textarea, select dropdowns).
*   Option to reorder specifications in the metabox.
*   Bulk editing capabilities for specifications.
*   Shortcode to display specifications anywhere.
*   Styling options for the frontend tab content.
*   Import/Export functionality for specifications.

---

Thank you for using WooCommerce Extra Specifications!
