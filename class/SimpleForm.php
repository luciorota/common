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

xoops_load('XoopsForm');

/**
 * Form that will output as a simple HTML form with minimum formatting
 */
class SimpleForm extends \XoopsForm
{
    /**
     * create HTML to output the form with minimal formatting
     *
     * @return string
     */
    public function render()
    {
        $ret = $this->getTitle() . "\n<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'" . $this->getExtra() . ">\n";
        foreach ($this->getElements() as $formElement) {
            if (!is_object($formElement)) {
                $ret .= $formElement;
            } else if (!$formElement->isHidden()) {
                $ret .= '' . $formElement->render() . ' &nbsp;' . $formElement->getCaption() . "<br />\n";
            } else {
                $ret .= $formElement->render() . "\n";
            }
        }
        $ret .= "</form>\n";
        return $ret;
    }
}
