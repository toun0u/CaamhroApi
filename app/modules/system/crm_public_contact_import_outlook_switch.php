<?php
//dims_print_r($_SESSION['dims']);
//header('Content-type: text/html; charset=UTF-8');
require_once(DIMS_APP_PATH . "/modules/system/class_ct_link.php");
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_import.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact_import_ent_similar.php');

//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('max_execution_time',-1);
ini_set('memory_limit','1024M');

function verifContact($current_line){
	$db = dims::getInstance()->getDb();
	$_SESSION['dims']['RL']++;
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']))
		str_replace("'","",$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']);
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['firstname']) && trim($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['firstname'])!='' && isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['lastname']) && trim($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['lastname'])!='' && isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
		$verif_email = explode('@',$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']);

		if(count($verif_email) > 1) {
			// verification ces noms
			// nouveau cas pour supprimer les quotes => fichier P. Steichen
			foreach ($_SESSION['dims']['IMPORT_CONTACT'][$current_line] as $id=>$elem) {
				if (substr($elem,0,1)=="'") $_SESSION['dims']['IMPORT_CONTACT'][$current_line][$id]=substr($elem,1);
				if (substr($elem,strlen($elem)-1,1)=="'") $_SESSION['dims']['IMPORT_CONTACT'][$current_line][$id]=substr($elem,0,strlen($elem)-1);
			}
			return true;
		}else{

			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'])=='')
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'])=='')
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'])=='')
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company']))
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company'] = "<span style='color:red;',>---</span>";
			unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
			return false;
		}
//		  if(dims_verifyemail($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
//			  return true;
//		  }else{
//			  $_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
//			  return false;
//		  }
	}else{
		$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'])=='')
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'])=='')
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email']) || trim($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'])=='')
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company']))
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company'] = "<span style='color:red;',>---</span>";
		unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
		return false;
	}
}

function verifEntExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])){
		$sql = "SELECT	id
				FROM	dims_mod_business_tiers
				WHERE	intitule = :intitule ";
		$res = $db->query($sql, array(
			':intitule' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])
		));
		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = $data['id'];
		}else{
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = 0;

			//on va chercher des similitudes eventuelles sur les entreprises
			if(!isset($_SESSION['dims']['ALL_ENTS'])) {
				//si la session n'existe pas, on y place toutes les entreprises en vu de la comparaison
				$sql_e = "SELECT id, intitule FROM dims_mod_business_tiers ORDER BY intitule";
				$res_e = $db->query($sql_e);
				while($tab_e = $db->fetchrow($res_e)) {
					$_SESSION['dims']['ALL_ENTS'][$tab_e['id']] = $tab_e;
				}
			}
			//on compare la valeur courante avec les entreprises en session
			foreach($_SESSION['dims']['ALL_ENTS'] as $id_entc => $tab_ent) {
				$lev_nom = levenshtein(strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company']), strtoupper($tab_ent['intitule']));
				$coef_nom = $lev_nom - (ceil(strlen($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])/4));

				$coef_tot = $coef_nom;

				if($coef_tot < 4) {

					//on stock les entreprises similaires en base
					$imp = new tiers_similar();
					$imp->init_description();
					if ($current_line>0) {
						$imp->fields['id_contact'] = $current_line;
						$imp->fields['ent_intitule'] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'];
						$imp->fields['id_ent_similar'] = $id_entc;
						$imp->fields['id_user'] = $_SESSION['dims']['userid'];
						$imp->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

						$imp->save();
					}
					//die('la'.$coef_tot." ".$current_line." ".$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company']);
				}
			}
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = 0;
	}
}

function verifContactExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){

		$sql = "SELECT DISTINCT		c.id
				FROM				dims_mod_business_contact c
				LEFT JOIN			dims_mod_business_contact_layer l
				ON					l.id = c.id
				WHERE				l.id_layer = :idlayer
				AND					c.firstname LIKE :firstname
				AND					c.lastname LIKE :lastname
				";
		/* AND				(c.email = '".addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])."'
					OR					l.email = '".addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])."')
		 */
		$res = $db->query($sql, array(
			':idlayer'		=> $_SESSION['dims']['workspaceid'],
			':firstname'	=> addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['firstname']),
			':lastname'		=> addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['lastname'])
		));

		if($db->numrows($res)>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			if($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] != 0){
				$_SESSION['dims']['IMPORT_NEW_LINK'][$data['id']] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			}
			unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line]['exist'] = $data['id'];
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
	}
}

//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
						mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			RIGHT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = :idobject
			AND			mc.admin=1
			AND			mf.used=1
			ORDER BY	mc.position, mf.position
			";
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
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
//dims_print_r($_SESSION['dims']['contact_fields_mode']);
switch($op){

	case 1:
		require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook_switch_1.php');
		break;
	case 2:
		require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook_switch_2.php');
		break;
	case 3: /////// Traitement des contacts avec similarites ////////////

		require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook_switch_3.php');
		break;

	case 4:
		//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
		$op = 4;

		unset($_SESSION['dims']['import_count_contact_similar']);
		unset($_SESSION['dims']['import_contact'],$_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);

		//on traite dans un premier temps les contacts ayant des entreprises rattachees par le biais de similitudes


		//s'il	reste des contacts inconnus
		if(count($_SESSION['dims']['IMPORT_CONTACT'])>0){
			$content_contact_import = "<p>".$_DIMS['cste']['_IMPORT_INSTRUCTION_STEP4']."<p>";
			$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_TAB_LAST_CONTACTS']." :</p><br/>";
			$content_contact_import .= "<p>".count($_SESSION['dims']['IMPORT_CONTACT'])." ".$_DIMS['cste']['_IMPORT_CONTACTS_RESTANT']."</p>";
			$content_contact_import .= '<br/><div style="text-align:center;width:100%">
												'.dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('contacts_restants').submit();").'
											</div>';
			$content_contact_import .= '<form action="./admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_OUTLOOK.'&part='._BUSINESS_TAB_IMPORT_OUTLOOK.'&op=5" method="post" id="contacts_restants">';
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tr class="trl1" style="font-size:12px;">
													<td style="width: 10%;">&nbsp;</td>
													<td style="width: 35%;">'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</td>
													<td style="width: 35%;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
														<td style="width: 20%;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
											</tr>';

			$class_col = 'trl1';
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				//$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
				$content_contact_import .= '<tr class="'.$class_col.'">
												<td><input type="checkbox" name="contact_import_'.$data['id'].'" value="'.$data['id'].'" checked="checked"/></td>
												<td>'.$data['firstname'].' '.$data['lastname'].'</td>
												<td>'.$data['email'].'</td>
													<td>'.$data['company'].'</td>
											</tr>';
				$token->field("cont");
			}

			$content_contact_import .= '</table>';

			$content_contact_import .= '<br/><div style="text-align:center;">
												'.dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('contacts_restants').submit();").'
											</div>
										</form>';
			$tokenHTML = $token->generate();
			$content_contact_import .= $tokenHTML;
		}else{
			$content_contact_import = '<p>'.$_DIMS['cste']['_IMPORT_ALL_CONTACTS_ALREADY_EXISTS'].'</p><br/>
										<div style="text-align:center;">
											'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=5');").'
										</div>';
		}
	break;

	case 5:
		require_once DIMS_APP_PATH.'/modules/system/crm_public_contact_import_execute.php';
	break;

	//	case 'del_ct_doublons':
//		//on selectionne les ct qui ont des doublons
//		$sql = "SELECT c.id, c.firstname, c.lastname
//				FROM dims_mod_business_contact c ORDER BY c.id";
//		$res = $db->query($sql);
//		$to_verif = array();
//		while($tab_d = $db->fetchrow($res)) {
//			$to_verif[$tab_d['id']] = $tab_d;
//		}
//		foreach($to_verif as $id_ct => $tab_ct) {
//			//on regarde si le ct existe encore
//			$sql_v = "SELECT id FROM dims_mod_business_contact WHERE id=".$tab_ct['id'];
//			$res_v = $db->query($sql_v);
//			if($db->numrows($res_v) > 0) {
//				//on cherche les doublons
//				$sql_d =	"SELECT id
//							FROM dims_mod_business_contact
//							WHERE lastname LIKE '".$tab_ct['lastname']."'
//							AND firstname LIKE '".$tab_ct['firstname']."'
//							AND id !=".$tab_ct['id'];
//				$res_d = $db->query($sql_d);
//				if($db->numrows($res_d) > 0) {
//					while($tab_del = $db->fetchrow($res_d)) {
//						$sql_del = "DELETE FROM dims_mod_business_contact WHERE id=".$tab_del['id'];
//						$db->query($sql_del);
//						$sql_del_layer = "DELETE FROM dims_mod_business_contact_layer_save WHERE id=".$tab_del['id'];
//						$db->query($sql_del_layer);
//					}
//				}
//			}
//
//		}
//		  die();
//	break;
};
?>
