<?php

require_once DIMS_APP_PATH . '/modules/system/class_action.php';

$sql = "SELECT
			a.id,
			u.lastname,
			u.firstname
		FROM
			dims_mod_business_action a
		INNER JOIN
			dims_mod_business_action_detail ad
			ON
				ad.action_id = a.id
		INNER JOIN
			dims_user u
			ON
				a.id_user = u.id
		WHERE
			a.type = ".dims_const::_PLANNING_ACTION_EVT."
		AND
			ad.tiers_id = ".$ent->fields['id'];

$res = $db->query($sql);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_EVT'], "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkevt');", ""); ?>
<div id="lkevt" style="display:<? if($db->numrows($res) > 0) echo "block"; else echo "none"; ?>;width:100%;height:160px;overflow:auto;">
<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
	<?php
			if($db->numrows($res) > 0)
			{
		?>
			<tr class="trl1" style="font-size:12px;">
				<td style="width: 3%;"/>
				<td style="width: 20%;"><? echo $_DIMS['cste']['_DIMS_LABEL_EVENT'] ?></td>
				<td style="width: 20%;"><? echo $_DIMS['cste']['_TYPE'] ?></td>
				<td style="width: 17%;"><? echo $_DIMS['cste']['_INFOS_START_DATE'] ?></td>
				<td style="width: 20%;"><? echo substr($_DIMS['cste']['_AGENDA_LABELTAB_ORGANIZERS'],0,-1); ?></td>
				<td style="width: 20%;"><? echo $_DIMS['cste']['_INFOS_CREATOR'] ?></td>
			</tr>
		<?php
				while($tab_res = $db->fetchrow($res))
				{
					$action = new action();
					$action->open($tab_res['id']);

					$date = explode("-", $action->fields['datejour']);
					$date = $date[2].'/'.$date[1].'/'.$date[0];

					if(isset($action->ctParticipate))
						$lst_contact = $action->ctParticipate;

					foreach($lst_contact as $id => $level)
					{
						if($level)
						{
							$organisateur[] = $id;
						}
					}

					if(!empty($organisateur))
					{
						$sql =	"SELECT
									c.lastname,
									c.firstname
								FROM
									dims_mod_business_contact c
								WHERE
									id in (".implode(",",$organisateur).")";

						$res=$db->query($sql);

						$nb_or= $db->numrows($res);
						$info = $db->fetchrow($res);
					}

					echo '	<tr class="'.$class_col.'">
								<td></td>
								<td style="cursor: default;" id="tickets_title_3">
									'.$action->fields['libelle'].'
								</td>
								<td style="cursor: default;" id="tickets_title_3">
									'.$_DIMS['cste'][$action->fields['typeaction']].'
								</td>
								<td style="cursor: default;" id="tickets_title_3">
									'.$date.'
								</td>
								<td align="center" style="cursor: default;" id="tickets_title_3">';
					if(!empty($organisateur))
					{
						echo strtoupper(substr($info['firstname'],0,1)).". ".$info['lastname'].'';
						if($nb_or > 1)
							echo '<br />('.$nb_or.')';
					}
					else
						echo '--';
					echo '		</td>
								<td align="center" style="cursor: default;" id="tickets_title_3">
								'.strtoupper(substr($tab_res['firstname'],0,1)).". ".$tab_res['lastname'].'
								</td>
							</tr>';
				}
			}
			else
				echo '<tr class="trl1"><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_EVENT'].'</td></tr>';
		?>
</table>
</div>
<? echo $skin->close_simplebloc(); ?>
