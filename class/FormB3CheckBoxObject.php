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
define('FORMB3CHECKBOXOBJECT_FILENAME', basename($currentPath));
define('FORMB3CHECKBOXOBJECT_PATH', dirname($currentPath));
define('FORMB3CHECKBOXOBJECT_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMB3CHECKBOXOBJECT_URL', XOOPS_URL . '/' . FORMB3CHECKBOXOBJECT_REL_URL . '/' . FORMB3CHECKBOXOBJECT_FILENAME);
define('FORMB3CHECKBOXOBJECT_JS_REL_URL', FORMB3CHECKBOXOBJECT_REL_URL . '/formb3checkboxobject/js');
define('FORMB3CHECKBOXOBJECT_CSS_REL_URL', FORMB3CHECKBOXOBJECT_REL_URL . '/formb3checkboxobject/css');
define('FORMB3CHECKBOXOBJECT_IMAGES_REL_URL', FORMB3CHECKBOXOBJECT_REL_URL . '/formb3checkboxobject/images');

xoops_load('XoopsFormLoader');

class FormB3CheckBoxObject extends \XoopsFormElement
{
    private $_id = null;
    private $_objectHandler = null;
    private $_objects = null;
    private $_objectCriteria = null;
    private $_vars = [];
    private $_values = [];
    private $_columnTitles = [];
    private $_size = false;

    
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param object $objectHandler
     * @param object $objectCriteria
     * @param mixed  $values        Either one value as a string or an array of them.
     * @param bool|strig  $size         
     */
    public function __construct($caption, $name, $objectHandler, $columnTitles = [], $vars = [], $objectCriteria = null, $values = null, $size = false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setId($name);
        if (isset($values)) {
            $this->setValues($values);
        }
        $this->_objectHandler = $objectHandler;
        $this->_columnTitles = $columnTitles;
        $this->_vars = $vars;
        $this->_objectCriteria = $objectCriteria;
        $this->_size = $size;
        $this->_objects = ($this->_objectHandler)->getObjects($this->_objectCriteria, true, true);
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
     * prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        static $isCommonFormB3CheckBoxObjectIncluded = false;
        $commonJs = ''; // redered only once in head
        $headJs = ''; // rendred in head
        $css = ''; // rendered just before html
        $js = ''; // rendered just after html
        $html = '';
        $ret = '';
        // add header js
        $headJs .= "
$(document).ready(function() {
    // First add class for pre-checked entries
    $('#table_{$this->getId()} tbody tr td input[type=checkbox]:checked').each(function() {
        $(this).closest('tr').addClass('info');
    });
    $('#table_{$this->getId()} tbody tr td input[type=checkbox]').on('change', function() {
        $(this).closest('tr').toggleClass('info');
    });
    $('#table_{$this->getId()} thead tr#search_{$this->getId()} th.search').each(function() {
        var title = $('#table_{$this->getId()} thead tr th').eq($(this).index()).text().trim();
        $(this).html('<input type=\'text\' class=\'form-control input-sm\' placeholder=\'" . _SEARCH . " ' + title + '\'>' );
    });";
        $headJs .= "
    table_{$this->getId()} = $('#table_{$this->getId()}').DataTable({
        ";
        if($this->_size === false) {
            $headJs .= "
        'scrollY': 'auto', 'paging': false,
            ";
        } else {
            $headJs .= "
        'scrollY': '{$this->_size}', 'scrollCollapse': true, 'paging': false, 
            ";
        }
        $headJs .= "
        'searching': false,
        'scrollX': false,
        'order': [],
        'columnDefs': [{ 'orderable': false, 'targets': 0 },],
        'lengthMenu': [[-1, 100, 50, 10], ['" . _ALL . "', 100, 50, 10] ], 
        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json'}
    });
    // Apply the search
    table_{$this->getId()}.columns().eq(0).each(function(colIdx) {
        $('input', table_{$this->getId()}.column( colIdx ).header()).on('keyup change clear', function () {
            table_{$this->getId()}
                .column(colIdx)
                .search(this.value)
                .draw();
            });
        });
    });
        ";

        // add common js
        $commonJs = '';
        if (is_object($GLOBALS['xoTheme'])) {
            if ( !$isCommonFormB3CheckBoxObjectIncluded) {
//                $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
//                $GLOBALS['xoTheme']->addStylesheet('https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css');
//                $GLOBALS['xoTheme']->addScript('https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js');
//                $GLOBALS['xoTheme']->addScript('https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js');
                //
                $GLOBALS['xoTheme']->addScript('', [], $commonJs);
                $isCommonFormB3CheckBoxObjectIncluded = true;
            }
            $GLOBALS['xoTheme']->addScript('', [], $headJs);
        } else {
            if (!$isCommonFormB3CheckBoxObjectIncluded) {
//                $ret .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
//                $ret .= "<style type='text/css'>@import url(https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css);</style>\n";
//                $ret .= "<script src='https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js' type='text/javascript'></script>\n";
//                $ret .= "<script src='https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js' type='text/javascript'></script>\n";
                //
                $ret .= "<script type='text/javascript'>\n";
                $ret .= "{$commonJs}\n";
                $ret .= "</script>\n";
                $isCommonFormB3CheckBoxObjectIncluded = true;
            }
            $ret .= "<script type='text/javascript'>\n";
            $ret .= "{$headJs}\n";
            $ret .= "</script>\n";
        }
        // add css
        $css .= "<style>\n";
        $css .= "</style>\n";
        $ret .= $css . "\n";
        // add html
        $html .= "<div style='width:100%;'>\n";
        $html .= "<table id='table_{$this->getId()}' style='width:100%;' class='table table-condensed table-bordered table-hover'>\n";
        $html .= "<thead>\n";
        //
        $html .= "<tr>\n";
        $html .= "<th>&nbsp;</th>\n";
        foreach ($this->_columnTitles as $columnTitle) {
            $html .= "<th>{$columnTitle}</th>\n";
        }
        $html .= "</tr>\n";
        //
        $html .= "<tr id='search_{$this->getId()}'>\n";
        $html .= "<th>&nbsp;</th>\n";
        foreach ($this->_vars as $key) {
            $html .= "<th class='search'>&nbsp;</th>\n";
        }
        $html .= "</tr>\n";
        //
        $html .= "</thead>\n";
        $html .= "<tbody>\n";
        $row = 1;
        foreach($this->_objects as $object_id => $object) {
            $values = $object->getValues();
            $checked = (count($this->getValues()) > 0 && in_array($object_id, $this->getValues())) ? 'checked' : '';
            $html .= "<tr>\n";
            $html .= "<td object_id='{$object_id}'><input type='checkbox' name='{$this->getName()}[]' id='{$this->getId()}{$row}' title='' value='{$object_id}' {$checked}></td>\n";
            foreach ($this->_vars as $key) {
                $html .= "<td>{$values[$key]}</td>";
            }
            $html .= "</tr>\n";
            $row++;
        }
        $html .= "</tbody>\n";
        $html .= "</table>\n";
        $html .= "</div>\n";
        $html .= "<br>\n";
        $ret .= $html . "\n";
        // add js
        $ret .= $js . "\n";
        
        return $ret;
    }
}
