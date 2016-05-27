<?php

/****************************************************
*****************************************************
*** @author	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @version	1.0				  ***
*** @package	projects			  ***
*** @access	public				  ***
*** @licence	GPL				  ***
*****************************************************
*****************************************************/

/* Le tableau des jours */
$day_array = array($_DIMS['cste']['_SUNDAY_SMALL'],$_DIMS['cste']['_MONDAY_SMALL'],$_DIMS['cste']['_THUESDAY_SMALL'],$_DIMS['cste']['_WEDNESDAY_SMALL'],$_DIMS['cste']['_THIRDAY_SMALL'],$_DIMS['cste']['_FRIDAY_SMALL'],$_DIMS['cste']['_SATURDAY_SMALL']);

/* Le tableau des mois */
$month_array = array($_DIMS['cste']['_JANUARY_SMALL'],$_DIMS['cste']['_FEBRUARY_SMALL'],$_DIMS['cste']['_MARCH_SMALL'],$_DIMS['cste']['_APRIL_SMALL'],$_DIMS['cste']['_MAY_SMALL'],$_DIMS['cste']['_JUNE_SMALL'],$_DIMS['cste']['_JULY_SMALL'],$_DIMS['cste']['_AUGUST_SMALL'],$_DIMS['cste']['_SEPTEMBER_SMALL'],$_DIMS['cste']['_OCTOBER_SMALL'],$_DIMS['cste']['_NOVEMBER_SMALL'],$_DIMS['cste']['_DECEMBER_SMALL']);


if($idproject>0) {
	/* On selectionne la date min et la date max entre toutes les t�ches du projet */
	$sql = "select min(concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))) as min_date,
	max(concat(right(t.date_end, 4),'-',mid(t.date_end, 4, 2),'-',left(t.date_end, 2))) as max_date
	from dims_task t, dims_project p
	on t.id_project = p.id and p.id=:idproject";

	$res = $db->query($sql, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));

	$minDate = 0;
	$maxDate = 0;
	while($fields = $db->fetchrow($res)) {
			 $varloc=dims_timestamp2local($fields['min_date']);
			 $minDate = $varloc['date'];
			 $varloc=dims_timestamp2local($fields['max_date']);
			 $maxDate = $varloc['date'];
	}

	/* Si il n'y a pas au moins une t�che, on sp�cifie des dates par d�faut */
	if ($minDate == "" || $maxDate == "") {
	    $minDate = "01/01/1970";
	    $maxDate = "02/01/1970";
	}

	$interval = get_view($minDate,$maxDate);

	/* On d�finit la taille des div en fonction de la taille du visualisateur
	et du nombre de colone que l'on doit afficher */

	$taille_visu_px = (substr($_SESSION['project']['visu_w'],0,2)/100)*$width;
	$taille_zoom_px = (substr($_SESSION['project']['visu_zoom_w'],0,2)/100)*$taille_visu_px;

	// 20px fixes + 2px pr chaque colonne (padding et border)
	$taille_borders_d = 20+2*$interval['nb_day'];
	$taille_borders_w = 20+2*$interval['nb_week'];
	$taille_borders_m = 20+2*$interval['nb_month'];
	$taille_borders_y = 20+2*$interval['nb_year'];

	$taille_small_gantt_d = $taille_visu_px-$taille_zoom_px-$taille_borders_d;
	$taille_small_gantt_w = $taille_visu_px-$taille_zoom_px-$taille_borders_w;
	$taille_small_gantt_m = $taille_visu_px-$taille_zoom_px-$taille_borders_m;
	$taille_small_gantt_y = $taille_visu_px-$taille_zoom_px-$taille_borders_y;

	/* On d�finit les tailles */
		if ($interval['nb_day']==0) define ("_WEEK_SMALL_WIDTH",0);
	else define ("_DAY_SMALL_WIDTH", intval(abs($taille_small_gantt_d/$interval['nb_day'])));

		if ($interval['nb_week']==0) define ("_WEEK_SMALL_WIDTH",0);
		else define ("_WEEK_SMALL_WIDTH", intval(abs($taille_small_gantt_w/$interval['nb_week'])));

		if ($interval['nb_month']==0) define ("_MONTH_SMALL_WIDTH",0);
		else define ("_MONTH_SMALL_WIDTH", intval(abs($taille_small_gantt_m/$interval['nb_month'])));

		if ($interval['nb_year']==0)  define ("_YEAR_SMALL_WIDTH",0);
		else define ("_YEAR_SMALL_WIDTH", intval(abs($taille_small_gantt_y/$interval['nb_year'])));

	/* On v�rifie que rien n'est �gal � z�ro */
	(_DAY_SMALL_WIDTH==0)?1:_DAY_SMALL_WIDTH;
	(_WEEK_SMALL_WIDTH==0)?1:_WEEK_SMALL_WIDTH;
	(_MONTH_SMALL_WIDTH==0)?1:_MONTH_SMALL_WIDTH;
	(_YEAR_SMALL_WIDTH==0)?1:_YEAR_SMALL_WIDTH;

       /* On cherche le premier jour et le premier mois de la premi�re t�che */

    // extraction des jour, mois, an de la date minimale

    list($annee, $mois, $jour) = explode('/', $minDate);
    // calcul du timestamp
    $timestamp = mktime (0, 0, 0, $mois, $jour, $annee);

       /* On selectionne toutes les t�ches li�es au projet */
	$sql = "select distinct t.*
	       from dims_task t
				inner join dims_project p
		on p.id=t.id_project and p.id = :idproject order by concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))";

	$res = $db->query($sql, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));

    /* Note : on met une scroll bar car quand il y a vraiment trop de colonnes (ex : zoom jour sur un projet de 500 jours
      On ne pourrai pas voir correctement (taille colonne = 1px) */

     echo '<div class="projects_div_gantt" style="width:100%;">';

	/* Si il y � au moins une t�che, on affiche */
	if ($db->numrows()>0) {

    /* On affiche l'ent�te : en jour, en mois ou en ann�e (suivant le zoom) */
	 switch ($zoom) {

	    case j: /* On affiche les jours � partir du premier que l'on a determin� pr�c�demment */
		    for($day = date("w",$timestamp); $day < $interval['nb_day']+date("w",$timestamp); $day++) {


			    /* Pour afficher une couleur diff�rente 1 colonne sur 2 */
			     if ($day%2==0) {
				  // Modulo 7 pour afficher les 7 jours en boucle
				  echo '<div class="projects_div_v_gantt1" style="float:left;width:'._DAY_SMALL_WIDTH.'px;text-align:center;"><b>'.$day_array[$day%7].'</b></div>';
			     } else {
				 echo '<div class="projects_div_v_gantt2" style="float:left;width:'._DAY_SMALL_WIDTH.'px;text-align:center;"><b>'.$day_array[$day%7].'</b></div>';
			     }

		    }
	    break;

	    case s: /* On affiche les semaines	gr�ce aussi au timestamp */
		    for($week = date("W",$timestamp); $week < $interval['nb_week']+date("W",$timestamp); $week++) {


			    if ($week%2==0) {
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._WEEK_SMALL_WIDTH.'px;text-align:center;"><b>Sem '.$week.'</b></div>';
			    } else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._WEEK_SMALL_WIDTH.'px;text-align:center;"><b>Sem '.$week.'</b></div>';
			    }

		    }
	    break;

	    case m: /* On affiche les mois gr�ce aussi au timestamp. Note le timestamp renvoie entre 1 et 12
		      donc il faut soustraire 1 � l'indice du tableau */
		    for($month = date("n",$timestamp); $month < $interval['nb_month']+date("n",$timestamp); $month++) {

			    if ($month%2==0) {
				// Modulo 12 pour afficher les 12 mois en boucle
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._MONTH_SMALL_WIDTH.'px;text-align:center;"><b>'.$month_array[($month%12)-1].'</b></div>';
			    } else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._MONTH_SMALL_WIDTH.'px;text-align:center;"><b>'.$month_array[($month%12)-1].'</b></div>';
			    }
			    /* On augmente le nombre de jour affich�s du nombre de jour du mois courant */
			    $nb_jour_display += intval(date("t",mktime(0,0,0,$month,1,date("Y",$timestamp))));

		    }
	    break;

	    case a: /* On affiche les ann�es */
		    for($year = date("Y",$timestamp); $year < $interval['nb_year']+date("Y",$timestamp); $year++) {


			    if ($year%2==0) {
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._YEAR_SMALL_WIDTH.'px;text-align:center;"><b>'.($year).'</b></div>';

			    } else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._YEAR_SMALL_WIDTH.'px;text-align:center;"><b>'.($year).'</b></div>';
			    }
			    /* On augmente le nombre de jour affich�s du nombre de jour de l'ann� courant */
			    $nb_jour_display += intval(date('z',strtotime("12/31/".$year))+1);

		    }
	    break;

	    default: /* Comportement par d�faut : visualisation par mois */
		    for($month = date("n",$timestamp); $month < $interval['nb_month']+date("n",$timestamp)+1; $month++) {


			    if ($month%2==0) {
				// Modulo 12 pour afficher les 12 mois en boucle
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._MONTH_SMALL_WIDTH.'px;text-align:center;"><b>'.$month_array[($month%12)-1].'</b></div>';
			    } else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._MONTH_SMALL_WIDTH.'px;text-align:center;"><b>'.$month_array[($month%12)-1].'</b></div>';
			    }
			    /* On augmente le nombre de jour affich�s du nombre de jour du mois courant */
			    $nb_jour_display += intval(date("t",mktime(0,0,0,$month,1,date("Y",$timestamp))));

		    }
	}
	echo '</div>';

$num_task=0;
while($fields = $db->fetchrow($res)) {

	$num_task++;

	/* On r�cup�re les dur�es */
	$view_date = get_view(datefr2us($fields['date_start']),datefr2us($fields['date_end']));

	/* On r�cup�r� les temps entre la date en cours et la date minimum */
	$view_date_left = get_view($minDate, datefr2us($fields['date_start']));

	/* La hauteur est fixe */
	$height = '10';

	/* Le coin sup gauche est plac� en dessous de la t�che pr�c�dente */
	$top = 10+($num_task*($height+10));

	 switch($zoom) {

		case j:

		    /* ###�Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
		     car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

		    /* La somme des tailles des border */
		    $before_taille_border_px = 2*($view_date_left['nb_day'])+1;

		    /* La somme des tailles de tous les jours */
		    $before_taille_days = $view_date_left['nb_day']*_DAY_SMALL_WIDTH;

		    /* La taille en pixel de la zone du d�but du diagramme au d�but de la t�che vaut :
		       La somme des tailles de tous les jours
		       + la somme des border */
		    $before_taille_pixel = $before_taille_days + $before_taille_border_px;

		    /* Le nombre de jour de la zone du d�but diagramme jusqu'� la t�che vaut :
		    Le nombre de jour entre la premi�re t�che et la t�che courante
		    + le jour de d�but de la premi�re t�che */
		    $before_nb_jour = $view_date_left['nb_day']+substr($minDate,8,2);

		    /* La taille d'un jour en px avant la t�che */
		    $before_taille_jour_in_px = $before_taille_pixel/$before_nb_jour;

		    /* La t�che est positionn�e � la diff�rence de jour avec le d�but de la premi�re t�che
		    + le premier �cart de la premi�re t�che */
		    $left = 5+intval(abs($before_taille_pixel));

		    /******* On r�cup�re la taille en pixel d'un jour pour la zone courante de la t�che *******/

		     /* La somme des tailles des border */
		    $after_taille_border_px = intval($view_date['nb_month']);

		    /* La somme des tailles de tous les jours */
		    $after_taille_days= $view_date['nb_day']*_DAY_SMALL_WIDTH;

		     /* La taille en pixel de la zone de la t�che vaut :
		       La somme des tailles de tous les jours
		       + la somme des border */

		    $after_taille_pixel = $after_taille_days + $after_taille_border_px;

		    /* La taille d'un jour en px pendant la t�che */
		    $after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

		    /* La largeur de la t�che */
		    $width = intval(abs($after_taille_pixel));

		    /* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
		    if ($left>$display_area) {

			$display_task = 0;
		    } else {

			/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
			   alors on coupe le div de la t�che */
			if ($width+$left>$display_area) {
			    $width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)))-5;

			    $task_cut=true;
			}
		    }

		    /* On sauvegarde les positions */
		    $tab_task[$fields['id']]['x']=$left;
		    $tab_task[$fields['id']]['y']=$top;

		    break;

		case s:

		    /* ###�Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
		     car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

		    /* Le nombre de jour du mois courant */
		    $before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields['date_start'],3,2)),1,intval(substr($fields['date_start'],6,4)))));

		    /* La somme des tailles des border */
		    $date1 = intval(substr($fields['date_start'],6,4))*12+intval(substr($fields['date_start'],4,2));
		    $date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
		    $before_taille_border_px = 2*($date1-$date2)+1;

		    /* La somme des tailles de tous les semaines (entieres)*/
		    $before_taille_weeks = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields['date_start']))/30)*_WEEK_SMALL_WIDTH;

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
		    $after_taille_months= ($view_date['nb_month']-1)*_MONTH_SMALL_WIDTH;

		    /* La taille sur le dernier mois courant */
		    $after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_SMALL_WIDTH/31);

		     /* La taille en pixel de la zone de la t�che vaut :
		       La somme des tailles de tous les mois
		       + les jours restant (-> On n'utilise pas le view car on veux une virgule)
		       + la somme des border */

		    $after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

		    /* La taille d'un jour en px pendant la t�che */
		    $after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

		    /* La largeur de la t�che */
		    $width = intval(abs($after_taille_pixel));

		    /* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
		    if ($left>$display_area) {

			$display_task = 0;
		    } else {

			/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
			   alors on coupe le div de la t�che */
			if ($width+$left>$display_area) {
			     $width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)));

			    $task_cut=true;
			}
		    }

		    /* On sauvegarde les positions */
		    $tab_task[$fields['id']]['x']=$left;
		    $tab_task[$fields['id']]['y']=$top;

		    break;

		case m:

		    /* ###�Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
		     car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

		    /* Le nombre de jour du mois courant */
		    $before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields['date_start'],3,2)),1,intval(substr($fields['date_start'],6,4)))));

		    /* La somme des tailles des border */
		    $date1 = intval(substr($fields['date_start'],6,4))*12+intval(substr($fields['date_start'],4,2));
		    $date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
		    $before_taille_border_px = 2*($date1-$date2)+1;

		    /* La somme des tailles de tous les mois (entiers)*/
		    $before_taille_months = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields['date_start']))/30)*_MONTH_SMALL_WIDTH;

		    /* La taille sur le mois courant */
		    $before_taille_current_month = (substr($fields['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_SMALL_WIDTH;

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
		    $after_taille_months= ($view_date['nb_month']-1)*_MONTH_SMALL_WIDTH;

		    /* La taille sur le dernier mois courant */
		    $after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_SMALL_WIDTH/31);

		     /* La taille en pixel de la zone de la t�che vaut :
		       La somme des tailles de tous les mois
		       + les jours restant (-> On n'utilise pas le view car on veux une virgule)
		       + la somme des border */

		    $after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

		    /* La taille d'un jour en px pendant la t�che */
		    $after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

		    /* La largeur de la t�che */
		    $width = intval(abs($after_taille_pixel));

		    /* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
		    if ($left>$display_area) {
			$display_task = 0;
		    } else {

			/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
			   alors on coupe le div de la t�che */
			if ($width+$left>$display_area) {
			    $width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)));

			    $task_cut=true;

			}
		    }

		    /* On sauvegarde les positions */
		    $tab_task[$fields['id']]['x']=$left;
		    $tab_task[$fields['id']]['y']=$top;

		    break;

		case a:

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

			$width=intval(abs($taille_gantt-$taille_gantt_hide))+_DAY_SMALL_WIDTH;
			$task_cut=true;

		    }


		    /* On sauvegarde les positions */
		    $tab_task[$fields['id']]['x']=$left;
		    $tab_task[$fields['id']]['y']=$top;

		    break;

		default: /* M�me traitement que pour le mois */

		     /* ###�Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affich�
		     car cela fausse les calculs (Ex : une t�che d�butant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On r�cup�re la taille en pixel d'un jour pour la zone pr�c�dent la t�che *******/

		    /* Le nombre de jour du mois courant */
		    $before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields['date_start'],3,2)),1,intval(substr($fields['date_start'],6,4)))));

		    /* La somme des tailles des border */
		    $date1 = intval(substr($fields['date_start'],6,4))*12+intval(substr($fields['date_start'],4,2));
		    $date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
		    $before_taille_border_px = 2*($date1-$date2)+1;

		    /* La somme des tailles de tous les mois (entiers)*/
		    $before_taille_months = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields['date_start']))/30)*_MONTH_SMALL_WIDTH;

		    /* La taille sur le mois courant */
		    $before_taille_current_month = (substr($fields['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_SMALL_WIDTH;

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
		    $after_taille_months= ($view_date['nb_month']-1)*_MONTH_SMALL_WIDTH;

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

		    /* Si la t�che ne commence pas dans la partie affich�e, on ne l'affiche pas */
		   // echo $view_date_left['nb_day']."aa ".(2*(substr($fields['date_start'],3,2)))."<br>left : ".$left." & area : ".$display_area;
		    if ($left>$display_area) {

			$display_task = 0;
		    } else {

			/* Si la taille de la t�che est plus grand que la zone d'affichage du diagramme de gantt
			   alors on coupe le div de la t�che */
			if ($width+$left>$display_area) {
			   $width-=intval(abs($width+$left-($taille_gantt-$taille_gantt_hide)));

			    $task_cut=true;

			}
		    }

		    /* On sauvegarde les positions */
		    $tab_task[$fields['id']]['x']=$left;
		    $tab_task[$fields['id']]['y']=$top;


	    }


	/* La couleur d�pend du niveau de priorit� de la t�che */
	switch ($fields['priority']) {
		case 0:
		    $color = '#e0e0e0';
		    break;
		case 1:
		    $color = '#ffad4d';
		    break;
		case 2:
		    $color = '#e486d9';
		    break;
		default:
		    $color = '#e0e0e0';
		    break;
	    }

       /* La t�che */
	echo '<div class="projects_div_h_gantt2" style="width:'.$width.'px;height:'.$height.'px;top:'.$top.'px;left:'.$left.'px;text-align:center;background-color:'.$color.'"">'.substr($fields['label'], 0, $width/8).'...</div>';


}
/* Si il n'y � aucune t�che � afficher */
    } else {
	echo '<img style="width:50%;float:right" src="./common/modules/projects/img/start_small_128.png">';
	echo '</div>';
    }	       // echo _DAY_SMALL_WIDTH.' et '._WEEK_SMALL_WIDTH.' et '._MONTH_SMALL_WIDTH.' et '._YEAR_SMALL_WIDTH;
}

?>


