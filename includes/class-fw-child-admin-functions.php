<?php
/**
 * This file provides functions for the backend only.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Admin_Functions {

    function __construct() {

        // If Visual Composer is installed, which we know it is,
        // add our own church portfolio widget to Visual Composer.
        if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {

            add_action( 'init', array( $this, 'include_visual_composer_widget' ), 10 );

        }

    }

    /**
     * Add our favicons to the head of both the front and backend.
     */
    public function include_visual_composer_widget() {

        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/fw-child-extend-vc.php', true );
    
    }

}
?>