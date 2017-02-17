<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_File_Functions {

    function __construct() {

    }

    /**
     * Retrieves the list of common file extensions and their types.
     *
     * @since 4.6.0
     *
     * @return array Array of file extensions types keyed by the type of file.
     */
    public static function get_allowed_file_extensions_and_mime_types() {

        /**
         * Filters file type based on the extension name.
         *
         * @since 2.5.0
         *
         * @see wp_ext2type()
         *
         * @param array $ext2type Multi-dimensional array with extensions for a default set
         *                        of file types.
         */
        return array(
            'image'       => array( 'jpg', 'jpeg', 'jpe', 'gif',  'png',  'bmp', 'tif', 'tiff', 'ico' ),
            'audio'       => array( 'aac', 'ac3', 'aif', 'aiff', 'm3a', 'm4a', 'm4b', 'mka',
                                    'mp1', 'mp2', 'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma' ),
            'video'       => array( '3g2', '3gp', '3gpp', 'asf', 'avi',  'divx', 'dv', 'flv', 'm4v', 'webm',
                                    'mkv',  'mov',  'mp4',  'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt', 'rm', 'vob', 'wmv' ),
            'document'    => array( 'doc', 'docx', 'docm', 'dotm', 'odt',  'pages', 'pdf', 
                                    'xps', 'oxps', 'rtf',  'wp', 'wpd', 'psd', 'xcf' ),
            'spreadsheet' => array( 'numbers', 'ods', 'xls', 'xlsx', 'xlsm', 'xlsb' ),
            'slideset'    => array( 'swf', 'ppt', 'pptx', 'pptm', 'pps', 'ppsx', 'ppsm', 'sldx', 'sldm', 'odp' ),
            'text'        => array( 'asc', 'csv', 'tsv', 'txt' ),
            'archive'     => array( 'bz2', 'cab', 'dmg', 'gz', 'rar', 'sea', 'sit', 'sqx', 'tar', 'tgz', 'zip', '7z' )
        );
    }

    /**
     * Validates a filename to ensure it does not contain any path info.
     *
     * @param string $filename
     * @return bool  True if basename is valid.
     */
    public static function is_valid_basename( $filename ) {

        if ( ( false === strpos( $filename, '..' ) ) &&
             ( false === strpos( $filename, '/' ) ) &&
             ( false === strpos( $filename, ':' ) ) ) {  // e.g.: prevents C:filename
            return true;
        }

        return false;
    }

    /**
     * Retrieve the file type from the filename.
     *
     * You can optionally define the mime array, if needed.
     *
     * @since 
     *
     * @param string $filename File name or path.
     * @param array  $mimes    Optional. Key is the file extension with value as the mime type.
     * @return array Values with extension first and mime type.
     */
    public static function get_file_type( $filename, $mimes = null ) {

        if ( empty( $mimes ) ) {
            $mimes = self::get_allowed_file_extensions_and_mime_types();
        }

        $type = false;
        $extension = false;

        foreach ( $mimes as $file_type => $file_extensions ) {

            foreach ( $file_extensions as $file_extension ) {
                
                $file_extension_preg = '/\.(' . $file_extension . ')$/i';
            
                if ( preg_match( $file_extension_preg, $filename, $matches ) ) {
                    $type      = $file_type;
                    $extension = $matches[1];
                    break;
                }
            }
        }

        return array( 'extension' => $extension, 'type' => $type );
    }
        
    /**
     * Retrieve the filename from a local url.
     *
     * Used primarily to extract a filename to be downloaded locally.
     *
     * @since  0.9
     * @param  string $url URL
     * @return string filename or empty string
     */
    public static function get_filename_from_local_url( $url ) {

        if ( FW_Child_Common_Functions::is_local_url( $url ) ) {

            $url_path = parse_url( $url, PHP_URL_PATH );

            if ( ! empty( $url_path ) ) {

                $filename = basename( $url_path ); // Get the filename and any query string.

                // Remove any query string.
                if ( false !== strpos( $filename , '?' ) ) {
                    $filename = substr( $filename , 0, strpos( $filename , '?' ) ); 
                }

                if ( FW_Child_File_Functions::is_valid_basename( $filename ) ) {

                    return $filename;

                }

            }

        }

        return '';

    }

    /**
     * Returns true if the given file exists in the /uploads folder.
     *
     * @since  0.9
     * @param  string $filename  Filename
     * @return bool
     */
    public static function is_filename_exists_in_uploads_folder( $filename ) {

        if ( ! empty( $filename ) ) {

            // Path to file in uploads folder
            $upload_dir = wp_upload_dir();
            $upload_file_path = $upload_dir['basedir'] . '/' . $filename;

            // True if file exists in uploads folder
            return file_exists( $upload_file_path );

        }

        return false;

    }


}
