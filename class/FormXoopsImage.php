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

xoops_loadLanguage('formxoopsimage', 'common');
xoops_load('XoopsFormLoader');

class FormXoopsImage extends \XoopsFormText
{
    /**
     * Initial text
     *
     * @var string
     * @access private
     */
    private $_previewformat;
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param int    $size Size
     * @param int    $maxlength Maximum length of text
     * @param string $value Initial text
     * @param string $previewformat Initial text
     */
    public function __constructor($caption, $name, $size, $maxlength, $value = '', $previewformat = null)
    {
        $this->_size = (int)$size;
        $this->_maxlength = (int)$maxlength;
        $this->setValue($value);
        $this->_previewformat = ($previewformat === null ? "<div style='height:100px;'><img src='%s' style='height:100px;' alt='" . _FORMXOOPSIMAGE_IMAGENOTFOUND . "' /></div>" : $previewformat);
        parent::__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial content
     *
     * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }
    public function getSrc($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    public function getPreviewformat()
    {
        return $this->_previewformat;
    }

    /**
     * Set initial text value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $html = '<div>';
        $html.= "<input type='text' name='{$this->getName()}' title='{$this->getTitle()}' size='{$this->getSize()}' maxlength='{$this->getMaxlength()}' value='{$this->getValue()}' />";
        $html.= "<img src='" . XOOPS_URL . "/images/image.gif' alt='" . _FORMXOOPSIMAGE_IMAGEMANAGER . "' title='" . _FORMXOOPSIMAGE_IMAGEMANAGER . "' onclick='randomId = Math.random().toString(); this.parentNode.firstChild.id = \"input_\" + randomId; openWithSelfMain(&quot;" . XOOPS_URL . "/imagemanager.php?target=input_&quot; + randomId + &quot;&amp;editor=src&quot;,&quot;imagemanager&quot;,800,600);' onmouseover='style.cursor=\"hand\"'/>";
        $html.= sprintf((string)$this->getPreviewformat(), $this->getSrc());
        $html.= '</div>';
        return $html;
    }
}
