<?php
require_once(DIMS_APP_PATH . "/modules/system/include/projects_functions.php");

//echo "<!--[if IE]><script type=\"text/javascript\" src=\"./js/excanvas.js\"></script><![endif]-->";
/****************************************************
*****************************************************
*** @author	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @author	Patrick NOURRISSIER [NETLOR]	  ***
*** @version	1.0				  ***
*** @package	projects			  ***
*** @access	public				  ***
*** @licence	GPL				  ***
*****************************************************
*****************************************************/

define ("_DAY_WIDTH", 60);
define ("_WEEK_WIDTH", 70);
define ("_MONTH_WIDTH", 70);
define ("_YEAR_WIDTH", 150);

define("_COLOR_OK","#10b836");
define("_COLOR_NON_OK","#ff0a00");
define("_COLOR_CLASSIC","#4E68A6");

if (!isset($zoom)) $zoom="m";
/* Le tableau des jours */
$day_array = array($_DIMS['cste']['_SUNDAY'],$_DIMS['cste']['_MONDAY'],$_DIMS['cste']['_THUESDAY'],$_DIMS['cste']['_WEDNESDAY'],$_DIMS['cste']['_THIRDAY'],$_DIMS['cste']['_FRIDAY'],$_DIMS['cste']['_SATURDAY']);

/* Le tableau des mois */
$month_array = array($_DIMS['cste']['_DECEMBER'],$_DIMS['cste']['_JANUARY'],$_DIMS['cste']['_FEBRUARY'],$_DIMS['cste']['_MARCH'],$_DIMS['cste']['_APRIL'],$_DIMS['cste']['_MAY'],$_DIMS['cste']['_JUNE'],$_DIMS['cste']['_JULY'],$_DIMS['cste']['_AUGUST'],$_DIMS['cste']['_SEPTEMBER'],$_DIMS['cste']['_OCTOBER'],$_DIMS['cste']['_NOVEMBER']);

$idproject=$_SESSION['dims']['currentproject'];
if($idproject>0) {

	/* On selectionne la date min et la date max entre toutes les t�ches du projet */
	$sql = "
	select		min(t.date_start) as min_date,
			max(t.date_end) as max_date
	from		dims_task as t
	where		t.id_project = :idproject";

	$res = $db->query($sql, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));

	while($fields = $db->fetchrow($res)) {
	 $varloc=dims_timestamp2local($fields['min_date']);
	 $minDate = $varloc['date'];
	 $varloc=dims_timestamp2local($fields['max_date']);
	 $maxDate = $varloc['date'];
	}

	/* Si il n'y a pas au moins une t�che, on sp�cifie des dates par d�faut */
	if ($minDate == "" || $maxDate == "") {
	$minDate = "01/01/1970";
	$maxDate = "01/01/1970";
	}

	$interval = get_view($minDate,$maxDate);
	/* On cherche le premier jour et le premier mois de la premi�re t�che */

	// extraction des jour, mois, an de la date minimale
	list($jour,$mois, $annee) = explode('/', $minDate);
	// calcul du timestamp
	$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);

	 $sql2 ="select tt.id as ttid, tt.id_task_needed, t.id as tid, t.*
		from	dims_task_task as tt
			inner	join dims_task as t
		on		t.id = tt.id_task
			inner	join	dims_project p
			on		t.id_project = p.id
			and		p.id = :idproject
			order by	t.date_start;";

	$tlink=array(); // tableau contenant
	$res2 = $db->query($sql2, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));
	while($fields2 = $db->fetchrow($res2)) {
		$tlink[$fields2['id_task_needed']][]=$fields2;
	}

	/* On selectionne toutes les t�ches li�es au projet (dans l'ordre chronologique) */
	$sql = "
	select		distinct t.*
	from		dims_task t
	inner join	dims_project p
	on		t.id_project = p.id
	and		p.id = :idproject
	order by	t.date_start";

	$res = $db->query($sql, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));

	/* Si il y � au moins une t�che, on affiche */
	if ($db->numrows($res)>0) {
	$div_h = (100+$db->numrows($res)*60);
	echo '<div class="projects_div_gantt" id="projects_div_gantt" style="height:'.$div_h.'px;min-height:500px;background-color:#FFFFFF;position:relative">';

	/* On affiche l'ent�te : en jour, en mois ou en ann�e (suivant le zoom) */
	$taille_gantt=2;
	$taille_gantt_hide=0;

	// La zone d'affichage du diagramme de gantt (le div)
	$div_area = ((substr($gantt_w,0,2)/100)*$width);

	$nb_jour_display=0;

	switch ($zoom) {
		case 's': /* On affiche les semaines  gr�ce aussi au timestamp */
			for($week = date("W",$timestamp); $week < $interval['nb_week']+date("W",$timestamp); $week++) {

			if ($div_area>$taille_gantt+150) {
				if ($week%2==0) {
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._WEEK_WIDTH.'px;height:100%;text-align:center;"><b>Sem '.$week.'</b></div>';
				} else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._WEEK_WIDTH.'px;height:100%;text-align:center;"><b>Sem '.$week.'</b></div>';
				}
			} else {
				$taille_gantt_hide+=(_WEEK_WIDTH+2);
			}
			$taille_gantt+=(_WEEK_WIDTH+2);
			}
		break;

		case 'm': /* On affiche les mois gr�ce aussi au timestamp. Note le timestamp renvoie entre 1 et 12
			  donc il faut soustraire 1 � l'indice du tableau */
			  // calcul du nombre de mois que l'on peut afficher
			  $nbmonth=intval((($_SESSION['project']['width']*$_SESSION['project']['gantt_w'])/100-80)/(_MONTH_WIDTH));
			  //date("n",$timestamp)+12
			  $x=0;
			for($month = date("n",$timestamp); $month < date("n",$timestamp)+$nbmonth; $month++) {
			//echo $div_area." --	 ".($taille_gantt+150)."<br>";
			if ($div_area>$taille_gantt+150) {
				if ($month%2==0) {
				// Modulo 12 pour afficher les 12 mois en boucle
				echo '<div class="projects_div_v_gantt1" style="position:absolute;left:'.$x.'px;top:0px;width:'._MONTH_WIDTH.'px;height:100%;text-align:center;"><b>'.$month_array[($month%12)].'</b></div>';
				} else {
				echo '<div class="projects_div_v_gantt2" style="position:absolute;left:'.$x.'px;top:0px;width:'._MONTH_WIDTH.'px;height:100%;text-align:center;"><b>'.$month_array[($month%12)].'</b></div>';
				}
				/* On augmente le nombre de jour affich�s du nombre de jour du mois courant */
				$nb_jour_display += intval(date("t",mktime(0,0,0,$month,1,date("Y",$timestamp))));
				$x+=_MONTH_WIDTH+2;
			} else {
				$taille_gantt_hide+=(_MONTH_WIDTH+2);
			}
			 $taille_gantt+=(_MONTH_WIDTH+2);
			}
		break;

		case 'a': /* On affiche les ann�es */
			for($year = date("Y",$timestamp); $year < $interval['nb_year']+date("Y",$timestamp); $year++) {

			if ($div_area>$taille_gantt+150) {
				if ($year%2==0) {
				echo '<div class="projects_div_v_gantt1" style="float:left;width:'._YEAR_WIDTH.'px;height:100%;text-align:center;"><b>'.($year).'</b></div>';

				} else {
				echo '<div class="projects_div_v_gantt2" style="float:left;width:'._YEAR_WIDTH.'px;height:100%;text-align:center;"><b>'.($year).'</b></div>';
				}
				/* On augmente le nombre de jour affich�s du nombre de jour de l'ann� courant */
				$nb_jour_display += intval(date('z',strtotime("12/31/".$year))+1);
			} else {
				$taille_gantt_hide+=(_YEAR_WIDTH+2);
			}
			$taille_gantt+=(_YEAR_WIDTH+2);
			}
		break;
	}

	/* On enl�ve 2px (pour le premier et dernier bord que l'on a compter en double) */
	$taille_gantt-=2;

	//echo	"<br><br><br><b>Le diagramme de gantt fait :".$taille_gantt.' px et on affiche seulement :'.($taille_gantt-$taille_gantt_hide).' px</b>';
	$num_task=0;

	/* la zone d'affichage r�el */
	$display_area = ($taille_gantt-$taille_gantt_hide);

	/* Bool�en pour savoir si l'on affiche le t�che ou pas (true par d�faut) */
	$display_task = 1;

	/* Le nombre de jours entre la date min et max des t�ches du projet */
	$view_nb_jour = get_view($minDate,$maxDate);

	/* Le nombre de jour entre le premier jour (01) du premier mois et le d�but de la premi�re t�che */
	$start_nb_jour = intval(substr($minDate,8,2));

	 /* Le nombre de jour entre le dernier jour du dernier mois et le fin de la derni�re t�che */
	$end_nb_jour = date( "j", mktime(0, 0, 0, substr($maxDate,3,2) + 1, 1, substr($maxDate,6,4)) - 1 );
	$end_nb_jour -= substr($maxDate,8,2);

	/* On r�cup�re le nombre de jour max que l'on a sur tout le projet
	 Le nombre de jour = le nombre de jour entre le debut et la fin des t�ches + l'espace du debut et de fin du diagramme */
	$nb_jour_total = $view_nb_jour['nb_day']+$start_nb_jour+$end_nb_jour;

	/*
	echo "<br>Zone d affichage en px : ".$display_area.
		"<br>Nombre de jour total : ".$nb_jour_total.
		"<br>Nombre de jour affich� : ".$nb_jour_display.
		"<br>Nombre de jour entre le d�but et le d�but de la premi�re t�che : ".$start_nb_jour.
		"<br>Nombre de jour entre la fin de la derni�re t�che et la fin : ".$end_nb_jour.
		"<br>";
							 */
	$tabcanvas = array();
		/* La hauteur est fixe */
		$height = '20';

	while($fields = $db->fetchrow($res)) {
			// test if already drawed
			if (!isset($tab_task[$fields['id']])) {
				/* On incr�mente l'identifiant temporaire de la t�che */
				$num_task++;

				/* render task view */
				echo renderTask($idproject,$fields,$zoom,$height,$minDate,$maxDate,$num_task,$display_area,$display_task,$tab_task,$left,$top);

				/************* AFFICHAGE DES DEPENDANCES ENTRE LES TACHES *************/

				/* on cherche toutes les t�ches dont la tache courante � besoin */
				if (isset($tlink[$fields['id']])) {
					foreach($tlink[$fields['id']] as $fields2) {

						/* On incr�mente l'identifiant temporaire de la t�che */
						$num_task++;

						/* render task view */
						echo renderTask($idproject,$fields2,$zoom,$height,$minDate,$maxDate,$num_task,$display_area,$display_task,$tab_task,$left,$top);
						/* les positions de la t�che en cours */
						$x_current_task = $left; // fixe
						$y_current_task = $top+($height/2); // fixe

						/* les informations de la t�che dont on est d�pendant */
						$x_task_needed = $tab_task[$fields2['id_task_needed']]['x'];
						$y_task_needed = $tab_task[$fields2['id_task_needed']]['y']+($height/2);
						$w_task_needed = $tab_task[$fields2['id_task_needed']]['w'];
						$d_task_needed = $tab_task[$fields2['id_task_needed']]['d'];

						// contruction du tableau de canvas
						$elemtabcanvas=array();
						$elemtabcanvas['xdeb']=$x_task_needed+$w_task_needed;
						$elemtabcanvas['ydeb']=$y_task_needed;
						$elemtabcanvas['xfin']=$x_current_task;
						$elemtabcanvas['yfin']=$y_current_task;
						$tabcanvas[]=$elemtabcanvas;
					}
				}
			}
	}

	// on affiche maintenant les canvas
	echo "<script type=\"text/javascript\">";
		foreach($tabcanvas as $id=>$canvas) {
			echo "addElementGantt(\"projects_div_gantt\",\"canvas\",\"canvas".$id,"\",\"\",".$canvas['xdeb'].",".($canvas['ydeb']).",".$canvas['xfin'].",".$canvas['yfin'].");";
		}
	echo "</script>";
	 /************* AFFICHAGE DES OBJECTIFS DU PROJET *************/

	/* on cherche tous les objectifs du projet	*/
	$sql2 = "select o.* from dims_milestone as o where o.id_project = :idproject";
	$res2 = $db->query($sql2, array(
		':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
	));

	while($fields2 = $db->fetchrow($res2)) {

		/* On r�cup�r� les temps entre la date en cours et la date minimum */
		$view_date_left = get_view($minDate, datefr2us($fields2['date']));

		 /* On r�cup�re la taille en pixel d'un jour */
		$taille_pixel = (($interval['nb_month']+1)*_MONTH_WIDTH)/$view_nb_jour['nb_day'];

		$left = 50+intval(abs(($view_date_left['nb_day']-1)*$taille_pixel));

	   /* La couleur de l'objectif */
	  $color = ($fields2['state']=="En cours")?"#ff0000":"#10840a";

		// La barre
		echo '	<div class="projects_objective" style="background:'.$color.';height:'.($div_h-30).'px;top:110px;left:'.$left.'px;" onmouseover="javascript:this.style.cursor=\'pointer\';" ondblclick="javascript:objective_delete(\''.$fields2['id'].'\',\''.$idproject.'\',\''.$zoom.'\');"></div>';

		// Le texte
		echo '	<div style="position:absolute;font-size:10px;font-weight:bold;color:'.$color.';text-align:center;float:left;top:110px;left:'.($left+15).'px;" onmouseover="javascript:this.style.cursor=\'pointer\';dims_showpopup(\''.$fields2['state'].'<br>'.$fields2['description'].'\',\'100\', event);" onmouseout="javascript:dims_hidepopup();" ondblclick="javascript:objective_change_state(\''.$fields2['id'].'\',\''.$idproject.'\',\''.$zoom.'\');">'.$fields2['label'].'</div>';

		// La fl�che du haut
		echo '	<div style="position:absolute;font-size:18px;font-weight:bold;color:'.$color.';opacity: 0.5;top:106px;left:'.($left-4).'px;" onmouseover="javascript:this.style.cursor=\'pointer\';" ondblclick="javascript:objective_delete(\''.$fields2['id'].'\',\''.$idproject.'\',\''.$zoom.'\');">/\</div>';

		// La fl�che du bas
		echo '	<div style="position:absolute;font-size:18px;font-weight:bold;color:'.$color.';opacity: 0.5;top:'.(63+$div_h).'px;left:'.($left-4).'px;" onmouseover="javascript:this.style.cursor=\'pointer\';" ondblclick="javascript:objective_delete(\''.$fields2['id'].'\',\''.$idproject.'\',\''.$zoom.'\');">\/</div>';

	}
	echo '</div>';

	/* Si il n'y � aucune t�che � afficher */
	} else {
	  echo '<div class="projects_div_gantt" style="height:400px;">';
	//echo '<img style="float:right;width:100%;height:100%;" src="./common/modules/system/img/start_big_960.png">';
	echo '</div>';
	}

	/* la l�gende (des couleurs) */
		/*
	  echo '<div class="projects_legend">

			<div style="float:left;width:100%;height:20px;">
			 <div style="width:80px">L�gende :</div>
			 <div style="background-color:'._COLOR_NON_OK.';width:94px;text-align:center;">Non termin�e</div>
			 <div style="background-color:'._COLOR_OK.';width:94px;text-align:center;">Termin�e</div>
			 <div style="background-color:'._COLOR_CLASSIC.';width:94px;text-align:center;">Future</div>
			 <img src="./common/modules/system/img/priority_1_16.png"><div>Priorit� par d�faut</div>
			 <img src="./common/modules/system/img/priority_2_16.png"><div>Priorit� moyenne</div>
			 <img src="./common/modules/system/img/priority_3_16.png"><div>Priorit� importante</div>
			</div>
			<div style="float:left;width:100%;">
			 <img src="./common/modules/system/img/add_before_16.png"><div>Ajouter avant</div>
			 <img src="./common/modules/system/img/edit_16.png"><div>Modifier</div>
			 <img src="./common/modules/system/img/attach_user_16.png"><div>Attacher un utilisateur</div>
			 <img src="./common/modules/system/img/attach_ticket_16.png"><div>Attacher un ticket</div>
			 <img src="./common/modules/system/img/attach_doc_16.png"><div>Attacher un document</div>
			 <img src="./common/modules/system/img/delete_16.png"><div>Supprimer</div>
			 <img src="./common/modules/system/img/add_after_16.png"><div>Ajouter apr�s</div>
			</div>
		   </div>';
		   */
}

?>


