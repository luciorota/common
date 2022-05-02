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
 * @author          luciorota
 * @version         svn:$Id$
 *
 * bootstrap-fileinput
 * http://plugins.krajee.com/file-input
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2022, Kartik Visweswaran, Krajee.com
 *
 * Licensed under the BSD-3-Clause
 * https://github.com/kartik-v/bootstrap-fileinput/blob/master/LICENSE.md
 *
 */

namespace XoopsModules\Common;

use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}

define('FORMB3FILEINPUT_FILENAME', basename($currentPath));
define('FORMB3FILEINPUT_PATH', dirname($currentPath));
define('FORMB3FILEINPUT_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3FILEINPUT_URL', XOOPS_URL . '/' . FORMB3FILEINPUT_REL_URL);
define('FORMB3FILEINPUT_JS_URL', FORMB3FILEINPUT_URL . '/formb3fileinput/js');
define('FORMB3FILEINPUT_CSS_URL', FORMB3FILEINPUT_URL . '/formb3fileinput/css');
define('FORMB3FILEINPUT_IMAGES_URL', FORMB3FILEINPUT_URL . '/formb3fileinput/img');
define('FORMB3FILEINPUT_THEMES_URL', FORMB3FILEINPUT_URL . '/formb3fileinput/themes');

xoops_loadLanguage('formfileinputbootstrap3', $GLOBALS['xoopsModule']->getVar('dirname'));
xoops_load('XoopsFormLoader');

class FormB3Fileinput extends \XoopsFormText {

    private $_id;
    private $_values = [];
    private $_multiple = false;
    private $_showThumbs;
    private $_allowedFileExtensions = [];
    private $_maxFileSize;

    /**
     * set the "id" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setId($name = null) {
        $this->_id = $name === null ? md5(uniqid(mt_rand(), true)) : $name;
    }

    /**
     * get the "id" attribute for the element
     *
     * @param bool $encode
     *
     * @return string "name" attribute
     */
    public function getId($encode = true) {
        if (false != $encode) {
            return str_replace('&amp;', '&', htmlspecialchars($this->_id, ENT_QUOTES));
        }
        return $this->_id;
    }

    /**
     * Set pre-selected values
     *
     * @param mixed $values
     */
    public function setValues($values) {
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->_values[] = $value;
            }
        } elseif (isset($values)) {
            $this->_values[] = $values;
        }
    }

    /**
     * Get an array of pre-selected values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValues($encode = false) {
        if (!$encode) {
            return $this->_values;
        }
        $value = [];
        foreach ($this->_values as $value) {
            $values[] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
        }
        return $values;
    }

    /**
     * Constructor
     *
     * @param string    $caption   Caption
     * @param string    $name      "name" attribute
     * @param array     $values    array of tags
     * @param bool      $showThumbs
     * @param array     $allowedFileExtensions
     * @param int       $maxFileSize
     */
    public function __construct(
            $caption,
            $name,
            $values = [],
            $multiple = false,
            $showThumbs = true,
            $allowedFileExtensions = [],
            $maxFileSize = 0
    ) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->setValues($values);
        //
        $this->_multiple = $multiple;
        $this->_showThumbs = $showThumbs;
        $this->_allowedFileExtensions = $allowedFileExtensions;
        $this->_maxFileSize = $maxFileSize;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        $language = substr(_LANGCODE, 0, 2);
        $theme = 'gly';
        $html = "\n";
        $allowedFileExtensions = count($this->_allowedFileExtensions) ? json_encode($this->_allowedFileExtensions) : 'null';

        $files = $this->getValues();        
        $multiple = $this->_multiple ? 'multiple' : '';
        $name = $this->_multiple ? "{$this->getName()}[]" : (string)($this->getName());

        // common code
        static $isCommonFormB3FileinputIncluded = false;
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3FileinputIncluded) {
                $isCommonFormB3FileinputIncluded = true;
//                $GLOBALS['xoTheme']->addStylesheet('https://use.fontawesome.com/releases/v5.15.4/css/all.css');
                $GLOBALS['xoTheme']->addStylesheet('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css');
                //
                $GLOBALS['xoTheme']->addStylesheet(FORMB3FILEINPUT_CSS_URL . '/fileinput.css');
                $GLOBALS['xoTheme']->addStylesheet(FORMB3FILEINPUT_THEMES_URL . "/{$theme}/theme.js");
                //
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                //
                $GLOBALS['xoTheme']->addScript(FORMB3FILEINPUT_JS_URL . '/plugins/piexif.js');
                $GLOBALS['xoTheme']->addScript(FORMB3FILEINPUT_JS_URL . '/plugins/sortable.js');
                $GLOBALS['xoTheme']->addScript(FORMB3FILEINPUT_JS_URL . '/fileinput.js');
                $GLOBALS['xoTheme']->addScript(FORMB3FILEINPUT_JS_URL . "/locales/{$language}.js");
                $GLOBALS['xoTheme']->addStylesheet(FORMB3FILEINPUT_THEMES_URL . "/{$theme}/theme.js");
            }
        } else {
            if (!$isCommonFormB3FileinputIncluded) {
                $isCommonFormB3FileinputIncluded = true;
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>\n";
//                $html .= "<style type='text/css'>@import url(" . 'https://use.fontawesome.com/releases/v5.15.4/css/all.css' . ");</style>\n";
                $html .= "<style type='text/css'>@import url(" . 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css' . ");</style>\n";
                //
                $html .= "<style type='text/css'>@import url(" . FORMB3FILEINPUT_CSS_URL . '/fileinput.css' . ");</style>\n";
                $html .= "<style type='text/css'>@import url(" . FORMB3FILEINPUT_THEMES_URL . "/{$theme}/theme.css" . ");</style>\n";
                //
                $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                //
                $html .= '<script src=' . FORMB3FILEINPUT_JS_URL . "plugins/piexif.js' type='text/javascript></script>\n";
                $html .= '<script src=' . FORMB3FILEINPUT_JS_URL . "/plugins/sortable.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMB3FILEINPUT_JS_URL . "/fileinput.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMB3FILEINPUT_JS_URL . "/locales/{$language}.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMB3FILEINPUT_THEMES_URL . "/{$theme}/theme.js' type='text/javascript'></script>\n";
            }
        }
        // not common code
        $css = '';
        $js = "
    $(document).ready(function () {
        $('#{$this->getId()}').fileinput({
            showUpload: true,
            dropZoneEnabled: true,
            inputGroupClass: '', // input-lg, input-sm
            theme: '{$theme}',
            showUpload: false,
            uploadUrl: '#',
            language: '{$language}',
            allowedFileExtensions: {$allowedFileExtensions},
            maxFileSize: {$this->_maxFileSize},
            minFileCount: 0,
            maxFileCount: 0,
            maxTotalFileCount: 0,
            fileActionSettings : {
                showUpload: false,
                showRemove: true,
                showZoom: true,
            },
        });
    });
        ";
        if (is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addStylesheet('', [], $css);
            $GLOBALS['xoTheme']->addScript('', '', $js);
        } else {
            $html .= "<style type='text/css'>\n{$css}\n</style>\n";
            $html .= "<script>\n{$js}\n</script>\n";
        }
        $html .= "
            <div class='row'>
                <div class='col-md-8'>
                    <div class='file-loading'>
                        <input id='{$this->getId()}' name='{$name}' type='file' {$multiple}>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='row'>
        ";
        foreach ($files as $file) {
            $html .= "
                        <div class='col-md-6'>
                            <div class='thumbnail'>
                                <img src='{$file['url']}' alt='...'>
                                <div class='caption'>
                                    <p>testo</p>
                                </div>
                            </div>
                        </div>
            ";    
        }
        $html .= '
                    </div>
                </div>
            </div>
        ';
        return $html;
    }

}
