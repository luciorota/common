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
if (DIRECTORY_SEPARATOR != "/") {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, "/", $currentPath);
}
define("FORMB4MULTISELECT_FILENAME", basename($currentPath));
define("FORMB4MULTISELECT_PATH", dirname($currentPath));
define("FORMB4MULTISELECT_REL_URL", str_replace(XOOPS_ROOT_PATH . "/", '', dirname($currentPath)));
define("FORMB4MULTISELECT_URL", XOOPS_URL . '/' . FORMB4MULTISELECT_REL_URL . '/' . FORMB4MULTISELECT_FILENAME);
define("FORMB4MULTISELECT_JS_REL_URL", FORMB4MULTISELECT_REL_URL . "/formb4multiselect/js");
define("FORMB4MULTISELECT_CSS_REL_URL", FORMB4MULTISELECT_REL_URL . "/formb4multiselect/css");
define("FORMB4MULTISELECT_IMAGES_REL_URL", FORMB4MULTISELECT_REL_URL . "/formb4multiselect/images");

xoops_load('XoopsFormLoader');

    /**
     * FormB4MultiSelect
     * 
     * Bootstrap Multiselect is a JQuery based plugin to provide an intuitive 
     * user interface for using select inputs with the multiple attribute 
     * present.
     *  
     * Instead of a select a bootstrap button will be shown a 
     * dropdown menu containing the single options as checkboxes.
     * 
     * https://github.com/davidstutz/bootstrap-multiselect
     * The stable release for Boostrap 3 is v0.9.15. 
     *
     *
     */
class FormB4MultiSelect extends \XoopsFormElement
{
    /**
     * Options
     *
     * @var array
     * @access private
     */
    public $_options = [];

    /**
     * Number of rows. "1" makes a dropdown list.
     *
     * @var int
     * @access private
     */
    public $_number;
    
    /**
     * Allow multiple selections?
     *
     * @var bool
     * @access private
     */
    public $_multiple = false;

    /**
     * Delimiter.
     *
     * @var string
     * @access private
     */
    public $_delimiter;

    /**
     * Pre-selcted values
     *
     * @var array
     * @access private
     */
    public $_values = [];

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param mixed  $values    Pre-selected value (or array of them).
     * @param int    $number    Number or rows. "1" makes a drop-down-list
     * @param bool   $multiple  Allow multiple selections?
     * @param string $delimiter Selected options delimiter
     */
    public function __construct($caption, $name, $values = null, $number = 1, $multiple = false, $delimiter = '; ')
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($values)) {
            $this->setValues($values);
        }
        $this->_number = (int)$number;
        $this->_multiple = $multiple;
        $this->_delimiter = $delimiter;
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the number
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_number;
    }

    /**
     * Get an array of pre-selected values
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValues($encode = false)
    {
        if (!$encode) {
            return $this->_values;
        }
        $values = [];
        foreach ($this->_values as $key =>$value) {
            $values[$key] = $value ? htmlspecialchars($value, ENT_QUOTES) : $value;
        }
        return $values;
    }

    /**
     * Set pre-selected values
     *
     * @param mixed $values
     */
    public function setValues($values)
    {
        if (is_array($values)) {
            foreach ($values as $key =>$value) {
                $this->_values[$key] = $value;
            }
        } elseif (isset($values)) {
            $this->_values[] = $values;
        }
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute
     * @param string $name  "name" attribute
     */
    public function addOption($value, $name = '', $description = '')
    {
        if ($name != '') {
            $this->_options[$value] = ['name' => $name, 'description' => $description];
        } else {
            $this->_options[$value] = ['name' => $value, 'description' => $description];
        }
    }

    /**
     * Add multiple options
     *
     * @param array $options Associative array of value->['name' => name, 'description' => description] pairs
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $this->addOption($key, $value['name'], $value['description']);
            }
        }
    }

    /**
     * Get an array with all the options
     *
     * Note: both name and value should be sanitized. However for backward compatibility, only value is sanitized for now.
     *
     * @param bool|int $encode To sanitizer the text? potential values: 0 - skip; 1 - only for value; 2 - for both value and name
     *
     * @return array Associative array of value->name pairs
     */
    public function getOptions($encode = false)
    {
        if (!$encode) {
            return $this->_options;
        }
        $values = [];
        foreach ($this->_options as $key => $value) {
            $values[$encode ? htmlspecialchars($key, ENT_QUOTES) : $key] = ($encode > 1) ? ['name' => htmlspecialchars($value['name'], ENT_QUOTES), 'description' => htmlspecialchars($value['description'], ENT_QUOTES)] : $value;
        }

        return $values;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        static $isCommonFormB4MultiSelectIncluded = false;
        $commonJs = ''; // redered only once in head
        $headJs = ''; // redered in head
        $js = ''; // rendered just after html
        $commonCss = ''; // redered only once in head
        $headCss = ''; // redered in head
        $css = ''; // rendered just before html
    
        $html = '';
        $html = '';
        // add common js
        // add css js
        if (is_object($GLOBALS['xoTheme'])) {
            if ( !$isCommonFormB4MultiSelectIncluded) {
                $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/browse.php?' . FORMB4MULTISELECT_CSS_REL_URL . '/bootstrap-multiselect.css');
                //$GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMB4MULTISELECT_JS_REL_URL . '/bootstrap-multiselect.js');
                //$GLOBALS['xoTheme']->addScript('', [], $commonJs);
                //
                $isCommonFormB4MultiSelectIncluded = true;
            }
            $GLOBALS['xoTheme']->addScript('', [], $headJs);
            $GLOBALS['xoTheme']->addStylesheet('', [], $commonCss);
        } else {
            if (!$isCommonFormB4MultiSelectIncluded) {
                $html .= "<style type='text/css'>@import url(" . XOOPS_URL . '/browse.php?' . FORMB4MULTISELECT_CSS_REL_URL . '/bootstrap-multiselect.css' . ");</style>\n";
                //$html .= "<style>\n" . $commonCss . "\n" . "</style>\n";
                $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMB4MULTISELECT_JS_REL_URL . '/bootstrap-multiselect.js' . "' type='text/javascript'></script>\n";
                //$html .= "<script type='text/javascript'>\n" . $commonJs . "\n" . "</script>\n";
                $isCommonFormSelectObjectIncluded = true;
            }
        }
        // add css just before html
        $css .= "<style>\n";
        $css .= "</style>\n";
        $html .= $css . "\n";
        //
        // html
        $html .= "<select {$this->getExtra()}";
        if ($this->isMultiple() != false) {
            $html .= " name='{$this->getName()}[]' id='{$this->getName()}' title='{$this->getTitle()}' multiple='multiple'>\n";
        } else {
            $html .= " name='{$this->getName()}' id='{$this->getName()}' title='{$this->getTitle()}'>\n";
        }
        foreach ($this->getOptions() as $key => $value) {
            $optionName = $value['name'];
            $optionDescription = $value['description'];
            $html .= "<option description='{$optionDescription}' value='" . htmlspecialchars($key, ENT_QUOTES) . "'";
            if (count($this->getValues()) > 0 && in_array($key, $this->getValues())) {
                $html .= " selected";
            }
            $html .= ">{$optionName}</option>\n";
        }
        $html .= "</select>\n";
        //
        // add js just after html
        $js .= "<script type='text/javascript'>\n";
        $js .= "
            $(document).ready(function() {
                $('#{$this->getName()}').multiselect({
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: '" . _SEARCH . "',
                    nonSelectedText: '" . _NONE . "',
                    nSelectedText: 'selected',
                    allSelectedText: '" . _ALL . "',
                    includeFilterClearBtn: false,
                    includeSelectAllOption: true,
                    selectAllText: ' " . _ALL . "',
                    numberDisplayed: " . $this->_number .",
                    delimiterText: '" . $this->_delimiter . "',
                    optionLabel: function(element) {
                        if ($(element).attr('description') !== '') {
                            return $(element).html() + ' (' + $(element).attr('description') + ')';
                        } else {
                            return $(element).html();                            
                        }
                    }
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
    public function renderValidationJS()
    {
        // render custom validation code if any
        if (!empty($this->customValidationCode)) {
            return implode("\n", $this->customValidationCode);
            // generate validation code if required
        } elseif ($this->isRequired()) {
            $eltname    = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg     = str_replace('"', '\"', stripslashes($eltmsg));

            return "\nvar hasSelected = false; var selectBox = myform.{$eltname};" . "for (i = 0; i < selectBox.options.length; i++) { if (selectBox.options[i].selected == true && selectBox.options[i].value != '') { hasSelected = true; break; } }" . "if (!hasSelected) { window.alert(\"{$eltmsg}\"); selectBox.focus(); return false; }";
        }

        return '';
    }
}
