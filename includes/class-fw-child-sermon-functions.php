<?php
/**
 * Sermon functions shared throughout this theme.
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
     * @since 1.0.0
     * @return object WP_Query object
     */
    public static function get_sermon_query() {

        return apply_filters( 'fw_child_sermon_query', false );

    }

    /**
     * 
     *
     * @since 1.0.0
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
    * Get all sermon speakers that are associated with at least one sermon.
    *
    * Returns:
    * Array (
    *   [0] => WP_Term Object (
    *       // Portion returned by get_terms():
    *       [term_id] => 101
    *       [name] => Jenny Markson
    *       [slug] => jenny-markson
    *       [term_group] => 0
    *       [term_taxonomy_id] => 101
    *       [taxonomy] => sermon_speaker
    *       [description] => '...'
    *       [parent] => 0
    *       [count] => 3
    *       [filter] => raw
    *       // Other values manually added:
    *       [link] => 'http://...'                  // Link to sermons by speaker.
    *       [speaker_image_attachment_id] => '...'  // Speaker image id.
    *       [speaker_url] => 'http://...'           // More info about speaker.
    *    )
    *    ...
    *  )
    *
    * @since 0.9
    * @return  array  See above.
    */
    public static function get_sermon_speaker_terms() {
    
        $sermon_speakers = array();

        $speaker_terms = get_terms( array(
            'taxonomy'   => 'sermon_speaker',
            'hide_empty' => true
        ) );

        // Add additional properties to each term object.
        foreach ( $speaker_terms as $speaker_term ) {
            
            // Link to sermons by speaker.
            $speaker_term->link = get_term_link( $speaker_term );

            // Add our custom [plugin] sermon series data to our object.
            // Add image attachment id.
            $custom_data = self::get_custom_sermon_speaker_data( $speaker_term->term_id );
            $speaker_term->speaker_image_attachment_id = $custom_data['speaker_image_attachment_id'];

            // Add speaker url.
            $speaker_term->speaker_url = $custom_data['speaker_url'];
        }

        return $speaker_terms;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_old_testament_books() {
    
        $books = array(
            'genesis',
            'exodus',
            'leviticus',
            'numbers',
            'duteronomy',
            'joshua',
            'judges',
            'ruth',
            '1 samuel',
            '2 samuel',
            '1 kings',
            '2 kings',
            '1 chronicles',
            '2 chronicles',
            'ezra',
            'nehemiah',
            'esther',
            'job',
            'psalms',
            'proverbs',
            'ecclesiastes',
            'song of Solomon',
            'isaiah',
            'jeremiah',
            'lamentations',
            'ezekiel',
            'daniel',
            'hosea',
            'joel',
            'amos',
            'obadiah',
            'jonah',
            'micah',
            'nahum',
            'habakkuk',
            'zephaniah',
            'haggai',
            'zechariah',
            'malachi'        
        );

        return $books;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_new_testament_books() {

        $books = array(
            'matthew',
            'mark',
            'luke',
            'john',
            'acts',
            'romans',
            '1 corinthians',
            '2 corinthians',
            'galatians',
            'ephesians',
            'philippians',
            'colossians',
            '1 thessalonians',
            '2 thessalonians',
            '1 timothy',
            '2 timothy',
            'titus',
            'philemon',
            'hebrews',
            'james',
            '1 peter',
            '2 peter',
            '1 john',
            '2 john',
            '3 john',
            'jude',
            'revelation'
        );

        return $books;

    }

   /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_sermon_book_terms() {
    
        $terms = array();

        /* Get all sermon book terms that are associated with at least one sermon.
           get_terms() returns:
            Array (
                [0] => WP_Term Object (
                    [term_id] => 117
                    [name] => Genesis
                    [slug] => genesis
                    [term_group] => 0
                    [term_taxonomy_id] => 117
                    [taxonomy] => sermon_book
                    [description] => 
                    [parent] => 0
                    [count] => 1
                    [filter] => raw
                )
                ...
            )
        */
        $terms = get_terms( array(
            'taxonomy'   => 'sermon_book',
            'hide_empty' => true
        ) );

        // Add the book url to each term object.
        foreach ( $terms as $term ) {
            
            $term->link = get_term_link( $term );

        }

        return $terms;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_sermon_books() {
    
        $old_testament_books = self::get_old_testament_books();
        $new_testament_books = self::get_new_testament_books();
        $book_terms          = self::get_sermon_book_terms();

        $list = array(
            array(
                'title'      => 'Old Testament',
                'book_terms' => array()
            ),
            array(
                'title'      => 'New Testament',
                'book_terms' => array()
            ),
            array(
                'title'      => 'Books',
                'book_terms' => array()
            )
        );

        // Sort through our book terms, placing each term in the correct book array.
        if ( isset( $book_terms ) ) {

            foreach ( $book_terms as $book_term ) {

                $book_name = strtolower( $book_term->name );

                if ( in_array( $book_name, $old_testament_books ) ) {

                    array_push( $list[0]['book_terms'], $book_term );

                }
                else if ( in_array( $book_name, $new_testament_books ) ) {

                    array_push( $list[1]['book_terms'], $book_term );

                }
                else {

                    array_push( $list[2]['book_terms'], $book_term );

                }

            }

            usort( $list[0]['book_terms'], 'self::sort_old_testament_book_terms_by_canonical_order' );
            usort( $list[1]['book_terms'], 'self::sort_new_testament_book_terms_by_canonical_order' );

        }

        return $list;

    }

    /**
     * Sort Old Testament Bible books in canonical order.
     *
     * @since 1.7.2
     */
    public static function sort_old_testament_book_terms_by_canonical_order( $a, $b ) {

        $old_testament_books = self::get_old_testament_books();

        $a_name  = strtolower( $a->name );
        $a_index = array_search( $a_name, $old_testament_books );

        $b_name  = strtolower( $b->name );
        $b_index = array_search( $b_name, $old_testament_books );
        
        if ( ( $a_index === false ) ||
             ( $b_index === false ) ) {
            return 0;
        }

        return ( $a_index === $b_index ) ? 0 : ( ( $a_index < $b_index ) ? -1 : 1 );

    }

    /**
     * Sort New Testament Bible books in canonical order.
     *
     * @since 1.7.2
     */
    public static function sort_new_testament_book_terms_by_canonical_order( $a, $b ) {

        $new_testament_books = self::get_new_testament_books();

        $a_name  = strtolower( $a->name );
        $a_index = array_search( $a_name, $new_testament_books );

        $b_name  = strtolower( $b->name );
        $b_index = array_search( $b_name, $new_testament_books );
        
        if ( ( $a_index === false ) ||
             ( $b_index === false ) ) {
            return 0;
        }

        return ( $a_index === $b_index ) ? 0 : ( ( $a_index < $b_index ) ? -1 : 1 );

    }

    /**
     * Sort series by latest sermon date
     *
     * @since 1.7.2
     */
    public static function sort_series_by_latest_sermon( $a, $b ) {

        return $b->sermon_latest_date - $a->sermon_latest_date;

    }

    /**
     *  Build and return an $archives lookup table, sorted by year in descending order.
     *
     *  $archives = array(
     *     '2016' => array(
     *          'month  => 'November',
     *          'count' => 2,
     *          'url'   => 'http...'
     *     ),
     *      ...
     *  );
     *
     * @since 0.9
     * @return array Sermon data
     */
    public static function get_sermon_archives() {

       $archives = array();
       $matches  = array();

      /*
       * Get string containing list of archive dates. Example:
        *  '<li><a href='http://church.freshwebstudio.com/2016/10/?post_type=sermon'>October 2016</a>&nbsp;(3)</li>
        *   <li><a href='http://church.freshwebstudio.com/2016/07/?post_type=sermon'>July 2016</a>&nbsp;(1)</li>
        *   <li><a href='http://church.freshwebstudio.com/2015/12/?post_type=sermon'>December 2015</a>&nbsp;(1)</li>'
       */
        $args = array(
            'post_type'       => 'sermon',
            'type'            => 'monthly',
            'echo'            => false,
            'show_post_count' => true,
            'format'          => 'html',
            'order'           => 'DESC'
        );
        $links = wp_get_archives( $args );

        if ( empty( $links ) ) {
            return $archives;
        }

        /*
         * Let's split our $links string into an array of date strings.
         *  array(
         * '<li><a href='http://church.freshwebstudio.com/2016/10/?post_type=sermon'>October 2016</a>&nbsp;(3)',
         *  '<li><a href='http://church.freshwebstudio.com/2016/07/?post_type=sermon'>July 2016</a>&nbsp;(1)',
         *  '<li><a href='http://church.freshwebstudio.com/2015/12/?post_type=sermon'>December 2015</a>&nbsp;(1)</li>'
         * )
         */
        $links = preg_split( "/<\/li>\s+/", trim( $links ) );

        if ( $links === false ) { // Abandon ship
            return $archives;
        }

        foreach( $links as $link ) {
            preg_match( "/^<li><a href='(.+)'>(.+)\s(.+)<\/a>&nbsp;\((\d+)\).*$/", $link, $matches );

            $url   = $matches[1];
            $month = $matches[2];
            $year  = $matches[3];
            $count = $matches[4];
            
            // Create a structure containing our month data parsed from the link
            $month_data = array(
                'month' => $month,
                'count' => $count,
                'url'   => $url
            );

            // If we haven't created an entry for our $year yet, do so.
            // Otherwise, push our month data onto the list for the existing $year. 
            if ( empty( $archives[$year] ) ) {
                $archives[$year] = array( $month_data );
            }
            else {
                array_push( $archives[$year], $month_data );
            }

        }
        
        // Sort the sermon archives with months in calendar order
        foreach( $archives as $year => $month_list ) {
            usort( $month_list, 'self::sort_sermon_archives_by_month' );
            $archives[$year] = $month_list;
        }

        return $archives;

    }

    /**
     * Sort the sermon archives with months in calendar order
     *
     *  array(
     *      array(
     *          'month  => 'November',
     *          'count' => 2,
     *          'url'   => 'http...'
     *      ),
     *      ...
     *  );
     *
     * @since 0.9
     * @return array  list sorted by months
     */
    public static function sort_sermon_archives_by_month( $a, $b ) {

        $a_month_index = date_parse( $a['month'] );
        $b_month_index = date_parse( $b['month'] );

        return ( $a_month_index['month'] - $b_month_index['month'] );

    }

    /**
     * 
     *
     * @since 0.9
     * @return array Sermon data
     */
    public static function get_custom_sermon_meta_data( $post_id = null ) {

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

        $speaker_image_attachment_id = get_term_meta( $term_id, 'fw_sermons_speaker_image_id', true );
        $speaker_url = get_term_meta( $term_id, 'fw_sermons_speaker_url', true );

        $data = array(
            'speaker_image_attachment_id' => $speaker_image_attachment_id,
            'speaker_url' => $speaker_url
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

        return add_query_arg( 'player', 'video', $url );

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function get_listen_audio_url( $url ) {

        return add_query_arg( 'player', 'audio', $url );

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function is_player_video() {

        return ( ! empty($_GET['player']) && ('video' === $_GET['player'] ) ) ? true : false;

    }

    /**
     * 
     *
     * @since 0.9
     * @return 
     */
    public static function is_player_audio() {
    
        return ( ! empty($_GET['player']) && ('audio' === $_GET['player'] ) ) ? true : false;

    }

    /**
     * Return embed code based on audio/video URL or provided embed code.
     *
     * If content is URL, use oEmbed to get embed code. If the url is local, then 
     * the file must exist or an empty string is returned. 
     *
     * If content is not URL, assume it is embed code and run do_shortcode() in
     * case of [video], [audio] or [embed]
     *
     * @since 0.9
     * @param  string  $content URL
     * @return string  HTML embed code or empty string
     */
    public static function get_embed_code( $content ) {

        global $wp_embed;
        $embed_code = '';

        // Convert URL into media shortcode like [audio] or [video]
        if ( FW_Child_Common_Functions::is_url( $content ) ) {

            if ( FW_Child_Common_Functions::is_local_url( $content ) ) {

                $filename = FW_Child_File_Functions::get_filename_from_local_url( $content );

                if ( empty( $filename ) || ! FW_Child_File_Functions::is_filename_exists_in_uploads_folder( $filename ) ) {

                    $content = '';

                }

            }

            $embed_code = $wp_embed->shortcode( array(), $content );

            // A link with the url is returned if the embed code failed. We don't want that.
            if ( strpos( $embed_code, '<a' ) === 0 ) {

                $embed_code = '';

            }

        }
        // HTML or shortcode embed may have been provided
        else {

            $embed_code = $content;

        }

        // Run shortcode
        // [video], [audio] or [embed] converted from URL or already existing in $content
        $embed_code = do_shortcode( $embed_code );

        return $embed_code;

    }

}
