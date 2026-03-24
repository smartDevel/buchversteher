<?php
/**
 * Post Type Redirects
 * 
 * - Book-Links auf /buchseite/?book_id=ID umleiten
 */

if (!defined('ABSPATH')) exit;

// Book-Links umschreiben
add_filter('post_type_link', function ($post_link, $post) {
    if ($post->post_type === 'book') {
        return home_url('/buchseite/?book_id=' . $post->ID);
    }
    return $post_link;
}, 10, 2);

// Singular Book auf Buchseite umleiten
add_action('template_redirect', function () {
    if (is_singular('book')) {
        $book_id = get_queried_object_id();
        wp_redirect(home_url('/buchseite/?book_id=' . $book_id), 301);
        exit;
    }
});

// Rating-Filter: Rating direkt in Buchkarten-HTML einbetten
add_action('template_redirect', function () {
    if (is_admin()) return;
    ob_start(function ($html) {
        if (strpos($html, 'rswpbs-book-loop-content-wrapper') === false) return $html;

        /* Ratings laden */
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'average_book_rating'",
            OBJECT_K
        );
        $ratings = [];
        foreach ($rows as $pid => $row) {
            if ($row->meta_value !== '' && $row->meta_value !== 'nan') {
                $ratings[$pid] = round(floatval($row->meta_value));
            }
        }

        /* Jede Buchkarte mit data-book-rating versehen */
        $html = preg_replace_callback(
            '/<div class="rswpbs-book-loop-content-wrapper">(.*?)<\/div>\s*<div class="rswpbs-book-buttons-wrapper/s',
            function ($matches) use ($ratings) {
                $content = $matches[1];
                /* Book ID aus Link extrahieren */
                if (preg_match('/book_id=(\d+)/', $content, $idMatch)) {
                    $bookId = $idMatch[1];
                    if (isset($ratings[$bookId])) {
                        return '<div class="rswpbs-book-loop-content-wrapper" data-book-rating="' . $ratings[$bookId] . '">' . $matches[1] . '</div><div class="rswpbs-book-buttons-wrapper';
                    }
                }
                return $matches[0];
            },
            $html
        );

        return $html;
    });
});

