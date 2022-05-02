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

xoops_loadLanguage('formemail', $GLOBALS['xoopsModule']->getVar('dirname'));
xoops_load('XoopsFormLoader');

class PageNav extends \XoopsPageNav
{
    private $_dirname;
    private $_moduleHelper;
    private $_tplSource;

    /**
     * Constructor
     *
     * @param int    $total_items   Total number of items
     * @param int    $items_perpage Number of items per page
     * @param int    $current_start First item on the current page
     * @param string $start_name    Name for "start" or "offset"
     * @param string $extra_arg     Additional arguments to pass in the URL
     */
    public function __construct($total_items, $items_perpage, $current_start, $start_name = 'start', $extra_arg = '') {
        $this->_dirname = basename(dirname(__DIR__));
        $this->_moduleHelper = \Xmf\Module\Helper::getHelper($this->_dirname);
        parent::__construct($total_items, $items_perpage, $current_start, $start_name, $extra_arg);
    }

    /**
     * Create themed text navigation
     *
     * @param  integer $offset
     * @param string $tplSource
     * @return string
     */
    public function renderThemedNav($offset = 4, $tplSource = '')
    {
        $this->_tplSource = $tplSource;
        //
        if ($this->total <= $this->perpage) {
            return '';
        }
        if (($this->total == 0) || ($this->perpage == 0)) {
            return '';
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ($total_pages <= 1) {
            return '';
        }
        //
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            include_once $GLOBALS['xoops']->path('/class/theme.php');
            $GLOBALS['xoTheme'] = new \xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('/class/template.php');
        $pagenavTpl = new \XoopsTpl();
        //
        $pagenav = array(
            'total' => $this->total,
            'perpage' => $this->perpage,
            'current' => $this->current,
            'url' => $this->url,
            'extra' => $this->extra
        );
        $pagenavTpl->assign('pagenav', $pagenav);
        //
        $pagenavTpl->assign('offset', $offset);
        //
        $pagenavTpl->assign('total_pages', $total_pages);
        //
        $prev = $this->current - $this->perpage;
        $pagenavTpl->assign('prev', $prev);
        //
        $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
        $pagenavTpl->assign('current_page', $current_page);
        //
        $next = $this->current + $this->perpage;
        $pagenavTpl->assign('next', $next);
        //
        if ($this->_tplSource != '') {
            $ret = $pagenavTpl->fetchFromData($this->_tplSource, false, null);
        } else {
            $ret = $pagenavTpl->fetch("db:{$this->_dirname}_co_themedpagenav.tpl");
        }
        unset($pagenavTpl);
        return $ret;
    }
}
