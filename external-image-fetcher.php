<?php
/*
Plugin Name: External Image Fetcher
Description: Fetch images from an external live site if not found locally.
Author: John Hobson
Author URI: https://damselflycreative.com
Version: 1.0
*/

// Add a menu item to the admin menu for the plugin settings
function external_image_fetcher_menu()
{
    add_options_page('External Image Fetcher Settings', 'External Image Fetcher', 'manage_options', 'external-image-fetcher-settings', 'external_image_fetcher_settings_page');
}

add_action('admin_menu', 'external_image_fetcher_menu');

// Function to display the settings page
function external_image_fetcher_settings_page()
{
    ?>
    <div class="wrap">
        <h2>External Image Fetcher Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('external_image_fetcher_settings');
            do_settings_sections('external_image_fetcher_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register plugin settings
function external_image_fetcher_register_settings()
{
    register_setting('external_image_fetcher_settings', 'live_site_url');
    add_settings_section('external_image_fetcher_section', 'External Image Fetcher Settings', 'external_image_fetcher_section_cb', 'external_image_fetcher_settings');
    add_settings_field('live_site_url', 'Live Site URL', 'live_site_url_field_cb', 'external_image_fetcher_settings', 'external_image_fetcher_section');
}

add_action('admin_init', 'external_image_fetcher_register_settings');

// Callback for settings section
function external_image_fetcher_section_cb()
{
    echo 'Enter the URL of the live site from which to fetch images if not found locally.';
}

// Callback for live site URL field
function live_site_url_field_cb()
{
    $live_site_url = get_option('live_site_url');
    echo '<input type="text" name="live_site_url" value="' . esc_attr($live_site_url) . '" />';
}

// Filter to modify image URLs
function dc_modify_image_urls($html, $post_id, $post_thumbnail_id, $size, $attr)
{
    $live_site_url = get_option('live_site_url');
    $image_src = wp_get_attachment_image_src($post_thumbnail_id, $size);

    if ($live_site_url && (!$image_src || !file_exists($image_src[0]))) {
        $image_url = wp_get_attachment_image_url($post_thumbnail_id, $size);
        if ($image_url) {
            $replacement_url = str_replace(site_url(), $live_site_url, $image_url);
            // Modify the HTML to use the external image URL
            $html = str_replace($image_src[0], $replacement_url, $html);
        }
    }

    return $html;
}

$live_site_url = get_option('live_site_url');
if ($live_site_url && $live_site_url !== site_url()) {
    add_filter('post_thumbnail_html', 'dc_modify_image_urls', 10, 5);
}
