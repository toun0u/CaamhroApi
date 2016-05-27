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

define ("_WEEK_WIDTH", 70);
define ("_MONTH_WIDTH", 70);
define ("_INFOS_WIDTH", 150);

define("_COLOR_OK","#10b836");
define("_COLOR_NON_OK","#ff0a00");
define("_COLOR_CLASSIC","#4E68A6");

/* Le tableau des mois */
$month_array = array($_DIMS['cste']['_DECEMBER'],$_DIMS['cste']['_JANUARY'],$_DIMS['cste']['_FEBRUARY'],$_DIMS['cste']['_MARCH'],$_DIMS['cste']['_APRIL'],$_DIMS['cste']['_MAY'],$_DIMS['cste']['_JUNE'],$_DIMS['cste']['_JULY'],$_DIMS['cste']['_AUGUST'],$_DIMS['cste']['_SEPTEMBER'],$_DIMS['cste']['_OCTOBER'],$_DIMS['cste']['_NOVEMBER']);

 /* On selectionne la date min et la date max entre toutes les tâches du projet */
$sql = "select min(concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))) as min_date,
    max(concat(right(t.date_end, 4),'-',mid(t.date_end, 4, 2),'-',left(t.date_end, 2))) as max_date
    from dims_mod_prjt_task t";

$res = $db->query($sql);


while($fields = $db->fetchrow($res)) {

     $minDate = $fields['min_date'];
     $maxDate = $fields['max_date'];
}

/* Si il n'y a pas au moins une tâche, on spécifie des dates par défaut */
if ($minDate == "" || $maxDate == "") {
    $minDate = "01/01/1970";
    $maxDate = "01/01/1970";
}

$interval = get_view($minDate,$maxDate);

/* On cherche le premier jour et le premier mois de la première tâche */
list($annee, $mois, $jour) = explode('-', $minDate);
$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);


/* On veut connaître la hauteur de notre affichage */

$h = 50;
$sql = "select * from dims_mod_prjt_project";
$res = $db->query($sql);
while($fields = $db->fetchrow($res)) {
     $h+=20; /* une bande */
     $h+=(getNbTask($fields['id'])*5); /* La taille des tâches */
}

/* On selectionne toutes les tâches de tous les projets (dans l'ordre chronologique) */
$sql = "select distinct t.*
    from dims_mod_prjt_task t, dims_mod_prjt_project p, dims_mod_prjt_project_task pt
    order by concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))";

$res = $db->query($sql);

echo '<div class="projects_div_gantt" style="widht:100%;height:'.$h.'px;margin-top:20px;">';

/* Si il y à au moins une tâche, on affiche */
if ($db->numrows()>0) {

     /* On affiche l'entête : en jour, en mois ou en année (suivant le zoom) */
      echo '<div style="float:left;width:'._INFOS_WIDTH.'px;height:100%;text-align:center;"></div>';

     switch ($zoom) {

	/* Zoom semaine */
	case s:
	       /* On initialise la semaine de départ*/
		 $week=date("W",$timestamp);

	       for($w = _WEEK_WIDTH; $w < ($width-_INFOS_WIDTH-10); $w+=_WEEK_WIDTH) {
		    $week++;
		    if ($week%2==0) {
			 echo '<div class="projects_div_v_gantt1" style="float:left;width:'._WEEK_WIDTH.'px;height:100%;text-align:center;"><b>Sem '.$week.'</b></div>';
		    } else {
			 echo '<div class="projects_div_v_gantt2" style="float:left;width:'._WEEK_WIDTH.'px;height:100%;text-align:center;"><b>Sem '.$week.'</b></div>';
		    }
		}
	break;

	/* Zoom mois */
	case m:
		/* On initialise le mois de départ */
		$month = date("n",$timestamp);

		for($w = _MONTH_WIDTH; $w < ($width-_INFOS_WIDTH-10); $w+=_MONTH_WIDTH) {
		    $month++;
			if ($month%2==0) {
			    // Modulo 12 pour afficher les 12 mois en boucle
			    echo '<div class="projects_div_v_gantt1" style="float:left;width:'._MONTH_WIDTH.'px;height:100%;text-align:center;"><b>'.$month_array[($month%12)].'</b></div>';
			} else {
			    echo '<div class="projects_div_v_gantt2" style="float:left;width:'._MONTH_WIDTH.'px;height:100%;text-align:center;"><b>'.$month_array[($month%12)].'</b></div>';
			}
		}
	break;

	default: break;
    }
    echo '</div>';

/* La première bande */
$top = 130;
/*
 *
 echo '<div style="position:absolute;width:99%;height:10px;top:'.$top.'px;left:5px;text-align:center;background-color:#f0f0f0"
		onmouseover="javascript:this.style.cursor=\'pointer\';dims_showpopup(\''.$task_infos.'\',\''.$lenght.'\', event);"
		onmouseout="javascript:dims_hidepopup();"
		'.$label.'</div>';
$top += 10;
*/
/* On selectionne tous les projets */
$sql = "select * from dims_mod_prjt_project";
$res = $db->query($sql);

while($fields = $db->fetchrow($res)) {


	echo '<div style="position:absolute;width:'.(_INFOS_WIDTH-5).'px;height:10px;top:'.$top.'px;left:5px;text-align:center;background-color:#f0f0f0"><a href="'.$scriptenv.'?op=project_view_gantt&idproject='.$fields['id'].'"">'.$fields['label'].'</a></div>';

	/* On enlève 2px (pour le premier et dernier bord que l'on a compter en double) */
	$taille_gantt-=2;

	//echo	"<br><br><br><b>Le diagramme de gantt fait :".$taille_gantt.' px et on affiche seulement :'.($taille_gantt-$taille_gantt_hide).' px</b>';
	$num_task=0;

	/* la zone d'affichage réel */
	$display_area = ($taille_gantt-$taille_gantt_hide);

	/* Booléen pour savoir si l'on affiche le tâche ou pas (true par défaut) */
	$display_task = 1;

	/* Le nombre de jours entre la date min et max des tâches du projet */
	$view_nb_jour = get_view($minDate,$maxDate);

	/* Le nombre de jour entre le premier jour (01) du premier mois et le début de la première tâche */
	$start_nb_jour = intval(substr($minDate,8,2));

	 /* Le nombre de jour entre le dernier jour du dernier mois et le fin de la dernière tâche */
	$end_nb_jour = date( "j", mktime(0, 0, 0, substr($maxDate,5,2) + 1, 1, substr($maxDate,0,4)) - 1 );
	$end_nb_jour -= substr($maxDate,8,2);

	/* On récupère le nombre de jour max que l'on a sur tout le projet
	 Le nombre de jour = le nombre de jour entre le debut et la fin des tâches + l'espace du debut et de fin du diagramme */
	$nb_jour_total = $view_nb_jour['nb_day']+$start_nb_jour+$end_nb_jour;


	/* On selectionne toutes les tâches liées au projet (dans l'ordre chronologique) */
$sql2 = "select distinct t.*
    from dims_mod_prjt_task t, dims_mod_prjt_project p, dims_mod_prjt_project_task pt
    where t.id = pt.id_task and pt.id_project=:idproject order by concat(right(t.date_start, 4),'-',mid(t.date_start, 4, 2),'-',left(t.date_start, 2))";

$res2 = $db->query($sql2, array(
	':idproject' => array('type' => PDO::PARAM_INT, 'value' => $fields['id']),
));

	while($fields2 = $db->fetchrow($res2)) {

	    /* On incrémente l'identifiant temporaire de la tâche */
	    $num_task++;

	    $tab_task_y=array();

	    /* On récupère les durées */
	    $view_date = get_view(datefr2us($fields2['date_start']),datefr2us($fields2['date_end']));

	    /* On récupéré les temps entre la date en cours et la date minimum */
	    $view_date_left = get_view($minDate, datefr2us($fields2['date_start']));

	    /* La hauteur est fixe */
	    $height = '5';

	    /* Le coin sup gauche est placé en dessous de la tâche précédente */
	    $top += ($height+2);

	    switch($zoom) {


		case s:

		    /* ### Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affiché
		     car cela fausse les calculs (Ex : une tâche débutant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On récupère la taille en pixel d'un jour pour la zone précédent la tâche *******/

		    /* Le nombre de jour du mois courant */
		    $before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields2['date_start'],3,2)),1,intval(substr($fields2['date_debut'],6,4)))));

		    /* La somme des tailles des border */
		    $date1 = intval(substr($fields2['date_start'],6,4))*12+intval(substr($fields2['date_start'],4,2));
		    $date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
		    $before_taille_border_px = 2*($date1-$date2)+1;

		    /* La somme des tailles de tous les semaines (entieres)*/
		    $before_taille_weeks = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields2['date_start']))/30)*_WEEK_WIDTH;

		    /* La taille sur le mois courant */
		    $before_taille_current_month = (substr($fields2['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_WIDTH;

		    /* La taille en pixel de la zone du début du diagramme au début de la tâche vaut :
		       La somme des tailles de tous les semaines
		       + la taille des jours de la semaine où la tâche commence
		       + la somme des border */
		    $before_taille_pixel = $before_taille_weeks + $before_taille_current_month + $before_taille_border_px;

		    /* Le nombre de jour de la zone du début diagramme jusqu'à la tâche vaut :
		    Le nombre de jour entre la première tâche et la tâche courante
		    + le jour de début de la première tâche */
		    $before_nb_jour = $view_date_left['nb_day']+substr($minDate,8,2);

		    /* La taille d'un jour en px avant la tâche */
		    $before_taille_jour_in_px = $before_taille_pixel/$before_nb_jour;

		    /* La tâche est positionnée à la différence de jour avec le début de la première tâche x la taille en pixel d'un jour
		    + le premier écart de la première tâche */
		    $left = _INFOS_WIDTH+5+intval(abs($before_taille_pixel));

		    /******* On récupère la taille en pixel d'un jour pour la zone courante de la tâche *******/

		     /* La somme des tailles des border */
		    $after_taille_border_px = intval($view_date['nb_month']);

		     /* Le nombre de jour de la tâche */
		    $after_nb_jour = $view_date['nb_day'];

		    /* Le nombre de mois de la tâhce */
		    $after_nb_mois = $view_date['nb_month'];

		    /* Le nombre de jour sur les mois entier (ex : janvier/février -> 61jours) */
		    $after_nb_jour_except_last_month = datediff(datefr2us($fields2['date_start']),datefr2us(date("d/m/Y",mktime(0,0,0,(substr($fields2['date_end'],4,2)-1),substr($fields2['date_end'],0,2),substr($fields2['date_end'],8,4)))))-1;

		    /* Le nombre de jour sur le dernier mois */
		    $after_nb_jour_last_month = $after_nb_jour - $after_nb_jour_except_last_month;

		     /* La moyenne du nombre de jour par mois sur la durée de la tâche */
		    //$after_nb_jour_average = intval(abs($after_nb_jour / $after_nb_mois));

		    /* La somme des tailles de tous les mois */
		    $after_taille_months= ($view_date['nb_month']-1)*_MONTH_WIDTH;

		    /* La taille sur le dernier mois courant */
		    $after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_WIDTH/31);

		     /* La taille en pixel de la zone de la tâche vaut :
		       La somme des tailles de tous les mois
		       + les jours restant (-> On n'utilise pas le view car on veux une virgule)
		       + la somme des border */

		    $after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

		    /* La taille d'un jour en px pendant la tâche */
		    $after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

		    /* La largeur de la tâche */
		    $width = intval(abs($after_taille_pixel));

		    break;

		case m:

		    /* ### Attention ### --> taille_pixel_jour
		     On ne peu pas prendre la taille d'affichage total / nombre de jour total affiché
		     car cela fausse les calculs (Ex : une tâche débutant le 1er mars s'affichera fin fevrier
		     car le mois de fevrier ne contient que 28 jours */

		    /******* On récupère la taille en pixel d'un jour pour la zone précédent la tâche *******/

		    /* Le nombre de jour du mois courant */
		    $before_nb_jour_current_month = intval(date("t",mktime(0,0,0,intval(substr($fields2['date_start'],3,2)),1,intval(substr($fields2['date_debut'],6,4)))));

		    /* La somme des tailles des border */
		    $date1 = intval(substr($fields2['date_start'],6,4))*12+intval(substr($fields2['date_start'],4,2));
		    $date2 = intval(substr($minDate,0,4))*12+intval(substr($minDate,5,2));
		    $before_taille_border_px = 2*($date1-$date2)+1;

		    /* La somme des tailles de tous les mois (entiers)*/
		    $before_taille_months = intval(datediff(datefr2us("01/".substr($minDate,5,2)."/".substr($minDate,0,4)),datefr2us($fields2['date_start']))/30)*_MONTH_WIDTH;

		    /* La taille sur le mois courant */
		    $before_taille_current_month = (substr($fields2['date_start'],0,2)/$before_nb_jour_current_month)*_MONTH_WIDTH;

		    /* La taille en pixel de la zone du début du diagramme au début de la tâche vaut :
		       La somme des tailles de tous les mois
		       + la taille des jours du mois où la tâche commence
		       + la somme des border */
		    $before_taille_pixel = $before_taille_months + $before_taille_current_month + $before_taille_border_px;

		    /* Le nombre de jour de la zone du début diagramme jusqu'à la tâche vaut :
		    Le nombre de jour entre la première tâche et la tâche courante
		    + le jour de début de la première tâche */
		    $before_nb_jour = $view_date_left['nb_day']+substr($minDate,8,2);

		    /* La taille d'un jour en px avant la tâche */
		    $before_taille_jour_in_px = $before_taille_pixel/$before_nb_jour;

		    /* La tâche est positionnée à la différence de jour avec le début de la première tâche x la taille en pixel d'un jour
		    + le premier écart de la première tâche */
		    $left = _INFOS_WIDTH+5+intval(abs($before_taille_pixel));

		    /******* On récupère la taille en pixel d'un jour pour la zone courante de la tâche *******/

		     /* La somme des tailles des border */
		    $after_taille_border_px = intval($view_date['nb_month']);

		     /* Le nombre de jour de la tâche */
		    $after_nb_jour = $view_date['nb_day'];

		    /* Le nombre de mois de la tâhce */
		    $after_nb_mois = $view_date['nb_month'];

		    /* Le nombre de jour sur les mois entier (ex : janvier/février -> 61jours) */
		    $after_nb_jour_except_last_month = datediff(datefr2us($fields2['date_start']),datefr2us(date("d/m/Y",mktime(0,0,0,(substr($fields2['date_end'],4,2)-1),substr($fields2['date_end'],0,2),substr($fields2['date_end'],8,4)))))-1;

		    /* Le nombre de jour sur le dernier mois */
		    $after_nb_jour_last_month = $after_nb_jour - $after_nb_jour_except_last_month;

		     /* La moyenne du nombre de jour par mois sur la durée de la tâche */
		    //$after_nb_jour_average = intval(abs($after_nb_jour / $after_nb_mois));

		    /* La somme des tailles de tous les mois */
		    $after_taille_months= ($view_date['nb_month']-1)*_MONTH_WIDTH;

		    /* La taille sur le dernier mois courant */
		    $after_taille_current_month = ($view_date['nb_day']%30)*(_MONTH_WIDTH/31);

		     /* La taille en pixel de la zone de la tâche vaut :
		       La somme des tailles de tous les mois
		       + les jours restant (-> On n'utilise pas le view car on veux une virgule)
		       + la somme des border */

		    $after_taille_pixel = $after_taille_months + $after_taille_current_month + $after_taille_border_px;

		    /* La taille d'un jour en px pendant la tâche */
		    $after_taille_jour_in_px = $after_taille_pixel/$view_date['nb_day'];

		    /* La largeur de la tâche */
		    $width = intval(abs($after_taille_pixel));


		    break;

		default: break;

	  }

	   /* La couleur dépend de la date et de l'état de la tâche */
	    if(datefr2us(dims_getdate())>datefr2us($fields2['date_end'])) {

	       switch ($fields2['state']) {
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

	    /* Si on doit afficher la tâche */
	    if ($display_task) {

		$lenght = 200;

		$task_infos = "Tâche : ".$fields2['label']." (".$fields2['id'].")  [".$fields2['state']."]";
		$task_infos .= "<br>Description : ".$fields2['description'];
		$task_infos .= "<br>Priorité : ".$fields2['priority'];
		$task_infos .=	"<br>Début : ".$fields2['date_start'];
		$task_infos .=	"<br>Fin : ".$fields2['date_end'];

		 /* La tâche */
		echo '<div id="task_'.$fields2['id'].'" class="projects_div_h_gantt3" style="width:'.$width.'px;height:'.$height.'px;top:'.$top.'px;left:'.$left.'px;text-align:center;background-color:'.$color.'"
		onmouseover="javascript:this.style.cursor=\'pointer\';dims_showpopup(\''.$task_infos.'\',\''.$lenght.'\', event);"
		onmouseout="javascript:dims_hidepopup();"
		'.$label.'</div>';

	    }

	}

	$top+=10;
	echo '<div style="position:absolute;width:99%;height:2px;top:'.$top.'px;left:5px;text-align:center;background-color:#f0f0f0"></div>';
	$top+=10;


    }

    /* La dernière bande */
   //echo '<div style="position:absolute;width:99%;height:10px;top:'.$top.'px;left:5px;text-align:center;background-color:#f0f0f0"></div>';

}
	  /* la légende (des couleurs) */
	  echo '<div class="projects_legend">
	       <div style="float:left;width:'._INFOS_WIDTH.'px;">Légende :</div>
	       <div style="background-color:'._COLOR_NON_OK.';width:94px;text-align:center;">Non terminée</div>
	       <div style="background-color:'._COLOR_OK.';width:94px;text-align:center;">Terminée</div>
	       <div style="background-color:'._COLOR_CLASSIC.';width:94px;text-align:center;">Future</div>
	       </div>';

?>


