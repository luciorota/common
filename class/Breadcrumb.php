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
use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

/**
 * Class breadcrumb
 *
 * Example:
 * $breadcrumb = new common\breadcrumb();
 * $breadcrumb->addLink( 'bread 1', 'index1.php' );
 * $breadcrumb->addLink( 'bread 2', '' );
 * $breadcrumb->addLink( 'bread 3', 'index3.php' );
 * echo $breadcrumb->render();
 */
class Breadcrumb
{
    private $_dirname;
    private $_moduleHelper;
    private $_breads = array();
    /**
     *
     */
    public function __construct()
    {
        $this->_dirname = basename(dirname(__DIR__));
        $this->_moduleHelper = \Xmf\Module\Helper::getHelper($this->_dirname);
    }

    /**
     * Add link to breadcrumb
     *
     * @param string $title
     * @param string $link
     */
    public function addLink($title='', $link='')
    {
        $this->_breads[] = array(
            'link'  => $link,
            'title' => $title
            );
    }

    /**
     * Render
     *
     */
    public function render()
    {
        $ret = '';
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            include_once $GLOBALS['xoops']->path('/class/theme.php');
            $GLOBALS['xoTheme'] = new \xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('/class/template.php');
        $breadcrumbTpl = new \XoopsTpl();
        $breadcrumbTpl->assign('breadcrumb', $this->_breads);
        $ret .= $breadcrumbTpl->fetch("db:{$this->_dirname}_co_breadcrumb.tpl");
        return $ret;
    }
}
