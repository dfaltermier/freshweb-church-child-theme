<?php
/**
 * Displays the footer for a single sermon entry
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

// Get sermon data.
$custom_sermon_meta_data = FW_Child_Sermon_Functions::get_custom_sermon_meta_data();

// Get the block of video or audio embed code. This performs file [existent] checking
// so if we get back non-empty content, then we know we have valid video or audio content.
$video_code = ( ! empty( $custom_sermon_meta_data['video_player_url'] ) )
    ? FW_Child_Sermon_Functions::get_embed_code( $custom_sermon_meta_data['video_player_url'] )
    : '';

$audio_code = ( ! empty( $custom_sermon_meta_data['audio_player_url'] ) )
    ? FW_Child_Sermon_Functions::get_embed_code( $custom_sermon_meta_data['audio_player_url'] )
    : '';

// Build the permalink needed to access the video and audio pages for the current sermon.
if ( $video_code ) {
    $video_permalink = FW_Child_Sermon_Functions::get_watch_video_url( get_the_permalink() );
}

if ( $audio_code ) {
    $audio_permalink = FW_Child_Sermon_Functions::get_listen_audio_url( get_the_permalink() );
}

?>

<div class="fw-child-sermon-entry-footer">

    <?php if ( $video_code || $audio_code ) : ?>

        <?php if ( $video_code ) : ?>
            <a href="<?php echo esc_url( $video_permalink ); ?>" 
               class="qbutton center default" style=""><i class="fa fa-fw fa-youtube-play"></i>Watch</a>
        <?php endif; ?>

        <?php if ( $audio_code ) : ?>
            <a href="<?php echo esc_url( $audio_permalink ); ?>" 
               class="qbutton center default" style=""><i class="fa fa-fw fa-volume-up"></i>Listen</a>
        <?php endif; ?>

    <?php else : ?>

        <a href="<?php echo esc_url( get_the_permalink() ); ?>" 
           class="qbutton center default" style=""><i class="fa fa-fw fa-file-text-o"></i>Read</a>

    <?php endif; ?>

</div>