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
 * Iscritti module
 *
 * @copyright       XOOPS Project https://xoops.org/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         common
 * @since           1.00
 * @author          luciorota, studiopas
 * @version         svn:$Id$
 */
use Xmf\Module\Admin;
use XoopsModules\Common\FormSelectGroupCriteria;

require dirname(__FILE__) . '/admin_header.php';

$op = Request::getCmd('op', 'permissions');
switch ($op) {
    default:
    case 'permissions':
        //  admin navigation
        //xoops_cp_header();
        $moduleAdmin = Admin::getInstance();
        //$indexAdmin->displayNavigation($currentFile);
        // buttons
        //$adminMenu = new ModuleAdmin();
        //$adminMenu->addItemButton(_AM_COMMON_PERM_GLOBAL, "{$currentFile}?op=global_perms.list", 'permissions');
        //echo $adminMenu->renderButton();
        //
        //$xoTheme->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
        //$GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
        //
        $GLOBALS['xoopsTpl']->display("db:{$commonHelper->getModule()->dirname()}_am_permissions.tpl");
        //
        include __DIR__ . '/admin_footer.php';
        break;



    case 'global_perms.list':
        //  admin navigation
        xoops_cp_header();
        $indexAdmin = \Xmf\Module\Admin::getInstance();
        $indexAdmin->displayNavigation($currentFile);
        // buttons
        $adminMenu = new ModuleAdmin();
        $adminMenu->addItemButton(_AM_COMMON_PERM_GLOBAL, '?op=global_perms.list', 'permissions');
        echo $adminMenu->renderButton();
        //
        $xoTheme->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
        $GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
        //
        $groups_perm_permname = $groupperm_handler->getGroupIds('permname', true, $iscrittiHelper->getModule()->mid());
        //
//        xoops_load('FormSelectGroupCriteria', 'common');
        $formObj = new \XoopsThemeForm(_CO_COMMON_PERMS_EDIT, 'op', xoops_getenv('PHP_SELF'));
        //
        $selectGroupLines = min(count($member_handler->getGroupList()), _CONST_COMMON_SELECTGROUPMAXLINES);
        $groupSelectCriteria = new CriteriaCompo();
        $groupSelectCriteria->setSort('name');
        $groupSelectCriteria->setOrder('ASC');
        $formObj->addElement(new FormSelectGroupCriteria(_CO_COMMON_PERM_GROUPS_PERMNAME, 'groups_perm_permname', true, $groups_perm_permname, $selectGroupLines, true, $groupSelectCriteria));
        //
        $formObj->addElement(new \XoopsFormHidden('op', 'global_perms.save'), false);
            $button_tray = new \XoopsFormElementTray('' ,'');
            $submit_btn = new \XoopsFormButton('', 'post', _CO_COMMON_PERM_SAVE, 'submit');
            $submit_btn->setExtra('accesskey="i"');
            $button_tray->addElement($submit_btn);
        $formObj->addElement($button_tray);
        $GLOBALS['xoopsTpl']->assign('permissions_form', $formObj->render());
        //
        $GLOBALS['xoopsTpl']->display("db:{$iscrittiHelper->getModule()->dirname()}_am_global_perms_list.tpl");
        //
        include __DIR__ . '/admin_footer.php';
        break;
    case 'global_perms.save':
        // resetta/aggiorna permesso permname
        $groupperm_handler->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), 'permname');
        $groups_perm_permname = Request::getArray('groups_perm_permname');
        foreach ($groups_perm_permname as $group) {
            $groupperm_handler->addRight('permname', true, $group, $iscrittiHelper->getModule()->mid());
        }
        redirect_header('?op=global_perms.list', 3, _CO_COMMON_PERM_STORED);
        break;
}
