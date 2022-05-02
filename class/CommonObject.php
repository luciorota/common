<?php

namespace XoopsModules\Common;

use Xmf\Debug;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;
use Xmf\Request;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class CommonObject
 *
 * MySQL default table structure:
 * CREATE TABLE mod_common_default (
 *   id                      int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   category_id             int(10) unsigned,
 *
 *   created_uid             mediumint(8) unsigned NOT NULL,
 *   modified_uid            mediumint(8) unsigned NOT NULL,
 *   created                 DATETIME,
 *   modified                DATETIME,
 *   PRIMARY KEY (id),
 * ) ENGINE=MyISAM;
 *
 * 
 */

abstract class CommonObject extends \XoopsObject {

    /**
     * @var archivioHelper
     * @access public
     */
    public $commonHelper;
    public $itemName;

    /**
     * constructor
     */
    public function __construct() {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->commonHelper = \Xmf\Module\Helper::getHelper('common');
        //
        parent::__construct();
        //
        $this->initVar('id', XOBJ_DTYPE_INT, null, false); // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false); // INT(10) UNSIGNED NULL DEFAULT 0
        $this->initVar('category_id', XOBJ_DTYPE_INT, 0, false); // INT(10) UNSIGNED NOT NULL, is the parent category id, set to 0 (default) if a no category/root record
        //
        $this->initVar('created', XOBJ_DTYPE_OTHER, date(_DBTIMESTAMPSTRING), false); // DATETIME: 'YYYY-MM-DD HH:MM:SS'
        $this->initVar('created_uid', XOBJ_DTYPE_INT, 0, false); //  MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL
        //
        $this->initVar('modified', XOBJ_DTYPE_OTHER, date(_DBTIMESTAMPSTRING), false); // DATETIME: 'YYYY-MM-DD HH:MM:SS'
        $this->initVar('modified_uid', XOBJ_DTYPE_INT, 0, false); //  MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL
    }

    /**
     * assign a value to a variable (also null is allowed)
     *
     * @access public
     * @param string $key   name of the variable to assign
     * @param mixed  $value value to assign
     * @param bool   $not_gpc
     */
    public function setVar($key, $value, $not_gpc = false) {
        if (!empty($key) && isset($value) && isset($this->vars[$key])) {
            $this->vars[$key]['value'] = & $value;
            $this->vars[$key]['not_gpc'] = $not_gpc;
            $this->vars[$key]['changed'] = true;
            $this->setDirty();
        }
        if (!empty($key) && $value === null && isset($this->vars[$key])) {
            $this->vars[$key]['value'] = null;
            $this->vars[$key]['not_gpc'] = $not_gpc;
            $this->vars[$key]['changed'] = false;
            $this->setDirty();
        }
    }

    /**
     * This method return values as array ready for html output
     *
     * @return  array
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1) {
        $values = parent::getValues($keys, $format, $maxDepth);
        //        
        $dateTimeObj = \DateTime::createFromFormat(_DBTIMESTAMPSTRING, (string)$values['created']);
        $values['created_timestamp'] = ($dateTimeObj === false) ? '' : $dateTimeObj->getTimestamp();
        $values['created_date_data'] = \XoopsLocal::formatTimestamp($values['created_timestamp'], _DATESTRING);
        //
        $values['created_uid_uname'] = \XoopsUserUtility::getUnameFromId($values['created_uid']);
        //
        $dateTimeObj = \DateTime::createFromFormat(_DBTIMESTAMPSTRING, (string)$values['modified']);
        $values['modified_timestamp'] = ($dateTimeObj === false) ? '' : $dateTimeObj->getTimestamp();
        $values['modified_date_data'] = \XoopsLocal::formatTimestamp($values['modified_timestamp'], _DATESTRING);
        //
        $values['modified_uid_uname'] = \XoopsUserUtility::getUnameFromId($values['modified_uid']);
        //
        return $values;
    }

    /**
     * This method get form values ready for database insert
     *
     * @return  bool
     */
    public function setValues($default = [], $hash = 'default') {
        $this->setVar('weight', Request::getInt('weight', 0, $hash));
        $this->setVar('category_id', Request::getInt('category_id', 0, $hash));
        //
        return true;
    }

}
