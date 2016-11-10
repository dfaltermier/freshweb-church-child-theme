<?php
/**
 * This file provides functions for the frontend and backend.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Core_Functions {

    function __construct() {

        // Add our favicons to the head of both the front and backend.
        add_action( 'wp_head',    array( $this, 'add_favicons' ) );
        add_action( 'admin_head', array( $this, 'add_favicons' ) );

    }

    /**
     * Add our favicons to the head of both the front and backend.
     */
    public function add_favicons() {
        
        echo '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />' . "\n";
        echo '<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />' . "\n";
        echo '<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />' . "\n";
        echo '<link rel="manifest" href="/manifest.json" />' . "\n";
        echo '<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5" />' . "\n";
        echo '<meta name="theme-color" content="#ffffff" />' . "\n";

    }

}
?>