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

xoops_loadLanguage('formtelephonenumber', 'common');
xoops_load('XoopsFormLoader');

class FormTelephonenumber extends \XoopsFormText
{
    /**
     * FormCap::FormCap()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $value
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '')
    {
        parent::__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * FormTelephonenumber::render()
     *
     * @return string
     */
    public function render()
    {
        return "<input type='tel' pattern='^[0-9]{5,{$this->_maxlength}}$' name='{$this->getName()}' title='{$this->getTitle()}' id='{$this->getName()}' size='{$this->getSize()}' maxlength='{$this->getMaxlength()}' value='{$this->getValue()}' {$this->getExtra()} />";
    }

    /**
     * Returns custom validation Javascript
     *
     * @return string Element validation Javascript
     */
    public function renderValidationJS()
    {
        $errorError = _FORMTELEPHONENUMBER_INCORRECT_ERROR;
        $js = "
function {$this->getName()}validaTelephonenumber(telephonenumber) {
    if (telephonenumber == '') return true;
    var pattern = /^[0-9]{5,{$this->_maxlength}}$/;
    if (!pattern.test(telephonenumber)) return false;
    return true;
}";
        $js .= "\n";
        $js .= "if (!{$this->getName()}validaTelephonenumber(myform.{$this->getName()}.value)) { window.alert(\"" . _FORMTELEPHONENUMBER_INCORRECT_ERROR . "\"); myform.{$this->getName()}.focus(); return false; }";
        return $js;
    }
}
