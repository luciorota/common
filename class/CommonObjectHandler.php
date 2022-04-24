<?php

namespace XoopsModules\Common;

use Xmf\Debug;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;
use Xmf\Request;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

abstract class CommonObjectHandler extends \XoopsPersistableObjectHandler {

    /**
     * @var commonHelper
     */
    var $commonHelper;

    /**
     * @param null|object   $db
     */
    public function __construct($db = null, $table = '', $className = '', $keyName = '', $identifierName = '') {
        parent::__construct($db, $table, $className, $keyName, $identifierName);
        $this->commonHelper = \Xmf\Module\Helper::getHelper('common');
    }

    public function create($isNew = true) {
        $object = parent::create($isNew);
        return $object;
    }

    /**
     * *#@+
     * Methods of write handler {@link XoopsObjectWrite}
     */

    /**
     * insert an object into the database
     *
     * @param  \XoopsObject $object {@link \XoopsObject} reference to object
     * @param  bool        $force  flag to force the query execution despite security settings
     * @return mixed       object ID
     */
    public function insert(\XoopsObject $object, $force = true) {
        if ($object->isNew()) {
            // created_uid, created
            $object->setVar('created', date(_DBTIMESTAMPSTRING));
            $object->setVar('created_uid', !empty($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getVar('uid') : 0);
            $object->setVar('modified_uid', 0);
        } else {
            // modified_uid, modified
            $object->setVar('modified', date(_DBTIMESTAMPSTRING));
            $object->setVar('modified_uid', !empty($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getVar('uid') : 0);
        }
//        $handler = $this->loadHandler('write');
//        return $handler->insert($object, $force);
        $ret = parent::insert($object, $force);
        // handle null values
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $vars = $object->getVars();
        foreach ($vars as $key => $value) {
            if (is_null($value['value']) && ($value['data_type'] != 0)) {
                $sql = "UPDATE `{$this->table}` SET `{$key}` = NULL WHERE `{$this->keyName}` = {$this->db->quote($object->getVar($this->keyName))}";
                if (!$result = $this->db->{$queryFunc}($sql)) {
                    //return false;
                }
            }
        }
        return $ret;
    }

    /**
     * get inserted id
     *
     * @param null
     * @return int reference to the object
     */
    public function getInsertId() {
        return $this->db->getInsertId();
    }

    public function deleteAll(\CriteriaElement $criteria = null, $force = true, $asObject = false) {
        return parent::deleteAll($criteria, $force, true); // delete ALWAYS as object
    }

    public function &getObjects(\CriteriaElement $tempCriteria = null, $id_as_key = false, $as_object = true) {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        if (null !== $tempCriteria) {
            $criteria->add($tempCriteria);
            if (!empty($tempCriteria->getSort())) {
                $criteria->setSort($tempCriteria->getSort());
            }
            if (!empty($tempCriteria->getOrder())) {
                $criteria->setOrder($tempCriteria->getOrder());
            }
        }
        //
        return parent::getObjects($criteria, $id_as_key, $as_object);
    }

    public function &getAll(\CriteriaElement $tempCriteria = null, $fields = null, $asObject = true, $id_as_key = true) {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        if (null !== $tempCriteria) {
            $criteria->add($tempCriteria);
            if (!empty($tempCriteria->getSort())) {
                $criteria->setSort($tempCriteria->getSort());
            }
            if (!empty($tempCriteria->getOrder())) {
                $criteria->setOrder($tempCriteria->getOrder());
            }
        }
        //
        return parent::getAll($criteria, $fields, $asObject, $id_as_key);
    }

    public function getList(\CriteriaElement $tempCriteria = null, $limit = 0, $start = 0) {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        if (null !== $tempCriteria) {
            $criteria->add($tempCriteria);
            if (!empty($tempCriteria->getSort())) {
                $criteria->setSort($tempCriteria->getSort());
            }
            if (!empty($tempCriteria->getOrder())) {
                $criteria->setOrder($tempCriteria->getOrder());
            }
        }
        //
        return parent::getList($criteria, $limit, $start);
    }

    public function &getIds(\CriteriaElement $tempCriteria = null) {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        if (null !== $tempCriteria) {
            $criteria->add($tempCriteria);
            if (!empty($tempCriteria->getSort())) {
                $criteria->setSort($tempCriteria->getSort());
            }
            if (!empty($tempCriteria->getOrder())) {
                $criteria->setOrder($tempCriteria->getOrder());
            }
        }
        //
        return parent::getIds($criteria);
    }

    public function &getValues(\CriteriaElement $tempCriteria = null) {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        if (null !== $tempCriteria) {
            $criteria->add($tempCriteria);
            if (!empty($tempCriteria->getSort())) {
                $criteria->setSort($tempCriteria->getSort());
            }
            if (!empty($tempCriteria->getOrder())) {
                $criteria->setOrder($tempCriteria->getOrder());
            }
        }
        //
        $objs = $this->getObjects($criteria, true, true); // id as key, as object
        $values = [];
        foreach ($objs as $id => $obj) {
            $value = $obj->getValues();
            $values[$id] = $value;
        }
        //
        return $values;
    }

    /**
     * clone an existing object but as s new object
     *
     * @param  \XoopsObject $object {@link \XoopsObject} reference to object
     * @return \XoopsObject
     */
    public function clone(\XoopsObject $object) {
        $notClonableKeys = [$this->keyName];

        $class = get_class($object);
        $cloneObject = new $this->className();
        foreach ($object->vars as $key => $var) {
            if (in_array($key, $notClonableKeys))
                continue;
            $cloneObject->setVar($key, $var['value']);
        }
        // need this to notify the handler class that this is a newly created object
        $cloneObject->setNew();
        //
        return $cloneObject;
    }

}
