/* This is your custom Javascript */

jQuery(document).ready(function($) {
    if ($('.elementor').length > 0) {
        $('.elementor').remove(); // Remove Elementor content
        $('.edit-post-header__settings').remove(); // Remove Edit with Elementor button
    }
});
