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
use Xmf\Request;

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}
include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

// Disable xoops debugger in dialog window
include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
$xoopsLogger = \XoopsLogger::getInstance();
$xoopsLogger->activated = false;
// Disable error reporting
error_reporting(0);

define('FORMAJAXTEXTAUTOCOMPLETE_FILENAME', basename($currentPath));
define('FORMAJAXTEXTAUTOCOMPLETE_PATH', dirname($currentPath));
define('FORMAJAXTEXTAUTOCOMPLETE_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMAJAXTEXTAUTOCOMPLETE_URL', XOOPS_URL . '/' . FORMAJAXTEXTAUTOCOMPLETE_REL_URL . '/' . FORMAJAXTEXTAUTOCOMPLETE_FILENAME);

//xoops_load('XoopsRequest');

$moduleDirname = \Request::getString('moduleDirname', 'system', 'POST');
$moduleHelper = \Xmf\Module\Helper::getHelper($moduleDirname);

$handlerName = \Request::getString('handlerName', '', 'POST');
$field = \Request::getString('field', '', 'POST');
$keyword = \Request::getString('keyword', '', 'POST');
if ($keyword != '') {
    $data = $moduleHelper->getHandler($handlerName)->getList(new \Criteria($field, "%{$keyword}%", 'LIKE'));
    echo json_encode($data);
}



if (!class_exists('FormAjaxTextAutocomplete')) {
    xoops_load('XoopsFormElement');

    /**
     * A select field
     *
     * @author 		luciorota <lucio.rota@gmail.com>
     * @copyright   XOOPS Project https://xoops.org/
     * @package 	comuniitaliani
     * @subpackage 	form
     * @access 		public
     */
    class FormAjaxTextAutocomplete extends \XoopsFormText
    {
        private $_id;
        private $_moduleDirname;
        private $_handlerName;
        private $_field;
        private $_min_length;
         /**
         * Constructor
         *
         * @param string $caption   Caption
         * @param string $name      "name" attribute
         * @param int    $size      "size" attribute
         * @param bool   $maxlength "maxlength" attribute
         * @param string $moduleDirname {@link module} dirname
         * @param string $handlerName name of handler to load
         * @param string $field
         * @param int    $min_length should be > 1
         */
        public function __construct($caption, $name, $size, $maxlength, $value = '', $moduleDirname = 'system', $handlerName, $field, $min_length = 3)
        {
            $this->setCaption($caption);
            $this->setName($name);
            $this->_size = (int)$size;
            $this->_maxlength = (int)$maxlength;
            $this->setValue($value);
            //
            $this->_moduleDirname = $moduleDirname;
            $this->_handlerName = $handlerName;
            $this->_field = $field;
            $this->_min_length = (int) $min_length;

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
//TODO verifica senso di questa transcodifica
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
            static $isCommonFormAjaxTextAutocompleteIncluded = false;
            if (is_object($GLOBALS['xoTheme'])) {
                if (!$isCommonFormAjaxTextAutocompleteIncluded) {
                    $isCommonFormAjaxTextAutocompleteIncluded = true;
                    $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                }
            } else {
                if (!$isCommonFormAjaxTextAutocompleteIncluded) {
                    $isCommonFormAjaxTextAutocompleteIncluded = true;
                    $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>\n";
                    $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                }
            }
            // not common code
            $css = "
                #{$this->getName()} {
                }
                #{$this->getName()}results {
                    width: auto;
                    position: absolute;
                    border-left: 1px solid #c0c0c0;
                    border-right: 1px solid #c0c0c0;
                    z-index:255;
                }
                #{$this->getName()}results .item {
                    padding: 3px;
                    font-family: Helvetica;
                    border-bottom: 1px solid #c0c0c0;
                    background-color: white;
                }
                #{$this->getName()}results .item:hover {
                    background-color: #f2f2f2;
                    cursor: pointer;
                }
            ";
            if ($this->_min_length > 0) {
                $js = "
                    $(document).ready(function() {
                        var width = $('#{$this->getName()}').width();
                        $('#{$this->getName()}results').width(width+4);
                        //
                        $('#{$this->getName()}').keyup(function(){
                            var keyword = $('#{$this->getName()}').val();
                            if (keyword.length >= {$this->_min_length}) {
                                $.ajax({
                                    url: '" . FORMAJAXTEXTAUTOCOMPLETE_URL . "',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        moduleDirname: '{$this->_moduleDirname}',
                                        handlerName: '{$this->_handlerName}',
                                        field: '{$this->_field}',
                                        keyword: keyword,
                                    },
                                    success: function (data, state) {
                                        var results = data;
                                        $('#{$this->getName()}results').html('');
                                        for (var key in results) {
                                            $('#{$this->getName()}results').append('<div class=\'item\'>' + results[key] + '</div>');
                                        }
                                        $('.item').click(function(){
                                            var text = $(this).html();
                                            $('#{$this->getName()}').val(text);
                                        })
                                    },
                                    error: function (request, state, error) {
                                        alert('ERROR. Ajax call state: ' + state);
                                    },
                                    complete: function (request, state) {
                                        // NOP
                                    }
                                });
                            } else {
                                $('#{$this->getName()}results').html('');
                            }
                        });

                        $('#{$this->getName()}').blur(function(){
                            $('#{$this->getName()}results').fadeOut(100);
                        })
                        .focus(function() {		
                            $('#{$this->getName()}results').show();
                        });
                    });
                ";
            } else {
                $js = '';
            }
            if (is_object($GLOBALS['xoTheme'])) {
                $GLOBALS['xoTheme']->addScript('','', $js);
                $GLOBALS['xoTheme']->addStylesheet('', array(), $css);
            } else {
                $html .= "<script type='text/javascript'>\n{$js}\n</script>\n";
                $html .= "<style type='text/css'>\n" . $css . "\n</style>\n";
            }
            $html .= "<input type='text' placeholder='{$this->getCaption()}' name='{$this->getName()}' title='" . $this->getTitle() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='{$this->getMaxlength()}' value='{$this->getValue()}'{$this->getExtra()} />";
            $html .= "<div id='{$this->getName()}results'></div>";
            return $html;
        }
    }
}

// Enable error reporting
error_reporting(1);
