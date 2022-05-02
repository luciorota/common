<?php

use XoopsModules\Common;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;
use Xmf\Module\Helper\Permission;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

class_exists('\Xmf\Module\Helper') or die('XMF is required');

// load Common module helper
$commonHelper = \Xmf\Module\Helper::getHelper('common');

// init session helper
$commonSessionHelper = new \Xmf\Module\Helper\Session();
$commonSessionHelper->init();

// common Xoops stuff
xoops_load('XoopsFormLoader');
xoops_load('XoopsPageNav');
xoops_load('XoopsUserUtility');
xoops_load('XoopsLocal');
//xoops_load('XoopsRequest');
xoops_load('XoopsLists');

// MyTextSanitizer object
global $myts;
$myts = MyTextSanitizer::getInstance();

// load Xoops handlers
$module_handler = xoops_getHandler('module');
$member_handler = xoops_getHandler('member');
$notification_handler = xoops_getHandler('notification');
$gperm_handler = xoops_getHandler('groupperm');
$group_handler = xoops_getHandler('group');

// common common stuff
define('COMMON_DIRNAME', basename(dirname(__DIR__)));
define('COMMON_URL', XOOPS_URL . '/modules/' . COMMON_DIRNAME);
define('COMMON_IMAGES_URL', COMMON_URL . '/assets/images');
define('COMMON_JS_URL', COMMON_URL . '/assets/js');
define('COMMON_CSS_URL', COMMON_URL . '/assets/css');
define('COMMON_ADMIN_URL', COMMON_URL . '/admin');
define('COMMON_ROOT_PATH', dirname(__DIR__));
define('COMMON_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . COMMON_DIRNAME); // WITHOUT Trailing slash
define('COMMON_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . COMMON_DIRNAME); // WITHOUT Trailing slash

$commonHelper->loadLanguage('common');

require_once COMMON_ROOT_PATH . '/vendor/autoload.php';

include_once COMMON_ROOT_PATH . '/include/functions.php';
include_once COMMON_ROOT_PATH . '/include/constants.php';

// this is needed or it will not work in blocks
global $xoopsUser, $isAdmin;
$isAdmin = false;
if (is_object($xoopsUser)) {
    $isAdmin = $commonHelper->isUserAdmin();
}
