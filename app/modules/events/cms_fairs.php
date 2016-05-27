<div id="contener2">
<?php
//Cyril - Refactoring pour le front fairs sinon trop galère avec les inclusions dans les inclusions
if(! $_SESSION['dims']['connected']) {
	$help = $_DIMS['cste']['_DIMS_FRONT_TEXT_LOGGIN'];
	require_once (DIMS_APP_PATH . '/modules/events/cms_event_accueil.php');
}
else{
	?>
	<div id="content2_1">
		<div class="home">
			<a class="logo_home" href="/index.php?article_id=<? echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']; ?>">
				<img src="<? echo $_SESSION['dims']['front_template_path']; ?>/gfx/home.png" border="0">
			</a>
		</div>
		<div id="fil_ariane">
			<a class="logo_home" href="/index.php?article_id=<? echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']; ?>"><? echo $_DIMS['cste']['_DIMS_RETURN_TO_HOME']; ?></a> >
			<?
			if (isset($evt) && isset($evt->fields['libelle']) && $evt->fields['libelle']!="") {
				echo $evt->fields['libelle'];
			}
			?>
		</div>
		<div class="profil">
		<?
		//$connectname= $_DIMS['cste']['_WELCOME']." ".$_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
		// <span><? echo $connectname; </span><span> | </span>
		?>
		<a class="lien_profil" href="<?= $dims->getScriptEnv().'?submenu=my_profile';?>"><img src="<? echo $_SESSION['dims']['front_template_path']; ?>/gfx/profile.png" border="0"><? echo $_DIMS['cste']['_DIMS_LABEL_MYPROFILE']; ?></a><span> | </span><a class="lien_profil" href="/index.php?dims_logout=1"><img src="<? echo $_SESSION['dims']['front_template_path']; ?>/gfx/logout.png" border="0"><? echo $_DIMS['cste']['_DIMS_LABEL_DISCONNECT']; ?></a>
		</div>
		<?
		$compl='';
		if (isset($evt) && isset($evt->fields['libelle']) && $evt->fields['libelle']!="") {
			$compl="&id_event=".$evt->fields['id'];
		}
		?>
		<span style="clear: both;float:right;margin-right:25px; color: #424242;"><?= dims_constant::getVal('_DIMS_LABEL_LANG'); ?> :<a href="/index.php?dimslang=1<? echo $compl; ?>"><img style="border:0px;margin-left: 10px;" src="./common/img/french.gif"></a> <a href="/index.php?dimslang=2<? echo $compl; ?>"><img style="border:0px;" src="./common/img/english.gif"></a></span>
	</div>
	<?php

	$submenu = dims_load_securvalue('submenu', dims_const::_DIMS_CHAR_INPUT, true, true);

	switch($submenu){
		default:
		case 'menu':
			$help = dims_constant::getVal('FAIRS_WELCOME_MENU');

			require_once (DIMS_APP_PATH . '/modules/events/cms_menu_events.php');
			break;

		case 'subscriptions':
			$help = dims_constant::getVal('FAIRS_PARTICIPATED_EVENT');
			require_once (DIMS_APP_PATH . '/modules/events/cms_subscriptions_list.php');
			break;
		case 'coming_events':
			$help = dims_constant::getVal('FAIRS_EVENTS_TO_COME');
			require_once (DIMS_APP_PATH . '/modules/events/cms_coming_events_list.php');
			break;
		case 'help':
			require_once (DIMS_APP_PATH . '/modules/events/cms_help.php');
			break;
		case 'my_profile':
			require_once (DIMS_APP_PATH . '/modules/events/cms_my_profile.php');
			break;
		case 'feedback':
			$help = dims_constant::getVal('FAIRS_FEEDBACK_FORM');
			require_once (DIMS_APP_PATH . '/modules/events/cms_feedback.php');
			break;
		case 'event_record':
			/*$help = "Votre inscription se compose de plusieurs étapes qui pour chacune nécessite la réception de documents.
					Une fois les documents reçus et validés, un email de validation de l'étape vous sera envoyé.
					Il est important de respecter les dates limites de validation de ces différentes étapes. n'hésitez pas à nous
					contacter si vous avez des questions, notre équipe est à votre disposition : <a href=\"mailto:Andre.Hansen@eco.etat.lu\">Andre.Hansen@eco.etat.lu";*/ //GEstion séparée pour la fiche event
			require_once(DIMS_APP_PATH . '/modules/events/cms_event.php');
			break;
	}
}
?>
</div> <!-- FIN DIV CONTENER2 -->
<?php
if(isset($help) && !empty($help)){
	?>
	<div class="footer_info">
		<div class="bloc_info">
			<table><tr><td style="vertical-align:top;"><img style="border: 0px none; float: left; padding-top: 5px;margin-right: 10px;" src="./common/img/icon_info.png" /></td><td style="vertical-align: center;"><?= $help; ?></td></tr></table>
		</div>
	</div>
	<?php
}
