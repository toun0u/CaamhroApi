<?php
define('_PLANNING_H_HEIGHT',		24);	// hauteur en px d'une heure
define('_PLANNING_LEFTCOL_WIDTH',	30); 	// largeur en px de la colonne des heures
define('_PLANNING_CAL_DAY_WIDTH',	140); 	// largeur en px d'une journée (affichage mois)
define('_PLANNING_CAL_DAY_HEIGHT',	70); 	// hauteur en px d'une journée (affichage mois)

$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,true);
$groupid=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true);

$weekadd=dims_load_securvalue('weekadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$dayadd=dims_load_securvalue('dayadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$monthadd=dims_load_securvalue('monthadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$viewmode=dims_load_securvalue('viewmode',dims_const::_DIMS_CHAR_INPUT,true,true);
$minimonthadd=dims_load_securvalue('minimonthadd',dims_const::_DIMS_CHAR_INPUT,true,true);

// ceux de l'espace courant
if ($groupid>0) {
	$_SESSION['business']['business_groupid'] = $groupid;
	$_SESSION['business']['business_userid'] = 0;
}

if ($userid>0) $_SESSION['business']['business_userid'] = $userid;
if ($weekadd!="") {
	$_SESSION['business']['business_weekadd'] = $weekadd;
	if ($weekadd==0) $_SESSION['business']['business_minimonthadd']=0;
}

if ($dayadd!="") $_SESSION['business']['business_dayadd'] = $dayadd;
if ($monthadd!="") $_SESSION['business']['business_monthadd'] = $monthadd;
if ($viewmode!="") $_SESSION['business']['business_viewmode'] = $viewmode;

if (!isset($_SESSION['business']['business_groupid'])) $_SESSION['business']['business_groupid'] = 0;
if (!isset($_SESSION['business']['business_userid'])) $_SESSION['business']['business_userid'] = $_SESSION['dims']['userid'];
if (!isset($_SESSION['business']['business_monthadd'])) $_SESSION['business']['business_monthadd'] = 0; // mois courant
if (!isset($_SESSION['business']['business_weekadd'])) $_SESSION['business']['business_weekadd'] = 0; // semaine courante
if (!isset($_SESSION['business']['business_dayadd'])) $_SESSION['business']['business_dayadd'] = 0; // jour courant
if(!isset($_SESSION['business']['business_viewmode']))$_SESSION['business']['business_viewmode']='month';//mode d'affichage
if ($_SESSION['business']['business_dayadd'] != 0) {
	$_SESSION['business']['business_weekadd'] += ($_SESSION['business']['business_dayadd'] - $_SESSION['business']['business_dayadd'] % 7) / 7;
	$_SESSION['business']['business_dayadd'] = $_SESSION['business']['business_dayadd'] % 7;
}

$currentworkspacesearch=$_SESSION['dims']['planning']['currentworkspacesearch'];
$currentprojectsearch=$_SESSION['dims']['planning']['currentprojectsearch'];
$currenttypeactionsearch=$_SESSION['dims']['planning']['currenttypeactionsearch'];

if (!isset($_SESSION['business']['business_minimonthadd'])) $_SESSION['business']['business_minimonthadd']=0;
if ($minimonthadd!="") $_SESSION['business']['business_minimonthadd'] = $minimonthadd;

if (isset($effacer_action)) {
	$action = new action();
	$action->open($effacer_action);
	$action->delete();
}

$tabfilter=array();
$res=$db->query("select * from dims_mod_business_enum where type='typeaction'");
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$tabfilter[$f['id']]=$f['libelle'];
	}
}

// get color for type actions
$coloractions=getColorTypeActions();

/* choix de l'intervalle de dates pour la requête
 *
 * dépend du mode d'affichage (jour/semaine/mois/année)
 *
 */
$today = mktime(0,0,0,date('n'),date('j'),date('Y'));

switch($_SESSION['business']['business_viewmode']) {
	case 'week':
		$wscreen= dims_load_securvalue("wscreen",dims_const::_DIMS_NUM_INPUT,true,false,false);
		$wscreen=floor($wscreen);
		if ($wscreen==0) $wscreen=120;
		define('_PLANNING_DAY_WIDTH',	$wscreen);	// largeur en pix d'une journée (mode semaine)
		// calcul du premier lundi qu correspond à la date
		if ($_SESSION['business']['business_dayadd']>0) {

		}
		$datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')-date('N')+($_SESSION['business']['business_weekadd']*7)+1,date('Y'));
		$datefin_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')-date('N')+($_SESSION['business']['business_weekadd']*7)+7,date('Y'));

		$datedeb = date('Y-m-d',$datedeb_timestp);
		$datefin = date('Y-m-d',$datefin_timestp);

		$jmax = 7;

		$prev = "&weekadd=".($_SESSION['business']['business_weekadd']-1);
		$next = "&weekadd=".($_SESSION['business']['business_weekadd']+1);
	break;

	default:
	case 'month':
		$wscreen= dims_load_securvalue("wscreen",dims_const::_DIMS_NUM_INPUT,true,false,false);
		$wscreen=floor($wscreen);
		if ($wscreen==0) $wscreen=120;
		define('_PLANNING_DAY_WIDTH',	$wscreen);	// largeur en pix d'une journée (mode semaine)

		$datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],1,date('Y'));
		$datefin_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd']+1,0,date('Y'));

		$datedeb = date('Y-m-d',$datedeb_timestp);
		$datefin = date('Y-m-d',$datefin_timestp);

		$jmax = date('t',$datefin_timestp);

		$prev = "&monthadd=".($_SESSION['business']['business_monthadd']-1);
		$next = "&monthadd=".($_SESSION['business']['business_monthadd']+1);
	break;
}

$planning = array();
$actionmulti = array();

$sql = "
		 SELECT 	a.*,
					u.id AS acteur,
					au.participate,
					u.firstname,
					u.lastname

		FROM 		dims_mod_business_action a

		INNER JOIN 	dims_task t
		ON 			t.id = a.id_task

		INNER JOIN 	dims_project p
		ON 			p.id = t.id_project
		AND 		p.type = 1

		LEFT JOIN 	dims_mod_business_action_utilisateur au
		ON 			a.id = au.action_id
		AND 		au.user_id = :userid

		LEFT JOIN 	dims_user u on a.id_user = u.id

		WHERE 		a.type = :type
		AND			a.datejour BETWEEN :datedeb AND :datefin
		AND 		a.id_workspace = :idworkspace

		GROUP BY	a.id
		ORDER BY	a.heuredeb, a.heurefin
		";
$result = $db->query($sql, array(
	':userid' => $_SESSION['dims']['userid'],
	':type' => dims_const::_PLANNING_ACTION_TSK,
	':datedeb' => $datedeb,
	':datefin' => $datefin,
	':idworkspace' => $_SESSION['dims']['currentworkspace']['id']
));

//Limitation de la vue pour les actions de type dims_const::_PLANNING_ACTION_TSK (Projet)
//on recherche tous les users rattaches aux actions
$sqlp = "SELECT         pu.id_ref as id_user,
                                        a.id as id_action,
                                        p.id_create,
                                        p.id_resp,
                                        p.id_resp2,
                                        p.id_resp3

                FROM            dims_project p

                INNER JOIN      dims_task t
                ON                      t.id_project = p.id

                INNER JOIN      dims_mod_business_action a
                ON                      a.id_task = t.id
                AND                     a.datejour BETWEEN :datedeb AND :datefin
                AND             a.id_workspace = :idworkspace

                LEFT JOIN       dims_project_user pu
                ON                      p.id = pu.id_project";

//echo $sqlp;
$resp = $db->query($sqlp, array(
	':datedeb' => $datedeb,
	':datefin' => $datefin,
	':idworkspace' => $_SESSION['dims']['workspaceid']
));
$tab_viewers = array();
//on construit le tableau de resultats
while($tab_r = $db->fetchrow($resp)) {
        if(!isset($tab_viewers[$tab_r['id_action']])) $tab_viewers[$tab_r['id_action']] = array();
        if($tab_r['id_user'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_user']] = $tab_r['id_user'];
        if($tab_r['id_create'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_create']] = $tab_r['id_create'];
        if($tab_r['id_resp'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp']] = $tab_r['id_resp'];
        if($tab_r['id_resp2'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp2']] = $tab_r['id_resp2'];
        if($tab_r['id_resp3'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp3']] = $tab_r['id_resp3'];
}

//dims_print_r($tab_viewers);

while ($fields = $db->fetchrow($result)) {
	if($fields['type'] == dims_const::_PLANNING_ACTION_TSK) {
		//on verifie si le user courant est lié à l'action courante (si non, on change d'itération (continue))
		if(!in_array($_SESSION['dims']['userid'], $tab_viewers[$fields['id']])) continue;
	}

	// calcul du numéro de journée par rapport au début de l'intervalle de recherche
	// si datejour = datedeb alors jour = 0, etc.
	$jour = business_datediff($datedeb,$fields['datejour']);
	if (!isset($planning[$jour+1])) $planning[$jour+1] = array();

	$fields['intersec'] = array(); // nb intersections
	$fields['intersec'][] = sizeof($planning[$jour+1]);

	// recherche d'intersections entre rdv (pour calculer le nb de colonnes à afficher sur une journée)
	if (!empty($planning[$jour+1])) {
		foreach($planning[$jour+1] as $a => $action) {
			$a_hd = $action['heuredeb'];
			$a_hf = $action['heurefin'];

			$f_hd = $fields['heuredeb'];
			$f_hf = $fields['heurefin'];

			// même action
			if ($action['id'] == $fields['id'])  {
				$fields['liee'][] = $a;
				$planning[$jour+1][$a]['liee'][] = sizeof($planning[$jour+1]);
			}

			if (($f_hd >= $a_hd && $f_hd <= $a_hf) || ($f_hf >= $a_hd && $f_hf <= $a_hf) || ($a_hd >= $f_hd && $a_hd <= $f_hf) || ($a_hf >= $f_hd && $a_hf <= $f_hf)) {
				$fields['intersec'][] = $a;
				$planning[$jour+1][$a]['intersec'][] = sizeof($planning[$jour+1]);
			}
		}
	}

	//test si couleur existe
	if ($fields['typeaction']=='' || !isset($coloractions[$fields['typeaction']])) $fields['coloraction']="#dcdcdc";
	else {
		$fields['coloraction']=$coloractions[$fields['typeaction']];
	}

	//calcul de l'icon
	$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$fields['color']).".png";
	$icon="";
	if (!file_exists($usericon) || $fields['color']=="" || $fields['color']!=strtoupper($fields['color'])) {
		// on génère
		$user = new user();
		$user->open($fields['id']);
		if ($user->fields['color']=="") {
			$user->fields['color']="#EFEFEF";
		}
		elseif ($fields['color']!=strtoupper($fields['color'])) {
			$user->fields['color']=strtoupper($fields['color']);
		}
		$fields['color']=$user->fields['color'];
		// generation du logo
		$user->createPicto();
		// save color
		$user->save();
	}
	$fields['icon']="<img src=\"./data/users/icon_".str_replace("#","",$fields['color']).".png\" alt=\"\" border=\"0\">";
	$fields['cpte']=1;
	//if ($fields['cpte']>1) array_push($actionmulti,$fields['id']);
	// affectation des actions par jour de semaine (1=lundi, etc...)
	$planning[$jour+1][] = $fields;
}

foreach($planning as $jour => $planning_jr) {
	foreach($planning_jr as $a => $fields) {
		$planning[$jour][$a]['detail_datejour'] = $datedeb_timestp + 86400 * ($jour-1);
	}
}

        $ajax = dims_load_securvalue("ajax", dims_const::_DIMS_CHAR_INPUT, true, false, false);
        if($ajax != "1"){
            ?><div id="block_ajax_calendar" style="display:none;"></div>
            <div style="overflow:hidden;width:100%" id="block_calendar">
		<?php
        }
			$widthcur=0;
			if ($_SESSION['business']['business_viewmode']=='month') {
				$widthcur=_PLANNING_LEFTCOL_WIDTH+((_PLANNING_DAY_WIDTH+4)*7)+15;
				echo "<div style=\"float:left;width:".$widthcur."px;\">";
			}
			else {
				$widthcur=_PLANNING_LEFTCOL_WIDTH+((_PLANNING_DAY_WIDTH+4)*$jmax)+15;
				echo "<div style=\"float:left;width:".$widthcur."px;\">";
			}

		?>
		<?php echo $skin->open_simplebloc('', 'width:100%;'); ?>
			<div class="" style="width:<? echo ($widthcur-240)?>px;float:left;">
				<div style="width:370px;margin: 0 auto;display:block;">
					<table style="width:100%"><tr><td width="50%"><table  style="width:100%"><tr>
					<?php
					echo "<td width=\"20%\">".dims_create_button("<<","","javascript:affiche_planning('".$prev."')","","","")."</td>";
					echo "<td width=\"30%\">".dims_create_button($_DIMS['cste']['_DIMS_LABEL_DAY'],"","javascript:affiche_planning('&monthadd=0&weekadd=0&dayadd=0')","","")."</td>";
					echo "<td width=\"20%\">".dims_create_button(">>","","javascript:affiche_planning('".$next."')","","")."</td>";
					?></tr></table>
					</td>
					<td align="center">

					</td>
					</tr></table>
				</div>
			</div>
			<div class="" style="width:155px;float:right;">
				<span style="width:155px;margin: 0 auto;display:block;">
						<table><tr><td><? echo $_DIMS['cste']['_DIMS_MODE']; ?></td><td>
						<?php
						$business_planning_viewmode['week'] 	= 'Semaine';
						$business_planning_viewmode['month'] 	= 'Mois';
						foreach($business_planning_viewmode as $value => $text) {
							?>
							<a title="<?php echo $text; ?>" href="#" onclick="javascript:affiche_planning('&viewmode=<? echo $value; ?>');"><img style="border:0;" alt="<? echo $text; ?>" src="./common/modules/system/img/planning_<? echo $value; ?><? echo ($_SESSION['business']['business_viewmode'] != $value) ? '_notsel' : ''; ?>.png"></a>
							<?php
						}
						?>
						</td></tr></table>
				</span>
			</div>


		<div style="width:100%;float:left;display:block;">
		<?php
		switch($_SESSION['business']['business_viewmode']) {
			case 'day':
			case 'week':
				?>
				<div style="float:left;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;height:420px;position:relative;">
				<?php
				// affichage des heures sur le côté du planning
				for ($i=dims_const::_PLANNING_H_START;$i<=dims_const::_PLANNING_H_END;$i++) {
					?>
					<div style="position:absolute;top:<?php echo 37+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;width:100%;text-align:right;color:#000000"><? echo $i; ?>h&nbsp;</div>
					<div style="position:absolute;top:<?php echo 49+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;width:100%;text-align:right;font-size:0.8em;color:#5E5E5E;;">30&nbsp;</div>
					<?php
				}
				?>
				</div>
				<?php
				$colord="";
				for ($j=1; $j<=$jmax; $j++) {
					$cols = 1;

					if (isset($planning[$j])) {
						// traitement de l'affichage
						foreach($planning[$j] as $a => $action) {
							$column_used = array();
							foreach($action['intersec'] as $b) {
								if ($b<$a) $column_used[$planning[$j][$b]['column']] = 1;
							}

							for ($column=1;$column<=$cols;$column++) {
								if (!isset($column_used[$column])) break;
							}

							$planning[$j][$a]['column'] = $column;

							if ($column>$cols) $cols = $column;
						}
					}

					$colwidth = 100/$cols;

					// détermination de la date du jour en fonction du début de l'intervalle de recherche
					$datejour = $datedeb_timestp + 86400 * ($j-1);
					$jour = date('j',$datejour);
					$mois = date('n',$datejour);
					$annee = date('y',$datejour);
					$joursem = date('w',$datejour);
					$datejour_fr = date('d/m/Y',$datejour); // pour le bouton "ajouter"

					$gras = ($today == $datejour);

					$colord = (!isset($colord) || $colord == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

					?>
					<div style="border: 1px solid rgb(192, 192, 192);margin-left:0px;background-color:<? echo $colord; ?>;float:left;width:<? echo ($_SESSION['business']['business_viewmode'] == 'day') ?_PLANNING_DAY_WIDTH-15 : _PLANNING_DAY_WIDTH; ?>px;height:420px;position:relative;">

						<?php
						// affichage des séparateurs d'heures (traits horizontaux)
						for ($i=dims_const::_PLANNING_H_START;$i<=dims_const::_PLANNING_H_END;$i++) {
							?>
							<div style="position:absolute;top:<?php echo 45+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;border-top:1px solid #c0c0c0;width:100%;"></div>
							<div style="position:absolute;top:<?php echo 57+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;border-top:1px solid #d0d0d0;width:100%;"></div>
							<?php
						}
						?>
						<div class="midb16" style="<? if ($gras) echo 'font-weight:bold;' ?>;">
						<?php printf("%s %02d/%02d/%02d",$business_jour[$joursem],$jour,$mois,$annee); ?>
						</div>

						<?php
						// affichage du détail de la journée
						if (isset($planning[$j])) {
							foreach($planning[$j] as $a => $action) {

								$column = $planning[$j][$a]['column'];

								$left = intval( 1+(($column-1)*$colwidth*_PLANNING_DAY_WIDTH)/100 );
								$width = intval( ($colwidth*_PLANNING_DAY_WIDTH)/100 ) - 3;

								$h_height = _PLANNING_H_HEIGHT;

								$hd = ($action['heuredeb'] < dims_const::_PLANNING_H_START) ? dims_const::_PLANNING_H_START : $action['heuredeb'];
								$hf = ($action['heurefin'] > dims_const::_PLANNING_H_END) ? dims_const::_PLANNING_H_END : $action['heurefin'];

								$top = intval(45 + ( substr($hd,0,2) + substr($hd,3,2)/60 - dims_const::_PLANNING_H_START)*$h_height);
								$height = (substr($hf,0,2) - substr($hd,0,2) + (substr($hf,3,2)-substr($hd,3,2))/60) * $h_height - 2;
								$border="";
								$cursor="";

								$url_to = dims_urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?action=add_dmd_insc&id_user='.$_SESSION['dims']['userid'].'&id_action='.$action['id'].'');
								$onclick="onclick=\"javascript:dims_confirmlink('".$url_to."', '".addslashes($_DIMS['cste']['_DIMS_LABEL_PROJ_DMD_INSC'])."');\"";

								if(!isset($action['participate'])) $action['participate'] = "-1";
								switch($action['participate']) {
									default:
									case '-1': //rien : couleur par défault
										$color = "#95E496";//$action['coloraction'];
										break;
									case '0': //en attente : jaune
										$color = "#EBE420";
										break;
									case '1': //inscrit : vert
										$color = "#8dc0f4";
										break;
									case '2': //refuse : orange
										$color = "#E7B257";
										break;
									case '3' : //annule : rouge
										$color = "#E75957";
										break;
								}

								?>
								<a class="planning_planning_action" <?php echo $onclick; ?> style="position:absolute;width:<? echo $width; ?>px;left:<? echo $left; ?>px;top:<? echo $top ?>px;height:<? echo $height; ?>px;background-color:<? echo $color; ?>;<? echo $border; ?>" onmouseover="javascript:displayActionDetail(event,<?php echo $action['id']; ?>,0);document.getElementById('dims_popup').style.visibility='visible';" onmouseout="javascript:document.getElementById('dims_popup').style.visibility='hidden';">
									<div style="width:100%" class="planning_planning_heure">
										<p class="planning_planning_heure"><span style="float:left;margin-right:2px;"><? echo $action['icon']; ?></span>
											<span class="planning_planning_heure" style="font-size:8px;"><? echo substr($action['heuredeb'],0,2); ?>:<? echo substr($action['heuredeb'],3,2); ?></span>
											<span class="planning_planning_heure" style="font-size:8px;"><? echo substr($action['heurefin'],0,2); ?>:<? echo substr($action['heurefin'],3,2); ?></span>
											<span class="planning_planning_heure">
											<?php
												echo ($action['libelle']);
											?>
											</span>
										</p>
									</div>
									<div class="planning_planning_detail">
									<?php
									$detail = array();
									if (true) { //$action['typeaction ']!='' &&  ($action['type']==2 || ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid']))
										if (!is_null($action['libelle'])) {
											if (!is_null($action['typeaction'])) $typea=$_DIMS['cste'][$action['typeaction']];
											else $typea="";
											$detail[] = '<div style="padding-top:2px;">'.$typea.'</div>';
										}
										echo implode('',$detail);
									}
									?>
									</div>
								</a>
								<?php
							}
						}
						?>
					</div>
					<?php
				}
			break;

			case 'month':

				$weekday = date('w', $datedeb_timestp);
				if ($weekday == 0) $weekday = 7;
				?>

				<div style="float:left;">
					<div class="planning_calendar_row">
					<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding:0px;">
						<?php echo substr($planning_mois[date('n',$datedeb_timestp)],0,3); ?>
					</div>
					<?php
					foreach($business_jour as $d) {
						?>
						<div style="float:left;overflow:hidden;">
							<div style="margin:0px;width:<?php echo _PLANNING_DAY_WIDTH; ?>px;font-size:9px;height:16px;text-align:center;">
							<?php echo $d; ?>
							</div>
						</div>
						<?php
					}
					?>
					</div>
					<?php
					if ($weekday > 1) {
						?>
						<div class="planning_calendar_row">
							<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding-top:2px;">
								<?php echo 'S'.date('W',$datedeb_timestp); ?>
							</div>
						<?php
						for ($j = 1; $j < $weekday; $j++) {
						?>
							<div style="float:left;overflow:hidden;">
								<div class="planning_calendar_day_title">
									<div style="padding:1px 1px;">&nbsp;</div>
								</div>
								<div class="planning_calendar_day" style="width:<?php echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;">
								&nbsp;
								</div>
							</div>
						<?php
						}
					}

					for ($j = 1; $j <= $jmax ; $j++) {
						//$datejour = $datedeb_timestp + 86400 * ($j-1);
						$datejour = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],$j,date('Y'));

						$jour = date('j',$datejour);
						$mois = date('n',$datejour);
						$annee = date('y',$datejour);
						$joursem = date('w',$datejour);
						$gras = ($today == $datejour);

						if ($weekday == 8) $weekday = 1;

						if ($weekday == 1) {
							?>
							<div class="planning_calendar_row">
								<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding-top:2px;">
									<?php
									echo 'S'.date('W',$datejour);
									?>
								</div>
							<?php
							//echo $sem = date('W',$datejour);
						}

						?>
							<div style="float:left;overflow:hidden;">
								<div class="midb16">
									<div style=";width:<?php echo _PLANNING_DAY_WIDTH; ?>px;padding:1px 1px;<?php if ($gras) echo 'font-weight:bold;'; ?>">
									<?php
									printf("%02d/%02d/%02d",$jour,$mois,$annee);
									?>
									</div>
								</div>
								<div class="planning_calendar_day<? if ($gras) echo '_selected'; ?>" style="width:<? echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;<? if ($gras) echo 'border-top-color:#808080;'; ?>">
								<?php
								$cols = 1;

								if (isset($planning[$j])) {
									foreach($planning[$j] as $a => $action) {
										$column_used = array();
										foreach($action['intersec'] as $b) {
											if ($b<$a) $column_used[$planning[$j][$b]['column']] = 1;
										}

										for ($column=1;$column<=$cols;$column++) {
											if (!isset($column_used[$column])) break;
										}

										$planning[$j][$a]['column'] = $column;

										if ($column>$cols) $cols = $column;
									}
								}

								$colwidth = (100-1)/$cols;

								// détermination de la date du jour en fonction du début de l'intervalle de recherche

								$gras = ($today == $datejour);
								$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

									// affichage du détail de la journée
									if (isset($planning[$j])) {
										foreach($planning[$j] as $a => $action) {
											$column = $planning[$j][$a]['column'];

											$left = intval( 1+(($column-1)*$colwidth*_PLANNING_DAY_WIDTH)/100 );
											$width = intval( ($colwidth*_PLANNING_DAY_WIDTH)/100 ) ;
											$h_height = _PLANNING_CAL_DAY_HEIGHT / (dims_const::_PLANNING_H_END - dims_const::_PLANNING_H_START);

											$hd = ($action['heuredeb'] < dims_const::_PLANNING_H_START) ? dims_const::_PLANNING_H_START : $action['heuredeb'];
											$hf = ($action['heurefin'] > dims_const::_PLANNING_H_END) ? dims_const::_PLANNING_H_END : $action['heurefin'];

											$top = intval(1 + ( substr($hd,0,2) + substr($hd,3,2)/60 - dims_const::_PLANNING_H_START)*$h_height);
											$height = (substr($hf,0,2) - substr($hd,0,2) + (substr($hf,3,2)-substr($hd,3,2))/60) * $h_height - 2;
											$cursor="";
											$pointer="";

											$url_to = dims_urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?action=add_dmd_insc&id_user='.$_SESSION['dims']['userid'].'&id_action='.$action['id'].'');
											$onclick="onclick=\"javascript:dims_confirmlink('".$url_to."', '".addslashes($_DIMS['cste']['_DIMS_LABEL_PROJ_DMD_INSC'])."');\"";

											if(!isset($action['participate'])) $action['participate'] = "-1";
											switch($action['participate']) {
												default:
												case '-1': //rien : couleur par défault
													$color = "#95E496";//$action['coloraction'];
													break;
												case '0': //en attente : jaune
													$color = "#EBE420";
													break;
												case '1': //inscrit : vert
													$color = "#8dc0f4";
													break;
												case '2': //refuse : orange
													$color = "#E7B257";
													break;
												case '3' : //annule : rouge
													$color = "#E75957";
													break;
											}


											?>
											<a class="planning_action" style="background-color:<?php echo $color; ?>;" <?php echo $onclick; ?> onmouseover="javascript:displayActionDetail(event,<?php echo $action['id']; ?>,0);document.getElementById('dims_popup').style.visibility='visible';" onmouseout="javascript:document.getElementById('dims_popup').style.visibility='hidden';">
													<div class="planning_planning_heure" style="font-size:9px;">
														<?php echo substr($action['heuredeb'],0,2).":".substr($action['heuredeb'],3,2)."&nbsp;".substr($action['heurefin'],0,2).":".substr($action['heurefin'],3,2); ?>
													</div>
												<div class="planning_info">
													<?php
													echo "/ ".substr(($action['libelle']), 0, 12);
													?>
												</div>
											</a>
									<?php
										}
									}
								?>
								</div>
							</div>
						<?php

						if ($weekday == 7) echo '</div>';
						$weekday++;
					}

					if ($weekday <= 7) {
						for ($j = $weekday; $j <= 7 ; $j++) {
						?>
							<div style="float:left;overflow:hidden;">
								<div class="planning_calendar_day_title">
									<div style="padding:1px 1px;">&nbsp;</div>
								</div>
								<div class="planning_calendar_day" style="width:<? echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;">
								&nbsp;
								</div>
							</div>
					<?php
						}
						echo '</div>';
					}
					?>
				</div>
				<?php

			break;
		}
		echo $skin->close_simplebloc();

		echo "</div>";
		//on affiche une legende pour les coloris utilises
		echo    '<div style="background-color:#FFFFFF;overflow:hidden;width:100%;">
					<p style="font-weight:bold;padding-left:2px;"><a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'div_legend\');">'.$_DIMS['cste']['_DIMS_LEGEND'].'</a></p>
					<div id="div_legend" style="display:block;">
						<div style="padding-right:10px;padding-bottom:5px;float:left;">
							<div style="width:20px;height:16px;background-color:#95E496;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
							<div style="height:16px;float:left;padding-top:4px;padding-left:5px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_NOINSC_OR_DMD'].'</div>
						</div>
						<div style="padding-right:10px;padding-bottom:5px;float:left;">
							<div style="width:20px;height:16px;background-color:#8dc0f4;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
							<div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_YOUR_INSC_OK'].'</div>
						</div>
						<div style="padding-right:10px;padding-bottom:5px;float:left;">
							<div style="width:20px;height:16px;background-color:#EBE420;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
							<div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_YOUR_INSC_WAIT'].'</div>
						</div>
						<div style="padding-right:10px;padding-bottom:5px;float:left;">
							<div style="width:20px;height:16px;background-color:#E7B257;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
							<div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_YOUR_INSC_REFUSED'].'</div>
						</div>
						<div style="padding-right:10px;padding-bottom:5px;float:left;">
							<div style="width:20px;height:16px;background-color:#E75957;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
							<div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_YOUR_INSC_DEL'].'</div>
						</div>
					</div>
				</div>';


		?>

<?php
    if($ajax != "1"){
        ?></div><?php
    }
?>
