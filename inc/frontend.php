<?php
/**
 * Frontend JavaScript & CSS
 * 
 * - Suchformular Toggle
 * - Select2 Labels
 * - Auto-Submit
 * - Review Form Toggle
 */

if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () { ?>
<style>
.rswpbs-books-search-form input[type="submit"] {
    background: #39b152;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.rswpbs-books-search-form input[type="submit"]:hover {
    background: #2d8a41;
}
.search-form-toggle,
.review-form-toggle {
    display: inline-block;
    background: #39b152;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 15px;
    transition: background 0.3s;
}
.search-form-toggle:hover,
.review-form-toggle:hover {
    background: #2d8a41;
}
</style>
<script>
jQuery(document).ready(function($) {

    /* Toggle Suchmaske */
    var $formArea = $('.rswpbs-advanced-search-form-area');
    if ($formArea.length) {
        var isOpen = localStorage.getItem('searchFormOpen') === 'true';
        if (isOpen) { $formArea.show(); } else { $formArea.hide(); }
        var btnText = $formArea.is(':visible') ? '🔍 Suche ausblenden' : '🔍 Suche einblenden';
        var $toggle = $('<button type="button" class="search-form-toggle">' + btnText + '</button>');
        $formArea.before($toggle);
        $toggle.on('click', function() {
            $formArea.slideToggle(300, function() {
                if ($formArea.is(':visible')) {
                    $toggle.text('🔍 Suche ausblenden');
                    localStorage.setItem('searchFormOpen', 'true');
                } else {
                    $toggle.text('🔍 Suche einblenden');
                    localStorage.setItem('searchFormOpen', 'false');
                }
            });
        });
    }

    /* Buchname Placeholder */
    var bookName = document.querySelector('input[name="book_name"]');
    if (bookName) bookName.placeholder = 'Buchname suchen...';

    /* Select Labels */
    var labels = {
        'category': 'Kategorie', 'author': 'Autor', 'format': 'Format',
        'publish_year': 'Erscheinungsjahr', 'series': 'Serie',
        'publisher': 'Verlag', 'language': 'Sprache'
    };
    $('.search-field select').each(function() {
        var name = $(this).attr('name');
        if (labels[name]) {
            try { $(this).select2('destroy'); } catch(e) {}
            $(this).find('option[value="all"]').text(labels[name] + ' wählen...');
            $(this).select2({
                searchField: ['text', 'value'], persist: false, create: false,
                allowEmptyOption: true, allowClear: true,
                placeholder: labels[name] + ' wählen...'
            });
        }
    });

    /* Auto-Submit */
    $('.search-field select').on('change', function() {
        $('#rswpbs-books-search-form').submit();
    });

    /* Sort Labels */
    var sortLabels = {
        'default': 'Sortieren...', 'price_asc': 'Preis aufsteigend',
        'price_desc': 'Preis absteigend', 'title_asc': 'Titel A-Z',
        'title_desc': 'Titel Z-A', 'date_asc': 'Datum aufsteigend',
        'date_desc': 'Datum absteigend'
    };
    var $sort = $('#rswpbs-sort');
    $sort.find('option').each(function() {
        var val = $(this).val();
        if (sortLabels[val]) $(this).text(sortLabels[val]);
    });
    $sort.select2({ placeholder: 'Sortieren...', allowClear: false });
    $sort.off('change').on('change', function() {
        $('#rswpbs-sortby').val(this.value);
        $('#rswpbs-books-search-form').submit();
    });

    /* Review Form Labels */
    var reviewLabels = {
        'review_title': 'Bewertungstitel', 'reviewer_name': 'Dein Name',
        'reviewer_email': 'Deine E-Mail', 'rating': 'Bewertung',
        'reviewer_comment': 'Deine Meinung'
    };
    for (var fieldId in reviewLabels) {
        var $label = $('label[for="' + fieldId + '"]');
        if ($label.length && !$label.text().trim()) $label.text(reviewLabels[fieldId]);
    }

    /* Review Form Toggle */
    var $reviewForm = $('#rswpbs-review-form');
    if ($reviewForm.length) {
        $reviewForm.hide();
        var $reviewToggle = $('<button type="button" class="review-form-toggle">💬 Eigene Bewertung schreiben</button>');
        $reviewForm.before($reviewToggle);
        $reviewToggle.on('click', function() {
            $reviewForm.slideToggle(300, function() {
                if ($reviewForm.is(':visible')) {
                    $reviewToggle.text('✖ Bewertung ausblenden');
                } else {
                    $reviewToggle.text('💬 Eigene Bewertung schreiben');
                }
            });
        });
        var $reviewSubmit = $reviewForm.find('.submit-btn');
        if ($reviewSubmit.length) $reviewSubmit.val('💬 Bewertung speichern');
    }

    /* Nur ISBN-10 ausblenden (ISBN-13 behalten) */
    $('input[name="isbn_10"]').each(function() {
        $(this).closest('.search-field').hide();
    });
    $('label[for="isbn_10"]').each(function() {
        $(this).closest('.search-field').hide();
    });
    $('select[name="isbn_10"]').each(function() {
        $(this).closest('.search-field').hide();
    });

    /* Rating-Filter: Sterne-Icons (kein Label, gleiche Höhe) */
    var urlParams = new URLSearchParams(window.location.search);
    var currentRating = urlParams.get('rating') || 'all';

    var $ratingFilter = $('<div class="search-field rating-filter-field" style="flex:1;min-width:200px;display:flex;align-items:center;gap:2px;padding-top:2px;">' +
        '<span class="rating-star" data-rating="1" style="cursor:pointer;font-size:22px;color:#ccc;" title="1 Stern">★</span>' +
        '<span class="rating-star" data-rating="2" style="cursor:pointer;font-size:22px;color:#ccc;" title="2 Sterne">★</span>' +
        '<span class="rating-star" data-rating="3" style="cursor:pointer;font-size:22px;color:#ccc;" title="3 Sterne">★</span>' +
        '<span class="rating-star" data-rating="4" style="cursor:pointer;font-size:22px;color:#ccc;" title="4 Sterne">★</span>' +
        '<span class="rating-star" data-rating="5" style="cursor:pointer;font-size:22px;color:#ccc;" title="5 Sterne">★</span>' +
        '<span class="rating-clear" style="cursor:pointer;margin-left:6px;font-size:14px;color:#999;" title="Filter zurücksetzen">✕</span>' +
        '<input type="hidden" name="rating" id="filter-rating" value="' + currentRating + '">' +
        '</div>');

    function updateStars(rating) {
        $ratingFilter.find('.rating-star').each(function() {
            var r = parseInt($(this).data('rating'));
            $(this).css('color', (r <= rating) ? '#f59e0b' : '#ccc');
        });
    }

    /* Initiale Sterne-Beleuchtung */
    if (currentRating !== 'all') {
        updateStars(parseInt(currentRating));
    }

    /* Klick auf Stern */
    $ratingFilter.on('click', '.rating-star', function() {
        var r = parseInt($(this).data('rating'));
        $('#filter-rating').val(r);
        updateStars(r);
        /* Formular absenden */
        $('#rswpbs-books-search-form').submit();
    });

    /* Klick auf Zurücksetzen */
    $ratingFilter.on('click', '.rating-clear', function() {
        $('#filter-rating').val('all');
        updateStars(0);
        $('#rswpbs-books-search-form').submit();
    });

    /* In Suchformular einfügen */
    var $searchFields = $('.rswpbs-advanced-search-form-area .search-fields');
    if ($searchFields.length) {
        $searchFields.append($ratingFilter);
    } else {
        var $form = $('#rswpbs-books-search-form');
        if ($form.length) {
            var $submit = $form.find('input[type="submit"]').closest('.search-field, .submit-field');
            if ($submit.length) {
                $submit.before($ratingFilter);
            }
        }
    }

    /* Search Button */
    $('#rswpbs-books-search-form input[type="submit"]').each(function() {
        if (!$(this).val()) $(this).attr('value', '🔍 Suchen');
    });
});
</script>
<?php });

