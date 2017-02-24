<?php
/**
 * This file contains utility methods for downloading video/audio files.
 * This feature is available on pages displaying single sermons.
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.
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
 * Class wrapper for all methods.
 *
 * @since 1.1.0
 */
class FW_Child_Download_Functions {

    function __construct() {

        // Certain url structures instruct us to download files. See method for detalls.
        add_action( 'template_redirect', array( $this, 'download' ) );

    }

    /**
     * Force download of certain file types from the WordPress /uploads folder.
     * Expected url query string: ?download=filename.ext
     *
     * This prompts "Save As" -- handy for MP3, PDF, etc. Only works on local files.
     *
     * This information was useful: http://wordpress.stackexchange.com/questions/3480/how-can-i-force-a-file-download-in-the-wordpress-backend
     *
     * @since  1.1.0
     * @global object $wp_query
     */    
    public function download() {

        global $wp_query;

        // Check if this URL is a request for file download
        if ( is_front_page() && ! empty( $_GET['download'] ) ) {

            // Get filename
            $filename = $_GET['download'];
            $filename = basename( $filename ); // Ensure we only have a filename and not path.

            // Prevent any path traversal attacks.
            if ( FW_Child_File_Functions::is_valid_basename( $filename ) ) {

                // Make sure file has the allowed extension.
                $file_type = FW_Child_File_Functions::get_file_type( $filename );

                if ( ! empty( $file_type['extension'] ) ) {

                    // Path to file in uploads folder
                    $upload_dir = wp_upload_dir();
                    $upload_file_path = $upload_dir['basedir'] . '/' . $filename;

                    // File exists in uploads folder?
                    if ( file_exists( $upload_file_path ) ) {

                        // headers to prompt "Save as"
                        $filesize = filesize( $upload_file_path );
                        header( 'Content-Type: application/octet-stream', true, 200 ); // replace WordPress 404 Not Found with 200 Okay
                        header( 'Content-Disposition: attachment; filename=' . $filename );
                        header( 'Expires: 0' );
                        header( 'Cache-Control: must-revalidate' );
                        header( 'Pragma: public' );
                        header( 'Content-Length: ' . $filesize );

                        // clear buffering just in case
                        @ob_end_clean();
                        flush();

                        @readfile( $upload_file_path );

                        // we're done, stop further execution
                        exit;

                    }

                }

            }

            // Failure of any type results in 404 file not found.
            $wp_query->set_404();
            status_header( 404 );

        }

    }

    /**
     * For local urls, convert the given url to one that is formatted to 
     * trigger a file download. If the file does not exist in the /uploads
     * folder, then return an empty string. External urls are returned as-is.
     *
     * Makes this:  http://yourname.com/?download=myfile.mp4
     * Out of:      http://yourname.com/wp-content/uploads/myfile.mp4
     *
     * @since  1.1.0
     * @param  string $url URL
     * @return string Download URL, empty string, or original url
     */
    public static function get_download_url( $url ) {

        $download_url = '';

        if ( FW_Child_Common_Functions::is_local_url( $url ) ) {

            $filename = FW_Child_File_Functions::get_filename_from_local_url( $url );

            If ( ! empty( $filename ) ) {

                // Make sure file has the allowed extension.
                $file_type = FW_Child_File_Functions::get_file_type( $filename );

                if ( ! empty( $file_type['extension'] ) ) {

                    // Ensure that the file exists in uploads folder
                    if ( FW_Child_File_Functions::is_filename_exists_in_uploads_folder( $filename ) ) {

                        // Add ?download=filename.ext to site URL
                        $download_url = home_url( '/' ) . '?download=' . urlencode( $filename ) . '&nocache';

                    }

                }

            }

        }
        else {

            // Return original url
            $download_url = $url;

        }

        return $download_url;

    }

}

