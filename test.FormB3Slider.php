<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    FormB3Slider,
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
$formObj = new ThemedForm('', 'iscrittoForm', '', 'POST', true);
$formObj->setExtra('enctype="multipart/form-data"');



//xoops_load('FormB3Slider', 'common');
$values = 200;
$options = [
    'ticks' => [0, 100, 200, 300, 400],
    'ticks_labels' => ['V0', 'V100', 'V200', 'V300', 'V400'],
    'ticks_snap_bounds' => 30,
];
$FormB3Slider = new FormB3Slider('FormB3Slider1', 'FormB3Slider1', $values, $options);
$FormB3Slider->setDescription("<a hef='https://github.com/seiyria/bootstrap-slider'>https://github.com/seiyria/bootstrap-slider</a><br>new FormB3Slider('FormB3Slider1', 'FormB3Slider1', \$values, \$options);");
$formObj->addElement($FormB3Slider);



$values = 200;
$options = [
    'min' => 0, 'max' => 400, 'step' => 10,
];
$FormB3Slider = new FormB3Slider('FormB3Slider2', 'FormB3Slider2', $values, $options);
$FormB3Slider->setDescription("new FormB3Slider('FormB3Slider2', 'FormB3Slider2', \$values, \$options);");
$formObj->addElement($FormB3Slider);



$values = [100, 300];
$options = [
    'min' => 0, 'max' => 400, 'step' => 50,
];
$FormB3Slider = new FormB3Slider('FormB3Slider3', 'FormB3Slider3', $values, $options);
$FormB3Slider->setDescription("new FormB3Slider('FormB3Slider3', 'FormB3Slider3', \$values, \$options);");
$formObj->addElement($FormB3Slider);



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);

$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
