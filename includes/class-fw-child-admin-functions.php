<?php
/**
 * This file is invoked when the backend is viewed.
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class wrapper for all methods.
 *
 * @since 1.1.0
 */
class FW_Child_Admin_Functions {

    function __construct() {

        /*
         * If Visual Composer is installed, which we know it is,
         * add our own church portfolio widget to Visual Composer.
         */
        if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {

            add_action( 'init', array( $this, 'include_visual_composer_widget' ), 10 );

        }

    }

    /*
     * If Visual Composer is installed, which we know it is,
     * add our own church portfolio widget to Visual Composer.
     * our favicons to the head of both the front and backend.
     *
     * @since  1.1.0
     */
    public function include_visual_composer_widget() {

        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/fw-child-extend-vc.php', true );
    
    }

}
