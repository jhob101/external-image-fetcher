<?php

/**
 * Maybe replace image URL in WP image array
 *
 * @param $html
 * @param $post_id
 * @param $post_thumbnail_id
 * @param $size
 * @param $attr
 * @return array
 */
function dc_modify_image_src($image_src, $attachment_id, $size, $icon): array {
    $live_site_url = get_option('live_site_url');

    if ($live_site_url && (!$image_src || !file_exists($image_src[0]))) {
        $image_src[0] = str_ireplace(site_url(), $live_site_url, $image_src[0]);
    }

    return $image_src;
}

/**
 * Maybe replace image URLs in HTML returned from wp_get_attachment_image
 *
 * @param $html
 * @param $post_id
 * @param $post_thumbnail_id
 * @param $size
 * @param $icon
 * @param $attr
 * @return string
 */
function dc_modify_image_html($html, $post_thumbnail_id, $size, $icon, $attr): string {
    $live_site_url = get_option('live_site_url');
    $image_url     = wp_get_attachment_image_url($post_thumbnail_id, $size);

    if ($live_site_url && $image_url && !file_exists(wp_get_original_image_path($post_thumbnail_id))) {
        $replacement_url = str_ireplace(site_url(), $live_site_url, $image_url);

        // Modify the HTML to use the external image URL
        $html = str_ireplace($image_url, $replacement_url, $html);

        // Modify the srcset attribute if it exists
        if (preg_match('/srcset="([^"]+)"/', $html, $matches)) {
            $srcset_urls          = explode(',', $matches[1]);
            $modified_srcset_urls = [];

            foreach ($srcset_urls as $srcset_url) {
                $modified_srcset_urls[] = str_ireplace(site_url(), $live_site_url, trim($srcset_url));
            }

            $modified_srcset = implode(',', $modified_srcset_urls);
            $html            = str_ireplace($matches[0], 'srcset="' . $modified_srcset . '"', $html);
        }
    }

    return $html;
}

/**
 * Maybe replace image URLs with live site URL in body content
 * @param $content
 * @return string
 */
function dc_modify_images_in_content($content): string {
    $live_site_url = get_option('live_site_url');
    if ($live_site_url) {
        $content = preg_replace_callback('/<img([^>]*)src="([^"]+)"([^>]*)>/i', function ($matches) use ($live_site_url) {
            $image_url = $matches[2];;

            if (!is_local($image_url) || exists_at_url($image_url)) { // Don't modify external images or local images that already exist
                return '<img' . $matches[1] . 'src="' . $image_url . '"' . $matches[3] . '>';
            } else { // It's local, so modify it
                $replacement_url = str_ireplace(site_url(), $live_site_url, $image_url);
                $new_img_tag     = '<img' . $matches[1] . 'src="' . $replacement_url . '"' . $matches[3] . '>';

                return $new_img_tag;
            }
        }, $content);

        // run for srcset
        $content = preg_replace_callback('/<img([^>]*)srcset="([^"]+)"([^>]*)>/i', function ($matches) use ($live_site_url) {
            $srcset_urls = explode(',', $matches[2]);

            foreach ($srcset_urls as &$srcset_url) {
                $srcset_url = trim($srcset_url);
                $entry      = explode(' ', $srcset_url);
                if (is_local($entry[0]) && !exists_at_url($entry[0])) {
                    $replacement_url = str_ireplace(site_url(), $live_site_url, $entry[0]);
                    $srcset_url      = implode(' ', [$replacement_url, $entry[1]]);
                }
            }

            return '<img' . $matches[1] . 'srcset="' . implode(',', $srcset_urls) . '"' . $matches[3] . '>';
        }, $content);
    }

    return $content;
}

function exists_at_url($url) {
    // Strip the sized image parts from the URL
    $url = preg_replace('/-[0-9]+x[0-9]+(\.[\w]+)$/', '$1', $url);
    $id  = attachment_url_to_postid($url);
    if ($id) {
        $path = wp_get_original_image_path($id);
        return file_exists($path);
    }
    return false;
}

function is_local($url) {
    return str_starts_with($url, site_url());
}
