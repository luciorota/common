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
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}
define('FORMB3TAGSINPUT_FILENAME', basename($currentPath));
define('FORMB3TAGSINPUT_PATH', dirname($currentPath));
define('FORMB3TAGSINPUT_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3TAGSINPUT_URL', XOOPS_URL . '/' . FORMB3TAGSINPUT_REL_URL . '/' . FORMB3TAGSINPUT_FILENAME);
define('FORMB3TAGSINPUT_JS_REL_URL', FORMB3TAGSINPUT_REL_URL . '/formb3tagsinput/js');
define('FORMB3TAGSINPUT_CSS_REL_URL', FORMB3TAGSINPUT_REL_URL . '/formb3tagsinput/css');
define('FORMB3TAGSINPUT_IMAGES_REL_URL', FORMB3TAGSINPUT_REL_URL . '/formb3tagsinput/images');

xoops_load('XoopsFormLoader');

/**
 * FormB3Tagsinput
 *
 * Bootstrap Tags Input with Autocomplete
 * jQuery plugin providing a Twitter Bootstrap user interface for managing tags
 * https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
 *
 */
class FormB3Tagsinput extends \XoopsFormElement {

    private $_id;
    private $_values = [];
    private $_tags = [];
    private $_availableTags = [];
    private $_freeInput = true;
    private $_limit = 0;
    private $_maxChars = 255;
    private $_allowDuplicates = false;
    private $_tagClass = '';

    /**
     * Constructor
     *
     * @param string $caption       Caption
     * @param string $name          "name" attribute
     * @param array  $values        array of tags
     * @param array  $availableTags array of availabl tags
     * @param bool   $freeInput     Allow creating tags which are not returned by typeahead's source (default: true)
     * @param int    $limit         Max number of tags (0 = unlimited)
     * @param int    $maxChars      Max tag lenght
     * @param bool   $allowDuplicates
     *
     */
    public function __construct(
            $caption,
            $name,
            $values = [],
            $availableTags = [],
            $freeInput = true,
            $limit = 0,
            $maxChars = 255,
            $allowDuplicates = false,
            $tagClass = 'label label-success'
    ) {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        $this->setValues($values);
        //
        $this->_tags = $values;
        $this->_availableTags = $availableTags;
        $this->_freeInput = $freeInput;
        $this->_limit = (int) $limit;
        $this->_maxChars = (int) $maxChars;
        $this->_allowDuplicates = $allowDuplicates;
        $this->_tagClass = (string) $tagClass;
    }

    /**
     * set the "id" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setId($name = null) {
        $this->_id = $name === null ? md5(uniqid(mt_rand(), true)) : $name;
    }

    /**
     * get the "id" attribute for the element
     *
     * @param bool $encode
     *
     * @return string "name" attribute
     */
    public function getId($encode = true) {
        if (false != $encode) {
            return str_replace('&amp;', '&', htmlspecialchars($this->_id, ENT_QUOTES));
        }
        return $this->_id;
    }

    /**
     * Set pre-selected values
     *
     * @param mixed $values
     */
    public function setValues($values) {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->_values[$key] = $value;
            }
        } elseif (isset($values)) {
            $this->_values[] = $values;
        }
    }

    /**
     * Get an array of pre-selected values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValues($encode = false) {
        if (!$encode) {
            return $this->_values;
        }
        $values = [];
        foreach ($this->_values as $key => $value) {
            $values[$key] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
        }
        return $values;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        static $isCommonFormB3TagsinputIncluded = false;
        $commonJs = ''; // redered only once in head
        $headJs = ''; // redered in head
        $js = ''; // rendered just after html
        $commonCss = ''; // redered only once in head
        $headCss = ''; // redered in head
        $css = ''; // rendered just before html

        $html = '';
        // add common js
        // add css js
        if (is_object($GLOBALS['xoTheme'])) {
            if (!$isCommonFormB3TagsinputIncluded) {
                $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/bootstrap-tagsinput.css');
                $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/bootstrap-tokenfield.css');
                $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/typeahead.css');
                //$GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_JS_REL_URL . '/bootstrap-tagsinput.js');
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_JS_REL_URL . '/typeahead.js');
                //$GLOBALS['xoTheme']->addScript('', [], $commonJs);
                //
                $isCommonFormB3TagsinputIncluded = true;
            }
            $GLOBALS['xoTheme']->addScript('', [], $headJs);
            $GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
        } else {
            if (!$isCommonFormB3TagsinputIncluded) {
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/bootstrap-multiselect.css' . ");</style>\n";
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/bootstrap-tokenfield.css' . ");</style>\n";
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_CSS_REL_URL . '/typeahead.css' . ");</style>\n";
                //$html .= "<style>\n" . $commonCss . "\n" . "</style>\n";
                $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_JS_REL_URL . '/bootstrap-tagsinput.js' . "' type='text/javascript'></script>\n";
                $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB3TAGSINPUT_JS_REL_URL . '/typeahead.js' . "' type='text/javascript'></script>\n";

                //$html .= "<script type='text/javascript'>\n" . $commonJs . "\n" . "</script>\n";
                $isCommonFormB3TagsinputIncluded = true;
            }
        }
        // add css just before html
        $css .= "<style>\n";
        $css .= "</style>\n";
        $html .= $css . "\n";
        //
        // html
        $html .= "<select {$this->getExtra()} name='{$this->getName()}[]' id='{$this->getId()}' title='{$this->getTitle()}' multiple>\n";
        foreach ($this->getValues() as $key => $value) {
            $optionName = $value;
            $html .= "<option value='" . htmlspecialchars($optionName, ENT_QUOTES) . "'>{$optionName}</option>\n";
        }
        $html .= "</select>\n";
        //
        // add js just after html
        $js .= "<script type='text/javascript'>\n";
        $js .= "
            $(document).ready(function() {
                var engine{$this->getId()} = new Bloodhound({
                    local: [
        ";
        foreach ($this->_availableTags as $availableTag) {
            $js .= " '{$availableTag}',";
        }
        $js .= "
                    ],
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    datumTokenizer: Bloodhound.tokenizers.whitespace
                });
                engine{$this->getId()}.initialize();
                $('#{$this->getId()}').tagsinput({
                    limit: {$this->_limit},
                    confirmKeys: [13, 188],
                    maxChars: {$this->_maxChars},
                    focusClass: '',
                    tagClass: '{$this->_tagClass}',
        ";
        $js .= ($this->_freeInput) ? ' freeInput: true,' : ' freeInput: false,';
        $js .= ($this->_allowDuplicates) ? ' allowDuplicates: true,' : ' allowDuplicates: false,';
        $js .= "
                    typeaheadjs: [null, { source: engine{$this->getId()}.ttAdapter() }],
                });
            });
            ";
        $js .= "</script>\n";
        $html .= $js . "\n";
        //
        return $html;
    }

    /**
     * Render custom javascript validation code
     *
     * @seealso XoopsForm::renderValidationJS
     */
    public function renderValidationJS() {
        // render custom validation code if any
        if (!empty($this->customValidationCode)) {
            return implode("\n", $this->customValidationCode);
            // generate validation code if required
        } elseif ($this->isRequired()) {
            $eltname = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg = str_replace('"', '\"', stripslashes($eltmsg));
            return "\nvar hasSelected = false; var selectBox = myform.{$eltname};" . "for (i = 0; i < selectBox.options.length; i++) { if (selectBox.options[i].selected == true && selectBox.options[i].value != '') { hasSelected = true; break; } }" . "if (!hasSelected) { window.alert(\"{$eltmsg}\"); selectBox.focus(); return false; }";
        }
        return '';
    }

}
