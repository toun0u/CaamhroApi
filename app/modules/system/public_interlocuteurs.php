<?
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
//require_once(DIMS_APP_PATH . '/modules/system/class_dossier.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . '/modules/system/class_interlocuteur.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_interlocuteur.php');
require_once(DIMS_APP_PATH . '/modules/system/action_detail.php');
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');

$interlocuteur = new interlocuteur();

$reset=dims_load_securvalue('reset',dims_const::_DIMS_NUM_INPUT,true,true);

if ($reset>0 && isset($_SESSION['business']['interlocuteur_id'])) unset($_SESSION['business']['interlocuteur_id']);
//if (isset($reset) && isset($_SESSION['business']['interlocuteur_id'])) unset($_SESSION['business']['interlocuteur_id']);

if (!isset($_SESSION['business']['interlocuteur_id'])) $_SESSION['business']['interlocuteur_id'] = '';
if (isset($interlocuteur_id) && $_SESSION['business']['interlocuteur_id'] != $interlocuteur_id) $_SESSION['business']['interlocuteur_id'] = $interlocuteur_id;

if (!isset($_SESSION['dims']['moduletabid'])) $_SESSION['dims']['moduletabid'] = _BUSINESS_TAB_INTERLOCUTEURSSEEK;

if ($_SESSION['dims']['moduletabid'] == _BUSINESS_TAB_INTERLOCUTEURSADD || $_SESSION['dims']['moduletabid'] == _BUSINESS_TAB_INTERLOCUTEURSSEEK) $_SESSION['business']['interlocuteur_id'] = '';

if ($_SESSION['business']['interlocuteur_id'])
{
	if (!$interlocuteur->open($_SESSION['business']['interlocuteur_id'])) // erreur lors de l'ouverture => tiers supprim�
	{
		$_SESSION['business']['interlocuteur_id'] = '';
	}
}

$tabscriptenv = "$scriptenv?cat="._BUSINESS_CAT_INTERLOCUTEUR;

if ($op == 'interlocuteurs_export')		include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_export.php');

if ($_SESSION['business']['interlocuteur_id'])
{
	$form_title = _BUSINESS_LABEL_INTERLOCUTEURS." &#187; {$interlocuteur->fields['genre']} {$interlocuteur->fields['nom']} {$interlocuteur->fields['prenom']}&nbsp;(".business_format_ref($interlocuteur->fields['id'],'I').")";

	$tabs[_BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS]['title'] = _BUSINESS_LABEL_INFORMATIONS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS]['icon'] = _BUSINESS_ICO_GENERALITES;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS]['width'] = 80;

	$tabs[_BUSINESS_TAB_INTERLOCUTEURSTIERS]['title'] = _BUSINESS_TIERS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSTIERS]['icon'] = _BUSINESS_ICO_TIERS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSTIERS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSTIERS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSTIERS]['width'] = 80;

	$tabs[_BUSINESS_TAB_INTERLOCUTEURSDOSSIERS]['title'] = _BUSINESS_DOSSIER;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSDOSSIERS]['icon'] =  _BUSINESS_ICO_DOSSIER;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSDOSSIERS]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSDOSSIERS;
	$tabs[_BUSINESS_TAB_INTERLOCUTEURSDOSSIERS]['width'] = 70;

	$tabs['']['title'] = _BUSINESS_DELETE;
	$tabs['']['icon'] =  _BUSINESS_ICO_SUPPRIMER;
	$tabs['']['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSSEEK."&op=interlocuteur_effacer&delete_id={$_SESSION['business']['interlocuteur_id']}";
	$tabs['']['width'] = 70;
	$tabs['']['confirm'] = _DIMS_CONFIRM;

}
else
{
	$interlocuteur->init_description();
	$form_title = _BUSINESS_LABEL_INTERLOCUTEURS;
}

$tabs[_BUSINESS_TAB_INTERLOCUTEURSSEEK]['title'] = _BUSINESS_LABEL_SEEK;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSSEEK]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSSEEK;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSSEEK]['icon'] = _BUSINESS_ICO_RECHERCHE;;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSSEEK]['width'] = 80;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSSEEK]['position'] = 'right';

$tabs[_BUSINESS_TAB_INTERLOCUTEURSADD]['title'] = _BUSINESS_LABEL_ADD;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSADD]['url'] = "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSADD;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSADD]['icon'] = _BUSINESS_ICO_INTERLOCUTEUR_AJOUT;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSADD]['width'] = 70;
$tabs[_BUSINESS_TAB_INTERLOCUTEURSADD]['position'] = 'right';

$tabs[_BUSINESS_CAT_ACCUEIL]['title'] = _BUSINESS_BACK_ACCUEIL;
$tabs[_BUSINESS_CAT_ACCUEIL]['icon'] = _BUSINESS_ICO_ACCUEIL;
$tabs[_BUSINESS_CAT_ACCUEIL]['url'] = "$tabscriptenv&dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS."&cat=-1";
$tabs[_BUSINESS_CAT_ACCUEIL]['width'] = 70;
$tabs[_BUSINESS_CAT_ACCUEIL]['position'] = 'right';

$tabs[_BUSINESS_TAB_CONTACT]['title'] = _BUSINESS_TIERS;
$tabs[_BUSINESS_TAB_CONTACT]['url'] = "{$scriptenv}?dims_action=public&cat="._BUSINESS_CAT_TIERS."&reset=1";
$tabs[_BUSINESS_TAB_CONTACT]['icon'] = "./common/modules/system/img/tiers.png";
$tabs[_BUSINESS_TAB_CONTACT]['width'] = 80;
$tabs[_BUSINESS_TAB_CONTACT]['position'] = 'right';

//require_once 'public_dernieresfiches.php';

$form_title = '<div style="float:left;padding-top:1px;">'.$form_title.'</div><div style="float:left;padding-left:8px;">'.dims_tickets_new(dims_const::_SYSTEM_OBJECT_INTERLOCUTEUR, $interlocuteur->fields['id'], "{$interlocuteur->fields['genre']} {$interlocuteur->fields['nom']} {$interlocuteur->fields['prenom']}").'</div>';

echo $skin->open_simplebloc($form_title,'border-color:'._BUSINESS_COLOR_INTERLOC.';','background-color:'._BUSINESS_COLOR_INTERLOC.';color:#ffffff;');

?>
<table cellpadding="0" cellspacing="0" width="100%">
<tr bgcolor="<? echo $skin->values['bgline2']; ?>">
	<td style="padding:2px;">
	<?
		echo $skin->create_toolbar($tabs,$_SESSION['dims']['moduletabid']);
	?>
	</td>
</tr>
<!--tr bgcolor="<? echo $skin->values['bgline1']; ?>">
	<td colspan="2">
	<?
	switch($_SESSION['dims']['moduletabid'])
	{
		case _BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS:
			$title = _BUSINESS_LABEL_INFORMATIONS;
		break;
		case _BUSINESS_TAB_INTERLOCUTEURSTIERS:
			$title = _BUSINESS_TIERS;
		break;
		case _BUSINESS_TAB_INTERLOCUTEURSDOSSIERS:
			$title = _BUSINESS_DOSSIER;
		break;
		case _BUSINESS_TAB_INTERLOCUTEURSSEEK:
			$title = _BUSINESS_LABEL_SEEK;
		break;
		case _BUSINESS_TAB_INTERLOCUTEURSADD:
			$title = _BUSINESS_LABEL_ADD;
		break;
	}
	?>
		<table cellpadding="2" cellspacing="0" width="100%">
			<tr>
				<td align="left"><? echo "&nbsp;<b>$form_title &#187; $title</b>"; ?></td>
				<?
				if (isset($_SESSION['business']['interlocuteur_id']) && $_SESSION['business']['interlocuteur_id'])
				{
					?>
					<td align="right"><b>[&nbsp;<a class="delete" href="javascript:dims_confirmlink('<? echo "$tabscriptenv&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSSEEK."&op=interlocuteur_effacer&delete_id={$_SESSION['business']['interlocuteur_id']}"; ?>','�tes-vous certain de vouloir supprimer cette fiche ?')">Supprimer cet Interlocuteur</a>&nbsp;]&nbsp;&nbsp;</b></td>
					<?
				}
				?>
			</tr>
		</table>
	</td>
</tr-->
<tr>
	<td colspan="2">
	<?
	switch($_SESSION['dims']['moduletabid'])
	{
		// RECHERCHE INTERLOCUTEUR
		case _BUSINESS_TAB_INTERLOCUTEURSSEEK:
			switch($op)
			{
				case 'interlocuteur_effacer':
					$interlocuteur_delete = new interlocuteur();
					$interlocuteur_delete->open($delete_id);
					$interlocuteur_delete->delete();
					dims_redirect("$scriptenv");
				break;

				default :
					include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_recherche.php');
				break;
			}
		break;

		// AJOUT INTERLOCUTEUR
		case _BUSINESS_TAB_INTERLOCUTEURSADD:
			switch($op)
			{
				case 'interlocuteur_enregistrer':
					$interlocuteur->setvalues($_POST,'interlocuteur_');
					$interlocuteur->setugm();
					$interlocuteur_id = $interlocuteur->save();
					dims_redirect("$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS."&op=interlocuteur_ouvrir&interlocuteur_id=$interlocuteur_id");
				break;

				default :
					include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_ajout.php');
				break;
			}
		break;

		// INTERLOCUTEURS / ONGLET GENERALITES
		case _BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS:
			switch($op)
			{
				case 'interlocuteur_enregistrer':
					$interlocuteur->setvalues($_POST,'interlocuteur_');
					$interlocuteur->save();

					$db->query("DELETE FROM dims_mod_business_interlocuteur_categorie WHERE id_interlocuteur = :idinterlocuteur ", array(
						':idinterlocuteur' => $interlocuteur->fields['id']
					));
					foreach($categorie_interlocuteur as $categorie)
					{
						$db->query("INSERT INTO dims_mod_business_interlocuteur_categorie SET id_interlocuteur = :idinterlocuteur , categorie = :categorie ", array(
							':idinterlocuteur'	=> $interlocuteur->fields['id'],
							':categorie'		=> $categorie
						));
					}
					dims_redirect($scriptenv);
				break;

				case 'interlocuteur_ouvrir':
					//dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRTIERS, $tiers_id);
				default:
					include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_generalites.php');
				break;
			}
		break;

		// INTERLOCUTEURS / ONGLET DOSSIERS
		case _BUSINESS_TAB_INTERLOCUTEURSDOSSIERS:
			switch($op)
			{
				default:
					include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_dossiers.php');
				break;
			}
		break;

		// INTERLOCUTEURS / ONGLET CLIENTS
		case _BUSINESS_TAB_INTERLOCUTEURSTIERS:
			switch($op)
			{
				case 'tiers_couper':
					$tiers_interlocuteur = new tiers_interlocuteur();
					$tiers_interlocuteur->open($tiers_id,$_SESSION['business']['interlocuteur_id']);
					$tiers_interlocuteur->delete();
					dims_redirect($scriptenv);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_tiers.php');
				break;
			}
		break;

		default :
				$_SESSION['dims']['moduletabid'] =_BUSINESS_TAB_INTERLOCUTEURSSEEK;
				include(DIMS_APP_PATH . '/modules/system/public_interlocuteurs_recherche.php');
			break;
	}
	?>
	</td>
</tr>
</table>
<?
echo $skin->close_simplebloc();

if ($_SESSION['business']['interlocuteur_id']) {
	echo $skin->open_simplebloc('','100%');
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
	dims_annotation(dims_const::_SYSTEM_OBJECT_INTERLOCUTEUR, $_SESSION['business']['interlocuteur_id'],trim("{$interlocuteur->fields['genre']} {$interlocuteur->fields['nom']} {$interlocuteur->fields['prenom']}"));
	echo $skin->close_simplebloc();
}


?>

