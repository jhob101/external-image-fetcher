<?php
// Add a menu item to the admin menu for the plugin settings
function external_image_fetcher_menu(): void {
    add_options_page('External Image Fetcher Settings', 'External Image Fetcher', 'manage_options', 'external-image-fetcher-settings', 'external_image_fetcher_settings_page');
}

add_action('admin_menu', 'external_image_fetcher_menu');

// Function to display the settings page
function external_image_fetcher_settings_page(): void {
    ?>
    <div class="wrap">
        <h2>External Image Fetcher Settings</h2>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" enctype="multipart/form-data" id="external-image-fetcher-settings-form" class="external-image-fetcher-settings-form" data-settings-url="options.php">
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
function external_image_fetcher_register_settings(): void {
    register_setting('external_image_fetcher_settings', 'live_site_url');
    add_settings_section('external_image_fetcher_section', 'External Image Fetcher Settings', 'external_image_fetcher_section_cb', 'external_image_fetcher_settings');
    add_settings_field('live_site_url', 'Live Site URL', 'live_site_url_field_cb', 'external_image_fetcher_settings', 'external_image_fetcher_section');
}

add_action('admin_init', 'external_image_fetcher_register_settings');

// Callback for settings section
function external_image_fetcher_section_cb(): void {
    echo 'Enter the URL of the live site from which to fetch images if not found locally.';
}

// Callback for live site URL field
function live_site_url_field_cb(): void {
    $live_site_url = get_option('live_site_url');
    echo '<input type="text" name="live_site_url" value="' . esc_attr($live_site_url) . '" />';
}
