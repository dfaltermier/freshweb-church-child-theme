<?php
/**
 * Child theme bootstrap file.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child {

    function __construct() { }

    /**
     * Kick it off
     */
    public function run() {

        $this->setup_constants();
        $this->includes();
    
    }

    /*
     * Setup global constants.
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
    }

    /**
     * Include required files
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
 */
function fw_child_run_theme() {

    $theme = new FW_Child();
    $theme->run();

}

fw_child_run_theme();
