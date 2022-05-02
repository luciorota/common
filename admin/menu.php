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

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

// get path to icons
$pathIcon32 = '';
if (class_exists('Xmf\Module\Admin', true)) {
    $pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
}

$adminmenu = array();
// Index
$adminmenu[] = array(
    'title' => _MI_COMMON_ADMENU_INDEX ,
    'link'  => 'admin/index.php' ,
    'icon'  => 'assets/images/icons/32/' . 'house.png'
);
// About
$adminmenu[] = array(
    'title' => _MI_COMMON_ADMENU_ABOUT ,
    'link'  => 'admin/about.php' ,
    'icon'  => $pathIcon32 . 'about.png'
);
/*
// Permissions
$adminmenu[] = array(
    'title' => _MI_COMMON_ADMENU_PERMISSIONS ,
    'link'  => 'admin/permissions.php' ,
    'icon'  => $pathIcon32 . 'permissions.png'
);
// Tools
$adminmenu[] = array(
    'title' => _MI_COMMON_ADMENU_TOOLS ,
    'link'  => 'admin/tools.php' ,
    'icon'  => 'assets/images/icons/32/' . 'toolbox.png'
);
*/
