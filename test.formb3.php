<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    FormB3CheckBoxObject,
    FormB3Datepicker,
    FormB3Doubleselect,
    FormB3Elementrow,
    FormB3MultiSelect,
    FormB3SelectGroup,
    FormB3Tagsinput,
    FormB3Toggle,
    FormEmail,
    FormTelephonenumber,
    FormUrl,
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



$formObj->insertBreak();



//xoops_load('FormB3Datepicker', 'common');
$formObj->addElement(new FormB3Datepicker('FormB3Datepicker', 'FormB3Datepicker'));



$formObj->insertBreak();



//xoops_load('FormB3Doubleselect', 'common');
$valuesFrom = [0 => 'value 0', 1 => 'value 1', 5 => 'value 5'];
$valuesTo = [2 => 'value 2', 3 => 'value 3', 4 => 'value 4', 7 => 'value 7'];
$FormB3Doubleselect = new FormB3Doubleselect('FormB3Doubleselect', 'FormB3Doubleselect', $valuesFrom , $valuesTo , 5, 'from', 'to');
$formObj->addElement($FormB3Doubleselect);
        
//xoops_load('FormB3SelectGroup', 'common');
$FormB3SelectGroup = Request::getArray('FormB3SelectGroup', []);
$formObj->addElement(new FormB3SelectGroup('FormB3SelectGroup<br>multiple true', 'FormB3SelectGroup', true, $FormB3SelectGroup, 10, true));



$formObj->insertBreak();



//xoops_load('FormB3Elementrow', 'common');
//xoops_load('FormTelephonenumber', 'common');
//xoops_load('FormEmail', 'common');
//xoops_load('FormUrl', 'common');
$FormB3Elementrow = new FormB3Elementrow('FormB3Elementrow', 3);
$FormB3Elementrow->addElement(new FormTelephonenumber('FormTelephonenumber', 'FormTelephonenumber', 10, 10, ''));
$FormB3Elementrow->addElement(new FormEmail('FormEmail', 'FormEmail', ''));
$FormB3Elementrow->addElement(new FormUrl('FormUrl', 'FormUrl', ''));
$formObj->addElement($FormB3Elementrow);



//xoops_load('FormB3MultiSelect', 'common');
$FormB3MultiSelect = Request::getArray('FormB3MultiSelect', []);
$testFormB3MultiSelect = new FormB3MultiSelect('FormB3MultiSelect_3', 'FormB3MultiSelect_3', $FormB3MultiSelect, 3, true);
for ($i = 1; $i <= 10; $i++) {
    $testFormB3MultiSelect->addOption((string)($i), "name #{$i}", "description #{$i}");
}
$formObj->addElement($testFormB3MultiSelect);

$testFormB3MultiSelect = new FormB3MultiSelect('FormB3MultiSelect_0', 'FormB3MultiSelect_0', $FormB3MultiSelect, 0, true);
for ($i = 1; $i <= 10; $i++) {
    $testFormB3MultiSelect->addOption((string)($i), "value #{$i}", "description #{$i}");
}
$formObj->addElement($testFormB3MultiSelect);



$formObj->insertBreak();



//xoops_load('FormB3Tagsinput', 'common');
$FormB3Tagsinput = Request::getArray('FormB3Tagsinput', []);
$availableTags = ['vanessa', 'maria', 'gaia'];
$freeInput = true;
$limit = 0;
$maxChars = 255;
$allowDuplicates = false;
$tagClass = 'label label-success';
$formObj->addElement(new FormB3Tagsinput('FormB3Tagsinput', 'FormB3Tagsinput', $FormB3Tagsinput, $availableTags, $freeInput, $limit, $maxChars, $allowDuplicates, $tagClass));



$formObj->insertBreak();



//xoops_load('FormB3Toggle', 'common');
$FormB3Toggle = Request::getBool('FormB3Toggle', true);
$on = _YES;
$off = _NO;
$size = 'normal';
$onstyle = 'primary';
$offstyle = 'default';
$formObj->addElement(new FormB3Toggle('FormB3Toggle', 'FormB3Toggle', $FormB3Toggle, $on, $off, $size, $onstyle, $offstyle));



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
