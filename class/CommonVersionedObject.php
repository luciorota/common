<?php

use Xmf\Debug;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;
use Xmf\Request;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';



abstract class CommonVersionedObject extends CommonObject
{

    public $itemName;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->commonHelper = \Xmf\Module\Helper::getHelper('common');
        //
        parent::__construct();
        //
        $this->initVar('prev_id', XOBJ_DTYPE_INT, null, false); // INT(10) UNSIGNED NULL DEFAULT NULL
        $this->initVar('next_id', XOBJ_DTYPE_INT, null, false); // INT(10) UNSIGNED NULL DEFAULT NULL
        $this->initVar('version', XOBJ_DTYPE_INT, 0, false); // INT(10) UNSIGNED NULL DEFAULT 0
        //
        $this->initVar('updated_uid', XOBJ_DTYPE_INT, 0, false); //  MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL
        $this->initVar('updated', XOBJ_DTYPE_OTHER, date(_DBTIMESTAMPSTRING), false); // DATETIME: 'YYYY-MM-DD HH:MM:SS'

    }

    /**
     * assign a value to a variable (also null is allowed)
     *
     * @access public
     * @param string $key   name of the variable to assign
     * @param mixed  $value value to assign
     * @param bool   $not_gpc
     */
    public function setVar($key, $value, $not_gpc = false)
    {
//TODO think about this method
        return parent::setVar($key, $value, $not_gpc);
    }

    /**
     * This method return values as array ready for html output
     *
     * @return  array
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1)
    {
        $values = parent::getValues($keys, $format, $maxDepth);
        $values['updated_uid_uname'] = \XoopsUserUtility::getUnameFromId($values['updated_uid']);
        $values['updated_date_data'] = \XoopsLocal::formatTimestamp($values['updated'], 'l');
        //
        return $values;
    }

    /**
     * This method get form values ready for database insert
     *
     * @return  bool
     */
    public function setValues($default = [], $hash = 'default')
    {
//        $this->setVar('weight', Request::getInt('weight', 0, $hash));
//        $this->setVar('category_id', Request::getInt('category_id', 0, $hash));
        //
        return true;
    }
    
    public function getPrevObj() // get previous object version
    {
//TODO
    }
    public function getNextObj() // get next object version
    {
//TODO
    }
    public function getLatestObj() // get latest/most recent object version (next_id IS NULL)
    {
//TODO
    }
    public function getOldestObj() // get oldest/first object version (prev_id IS NULL)
    {
//TODO
    }
    

}
