<?php

declare(strict_types=1);

namespace XoopsModules\Passds;

use Xmf\{
    Request,
    Debug,
    Module\Helper,
    Module\Helper\Session
};

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once \dirname(__DIR__) . '/include/common.php';

xoops_load('Object', 'common');

/**
 * Class Ingrediente
 */
class Ingrediente extends CommonObject {

    /**
     * @var moduleHelper
     */
    public $moduleHelper;

    /** @var \XoopsMySQLDatabase */
    public $db;

    /**
     * @param null|int $id
     */
    public function __construct($id = null) {
        /** @var Helper $this->helper */
        $this->moduleHelper = \Xmf\Module\Helper::getHelper('passds');
        /** @var \XoopsMySQLDatabase $db */
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        //
        parent::__construct($id);
        //
        $this->initVar('nome', XOBJ_DTYPE_TXTBOX, null, false, 255); // Identificazione
        $this->initVar('nomeiupac', XOBJ_DTYPE_TXTBOX, null, false, 255); // nome IUPAC
        $this->initVar('numerocas', XOBJ_DTYPE_TXTBOX, null, false, 255); // numero CAS
        $this->initVar('conc', XOBJ_DTYPE_TXTBOX, null, false, 255); // x = Conc. %
        $this->initVar('descrizione', XOBJ_DTYPE_TXTAREA, '', false, null);
        $this->initVar('scheda_id', XOBJ_DTYPE_INT, null, false); // INT(10) UNSIGNED NOT NULL
        // link_ingrediente_fraseh
    }

    /**
     * This method return values as array ready for html output
     *
     * @return  array
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1) {
        \xoops_load('XoopsUserUtility');
        //
        $values = parent::getValues($keys, $format, $maxDepth);
        //
        //
        //
        // ingrediente: descrizione_textarea
        $values['descrizione_textarea'] = $this->getVar('descrizione', 'e');
        // ingrediente: frasehs, link_ingrediente_fraseh
        $frasehHandler = new FrasehHandler();
        $link_ingrediente_frasehHandler = new Link_ingrediente_frasehHandler();
        //
        $frasehs = $frasehHandler->getObjects(null, true, false); // id as key, as array
        $link_ingrediente_frasehCriteria = new \CriteriaCompo();
        $link_ingrediente_frasehCriteria->add(new \Criteria('ingrediente_id', $this->getVar('id')));
        $link_ingrediente_frasehs = $link_ingrediente_frasehHandler->getObjects($link_ingrediente_frasehCriteria, true, false); // id as key, as array
        $fraseh_ids = [];
        foreach ($link_ingrediente_frasehs as $link_ingrediente_fraseh) {
            $fraseh_ids[] = $link_ingrediente_fraseh['fraseh_id'];
        }
        $values['fraseh_ids'] = $fraseh_ids;
        //
        //
        //
        return $values;
    }

    /**
     * This method get form values ready for database insert
     *
     * @return  bool
     */
    public function setValues($default = array(), $hash = 'default') {
        parent::setValues($default, $hash);
        // nome
        $nome = trim(Request::getString('nome', '', $hash));
        $this->setVar('nome', $nome);
        // nomeiupac
        $nomeiupac = Request::getText('nomeiupac', '', $hash);
        $this->setVar('nomeiupac', $nomeiupac);
        // numerocas
        $numerocas = Request::getText('numerocas', '', $hash);
        $this->setVar('numerocas', $numerocas);
        // conc
        $this->setVar('conc', Request::getText('conc', '', $hash));
        // descrizione
        $this->setVar('descrizione', Request::getText('descrizione', '', $hash));
        // scheda_id
        $scheda_id = Request::getInt('scheda_id', 0, $hash);
        if (empty($scheda_id)) {
            return false;
        }
        $this->setVar('scheda_id', $scheda_id);
        //
        // load sostanza handlers
        $sostanzaHandler = new SostanzaHandler();//  $passdsHelper->getHandler('ingrediente');
        $fields = [
            'nome' => $nome,
            'nomeiupac' => $nomeiupac,
            'numerocas' => $numerocas,
        ];
        $sostanzaHandler->checkSostanza($fields);
        
        return true;
    }
    
    /**
     * Get {@link XoopsThemeForm}
     *
     * @param array         $default
     * @param string        $hash
     * @return bool       {@link XoopsThemeForm}
     */
    public function setLinksFraseh($default = [], $hash = 'default') {
        // link_ingrediente_fraseh
        $link_ingrediente_frasehHandler = New Link_ingrediente_frasehHandler();
        //
        $fraseh_ids = [];
        for ($i = 0; $i <= 3; $i++) {
            $temp_fraseh_ids = Request::getArray("fraseh_ids_{$i}", [], $hash);
            $fraseh_ids = array_merge($fraseh_ids, $temp_fraseh_ids);
        }
        $link_ingrediente_frasehCriteria = new \CriteriaCompo();
        $link_ingrediente_frasehCriteria->add(new \Criteria('ingrediente_id', $this->getVar('id')));
        if (!$link_ingrediente_frasehHandler->deleteAll($link_ingrediente_frasehCriteria, true, true)) { // force, as object
            //return false;
        }
        foreach ($fraseh_ids as $fraseh_id) {
            $link_ingrediente_frasehObj = $link_ingrediente_frasehHandler->create();
            $link_ingrediente_frasehObj->setVar('ingrediente_id', $this->getVar('id'));
            $link_ingrediente_frasehObj->setVar('fraseh_id', $fraseh_id);
            if (!$link_ingrediente_frasehHandler->insert($link_ingrediente_frasehObj)) {
                return false;
            };
        }
        return true;
    }

    /**
     * @return Form\IngredienteForm
     */
    public function getForm($action = false, $scheda_id) {
        $formObj = new Form\IngredienteForm($this, $action = false, $scheda_id);

        return $formObj;
    }

}
