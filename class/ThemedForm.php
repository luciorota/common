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

class ThemedForm extends \XoopsForm {
    /**
     * @var IscrittiIscritti
     * @access public
     */
    private $_dirname;
    private $_moduleHelper;
    private $_tplSource;

    /**
     * Constructor
     **/

    /**
     * ThemedForm::ThemedForm()
     *
     * @param string $title    title of the form
     * @param string $name     "name" attribute for the <form> tag
     * @param string $action   "action" attribute for the <form> tag
     * @param string $method   "method" attribute for the <form> tag
     * @param bool   $addtoken whether to add a security token to the form
     * @param string $summary
     * @param string $tplSource
     */
    public function __construct($title, $name, $action, $method = 'post', $addtoken = false, $summary = '', $tplSource = '') {
        $this->_dirname = basename(dirname(__DIR__));
        $this->_moduleHelper = \Xmf\Module\Helper::getHelper($this->_dirname);
        $this->_tplSource = $tplSource;
        parent::__construct($title, $name, $action, $method, $addtoken, $summary);
    }

    public function insertBreak($extra = '', $class= '') {
        $class = ($class != '') ? " class='$class'" : '';
        //Fix for $extra tag not showing
        if ($extra) {
            $extra = "<tr><td colspan='2' $class>$extra</td></tr>";
            $this->addElement($extra);
        } else {
            $extra = "<tr><td colspan='2' $class>&nbsp;</td></tr>";
            $this->addElement($extra);
        }
    }

    public function render() {
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            include_once $GLOBALS['xoops']->path('/class/theme.php');
            $GLOBALS['xoTheme'] = new \xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('/class/template.php');
        $formTpl = new \XoopsTpl();
        //
        $form = array();
        $form['getName'] = $this->getName();
        $form['getAction'] = $this->getAction();
        $form['getTitle'] = $this->getTitle();
        $form['getMethod'] = $this->getMethod();
        $form['getExtra'] = $this->getExtra();
        $form['getSummary'] = $this->getSummary();
        $formTpl->assign('form', $form);
        //
        $formElementObjs = $this->getElements();
        foreach ($formElementObjs as $formElementObj) {
            $formElement = array();

            if (is_string($formElementObj)) {
                $formElement['is_string'] = true;
                //
                $formElement['render'] = $formElementObj;
            } else {
                $formElement['is_string'] = false;
                //
             	$formElement['getAccessKey'] = $formElementObj->getAccessKey();
//???             	$formElement['getAccessString'] = $formElementObj->getAccessString();
             	$formElement['getCaption'] = $formElementObj->getCaption();
             	$formElement['getClass'] = $formElementObj->getClass();
             	$formElement['getDescription'] = $formElementObj->getDescription();
             	$formElement['getExtra'] = $formElementObj->getExtra();
             	$formElement['getFormType'] = $formElementObj->getFormType();
             	$formElement['getName'] = $formElementObj->getName();
             	$formElement['getNocolspan'] = $formElementObj->getNocolspan();
             	$formElement['getTitle'] = $formElementObj->getTitle();
             	$formElement['isContainer'] = $formElementObj->isContainer();
             	$formElement['isHidden'] = $formElementObj->isHidden();
             	$formElement['isRequired'] = $formElementObj->isRequired();
             	$formElement['render'] = $formElementObj->render();
             	$formElement['renderValidationJS'] = $formElementObj->renderValidationJS();
            }
            $formTpl->append('formElements', $formElement);
            unset($formElement);
        }
        //
        if ($this->_tplSource != '') {
            $ret = $formTpl->fetchFromData($this->_tplSource, false, null);
        } else {
            $ret = $formTpl->fetch("db:{$this->_dirname}_co_themedform.tpl");
        }
        $ret .= $this->renderValidationJS(true);
        unset($formTpl);
        return $ret;
    }
}
