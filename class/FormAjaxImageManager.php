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
 * @author          luciorota
 * @version         svn:$Id$
 */
namespace XoopsModules\Common;
use Xmf\Request;

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}
include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

// Disable xoops debugger in dialog window
include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
$xoopsLogger = \XoopsLogger::getInstance();
$xoopsLogger->activated = false;
// Disable error reporting
error_reporting(0);

define('FORMAJAXIMAGEMANAGER_FILENAME', basename($currentPath));
define('FORMAJAXIMAGEMANAGER_PATH', dirname($currentPath));
define('FORMAJAXIMAGEMANAGER_REL_URL', str_replace(XOOPS_ROOT_PATH . '/', '', dirname($currentPath)));
define('FORMAJAXIMAGEMANAGER_URL', XOOPS_URL . '/' . FORMAJAXIMAGEMANAGER_REL_URL . '/' . FORMAJAXIMAGEMANAGER_FILENAME);
define('FORMAJAXIMAGEMANAGER_JS_REL_URL', FORMAJAXIMAGEMANAGER_REL_URL . '/formajaximagemanager/js');
define('FORMAJAXIMAGEMANAGER_CSS_REL_URL', FORMAJAXIMAGEMANAGER_REL_URL . '/formajaximagemanager/css');
define('FORMAJAXIMAGEMANAGER_IMAGES_REL_URL', FORMAJAXIMAGEMANAGER_REL_URL . '/formajaximagemanager/images');

$dirname = basename(dirname(__DIR__));

xoops_loadLanguage('formajaximagemanager', $dirname);

// Ajax check
if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $_GET['ajax'] == 1) {
	/**
	 *  ajax request
	 *  render JSON output
	 */

    $GLOBALS['xoopsLogger']->activated = false;

    $module_handler = xoops_getHandler('module');
    $systemModule = $module_handler->getByDirname('system');
    $systemModuleId = $systemModule->id;
    $userGroups = (is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->getGroups() : [XOOPS_GROUP_ANONYMOUS];
    $userIsAdmin = (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin($systemModuleId));

    $imgcat_handler = xoops_getHandler('imagecategory');
    $image_handler = xoops_getHandler('image');
    $imgcatObjperm_handler = xoops_getHandler('groupperm');

//    xoops_load('XoopsRequest');

    $op = Request::getString('op', '', 'POST');
    switch ($op) {
        default :
            // NOP
            break;

        case 'getImgcats' :
            $name = Request::getString('name');
            $imgcat_id = Request::getInt('imgcat_id', 0);

            $criteria = new \CriteriaCompo();
            if ($userIsAdmin) {
                $imgcatReadListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_read'));
                $imgcatWriteListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_write'));
            } else {
                $imgcatReadListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_read', true));
                $imgcatWriteListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_write', true));
                $criteria->add(new \Criteria('imgcat_display', true));
            }
            if ($imgcat_handler->getCount($criteria) == 0) {
                // ERROR: no imgcats
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => false,
                    'message' => _FAIM_ERROR_NO_IMGCAT,
                    'html' => _FAIM_ERROR_NO_IMGCATS,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            }
            $criteria->setSort('imgcat_weight');
            $criteria->setOrder('ASC');
            $imgcatObjs = $imgcat_handler->getObjects($criteria, true);

            $imgcats = [];
            foreach ($imgcatObjs as $imgcat_id => $imgcatObj) {
                $imgcat = [];
                $imgcat['data'] = $imgcatObj->toArray();
                if ($userIsAdmin) {
                    $imgcat['perm']['edit'] = true;
                    $imgcat['perm']['delete'] = true;
                    $imgcat['perm']['img_read'] = true;
                    $imgcat['perm']['img_write'] = true;
                    $imgcat['perm']['img_delete'] = true;
                } else {
                    $imgcat['perm']['edit'] = false;
                    $imgcat['perm']['delete'] = false;
                    $imgcat['perm']['img_read'] = in_array($imgcat_id, $imgcatReadListArray);
                    $imgcat['perm']['img_write'] = in_array($imgcat_id, $imgcatWriteListArray);
                    $imgcat['perm']['img_delete'] = false; // IN PROGRESS
                }
                $imgcats[$imgcat_id] = $imgcat;
                unset($imgcat);
            }

            $data['imgcats'] = $imgcats;
            $data['xoopsUser']['isAdmin'] = (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin($systemModuleId));
            // Generate ajax return
            $ret = [
                'data' => $data,
                'error' => false,
                'message' => '',
                'html' => '',
                'javascript' => '// NOP'
            ];
            echo json_encode($ret);
            exit();

        case 'getImgcatPermissions' :
            $name = Request::getString('name');
            $imgcat_id = Request::getInt('imgcat_id', 0);

            // get imgcat/imgcats
            $criteria = new \CriteriaCompo();
            if ($imgcat_id != 0) {
                $criteria->add(new \Criteria('imgcat_id', $imgcat_id));
            }
            if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin($systemModuleId)) {
                $imgcatReadListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_read'));
                $imgcatWriteListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_write'));
            } else {
                $imgcatReadListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_read', true));
                $imgcatWriteListArray = array_keys($imgcat_handler->getList($userGroups, 'imgcat_write', true));
                $criteria->add(new \Criteria('imgcat_display', true));
            }
            if ($imgcat_handler->getCount($criteria) == 0) {
                // ERROR: no imgcats
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => false,
                    'message' => _FAIM_ERROR_NO_IMGCATS,
                    'html' => _FAIM_ERROR_NO_IMGCATS,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            }
            $criteria->setSort('imgcat_weight');
            $criteria->setOrder('ASC');
            $imgcatsObj = $imgcat_handler->getObjects($criteria, true);

            $imgcatsArray = [];
            foreach ($imgcatsObj as $imgcat_id => $imgcatObj) {
                $imgcat = [];
                $imgcat['data'] = $imgcatObj->toArray();
                if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin($systemModuleId)) {
                    $imgcat['perm']['edit'] = true;
                    $imgcat['perm']['delete'] = true;
                    $imgcat['perm']['img_read'] = true;
                    $imgcat['perm']['img_write'] = true;
                    $imgcat['perm']['img_delete'] = true;
                } else {
                    $imgcat['perm']['edit'] = false;
                    $imgcat['perm']['delete'] = false;
                    $imgcat['perm']['img_read'] = in_array($imgcat_id, $imgcatReadListArray);
                    $imgcat['perm']['img_write'] = in_array($imgcat_id, $imgcatWriteListArray);
                    $imgcat['perm']['img_delete'] = false; // IN PROGRESS
                }
                $imgcatsArray[$imgcat_id] = $imgcat;
                unset($imgcat);
            }
            $data['imgcats'] = $imgcatsArray;
            $data['xoopsUser']['isAdmin'] = (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin($systemModuleId));
            // generate ajax return
            $ret = [
                'data' => $data,
                'error' => false,
                'message' => '',
                'html' => '',
                'javascript' => '// NOP'
            ];
            echo json_encode($ret);
            exit();

        case 'getImgcatImagesList' :
            $imgcat_id = Request::getInt('imgcat_id', 0);

            // get imgcat object
            $imgcatObj = $imgcat_handler->get($imgcat_id);
            // check imgcat object
            if (!is_object($imgcatObj)) {
                // ERROR
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => true,
                    'message' => _FAIM_ERROR_NO_IMGCAT,
                    'html' => _FAIM_ERROR_NO_IMGCAT,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            }
            // check user read permissions
            $imgcatperm_handler = xoops_getHandler('groupperm');
            if (!$imgcatperm_handler->checkRight('imgcat_read', $imgcat_id, $userGroups)) {
                // ERROR: no read permissions
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => true,
                    'message' => _FAIM_ERROR_NO_IMGCAT_VIEW,
                    'html' => _FAIM_ERROR_NO_IMGCAT_VIEW,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            }
            // get images objects
            $criteria = new \Criteria('imgcat_id', $imgcat_id);
            $criteria->setSort('image_weight');
            $criteria->setOrder('ASC');
            $imagesObjs = $image_handler->getObjects($criteria, false, false);
            foreach ($imagesObjs as $imageObj) {
                $image['image_id'] = $imageObj->getVar('image_id');
                $image['image_name'] = $imageObj->getVar('image_name');
                $image['image_url'] = XOOPS_URL . '/uploads/' . $imageObj->getVar('image_name');
                $image['image_path'] = XOOPS_UPLOAD_PATH . '/' . $imageObj->getVar('image_name');
                $imagesize = getimagesize($image['image_path']);
                $image['image_width'] = $imagesize[0];
                $image['image_height'] = $imagesize[1];
                $image['image_mime'] = $imagesize['mime'];
                $image['image_nicename'] = $imageObj->getVar('image_nicename');
                $image['image_mimetype'] = $imageObj->getVar('image_mimetype');
                $image['image_created'] = $imageObj->getVar('image_created');
                $image['image_display'] = $imageObj->getVar('image_display');
                $image['image_weight'] = $imageObj->getVar('image_weight');
                $image['image_body'] = $imageObj->getVar('image_body');
                $image['imgcat_id'] = $imageObj->getVar('imgcat_id');
                $imagesArray[] = $image;
                unset($image);
            }
            // get read/write permissions
            $perm['img_read'] = true;
            $perm['img_write'] = $imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $userGroups);
            // generate ajax return
            $ret = [];
            $ret['data']['images'] = $imagesArray;
            $ret['data']['perm'] = $perm;
            $ret['error'] = false;
            $ret['message'] = '';
            $ret['html'] = 'ok';
            $ret['javascript'] = '// NOP';
            echo json_encode($ret);
            break;


        // Images operations
        case 'getImageForm' :
            $name = Request::getString('name', '');
            $image_id = Request::getInt('image_id', 0);
            $imgcat_id = Request::getInt('imgcat_id', 0, 'POST');

            // get image object
            if ($image_id <= 0) {
                $imageObj = $image_handler->create();
                $title = _FAIM_ADD_IMAGE;
            } else {
                $imageObj = $image_handler->get($image_id);
                $imgcat_id = $imageObj->getVar('imgcat_id');
                $title = _FAIM_EDIT_IMAGE;
            }
            // check image object
            if (!is_object($imageObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMAGE_OBJECT;
                $ret['html'] = _FAIM_ERROR_NO_IMAGE_OBJECT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // get imgcat object
            $imgcatObj = $imgcat_handler->get($imgcat_id);
            // create image form
            xoops_load('XoopsFormLoader');
            $formObj = new \XoopsSimpleForm($title, 'image_form', 'formajaximagemanager.php', 'post', true);
            $formObj->setExtra('enctype="multipart/form-data"');
            $formObj->addElement(new \XoopsFormText(_IMAGENAME, $name . '_image_nicename', 50, 255, $imageObj->getVar('image_nicename')), true);
            $select = new \XoopsFormSelect(_IMAGECAT, $name . '_image_imgcat_id', $imgcat_id);
            if ($userIsAdmin) {
                $categoriesListArray = $imgcat_handler->getList($userGroups, 'imgcat_write');
            } else {
                $categoriesListArray = $imgcat_handler->getList($userGroups, 'imgcat_write', true);
            }
            $select->addOptionArray($categoriesListArray);
            $formObj->addElement($select, true);
            if (!$imageObj->isNew()) {
                $formObj->addElement(new \XoopsFormLabel(_IMAGEFILE, "<img src='" . XOOPS_URL . '/image.php?id=' . $image_id . "&width=140&height=140' />"));
            } else {
                $formObj->addElement(new \XoopsFormFile(_IMAGEFILE, $name . '_image_file', $imgcatObj->getVar('imgcat_maxsize')), true);
            }
            $formObj->addElement(new \XoopsFormText(_IMGWEIGHT, $name . '_image_weight', 3, 4, $imageObj->getVar('image_weight')));
            $formObj->addElement(new \XoopsFormRadioYN(_IMGDISPLAY, $name . '_image_display', $imageObj->getVar('image_display'), _YES, _NO));
            if (!$imageObj->isNew()) {
                $formObj->addElement(new \XoopsFormHidden($name . '_op', 'submitUpdateImageForm'));
            } else {
                $formObj->addElement(new \XoopsFormHidden($name . '_op', 'submitNewImageForm'));
            }
            $formObj->addElement(new \XoopsFormHidden($name . '_image_id', $image_id));
            $formObj->addElement(new \XoopsFormHidden($name . '_imgcat_id', $imgcat_id));
            $formObj->addElement(new \XoopsFormButton('', $name . '_submit_image_button', _SUBMIT, 'button'));
            // render edit image form
            $html = '';
            $html .= "<div id='" . $name . "_edit_image_form'>";
            $html .= '<b>' . $formObj->getTitle() . "</b>\n";
            $html .= "<br />\n";
            foreach ($formObj->getElements() as $ele) {
                if (!$ele->isHidden()) {
                    $caption = $ele->getCaption();
                    if (!empty($caption)) {
                        $html .= '<strong>' . $ele->getCaption() . "</strong><br />\n";
                    }
                    $html .= $ele->render() . "<br />\n";
                } else {
                    $html .= $ele->render() . "\n";
                }
            }
            $html .= '</div>';
            // generate ajax return
            $ret = [];
            $ret['data'] = null;
            $ret['error'] = false;
            $ret['message'] = '';
            $ret['html'] = $html;
            $ret['javascript'] = '// NOP';
            echo json_encode($ret);
            break;

        case 'submitNewImageForm' :
            $name = Request::getString('name', '', 'POST');
            $filename = basename($_FILES[$name . '_image_file']['name']);
            //$filetype = basename($_FILES[$name . '_image_file']['type']);
            //$filesize = baseanme($_FILES[$name . '_image_file']['size']);
            $image_nicename = Request::getString('image_nicename', '', 'POST');
            $image_weight = Request::getInt('image_weight', 0, 'POST');
            $image_display = Request::getBool('image_display', true, 'POST');

            // get imgcat_id
            $imgcat_id = Request::getInt('imgcat_id', 0, 'REQUEST'); //isset($_REQUEST['imgcat_id']) ? (int)$_REQUEST['imgcat_id'] : 0;
            // get imgcat object
            $imgcatObj = $imgcat_handler->get($imgcat_id);
            // check imgcat object
            if (!is_object($imgcatObj)) {
                // ERROR
                // Generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // check user write permissions
            $imgcatperm_handler = xoops_getHandler('groupperm');
            if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $userGroups)) {
                // ERROR: non write permissions
                // Generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_SUBMIT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_SUBMIT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // upload image file and update db
            $error = false;
            xoops_load('XoopsMediaUploader');
            $uploader = new \XoopsMediaUploader(XOOPS_UPLOAD_PATH, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'), $imgcatObj->getVar('imgcat_maxsize'), $imgcatObj->getVar('imgcat_maxwidth'), $imgcatObj->getVar('imgcat_maxheight'));
            $uploader->setPrefix('img');
            if ($uploader->fetchMedia($name . '_image_file')) {
                if (!$uploader->upload()) {
                    $error = implode('<br />', $uploader->getErrors(false));
                } else {
                    // create image object
                    $imageObj = $image_handler->create();
                    $imageObj->setVar('image_name', $uploader->getSavedFileName());
                    $image_nicename = !empty($image_nicename) ? trim($image_nicename) : $filename;
                    $imageObj->setVar('image_nicename', $image_nicename);
                    $imageObj->setVar('image_mimetype', $uploader->getMediaType());
                    $imageObj->setVar('image_created', time());
                    $imageObj->setVar('image_display', $image_display);
                    $imageObj->setVar('image_weight', $image_weight);
                    $imageObj->setVar('imgcat_id', $imgcat_id);
                    if ($imgcatObj->getVar('imgcat_storetype') == 'db') {
                        $fp = @fopen($uploader->getSavedDestination(), 'rb');
                        $fbinary = @fread($fp, filesize($uploader->getSavedDestination()));
                        @fclose($fp);
                        $imageObj->setVar('image_body', $fbinary, true);
                        @unlink($uploader->getSavedDestination());
                    }
                    // store image object in db
                    if (!$image_handler->insert($imageObj)) {
                        $error = sprintf(_FAILSAVEIMG, $imageObj->getVar('image_nicename'));
                    }
                }
            } else {
                $error = sprintf(_FAILFETCHIMG, 0) . '|' . implode('|', $uploader->getErrors(false));
            }
            // generate ajax return
            if ($error) {
                // ERROR
                // Generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = $error;
                $ret['html'] = $error;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            } else {
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = false;
                $ret['message'] = '';
                $ret['html'] = '';
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            break;

        case 'submitUpdateImageForm' :
            $name = Request::getString('name', '');
            $image_id = Request::getInt('image_id', 0);
            $imgcat_id = Request::getInt('imgcat_id');
            $image_nicename = Request::getString('image_nicename', '', 'POST');
            $image_weight = Request::getInt('image_weight', 0, 'POST');
            $image_display = Request::getBool('image_display', true, 'POST');

            // get imgcat object
            $imgcatObj = $imgcat_handler->get($imgcat_id);
            // check imgcat object
            if (!is_object($imgcatObj)) {
                // ERROR
                // Generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // check user write permissions
            $imgcatperm_handler = xoops_getHandler('groupperm');
            if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $userGroups)) {
                // ERROR: no write permissions
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_SUBMIT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_SUBMIT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            // check image_id
            if ($image_id <= 0) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMAGE;
                $ret['html'] = _FAIM_ERROR_NO_IMAGE;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            // get image object
            $imageObj = $image_handler->get($image_id);
            // check image object
            if (!is_object($imageObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMAGE;
                $ret['html'] = _FAIM_ERROR_NO_IMAGE;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            // update image object
            $error = false;
            $image_nicename = !empty($image_nicename) ? trim($image_nicename) : $imageObj->getVar('image_nicename');
            $imageObj->setVar('image_nicename', $image_nicename);
            $imageObj->setVar('image_display', $image_display);
            $imageObj->setVar('image_weight', $image_weight);
            $imageObj->setVar('imgcat_id', $imgcat_id);
            // store image object in db
            if (!$image_handler->insert($imageObj)) {
                $error = sprintf(_FAILSAVEIMG, $imageObj->getVar('image_nicename'));
            }
            // generate ajax return
            if ($error) {
                // ERROR
                $arr = ['error', $error];
            } else {
                $arr = ['success', $imageObj->getVar('image_name'), $imageObj->getVar('image_nicename')];
            }
            echo json_encode($arr);
            exit();

        case 'deleteImage' :
            $name = Request::getString('name', '');
            $ok = Request::getBool('delete_image_ok', false);
            $image_id = Request::getInt('image_id', 0);

            // check image_id
            if ($image_id <= 0) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMAGE;
                $ret['html'] = _FAIM_ERROR_NO_IMAGE;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // get image object
            $imageObj = $image_handler->get($image_id);
            // check image
            if (!is_object($imageObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMAGE_OBJECT;
                $ret['html'] = _FAIM_ERROR_NO_IMAGE_OBJECT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            if ($ok == false) {
                // NOT USED
                /*
                  $imagesListArray = $image_handler->getList($imgcat_id);
                  $imagesListArrayCount = count($imagesListArray);
                  // render delete imgcat form
                  $html = '';
                  // generate ajax return
                  $ret = array();
                  $ret['data'] = array(
                  'name' => $name,
                  'image_id' => $image_id,
                  'image_nicename' => $image->getVar('image_nicename')
                  );
                  $ret['error'] = true;
                  $ret['message'] = _ERRORS;
                  $ret['html'] = '';
                  $ret['javascript'] = '// NOP';
                  echo json_encode($ret);
                 */
                exit();
            }
            // delete image object form db
            $errors = [];
            if (!$image_handler->delete($imageObj)) {
                // ERROR
                $errors[] = sprintf(_FAIM_ERROR_NO_IMAGE_DELETE, $imageObj->getVar('image_nicename'));
            } else {
                // delete image file from filesystem
                if (file_exists(XOOPS_UPLOAD_PATH . '/' . $imageObj->getVar('image_name')) && !unlink(XOOPS_UPLOAD_PATH . '/' . $imageObj->getVar('image_name'))) {
                    // ERROR
                    $errors[] = sprintf(_FAIM_ERROR_NO_IMAGE_DELETE, $imageObj->getVar('image_name'));
                }
            }

            if (count($errors) > 0) {
                ob_start(); // start output buffering
                xoops_error($errors);
                $error_html = ob_get_contents(); // store buffer in variable
                ob_end_clean(); // end buffering and clean up
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = $error_html;
                $ret['html'] = $error_html;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            } else {
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = false;
                $ret['message'] = _FAIM_IMAGE_DELETED;
                $ret['html'] = _FAIM_IMAGE_DELETED;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            break;


        // Imgcats operations
        case 'getImgcatForm' :
            $name = Request::getString('name');
            $imgcat_id = Request::getInt('imgcat_id', 0);

            // check imgcat_id
            if ($imgcat_id <= 0) {
                // create imgcat object
                $imgcatObj = $imgcat_handler->create();
                $imgcatObj->setVar('imgcat_maxsize', 50000); // default for new imgcat
                $imgcatObj->setVar('imgcat_maxwidth', 120); // default for new imgcat
                $imgcatObj->setVar('imgcat_maxheight', 120); // default for new imgcat
                $title = _FAIM_ADD_IMGCAT;
            } else {
                // get imgcat object
                $imgcatObj = $imgcat_handler->get($imgcat_id);
                $title = _FAIM_EDIT_IMGCAT;
            }
            // check imgcat object
            if (!is_object($imgcatObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_OBJECT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_OBJECT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // create imgcat form
            xoops_load('XoopsFormLoader');
            $formObj = new \XoopsSimpleForm($title, 'imagecat_form', 'formajaximagemanager.php', 'post', true);
            $formObj->addElement(new \XoopsFormText(_MD_IMGCATNAME, $name . '_imgcat_name', 50, 255, $imgcatObj->getVar('imgcat_name')), true);
            $formObj->addElement(new \XoopsFormSelectGroup(_MD_IMGCATRGRP, $name . '_imgcat_readgroup', true, array_merge($imgcatObjperm_handler->getGroupIds('imgcat_read', $imgcat_id), array(XOOPS_GROUP_ADMIN)), 3, true));
            $formObj->addElement(new \XoopsFormSelectGroup(_MD_IMGCATWGRP, $name . '_imgcat_writegroup', true, array_merge($imgcatObjperm_handler->getGroupIds('imgcat_write', $imgcat_id), array(XOOPS_GROUP_ADMIN)), 3, true));
            $formObj->addElement(new \XoopsFormText(_IMGMAXSIZE, $name . '_imgcat_maxsize', 10, 10, $imgcatObj->getVar('imgcat_maxsize')));
            $formObj->addElement(new \XoopsFormText(_IMGMAXWIDTH, $name . '_imgcat_maxwidth', 3, 4, $imgcatObj->getVar('imgcat_maxwidth')));
            $formObj->addElement(new \XoopsFormText(_IMGMAXHEIGHT, $name . '_imgcat_maxheight', 3, 4, $imgcatObj->getVar('imgcat_maxheight')));
            $formObj->addElement(new \XoopsFormText(_MD_IMGCATWEIGHT, $name . '_imgcat_weight', 3, 4, $imgcatObj->getVar('imgcat_weight')));
            $formObj->addElement(new \XoopsFormRadioYN(_MD_IMGCATDISPLAY, $name . '_imgcat_display', $imgcatObj->getVar('imgcat_display'), _YES, _NO));
            $storetype = ['db' => _MD_INDB, 'file' => _MD_ASFILE];
            //$formObj->addElement(new \XoopsFormLabel( _MD_IMGCATSTRTYPE, $storetype[$imgcatObj->getVar('imgcat_storetype')]));
            if (!$imgcatObj->isNew()) {
                $formObj->addElement(new \XoopsFormHidden($name . '_imgcat_id', $imgcat_id));
                $formObj->addElement(new \XoopsFormHidden($name . '_op', 'update.imgcat'));
            } else {
                $formObj->addElement(new \XoopsFormHidden($name . '_imgcat_id', 0));
                $formObj->addElement(new \XoopsFormHidden($name . '_op', 'create.imgcat'));
            }
            $formObj->addElement(new \XoopsFormHidden('fct', 'images'));
            $formObj->addElement(new \XoopsFormButton('', $name . '_submit_imgcat_button', _SUBMIT, 'button'));
            // render edit imgcat form
            $html = '';
            $html .= "<div id='" . $name . "_edit_imgcat_form'>";
            $html .= '<b>' . $formObj->getTitle() . "</b>\n";
            $html .= "<br />\n";
            foreach ($formObj->getElements() as $ele) {
                if (!$ele->isHidden()) {
                    $caption = $ele->getCaption();
                    if (!empty($caption)) {
                        $html .= '<strong>' . $ele->getCaption() . "</strong><br />\n";
                    }
                    $html .= $ele->render() . "<br />\n";
                } else {
                    $html .= $ele->render() . "\n";
                }
            }
            $html .= '</div>';
            // generate ajax return
            $ret = [];
            $ret['data'] = null;
            $ret['error'] = false;
            $ret['message'] = '';
            $ret['html'] = $html;
            $ret['javascript'] = '// NOP';
            echo json_encode($ret);
            exit();

        case 'submitImgcatForm' :
            $name = Request::getString('name', '');
            $imgcat_id = Request::getInt('imgcat_id', 0);

            // check imgcat_id
            if ($imgcat_id <= 0) {
                // create imgcat object
                $imgcatObj = $imgcat_handler->create();
                $title = _FAIM_ADD_IMGCAT;
            } else {
                // get imgcat object
                $imgcatObj = $imgcat_handler->get($imgcat_id);
                $title = _FAIM_EDIT_IMGCAT;
            }
            // check imgcat object
            if (!is_object($imgcatObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_OBJECT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_OBJECT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            $imgcatObj->setVar('imgcat_name', $_POST['imgcat_name']);
            $imgcatObj->setVar('imgcat_maxsize', $_POST['imgcat_maxsize']);
            $imgcatObj->setVar('imgcat_maxwidth', $_POST['imgcat_maxwidth']);
            $imgcatObj->setVar('imgcat_maxheight', $_POST['imgcat_maxheight']);
            $imgcat_display = empty($_POST['imgcat_display']) ? false : true;
            $imgcatObj->setVar('imgcat_display', $imgcat_display);
            $imgcatObj->setVar('imgcat_weight', $_POST['imgcat_weight']);
            if ($imgcat_id <= 0) {
                $imgcatObj->setVar('imgcat_storetype', $_POST['imgcat_storetype']);
                $imgcatObj->setVar('imgcat_type', 'C');
            } else {
                // NOP
            }

            if (!$imgcat_handler->insert($imgcatObj)) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_DB;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_DB;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            $imgcat_id = $imgcatObj->getVar('imgcat_id');
            // update permissions
            $imgcatObjperm_handler = xoops_getHandler('groupperm');
            $readGroups = isset($_POST['imgcat_readgroup']) ? $_POST['imgcat_readgroup'] : [];
            //$readGroups = XoopsRequest::getArray('imgcat_readgroup', array(), 'POST');
            //if (!is_array($readgroups)) $readgroups = array();
            if (!in_array(XOOPS_GROUP_ADMIN, $readGroups)) {
                array_push($readGroups, XOOPS_GROUP_ADMIN);
            }
            foreach ($readGroups as $readGroup) {
                $imgcatObjperm = $imgcatObjperm_handler->create();
                $imgcatObjperm->setVar('gperm_groupid', $readGroup);
                $imgcatObjperm->setVar('gperm_itemid', $imgcat_id);
                $imgcatObjperm->setVar('gperm_name', 'imgcat_read');
                $imgcatObjperm->setVar('gperm_modid', 1);
                $imgcatObjperm_handler->insert($imgcatObjperm);
                unset($imgcatObjperm);
            }

            $writeGroups = isset($_POST['imgcat_writegroup']) ? $_POST['imgcat_writegroup'] : [];
            //$writeGroups = XoopsRequest::getArray('imgcat_writegroup', array(), 'POST');
            if (!in_array(XOOPS_GROUP_ADMIN, $writeGroups)) {
                array_push($writeGroups, XOOPS_GROUP_ADMIN);
            }
            foreach ($writeGroups as $writeGroup) {
                $imgcatObjperm = $imgcatObjperm_handler->create();
                $imgcatObjperm->setVar('gperm_groupid', $writeGroup);
                $imgcatObjperm->setVar('gperm_itemid', $imgcat_id);
                $imgcatObjperm->setVar('gperm_name', 'imgcat_write');
                $imgcatObjperm->setVar('gperm_modid', 1);
                $imgcatObjperm_handler->insert($imgcatObjperm);
                unset($imgcatObjperm);
            }

            // generate ajax return
            $ret = [];
            $ret['data'] = null;
            $ret['error'] = false;
            $ret['message'] = '';
            $ret['html'] = '';
            $ret['javascript'] = '// NOP';
            echo json_encode($ret);
            exit();

        case 'deleteImgcat' :
            $name = Request::getString('name', '');
            $ok = Request::getBool('delete_imgcat_ok', false);
            $imgcat_id = Request::getInt('imgcat_id', 0);

            // check imgcat_id
            if ($imgcat_id <= 0) {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }

            $imgcatObj = $imgcat_handler->get($imgcat_id);
            // check imgcatObj
            if (!is_object($imgcatObj)) {
                // generate ajax return
                // ERROR
                $ret = [];
                $ret['data'] = null;
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // check imgcat_type
            if ($imgcatObj->getVar('imgcat_type') != 'C') {
                // ERROR
                // generate ajax return
                $ret = [];
                $ret['data'] = array(
                    'name' => $name,
                    'imgcat_id' => $imgcat_id,
                    'imgcat_name' => $imgcatObj->getVar('imgcat_name')
                );
                $ret['error'] = true;
                $ret['message'] = _FAIM_ERROR_NO_IMGCAT_EDIT;
                $ret['html'] = _FAIM_ERROR_NO_IMGCAT_EDIT;
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            if ($ok == false) {
                $imagesListArray = $image_handler->getList($imgcat_id);
                $imagesListArrayCount = count($imagesListArray);
                // render delete imgcat form
                $html = '';
                // generate ajax return
                $ret = [];
                $ret['data'] = array(
                    'name' => $name,
                    'imgcat_id' => $imgcat_id,
                    'imgcat_name' => $imgcatObj->getVar('imgcat_name')
                );
                $ret['error'] = true;
                $ret['message'] = _ERRORS;
                $ret['html'] = '';
                $ret['javascript'] = '// NOP';
                echo json_encode($ret);
                exit();
            }
            // delete images
            $imagesObjs = $image_handler->getObjects(new \Criteria('imgcat_id', $imgcat_id), true, false);
            $errors = [];
            foreach ($imagesObjs as $imageObj) {
                if (!$image_handler->delete($imageObj)) {
                    // ERROR
                    $errors[] = sprintf(_FAIM_ERROR_NO_IMAGE_DELETE, $imageObj->getVar('image_nicename'));
                } else {
                    if (file_exists(XOOPS_UPLOAD_PATH . '/' . $imageObj->getVar('image_name')) && !unlink(XOOPS_UPLOAD_PATH . '/' . $imageObj->getVar('image_name'))) {
                        // ERROR
                        $errors[] = sprintf(_FAIM_ERROR_NO_IMAGE_DELETE, $imageObj->getVar('image_name'));
                    }
                }
            }
            // delete imgcat
            if (!$imgcat_handler->delete($imgcatObj)) {
                // ERROR
                $errors[] = sprintf(_FAIM_ERROR_NO_IMGCAT_DELETE, $imgcatObj->getVar('imgcat_name'));
            }
            if (count($errors) > 0) {
                ob_start(); // start output buffering
                xoops_error($errors);
                $error_html = ob_get_contents(); // store buffer in variable
                ob_end_clean(); // end buffering and clean up
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => true,
                    'message' => $error_html,
                    'html' => $error_html,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            } else {
                // generate ajax return
                $ret = [
                    'data' => null,
                    'error' => false,
                    'message' => _FAIM_IMGCAT_DELETED,
                    'html' => _FAIM_IMGCAT_DELETED,
                    'javascript' => '// NOP'
                ];
                echo json_encode($ret);
                exit();
            }
            break;
    }
} else {
	/**
	 * 	no ajax request
	 *  render HTML output
	 */
    if (!class_exists('FormAjaxImageManager')) {
        xoops_load('XoopsFormElement');

        /**
         * A ajax image manager
         */
        class FormAjaxImageManager extends \XoopsFormElement {

            /**
             * return mode: 'url', 'id', 'bbcode'
             *
             * @var string
             * @access private
             */
            private $_return;

            /**
             * value: default field value
             *
             * @var string
             * @access private
             */
            private $_value = '';

            /**
             * image id
             *
             * @var int
             * @access private
             */
            private $_image_id;

            /**
             * image category id
             *
             * @var int
             * @access private
             */
            private $_imgcat_id;

            /**
             * if true it's possible to edit/create categories from this element
             *
             * @var boolean
             * @access private
             */
            private $_editimgcat;

            /**
             * element id, it will different from element name for javascript issues
             *
             * @var string
             * @access private
             */
            private $_id;

            /**
             * Constuctor
             *
             * @param string $caption caption
             * @param string $name    name
             * @param string $value   initial content
             * @param array  $options
             */
            public function __construct($caption, $name, $value = '', $options = []) {
                $this->setCaption($caption);
                $this->setName($name);
                $this->setId($name);
                $this->setValue($value);
                $this->setReturn(isset($options['return']) ? $options['return'] : 'url'); // 'url', 'html', 'bbcode', 'id', 'image_id' ...
                $this->_editimgcat = isset($options['editimgcat']) ? (boolean) $options['editimgcat'] : false;

                $myts = \MyTextSanitizer::getInstance();

                $imgcat_handler = xoops_getHandler('imagecategory');
                $image_handler = xoops_getHandler('image');

                $userGroups = (is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->getGroups() : [XOOPS_GROUP_ANONYMOUS];

                $categories = $imgcat_handler->getList($userGroups, 'imgcat_read', 1);
                $categoriesCount = count($categories);

                // set default imgcat_id
                $this->setDefaultImgcatId(($categoriesCount > 0) ? current(array_keys($categories)) : null);

                // get imgcat_id from image_name/image_id
                if ($value != '') {
                    switch ($this->getReturn()) {
                        case 'url' :
                            $image_name = basename($value);
                            $image_name = preg_replace('/^.+[\\\\\\/]/', '', $value); // because basename() has a bug when processes Asian characters like Chinese
                            $criteria = new \Criteria('image_name', $image_name);
                            break;
                        case 'html' :
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($value);
                            $tags = $doc->getElementsByTagName('img');
                            foreach ($tags as $tag) {
                                $image_url = $tag->getAttribute('src');
                            }
                            $image_name = basename($image_url); // IN PROGRESS
                            $image_name = preg_replace('/^.+[\\\\\\/]/', '', $image_url); // because basename() has a bug when processes Asian characters like Chinese
                            $criteria = new \Criteria('image_name', $image_name);
                            break;
                        case 'id' :
                        case 'image_id' :
                            $criteria = new \Criteria('image_id', $value);
                            break;
                        case 'bbcode' :
                            $value = $myts->displayTarea($value, false, false, true, true, false);
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($value);
                            $tags = $doc->getElementsByTagName('img');
                            foreach ($tags as $tag) {
                                $image_url = $tag->getAttribute('src');
                            }
                            $image_name = basename($image_url); // IN PROGRESS
                            $image_name = preg_replace('/^.+[\\\\\\/]/', '', $image_url); // because basename() has a bug when processes Asian characters like Chinese
                            $criteria = new \Criteria('image_name', $image_name);
                            break;
                    }
                    $imagesObjs = $image_handler->getObjects($criteria, false, false);
                    if (count($imagesObjs) == 1) {
                        $imageObj = $imagesObjs[0];
                        $this->setDefaultImageId($imageObj->getVar('image_id'));
                        $this->setDefaultImgcatId($imageObj->getVar('imgcat_id'));
                    }
                }
            }

            /**
             * set the "id" attribute for the element
             *
             * @param string $name "name" attribute for the element
             */
            public function setId($name = null) {
                $this->_id = $name === null ? md5(uniqid(mt_rand(), true)) : $name;
            }

            /**
             * get the "id" attribute for the element
             *
             * @param bool $encode
             *
             * @return string "name" attribute
             */
            public function getId($encode = true)
            {
                if (false != $encode) {
                    return str_replace('&amp;', '&', htmlspecialchars($this->_id, ENT_QUOTES));
                }

                return $this->_id;
            }

            /**
             * get return mode
             *
             * @return string
             */
            public function getReturn()
            {
                return $this->_return;
            }

            /**
             * set return mode
             *
             * @param $return
             *
             * @return void
             */
            public function setReturn($return)
            {
                $this->_return = $return;
            }

            /**
             * Set initial content
             *
             * @param string $value
             */
            public function setValue($value)
            {
                $this->_value = $value;
            }

            /**
             * Get initial content
             *
             * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
             * @return string
             */
            public function getValue($encode = false)
            {
                return $encode ? htmlspecialchars($this->_value, ENT_QUOTES | ENT_HTML5) : $this->_value;
            }

            /**
             * Set initial content
             *
             * @param $image_id
             * @internal param string $value
             */
            public function setDefaultImageId($image_id)
            {
                $this->_image_id = $image_id;
            }

            /**
             * Get image_id
             *
             * @return int
             */
            public function getDefaultImageId()
            {
                return $this->_image_id;
            }

            /**
             * Set initial content
             *
             * @param $imgcat_id
             * @internal param string $value
             */
            public function setDefaultImgcatId($imgcat_id)
            {
                $this->_imgcat_id = $imgcat_id;
            }

            /**
             * Get imgcat_id
             *
             * @return int
             */
            public function getDefaultImgcatId()
            {
                return $this->_imgcat_id;
            }

            /**
             * prepare HTML for output
             *
             * @return string HTML
             */
            public function render()
            {
                static $isCommonFormAjaxImageManagerIncluded = false;
                $commonJs = '';
                $js = '';
                $html = '';
                $ret = '';

                $myts = \MyTextSanitizer::getInstance();

                if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/' . $_GLOBALS['xoopsConfig']['language'] . '/images/lightbox-btn-close.gif')) {
                    $xoops_language = $_GLOBALS['xoopsConfig']['language'];
                } else {
                    $xoops_language = 'english';
                }

                $commonJs = '
    var ajaxloader = new Array();
    var selected_imgcat_id = new Array();
    var selected_image_id = new Array();
    var returnType = new Array();

    $.ajaxSetup({
      beforeSend: function(jqXHR, settings) {
          if (settings.url.split("?").length > 1) {
             settings.url = settings.url + "&ajax=1";
          } else {
              settings.url = settings.url + "?ajax=1";
          }
       },
    data: {
        ajax: "1"
      }
    });

    //var $xoopsJQuery = jQuery.noConflict();
    function getImgcatImagesList(name, imgcat_id) {
        $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "getImgcatImagesList",
                imgcat_id: imgcat_id
                },
            success: function (data, state) {
                if (!data.error) {
                    var images = data.data.images;
                    var perm = data.data.perm;
                    if (perm["img_write"] == true) {
                        $("input#" + name + "_upload_image_button").show();
                    } else {
                        $("input#" + name + "_upload_image_button").hide();
                    }
                    var content = "";
                    $("div#" + name + "_container").empty();
                    var image = "";
                    for (var key in images) {
                        var imageUrl = "' . XOOPS_URL . '/image.php?id=" + images[key].image_id + "";
                        var imageUrl = "' . XOOPS_URL . '/uploads/" + images[key].image_name + "";
                        image =  "<div class=\"floatleft\">";
                        image += "  <div class=\"faim-thumb\" image_id=\"" + images[key].image_id + "\" image_url=\"" + imageUrl + "\" image_nicename=\"" + images[key].image_nicename + "\" title=\"' . _FAIM_IMAGE_NICENAME . ': " + images[key].image_nicename + "&#13;' . _FAIM_IMAGE_ID . ': " + images[key].image_id + "&#13;' . _FAIM_IMAGE_WIDTH . ': " + images[key].image_width + "px&#13;' . _FAIM_IMAGE_HEIGHT . ': " + images[key].image_height + "px\" >";
                        image += "      <div class=\"faim-thumbimg\" style=\"background-image:url(\'' . XOOPS_URL . '/image.php?id=" + images[key].image_id + "&width=140&height=140\')\">";
                        image += "          <div class=\"faim-actions\"><div class=\"inner5\">";
                        image += "              <a class=\"lightbox\" href=\"" + imageUrl + "\" title=\"' . _FAIM_VIEW_IMAGE . '\">";
                        image += "                  <img src=\"' . XOOPS_URL . '/modules/system/images/icons/default/display.png\" alt=\"' . _FAIM_VIEW_IMAGE . '\" title=\"\" /></a>";
                        if (perm["img_write"] == true) {
                            image += "              <span class=\"faim-edit\" href=\"' . XOOPS_URL . '/uploads/" + images[key].image_name + "\" title=\"' . _FAIM_EDIT_IMAGE . '\">";
                            image += "                  <img src=\"' . XOOPS_URL . '/modules/system/images/icons/default/edit.png\" alt=\"' . _FAIM_EDIT_IMAGE . '\" title=\"\" /></span>";
                            image += "              <span class=\"faim-delete\" href=\"' . XOOPS_URL . '/uploads/" + images[key].image_name + "\" title=\"' . _FAIM_DELETE_IMAGE . '\">";
                            image += "                  <img src=\"' . XOOPS_URL . '/modules/system/images/icons/default/delete.png\" alt=\"' . _FAIM_DELETE_IMAGE . '\" title=\"\" /></span>";
                        }
                        image += "          </div></div>";
                        image += "      </div>";
                        image += "      <div class=\"faim-informations\">";
                        image += "          <span>" + images[key].image_nicename + "</span>";
                        image += "      </div>";
                        image += "      <div class=\"faim-selects\"><div class=\"inner0\">";
                        switch (returnType[name]) {
                            case "url" :
                                image += "          <span class=\"faim-url\" title=\"' . _FAIM_IMAGE_URL . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/url.gif\" alt=\"' . _FAIM_IMAGE_URL . '\" title=\"\" /></span>";
                                break;
                            case "html" :
                                image += "          <span class=\"faim-html\" title=\"' . _FAIM_IMAGE_HTML . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/code.gif\" alt=\"' . _FAIM_IMAGE_HTML . '\" title=\"\" /></span>";
                                break;
                            case "id" :
                            case "image_id" :
                                image += "          <span class=\"faim-id\" title=\"' . _FAIM_IMAGE_HTML . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/code.gif\" alt=\"' . _FAIM_IMAGE_HTML . '\" title=\"\" /></span>";
                                break;
                            case "bbcode" :
                                image += "          <span class=\"faim-bbcode-left\" title=\"' . _LEFT . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/alignleft.gif\" alt=\"' . _LEFT . '\" title=\"\" /></span>";
                                image += "          <span class=\"faim-bbcode-center\" title=\"' . _CENTER . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/aligncenter.gif\" alt=\"' . _CENTER . '\" title=\"\" /></span>";
                                image += "          <span class=\"faim-bbcode-right\" title=\"' . _RIGHT . '\">";
                                image += "              <img src=\"' . XOOPS_URL . '/images/alignright.gif\" alt=\"' . _RIGHT . '\" title=\"\" /></span>";
                        }
                        image += "      </div></div>";
                        image += "  </div>";
                        image += "</div>";
                        $("div#" + name + "_container").append(image);
                        $("div#" + name + "_container div[image_id=\"" + images[key].image_id + "\"] a.lightbox").lightBox({
                        	overlayBgColor: "#FFF",
                        	overlayOpacity: 0.6,
                        	imageLoading: "' . XOOPS_URL . '/modules/system/language/' . $xoops_language . '/images/lightbox-ico-loading.gif",
                        	imageBtnClose: "' . XOOPS_URL . '/modules/system/language/' . $xoops_language . '/images/lightbox-btn-close.gif",
                        	imageBtnPrev: "' . XOOPS_URL . '/modules/system/language/' . $xoops_language . '/images/lightbox-btn-prev.gif",
                        	imageBtnNext: "' . XOOPS_URL . '/modules/system/language/' . $xoops_language . '/images/lightbox-btn-next.gif",
                            imageBlank: "' . XOOPS_URL . '/modules/system/language/' . $xoops_language . '/images/lightbox-blank.gif",
                        	containerResizeSpeed: 350,
                        	txtImage: "Image",
                        	txtOf: "of"
                           });
                    }
                    // actions
                    $("div#" + name + "_container .faim-thumb .faim-actions .faim-edit").click(function() {
                        var image_id = $(this).parent().parent().parent().parent().attr("image_id");
                        getImageForm(name, image_id, selected_imgcat_id[name]);
                    });
                    $("div#" + name + "_container .faim-thumb .faim-actions .faim-delete").click(function() {
                        var image_id = $(this).parent().parent().parent().parent().attr("image_id");
                        deleteImage(name, image_id, false);
                    });
                    // select return: url
                    $("div#" + name + "_container .faim-thumb .faim-selects .faim-url").click(function() {
                        var image_url = $(this).parent().parent().parent().attr("image_url");
                        var image_id = $(this).parent().parent().parent().attr("image_id");
                        var image_nicename = $(this).parent().parent().parent().attr("image_nicename");
                        selected_image_id[name] = image_id;
                        $("input#" + name + "").val(image_url);
                    });
                    // select return: html
                    $("div#" + name + "_container .faim-thumb .faim-selects .faim-html").click(function() {
                        var image_url = $(this).parent().parent().parent().attr("image_url");
                        var image_id = $(this).parent().parent().parent().attr("image_id");
                        var image_nicename = $(this).parent().parent().parent().attr("image_nicename");
                        selected_image_id[name] = image_id;
                        $("input#" + name + "").val("<img src=\"" + image_url + "\" alt=\"" + image_nicename + "\" />");
                    });
                    // select return: bbcode-left
                    $("div#" + name + "_container .faim-thumb .faim-selects .faim-bbcode-left").click(function() {
                        var image_url = $(this).parent().parent().parent().attr("image_url");
                        var image_id = $(this).parent().parent().parent().attr("image_id");
                        var image_nicename = $(this).parent().parent().parent().attr("image_nicename");
                        selected_image_id[name] = image_id;
                        $("input#" + name + "").val("[img align=left]" + image_url + "[/img]");
                    });
                    // select return: bbcode-center
                    $("div#" + name + "_container .faim-thumb .faim-selects .faim-bbcode-center").click(function() {
                        var image_url = $(this).parent().parent().parent().attr("image_url");
                        var image_id = $(this).parent().parent().parent().attr("image_id");
                        var image_nicename = $(this).parent().parent().parent().attr("image_nicename");
                        selected_image_id[name] = image_id;
                        $("input#" + name + "").val("[img align=center]" + image_url + "[/img]");
                    });
                    // select return: bbcode-right
                    $("div#" + name + "_container .faim-thumb .faim-selects .faim-bbcode-right").click(function() {
                        var image_url = $(this).parent().parent().parent().attr("image_url");
                        var image_id = $(this).parent().parent().parent().attr("image_id");
                        var image_nicename = $(this).parent().parent().parent().attr("image_nicename");
                        selected_image_id[name] = image_id;
                        $("input#" + name + "").val("[img align=right]" + image_url + "[/img]");
                    });
                    eval(data.javascript);
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function getImgcatForm(name, imgcat_id) {
        $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        $.ajax({
            url : "' . FORMAJAXIMAGEMANAGER_URL . '",
            type : "POST",
            dataType : "json",
            data : {
                op : "getImgcatForm",
                name: name,
                imgcat_id : imgcat_id
                },
            success : function (data, state) {
                if (!data.error) {
                    $("div#" + name + "_container").html(data.html);
                    eval(data.javascript);
                    $("input#" + name + "_submit_imgcat_button").click(function() {
                        submitImgcatForm(name);
                    });
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error : function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function submitImgcatForm(name) {
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        var imgcat_id = $("#" + name + "_imgcat_id").val()
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "submitImgcatForm",
                name: name,
                imgcat_name: $("#" + name + "_imgcat_name").val(),
                imgcat_readgroup: $("#" + name + "_imgcat_readgroup").val(),
                imgcat_writegroup: $("#" + name + "_imgcat_writegroup").val(),
                imgcat_maxsize: $("#" + name + "_imgcat_maxsize").val(),
                imgcat_maxwidth: $("#" + name + "_imgcat_maxwidth").val(),
                imgcat_maxheight: $("#" + name + "_imgcat_maxheight").val(),
                imgcat_weight: $("#" + name + "_imgcat_weight").val(),
                imgcat_display: $("input[name=\'" + name + "_imgcat_display\']:checked").val(),
                imgcat_id: imgcat_id
                },
            success: function (data, state) {
                if (!data.error) {
                    for (var key in selected_imgcat_id) {
                        update_imgcat_select(key);
                    }
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function deleteImgcat(name, imgcat_id, ok) {
        ok = typeof ok !== "undefined" ? ok : false; // default 0/false
        $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        var $div = $("<div></div>").attr("id", name + "_delete_imgcat_form").attr("class", "confirmMsg faim-formajaximagemenager_confirmMsg");
        $div.append("' . _FAIM_RUS_DELETE_IMGCAT . '");
        $div.append("<br>");
        var $button_delete_imgcat_yes = $("<button/>", {
            id: "" + name + "_delete_imgcat_yes",
            type: "button",
            value: "' . _YES . '",
            title: "' . _YES . '"
            }).html("' . _YES . '");
        $button_delete_imgcat_yes.click(function(){
            $.ajax({
                url : "' . FORMAJAXIMAGEMANAGER_URL . '",
                type : "POST",
                dataType : "json",
                data : {
                    op : "deleteImgcat",
                    name: name,
                    imgcat_id : imgcat_id,
                    delete_imgcat_ok : true
                    },
                success : function (data, state) {
                    if (!data.error) {
                        for (var key in selected_imgcat_id) {
                            update_imgcat_select(key);
                        }
                    } else {
                        alert(data.message);
                    }
                    ajaxloader[name].remove();
                },
                error : function (request, state, error) {
                    alert("ERROR. Ajax call state: " + state);
                    ajaxloader[name].remove();
                },
                complete: function (request, state) {
                    // NOP
                }
            });
        });
        var $button_delete_imgcat_no = $("<button/>", {
            id: "" + name + "_delete_imgcat_no",
            type: "button",
            value: "' . _NO . '",
            title: "' . _NO . '"
            }).html("' . _NO . '");
        $button_delete_imgcat_no.click(function(){
            selected_imgcat_id[name] = $("select#" + name + "_imgcat_select").val();
            getImgcatImagesList(name, selected_imgcat_id[name]);
        });
        $div.append($button_delete_imgcat_yes);
        $div.append("&nbsp;");
        $div.append($button_delete_imgcat_no);
        $("div#" + name + "_container").html($div);
        ajaxloader[name].remove();
    }

    function getImageForm(name, image_id, imgcat_id) {
        $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "getImageForm",
                name: name,
                image_id: image_id,
                imgcat_id: imgcat_id
                },
            success: function (data, state) {
                if (!data.error) {
                    $("div#" + name + "_container").html(data.html);
                    eval(data.javascript);
                    if (image_id == 0) {
                        // new image: upload image file and update database
                        submitNewImageForm(name);
                    } else {
                        // not new image: update database
                        $("input#" + name + "_submit_image_button").click(function () {
                            submitUpdateImageForm(name);
                        });
                    }
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function submitNewImageForm(name) {
        $("div#" + name + "_edit_image_form :file").fileupload({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 5000000, // 5 MB
            // Enable image resizing, except for Android and Opera, which actually support image resizing, but fail to send Blob objects via XHR requests:
            disableImageResize: false,
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator && navigator.userAgent),
// IN PROGRESS get maxwidth from imgcat
            imageMaxWidth: 600,
 // IN PROGRESS get maxheight from imgcat
            imageMaxHeight: 600,
            imageCrop: true, // Force cropped images
            previewMaxWidth: 140,
            previewMaxHeight: 140,
            previewCrop: true,
            add: function (e, data) {
                data.context = $("input#" + name + "_submit_image_button").click(function () {
                    ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
                    data.formData = {
                        op: "submitNewImageForm",
                        name: name,
                        image_nicename: $("#" + name + "_image_nicename").val(),
                        imgcat_id: $("#" + name + "_image_imgcat_id").val(),
                        image_weight: $("#" + name + "_image_weight").val(),
                        image_display: $("input[name=\'" + name + "_image_display\']:checked").val()
                    };
                    data.submit();
                });
            },
            done: function (e, data) {
    //            data.result;
    //            data.textStatus;
    //            data.jqXHR;
                data = data.result;
                if (!data.error) {
                    // NOP
                } else {
                    alert(data.message);
                }
                for (var key in selected_imgcat_id) {
                    getImgcatImagesList(key, selected_imgcat_id[key]);
                }
                ajaxloader[name].remove();
            },
            fail: function (e, data) {
    //            data.errorThrown
    //            data.textStatus;
    //            data.jqXHR;
                ajaxloader[name].remove();
            },
            always: function (e, data) {
    //            data.result
    //            data.textStatus;
    //            data.jqXHR;
                // NOP
            }
        }).on("fileuploadadd", function (e, data) {
    //        console.log("fileuploadadd");
    //        data.context = $("<div/>").appendTo("#" + name + "_image_image_file");
    //        var index = data.index;
    //        var file = data.files[index];
    //        var node = $(data.context.children()[index]);
    //        if (file.preview) {
    //            node
    //                .prepend("<br>")
    //                .prepend(file.preview);
    //        }
        }).on("fileuploadprocessalways", function (e, data) {
    //        console.log("fileuploadprocessalways");
        }).on("fileuploadprogressall", function (e, data) {
    //        console.log("fileuploadprogressall");
        }).on("fileuploaddone", function (e, data) {
    //        console.log("fileuploaddone");
        }).on("fileuploadfail", function (e, data) {
    //        console.log("fileuploadfail");
        });
    }

    function submitUpdateImageForm(name) {
    //    $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "submitUpdateImageForm",
                name: name,
                image_id: $("#" + name + "_image_id").val(),
                image_nicename: $("#" + name + "_image_nicename").val(),
                imgcat_id: $("#" + name + "_image_imgcat_id").val(),
                image_weight: $("#" + name + "_image_weight").val(),
                image_display: $("input[name=\'" + name + "_image_display\']:checked").val()
                },
            success: function (data, state) {
                if (!data.error) {
                    selected_imgcat_id[name] = $("select#" + name + "_imgcat_select").val();
                    for (var key in selected_imgcat_id) {
                        getImgcatImagesList(key, selected_imgcat_id[key]);
                    }

                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function deleteImage(name, image_id, ok) {
        ok = typeof ok !== "undefined" ? ok : false; // default 0/false
        $("div#" + name + "_container").empty();
        ajaxloader[name] = new ajaxLoader("div#" + name + "_container");
        var $div = $("<div></div>").attr("id", name + "_delete_image_form").attr("class", "confirmMsg faim-formajaximagemenager_confirmMsg");
        $div.append("' . _FAIM_RUS_DELETE_IMAGE . '");
        $div.append("<br>");
        var $button_delete_image_yes = $("<button/>", {
            id: "" + name + "_delete_image_yes",
            type: "button",
            value: "' . _YES . '",
            title: "' . _YES . '"
            }).html("' . _YES . '");
        $button_delete_image_yes.click(function(){
            $.ajax({
                url : "' . FORMAJAXIMAGEMANAGER_URL . '",
                type : "POST",
                dataType : "json",
                data : {
                    op : "deleteImage",
                    name: name,
                    image_id : image_id,
                    delete_image_ok : true
                    },
                success : function (data, state) {
                    if (!data.error) {
                        for (var key in selected_imgcat_id) {
                            getImgcatImagesList(key, selected_imgcat_id[key]);
                        }
                    } else {
                        alert(data.message);
                    }
                    ajaxloader[name].remove();
                },
                error : function (request, state, error) {
                    alert("ERROR. Ajax call state: " + state);
                    ajaxloader[name].remove();
                },
                complete: function (request, state) {
                    // NOP
                }
            });
        });
        var $button_delete_image_no = $("<button/>", {
            id: "" + name + "_delete_image_no",
            type: "button",
            value: "' . _NO . '",
            title: "' . _NO . '"
            }).html("' . _NO . '");
        $button_delete_image_no.click(function(){
            selected_imgcat_id[name] = $("select#" + name + "_imgcat_select").val();
            getImgcatImagesList(name, selected_imgcat_id[name]);
        });
        $div.append($button_delete_image_yes);
        $div.append("&nbsp;");
        $div.append($button_delete_image_no);
        $("div#" + name + "_container").html($div);
        ajaxloader[name].remove();
    }

    function update_imgcat_select(name) {
        ajaxloader[name] = new ajaxLoader("select#" + name + "_imgcat_select");
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "getImgcats",
                name: name,
                select_imgcat_id: selected_imgcat_id[name]
                },
            success: function (data, state) {
                if (!data.error) {
                    if (data.data) {
                        var imgcats = data.data.imgcats;
                        // update imgcat_select
                        $("select#" + name + "_imgcat_select").empty();
                        for (var key in imgcats) {
                            var imgcat = imgcats[key].data;
                            var perm = imgcats[key].perm;
                            $("select#" + name + "_imgcat_select").append($("<option></option>").attr("value",imgcat["imgcat_id"]).text(imgcat["imgcat_name"]));
                        }
                        $("select#" + name + "_imgcat_select > option[value=\'" + selected_imgcat_id[name] + "\']").attr("selected", true);
                        selected_imgcat_id[name] = $("select#" + name + "_imgcat_select").val();
                        getImgcatImagesList(name, selected_imgcat_id[name]);
                     } else {
                        // no imgcats available
                        $("select#" + name + "_imgcat_select").empty();
                        $("div#" + name + "_container").html(data.html);
                    }
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });
    }

    function getImgcatPermissions(name, imgcat_id) {
        ajaxloader[name] = new ajaxLoader("select#" + name + "_imgcat_select");
        $.ajax({
            url: "' . FORMAJAXIMAGEMANAGER_URL . '",
            type: "POST",
            dataType: "json",
            data: {
                op: "getImgcatPermissions",
                name: name,
                imgcat_id : imgcat_id
                },
            success: function (data, state) {
                if (!data.error) {
                    // show/hide/enable/disable buttons
                    if (data.data.xoopsUser.isAdmin == true) {
                        $("input#" + name + "_create_imgcat_button").show();
                        $("input#" + name + "_edit_imgcat_button").show();
                        $("input#" + name + "_delete_imgcat_button").show();
                        $("input#" + name + "_upload_image_button").show();
                    } else {
                        $("input#" + name + "_create_imgcat_button").hide();
                        $("input#" + name + "_edit_imgcat_button").hide();
                        $("input#" + name + "_delete_imgcat_button").hide();

                        if (data.data.imgcats[imgcat_id].perm.img_write == true) {
                            $("input#" + name + "_upload_image_button").show();
                        } else {
                            $("input#" + name + "_upload_image_button").hide();
                        }
                    }
                } else {
                    alert(data.message);
                }
                ajaxloader[name].remove();
            },
            error: function (request, state, error) {
                alert("ERROR. Ajax call state: " + state);
                ajaxloader[name].remove();
            },
            complete: function (request, state) {
                // NOP
            }
        });

    }

    function formajaximagemanager_init(name) {
        selected_imgcat_id[name] = $("select#" + name + "_imgcat_select").val();
        getImgcatPermissions(name, selected_imgcat_id[name]);
        getImgcatImagesList(name, selected_imgcat_id[name]);
    }
                ';
                $html = "\n";
                if (is_object($GLOBALS['xoTheme'])) {
                    if (!$isCommonFormAjaxImageManagerIncluded) {
                        $isCommonFormAjaxImageManagerIncluded = true;
                        //$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/xoops.css');
                        $GLOBALS['xoTheme']->addStylesheet(FORMAJAXIMAGEMANAGER_CSS_REL_URL . '/formajaximagemanager.css');
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
                        //
                        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
                        //
                        $css = '.ajax_loader {
                            background: url(' . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.ajaxloader/spinner.gif) no-repeat center center transparent;
                            width:100%;
                            height:100%;
                        }';
                        $GLOBALS['xoTheme']->addStylesheet('', [], $css);
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.ajaxloader/jquery.ajaxloader.js');
                        // The jQuery UI widget factory, can be omitted if jQuery UI is already included
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/vendor/jquery.ui.widget.js');
                        // The Load Image plugin is included for the preview images and image resizing functionality
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.JavaScript-Load-Image-master/js/load-image.min.js');
                        // The Canvas to Blob plugin is included for image resizing functionality
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.JavaScript-Canvas-to-Blob-master/js/canvas-to-blob.min.js');
                        // The Iframe Transport is required for browsers without support for XHR file uploads
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/jquery.iframe-transport.js');
                        // The basic File Upload plugin
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/jquery.fileupload.js');
                        // The File Upload processing plugin
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/jquery.fileupload-process.js');
                        // The File Upload image preview & resize plugin
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/jquery.fileupload-image.js');
                        // The File Upload validation plugin
                        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.jQuery-File-Upload-master/js/jquery.fileupload-validate.js');
                        //
                        $GLOBALS['xoTheme']->addScript('', '', $commonJs);
                    }
                } else {
                    if (!$isCommonFormAjaxImageManagerIncluded) {
                        $isCommonFormAjaxImageManagerIncluded = true;
                        $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/xoops.css);</style>\n";
                        //
                        $html .= "<style type='text/css'>@import url(" . FORMAJAXIMAGEMANAGER_CSS_REL_URL . "/formajaximagemanager.css);</style>\n";
                        $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js' type='text/javascript'></script>\n";
                        //
                        $html .= "<style type='text/css'>@import url(" . XOOPS_URL . "/modules/system/css/lightbox.css);</style>\n";
                        $html .= "<script src='" . XOOPS_URL . "/browse.php?Frameworks/jquery/plugins/jquery.lightbox.js' type='text/javascript'></script>\n";
                        //
                        $css = '.ajax_loader {
                            background: url(' . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . '/jquery.ajaxloader/spinner.gif) no-repeat center center transparent;
                            width:100%;
                            height:100%;
                        }';
                        $html .= "<style type='text/css'>\n" . $css . "\n</style>\n";
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.ajaxloader/jquery.ajaxloader.js' type='text/javascript'></script>\n";
                        // The jQuery UI widget factory, can be omitted if jQuery UI is already included
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/vendor/jquery.ui.widget.js'></script>\n";
                        // The Load Image plugin is included for the preview images and image resizing functionality
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.JavaScript-Load-Image-master/js/load-image.min.js'></script>\n";
                        // The Canvas to Blob plugin is included for image resizing functionality
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.JavaScript-Canvas-to-Blob-master/js/canvas-to-blob.min.js'></script>\n";
                        // The Iframe Transport is required for browsers without support for XHR file uploads
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/jquery.iframe-transport.js'></script>\n";
                        // The basic File Upload plugin
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/jquery.fileupload.js'></script>\n";
                        // The File Upload processing plugin
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/jquery.fileupload-process.js'></script>\n";
                        // The File Upload image preview & resize plugin
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/jquery.fileupload-image.js'></script>\n";
                        // The File Upload validation plugin
                        $html .= "<script src='" . XOOPS_URL . '/browse.php?' . FORMAJAXIMAGEMANAGER_JS_REL_URL . "/jquery.jQuery-File-Upload-master/js/jquery.fileupload-validate.js'></script>\n";
                        //
                        $html .= "<script type='text/javascript'>\n" . $commonJs . "\n</script>\n";
                    }
                }

                $js = "
    $(document).ready(function() {
        var name = '{$this->getId()}';
        returnType[name] = '{$this->getReturn()}';
        selected_image_id[name] = '{$this->getDefaultImageId()}';
        selected_imgcat_id[name] = '{$this->getDefaultImgcatId()}';

        $('input#' + name + '_upload_image_button').click(function() {
            selected_imgcat_id[name] = $('select#' + name + '_imgcat_select').val();
            if (selected_imgcat_id[name]) {
                getImageForm(name, 0, selected_imgcat_id[name]);
            } else {
                // NOP
            }
        });
        $('input#' + name + '_create_imgcat_button').click(function() {
            getImgcatForm(name, 0);
        });

        $('select#' + name + '_imgcat_select').change(function() {
            selected_imgcat_id[name] = $('select#' + name + '_imgcat_select').val();
            if (selected_imgcat_id[name]) {
                getImgcatImagesList(name, selected_imgcat_id[name]);
            } else {
                // NOP
            }
        });
        $('input#' + name + '_view_imgcat_button').click(function() {
            selected_imgcat_id[name] = $('select#' + name + '_imgcat_select').val();
            if (selected_imgcat_id[name]) {
                getImgcatImagesList(name, selected_imgcat_id[name]);
            } else {
                // NOP
            }
        });
        $('input#' + name + '_edit_imgcat_button').click(function() {
            selected_imgcat_id[name] = $('select#' + name + '_imgcat_select').val();
            if (selected_imgcat_id[name]) {
                getImgcatForm(name, selected_imgcat_id[name]);
            } else {
                // NOP
            }
        });
        $('input#' + name + '_delete_imgcat_button').click(function() {
            selected_imgcat_id[name] = $('select#' + name + '_imgcat_select').val();
            if (selected_imgcat_id[name]) {
                deleteImgcat(name, selected_imgcat_id[name], false);
            } else {
                // NOP
            }
        });
        formajaximagemanager_init(name);
    });
                ";
                if (is_object($GLOBALS['xoTheme'])) {
                    $GLOBALS['xoTheme']->addScript('', '', $js);
                } else {
                    $html .= "<script type='text/javascript'>\n";
                    $html .= $js;
                    $html .= "</script>\n";
                }
                //
                $module_handler = xoops_getHandler('module');
                $systemModule = $module_handler->getByDirname('system');
                $systemModuleId = $systemModule->id;
                $userGroups = (is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->getGroups() : [XOOPS_GROUP_ANONYMOUS];
                $userIsAdmin = (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin());
                //
                $imgcat_handler = xoops_getHandler('imagecategory');
                if ($userIsAdmin) {
                    $categoriesListArray = $imgcat_handler->getList($userGroups, 'imgcat_read');
                } else {
                    $categoriesListArray = $imgcat_handler->getList($userGroups, 'imgcat_read', true);
                }
                $categoriesCount = count($categoriesListArray);
                //
                $html .= "<fieldset>\n";
                //$html .= "<legend>" . _FAIM_IMAGE_MANAGER . "</legend>\n";
                $html .= "<div>\n";
                $html .= _FAIM_IMGCAT . "\n";
                $html .= "<br />\n";
                $html .= "<select name='{$this->getId()}_imgcat_select' id='{$this->getId()}_imgcat_select' title='" . _FAIM_IMGCATS . "'>\n";
                if ($categoriesCount > 0) {
                    foreach ($categoriesListArray as $id => $imgcat_name) {
                        $html .= "<option value='{$id}'";
                        $html .= ($this->getDefaultImgcatId() !== null && $id == $this->getDefaultImgcatId()) ? " selected='selected'" : '';
                        $html .= ">{$imgcat_name}</option>\n";
                    }
                }
                $html .= "</select>\n";
                $html .= "&nbsp;\n";
                $html .= "<input type='button' name='{$this->getId()}_view_imgcat_button' id='{$this->getId()}_view_imgcat_button' title='" . _FAIM_VIEW_IMGCAT . "' value='" . _FAIM_VIEW_IMGCAT . "' />\n";
                if ($userIsAdmin) {
                    $html .= "&nbsp;\n";
                    $html .= "<input type='button' name='{$this->getId()}_edit_imgcat_button' id='{$this->getId()}_edit_imgcat_button' title='" . _FAIM_EDIT_IMGCAT . "' value='" . _EDIT . "' />\n";
                    $html .= "&nbsp;\n";
                    $html .= "<input type='button' name='{$this->getId()}_delete_imgcat_button' id='{$this->getId()}_delete_imgcat_button' title='" . _FAIM_DELETE_IMGCAT . "' value='" . _DELETE . "' />\n";
                    $html .= "&nbsp;\n";
                    $html .= "<input type='button' name='{$this->getId()}_create_imgcat_button' id='{$this->getId()}_create_imgcat_button' title='" . _FAIM_ADD_IMGCAT . "' value='" . _ADD . "' />\n";
                }
                $html .= "</div>\n";
                $html .= "<div>\n";
                $html .= "<input type='button' name='{$this->getId()}_upload_image_button' id='{$this->getId()}_upload_image_button' title='" . _FAIM_ADD_IMAGE . "' value='" . _FAIM_ADD_IMAGE . "' />\n";
                $html .= "</div>\n";
                $html .= "<br />\n";
                $html .= "<div name='{$this->getId()}_container' id='{$this->getId()}_container' class='faim-formajaximagemenager_container' {$this->getExtra()} >\n";
                $html .= "</div>\n";
                $html .= "</fieldset>\n";
                $html .= "<fieldset>\n";
                //$html .= "<legend>" . _FAIM_IMAGE . "</legend>\n";
                $html .= "<input type='text' name='{$this->getName()}' title='{$this->getTitle()}' id='{$this->getId()}' size='80' maxlength='255' value='{$myts->htmlSpecialChars($this->getValue())}' {$this->getExtra()} />\n";
                $html .= "</fieldset>\n";
                return $html;
            }

        }

    }
}

// Enable error reporting
error_reporting(1);
