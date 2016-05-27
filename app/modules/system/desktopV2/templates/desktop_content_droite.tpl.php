<!--<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/recent_connexions/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/tag/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/shortcuts/css/styles.css" media="screen" />-->
<div class="content_droite">
	<div class="container_zone_droite">
		<?php include _DESKTOP_TPL_LOCAL_PATH.'/recent_folders/recent_folders.tpl.php'; ?>
	</div>
	<!--<div class="container_zone_droite">
		<?php //include _DESKTOP_TPL_LOCAL_PATH.'/todos/todos_wall.tpl.php'; ?>
	</div>-->
	<?php
	// on affiche les infos contextuelles que
	// si on visualise une fiche activité ou une fiche opportunité
	// $action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
	if ( ( $_SESSION['desktopv2']['mode'] == 'activity' || $_SESSION['desktopv2']['mode'] == 'leads' ) && $action == 'view' ) {
		?>
		<div class="contextual_infos">
			<?php include _DESKTOP_TPL_LOCAL_PATH.'/contextual_infos/infos.tpl.php'; ?>
		</div>
		<?php
	}
	?>
    <div class="container_zone_droite">
		<?php include _DESKTOP_TPL_LOCAL_PATH.'/shortcuts/shortcuts.tpl.php'; ?>
	</div>
	<div class="container_zone_droite">
		<?php include _DESKTOP_TPL_LOCAL_PATH.'/contextual_functions/functions.tpl.php'; ?>
	</div>
		<?
		/*
	<div class="recent_connexions">
        <h2 class="h2_recent_connexion" style="float:left">
			<?php echo $_SESSION['cste']['RECENT_CONNEXIONS']; ?>
		</h2>
		<?php
		//hack demandé par André Hansen, par défaut la pupuce rouge
		if(!isset($_SESSION['desktopV2']['content_droite']['zone_recent_connexions'])){
			$_SESSION['desktopV2']['content_droite']['zone_recent_connexions'] = 0;
		}
		?>
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_recent_connexions']) && $_SESSION['desktopV2']['content_droite']['zone_recent_connexions'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_recent_connexions').slideToggle('fast',flip_flop($('div.zone_recent_connexions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));"/>
    </div>

    <div class="zone_recent_connexions" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_recent_connexions']) && $_SESSION['desktopV2']['content_droite']['zone_recent_connexions'] == 0) echo 'style="display:none;"'; ?>>
	<?php //include _DESKTOP_TPL_LOCAL_PATH.'/recent_connexions/recent_connexions.tpl.php'; ?>
    </div>
	*/?>

    <?php
		if(empty($_SESSION['dims']['search']['current_search']) && $mode=='default') include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag.tpl.php';
	?>
	<div id="my_selections">
		<?php include _DESKTOP_TPL_LOCAL_PATH.'/selection/selection.tpl.php'; ?>
	</div>
</div>
