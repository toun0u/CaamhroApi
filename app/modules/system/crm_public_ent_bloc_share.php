<?php
	echo $skin->open_widgetbloc($_DIMS['cste']['_SHARE'], 'width:100%;', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_share.png','26px', '26px', '-17px', '-5px', $url, '', '');
	$arrayshare=array();
	$color='';
	$id_module=$ent->fields['id_module'];
	$id_object=dims_const::_SYSTEM_OBJECT_TIERS;
	if (isset($ent->fields['id']) && $ent->fields['id']>0) {
		$id_record=$ent->fields['id'];
		// chargement des partages existants
		$arrayshare=$dims->getShares(1, dims_const::_SYSTEM_OBJECT_TIERS, $id_record);
	}
	if (empty($arrayshare)) {
		echo $_DIMS['cste']['_DIMS_NO_SHARE'];
	}
	else {
		echo '<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">';
		echo '<tr class="trl1">
				<td style="width: 15%;text-align:center;">'.$_DIMS['cste']['_TYPE'].'</td>
                <td style="width: 70%;">'.$_DIMS['cste']['_DIMS_LABEL_ORIGIN'].'</td>';
                //<td style="width: 15%;">'.$_DIMS['cste']['_DIMS_LABEL_VIEW'].'</td></tr>';

		// boucle sur les partages
		$icon='';
		$label='';
		foreach($arrayshare as $k=>$sh) {
			if ($sh['type_from']==0 && isset($works[$sh['id_from']]) || $sh['type_from']==1) {
				switch($sh['type_from']) {
					case 0: // partageable
						$icon="<img src=\"./common/img/share.png\" alt=\"\">";
						$works = $dims->getAllWorkspaces();
						$work= $works[$sh['id_from']];
						$label=$work['label'];
						break;
					case 1:
					default:
						$icon="<img src=\"./common/img/users.png\" alt=\"\">";
						$works = $dims->getAllWorkspaces();
						$work= $works[$sh['id_from']];
						$label=$work['label'];
						break;
					case 2:
						$icon="<img src=\"./common/img/user.png\" alt=\"\">";
						$usr= new user();
						$usr->open($sh['id_from']);
						$label=$usr->fields['firstname']." ".$usr->fields['lastname'];
						break;
				}
			}
			$color=($color==1) ? 2 : 1;
			if ($icon!='' && $label!='') {
				echo '<tr class="trl'.$color.'">
					<td style="text-align:center;">'.$icon.'</td>
					<td>'.$label.'</td>';
			}
			// on regarde si on peut supprimer un partage
			/*if ($_SESSION['dims']['workspaceid']==$sh['id_from']) {
					echo "<td><a href=\"javascript:dims_confirmlink('".dims_urlencode($scriptenv."?cat="._BUSINESS_CAT_CONTACT."&op=deletepublicshare&action="._BUSINESS_TAB_ENT_FORM."&id_work=".$_SESSION['dims']['workspaceid']."&id_tiers=".$ent->fields['id']."&part="._BUSINESS_TAB_ENT_IDENTITE)."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\"></a></td>";
			}*/
			echo '</tr>';
		}
		echo '</table>';
	}
	echo $skin->close_widgetbloc();
?>
