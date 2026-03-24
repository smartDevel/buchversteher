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

// Rating-Filter für Bücher (alle Queries, nicht nur main query)
add_filter('posts_clauses', function ($clauses, $query) {
    if (is_admin()) return $clauses;
    if (empty($_GET['rating']) || $_GET['rating'] === 'all') return $clauses;
    $rating = intval($_GET['rating']);
    if ($rating < 1 || $rating > 5) return $clauses;

    global $wpdb;

    $clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS rating_filter ON (
        {$wpdb->posts}.ID = rating_filter.post_id
        AND rating_filter.meta_key = 'average_book_rating'
        AND rating_filter.meta_value = '{$rating}'
    )";

    $clauses['groupby'] = "{$wpdb->posts}.ID";

    return $clauses;
}, 10, 2);

