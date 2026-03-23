<?php
/**
 * Single Book Template — Child Theme Override
 * Ersetzt die Standard-Darstellung für Buch-Detailseiten.
 */

get_header(); ?>

<div class="rswpbs-book-single-wrapper">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>

            <?php
            // Book Meta Fields
            $cover_url  = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $buy_link   = get_post_meta(get_the_ID(), '_rsbs_buy_btn_link', true);
            $buy_text   = get_post_meta(get_the_ID(), '_rsbs_buy_btn_text', true) ?: 'Auf Amazon kaufen';
            $price      = get_post_meta(get_the_ID(), '_rsbs_book_price', true);
            $desc       = get_post_meta(get_the_ID(), '_rsbs_short_description', true);
            $orig_name  = get_post_meta(get_the_ID(), '_rsbs_original_book_name', true);
            $orig_url   = get_post_meta(get_the_ID(), '_rsbs_original_book_url', true);
            ?>

            <div class="rswpbs-row" style="display:flex;flex-wrap:wrap;gap:30px;">

                <!-- LINKE SPALTE: Cover -->
                <div style="flex:0 0 300px;max-width:300px;">
                    <?php if ($cover_url) : ?>
                        <?php if ($buy_link) : ?>
                            <a href="<?php echo esc_url($buy_link); ?>" target="_blank" rel="nofollow">
                        <?php endif; ?>
                        <img src="<?php echo esc_url($cover_url); ?>" alt="<?php the_title(); ?>" style="width:100%;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.2);">
                        <?php if ($buy_link) : ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($price) : ?>
                        <div style="text-align:center;margin-top:15px;">
                            <span style="font-size:24px;font-weight:700;color:#39b152;">€<?php echo esc_html($price); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($buy_link) : ?>
                        <div style="text-align:center;margin-top:10px;">
                            <a href="<?php echo esc_url($buy_link); ?>" target="_blank" rel="nofollow" style="display:inline-block;background:#39b152;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:16px;">
                                🛒 <?php echo esc_html($buy_text); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RECHTE SPALTE: Details -->
                <div style="flex:1;min-width:300px;">

                    <h1 style="margin:0 0 10px;font-size:28px;line-height:1.3;"><?php the_title(); ?></h1>

                    <?php
                    $authors = get_the_terms(get_the_ID(), 'book-author');
                    if ($authors && !is_wp_error($authors)) :
                        foreach ($authors as $author) :
                    ?>
                        <p style="font-size:18px;color:#666;margin-bottom:15px;">
                            Von <a href="<?php echo get_term_link($author); ?>" style="color:#39b152;text-decoration:none;font-weight:600;"><?php echo esc_html($author->name); ?></a>
                        </p>
                    <?php endforeach; endif; ?>

                    <?php if ($desc) : ?>
                        <p style="font-size:16px;line-height:1.6;margin-bottom:20px;color:#333;"><?php echo nl2br(esc_html($desc)); ?></p>
                    <?php endif; ?>

                    <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                        <?php
                        $fields = [
                            'book_publisher_name'     => ['Verlag',         '📚'],
                            'book_publish_date'       => ['Erschienen',     '📅'],
                            'book_pages'              => ['Seiten',         '📖'],
                            'book_format'             => ['Format',         '📦'],
                            'book_isbn_13'            => ['ISBN-13',        '🔢'],
                            'book_isbn_10'            => ['ISBN-10',        '🔢'],
                            'book_asin'               => ['ASIN',           '🏷️'],
                            'book_language'           => ['Sprache',        '🌐'],
                            'book_country'            => ['Land',           '🌍'],
                            'book_dimension'          => ['Abmessungen',    '📐'],
                            'print_length'            => ['Drucklänge',    '📏'],
                            'book_availability_status'=> ['Verfügbarkeit',  '✅'],
                        ];
                        foreach ($fields as $key => $info) :
                            $value = get_post_meta(get_the_ID(), '_rsbs_' . $key, true);
                            if ($value) :
                                if ($key === 'book_availability_status') {
                                    $value = ($value === 'available') ? '✅ Verfügbar' : '🕐 Demnächst';
                                }
                        ?>
                            <tr style="border-bottom:1px solid #eee;">
                                <td style="padding:8px 0;font-weight:600;color:#555;width:40%;"><?php echo $info[1] . ' ' . $info[0]; ?></td>
                                <td style="padding:8px 0;color:#333;"><?php echo esc_html($value); ?></td>
                            </tr>
                        <?php endif; endforeach; ?>
                    </table>

                    <?php if ($orig_name) : ?>
                        <div style="background:#f9f9f9;padding:15px;border-radius:8px;margin-bottom:20px;">
                            <strong>Originaltitel:</strong>
                            <?php if ($orig_url) : ?>
                                <a href="<?php echo esc_url($orig_url); ?>"><?php echo esc_html($orig_name); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($orig_name); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $categories = get_the_terms(get_the_ID(), 'book-category');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                        <div style="margin-bottom:20px;">
                            <strong>Kategorien:</strong>
                            <?php foreach ($categories as $cat) : ?>
                                <a href="<?php echo get_term_link($cat); ?>" style="display:inline-block;background:#e8f5e9;color:#39b152;padding:4px 12px;border-radius:20px;margin:2px;text-decoration:none;font-size:14px;">
                                    <?php echo esc_html($cat->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php echo do_shortcode('[rswpbs_reviews book_ids="' . get_the_ID() . '" review_layout="grid" layout_style="classic" section_heading="Leserbewertungen"]'); ?>

            <div style="margin-top:30px;" class="awt-dynamic-content">
                <?php the_content(); ?>
            </div>

        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
