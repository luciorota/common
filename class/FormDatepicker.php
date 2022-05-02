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

xoops_load('XoopsFormLoader');

class FormDatepicker extends \XoopsFormText {

    private $_attributes = array();
    private $_id;
    private $_min;
    private $_max;
    private $_step;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param string $value
     * @param null   $min
     * @param null   $max
     * @param null   $step
     */
    public function __construct($caption, $name, $value = '', $min = null, $max = null, $step = null) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setValue($value);
        $this->_min = $min;
        $this->_max = $max;
        $this->_step = $step;
    }

    /**
     * set the "id" attribute for the element
     *
     */
    public function setId() {
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

//TODO valutare, migliorare ...
    /**
     * render the attributes string for HTML tag
     *
     * @return string attributes string
     */
    public function renderAttributes() {
        $ret = '';
        foreach ($this->_attributes as $name => $value) {
            $ret .= "{$name}='{$value}' ";
        }
        return $ret;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        $html = "\n";
        // common javascript/css code
        static $isCommonFormDatepickerIncluded = false;
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormDatepickerIncluded) {
                $isCommonFormDatepickerIncluded = true;
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                // NOP
            }
        } else {
            if (!$isCommonFormDatepickerIncluded) {
                $isCommonFormDatepickerIncluded = true;
                $html .= "
<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>
<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>
                ";
                // NOP
            }
        }
        // not common javascript/css code
        $css = '';
        $js = '';
        if (is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addStylesheet('', array(), $css);
            $GLOBALS['xoTheme']->addScript('', '', $js);
        } else {
            $html .= "<style type='text/css'>\n{$css}\n</style>\n";
            $html .= "<script type='text/javascript'>\n{$js}\n</script>\n";
        }
        // set attributes
        $this->_attributes['type'] = 'date';
        $this->_attributes['id'] = $this->getName();
        $this->_attributes['name'] = $this->getName();
        $this->_attributes['title'] = $this->getTitle();
        $this->_attributes['value'] = $this->getValue();
        if ($this->_min !== null) {
            $this->_attributes['min'] = $this->_min;
        }
        if ($this->_max !== null) {
            $this->_attributes['max'] = $this->_max;
        }
        if ($this->_step !== null) {
            $this->_attributes['step'] = $this->_step;
        }
        // render output
        $html .= "<input {$this->renderAttributes()} {$this->getExtra()} />";
        return $html;
    }

}
