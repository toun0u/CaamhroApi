<?php

/**
 * Description of global
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
require_once 'class_dims_import.php';
require_once 'class_import_champs_fichier_modele.php';
require_once 'class_import_correspondance_colonne_champs.php';
require_once 'class_import_fichier_modele.php';
require_once 'class_import_linked.php';
require_once 'class_import_type_champs_fichier_modele.php';

/* Liste des statuts d'import */
define('_IMPORT_STATUT_NO_FILE_IMPORT', 0);
define('_IMPORT_STATUT_MODEL_NOT_CORRECT', 99);
define('_IMPORT_STATUT_FILE_NOT_CORRECT', 1);
define('_IMPORT_STATUT_FILE_IMPORTED', 2);
define('_IMPORT_STATUT_IMPORT_IN_PROGRESS', 3);
define('_IMPORT_STATUT_DATE_IMPORTED', 4);

define ('_OP_DEFAULT_IMPORT', '25');
define ('_OP_MODULE_IMPORT', '10');
define ('_OP_SHOW_IMPORT',1);
define ('_OP_NEW_IMPORT',2);
define ('_OP_UPLOAD_FILE',3);
define ('_OP_DELETE_IMPORT',4);
define ('_OP_LOAD_IMPORT',5);
define ('_OP_RESOLVE_ERROR_IMPORT',6);
define ('_OP_HOME_IMPORT',7);
define('_OP_SAVE_PREFERENCE',8);
define('_OP_SAVE_HISTORY',9);
define('_OP_MERGE_IMPORT',11);
define('_OP_MERGE_IMPORT_SAVE',12);

define('_STATUS_IMPORT_NO_TRAITED',		0);
define('_STATUS_IMPORT_ERR_CT',			1);
define('_STATUS_IMPORT_ERR_TIERS',		2);
define('_STATUS_IMPORT_ERR_CT_TIERS',	3);
define('_STATUS_IMPORT_OK_CT',			4);
define('_STATUS_IMPORT_OK_TIERS',		5);
define('_STATUS_IMPORT_OK',				6);
?>
