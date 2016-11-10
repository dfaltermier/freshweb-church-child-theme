<?php
/**
 * Sermon functions shared throughout the theme.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Sermon_Functions {

    /**
     *
     */
    function __construct() { }

    /**
     * 
     *
     * @since 0.9
     * @return object WP_Query object
     */
    public static function get_sermon_query() {

        return apply_filters( 'fw_child_sermon_query', false );

    }

    /**
     * 
     *
     * @since 0.9
     * @return array 
     */
    public static function get_sermon_header_banner_data() {

        return apply_filters( 'fw_child_sermon_header_banner_data', array(
            'class'    => 'child-page-header-background',
            'title'    => 'Sermons',
            'subtitle' => 'Our church messages'
        ) );

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_sermon_series() {
    
        $sermon_series = array();

        /* Get all sermon series.
           get_terms() returns:

            Array (
                [0] => WP_Term Object (
                    [term_id] => 98
                    [name] => Loving God, Loving Others
                    [slug] => loving-god-loving-others
                    [term_group] => 0
                    [term_taxonomy_id] => 98
                    [taxonomy] => sermon_series
                    [description] => "...",
                    [parent] => 0
                    [count] => 2
                    [filter] => raw
                )
                ...
            )
        */
        $series_terms = get_terms( array(
            'taxonomy'   => 'sermon_series',
            'hide_empty' => true
        ) );

        // Build our array of sermon series data indexed by term id.
        $series_ids = array();

        foreach ( $series_terms as $series_term ) {

            $series_ids[] = $series_term->term_id;
            $sermon_series[$series_term->term_id] = $series_term;

            // Add our custom [plugin] sermon series data to our object. In this case,
            // our image attachment id.
            $custom_data = self::get_custom_sermon_series_data( $series_term->term_id );
            $sermon_series[$series_term->term_id]->image_attachment_id = $custom_data['image_attachment_id'];
            $sermon_series[$series_term->term_id]->image_url = $custom_data['image_url'];
        }

        /* Get all sermons that are assigned to a series.
           get_posts() returns:

            Array (
                [0] => WP_Post Object (
                    [ID] => 1156
                    [post_author] => 1
                    [post_date] => 2016-10-10 09:50:46
                    [post_date_gmt] => 2016-10-10 15:50:46
                    [post_content] => ...
                    [post_title] => The Case for the Resurrection
                    [post_excerpt] => ...
                    [post_status] => publish
                    [comment_status] => closed
                    [ping_status] => closed
                    [post_password] => 
                    [post_name] => the-case-for-the-resurrection
                    [to_ping] => 
                    [pinged] => 
                    [post_modified] => 2016-10-30 14:33:10
                    [post_modified_gmt] => 2016-10-30 20:33:10
                    [post_content_filtered] => 
                    [post_parent] => 0
                    [guid] => http://church.freshwebstudio.com/?post_type=sermon&p=1156
                    [menu_order] => 0
                    [post_type] => sermon
                    [post_mime_type] => 
                    [comment_count] => 0
                    [filter] => raw
                )
                ...
            )
        */
        $sermons = get_posts( array(
            'post_type'      => 'sermon',
            'posts_per_page' => -1,
            'posts_status'   => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'sermon_series',
                    'field'    => 'id',
                    'terms'    => $series_ids
                )
            )
        ) );


        // For each WP_Term Object in our $sermon_series array, add a new array field
        // call 'sermons' in which we'll store each of the WP_Post objects returned
        // above that are associated with the series. Note: It may be the case that
        // a sermon is associated with more than one series.
        foreach ( $sermons as $sermon ) {

            // Get series having this sermon
            $series_ids_for_given_sermon = wp_get_post_terms( 
                $sermon->ID, 
                'sermon_series',
                array( 'fields' => 'ids' ) // Return only ids
            );

            // Add a 'sermons'
            foreach ( $series_ids_for_given_sermon as $series_id ) {

                if ( isset( $sermon_series[$series_id] ) ) {

                    if ( ! isset( $sermon_series[$series_id]->sermons ) ) {
                        $sermon_series[$series_id]->sermons = array();
                    }

                    $sermon_series[$series_id]->sermons[$sermon->ID] = $sermon;

                }

            }

        }

        // Loop series to record latest and earliest sermon dates
        foreach ( $sermon_series as $series_id => $series_item ) {

            if ( isset( $series_item->sermons ) ) {

                $sermons = $series_item->sermons;

                // Latest sermon
                $values = array_values( $sermons );
                $latest_sermon = array_shift( $values );
                $sermon_series[$series_id]->sermon_latest_date = strtotime( $latest_sermon->post_date );

                // Earliest sermon
                $values = array_values( $sermons );
                $earliest_sermon = end( $values );
                $sermon_series[$series_id]->sermon_earliest_date = strtotime( $earliest_sermon->post_date );

            }

        }

        // Sort the sermon series with the most recent sermon dates first.
        usort( $sermon_series, 'self::sort_series_by_latest_sermon' );

        return $sermon_series;

    }

   /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_sermon_speaker_terms() {
    
        $sermon_speakers = array();

        /* Get all sermon speakers that are associated with at least one sermon.
           get_terms() returns:

            Array (
                [0] => WP_Term Object (
                    [term_id] => 101
                    [name] => Jenny Markson
                    [slug] => jenny-markson
                    [term_group] => 0
                    [term_taxonomy_id] => 101
                    [taxonomy] => sermon_speaker
                    [description] => '...'
                    [parent] => 0
                    [count] => 3
                    [filter] => raw
                )
                ...
            )
        */
        $speaker_terms = get_terms( array(
            'taxonomy'   => 'sermon_speaker',
            'hide_empty' => true
        ) );

        // Add the speaker url to each term object.
        foreach ( $speaker_terms as $speaker_term ) {
            
            $speaker_term->link = get_term_link( $speaker_term );

            // Add our custom [plugin] sermon series data to our object. In this case,
            // our image attachment id.
            $custom_data = self::get_custom_sermon_speaker_data( $speaker_term->term_id );
            $speaker_term->image_attachment_id = $custom_data['image_attachment_id'];
        }

        return $speaker_terms;

    }

    /**
     * Sort series by latest sermon date
     *
     * Assist ctfw_content_type_archives by sorting series by sermon_latest_date
     *
     * @since 1.7.2
     */
    public static function sort_series_by_latest_sermon( $a, $b ) {
        return $b->sermon_latest_date - $a->sermon_latest_date;
    }


    /**
     * 
     *
     * @since 0.9
     * @return array Sermon data
     */
    public static function get_custom_sermon_data( $post_id = null ) {

        $post_id = $post_id ? $post_id : get_the_ID();

        $data = array(
            'audio_player_url'   => get_post_meta( $post_id, '_fw_sermons_audio_player_url', true ),
            'audio_download_url' => get_post_meta( $post_id, '_fw_sermons_audio_download_url', true ),
            'video_player_url'   => get_post_meta( $post_id, '_fw_sermons_video_player_url', true ),
            'video_download_url' => get_post_meta( $post_id, '_fw_sermons_video_download_url', true ),
            'document_links'     => get_post_meta( $post_id, '_fw_sermons_document_links', true )
        );

        return $data;

    }

    /**
     * 
     *
     * @since 0.9
     * @return array Sermon data
     */
    public static function get_custom_sermon_series_data( $term_id ) {

        $image_attachment_id = get_term_meta( $term_id, 'fw_sermons_series_image_id', true );
        $image_url   = isset( $image_attachment_id ) ? wp_get_attachment_url( $image_attachment_id ) : null;

        $data = array(
            'image_attachment_id' => $image_attachment_id,
            'image_url'           => $image_url
        );

        return $data;

    }

    /**
     * 
     *
     * @since 0.9
     * @return array Sermon data
     */
    public static function get_custom_sermon_speaker_data( $term_id ) {

        $image_attachment_id = get_term_meta( $term_id, 'fw_sermons_speaker_image_id', true );

        $data = array(
            'image_attachment_id' => $image_attachment_id
        );

        return $data;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_watch_video_url( $url ) {

        return add_query_arg( 'watch', 'true', $url );

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_listen_audio_url( $url ) {

        return add_query_arg( 'listen', 'true', $url );

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function is_watch_video_url() {

        return ( 'true' === $_GET['watch'] ) ? true : false;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function is_listen_audio_url() {
    
        return ( 'true' === $_GET['listen'] ) ? true : false;

    }

    /**
     *
     * @since 
     * @global 
     * @return 
     */
    public static function get_trimmed_excerpt( $excerpt = null , $number_of_words = 55 ) {

        $excerpt = isset( $excerpt ) ? $excerpt : get_the_excerpt();
        $excerpt = FW_Child_Common_Functions::get_trimmed_excerpt( $excerpt, $number_of_words );
        return $excerpt;

    }

}
