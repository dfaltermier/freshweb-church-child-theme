<?php
/**
 * Download Functions
 *
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Download_Functions {

    /**
     * 
     *
     */
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

            // Failure of any type results in 404 file not found
            $wp_query->set_404();
            status_header( 404 );

        }

    }

    /**
     * Get download URL
     *
     * If URL is local and theme supports 'ctfw-force-downloads', it will be piped through script to send "Save As" headers.
     * Otherwise, original URL will be returned (local or external) but only if it has an extension (ie. not URL to YouTube, SoundCloud, etc.)
     *
     * On <a> tags use download="download" attribute to attempt "Save As" for externally hosted files.
     * As of October, 2015, download attribute works on 60% browser use. When near 100%, will deprecate ctfw_force_download_url().
     *
     * Makes this:  http://yourname.com/?download=myfile.mp4
     * Out of:      http://yourname.com/wp-content/uploads/myfile.mp4
     *
     * @since 
     * @param  string $url URL for file
     * @return string URL modified to force Save As if local or as is if external and has extension
     */
    public static function get_download_url( $url ) {

        $download_url = $url;

        // Has extension? If not, is not actual file (may be URL to YouTube, etc.)
        $file_type = FW_Child_File_Functions::get_file_type( $download_url );

        if ( empty( $file_type['extension'] ) ) {

            // Return nothing; there is no file to download
            $download_url = '';

        } else {

            // If local file, force "Save As" headers by piping via special URL
            $download_url = self::force_download_url( $download_url );

        }

        return $download_url;

    }

    /**
     * Convert download URL to one that forces "Save As" via headers
     *
     * This keeps the browser from doing what it wants with the file (such as play MP3 or show PDF).
     * Note that file must be in uploads folder and extension must be allowed by WordPress.
     *
     * See ctfw_download_url() which uses this. Use it with download="download" attribute as fallback.
     * This function will be deprecated when near 100% browser support exists for the attribute.
     *
     * Makes this:  http://yourname.com/?download=myfile.mp4
     * Out of:      http://yourname.com/wp-content/uploads/myfile.mp4
     *
     * @since  0.9
     * @param  string $url URL for file
     * @return string URL forcing "Save As" on file if local
     */
    public static function force_download_url( $url ) {

        $download_url = $url;

        if ( FW_Child_Common_Functions::is_local_url( $download_url ) ) {

            $url_path = parse_url( $download_url, PHP_URL_PATH );

            if ( ! empty( $url_path ) ) {

                $filename = basename( $url_path ); // Get the filename and any query string.
                $filename = substr( $filename , 0, strpos( $filename , '?' ) ); // Remove any query string.

                if ( FW_Child_File_Functions::is_valid_basename( $filename ) ) {

                    // Add ?download=file to site URL
                    $download_url = home_url( '/' ) . '?download=' . urlencode( $filename ) . '&nocache';

                }
            }

        }

        return $download_url;

    }

}


