<?php
/**
 * Provide utility methods for Sermon functions
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 *
 * This file incorporates portions of code from the Maranatha Church Theme
 * (https://churchthemes.com/themes/maranatha). The original code is 
 * copyright (c) 2015, churchthemes.com and is distributed under the terms
 * of the GNU GPL license 2.0 or later 
 * (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide utility methods for Sermon functions.
 *
 * @since 1.1.0
 */
class FW_Child_Sermon_Functions {

    /**
     * This query parameter in the url will determine the sermon series page to display when
     * paginated. We home-cook our own pagination for the sermon series so we need a query
     * parameter that is unlike WP's paged' or 'page' parameter.
     *
     * @since 1.1.0
     */
    const SERMON_QUERY_PARAM_PAGE_NUMBER = 'page_number';

    /**
     * Default number of sermon series entries to display on one page. This number is only
     * used if we fail to get_option( 'posts_per_page' ). 
     *
     * @since 1.1.0
     */
    const SERMON_SERIES_DEFAULT_ENTRIES_PER_PAGE = 10;

    /**
     * Because the database load is heavy when building our sermon series
     * data structure, we'll cache the db query data in a transient for 
     * SERMON_SERIES_TRANSIENT_TIMEOUT seconds.
     *
     * @since 1.1.0
     */
    const SERMON_SERIES_TRANSIENT_TIMEOUT = 20;  // Seconds
    const SERMON_SERIES_TRANSIENT_NAME    = 'fw_child_sermon_series';

    /**
     * Constructor
     */
    function __construct() {

        /*
         * When a sermon is created, updated, or deleted, we'll want to delete our transient holding 
         * our cached sermon series data structure.
         * Format example: 'save_post_{custom_post_type}'
         */
        add_action( 'save_post_sermon', array( $this, 'delete_sermon_series_transient_on_sermon_post_type_change', 10, 3 ) );

        /*
         * When a sermon taxonomy term is created, updated, or deleted, we'll want to delete our
         * transient holding our cached sermon series data structure.
         * Format example: 'create_{$taxonomy}'
         */
        add_action( 'create_sermon_series', array( $this, 'delete_sermon_series_transient_on_sermon_series_term_change' ) );
        add_action( 'edit_sermon_series',   array( $this, 'delete_sermon_series_transient_on_sermon_series_term_change' ) );
        add_action( 'delete_sermon_series', array( $this, 'delete_sermon_series_transient_on_sermon_series_term_change' ) );

    }

    /**
     * Return custom queries for our database. These may defined in any one of our sermon files.
     *
     * @since  1.1.0
     * @return object WP_Query object
     */
    public static function get_sermon_query() {

        return apply_filters( 'fw_child_sermon_query', false );

    }

    /**
     * Returns the text used in the header of each page. This text overlays the background
     * header image. Each page can override this text via the apply_filter().
     *
     * @since  1.1.0
     * @return array  See below.
     */
    public static function get_sermon_header_banner_data() {

        return apply_filters( 'fw_child_sermon_header_banner_data', array(
            'class'    => 'fw-child-sermon-page-header-background',
            'title'    => 'Sermons',
            'subtitle' => 'Our church messages'
        ) );

    }

    /**
     * Get the sermon series data used to populate the sermon-series.php page template.
     * We return only the sermon series to populate the current page.
     * See below for data structure returned.
     *
     * @since  1.1.0
     * @see    get_sermon_series()
     *
     * @return array  See below for data structure.
     */
    public static function get_sermon_series_with_pagination() {

        $sermon_series = self::get_sermon_series();

        // What page are we on?
        $paged = self::get_current_page_number();

        // Number of sermon series to show per-page.
        $sermon_series_per_page = get_option( 'posts_per_page', self::SERMON_SERIES_DEFAULT_ENTRIES_PER_PAGE );

        // Get the total number of sermon series.
        $total_number_of_sermon_series = count( $sermon_series );

        $pagination_offset = ( $paged - 1 ) * $sermon_series_per_page;

        //$big_number = 999999999; // Just for a placeholder.
        $pagination = paginate_links( array(
            //'base'      => str_replace( $big_number, '%_%', get_pagenum_link( $big_number ) ),
            'base'      => get_permalink() . '%_%',
            'format'    => '?' . self::SERMON_QUERY_PARAM_PAGE_NUMBER . '=%#%',
            'current'   => $paged,
            'total'     => ceil( $total_number_of_sermon_series / $sermon_series_per_page ),
            'type'      => 'list',
            'prev_text' => 'Previous',
            'next_text' => 'Next'
        ) );

        $sermon_series = array_slice( $sermon_series, $pagination_offset, $sermon_series_per_page );

        // Finally, return both our sermon series array and pagination code.
        $sermon_series_data = array(
            'series'     => $sermon_series,
            'pagination' => $pagination
        );

        return $sermon_series_data;

    }

    /**
     * Get the sermon series data used to populate the sermon-series.php page template.
     * See below for data structure returned.
     *
     * The data we pull and build from the database is cached in a transient for
     * SERMON_SERIES_TRANSIENT_TIMEOUT seconds.
     *
     * @since  1.1.0
     * @return array  See below for data structure.
     */
    public static function get_sermon_series() {
    
        // Cache with transient because getting all sermon series entries can be intensive.
        // It's possible this function is called more than once during a page load
        // so let's cache it for a few seconds so the queries and loops are not repeated.
        $transient = get_transient( self::SERMON_SERIES_TRANSIENT_NAME );

        if ( $transient ) {

            $sermon_series = $transient;
            return $sermon_series;

        }

        $sermon_series = array();

        /* Get all sermon series terms.
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

            // Add 'sermons'
            foreach ( $series_ids_for_given_sermon as $series_id ) {

                if ( isset( $sermon_series[$series_id] ) ) {

                    if ( ! isset( $sermon_series[$series_id]->sermons ) ) {
                        $sermon_series[$series_id]->sermons = array();
                    }

                    $sermon_series[$series_id]->sermons[$sermon->ID] = $sermon;

                }

            }

        }

        // Loop through series to record latest and earliest sermon dates. We'll want to
        // sort the sermon series by the latest date, but display both dates in the 
        // sermon series entries.
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

        // Sort the sermon series with the most recent sermon [latest] dates first.
        usort( $sermon_series, 'self::sort_series_by_latest_sermon' );

        // Cache our sermon series.
        set_transient( self::SERMON_SERIES_TRANSIENT_NAME, $sermon_series, self::SERMON_SERIES_TRANSIENT_TIMEOUT );

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
    * @since  1.1.0
    * @return array  See above.
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
     * Return a list of Old Testament books sorted in Canonical order.
     *
     * @since  1.1.0
     * @return array  See data structure below.
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
     * Return a list of New Testament books sorted in Canonical order.
     *
     * @since  1.1.0
     * @return array  See data structure below.
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
     * Return all sermon book terms that are associated with at least one sermon.
     *
     * @since  1.1.0
     * @return array  See data structure below.
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
                    // We manually add the following:
                    [link] => 'http://...'
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
     * Return all books associated with at least one sermon. The data structure returned
     * orders the books by 'Old Testament,' 'New Testament,' and just 'Books.' The Biblical
     * books are ordered in Canonical order.
     *
     * @since  1.1.0
     * @return array  See data structure below.
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

            // Only need to sort Biblical books.
            usort( $list[0]['book_terms'], 'self::sort_old_testament_book_terms_by_canonical_order' );
            usort( $list[1]['book_terms'], 'self::sort_new_testament_book_terms_by_canonical_order' );

        }

        return $list;

    }

    /**
     * Callback method to pass to usort() that will sort Old Testament Bible books in canonical order.
     *
     * @since  1.1.0
     *
     * @param  object $a  $a->name is Bible book name.
     * @param  object $b  $b->name is Bible book name.
     * @return int        -1, 0, or 1. Used by usort.
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
     * Callback method to pass to usort() that will sort New Testament Bible books in canonical order.
     *
     * @since  1.1.0
     *
     * @param  object $a  $a->name is Bible book name.
     * @param  object $b  $b->name is Bible book name.
     * @return int        -1, 0, or 1. Used by usort.
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
     * Callback method to pass to usort(). Will sort sermons by the lastest sermon date
     * in the sermon series.
     *
     * @since  1.1.0
     *
     * @param  object $a  $a->sermon_latest_date is Bible book name.
     * @param  object $b  $b->sermon_latest_date is Bible book name.
     * @return int        Positive number, 0, or negative number. See usort() for details.
     */
    public static function sort_series_by_latest_sermon( $a, $b ) {

        return $b->sermon_latest_date - $a->sermon_latest_date;

    }

    /**
     * Filter callback used to modify the SELECT portion of the sql query for get_sermons_by_date().
     *
     * @since  1.1.0
     *
     * @param  string $sql  SELECT portion of default sql statement.
     * @return string       Modified $sql.
     */
    public static function filter_sermons_by_date( $sql ) {

        global $wpdb;
        
        $post_table = $wpdb->prefix . 'posts';

        $my_sql = 
            $post_table . '.ID, ' .
            $post_table . '.post_title, ' .
            $post_table . '.post_name, '  .  // Needed to create permalink
            $post_table . '.post_date';

        return $my_sql;

    }

    /**
     * Returns a list of sermons grouped by month number for the given year.
     *
     * Data structure returned:
     *     $months = array(
     *         '1' => array(
     *             'ID'             => get_the_ID(),
     *             'post_date'      => get_the_date( get_option( 'date_format' ) ),
     *             'post_title'     => get_the_title(),
     *             'post_permalink' => get_permalink()
     *         ),
     *         ...
     *     );
     *
     * @since  1.1.0
     *
     * @param  string    $year   Optional. e.g.: '2017'. Default: current year.
     * @return array             Sermons
     */
    public static function get_sermons_by_date( $year = '' ) {

        // Use the current year by default.
        $year = empty( $year ) ? date( 'Y' ) : $year; 

        // Map our months to a list of sermons associated for that month.
        $months = array();

        /*
         * Fetch all sermons from the database. Retrieve only those fields that we need
         * by applying a filter that modifies the SELECT statement.
         */
        add_filter( 'posts_fields', array( 'FW_Child_Sermon_Functions', 'filter_sermons_by_date' ) );

        $args = array(
           'post_type'  => 'sermon',
           'nopaging'   => true,          // Fetch all sermons
           'order'      => 'ASC',         // Jan, Feb, Mar,...
           'date_query' => array(
                array( 'year' => $year )  // Fetch only the given year's worth of sermons.
            )
        );

        $query = new WP_Query( $args );

        // Remove our filter to be clean for later queries.
        remove_filter( 'posts_fields', array( 'FW_Child_Sermon_Functions', 'filter_sermons_by_date' ) );

        if ( $query->have_posts() ) {

            while ( $query->have_posts() ) {

                $query->the_post();

                $sermon = array(
                    'ID'             => get_the_ID(),
                    'post_date'      => get_the_date( get_option( 'date_format' ) ),
                    'post_title'     => get_the_title(),
                    'post_permalink' => get_permalink()
                );

                // The month number will be the index to our $months array.
                $month_index = get_the_date( 'n' ); // '1' for January, etc.

                if ( empty( $months[$month_index] ) ) {
                    $months[$month_index] = array();
                }
                    
                array_push( $months[$month_index], $sermon );

            }

            wp_reset_postdata();
        
        }

        return $months;

    }

    /**
     * Callback method to pass to usort(). Sort the sermon archives with months in calendar order.
     *
     * Returns:
     *   array(
     *       array(
     *           'month  => 'November',
     *           'count' => 2,
     *           'url'   => 'http...'
     *       ),
     *       ...
     *   );
     *
     * @since  1.1.0
     * @return int    Positive number, 0, or negative number. See usort() for details.
     */
    public static function sort_sermon_archives_by_month( $a, $b ) {

        $a_month_index = date_parse( $a['month'] );
        $b_month_index = date_parse( $b['month'] );

        return ( $a_month_index['month'] - $b_month_index['month'] );

    }

    /**
     * Return the custom sermon post type data from our Sermon plugin.
     *
     * @since  1.1.0
     * @return array  Sermon data.
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
     * Return the sermon series term data from our Sermon plugin.
     *
     * @since  1.1.0
     * @return array Sermon data.
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
     * Return the sermon speaker term data from our Sermon plugin.
     *
     * @since  1.1.0
     * @return array Sermon data.
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
     * Add 'player=video' query param to the give url.
     *
     * @since  1.1.0
     * @return string  Modified url
     */
    public static function get_watch_video_url( $url ) {

        return add_query_arg( 'player', 'video', $url );

    }

    /**
     * Add 'player=audio' query param to the give url.
     *
     * @since  1.1.0
     * @return string Modified url.
     */
    public static function get_listen_audio_url( $url ) {

        return add_query_arg( 'player', 'audio', $url );

    }

    /**
     * Return true if the url contains the query params 'player=video'.
     *
     * @since  1.1.0
     * @return bool
     */
    public static function is_player_video() {

        return ( ! empty( $_GET['player'] ) && ('video' === $_GET['player'] ) ) ? true : false;

    }

    /**
     * Return true if the url contains the query params 'player=audio'.
     *
     * @since  1.1.0
     * @return bool
     */
    public static function is_player_audio() {
    
        return ( ! empty( $_GET['player'] ) && ('audio' === $_GET['player'] ) ) ? true : false;

    }

    /**
     * Return the url of the current page.
     *
     * @since  1.1.0
     * @global $wp
     *
     * @return string    Url of current page minus any query params.
     */
    public static function get_current_page_url() {
    
        global $wp;

        $url = home_url( $wp->request );
        return $url;

    }

    /**
     * Returns the navigation needed for the Sermon 'Browes Dates' page.
     *
     * Returns a string ready to be inserted between <ul> tags.
     * 
     * @since  1.1.0
     * @global $wpdb
     *
     * @param  string   $active_year   The list item for this year will include a class 'fw-child-sermon-year-active'. 
     * @return string                  Navigation string.
     */
    public static function get_sermon_year_navigation( $active_year ) {

        global $wpdb;
        $list = array();
 
        $years = $wpdb->get_col( "SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sermon' ORDER BY post_date ASC" );

        foreach ( $years as $year ) {

            $url = self::set_url_sermon_year( $year );

            $class = ( $active_year === $year ) ? 'fw-child-sermon-year-active' : '';

            array_push( 
                $list, 
                '<li><a href="' . $url . '" class="' . $class . '">' . $year . '</a></li>'
            );

        }

        return join( '', $list );

    }

    /**
     * Return the given url with the year query parameter appended.
     *
     * @since  1.1.0
     *
     * @param  string  $year  Four-digit year.
     * @param  string  $url   Optional. Url. Uses current page url if not provided.
     * @return string         Url.
     */
    public static function set_url_sermon_year( $year, $url = null ) {

        $url = empty( $url ) ? self::get_current_page_url() : $url;

        return add_query_arg( 'sermon_year', $year, $url );

    }

    /**
     * Return the 'year' query parameter from the current sermon page url.
     *
     * @since  1.1.0
     *
     * @return string   Four-digit year or empty string.
     */
    public static function get_url_sermon_year() {
    
        return empty( $_GET['sermon_year'] ) ? '' : $_GET['sermon_year'];

    }

    /**
     * Returns the url necessary to display the sermon archives for the given year/month.
     *
     * Returned format: http://your-church-domain/2016/10/?post_type=sermon
     *
     * @since  1.1.0
     *
     * @param  string  $year          Four digit year.
     * @param  string  $month_number  e.g.: 1 through 12 (January - December).
     * @return string                 Url.
     */
    public static function get_sermon_archive_dated_url( $year, $month_number ) {

        $url = home_url() . '/' . $year . '/' . $month_number . '/?post_type=sermon';
        return $url;

    }

    /**
     * We've created our own paginated pages when displaying the sermon-series.php page template.
     * We don't want to display ALL sermon series on the one page. This gets lengthy for the user.
     * Also, because we are caching the sermon series data structure, we have to implement our
     * own pagination.
     *
     * To do this, we create the url /sermons/series?SERMON_QUERY_PARAM_PAGE_NUMBER={page number}.
     * We examine the page number to determine which sermon series set to display.
     *
     * @since  1.1.0
     * @return int   Page number
     */
    public static function get_current_page_number() {
    
        $page_number = ! empty( $_GET[self::SERMON_QUERY_PARAM_PAGE_NUMBER] ) 
            ? $_GET[self::SERMON_QUERY_PARAM_PAGE_NUMBER]
            : 1;

        return $page_number;

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
     * @since  1.1.0
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

    /*********************************
     * MAINTENANCE
     *********************************
    /**
     * This method is called by the do_action() registered in our constructor. 
     *
     * When a sermon is created, updated, or deleted, we'll want to delete our
     * transient holding our cached sermon series data structure.
     *
     * Note: save_post is called on Trash / Restore too, not just Add and Update (this is good)
     *
     * @since 1.1.0
     * @param int   $post_id The post ID.
     * @param post  $post    The post object.
     * @param bool  $update  Whether this is an existing post being updated or not.
     */
    public function delete_sermon_series_transient_on_sermon_post_type_change( $post_id, $post, $update ) {

        // Not on revisions
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        delete_transient( self::SERMON_SERIES_TRANSIENT_NAME );

    }

    /**
     * This method is called by the do_action() registered in our constructor. 
     *
     * When a sermon taxonomy term is created, updated, or deleted, we'll want to delete our
     * transient holding our cached sermon series data structure.
     *
     * @since 1.7.9
     */
    public function delete_sermon_series_transient_on_sermon_series_term_change() {

        delete_transient( self::SERMON_SERIES_TRANSIENT_NAME );

    }

}
