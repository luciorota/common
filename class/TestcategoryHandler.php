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

class TestcategoryHandler extends CommonObjectHandler {

    /**
     * @var moduleHelper, sessionHelper, passds_id
     * @access private
     */
    public $moduleHelper = null;

    /**
     * @param null|object   $db
     */
    public function __construct($db = null, $table = '', $className = '', $keyName = '', $identifierName = '') {
        $this->moduleHelper = \Xmf\Module\Helper::getHelper('common');
        //
        parent::__construct($db, $table, $className, $keyName, $identifierName);
    }

    public function delete(\XoopsObject $object, $force = false) {
        /**
         * delete all subcategories
         */
        $category_id = $object->getVar('id');
        $categoryCriteria = new \CriteriaCompo();
        $categoryCriteria->add(new \Criteria('category_id', $category_id));
        $categoryObjs = $this->getObjects($categoryCriteria);
        foreach ($categoryObjs as $categoryObj) {
            $this->delete($categoryObj, $force);
        }
        return parent::delete($object, $force);
    }

    /**
     * get a array of categorys objects|arrays as an ordered list
     *
     * @param \CriteriaElement|null $tempCriteria {@link CriteriaElement} to match
     * @param bool                  $id_as_key    use the ID as key for the array
     * @param bool                  $as_object    flag indicating as object, otherwise as array
     * @param int                   $category_id
     * @param int                   $level
     * @return array            associative array('id' =>, 'parentId' =>, 'level' =>,'element' =>) where element are objects/array {@link XoopsObject}
     */
    public function getObjectTreeToArray(\CriteriaElement $tempCriteria = null, $id_as_key = false, $as_object = true, $category_id = 0, $level = 0) {
        $criteria = new \CriteriaCompo();
        if ($tempCriteria != null) {
            $criteria->add($tempCriteria);
        }
        $criteria->add(new \Criteria('category_id', $category_id));
        $childCategoryObjs = $this->getObjects($criteria, true, true);
        //
        $sorted = [];
        if (count($childCategoryObjs) > 0) {
            foreach ($childCategoryObjs as $id => $childCategoryObj) {
                if ($as_object) {
                    $element = $childCategoryObj;
                } else {
                    $element = $childCategoryObj->getValues();
                }
                if ($id_as_key) {
                    $sorted[$id] = [
                        'id' => $id,
                        'parentId' => $category_id,
                        'level' => $level,
                        'element' => $element
                    ];
                } else {
                    $sorted[] = [
                        'id' => $id,
                        'parentId' => $category_id,
                        'level' => $level,
                        'element' => $element
                    ];
                }
                if ($subSorted = $this->{__FUNCTION__}($tempCriteria, $id_as_key, $as_object, $id, $level + 1)) {
                    $sorted = array_merge($sorted, $subSorted);
                }
            }
        }
        return $sorted;
    }

    /**
     * get a tree of categorys objects|arrays as an ordered list
     * getObjectTreeToArray vrapper
     */
    public function getObjectsList(\CriteriaElement $tempCriteria = null, $id_as_key = false, $as_object = true, $category_id = 0, $level = 0) {
        return $this->getObjectTreeToArray($tempCriteria, $id_as_key, $as_object, $category_id, $level);
    }

    /**
     * get a hierarchy of categorys objects|arrays as an ordered list
     *
     * @param \CriteriaElement|null $tempCriteria
     * @param bool                  $id_as_key use the ID as key for the array
     * @param bool                  $as_object flag indicating as object, otherwise as array
     * @param int                   $category_id
     * @param int                   $level
     * @return array           associative array('id' =>, 'parentId' =>, 'level' =>,'element' =>) where element are objects/array {@link XoopsObject}
     */
    public function getObjectTreeToHerarchy(\CriteriaElement $tempCriteria = null, $id_as_key = false, $as_object = true, $category_id = 0, $level = 0) {
        $criteria = new \CriteriaCompo();
        if ($tempCriteria != null) {
            $criteria->add($tempCriteria);
        }
        $criteria->add(new \Criteria('category_id', $category_id));
        $childCategoryObjs = $this->getObjects($criteria, true, true);
        //
        $sorted = [];
        if (count($childCategoryObjs) > 0) {
            foreach ($childCategoryObjs as $id => $childCategoryObj) {
                if ($as_object) {
                    $element = $childCategoryObj;
                } else {
                    $element = $childCategoryObj->getValues();
                }
                if ($id_as_key) {
                    $sorted[$id] = [
                        'id' => $id,
                        'parentId' => $category_id,
                        'level' => $level,
                        'element' => $element, 
                        'childs' => $this->{__FUNCTION__}($tempCriteria, $id_as_key, $as_object, $id, $level + 1)
                    ];
                } else {
                    $sorted[] = [
                        'id' => $id,
                        'parentId' => $category_id,
                        'level' => $level,
                        'element' => $element,
                        'childs' => $this->{__FUNCTION__}($tempCriteria, $id_as_key, $as_object, $id, $level + 1)
                    ];
                }
            }
        }
        return $sorted;
    }
    
}
