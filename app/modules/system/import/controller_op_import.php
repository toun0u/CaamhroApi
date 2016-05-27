<?php

require_once(DIMS_APP_PATH . '/modules/system/import/global.php');

/**
 * Description of controller_op_import : Functionnement identique à un op normal
 * Les valeurs devront êtres récupérée via le dims load secure_value.
 *
 * @author Netlor
 * @copyright Netlor 2011
 */
class controller_op_import {

	public static function op_import($import_op, $params = array()){
		global $dims;
		echo view_import_factory::buildAccueil();
		echo "<div style=\"clear:both;margin-top:20px;\">";

		switch($import_op){
			case _OP_UPLOAD_FILE:
				$import= new import_contact();

				$import->loadImportFile();
				break;
			case _OP_NEW_IMPORT:
				echo view_import_factory::new_import();
				break;
			case _OP_SHOW_IMPORT:
					echo view_import_factory::display_list_import();
					break;
			case _OP_DELETE_IMPORT:
					$id_import = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

					$import = new import();
					$import->open($id_import);

					$import->delete();

					dims_redirect($dims->getScriptEnv().'?action=display');
					break;

			case 'home':
			default :
				echo view_import_factory::buildImportListFiles();
				break;
		}
		echo "</div>";
	}
}

?>
