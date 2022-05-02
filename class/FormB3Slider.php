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
define('FORMB3SLIDER_FILENAME', basename($currentPath));
define('FORMB3SLIDER_PATH', dirname($currentPath));
define('FORMB3SLIDER_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3SLIDER_URL', XOOPS_URL . '/' . FORMB3SLIDER_REL_URL . '/' . FORMB3SLIDER_FILENAME);
define('FORMB3SLIDER_JS_REL_URL', FORMB3SLIDER_REL_URL . '/formb3slider');

xoops_loadLanguage('forminputmask', 'common');
xoops_load('XoopsFormLoader');

/**
 * FormB3Slider
 * 
 * Inputmask is a javascript library that creates an input mask.
 * An inputmask helps the user with the input by ensuring a predefined format. This can be useful for dates, numerics, phone numbers, ...
 * 
 * More info here: https://github.com/RobinHerbots/Inputmask
 *
 */
class FormB3Slider extends \XoopsFormElement {

    private $_id;
    private $_attributes = [];
    private $_options = [];
    private $_values = [];

    /**
     * Constructor
     *
     * @param mixed $caption
     * @param mixed $name
     * @param null  $values
     * @param array $options more info here: https://github.com/seiyria/bootstrap-slider
     */
    public function __construct($caption, $name, $values = null, $options = []) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
//        $this->setValues($values);
        $this->_values = $values;
        //
        $this->_options = $options;
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
//    public function setValues($values) {
//        if (is_array($values)) {
//            foreach ($values as $key => $value) {
//                $this->_values[$key] = $value;
//            }
//        } elseif (isset($values)) {
//            $this->_values[] = $values;
//        }
//    }

    /**
     * Get an array of pre-selected values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
//    public function getValues($encode = false) {
//        if (!$encode) {
//            return $this->_values;
//        }
//        $values = [];
//        foreach ($this->_values as $key => $value) {
//            $values[$key] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
//        }
//        return $values;
//    }
    
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
        $ret = '';
        foreach ($this->_options as $name => $value) {
            if (is_array($value)) {
                $vals = [];
                foreach ($value as $val) {
                    $vals[] = is_string($val) ? (string)"'{$val}'" : (string)($val);
                }
                $value = '[' . implode(',', $vals) . ']';
            }
            $ret .= "{$name}: {$value}, ";
        }
        return $ret;
    }

    /**
     * FormB3Slider::render()
     *
     * @return string
     */
    public function render() {
//TODO COMPLETARE QUESTO
        static $isCommonFormB3SliderIncluded = false;
        $commonJs = '';
        $css = '';
        $js = '';
        $html = '';
        $ret = '';
        // add common js
        $commonJs = '';
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3SliderIncluded) {
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                $GLOBALS['xoTheme']->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css');
                $GLOBALS['xoTheme']->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.js');
                $GLOBALS['xoTheme']->addScript('', [], $commonJs);
                $isCommonFormB3SliderIncluded = true;
            }
        } else {
            if (!$isCommonFormB3SliderIncluded) {
                $ret .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                $ret .= "<style type='text/css'>@import url(https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css);</style>\n";
                $ret .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.js' type='text/javascript'></script>\n";
                $ret .= "<script type='text/javascript'>\n";
                $ret .= $commonJs . "\n";
                $ret .= "</script>\n";
                $isCommonFormB3SliderIncluded = true;
            }
        }
        // add css
        $css .= "<style>\n";
        $css .= "</style>\n";
        $ret .= $css . "\n";
        // add html
        $html .= "<input id='slider_{$this->getId()}' name='{$this->getName()}' type='text' {$this->renderAttributes()} {$this->getExtra()} />";
        $ret .= $html . "\n";
        // add js
        $this->_options['value'] = $this->_values;
        $js .= "<script type='text/javascript'>\n";
        $js .= "
$(document).ready(function(){
    $('#slider_{$this->getId()}').slider({
        {$this->renderOptions()}
    });
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
