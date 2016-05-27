<?
echo "<table style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\">";

if (!isset($_SESSION['business']['business_monthadd'])) $_SESSION['business']['business_monthadd']=0;
// get color for type actions
$coloractions=getColorTypeActions();
$today = mktime(0,0,0,date('n'),date('j'),date('Y'));

$datefin_timestp = $datedeb_timestp = mktime(0,0,0,date('n'),date('j')+1,date('Y'));
$datedeb = date('Y-m-d',$today);
$datefin = date('Y-m-d',$datefin_timestp);

$res=$db->query("SELECT distinct(u.id)
				FROM dims_user as u
				INNER JOIN dims_mod_business_user_planning as up
				ON up.id_user_sel=.u.id
				AND up.id_user= :userid and up.display=1", array(
		':userid' => $_SESSION['dims']['userid']
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$_SESSION['business']['users'][$f['id']]=$f['id'];
	}
}

if (empty($_SESSION['business']['users'])) $_SESSION['business']['users'][$_SESSION['dims']['userid']]=$_SESSION['dims']['userid'];
$user_filter = ' AND au.user_id IN ('.implode(',',array_keys($_SESSION['business']['users'])).')';

$sql = "
		 SELECT 	a.*,
					au.user_id AS acteur,
					au.participate,
					t.id AS tiers_id,
					t.intitule AS tiers_intitule,
					d.id AS dossier_id,
					d.objet_dossier AS dossier_intitule,
					i.firstname as nom, i.lastname as prenom,
					u.color,
					u.login,
					u.firstname,
					u.lastname,
					count(au2.user_id) as cpte

		FROM 		dims_mod_business_action a

		INNER JOIN	dims_mod_business_action_detail ad on a.id = ad.action_id
		AND			a.datejour BETWEEN :datedeb AND :datefin
		AND 		a.id_module = :idmodule
		INNER JOIN 	dims_mod_business_action_utilisateur au on a.id = au.action_id
		$user_filter
		INNER JOIN 	dims_user u on au.user_id = u.id

		LEFT JOIN 	dims_mod_business_tiers t ON ad.tiers_id = t.id
		LEFT JOIN 	dims_mod_business_dossier d ON ad.dossier_id = d.id
		LEFT JOIN 	dims_mod_business_contact i ON ad.contact_id = i.id
		LEFT JOIN 	dims_mod_business_action_utilisateur as au2 on a.id = au2.action_id
		GROUP BY	a.id
		ORDER BY	a.datejour,au.user_id,a.heuredeb, a.heurefin";


// compteur sur la repartition des rdv par users
$nbbyuser=array();
foreach ($_SESSION['business']['users'] as $user) {
    $nbbyuser[$user['id']]=0;
}

$tabfilter=array();
$res=$db->query("SELECT * from dims_mod_business_enum where type='typeaction'");
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$tabfilter[$f['id']]=$f['libelle'];
	}
}

$planning = array();
$actionmulti = array();
$result = $db->query($sql, array(
	':datedeb'	=> $datedeb,
	':datefin'	=> $datefin,
	':idmodule'	=> $_SESSION['dims']['moduleid']
));
while ($fields = $db->fetchrow($result)) {
	// calcul du num�ro de journ�e par rapport au d�but de l'intervalle de recherche
	// si datejour = datedeb alors jour = 0, etc.
	$jour = business_datediff($datedeb,$fields['datejour']);
	if (!isset($planning[$jour+1])) $planning[$jour+1] = array();

	$fields['intersec'] = array(); // nb intersections
	$fields['intersec'][] = sizeof($planning[$jour+1]);

	// recherche d'intersections entre rdv (pour calculer le nb de colonnes � afficher sur une journ�e)
	if (!empty($planning[$jour+1])) {
		foreach($planning[$jour+1] as $a => $action) {
			$a_hd = $action['heuredeb'];
			$a_hf = $action['heurefin'];

			$f_hd = $fields['heuredeb'];
			$f_hf = $fields['heurefin'];

			// m�me action
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
		// on g�n�re
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

	if ($fields['cpte']>1) array_push($actionmulti,$fields['id']);
	// affectation des actions par jour de semaine (1=lundi, etc...)
	$planning[$jour+1][] = $fields;
}

$tabcorrespmulti=array();
$tabparticipate=array();
// requete pour r�cup�rer la liste des personnes rattachees au rdv pris � plusieurs
if (!empty($actionmulti)) {
	$params = array();
	$sql = "
		SELECT 		action_id,user_id,u.firstname,u.lastname,participate
		FROM 		dims_user as u
		INNER JOIN 	dims_mod_business_action_utilisateur as au
		on 			au.user_id = u.id
		AND			au.action_id in (".$db->getParamsFromArray($actionmulti, 'action', $params).")";

	$res=$db->query($sql, $params);

	while ($f=$db->fetchrow($res)) {
		$tabcorrespmulti[$f['action_id']][$f['user_id']]=strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname'];
		$tabparticipate[$f['action_id']][$f['user_id']]=$f['participate'];
	}
}

foreach($planning as $jour => $planning_jr) {
	foreach($planning_jr as $a => $fields) {

		// test si on a le droit de voir le d�tail du rdv ou non
		if ($fields['cpte']>1) $planning[$jour][$a]['icon'] = "<img src=\"./common/img/icon_group.gif\" />";
		else {
			$planning[$jour][$a]['icon'] = $fields['icon'];
		}

		if (($fields['participate']==0 && !isset($tabparticipate[$fields['id']])) || isset($tabparticipate[$fields['id']][$fields['id_user']]) && $tabparticipate[$fields['id']][$fields['id_user']]==0) {
			$planning[$jour][$a]['icon'] = "<img src=\"./common/img/properties.png\" />";
		}

		$planning[$jour][$a]['detail_datejour'] = $datedeb_timestp + 86400 * ($jour-1);

		$detail = "<div style=\"float:left;margin:0 6px 0 0;\">{$planning[$jour][$a]['icon']}</div><div>";

		$detail .= sprintf("<div><b>%s</b></div><div><b>de %s � %s</b></div>",business_datefr_planning($planning[$jour][$a]['detail_datejour']),substr($fields['heuredeb'],0,5),substr($fields['heurefin'],0,5));
		if ($fields['libelle'] != '') {
			$detail .= '<div><b>Libell&eacute;</b> : '.addslashes($fields['libelle']).'</div>';
		}

		if ($fields['typeaction'] != '') {
			$detail .= '<div><b>Type</b> : '.addslashes($fields['typeaction']).'</div>';
		}
		if ($fields['description'] != '') {
			$detail .= '<div><b>Description</b> : '.addslashes($fields['description']).'</div>';
		}

		if (!is_null($fields['tiers_intitule']) && !is_null($fields['dossier_intitule']))
		{
			$detail .= "<div><b>"._planning_LABEL_TIERS."</b> : {$fields['tiers_intitule']}</div><div><b>Dossier</b> : {$fields['dossier_intitule']}</div>";
		}

		/*if ($fields['interne']) {
			$detail .= "<div><b>Interne</b></div>";
		}
		*/
		if ($fields['personnel']) {
			$detail .= "<div><b>Personnel</b></div>";
		}

		if ($fields['conges']) {
			$detail .= "<div><b>Cong&eacute;s</b></div>";
		}
		$detailinfo="";

		if ($fields['cpte']>1) {
			$pers = array();
			$persinfo = array();
			foreach($tabcorrespmulti[$fields['id']] as $iduser=>$nom) {
				// test si participe ou pour info
				if (isset($tabparticipate[$fields['id']][$iduser]) && $tabparticipate[$fields['id']][$iduser]==0) $persinfo[]=$nom;
				else $pers[]=$nom;
			}

			if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_PARTICIP'].'</b> : '.implode(', ',$pers).'</div>';
			// calcul du pour info
			if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_TOINFO'].'</b> : '.implode(', ',$persinfo).'</div>';
		}
		$detail = '<div class="planning_planning_popup" style="background-color:'.$fields['coloraction'].';">'.$detail.'</div></div>';

		$planning[$jour][$a]['detail'] = $detail;
	}
}

echo "<tr><td colspan=\"4\" align=\"center\">";

echo $_DIMS['cste']['_DIMS_FILTER']."&nbsp;";
if (!empty($tabfilter)) {
    echo "<select style=\"width:100px\" id=\"currenttypeactionsearch\" name=\"currenttypeactionsearch\" onchange=\"javascript:afficheDesktopPlanning('currenttypeactionsearch='+document.getElementById('currenttypeactionsearch').options[document.getElementById('currenttypeactionsearch').selectedIndex].value);\"></span>";
    if (isset($currenttypeactionsearch) && $currenttypeactionsearch==0) $chselected="selected=\"selected\"";
    else $chselected="";
    echo "<option value=\"0\" $chselected>".$_DIMS['cste']['_DIMS_ALL']."</option>";
    foreach ($tabfilter as $idf=>$f) {
        if (isset($currenttypeactionsearch) && $currenttypeactionsearch==$idf) $chselected="selected=\"selected\"";
        else $chselected="";
        echo "<option value=\"".$idf."\" $chselected>".$f."</option>";
    }
    echo "</select>";
}
echo "</td></tr>";

$groupday="";
$jourday=-1;

foreach ($planning as $j => $planningjour) {
    foreach($planningjour as $a => $action) {

		$jour = business_datediff($datedeb,$action['datejour']);

		if ($jour!=$jourday) {
			$jourday=$jour;

			switch ($jourday) {
				case 0:$csteday=$_DIMS['cste']['_DIMS_LABEL_DAY'];
						break;
				case 1: $csteday=$_DIMS['cste']['_DIMS_LABEL_TOMOROW'];
						break;
				default:
						$csteday=business_dateus2fr($fields['datejour']);
						break;
			}

			echo "<tr><td colspan=\"4\" align=\"left\"><table><tr><td><img src=\"./common/img/date.gif\" alt=\"\"></td><td>
				<span style=\"margin-left:16px;\"><font class=\"fontgray\">".$csteday."</font></span>
			</td></tr></table></td></tr>";
		}

        //$column = $planning[$j][$a]['column'];
        $border="";
		$maintenant=$datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')-date('N')+($_SESSION['business']['business_weekadd']*7)+1,date('Y'));

        if (isset($_SESSION['business']['business_actionid']) && $_SESSION['business']['business_actionid']>0 && $_SESSION['business']['business_actionid']==$action['id']) {
            $color="#f0fbcc";
            $border="border-style:dotted;border-color:#9A9A9A;border-width:2px;";
        }
        else $color=$action['coloraction'];

		//calcul de diff�rence de jour
		$annee = substr($action['datejour'], 0, 4); // on r�cup�re le jour
		$mois = substr($action['datejour'], 5, 2); // puis le mois
		$jour = substr($action['datejour'], 8, 2);

		if (DIMS_DATEFORMAT==dims_const::DIMS_DATEFORMAT_FR)
			$datecumul=$jour."/".$mois."/".$annee;
		else
			$datecumul=$annee."/".$mois."/".$jour;

		$timestamp = mktime(0, 0, 0, $mois, $jour, $annee);
		//$maintenant=time();
		$ecart_secondes = $timestamp-$maintenant;
		$ecart=floor($ecart_secondes / (60*60*24));

		// correction du lien vers la semaine prochaine
		$pointer="";
		$cursor="";
		$link=dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&cat=-1&dayadd=".$ecart."&actionid=".$action['id']);
        // modification pour administrateur ayant des droits de voir
        if ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_GROUPADMIN) || $action['acteur']==$_SESSION['dims']['userid']) {
            $detailpopup=$action['detail'];
            $onclick="onclick=\"javascript:document.location.href='".$link."';\"";
            $cursor="pointer";

            // cas admin
            if (!isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) && $action['acteur']!=$_SESSION['dims']['userid']) {
                $action['icon']="<img src=\"./common/img/view.png\" alt=\"\">";
            }
        }
        else {
            $detailpopup="<br>Non disponible";
            $onclick="";
            $cursor="";
        }
        ?>
        <tr >
           <td width="20%"><? echo substr($action['heuredeb'],0,2); ?>:<? echo substr($action['heuredeb'],3,2); ?> / <? echo substr($action['heurefin'],0,2).":".substr($action['heurefin'],3,2); ?> </td>
           <td width="5%"><? echo $action['icon']; ?></td>
		   <td width="75%" onmouseover="javascript:this.style.cursor='<? echo $cursor ?>'; dims_showpopup('<? echo (dims_nl2br(addslashes($detailpopup))); ?>', '300', event);" <? echo $onclick; ?> onmouseout="javascript:this.style.cursor='<? echo $pointer; ?>'; dims_hidepopup();">
                <?
				 $typea="";
			   if ($action['personnel']) echo "Personnel / ";
			   elseif ($action['conges']) echo "cong&eacute; / ";
			   elseif (!is_null($action['typeaction']) && $action['typeaction']!="") echo $_DIMS['cste'][$action['typeaction']]." / ";

                if ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_GROUPADMIN) || $action['acteur']==$_SESSION['dims']['userid']) {
                    echo ($action['libelle'])." - ";

                    $pers=array();
                    if (isset($tabcorrespmulti[$action['id']])) {
                        $sizetab=sizeof($tabcorrespmulti[$action['id']]);

                        foreach($tabcorrespmulti[$action['id']] as $iduser=>$nom) {
                            if (!isset($tabparticipate[$action['id']][$iduser]) || $tabparticipate[$action['id']][$iduser]==1) {
                                if ($sizetab>2) {
                                    // on filtre pour ne pas nous mettre en affichage
                                    if ($iduser!=$_SESSION['dims']['userid']) $pers[]=$nom;
                                }
                                else $pers[]=$nom;
                            }
                        }
                        if (sizeof($pers)>0) {
                            echo implode(', ',$pers);
                            if (sizeof($pers)>2) echo ", ...";
                        }
                    }
                    else echo strtoupper(substr($action['firstname'],0,1)).". ".$action['lastname'];
                }
                else {
                    echo "Non disponible";
                }
                ?>
           </td>
           <?
        }
    }
echo "</table>";
?>
