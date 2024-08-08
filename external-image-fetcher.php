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
        add_filter('post_thumbnail_html', 'dc_modify_image_urls', 10, 5);
    }
}

add_action('init', 'modify_image_urls_init');
