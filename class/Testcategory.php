<?php

declare(strict_types=1);

namespace XoopsModules\Common;

use Xmf\{
    Request,
    Debug,
    Module\Helper,
    Module\Helper\Session,
};

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once \dirname(__DIR__) . '/include/common.php';

class Testcategory extends CommonObject {

    /**
     * @var moduleHelper
     */
    public $moduleHelper;

    /** @var \XoopsMySQLDatabase */
    public $db;
    /**
     */
//TODO verificare il senso di $id
//    public function __construct($id = null) {
    public function __construct() {
        /** @var Helper $this->helper */
        $this->moduleHelper = \Xmf\Module\Helper::getHelper('common');
        /** @var \XoopsMySQLDatabase $db */
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        //
//        parent::__construct($id);
        parent::__construct();;
        //
        $this->initVar('category_id', XOBJ_DTYPE_INT, 0, false); // category_id: default 0 (root)
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 127);
    }

    /**
     * This method return values as array ready for html output
     *
     * @return  array
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1) {
        xoops_load('XoopsUserUtility');
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
    public function setValues($default = array(), $hash = 'default') {
        parent::setValues($default, $hash);
        // category_id
        $category_id = Request::getInt('category_id', 0, $hash);
        $this->setVar('category_id', $category_id);
        // name
        $this->setVar('name', trim(\Request::getString('nome', '', $hash)));
        //
        return true;
    }

}
