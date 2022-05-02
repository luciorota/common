<?php

declare(strict_types=1);

namespace XoopsModules\Common;

use Xmf\{
    Request,
    Debug,
    Module\Helper,
    Module\Helper\Session,
};

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once \dirname(__DIR__) . '/include/common.php';

//xoops_load('Object', 'common');

class TestobjectHandler extends CommonObjectHandler {

    private const TABLE = 'mod_common_test';
    private const ENTITY = Testobject::class;
    private const ENTITYNAME = 'Testobject';
    private const KEYNAME = 'id';
    private const IDENTIFIER = 'name';

    public $table_link = '';
    /**
     * @var moduleHelper
     */
    public $moduleHelper;

    public function __construct(\XoopsDatabase $db = null, Helper $moduleHelper = null) {
        
        //$this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->db = $db;
        /** @var moduleHelper $this->helper */
        $this->moduleHelper = $moduleHelper ?? \Xmf\Module\Helper::getHelper('common');
        //
        parent::__construct($db, static::TABLE, static::ENTITY, static::KEYNAME, static::IDENTIFIER);
    }

}
