<?php

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
define('FORMB3INPUTMASK_FILENAME', basename($currentPath));
define('FORMB3INPUTMASK_PATH', dirname($currentPath));
define('FORMB3INPUTMASK_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3INPUTMASK_URL', XOOPS_URL . '/' . FORMB3INPUTMASK_REL_URL . '/' . FORMB3INPUTMASK_FILENAME);
define('FORMB3INPUTMASK_JS_REL_URL', FORMB3INPUTMASK_REL_URL . '/forminputmask');

xoops_loadLanguage('forminputmask', 'common');
xoops_load('XoopsFormLoader');

/**
 * FormB3Inputmask
 * 
 * Inputmask is a javascript library that creates an input mask.
 * An inputmask helps the user with the input by ensuring a predefined format. This can be useful for dates, numerics, phone numbers, ...
 * 
 * More info here: https://github.com/RobinHerbots/Inputmask
 *
 */
class FormB3Inputmask extends \XoopsFormElement {

    private $_id;
    private $_attributes = [];
    private $_inputmask;
    private $_options = [];
    private $_pre;
    private $_post;

    /**
     * Constructor
     *
     * @param mixed  $caption
     * @param mixed  $name
     * @param string $value
     * @param string $inputmask more info here:https://github.com/RobinHerbots/Inputmask
     * @param array  $options
     * @param string $pre
     * @param string $post
     * @param string $placeholder
     */
    public function __construct($caption, $name, $value = '', $inputmask = null, $options = [], $pre = '', $post = '', $placeholder = '') {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->_value = $value;
        //
        $this->_inputmask = $inputmask;
        $this->_options = $options;
        $this->_pre = $pre;
        $this->_post = $post;
        $this->setAttributes(['placeholder' => $placeholder]);
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
     * Set attributes
     *
     * @param mixed $attributes
     */
    public function setAttributes($attributes) {
        if (is_array($attributes)) {
            foreach ($attributes as $key => $attribute) {
                $this->_attributes[$key] = $attribute;
            }
        } elseif (isset($attributes)) {
            $this->_attributes[$attributes] = $attributes;
        }
    }
    
    /**
     * Get an array of pre-selected attributes
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getAttributes($encode = false) {
        if (!$encode) {
            return $this->_attributes;
        }
        $attributes = [];
        foreach ($this->_attributes as $key => $attribute) {
            $attributes[$key] = $attribute ? htmlspecialchars($attribute, ENT_QUOTES) : $attribute;
        }
        return $attributes;
    }

    /**
     * Render a string of attributes
     *
     * @return string
     */
    public function renderAttributes() {
        $ret = '';
        foreach ($this->_attributes as $name => $value) {
            $ret .= "{$name}='" . htmlspecialchars($value, ENT_QUOTES) . "' ";
        }
        return $ret;
    }
    
//TODO valutare, migliorare ...

    /**
     * render the options string for HTML tag
     *
     * @return string options string
     */
    public function renderOptions() {
        $ret = json_encode($this->_options, JSON_FORCE_OBJECT);
        return $ret;
//        exit();
//        foreach ($this->_options as $name => $value) {
//            $ret .= "{$name}: '{$value}', ";
//        }
//        return $ret;
    }

    /**
     * FormB3Inputmask::render()
     *
     * @return string
     */
    public function render() {
//TODO COMPLETARE QUESTO
        static $isCommonFormB3InputmaskIncluded = false;
        $commonJs = '';
        $css = '';
        $js = '';
        $html = '';
        $ret = '';
        // add common js
        $commonJs = '';
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3InputmaskIncluded) {
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . '/dist/jquery.inputmask.min.js');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . '/lib/extensions/inputmask.date.extensions.js');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . '/lib/extensions/inputmask.extensions.js');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . '/lib/extensions/inputmask.numeric.extensions.js');
                $GLOBALS['xoTheme']->addScript('', [], $commonJs);
                $isCommonFormB3InputmaskIncluded = true;
            }
        } else {
            if (!$isCommonFormB3InputmaskIncluded) {
                $ret .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . "/dist/jquery.inputmask.min.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . "/lib/extensions/inputmask.date.extensions.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . "/lib/extensions/inputmask.extensions.js' type='text/javascript'></script>\n";
                $ret .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3INPUTMASK_JS_REL_URL . "/lib/extensions/inputmask.numeric.extensions.js' type='text/javascript'></script>\n";
                $ret .= "<script type='text/javascript'>\n";
                $ret .= $commonJs . "\n";
                $ret .= "</script>\n";
                $isCommonFormB3InputmaskIncluded = true;
            }
        }
        // add css
        $css .= "<style>\n";
        $css .= "</style>\n";
        $ret .= $css . "\n";
        // add html
        $html .= (!empty($this->_pre) || !empty($this->_post)) ? "<div class='input-group'>" : '<div>';
        if (!empty($this->_pre)) {
            $html .= "<span class='input-group-addon'>{$this->_pre}</span>";
        
        }
        $html .= "<input type='text' class='form-control' id='inputmask_{$this->getId()}' {$this->renderAttributes()} {$this->getExtra()} />";
        if (!empty($this->_post)) {
            $html .= "<span class='input-group-addon'>{$this->_post}</span>";
        }
        $html .= '</div>';
        $ret .= $html . "\n";
        // add js
        if ($this->_inputmask !== null) {
            $this->_options['mask'] = $this->_inputmask;
        }
        $js .= "<script type='text/javascript'>\n";
        $js .= "
$(document).ready(function(){
    $('#inputmask_{$this->getId()}').inputmask({$this->renderOptions()});
});
";
        $js .= "</script>\n";
        $ret .= $js . "\n";
        //
        return $ret;
    }

    /**
     * Returns custom validation Javascript
     *
     * @return string Element validation Javascript
     */
    public function renderValidationJS() {
//TODO
        $js = '';
        return $js;
    }

}
