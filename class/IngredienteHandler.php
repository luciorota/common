<?php

declare(strict_types=1);

namespace XoopsModules\Passds;

use Xmf\{
    Debug,
    Module\Helper,
    Module\Helper\Session,
};

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

//xoops_load('Object', 'common');

class IngredienteHandler extends CommonObjectHandler {

    private const TABLE = 'mod_passds_ingrediente';
    private const ENTITY = Ingrediente::class;
    private const ENTITYNAME = 'Ingrediente';
    private const KEYNAME = 'id';
    private const IDENTIFIER = 'nome';

    public $table_link = '';
    /**
     * @var moduleHelper
     */
    public $moduleHelper;

    public function __construct(\XoopsDatabase $db = null, Helper $moduleHelper = null) {
        
        //$this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->db = $db;
        /** @var moduleHelper $this->helper */
        $this->moduleHelper = $moduleHelper ?? \Xmf\Module\Helper::getHelper('passds');
        //
        parent::__construct($db, static::TABLE, static::ENTITY, static::KEYNAME, static::IDENTIFIER);
    }
    
    public function delete(\XoopsObject $ingredienteObj, $force = false) {
        //
        // delete fraseh links
        $link_ingrediente_frasehHandler = new Link_ingrediente_frasehHandler();
        $link_ingrediente_frasehCriteria = new \Criteria('ingrediente_id', $ingredienteObj->getVar('id'));
        $link_ingrediente_frasehObjs = $link_ingrediente_frasehHandler->getObjects($link_ingrediente_frasehCriteria, true, true);
        foreach ($link_ingrediente_frasehObjs as $link_ingrediente_frasehObj) {
            $link_ingrediente_frasehHandler->delete($link_ingrediente_frasehObj);
        }
//TODO perchè non funziona
        //$link_ingrediente_frasehHandler->deleteAll($link_ingrediente_frasehCriteria, true, true); // delete as object
        // delete object
        return parent::delete($ingredienteObj, $force);
    }
    
}
