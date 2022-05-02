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

xoops_loadLanguage('formcap', 'common');
xoops_load('XoopsFormLoader');

class FormCap extends \XoopsFormText
{
    /**
     * FormCap::FormCap()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $value
     */
    public function __construct($caption, $name, $value = '')
    {
        parent::__construct($caption, $name, 5, 5, $value);
    }

    /**
     * FormCap::render()
     *
     * @return string
     */
    public function render()
    {
        return parent::render();
    }

    /**
     * Returns custom validation Javascript
     *
     * @return string Element validation Javascript
     */
    public function renderValidationJS()
    {
        $js = "
function {$this->getName()}validaCap(cap) {
    if (cap == '') return true;
    if (cap.length != 5) return false;
    var pattern = /^\d{5}$/;
    if (!pattern.test(cap)) return false;
    return true;
}
if ((myform.{$this->getName()}.value.length > 0) && (myform.{$this->getName()}.value.length < 5)) {
    window.alert(\"" . _FORMCAP_INCORRECT_LENGTH . "\");
    myform.{$this->getName()}.focus();
    return false;
}
if (!{$this->getName()}validaCap(myform.{$this->getName()}.value)) {
    window.alert(\"" . _FORMCAP_INCORRECT_ERROR . "\");
    myform.{$this->getName()}.focus();
    return false;
}";
        return $js;
    }
}
