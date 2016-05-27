<?php

$field_types = array(	'text' => $_DIMS['cste']['_DIMS_LABEL_SIMPLE_TEXT'],
			'textarea' => $_DIMS['cste']['_DIMS_LABEL_ADVANCED_TEXT'],
			'checkbox' => $_DIMS['cste']['_DIMS_LABEL_CHECKBOX'],
			'radio' => $_DIMS['cste']['_DIMS_LABEL_RADIO_BUTTON'],
			'select' => $_DIMS['cste']['_DIMS_LABEL_LIST'],
			'tablelink' => $_DIMS['cste']['_DIMS_LABEL_FORM_LINK'],
			'file' => $_DIMS['cste']['_DIMS_LABEL_FILE'],
			'autoincrement' => $_DIMS['cste']['_DIMS_LABEL_AUTO_NUMBER'],
			'color' => $_DIMS['cste']['_DIMS_LABEL_COLOR']
		    );

// gestion des champs generiques
if (isset($_SESSION['dims']['importform']['firstdataline'])){
    //on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
    $sql =  "
		SELECT      mf.*,mc.label as categlabel, mc.id as id_cat,
			    mb.protected,mb.name as namefield,mb.label as titlefield
		FROM        dims_mod_business_meta_field as mf
		INNER JOIN dims_mb_field as mb
		ON   mb.id=mf.id_mbfield
		RIGHT JOIN  dims_mod_business_meta_categ as mc
		ON          mf.id_metacateg=mc.id
		WHERE         mf.id_object = :idobject
		AND   mf.used=1
		ORDER BY    mc.position, mf.position
		";
    $rs_fields=$db->query($sql, array(
    	':idobject' => $_SESSION['dims']['importform']['object_id']
    ));

    $rubgen=array();
    $convmeta = array();

    while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
	    $rubgen[$fields['id_cat']]=array();
	    $rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
	    $rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
	    if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	$fields['use']=0;// par defaut non utilise
	$fields['enabled']=array();
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

	$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

	// enregistrement de la conversion
	$convmeta[$fields['namefield']]=$fields['id'];
    }
}

?>