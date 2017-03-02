<?php
/**
 * Displays a single sermon entry
 *
 * WordPress loads this partial file with a url similar to:
 *     http://your-church-domain/sermons/.
 *
 * @package    FreshWeb_Church
 * @subpackage Partial
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$speakers = get_the_term_list( $post->ID, 'sermon_speaker', '', ', ' );
$topics   = get_the_term_list( $post->ID, 'sermon_topic',   '', ', ' );
$series   = get_the_term_list( $post->ID, 'sermon_series',  '', ', ' );
$books    = get_the_term_list( $post->ID, 'sermon_book',    '', ', ' );

$excerpt = FW_Child_Common_Functions::get_the_excerpt();

$date_format = get_option( 'date_format' );
$date_string = get_the_date( $date_format );

?>

<article class="fw-child-sermon-entry">

    <?php if ( has_post_thumbnail() ) : ?>

        <header>
            <div class="fw-child-sermon-entry-image">
                <a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
            </div>
        </header>

    <?php endif; ?>

    <section class="fw-child-sermon-entry-body">

        <h2 class="fw-child-sermon-entry-title">
            <a href="<?php echo esc_url( get_permalink() ); ?>" 
               title="<?php the_title_attribute( array( 'echo' => false ) ); ?>"><?php esc_html( the_title() ); ?></a>
        </h2>

        <div class="fw-child-sermon-entry-date">
            <time datetime="<?php echo esc_attr( the_time( 'c' ) ); ?>"><?php echo $date_string; ?></time>
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
        // Display footer buttons.
        get_template_part( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-entry-footer' );
        ?>

    </section>

</article>
