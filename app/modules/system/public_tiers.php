<?
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
//require_once(DIMS_APP_PATH . '/modules/system/class_dossier.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . '/modules/system/class_interlocuteur.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_interlocuteur.php');
require_once(DIMS_APP_PATH . '/modules/system/action_detail.php');
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');

$tiers = new tiers();

$reset=dims_load_securvalue('reset',dims_const::_DIMS_NUM_INPUT,true,true);

if ($reset>0 && isset($_SESSION['business']['tiers_id'])) unset($_SESSION['business']['tiers_id']);

if (!isset($_SESSION['business']['tiers_id'])) $_SESSION['business']['tiers_id'] = '';

$tiers_id=dims_load_securvalue('tiers_id',dims_const::_DIMS_NUM_INPUT,true,true);

if ($tiers_id>0)  $_SESSION['business']['tiers_id'] = $tiers_id;

$_SESSION['business']['tiers']['actions'] = array();

$select =	"
			SELECT		a.*
			FROM		dims_mod_business_action a
			WHERE		a.tiers_id = :idtiers
			ORDER BY	datejour DESC, heuredeb DESC, heurefin DESC
			";

$res=$db->query($select, array(
	':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
));

while ($fields = $db->fetchrow($res)) {
	$_SESSION['business']['tiers']['actions'][$fields['id']] = $fields['id'];
	//if ($fields['tiers_id'] == $_SESSION['business']['tiers_id']) $_SESSION['business']['tiers']['actions'][$fields['id']] = $fields['id'];
}

if (!isset($_SESSION['dims']['moduletabid'])) $_SESSION['dims']['moduletabid'] =_BUSINESS_TAB_TIERSSEEK;

if ($_SESSION['dims']['moduletabid'] == _BUSINESS_TAB_TIERSADD || $_SESSION['dims']['moduletabid'] == _BUSINESS_TAB_TIERSSEEK) $_SESSION['business']['tiers_id'] = '';

if ($_SESSION['business']['tiers_id']) {
	if (!$tiers->open($_SESSION['business']['tiers_id']))  {// erreur lors de l'ouverture => tiers supprim�
		$_SESSION['business']['tiers_id'] = '';
	}
}

$tabscriptenv = "$scriptenv?cat="._BUSINESS_CAT_TIERS;

if ($op == 'tiers_export')	include(DIMS_APP_PATH . '/modules/system/public_tiers_export.php');

if ($_SESSION['business']['tiers_id']) {
	$form_title = _BUSINESS_LABEL_TIERS." &#187; {$tiers->fields['intitule']} / {$tiers->fields['typeclient']}&nbsp;(".business_format_ref($tiers->fields['id']).")";

	$tabs[_BUSINESS_TAB_TIERSINFORMATIONS]['title'] = _BUSINESS_LABEL_INFORMATIONS;
	$tabs[_BUSINESS_TAB_TIERSINFORMATIONS]['icon'] =  _BUSINESS_ICO_GENERALITES;
	$tabs[_BUSINESS_TAB_TIERSINFORMATIONS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS;
	$tabs[_BUSINESS_TAB_TIERSINFORMATIONS]['width'] = 80;

	$tabs[_BUSINESS_TAB_TIERSDETAILS]['title'] = _BUSINESS_LABEL_DETAILS;
	$tabs[_BUSINESS_TAB_TIERSDETAILS]['icon'] = _BUSINESS_ICO_DETAILS;
	$tabs[_BUSINESS_TAB_TIERSDETAILS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSDETAILS;
	$tabs[_BUSINESS_TAB_TIERSDETAILS]['width'] = 60;

	$tabs[_BUSINESS_TAB_TIERSINTERLOCUTEURS]['title'] = _BUSINESS_INTERLOCUTEUR;
	$tabs[_BUSINESS_TAB_TIERSINTERLOCUTEURS]['icon'] =	_BUSINESS_ICO_INTERLOCUTEUR;
	$tabs[_BUSINESS_TAB_TIERSINTERLOCUTEURS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSINTERLOCUTEURS;
	$tabs[_BUSINESS_TAB_TIERSINTERLOCUTEURS]['width'] = 90;

	$tabs[_BUSINESS_TAB_TIERSPROJECTS]['title'] = _BUSINESS_DOSSIER;
	$tabs[_BUSINESS_TAB_TIERSPROJECTS]['icon'] = _BUSINESS_ICO_DOSSIER;
	$tabs[_BUSINESS_TAB_TIERSPROJECTS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSPROJECTS;
	$tabs[_BUSINESS_TAB_TIERSPROJECTS]['width'] = 60;

	$tabs[_BUSINESS_TAB_TIERSACTIONS]['title'] = _BUSINESS_ACTION;
	$tabs[_BUSINESS_TAB_TIERSACTIONS]['icon'] =  _BUSINESS_ICO_ACTION;
	$tabs[_BUSINESS_TAB_TIERSACTIONS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSACTIONS;
	$tabs[_BUSINESS_TAB_TIERSACTIONS]['width'] = 60;

	if (_BUSINESS_USE_SUIVIS) {
		$tabs[_BUSINESS_TAB_TIERSSUIVIS]['title'] = _BUSINESS_LABEL_SUIVIS;
		$tabs[_BUSINESS_TAB_TIERSSUIVIS]['icon'] =	_BUSINESS_ICO_DOC;
		$tabs[_BUSINESS_TAB_TIERSSUIVIS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSSUIVIS;
		$tabs[_BUSINESS_TAB_TIERSSUIVIS]['width'] = 50;
	}

	$tabs['']['title'] = _BUSINESS_DELETE;
	$tabs['']['icon'] =  _BUSINESS_ICO_SUPPRIMER;
	$tabs['']['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSSEEK."&op=tiers_effacer&delete_id={$_SESSION['business']['tiers_id']}";
	$tabs['']['width'] = 70;
	$tabs['']['confirm'] = _DIMS_CONFIRM;

	$datejour = date(_dims_const::DIMS_DATEFORMAT_US);

}
else {
	$tiers->init_description();
	$form_title = _BUSINESS_LABEL_TIERS;
}

$tabs[_BUSINESS_TAB_TIERSSEEK]['title'] = _BUSINESS_LABEL_SEEK;
$tabs[_BUSINESS_TAB_TIERSSEEK]['url'] = dims_urlencode("$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSSEEK);
$tabs[_BUSINESS_TAB_TIERSSEEK]['icon'] = _BUSINESS_ICO_RECHERCHE;
$tabs[_BUSINESS_TAB_TIERSSEEK]['width'] = 80;
$tabs[_BUSINESS_TAB_TIERSSEEK]['position'] = 'right';

$tabs[_BUSINESS_TAB_TIERSADD]['title'] = _BUSINESS_LABEL_ADD;
$tabs[_BUSINESS_TAB_TIERSADD]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSADD;
$tabs[_BUSINESS_TAB_TIERSADD]['icon'] = _BUSINESS_ICO_TIERS_AJOUT;
$tabs[_BUSINESS_TAB_TIERSADD]['width'] = 70;
$tabs[_BUSINESS_TAB_TIERSADD]['position'] = 'right';

$tabs[_BUSINESS_CAT_ACCUEIL]['title'] = _BUSINESS_BACK_ACCUEIL;
$tabs[_BUSINESS_CAT_ACCUEIL]['icon'] = _BUSINESS_ICO_ACCUEIL;
$tabs[_BUSINESS_CAT_ACCUEIL]['url'] = "$tabscriptenv&dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS."&cat=-1";
$tabs[_BUSINESS_CAT_ACCUEIL]['width'] = 70;
$tabs[_BUSINESS_CAT_ACCUEIL]['position'] = 'right';

$tabs[_BUSINESS_TAB_CONTACT]['title'] = _BUSINESS_INTERLOCUTEUR;
$tabs[_BUSINESS_TAB_CONTACT]['url'] = "{$scriptenv}?dims_action=public&cat="._BUSINESS_CAT_INTERLOCUTEUR."&reset=1";
$tabs[_BUSINESS_TAB_CONTACT]['icon'] = "./common/modules/system/img/contact.png";
$tabs[_BUSINESS_TAB_CONTACT]['width'] = 80;
$tabs[_BUSINESS_TAB_CONTACT]['position'] = 'right';

//require_once 'public_dernieresfiches.php';

$form_title = '<div style="float:left;padding-top:1px;">'.$form_title.'</div><div style="float:left;padding-left:8px;">'.dims_tickets_new(dims_const::_SYSTEM_OBJECT_TIERS, $tiers->fields['id'], $tiers->fields['intitule']).'</div>';

echo $skin->open_simplebloc($form_title,'border:2px solid '._BUSINESS_COLOR_TIERS.';','background-color:'._BUSINESS_COLOR_TIERS.';color:#ffffff;');
?>

<table cellpadding="0" cellspacing="0" width="100%">
<tr bgcolor="<? echo $skin->values['bgline2']; ?>">
	<td style="padding:2px;">
	<?
	echo $skin->create_toolbar($tabs,$_SESSION['dims']['moduletabid'],true,false);
	?>
	</td>
</tr>
<!--tr bgcolor="<? echo $skin->values['bgline1']; ?>">
	<td colspan="2">
	<?

	switch($_SESSION['dims']['moduletabid'])
	{
		case _BUSINESS_TAB_TIERSINFORMATIONS:
			$title = _BUSINESS_LABEL_INFORMATIONS;
		break;
		case _BUSINESS_TAB_TIERSDETAILS:
			$title = _BUSINESS_LABEL_DETAILS;
		break;
		case _BUSINESS_TAB_TIERSEQUIPCOMP:
			$title = _BUSINESS_LABEL_EQUIPCOMP;
		break;
		case _BUSINESS_TAB_TIERSINTERLOCUTEURS:
			$title = _BUSINESS_INTERLOCUTEUR;
		break;
		case _BUSINESS_TAB_TIERSPROJECTS:
			$title = _BUSINESS_DOSSIER;
		break;
		case _BUSINESS_TAB_TIERSACTIONS:
			$title = _BUSINESS_ACTION;
		break;
		case _BUSINESS_TAB_TIERSSEEK:
			$title = _BUSINESS_LABEL_SEEK;
		break;
		case _BUSINESS_TAB_TIERSADD:
			$title = _BUSINESS_LABEL_ADD;
		break;
		case _BUSINESS_TAB_TIERSSUIVIS:
			$title = _BUSINESS_LABEL_SUIVIS;
		break;
	}
	?>
		<table cellpadding="2" cellspacing="0" width="100%">
			<tr>
				<td align="left"><? echo "&nbsp;<b>$form_title &#187; $title</b>"; ?></td>
				<?
				if (isset($_SESSION['business']['tiers_id']) && $_SESSION['business']['tiers_id'])
				{
					?>
					<td align="right"><b>[&nbsp;<a class="delete" href="javascript:dims_confirmlink('<? echo "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_TIERSSEEK."&op=tiers_effacer&delete_id={$_SESSION['business']['tiers_id']}"; ?>','�tes-vous certain de vouloir supprimer cette fiche ?')">Supprimer ce Client</a>&nbsp;]&nbsp;&nbsp;</b></td>
					<?
				}
				?>
			</tr>
		</table>
	</td>
</tr-->
<tr>
	<td style="padding:2px;">
	<?
	switch($_SESSION['dims']['moduletabid']) {

		// RECHERCHE TIERS
		case _BUSINESS_TAB_TIERSSEEK:

			switch($op) {
				case 'tiers_effacer':
					$tiers_delete = new tiers();
					$tiers_delete->open($delete_id);
					$tiers_delete->delete();
					dims_redirect("$scriptenv");
				break;

				default :
					include(DIMS_APP_PATH . '/modules/system/public_tiers_recherche.php');
				break;
			}
		break;

		// AJOUT TIERS
		case _BUSINESS_TAB_TIERSADD:
			switch($op)
			{
				case 'tiers_enregistrer':
					if ($tiers_id == 0) // nouveau tiers
					{
						$tiers->setvalues($_POST,'tiers_');
						if (!isset($tiers_actif)) $tiers->fields['actif'] = "Oui";
						$tiers->setugm();
						$tiers_id = $tiers->save();
					}
					dims_redirect("$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS."&op=tiers_ouvrir&tiers_id=$tiers_id");
				break;

				default :
					include(DIMS_APP_PATH . '/modules/system/public_tiers_ajout.php');
				break;
			}
		break;

		// TIERS / ONGLET GENERALITES
		case _BUSINESS_TAB_TIERSINFORMATIONS:

			switch($op)
			{
				case 'tiers_enregistrer':
					$tiers->setvalues($_POST,'tiers_');
					if (!isset($tiers_actif)) $tiers->fields['actif'] = "Non";
					$tiers->save();
					dims_redirect($scriptenv);
				break;

				case 'tiers_ouvrir':

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_generalites.php');
				break;
			}
		break;

		// TIERS / ONGLET EQUIP/COMP
		case _BUSINESS_TAB_TIERSEQUIPCOMP:
			switch($op)
			{
				case 'tiers_ajoutercompetence':
					$tiers_competence = new tiers_competence();
					$tiers_competence->open($tiers_id, $competence_code);
					$tiers_competence->fields['tiers_id'] = $tiers_id;
					$tiers_competence->fields['competence_code'] = $competence_code;
					$tiers_competence->save();
					dims_redirect($scriptenv);
				break;

				case 'tiers_ajouterequipement':
					$tiers_equipement = new tiers_equipement();
					$tiers_equipement->open($tiers_id, $equipement_code);
					$tiers_equipement->fields['tiers_id'] = $tiers_id;
					$tiers_equipement->fields['equipement_code'] = $equipement_code;
					$tiers_equipement->save();
					dims_redirect($scriptenv);
				break;

				case 'tiers_supprimercompetence':
					$tiers_competence = new tiers_competence();
					$tiers_competence->open($tiers_id, $competence_code);
					$tiers_competence->delete();
					dims_redirect($scriptenv);
				break;

				case 'tiers_supprimerequipement':
					$tiers_equipement = new tiers_equipement();
					$tiers_equipement->open($tiers_id, $equipement_code);
					$tiers_equipement->delete();
					dims_redirect($scriptenv);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_equipcomp.php');
				break;
			}
		break;

		// TIERS / ONGLET DETAILS
		case _BUSINESS_TAB_TIERSDETAILS:
			switch($op) {
				case 'tiers_enregistrer':
					$tiers->setvalues($_POST,'tiers_');
					if (isset($tiers_ent_datecreation)) $tiers->fields['ent_datecreation'] = business_datefr2us($tiers_ent_datecreation);
					if (!isset($tiers_ent_hebergee)) $tiers->fields['ent_hebergee'] = "non";
					if (isset($tiers_cre_datenaissance)) $tiers->fields['cre_datenaissance'] = business_datefr2us($tiers_cre_datenaissance);
					if (!isset($tiers_cre_issu_recherche)) $tiers->fields['cre_issu_recherche'] = "non";
					if (!isset($tiers_cre_heb_libreservice)) $tiers->fields['cre_heb_libreservice'] = "non";
					if (!isset($tiers_cre_heb_bureau)) $tiers->fields['cre_heb_bureau'] = "non";
					if (!isset($tiers_cre_heb_pepiniere)) $tiers->fields['cre_heb_pepiniere'] = "non";
					$tiers->save();
					dims_redirect($scriptenv);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_details.php');
				break;
			}
		break;

		// TIERS / ONGLET INTERLOCUTEURS
		case _BUSINESS_TAB_TIERSINTERLOCUTEURS:
			switch($op)
			{
				case 'interlocuteur_couper':
					$tiers_interlocuteur = new tiers_interlocuteur();
					$tiers_interlocuteur->open($_SESSION['business']['tiers_id'],$interlocuteur_id);
					$tiers_interlocuteur->delete();
					dims_redirect($scriptenv);
				break;

				case 'interlocuteur_ouvrir':
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_interlocuteurs_modifier.php');
				break;

				case 'interlocuteur_enregistrer':
					$interlocuteur = new interlocuteur();
					$tiers_interlocuteur = new tiers_interlocuteur();

					$interlocuteur->open($interlocuteur_id);
					$tiers_interlocuteur->open($_SESSION['business']['tiers_id'],$interlocuteur_id);

					$interlocuteur->setvalues($_POST,'interlocuteur_');
					$tiers_interlocuteur->setvalues($_POST,'tiersinterlocuteur_');

					$interlocuteur->save();
					$tiers_interlocuteur->save();

					$db->query("DELETE FROM dims_mod_business_interlocuteur_categorie WHERE id_interlocuteur = :idinterlocutor", array(
						':idinterlocutor' => array('type' => PDO::PARAM_INT, 'value' => $interlocuteur->getId()),
					));
					foreach($categorie_interlocuteur as $categorie)
					{
						$db->query("INSERT INTO dims_mod_business_interlocuteur_categorie SET id_interlocuteur = :idinterlocutor, categorie = :categorie", array(
							':idinterlocutor' => array('type' => PDO::PARAM_INT, 'value' => $interlocuteur->getId()),
							':categorie' => array('type' => PDO::PARAM_STR, 'value' => $categorie),
						));
					}
					//dims_redirect("$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSINTERLOCUTEURS);
					dims_redirect($scriptenv);
				break;

				case 'interlocuteur_ajouter':
				case 'interlocuteur_ajout1':
				case 'interlocuteur_ajout2':
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_interlocuteurs_ajouter.php');
				break;

				case 'interlocuteur_ajout3':
					$interlocuteur = new interlocuteur();
					$tiers_interlocuteur = new tiers_interlocuteur();

					if ($interlocuteur_id)
					{
						$interlocuteur->open($interlocuteur_id);
						$tiers_interlocuteur->open($_SESSION['business']['tiers_id'],$interlocuteur_id);
					}
					else
					{
						$interlocuteur->setugm();
						$tiers_interlocuteur->setugm();
					}

					$interlocuteur->setvalues($_POST,'interlocuteur_');
					$tiers_interlocuteur->setvalues($_POST,'tiersinterlocuteur_');

					$interlocuteur->save();

					$tiers_interlocuteur->fields['interlocuteur_id'] = $interlocuteur->fields['id'];
					$tiers_interlocuteur->fields['tiers_id'] = $_SESSION['business']['tiers_id'];
					$tiers_interlocuteur->save();

					dims_redirect($scriptenv);
					//dims_redirect("$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSINTERLOCUTEURS);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_interlocuteurs.php');
				break;
			}
		break;

		// TIERS / ONGLET DOSSIERS
		case _BUSINESS_TAB_TIERSPROJECTS:
			switch($op)
			{
				case 'project_enregistrer':
					$project_id=dims_load_securvalue("id_project",dims_const::_DIMS_NUM_INPUT,false,true);

					$tiers_project = new tiers_project();

					if ($project_id>0) {
						$project->open($project_id);
						$tiers_project->open($_SESSION['business']['tiers_id'], $project_id);

						$tiers_project->setvalues($_POST,'tiersproject_');
						$tiers_project->fields['project_id'] = $project->fields['id'];
						$tiers_project->fields['tiers_id'] = $_SESSION['business']['tiers_id'];

						$tiers_project->save();
					}
					dims_redirect($scriptenv);
				break;

				case 'project_ouvrir':
					dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRPROJECT, $project_id);
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_projects_modifier.php');
				break;

				case 'project_ajouter':
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_projects_ajouter.php');
				break;

				case 'project_effacer':
					if (isset($project_id)) {
						//$project = new project();
						$tiers_project = new tiers_project();

						//$project->open($project_id);
						$tiers_project->open($_SESSION['business']['tiers_id'], $project_id);

						//$project->delete();
						$tiers_project->delete();

						dims_redirect($scriptenv);
					}
				break;

				case 'project_couper':
					$project_id=dims_load_securvalue("project_id",dims_const::_DIMS_NUM_INPUT,true,true);
					if ($project_id>0) {
						$tiers_project = new tiers_project();
						$tiers_project->open($_SESSION['business']['tiers_id'], $project_id);
						$tiers_project->delete();
						dims_redirect($scriptenv);
					}
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiersform_projects.php');
				break;
			}
		break;

		// TIERS / ONGLET ACTIONS
		case _BUSINESS_TAB_TIERSACTIONS:
			switch($op)
			{
				case 'action_dupliquer':
				case 'action_ajouter':
					include(DIMS_APP_PATH . '/modules/system/public_tiers_actions_ajouter.php');
				break;

				case 'action_enregistrer':
					$action = new action();
					if (isset($action_id)) $action->open($action_id);
					else $action->setugm(); // nouvelle action

					$action->setvalues($_POST,'action_');
					$action->fields['datejour'] = business_datefr2us($action->fields['datejour']);

					$action->fields['heuredeb'] = sprintf("%02d:%02d:00",$actionx_heuredeb_h,$actionx_heuredeb_m);

					if ($actionx_duree) // > 0 => calcul heure de fin en fonction de dur�e
					{
						$action->fields['temps_prevu'] = $actionx_duree;
						$heurefin = $actionx_heuredeb_h*60+$actionx_heuredeb_m+$action->fields['temps_prevu'];
						$heurefin_h = ($heurefin-$heurefin%60)/60;
						$heurefin_m = $heurefin%60;
						$action->fields['heurefin'] = sprintf("%02d:%02d:00",$heurefin_h,$heurefin_m);
					}
					else // r�cup la saisie de l'heure de fin
					{
						$action->fields['heurefin'] = sprintf("%02d:%02d:00",$actionx_heurefin_h,$actionx_heurefin_m);
						$action->fields['temps_prevu'] = ($actionx_heurefin_h-$actionx_heuredeb_h)*60+$actionx_heurefin_m-$actionx_heuredeb_m;
					}

					$action->fields['temps_passe'] = $action->fields['temps_prevu'];

					$action->fields['tiers_id'] = $_SESSION['business']['tiers_id'];
					if (isset($actiondetail_project_id)) $action->dossiers = $actiondetail_project_id;
					if (isset($actionutilisateur_id)) $action->utilisateurs = $actionutilisateur_id;

					$action->save();

					dims_redirect($scriptenv);
				break;

				case 'action_affecter':
					include(DIMS_APP_PATH . '/modules/system/public_tiers_actions_affecter.php');
				break;

				case 'action_affecter_suite':

					if (isset($nouveau_dossier) && $nouveau_dossier = "1" && $rech_dossier != '')
					{
						$project = new project();
						$project->fields['objet_dossier'] = $rech_dossier;
						$project->setugm();
						$actiondetail_project_id = $project->save();
						dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRPROJECT, $actiondetail_project_id);
					}
					if (isset($nouveau_tiers) && $nouveau_tiers = "1" && $rech_tiers != '')
					{
						$tiers = new tiers();
						$tiers->fields['intitule'] = $rech_tiers;
						$tiers->setugm();
						$actiondetail_tiers_id = $tiers->save();
						dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRTIERS, $actiondetail_tiers_id);
					}

					$action = new action();
					if (isset($action_id)) $action->open($action_id);
					else $action->setugm(); // nouvelle action

					if ($choix == 'tiers')
					{
						$action->fields['tiers_id'] = $actiondetail_tiers_id;
						$action->fields['project_id'] = '';
						if (isset($actiondetail_project_id))
						{
							$action->dossiers = explode(',',$actiondetail_project_id);
							foreach($action->dossiers as $id)
							{
								$tiers_project = new tiers_project();
								$tiers_project->open($actiondetail_tiers_id, $id);
								$tiers_project->fields['tiers_id'] = $actiondetail_tiers_id;
								$tiers_project->fields['project_id'] = $id;
								$tiers_project->save();
							}
						}
						//if (isset($actionutilisateur_id)) $action->utilisateurs = $actionutilisateur_id;
					}
					else //dossier
					{
						$action->fields['project_id'] = $actiondetail_project_id;
						$action->fields['tiers_id'] = '';
						if (isset($actiondetail_tiers_id))
						{
							$action->tiers = explode(',',$actiondetail_tiers_id);
							foreach($action->tiers as $id)
							{
								$tiers_project = new tiers_project();
								$tiers_project->open($id, $actiondetail_project_id);
								$tiers_project->fields['tiers_id'] = $id;
								$tiers_project->fields['project_id'] = $actiondetail_project_id;
								$tiers_project->save();
							}
						}
						//if (isset($actionutilisateur_id)) $action->utilisateurs = $actionutilisateur_id;
					}

					$action->save();
					dims_redirect($scriptenv);
				break;
				case 'action_modifier':
					include(DIMS_APP_PATH . '/modules/system/public_tiers_actions_modifier.php');
				break;

				case 'action_supprimer':
					$action = new action();
					$action->open($action_id);
					$action->delete();
					dims_redirect($scriptenv);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiers_actions.php');
				break;
			}
		break;



		case _BUSINESS_TAB_TIERSSUIVIS:
			switch($op)
			{
				case 'suivi_detail_movedown':
				case 'suivi_detail_moveup':

					$suividetail = new suividetail();
					$suividetail->open($suivi_detail_id);

					$select = "	SELECT min(position) as minpos, max(position) as maxpos
								FROM dims_mod_business_suivi_detail
								WHERE suivi_id = :idsuivi
								AND suivi_type = :typesuivi
								AND suivi_exercice = :exercicesuivi
								AND id_workspace = :idworkspace";
					$db->query($select, array(
						':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
						':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
						':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					));
					$fields = $db->fetchrow();

					if ($fields['maxpos'] == 0 || $fields['minpos'] == 0) //pb de positionnement (import des donn�es)
					{
						$select = "	SELECT id
									FROM dims_mod_business_suivi_detail
									WHERE suivi_id = :idsuivi
									AND suivi_type = :typesuivi
									AND suivi_exercice = :exercicesuivi
									AND id_workspace = :idworkspace";
						$db->query($select, array(
							':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
							':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
							':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						$pos = 1;
						while ($fields = $db->fetchrow())
						{
							$svdet = new suividetail();
							$svdet->open($fields['id']);
							$svdet->fields['position'] = $pos++;
							$svdet->save();
						}
					}
					else
					{
						if ($op == 'suivi_detail_movedown')
						{
							if ($fields['maxpos'] != $suividetail->fields['position']) // ce n'est pas le dernier champ
							{
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=0
											WHERE position=:position
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':position' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position'] + 1),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=:newposition
											WHERE position=:oldposition
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':newposition' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position'] + 1),
									':oldposition' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position']),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=:position
											WHERE position=0
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':position' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position']),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
							}
						}
						else
						{
							if ($fields['minpos'] != $suividetail->fields['position']) // ce n'est pas le premier champ
							{
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=0
											WHERE position= :position
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':position' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position'] - 1),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=:newposition
											WHERE position=:oldposition
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':newposition' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position'] - 1),
									':oldposition' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position']),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								$db->query("UPDATE dims_mod_business_suivi_detail
											SET position=:position
											WHERE position=0
											AND suivi_id = :idsuivi
											AND suivi_type = :typesuivi
											AND suivi_exercice = :exercicesuivi
											AND id_workspace = :idworkspace", array(
									':position' => array('type' => PDO::PARAM_INT, 'value' => $suividetail->fields['position']),
									':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_id),
									':typesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_type),
									':exercicesuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi_exercice),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
							}
						}
					}
					dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi_id}&suivi_type={$suivi_type}&suivi_exercice={$suivi_exercice}");
				break;

				case 'suivi_verser':
					if (isset($montant) && is_numeric($montant) && $montant > 0)
					{

						$suivi = new suivi();
						$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
						$versement = new versement();
						$versement->fields['date_paiement'] = dims_createtimestamp();
						$versement->fields['montant'] = $montant;
						$versement->fields['suivi_id'] = $suivi_id;
						$versement->fields['suivi_type'] = $suivi_type;
						$versement->fields['suivi_exercice'] = $suivi_exercice;
						$versement->save();
						$suivi->save();
					}
					if (isset($retour) && $retour == 'fiche') dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi->fields['id']}&suivi_type={$suivi->fields['type']}&suivi_exercice={$suivi->fields['exercice']}");
					else dims_redirect("$scriptenv");
				break;

				case 'suivi_solder':
					$suivi = new suivi();
					$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
					$versement = new versement();
					$versement->fields['date_paiement'] = dims_createtimestamp();
					$versement->fields['montant'] = $suivi->fields['solde'];
					$versement->fields['suivi_id'] = $suivi_id;
					$versement->fields['suivi_type'] = $suivi_type;
					$versement->fields['suivi_exercice'] = $suivi_exercice;
					$versement->save();
					$suivi->save();

					if (isset($retour) && $retour == 'fiche') dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi->fields['id']}&suivi_type={$suivi->fields['type']}&suivi_exercice={$suivi->fields['exercice']}");
					else dims_redirect("$scriptenv");
				break;

				case 'suivi_genererfacture':
				case 'suivi_dupliquer':
					$suivi = new suivi();
					$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
					if ($op == 'suivi_genererfacture') $clone_suivi = $suivi->dupliquer('Facture');
					else $clone_suivi = $suivi->dupliquer();
					dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$clone_suivi->fields['id']}&suivi_type={$clone_suivi->fields['type']}&suivi_exercice={$clone_suivi->fields['exercice']}");
				break;

				case 'suivi_ajouter':
					include(DIMS_APP_PATH . '/modules/system/public_suivis_ajouter.php');
				break;

				case 'suivi_enregistrer':
					$suivi = new suivi();
					if (isset($suivi_id)) $suivi->open($suivi_id, $suivi_type, $suivi_exercice);
					else $suivi->setugm(); // nouveau suivi

					$suivi->setvalues($_POST,'suivi_');
					$suivi->fields['datejour'] = business_datefr2us($suivi->fields['datejour']);

					$suivi->fields['tiers_id'] = $_SESSION['business']['tiers_id'];

					$suivi->save();

					dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi->fields['id']}&suivi_type={$suivi->fields['type']}&suivi_exercice={$suivi->fields['exercice']}");
				break;

				case 'suivi_modifier':
					if (isset($supprimer_versement))
					{
						$versement = new versement();
						$versement->open($supprimer_versement);
						$versement->delete();

						// mise � jour suivi
						$suivi = new suivi();
						$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
						$suivi->save();

						dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi_id}&suivi_type={$suivi_type}&suivi_exercice={$suivi_exercice}");
					}

					if (isset($supprimer_ligne))
					{
						$suividetail = new suividetail();
						$suividetail->open($supprimer_ligne);
						$suividetail->delete();

						// mise � jour suivi
						$suivi = new suivi();
						$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
						$suivi->save();

						dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi_id}&suivi_type={$suivi_type}&suivi_exercice={$suivi_exercice}");
					}
					else include(DIMS_APP_PATH . '/modules/system/public_suivis_modifier.php');
				break;

				case 'suivi_supprimer':
					$suivi = new suivi();
					$suivi->open($suivi_id, $suivi_type, $suivi_exercice);
					$suivi->delete();
					dims_redirect($scriptenv);
				break;

				case 'suivi_detail_enregistrer':
					$suividetail = new suividetail();
					if (isset($suivi_detail_id)) $suividetail->open($suivi_detail_id);

					$suividetail->setvalues($_POST,'suivi_detail_');
					$suividetail->save();

					// mise � jour du montant
					$suivi = new suivi();
					$suivi->open($suivi_detail_suivi_id, $suivi_detail_suivi_type, $suivi_detail_suivi_exercice);
					$suivi->save();

					dims_redirect("$scriptenv?op=suivi_modifier&suivi_id={$suivi_detail_suivi_id}&suivi_type={$suivi_detail_suivi_type}&suivi_exercice={$suivi_detail_suivi_exercice}");
				break;

				case 'suivi_imprimer':
					include(DIMS_APP_PATH . '/modules/system/public_suivis_imprimer.php');
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_tiers_suivis.php');
				break;
			}
		break;


		default :
				$_SESSION['dims']['moduletabid'] =_BUSINESS_TAB_TIERSSEEK;
				include(DIMS_APP_PATH . '/modules/system/public_tiers_recherche.php');
		break;

	}
	?>
	</td>
</tr>
</table>
<?
echo $skin->close_simplebloc();

if ($_SESSION['business']['tiers_id']) {
	echo $skin->open_simplebloc('','100%');
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
	dims_annotation(dims_const::_SYSTEM_OBJECT_TIERS, $_SESSION['business']['tiers_id'],$tiers->fields['intitule']);
	echo $skin->close_simplebloc();
}
?>
