<?php
/**
 * Displays the sermons by blocks of related dates
 *
 * WordPress loads this partial file with a url similar to:
 *     http://your-church-domain/sermons/dates/
 *
 * If the page url is given the following query paramter:
 *     http://your-church-domain/sermons/dates/?sermon_year=2016
 * Then we display the sermons for only the given year.
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

/*
 * Look into the url query for a year string. If given, then display the sermons
 * for only this year. Otherwise, use the current year from today's date.
 */
$selected_year = FW_Child_Sermon_Functions::get_url_sermon_year();
$selected_year = ! empty( $selected_year ) ? $selected_year : date( 'Y' );

/*
 * Get the list of years for ALL sermons. We'll use this list for our navigation.
 *
 * Returns string:
 *    <li><a href="http://<domain-name>/sermons/dates?sermon_year=2016" 
 *           class="fw-child-sermon-year-active">2016</a></li>
 *    <li><a href="http://<domain-name>/sermons/dates?sermon_year=2017" 
 *           class="">2017</a></li>
 */
$year_navigation = FW_Child_Sermon_Functions::get_sermon_year_navigation( $selected_year );

/*
 * Get the sermons for all months in the given year.
 *
 * Returns array:
 *     $months = array(
 *         '1' => array(
 *             'ID'             => get_the_ID(),
 *             'post_date'      => get_the_date( get_option( 'date_format' ) ),
 *             'post_title'     => get_the_title(),
 *             'post_permalink' => get_permalink()
 *         ),
 *         ...
 *     );
 */
$months = FW_Child_Sermon_Functions::get_sermons_by_date( $selected_year );
$sermon_counter = 0;

?>

<div class="fw-child-sermon-archives fw-child-clearfix">

    <?php if ( ! empty( $months ) ) : ?>

        <ul class="fw-child-sermon-archives-year-navigation">
          
            <?php echo $year_navigation; ?>
            
        </ul>

        <div class="fw-child-sermon-archives-year-navigation-separator"></div>
 
        <ul class="fw-child-sermon-archives-list">

            <?php foreach ( $months as $month_number => $sermons ) : ?>

                <?php
                // We must not leave any whitespace between the closing 'li' tag and the
                // beginning of the next in order for the page flow to work with the set widths.
                ?>
                <?php if ( $sermon_counter > 0 ) { echo '</li>'; } ?><li class="fw-child-sermon-archives-entry">

                <?php 
                $sermon_counter++; 
                $image_url = FW_CHILD_THEME_IMAGE_URI . '/sermon-season-' . $month_number . '-min.jpg';
        
                // Get the url to the sermon archive for the whole month.
                $archive_url = FW_Child_Sermon_Functions::get_sermon_archive_dated_url( $selected_year, $month_number );
                ?>

                <div class="fw-child-sermon-archives-entry-image-container">
                    <a href="<?php echo esc_url( $archive_url ); ?>"><img 
                       src="<?php echo esc_url( $image_url ); ?>"
                       alt="" /></a>
                </div>

                <?php foreach ( $sermons as $sermon ) : ?>

                    <?php if ( ! empty( $sermon['post_title'] ) ) : ?>

                        <div class="fw-child-sermon-archives-entry-title">
                            <a href="<?php echo esc_url( $sermon['post_permalink'] ); ?>">
                                <h2><?php echo esc_html( $sermon['post_title'] ); ?></h2>
                            </a>
                        </div>

                    <?php endif; ?>

                    <?php if ( ! empty( $sermon['post_date'] ) ) : ?>

                        <div class="fw-child-sermon-archives-entry-date">
                            <?php echo esc_html( $sermon['post_date'] ); ?>
                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            <?php endforeach; ?>

            </li>
        </ul>

    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon archives to display for <?php echo esc_html( $selected_year ); ?></div>

    <?php endif; ?>

</div>
