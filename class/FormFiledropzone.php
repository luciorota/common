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

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos( $currentPath, "\\\\", 2 ) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}

define('FORMFILEDROPZONE_FILENAME', basename($currentPath));
define('FORMFILEDROPZONE_PATH', dirname($currentPath));
define('FORMFILEDROPZONE_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMFILEDROPZONE_URL', XOOPS_URL . '/' . FORMFILEDROPZONE_REL_URL);
define('FORMFILEDROPZONE_JS_REL_URL', FORMFILEDROPZONE_REL_URL . '/formfiledropzone/dropzone');
define('FORMFILEDROPZONE_CSS_REL_URL', FORMFILEDROPZONE_REL_URL . '/formfiledropzone/dropzone');
define('FORMFILEDROPZONE_IMAGES_REL_URL', FORMFILEDROPZONE_REL_URL . '/formfiledropzone/dropzone');
define('FORMFILEDROPZONE_JS_URL', FORMFILEDROPZONE_URL . '/formfiledropzone/dropzone');
define('FORMFILEDROPZONE_CSS_URL', FORMFILEDROPZONE_URL . '/formfiledropzone/dropzone');
define('FORMFILEDROPZONE_IMAGES_URL', FORMFILEDROPZONE_URL . '/formfiledropzone/dropzone');

xoops_loadLanguage('formfiledropzone', $GLOBALS['xoopsModule']->getVar('dirname'));
xoops_load('XoopsFormLoader');

class FormFiledropzone extends \XoopsFormText
{
    private $_id;
    private $_moduleDirname;
    private $_field;
    private $_segreteria_ids;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param string $value
     */
    public function __construct($caption, $name, $value = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setValue($value);
    }

    /**
     * set the "id" attribute for the element
     *
     */
    public function setId()
    {
        $this->_id = md5(uniqid(mt_rand(), true));
    }

    /**
     * get the "id" attribute for the element
     *
     * @param bool $encode
     *
     * @return string "name" attribute
     */
    public function getId($encode = true)
    {
        if (false != $encode) {
            return str_replace('&amp;', '&', htmlspecialchars($this->_id, ENT_QUOTES));
        }

        return $this->_id;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $html = "\n";
        // common code
        static $isCommonFormFiledropzoneIncluded = false;
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormFiledropzoneIncluded) {
                $isCommonFormFiledropzoneIncluded = true;
                $GLOBALS['xoTheme']->addStylesheet(FORMFILEDROPZONE_CSS_URL . '/dropzone.css');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                $GLOBALS['xoTheme']->addScript(FORMFILEDROPZONE_JS_URL . '/dropzone.js');
            }
        } else {
            if (!$isCommonFormFiledropzoneIncluded) {
                $isCommonFormFiledropzoneIncluded = true;
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>\n";
                $html .= "<style type='text/css'>@import url(" . FORMFILEDROPZONE_CSS_URL . '/dropzone.css' . ");</style>\n";
                $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMFILEDROPZONE_JS_URL . "/dropzone.js' type='text/javascript'></script>\n";
            }
        }
        // not common code
        $css = '';
        $js = "
            $( document ).ready(function() {
//            var myDropzone = new Dropzone('div#{$this->getName()}dropzone', { url: '/'});
                // Dropzone class:
//                $('#{$this->getName()}dropzone').dropzone({
//                    url: '/',
//                    paramName: '{$this->getName()}', // The name that will be used to transfer the file
//                    maxFilesize: 2, // MB
//                });
            });
        ";
        if (is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addStylesheet('', array(), $css);
            $GLOBALS['xoTheme']->addScript('','', $js);
        } else {
            $html .= "<style type='text/css'>\n{$css}\n</style>\n";
            $html .= "<script type='text/javascript'>\n{$js}\n</script>\n";
        }
        $html .= "
            <script>

            </script>\n";
        $html .= "<div id='{$this->getName()}dropzone' class='dropzone'>";
        $html .= "<div class='fallback'>";
        $html .= "<input type='file' name='{$this->getName()}' title='{$this->getTitle()}' id='{$this->getName()}' class='{$this->getClass()}' value='{$this->getValue()}' {$this->getExtra()} />";
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
