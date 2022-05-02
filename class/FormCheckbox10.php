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

xoops_load('XoopsFormElement');

/**
 * 1/0 checkbox
 */
class FormCheckbox10 extends \XoopsFormElement
{
    public $_value;

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value Either one value as a string or an array of them.
     */
    public function __construct($caption, $name, $value = 0)
    {
        $this->_caption = $caption;
        $this->_name = $name;
        $this->_value = isset ($value) ? $value : 0;
    }

    /**
     * Get the "value"
     *
     * @return array
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the "value"
     *
     * @param array $value
     *
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function render() {
        $ret = '';
        $ret .= "<input type='hidden' name='{$this->_name}' value='0' />";
        $ret .= "<input type='checkbox' name='{$this->_name}' id='{$this->_name}' title='{$this->getTitle()}' value='1'";
        if ($this->_value == 1) {
            $ret .= ' checked="checked"';
        }
        $ret .= $this->getExtra() . ' />';
        return $ret;
    }
}
