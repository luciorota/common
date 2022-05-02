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
define('FORMSELECTGROUP_FILENAME', basename($currentPath));
define('FORMSELECTGROUP_PATH', dirname($currentPath));
define('FORMSELECTGROUP_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMSELECTGROUP_URL', XOOPS_URL . '/' . FORMSELECTGROUP_REL_URL . '/' . FORMSELECTGROUP_FILENAME);
define('FORMSELECTGROUP_JS_REL_URL', FORMSELECTGROUP_REL_URL . '/formselectgroup/js');
define('FORMSELECTGROUP_CSS_REL_URL', FORMSELECTGROUP_REL_URL . '/formselectgroup/css');
define('FORMSELECTGROUP_IMAGES_REL_URL', FORMSELECTGROUP_REL_URL . '/formselectgroup/images');

xoops_load('XoopsFormLoader');

class FormSelectGroup extends \XoopsFormElement {

    private $_id;
    private $_members = [];
    private $_values = [];
    private $_size;
    private $_multiple;

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool   $include_anon Include group "anonymous"?
     * @param mixed  $value        Pre-selected value (or array of them).
     * @param int    $size         Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple     Allow multiple selections?
     */
    public function __construct($caption, $name, $include_anon = false, $value = null, $size = 1, $multiple = false) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->setValues($value);
        //
        $member_handler = xoops_getHandler('member');
        if (!$include_anon) {
            $this->_members = $member_handler->getGroupList(new \Criteria('groupid', XOOPS_GROUP_ANONYMOUS, '!='));
        } else {
            $this->_members = $member_handler->getGroupList();
        }
        $this->_size = $size;
        $this->_multiple = $multiple;
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
     * Set pre-selected values
     *
     * @param mixed $values
     */
    public function setValues($values) {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->_values[$key] = $value;
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
        $values = [];
        foreach ($this->_values as $key => $value) {
            $values[$key] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
        }
        return $values;
    }

    /**
     * prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        static $isCommonFormSelectGroupIncluded = false;
        $commonJs = '';
        $css = '';
        $js = '';
        $html = '';
        $ret = '';
        // add common js
        $commonJs = '';
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormSelectGroupIncluded) {
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                $GLOBALS['xoTheme']->addStylesheet(FORMSELECTGROUP_JS_REL_URL . '/lou-multi-select/css/multi-select.css');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMSELECTGROUP_JS_REL_URL . '/lou-multi-select/js/jquery.multi-select.js');
                $GLOBALS['xoTheme']->addScript('', array(), $commonJs);
                $isCommonFormSelectGroupIncluded = true;
            }
        } else {
            if (!$isCommonFormSelectGroupIncluded) {
                $ret .= "<style type='text/css'>@import url(" . FORMSELECTGROUP_JS_REL_URL . "//lou-multi-select/css/multi-select.css);</style>\n";
                $ret .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMSELECTGROUP_JS_REL_URL . "/lou-multi-select/js/jquery.multi-select.js' type='text/javascript'></script>\n";
                $ret .= "<script type='text/javascript'>\n";
                $ret .= $commonJs . "\n";
                $ret .= "</script>\n";
                $isCommonFormSelectGroupIncluded = true;
            }
        }
        // add css
        $css .= "<style>\n";
        $css .= "</style>\n";
        $ret .= $css . "\n";
        // add html
        $members = new \XoopsFormSelect('', $this->getName(), $this->getValues(), $this->_size, $this->_multiple);
        $members->addOptionArray($this->_members);
        $html .= $members->render();
        $ret .= $html . "\n";
        if ($this->_multiple) {
            // add js
            $js .= "<script type='text/javascript'>\n";
            $js .= "jQuery(document).ready(function($) {
                    $('#" . $this->getName() . "').multiSelect();
            });";
            $js .= "</script>\n";
        }
        $ret .= $js . "\n";


        return $ret;
    }

}
