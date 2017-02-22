<?php
/**
 * 
 */
// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Note: The variable $sermon_post is inherited by our calling file.
 */

$thumbnail_image = get_the_post_thumbnail( $sermon_post->ID );

$title = $sermon_post->post_title;
$permalink = get_the_permalink( $sermon_post->ID );

$speakers = get_the_term_list( $sermon_post->ID, 'sermon_speaker', '', ', ' );
$topics   = get_the_term_list( $sermon_post->ID, 'sermon_topic',   '', ', ' );
$series   = get_the_term_list( $sermon_post->ID, 'sermon_series',  '', ', ' );
$books    = get_the_term_list( $sermon_post->ID, 'sermon_book',    '', ', ' );

$excerpt = $sermon_post->post_excerpt;
$excerpt = wp_strip_all_tags( $excerpt );
$excerpt = wptexturize( $excerpt );

$date_format = get_option( 'date_format' );
$date_string = date( $date_format, strtotime( $sermon_post->post_date ) );

?>

<article class="fw-child-single-sermon-pagination-entry">

    <?php if ( ! empty($thumbnail_image) ) : ?>

        <header>
            <div class="fw-child-sermon-entry-image">
                <a href="<?php echo esc_url( $permalink ); ?>" 
                   title="<?php echo esc_attr( $title ); ?>"><?php echo $thumbnail_image; ?></a>
            </div>
        </header>

    <?php endif; ?>

    <section class="fw-child-sermon-entry-body">

        <h2 class="fw-child-sermon-entry-title">
            <a href="<?php echo esc_url( $permalink ); ?>" 
               title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></a>
        </h2>

        <div class="fw-child-sermon-entry-date">
            <span><?php echo $date_string; ?></span>
        </div>

        <div class="fw-child-sermon-entry-terms"> 
            <ul>
                <?php if ( $speakers ) : ?>
                    <li>Speakers: <?php echo $speakers; ?></li>
                <?php endif; ?>

                <?php if ( $series ) : ?>
                    <li>Series: <?php echo $series; ?></li>
                <?php endif; ?>

                <?php if ( $books ) : ?>
                    <li>Books: <?php echo $books; ?></li>
                <?php endif; ?>

                <?php if ( $topics ) : ?>
                    <li>Topics: <?php echo $topics; ?></li>
                <?php endif; ?>

            </ul>
        </div>

       <div class="fw-child-sermon-entry-separator"></div>

        <?php if ( $excerpt ) : ?>

            <div class="fw-child-sermon-entry-excerpt"><?php echo $excerpt; ?></div>

        <?php endif; ?>

        <?php 
        // Display Footer buttons.
        // Including the file this way will allow us to access this file's variable $sermon_post.
        include( locate_template( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-entry-footer-prev-or-next.php' ) );
        ?>        

    </section>

</article>