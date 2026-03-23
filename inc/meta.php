<?php
/**
 * Meta Fields & REST API Registration
 * 
 * - Book REST Fields (original_book_name, etc.)
 * - average_book_rating REST Field
 * - save_post_book Hook
 */

if (!defined('ABSPATH')) exit;

// Book REST Fields (alle _rsbs_ Meta-Felder)
add_action('rest_api_init', function () {
    $field_map = [
        'original_book_name' => '_rsbs_original_book_name',
        'original_book_url' => '_rsbs_original_book_url',
        'book_name' => '_rsbs_book_name',
        'book_publish_date' => '_rsbs_book_publish_date',
        'book_publisher_name' => '_rsbs_book_publisher_name',
        'book_language' => '_rsbs_book_language',
        'book_format' => '_rsbs_book_format',
        'book_pages' => '_rsbs_book_pages',
        'book_asin' => '_rsbs_book_asin',
        'book_isbn' => '_rsbs_book_isbn',
        'book_isbn_10' => '_rsbs_book_isbn_10',
        'book_isbn_13' => '_rsbs_book_isbn_13',
        'book_dimension' => '_rsbs_book_dimension',
        'print_length' => '_rsbs_print_length',
        'book_availability_status' => '_rsbs_book_availability_status',
        'enhanced_typesetting' => '_rsbs_enhanced_typesetting',
        'book_text_to_speech' => '_rsbs_book_text_to_speech',
        'screen_reader' => '_rsbs_screen_reader',
        'x_ray' => '_rsbs_x_ray',
        'word_wise' => '_rsbs_word_wise',        
        'book_price' => '_rsbs_book_price',
        'buy_btn_text' => '_rsbs_buy_btn_text',
        'buy_btn_link' => '_rsbs_buy_btn_link',
        'book_country' => '_rsbs_book_country',
        'short_description' => '_rsbs_short_description',
    ];

    foreach ($field_map as $key => $meta_key) {
        register_rest_field('book', $key, [
            'get_callback' => function ($post) use ($meta_key) {
                return get_post_meta($post['id'], $meta_key, true);
            },
            'update_callback' => function ($value, $post) use ($meta_key) {
                delete_post_meta($post->ID, $meta_key);
                return update_post_meta($post->ID, $meta_key, sanitize_text_field($value));
            },
            'schema' => ['type' => 'string', 'description' => $key],
        ]);
    }
});

// Average Book Rating REST Field
add_action('rest_api_init', function () {
    register_rest_field('book', 'average_book_rating', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], 'average_book_rating', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, 'average_book_rating', sanitize_text_field($value));
        },
        'schema' => ['type' => 'string', 'description' => 'Average Book Rating'],
    ]);
});

// Average Book Rating beim Speichern sichern
add_action('save_post_book', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['average_book_rating'])) {
        update_post_meta($post_id, 'average_book_rating', sanitize_text_field($_POST['average_book_rating']));
    }
});

