<?php
/**
 * Bootstrapping WordPress Theme functions file
 *
 * This file is used by WordPress to kick-start our theme.
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
 * Bootstrapping class.
 *
 * All of our theme dependencies are initalized here. This class is instantiated below.
 *
 * @since 1.1.0
 */
class FW_Child {

    function __construct() { }

    /**
     * Run our initialization.
     *
     * @since 1.1.0
     */
    public function run() {

        $this->setup_constants();
        $this->includes();
    
    }

    /**
     * Define global constants.
     *
     * @since 1.1.0
     */
    private function setup_constants() {

        $theme = wp_get_theme();

        // Theme version
        if ( ! defined( 'FW_CHILD_THEME_VERSION' ) ) {
            define( 'FW_CHILD_THEME_VERSION', $theme->Version );
        }

        // Theme directory
        if ( ! defined( 'FW_CHILD_THEME_PATH' ) ) {
            define( 'FW_CHILD_THEME_PATH', get_stylesheet_directory() );
        }

        // Theme uri
        if ( ! defined( 'FW_CHILD_THEME_URI' ) ) {
            define( 'FW_CHILD_THEME_URI', get_stylesheet_directory_uri() );
        }

        // CSS uri
        if ( ! defined( 'FW_CHILD_THEME_CSS_URI' ) ) {
            define( 'FW_CHILD_THEME_CSS_URI', FW_CHILD_THEME_URI . '/css' );
        }

        // JS uri
        if ( ! defined( 'FW_CHILD_THEME_JS_URI' ) ) {
            define( 'FW_CHILD_THEME_JS_URI', FW_CHILD_THEME_URI . '/js' );
        }

        // Images uri
        if ( ! defined( 'FW_CHILD_THEME_IMAGE_URI' ) ) {
            define( 'FW_CHILD_THEME_IMAGE_URI', FW_CHILD_THEME_URI . '/images' );
        }

        // Includes directory
        if ( ! defined( 'FW_CHILD_THEME_INCLUDES_DIR' ) ) {
            define( 'FW_CHILD_THEME_INCLUDES_DIR', 'includes' );
        }

        // Partials directory
        if ( ! defined( 'FW_CHILD_THEME_PARTIALS_DIR' ) ) {
            define( 'FW_CHILD_THEME_PARTIALS_DIR', 'partials' );
        }

        /*
         * Define the file path and uri to our client-specific stylesheet, which may or may not exist.
         * Normally, this stylesheet is maintained in a pseudo-theme folder created just for the purpose
         * of holding this file.
         */
        if ( ! defined( 'FW_CHILD_CLIENT_CSS_FILE_PATH' ) ) {
            $root_path = dirname( FW_CHILD_THEME_PATH );
            define( 'FW_CHILD_CLIENT_CSS_FILE_PATH', $root_path . '/bridge-child-client/css/client.css' );
        }

        if ( ! defined( 'FW_CHILD_CLIENT_CSS_FILE_URI' ) ) {
            $root_uri = dirname( FW_CHILD_THEME_URI );
            define( 'FW_CHILD_CLIENT_CSS_FILE_URI', $root_uri . '/bridge-child-client/css/client.css' );
        }

    }

    /**
     * Include required files.
     *
     * @since 1.1.0
     */
    private function includes() {

        // Functions common to the front and back end.
        locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-core-functions.php', true );
        $core_functions = new FW_Child_Core_Functions();

        if ( is_admin() ) {

            locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-admin-functions.php', true );
            $admin_functions = new FW_Child_Admin_Functions();

        }
        else {

            locate_template( FW_CHILD_THEME_INCLUDES_DIR . '/class-fw-child-front-functions.php', true );     
            $front_functions = new FW_Child_Front_Functions();

        }

    }

}

/**
 * Begin execution of the theme.
 *
 * @since 1.1.0
 */
function fw_child_run_theme() {

    $theme = new FW_Child();
    $theme->run();

}

// Kick things off.
fw_child_run_theme();

