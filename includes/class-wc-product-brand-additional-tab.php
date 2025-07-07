<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jigarbhanushali.com
 * @since      1.0.0
 *
 * @package    product-brand-additional-tab-for-woocommerce
 * @subpackage product-brand-additional-tab-for-woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    product-brand-additional-tab-for-woocommerce
 * @subpackage product-brand-additional-tab-for-woocommerce/includes
 * @author     Jigar Bhanushali <sales@jigarbhanushali.com>
 */
class WC_Product_Brand_Additional_Tab {

	/**
	 * The main instance var.
	 *
	 * @var WC_Product_Brand_Additional_Tab The one WC_Product_Brand_Additional_Tab instance.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Init the main instance class.
	 *
	 * @return WC_Product_Brand_Additional_Tab Return the instance class
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof \WC_Product_Brand_Additional_Tab ) ) {
			self::$instance = new \WC_Product_Brand_Additional_Tab();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		add_filter( 'woocommerce_products_general_settings', array( $this, 'add_products_general_settings' ) );
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tabs' ) );
		add_filter( 'wc_pbat_filter_tab_content', array( $this, 'filter_content' ) );
		add_filter( 'woocommerce_structured_data_product', array( $this, 'filter_structured_data_product' ), 99, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add product general settings.
	 *
	 * @param array $settings General settings.
	 * @return array
	 */
	public function add_products_general_settings( $settings ) {
		$product_brand_additional_tab_options = array(
			array(
				'title' => __( 'Brand additional tab', 'product-brand-additional-tab-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'product_brand_additional_tab_options',
			),
			array(
				'title'           => __( 'Enable tab', 'product-brand-additional-tab-for-woocommerce' ),
				'desc'            => __( 'Enable this option to display the product’s brand name in the Additional Information tab.', 'product-brand-additional-tab-for-woocommerce' ),
				'id'              => 'wc_pbat_enable_tab',
				'default'         => 'yes',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'name'    => __( 'Default tab label', 'product-brand-additional-tab-for-woocommerce' ),
				'desc'    => __( 'Enter a default tab label to show for brand info.', 'product-brand-additional-tab-for-woocommerce' ),
				'id'      => 'wc_pbat_default_brand_label',
				'type'    => 'text',
				'default' => esc_attr__( 'Brand Information', 'product-brand-additional-tab-for-woocommerce' ),
			),
			array(
				'name'    => __( 'Priority', 'product-brand-additional-tab-for-woocommerce' ),
				'desc'    => '',
				'id'      => 'wc_pbat_tab_priority',
				'type'    => 'number',
				'default' => 25,
			),
			array(
				'title'           => __( 'Filter brand schema context', 'product-brand-additional-tab-for-woocommerce' ),
				'desc'            => __( 'Add structured data (schema.org) for the product brand to enhance SEO and increase visibility in search results. Includes brand details such as the description, logo, and link within the product schema.', 'product-brand-additional-tab-for-woocommerce' ),
				'id'              => 'wc_pbat_schema_markup',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'product_brand_additional_tab_options',
			),
		);

		$settings = array_merge( $settings, $product_brand_additional_tab_options );
		return $settings;
	}

	/**
	 * Check additional tab option is enabled.
	 *
	 * @return bool
	 */
	public function check_additional_tab_enabled() {
		$enabled = get_option( 'wc_pbat_enable_tab', 'yes' );
		return 'yes' === $enabled;
	}

	/**
	 * Check additional tab option is enabled.
	 *
	 * @return bool
	 */
	public function check_schema_markup_enabled() {
		$enabled = get_option( 'wc_pbat_schema_markup', 'no' );
		return 'yes' === $enabled;
	}

	/**
	 * Filter product additional tab and add brand tab.
	 *
	 * @param array $tabs Additional tab.
	 * @return array
	 */
	public function add_product_tabs( $tabs ) {
		if ( ! $this->check_additional_tab_enabled() ) {
			return $tabs;
		}

		global $product;

		$product_brands = wp_get_post_terms( $product->get_id(), 'product_brand' );

		if ( empty( $product_brands ) || is_wp_error( $product_brands ) ) {
			return $tabs;
		}

		$tabs['brand_tab'] = array(
			'title'    => apply_filters( 'wc_pbat_tab_label_text', get_option( 'wc_pbat_default_brand_label', __( 'Brand Information', 'product-brand-additional-tab-for-woocommerce' ) ) ),
			'priority' => get_option( 'wc_pbat_tab_priority', 25 ),
			'callback' => function () use ( $product_brands ) {
				$this->render_tab_content( $product_brands );
			},
		);
		return $tabs;
	}

	/**
	 * Render tab content.
	 *
	 * @param object $product_brands product brands.
	 */
	public function render_tab_content( $product_brands ) {
		global $product;
		wc_get_template(
			'single-product/tabs/brand-information.php',
			array(
				'product' => $product,
				'brands'  => $product_brands,
			),
			'',
			WC_PBAT_ABSPATH . 'templates/'
		);
	}

	/**
	 * Filter the tab content.
	 *
	 * @param string $content Content for the current tab.
	 * @return string Tab content.
	 */
	public function filter_content( $content ) {
		$content = function_exists( 'capital_P_dangit' ) ? capital_P_dangit( $content ) : $content;
		$content = function_exists( 'wptexturize' ) ? wptexturize( $content ) : $content;
		$content = function_exists( 'convert_smilies' ) ? convert_smilies( $content ) : $content;
		$content = function_exists( 'wpautop' ) ? wpautop( $content ) : $content;
		$content = function_exists( 'shortcode_unautop' ) ? shortcode_unautop( $content ) : $content;
		$content = function_exists( 'prepend_attachment' ) ? prepend_attachment( $content ) : $content;
		$content = function_exists( 'wp_filter_content_tags' ) ? wp_filter_content_tags( $content ) : $content;
		$content = function_exists( 'do_shortcode' ) ? do_shortcode( $content ) : $content;

		if ( class_exists( 'WP_Embed' ) ) {
			$embed   = new \WP_Embed();
			$content = method_exists( $embed, 'autoembed' ) ? $embed->autoembed( $content ) : $content;
		}

		return $content;
	}

	/**
	 * Filter the tab content.
	 *
	 * @param string $brand_data Brand data.
	 * @return string Tab content.
	 */
	public static function get_content( $brand_data ) {
		if ( isset( $brand_data->description ) ) {
			$use_the_content_filter = apply_filters( 'wc_pbat_use_the_content_filter', true );

			if ( true === $use_the_content_filter ) {
				$content = apply_filters( 'the_content', $brand_data->description );
			} else {
				$content = apply_filters( 'wc_pbat_filter_tab_content', $brand_data->description );
			}
			return $content;
		}
	}

	/**
	 * Add frontend scripts/styles.
	 */
	public function enqueue_scripts() {
		$css = '.wc-pbat-brand-info {
			background: #f9f9f9;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 0 8px rgba(0,0,0,0.05);
			margin: 0 auto;
		}
		.wc-pbat-brand-info h2 {
			text-align: center;
			margin-bottom: 20px;
		}
		.wc-pbat-brand-logo {
			text-align: center;
			margin-bottom: 20px;
		}';

		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $css );
		$css = trim( $css );

		wp_add_inline_style(
			'woocommerce-layout',
			$css
		);
	}

	/**
	 * Filter WC structured data context.
	 *
	 * @param array  $markup Structured data.
	 * @param object $product Product object.
	 * @return array
	 */
	public function filter_structured_data_product( $markup, $product ) {
		if ( ! $this->check_schema_markup_enabled() ) {
			return $markup;
		}

		$product_brands = wp_get_post_terms( $product->get_id(), 'product_brand' );

		if ( empty( $product_brands ) || is_wp_error( $product_brands ) ) {
			return $markup;
		}
		$product_brands                 = reset( $product_brands );
		$thumbnail_id                   = get_term_meta( $product_brands->term_id, 'thumbnail_id', true );
		$markup['brand']['description'] = wp_strip_all_tags( $product_brands->description );
		if ( $thumbnail_id ) {
			$markup['brand']['logo'] = wp_get_attachment_url( $thumbnail_id );
		}
		$markup['brand']['url'] = esc_url( get_term_link( $product_brands->term_id, 'product_brand' ) );
		return $markup;
	}
}
