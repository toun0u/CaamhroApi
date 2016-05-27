<?php
require_once(DIMS_APP_PATH . "/modules/system/class_metafield.php");
require_once(DIMS_APP_PATH . "/modules/system/class_metafielduse.php");

?>
<script type="text/javascript">
	<?php include_once(DIMS_APP_PATH . "/modules/system/include/javascript.php"); ?>
</script>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
if (true) {
	// creation des champs dans la table dims_mod_business_
	$sql="ALTER TABLE `dims_mod_business_contact`";

	for ($i=1;$i<=100;$i++) {
	   if($i>1) $sql.=",";
	   $sql.=" DROP `field$i`";
	}

	//echo $sql."<br>";
	$sql="ALTER TABLE `dims_mod_business_contact`";

	for ($i=1;$i<=100;$i++) {
	   if($i>1) $sql.=",";
	   $sql.=" ADD `field$i` VARCHAR( 255 ) NOT NULL";
	   //$sql.=" ADD `timestp_field$i` BIGINT( 14 ) DEFAULT 0";
	}
	echo $sql;
}
*/
// ajout du lien pour la gestion des rubriques
if (dims_isadmin()) {
	echo "<div style=\"width:100%;display:block;margin-right:20px;float:left;text-align:right\">".dims_create_button($_DIMS['cste']['_CATEGORIES'],'./common/modules/wce/img/publish.png','javascript:document.location.href=\''.$scriptenv.'?op=admin_categ\'','admin','')."</div>";
}
$object_id=dims_load_securvalue("object_id",dims_const::_DIMS_NUM_INPUT,true,true);
$metafield_id=dims_load_securvalue("metafield_id",dims_const::_DIMS_NUM_INPUT,true,true);

switch ($op) {
	case 'savemetause':
		$metafield = new metafield();

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['current_metafield_id']) && $_SESSION['dims']['current_metafield_id']>0) {
			$metafield->open($_SESSION['dims']['current_metafield_id']);
			$sharemode=dims_load_securvalue('sharemode',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$db->query("DELETE from dims_mod_business_meta_use where id_metafield= :metafield and id_object= :metafieldobject ", array(
				':metafield' => $metafield->fields['id'],
				':metafieldobject' => $metafield->fields['id_object']
			));

			switch($sharemode) {
				case 1:
					// on traite les workspaces

					$selwork = dims_load_securvalue('selwork', dims_const::_DIMS_NUM_INPUT, true, true, true);
					foreach ($selwork as $wid) {
						// on ajoute ds la base
						$metafielduse = new metafielduse();
						$metafielduse->fields['id_metafield']=$_SESSION['dims']['current_metafield_id'];
						$metafielduse->fields['id_object']=$metafield->fields['id_object'];
						$metafielduse->fields['id_workspace']=$wid;
						$metafielduse->fields['sharemode']=1;
						$metafielduse->save();
					}
					break;
				case 2:
					$metafielduse = new metafielduse();
					$metafielduse->fields['id_metafield']=$_SESSION['dims']['current_metafield_id'];
					$metafielduse->fields['id_object']=$metafield->fields['id_object'];
					$metafielduse->fields['id_workspace']=0;
					$metafielduse->fields['sharemode']=2;
					$metafielduse->save();
					break;
				default:
					break;
			}
		}
		dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN);
		break;
	case 'editmetause':
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			ob_end_clean();
			//require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");

			if ($object_id>0 && $metafield_id>0) {
				$_SESSION['dims']['current_metafield_id']=$metafield_id;
				require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_field_edituse.php');
			}
		}
		die();
	break;
		case 'editmetausedoublon':
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			ob_end_clean();
			//require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");

			if ($object_id>0 && $metafield_id>0) {
				$_SESSION['dims']['current_metafield_id']=$metafield_id;
				require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_field_edituse_doublon.php');
			}
		}
		die();
	break;
	case 'savemetafield':
		require_once(DIMS_APP_PATH . "/modules/system/class_mb_field.php");
		$id_newmbfield=0;

		if ($object_id>0) {
			$metafield = new metafield();
			$trouve=false;
			$fieldnew_position=dims_load_securvalue('fieldnew_position',dims_const::_DIMS_NUM_INPUT,true,true,false);

			if (isset($_SESSION['dims']['current_metafield_id']) && $_SESSION['dims']['current_metafield_id']>0) {
				$metafield->open($_SESSION['dims']['current_metafield_id']);
				$trouve=true;
			}
			else {
				$metafield->init_description();
				$id_metacateg=dims_load_securvalue('field_id_metacateg',dims_const::_DIMS_NUM_INPUT,true,true,false);
				$select = "Select max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";

				$res=$db->query($select, array(
					':idobject' 	=> $object_id,
					':idmetacateg' 	=> $id_metacateg
				));
				$fields = $db->fetchrow($res);
				$maxpos = $fields['maxpos'];

				if (!is_numeric($maxpos) || $maxpos==0) $maxpos = 0;
				$metafield->fields['position'] = $maxpos+1;
				$fieldnew_position=$metafield->fields['position'];
				$id_newmbfield=0;
				$metafield->fields['used']="1";
			}

			if (!isset($_SESSION['dims']['current_metafield_id']) && isset($_POST['field_id_mbfield']) && $_POST['field_id_mbfield']==0 || !isset($_POST['field_id_mbfield'])) {
				// important calcul du plus petit entier contenant l'indice de colonne
				//dims_print_r($_POST);die();
				$ind=1;
				$slots=array();
				$trouve=false;
				// on remplit avec ce qui est pris
				$res=$db->query("select fieldname from dims_mod_business_meta_field where id_object = :idobject ", array(
					':idobject' 	=> $object_id
				));

				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
						$slots[$f['fieldname']]=1;
					}
				}
				else {
					$ind=1; // 1er element
					$trouve=true;
				}
				// on boucle jusqu'a trouver un slot de libre (=non d�finit)
				while (!$trouve && $ind<=200) {
					if (isset($slots[$ind])) {
						$ind++;
					}
					else {
						$trouve=true; // on a trouve un slot de dispo
					}
				}

				if ($trouve) {
					// on ajout cette nouvelle r�f�rence dans le champ
					$metafield->fields['fieldname'] = $ind;

					if ($object_id==dims_const::_SYSTEM_OBJECT_CONTACT) {
						$tablename="dims_mod_business_contact";
						$tablename_layer="dims_mod_business_contact_layer";
					}
					else {
						$tablename="dims_mod_business_tiers";
						$tablename_layer="dims_mod_business_tiers_layer";
					}

					// check de la colonne si column existe ou non
					$sql = "SELECT	column_name
							FROM	information_schema.columns
							WHERE	TABLE_SCHEMA = :tableschema
							AND		table_name = :tablename
							AND		column_name LIKE :colname ";

					$res=$db->query($sql, array(
						':tableschema' 	=> _DIMS_DB_DATABASE ,
						':tablename' 	=> $tablename ,
						':colname' 		=> "field".$ind
					));

					if ($db->numrows($res)==0) {
						$complet="";
						// on cr�e la colonne sur contact ou enteprise
						if (isset($slots[$ind+1])) {
							// deux cas, soit on est >1 soit = 1
							if ($ind==1) {
								// on va rechercher le dernier champ g�n�rique

							}
							else {
								$complet=" AFTER field".($ind-1);
							}
						}

						// on ajoute le nouveau champ
						$db->query("ALTER TABLE `".$tablename."` ADD `field".$ind."` VARCHAR( 255 ) NULL ".$complet);
						$db->query("ALTER TABLE `".$tablename_layer."` ADD `field".$ind."` VARCHAR( 255 ) NULL ".$complet);
					}

					// on cr�� la r�f�rence dans la table mb_fields
					$mbf = new mb_field();
					$mbf->init_description();
					if(!empty($_SESSION['dims']['permanent_data']['mb_tables']['tables'][$tablename]['id'])){
						$mbf->fields['id_table'] = $_SESSION['dims']['permanent_data']['mb_tables']['tables'][$tablename]['id'];
					}else{
						require_once DIMS_APP_PATH."modules/system/class_mb_table.php";
						$mbt = mb_table::find_by(array('name'=>$tablename),null,1);
						if(!empty($mbt)){
							$mbf->fields['id_table'] = $mbt->get('id');
						}
					}
					//$mbf->fields['tablename']=$tablename;
					$mbf->fields['name']="field".$ind;
					$mbf->fields['label']=dims_load_securvalue('field_name', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					$mbf->fields['type']="varchar(255)";
					$mbf->fields['visible']="1";
					//$mbf->fields['id_module_type']=1;
					//$mbf->fields['id_object']=0;

					// verification de l'indexation
					$mbf->fields['indexed']=isset($_POST['is_indexed'])?1:0;

					$mbf->fields['protected']=0;
					$mbf->save();

					//il faut l'ajouter en session
					$dims->addMetaField($tablename, $mbf);

					$id_newmbfield=$mbf->fields['id'];
				}
				else {
					echo "Error no empty space";
				}

			}
			else {
				$trouve=true;
			}

			if ($trouve) {
				$ancien_idmetacateg=$metafield->fields['id_metacateg'];
				$ancienne_position=$metafield->fields['position'];

				// sauvegarde ancienne valeur
				$oldenum=$metafield->fields['enum'];

				if ($metafield->fields['id_mbfield']>0) unset($_POST['field_id_mbfield']);

				$metafield->setvalues($_POST,'field_');
				$metafield->fields['id_object'] = $object_id;

				if ($id_newmbfield>0) {
					// ajout de la nouvelle valeur de mbfield pour ce metachamp
					$metafield->fields['id_mbfield'] = $id_newmbfield;
				}

				if ($id_newmbfield==0 && $metafield->fields['id_mbfield']>0) {
					$mbf = new mb_field();
					$mbf->open($metafield->fields['id_mbfield']);
					$mbf->fields['indexed']=isset($_POST['is_indexed'])?1:0;
					$mbf->save();
				}

				if ($oldenum != $metafield->fields['enum']) {
					// chargement des valeurs d'enum
					$tabenums = array();
					$sql_sa = "SELECT * FROM dims_mod_business_enum WHERE type LIKE :type order by libelle";

					$res_sa = $db->query($sql_sa, array(
						':type' 	=> $oldenum
					));
					while($tab = $db->fetchrow($res_sa)) {

						if ($tab['libelle']!="") {
							$tabenums[$tab['id']]=html_entity_decode($tab['libelle']);
						}
					}

					$namefield=$mbf->fields['name'];

					// on boucle pour mettre à jour les elements ds contact et contact_layer
					foreach ($tabenums as $key =>$value) {

						$db->query("update dims_mod_business_contact set ".$namefield."= :val where ".$namefield."= :key ", array(
							':val' => addslashes($value),
							':key' => $key
						));
						$db->query("update dims_mod_business_contact_layer set ".$namefield."= :val where ".$namefield."= :key ", array(
							':val' => addslashes($value),
							':key' => $key
						));
						echo "update dims_mod_business_contact set ".$namefield."='".addslashes($value)."' where ".$namefield."=".$key."<br>";
						echo "update dims_mod_business_contact_layer set ".$namefield."='".addslashes($value)."' where ".$namefield."=".$key."<br>";
					}

				}

				//echo $id_newmbfield;
				//dims_print_r($metafield->fields);die();
				if (!isset($_POST['field_option_needed'])) $metafield->fields['option_needed'] = 0;
				if (!isset($_POST['field_option_arrayview'])) $metafield->fields['option_arrayview'] = 0;
				if (!isset($_POST['field_option_exportview'])) $metafield->fields['option_exportview'] = 0;
				if (!isset($_POST['field_option_search'])) $metafield->fields['option_search'] = 0;
				/*
				if (!isset($field_option_arrayview)) $metafield->fields['option_arrayview'] = 0;
				if (!isset($field_option_exportview)) $metafield->fields['option_exportview'] = 0;
				if (!isset($field_option_cmsgroupby)) $metafield->fields['option_cmsgroupby'] = 0;
				if (!isset($field_option_cmsorderby)) $metafield->fields['option_cmsorderby'] = 0;
				if (!isset($field_option_cmsdisplaylabel)) $metafield->fields['option_cmsdisplaylabel'] = 0;
				if (!isset($field_option_cmsshowfilter)) $metafield->fields['option_cmsshowfilter'] = 0;
				*/

				if ($metafield->fields['id_metacateg'] != $ancien_idmetacateg && isset($_SESSION['dims']['current_metafield_id']) && $_SESSION['dims']['current_metafield_id']>0) {// nouvelle categ definie
					// fonctionnement:= on rattache en queue de categorie nouvelle
					// on decale sur celle existante
					$id_metacateg=$metafield->fields['id_metacateg'];
					$select = "SELECT max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";

					$res=$db->query($select, array(
						':idobject' 	=> $object_id,
						':idmetacateg' 	=> $id_metacateg
					));
					$fields = $db->fetchrow($res);
					$maxpos = $fields['maxpos'];

					if (!is_numeric($maxpos) || $maxpos==0) $maxpos = 0;
					// on a maintenant le max
					$metafield->fields['position']=$maxpos+1;
					$metafield->save();

					// on s'occupe maintenant de la categ existante
					$db->query("UPDATE dims_mod_business_meta_field
								SET position=position-1
								WHERE position > :position
								AND id_object = :idobject
								AND id_metacateg= :idmetacateg ", array(

						':position' 	=> $ancienne_position,
						':idobject' 	=> $metafield->fields['id_object'],
						':idmetacateg' 	=> $ancien_idmetacateg
					));
				}
				elseif ($fieldnew_position != $ancienne_position) {// nouvelle position d�finie

						if ($fieldnew_position<1) $fieldnew_position=1;
						else {
							$select = "SELECT max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";
							$res=$db->query($select, array(
								':idobject' 	=> $object_id,
								':idmetacateg' 	=> $metafield->fields['id_metacateg']
							));
							$fields = $db->fetchrow($res);
							if ($fieldnew_position > $fields['maxpos']) $fieldnew_position = $fields['maxpos'];
						}

						$db->query("UPDATE dims_mod_business_meta_field
									SET position=0
									WHERE position= :position
									AND id_object = :idobject
									AND id_metacateg= :idmetacateg ", array(

							':position' 	=> $ancienne_position,
							':idobject' 	=> $metafield->fields['id_object'],
							':idmetacateg' 	=> $metafield->fields['id_metacateg']
						));
						if ($fieldnew_position > $ancienne_position) {
							$db->query("UPDATE dims_mod_business_meta_field
										SET position=position-1
										WHERE position BETWEEN ".($ancienne_position-1)." AND ".$fieldnew_position."
										AND id_object = :idobject
										AND id_metacateg= :idmetacateg ", array(
								':idobject' 	=> $metafield->fields['id_object'],
								':idmetacateg' 	=> $metafield->fields['id_metacateg']
							));
						}
						else {
							$db->query("UPDATE dims_mod_business_meta_field
										SET position=position+1
										WHERE position BETWEEN ".$fieldnew_position." AND ".($ancienne_position-1)."
										AND id_object = :idobject
										AND id_metacateg= :idmetacateg ", array(
								':idobject' 	=> $metafield->fields['id_object'],
								':idmetacateg' 	=> $metafield->fields['id_metacateg']
							));
						}
						$db->query("UPDATE dims_mod_business_meta_field
									SET position= :position
									WHERE position=0
									AND id_object = :idobject
									AND id_metacateg= :idmetacateg ", array(
							':postion' 		=> $fieldnew_position,
							':idobject' 	=> $metafield->fields['id_object'],
							':idmetacateg' 	=> $metafield->fields['id_metacateg']
						));
						$metafield->fields['position'] = $fieldnew_position;
						$metafield->save();
				}
				else $metafield->save();
			}
			if ($trouve) dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN."&link_edit=".$metafield->fields['id']);
		}
		break;

	case 'usemetafield':
		$metafield = new metafield();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$metafield->fields['used']=1;
			$metafield->save();
		}
		dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN);
		break;

	case 'deletemetafield':
		require_once(DIMS_APP_PATH . "/modules/system/class_mb_field.php");

		$metafield = new metafield();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);

			$ancien_idmetacateg=$metafield->fields['id_metacateg'];
			$ancienne_position=$metafield->fields['position'];
			$fieldname=$metafield->fields['fieldname'];

			// on ne traite en suppression que les champs dynamiques
			if ($fieldname>0 || true) {

				// on regarde si il y a des donn�es
				if ($metafield->fields['id_object']==dims_const::_SYSTEM_OBJECT_CONTACT) {
					$res=$db->query("select id from dims_mod_business_contact where field".$fieldname."<>''");

					if ($db->numrows($res)>0) {
						// on change used � 0
						$metafield->fields['used']=0;
						$metafield->save();
					}
					else {
						//$db->query("update dims_mod_business_contact set field".$fieldname."=''");
						$metafield->delete();
					}
				}
				else {
					///////////////////////////////////////////////////////////////////////////////////////////////////////
					// importante operation de maj, peut risque de perdre des donnees si user efface par erreur une colonne
					// on met a jour dans la table colonne la colonne concern�e par le champ � vide
					$res=$db->query("SELECT id FROM dims_mod_business_tiers_field WHERE id_metafield= :idmetafield ", array(
							':idmetafield' 	=> $metafield_id
						));

					if ($db->numrows($res)>0) {
						// on change used � 0
						$metafield->fields['used']=0;
						$metafield->save();
					}
					else {
						$db->query("update dims_mod_business_tiers set field".$fieldname."=''");
						$db->query("optimize table dims_mod_business_tiers");
						$metafield->delete();
						// on s'occupe maintenant de la categ existante
						//$db->query("update dims_mod_business_meta_field set position=position-1 where position > ".$ancienne_position." and id_object = ".$metafield->fields['id_object']." and id_metacateg=".$ancien_idmetacateg);
					}
				}
			}


		 }
		dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN);
		break;

	case 'moveupmetafield':
		$metafield = new metafield();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$id_metacateg=$metafield->fields['id_metacateg'];
			$position=$metafield->fields['position'];
			if($metafield->fields['position']>1) {
				$position=$metafield->fields['position'];
				// on bouge celui du dessus en dessous
				$db->query("UPDATE dims_mod_business_meta_field
							SET position=position+1
							WHERE position = :position
							AND id_object = :idobject
							AND id_metacateg= :idmetacateg ", array(
					':position' 	=> ($position-1),
					':idobject' 	=> $metafield->fields['id_object'],
					':idmetacateg' 	=> $id_metacateg
				));
				// on bouge celui courant au dessus
				$db->query("UPDATE dims_mod_business_meta_field set position= :position where id = :id ", array(
					':position' => ($position-1),
					':id' 		=> $metafield->fields['id']
				));
			}
		}
		dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN);
		break;

	case 'movedownmetafield':
		$metafield = new metafield();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$id_metacateg=$metafield->fields['id_metacateg'];
			$select = "SELECT max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";

			$res=$db->query($select, array(
				':idobject'		=> $object_id,
				':idmetacateg'	=> $id_metacateg
			));
			$fields = $db->fetchrow($res);
			$maxpos = $fields['maxpos'];
			if($metafield->fields['position']<$maxpos) {
				$position=$metafield->fields['position'];
				// on bouge celui du dessous en dessus
				$db->query("UPDATE dims_mod_business_meta_field
							SET position=position-1
							WHERE position = :position
							AND id_object = :idobject
							AND id_metacateg= :idmetacateg ", array(
					':position'		=> ($position+1),
					':idobject'		=> $metafield->fields['id_object'],
					':idmetacateg'	=> $id_metacateg
				));
				// on bouge celui courant au dessus
				$db->query("UPDATE dims_mod_business_meta_field
							SET position= :position
							WHERE id = :id ", array(
					':position'	=> ($position+1),
					':id'		=> $metafield->fields['id']
				));
			}
		}
		dims_redirect("$scriptenv?op="._BUSINESS_CAT_ADMIN);
		break;

	case 'add_metafield':
		unset($_SESSION['dims']['current_metafield_id']);
		include (DIMS_APP_PATH . "/modules/system/crm_business_admin_field.php");
		break;
	case 'display_metafield':
	if ($object_id>0)
		require_once(DIMS_APP_PATH . '/modules/system/crm_business_display.php');
	break;


// importation fichier excel
	case 'import_tiers2':
	case 'import_contact2':
		if (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_ADDREPLY) || dims_isactionallowed(0)){
			$_SESSION['dims']['importform']['object_id'] = $object_id;
			include(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_step2.php');
		}
		break ;

	case 'import_contact3' :
		if (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_ADDREPLY) || dims_isactionallowed(0))
			include(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_step3.php');
		break ;

	case 'import_save_contact' :
		include(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_save.php');
		break ;

// gestion des profils utilisateurs
		case 'gest_profil' :
						include(DIMS_APP_PATH . '/modules/system/crm_business_admin_profil.php');
		break ;

	default:
		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DYNFIELD_CONTACT'],'width:50%;float:left;clear:none;','','');
		// ajout d'un champ
		$object_id=dims_const::_SYSTEM_OBJECT_CONTACT;
		include (DIMS_APP_PATH . "/modules/system/crm_business_admin_field_list.php");
		echo $skin->close_simplebloc();

		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DYNFIELD_ENT'],'width:49%;float:right;clear:none;','','');
		$object_id=dims_const::_SYSTEM_OBJECT_TIERS;
		include (DIMS_APP_PATH . "/modules/system/crm_business_admin_field_list.php");
		echo $skin->close_simplebloc();

		$link_edit=dims_load_securvalue("link_edit",dims_const::_DIMS_NUM_INPUT,true,true);
		if ($link_edit>0) {
			echo '<script language="JavaScript" type="text/JavaScript">window.onload=function(){ $("link_edit_'.$link_edit.'").focus();}</script>';
		}
	break;
}
?>
