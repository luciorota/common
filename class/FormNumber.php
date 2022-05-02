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

xoops_loadLanguage('formnumber', 'common');
xoops_load('XoopsFormLoader');

class FormNumber extends \XoopsFormText
{
    /**
     * FormNumber::FormNumber()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $value
     * @param integer $max
     * @param integer $min
     * @param float $step
     */
    public function __construct($caption, $name, $value = '', $max = PHP_INT_MAX, $min = 0, $step = 0)
    {
//TODO definire attributi qui
        $this->setCaption($caption);
        $this->setName($name);
        $this->setValue($value);
        $this->setAttribute('max', (int)$max);
        $this->setAttribute('min', (int)$min);
        $this->setAttribute('step', (float)$step);
    }

    public $attributes = array();

    public function setAttribute($attributeName, $attributeValue = '')
    {
        $this->attributes[$attributeName] = $attributeValue;
    }

    public function getAttribute($attributeName)
    {
        return isset($this->attributes[$attributeName]) ? $this->attributes[$attributeName] : '';
    }

    /**
     * FormNumber::render()
     *
     * @return string
     */
    public function render()
    {
//TODO definire attributi altrove
        $attributes['type'] = 'number';
        $attributes['name'] = $this->getName();
        $attributes['title'] = $this->getTitle();
        $attributes['id'] = $this->getName();
        $attributes['value'] = $this->getValue();
        $attributes['max'] = (float)$this->getAttribute('max');
        $attributes['min'] = (float)$this->getAttribute('min');
        $attributes['step'] = (float)$this->getAttribute('step');
        $ret = '<input ';
        foreach ($attributes as $attributeName => $attributeValue) {
            $ret .= "{$attributeName}='{$attributeValue}' ";
        }
        $ret .= "{$this->getExtra()} />";

        return $ret;
    }

    /**
     * Returns custom validation Javascript
     *
     * @return string Element validation Javascript
     */
    public function renderValidationJS()
    {
        $js = "
function {$this->getName()}ValidateNumber(number) {
    if (number == '') return true;
    var pattern = /^[0-9.,]+$/;
    if (!pattern.test(number)) return false;
    return true;
}
if (!{$this->getName()}ValidateNumber(myform.{$this->getName()}.value)) {
    window.alert(\"" . _FORMNUMBER_INCORRECT_ERROR . "\"); myform.{$this->getName()}.focus();
    return false; }";
        return $js;
    }
}
