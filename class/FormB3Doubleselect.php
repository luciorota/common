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
define('FORMB3DOUBLESELECT_FILENAME', basename($currentPath));
define('FORMB3DOUBLESELECT_PATH', dirname($currentPath));
define('FORMB3DOUBLESELECT_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3DOUBLESELECT_URL', XOOPS_URL . '/' . FORMB3DOUBLESELECT_REL_URL . '/' . FORMB3DOUBLESELECT_FILENAME);
define('FORMB3DOUBLESELECT_JS_REL_URL', FORMB3DOUBLESELECT_REL_URL . '/formb3doubleselect/js');
define('FORMB3DOUBLESELECT_CSS_REL_URL', FORMB3DOUBLESELECT_REL_URL . '/formb3doubleselect/css');
define('FORMB3DOUBLESELECT_IMAGES_REL_URL', FORMB3DOUBLESELECT_REL_URL . '/formb3doubleselect/images');

xoops_loadLanguage('formb3doubleselect', 'common');
xoops_load('XoopsFormLoader');

class FormB3Doubleselect extends \XoopsFormElement {

    private $_valuesFrom = [];
    private $_valuesTo = [];
    private $_size;
    private $_fromCaption;
    private $_toCaption;

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $valuesFrom   Pre-selected From options, From options and To options must have different values
     * @param mixed  $valuesTo     Pre-selected To options, From options and To options must have different values
     * @param int    $size         Number or rows, "1" makes a drop-down-list
     * @param string $fromCaption
     * @param string $toCaption
     *      */
    public function __construct($caption, $name, $valuesFrom = [], $valuesTo = [], $size = 1, $fromCaption = '', $toCaption = '') {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->setValuesFrom($valuesFrom);
        $this->setValuesTo($valuesTo);
        $this->_size = $size;
        $this->_fromCaption = $fromCaption;
        $this->_toCaption = $toCaption;

        
        
    }

    /**
     * set the "id" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setId($name) {
        $this->_id = md5(uniqid(mt_rand(), true));
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
     * Set To values
     *
     * @param $values
     */
    public function setValuesTo($values) {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->_valuesTo[$key] = $value;
            }
        } elseif (isset($values)) {
            $this->_valuesTo[] = $values;
        }
    }

    /**
     * Get an array of To values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValuesTo($encode = false) {
        if (!$encode) {
            return $this->_valuesTo;
        }
        $value = [];
        foreach ($this->_valuesTo as $key => $value) {
            $values[$key] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
        }
        return $values;
    }

    /**
     * Set From values
     *
     * @param $values
     */
    public function setValuesFrom($values) {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->_valuesFrom[$key] = $value;
            }
        } elseif (isset($values)) {
            $this->_valuesFrom[] = $values;
        }
    }

    /**
     * Get an array of From values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValuesFrom($encode = false) {
        if (!$encode) {
            return $this->_valuesFrom;
        }
        $value = [];
        foreach ($this->_valuesFrom as $key => $value) {
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
        static $isCommonFormB3DoubleselectIncluded = false;
        $commonJs = '';
        $css = '';
        $js = '';
        $html = '';
        $ret = '';
        // add common js
        $commonJs = '';
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3DoubleselectIncluded) {
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                //$GLOBALS['xoTheme']->addStylesheet(FORMB3DOUBLESELECT_JS_REL_URL . '/lou-multi-select/css/multi-select.css');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3DOUBLESELECT_JS_REL_URL . '/multiselect-master/dist/js/multiselect.min.js');
                $GLOBALS['xoTheme']->addScript('', [], $commonJs);
                $isCommonFormB3DoubleselectIncluded = true;
            }
        } else {
            if (!$isCommonFormB3DoubleselectIncluded) {
                //$ret .= "<style type='text/css'>@import url(" . FORMB3DOUBLESELECT_JS_REL_URL . "//lou-multi-select/css/multi-select.css);</style>\n";
                $ret .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3DOUBLESELECT_JS_REL_URL . "/multiselect-master/dist/js/multiselect.min.js' type='text/javascript'></script>\n";
                $ret .= "<script type='text/javascript'>\n";
                $ret .= $commonJs . "\n";
                $ret .= "</script>\n";
                $isCommonFormB3DoubleselectIncluded = true;
            }
        }
        // add css
        $css .= "<style>\n";
        $css .= ".members_from select.form-control {width:100% !important;}\n";
        $css .= ".members_to select.form-control {width:100% !important;}\n";
        $css .= "</style>\n";
        $ret .= $css . "\n";
        // add html
        $valuesFrom = $this->getValuesFrom();
        $valuesTo = $this->getValuesTo();
        $select_from = new \XoopsFormSelect('', "{$this->getName()}_{$this->getId()}", null, $this->_size, true);
        $select_from->addOptionArray($valuesFrom);
        $select_from->setClass('w-100');
        //
        $select_to = new \XoopsFormSelect('', (string)($this->getName()), null, $this->_size, true);
        $select_to->addOptionArray($valuesTo);
        $select_to->setClass('form-control w-100');
        $html .= "        
<div class='row'>
    <div class='members_from col-md-5'>
        {$this->_fromCaption}<br>
        {$select_from->render()}
    </div>
    <div class='col-md-2'>
        &nbsp;<br>
        <button type='button' id='{$this->getName()}_{$this->getId()}_undo' class='btn btn-primary btn-block'>" . _FORMB3DOUBLESELECT_UNDO . "</button>
        <button type='button' id='{$this->getName()}_{$this->getId()}_rightAll' class='btn btn-default btn-block'>&#8649;</button>
        <button type='button' id='{$this->getName()}_{$this->getId()}_rightSelected' class='btn btn-default btn-block'>&rarr;</button>
        <button type='button' id='{$this->getName()}_{$this->getId()}_leftSelected' class='btn btn-default btn-block'>&larr;</button>
        <button type='button' id='{$this->getName()}_{$this->getId()}_leftAll' class='btn btn-default btn-block'>&#8647;</button>
        <button type='button' id='{$this->getName()}_{$this->getId()}_redo' class='btn btn-primary btn-block'>" . _FORMB3DOUBLESELECT_REDO . "</button>
    </div>
    <div class='members_to col-md-5'>
        {$this->_toCaption}<br>
        {$select_to->render()}
    </div>
</div>
";
        $ret .= $html . "\n";
        // add js
        $js .= "<script type='text/javascript'>\n";
        $js .= "
jQuery(document).ready(function($) {
    $('#{$this->getName()}_{$this->getId()}').multiselect({
        right: '#{$this->getName()}',
        rightSelected: '#{$this->getName()}_{$this->getId()}_rightSelected',
        rightAll: '#{$this->getName()}_{$this->getId()}_rightAll',
        leftSelected: '#{$this->getName()}_{$this->getId()}_leftSelected',
        leftAll: '#{$this->getName()}_leftAll',
        undo: '#{$this->getName()}_{$this->getId()}_undo',
        redo: '#{$this->getName()}_{$this->getId()}_redo',
        submitAllLeft: true,
        submitAllRight: true,
    });
});";
        $js .= "</script>\n";
        $ret .= $js . "\n";


        return $ret;
    }

}
