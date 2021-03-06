<?php

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
xoops_load('breadcrumb', 'common');
$breadcrumb = new common\breadcrumb();
$breadcrumb->addLink($commonHelper->getModule()->getVar('name'), COMMON_URL);
$xoopsTpl->assign('commonBreadcrumb', $breadcrumb->render());

// template: isAdmin
$GLOBALS['xoopsTpl']->assign('$isAdmin', $isAdmin);

xoops_load('XoopsUserUtility');



xoops_load('XoopsRequest');
$op = XoopsRequest::getCmd('op', '');
switch ($op) {
    default:
        break;

    case 'save':
        //var_dump($_POST);
        break;
}



// template: form
xoops_load('XoopsFormLoader');
xoops_load('ThemedForm', 'common');
$formObj = new common\ThemedForm('title', 'iscrittoForm', '', 'POST', true);
$formObj->setExtra('enctype="multipart/form-data"');

//xoops_load('FormGoogleMap', 'common');
//$formObj->addElement(new common\FormGoogleMap('FormGoogleMap', 'FormGoogleMap', array(), $commonHelper->getConfig('GoogleMapsAPIKey')));



$formObj->insertBreak();



xoops_load('FormXoopsImage', 'common');
$FormXoopsImage = new common\FormXoopsImage('FormXoopsImage', 'FormXoopsImage', 255, 255, '', null);
$FormXoopsImage->setDescription("new common\FormXoopsImage('FormXoopsImage', 'FormXoopsImage', 255, 255, '', null)");
$formObj->addElement($FormXoopsImage);

xoops_load('FormCodiceFiscale', 'common');
$FormCodiceFiscale = new common\FormCodiceFiscale('FormCodiceFiscale', 'FormCodiceFiscale');
$FormCodiceFiscale->setDescription("new common\FormCodiceFiscale('FormCodiceFiscale', 'FormCodiceFiscale')");
$formObj->addElement($FormCodiceFiscale);

xoops_load('FormCap', 'common');
$FormCap = new common\FormCap('FormCap', 'FormCap');
$FormCap->setDescription("new common\FormCap('FormCap', 'FormCap')");
$formObj->addElement($FormCap);

xoops_load('FormTelephonenumber', 'common');
$FormTelephonenumber = new common\FormTelephonenumber('FormTelephonenumber', 'FormTelephonenumber', 10, 10, '');
$FormTelephonenumber->setDescription("new common\FormTelephonenumber('FormTelephonenumber', 'FormTelephonenumber', 10, 10, '')");
$formObj->addElement($FormTelephonenumber);

xoops_load('FormEmail', 'common');
$FormEmail = new common\FormEmail('FormEmail', 'FormEmail');
$FormEmail->setDescription("new common\FormEmail('FormEmail', 'FormEmail')");
$formObj->addElement($FormEmail);

xoops_load('FormNumber', 'common');
$FormNumber = new common\FormNumber('FormNumber', 'FormNumber', 0, 255, 0, 1);
$FormNumber->setDescription("new common\FormNumber('FormNumber', 'FormNumber', 0, 255, 0, 1)");
$formObj->addElement($FormNumber);



xoops_load('FormDatepicker', 'common');
$FormDatepicker = new common\FormDatepicker('FormDatepicker', 'FormDatepicker');
$FormDatepicker->setDescription('Click to open a datepicker pop-up');
$formObj->addElement($FormDatepicker);



$formObj->insertBreak();



$formObj->addElement(new \XoopsFormSelectGroup('XoopsFormSelectGroup', 'XoopsFormSelectGroup', false, null, 5, true));
xoops_load('FormSelectGroup', 'common');
$FormSelectGroup = new common\FormSelectGroup('FormSelectGroup<br>multiple true', 'FormSelectGroup-multiple', false, null, 10, true);
$FormSelectGroup->setDescription("new common\FormSelectGroup('FormSelectGroup<br>multiple true', 'FormSelectGroup-multiple', false, null, 10, true)");
$formObj->addElement($FormSelectGroup);
$formObj->addElement(new common\FormSelectGroup('FormSelectGroup<br>multiple false', 'FormSelectGroup', false, null, 10, false));



$formObj->insertBreak();



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
