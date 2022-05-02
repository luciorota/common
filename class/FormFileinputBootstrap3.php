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

define('FORMFILEINPUTBOOTSTRAP3_FILENAME', basename($currentPath));
define('FORMFILEINPUTBOOTSTRAP3_PATH', dirname($currentPath));
define('FORMFILEINPUTBOOTSTRAP3_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMFILEINPUTBOOTSTRAP3_URL', XOOPS_URL . '/' . FORMFILEINPUTBOOTSTRAP3_REL_URL);
define('FORMFILEINPUTBOOTSTRAP3_JS_URL', FORMFILEINPUTBOOTSTRAP3_URL . '/formfileinputbootstrap3/js');
define('FORMFILEINPUTBOOTSTRAP3_CSS_URL', FORMFILEINPUTBOOTSTRAP3_URL . '/formfileinputbootstrap3/css');
define('FORMFILEINPUTBOOTSTRAP3_IMAGES_URL', FORMFILEINPUTBOOTSTRAP3_URL . '/formfileinputbootstrap3/img');
define('FORMFILEINPUTBOOTSTRAP3_THEMES_URL', FORMFILEINPUTBOOTSTRAP3_URL . '/formfileinputbootstrap3/themes');

xoops_loadLanguage('formfileinputbootstrap3', $GLOBALS['xoopsModule']->getVar('dirname'));
xoops_load('XoopsFormLoader');

class FormFileinputBootstrap3 extends \XoopsFormText
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
        $language = substr(_LANGCODE, 0, 2);
        $theme = 'fas';
        $html = "\n";
        // common code
        static $isCommonFormFileinputBootstrap3Included = false;
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormFileinputBootstrap3Included) {
                $isCommonFormFileinputBootstrap3Included = true;
                $GLOBALS['xoTheme']->addStylesheet('https://use.fontawesome.com/releases/v5.5.0/css/all.css');
                //
                $GLOBALS['xoTheme']->addStylesheet(FORMFILEINPUTBOOTSTRAP3_CSS_URL . '/fileinput.css');
                $GLOBALS['xoTheme']->addStylesheet(FORMFILEINPUTBOOTSTRAP3_THEMES_URL . "/{$theme}/theme.css");
                //
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                //
                $GLOBALS['xoTheme']->addScript(FORMFILEINPUTBOOTSTRAP3_JS_URL . '/plugins/piexif.js');
                $GLOBALS['xoTheme']->addScript(FORMFILEINPUTBOOTSTRAP3_JS_URL . '/plugins/sortable.js');
                $GLOBALS['xoTheme']->addScript(FORMFILEINPUTBOOTSTRAP3_JS_URL . '/fileinput.js');
                $GLOBALS['xoTheme']->addScript(FORMFILEINPUTBOOTSTRAP3_JS_URL . "/locales/{$language}.js");
                $GLOBALS['xoTheme']->addStylesheet(FORMFILEINPUTBOOTSTRAP3_THEMES_URL . "/{$theme}/theme.js");
            }
        } else {
            if (!$isCommonFormFileinputBootstrap3Included) {
                $isCommonFormFileinputBootstrap3Included = true;
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>\n";
                $html .= "<style type='text/css'>@import url(" . 'https://use.fontawesome.com/releases/v5.5.0/css/all.css' . ");</style>\n";
                //
                $html .= "<style type='text/css'>@import url(" . FORMFILEINPUTBOOTSTRAP3_CSS_URL . '/fileinput.css' . ");</style>\n";
                $html .= "<style type='text/css'>@import url(" . FORMFILEINPUTBOOTSTRAP3_THEMES_URL . '/{$theme}/theme.css' . ");</style>\n";
                //
                $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascriFORMFILEINPUTBOOTSTRAP3_CSS_URL . '/fileinput.css'pt'></script>\n";
                //
                $html .= '<script src=' . FORMFILEINPUTBOOTSTRAP3_JS_URL . "plugins/piexif.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMFILEINPUTBOOTSTRAP3_JS_URL . "/plugins/sortable.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMFILEINPUTBOOTSTRAP3_JS_URL . "/fileinput.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMFILEINPUTBOOTSTRAP3_JS_URL . "/locales/{$language}.js' type='text/javascript'></script>\n";
                $html .= '<script src=' . FORMFILEINPUTBOOTSTRAP3_THEMES_URL . "/{$theme}/theme.js' type='text/javascript'></script>\n";
            }
        }
        // not common code
        $css = '';
        
        $js = "
    $(document).ready(function () {
        $('#{$this->getName()}').fileinput({
            theme: '{$theme}',
            uploadUrl: '#',
            language: '{$language}',
            allowedFileExtensions: ['jpg', 'png', 'gif'],
            maxFileSize: 1000,
            maxFilesNum: 10,
            overwriteInitial: false,
            initialPreviewAsData: true,
            initialPreview: [
                'http://lorempixel.com/1920/1080/nature/1',
                'http://lorempixel.com/1920/1080/nature/2',
                'http://lorempixel.com/1920/1080/nature/3'
            ],
            initialPreviewConfig: [
                {caption: 'nature-1.jpg', size: 329892, width: '120px', url: '{$url}', key: 1},
                {caption: 'nature-2.jpg', size: 872378, width: '120px', url: '{$url}', key: 2},
                {caption: 'nature-3.jpg', size: 632762, width: '120px', url: '{$url}', key: 3}
            ]
        });
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
        $html .= "<div class='file-loading'>";
        $html .= "<input id='{$this->getName()}' type='file' multiple>";
        $html .= '</div>';
        return $html;
    }
}
