<script language="JavaScript" type="text/JavaScript">
	function activeFiche(id, type) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=activefiche&id_record="+id+"&type="+type,"");
		document.location.reload();
	}

	function desactiveFiche(id, type) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=desactivefiche&id_record="+id+"&type="+type,"");
		document.location.reload();
	}

	function contWatch(id_pers, type) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=savewatch&id_cont="+id_pers+"&type="+type,"");
		alert(retour);
	}

	function contvcard(id_pers, type) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=generatevcard&id_cont="+id_pers+"&type="+type,"");
		alert(retour);
	}
	window['displayInfoCtCrm'] = function displayInfoCtCrm(id) {
		var id_popup = dims_openOverlayedPopup(350,500);
		dims_xmlhttprequest_todiv('admin.php', "dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<?php echo _BUSINESS_CAT_CONTACT; ?>&action=<?php echo _BUSINESS_TAB_CONTACTSTIERS; ?>&op=show_minimal_info_ct&id="+id+'&id_popup='+id_popup,'','p'+id_popup);
	}
	window['displayInfoEntCrm'] = function displayInfoEntCrm(id) {
		var id_popup = dims_openOverlayedPopup(350,500);
		dims_xmlhttprequest_todiv('admin.php', "dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<?php echo _BUSINESS_CAT_CONTACT; ?>&action=<?php echo _BUSINESS_TAB_CONTACTSTIERS; ?>&op=show_minimal_info_ent&id="+id+'&id_popup='+id_popup,'','p'+id_popup);
	}
</script>
<div id="module_ct">
<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_layer.php");
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . "/modules/system/class_contact_layer.php");
require_once(DIMS_APP_PATH . '/modules/system/class_business_metacateg.php');
require_once(DIMS_APP_PATH . '/modules/system/class_vcard.php');
require_once(DIMS_APP_PATH . '/modules/system/class_enum.php');
require_once(DIMS_APP_PATH . '/include/functions/mail.php');

$init=dims_load_securvalue('init',dims_const::_DIMS_NUM_INPUT,true,true);
if ($init) {
	unset($_SESSION['dims']['businness']['action']);
	unset($_SESSION['business']['contact_id']);
	unset($_SESSION['dims']['businness']['part']);
	$_SESSION['dims']['businness']['part']=_BUSINESS_TAB_ADMIN;
}
$part="";

if (!isset($_SESSION['dims']['businness']['action'])) $_SESSION['dims']['businness']['action']=_BUSINESS_TAB_ADMIN;
if (!isset($_SESSION['dims']['businness']['part'])) $_SESSION['dims']['businness']['part']="";

$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['businness']['action'],_BUSINESS_TAB_ADMIN);
$part = dims_load_securvalue('part',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['businness']['part'],"");

$workspace= new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);

$tabs=array();
////////////////////////////////////////////////////////////
// Affichage du menu
if (isset($action) && $action!=_BUSINESS_TAB_CONTACT_GRAPH)
	require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_menu.php');

$form_title = $_DIMS['cste']['_DIMS_LABEL_CONTACTS'];

$include ='';
switch($action) {
	default :
	case _BUSINESS_TAB_CONTACTSSEEK:
		$title = $_DIMS['cste']['_SEARCH'];

		switch ($op) {
			case "export_pdf":
				$id_contact = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true);
				$contact = new contact();
				$contact->open($id_contact);
				// include
				break;
			case "activefiche" :
				$id_record = dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true);
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true);

				switch($type) {
					case "pers" :
						$cont = new contact();
						$cont->open($id_record);
						$cont->fields['inactif'] = 0;
						$cont->save();
						break;
					case "ent" :
						$ent = new tiers();
						$ent->open($id_record);
						$ent->fields['inactif'] = 0;
						$ent->save();
						break;
				}

				break;

			case "desactivefiche":
				$id_record = dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true);
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true);

				switch($type) {
					case "pers" :
						$cont = new contact();
						$cont->open($id_record);
						$cont->fields['inactif'] = 1;
						$cont->save();
						break;
					case "ent" :
						$ent = new tiers();
						$ent->open($id_record);
						$ent->fields['inactif'] = 1;
						$ent->save();
						break;
				}
				break;
			case "supprcomm" :
				include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
				$id_cmt = dims_load_securvalue('id_cmt', dims_const::_DIMS_NUM_INPUT, true, true);
				$cmt = new commentaire();
				$cmt->open($id_cmt);
				$cmt->delete();
				break;
			case "exportsearchpers_adr" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des adresses Contact");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport_adr.php');
				break;
			case "exportsearchpers_mail" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des emails");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport_mail.php');
				break;
			case "exportsearchpers" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des contacts");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport.php');
				break;
			case "exportsearchpersetiquette" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des contacts");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport_etiquette.php');
				break;
			case "exportsearchent" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des entreprises");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_searchexport.php');
				break;
			case "exportsearchent_adr" :
				dims_create_user_action_log(_SYSTEM_ACTION_EXPORT, "Export des adresses Entreprise");
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_searchexport_adr.php');
				break;
			case "delete_search_val" :
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
				switch($type) {
					case "pers":
						unset($_SESSION['business']['search_ct']);
						unset($_SESSION['business']['search_ent']);
						unset($_SESSION['business']['search_lkent']);
						unset($_SESSION['business']['search_ct_sql']);

						break;
					case "ent" :
						unset($_SESSION['business']['ent_search_ent']);
						unset($_SESSION['business']['ent_search_ct']);
						unset($_SESSION['business']['ent_search_lkct']);
						unset($_SESSION['business']['search_ent_sql']);

						break;
				}
				break;

			case "exec_search":
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_execsearch.php');
				$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_search.php';
				break;

			case "exec_search_ent":
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');

				$_SESSION['business']['ent_search_ent']	= array();
				$_SESSION['business']['ent_search_ct']	= array();
				$_SESSION['business']['ent_search_lkct'] = array();
				$_SESSION['business']['search_ent_sql'] = array();

				//on recupere les infos du formulaire
				//les infos generiques
				$inf_e = new tiers();
				$inf_e->init_description();
				$inf_e->setvalues($_POST, "ent_");

				$_SESSION['business']['ent_search_ent']=$inf_e->fields;

				//les infos personnes
				$inf_ct = new contact();
				$inf_ct->init_description();
				$inf_ct->setvalues($_POST, "ct_");

				$_SESSION['business']['ent_search_ct']=$inf_ct->fields;

				//les infos du lien pers->ent
				$inf_cte = new tiersct();
				$inf_cte->init_description();
				$inf_cte->setvalues($_POST, "lkct_");

				$_SESSION['business']['ent_search_lkct']=$inf_cte->fields;

				//traitement des conditions propres a la personne
				$where = '';
				foreach ($_SESSION['business']['ent_search_ent'] as $lbl_f => $val_f) {
					if (isset($_POST['ent_'.$lbl_f])) {
						if($val_f != "") $where .= " t.$lbl_f LIKE '%$val_f%' AND ";
					}
				}
				//dims_print_r($_SESSION['business']['ent_search_ent']);
				//traitement des conditions propres a l'entreprise
				$where_ct = '';
				foreach ($_SESSION['business']['ent_search_ct'] as $lbl_f => $val_f) {
					if (isset($_POST['ct_'.$lbl_f])) {
						if($val_f != "" || $val_f != 0) $where_ct .= " AND c.$lbl_f LIKE '$val_f%' ";
					}
				}
				//if($where_ct == '') $where_ct .= " AND c.lastname LIKE '%' ";

				//traitement des conditions propres au lien avec l'entreprise
				$where_lkct = '';
				foreach ($_SESSION['business']['ent_search_lkct'] as $lbl_f => $val_f) {
					if($val_f != "" && $lbl_f!='id_tiers' && $lbl_f!='id_contact') $where_lkct .= " AND tc.$lbl_f LIKE '$val_f%' ";
				}


				//elements concernant la personne a selectionner
				if($where_ct != '' || $where_lkct != '')
				//if ($iscontact)
					$sel_ct = ', c.id as id_ct, c.firstname, c.lastname, tc.type_lien,tc.function';
				else $sel_ct ='';

				//traitement des conditions du lien avec entreprise
				$opt_ent = '';
				if($where_ct != '' || $where_lkct != '') {
					$opt_ent = '	INNER JOIN	dims_mod_business_tiers_contact tc
									ON			tc.id_tiers = t.id ';
					$opt_ent .= $where_lkct;
					$opt_ent .='	INNER JOIN	dims_mod_business_contact c
									ON			c.id = tc.id_contact ';
					$opt_ent .= $where_ct;
				}

				if (isset($_POST['id_workspace_from']) && $_POST['id_workspace_from']!='0' && $_POST['id_workspace_from']!='') {
						$id_workspace_from = dims_load_securvalue('id_workspace_from', dims_const::_DIMS_NUM_INPUT, true, true);

						$_SESSION['business']['ent_search_ent']['id_workspace_from']=$id_workspace_from;

						// on ajoute les liens layer
						$opt_ent.=" inner join dims_mod_business_tiers_layer as tl on tl.id=t.id and tl.type_layer=1 and tl.id_layer=".$id_workspace_from;

				}
				if($where == '') $where = '1';
				else $where = substr($where, 0, -4);

				$sql_s = "";
				$sql_s .= "SELECT	t.id as id_ent,
									t.intitule,
									t.timestp_modify,
									t.adresse,
									t.codepostal,
									t.ville,
									t.telephone,
									t.telecopie,
									t.mel,
									t.ent_capital,
									t.ent_activiteprincipale,
									t.ent_effectif,
									t.ent_datecreation,
									t.pays,
									t.site_web,
									t.inactif,
									t.presentation ";
				$sql_s .= $sel_ct;
				$sql_s .= " FROM dims_mod_business_tiers t ";
				$sql_s .= $opt_ent;
				$sql_s .= " WHERE ".$where." ";
				$sql_s .= " ORDER BY t.intitule";

				//mise en session de la requete pour l'export (lfb_public_contact_searchexport.php)
				$_SESSION['business']['search_ent_sql'] = $sql_s;

				//ajout de la limite sur la requete (que l'on ne met pas sur l'export car on doit limiter l'affichage à 100)
				$sql_s .= " LIMIT 0,101 ";

				$res_s = $db->query($sql_s);

				// rebuild des champs pour savoir ceux coches
				$sql_s = "SELECT	t.id as id_ent,";

				$sql =	"SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
									mb.protected,mb.name as namefield,mb.label as titlefield
						FROM		dims_mod_business_meta_field as mf
						INNER JOIN	dims_mb_field as mb
						ON			mb.id=mf.id_mbfield
						RIGHT JOIN	dims_mod_business_meta_categ as mc
						ON			mf.id_metacateg=mc.id
						WHERE		  mf.id_object = :idobject
						AND			mf.used=1
						AND			mf.option_exportview=1
						ORDER BY	mc.position, mf.position
						";
				$rs_fields=$db->query($sql, array(
					':idobject'	=> dims_const::_SYSTEM_OBJECT_TIERS
				));
				$_SESSION['business']['exportdata']=array();

				while ($fields = $db->fetchrow($rs_fields)) {
					$sql_s .= ",t.".$fields['namefield'];

					if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
					else $namevalue=$fields['name'];
					$elem=array();
					$elem['title']=$namevalue;
					$elem['namefield']=$fields['namefield'];

					$_SESSION['business']['exportdata'][]=$elem;
				}

				$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_search.php';
				break;

			case "modcmtpers_author":
				ob_start();

					include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');

					$id_cmt = dims_load_securvalue('id_cmt', dims_const::_DIMS_NUM_INPUT, true, true);

					if(dims_load_securvalue('id_cont', dims_const::_DIMS_NUM_INPUT, true, true)) {
						$id_ct = dims_load_securvalue('id_cont', dims_const::_DIMS_NUM_INPUT, true, true);
						$type = "pers";
					}
					elseif(dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true, true)) {
						$id_ct = dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true, true);
						$type = "ent";
					}

					$from = dims_load_securvalue('from', dims_const::_DIMS_NUM_INPUT, true, true);

					$niv = '';

					if(!empty($id_cmt)) {
						$cmt = new commentaire();
						$cmt->open($id_cmt);
						$niv = $cmt->fields['com_level'];
					}

				$option = '';
				$option .= '<option value="1"';
				if($niv == 1 || $from == 1) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
							<option value="2"';
				if($niv == 2 || $from == 2) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>
							<option value="3"';
				if($niv == 3 || $from == 3) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_PERSO'].'</option>';

				if(!empty($cmt->fields['commentaire'])) $txt_com = stripslashes($cmt->fields['commentaire']); else $txt_com="";

				$affiche = '';
				$affiche .= '	<form method="post" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSSEEK.'&op=savecommentp_aut&type='.$type.'">';
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("id_pers", 	$id_ct);
				$token->field("id_cmt",		$id_cmt);
				$token->field("comment_level");
				$token->field("commentaire");
				$tokenHTML = $token->generate();
				$affiche .= $tokenHTML;
				$affiche .= '		<input type="hidden" name="id_pers" value="'.$id_ct.'"/>
									<input type="hidden" name="id_cmt" value="'.$id_cmt.'"/>
									<table width="100%" cellpadding="3" cellspacing="0">
										<tr>
											<td style="vertical-align:top;align:right;">
												'.$_DIMS['cste']['_DIMS_COMMENTS_LVL'].'
											</td>
											<td style="vertical-align:top;align:left;">
												<select id="comment_level" name="comment_level">
													'.$option.'
												</select>
											</td>
										</tr>
										<tr>
											<td style="vertical-align:top;align:right;">
												'.$_DIMS['cste']['_DIMS_COMMENTS'].'
											</td>
											<td style="vertical-align:top;align:left;">
												<textarea id="commentaire" name="commentaire" rows="10" cols="50">'.$txt_com.'</textarea>
											</td>
										</tr>
										<tr>
											<td colspan="2" align="center">
												<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].'"/>
											</td>
										</tr>
									</table>
								</form>';

				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_ADD_COMMENT'],'font-weight:bold;width:100%','','');
				echo $affiche;
				echo $skin->close_widgetbloc();

				ob_end_flush();
				die();
				break;
			case "savecommentp_aut":
				include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');

				//recuperation des donnees
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
				$id_pers = dims_load_securvalue('id_pers', dims_const::_DIMS_NUM_INPUT, true, true);
				$id_cmt = dims_load_securvalue('id_cmt', dims_const::_DIMS_NUM_INPUT, true, true);
				$lvl = dims_load_securvalue('comment_level', dims_const::_DIMS_NUM_INPUT, true, true);
				$com = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true, false);

				$cmt = new commentaire();
				$cmt->open($id_cmt);
				$cmt->fields['id_contact'] = $id_pers;
				$cmt->fields['commentaire'] = ($com);
				if($type == "pers") {
					$cmt->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
				}
				elseif($type == "ent") {
					$cmt->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TIERS;
				}
				$cmt->fields['com_level'] = $lvl;
				$cmt->fields['date_create'] = date("YmdHis");
				$cmt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$cmt->fields['id_user'] = $_SESSION['dims']['userid'];
				$cmt->fields['id_user_ct'] = $_SESSION['dims']['user']['id_contact'];
				$cmt->fields['id_module'] = $_SESSION['dims']['moduleid'];

				$cmt->save();

				if($type == "pers") {
					dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_NEWS."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$id_pers);
				}
				elseif($type == "ent") {
					dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_NEWS."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$id_pers);
				}

				//dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_NEWS."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$id_pers);
				break;
			case "modcmtpers":
				ob_start();

					include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
					$type="";
					$id_cmt = dims_load_securvalue('id_cmt', dims_const::_DIMS_NUM_INPUT, true, true);
					if(dims_load_securvalue('id_cont', dims_const::_DIMS_NUM_INPUT, true, true)) {
						$id_ct = dims_load_securvalue('id_cont', dims_const::_DIMS_NUM_INPUT, true, true);
						$type = "pers";
					}
					elseif(dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true, true)) {
						$id_ct = dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true, true);
						$type = "ent";
					}

					$from = dims_load_securvalue('from', dims_const::_DIMS_NUM_INPUT, true, true);

					$niv = '';

					if(!empty($id_cmt)) {
						$cmt = new commentaire();
						$cmt->open($id_cmt);
						$niv = $cmt->fields['com_level'];
					}

				$option = '';
				$option .= '<option value="1"';
				if($niv == 1 || $from == 1) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
							<option value="2"';
				if($niv == 2 || $from == 2) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>
							<option value="3"';
				if($niv == 3 || $from == 3) $option .= ' selected="selected" ';
				$option .= '>'.$_DIMS['cste']['_DIMS_LABEL_PERSO'].'</option>';

				if(!empty($cmt->fields['commentaire'])) $txt_com = stripslashes($cmt->fields['commentaire']); else $txt_com="";

				$affiche = '';
				$affiche .= '	<form method="post" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSSEEK.'&op=savecommentpers&type='.$type.'">';
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("id_pers",	$id_ct);
				$token->field("comment_level");
				$token->field("commentaire");
				$tokenHTML = $token->generate();
				$affiche .= $tokenHTML;
				$affiche .= '	<input type="hidden" name="id_pers" value="'.$id_ct.'"/>
									<table width="100%" cellpadding="3" cellspacing="0">
										<tr>
											<td style="vertical-align:top;align:right;">
												'.$_DIMS['cste']['_DIMS_COMMENTS_LVL'].'
											</td>
											<td style="vertical-align:top;align:left;">
												<select id="comment_level" name="comment_level">
													'.$option.'
												</select>
											</td>
										</tr>
										<tr>
											<td style="vertical-align:top;align:right;">
												'.$_DIMS['cste']['_DIMS_COMMENTS'].'
											</td>
											<td style="vertical-align:top;align:left;">
												<textarea id="commentaire" name="commentaire" rows="10" cols="50">';
				//$affiche .= $txt_com;
				$affiche .= '</textarea>
											</td>
										</tr>
										<tr>
											<td colspan="2" align="center">
												<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].'"/>
											</td>
										</tr>
									</table>
								</form>';

				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_ADD_COMMENT'],'font-weight:bold;width:100%','','');
				echo $affiche;
				echo $skin->close_widgetbloc();

				ob_end_flush();
				die();
				break;

			case "savecommentpers":
				include(DIMS_APP_PATH . '/modules/system/class_commentaire.php');

				//recuperation des donnees
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
				$id_pers = dims_load_securvalue('id_pers', dims_const::_DIMS_NUM_INPUT, true, true);
				$lvl = dims_load_securvalue('comment_level', dims_const::_DIMS_NUM_INPUT, true, true);
				$com = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true, false);

				$cmt = new commentaire();
				$cmt->init_description();
				$cmt->fields['id_contact'] = $id_pers;
				$cmt->fields['commentaire'] = ($com);
				if($type == "pers") {
					$cmt->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
				}
				elseif($type == "ent") {
					$cmt->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TIERS;
				}
				$cmt->fields['com_level'] = $lvl;
				$cmt->fields['date_create'] = date("YmdHis");
				$cmt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$cmt->fields['id_user'] = $_SESSION['dims']['userid'];
				$cmt->fields['id_user_ct'] = $_SESSION['dims']['user']['id_contact'];
				$cmt->fields['id_module'] = $_SESSION['dims']['moduleid'];

				$cmt->save();

				if($type == "pers") {
					dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_NEWS."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$id_pers);
				}
				elseif($type == "ent") {
					dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_NEWS."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$id_pers);
				}


				break;

			case "add_ticket":
				ob_start();
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_add_ticket.php');
				ob_end_flush();
				die();
				break;

			case "send_tickets" :
				require_once(DIMS_APP_PATH . '/include/functions/tickets.php');
				$subject = dims_load_securvalue('ticket_sujet', dims_const::_DIMS_CHAR_INPUT, true, true);
				$categ1 = dims_load_securvalue('ticket_type_inf1', dims_const::_DIMS_CHAR_INPUT, true, true);
				$categ2 = dims_load_securvalue('ticket_type_inf2', dims_const::_DIMS_CHAR_INPUT, true, true);
				$categ3 = dims_load_securvalue('ticket_type_inf3', dims_const::_DIMS_CHAR_INPUT, true, true);
				$message = dims_load_securvalue('ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true);
				$id_record = dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true);
				$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);

				$categ = '';
				if(!empty($categ1)) $categ.= $categ1.", ";
				if(!empty($categ2)) $categ.= $categ2.", ";
				if(!empty($categ3)) $categ.= $categ3;

				//type de l'objet
				switch($type) {
					case 1 :
						$objet = dims_const::_SYSTEM_OBJECT_CONTACT;

						//Personne concernee
						$pers = new contact();
						$pers->open($id_record);
						$obj_label = $pers->fields['firstname']." ".$pers->fields['lastname'];

						$action = _BUSINESS_TAB_CONTACT_FORM;

						$redirect = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action=$action&contact_id=$id_record";
						break;
					case 2 :
						$objet = dims_const::_SYSTEM_OBJECT_TIERS;

						//entreprise concernï¿œe
						$ent = new tiers();
						$ent->open($id_record);
						$obj_label = $ent->fields['intitule'];

						$action = _BUSINESS_TAB_ENT_FORM;

						$redirect = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action=$action&id_ent=$id_record";
						break;
					case 3 :
						$objet = dims_const::_SYSTEM_OBJECT_CONTACT;

						//Personne concernee
						$pers = new contact();
						$pers->open($id_record);
						$obj_label = $pers->fields['firstname']." ".$pers->fields['lastname'];

						$action = _BUSINESS_TAB_CONTACT_INTELL;
						$part= _BUSINESS_TAB_CT_INTEL_PERS;
						$redirect = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action=$action&part=$part&contact_id=$id_record";
						break;
					case 4 :
						$objet = dims_const::_SYSTEM_OBJECT_TIERS;

						//entreprise concernï¿œe
						$ent = new tiers();
						$ent->open($id_record);
						$obj_label = $ent->fields['intitule'];

						$action = _BUSINESS_TAB_ENT_INTELL;
						$part = _BUSINESS_TAB_ENT_IDENTITE;

						$redirect = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action=$action&part=$part&id_ent=$id_record";
						break;
				}

				//expediteur
				$id_exp = $_SESSION['dims']['userid'];
				//dims_print_r($_SESSION['contact']['current_last_modify']); die();
				//destinataires
				$_SESSION['dims']['tickets']['users_selected'] = array();
				foreach($_SESSION['contact']['current_last_modify'] as $id_workspace => $tab_lastmod) {
					if(!isset($_SESSION['dims']['tickets']['users_selected'][$tab_lastmod['id_user']])) $_SESSION['dims']['tickets']['users_selected'][$tab_lastmod['id_user']] = $tab_lastmod['id_user'];
				}

				//ajout de la prï¿œcision sur la catï¿œgorie concernï¿œe dans le message
				$message .= "<br/><br/>".$_DIMS['cste']['_DIMS_LABEL_CAT_CONC']." : ".$categ;

				//envoi du message
				dims_tickets_send(stripslashes($subject), $message, '', '', $objet, $id_record, $obj_label, '');

				dims_redirect($redirect);

				break;

			case "savelinkbetweencts":
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_savelinkbetweencts.php");
				break;
			case "savelinkentct":
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_savelinkentct.php");
				break;
			case "modlinkbetweencts":
				ob_start();
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_modlinkbetweencts.php");
				ob_end_flush();
				die();
				break;

			case "modlkentct" :
				ob_start();
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_modlkentct.php");
				ob_end_flush();
				die();
				break;

			case "dellkentct" :
				ob_start();

				$idlink = dims_load_securvalue('id_lk',dims_const::_DIMS_CHAR_INPUT,true,true);
				$sql_del = "DELETE FROM dims_mod_business_tiers_contact WHERE id = :id ";
				$db->query($sql_del, array(
					':id' => $idlink
				));

				echo $_DIMS['cste']['_DELETED_LINK'];

				ob_end_flush();
				break;

		case "deletelinkbetweencts":
			ob_start();

			$idlink = dims_load_securvalue('id_link',dims_const::_DIMS_CHAR_INPUT,true,true);
			$sql_del = "DELETE FROM dims_mod_business_ct_link WHERE id = :id ";
			$db->query($sql_del, array(
					':id' => $idlink
				));

			echo $_DIMS['cste']['_DELETED_LINK'];

			ob_end_flush();
			die();
			break;

		case "savelinkcts" :
			@ob_end_clean();
			ob_start();
			$type_ct = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_ct = dims_load_securvalue('id_cont',dims_const::_DIMS_CHAR_INPUT,true,true);

			//avant de creer le lien, on verifie qu'il n'existe pas dï¿œjï¿œ
			$sql_l = 	"SELECT * FROM dims_mod_business_ct_link
						WHERE link_level = 2
						AND ((id_contact1 = :idcontact AND id_contact2 = :idct )
								OR (id_contact2 = :idcontact AND id_contact1 = :idct ))";

			$res_l = $db->query($sql_l, array(
				':idcontact' => $_SESSION['dims']['user']['id_contact'],
				':idct' => $id_ct,
 			));
			$nb_repl = $db->numrows($res_l);
			if($nb_repl > 0) {
				echo $_DIMS['cste']['_DIMS_LABEL_LINK_EXIST'];
			}
			else {
								require_once(DIMS_APP_PATH . "/modules/system/class_ct_link.php");
								$ctlink = new ctlink();
								$ctlink->fields['id_contact1']=$_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_contact2']=$id_ct;
								$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link']="contact";
								$ctlink->fields['link_level']=2;
								$ctlink->fields['time_create']=date("YmdHis");
								$ctlink->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_user']=$_SESSION['dims']['userid'];
								$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$ctlink->save();

				/*$sql_i = '';
				$sql_i .= "INSERT INTO `dims_mod_business_ct_link` (
										`id_contact1` ,
										`id_contact2` ,
										`id_object` ,
										`type_link` ,
										`link_level`,
										`time_create`,
										`id_ct_user_create`,
										`date_deb`,
										`date_fin`,
										`id_workspace`
										)
										VALUES (
										'".$_SESSION['dims']['user']['id_contact']."',
										'".$id_ct."',
										'".dims_const::_SYSTEM_OBJECT_CONTACT."',
										'contact',
										'2',
										'".date("YmdHis")."',
										'".$_SESSION['dims']['user']['id_contact']."',
										'',
										'',
										'".$_SESSION['dims']['workspaceid']."'
										);";
				$db->query($sql_i);*/

				echo $_DIMS['cste']['_DIMS_LABEL_LINK_OK'];
			}
			ob_end_flush();
			die();
			break;

		case "delcttowatch" :
			while(@ob_end_clean());
			ob_start();
			$type_ct = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_CHAR_INPUT,true,true);

			$sql_del = "DELETE FROM dims_mod_business_ct_watch WHERE id_user = :iduser AND id_".$type_ct." = :idct ";

			$db->query($sql_del, array(
				':iduser' 	=> $_SESSION['dims']['userid'],
				':idct'		=> $id_ct
			));
			echo _DIMS_LABEL_UNWATCH;

			ob_end_flush();
			die();
			break;
		case "generatevcard":
			@ob_end_clean();
			ob_start();
			$retour = '';

			$type_ct = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_ct = dims_load_securvalue('id_cont',dims_const::_DIMS_CHAR_INPUT,true,true);

			//recuperation de la structure des donnees
			if(!isset($convmeta) || !isset($_SESSION['dims']['contact_fields_mode'])) {
				//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer

				$sql =	"
							SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
										mb.protected,mb.name as namefield,mb.label as titlefield
							FROM		dims_mod_business_meta_field as mf
							INNER JOIN	dims_mb_field as mb
							ON			mb.id=mf.id_mbfield
							RIGHT JOIN	dims_mod_business_meta_categ as mc
							ON			mf.id_metacateg=mc.id
							WHERE		  mf.id_object = :idobject
							AND			mc.admin=1
							AND			mf.used=1
							ORDER BY	mc.position, mf.position
							";
				$rs_fields=$db->query($sql, array(
					':idobject'	=> dims_const::_SYSTEM_OBJECT_CONTACT
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

			//infos du contact
			$contact = new contact();
			$contact->open($id_ct);

			//chargement du layer correspondant au contact
			$ct_layer = new contact_layer();
			$ct_layer->open($id_ct,1,$_SESSION['dims']['workspaceid']);

			//on construit le tableau de donnees utiles pour la vcard
			$tab_vcard = array();

			$tab_vcard['lastname'] = $contact->fields['lastname'];
			$tab_vcard['firstname'] = $contact->fields['firstname'];

			//Civilite
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['civilite'] !="") {
						$tab_vcard['civilite'] = $contact->fields['civilite'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['civilite'] !="") {
						$tab_vcard['civilite'] = $ct_layer->fields['civilite'];
					}
				}
			}
			//Adresse
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['address'] !="") {
						$tab_vcard['address'] = $contact->fields['address'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['address'] !="") {
						$tab_vcard['address'] = $ct_layer->fields['address'];
					}
				}
			}
			//CP
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['postalcode'] !="") {
						$tab_vcard['postalcode'] = $contact->fields['postalcode'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['postalcode'] !="") {
						$tab_vcard['postalcode'] = $ct_layer->fields['postalcode'];
					}
				}
			}
			//Ville
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['city'] !="") {
						$tab_vcard['city'] = $contact->fields['city'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['city'] !="") {
						$tab_vcard['city'] = $ct_layer->fields['city'];
					}
				}
			}
			//Pays
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['country'] !="") {
						//on va chercher la valeur dans la table d'enum
						$pays = new enum();
						$pays->open($contact->fields['country']);

						$tab_vcard['country'] = $pays->fields['libelle'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['country'] !="") {
						//on va chercher la valeur dans la table d'enum
						$pays = new enum();
						$pays->open($ct_layer->fields['country']);

						$tab_vcard['country'] = $pays->fields['libelle'];
					}
				}
			}
			//Email
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['email'] !="") {
						$tab_vcard['email'] = $contact->fields['email'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['email'] !="") {
						$tab_vcard['email'] = $ct_layer->fields['email'];
					}
				}
			}
			//Tel bureau
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['phone'] !="") {
						$tab_vcard['phone'] = $contact->fields['phone'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['phone'] !="") {
						$tab_vcard['phone'] = $ct_layer->fields['phone'];
					}
				}
			}
			//tel mobile
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 0) {
					//c'est un champ generique -> on prend la valeur dans contact
					if($contact->fields['mobile'] !="") {
						$tab_vcard['mobile'] = $contact->fields['mobile'];
					}
				}
				else {
					//c'est un champ metier -> on prend la valeur dans un layer
					if($ct_layer->fields['mobile'] !="") {
						$tab_vcard['mobile'] = $ct_layer->fields['mobile'];
					}
				}
			}

			//creation de la vcard
			$vcard = new vCard();
			$vcard->setName($tab_vcard['lastname'], $tab_vcard['firstname'], '', $tab_vcard['civilite']);
			$vcard->setPhoneNumber($tab_vcard['phone'], "WORK");
			$vcard->setPhoneNumber($tab_vcard['mobile'], "CELL");
			$vcard->setEmail($tab_vcard['email']);
			$vcard->setAddress('','',$tab_vcard['address'], $tab_vcard['city'], '', $tab_vcard['postalcode'], $tab_vcard['country']);

			//on envoie la vcard

			$to[0]['name'] = $_SESSION['dims']['user']['lastname']." ".$_SESSION['dims']['user']['firstname'];
			$to[0]['address'] = $_SESSION['dims']['user']['email'];

			$from[0]['name'] = "I-net Portal";
			$from[0]['address'] = "";

			$subject = "Vcard from I-net Portal";

			$content = "Dear ".$_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'].", <br /><br />";
			$content .= "This is the ".$contact->fields['firstname']." ".$contact->fields['lastname']."'s Vcard.<br /> You will find it in attachement of this email.<br /><br />";
			$content .= "Best regards <br /><br />";
			$content .= "I-net Portal";


			$files[0]['name'] = $vcard->getFileName();
			$files[0]['mime-type'] = "text/x-vcard";
			$files[0]['content'] = $vcard->getVcard();


			dims_send_mail_with_files($from, $to, $subject, $content, $files);

			$retour = "La Vcard est transmise par email.";//$_DIMS['cste']['_DIMS_VCARD_SEND_OK']
			//$retour=$vcard->getVcard();

			echo $retour;
			ob_end_flush();
			die();
			break;
		case "savewatch":
			@ob_end_clean();
			ob_start();
			$retour = '';
			$type_ct = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_ct = dims_load_securvalue('id_cont',dims_const::_DIMS_CHAR_INPUT,true,true);

			//avant de faire l'insertion on verifie que la fiche n'est pas dï¿œjï¿œ en veille
			$sql_verif = "SELECT id_".$type_ct." FROM dims_mod_business_ct_watch WHERE id_user = :iduser AND id_".$type_ct."= :idct ";
			//echo $sql_verif;
			$res = $db->query($sql_verif, array(
				':iduser'	=> $_SESSION['dims']['userid'],
				':idct'		=> $id_ct
			));
			$nb_rep = $db->numrows($res);
			if($nb_rep > 0) {
				$retour =  $_DIMS['cste']['_DIMS_LABEL_WATCH_EXIST'];
			}
			else {
				$sql_i = '';
				$sql_i .= "INSERT INTO `dims_mod_business_ct_watch` (
										`id_user` ,";
				if($type_ct == "personne")	$sql_i .= "`id_personne`,"; else $sql_i .= "`id_tiers`,";
				$sql_i .= "				`time_lastseen`
										)
										VALUES (
										:userid ,
										:idct ,
										'".date("YmdHis")."'
										);";
				$db->query($sql_i, array(
					':userid'	=> $_SESSION['dims']['userid'],
					':idct'		=> $id_ct,
				));
				$retour = $_DIMS['cste']['_DIMS_LABEL_WATCH_OK'];
			}
			echo $retour;
			ob_end_flush();
			die();
			break;

		case "last_modify":
			// recherche simple en attendant l'utilisation du moteur de recherche de dims
			$sql="select		c.*,
								u.firstname as prenomcreate,
								u.lastname as nomcreate
					from		dims_mod_business_contact as c
					left join	dims_user as u
					on			u.id= c.id_user
					order by	c.timestp_modify desc limit 0,10";

			$res=$db->query($sql);

			if ($db->numrows($res)>0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;background:#FFFFFF\">
					<tbody>
					<tr class=\"fontgray\" style=\"font-size:12px;\">
						<td style=\"width: 3%;\"/>
						<td style=\"width: 25%;\">"._DIMS_LABEL_CONTACT."</td>
						<td style=\"width: 25%;\">"._DIMS_LABEL_GROUP_LIST."</td>
						<td style=\"width: 22%;\">"._DIMS_LABEL_CREATE_ON."</td>
						<td style=\"width: 20%;\">"._DIMS_LABEL_FROM."</td>
					</tr>";

				while ($cont=$db->fetchrow($res)) {
					$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
					echo "<tr bgcolor=\"".$color."\" onclick=\"document.location.href='".dims_urlencode($scriptenv."?action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$cont['id'])."\"><td></td>";
					echo "<td>".$cont['lastname']." ".$cont['firstname']."</td>";
					echo "<td>-</td>";
					//date de creation
					//$datecreate=explode("-",$cont["date_creation"]);
					$datecreate=dims_timestamp2local($cont["date_create"]);
					echo "<td>".$datecreate['date']."</td>";
					echo "<td>".$cont['nomcreate']." ".$cont['prenomcreate']."</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else echo _DIMS_NO_RESPONSE;
			die();
			break;
		case "search_ent":
			$raisoc = dims_load_securvalue("raisoc",dims_const::_DIMS_CHAR_INPUT,true,true,true);	// 5eme param ï¿œ true pour filtre sql

			// recherche simple en attendant l'utilisation du moteur de recherche de dims
			$sql="select		t.*
					from		dims_mod_business_tiers as t
					where		1=1";
			if ($raisoc!="") {
				$sql.=" and t.intitule like :intitule ";
				$res=$db->query($sql, array(
					':intitule' => $raisoc."%"
				));
			} else {
				$res=$db->query($sql);
			}


			if ($db->numrows($res)>0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;background:#FFFFFF\">
					<tbody>
						<tr class=\"fontgray\" style=\"font-size:12px;\">
							<td style=\"width: 3%;\"/>
							<td style=\"width: 25%;\">".$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']."</td>
							<td style=\"width: 25%;\">".$_DIMS['cste']['_LOCATION']."</td>
							<td style=\"width: 22%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
							<td style=\"width: 20%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
						</tr>";

					while ($cont=$db->fetchrow($res)) {
						$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
						echo "<tr bgcolor=\"".$color."\" onclick=\"document.location.href='".dims_urlencode("admin.php?action="._BUSINESS_TAB_ENT_FORM."&id_ent=".$cont['id'])."';\"><td></td>";
						echo "<td>".$cont['intitule']."</td>";
						echo "<td>".$cont['ville']."</td>";
						//date de creation
						$datecreate=explode("-",$cont["date_creation"]);
						$datecreate=$datecreate[2]."/".$datecreate[1]."/".$datecreate[0];
						//$datecreate=dims_timestamp2local($cont["date_create"]);
						echo "<td>".$datecreate."</td>";
						echo "<td>-</td>";
						echo "</tr>";
					}
				echo "</table>";
			}
			else echo _DIMS_NO_RESPONSE;
			die();
			break;
		case "search_contact":
			$lastname= dims_load_securvalue("lastname",dims_const::_DIMS_CHAR_INPUT,true,true,true);   // 5eme param ï¿œ true pour filtre sql
			$firstname= dims_load_securvalue("firstname",dims_const::_DIMS_CHAR_INPUT,true,true,true); // 5eme param ï¿œ true pour filtre sql

			// recherche simple en attendant l'utilisation du moteur de recherche de dims
			$param = array();
			$sql="select		c.*,
								u.firstname as prenomcreate,
								u.lastname as nomcreate
					from		dims_mod_business_contact as c
					left join	dims_user as u
					on			u.id= c.id_user
					where		1=1";
			if ($lastname!="") {
				$sql.=" and c.lastname like :lastname ";
				$param[':lastname'] = $lastname."%";
			}
			if ($firstname!="") {
				$sql.=" and c.firstname like :firstname ";
				$param[':firstname'] = $firstname."%";
			}

			$res=$db->query($sql, $param);

			if ($db->numrows($res)>0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;background:#FFFFFF\">
					<tbody>
						<tr class=\"fontgray\" style=\"font-size:12px;\">
							<td style=\"width: 3%;\"/>
							<td style=\"width: 25%;\">".$_DIMS['cste']['_DIMS_LABEL_CONTACT']."</td>
							<td style=\"width: 22%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
							<td style=\"width: 20%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
						</tr>";

					while ($cont=$db->fetchrow($res)) {
						$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
						echo "<tr bgcolor=\"".$color."\" onclick=\"document.location.href='".dims_urlencode("admin.php?action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$cont['id'])."';\"><td></td>";
						echo "<td>".$cont['lastname']." ".$cont['firstname']."</td>";
						//date de creation
						//$datecreate=explode("-",$cont["date_creation"]);
						$datecreate=dims_timestamp2local($cont["date_create"]);
						echo "<td>".$datecreate['date']."</td>";
						echo "<td>".$cont['nomcreate']." ".$cont['prenomcreate']."</td>";
						echo "</tr>";
					}
				echo "</table>";
			}
			else echo _DIMS_NO_RESPONSE;
			die();
			break;
		case "contact_newsearch":
			ob_end_clean();
			require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
			$dimsearch = new search($dims);
			// remise en objet du contexte courant de recherche
			$dimsearch->updateSearchObject();

			$expression		= dims_load_securvalue('expression', dims_const::_DIMS_CHAR_INPUT, true, true);
			$dimsearch->executeSearch($expression);
			die();
			break;
		default:
			$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_search.php";
			//$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_newsearch.php";
			break;
		}
	break;

	case _BUSINESS_TAB_CONTACTSTIERS:
		$title = $_DIMS['cste']['_DIMS_ADD'];

	switch($op) {

		case "search_linktoadd" :
			ob_start();
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_search_linktoadd.php');
			ob_end_flush();
			die();
			break;

		case "save_link_intelligence" :
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_save_link_intelligence.php');
			break;

		case "search_enttoadd" :
			while(@ob_end_clean());
			ob_start();
			$ent_search = dims_load_securvalue('ent_name', dims_const::_DIMS_CHAR_INPUT, true, true);

			$sql_ent = "SELECT id, intitule FROM dims_mod_business_tiers WHERE intitule LIKE :intitule ";
			$ent = $db->query($sql_ent, array(
				':intitule' => $ent_search."%"
			));
			$nb_rep = $db->numrows($ent);

			$retour = '';
			$retour .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>';

			if($nb_rep>0) {
				$retour .= '<input type="hidden" id="type_rat_ent" name="type_rat_ent" value="exist"/>
							<table width="100%">
								<tr>
									<td colspan="3" align="center">
										<select id="ent_name" name="ent_id" size="10" style="width:236px;">';
				$token->field("type_rat_ent", "exist");
				$token->field("ent_id");
				while($list_ent = $db->fetchrow($ent)) {
					$retour .= '<option value="'.$list_ent['id'].'">'.$list_ent['intitule'].'</option>';
				}
				$retour .= '			</select>
									</td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_TYPERAT'].' : </td>
									<td width="20%" align="left">
										<select id="ent_type_link" name="ent_type_link" style="width:200px;">
											<option value="emploi">emploi</option>
											<option value="associ&eacute;">associ&eacute;</option>
											<option value="r&eacute;seau">r&eacute;seau</option>
											<option value="client">client</option>
											<option value="fournisseur">fournisseur</option>
									</td>
									<td></td>
								</tr>
							</table>';
				$token->field("ent_type_link");
			}
			else {
				//formulaire de creation de l'entreprise
				$retour .= '<input type="hidden" id="type_rat_ent" name="type_rat_ent" value="create"/>
							<table width="100%">
								<tr>
									<td align="center" colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESPONSE'].'</td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_RAISONSOC'].' : </td>
									<td width="20%" align="left">
										<input type="text" id="ent_intitule" name="ent_intitule" value=""/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_TYPERAT'].' : </td>
									<td width="20%" align="left">
										<select id="ent_type_link" name="ent_type_link" style="width:200px;">
											<option value="emploi">emploi</option>
											<option value="associ&eacute;">associ&eacute;</option>
											<option value="r&eacute;seau">r&eacute;seau</option>
											<option value="client">client</option>
											<option value="fournisseur">fournisseur</option>
									</td>
									<td></td>
								</tr>
							</table>';
				$token->field("type_rat_ent", "exist");
				$token->field("ent_intitule");
				$token->field("ent_type_link");
			}
			$retour .= '</td></tr></table>';

			echo $retour;
			ob_end_flush();
			die();
			break;

		case "save_contact":
			require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_save.php");
		break;

		case 'verif_similar_pers':
			ob_start();
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_verif_similar.php');
			ob_end_flush();
			die();
		break;

		case 'show_minimal_info_ct':
			ob_start();
			$_SESSION['dims']['current_object']['id_record'] = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tabscriptenv = "admin.php?cat=".$cat;
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
					<img src="img/icon_close.gif" />
				</a>
			</div>
			<h2>&nbsp;
			</h2>
			<div style="float:left;width:330px;">
				<?
				include(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
				?>
			</div>
			<?
			unset($_SESSION['dims']['current_object']['id_record']);
			ob_end_flush();
			die();
		break;

		case 'verif_similar_ent' :
			ob_start();
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_verif_similar.php');
			ob_end_flush();
			die();
			break;
		case 'show_minimal_info_ent':
			ob_start();
			$ent_id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tabscriptenv = "admin.php?cat=".$cat;
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
					<img src="img/icon_close.gif" />
				</a>
			</div>
			<h2>&nbsp;
			</h2>
			<div style="float:left;width:330px;">
				<?
				include(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_profil.php');
				?>
			</div>
			<?
			unset($_SESSION['dims']['current_object']['id_record']);
			ob_end_flush();
			die();
		break;

		case "save_ent":
			require_once(DIMS_APP_PATH . "/modules/system/crm_public_ent_save.php");
		break;
	}
		$include = DIMS_APP_PATH . "/modules/system/crm_public_add.php";
	break;
	case _BUSINESS_TAB_CONTACTSADD:
	//$title = _DIMS_LABEL_WATCHING;
	$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_watch.php";
	break;
	case _BUSINESS_CAT_ACCUEIL:
		//$title = _DIMS_LABEL_EVENT;
		$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_event.php";
	break;

	case _BUSINESS_TAB_CONTACT_DELETE_ADMIN :
		$id_record = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true);

		switch($type) {
			case "pers" :
				$cont = new contact();
				$cont->open($id_record);
				$cont->delete();
				break;
			case "ent" :
				$ent = new tiers();
				$ent->open($id_record);
				$ent->delete();
				break;
		}

		//dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACTSSEEK);
		dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSSEEK);

	break;
   /* case _BUSINESS_TAB_CONTACT:
		//$title = _DIMS_OPTIONS;
		$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_option.php";
	break;*/
	case _BUSINESS_TAB_CONTACT_FORM :
		switch ($op) {
			case 'deletepublicshare':
				$id_share=0;
				$id_contact = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true);
				$idw = dims_load_securvalue('id_work', dims_const::_DIMS_NUM_INPUT, true);
				if ($id_contact==$_SESSION['business']['contact_id'] && $idw==$_SESSION['dims']['workspaceid']) {
					// on verifie si le lien de partage existe ou non
					$sql = "SELECT		id
							from		dims_share
							where		id_module=1
							and			id_object= :idobject
							and			id_record= :idrecord
							and			type_from=0
							and			id_from= :idfrom
							and			level_from=0";

					$res=$db->query($sql, array(
						':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT,
						':idrecord'		=> $_SESSION['business']['contact_id'],
						':idfrom'		=> $_SESSION['dims']['workspaceid']
					));

					if ($db->numrows($res)>0) {
						while ($sh=$db->fetchrow($res)) {
							$share = new share();
							$share->open($sh['id']);
							$share->delete();
						}
					}
				}
				dims_redirect($scriptenv."?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$_SESSION['business']['contact_id']."&part="._BUSINESS_TAB_CONTACT_IDENTITE);
				break;
			case 'validpublicshare':
				$id_share=0;
				$id_contact = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true);
				if ($id_contact==$_SESSION['business']['contact_id']) {
					// on verifie si le lien de partage existe ou non
					$sql = "select		id
							from		dims_share
							where		id_module=1
							and			id_object= :idobject
							and			id_record= :idrecord
							and			type_from=0
							and			id_from= :idfrom
							and			level_from=0";

					$res=$db->query($sql, array(
						':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT,
						':idrecord'		=> $_SESSION['business']['contact_id'],
						':idfrom'		=> $_SESSION['dims']['workspaceid']
					));

					if ($db->numrows($res)>0) {
						while ($sh=$db->fetchrow($res)) {
							$id_share=$sh['id'];
						}
					}

					if ($id_share==0) {
						$share = new share();
						$share->fields['id_module']=1;
						$share->fields['id_module_type']=1;
						$share->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$share->fields['id_record']=$_SESSION['business']['contact_id'];
						$share->fields['type_from']=0;
						$share->fields['id_from']=$_SESSION['dims']['workspaceid'];
						$share->fields['level_from']=0;
						$share->save();
					}
				}
				dims_redirect($scriptenv."?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$_SESSION['business']['contact_id']."&part="._BUSINESS_TAB_CONTACT_IDENTITE);
				break;
			case 'update_viewfieldshare':
			case 'refresh_viewfieldshare':
					ob_end_clean();

					// affichage des vues eventuels sur les donnees
					$id= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
					$value= dims_load_securvalue('value', dims_const::_DIMS_NUM_INPUT, true, true);

					if (isset($_SESSION['dims']['contact_fields_view'][$id])) {
						if ($op=='update_viewfieldshare') {
							// on change la valeur
							if ($value<=3) {
								$_SESSION['dims']['contact_fields_view'][$id]=$value;
							}
						}
						$continue=false;
						$selbefore=false;
						$selnext=false;
						echo "<table style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\"><tr>";

						for ($c=0;$c<=2;$c++) {
							$link='';
							$mode=$_SESSION['dims']['contact_fields_mode'][$id];
							$sel=($_SESSION['dims']['contact_fields_view'][$id]==$c);
							$selbefore=($_SESSION['dims']['contact_fields_view'][$id]==($c-1));
							$selnext=($_SESSION['dims']['contact_fields_view'][$id]==($c+1));
							$selold=($_SESSION['dims']['contact_fields_view_old'][$id]==$c);
							switch ($c) {
								case 0 : // public
									$label=$_DIMS['cste']['_DIMS_LABEL_PUBLIC'];
									if ($sel) {
										if ($mode) {
											$src="./common/img/share.png";
										}
										else {
											$src="./common/img/all.png";
										}
									}
									else {
										$link="javascript:updateValueShare(".$id.",0);";
										if ($selold) $src="./common/img/check.png";
										else {
											$src="./common/img/bullet_sel.png";
										}
									}
									$colorfield='background-color:#dbdeef;';
									break;
								case 1 : // workspace
									$label=$_DIMS['cste']['_WORKSPACE'];
									$colorfield='background-color:#dbdeef;';
									if ($sel) {
										$src="./common/img/checkdo.png";
									}
									else {
										if ($selold) {
											$src="./common/img/check.png";
											$link="javascript:updateValueShare(".$id.",1);";
										}
										else {

											if ($mode!=0) {
												$link="javascript:updateValueShare(".$id.",1);";
												$src="./common/img/bullet_sel.png";
											}
											else {
												$link='';
												$src="./common/img/bullet.png";
											}
										}
									}
									break;
								case 2 : // private
									$label=$_DIMS['cste']['_PRIVATE'];
									$colorfield='background-color:#efdedb;';
									if ($sel) {
										$src="./common/img/checkdo.png";
									}
									else {
										if ($selold) {
											$src="./common/img/check.png";
											$link="javascript:updateValueShare(".$id.",2);";
										}
										else {
											if ($mode!=0) {
												$link="javascript:updateValueShare(".$id.",2);";
												$src="./common/img/bullet_sel.png";
											}
											else {
												$link='';
												$src="./common/img/bullet.png";
											}
										}
									}
									break;
							}
							$margin="";
							if ($c>0 && $_SESSION['dims']['contact_fields_view_old'][$id]<$c) $sens="r";
							else $sens="g";

							//echo "<span style=\"float:left;".$margin."display:block;width:40px;height:17px;text-align:center;\">";
							echo "<td align=\"center\" width=\"33%\">";
							if ($link!='') echo "<a href=\"javascript:void(0);\" onclick=\"".$link."\">";
							if ($src!='') {
								echo "<img id=\"view".$id."\" title=\"".$label."\" name=\"view".$id."\" src=\"".$src."\" border=\"0\"/>";
								$token->field("view".$id);
							}
							if ($link!='') echo "</a>";
							//echo "</span>";
							echo "</td>";
						}
						echo "</tr></table>";
					}
					die();
				break;
			default:
			$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_view.php";
		}
	break;

	case _BUSINESS_TAB_ENT_FORM :
		switch($op) {
			case 'deletepublicshare':

				$id_share=0;
				$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true,true);
				$idw = dims_load_securvalue('id_work', dims_const::_DIMS_NUM_INPUT, true,true);
				if ($id_tiers==$_SESSION['business']['tiers_id'] && $idw==$_SESSION['dims']['workspaceid']) {

					// on verifie si le lien de partage existe ou non
					$sql = "select		id
							from		dims_share
							where		id_module=1
							and			id_object= :idobject
							and			id_record= :idrecord
							and			type_from=0
							and			id_from= :idfrom
							and			level_from=0";

					$res=$db->query($sql, array(
						':idobject'		=> dims_const::_SYSTEM_OBJECT_TIERS,
						':idrecord'		=> $_SESSION['business']['tiers_id'],
						':idfrom'		=> $_SESSION['dims']['workspaceid']
					));

					if ($db->numrows($res)>0) {
						while ($sh=$db->fetchrow($res)) {
							$share = new share();
							$share->open($sh['id']);
							$share->delete();
						}
					}
				}
				dims_redirect($scriptenv."?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_FORM."&id_ent=".$_SESSION['business']['tiers_id']."&part="._BUSINESS_TAB_ENT_IDENTITE);
				break;
			case 'validpublicshare':
				$id_share=0;
				$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true);
				if ($id_tiers==$_SESSION['business']['tiers_id']) {
					// on verifie si le lien de partage existe ou non
					$sql = "select		id
							from		dims_share
							where		id_module=1
							and			id_object= :idobject
							and			id_record= :idrecord
							and			type_from=0
							and			id_from= :idfrom
							and			level_from=0";

					$res=$db->query($sql, array(
						':idobject'		=> dims_const::_SYSTEM_OBJECT_TIERS,
						':idrecord'		=> $_SESSION['business']['tiers_id'],
						':idfrom'		=> $_SESSION['dims']['workspaceid']
					));

					if ($db->numrows($res)>0) {
						while ($sh=$db->fetchrow($res)) {
							$id_share=$sh['id'];
						}
					}

					if ($id_share==0) {
						$share = new share();
						$share->fields['id_module']=1;
						$share->fields['id_module_type']=1;
						$share->fields['id_object']=dims_const::_SYSTEM_OBJECT_TIERS;
						$share->fields['id_record']=$_SESSION['business']['tiers_id'];
						$share->fields['type_from']=0;
						$share->fields['id_from']=$_SESSION['dims']['workspaceid'];
						$share->fields['level_from']=0;
						$share->save();
					}
				}
				dims_redirect($scriptenv."?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_FORM."&id_ent=".$_SESSION['business']['tiers_id']."&part="._BUSINESS_TAB_ENT_IDENTITE);
				break;
			case 'update_viewfieldshare':
			case 'refresh_viewfieldshare':

					ob_end_clean();

					// affichage des vues eventuels sur les donnees
					$id= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
					$value= dims_load_securvalue('value', dims_const::_DIMS_NUM_INPUT, true, true);

					if (isset($_SESSION['dims']['tiers_fields_view'][$id])) {
						if ($op=='update_viewfieldshare') {
							// on change la valeur
							if ($value<=3) {
								$_SESSION['dims']['tiers_fields_view'][$id]=$value;
							}
						}
						$continue=false;
						$selbefore=false;
						$selnext=false;
						echo "<table style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\"><tr>";

						for ($c=0;$c<=2;$c++) {
							$link='';
							$mode=$_SESSION['dims']['tiers_fields_mode'][$id];
							$sel=($_SESSION['dims']['tiers_fields_view'][$id]==$c);
							$selbefore=($_SESSION['dims']['tiers_fields_view'][$id]==($c-1));
							$selnext=($_SESSION['dims']['tiers_fields_view'][$id]==($c+1));
							$selold=($_SESSION['dims']['tiers_fields_view_old'][$id]==$c);
							switch ($c) {
								case 0 : // public
									$label=$_DIMS['cste']['_DIMS_LABEL_PUBLIC'];
									if ($sel) {
										if ($mode) {
											$src="./common/img/share.png";
										}
										else {
											$src="./common/img/all.png";
										}
									}
									else {
										$link="javascript:updateValueShare(".$id.",0);";
										if ($selold) $src="./common/img/check.png";
										else {
											$src="./common/img/bullet_sel.png";
										}
									}
									$colorfield='background-color:#dbdeef;';
									break;
								case 1 : // workspace
									$label=$_DIMS['cste']['_WORKSPACE'];
									$colorfield='background-color:#dbdeef;';
									if ($sel) {
										$src="./common/img/checkdo.png";
									}
									else {
										if ($selold) {
											$src="./common/img/check.png";
											$link="javascript:updateValueShare(".$id.",1);";
										}
										else {
											if ($mode!=0) {
												$link="javascript:updateValueShare(".$id.",1);";
												$src="./common/img/bullet_sel.png";
											}
											else {
												$link='';
												$src="./common/img/bullet.png";
											}
										}
									}
									break;
								case 2 : // private
									$label=$_DIMS['cste']['_PRIVATE'];
									$colorfield='background-color:#efdedb;';
									if ($sel) {
										$src="./common/img/checkdo.png";
									}
									else {
										if ($selold) {
											$src="./common/img/check.png";
											$link="javascript:updateValueShare(".$id.",2);";
										}
										else {
											if ($mode!=0) {
												$link="javascript:updateValueShare(".$id.",2);";
												$src="./common/img/bullet_sel.png";
											}
											else {
												$link='';
												$src="./common/img/bullet.png";
											}
										}
									}
									break;
							}
							$margin="";
							if ($c>0 && $_SESSION['dims']['tiers_fields_view_old'][$id]<$c) $sens="r";
							else $sens="g";

							//echo "<span style=\"float:left;".$margin."display:block;width:40px;height:17px;text-align:center;\">";
							echo "<td align=\"center\" width=\"33%\">";
							if ($link!='') echo "<a href=\"javascript:void(0);\" onclick=\"".$link."\">";
							if ($src!='') {
								echo "<img id=\"view".$id."\" title=\"".$label."\" name=\"view".$id."\" src=\"".$src."\" border=\"0\"/>";
								$token->field("view".$id);
							}
							if ($link!='') echo "</a>";
							//echo "</span>";
							echo "</td>";
						}
						echo "</tr></table>";
					}
					die();
				break;
			default:
				$include = DIMS_APP_PATH . "/modules/system/crm_public_ent_view.php";
				break;
		}

	break;

	case _BUSINESS_TAB_MANAGE_DOUBLONS:
		if (dims_isadmin()) {
			switch($op) {
				case 'mergeContact':
					$id_user= dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, true);
					if (isset($_POST['doublon_from'])) {
						$doublon_from = dims_load_securvalue('doublon_from', dims_const::_DIMS_NUM_INPUT, true, true, true);
						$doublon_to = dims_load_securvalue("doublon_to",dims_const::_DIMS_NUM_INPUT,false,true,false);

						if ($doublon_to>0) {
							// on a le contact qui réceptionne le tout
							$ctdest = new contact();
							$ctdest->open($doublon_to);

							foreach ($doublon_from as $k => $ctfrom) {
								$ctdest->MergeContact($ctfrom);
							}
						}
					}
					dims_redirect('/admin.php');
					break;

				case 'detail_doublon':
					require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_detaildoublons.php');
					die();
					break;

				case 'search_doublons':
					require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_searchdoublons.php');
					die();

				break;
				default:
					$include = DIMS_APP_PATH . "/modules/system/crm_business_admin_doublon.php";
					break;
			}
		}
		break;
	case _BUSINESS_TAB_INCOMPLETE_RECORDS :
		if (dims_isadmin()) {
			switch ($op){
				default:
					$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_incomplete_records_list.php' ;
					break ;
			}
		}
		break ;

	case _BUSINESS_TAB_ADMIN:
		if (dims_isadmin()) {
			switch($op) {
				case "delete_metacateg":
					$id_metacateg= dims_load_securvalue("id_metacateg",dims_const::_DIMS_NUM_INPUT,true,true,false);
					$meta_categ = new business_metacateg();
					if ($id_metacateg>0) {
						$meta_categ->open($id_metacateg);
						$position=$meta_categ->fields['position'];
						// update position
						$db->query("UPDATE dims_mod_business_meta_categ set position=position-1 where position> :position ", array(
							':position' => $position
						));
						$meta_categ->delete();
					}
					dims_redirect($scriptenv."?op=admin_categ");
					break;
				case "savecateg":
					$id_metacateg= dims_load_securvalue("id_metacateg",dims_const::_DIMS_NUM_INPUT,true,true,false);
					$labelcateg=dims_load_securvalue("metacateg_label",dims_const::_DIMS_CHAR_INPUT,true,true,false);
					$meta_categ = new business_metacateg();
					if ($labelcateg!="") {
						if ($id_metacateg>0) {
								$meta_categ->open($id_metacateg);
								$meta_categ->setvalues($_POST, "metacateg_");
						}
						else {
							 $meta_categ->setvalues($_POST, "metacateg_");
							// on compte le nb + 1
							 $res=$db->query("SELECT * from dims_mod_business_meta_categ");
							 $position=$db->numrows($res);
							 $meta_categ->fields['position']=$position+1;
						}
						$meta_categ->save();
					}
					dims_redirect($scriptenv."?op=admin_categ");
					break;
				case "savecategposition":
					// construction de la structure courante
					$res=$db->query("SELECT * from dims_mod_business_meta_categ order by position");

					$tabcateg = array();
					$tabcategused = array();

					while ($mod=$db->fetchrow($res)) {
						$tabcateg[$mod['position']]=$mod;
					}

					$position=1;

					$elems = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
					foreach ($elems as $key=>$value) {
						if (substr($key,0,3)=="use") {
							// on extrait la valeur
							$ind=substr($key,3);
							$elem= array();
							$elem['id']=$ind;

							$elem['position']=0;
							if (isset($_POST['position'.$ind])) {
								$elem['position']=dims_load_securvalue('position'.$ind, dims_const::_DIMS_NUM_INPUT, true, true, true);
							}

							$tabcategused[$position]=$elem;
							$position++;
						}
					}

					// 1er	on regarde si on a en ajoute ou supprime un
					// si oui
					if (sizeof($tabcateg)==sizeof($tabcategused)) {
						// on regarde les changements : trois types position, used ou fposition
						$i=1;
						$taille=sizeof($tabcateg);

						$trouve=false;
						while ($i<=$taille && !$trouve) {

							$elemfrom=$tabcateg[$i];
							$elemto=$tabcategused[$i];

							// position
							if ($elemfrom['position']!=$elemto['position']) {
								$trouve=true;
								if ($elemfrom['position']<$elemto['position']) {
									$db->query("UPDATE dims_mod_business_meta_categ set position=position-1 where position> :elemfrom and position <= :elemto ", array(
										':elemfrom' => $elemfrom['position'],
										':elemto'	=> $elemto['position']
									));
									$db->query("UPDATE dims_mod_business_meta_categ set position= :position where id = :id", array(
										':position'	=> $elemto['position'],
										':id'		=> $elemto['id']
									));
								}
								else {
									$db->query("UPDATE dims_mod_business_meta_categ set position=position+1 where position < :elemfrom and position >= :elemto ", array(
										':elemfrom' => $elemfrom['position'],
										':elemto'	=> $elemto['position']
									));
									$db->query("UPDATE dims_mod_business_meta_categ set position= :position where id = :id ", array(
										':position'	=> $elemto['position'],
										':id'		=> $elemto['id']
									));
								}
							}

							$i++;
						}

					}
					dims_redirect($scriptenv."?op=admin_categ");
					break;
				case "add_metacateg":
				case "admin_categ":
					$id_metacateg= dims_load_securvalue("id_metacateg",dims_const::_DIMS_NUM_INPUT,true,true,false);
					$include = DIMS_APP_PATH . "/modules/system/crm_business_admin_categ.php";
					break;
				default:
					$include = DIMS_APP_PATH . "/modules/system/crm_business_admin.php";
				break;
			}

		}
	break;
	case _BUSINESS_TAB_CONTACT_INTELL :
		$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_intel_view.php";
		break;
	case _BUSINESS_TAB_ENT_INTELL :
		$include = DIMS_APP_PATH . "/modules/system/crm_public_ent_intel_view.php";
		break;
	case _BUSINESS_TAB_CONTACT_NEWS :
		$include = DIMS_APP_PATH . "/modules/system/crm_public_contact_news_view.php";
		break;
	case _BUSINESS_TAB_ENT_NEWS :
		$include = DIMS_APP_PATH . "/modules/system/crm_public_ent_news_view.php";
		break;
	case _BUSINESS_TAB_CONTACT_DOCS :
		$include = DIMS_APP_PATH . "/modules/system/crm_public_ct_docs_view.php";
		break;
	case _BUSINESS_TAB_IMPORT:
		require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_admin_contact_import_switch.php');
	break;

	case _BUSINESS_TAB_IMPORT_ENTREPRISES:
		$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_import_entreprises.php';
	break;

	case _BUSINESS_TAB_CONTACT_MAIL:
		$include = DIMS_APP_PATH.'modules/system/crm_public_contact_view_mail.php';
	break;

	case _BUSINESS_TAB_IMPORT_OUTLOOK:
		$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook.php';
	break;

	case _BUSINESS_TAB_CONTACT_HISTO:
		$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_historique.php';
	break;

	case _BUSINESS_TAB_CONTACT_GRAPH:
		$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_graph.php';
	break;

	case _BUSINESS_TAB_CONTACT_GROUP:
		//$include = DIMS_APP_PATH . '/modules/system/crm_public_contact_groups.php';
		$include =DIMS_APP_PATH . '/modules/system/desktop_bloc_tag_manage.php';
	break;
}
?>
<script type="text/javascript">
function affichePhoto(div) {
		var div_to_open = dims_getelem(div);
		if(div_to_open.style.display == "block") {
			div_to_open.style.display ="none";
		}
		else {
			div_to_open.style.display ="block";
		}
	}
</script>

<table cellpadding="0" cellspacing="0" width="100%"">
	<tr>
		<td>
			<?php
			if (!empty($tabs)) {
					if ($part==_BUSINESS_TAB_CONTACTSSEEK || $part=="")
							echo "<div id=\"content_onglet_transparent\" style=\"border-bottom:0px;\">";
					else {
						?>
				<script language="javascript">
					function dims_word_keyupExecContact(e) {
						e=e||window.event;
						src = (e.srcElement) ? e.srcElement : e.target; // get source field

						switch(e.keyCode) {
							case 13:
								document.form.searchcontact.submit();
							default:
								break;
							break;
						}
					}
				</script>
<?php
/*
						<form name="searchcontact" action="/admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1" method="post">
							<?
								// Sécurisation du formulaire par token
								require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
								$token = new FormToken\TokenField;
								$token->field("searchBar_obj_bar");
								$tokenHTML = $token->generate();
								echo $tokenHTML;
							?>
							<div style="float: left; width: 100%; display: block;margin:0px;">
								<div style="margin:5px auto 10px;display:block;width:900px;clear:both;">
									<input style="width:450px;" id="searchBar_obj_bar" name="searchBar_obj_bar" class="ui-button ui-autocomplete" type="text" onkeypress="javascript:dims_word_keyupExec(event);"
									 onkeyup="javascript:dims_word_keyup(event);" value="<?
										   if (mb_detect_encoding($expression_brute, "UTF-8") == "UTF-8") {
											   $expression_brute = utf8_encode($expression_brute);
										   }
										   $expr=stripslashes($expression_brute);
										   echo str_replace('"','&quot;',$expr);
										   ?>" />
									<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" onclick="searchWord();">
										<span class="ui-icon ui-icon-search"></span>
										<span class="ui-button-text">Search</span>
									</button>

									<?
									echo dims_create_button($_DIMS['cste']['_DIMS_RETURN_TO_HOME'],'./common/img/go-previous.png','javascript:document.location.href=\'/admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1&init_desktop=1\';','','');
									?>
								</div>
							</div>
						</form>

						<?
*/

							echo "<div id=\"content_onglet\" style=\"clear:both;\">";
					}
					?>
							<div id="menu_content_onglet">
							<?php
								   // echo $skin->create_onglet($tabs,$part,$part,'0',"onglet");
							echo $skin->create_toolbar($tabs,$part,$part,'0',"onglet");

							?>
							</div>
			<?
			}
			?>
			<table cellpadding="0" cellspacing="3" width="100%">
				<tr>
					<td style="vertical-align:top;">
						<?php
							// construction de la structure rubgen

							if (file_exists($include)) include($include);
							//if(!empty($view)) echo $view;
						?>
					</td>
				</tr>
			</table>
					<?php
					if (!empty($tabs)) {
					?>
					</div>
					<?
					}
					?>
		</td>
	</tr>
</table>
</div>
