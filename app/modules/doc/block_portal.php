<?
dims_init_module('doc');

$workspaces = dims_viewworkspaces(dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true));

switch($dims_op) {
	case 'title':
		$moduleid=dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		switch($idobject) {
			case _DOC_OBJECT_FILE:
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
				$obj=new docfile();
				$obj->open($idrecord);
				$label=$obj->fields['name'];
				break;
			case _DOC_OBJECT_FOLDER:
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
				$obj=new docfolder();
				$obj->open($idrecord);
				$label=$obj->fields['name'];
				break;
		}
		break;
	case 'content':
		// on affiche les propriétés par défaut de l'objet
		$moduleid=dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if($idobject==_DOC_OBJECT_FILE) {
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
			$obj=new docfile();
			$obj->open($idrecord);
			$label=$obj->fields['name'];
		}
		else {
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
			$obj=new docfolder();
			$obj->open($idrecord);
			$label=$obj->fields['name'];
		}

		echo dims_getContent($moduleid,$idobject,$idrecord,$obj,$label);

		break;
	case 'searchnews':
	default:
		if (isset($_GET['moduleid'])) $moduleid=dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		require_once(DIMS_APP_PATH . '/modules/doc/block_portal_search.php');
		break;
}

?>
