<?php
require_once DIMS_APP_PATH . '/modules/system/class_action.php';

$upevt = dims_load_securvalue('upevt', dims_const::_DIMS_NUM_INPUT, true, true, false);

$sql = "SELECT distinct
			a.id,
			u.lastname,
			u.firstname
		FROM
			dims_mod_business_action a
		INNER JOIN
			dims_user u
			ON
				a.id_user = u.id
		INNER JOIN
			dims_mod_business_event_inscription ev_ins
			ON
				ev_ins.id_action = a.id
		WHERE
			a.type = :type
		AND
			(u.id_contact = :idcontact
			OR
				(ev_ins.id_contact = :idcontact
				AND
				ev_ins.validate = 2))";
//echo $sql;
if(isset($upevt) && $upevt == 1 ) {
	$sql .= " ORDER BY		a.libelle DESC,  a.typeaction ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upevt) && $upevt == -1) {
	$sql .= " ORDER BY		a.libelle ASC,	a.typeaction ";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upevt) && $upevt == 2) {
	$sql .= " ORDER BY		a.typeaction DESC, a.libelle ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upevt) && $upevt == -2) {
	$sql .= " ORDER BY		a.typeaction ASC, a.libelle ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upevt) && $upevt == 3) {
	$sql .= " ORDER BY		u.lastname DESC, u.firstname DESC, a.libelle ASC,  a.typeaction ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upevt) && $upevt == -3) {
	$sql .= " ORDER BY		u.lastname ASC, u.firstname DESC, a.libelle ASC,  a.typeaction ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql .= " ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}


$res = $db->query($sql, array(
	':type'			=> dims_const::_PLANNING_ACTION_EVT,
	':idcontact'	=> $contact->fields['id']
));

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_EVT'], "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkevt');", ""); ?>
<div id="lkevt" style="display:<? if($db->numrows($res) > 0) echo "block"; else echo "none"; ?>;height:160px;overflow:auto;">
<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
		<?php
			if($db->numrows($res) > 0) {
		?>
		<tr class="trl1" style="font-size:12px;">
			<td style="width: 3%;"/>
			<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upevt=".$opt_trip; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_EVENT'] ?></a></td>
			<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upevt=".$opt_trit; ?>"><?php echo $_DIMS['cste']['_TYPE'] ?></a></td>
			<td style="width: 17%;"><?php echo $_DIMS['cste']['_INFOS_START_DATE'] ?></td>
			<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upevt=".$opt_tric; ?>"><?php echo $_DIMS['cste']['_INFOS_CREATOR'] ?></a></td>
		</tr>
		<?php
			$class_col = '';
			while($tab_res = $db->fetchrow($res)) {
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				$action = new action();
				$action->open($tab_res['id']);

				$date = explode("-", $action->fields['datejour']);
				$date = $date[2].'/'.$date[1].'/'.$date[0];

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
							</td>';
				echo '	<td style="cursor: default;" id="tickets_title_3">
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
<?php echo $skin->close_simplebloc(); ?>
