<?php
$dims = dims::getInstance();
$db = $dims->getDb();

// recherche des objets liés à l'activité
require DIMS_APP_PATH.'modules/system/class_search.php';
$matrix = new search();
$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($this->fields['id_globalobject']));

// gestion des permissions
$bView = false;
if (
	($this->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OWNS))
	|| ($this->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OTHERS))
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
	/*$returnLink = '';
	if (!empty($_SERVER['HTTP_REFERER'])) {
		$returnLink = $_SERVER['HTTP_REFERER'];
	} else {
		$returnLink = $dims->getScriptEnv().'?submenu=1&mode=planning';
	}*/

    $returnLink=$dims->getScriptEnv().'?submenu=1&mode=activity&action=manage';

	// créateur
	$user = new user();
	$user->open($this->fields['id_user']);

	// date de creation
	$date_create = dims_timestamp2local($this->fields['timestp_create']);
	?>

	<div class="title_activities">
		<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/manage_activities.png"/>
		<h2><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_ACTIVITIES']; ?></h2>
	</div>

	<table id="activity_title" class="w100">
	<tr>
		<td valign="top">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/calendar.png" />
		</td>
		<td valign="top">
			Fiche de l'activité - <?= $this->fields['libelle']; ?> - <span class="red"><?php echo $this->getTitle(); ?></span><br/>
			<span class="small_info">Créée par <strong><?php echo $user->fields['firstname'].' '.$user->fields['lastname']; ?></strong> - le <?php echo $date_create['date']; ?></span>
		</td>
		<td align="right" valign="top">
			<a class="returnLink" href="<?= $returnLink; ?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_back.png" alt="Retour à la liste des activités" /> Retour à la liste des événements</a>
		</td>
	</tr>
	</table>

	<?php
	// onglets de la fiche
	if (!isset($_SESSION['desktopv2']['activity']['tab'])) {
		$_SESSION['desktopv2']['activity']['tab'] = 'general';
	}
	$current_tab = dims_load_securvalue('tab', dims_const::_DIMS_CHAR_INPUT, true, true);
	if ($current_tab != '') {
		$_SESSION['desktopv2']['activity']['tab'] = $current_tab;
	}

	// création des onglets
	$tabs = array(
		'general'	=> array( 'label' => 'Informations principales' ),
		'documents'	=> array( 'label' => 'Documents' ),
		//'todos'		=> array( 'label' => $_SESSION['cste']['_TODOS'])
		);

	// affichage des onglets
	?>
	<ul id="activity_tabs" class="clearfix">
		<?php
		foreach ($tabs as $key => $tab) {
			$link = $dims->getScriptEnv().'?action=view&activity_id='.$this->getId().'&tab='.$key;
			$selected = ($key == $_SESSION['desktopv2']['activity']['tab']) ? ' class="selected"' : '';
			echo '<li><a'.$selected.' href="'.$link.'" title="'.$tab['label'].'">'.$tab['label'].'</a></li>';
		}
		?>
	</ul>

	<div class="fiche_activite">
		<?php
		// ouverture de l'onglet sélectionné
		switch ($_SESSION['desktopv2']['activity']['tab']) {
			case 'general':
				include DIMS_APP_PATH.'modules/system/desktopV2/templates/activity/view_activity_general.tpl.php';
				break;
			case 'documents':
				include DIMS_APP_PATH.'modules/system/desktopV2/templates/activity/view_activity_documents.tpl.php';
				break;
			/*case 'todos':
				$go = new dims_globalobject();
				$go->open($this->fields['id_globalobject']);
				$go->setLightAttribute('keep_context', '&submenu=1&mode=activity&action=view&activity_id='.$this->getId());
				$go->setLightAttribute('title_object', $this->getTitle());
				$go->setLightAttribute('on_the_record', $_SESSION['cste']['ON_THE_ACTIVITY_RECORD']);
				$go->setLightAttribute('mail_link', dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/admin.php?dims_mainmenu=0&submenu=1&mode=activity&action=view&activity_id='.$this->getId());
				$go->display(DIMS_APP_PATH.'/include/controllers/todos/controller.php');//on utilise un display pour pouvoir jouer avec $this
				break;*/
		}
		?>
	</div>
	<?php
}
