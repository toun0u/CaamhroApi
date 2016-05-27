<?php
//dims_print_r($_SESSION['dims']);
//header('Content-type: text/html; charset=UTF-8');
require_once(DIMS_APP_PATH . "/modules/system/class_ct_link.php");
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_import.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_import_ent_similar.php');

require_once(DIMS_APP_PATH . '/modules/system/import/global.php');

$import_op = dims_load_securvalue('import_op', dims_const::_DIMS_NUM_INPUT, true, true);
// appel du nouvel envoi

controller_op_import::op_import($import_op);

?>
