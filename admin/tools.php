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
 * @author          luciorota, studiopas
 * @version         svn:$Id$
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';

$op = Request::getCmd('op', 'tools');
switch ($op) {
    default:
    case 'tools':
        //  admin navigation
        xoops_cp_header();
        $indexAdmin = \Xmf\Module\Admin::getInstance();
        $indexAdmin->displayNavigation($currentFile);
        // buttons
        $adminMenu = new ModuleAdmin();
        $adminMenu->addItemButton(_AM_COMMON_CHECK, "{$currentFile}?op=checks.list", 'alert');
        echo $adminMenu->renderButton();
        //
        $GLOBALS['xoopsTpl']->display("db:{$commonHelper->getModule()->dirname()}_am_tools.tpl");
        //
        include __DIR__ . '/admin_footer.php';
        //
        break;
}
