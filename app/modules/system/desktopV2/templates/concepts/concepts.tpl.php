<?php
$versioncss="?v=AF2314DF00468012";
?>
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/description/event/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/description/contact/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_mission/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_last_participations/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_contact/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_contact_fiche/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_document/css/styles.css?<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_comment/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/context_tags/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/shortcuts/css/styles.css<?php echo $versioncss;?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/styles.css<?php echo $versioncss;?>" media="screen" />

<?php

if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
	?><link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/concepts/bloc_suivi/css/styles.css" media="screen" /><?php
}
?>
<!--<script language="JavaScript" type="text/JavaScript" src="./common/js/chosen/chosen.jquery.js"></script>-->
<script language="JavaScript" type="text/JavaScript" src="<?php echo _DESKTOP_TPL_PATH; ?>/functions.js"></script>
<!--<script language="JavaScript" type="text/JavaScript" src="./common/js/portal_v5.js"></script>-->
<script language="Javascript" type="text/JavaScript">
	function toggle_menu(id_menu, image) {
	$('#'+id_menu).toggle();

	if(image.attr('src').contains('replier'))
		image.attr('src', '<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png');
	else
		image.attr('src', '<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png');
	}
</script>

<div class="zone_search">
	<span class="house">
		<a href="/admin.php?submenu=<? echo _DESKTOP_V2_DESKTOP; ?>&mode=default&force_desktop=1">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_house.png" />
		</a>
	</span>
	<span class="text-search">
		<?php echo $_SESSION['cste']['SEARCH_ON']; ?>
	</span>
	<img class="home_logo" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_logo.png" />
	<div class="searchform_concepts">
		<form action="/admin.php?submenu=<?php echo _DESKTOP_V2_DESKTOP; ?>&dims_op=desktopv2&action=search2&force_desktop=1" method="post" name="formsearch" id="formsearch_concepts">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
				$token->field("button_search_y");
				$token->field("desktop_editbox_search");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<span>
				<input onclick="javascript:if ($('#editbox_search_concepts').val() != '<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>') $('#formsearch_concepts').submit(); else return false;" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left;" />
				<input type="text"
					   name="desktop_editbox_search"
					   class="editbox_search <?php if (!empty($_SESSION['dims']['modsearch']['my_real_expression']))echo 'working'; ?>"
					   id="editbox_search_concepts"
					   maxlength="80"
					   value="<?php if(!empty($_SESSION['dims']['modsearch']['my_real_expression']))echo $_SESSION['dims']['modsearch']['my_real_expression']; else echo  $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS']; ?>"
					   onfocus="Javascript:$(this).addClass('working');<?php if(empty($_SESSION['dims']['modsearch']['my_real_expression']))echo "this.value=''";?>"
					   onblur="Javascript:<?php if(empty($_SESSION['dims']['modsearch']['my_real_expression'])) echo "if($(this).hasClass('working') && $(this).val()=='')$(this).removeClass('working');";?> if (this.value=='')this.value='<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>';">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left"/>
				<?php if(!empty($_SESSION['dims']['modsearch']['my_real_expression'])){ ?><a class="discard_seach" href="<?php echo $dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&force_desktop=1'; ?>" title="Discard the search"> <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png"/></a><?php } ?>

				<input onclick="javascript:if ($('#editbox_search_concepts').val() != '<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>') $('#formsearch_concepts').submit(); else return false;" style="margin-top: 4px; margin-left: 5px;" type="button" value="<?php echo $_SESSION['cste']['_SEARCH']; ?>" />
			</span>
		</form>
	</div>


	<div class="filtre_advanced">
		<a href="<?php echo $dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&force_desktop=1';?>">
			<span class="close_advanced">
				<?php echo $_SESSION['cste']['ADVANCED_SEARCH']; ?>
			</span>
		</a>
		<font style="color: #df1d31;">
			<span class="separator_advanced">|</span>
		</font>
		<a href="<?php echo $dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&force_desktop=1&map=1';?>">
			<span class="map_advanced">
				<?php echo $_SESSION['cste']['_DIMS_LABEL_MAP']; ?>
			</span>
		</a>
	</div>
</div>

<div class="map_search" style="display:none;">
	<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/Pour exemples/carte/world_activity.png" />
</div>

<?php
// on conserve le nombre de groupes du carnet d'adresses en mémoire
if (!isset($_SESSION['desktopv2']['ab_groups'])) {
	$_SESSION['desktopv2']['ab_groups'] = sizeof($desktop->getGroupsUser());
}

if (!isset($_SESSION['desktopv2']['concepts']['sel_type'])) $_SESSION['desktopv2']['concepts']['sel_type'] = -1;
if (!isset($_SESSION['desktopv2']['concepts']['sel_id'])) $_SESSION['desktopv2']['concepts']['sel_id'] = -1;

if(!isset($_SESSION['desktopv2']['concepts']['filters']['events']))			$_SESSION['desktopv2']['concepts']['filters']['events'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['activities']))		$_SESSION['desktopv2']['concepts']['filters']['activities'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['opportunities']))	$_SESSION['desktopv2']['concepts']['filters']['opportunities'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['companies']))		$_SESSION['desktopv2']['concepts']['filters']['companies'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['contacts']))		$_SESSION['desktopv2']['concepts']['filters']['contacts'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['documents']))		$_SESSION['desktopv2']['concepts']['filters']['documents'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['dossiers']))		$_SESSION['desktopv2']['concepts']['filters']['dossiers'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['suivis']))			$_SESSION['desktopv2']['concepts']['filters']['suivis'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['years']))			$_SESSION['desktopv2']['concepts']['filters']['years'] = array();
if(!isset($_SESSION['desktopv2']['concepts']['filters']['countries']))		$_SESSION['desktopv2']['concepts']['filters']['countries'] = array();
if(!isset($_SESSION['desktop']['concept']['tags']))							$_SESSION['desktop']['concept']['tags'] = array();

$_SESSION['desktopv2']['concepts']['sel_type'] = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['sel_type']);
$_SESSION['desktopv2']['concepts']['sel_id'] = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['sel_id']);

if(!isset($_SESSION['desktopv2']['concepts']['op']))
	$_SESSION['desktopv2']['concepts']['op'] = dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES;

$concepts_op = dims_load_securvalue("concepts_op", dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['desktopv2']['concepts']['op']);
$mode = dims_load_securvalue("mode", dims_const::_DIMS_CHAR_INPUT, true, true, true);

if($mode == "edit")
	$concepts_op = dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES;

$_SESSION['desktopv2']['concepts']['op'] = $concepts_op;

// $init_pivot = dims_load_securvalue('init_pivot', dims_const::_DIMS_NUM_INPUT, true, true);
// if ($init_pivot) {
//	$_SESSION['desktopv2']['concepts']['filters']['pivot'] = null;
//	$_SESSION['desktopv2']['concepts']['filters']['stack'] = array();
// }

// insertion du pivot dans les filtres de recherche
$obj = null;
$currentworkspace = $_SESSION['dims']['workspaceid'];
//dims_print_r($_SESSION['desktopv2']['concepts']['filters']);
switch($_SESSION['desktopv2']['concepts']['sel_type']){
	case dims_const::_SYSTEM_OBJECT_EVENT :
	case dims_const::_SYSTEM_OBJECT_ACTION :
		$obj = new action();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['events'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['events'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('event', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', false);
		}
		break;
	case dims_const::_SYSTEM_OBJECT_ACTIVITY :
		require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
		$obj = new dims_activity();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id']) && $obj->fields['typeaction'] == dims_activity::TYPE_ACTION) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['activities'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['activities'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('activity', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', false);
		}
		break;
	case dims_const::_SYSTEM_OBJECT_OPPORTUNITY :
		require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';
		$obj = new dims_opportunity();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id']) && $obj->fields['typeaction'] == dims_opportunity::TYPE_ACTION) {
			if(!isset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['opportunities'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('opportunity', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', false);
		}
		break;
	case dims_const::_SYSTEM_OBJECT_CONTACT :
		$obj = new contact();
		if($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['contacts'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('contact', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', true);
			if($mode == 'edit_adr'){
				$_SESSION['dims']['newcontact']['id_contact'] = $obj->get('id');
				dims_redirect($dims->getScriptEnv().'?submenu=1&mode=new_contact&action=step2');
			}
		}
		break;
	case dims_const::_SYSTEM_OBJECT_TIERS :
		$obj = new tiers();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['companies'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['companies'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('company', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', true);

			// vérif si client de type partagé ou pas
			if (!empty($obj->fields['share_suivi'])) {
				$currentworkspace = null;
			}
		}
		break;
	case dims_const::_SYSTEM_OBJECT_DOCFILE :
		$obj = new docfile();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['documents'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['documents'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('doc', $obj->fields['id_globalobject']);
			}
			$obj->setLightAttribute('concept_not_event', true);
		}
		break;
	case dims_const::_SYSTEM_OBJECT_CASE :
		$obj = new dims_case();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['dossiers'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('dossier', $obj->fields['id_globalobject']);
			}
		}
		break;
	case dims_const::_SYSTEM_OBJECT_SUIVI :
		$obj = new suivi();
		if ($obj->open($_SESSION['desktopv2']['concepts']['sel_id'])) {
			if (!isset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$obj->fields['id_globalobject']])) {
				$_SESSION['desktopv2']['concepts']['filters']['suivis'][$obj->fields['id_globalobject']] = $obj->fields['id_globalobject'];
				$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('suivi', $obj->fields['id_globalobject']);
			}
		}
		break;
}

// création du pivot si il existe pas
if ($obj != null && !isset($_SESSION['desktopv2']['concepts']['filters']['pivot'])) {
	$_SESSION['desktopv2']['concepts']['filters']['pivot'] = $_SESSION['desktopv2']['concepts']['sel_type'].'-'.$obj->fields['id_globalobject'];
}

// filtre avec tous les paramètres
require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
$matrix = new search();
$linkedObjectsIds = $matrix->exploreMatrice(
	$currentworkspace,
	$_SESSION['desktopv2']['concepts']['filters']['events'],
	$_SESSION['desktopv2']['concepts']['filters']['activities'],
	$_SESSION['desktopv2']['concepts']['filters']['opportunities'],
	$_SESSION['desktopv2']['concepts']['filters']['companies'],
	$_SESSION['desktopv2']['concepts']['filters']['contacts'],
	$_SESSION['desktopv2']['concepts']['filters']['documents'],
	$_SESSION['desktopv2']['concepts']['filters']['dossiers'],
	$_SESSION['desktopv2']['concepts']['filters']['suivis'],
	$_SESSION['desktopv2']['concepts']['filters']['years'],
	$_SESSION['desktopv2']['concepts']['filters']['countries']
	);

$lstObj=array();
$lstObj = $desktop->getLinkedObjects($linkedObjectsIds, $_SESSION['desktop']['concept']['tags']);

// dims_print_r($linkedObjectsIds);
// dims_print_r($lstObj);
// dims_print_r($_SESSION['desktopv2']['concepts']['filters']['pivot']);
// dims_print_r($_SESSION['desktopv2']['concepts']['filters']['stack']);

// lien de retour
$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);
if ($from != '') {
	switch ($from) {
		case 'desktop':
			$_SESSION['desktop']['return_link']['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=default&force_desktop=1';
			$_SESSION['desktop']['return_link']['label'] = $_SESSION['cste']['BACK_TO_YOUR_DESKTOP'];
			break;
		case 'search':
			$_SESSION['desktop']['return_link']['link'] = $_SERVER['HTTP_REFERER'];
			$_SESSION['desktop']['return_link']['label'] = $_SESSION['cste']['BACK_TO_YOUR_SEARCH'];
			break;
		case 'address_book':
			$_SESSION['desktop']['return_link']['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=address_book';
			$_SESSION['desktop']['return_link']['label'] = $_SESSION['cste']['BACK_TO_YOUR_ADDRESS_BOOK'];
			break;
		case 'planning':
			$_SESSION['desktop']['return_link']['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=planning';
			$_SESSION['desktop']['return_link']['label'] = $_SESSION['cste']['BACK_TO_THE_PLANNING'];
			break;
	}
}


if(!empty($_SESSION['desktopv2']['concepts']['filters']['stack'])){
	?>
	<div class="cadre_build_search">
		<div class="title_exploration"><span class="colorized">Explore</span></div>
		<div class="building">
			<div class="bloc expression">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/grand_cube_picto.png">
			</div>
			<div class="operator egal">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/egal.png">
			</div>

			<?php
			foreach ($_SESSION['desktopv2']['concepts']['filters']['stack'] as $nb => $filter) {
				$filter_type = $filter[0];
				$filter_value = $filter[1];

				if ($nb > 0) {
					?>
					<div class="operator">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/infini.png" />
					</div>
					<?php
				}

				switch ($filter_type) {
					case 'contact':
						$contact = new contact();
						$contact->openWithGB($filter_value);
						$contact->setLightAttribute('mode', 'concept');
						$contact->setLightAttribute('filter_type', 'contact');
						$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/contact.tpl.php');
						break;
					case 'company':
						$tiers = new tiers();
						$tiers->openWithGB($filter_value);
						$tiers->setLightAttribute('mode', 'concept');
						$tiers->setLightAttribute('filter_type', 'company');
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/company.tpl.php');
						break;
					case 'event':
						$action = new action();
						$action->openWithGB($filter_value);
						$action->setLightAttribute('mode', 'concept');
						$action->setLightAttribute('filter_type', 'event');
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						break;
					case 'activity':
						$action = new action();
						$action->openWithGB($filter_value);
						$action->setLightAttribute('mode', 'concept');
						$action->setLightAttribute('filter_type', 'activity');
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						break;
					case 'opportunity':
						$action = new action();
						$action->openWithGB($filter_value);
						$action->setLightAttribute('mode', 'concept');
						$action->setLightAttribute('filter_type', 'opportunity');
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						break;
					case 'doc':
						$doc = new docfile();
						$doc->openWithGB($filter_value);
						$doc->setLightAttribute('mode', 'concept');
						$doc->setLightAttribute('filter_type', 'doc');
						$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/document.tpl.php');
						break;
					case 'dossier':
						$case = new dims_case();
						$case->openWithGB($filter_value);
						$case->setLightAttribute('mode', 'concept');
						$case->setLightAttribute('filter_type', 'dossier');
						$case->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/dossier.tpl.php');
						break;
					case 'suivi':
						$suivi = new suivi();
						$suivi->openWithGB($filter_value);
						$suivi->setLightAttribute('mode', 'concept');
						$suivi->setLightAttribute('filter_type', 'suivi');
						$suivi->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/suivi.tpl.php');
						break;
					case 'year':
						?>
						<div class="bloc item">
							<div class="as_picto">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/build_year.png">
							</div>
							<div>
								<span class="label">
									<a class="remove_item" href="<?php echo $dims->getScriptEnv();?>?action=drop_filter&filter_type=year&filter_value=<?php echo $filter_value;?>" title="<?php echo $_SESSION['cste']['DELETE_THIS_FILTER']; ?>">
										<span><?php echo $filter_value; ?></span>
									</a>
								</span>
							</div>
						</div>
						<?php
						break;
					case 'country':
						require_once DIMS_APP_PATH.'modules/system/class_country.php';
						$country = new country();
						$country->open($filter_value);
						?>
						<div class="bloc item">
							<div class="as_picto">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/build_country.png">
							</div>
							<div>
								<span class="label">
									<a class="remove_item" href="<?php echo $dims->getScriptEnv();?>?action=drop_filter&filter_type=country&filter_value=<?php echo $filter_value;?>" title="delete this filter">
										<span><?php echo dims_strcut($country->fields['printable_name'], 15); ?></span>
									</a>
								</span>
							</div>
						</div>
						<?php
						break;
				}
			}
			?>

			<div style="clear:both;height:0px;"></div>
		</div>
	</div>
	<?php
}
?>

<div class="zone_explore_inet_content_gauche">
	<?php

	if (isset($_SESSION['desktop']['return_link'])) {
		echo '<a id="return_link" href="'.$_SESSION['desktop']['return_link']['link'].'" title="'.$_SESSION['desktop']['return_link']['label'].'"><img src="./common/modules/system/desktopV2/templates/gfx/common/icon_back.png" alt="'.$_SESSION['desktop']['return_link']['label'].'" /><span>'.$_SESSION['desktop']['return_link']['label'].'</span></a>';
	}

	if ($_SESSION['desktopv2']['concepts']['sel_id'] != '' && $_SESSION['desktopv2']['concepts']['sel_id'] > 0){
		?>
		<div class="zone_description">
			<?php
			if ($obj != null) {
				switch($_SESSION['desktopv2']['concepts']['sel_type']){
					case dims_const::_SYSTEM_OBJECT_ACTIVITY :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/event/desc_activity.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/event/desc_opportunity.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_EVENT :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/event/desc_event.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/contact/desc_contact.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/tiers/desc_tiers.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_DOCFILE :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/document/desc_document.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_CASE :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/dossier/desc_dossier.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_SUIVI :
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/suivi/desc_suivi.tpl.php');
						break;
				}
			}
			?>
		</div>
		<div class="concepts_bloc_content">
			<div class="global_content_record">
				<ul class="sub_menus">
					<li <?php echo ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES)?'class="selected"':'';?>>
						<a href="<?php echo $dims->getScriptEnv().'?concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES;?>"><?php echo $_SESSION['cste']['GENERAL_INFORMATION_LABEL']; ?></a>
					</li>
					<li <?php echo ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_EVENTS_ACTIVITIES)?'class="selected"':'';?>>
						<a href="<?php echo $dims->getScriptEnv().'?concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_EVENTS_ACTIVITIES;?>"><?php echo $_SESSION['cste']['EVENTS_ACTIVITIES']; ?></a>
					</li>
					<li <?php echo ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_SUIVIS)?'class="selected"':'';?>>
						<a href="<?php echo $dims->getScriptEnv().'?concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_SUIVIS;?>"><?php echo $_SESSION['cste']['_MONITORINGS']; ?></a>
					</li>
					<li <?php echo ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_DOCUMENTS)?'class="selected"':'';?>>
						<a href="<?php echo $dims->getScriptEnv().'?concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_DOCUMENTS;?>"><?php echo $_SESSION['cste']['_DOCS']; ?></a>
					</li>
					<li <?php echo ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_TODOS)?'class="selected"':'';?>>
						<a href="<?php echo $dims->getScriptEnv().'?concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_TODOS.'&todo_op='.dims_const::_SHOW_COLLABORATION;?>"><?php echo $_SESSION['cste']['_TODOS']; ?></a>
					</li>
				</ul>
			</div>
			<?php

			if($obj != null && $concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES) {
				$obj->display(_DESKTOP_TPL_LOCAL_PATH."/concepts/description/full_desc_contact.tpl.php");
				$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/bloc_contact.tpl.php');
				$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/link_entity.tpl.php');
				// $obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_document/bloc_document.tpl.php');
				$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_comment/bloc_comment.tpl.php');
			}

			if ($obj != null && $obj->fields['id'] != '' && $obj->fields['id'] > 0){
				if($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_EVENTS_ACTIVITIES) {
					$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_mission/bloc_mission.tpl.php');
				}
				if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
					if($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_SUIVIS)
						$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_suivi/bloc_suivi.tpl.php');
				}
				if($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_DOCUMENTS)
					$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_document/bloc_document.tpl.php');

				else if ($concepts_op == dims_const_desktopv2::DESKTOP_V2_CONCEPTS_TODOS){

					$go = new dims_globalobject();
					$go->open($obj->fields['id_globalobject']);

					$link_to = dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/admin.php?dims_mainmenu=0&submenu=2&id='.$go->fields['id_record'].'&type='.$go->fields['id_object'].'&init_filters=1&from=desktop&concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_TODOS.'#todo_';
					switch($go->fields['id_object']){
						case dims_const::_SYSTEM_OBJECT_EVENT :
							$title_object = $obj->fields['libelle'];
							$on_the_record = $_SESSION['cste']['ON_THE_EVENT_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_ACTIVITY :
							require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
							$title_object = $obj->getLibelle();
							$on_the_record = $_SESSION['cste']['ON_THE_ACTIVITY_RECORD'];
							$link_to = dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/admin.php?dims_mainmenu=0&submenu=1&mode=activity&action=view&activity_id='.$obj->getid();
							$ig_param = '&tab=general';
							$todo_param = '&tab=todos#todo_'.$obj->getId();
							break;
						case dims_const::_SYSTEM_OBJECT_OPPORTUNITY :
							$title_object = $obj->fields['libelle'];
							$on_the_record = $_SESSION['cste']['ON_THE_OPPORTUNITY_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_CONTACT :
							$title_object = $obj->fields['firstname'].' '.$obj->fields['lastname'];
							$on_the_record = $_SESSION['cste']['ON_THE_CONTACT_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_TIERS :
							$title_object = $obj->fields['intitule'];
							$on_the_record = $_SESSION['cste']['ON_THE_COMPANY_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_DOCFILE :
							$title_object = $obj->fields['name'];
							$on_the_record = $_SESSION['cste']['ON_THE_DOCUMENT_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_CASE :
							$title_object = $obj->fields['label'];
							$on_the_record = $_SESSION['cste']['ON_THE_CASE_RECORD'];
							break;
						case dims_const::_SYSTEM_OBJECT_SUIVI :
							$title_object = $obj->fields['libelle'];
							$on_the_record = $_SESSION['cste']['ON_THE_COMMERCIAL_DOCUMENT_RECORD'];
							break;
					}

					//$go->setLightAttribute('keep_context', '&dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&id='.$article->getId().'&action='.module_wiki::_COLLABORATION_VIEW.'&wce_mode=render');
					$go->setLightAttribute('title_object', $title_object);
					$go->setLightAttribute('on_the_record', $on_the_record);
					$go->setLightAttribute('mail_link', $link_to);

					$go->display(DIMS_APP_PATH.'/include/controllers/todos/controller.php');//on utilise un display pour pouvoir jouer avec $this

				}
			}
			else {
				dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP);
			}
			?>
		</div>
		<?
	}
	?>
</div>
<!-- bloc de droite -->
<div class="zone_explore_inet_content_droite">
	<?php
	if (!isset($obj->title)) $obj->settitle();
	$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/context_tags/tag_of_period.tpl.php');
	$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/context_tags/tag_of_area.tpl.php');
		if ($currentworkspace['activenewsletter']){
			$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/context_tags/tag_of_newsletters.tpl.php');
		}
	$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/context_tags/tag_of_business.tpl.php');
	?>
	<!--div class="title_concept_droite">
		<h1 class="title_period_activity">
			Pictures / Videos of <? if ($_SESSION['desktopv2']['concepts']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT) echo substr($obj->fields['firstname'],0,1).". ".$obj->fields['lastname']; else echo $obj->title; ?>
		</h1>
	</div>
	<div class="concept_picture_videos">
		<table cellspacing="10" cellpadding="0" style="width: 100%;">
			<tbody>
				<tr>
					<td>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/japan.png" style="float:left">
					</td>
				</tr>
				<tr>
					<td style="float: right; text-align: center;">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/visu_picto.png">
						<span>See the full gallery</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div-->
	<div class="shortcuts">
		<?php include _DESKTOP_TPL_LOCAL_PATH.'/shortcuts/shortcuts.tpl.php'; ?>
	</div>
	<div>
		<?php
		if (isset($contextual)) {
			if(!empty($contextual)){
			?>
				<div id="contextual_functions" class="elem">
					<h3>
						<?php echo $_SESSION['cste']['CONTEXTUAL_FUNCTIONS']; ?>
						<a class="buttons toggler_fold unfolded" href="Javascript:void(0);" onclick="Javascript: toggleFold(this);">&nbsp;</a>
					</h3>
					<div class="content">
						<table>
						<?php
						foreach($contextual as $function){
							?>
							<tr>
								<td class="cf_icon">
									<a href="<?php echo $function['href'];?>" <? echo (isset($function['js'])) ? 'onclick="'.$function['js'].';" ': ''; ?>>
										<img src="<?php echo $function['image'];?>" alt="<?php echo $function['label'];?>" />
									</a>
								</td>
								<td class="cf_label">
									<a href="<?php echo $function['href'];?>" <? echo (isset($function['js'])) ? 'onclick="'.$function['js'].';" ': ''; ?>>
										<?php echo $function['label'];?>
									</a>
								</td>
							</tr>
							<?php
						}
						?>
						</table>
					</div>
				</div>
			<?php
			}
		}

		?>
	</div>
</div>
<script type="text/javascript">
	adapteImage('img.avatar_action',true,60);

	<?php
	// réouverture du popup le cas échéant
	if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
		if (isset($_SESSION['desktopv2']['business']['popup'])) {
			if ($_SESSION['desktopv2']['business']['popup'] == 'suivi_modifier') {
				if ($_SESSION['desktopv2']['business']['id_suivi'] > 0) {
					echo 'openSuivi('.$_SESSION['desktopv2']['business']['id_suivi'].');';
				}
				unset($_SESSION['desktopv2']['business']['popup']);
			}
		}
	}
	?>
</script>
