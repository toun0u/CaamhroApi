<div class="business_dernieresfiches">
<a style="color:#000000;" href="">Historique</a>
<?
$sizeof_intitule = 35;

if (isset($op))
{
	switch($op)
	{
		case 'tiers_ouvrir':
			dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRTIERS, $tiers_id);
		break;
		case 'dossier_ouvrir':
			dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRDOSSIER, $dossier_id);
		break;
		case 'interlocuteur_ouvrir':
			dims_create_user_action_log(_BUSINESS_ACTION_OUVRIRINTERLOCUTEUR, $interlocuteur_id);
		break;
	}
}
?>

<?
	$lastconsult = array();

	//TIERS
	$select =  "SELECT 	max(timestp) as timestp, id_action, id_record
				FROM	dims_user_action_log
				WHERE	id_user = :userid
				AND		id_module = :moduleid
				AND		id_action = :idaction
				GROUP BY id_record
				ORDER BY timestp DESC
				LIMIT 0,10
				";

	$res=$db->query($select, array(
		':userid'	=> $_SESSION['dims']['userid'],
		':moduleid'	=> $_SESSION['dims']['moduleid'],
		':idaction'	=> _BUSINESS_ACTION_OUVRIRTIERS
	));

	while ($fields = $db->fetchrow($res)) {
		$cle = "{$fields['timestp']}_{$fields['id_action']}";
		$h_tiers = new tiers();
		if ($h_tiers->open($fields['id_record']))
		{
			$typeclient = ($h_tiers->fields['typeclient']=='') ? 'Client' : $h_tiers->fields['typeclient'];

			$lastconsult[$cle] = $fields;
			$lastconsult[$cle]['type'] = "<font color=\""._BUSINESS_COLOR_TIERS."\">$typeclient</font>";
			$lastconsult[$cle]['intitule'] = "<FONT CLASS=\"SubTitle\">".dims_strcut($h_tiers->fields['intitule'],$sizeof_intitule)."</font>";
			$lastconsult[$cle]['url'] = "$scriptenv?cat="._BUSINESS_CAT_TIERS."&op=tiers_ouvrir&tiers_id={$fields['id_record']}&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS;
			$lastconsult[$cle]['color'] = _BUSINESS_COLOR_TIERS;
		}
	}

	/*
	//DOSSIERS
	$select = 	"
				SELECT 	max(timestp) as timestp, id_action, id_record
				FROM	dims_user_action_log
				WHERE	id_user = {$_SESSION['dims']['userid']}
				AND		id_module = {$_SESSION['dims']['moduleid']}
				AND		id_action = "._BUSINESS_ACTION_OUVRIRDOSSIER."
				GROUP BY id_record
				ORDER BY timestp DESC
				LIMIT 0,10
				";

	$db->query($select);

	while ($fields = $db->fetchrow())
	{
		$cle = "{$fields['timestp']}_{$fields['id_action']}";
		$h_dossier = new dossier();
		if ($h_dossier->open($fields['id_record']))
		{


			if ($h_dossier->fields['termine'] == 'Oui')
			{
				$h_avancement = "terminé";
			}
			else
			{
				$h_avancement = business_get_avancement($h_dossier->fields['date_debut'],$h_dossier->fields['date_fin']);
			}


			$lastconsult[$cle] = $fields;
			$lastconsult[$cle]['type'] = "<font color=\""._BUSINESS_COLOR_DOSSIER."\">".'Dossier'."</font>";
			$lastconsult[$cle]['intitule'] = "<FONT CLASS=\"SubTitle\">".dims_strcut($h_dossier->fields['objet_dossier'], $sizeof_intitule)."</font>";
			$lastconsult[$cle]['avancement'] = $h_avancement;
			$lastconsult[$cle]['url'] = "$scriptenv?cat="._BUSINESS_CAT_DOSSIER."&dims_moduletabid="._BUSINESS_TAB_DOSSIERSINFORMATIONS."&op=dossier_ouvrir&dossier_id={$fields['id_record']}";
			$lastconsult[$cle]['color'] = _BUSINESS_COLOR_DOSSIER;
		}
	}
	*/

	//INTERLOCUTEURS
	$select =  "SELECT 	max(timestp) as timestp, id_action, id_record
				FROM	dims_user_action_log
				WHERE	id_user = :userid
				AND		id_module = :moduleid
				AND		id_action = :idaction
				GROUP BY id_record
				ORDER BY timestp DESC
				LIMIT 0,10
				";

	$db->query($select, array(
		':userid'	=> $_SESSION['dims']['userid'],
		':moduleid'	=> $_SESSION['dims']['moduleid'],
		':idaction'	=> _BUSINESS_ACTION_OUVRIRINTERLOCUTEUR
	));

	while ($fields = $db->fetchrow())
	{
		$cle = "{$fields['timestp']}_{$fields['id_action']}";
		$h_interlocuteur = new interlocuteur();
		if ($h_interlocuteur->open($fields['id_record']))
		{
			$lastconsult[$cle] = $fields;
			$lastconsult[$cle]['type'] = "<font color=\""._BUSINESS_COLOR_INTERLOC."\">".'Interlocuteur'."</font>";
			$lastconsult[$cle]['intitule'] = "<FONT CLASS=\"SubTitle\">".dims_strcut("{$h_interlocuteur->fields['genre']} {$h_interlocuteur->fields['nom']} {$h_interlocuteur->fields['prenom']}", $sizeof_intitule)."</font>";
			$lastconsult[$cle]['url'] = "$scriptenv?cat="._BUSINESS_CAT_INTERLOCUTEUR."&op=interlocuteur_ouvrir&interlocuteur_id={$fields['id_record']}&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS;
			$lastconsult[$cle]['color'] = _BUSINESS_COLOR_INTERLOC;
		}
	}

	krsort($lastconsult);
	$tabs_fiches = array();
	$c=1;
	foreach($lastconsult as $consult)
	{
		if ($c == 1 && $_SESSION['business']['cat'] != -1)
		{
			?>
			<a style="margin-bottom:0px;border-width:0 0 3px 0;filter:alpha(opacity=100);opacity:1;color:<? echo $consult['color']; ?>;border-color:<? echo $consult['color']; ?>;" href="<? echo $consult['url']; ?>"><? echo $consult['intitule']; ?></a>
			<?
		}
		else
		{
			?>
			<a style="color:<? echo $consult['color']; ?>;border-color:<? echo $consult['color']; ?>;" href="<? echo $consult['url']; ?>"><? echo $consult['intitule']; ?></a>
			<?
		}
		if ($c++>=5) break;
	}

?>
</div>