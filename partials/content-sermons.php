<?php
/**
 * Display the list of sermons. Used my 'Sermons' menu option.
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

<?php get_header(); ?>

<div class="full_width">

    <div class="full_width_inner">

        <section class="fw-child-sermon-section">

            <?php

            // Display header banner with page title.
            get_template_part( FW_CHILD_THEME_PARTIALS_DIR . '/content-header-banner' );

            // Display sermon navigation icons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-navigation'
            );
 
            // Display sermons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-loop'
            );

            // Display pagination.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-pagination'
            );

            ?>

        </section>

    </div>

</div>

<?php get_footer(); ?>


