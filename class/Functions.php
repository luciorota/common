<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/*
 * common module
 *
 * @copyright       XOOPS Project https://xoops.org/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         common
 * @since           1.00
 * @author          luciorota, studiopas
 * @version         svn:$Id$
 */

namespace XoopsModules\Common;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

if (!defined('COMMON_FUNCTIONS_INCLUDED')) {
    define('COMMON_FUNCTIONS_INCLUDED', 1);

    /**
     * XoopsLists
     *
     * @author              John Neill <catzwolf@xoops.org>
     * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
     * @package             kernel
     * @subpackage          form
     * @access              public
     */
    class Functions
    {

        /**
         *
         * Filesystem functions
         *
         */

        /**
         * Create a new directory that contains the file index.html
         *
         * @param string            $dir is the directory to create
         *
         * @return bool             Returns TRUE on success or FALSE on failure
         */
        public static function makeDir($dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir)) {
                    return false;
                } else {
                    if ($fh = @fopen($dir . '/index.html', 'w')) {
                        fwrite($fh, '<script>history.go(-1);</script>');
                    }
                    @fclose($fh);
                    return true;
                }
            }
        }
        /**
         * Create a new directory that contains the file index.html
         *
         * @param string            $source is the original directory
         * @param string            $destination is the destination directory
         *
         * @return bool             Returns TRUE on success or FALSE on failure
         *
         */
        public static function copyDir($source, $destination) {
            if ( !$dir = opendir( $source ) ) {
                return false;
            }
            @mkdir($destination);
            while ( false !== ( $file = readdir( $dir ) ) ) {
                if ( ( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir( $source . '/' . $file ) ) {
                        if ( !copyDir( $source . '/' . $file, $destination . '/' . $file ) ) {
                            return false;
                        }
                    } else {
                        if ( !copy($source . '/' . $file, $destination . '/' . $file ) ) {
                            return false;
                        }
                    }
                }
            }
            closedir($dir);
            return true;
        }
        /**
         * Copy a file
         *
         * @param string            $source is the original directory
         * @param string            $destination is the destination directory
         *
         * @return bool             TRUE on success or FALSE on failure
         */
        public static function copyFile($source, $destination) {
            // Simple copy for a file
            if (is_file($source)) {
                return copy($source, $destination);
            } else {
                return false;
            }
        }
        /**
         * Delete a not empty directory
         *
         * @param string            $dir is the directory to delete
         * @param boll              $if_not_empty if FALSE it delete directory only if false
         *
         * @return bool             Returns TRUE on success or FALSE on failure
         */
        public static function delDir($dir, $if_not_empty = true) {
            if ( !file_exists( $dir ) ) {
                return true;
            }
            if ( $if_not_empty == true ) {
                if ( !is_dir( $dir ) ) {
                    return unlink($dir);
                }
                foreach ( scandir( $dir ) as $item ) {
                    if ($item == '.' || $item == '..') {
                        continue;
                    }
                    if ( !delDir( $dir . '/' . $item ) ) {
                        return false;
                    }
                }
            } else {
                // NOP
            }
            return rmdir($dir);
        }
        /**
         * Delete a file
         *
         * @param string            $path is the file absolute path
         *
         * @return  bool            TRUE on success or FALSE on failure
         */
        public static function delFile($path) {
            if (is_file($path)) {
                @chmod($path, 0777);
                return @unlink($path);
            } else {
                return false;
            }
        }
        /**
         * Get a list of files in a directory
         *
         * @param string            $dir directory name containing the files to be listed.
         *
         * @return string[]         array of file names
         */
        public static function getFileList($dir) {
            \XoopsLoad::load('xoopslists');
            $files = \XoopsLists::getFileListAsArray($dir);
            return $files;
        }

        /**
         * @param string            $path
         *
         * @return array
         */
        public static function getFiles($path = '.') {
            $files = array();
            $dir = opendir($path);
            while ($file = readdir($dir)) {
                if (is_file($path . $file)) {
                    if ($file != '.' && $file != '..' && $file != 'blank.gif' && $file != 'index.html') {
                        $files[] = $file;
                    }
                }
            }
            return $files;
        }

        /**
         * This function will read the full structure of a directory.
         * It's recursive because it doesn't stop with the one directory,
         * it just keeps going through all of the directories in the folder you specify.
         *
         * @param string            $path path to the directory to make
         * @param int               $level
         *
         * @return array
         */
        public static function getDirTree($path = '.', $level = 0) {
            $ret = array();
            $ignore = array('cgi-bin', '.', '..');
            // Directories to ignore when listing output. Many hosts will deny PHP access to the cgi-bin.
            $dirHandler = opendir($path);
            // Open the directory to the handle $dirHandler
            while (false !== ($file = readdir($dirHandler))) {
                // Loop through the directory
                if (!in_array($file, $ignore)) {
                    // Check that this file is not to be ignored
                    $spaces = str_repeat('&nbsp;', ($level * 4));
                    // Just to add spacing to the list, to better show the directory tree.
                    if (is_dir("$path/$file")) {
                        // Its a directory, so we need to keep reading down...
                        $ret[] = "<strong>{$spaces} {$file}</strong>";
                        $ret = array_merge($ret, getDirTree($path . DIRECTORY_SEPARATOR . $file, ($level + 1)));
                        // Re-call this same function but on a new directory.
                        // this is what makes function recursive.
                    } else {
                        $ret[] = "{$spaces} {$file}";
                        // Just print out the filename
                    }
                }
            }
            closedir($dirHandler);
            // Close the directory handle
            return $ret;
        }

        /**
         *
         * Xoops functions
         *
         */

        /**
         * Check if a module exist and return module verision
         * @author luciorota
         * @param string            $dirname
         *
         * @return boolean|int      FALSE if module is not installed or not active, module version if module is installed
         */
        public static function checkModule($dirname) {
            if (!xoops_isActiveModule($dirname)) {
                return false;
            }
            $module_handler = xoops_getHandler('module');
            $module = $module_handler->getByDirname($dirname);
            return $module->getVar('version');
        }

        /**
         *
         * Useful functions
         *
         */

        /**
         * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
         * www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags
         * www.cakephp.org
         *
         * @param string  $text         String to truncate.
         * @param integer $length       Length of returned string, including ellipsis.
         * @param string  $ending       Ending to be appended to the trimmed string.
         * @param boolean $exact        If false, $text will not be cut mid-word
         * @param boolean $considerHtml If true, HTML tags would be handled correctly
         *
         * @return string Trimmed string.
         */
        public static function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
            if ($considerHtml) {
                // if the plain text is shorter than the maximum length, return the whole text
                if (strlen(preg_replace('/<.*?'.'>/', '', $text)) <= $length) {
                    return $text;
                }
                // splits all html-tags to scanable lines
                preg_match_all('/(<.+?'.'>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
                $total_length = strlen($ending);
                $open_tags    = array();
                $truncate     = '';
                foreach ($lines as $line_matchings) {
                    // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                    if (!empty($line_matchings[1])) {
                        // if it's an "empty element" with or without xhtml-conform closing slash
                        if (preg_match(
                            '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is',
                            $line_matchings[1]
                        )
                        ) {
                            // do nothing
                            // if tag is a closing tag
                        } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                            // delete tag from $open_tags list
                            $pos = array_search($tag_matchings[1], $open_tags);
                            if ($pos !== false) {
                                unset($open_tags[$pos]);
                            }
                            // if tag is an opening tag
                        } elseif (preg_match('/^<\s*([^\s>!]+).*?'.'>$/s', $line_matchings[1], $tag_matchings)) {
                            // add tag to the beginning of $open_tags list
                            array_unshift($open_tags, strtolower($tag_matchings[1]));
                        }
                        // add html-tag to $truncate'd text
                        $truncate .= $line_matchings[1];
                    }
                    // calculate the length of the plain text part of the line; handle entities as one character
                    $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                    if ($total_length + $content_length > $length) {
                        // the number of characters which are left
                        $left            = $length - $total_length;
                        $entities_length = 0;
                        // search for html entities
                        if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                            // calculate the real length of all entities in the legal range
                            foreach ($entities[0] as $entity) {
                                if ($entity[1] + 1 - $entities_length <= $left) {
                                    $left--;
                                    $entities_length += strlen($entity[0]);
                                } else {
                                    // no more characters left
                                    break;
                                }
                            }
                        }
                        $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                        // maximum lenght is reached, so get off the loop
                        break;
                    } else {
                        $truncate .= $line_matchings[2];
                        $total_length += $content_length;
                    }
                    // if the maximum length is reached, get off the loop
                    if ($total_length >= $length) {
                        break;
                    }
                }
            } else {
                if (strlen($text) <= $length) {
                    return $text;
                } else {
                    $truncate = substr($text, 0, $length - strlen($ending));
                }
            }
            // if the words shouldn't be cut in the middle...
            if (!$exact) {
                // ...search the last occurance of a space...
                $spacepos = strrpos($truncate, ' ');
                if (isset($spacepos)) {
                    // ...and cut the text in this position
                    $truncate = substr($truncate, 0, $spacepos);
                }
            }
            // add the defined ending to the text
            $truncate .= $ending;
            if ($considerHtml) {
                // close all unclosed html-tags
                foreach ($open_tags as $tag) {
                    $truncate .= '</' . $tag . '>';
                }
            }

            return $truncate;
        }

        /**
         * Transforms a numerical size (like 2048) to a letteral size (like 2MB)
         *
         * @param int               $bytes numerical size
         * @param int               $precision
         * @return string           letteral size
         **/
        public static function bytesToSize($bytes, $precision = 2) {
            // human readable format -- powers of 1000
            $units = array('b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb');
            return round($bytes / pow(1000, ($i = floor(log($bytes, 1000)))), $precision) . ' ' . $units[(int) $i];
        }

        /**
         * @param int               $bytes
         * @param int               $precision
         * @return string
         */
        public static function bytesToSize1024($bytes, $precision = 2) {
            // Human readable format -- powers of 1024
            $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');
            return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision ) . ' ' . $units[(int) $i];
        }

        /**
         * Transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
         *
         * @param string            $size letteral size
         * @return int              numerical size
         **/
        public static function sizeToBytes1024($size) {
            $l   = substr($size, -1);
            $ret = substr($size, 0, -1);
            switch (strtoupper($l)) {
                case 'E':
                case 'e':
                    $ret *= 1024;
                    break;
                case 'P':
                case 'p':
                    $ret *= 1024;
                    break;
                case 'T':
                case 't':
                    $ret *= 1024;
                    break;
                case 'G':
                case 'g':
                    $ret *= 1024;
                    break;
                case 'M':
                case 'm':
                    $ret *= 1024;
                    break;
                case 'K':
                case 'k':
                    $ret *= 1024;
                    break;
            }
            return $ret;
        }

        /**
         * @param int       $amount is… how much of $what you want.
         * @param string    $what is either paras, words, bytes or lists.
         * @param int       $start is whether or not to start the result with ‘Lorem ipsum dolor sit amet…‘
         *
         * @return false|\SimpleXMLElement
         */
        public static function randomLipsum($amount = 1, $what = 'paras', $start = 0) {
            //$ret = file_get_contents('http://loripsum.net/api')
            $ret = simplexml_load_file("http://www.lipsum.com/feed/xml?amount=$amount&what=$what&start=$start")->lipsum;
            return $ret;
        }

// in progress
        /**
         * @param           $filePath
         * @param bool      $isBinary
         * @param bool      $retBytes
         *
         * @return bool|int
         */
        public function download($filePath, $isBinary = true, $retBytes = true)
        {
            // how many bytes per chunk
            //$chunkSize = 1 * (1024 * 1024);
            $chunkSize    = 8 * (1024 * 1024); //8MB (highest possible fread length)
            $buffer       = '';
            $bytesCounter = 0;

            if ($isBinary == true) {
                $handler = fopen($filePath, 'rb');
            } else {
                $handler = fopen($filePath, 'r');
            }
            if ($handler === false) {
                return false;
            }
            while (!feof($handler)) {
                $buffer = fread($handler, $chunkSize);
                echo $buffer;
                ob_flush();
                flush();
                if ($retBytes) {
                    $bytesCounter += strlen($buffer);
                }
            }
            $status = fclose($handler);
            if ($retBytes && $status) {
                return $bytesCounter; // return num. bytes delivered like readfile() does.
            }

            return $status;
        }

// in progress
        /**
         * @param           $html
         *
         * @return string
         * @throws Html2TextException
         */
        public function html2text($html)
        {
            include_once COMMON_ROOT_PATH . '/assets/php/html2text/html2text.php';
            //
            return convert_html_to_text($html);
        }

// in progress
        /**
         * @author     Jack Mason
         * @website    volunteer @ http://www.osipage.com, web access application and bookmarking tool.
         * @copyright  Free script, use anywhere as you like, no attribution required
         * @created    2014
         * The script is capable of downloading really large files in PHP. Files greater than 2GB may fail in 32-bit windows or similar system.
         * All incorrect headers have been removed and no nonsense code remains in this script. Should work well.
         * The best and most recommended way to download files with PHP is using xsendfile, learn
         * more here: https://tn123.org/mod_xsendfile/
         *
         * @param           $filePath
         * @param           $fileMimetype
         */
        public function largeDownload($filePath, $fileMimetype)
        {
            /* You may need these ini settings too */
            set_time_limit(0);
            ini_set('memory_limit', '512M');
            if (!empty($filePath)) {
                $fileInfo            = pathinfo($filePath);
                $fileName            = $fileInfo['basename'];
                $fileExtrension      = $fileInfo['extension'];
                $default_contentType = 'application/octet-stream';
                // to find and use specific content type, check out this IANA page : http://www.iana.org/assignments/media-types/media-types.xhtml
                if ($fileMimetype = !'') {
                    $contentType = $fileMimetype;
                } else {
                    $contentType = $default_contentType;
                }
                if (file_exists($filePath)) {
                    $size   = filesize($filePath);
                    $offset = 0;
                    $length = $size;
                    //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
                    if (isset($_SERVER['HTTP_RANGE'])) {
                        preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                        $offset  = (int)$matches[1];
                        $length  = (int)$matches[2] - $offset;
                        $fhandle = fopen($filePath, 'r');
                        fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request
                        $data = fread($fhandle, $length);
                        fclose($fhandle);
                        header('HTTP/1.1 206 Partial Content');
                        header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size);
                    }//HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
                    //USUAL HEADERS FOR DOWNLOAD
                    header('Content-Disposition: attachment;filename=' . $fileName);
                    header('Content-Type: ' . $contentType);
                    header('Accept-Ranges: bytes');
                    header('Pragma: public');
                    header('Expires: -1');
                    header('Cache-Control: no-cache');
                    header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
                    header('Content-Length: ' . filesize($filePath));
                    $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)
                    if ($size > $chunksize) {
                        $handle = fopen($_FILES['file']['tmp_name'], 'rb');
                        $buffer = '';
                        while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) {
                            $buffer = fread($handle, $chunksize);
                            print $buffer;
                            ob_flush();
                            flush();
                        }
                        if (connection_status() !== CONNECTION_NORMAL) {
                            //TODO traslation
                            echo 'Connection aborted';
                        }
                        fclose($handle);
                    } else {
                        ob_clean();
                        flush();
                        readfile($filePath);
                    }
                } else {
                    //TODO traslation
                    echo 'File does not exist!';
                }
            } else {
                //TODO traslation
                echo 'There is no file to download!';
            }
        }












    }
}
