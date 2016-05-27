<?php

// Familles de même niveau et même parent
$brothers = array();
foreach($cata_famille->getbrothers() as $brother_id) {
	if (isset($_SESSION['catalogue']['familys']['list'][$brother_id])) {
		$brother = new cata_famille();
		$brother->open($brother_id);
		$brothers[] = array(
			'id' 		=> $brother->get('id'),
			'label' 	=> $brother->get('label'),
			'url' 		=> $brother->getRewritedLink(),
			'selected' 	=> $brother->get('id') == $cata_famille->get('id')
			);
	}
}

// Champs libres activés sur la famille courante
$champs_libres = $cata_famille->getChampsLibre();

$fields = array();
foreach ($champs_libres as $champ_libre) {
	$fields[$champ_libre->fields['id']] = array(
		'id' 		=> $champ_libre->fields['id'],
		'label' 	=> $champ_libre->fields['libelle'],
		'type'	 	=> $champ_libre->fields['type'],
		'values' 	=> array()
		);
}

// Familles enfant dont on va afficher le contenu des champs libres
$children = $cata_famille->getDirectChilds();
$families = array();
foreach ($children as $child) {
	// 1e photo de la famille
	$photo = $child->getThumbnails(1);
	$photo_path = (empty($photo)) ? '' : '/'.$photo[0]->getDocfile()->getwebpath();

	// infos des champs de la famille
	$families[] = array(
		'id' 			=> $child->fields['id'],
		'label' 		=> $child->fields['label'],
		'photo' 		=> $photo_path,
		'url' 			=> $child->getRewritedLink()
		);
	foreach ($fields as $id_field => $field) {
		switch ($field['type']) {
			case cata_champ::TYPE_TEXT:
				$fields[$id_field]['values'][$child->fields['id']] = str_replace('|', '<br>', $child->fields['fields'.$id_field]);
				break;
			case cata_champ::TYPE_LIST:
				$champ_valeur = new cata_champ_valeur();
				$champ_valeur->open($child->fields['fields'.$id_field]);
				$fields[$id_field]['values'][$child->fields['id']] = $champ_valeur->get('valeur');
				break;
		}
	}
}

require_once(DIMS_APP_PATH . "/modules/wce/include/classes/class_wce_site.php");
$wcesite = new wce_site($db);
$extraparams = '';
$webasklink = $wcesite->getArticleByObject('gescom', 'Gescom - demande de devis', $extraparams);
$webasklink.= '/index.php?t='.cata_famille::MY_GLOBALOBJECT_CODE;

if(!empty($extraparams)) {
	$webasklink .= $extraparams;
}

$smarty->assign('webasklink', $webasklink);
$smarty->assign('current_family', $cata_famille->fields);
$smarty->assign('brothers', $brothers);
$smarty->assign('families', $families);
$smarty->assign('fields', $fields);
