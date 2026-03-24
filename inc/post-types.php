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

// Rating-Filter für Bücher (via the_posts — funktioniert mit Plugin-Queries)
add_filter('the_posts', function ($posts, $query) {
    if (is_admin()) return $posts;
    if (empty($_GET['rating']) || $_GET['rating'] === 'all') return $posts;
    $rating = intval($_GET['rating']);
    if ($rating < 1 || $rating > 5) return $posts;

    $filtered = [];
    foreach ($posts as $post) {
        $avg = floatval(get_post_meta($post->ID, 'average_book_rating', true));
        if (round($avg) === $rating) {
            $filtered[] = $post;
        }
    }
    return $filtered;
}, 10, 2);

