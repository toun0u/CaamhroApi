<?php
$dims = dims::getInstance();
$db = $dims->getDb();

// recherche des objets liés à l'opportunité
require DIMS_APP_PATH.'modules/system/class_search.php';
$matrix = new search();
$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, null, array($this->fields['id_globalobject']));

// gestion des permissions
$bView = false;
if (
	($this->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OWNS))
	|| ($this->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OTHERS))
) {
	$bView = true;
}

// recherche dans les participants
if (!empty($linkedObjectsIds['distribution']['contacts'])) {
	$params = array();
	$rs = $db->query('
		SELECT	u.id
		FROM	dims_mod_business_contact c
		INNER JOIN	dims_user u
		ON u.id_contact = c.id
		WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')', $params);
	while ($row = $db->fetchrow($rs)) {
		if ($row['id'] == $_SESSION['dims']['userid']) {
			$bView = true;
			break;
		}
	}
}

if ($bView) {
	// horaires
	$a_df = explode('-', $this->fields['datejour']);
	$date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];

	$a_dt = explode('-', $this->fields['datefin']);
	$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];

	if ($this->fields['datefin'] != '0000-00-00' && $this->fields['datefin'] != $this->fields['datejour']) {
		$horaires = 'du '.$date_from.' à '.substr($this->fields['heuredeb'], 0, -3).' au '.$date_to.' à '.substr($this->fields['heurefin'], 0, -3);
	}
	else {
		$horaires = $date_from.' ('.substr($this->fields['heuredeb'], 0, -3).' - '.substr($this->fields['heurefin'], 0, -3).')';
	}



	// créateur
	$user = new user();
	$user->open($this->fields['id_user']);

	// date de creation
	$date_create = dims_timestamp2local($this->fields['timestp_create']);
	?>

	<div class="title_activities">
		<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/manage_opportunities.png"/>
		<h2><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_OPPORTUNITIES']; ?></h2>
	</div>

	<table id="lead_title" class="w100">
	<tr>
		<td valign="top">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/calendar.png" />
		</td>
		<td valign="top">
			Fiche de l'opportunité - <span class="red"><?php echo stripslashes($this->fields['libelle']); ?> (<?php echo $this->fields['opportunity_budget']; ?> €)</span><br/>
			<span class="small_info">Créée par <strong><?php echo $user->fields['firstname'].' '.$user->fields['lastname']; ?></strong> - le <?php echo $date_create['date']; ?></span>
		</td>
		<td align="right" valign="top">
			<a class="returnLink" href="<?php $dims->getScriptEnv(); ?>?mode=leads&action=manage"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_back.png" alt="Retour à la liste des opportunités" /> Retour à la liste des opportunités</a>
		</td>
	</tr>
	</table>

	<?php
	// onglets de la fiche
	if (!isset($_SESSION['desktopv2']['lead']['tab'])) {
		$_SESSION['desktopv2']['lead']['tab'] = 'general';
	}
	$current_tab = dims_load_securvalue('tab', dims_const::_DIMS_CHAR_INPUT, true, true);
	if ($current_tab != '') {
		$_SESSION['desktopv2']['lead']['tab'] = $current_tab;
	}

	// création des onglets
	$tabs = array(
		'general'	=> array( 'label' => 'Informations principales' ),
		'documents'	=> array( 'label' => 'Documents' )
		);

	// affichage des onglets
	?>
	<ul id="activity_tabs" class="clearfix">
		<?php
		foreach ($tabs as $key => $tab) {
			$link = $dims->getScriptEnv().'?action=view&lead_id='.$this->getId().'&tab='.$key;
			$selected = ($key == $_SESSION['desktopv2']['lead']['tab']) ? ' class="selected"' : '';
			echo '<li><a'.$selected.' href="'.$link.'" title="'.$tab['label'].'">'.$tab['label'].'</a></li>';
		}
		?>
	</ul>

	<div class="fiche_activite">
		<?php
		// ouverture de l'onglet sélectionné
		switch ($_SESSION['desktopv2']['lead']['tab']) {
			case 'general':
				include DIMS_APP_PATH.'modules/system/desktopV2/templates/leads/view_lead_general.tpl.php';
				break;
			case 'documents':
				include DIMS_APP_PATH.'modules/system/desktopV2/templates/leads/view_lead_documents.tpl.php';
				break;
		}
		?>
	</div>
	<?php
}
