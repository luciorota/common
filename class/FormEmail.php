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

xoops_loadLanguage('formemail', 'common');
xoops_load('XoopsFormLoader');

class FormEmail extends \XoopsFormText
{
    /**
     * FormEmail::FormEmail()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $value
     */
    public function __construct($caption, $name, $value = '')
    {
        parent::__construct($caption, $name, 50, 254, $value);
    }

    /**
     * FormEmail::render()
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
function {$this->getName()}ValidateEmail(email) {
    if (email == '') return true;
    var pattern = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!pattern.test(email)) return false;
    return true;
}
if (!{$this->getName()}ValidateEmail(myform.{$this->getName()}.value)) {
    window.alert(\"" . _FORMEMAIL_INCORRECT_ERROR . "\"); myform.{$this->getName()}.focus();
    return false; }";
        return $js;
    }
}
