<?php

declare(strict_types=1);

namespace XoopsModules\Common;

use Xmf\{
    Request,
    Debug,
    Module\Helper,
    Module\Helper\Session
};

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once \dirname(__DIR__) . '/include/common.php';

//xoops_load('Object', 'common');

/**
 * Class Testobject
 */
class Testobject extends CommonObject {

    /**
     * @var moduleHelper
     */
    public $moduleHelper;

    /** @var \XoopsMySQLDatabase */
    public $db;

    /**
     * @param null|int $id
     */
    public function __construct($id = null) {
        /** @var Helper $this->helper */
        $this->moduleHelper = \Xmf\Module\Helper::getHelper('common');
        /** @var \XoopsMySQLDatabase $db */
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        //
        parent::__construct($id);
        //
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 255);
    }

    /**
     * This method return values as array ready for html output
     *
     * @return  array
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1) {
        xoops_load('XoopsUserUtility');
        $XoopsLocal = new \XoopsLocal();
        //
        $values = parent::getValues($keys, $format, $maxDepth);
        //
        return $values;
    }

    /**
     * This method get form values ready for database insert
     *
     * @return  bool
     */
    public function setValues($default = [], $hash = 'default') {
        parent::setValues($default, $hash);
        // name
        $this->setVar('name', trim(Request::getString('name', '', $hash)));
        //
        return true;
    }

    /**
     * @return \XoopsModules\Common\ThemedForm
     */
    public function getForm($action = false) {
        if ($action === false) {
            $action = Request::getText('REQUEST_URI', '', 'SERVER');
        }

        \xoops_load('XoopsFormLoader');
//        \xoops_load('ThemedForm', 'common');
        $formObj = new ThemedForm($this->isNew() ? _ADD : _EDIT, 'testobjectForm', $action, 'POST', true);
        $formObj->setExtra('enctype="multipart/form-data"');
        $formObj->setClass('form-horizontal');
        // name
        $formObj->addElement(new \XoopsFormText('name', 'name', 10, 255, $this->getVar('name')), true);
        // weight
//        \xoops_load('FormNumber', 'common');
        $formObj->addElement(new FormNumber('weight', 'weight', $this->getVar('weight'), 255, 0, 1));
        // captcha
        if ($this->moduleHelper->isUserAdmin()) {
            // NOP
        } else {
            $formObj->insertBreak();
            xoops_load('xoopscaptcha');
            $formObj->addElement(new \XoopsFormCaptcha(), true);
        }
        // id
        if (!$this->isNew()) {
            $formObj->addElement(new \XoopsFormHidden('id', $this->getVar('id'), false));
        }
        // button tray
        $button_tray = new \XoopsFormElementTray('', '');
        $button_tray->addElement(new \XoopsFormHidden('op', 'save'));
        //
        $button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
        $button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
        $button_submit->setClass('btn-primary');
        $button_tray->addElement($button_submit);
        //
        $button_cancel = new \XoopsFormButton('', 'cancel', _BACK, 'button');
        $button_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($button_cancel);
        //
        $formObj->addElement($button_tray);
        //
        return $formObj;
    }

}
