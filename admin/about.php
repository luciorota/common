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

use Xmf\Module\Admin;

require dirname(__FILE__) . '/admin_header.php';

$moduleAdmin = Admin::getInstance();
$moduleAdmin->addItemButton('title', 'link.php');
$moduleAdmin->renderButton();
$moduleAdmin->displayNavigation('about.php');
Admin::setPaypal('6KJ7RW5DR3VTJ');
$moduleAdmin->displayAbout(false);

require dirname(__FILE__) . '/admin_footer.php';
