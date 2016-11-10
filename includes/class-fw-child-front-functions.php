<?php
/**
 * This file provides functions for the frontend only.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Front_Functions {

    function __construct() {

        // The Bridge theme comes with a child theme template. It shows to register the add_action
        // method at '11' so that all parent stylesheets get loaded first.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );

        // Include files needed for the frontend.
        $this->includes();

    }

    /**
     * Enqueue our scripts and stylesheets.
     */
    public function enqueue_scripts() {

        // Add our scripts and stylesheets as long as we're not on the login page.
        if ( ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {

            wp_enqueue_style(
                'fw-child-theme-stylesheet', 
                FW_CHILD_THEME_CSS_URI . '/main.css', 
                array(),
                FW_CHILD_THEME_VERSION
            );

            wp_enqueue_style(
                'fw-child-theme-sermon-stylesheet', 
                FW_CHILD_THEME_CSS_URI . '/sermon.css', 
                array( 'fw-child-theme-stylesheet' ),
                FW_CHILD_THEME_VERSION
            );

        }

    }

    /**
     * Include required frontend files.
     */
    public function includes() {

        // Theme shortcodes.
        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-shortcodes.php', true );
        $shortcodes = new FW_Child_Shortcodes();

        // Common helper functions.
        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-common-functions.php', true );

        // Sermon helper functions.
        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-sermon-functions.php', true );
    
    }

}
