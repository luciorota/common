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

xoops_loadLanguage('formurl', 'common');
xoops_load('XoopsFormLoader');

class FormUrl extends \XoopsFormText
{
    /**
     * FormUrl::FormUrl()
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
     * FormUrl::render()
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
        $eltname = $this->getName();
        $js = "
function {$eltname}ValidateURL(url) {
    if (url == '') return true;
    var pattern = new RegExp('^(https?:\/\/)?'+ // protocol
        '((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|'+ // domain name
        '((\d{1,3}\.){3}\d{1,3}))'+ // OR ip (v4) address
        '(\:\d+)?(\/[-a-z\d%_.~+]*)*'+ // port and path
        '(\?[;&a-z\d%_.~+=-]*)?'+ // query string
        '(\#[-a-z\d_]*)?$','i'); // fragment locater
    if (!pattern.test(url)) return false;
    return true;
}
if (!{$this->getName()}ValidateURL(myform.{$this->getName()}.value)) {
    window.alert(\"" . _FORMURL_INCORRECT_ERROR . "\");
    myform.{$this->getName()}.focus();
    return false; 
}";
        return $js;
    }
}

