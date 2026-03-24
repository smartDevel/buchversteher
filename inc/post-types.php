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

// Rating-Filter: Slug→Rating Mapping im Footer + Client-Filter
add_action('wp_footer', function () {
    if (is_admin()) return;
    global $wpdb;
    $mappings = $wpdb->get_results(
        "SELECT p.post_name AS slug, ROUND(CAST(pm.meta_value AS DECIMAL)) AS rating
         FROM {$wpdb->posts} p
         JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
         WHERE p.post_type = 'book' AND p.post_status = 'publish'
         AND pm.meta_key = 'average_book_rating'
         AND pm.meta_value IS NOT NULL AND pm.meta_value != '' AND pm.meta_value != 'nan'",
        OBJECT_K
    );
    $slugRating = [];
    foreach ($mappings as $slug => $row) {
        $slugRating[$slug] = intval($row->rating);
    }
    if (empty($slugRating)) return;
    ?>
    <script>
    window.__bookSlugRating = <?php echo json_encode($slugRating); ?>;
    </script>
    <?php
});

