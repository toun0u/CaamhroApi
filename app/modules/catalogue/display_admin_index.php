<?php
dims_init_module('system');
dims_init_module('catalogue');

include_once DIMS_APP_PATH.'/modules/catalogue/include/class_budget.php';
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_livraison.php';
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_group_livraison.php';

include_once './modules/system/class_group.php';
include_once './modules/system/class_module.php';

$group = new group();
$group->open($groupid);

$currentgroup = '';
$childgroup = '';

//if (isset($_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"]) && $_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"] != '') {
//	$currentgroup = "(" . $_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"] . ")";
//}
//
//if (isset($_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"]) && $_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"] != '') {
//	$childgroup = "(" . $_SESSION['session_modules'][_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"] . ")";
//}

$dims_moduleicon = dims_load_securvalue('dims_moduleicon', dims_const::_DIMS_CHAR_INPUT, true, true, false,$_SESSION['dims']['moduleicon']);
$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

$toolbar[_CATALOGUE_ICON_USERS]['title'] = _CATALOGUE_LABELICON_USERS;
$toolbar[_CATALOGUE_ICON_USERS]['url'] = $dims->getScriptEnv()."?dims_moduleicon="._CATALOGUE_ICON_USERS ."&op=administration";
$toolbar[_CATALOGUE_ICON_USERS]['icon'] = './common/modules/catalogue/img/tab_user.png';
$toolbar[_CATALOGUE_ICON_USERS]['width'] = '150';
$toolbar[_CATALOGUE_ICON_USERS]['id_help'] = 'system_icon_users';

$toolbar[_CATALOGUE_ICON_GROUP]['title'] = _CATALOGUE_LABELICON_GROUP ."<br/>$currentgroup";
$toolbar[_CATALOGUE_ICON_GROUP]['url'] = $dims->getScriptEnv()."?dims_moduleicon="._CATALOGUE_ICON_GROUP ."&op=administration";
$toolbar[_CATALOGUE_ICON_GROUP]['icon'] = './common/modules/catalogue/img/tab_group.png';
$toolbar[_CATALOGUE_ICON_GROUP]['width'] = '150';
$toolbar[_CATALOGUE_ICON_GROUP]['id_help'] = 'system_icon_group';

$toolbar[_CATALOGUE_ICON_HISTORY]['title'] = _CATALOGUE_LABELICON_HISTORY;
$toolbar[_CATALOGUE_ICON_HISTORY]['url'] = $dims->getScriptEnv()."?dims_moduleicon="._CATALOGUE_ICON_HISTORY ."&op=administration";
$toolbar[_CATALOGUE_ICON_HISTORY]['icon'] = './common/modules/catalogue/img/tab_historique.png';
$toolbar[_CATALOGUE_ICON_HISTORY]['width'] = '150';
$toolbar[_CATALOGUE_ICON_HISTORY]['id_help'] = 'system_icon_history';

echo '<div id="admin_content">';
echo $skin->create_toolbar($toolbar, $_SESSION['dims']['moduleicon']);

if (!isset($action)) $action = "";
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="left" valign="top">&nbsp; </td>
</tr>
<tr>
	<td align="left" valign="top">
	<?php
	switch ($_SESSION['dims']['moduleicon']) {
		// ---------------
		// ONGLET "GROUPE"
		// ---------------
		case _CATALOGUE_ICON_GROUP:
			switch ($action) {
				case 'save_group' :
					$group_id =  dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true);
					$groupinterid =  dims_load_securvalue('groupinterid', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_cata_restreint =  dims_load_securvalue('client_cata_restreint', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_afficher_prix =  dims_load_securvalue('client_afficher_prix', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_budget_non_bloquant =  dims_load_securvalue('client_budget_non_bloquant', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_change_livraison =  dims_load_securvalue('client_change_livraison', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_hors_catalogue =  dims_load_securvalue('client_hors_catalogue', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_imprimer_selection =  dims_load_securvalue('client_imprimer_selection', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_utiliser_selection =  dims_load_securvalue('client_utiliser_selection', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_statistiques =  dims_load_securvalue('client_statistiques', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_export_catalogue =  dims_load_securvalue('client_export_catalogue', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_retours =  dims_load_securvalue('client_retours', dims_const::_DIMS_NUM_INPUT, true, true);
					$client_ref_cde_oblig =  dims_load_securvalue('client_ref_cde_oblig', dims_const::_DIMS_NUM_INPUT, true, true);

					if (isset($group_id)) $group->open($group_id);
					$group->setvalues($_POST, 'group_');
					$group->save();

					$group_livraison = new group_livraison();
					$group_livraison->open($group->fields['id']);
					$group_livraison->setvalues($_POST, 'gl_');
					if ($group_livraison->fields['id_livraison'] == -1) {
						$group_livraison->delete();
					}
					else {
						$group_livraison->save();
					}

					$client = new client();
					$client->open($_SESSION['catalogue']['code_client']);
					$client->setvalues($_POST,'client_');
					$client->save();

					if ($client->fields['limite_budget'] == 1) {
						// récupération des infos du budget
						$db->query("SELECT * FROM dims_mod_vpc_budget WHERE id_group = $groupid AND id_client = '{$_SESSION['catalogue']['code_client']}' AND en_cours = 1");
						$budget_fields = $db->fetchrow();

						catalogue_budgetlog($budget_fields['id'], $groupid, 2, $budget_fields['code'], $budget_fields['valeur']);
					}

					dims_redirect($dims->getScriptEnv()."?groupid=$group_id&reloadsession&op=administration&groupinterid=". $_SESSION['catalogue']['root_group']);
					break;
				case 'create_child' :
					$child = $group->createchild();
					$groupid = $child->save();
					dims_redirect($dims->getScriptEnv()."?op=administration&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
					break;
				case 'delete_group' :
					$idfather = $group->fields['id_group'];
					$group->delete();
					$db->query("DELETE FROM dims_mod_vpc_budget WHERE id_group = $groupid AND id_client = '{$_SESSION['catalogue']['code_client']}'");
					dims_redirect($dims->getScriptEnv()."?op=administration&groupid=$idfather&groupinterid=". $_SESSION['catalogue']['root_group']);
					break;
				case 'affecter_budget':
					$groupid =  dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, true, true);
					$groupinterid =  dims_load_securvalue('groupinterid', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_budget = dims_load_securvalue('id_budget', dims_const::_DIMS_NUM_INPUT, true, true);
					$budget_id_group =  dims_load_securvalue('budget_id_group', dims_const::_DIMS_NUM_INPUT, true, true);
					$budget_en_cours =  dims_load_securvalue('budget_en_cours', dims_const::_DIMS_NUM_INPUT, true, true);
					$budget_valeur =  dims_load_securvalue('budget_valeur', dims_const::_DIMS_NUM_INPUT, true, true);

					$client = new client();
					$client->open($_SESSION['catalogue']['code_client']);
					$id_action = 1;

					if ($client->fields['budget_non_bloquant']) {
						$budget = new budget();
						if(isset($id_budget)) {
							$budget->open($id_budget);
							$id_action = 3;
						}
						$budget->setvalues($_POST,'budget_');
						$budget->fields['id_client'] = $_SESSION['catalogue']['code_client'];
						$budget->save();

						catalogue_budgetlog($budget->fields['id'], $groupid, $id_action, $budget->fields['code'], $budget->fields['valeur']);
					}
					else {
						$max_money = getAvailableMoney($groupid);

						if ($budget_valeur <= $max_money) {
							$budget = new budget();
							if(isset($id_budget) && $id_budget != '') {
								$budget->open($id_budget);
								$id_action = 3;
							}
							$budget->setvalues($_POST, 'budget_');
							$budget->fields['id_client'] = $_SESSION['catalogue']['code_client'];

							$minimumMoney = getminimummoney($groupid);
							if ($budget_valeur >= $minimumMoney) {
								$budget->save();
								catalogue_budgetlog($budget->fields['id'], $groupid, $id_action, $budget->fields['code'], $budget->fields['valeur']);
							}
							else {
								$budget->fields['valeur'] = $minimumMoney;
								$budget->save();
								catalogue_budgetlog($budget->fields['id'], $groupid, $id_action, $budget->fields['code'], $budget->fields['valeur']);
								dims_redirect($dims->getScriptEnv()."?op=administration&err=2&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group']);
							}
						}
						else {
							dims_redirect($dims->getScriptEnv()."?op=administration&err=1&groupinterid=". $_SESSION['catalogue']['root_group']);
						}
					}
					dims_redirect($dims->getScriptEnv()."?op=administration&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group']);

					break;
				case 'save_budget':
					//cyril : il manque le settage de la limite budget nan ?
					$limite_budget =  dims_load_securvalue('limite_budget', dims_const::_DIMS_NUM_INPUT, true, true);
					$budget_non_bloquant =  dims_load_securvalue('budget_non_bloquant', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_budget =  dims_load_securvalue('id_budget', dims_const::_DIMS_NUM_INPUT, true, true);
					if (!isset($limite_budget)) $limite_budget = null;
					$client = new client();
					$client->open($_SESSION['catalogue']['code_client']);
					if ($client->fields['budget_non_bloquant'] && isset($id_budget)) {
						$id_action = 2;
						$budget = new budget();
						$budget->open($id_budget);
						$budget->setvalues($_POST, 'budget_');
						$budget->fields['id_client'] = $_SESSION['catalogue']['code_client'];
						$budget->save();

						catalogue_budgetlog($budget->fields['id'], $groupid, $id_action, $budget->fields['code'], $budget->fields['valeur']);
					}
					else {
						$id_action = 1;
						$client->fields['limite_budget'] = $limite_budget;
						$client->fields['budget_non_bloquant'] = $budget_non_bloquant;
						$client->fields['budget_date_reconduction'] = dims_createtimestamp();
						$client->save();

						if ($limite_budget == 1){
							$budget = new budget();
							if (!empty($id_budget))
							{
								$budget->open($id_budget);
								$id_action = 2;
							}
							$budget->setvalues($_POST, 'budget_');
							$budget->fields['id_client'] = $_SESSION['catalogue']['code_client'];
							$minimumMoney = getminimummoney($groupid);
							if ($budget_valeur >= $minimumMoney) {
								$budget->save();
							}
							else {
								$budget->fields['valeur'] = $minimumMoney;
								$budget->save();
								dims_redirect($dims->getScriptEnv()."?op=administration&err=2&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group']);
							}
							catalogue_budgetlog($budget->fields['id'], $groupid, $id_action, $budget->fields['code'], $budget->fields['valeur']);
						}
					}
 					dims_redirect($dims->getScriptEnv()."?op=administration&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group']);
					break;
				case 'rectif_budget':
					$budget_id_group =  dims_load_securvalue('budget_id_group', dims_const::_DIMS_CHAR_INPUT, true, true);
					$budget_nv = dims_load_securvalue('budget_nv', dims_const::_DIMS_NUM_INPUT, true, true);
					$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true);
					$id_budget = dims_load_securvalue('id_budget', dims_const::_DIMS_CHAR_INPUT, true, true);
					$budget_orig = dims_load_securvalue('budget_orig', dims_const::_DIMS_CHAR_INPUT, true, true);
					$grp = new group();
					$grp->open($budget_id_group);

					$realbudget = getrealbudget($budget_id_group);
					if ($budget_nv > $realbudget) {
						$budget_nv = $realbudget;
						$err2 = 5;
					}

					$ref = $grp->fields['label'];
					$articles[$ref]['des'] = $grp->fields['label'];
					$articles[$ref]['pu'] = $budget_orig - $budget_nv;
					$articles[$ref]['qte'] = 1;

					include_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
					$commande = new commande();
					$commande->fields['hors_cata'] = 1;
					$commande->fields['id_group'] = $budget_id_group;
					$commande->fields['ref_client'] = $_SESSION['catalogue']['code_client'];
					$commande->fields['libelle'] = "Budget rectifié";
					$commande->fields['commentaire'] = htmlentities($commentaire, ENT_QUOTES);
					$commande->fields['user_name'] = $_SESSION['catalogue']['client_firstname'] .' '. $_SESSION['catalogue']['client_lastname'];
					$commande->fields['id_user'] = $_SESSION['dims']['userid'];
					$commande->fields['id_budget'] = $id_budget;
					$commande->fields['etat'] = "validee";
					$commande->articles = $articles;
					$commande->save();

					include_once DIMS_APP_PATH.'/modules/catalogue/include/class_budget.php';
					$budget = new budget();
					$budget->open($id_budget);

					catalogue_budgetlog($id_budget, $_SESSION['catalogue']['root_group'], 6, $budget->fields['code'], $budget->fields['valeur']);

					$redirect = $dims->getScriptEnv()."?op=administration&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group'];
					if (isset($err2)) $redirect .= "&err2=$err2";
					dims_redirect($redirect);
					break;
				case 'close_budget':
					$id_budget = dims_load_securvalue('id_budget', dims_const::_DIMS_NUM_INPUT, true, true);
					$client = new client();
					$client->open($_SESSION['catalogue']['code_client']);
					$client->fields['limite_budget'] = null;
					$client->save();

					if (isset($id_budget) && $id_budget != '') {
						$budget = new budget();
						$budget->open($id_budget);
						$budget->fields['en_cours'] = 0;
						$budget->save();

						catalogue_budgetlog($budget->fields['id'], $groupid, 4, $budget->fields['code']);
					}

					// On close le budegt de tous les utilisateurs du groupe
					$sql = "
						UPDATE dims_user u, dims_group_user gu
						SET u.limite_budget = 0
						WHERE u.id = gu.id_user
						AND gu.id_group = $groupid";
					$db->query($sql);
					$sql = "
						UPDATE dims_mod_vpc_user_budget ub, dims_group_user gu
						SET ub.en_cours = 0
						WHERE ub.id_user = gu.id_user
						AND gu.id_group = $groupid";
					$db->query($sql);

					// On clos le budget de tous les sous-groupes
					$group = new group();
					$group->open($_SESSION['catalogue']['root_group']);
					$children = $group->getgroupchildrenlite();
					if (is_array($children) && count($children)) {
						foreach ($children as $key) {
							$sql = "
								UPDATE dims_mod_vpc_budget
								SET en_cours = 0
								WHERE id_group = $key
								AND id_client = '{$_SESSION['catalogue']['code_client']}'";
							$db->query($sql);
							catalogue_budgetlog($budget->fields['id'], $key, 4, $budget->fields['code']);

							// On close le budegt de tous les utilisateurs du sous-groupe
							$sql = "
								UPDATE dims_user u, dims_group_user gu
								SET u.limite_budget = 0
								WHERE u.id = gu.id_user
								AND gu.id_group = $key";
							$db->query($sql);
							$sql = "
								UPDATE dims_mod_vpc_user_budget ub, dims_group_user gu
								SET ub.en_cours = 0
								WHERE ub.id_user = gu.id_user
								AND gu.id_group = $key";
							$db->query($sql);
						}
					}

					dims_redirect($dims->getScriptEnv()."?groupid=$groupid&op=administration&reloadsession&groupinterid=". $_SESSION['catalogue']['root_group']);
					break;
				default :
					include_once DIMS_APP_PATH.'/modules/catalogue/display_admin_index_group.php';
					break;
			}
			break;
		// ---------------------
		// USER MANAGEMENT
		// ---------------------
		case _CATALOGUE_ICON_USERS:

			include_once DIMS_APP_PATH.'/modules/catalogue/display_admin_index_users.php';
			break;
		case _CATALOGUE_ICON_HISTORY:
			include_once DIMS_APP_PATH.'/modules/catalogue/display_admin_index_history.php';
			break;
	default: break;
	} // switch
	?>
	</td>
</tr>
</table>
</div>
