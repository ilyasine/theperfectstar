<?php

/**
 * Class WC_Product_Courses
 */
class WC_Product_Courses extends \WC_Product {

	/**
	 * Get the product if ID is passed, otherwise the product is new and empty.
	 * This class should NOT be instantiated, but the wc_get_product() function
	 * should be used. It is possible, but the wc_get_product() is preferred.
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	/**
	 * Get internal type. Should return string and *should be overridden* by child classes.
	 *
	 * The product_type property is deprecated but is used here for BW compat with child classes which may be defining product_type and not have a get_type method.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function get_type() {
		return 'courses';
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = __( 'Add to cart', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = add_query_arg( 'add-to-cart', $this->get_id(), wc_get_cart_url() );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get virtual.
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @since 3.0.0
	 *
	 */
	public function is_virtual( $context = 'view' ) {
		return apply_filters( 'ulgm_courses_is_virtual', $this->get_prop( 'virtual', $context ), $this );
	}

	/**
	 * Get virtual.
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @since 3.0.0
	 *
	 */
	public function is_downloadable( $context = 'view' ) {
		return apply_filters( 'ulgm_courses_is_downloadable', $this->get_prop( 'downloadable', $context ), $this );
	}

	/**
	 * Return if should be sold individually.
	 *
	 * @param string $context
	 *
	 * @return boolean
	 * @since 3.0.0
	 */
	public function is_sold_individually( $context = 'view' ) {
		return apply_filters( 'ulgm_courses_is_sold_individually', true, $this );
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'ulgm_courses_is_purchasable', true, $this );
	}

	/**
	 * Returns false if the product is taxable.
	 *
	 * @return bool
	 */
	public function is_taxable() {
		return apply_filters( 'ulgm_courses_is_taxable', 'taxable', $this );
	}
}
