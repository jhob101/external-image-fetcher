<?php
/*
Plugin Name: External Image Fetcher
Description: Fetch images from an external live site if not found locally.
Author: John Hobson
Author URI: https://damselflycreative.com
Version: 1.0.1
Requires PHP: 8.2
*/

require_once __DIR__ . '/vendor/autoload.php';

// Exit if accessed directly
defined('ABSPATH') || exit;

require_once __DIR__ . '/admin/settings.php';
require_once __DIR__ . '/public/functions.php';

function modify_image_urls_init(): void {
    $live_site_url = get_option('live_site_url');
    if ($live_site_url && $live_site_url !== site_url()) {

        // WP image HTML
        add_filter('wp_get_attachment_image', 'dc_modify_image_html', 99, 5);

        // WP image array
        add_filter('wp_get_attachment_image_src', 'dc_modify_image_src', 99, 4);

        // WP
        add_filter('the_content', 'dc_modify_images_in_content', 99, 1);

        // ACF
        add_filter('acf_the_content', 'dc_modify_images_in_content', 99, 1);

    }
}

add_action('init', 'modify_image_urls_init');
