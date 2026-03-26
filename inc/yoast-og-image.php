<?php
/**
 * og:image für Buchseiten und Blog-Rezensionen
 * Unabhängig von Yoast — gibt direkt im <head> aus
 */

if (!defined('ABSPATH')) exit;

add_action('wp_head', function () {
    $image_url = '';
    $title_override = '';
    
    // FALL 1: Buchseite mit ?book_id= Parameter
    if (isset($_GET['book_id'])) {
        $book_id = intval($_GET['book_id']);
        if ($book_id && get_post_type($book_id) === 'book') {
            $image_url = get_the_post_thumbnail_url($book_id, 'large');
            $title_override = get_the_title($book_id) . ' - Buchversteher.de';
        }
    }
    
    // FALL 2: Blog-Rezension mit _linked_book_id
    if (!$image_url && is_single()) {
        global $post;
        if ($post) {
            $linked_book_id = get_post_meta($post->ID, '_linked_book_id', true);
            if ($linked_book_id) {
                $image_url = get_the_post_thumbnail_url($linked_book_id, 'large');
            }
            
            // FALL 3: Erstes Bild im Content
            if (!$image_url) {
                $content = $post->post_content;
                if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
                    $image_url = $matches[1];
                }
            }
        }
    }
    
    if (!$image_url) return;
    
    $image_url = esc_url($image_url);
    
    // Nur ausgeben wenn Yoast KEIN og:image gesetzt hat
    // (d.h. kein Featured Image vorhanden)
    global $wpseo_og_image_set;
    $wpseo_og_image_set = false;
    
    // og:image Tags
    echo '<meta property="og:image" content="' . $image_url . '" />' . "\n";
    echo '<meta property="og:image:url" content="' . $image_url . '" />' . "\n";
    
    $attachment_id = attachment_url_to_postid($image_url);
    if ($attachment_id) {
        $meta = wp_get_attachment_metadata($attachment_id);
        if ($meta && isset($meta['width'], $meta['height'])) {
            echo '<meta property="og:image:width" content="' . intval($meta['width']) . '" />' . "\n";
            echo '<meta property="og:image:height" content="' . intval($meta['height']) . '" />' . "\n";
        }
    }
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:image" content="' . $image_url . '" />' . "\n";
    
    // og:title überschreiben (nur Buchseiten)
    if ($title_override) {
        echo '<meta property="og:title" content="' . esc_attr($title_override) . '" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title_override) . '" />' . "\n";
    }
}, 2);
