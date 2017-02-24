<?php
/**
 * Displays a single sermon page
 *
 * WordPress loads this partial file with a url similar to:
 *     http://your-church-domain/sermon/the-case-for-grace/
 * where 'the-case-for-grace' is the selected sermon by the user.
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
?>

<?php if (have_posts()) : ?>

    <?php while (have_posts()) : the_post(); ?>

        <?php
        $custom_sermon_meta_data = FW_Child_Sermon_Functions::get_custom_sermon_meta_data();

        // Determine the page we should display based on the query string contents (e.g.: player=video)
        $is_video_page = FW_Child_Sermon_Functions::is_player_video();
        $is_audio_page = FW_Child_Sermon_Functions::is_player_audio();
        $is_read_page  = ( ! $is_video_page && ! $is_audio_page ) ? true : false;
        
        // Build the permalink needed to access the video and audio pages for the current sermon.
        $video_page_url = ( ! empty( $custom_sermon_meta_data['video_player_url'] ) )
            ? FW_Child_Sermon_Functions::get_watch_video_url( get_the_permalink() )
            : '';

        $audio_page_url = ( ! empty( $custom_sermon_meta_data['audio_player_url'] ) )
            ? FW_Child_Sermon_Functions::get_listen_audio_url( get_the_permalink() )
            : '';

        // Get the block of video or audio embed code we'll need later
        $video_code = ( ! empty( $custom_sermon_meta_data['video_player_url'] ) )
            ? FW_Child_Sermon_Functions::get_embed_code( $custom_sermon_meta_data['video_player_url'] )
            : '';

        $audio_code = ( ! empty( $custom_sermon_meta_data['audio_player_url'] ) )
            ? FW_Child_Sermon_Functions::get_embed_code( $custom_sermon_meta_data['audio_player_url'] )
            : '';

        // Get the video/audio download urls
        $video_download_url = ( ! empty( $custom_sermon_meta_data['video_download_url'] ) ) 
            ? FW_Child_Download_Functions::get_download_url ($custom_sermon_meta_data['video_download_url'] )
            : '';

        $audio_download_url = ( ! empty( $custom_sermon_meta_data['audio_download_url'] ) ) 
            ? FW_Child_Download_Functions::get_download_url ($custom_sermon_meta_data['audio_download_url'] )
            : '';

        $document_links = ! empty( $custom_sermon_meta_data['document_links'] )
            ? $custom_sermon_meta_data['document_links']
            : array();

        // Fetch all of the taxonomies.
        $speakers = get_the_term_list( $post->ID, 'sermon_speaker', '', ', ' );
        $topics   = get_the_term_list( $post->ID, 'sermon_topic',   '', ', ' );
        $series   = get_the_term_list( $post->ID, 'sermon_series',  '', ', ' );
        $books    = get_the_term_list( $post->ID, 'sermon_book',    '', ', ' );

        // Don't forget the post date.
        $date_format = get_option( 'date_format' );
        $date_string = get_the_date( $date_format );

        $prev_sermon_post = get_previous_post();
        $next_sermon_post = get_next_post();

        ?>

        <article class="fw-child-single-sermon">

            <?php if ( $is_video_page && ! empty( $video_code ) ) : ?>

                <div class="fw-child-single-sermon-video-player">
                    <?php echo $video_code; ?>                 
                </div>

            <?php elseif ( $is_audio_page && ! empty( $audio_code ) ) : ?>
                
                <div class="fw-child-single-sermon-audio-player">
                    <?php echo $audio_code; ?>                 
                </div>

            <?php elseif ( $is_read_page && has_post_thumbnail() ) : ?>

                <div class="fw-child-single-sermon-featured_image">
                    <?php the_post_thumbnail(); ?>
                </div>

            <?php endif; ?>

            <ul class="fw-child-single-sermon-terms">

                <li class="fw-child-single-sermon-term">
                    <div class="fw-child-single-sermon-term-label">Date</div>
                    <div class="fw-child-single-sermon-term-value"><?php echo $date_string; ?></div>
                </li>

                <?php if ( $speakers ) : ?>

                    <li class="fw-child-single-sermon-term">
                        <div class="fw-child-single-sermon-term-label">Speaker</div>
                        <div class="fw-child-single-sermon-term-value"><?php echo $speakers; ?></div>
                    </li>

                <?php endif; ?>

                <?php if ( $topics ) : ?>

                    <li class="fw-child-single-sermon-term">
                        <div class="fw-child-single-sermon-term-label">Topic</div>
                        <div class="fw-child-single-sermon-term-value"><?php echo $topics; ?></div>
                    </li>

                <?php endif; ?>

                <?php if ( $books ) : ?>

                    <li class="fw-child-single-sermon-term">
                        <div class="fw-child-single-sermon-term-label">Book</div>
                        <div class="fw-child-single-sermon-term-value"><?php echo $books; ?></div>
                    </li>

                <?php endif; ?>

                <?php if ( $series ) : ?>

                    <li class="fw-child-single-sermon-term">
                        <div class="fw-child-single-sermon-term-label">Series</div>
                        <div class="fw-child-single-sermon-term-value"><?php echo $series; ?></div>
                    </li>

                <?php endif; ?>

            </ul>

            <?php if ( ( ! empty( $video_code ) ) || ( ! empty( $audio_code ) ) ) : ?>

                <div class="fw-child-single-sermon-action-buttons">

                    <?php if ( $is_read_page ) : ?>

                        <?php if ( $video_code ) : ?>
                            <a href="<?php echo esc_url( $video_page_url ); ?>" 
                               class="fw-child-single-sermon-action-button qbutton center default"><i class="fa fa-fw fa-youtube-play"></i>Watch</a>
                        <?php endif; ?>

                        <?php if ( $audio_code ) : ?>
                            <a href="<?php echo esc_url( $audio_page_url ); ?>"
                               class="fw-child-single-sermon-action-button qbutton center default"><i class="fa fa-fw fa-volume-up"></i>Listen</a>
                        <?php endif; ?>

                    <?php elseif ( $is_video_page ) : ?>

                        <?php if ( $audio_code ) : ?>
                            <a href="<?php echo esc_url( $audio_page_url ); ?>"
                               class="fw-child-single-sermon-action-button qbutton center default"><i class="fa fa-fw fa-volume-up"></i>Listen</a>
                        <?php endif; ?>

                    <?php elseif ( $is_audio_page ) : ?>

                        <?php if ( $video_code ) : ?>
                            <a href="<?php echo esc_url( $video_page_url ); ?>"
                               class="fw-child-single-sermon-action-button qbutton center default"><i class="fa fa-fw fa-youtube-play"></i>Watch</a>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <?php if ( ( ! empty( $video_download_url ) ) || ( ! empty( $audio_download_url ) ) ) : ?>

                <div class="fw-child-single-sermon-download-buttons">

                    <?php if ( ! empty( $video_download_url ) ) : ?>
                        <a href="<?php echo esc_url( $video_download_url ); ?>" download
                           class="fw-child-single-sermon-download-button qbutton center default"><i class="fa fa-fw fa-download"></i>Download Video</a>
                    <?php endif; ?>

                    <?php if ( ! empty( $audio_download_url ) ) : ?>
                        <a href="<?php echo esc_url( $audio_download_url ); ?>" download
                           class="fw-child-single-sermon-download-button qbutton center default"><i class="fa fa-fw fa-download"></i>Download Audio</a>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <div class="fw-child-single-sermon-title">
                <h2><?php echo htmlentities( get_the_title() ); ?></h2>
            </div>

            <div class="fw-child-single-sermon-copy">
                <?php echo the_content(); ?>
            </div>

            <?php if ( ! empty( $document_links ) ) : ?>

                <div class="fw-child-single-sermon-documents-container">
                    <h3 class="fw-child-single-sermon-documents-title">Sermon Documents</h3>
                    <ul class="fw-child-single-sermon-documents fa-ul">

                        <?php foreach ( $document_links as $document_link ) : ?>

                            <?php if ( ! empty( $document_link ) &&
                                       ! empty( $document_link['label'] ) &&
                                       ! empty( $document_link['url'] ) ) : ?>

                                <li class="fw-child-single-sermon-document"><a href="<?php echo esc_url( $document_link['url'] ); ?>" target="_blank"><i class="fa-li fa fa-download"></i><?php echo htmlentities( $document_link['label'] ); ?></a></li>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </ul>
                </div>

            <?php endif; ?>

        </article>

        <?php if ( ! empty( $prev_sermon_post ) || ! empty( $next_sermon_post ) ) : ?>

            <div class="fw-child-single-sermon-pagination">

                <div class="fw-child-single-sermon-pagination-previous-entry">

                    <?php if ( ! empty( $prev_sermon_post ) ) : ?>

                        <a href="<?php echo esc_url( get_the_permalink( $prev_sermon_post->ID ) ); ?>"><img
                           class="fw-child-single-sermon-pagination-previous-tab" 
                           src="<?php echo FW_CHILD_THEME_IMAGE_URI . '/sermon-corner-previous.600x600-min.png' ?>"
                           alt="Previous sermon" /></a>

                        <?php
                        // Pass the variable $sermon_post to our partial template.
                        ?>
                        <?php $sermon_post = $prev_sermon_post; ?>
                        <?php include( locate_template( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-entry-prev-or-next.php' ) ); ?>

                    <?php endif; ?>

                </div>

                <div class="fw-child-single-sermon-pagination-next-entry">

                    <div class="fw-child-single-sermon-pagination-entry">

                    <?php if ( ! empty( $next_sermon_post ) ) : ?>

                        <a href="<?php echo esc_url( get_the_permalink( $next_sermon_post->ID ) ); ?>"><img
                           class="fw-child-single-sermon-pagination-next-tab" 
                           src="<?php echo FW_CHILD_THEME_IMAGE_URI . '/sermon-corner-next.600x600-min.png' ?>"
                           alt="Next sermon" /></a>

                        <?php
                        // Pass the variable $sermon_post to our partial template.
                        ?>
                        <?php $sermon_post = $next_sermon_post; ?>
                        <?php include( locate_template( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-entry-prev-or-next.php' ) ); ?>

                    <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    <?php endwhile; ?>

<?php endif; ?> 

