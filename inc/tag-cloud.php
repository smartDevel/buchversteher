<?php
/**
 * Schlagwort-Wolke verbessern
 * 
 * - Begrenzte Anzahl (Top 30)
 * - Klickbares Ein-/Ausklappen (mehr/weniger)
 * - Kompaktere Darstellung
 */

if (!defined('ABSPATH')) exit;

// Tag-Cloud-Argumente begrenzen
add_filter('widget_tag_cloud_args', function ($args) {
    $args['number']  = 100;   // Maximal 100 Tags laden
    $args['orderby'] = 'count';
    $args['order']   = 'DESC';
    $args['smallest'] = 12;
    $args['largest']  = 18;
    $args['unit']     = 'px';
    return $args;
});

// CSS + JS für die Tag-Cloud
add_action('wp_footer', function () {
    ?>
    <style>
    /* Tag-Cloud Container */
    .widget_tag_cloud .tagcloud {
        position: relative;
        max-height: 120px;
        overflow: hidden;
        transition: max-height 0.4s ease;
        padding-bottom: 40px;
    }
    .widget_tag_cloud .tagcloud.expanded {
        max-height: none;
    }
    /* Fade-Out-Gradient am unteren Rand */
    .widget_tag_cloud .tagcloud::after {
        content: '';
        position: absolute;
        bottom: 35px;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(transparent, #fff);
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .widget_tag_cloud .tagcloud.expanded::after {
        opacity: 0;
    }
    /* Tags */
    .widget_tag_cloud .tagcloud a {
        display: inline-block;
        margin: 3px 4px;
        padding: 4px 10px;
        background: #f0f0f0;
        border-radius: 15px;
        font-size: 13px !important;
        color: #444;
        text-decoration: none;
        transition: all 0.2s;
    }
    .widget_tag_cloud .tagcloud a:hover {
        background: #39b152;
        color: #fff;
    }
    /* Toggle-Button */
    .tag-cloud-toggle {
        display: block;
        width: 100%;
        text-align: center;
        background: none;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 8px;
        margin-top: 10px;
        color: #39b152;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tag-cloud-toggle:hover {
        background: #39b152;
        color: #fff;
        border-color: #39b152;
    }
    </style>
    <script>
    jQuery(document).ready(function($) {
        var $tagcloud = $('.widget_tag_cloud .tagcloud');
        if (!$tagcloud.length) return;

        // Toggle-Button erstellen
        var $toggle = $('<button type="button" class="tag-cloud-toggle">▼ Alle Schlagwörter anzeigen</button>');
        $tagcloud.after($toggle);

        var expanded = false;
        $toggle.on('click', function() {
            expanded = !expanded;
            if (expanded) {
                $tagcloud.addClass('expanded');
                $toggle.text('▲ Weniger anzeigen');
            } else {
                $tagcloud.removeClass('expanded');
                $toggle.text('▼ Alle Schlagwörter anzeigen');
                // Zurück nach oben scrollen
                $('html, body').animate({
                    scrollTop: $tagcloud.offset().top - 100
                }, 300);
            }
        });
    });
    </script>
    <?php
});
