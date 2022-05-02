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

xoops_loadLanguage('formcodicefiscale', 'common');
xoops_load('XoopsFormLoader');

class FormCodiceFiscale extends \XoopsFormText
{
    /**
     * FormCodiceFiscale::FormCodiceFiscale()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $value
     */
    public function __construct($caption, $name, $value = '')
    {
        parent::__construct($caption, $name, 20, 16, $value);
    }

    /**
     * FormCodiceFiscale::render()
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
        $errorLength = _FORMCODICEFISCALE_INCORRECT_LENGTH;
        $errorError = _FORMCODICEFISCALE_INCORRECT_ERROR;
        $js = "
function {$this->getName()}ValidateCodiceFiscale(cf) {
    var validi, i, s, set1, set2, setpari, setdisp;
    if (cf == '') return true;
    if (cf.length != 16) return false;
    cf = cf.toUpperCase();
    validi = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for (i = 0; i < 16; i++) {
        if (validi.indexOf(cf.charAt(i)) == -1) return false;
    }
    set1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    set2 = 'ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ';
    setpari = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    setdisp = 'BAKPLCQDREVOSFTGUHMINJWZYX';
    s = 0;
    for (i = 1; i <= 13; i += 2)
        s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
    for (i = 0; i <= 14; i += 2 )
        s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
    if (s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0))
            return false;
    return true;
}
if ((myform.{$this->getName()}.value.length > 0) && (myform.{$this->getName()}.value.length < 16)) {
    window.alert(\"{$errorLength}\");
    myform.{$this->getName()}.focus();
    return false;
}
if (!{$this->getName()}ValidateCodiceFiscale(myform.{$this->getName()}.value)) {
    window.alert(\"{$errorError}\");
    myform.{$this->getName()}.focus();
    return false;
}";
        return $js;
    }
}
