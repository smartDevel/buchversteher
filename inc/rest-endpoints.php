<?php
/**
 * Custom REST API Endpoints
 * 
 * - /buchversteher/v1/set-review-meta — Review Meta setzen
 * - /buchversteher/v1/set-linked-book — Buch-Verlinkung setzen
 */

if (!defined('ABSPATH')) exit;

// Review Meta setzen (Plugin-Keys: _rswpbs_*)
add_action('rest_api_init', function () {
    register_rest_route('buchversteher/v1', '/set-review-meta', [
        'methods' => 'POST',
        'callback' => function ($request) {
            $review_id = intval($request->get_param('review_id'));
            if (!$review_id || get_post_type($review_id) !== 'book_reviews') {
                return new WP_Error('invalid_review', 'Invalid review ID', ['status' => 400]);
            }
            $json = $request->get_json_params();
            unset($json['review_id']);
            $results = [];
            if (!empty($json['reviewed_book'])) {
                $results['_rswpbs_reviewed_book'] = update_post_meta($review_id, '_rswpbs_reviewed_book', intval($json['reviewed_book']));
            }
            if (!empty($json['rating'])) {
                $results['_rswpbs_rating'] = update_post_meta($review_id, '_rswpbs_rating', intval($json['rating']));
            }
            if (!empty($json['reviewer_name'])) {
                $results['_rswpbs_reviewer_name'] = update_post_meta($review_id, '_rswpbs_reviewer_name', sanitize_text_field($json['reviewer_name']));
            }
            if (!empty($json['reviewer_email'])) {
                $results['_rswpbs_reviewer_email'] = update_post_meta($review_id, '_rswpbs_reviewer_email', sanitize_email($json['reviewer_email']));
            }
            return ['success' => true, 'review_id' => $review_id, 'set' => $results];
        },
        'permission_callback' => '__return_true',
    ]);
});

// Buch-Verlinkung für Rezensionen setzen
add_action('rest_api_init', function () {
    register_rest_route('buchversteher/v1', '/set-linked-book', [
        'methods' => 'POST',
        'callback' => function ($request) {
            $post_id = intval($request->get_param('post_id'));
            $book_id = intval($request->get_param('book_id'));
            if (!$post_id || !$book_id) {
                return new WP_Error('invalid', 'post_id and book_id required', ['status' => 400]);
            }
            update_post_meta($post_id, '_linked_book_id', $book_id);
            return ['success' => true, 'post_id' => $post_id, 'book_id' => $book_id];
        },
        'permission_callback' => '__return_true',
    ]);
});

