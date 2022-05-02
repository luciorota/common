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
define('_CO_COMMON_ACTIONS', 'Actions');
define('_CO_COMMON_ACTION_ENABLED', 'Enabled');
define('_CO_COMMON_ACTION_DISABLED', 'Disabled');
define('_CO_COMMON_ACTION_TOGGLED', 'Toggled');
define('_CO_COMMON_GROUP', 'Group');
define('_CO_COMMON_GROUPS', 'Groups');

// warnings
define('_CO_COMMON_WARNING_NOUPLOADDIR', 'Warning: the module upload directory does not exist');
define('_CO_COMMON_WARNING_NOTTOGGLED', 'Warning: not toggled');

// errors
define('_CO_COMMON_ERROR_NOTTOGGLED', 'Error: not toggled');
define('_CO_COMMON_ERROR_QUERY_DATABASE', 'Error: query/database error');

// filter
define('_CO_COMMON_FILTER_SEARCH', 'Search');
define('_CO_COMMON_FILTER_FILTER', 'Filter');
define('_CO_COMMON_FILTER_CONDITION', 'Condition');

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
define('_CO_COMMON_BUTTON_LIST', 'List');
define('_CO_COMMON_BUTTON_MOVE', 'Move');
define('_CO_COMMON_BUTTON_EDIT', 'Edit');
define('_CO_COMMON_BUTTON_ADD', 'Add');
define('_CO_COMMON_BUTTON_VIEW', 'View');
define('_CO_COMMON_BUTTON_EXECUTE', 'Run');
define('_CO_COMMON_BUTTON_DELETE', 'Delete');
define('_CO_COMMON_BUTTON_FILTER', 'Filter');
define('_CO_COMMON_BUTTON_SEARCH', 'Search');
define('_CO_COMMON_BUTTON_REORDER', 'Reorder');
define('_CO_COMMON_BUTTON_APPROVE', 'Approve');
define('_CO_COMMON_BUTTON_IGNORE', 'Ignore');
define('_CO_COMMON_BUTTON_CANCEL', 'Cancel');
define('_CO_COMMON_BUTTON_SAVE', 'Save');
define('_CO_COMMON_BUTTON_RESET', 'Reset');
define('_CO_COMMON_BUTTON_UPLOAD', 'Upload');
define('_CO_COMMON_BUTTON_PRINT', 'Print');
define('_CO_COMMON_BUTTON_EXPORT', 'Export');
define('_CO_COMMON_BUTTON_NEXT', 'Next');
define('_CO_COMMON_BUTTON_PREV', 'Previous');
define('_CO_COMMON_BUTTON_CHECK', 'Check');
define('_CO_COMMON_BUTTON_GET', 'Get');
define('_CO_COMMON_BUTTON_SET', 'Set');

define('_CO_COMMON_ISCRITTOS_QUICK_SEARCH', 'Quick search in the whole database');
define('_CO_COMMON_ISCRITTOS_QUICK_SEARCH_DESC', "search in the fields 'Name', 'Surname', 'Tax code', 'Card number'");

define('_CO_COMMON_SEGRETERIAS_QUICK_SEARCH', 'Quick Search');
define('_CO_COMMON_SEGRETERIAS_QUICK_SEARCH_DESC', "search in the fields' Name");



// database
define('_CO_COMMON_DATABASE_RECORD', 'Record');
define('_CO_COMMON_DATABASE_RECORDS', 'Record');
define('_CO_COMMON_DATABASE_RECORDS_ACCESSIBLE', 'Accessible records');
define('_CO_COMMON_DATABASE_RECORDS_FILTERED', 'Filtered records');
define('_CO_COMMON_DATABASE_RECORDS_SHOWN', 'Records displayed');
define('_CO_COMMON_DATABASE_QUERY', 'Query');
define('_CO_COMMON_DATABASE_SELECT', 'Select (SELECT)');
define('_CO_COMMON_DATABASE_WHERE', 'Where (WHERE)');

define('_CO_COMMON_DATABASE_FIELD', 'Field');
define('_CO_COMMON_DATABASE_FIELDS', 'Fields');
define('_CO_COMMON_DATABASE_VALUE', 'Value');
define('_CO_COMMON_DATABASE_VALUES', 'Values');


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
