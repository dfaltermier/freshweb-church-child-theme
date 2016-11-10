<?php
/**
 * 
 */
// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Get sermon data:
$sermon_data = FW_Child_Sermon_Functions::get_custom_sermon_data();

$video_url = $sermon_data['video_player_url'];
$audio_url = $sermon_data['audio_player_url'];

// Build the permalink needed to access the video and audio pages for the current sermon.
if ( $video_url ) {
    $video_permalink = FW_Child_Sermon_Functions::get_watch_video_url( get_the_permalink() );
}

if ( $audio_url ) {
    $audio_permalink = FW_Child_Sermon_Functions::get_listen_audio_url( get_the_permalink() );
}

?>

<?php if ( $video_url || $audio_url ) : ?>

    <div class="fw-child-sermon-entry-footer">

        <?php if ( $video_url ) : ?>
            <a itemprop="url" href="<?php echo esc_url( $video_permalink ); ?>" 
               class="qbutton center default" style="">Watch</a>
        <?php endif; ?>

        <?php if ( $audio_url ) : ?>
            <a itemprop="url" href="<?php echo esc_url( $audio_permalink ); ?>" 
               class="qbutton center default" style="">Listen</a>
        <?php endif; ?>

    </div>

<?php endif; ?>
