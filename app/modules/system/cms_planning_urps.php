<?
// Récupération de la liste des events (planning)
require_once(DIMS_APP_PATH.'modules/system/class_action.php');

$publi = (floor(dims_createtimestamp()/1000000)+1)*1000000;

$sel = "SELECT		id
		FROM		dims_mod_business_action
		WHERE		type = :type
		AND			timestp_open <= :publi
		ORDER BY	datejour DESC";

$list_events = array();
$res = $db->query($sel, array(
	':type' 	=> dims_const::_PLANNING_ACTION_EVT,
	':publi' 	=> $publi
));

while ($id = $db->fetchrow($res)){
	$event = new action();
	$event->open($id['id']);

	$eve = array();
	$eve['id'] = $id['id'];
	$eve['type'] = $event->fields['typeaction'];

	$eve['label'] = $event->fields['libelle'];

	$eve['date_deb'] = array();
	$date_deb = explode('-',$event->fields['datejour']);
	$eve['date_deb']['jour'] = $date_deb[2];
	$eve['date_deb']['mois'] = $date_deb[1];
	$eve['date_deb']['annee'] = $date_deb[0];
	$date_deb = explode(':',$event->fields['heuredeb']);
	$eve['date_deb']['heure'] = $date_deb[0];
	$eve['date_deb']['min'] = $date_deb[1];

	$eve['date_fin'] = array();
	$date_fin = explode('-',$event->fields['datefin']);
	$eve['date_fin']['jour'] = $date_fin[2];
	$eve['date_fin']['mois'] = $date_fin[1];
	$eve['date_fin']['annee'] = $date_fin[0];
	$date_fin = explode(':',$event->fields['heurefin']);
	$eve['date_fin']['heure'] = $date_fin[0];
	$eve['date_fin']['min'] = $date_fin[1];

	$eve['lieu'] = $event->fields['lieu'];

	$eve['teaser'] = $event->fields['teaser'];

	$eve['description'] = $event->fields['description'];

	$eve['header'] = $event->fields['banner_path'];

	$eve['docs'] = array();
	$id_module = 1;
	$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
	$id_record = $event->fields['id'];
	$sql = 'SELECT		id
			FROM		dims_mod_doc_file
			WHERE		id_module= :idmodule
			AND			id_object= :idobject
			AND			id_record= :idrecord ';
	$res_doc=$db->query($sql, array(
		':idmodule' 	=> $id_module ,
		':idobject' 	=> $id_object ,
		':idrecord' 	=> $id_record
	));
	if ($db->numrows($res_doc)>0){
		require_once(DIMS_APP_PATH.'modules/doc/class_docfile.php');
		$doc = new docfile();
		while($f=$db->fetchrow($res_doc)) {
			$doc->open($f['id']);
			if (file_exists($doc->getfilepath())) {
				$do = array();
				$do['nom'] = $doc->fields['name'];
				$do['path'] = $dims->getProtocol().$dims->getHttpHost().'/'.$doc->getwebpath();
				$do['type'] = $doc->fields['extension'];
				$eve['docs'][] = $do;
			}
		}
	}

	//$eve['relation'] = $event->fields['description'];
	$list_events[] = $eve;
}

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
	$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

// Initialisation de l'objet Smarty
$smartypath=$_SESSION['dims']['smarty_path'];
$smartyobject = new Smarty();
$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = "./common/modules/system/templates/planning/liste_publique";
$smartyobject->debugging = 0;
if (!file_exists($smartypath.'/templates_c/liste_publique/index.tpl')) {
	dims_makedir ($smartypath."/templates_c/liste_publique/", 0777, true);
}

$smartyobject->compile_dir = $smartypath."/templates_c/liste_publique/";
$smartyobject->assign('liste_reunions',$list_events);
$smartyobject->display('index.tpl');
?>
