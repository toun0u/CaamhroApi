<?php
// chargement des paramètres
require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
$db = dims::getInstance()->getDb();
$sel = "SELECT 	* 
		FROM 	dims_mod_business_params 
		WHERE 	id_workspace = :idworkspace";
$params = array(
	':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
);
$res=$db->query($sel,$params);
$params = array();
while( $fields = $db->fetchrow($res)){
	$params[$fields['param']] = $fields['value'];
}
?>

<div>
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?= $this->getLightAttribute('id_popup'); ?>');">
			<img src="/common/modules/system/desktopV2/templates/gfx/common/close_news.png" />
		</a>
	</div>
	<h2><?php echo $_SESSION['cste']['_DIMS_ADD_MONITORING']; ?></h2>

	<?php
	$form = new Dims\form(array(
		'name' 			=> "new_suivi",
		'method'		=> "POST",
		'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=create",
		'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
		'back_name'		=> $_SESSION['cste']['_DIMS_CANCEL'],
		'back_url'		=> "javascript:void(0);\" onclick=\"javascript:dims_closeOverlayedPopup('".$this->getLightAttribute('id_popup')."');",
	));
	$form->add_hidden_field(array(
		'name'	=> "suivi_tiers_id",
		'value'	=> $this->getLightAttribute('id_tiers'),
	));
	$form->add_hidden_field(array(
		'name'	=> "suivi_contact_id",
		'value'	=> $this->getLightAttribute('id_contact'),
	));
	$form->add_hidden_field(array(
		'name'	=> "suivi_exercice",
		'value'	=> (isset($params['exercice']) ? $params['exercice'] : ""),
	));

	$form->add_simple_text(array(
		'label' 	=> $_SESSION['cste']['_DUTY'],
		'value'		=> (isset($params['exercice']) ? $params['exercice'] : ""),
	));

	$lstTypes = array(0=>"");
	$types = suivi_type::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'status'=>1)," ORDER BY label ");
	foreach($types as $t) {
		$lstTypes[$t->fields['id']] = $t->fields['label'];
	}
	$form->add_select_field(array(
		'name'		=> 'suivi_id_type',
		'label' 	=> $_SESSION['cste']['_TYPE'],
		'options'	=> $lstTypes,
		'db_field'	=> "id_type",
	));

	$form->add_text_field(array(
		'name'						=> 'suivi_datejour',
		'label' 					=> $_SESSION['cste']['_DIMS_DATE'],
		'value'						=> date('d/m/Y'),
		'revision'					=> "date_jj/mm/yyyy",
		'additionnal_attributes'	=> 'maxlength="10" size="20"',
	));

	$form->add_text_field(array(
		'name'		=> 'suivi_libelle',
		'label' 	=> $_SESSION['cste']['_AGENDA_LABEL_LABEL'],
		'value'		=> "",
		'mandatory'	=> true,
	));

	$tiers = tiers::find_by(array('id'=>$this->getLightAttribute('id_tiers')),null,1);
	$arrayTiers = array();
	if(!empty($tiers)){
		$arrayTiers[] = $tiers->get('id_globalobject');
	}
	$ct = contact::find_by(array('id'=>$this->getLightAttribute('id_contact')),null,1);
	$arrayCt = array();
	if(!empty($ct)){
		$arrayCt[] = $ct->get('id_globalobject');
	}
	require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
	$matrix = new search();
	$linkedObjectsIds = $matrix->exploreMatrice(
		array($_SESSION['dims']['workspaceid']),
		null,
		null,
		null,
		$arrayTiers,
		$arrayCt
	);
	$desktop = new desktopv2();
	$lstObj = $desktop->getLinkedObjects($linkedObjectsIds);
	$lstDossiers = array(
		0 => "",
	);
	if(!empty($lstObj['dossiers'])){
		foreach ($lstObj['dossiers'] as $dossier) {
			$lstDossiers[$dossier->get('id_globalobject')] = $dossier->get('label');
		}
	}
	$form->add_select_field(array(
		'name'		=> 'suivi_dossier_id',
		'label' 	=> $_SESSION['cste']['_DOC_FOLDER'],
		'options'	=> $lstDossiers,
	));

	$form->add_textarea_field(array(
		'name'		=> 'suivi_description',
		'label' 	=> $_SESSION['cste']['_DIMS_COMMENTS'],
		'value'		=> "",
	));
	$form->build();
	?>
</div>
