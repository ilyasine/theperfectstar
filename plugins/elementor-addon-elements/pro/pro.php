<?php

namespace WTS_EAE\Pro;

class Pro {
    public static $instance;

    public static $schemas;

    public static function get_instance() {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // add action to wp_footer to add schema to footer
        add_action(
            'wp_enqueue_scripts',
            array($this, 'enqueue_pro_scripts'),
            10,
            1
        );
        add_action( 'wp_footer', array($this, 'render_schema') );
        add_filter(
            'eae_localize_data',
            array($this, 'add_pro_localization'),
            10,
            1
        );
    }

    public function enqueue_pro_scripts() {
        // Floating Images
        wp_register_script(
            'eae-keyframes',
            EAE_URL . 'pro/assets/lib/keyframes/jquery.keyframes' . EAE_SCRIPT_SUFFIX . '.js',
            ['jquery'],
            '1.0.8',
            true
        );
        //Lightgallery CSS
        wp_register_style(
            'lightgallery-css',
            EAE_URL . 'pro/assets/lib/lightgallery/css/lightgallery-bundle' . EAE_SCRIPT_SUFFIX . '.css',
            [],
            EAE_VERSION
        );
        //wp_enqueue_style( 'lightgallery-css', EAE_URL . 'pro/assets/lib/lightgallery/css/lightgallery-bundle' . EAE_SCRIPT_SUFFIX . '.css', [], EAE_VERSION );
        // LightGallery JS
        wp_register_script(
            'lightgallery-js',
            EAE_URL . 'pro/assets/lib/lightgallery/lightgallery' . EAE_SCRIPT_SUFFIX . '.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-fullscreen-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/fullscreen/lg-fullscreen.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-hash-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/hash/lg-hash.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-rotate-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/rotate/lg-rotate.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-share-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/share/lg-share.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-video-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/video/lg-video.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-zoom-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/zoom/lg-zoom.min.js',
            [],
            '2.7.1',
            true
        );
        wp_register_script(
            'lg-thumbnail-js',
            EAE_URL . 'pro/assets/lib/lightgallery/plugins/thumbnail/lg-thumbnail.min.js',
            [],
            '2.7.1',
            true
        );
        // For Vimeo video-Video Box Widget
        wp_register_script(
            'eae-player-js',
            EAE_URL . 'pro/assets/lib/lightgallery/player.min.js',
            [],
            '2.19.0',
            true
        );
        // For selfhosted video-Video Box Widget
        wp_register_script(
            'eae-video-js',
            EAE_URL . 'pro/assets/lib/lightgallery/video.min.js',
            [],
            '8.3.0',
            true
        );
        wp_register_style(
            'eae-video-css',
            EAE_URL . 'pro/assets/lib/lightgallery/css/video' . '.css',
            [],
            EAE_VERSION
        );
        wp_register_script(
            'eae-popper',
            EAE_URL . 'pro/assets/lib/tippy/popper.js',
            [],
            '2.11.8',
            false
        );
        wp_register_script(
            'eae-tippy',
            EAE_URL . 'pro/assets/lib/tippy/tippy.js',
            [],
            '1.1',
            false
        );
        wp_register_style(
            'eae-tippy-css',
            EAE_URL . 'pro/assets/lib/tippy/tippy.css',
            [],
            '1.1',
            false
        );
    }

    public function add_pro_localization( $localize_data ) {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $checkout_url = wc_get_checkout_url();
            $cart_url = wc_get_cart_url();
        } else {
            $checkout_url = '';
            $cart_url = '';
        }
        $localize_data['checkout_url'] = $checkout_url;
        $localize_data['cart_url'] = $cart_url;
        return $localize_data;
    }

    function render_schema() {
        if ( self::$schemas == null ) {
            return;
        }
        // loop through $schema and add schema tag to footer
        foreach ( self::$schemas as $schema ) {
            $video_schema = '<script type="application/ld+json" id="eae-video-schema" >' . wp_json_encode( $schema ) . '</script>';
            echo $video_schema;
        }
    }

}

if ( !function_exists( 'wpv_eae' ) ) {
    // Create a helper function for easy SDK access.
    function wpv_eae() {
        global $wpv_eae;
        if ( !isset( $wpv_eae ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wpv_eae = fs_dynamic_init( array(
                'id'             => '4599',
                'slug'           => 'addon-elements-for-elementor-page-builder',
                'premium_slug'   => 'elementor-addon-elements',
                'type'           => 'plugin',
                'public_key'     => 'pk_086ef046431438c9a172bb55fde28',
                'is_premium'     => true,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                    'slug'    => 'eae-settings',
                    'contact' => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $wpv_eae;
    }

    // Init Freemius.
    wpv_eae();
    // Signal that SDK was initiated.
    do_action( 'wpv_eae_loaded' );
}
Pro::get_instance();