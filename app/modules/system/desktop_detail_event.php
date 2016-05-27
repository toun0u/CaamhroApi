<?php

// On traite l'onglet courant
switch($_SESSION['dims']['submenuobject'][$moduleid][$objectid]) {
	case dims_const::_DIMS_SUBMENU_DETAIL:
	case dims_const::_DIMS_SUBMENU_PREVIEW:
	case 'preview':
		require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
		$desktopobjectheight = dims_load_securvalue('desktopobjectheight', dims_const::_DIMS_NUM_INPUT, true);

		$evt = new action();
		if (isset($id_evt) && $id_evt>0) {
			$evt->open($id_evt);
		}
		else {
			$evt->open($recordid);
		}

		$title = $_DIMS['cste']['_DIMS_LABEL_EVENT'].' : '.$evt->fields['libelle'];
		$domain=$dims->getProtocol().$dims->getHttpHost();

		//echo '<div style="overflow:auto;">
		if ($evt->fields['type']==2) {
			echo '<iframe src="'.$domain.'/index.php?id_event='.$evt->fields['id'].'" style="border: 0pt none ; margin: 0pt; padding: 0pt; width: 100%; height: '.($desktopobjectheight-10).'px;"></iframe>';
		}
		else {
			$detail = array();
			if ($evt->fields['type'] == dims_const::_PLANNING_ACTION_TSK || $evt->fields['typeaction']!='' &&  ($evt->fields['type']==2 || ($evt->fields['personnel']==0 && (isset($tabcorrespmulti[$evt->fields['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $evt->fields['acteur']==$_SESSION['dims']['userid']))) {
				if (!is_null($evt->fields['tiers_intitule'])) $detail[] = '<div style="padding-top:2px;"><b>'.($evt->fields['tiers_intitule']).'</b></div>';
				if (!is_null($evt->fields['dossier_intitule'])) $detail[] = '<div style="padding-top:2px;"><b>'.($evt->fields['dossier_intitule']).'</b></div>';
				if ($evt->fields['interne']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Interne</div>';
				if ($evt->fields['personnel']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Personnel</div>';
				if ($evt->fields['conges']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Cong�s</div>';
				if (!is_null($evt->fields['libelle'])) {
					if (!is_null($evt->fields['typeaction'])) $typea=$_DIMS['cste'][$evt->fields['typeaction']]." - ";
					else $typea="";
					$detail[] = '<div style="padding-top:2px;"><b>'.$_DIMS['cste']['_DIMS_LABEL_LABEL']."</b> : ".$typea.($evt->fields['libelle']).'</div>';
				}
				?>
				<div class="planning_planning_heure">
					<?php

					echo $_DIMS['cste']['_FROM']." ";
					echo substr($evt->fields['heuredeb'],0,2).":".substr($evt->fields['heuredeb'],3,2)."&nbsp;".$_DIMS['cste']['_APRIL_SMALL']." ".substr($evt->fields['heurefin'],0,2).":".substr($evt->fields['heurefin'],3,2); ?>
				</div>
				<?
				$detail[] = '<div style="padding-top:2px;"><b>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</b> : ".$typea.($evt->fields['description']).'</div>';

				echo implode('',$detail);
			}
			else echo $_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
		}
		//echo $evt->fields['description'];
		//echo '</div>';
		break;
}

?>
