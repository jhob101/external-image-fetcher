<?php

/**
 * Check if image exists locally, if not fetch from remote
 *
 * @param $html
 * @param $post_id
 * @param $post_thumbnail_id
 * @param $size
 * @param $attr
 * @return string
 */
function dc_modify_image_urls($html, $post_id, $post_thumbnail_id, $size, $attr): string {
    $live_site_url = get_option('live_site_url');
    $image_src     = wp_get_attachment_image_src($post_thumbnail_id, $size);

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
