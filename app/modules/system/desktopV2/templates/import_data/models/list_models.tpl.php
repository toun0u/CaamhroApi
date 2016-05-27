<div style="float:left;clear:both;width:99%;margin:2px auto;">
<?php
	global $skin;
	// construction du tableau de présentation des champs
	$data =array();
	$elements=array();

	// collecte des données de la base
	$fields=import_fichier_modele::getFichiersModeles();// getModelesFilesAssureurs();

	// headers

	$data['headers'][]=$_SESSION['cste']['_LABEL_MODEL'];
	$data['headers'][]=$_SESSION['cste']['_DIMS_ACTIONS'];

	// construction des données à afficher
	foreach ($fields as $f) {
		$elem=array();

		foreach ($f as $id_modele => $dobject) {
			$globalob= $dobject->getGlobalobjectConcerned();

			$elem[0]=$globalob->fields['title'];
			$elem[1]=$dobject->fields['libelle'];
			$elem[2]='<a href="/admin.php?op_model=displayModelFieldsCorrespRh&id_globalobject='.$globalob->fields['id'].'&id_modele_fichier='.$id_modele.'"><img src="./common/img/go-next.png"></a>';
		}

		//nbelements
		$elements[]=$elem;
	}

	//elements of table
	$data['data']['elements']=$elements;
	echo $skin->displayArray($data);
?>
</div>