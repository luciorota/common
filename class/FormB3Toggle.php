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
 */

namespace XoopsModules\Common;

use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}
define('FORMB3TOGGLE_FILENAME', basename($currentPath));
define('FORMB3TOGGLE_PATH', dirname($currentPath));
define('FORMB3TOGGLE_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3TOGGLE_URL', XOOPS_URL . '/' . FORMB3TOGGLE_REL_URL . '/' . FORMB3TOGGLE_FILENAME);
define('FORMB3TOGGLE_JS_REL_URL', FORMB3TOGGLE_REL_URL . '/formb3toggle/js');
define('FORMB3TOGGLE_CSS_REL_URL', FORMB3TOGGLE_REL_URL . '/formb3toggle/css');
define('FORMB3TOGGLE_IMAGES_REL_URL', FORMB3TOGGLE_REL_URL . '/formb3toggle/images');

xoops_load('XoopsFormLoader');

/**
 * FormB3Toggle
 *
 * Bootstrap Tags Input with Autocomplete
 * jQuery plugin providing a Twitter Bootstrap user interface for managing tags
 * https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
 *
 */
class FormB3Toggle extends \XoopsFormElement {

    private $_id;
    private $_value;
    private $_on;
    private $_off;
    private $_size;
    private $_onstyle;
    private $_offstyle;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param bool   $value     value
     * @param string $on        Text of the on toggle
     * @param string $off       Text of the off toggle
     * @param string $size      Size of the toggle. Possible values are:large,normal,small,mini
     * @param string $onstyle   Style of the on toggle. Possible values are:default,primary,success,info,warning,danger
     * @param string $offstyle  Style of the off toggle. Possible values are:default,primary,success,info,warning,danger
     *
     */
    public function __construct(
            $caption,
            $name,
            $value,
            $on = _YES,
            $off = _NO,
            $size = 'normal',
            $onstyle = 'primary',
            $offstyle = 'default'
    ) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->_value = $value;
        $this->_on = $on;
        $this->_off = $off;
        $this->_size = $size;
        $this->_onstyle = $onstyle;
        $this->_offstyle = $offstyle;
    }

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
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        static $isCommonFormB3ToggleIncluded = false;
        $commonJs = ''; // redered only once in head
        $headJs = ''; // redered in head
        $js = ''; // rendered just after html
        $commonCss = ''; // redered only once in head
        $headCss = ''; // redered in head
        $css = ''; // rendered just before html

        $html = '';
        // add common js
        // add css js
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3ToggleIncluded) {
                $GLOBALS['xoTheme']->addStylesheet('https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css');
                //$GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
                $GLOBALS['xoTheme']->addScript('https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js');
                //$GLOBALS['xoTheme']->addScript('', [], $commonJs);
                //
                $isCommonFormB3ToggleIncluded = true;
            }
            $GLOBALS['xoTheme']->addScript('', [], $headJs);
            $GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
        } else {
            if (!$isCommonFormB3ToggleIncluded) {
                $html .= "<style type='text/css'>@import url(https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css);</style>\n";
                //$html .= "<style>\n" . $commonCss . "\n" . "</style>\n";
                $html .= "<script src='https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js' type='text/javascript'></script>\n";
                //$html .= "<script type='text/javascript'>\n" . $commonJs . "\n" . "</script>\n";
                $isCommonFormB3ToggleIncluded = true;
            }
        }
        // add css just before html
        $css .= "<style>\n";
        $css .= "</style>\n";
        $html .= $css . "\n";
        //
        // html
        $checked = ($this->_value) ? 'checked ' : '';
        $html .= "<input type='checkbox' {$this->getExtra()} name='{$this->getName()}' id='{$this->getId()}' title='{$this->getTitle()}' {$checked}>\n";
        //
        // add js just after html
        $js .= "<script type='text/javascript'>\n";
        $js .= "
            $(document).ready(function() {
                $('#{$this->getId()}').bootstrapToggle({
                    on: '{$this->_on}',
                    off: '{$this->_off}',
                    size: '{$this->_size}',
                    onstyle: '{$this->_onstyle}',
                    offstyle: '{$this->_offstyle}',
                });
            });
            ";
        $js .= "</script>\n";
        $html .= $js . "\n";
        //
        return $html;
    }

}
