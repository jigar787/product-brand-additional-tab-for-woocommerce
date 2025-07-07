=== Product Brand Additional Tab for WooCommerce ===
Contributors: jigar-bhanushali
Tags: woocommerce tabs, brand details, brand schema, additional tab, product brand tabs for woocommerce
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Displays WooCommerce native product brand in the Additional Information tab and adds schema.org brand markup (description, logo, link) for better SEO.

== Description ==

This plugin enhances WooCommerce product detail page by:

* Displaying the assigned product brand inside the "Additional Information" tab.
* Outputting structured data (JSON-LD) for the product's brand using schema.org markup.
* Improving brand visibility in search results by adding brand name, description, logo, and link to the product schema.

It works out of the box with WooCommerce's `product_brand` taxonomy or any compatible brand plugin using the same taxonomy.

== Compatibility ==

This plugin is compatible with:

* WooCommerce v9.4 or later

== Developer Guide ==

=== 🧩 Template Override Support ===

To override the brand tab layout, copy the default template into your theme:

**Destination Path:**
yourtheme/woocommerce/single-product/tabs/brand-information.php

**Original Plugin Template:**
templates/single-product/tabs/brand-information.php

You can customize how the brand name, logo, and description are displayed in the additional tab.

=== 🔄 Custom Action Hooks ===

Use these hooks to inject content before or after the brand tab output:

Before the brand content block.

`do_action( 'wc_pbat_before_brand_content', $brand );`

After the brand content block.

`do_action( 'wc_pbat_after_brand_content', $brand );`


=== 🔧 Helper Method ===

Render clean and filtered brand description using:

`WC_Product_Brand_Additional_Tab::get_content( $term );`

This ensures the description is properly formatted and filtered.

=== 🔃 Filter Hooks ===

Controls whether the plugin should use `the_content` filter to format the brand description.

`add_filter( 'wc_pbat_use_the_content_filter', '__return_false' )`

**Default:** `true`


Fires only when `the_content` filter is skipped. Allows custom formatting of brand descriptions in the tab.

`add_filter( 'wc_pbat_filter_tab_content', function( $description ) {
return wpautop( esc_html( $description ) );
});`


These filters give developers full control over how brand descriptions are rendered in the tab.

== Installation ==

1. Upload the `product-brand-additional-tab-for-woocommerce` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin from the WordPress Plugins screen.
3. Make sure brands are assigned using the `product_brand` taxonomy.
4. Optional: Configure schema output settings from WooCommerce → Settings → Products (if available).

== Frequently Asked Questions ==

= Does it support custom brand plugins? =
Yes, if they use the `product_brand` taxonomy.

= Can I disable the schema markup? =
Yes, you can toggle schema output from the WooCommerce product settings.

== Upgrade Notice ==

== Screenshots == 

== Changelog ==

= 1.0 =
* Initial Release.