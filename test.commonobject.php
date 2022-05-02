<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb
};

$currentBasename = basename(__FILE__);
$currentFilename = basename(__FILE__, '.php');
include __DIR__ . '/header.php';

$TestObjectHandler = new \XoopsModules\Common\TestobjectHandler();

$xoopsOption['template_main'] = "{$commonHelper->getModule()->dirname()}_test.commonobject.tpl";
include XOOPS_ROOT_PATH . '/header.php';

// template: common\breadcrumb
//xoops_load('breadcrumb', 'common');
$breadcrumb = new Breadcrumb();
$breadcrumb->addLink($commonHelper->getModule()->getVar('name'), COMMON_URL);
$GLOBALS['xoopsTpl']->assign('commonBreadcrumb', $breadcrumb->render());

//xoops_load('XoopsRequest');
$op = Request::getCmd('op', '');
switch ($op) {
    default:
    case 'edit':
        $id = Request::getInt('id', 0);
        $testObj = $TestObjectHandler->get($id);
        $GLOBALS['xoopsTpl']->assign('form', ($testObj->getForm())->render());
        break;

    case 'save':
        $id = Request::getInt('id', 0);
        $testObj = $TestObjectHandler->get($id);
        $testObj->setValues([], 'POST');
        $TestObjectHandler->insert($testObj);
        redirect_header((string)($currentBasename), 3, 'saved');
        break;

    case 'delete':
        $id = Request::getInt('id', 0);
        $testObj = $TestObjectHandler->get($id);
        if (Request::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header((string)($currentBasename), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $TestObjectHandler->delete($testObj);
            redirect_header((string)($currentBasename), 3, 'deleted');
        } else {
            ob_start();
            xoops_confirm(
                ['ok' => true, 'op' => $op, 'id' => $id], 
                Request::getText('REQUEST_URI', '', 'SERVER'),
                _DELETE . '?', 
                _DELETE
            );
            $form = ob_get_contents();
            ob_end_clean();
            $GLOBALS['xoopsTpl']->assign('form', $form);
        }
        break;
}

$testobjectCriteria = new \Criteriacompo();
$tests = $TestObjectHandler->getValues($testobjectCriteria);
$GLOBALS['xoopsTpl']->assign('tests', $tests);

include __DIR__ . '/footer.php';
