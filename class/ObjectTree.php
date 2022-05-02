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

include_once XOOPS_ROOT_PATH . '/class/tree.php';

/**
 * Form element that ...
 */
class ObjectTree extends \XoopsObjectTree
{
    /**
     * Make options for a select box from
     *
     * @param string $fieldName    Name of the member variable from the node objects that should be used as the title for the options.
     * @param int    $key          ID of the object to display as the root of select options
     * @param array  $optionsArray (reference to a string when called from outside) Result from previous recursions
     * @param string $prefix_orig  String to indent items at deeper levels
     * @param string $prefix_curr  String to indent the current item
     *
     * @return string
     * @access private
     */
    public function _makeSelBoxOptionsArray($fieldName, $key, &$optionsArray, $prefix_orig, $prefix_curr = '')
    {
        if ($key > 0) {
            $value = $this->tree[$key]['obj']->getVar( $this->myId );
            $optionsArray[$value] = $prefix_curr . $this->tree[$key]['obj']->getVar($fieldName);
            $prefix_curr .= $prefix_orig;
        }
        if (isset($this->tree[$key]['child']) && !empty($this->tree[$key]['child'])) {
            foreach ($this->tree[$key]['child'] as $childkey) {
                $this->_makeSelBoxOptionsArray($fieldName, $childkey, $optionsArray, $prefix_orig, $prefix_curr);
            }
        }

        return $optionsArray;
    }

    /**
     * Make a select box with options from the tree
     *
     * @param  string   $fieldName      Name of the member variable from the node objects that should be used as the title for the options.
     * @param  string   $prefix         String to indent deeper levels
     * @param  bool     $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
     * @param  integer  $key            ID of the object to display as the root of select options
     * @return string    $optionsArray   Associative array of value->name pairs, useful for {@link XoopsFormSelect}->addOptionArray method
     */
    public function makeSelBoxOptionsArray($fieldName, $prefix = '-', $addEmptyOption = false, $key = 0)
    {
        $optionsArray = array();
        if ($addEmptyOption)
            $optionsArray[0] = '';

        return $this->_makeSelBoxOptionsArray($fieldName, $key, $optionsArray, $prefix);
    }

    /**
     * Make a select box with options from the tree
     *
     * @param  string   $name           Name of the select box
     * @param  string   $fieldName      Name of the member variable from the
     *                                  node objects that should be used as the title for the options.
     * @param  string   $prefix         String to indent deeper levels
     * @param  string   $selected       Value to display as selected
     * @param  bool     $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
     * @param  integer  $key            ID of the object to display as the root of select options
     * @param  string   $extra
     * @return string   HTML select box
     */
    public function makeSelBox($name, $fieldName, $prefix = '-', $selected = '', $addEmptyOption = false, $key = 0, $extra = '')
    {
        $ret = '<select name="' . $name . '" id="' . $name . '" ' . $extra . '>';
        if (is_array($addEmptyOption)) {
            foreach($addEmptyOption as $key => $value) {
                $ret .= '<option value="' . $key . '">' . $value . '</option>';
            }
        } elseif (false != $addEmptyOption) {
            $ret .= '<option value="0"></option>';
        }
        $this->_makeSelBoxOptions($fieldName, $selected, $key, $ret, $prefix);

        return $ret . '</select>';
    }
}
