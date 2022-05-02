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
 * @author          luciorota, studiopas
 * @version         svn:$Id$
 */

// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: luciorota

// GENERIC
define('_CO_COMMON_ACTIONS', 'Azioni');
define('_CO_COMMON_ACTION_ENABLED', 'Abilitato');
define('_CO_COMMON_ACTION_DISABLED', 'Disabilitato');
define('_CO_COMMON_ACTION_TOGGLED', 'Toggled');
define('_CO_COMMON_GROUP', 'Gruppo');
define('_CO_COMMON_GROUPS', 'Gruppi');

// warnings
define('_CO_COMMON_WARNING_NOUPLOADDIR', 'Warning: the module upload directory does not exist');
define('_CO_COMMON_WARNING_NOTTOGGLED', 'Warning: not toggled');

// errors
define('_CO_COMMON_ERROR_NOTTOGGLED', 'Error: not toggled');
define('_CO_COMMON_ERROR_QUERY_DATABASE', 'Error: query/database error');

// filter
define('_CO_COMMON_FILTER_SEARCH', 'Cerca');
define('_CO_COMMON_FILTER_FILTER', 'Filtro');
define('_CO_COMMON_FILTER_CONDITION', 'Condizione');

// form options
define('_CO_COMMON_TEXTOPTIONS', 'Text options');
define('_CO_COMMON_TEXTOPTIONS_DESC', '');
define('_CO_COMMON_ALLOWHTML', ' Allow HTML tags');
define('_CO_COMMON_ALLOWSMILEY', ' Allow Smiley icons');
define('_CO_COMMON_ALLOWXCODE', ' Allow XOOPS codes');
define('_CO_COMMON_ALLOWIMAGES', ' Allow images');
define('_CO_COMMON_ALLOWBREAK', ' Use XOOPS line break conversion');

define('_CO_COMMON_SEL_MULTIPLE','consentita selezione multipla<br>nessuna selezione = TUTTI');
define('_CO_COMMON_ND', 'n.d.');
define('_CO_COMMON_ALL', 'Tutti');

//  buttons
define('_CO_COMMON_BUTTON_LIST', 'Lista');
define('_CO_COMMON_BUTTON_MOVE', 'Muovi');
define('_CO_COMMON_BUTTON_EDIT', 'Modifica');
define('_CO_COMMON_BUTTON_ADD', 'Aggiungi');
define('_CO_COMMON_BUTTON_VIEW', 'Visualizza');
define('_CO_COMMON_BUTTON_EXECUTE', 'Esegui');
define('_CO_COMMON_BUTTON_DELETE', 'Elimina');
define('_CO_COMMON_BUTTON_FILTER', 'Filtra');
define('_CO_COMMON_BUTTON_SEARCH', 'Cerca');
define('_CO_COMMON_BUTTON_REORDER', 'Riordina');
define('_CO_COMMON_BUTTON_APPROVE', 'Approva');
define('_CO_COMMON_BUTTON_IGNORE', 'Ignora');
define('_CO_COMMON_BUTTON_CANCEL', 'Annulla');
define('_CO_COMMON_BUTTON_SAVE', 'Salva');
define('_CO_COMMON_BUTTON_RESET', 'Reset');
define('_CO_COMMON_BUTTON_UPLOAD', 'Carica');
define('_CO_COMMON_BUTTON_PRINT', 'Stampa');
define('_CO_COMMON_BUTTON_EXPORT', 'Esporta');
define('_CO_COMMON_BUTTON_NEXT', 'Prossimo');
define('_CO_COMMON_BUTTON_PREV', 'Precedente');
define('_CO_COMMON_BUTTON_CHECK', 'Controlla');
define('_CO_COMMON_BUTTON_GET', 'Ottieni');
define('_CO_COMMON_BUTTON_SET', 'Imposta');

define('_CO_COMMON_ISCRITTOS_QUICK_SEARCH', 'Ricerca rapida in tutto il database');
define('_CO_COMMON_ISCRITTOS_QUICK_SEARCH_DESC', "ricerca nei campi 'Nome', 'Cognome', 'Codice fiscale', 'Numero tessera'");

define('_CO_COMMON_SEGRETERIAS_QUICK_SEARCH', 'Ricerca rapida');
define('_CO_COMMON_SEGRETERIAS_QUICK_SEARCH_DESC', "ricerca nei campi 'Nome");



// database
define('_CO_COMMON_DATABASE_RECORD', 'Record');
define('_CO_COMMON_DATABASE_RECORDS', 'Record');
define('_CO_COMMON_DATABASE_RECORDS_ACCESSIBLE', 'Record accessibili');
define('_CO_COMMON_DATABASE_RECORDS_FILTERED', 'Record filtrati');
define('_CO_COMMON_DATABASE_RECORDS_SHOWN', 'Record visualizzati');
define('_CO_COMMON_DATABASE_QUERY', 'Query');
define('_CO_COMMON_DATABASE_SELECT', 'Seleziona (SELECT)');
define('_CO_COMMON_DATABASE_WHERE', 'Dove (WHERE)');

define('_CO_COMMON_DATABASE_FIELD', 'Campo');
define('_CO_COMMON_DATABASE_FIELDS', 'Campi');
define('_CO_COMMON_DATABASE_VALUE', 'Valore');
define('_CO_COMMON_DATABASE_VALUES', 'Valori');



/*
 *  INFO
 */
define('CO_COMMON_GDLIBSTATUS', 'GD library support: ');
define('CO_COMMON_GDLIBVERSION', 'GD Library version: ');
define('CO_COMMON_GDOFF', "<span style='font-weight: bold;'>Disabled</span> (No thumbnails available)");
define('CO_COMMON_GDON', "<span style='font-weight: bold;'>Enabled</span> (Thumbsnails available)");
define('CO_COMMON_IMAGEINFO', 'Server status');
define('CO_COMMON_MAXPOSTSIZE', 'Max post size permitted (post_max_size directive in php.ini): ');
define('CO_COMMON_MAXUPLOADSIZE', 'Max upload size permitted (upload_max_filesize directive in php.ini): ');
define('CO_COMMON_MEMORYLIMIT', 'Memory limit (memory_limit directive in php.ini): ');
define('CO_COMMON_METAVERSION', "<span style='font-weight: bold;'>Downloads meta version:</span> ");
define('CO_COMMON_OFF', "<span style='font-weight: bold;'>OFF</span>");
define('CO_COMMON_ON', "<span style='font-weight: bold;'>ON</span>");
define('CO_COMMON_SERVERPATH', 'Server path to XOOPS root: ');
define('CO_COMMON_SERVERUPLOADSTATUS', 'Server uploads status: ');
define('CO_COMMON_SPHPINI', "<span style='font-weight: bold;'>Information taken from PHP ini file:</span>");
