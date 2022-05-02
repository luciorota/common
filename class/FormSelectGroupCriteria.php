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

xoops_loadLanguage('formselectgroupcriteria', 'common');
xoops_load('XoopsFormLoader');

/**
 * A select field with a choice of available groups
 */
class FormSelectGroupCriteria extends \XoopsFormSelect {

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool $include_anon Include group "anonymous"?
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list.
     * @param bool $multiple Allow multiple selections?
     * @param onject $criteria Sort/groups criteria
     */
    public function __construct($caption, $name, $include_anon = false, $value = null, $size = 1, $multiple = false, $criteria = null) {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        parent::__construct($caption, $name, $value, $size, $multiple);
        $groupCriteria = new \CriteriaCompo();
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $groupCriteria->add($criteria);
            $groupCriteria->setSort($criteria->getSort());
            $groupCriteria->setOrder($criteria->getOrder());
        }
        if (!$include_anon) {
            $groupCriteria->add(new \Criteria('groupid', XOOPS_GROUP_ANONYMOUS, '!='));
        } else {
            // NOP
        }
        //
        $groups = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM ' . $this->db->prefix('groups');
        if (isset($groupCriteria) && is_subclass_of($groupCriteria, 'criteriaelement')) {
            $sql .= ' ' . $groupCriteria->renderWhere();
            $limit = $groupCriteria->getLimit();
            $start = $groupCriteria->getStart();
            if ($sort = $groupCriteria->getSort()) {
                $sql .= " ORDER BY {$sort} " . $groupCriteria->getOrder();
            }
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $groups[$myrow['groupid']] = $myrow['name'];
        }
        $this->addOptionArray($groups);
    }

}
