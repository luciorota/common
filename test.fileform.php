<?php

use Xmf\Request;
use XoopsModules\Common\{
    Breadcrumb,
    MediaUploader,
    ThemedForm,
    FormB3Fileinput

};

$currentFile = basename(__FILE__);
include __DIR__ . '/header.php';

$xoopsOption['template_main'] = "{$commonHelper->getModule()->dirname()}_test.form.tpl";
include XOOPS_ROOT_PATH . '/header.php';

$xoTheme->addStylesheet(COMMON_CSS_URL . '/module.css');
$xoTheme->addScript(COMMON_JS_URL . '/module.js');
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



//\xoops_load('MediaUploader', 'common');
\xoops_load('XoopsMediaUploader');



// upload new files, if exist
$uploadeds = [];
if ((isset($_FILES['FormB3Fileinput'])) && ($_FILES['FormB3Fileinput']['error'] != UPLOAD_ERR_NO_FILE)) {
    $allowedMimeTypes = ['application/pdf', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png'];
    $phpiniMaxFileSize = (min((int) (ini_get('upload_max_filesize')), (int) (ini_get('post_max_size')), (int) (ini_get('memory_limit')))) * 1024 * 1024; // bytes
    $maxFileSize = $phpiniMaxFileSize;
    $maxFileWidth = null;
    $maxFileHeight = null;
    $randomFilename = true; // per evitare di sovrascrivere accidentalmente file
    $fileUploader = new MediaUploader(
            COMMON_UPLOAD_PATH . '/',
            $allowedMimeTypes,
            $maxFileSize,
            $maxFileWidth,
            $maxFileHeight,
            $randomFilename
    );
    $normalizedFILES = $fileUploader->normalizedFILES();
    foreach(array_keys($normalizedFILES['FormB3Fileinput']) as $key) {
        if ($fileUploader->fetchMedia('FormB3Fileinput', $key)) {
            if (!$fileUploader->upload()) {
                echo $fileUploader->getErrors();
            } else {
                $media = [
                    'mediaName' => $fileUploader->getMediaName(),
                    'mediaType' => $fileUploader->getMediaType(),
                    'mediaSize' => $fileUploader->getMediaSize(),
                    'savedFileName' => $fileUploader->getSavedFileName(),
                    'savedDestination' => $fileUploader->getSavedDestination(),
                ];
                $uploadeds[] = $media;

            }
        } else {
            echo $fileUploader->getErrors();
        }
    }
} else {
    // NOP
}



// get stored files
$fileNames = array_diff(scandir(COMMON_UPLOAD_PATH), ['..', '.', 'index.html']);
$files = [];
foreach ($fileNames as $fileName) {
    $file = [
        'fileName' => $fileName,
        'description' => 'description',
        'url' => COMMON_UPLOAD_URL . '/' . $fileName,
        'path' =>  COMMON_UPLOAD_PATH . '/' . $fileName,
    ];
    $files[] = $file;
}



// template: form
xoops_load('XoopsFormLoader');
//xoops_load('ThemedForm', 'common');
$formObj = new ThemedForm('title', 'iscrittoForm', '', 'POST', true);
$formObj->setExtra('enctype="multipart/form-data"');



// template: $FormB3Fileinput
//\xoops_load('FormB3Fileinput', 'common');
$multiple = true;
$showThumbs = true;
$allowedFileExtensions = [];
$maxFileSize = 0;
$FormB3Fileinput = new FormB3Fileinput(
    'FormB3Fileinput',
    'FormB3Fileinput',
    $files,
    $multiple,
    $showThumbs,
    $allowedFileExtensions,
    $maxFileSize
);
$formObj->addElement($FormB3Fileinput);



$formObj->addElement(new \XoopsFormHidden('op', 'save'));
$button_submit = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
$button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
$formObj->addElement($button_submit);



$xoopsTpl->assign('form', $formObj->render());



include __DIR__ . '/footer.php';
