<?php

$tab_evt = array();
$nb_niv2=0;


$reset_search_event= dims_load_securvalue('reset_search_event', dims_const::_DIMS_NUM_INPUT, true,true,true);

if ($reset_search_event>0) {
	unset($_SESSION['dims']['search_evt']);
	unset($_SESSION['dims']['search_typeaction']);
	unset($_SESSION['dims']['evt_filter']);
}

if (!isset($_SESSION['dims']['search_evt'])) $_SESSION['dims']['search_evt']='';
$search_evt= dims_load_securvalue('search_evt', dims_const::_DIMS_CHAR_INPUT, true,true,true,$_SESSION['dims']['search_evt'],'');

if (!isset($_SESSION['dims']['search_typeaction'])) $_SESSION['dims']['search_typeaction']='';
$typeaction= dims_load_securvalue('typeaction', dims_const::_DIMS_CHAR_INPUT, true,true,true,$_SESSION['dims']['search_typeaction'],'');

$opt='';

if ($typeaction!='') {
	$opt = " AND a.typeaction = '".$typeaction."' ";
}
//$search_evt= dims_load_securvalue('search_evt', dims_const::_DIMS_CHAR_INPUT, true,true);
//$passed_evt= dims_load_securvalue('passed_evt', dims_const::_DIMS_NUM_INPUT, true,true);
//if (isset($_POST['passed_evt'])) {
//	$passed_evt=1;
//}
//else {
//	$passed_evt=0;
//}
if (!isset($_SESSION['dims']['evt_filter'])) $_SESSION['dims']['evt_filter']='';
$activ_filter = dims_load_securvalue('evt_filter', dims_const::_DIMS_CHAR_INPUT, true, true,true,$_SESSION['dims']['evt_filter'],'');

switch($activ_filter) {
	default:
	case 'all':
		$jointure = ' '; //LEFT JOIN dims_mod_business_event_inscription ei ON ei.id_action = a.id
		$opt .= " ";
		break;
	case 'clos':
		$opt .= " AND a.close = 1 ";
		$jointure = "";
		break;
	case 'past':
		$opt .= " AND a.datejour <= CURDATE() ";
		$jointure = "";
		break;
	case 'coming':
		$opt .= " AND a.datejour > CURDATE() ";
		$jointure = "";
		break;
	case 'wait' :
		$jointure = ' INNER JOIN dims_mod_business_event_inscription ei ON ei.id_action = a.id ';
		$opt .= " ";
		break;
	case 'lvl1':
		$opt .= " AND a.niveau = 1 ";
		$jointure = "";
		break;
	case 'lvl2':
		$opt .= " AND a.niveau = 2 ";
		$jointure = "";
		break;
	case 'lvl1a':
		$opt .= " AND a.niveau = 1";
		$jointure = " INNER JOIN dims_mod_business_event_inscription ei ON ei.id_action = a.id ";
		break;
	case 'lvl2a':
		$opt .= " AND a.niveau = 2 ";
		$jointure = " INNER JOIN dims_mod_business_event_inscription ei ON ei.id_action = a.id ";
		break;
	case 'models':
		$opt .= " AND a.is_model >= 1 AND a.libelle LIKE '%_model'";
		$jointure = "";
		$passed_evt = 1;
		break;
}

// on charge les countries
// conversion des tags vers country
$resu=$db->query("select * from dims_country");

$countries=array();

$c=0;
if ($db->numrows($resu)>0) {
	while ($a=$db->fetchrow($resu)) {
		$countries[$a['id']]=$a['printable_name'];
	}
}

//Recherche des events a administrer et inscription a valider
$param = array();
$sql = 'SELECT
			a.id AS id_evt,
			a.libelle,
			a.typeaction,
			a.datejour,
			a.datefin,
			a.heuredeb,
			a.heurefin,
			a.close,
			a.niveau,
			a.id_country
		FROM
			dims_mod_business_action a
		INNER JOIN
			dims_user u
			ON
				u.id = a.id_user
		'.$jointure.'
		WHERE
			a.type = :type
		AND	a.id_workspace = :workspaceid
		AND
			a.id_parent = 0';
$param[':type'] = dims_const::_PLANNING_ACTION_EVT;
$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
/*
if (!$currentworkspace['activeevent']) {
	 $sql.='   AND
			(
				u.id = '.$_SESSION['dims']['userid'].'
			OR
				a.id_organizer = '.$_SESSION['dims']['user']['id_contact'].'
			OR
				a.id_responsible = '.$_SESSION['dims']['user']['id_contact'].'
			)
		'.$opt;
*/
	$sql.=' '.$opt;
	//if ($passed_evt==0) {
	//	$sql.=" and a.datejour >= CURDATE()";
	//}
	$retour = '';
	switch($action) {
		case 'view_models_events':
			$sql.=" AND a.is_model >= 1 AND a.libelle LIKE '%_model' AND a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS'";
			$retour = 'models';
			break;
		case 'view_old_events':
			$sql.=" AND ( a.is_model = 0 OR ( a.is_model >= 1 AND a.libelle NOT LIKE '%_model' )) AND a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS'";
			$retour = 'fairs';
			break;
		default:
			$sql.=" AND a.is_model = 0 AND a.typeaction NOT LIKE '_DIMS_PLANNING_FAIR_STEPS' ";
			$retour = 'events';
			break;
	}

	if ($search_evt!='') {
		$search_evt_sql= dims_load_securvalue('search_evt', dims_const::_DIMS_CHAR_INPUT, true,true,true);
		$sql.=" AND ( a.libelle like :searchevt1 OR a.typeaction like :searchevt2 OR a.description like :searchevt3 )";
		$param[':searchevt1'] = "%$search_evt_sql%" ;
		$param[':searchevt2'] = "%$search_evt_sql%" ;
		$param[':searchevt3'] = "%$search_evt_sql%" ;
	}

	$sql.=' ORDER BY a.datejour DESC, a.niveau DESC';

$ressource = $db->query($sql, $param);

//Mise en table
$array_req = '0,';
$niv2  = 0;
while($result = $db->fetchrow($ressource))
{
	if($result['niveau'] == 2) $niv2++;
	$tab_evt[$result['id_evt']] = $result;
	$array_req .= $result['id_evt'].',';
}

$sql_cpt = 'SELECT		id_action,
						COUNT(ei.validate) AS cpt_insc,
						ei.validate
			FROM		dims_mod_business_event_inscription ei
			WHERE		id_action IN ('.substr($array_req,0,-1).')
			GROUP BY	ei.id_action, ei.validate';

//echo $sql_cpt;

$res_cpt = $db->query($sql_cpt);

while($compteur = $db->fetchrow($res_cpt)) {
	$tab_nbrinsc[$compteur['id_action']][$compteur['validate']] = $compteur['cpt_insc'];
}

//dims_print_r($tab_nbrinsc);

//dims_print_r($tab_evt);


//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF']);

?>
<div style="padding:0px 5px 0px 5px">
	<table width="100%">
		<tr>
			<td>
				<div class="filtre_header_news">
				<form name="form_filter" action="<? dims_urlencode('admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_admin_events');?>" method="post">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" width="32%">
							<?
							echo $_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="search_evt" name="search_evt" value="'.htmlspecialchars($search_evt).'"><a href="javascript:void(0);" onclick="javascript:document.form_filter.submit();"><img src="./common/img/search.png" border="0"></a>';
							?>
							</td>
							<td align="left" width="38%">
								<? echo $_DIMS['cste']['_DIMS_FILTER'] ?>&nbsp;
								<select id="evt_filter" name="evt_filter" onchange="javascript:document.form_filter.submit();">
									<option value="">--</option>
									<option value="all" <?php if($activ_filter == 'all') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_EVT_ALL'] ?></option>
									<option value="past" <?php if($activ_filter == 'past') echo 'selected="selected"'; ?>><?php echo $_DIMS['cste']['_DIMS_LABEL_EVT_PAST']; ?></option>
									<option value="coming" <?php if($activ_filter == 'coming') echo 'selected="selected"'; ?>><?php echo $_DIMS['cste']['_DIMS_LABEL_EVT_COMING']; ?></option>
									<option value="wait" <?php if($activ_filter == 'wait') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT']; ?></option>
									<option value="clos" <?php if($activ_filter == 'clos') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_CLOSED_INSCR_EVT']; ?></option>
									<option value="lvl1" <?php if($activ_filter == 'lvl1') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_EVENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?> 1</option>
								<? if($niv2 > 0) { ?>
									<option value="lvl2" <?php if($activ_filter == 'lvl2') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_EVENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?> 2</option>
								<? } ?>
									<option value="lvl1a" <?php if($activ_filter == 'lvl1a') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_EVENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_LEVEL'].' 1 : '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT']; ?></option>
								<? if($niv2 > 0) { ?>
									<option value="lvl2a" <?php if($activ_filter == 'lvl2a') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_EVENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_LEVEL'].' 2 : '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT']; ?></option>
								<? }
								//<option value="models" <?php if($activ_filter == 'models') echo 'selected="selected"'; >Models</option>
								?>

								</select>
							</td>
							<td align="center">

														<?php echo $_DIMS['cste']['_TYPE']; ?>
															<select class="select" name="typeaction" onchange="javascript:document.form_filter.submit();">

															<?php
															//<option value="">--</option>
															$listenum = array();

															//$listenum = business_getlistenum('typeaction');
															$listenum = business_getlistenum('typeaction_evt');

															if($enabledAdminEvent && $enableeventsteps== false) {
																	foreach($listenum as $tab_id => $tabe) {
																			$id_tab = array_search('_DIMS_PLANNING_FAIR_STEPS', $tabe);
																			if($id_tab != false ) {
																					unset($listenum[$tab_id]);
																			}
																	}
															}

															if($enabledAdminEvent == false && $enableeventsteps) {
																	foreach($listenum as $tab_id => $tabe) {
																			$id_tab = array_search('_DIMS_PLANNING_FAIR_STEPS', $tabe);
																			if($id_tab == false ) {
																					unset($listenum[$tab_id]);
																			}
																	}
															}

															foreach($listenum as $id_enum => $enum) {
																	if(isset($typeaction) && $enum['libelle'] == $typeaction) $sel = 'selected';
																	elseif($enabledAdminEvent == false && $enableeventsteps && $enum['libelle'] == '_DIMS_PLANNING_FAIR_STEPS') {
																		$sel = 'selected';
																		$action->fields['typeaction']='_DIMS_PLANNING_FAIR_STEPS';
																		$action->fields['niveau'] = 2;
								$action->fields['allow_fo']=1;
																	}
																	else $sel = '';

																	echo "<option $sel value=\"".html_entity_decode($enum['libelle'])."\">{$_DIMS['cste'][$enum['libelle']]}</option>";
															}
															?>
							</select>
							</td>
							<td align="right">
								<a href="/admin.php?dims_action=public&action=view_admin_events&ssubmenu=0&reset_search_event=1"><? echo $_SESSION['cste']['RESET_ADVANCED_SEARCH']; ?> <img style="border:0px;" src="./common/img/close.png" alt="reset"></a>
								<?php

									//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_ADD_EVENT'], './common/img/icon_add.gif', "location.href='admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&op=xml_planning_modifier_action&type=2&id=0';");
									//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_ADD_EVENT'], './common/img/icon_add.gif', "location.href='admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=add_evt&type=2&id=0';");
								// http://inetv2/admin.php?dims_mainmenu=events&submenu=8&dims_desktop=block&dims_action=public&action=add_evt&id=591
								?>
							</td>
						</tr>
					</table>
				</form>
				</div>
			</td>
		</tr>
	</table>
	<div class="table_news_group">
	<table width="100%" style="border-collapse: collapse;">
		<tr class="trl1">

			<th class="title_table_news" style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?></th>
			<th class="title_table_news" ><?php echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?></th>
			<th class="title_table_news" style="width: 10%;"><?php echo $_DIMS['cste']['_TYPE']; ?></th>
			<th class="title_table_news" style="width: 10%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?></th>
			<th class="title_table_news" ><?php echo $_DIMS['cste']['_INFOS_START_DATE']; ?></th>
			<th class="title_table_news" ><?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?></th>
			<th class="title_table_news" ><?php echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S']; ?></th>
			<th class="title_table_news" ><?php echo $_DIMS['cste']['_DIMS_LABEL_EDIT']; ?></th>
			<th><?php echo $_DIMS['cste']['_INFOS_STATE']; ?></th>
			<th><?php echo $_DIMS['cste']['_PLANNING']; ?></th>
		</tr>
		<?php
			$class='ligne_2_news';

			if(count($tab_evt) > 0) {
				$nb_niv2 = 0;
				foreach($tab_evt as $event) {
					$act = 1;
					if($event['niveau'] == 2) $nb_niv2++;
					if($event['close'] == 0) {
						$chg_state = '<img src="./common/img/delete.png" alt="'.$_DIMS['cste']['_DELETE'].'" />';
					}
					elseif($event['close'] == 1) {
						//en fait, si on remet l'icone ...
						$chg_state = '<img src="./common/img/delete.png" alt="'.$_DIMS['cste']['_DELETE'].'" />';//'<img src="./common/img/add.gif" alt="" />';
						$act = 0;
					}
					/** Mise en forme date */
					$date_evt = array();
					preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event['datejour'],$date_evt);
					/***********************/
					$date_fin = array();
					//ereg('^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$',$event['datefin'],$date_fin);
					preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event['datefin'],$date_fin);
					/***********************/

					/*** compte des inscriptions **/
					$inscrit = '';
					if(isset($tab_nbrinsc[$event['id_evt']])) {
						switch($event['niveau']) {
							case 1 :
								if(isset($tab_nbrinsc[$event['id_evt']][0])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_grey.png"/> : '.$tab_nbrinsc[$event['id_evt']][0];
								}
								if(isset($tab_nbrinsc[$event['id_evt']][2])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_green.png"/> : '.$tab_nbrinsc[$event['id_evt']][2];
								}
								if(isset($tab_nbrinsc[$event['id_evt']][-1])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_red.png"/> : '.$tab_nbrinsc[$event['id_evt']][-1];
								}
								break;
							case 2 :
								if(isset($tab_nbrinsc[$event['id_evt']][0])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_grey.png"/> : '.$tab_nbrinsc[$event['id_evt']][0];
								}
								if(isset($tab_nbrinsc[$event['id_evt']][1])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_orange.png"/> : '.$tab_nbrinsc[$event['id_evt']][1];
								}
								if(isset($tab_nbrinsc[$event['id_evt']][2])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_green.png"/> : '.$tab_nbrinsc[$event['id_evt']][2];
								}
								if(isset($tab_nbrinsc[$event['id_evt']][-1])) {
									$inscrit .=' <img src="./common/modules/system/img/ico_point_red.png"/> : '.$tab_nbrinsc[$event['id_evt']][-1];
								}
								break;
						}
					}
					else {
						$inscrit = $_DIMS['cste']['_DIMS_LABEL_NO_REGISTRATION'];
					}

					echo '<tr class="'.$class.'"  style="text-align: center" >';

					echo '<td style="text-align: left;" onclick="location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\'">';
						echo $event['libelle'];
					echo '</td>';
					echo '<td  onclick="location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\'">';
						echo $event['niveau'];
					echo '</td>';
					echo '<td  onclick="location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\'">';
						echo $_DIMS['cste'][$event['typeaction']];
					echo '</td>';
										echo "<td>";
					// recherche du pays
					if (isset($countries[$event['id_country']])) {
						echo $countries[$event['id_country']];
					}
					else echo "-";
										echo '</td>';
					echo '<td onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\';">';
						echo $date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1];
					echo '</td>';
					echo '<td onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\';">';
						echo $date_fin[3].'/'.$date_fin[2].'/'.$date_fin[1];
					echo '</td>';


					echo '<td onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\';">';
					echo $inscrit;
					echo '</td><td>';


					$chg_disabled = '<img src="./common/img/go-down.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_DISABLED'].'" />';
					$chg_active = '<img src="./common/img/go-up.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_ACTIVE'].'" />';

					// on affiche le lien vers les personnes
					echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" href="javascript: void(0);" onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$event['id_evt'].'\';">';
					echo '<img src="./common/img/contact.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" />';
					echo '</a>&nbsp;&nbsp;';

					// on fait le lien en edition direct
					echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" href="javascript: void(0);" onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&dims_desktop=block&dims_action=public&action=add_evt&id='.$event['id_evt'].'\';">';
					echo '<img src="./common/img/edit.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" />';
					echo '</a>&nbsp;&nbsp;';

					if($action != 'view_models_events') {
						if ($act == 1) {
							echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_DISABLED'].'" href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=xml_planning_disabled_action&action_id='.$event['id_evt'].'&retour='.$retour.'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_disabled.'</a>';
							echo '</a>&nbsp;&nbsp;';
						}
						else {
							// on peut reactiver
							echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_ACTIVE'].'" href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=xml_planning_active_action&action_id='.$event['id_evt'].'&retour='.$retour.'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_active.'</a>';
							echo '</a>&nbsp;&nbsp;';
						}
					}

					//suppression
					echo '<a title="'.$_DIMS['cste']['_DELETE'].'" href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=xml_planning_delete_action&action_id='.$event['id_evt'].'&retour='.$retour.'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_state.'</a>&nbsp;&nbsp;';

					if($action != 'view_models_events') {
						//creation d'un model � partir d'un event existant
						if($event['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') {
							echo '&nbsp;<a href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=create_model&action_id='.$event['id_evt'].'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/data_view.png" title="'.$_DIMS['cste']['_DIMS_CREATE_MODEL_FAIR'].'"/></a>';
							echo '</a>';
						}
						else {
							echo '&nbsp;<a href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=create_clone&action_id='.$event['id_evt'].'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/zoom2.png" title="'.$_DIMS['cste']['_DIMS_LABEL_EVENT_CREATE_COPY'].'"/></a>';
							echo '</a>';
						}
					}
					echo '</td><td>';

					if($event['close'] == 0) {
						echo '<img title="'.$_DIMS['cste']['_DIMS_LABEL_OPENED_INSCR_EVT'].'" src="./common/modules/system/img/ico_point_green.png" />';
					}
					else {
						echo '<img title="'.$_DIMS['cste']['_DIMS_LABEL_CLOSED_INSCR_EVT'].'" src="./common/modules/system/img/ico_point_red.png" />';
					}

					// affichage lien planning + calcul du mois de d�calage
					$day1="1";
					$month1=date("n");
					$year1=date("Y");
					$date2=$date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1];
					list($day2,$month2,$year2) = explode("/",$date2);
					$nbmonth = (($year2-$year1)*12 + ($month2-$month1));
					echo '<td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_PLANNING.'&dims_desktop=block&dims_action=public&cat=-1&viewmode=month&monthadd='.$nbmonth.'" title="Planning">
					<img src="./common/img/calendar.png" alt="Mois" style="border: 0pt none ;"/>';'
					</a></td>';

					echo '</tr>';
					$class = ($class == 'ligne_2_news') ? '' : 'ligne_2_news';
				}
			}
			else
				echo '<tr><td align="center" colspan="8">'.$_DIMS['cste']['_DIMS_LABEL_NO_EVENT'].'</td></tr>';
		?>
	</table>
	</div>
</div>
<?php
		echo '<div>
					<p>
						'.$_DIMS['cste']['_DIMS_LEGEND'].' :
					</p>
					<p>
						<ul>
							<li>
								<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.png" />
								'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'
							</li>';

				if($nb_niv2 > 0 && $enableeventsteps) {
					 echo	'<li>
								<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'" src="./common/modules/system/img/ico_point_orange.png" />
								'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'
							</li>';
				}
				echo		'<li>
								<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'" src="./common/modules/system/img/ico_point_green.png" />
								'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'
							</li>
							<li>
								<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'" src="./common/modules/system/img/ico_point_red.png" />
								'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'
							</li>
						</ul>
					</p>
				</div>';
//echo $skin->close_simplebloc();
echo "<script language=\"JavaScript\" type=\"text/JavaScript\">document.getElementById('search_evt').focus();</script>";
?>
