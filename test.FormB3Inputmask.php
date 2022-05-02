<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    FormB3Inputmask,
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



//xoops_load('FormB3Inputmask', 'common');
$inputmask = '(999) 999-9999';
$FormB3Inputmask = new FormB3Inputmask('FormB3Inputmask1', 'FormB3Inputmask1', '', $inputmask, null, 'tel.', '', 'telephone number');
$FormB3Inputmask->setDescription("<a href='https://github.com/RobinHerbots/Inputmask'>https://github.com/RobinHerbots/Inputmask</a><br>inputmask = '{$inputmask}'");
$formObj->addElement($FormB3Inputmask);

$options = [
    'alias' => 'datetime',
    'inputFormat' => 'dd/mm/yyyy'
];
$FormB3Inputmask = new FormB3Inputmask('FormB3Inputmask2', 'FormB3Inputmask2', '', null, $options);
$FormB3Inputmask->setAttributes(['placeholder' => 'input date in dd/mm/yyyy format']);
$FormB3Inputmask->setDescription('Date');
$formObj->addElement($FormB3Inputmask);

$options = [
    'alias' => 'email',
];
$FormB3Inputmask = new FormB3Inputmask('FormB3Inputmask3', 'FormB3Inputmask3', '', null, $options, '@', '', 'email');
$FormB3Inputmask->setDescription('Email');
$formObj->addElement($FormB3Inputmask);

$options = [
    'mask' => '999.999.999.999',
    'placeholder' => '___.___.___.___',
];
$FormB3Inputmask = new FormB3Inputmask('FormB3Inputmask4', 'FormB3Inputmask4', '', null, $options, 'pre IP', ' post /', 'IP address');
$FormB3Inputmask->setDescription("IP Address<br>new FormB3Inputmask('FormB3Inputmask4', 'FormB3Inputmask4', '', null, \$options, 'pre IP', 'post /', 'IP address')");
$formObj->addElement($FormB3Inputmask);

$options = [
    'alias' => 'mac',
];
$FormB3Inputmask = new FormB3Inputmask('FormB3Inputmask5', 'FormB3Inputmask5', '', null, $options);
$FormB3Inputmask->setDescription('Mac address');
$formObj->addElement($FormB3Inputmask);



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
