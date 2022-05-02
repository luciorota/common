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
 *
 * Upload Media files
 *
 * Example of usage:
 * <code>
 * \xoops_load('MediaUploader', 'common');
 * $uploadPath = '/home/xoops/uploads';
 * $allowedMimeTypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png'];
 * $maxFileSize = 50000; // bytes
 * $maxFileWidth = 120; // pixels
 * $maxFileHeight = 120;  // pixels
 * $uploader = new common\MediaUploader($uploadPath, $allowedMimeTypes, $maxFileSize, $maxFileWidth, $maxFileHeight);
 * if ($uploader->fetchMedia($_POST['uploader_file_name'])) {
 *        if (!$uploader->upload()) {
 *           echo $uploader->getErrors();
 *        } else {
 *           echo '<h4>File uploaded successfully!</h4>'
 *           echo 'Saved as: ' . $uploader->getSavedFileName();
 *           echo '<br>';
 *           echo 'Full path: ' . $uploader->getSavedDestination();
 *        }
 * } else {
 *        echo $uploader->getErrors();
 * }
 * </code>
 *
 */

namespace XoopsModules\Common;

use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

$currentPath = __FILE__;
if (DIRECTORY_SEPARATOR != '/') {
    $currentPath = str_replace(strpos($currentPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $currentPath);
}

define('_ER_UP_PHPERR_INI_SIZE', 'The uploaded file exceeds the upload_max_filesize directive in php.ini');
define('_ER_UP_PHPERR_PARTIAL', 'The uploaded file was only partially uploaded');
define('_ER_UP_PHPERR_NO_FILE', 'No file was uploaded');
define('_ER_UP_PHPERR_NO_TMP_DIR', 'Missing a temporary folder');
define('_ER_UP_PHPERR_CANT_WRITE', 'Failed to write file to disk');
define('_ER_UP_PHPERR_EXTENSION', 'A PHP extension stopped the file upload');

xoops_load('XoopsMediaUploader');

/**
 * Class mediaUploader
 */
class MediaUploader extends \XoopsMediaUploader {

    public $warnings = [];

    /**
     * Check if uploaded file exists
     *
     * @param string        $mediaName Name of the input file form element $_FILES[$mediaName]
     * @param int           $index Index of the file (if more than one uploaded under that name)
     * @return bool
     */
    public function mediaExists($mediaName, $index = null) {
        // 4: UPLOAD_ERR_NO_FILE
        if (empty($_FILES[$mediaName]['name']) && $_FILES[$mediaName]['size'] == 0 && $_FILES[$mediaName]['error'] == UPLOAD_ERR_NO_FILE) {
            return false;
        } else if (is_array($_FILES[$mediaName]['name']) && isset($index) && empty($_FILES[$mediaName]['name'][$index]) && $_FILES[$mediaName]['size'][$index] == 0 && $_FILES[$mediaName]['error'][$index] == UPLOAD_ERR_NO_FILE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Normalize $_FILES array
     *
     * @return array
     */
    public function normalizedFILES() {
        $out = [];
        foreach ($_FILES as $key => $file) {
            if (isset($file['name']) && is_array($file['name'])) {
                $new = [];
                foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $k) {
                    array_walk_recursive($file[$k], function (&$data, $key, $k) {
                        $data = [$k => $data];
                    }, $k);
                    $new = array_replace_recursive($new, $file[$k]);
                }
                $out[$key] = $new;
            } else {
                $out[$key] = $file;
            }
        }
        return $out;
    }

    /**
     * Fetch the uploaded file
     *
     * @param string        $mediaName Name of the file field
     * @param int           $index Index of the file (if more than one uploaded under that name)
     * @return bool
     */
    public function fetchMedia($mediaName, $index = null) {
        $this->errors = [];
        if (empty($this->extensionToMime)) {
            $this->setErrors(_ER_UP_MIMETYPELOAD);
            return false;
        }
        if (!isset($_FILES[$mediaName])) {
            $this->setErrors(_ER_UP_FILENOTFOUND);
            return false;
        } else if (is_array($_FILES[$mediaName]['name']) && isset($index)) {
            // multiple files upload
            $index = (int) $index;
            $this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($_FILES[$mediaName]['name'][$index]) : $_FILES[$mediaName]['name'][$index];
            $this->mediaType = $_FILES[$mediaName]['type'][$index];
            $this->mediaSize = $_FILES[$mediaName]['size'][$index];
            $this->mediaTmpName = $_FILES[$mediaName]['tmp_name'][$index];
            $this->mediaError = !empty($_FILES[$mediaName]['error'][$index]) ? $_FILES[$mediaName]['error'][$index] : 0;
        } else {
            // single file upload
            $mediaName = & $_FILES[$mediaName];
            $this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($mediaName['name']) : $mediaName['name'];
            $this->mediaType = $mediaName['type'];
            $this->mediaSize = $mediaName['size'];
            $this->mediaTmpName = $mediaName['tmp_name'];
            $this->mediaError = !empty($mediaName['error']) ? $mediaName['error'] : 0;
        }
        if (($ext = strrpos($this->mediaName, '.')) !== false) {
            $ext = strtolower(substr($this->mediaName, $ext + 1));
            if (isset($this->extensionToMime[$ext])) {
                $this->mediaRealType = $this->extensionToMime[$ext];
            }
        }
        // check first for file-upload errors
        if ($this->mediaError > 0) {
            $this->setErrors(sprintf(_ER_UP_ERROROCCURRED, $this->mediaError));
            switch ($this->mediaError) {
                case UPLOAD_ERR_INI_SIZE: // 1: the uploaded file exceeds the upload_max_filesize directive in php.ini
                    $this->setErrors(_ER_UP_PHPERR_INI_SIZE);
                    break;
                case UPLOAD_ERR_FORM_SIZE: // 2: the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
                    // should be done by checkMaxFileSize
                    // NOP
                    break;
                case UPLOAD_ERR_PARTIAL: // 3: the uploaded file was only partially uploaded
                    $this->setErrors(_ER_UP_PHPERR_PARTIAL);
                    break;
                case UPLOAD_ERR_NO_FILE: // 4: no file was uploaded
                    $this->setErrors(_ER_UP_PHPERR_NO_FILE);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR: // 6: missing a temporary folder
                    $this->setErrors(_ER_UP_PHPERR_NO_TMP_DIR);
                    break;
                case UPLOAD_ERR_CANT_WRITE: // 7: failed to write file to disk
                    $this->setErrors(_ER_UP_PHPERR_CANT_WRITE);
                    break;
                case UPLOAD_ERR_EXTENSION: // 8: a PHP extension stopped the file upload
                    $this->setErrors(_ER_UP_PHPERR_EXTENSION);
                    break;
                case UPLOAD_ERR_OK: // 0: there is no error, the file uploaded with success
                default:
                    break;
            }
            return false;
        }
        // than checks by xoopsuploader
        if ((int)$this->mediaSize < 0) {
            $this->setErrors(_ER_UP_INVALIDFILESIZE);
            return false;
        }
        if ($this->mediaName == '') {
            $this->setErrors(_ER_UP_FILENAMEEMPTY);
            return false;
        }
        if ($this->mediaTmpName == 'none' || !is_uploaded_file($this->mediaTmpName)) {
            $this->setErrors(_ER_UP_NOFILEUPLOADED);
            return false;
        }
        return true;
    }

    /**
     * Copy the file to its destination
     *
     * @param               $chmod
     * @return bool
     */
    public function _copyFile($chmod) {
        // should be private, but is public because common\MediaUploader extends XoopsMediaUploader :-(
        // get file exstension
        $matched = [];
        if (!preg_match('/\.([a-zA-Z0-9]+)$/', $this->mediaName, $matched)) {
            $this->setErrors(_ER_UP_INVALIDFILENAME);
            return false;
        }
        $extension = $matched[1];
        
        \Xmf\Debug::dump($this->mediaName, $matched[0]);        
        \Xmf\Debug::dump($this->mediaName, $matched[1]);
        
        if (isset($this->targetFileName)) {
            $this->savedFileName = $this->targetFileName;
        } else if (isset($this->prefix)) {
            $this->savedFileName = uniqid($this->prefix, true) . '.' . strtolower($extension);
        } else if ($this->randomFilename) {
            $this->savedFileName = uniqid(time(), true) . '.' . strtolower($extension);
        } else {
            $this->savedFileName = strtolower($this->mediaName);
        }
        $this->savedFileName = iconv('UTF-8', 'ASCII//TRANSLIT', $this->savedFileName);
        $this->savedFileName = preg_replace('!\s+!', '_', $this->savedFileName);
        $this->savedFileName = preg_replace('/[^a-zA-Z0-9\._-]/', '', $this->savedFileName);
        //
        $this->savedDestination = $this->uploadDir . '/' . $this->savedFileName;
        if (!move_uploaded_file($this->mediaTmpName, $this->savedDestination)) {
            $this->setErrors(sprintf(_ER_UP_FAILEDSAVEFILE, $this->savedDestination));

            return false;
        }
        // Check IE XSS before returning success
        $ext = strtolower(substr(strrchr($this->savedDestination, '.'), 1));
        if (in_array($ext, $this->imageExtensions)) {
            $info = @getimagesize($this->savedDestination);
            if ($info === false || $this->imageExtensions[(int) $info[2]] != $ext) {
                $this->setErrors(_ER_UP_SUSPICIOUSREFUSED);
                @unlink($this->savedDestination);
                return false;
            }
        }
        @chmod($this->savedDestination, $chmod);

        return true;
    }

}
