<?php
/**
 * This file is invoked when both the frontend and backend is viewed.
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
class FW_Child_Core_Functions {

    function __construct() {

        // Add our favicons to the head of both the front and backend.
        add_action( 'wp_head',    array( $this, 'add_favicons' ) );
        add_action( 'admin_head', array( $this, 'add_favicons' ) );

    }

    /**
     * Add our favicons to the head of both the front and backend.
     *
     * @since  1.1.0
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
