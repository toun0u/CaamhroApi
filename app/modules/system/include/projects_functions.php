<?php

/****************************************************
*****************************************************
*** @author 	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @version  	1.0                               ***
*** @package  	projects                          ***
*** @access  	public                            ***
*** @licence	GPL                               ***
*****************************************************
*****************************************************/

function datefr2us($dateus)
{
	if ($dateus != '')
	{
		ereg(dims_const::DIMS_DATEFORMAT_EREG_FR, $dateus, $regs);
		return(date(dims_const::DIMS_DATEFORMAT_US, mktime(0,0,0,$regs[2],$regs[1],$regs[3])));
	}
	else return('');
}

function datediffpourcent($total,$pourcent,$datedecal)
{

	$duree=intval(($total*$pourcent)/100);
	echo "<br>duree:".$total." - ".$duree;
	echo $datedecal;
	$datedecal = split('/',$datedecal);

	$date_fin=mktime( 0, 0, 0, $datedecal[1], $datedecal[0]+$duree, $datedecal[2] );
	return(date('d/m/Y',$date_fin));
}

function datediff($date_debut,$date_fin)
{
	$date_debut = split('-',$date_debut);
	$date_fin = split('-',$date_fin);

	$date_debut = mktime( 0, 0, 0, $date_debut[1], $date_debut[2], $date_debut[0] );
	$date_fin = mktime( 0, 0, 0, $date_fin[1], $date_fin[2], $date_fin[0] );

	return (($date_fin - $date_debut) / (3600*24));
}

function datediff2($date_debut,$date_fin)
{
	$date_debut = split('/',$date_debut);
	$date_fin = split('/',$date_fin);

	$date_debut = mktime( 0, 0, 0, $date_debut[1], $date_debut[0], $date_debut[2] );
	$date_fin = mktime( 0, 0, 0, $date_fin[1], $date_fin[0], $date_fin[2] );

	return (($date_fin - $date_debut) / (3600*24));
}

function get_avancement($date_debut, $date_fin)
{
	$datejour = date(dims_const::DIMS_DATEFORMAT_US);
	if ($date_fin == '' || $date_debut == '') $avancement = 0;
	elseif ($datejour > $date_fin) $avancement = 100;
	else
	{
		$duree_dossier = datediff($date_debut,$date_fin);
		$temps_ecoule = datediff($date_debut,$datejour);

		$avancement = ($temps_ecoule * 100) / $duree_dossier;
		if ($avancement < 0) $avancement = 0;
	}

	return($avancement);
}

/* Permet de d�terminer le navigateur utilis� */

function get_nav() {

    if ((ereg("Nav", getenv("HTTP_USER_AGENT"))) || (ereg("Gold", getenv("HTTP_USER_AGENT"))) ||
    (ereg("X11", getenv("HTTP_USER_AGENT"))) || (ereg("Mozilla", getenv("HTTP_USER_AGENT"))) ||
    (ereg("Netscape", getenv("HTTP_USER_AGENT")))
    AND (!ereg("MSIE", getenv("HTTP_USER_AGENT"))) AND (!ereg("Konqueror", getenv("HTTP_USER_AGENT"))))
        $browser = "Netscape";
    elseif (ereg("Opera", getenv("HTTP_USER_AGENT")))
        $browser = "Opera";
    elseif (ereg("MSIE", getenv("HTTP_USER_AGENT")))
        $browser = "MSIE";
    elseif (ereg("Lynx", getenv("HTTP_USER_AGENT")))
        $browser = "Lynx";
    elseif (ereg("WebTV", getenv("HTTP_USER_AGENT")))
        $browser = "WebTV";
    elseif (ereg("Konqueror", getenv("HTTP_USER_AGENT")))
        $browser = "Konqueror";
    elseif ((eregi("bot", getenv("HTTP_USER_AGENT"))) || (ereg("Google", getenv("HTTP_USER_AGENT"))) ||
    (ereg("Slurp", getenv("HTTP_USER_AGENT"))) || (ereg("Scooter", getenv("HTTP_USER_AGENT"))) ||
    (eregi("Spider", getenv("HTTP_USER_AGENT"))) || (eregi("Infoseek", getenv("HTTP_USER_AGENT"))))
        $browser = "Bot";
    else
        $browser = "Autre";
return $browser;
}

/* Permet d'avoir une vue entre 2 dates (en jour,semaine,mois et ann�e) */

function get_view($date_deb, $date_fin) {

	$view=Array();

	$nb_jour = datediff2($date_deb,$date_fin);
	$nb_semaine = $nb_jour/7;
	$nb_mois = $nb_jour/30;

	$nb_annee = $nb_jour/365;
	$view['nb_day'] = round($nb_jour);
	$view['nb_week'] = round($nb_semaine)+1;
	$view['nb_month'] = floor($nb_mois)+1;
	$view['nb_year'] = round($nb_annee);

	return $view;
}

function get_viewTimestamp($date_deb, $date_fin) {
	$view=Array();

        $date_debut = mktime( 0, 0, 0, substr($date_deb,6,2), substr($date_deb,4,2), substr($date_deb,0,4));
	$date_fin = mktime( 0, 0, 0, substr($date_fin,6,2), substr($date_fin,4,2), substr($date_fin,0,4));

	$nb_jour = ($date_fin - $date_debut) / (3600*24);
	$nb_semaine = $nb_jour/7;
	$nb_mois = $nb_jour/30;

	$nb_annee = $nb_jour/365;
	$view['nb_day'] = round($nb_jour);
	$view['nb_week'] = round($nb_semaine) +1;
	$view['nb_month'] = floor($nb_mois)+1;
	$view['nb_year'] = round($nb_annee) +1;

	return $view;
}
/* Permet d'obtenir les informations g�n�rales d'un projet */

function displayInfos($idproject) {

        $db = dims::getInstance()->getDb();

        /* On selectionne la date min et la date max entre toutes les t�ches du projet */
        $sql = "select min(concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))) as min_date,
        max(concat(right(t.date_end, 4),'-',mid(t.date_end, 4, 2),'-',left(t.date_end, 2))) as max_date
        from dims_task as t inner join dims_project as p on t.id_project = p.id	and p.id = ".$idproject;

        $res = $db->query($sql);
        $minDate = "";
        $maxDate = "";

        while($fields = $db->fetchrow($res)) {
			$varloc=dims_timestamp2local($fields['min_date']);
			$minDate = $varloc['date'];
			$varloc=dims_timestamp2local($fields['max_date']);
			$maxDate = $varloc['date'];
		}

	/* Si il y a au moins une t�che */
	if ($minDate != "" || $maxDate != "") {

		$interval = get_view($minDate,$maxDate);

		$contents = "<br><b>Le projet comporte ".getNbTask($idproject)." t�che(s)</b>";
		$contents .= "<br><b>La premi�re t�che � d�but�e le :</b> ".$minDate;
		$contents .= "<br><b>La derni�re t�che se termine le :</b> ".$maxDate;
		$contents .= "<br><b>La dur�e totale du traitement des t�ches est de ".$interval['nb_day']." jour(s). Soit ".$interval['nb_week']." semaine(s) s'�chellonant sur ".$interval['nb_month']." mois et ".$interval['nb_year']." ann�e(s).</b> ";

	/* sinon */
	} else {
		$contents = "<br><b>Le projet ne comporte aucune t�che, vous pouvez en ajouter une via ce menu.</b>";
	}

        return addslashes($contents);
}

/* Permet de conn�itre le nombre de t�che d'un projet */

function getNbTask($idproject) {

        $db = dims::getInstance()->getDb();

        /* On selectionne toutes les t�ches li�es au projet */
        $sql = "select distinct t.*
               from dims_task as t inner join dims_project p
				on t.id_project = p.id	and p.id = ".$idproject;

        $res = $db->query($sql);

        // Le nombre de t�che
        return $db->numrows();
}

/* Permet d'attacher des utilisateurs � une t�che
  Note : On se sert de la fonction utilisant la variable de session r�f�ren�ant la gestion des tickets */

function attachUsers($show_message = false, $userlist = null, $width = 500)
{
	if (isset($_SESSION['dims']['tickets']['users_selected'])) unset($_SESSION['dims']['tickets']['users_selected']);

	?>
	<table cellpadding="0" cellspacing="0" style="width:<? echo $width; ?>;">
	<?
	if ($show_message)
	{
		?>
		<tr>
			<td><textarea name="dims_ticket_message" class="text" style="width:<? echo $width-10; ?>px;height:50px"></textarea></td>
		</tr>
		<?
	}
	if (is_null($userlist))
	{
		?>
		<tr>
			<td>
			<table style="padding:2px 0 0 0" cellspacing="0">
				<tr>
					<td>Recherche utilisateur:&nbsp;&nbsp;</td>
					<td><input type="text" id="dims_ticket_userfilter" class="text">&nbsp;</td>
					<td><a href="#" onclick="dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_search_users&dims_ticket_userfilter='+dims_getelem('dims_ticket_userfilter').value,'','div_ticket_search_result');"><img onmouseover="javascript:this.style.cursor='pointer';" style="border:0px" src="./common/img/icon_loupe.png"></a></td>
				</tr>
			</table>
			</td>
		</tr>
		<?
	}
	else
	{
		foreach($userlist as $userid)	{
			$_SESSION['dims']['tickets']['users_selected'][$userid] = $userid;
		}
	}
	?>
	</table>
	<?
	if (is_null($userlist))	{
		?>
		<div id="div_ticket_search_result" style="padding:2px 0 6px 0;">
		</div>
		Liste des utilisateurs � attacher � la t�che :
		<div id="div_ticket_users_selected" style="padding:2px 0 0 0;">
		</div>
		<?
	}

}

/*
 * Fonction de generation graphique d'une tache, appele pour le gantt
 */
function renderTask($idproject,$fields,$zoom,$height,$minDate,$maxDate,$num_task,$display_area,$display_task,&$tab_task,&$left,&$top) {
	// output buffer
	$result="";
	$left=0;
	$top=0;

	/* On r�cup�re les dur�es */
	$datestartfr=dims_timestamp2local($fields['date_start']);
	$fields['date_start']=$datestartfr['date'];
	$dateendfr=dims_timestamp2local($fields['date_end']);
	$fields['date_end']=$dateendfr['date'];

	$view_date = get_view($fields['date_start'],$fields['date_end']);

	//$view_date = get_viewTimestamp($fields['date_start'],$fields['date_end']);

	/* On r�cup�r� les temps entre la date en cours et la date minimum */
	$view_date_left = get_view($minDate, $fields['date_start']);

	/* Le coin sup gauche est plac� en dessous de la t�che pr�c�dente */
	$top = ($num_task*($height+5));

	switch($zoom) {
		case 's':

			/* ###�Attention ### --> taille_pixel_jour
			 On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
			 car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
			 car le mois de fevrier ne contient que 28 jours */

			/******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

			/* Le nombre de jour du mois courant */
			$before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields['date_start'],3,2)),1,intval(substr($fields['date_debut'],6,4)))));

			/* La somme des tailles des border */
			$date1 = intval(substr($fields['date_start'],6,4))*12+intval(substr($fields['date_start'],4,2));
			$date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
			$before_taille_border_px = 2*($date1-$date2)+1;

			/* La somme des tailles de tous les semaines (entieres)*/
			$before_taille_weeks = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields['date_start']))/30)*_WEEK_WIDTH;

			/* La taille sur le mois courant */
			$before_taille_current_month = (substr($fields['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_WIDTH;

			/* La taille en pixel de la zone du d�but du diagramme au d�but de la t�che vaut :
			   La somme des tailles de tous les semaines
			   + la taille des jours de la semaine o� la t�che commence
			   + la somme des border */
			$before_taille_pixel = $before_taille_weeks + $before_taille_current_month + $before_taille_border_px;

			/* Le nombre de jour de la zone du d�but diagramme jusqu'� la t�che vaut :
			Le nombre de jour entre la premi�re t�che et la t�che courante
			+ le jour de d�but de la premi�re t�che */
			$before_nb_jour = $view_date_left['nb_day']+substr($minDate,8,2);

			/* La taille d'un jour en px avant la t�che */
			$before_taille_jour_in_px = $before_taille_pixel/$before_nb_jour;

			/* La t�che est positionn�e � la diff�rence de jour avec le d�but de la premi�re t�che x la taille en pixel d'un jour
			+ le premier �cart de la premi�re t�che */
			$left = 5+intval(abs($before_taille_pixel));

			/******* On r�cup�re la taille en pixel d'un jour pour la zone courante de la t�che *******/

			 /* La somme des tailles des border */
			$after_taille_border_px = intval($view_date['nb_month']);

			 /* Le nombre de jour de la t�che */
			$after_nb_jour = $view_date['nb_day'];

			/* Le nombre de mois de la t�hce */
			$after_nb_mois = $view_date['nb_month'];

			/* Le nombre de jour sur les mois entier (ex : janvier/f�vrier -> 61jours) */
			$after_nb_jour_except_last_month = datediff(datefr2us($fields['date_start']),datefr2us(date("d/m/Y",mktime(0,0,0,(substr($fields['date_end'],4,2)-1),substr($fields['date_end'],0,2),substr($fields['date_end'],8,4)))))-1;

			/* Le nombre de jour sur le dernier mois */
			$after_nb_jour_last_month = $after_nb_jour - $after_nb_jour_except_last_month;

			 /* La moyenne du nombre de jour par mois sur la dur�e de la t�che */
			//$after_nb_jour_average = intval(abs($after_nb_jour / $after_nb_mois));

			/* La somme des tailles de tous les mois */
			$after_taille_months= ($view_date['nb_month']-1)*_MONTH_WIDTH;

			/* La taille sur le dernier mois courant */
			$after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_WIDTH/31);

			 /* La taille en pixel de la zone de la t�che vaut :
			   La somme des tailles de tous les mois
			   + les jours restant (-> On n'utilise pas le view car on veux une virgule)
			   + la somme des border */

			$after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

			/* La taille d'un jour en px pendant la t�che */
			$after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

			/* La largeur de la t�che */
			$width = intval(abs($after_taille_pixel));

			/* POUR DEBUG
			echo "<br>############�T�che ".$fields['label']."(".$fields['id'].")>############";
			echo "<br># Nb jour dans le mois de d�part: ".$before_nb_jour_current_month;
			echo "<br># Taille border avant la t�che : ".$before_taille_border_px;
			echo "<br># Taille border apr�s la t�che : ".$after_taille_border_px;
			echo "<br># La taille (px) des mois avant la t�che : ".$before_taille_months;
			echo "<br># La taille (px) des mois apr�s la t�che : ".$after_taille_months;
			echo "<br># La taille (px) de d�callage sur le premier mois de la t�che : ".$before_taille_current_month;
			echo "<br># Le nombre de jour sur le dernier mois de la t�che : ".$after_nb_jour_last_month;
			echo "<br># La taille (px) de plus sur le dernier mois de la t�che : ".$after_taille_current_month;

			echo "<br>#Nombre de jour de la tache : ".$view_date['nb_day'];

			echo "<br># ----> Taille en px before : ".$before_taille_pixel;
			echo "<br>#  ----> Nombre de jour before : ".$before_nb_jour;
			echo "<br>#  ----> Taille en px d un jour before: ".$before_taille_jour_in_px;
			echo "<br>#  ----> Left : ".$left." (= 5+$before_taille_pixel + ".$tab_task[$id_first_task]['x'].")";

			echo "<br># ----> Taille en px after : ".$after_taille_pixel;
			echo "<br>#  ----> Nombre de jour after : ".$after_nb_jour;
			echo "<br>#  ----> Taille en px d un jour after: ".$after_taille_jour_in_px;
			echo "<br>#  ----> Width : ".$width;

			echo "<br>#####################################################################<br>";
			/* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
			if ($left>$display_area) {
				$result.="<br> BIG DIAG GANNT -> t�che ".$fields['id']." non affich�e";
				$display_task = 0;
			} else {

				/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
				   alors on coupe le div de la t�che */
				if ($width+$left>$display_area) {
					$result.="<br>calcul ".($width+$left)." - ".($taille_gantt-$taille_gantt_hide);
					$result.="<br>BIG DIAG GANNT -> t�che ".$fields['id']." coup�e (ancienne taille :".$width."<br><br>";
					$width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)));

					$task_cut=true;
					$result.=", nouvelle taille :".$width.")";
				}
			}

			/* On sauvegarde les informations */
			$tab_task[$fields['id']]['x']=$left;
			$tab_task[$fields['id']]['y']=$top;
			$tab_task[$fields['id']]['w']=$width;
			$tab_task[$fields['id']]['d']=$fields['date_end'];
			break;

		case 'm':

			/* ###�Attention ### --> taille_pixel_jour
			 On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
			 car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
			 car le mois de fevrier ne contient que 28 jours */

			/******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

			/* Le nombre de jour du mois courant */
			$before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields['date_start'],5,2)),1,intval(substr($fields['date_start'],0,4)))));

			/* La somme des tailles des border */
			$date1 = intval(substr($fields['date_start'],6,4))*12+intval(substr($fields['date_start'],3,2));
			$date2 = intval(substr($minDate,6,4))*12+intval(substr($minDate,3,2));

			$before_taille_border_px = 2*($date1-$date2)+1;

			/* La somme des tailles de tous les mois (entiers) */
			$before_taille_months = intval(datediff(datefr2us("01/".substr($minDate,3,2)."/".substr($minDate,6,4)),datefr2us($fields['date_start']))/30)*_MONTH_WIDTH;

			/* La taille sur le mois courant */
			$before_taille_current_month = (substr($fields['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_WIDTH;

			/* La taille en pixel de la zone du d�but du diagramme au d�but de la t�che vaut :
			   La somme des tailles de tous les mois
			   + la taille des jours du mois o� la t�che commence
			   + la somme des border */
			$before_taille_pixel = $before_taille_months + $before_taille_current_month + $before_taille_border_px;

			/* Le nombre de jour de la zone du d�but diagramme jusqu'� la t�che vaut :
			Le nombre de jour entre la premi�re t�che et la t�che courante
			+ le jour de d�but de la premi�re t�che */
			$before_nb_jour = $view_date_left['nb_day']+substr($minDate,8,2);

			/* La taille d'un jour en px avant la t�che */
			$before_taille_jour_in_px = $before_taille_pixel/$before_nb_jour;

			/* La t�che est positionn�e � la diff�rence de jour avec le d�but de la premi�re t�che x la taille en pixel d'un jour
			+ le premier �cart de la premi�re t�che */
			$left = 5+intval(abs($before_taille_pixel));

			/******* On r�cup�re la taille en pixel d'un jour pour la zone courante de la t�che *******/

			 /* La somme des tailles des border */
			$after_taille_border_px = intval($view_date['nb_month']);

			 /* Le nombre de jour de la t�che */
			$after_nb_jour = $view_date['nb_day'];

			/* Le nombre de mois de la t�hce */
			$after_nb_mois = $view_date['nb_month'];

			/* Le nombre de jour sur les mois entier (ex : janvier/f�vrier -> 61jours) */
			$after_nb_jour_except_last_month = datediff(datefr2us($fields['date_start']),datefr2us(date("d/m/Y",mktime(0,0,0,(substr($fields['date_end'],4,2)-1),substr($fields['date_end'],0,2),substr($fields['date_end'],8,4)))))-1;

			/* Le nombre de jour sur le dernier mois */
			$after_nb_jour_last_month = $after_nb_jour - $after_nb_jour_except_last_month;

			 /* La moyenne du nombre de jour par mois sur la dur�e de la t�che */
			//$after_nb_jour_average = intval(abs($after_nb_jour / $after_nb_mois));

			/* La somme des tailles de tous les mois */
			$after_taille_months= ($view_date['nb_month']-1)*_MONTH_WIDTH;

			/* La taille sur le dernier mois courant */
			$after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_WIDTH/31);

			 /* La taille en pixel de la zone de la t�che vaut :
			   La somme des tailles de tous les mois
			   + les jours restant (-> On n'utilise pas le view car on veux une virgule)
			   + la somme des border */

			$after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

			/* La taille d'un jour en px pendant la t�che */
			$after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

			/* La largeur de la t�che */
			$width = intval(abs($after_taille_pixel));

			// POUR DEBUG
			/*
			echo "<br>############�T�che ".$fields['label']."(".$fields['id'].")>############";
			echo "<br># Nb jour dans le mois de d�part: ".$before_nb_jour_current_month;
			echo "<br># Taille border avant la t�che : ".$before_taille_border_px;
			echo "<br># Taille border apr�s la t�che : ".$after_taille_border_px;
			echo "<br># La taille (px) des mois avant la t�che : ".$before_taille_months;
			echo "<br># La taille (px) des mois apr�s la t�che : ".$after_taille_months;
			echo "<br># La taille (px) de d�callage sur le premier mois de la t�che : ".$before_taille_current_month;
			echo "<br># Le nombre de jour sur le dernier mois de la t�che : ".$after_nb_jour_last_month;
			echo "<br># La taille (px) de plus sur le dernier mois de la t�che : ".$after_taille_current_month;

			echo "<br>#Nombre de jour de la tache : ".$view_date['nb_day'];

			echo "<br># ----> Taille en px before : ".$before_taille_pixel;
			echo "<br>#  ----> Nombre de jour before : ".$before_nb_jour;
			echo "<br>#  ----> Taille en px d un jour before: ".$before_taille_jour_in_px;
			echo "<br>#  ----> Left : ".$left." (= 5+$before_taille_pixel + ".$tab_task[$id_first_task]['x'].")";

			echo "<br># ----> Taille en px after : ".$after_taille_pixel;
			echo "<br>#  ----> Nombre de jour after : ".$after_nb_jour;
			echo "<br>#  ----> Taille en px d un jour after: ".$after_taille_jour_in_px;
			echo "<br>#  ----> Width : ".$width;

			echo "<br>#####################################################################<br>";
			/* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
			if ($left>$display_area) {
				$result.="<br> BIG DIAG GANNT -> t�che ".$fields['id']." non affich�e";
				$display_task = 0;
			} else {

				/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
				   alors on coupe le div de la t�che */
				if ($width+$left>$display_area) {
					$result.="<br>calcul ".($width+$left)." - ".($taille_gantt-$taille_gantt_hide);
					$result.="<br>BIG DIAG GANNT -> t�che ".$fields['id']." coup�e (ancienne taille :".$width."<br><br>";
					$width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)));

					$task_cut=true;
					$result.=", nouvelle taille :".$width.")";
				}
			}

			/* On sauvegarde les informations */
			$tab_task[$fields['id']]['x']=$left;
			$tab_task[$fields['id']]['y']=$top;
			$tab_task[$fields['id']]['w']=$width;
			$tab_task[$fields['id']]['d']=$fields['date_end'];

			break;

		case 'a':

			 /* On r�cup�re la taille en pixel d'un jour (en arrondie grossier car visualisaution tr�s lointaine -> en ann�e) */
			 $taille_pixel = $display_area/$nb_jour_display;

	   /* La t�che est positionn�e � la diff�rence de jour avec le d�but de la premi�re t�che x la taille en pixel d'un jour
			 + le premier �cart de la premi�re t�che (sommes des jours et des mois) */
			$left = 5+intval(abs(($view_date_left['nb_month']-1)*30*$taille_pixel + (substr($minDate,5,2)-1)*30*$taille_pixel + substr($minDate,8,2)*$taille_pixel));

			  /* On d�termine la taille de la t�che gr�ce � sa dur�e et � la variable pr�c�dente
			  Note : on fait +$view_date['nb_year'] pour avoir une taille sup de 1px a chaque ann�e (= le border de la div) */
			 $width = intval(abs($view_date['nb_day']*$taille_pixel))+$view_date['nb_year'];

			/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt alors on coupe
			  le div de la t�che */
			if ($width+150>$div_area) {
				$result.="<br> t�che coup�e (ancienne taille :".$width;
				$width=intval(abs($taille_gantt-$taille_gantt_hide))+_DAY_WIDTH;
				$task_cut=true;
				$result.=", nouvelle taille :".$width.")";
			}

			/* On sauvegarde les informations */
			$tab_task[$fields['id']]['x']=$left;
			$tab_task[$fields['id']]['y']=$top;
			$tab_task[$fields['id']]['w']=$width;
			$tab_task[$fields['id']]['d']=$fields['date_end'];
			break;
	}

	/* La couleur d�pend du niveau de priorit� de la t�che */
	if(datefr2us(dims_getdate())>datefr2us($fields['date_end'])) {

	   switch ($fields['state']) {
		   case "En cours":
			   $color = _COLOR_NON_OK;
			   break;
		   case "Clos":
			   $color = _COLOR_OK;
			   break;
		   default:
			   $color = _COLOR_CLASSIC;
			   break;
	   }
	} else {
	   $color = _COLOR_CLASSIC;
	}

	/* Si on doit afficher la t�che */
	if ($display_task) {

		$lenght = 200;

		$task_infos = "T�che : ".$fields['label']." (".$fields['id'].")  [".$fields['state']."]";
		$task_infos .= "<br>Description : ".$fields['description'];
		$task_infos .= "<br>Priorit� : ".$fields['priority'];
		$task_infos .=  "<br>D�but : ".$fields['date_start'];
		$task_infos .=  "<br>Fin : ".$fields['date_end'];
		$task_infos .=  "<br>Nb jour : ".$view_date['nb_day'];
		$task_infos .=  "<br>Nb semaine : ".$view_date['nb_week'];
		$task_infos .=  "<br>Nb de mois : ".$view_date['nb_month'];
		$task_infos .=  "<br>Nb d\'ann�e : ".$view_date['nb_year'];

		/* Zone pour les diff�rentes op�rations qu'il est possible de faire sur une t�che */

		switch ($fields['priority']) {
			case '0':
				 $priority = '<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'"">
				 <img src="./common/modules/system/img/priority_1_16.png"></a>
				 </div>';
				 break;
			case '1':
				 $priority = '<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'"">
				 <img src="./common/modules/system/img/priority_2_16.png"></a>
				 </div>';
				 break;
			case '2':
				 $priority = '<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'"">
				 <img src="./common/modules/system/img/priority_3_16.png"></a>
				 </div>';
				 break;
			default:
				 $priority = '<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'"">
				 <img src="./common/modules/system/img/priority_1_16.png"></a>
				 </div>';
				 break;
		}

		$result.='<div id="header_'.$num_task.'" class="projects_div_h_gantt1" style="display:none;width:160px;height:15px;top:'.($top-20).'px;left:'.$left.'px;text-align:center;background-color:'.$color.'"">
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:task_near_new(event,\''.$idproject.'\',\''.$zoom.'\',\''.$fields['date_end'].'\',\'after\');" src="./common/modules/system/img/add_after_16.png"></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:task_delete(\''.$fields['id'].'\',\''.$idproject.'\',\''.$zoom.'\');" src="./common/modules/system/img/delete_16.png"></a></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:alert(\'attacher un doc \');" src="./common/modules/system/img/attach_doc_16.png"></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:alert(\'attacher un ticket \');" src="./common/modules/system/img/attach_ticket_16.png"></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:attach_user(event,\''.$fields['id'].'\',\''.$idproject.'\',\''.$zoom.'\');"  src="./common/modules/system/img/attach_user_16.png"></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:task_modify(event,\''.$fields['id'].'\',\''.$idproject.'\',\''.$zoom.'\');" src="./common/modules/system/img/edit_16.png"></div>
				<div style="float:right;width:20px;height:100%;text-align:center;background-color:'.$color.'""><img onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="javascript:task_near_new(event,\''.$idproject.'\',\''.$zoom.'\',\''.$fields['date_start'].'\',\'before\');" src="./common/modules/system/img/add_before_16.png"></div>
				 '.$priority.'
		</div>';


		/* Le label de la t�che */
		$label = ((strlen($fields['label'])*10)>$width)?substr($fields['label'], 0, $width/8)."...":$fields['label'];

		/* La t�che */
		$result.='<div id="task_'.$fields['id'].'" class="projects_div_h_gantt2" style="width:'.$width.'px;height:'.$height.'px;top:'.$top.'px;left:'.$left.'px;text-align:center;background-color:'.$color.'"
		onmouseover="javascript:this.style.cursor=\'pointer\';dims_showpopup(\''.$task_infos.'\',\''.$lenght.'\', event);"
		onmouseout="javascript:dims_hidepopup();"
		onclick="javascript:task_infos(\''.$fields['id'].'\',\''.$idproject.'\',\''.$zoom.'\');javascript:displayBarMenu(\'header_'.$num_task.'\',\'dfgdfgdfg\',\'22\');"
		ondblclick="javascript:task_change_state(\''.$fields['id'].'\',\''.$idproject.'\',\''.$zoom.'\');"
		oncontextmenu="javascript:task_link(event,\''.$fields['id'].'\', \''.$fields['date_start'].'\', \''.$fields['date_end'].'\',\''.$idproject.'\',\''.$zoom.'\');return false;">'
		.$label.'</div>';

	}
	return $result;
}
?>
