<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    FormInputmask,
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



//xoops_load('FormInputmask', 'common');
$inputmask = '(999) 999-9999';
$FormInputmask = new FormInputmask('FormInputmask1', 'FormInputmask1', '', $inputmask);
$FormInputmask->setDescription("<a href='https://github.com/RobinHerbots/Inputmask'>https://github.com/RobinHerbots/Inputmask</a><br>inputmask = '{$inputmask}'");
$formObj->addElement($FormInputmask);

$options = [
    'alias' => 'datetime',
    'inputFormat' => 'dd/mm/yyyy'
];
$FormInputmask = new FormInputmask('FormInputmask2', 'FormInputmask2', '', null, $options);
$FormInputmask->setDescription('Date');
$formObj->addElement($FormInputmask);

$options = [
    'alias' => 'email',
];
$FormInputmask = new FormInputmask('FormInputmask3', 'FormInputmask3', '', null, $options);
$FormInputmask->setDescription('Email');
$formObj->addElement($FormInputmask);

$options = [
    'mask' => '999.999.999.999',
    'placeholder' => '___.___.___.___',
];
$FormInputmask = new FormInputmask('FormInputmask4', 'FormInputmask4', '', null, $options);
$FormInputmask->setDescription('IP Address');
$formObj->addElement($FormInputmask);

$options = [
    'alias' => 'mac',
];
$FormInputmask = new FormInputmask('FormInputmask5', 'FormInputmask5', '', null, $options);
$FormInputmask->setDescription('Mac address');
$formObj->addElement($FormInputmask);



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
