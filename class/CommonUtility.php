<?php

namespace XoopsModules\Common;

use Xmf\Debug;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');



/**
 * Class CommonUtility
 */
class CommonUtility extends \XoopsObject {

    /**
     * Transforms a numerical size (like 2048) to a letteral size (like 2MB)
     *
     * @param int               $bytes numerical size
     * @param int               $precision
     * @return string           letteral size
     * */
    public static function bytesToSize1000($bytes, $precision = 2) {
        // human readable format -- powers of 1000
        $units = array('b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb');
        return @round(
                        $bytes / pow(1000, ($i = floor(log($bytes, 1000)))), $precision
                ) . ' ' . $units[(int) $i];
    }

    /**
     * @param int               $bytes
     * @param int               $precision
     * @return string
     */
    public static function bytesToSize1024($bytes, $precision = 2) {
        if ($bytes <= 0) {
            return '0 B';
        }
        // Human readable format -- powers of 1024
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');
        return @round(
                        $bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision
                ) . ' ' . $units[(int) $i];
    }

    /**
     * Transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     *
     * @param string            $size letteral size
     * @return int              numerical size
     * */
    public static function sizeToBytes1024($size) {
        $l = substr($size, -1);
        $ret = substr($size, 0, -1);
        switch (strtoupper($l)) {
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
     *
     * Filesystem functions
     *
     */

    /**
     * This function will read the full structure of a directory.
     * It's recursive because it doesn't stop with the one directory,
     * it just keeps going through all of the directories in the folder you specify.
     *
     * @param string            $path path to the directory to make
     * @param int               $level
     * @return array
     */
    public static function getDir($path = '.', $level = 0) {
        $ret = array();
        $ignore = array('cgi-bin', '.', '..');
        // Directories to ignore when listing output. Many hosts will deny PHP access to the cgi-bin.
        $dirHandler = @opendir($path);
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
                    $ret = array_merge($ret, self::getDir($path . DIRECTORY_SEPARATOR . $file, ($level + 1)));
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
     * Create a new directory that contains the file index.html
     *
     * @param string            $dir path to the directory to make
     * @param int               $perm mode
     * @param bool              $create_index if true create index.html
     * @return bool             TRUE on success or FALSE on failure
     */
    public static function makeDir($dir, $perm = 0777, $create_index = true) {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, $perm)) {
                return false;
            } else {
                if ($create_index) {
                    if ($fileHandler = @fopen($dir . '/index.html', 'w')) {
                        fwrite($fileHandler, '<script>history.go(-1);</script>');
                    }
                    @fclose($fileHandler);
                }
                return true;
            }
        }
        return null;
    }

    /**
     * @param string            $path
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
     * Copy a file
     *
     * @param string            $source is the original directory
     * @param string            $destination is the destination directory
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
     * Copy a directory and its contents
     *
     * @param string            $source is the original directory
     * @param string            $destination is the destination directory
     * @return  bool            TRUE on success or FALSE on failure
     */
    public static function copyDir($source, $destination) {
        if (!$dirHandler = opendir($source)) {
            return false;
        }
        @mkdir($destination);
        while (false !== ($file = readdir($dirHandler))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir("{$source}/{$file}")) {
                    if (!self::copyDir("{$source}/{$file}", "{$destination}/{$file}")) {
                        return false;
                    }
                } else {
                    if (!copy("{$source}/{$file}", "{$destination}/{$file}")) {
                        return false;
                    }
                }
            }
        }
        closedir($dirHandler);
        return true;
    }

    /**
     * Delete a file
     *
     * @param string            $path is the file absolute path
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
     * Delete a empty/not empty directory
     *
     * @param   string          $dir path to the directory to delete
     * @param   bool            $if_not_empty if false it delete directory only if false
     * @return  bool            TRUE on success or FALSE on failure
     */
    public static function delDir($dir, $if_not_empty = true) {
        if (!file_exists($dir)) {
            return true;
        }
        if ($if_not_empty == true) {
            if (!is_dir($dir)) {
                return unlink($dir);
            }
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                if (!self::delDir("{$dir}/{$item}")) {
                    return false;
                }
            }
        } else {
            // NOP
        }
        return rmdir($dir);
    }

    /**
     *
     * Module functions
     *
     */

    /**
     * Check if a module exist and return module version
     * @author luciorota
     *
     * @param string            $dirname
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
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @param null|string $requiredVer
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(\XoopsModule $module = null, $requiredVer = null) {
        $moduleDirName = basename(dirname(__DIR__));
        if (null === $module) {
            $module = \XoopsModule::getByDirname($moduleDirName);
        }
        xoops_loadLanguage('admin', $moduleDirName);
        //check for minimum XOOPS version
        $currentVer = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray = explode('.', $currentVer);
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $reqArray = explode('.', $requiredVer);
        $success = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int) $v > 0) { // handles things like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $module->setErrors(sprintf(_AM_COMMON_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }

    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(\XoopsModule $module) {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum = PHP_VERSION;
        $reqVer = $module->getInfo('min_php');
        if (false !== $reqVer && '' !== $reqVer) {
            if (version_compare($verNum, $reqVer, '<')) {
                $module->setErrors(sprintf(_AM_COMMON_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @return array
     */
    public static function getCurrentUrls() {
        $http = ((strpos(XOOPS_URL, 'https://')) === false) ? ('http://') : ('https://');
        $phpSelf = $_SERVER['PHP_SELF'];
        $httpHost = $_SERVER['HTTP_HOST'];
        $queryString = $_SERVER['QUERY_STRING'];

        If ($queryString != '') {
            $queryString = '?' . $queryString;
        }

        $currentURL = $http . $httpHost . $phpSelf . $queryString;

        $urls = array();
        $urls['http'] = $http;
        $urls['httphost'] = $httpHost;
        $urls['phpself'] = $phpSelf;
        $urls['querystring'] = $queryString;
        $urls['full'] = $currentURL;

        return $urls;
    }

    public static function getCurrentPage() {
        $urls = self::getCurrentUrls();

        return $urls['full'];
    }

    /**
     * save_Permissions()
     *
     * @param array             $groups
     * @param int               $id
     * @param string            $permName
     * @internal param $perm_name
     * @return bool
     */
    public static function savePermissions($groups, $id, $permName) {
        $archivioHelper = \Xmf\Module\Helper::getHelper('archivio');

        $id = (int) $id;
        $result = true;
        $mid = $archivioHelper->getModule()->mid();
        $groupperm_handler = xoops_getHandler('groupperm');

        // First, if the permissions are already there, delete them
        $groupperm_handler->deleteByModule($mid, $permName, $id);
        // Save the new permissions
        if (is_array($groups)) {
            foreach ($groups as $group_id) {
                $groupperm_handler->addRight($permName, $id, $group_id, $mid);
            }
        }
        return $result;
    }

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
            if (strlen(preg_replace('/<.*?' . '>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?' . '>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match(
                                    '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]
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
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?' . '>$/s', $line_matchings[1], $tag_matchings)) {
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
                    $left = $length - $total_length;
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
     * @param  string $document
     * @return array|string|string[]|null
     */
    public static function html2text($document) {
        // PHP Manual:: function preg_replace
        // $document should contain an HTML document.
        // This will remove HTML tags, javascript sections
        // and white space. It will also convert some
        // common HTML entities to their text equivalent.
        // Credits : newbb2
        $search = [
            "'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<img.*?>'si", // Strip out img tags
            "'<[\/\!]*?[^<>]*?>'si", // Strip out HTML tags
            "'([\r\n])[\s]+'", // Strip out white space
            "'&(quot|#34);'i", // Replace HTML entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i"
        ]; // evaluate as php

        $replace = [
            '',
            '',
            '',
            "\\1",
            '"',
            '&',
            '<',
            '>',
            ' ',
            chr(161),
            chr(162),
            chr(163),
            chr(169)
        ];

        $text = preg_replace($search, $replace, $document);

        preg_replace_callback('/&#(\d+);/', function ($matches) {
            return chr($matches[1]);
        }, $document);

        return $text;
        //<?php
    }

    /**
     * @param string $string
     * @param bool   $alphanumeric
     * @param null   $digits
     * @return string
     */
    public static function increment($string, $alphanumeric = false, $digits = null) {
        // init
        if ($string === false) {
            if (empty($digits)) {
                $string = ($alphanumeric) ? 'A' : '0';
            } else {
                $string = ($alphanumeric) ? str_repeat('A', $digits) : str_repeat('0', $digits);
            }
            return $string;
        }
        //
        if ($alphanumeric === false) {
            $string = (int) $string; // integer
        } else {
            // NOP
        }
        $string++;
        if (!empty($digits)) {
            $string = str_repeat('0', $digits) . $string;
            $string = substr($string, -$digits);
        } else {
            // NOP
        }
        return (string) $string;
    }

    /**
     * serverStats()
     *
     * @return string
     */
    public static function getServerStats()
    {
        //mb    $wfdownloads = WfdownloadsWfdownloads::getInstance();
        $moduleDirName      = basename(dirname(__DIR__));
        xoops_loadLanguage('common', $moduleDirName);
        $html = '';
        $html .= '<fieldset>';
        $html .= "<legend style='font-weight: bold; color: #900;'>" . constant('CO_COMMON_IMAGEINFO') . '</legend>';
        $html .= "<div style='padding: 8px;'>";
        $html .= '<div>' . constant('CO_COMMON_SPHPINI') . '</div>';
        $html .= '<ul>';
        $gdlib = function_exists('gd_info') ? '<span style="color: #008000;">' . constant('CO_COMMON_GDON') . '</span>' : '<span style="color: #ff0000;">' . constant('CO_COMMON_GDOFF') . '</span>';
        $html  .= '<li>' . constant('CO_COMMON_GDLIBSTATUS') . $gdlib;
        if (function_exists('gd_info')) {
            if (true === ($gdlib = gd_info())) {
                $html .= '<li>' . constant('CO_COMMON_GDLIBVERSION') . '<b>' . $gdlib['GD Version'] . '</b>';
            }
        }
        $downloads = ini_get('file_uploads') ? '<span style="color: #008000;">' . constant('CO_COMMON_ON') . '</span>' : '<span style="color: #ff0000;">' . constant('CO_COMMON_OFF') . '</span>';
        $html .= '<li>' . constant('CO_COMMON_SERVERUPLOADSTATUS') . $downloads;
        $html .= '<li>' . constant('CO_COMMON_MAXUPLOADSIZE') . ' <b><span style="color: #0000ff;">' . ini_get('upload_max_filesize') . '</span></b>';
        $html .= '<li>' . constant('CO_COMMON_MAXPOSTSIZE') . ' <b><span style="color: #0000ff;">' . ini_get('post_max_size') . '</span></b>';
        $html .= '<li>' . constant('CO_COMMON_MEMORYLIMIT') . ' <b><span style="color: #0000ff;">' . ini_get('memory_limit') . '</span></b>';
        $html .= '</ul>';
        $html .= '<ul>';
        $html .= '<li>' . constant('CO_COMMON_SERVERPATH') . ' <b>' . XOOPS_ROOT_PATH . '</b>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</fieldset><br>';

        return $html;
    }

}
