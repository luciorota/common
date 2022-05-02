<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    FormB3CheckBoxObject,
    ThemedForm
};

$currentFile = basename(__FILE__);
include __DIR__ . '/header.php';

$xoopsOption['template_main'] = "{$commonHelper->getModule()->dirname()}_test.form.tpl";
include XOOPS_ROOT_PATH . '/header.php';

//$xoTheme->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addStylesheet(COMMON_CSS_URL . '/module.css');
//$xoTheme->addStylesheet(COMMON_CSS_URL . '/' . $currentFile . '.css'); // ie: index.php.css
$xoTheme->addScript(COMMON_JS_URL . '/module.js');
//$xoTheme->addScript(COMMON_JS_URL . '/' . $currentFile . '.js'); // ie: index.php.js
// template: common\breadcrumb
//xoops_load('breadcrumb', 'common');
$breadcrumb = new Breadcrumb();
$breadcrumb->addLink($commonHelper->getModule()->getVar('name'), COMMON_URL);
$xoopsTpl->assign('commonBreadcrumb', $breadcrumb->render());

// template: isAdmin
$GLOBALS['xoopsTpl']->assign('$isAdmin', $isAdmin);

xoops_load('XoopsUserUtility');



//xoops_load('XoopsRequest');
$op = Request::getCmd('op', '');
switch ($op) {
    default:
        break;

    case 'save':
        //var_dump($_POST);
        break;
}



// template: form
xoops_load('XoopsFormLoader');
//xoops_load('ThemedForm', 'common');
$formObj = new ThemedForm('title', 'iscrittoForm', '', 'POST', true);
$formObj->setExtra('enctype="multipart/form-data"');



$formObj->insertBreak();



//xoops_load('FormB3CheckBoxObject', 'common');
$TestObjectHandler = new \XoopsModules\Common\TestobjectHandler();
$FormB3CheckBoxObject = new FormB3CheckBoxObject(
        'FormB3CheckBoxObject',
        'FormB3CheckBoxObject',
        $TestObjectHandler,
        ['id column', 'name column', 'weight column', 'created in date'],
        ['id', 'name', 'weight', 'created_date_data'],
        null, null, 10
);
$formObj->addElement($FormB3CheckBoxObject);




$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
