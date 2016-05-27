<?
echo "<div style=\"width:100%;display:block;margin-left:20%;float:right;\">";

// liste des contacts incomplets
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');

	//on regarde d'abord quel droit de partage on a
	$cur_wksp = new workspace();
	$cur_wksp->open($_SESSION['dims']['workspaceid']);
	$sqltagfilter='';
	$in = '0';

	$sql_in = "	SELECT	id_to
				FROM	dims_workspace_share
				WHERE	id_from = :idfrom
				AND		active = 1
				AND		id_object = :idobject ";
	$res_in = $db->query($sql_in, array(
		':idfrom'	=> $_SESSION['dims']['workspaceid'],
		':idobject'	=> dims_const::_SYSTEM_OBJECT_CONTACT
	));

	if($db->numrows($res_in) >= 1) {
		while($tabw = $db->fetchrow($res_in)) {
			$in .= ", ".$tabw['id_to'];
		}
		$in .= ", ".$_SESSION['dims']['workspaceid']; //on ajoute le workspace courant sinon il sera exclu des recherches
	}
	else {
		$in = $_SESSION['dims']['workspaceid'];
	}

	$_SESSION['business']['search_ct']	= array();
	$search_ct = array();
	$_SESSION['business']['search_ent']	= array();
	$_SESSION['business']['search_lkent'] = array();
	$_SESSION['business']['search_ct_sql'] = array();

	//les infos generiques
	$inf_ct = new contact();
	$inf_ct->init_description();
	$inf_ct->setvalues($_POST, "ct_");

	$search_ct=$inf_ct->fields;

	//les infos entreprise
	$inf_e = new tiers();
	$inf_e->init_description();
	$inf_e->setvalues($_POST, "ent_");

	$_SESSION['business']['search_ent']=$inf_e->fields;
	if($_SESSION['business']['search_ent']['partenaire'] == -1)  $_SESSION['business']['search_ent']['partenaire'] = "";

	//les infos du lien pers->ent
	$inf_cte = new tiersct();
	$inf_cte->init_description();
	$inf_cte->setvalues($_POST, "lke_");

	$_SESSION['business']['search_lkent']=$inf_cte->fields;

	//if($where != '' || $where_ent!='') {
	$sql_s = "";
	$sql_s .= "	SELECT		DISTINCT c.id as id_ct, c.lastname, c.firstname, c.timestp_modify, c.email, c.phone, c.address, c.postalcode, c.city, c.country, c.inactif ";
	$sql_s .= "	FROM		dims_mod_business_contact c ";

	$sql_s .= "	WHERE		c.lastname!=''
				AND			c.firstname!=''
				AND			c.id_workspace IN (".$in.")";
	$sql_s .= "	AND			email=''
				AND			phone=''
				AND			address=''
				AND			postalcode=''
				AND			city=''
				AND			country=''";
	$sql_s .= " ORDER BY	c.lastname, c.firstname";


$r = $db->query($sql_s);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONTACTS']." : ".$db->numrows($r),"width:40%;float:left");

echo '<div style="width:100%;height:500px;overflow:auto;">';

if ($db->numrows($r) > 0){
	echo '<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'
					</td>
					<td>
						'.$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME'].'
					</td>
				</tr>';
	$class = 'trl2';
	while ($res = $db->fetchrow($r)){
		echo '<tr style="cursor:pointer;" class="'.$class.'" onclick="javascript:window.location=\''.$tabscriptenv.'?cat=0&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$res['id_ct'].'\'">';
		echo '<td>'.$res['lastname'].'</td>';
		echo '<td>'.$res['firstname'].'</td>';
		//echo '<td><a href="/admin.php?cat=0&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$res['id_ct'].'"><img src="./common/img/view.png"></a></td>';
		echo '</tr>';
		if ($class == 'trl2') $class = 'trl1' ; else $class = 'trl2' ;
		//dims_print_r($res);die();
	}

	echo '</table>';
}else{
	echo '	<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						Pas de contact
					</td>
				</tr>
			</table>';
}
echo '</div>';
echo $skin->close_simplebloc();

// graphs
echo '<div style="width:50%;float:right;margin-left:20px;"';

// nombre de mise à jour de contacts pour l'utilisateur courant
echo $skin->open_simplebloc('',"width:100%;float:left;");

if (!isset($_SESSION['dims']['desktop_date_connexion'])) $_SESSION['dims']['desktop_date_connexion']='month';
$desktop_date_connexion=dims_load_securvalue('desktop_date_connexion',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_date_connexion'],1);

$url = '';
if (dims_load_securvalue('desktop_date_connexion2',dims_const::_DIMS_NUM_INPUT,true,true,false) != '')
	$url = "&desktop_date_connexion2=".dims_load_securvalue('desktop_date_connexion2',dims_const::_DIMS_NUM_INPUT,true,true,false);

$arrayChoice = array();
$arrayChoice[0]=$_DIMS['cste']['_DIMS_WEEK'];
$arrayChoice[1]=$_DIMS['cste']['_DIMS_MONTH'];
$arrayChoice[2]=$_DIMS['cste']['_DIMS_LABEL_YEAR'];

echo "<div style='width:100%;text-align:center;'>";

$taille=sizeof($arrayChoice);
foreach($arrayChoice as $i=>$val) {
		if ($i==$desktop_date_connexion) $sel=true;
		else $sel=false;

		if ($sel) echo '<font style="font-weight:bold;">'.$val.'</font>';
		else {
				echo '<a href="/admin.php?desktop_date_connexion='.$i.$url.'">'.$val.'</a>';
		}

		if ($i<$taille-1) {
				echo "&nbsp;-&nbsp;";
		}
}
echo "</div>";

$datemodif=0;
// generation du fichier contenant la langue
$id_lang= $_SESSION['dims']['currentlang'];
$filegraph = DIMS_TMP_PATH . '/graph_'.$desktop_date_connexion.'_'.$id_lang.'.png';
$webfilegraph = DIMS_TMP_PATH . '/graph_'.$desktop_date_connexion.'_'.$id_lang.'.png';

if (file_exists($filegraph)) {
		// date de modif
		$datemodif=filectime($filegraph);

		switch ($desktop_date_connexion) {
				case 0:
						if ($datemodif>0) {
								$now			 = time();
								$nbminutes= ($now-$datemodif)/60;

								if ($nbminutes>5) $datemodif=0;
						}
						break;
				case 1:
						if ($datemodif>0) {
								$now			 = time();
								$nbhours= ($now-$datemodif)/3600;

								if ($nbhours>24) $datemodif=0;
						}
						break;
				case 2:
						if ($datemodif>0) {
								$now			 = time();
								$nbhours= ($now-$datemodif)/3600;

								if ($nbhours>24) $datemodif=0;
						}
						break;
		}
}


		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph.php');
		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_bar.php');
		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_line.php');

		$xtitle='';
		$graph = new Graph(500,200);
		$graph->SetScale('textlin');


		$nameofColum=array();

		$jdeb='';
		switch ($desktop_date_connexion) {
				case 0:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_LABEL_DAYS']);
						foreach ($dims_agenda_days as $i=>$d) {
								$nameofColum[$i]=$d;
						}
						$graph->xaxis->SetTickLabels($nameofColum);

						$datedeb = date('Ymd',mktime(0,0,0,date('n'),date('j')-date('N')+1,date('Y'))).'000000';
						$maxelem = 7;
						$jdeb = date('d',mktime(0,0,0,date('n'),date('j')-date('N')+1,date('Y')));
						$posdeb=6;
						$poslen=2;
						break;
				case 1:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_LABEL_DAYS']);
						$datedeb_timestp = mktime(0,0,0,date('n'),1,date('Y'));
						$datefin_timestp = mktime(0,0,0,date('n')+1,0,date('Y'));

						$jdeb = date('N',$datedeb_timestp);
						$jfin = date('d',$datefin_timestp);

						for ($i=0;$i<$jfin;$i++) {
								$e=($jdeb+$i)%7;
								if ($e==0) $e=7;

								$nameofColum[$i+1]=substr($dims_agenda_days[$e],0,1);
						}
						$graph->xaxis->SetTickLabels($nameofColum);
						$datedeb = date('Ymd',mktime(0,0,0,date('n'),1,date('Y'))).'000000';
						$maxelem = 31;
						$jdeb = 1;
						$posdeb=6;
						$poslen=2;
						break;
				case 2:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_MONTH']);
						foreach ($dims_agenda_months as $i=>$d) {
								$nameofColum[$i]=substr($d,0,3);
						}
						$graph->xaxis->SetTickLabels($nameofColum);
						$datedeb = date('Ymd',mktime(0,0,0,1,1,date('Y'))).'000000';
						$maxelem = 12;
						$jdeb = 1;
						$posdeb=4;
						$poslen=2;
						break;
		}

		$sql = "SELECT	id, date_modify
				FROM	dims_mod_business_activity_contact
				WHERE	date_modify > :datedeb
				AND		id_user = :iduser ";
		$res = $db->query($sql, array(
			':datedeb'	=> $datedeb,
			':iduser'	=> $_SESSION['dims']['userid']
		));

		for ($i=1;$i<=$maxelem;$i++)
			$datay[$i] = 0 ;

		while ($data = $db->fetchrow($res)){
			$val = intval(substr($data['date_modify'],$posdeb,$poslen))-$jdeb+1;
			$datay[$val]++;
		}

		// construction
		$lineplot=new LinePlot($datay);

		// Adjust fill color
		$lineplot->SetFillColor('#a6b8d5');
		$lineplot->value->Show();
		$lineplot->value->SetFormat('%d');

		$graph->Add($lineplot);

		//$graph->SetFrame(false);
		//$graph->SetBackgroundGradient('blue','navy:0.5',GRAD_HOR,BGRAD_PLOT);

		$graph->xgrid->SetColor('gray@0.5');
		// Setup the titles
		$graph->title->Set('Stats');
		$graph->title->SetFont(FF_VERDANA,FS_BOLD,28);
		$graph->xaxis->title->Set($xtitle);
		$graph->xaxis->title->SetFont(FF_VERDANA,FS_BOLD,14);
		$graph->yaxis->title->Set($_DIMS['cste']['_DIMS_LABEL_UPDATE_PROFILE']);

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

		$graph->xaxis->SetTextTickInterval(1);
		// Display the graph
		$graph->Stroke($filegraph);

echo "<p style=\"text-align:center;\"><img src=\"".$webfilegraph."\"></p>";
echo $skin->close_simplebloc();


// nombre de contacts incomplets pour ce workspace uniquement
echo $skin->open_simplebloc($_DIMS['cste']['_INCOMPLETE_RECORDS'],"width:100%;float:left;");

if (!isset($_SESSION['dims']['desktop_date_connexion2'])) $_SESSION['dims']['desktop_date_connexion2']='month';
$desktop_date_connexion=dims_load_securvalue('desktop_date_connexion2',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_date_connexion2'],1);

$url = '';
if (dims_load_securvalue('desktop_date_connexion',dims_const::_DIMS_NUM_INPUT,true,true,false) != '')
	$url = "&desktop_date_connexion=".dims_load_securvalue('desktop_date_connexion',dims_const::_DIMS_NUM_INPUT,true,true,false);

$arrayChoice = array();
$arrayChoice[0]=$_DIMS['cste']['_DIMS_WEEK'];
$arrayChoice[1]=$_DIMS['cste']['_DIMS_MONTH'];
$arrayChoice[2]=$_DIMS['cste']['_DIMS_LABEL_YEAR'];

echo "<div style='width:100%;text-align:center;'>";

$taille=sizeof($arrayChoice);
foreach($arrayChoice as $i=>$val) {
		if ($i==$desktop_date_connexion) $sel=true;
		else $sel=false;

		if ($sel) echo '<font style="font-weight:bold;">'.$val.'</font>';
		else {
				echo '<a href="/admin.php?desktop_date_connexion2='.$i.$url.'">'.$val.'</a>';
		}

		if ($i<$taille-1) {
				echo "&nbsp;-&nbsp;";
		}
}
echo "</div>";

$datemodif=0;
// generation du fichier contenant la langue
$id_lang= $_SESSION['dims']['currentlang'];
$filegraph = DIMS_TMP_PATH . '/graph_'.$desktop_date_connexion.'_'.$id_lang.'.png';
$webfilegraph = DIMS_TMP_PATH . '/graph_'.$desktop_date_connexion.'_'.$id_lang.'.png';

if (file_exists($filegraph)) {
		// date de modif
		$datemodif=filectime($filegraph);

		switch ($desktop_date_connexion) {
				case 0:
						if ($datemodif>0) {
								$now			 = time();
								$nbminutes= ($now-$datemodif)/60;

								if ($nbminutes>5) $datemodif=0;
						}
						break;
				case 1:
						if ($datemodif>0) {
								$now			 = time();
								$nbhours= ($now-$datemodif)/3600;

								if ($nbhours>24) $datemodif=0;
						}
						break;
				case 2:
						if ($datemodif>0) {
								$now			 = time();
								$nbhours= ($now-$datemodif)/3600;

								if ($nbhours>24) $datemodif=0;
						}
						break;
		}
}


		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph.php');
		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_bar.php');
		require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_line.php');

		$xtitle='';
		$graph = new Graph(500,200);
		$graph->SetScale('textlin');


		$nameofColum=array();

		$jdeb='';
		switch ($desktop_date_connexion) {
				case 0:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_LABEL_DAYS']);
						foreach ($dims_agenda_days as $i=>$d) {
								$nameofColum[$i]=$d;
						}
						$graph->xaxis->SetTickLabels($nameofColum);

						$datedeb = date('Ymd',mktime(0,0,0,date('n'),date('j')-date('N')+1,date('Y'))).'000000';
						$maxelem = 7;
						$jdeb = date('d',mktime(0,0,0,date('n'),date('j')-date('N')+1,date('Y')));
						$posdeb=6;
						$poslen=2;
						break;
				case 1:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_LABEL_DAYS']);
						$datedeb_timestp = mktime(0,0,0,date('n'),1,date('Y'));
						$datefin_timestp = mktime(0,0,0,date('n')+1,0,date('Y'));

						$jdeb = date('N',$datedeb_timestp);
						$jfin = date('d',$datefin_timestp);

						for ($i=0;$i<$jfin;$i++) {
								$e=($jdeb+$i)%7;
								if ($e==0) $e=7;

								$nameofColum[$i+1]=substr($dims_agenda_days[$e],0,1);
						}
						$graph->xaxis->SetTickLabels($nameofColum);
						$datedeb = date('Ymd',mktime(0,0,0,date('n'),1,date('Y'))).'000000';
						$maxelem = 31;
						$jdeb = 1;
						$posdeb=6;
						$poslen=2;
						break;
				case 2:
						$xtitle=ucfirst($_DIMS['cste']['_DIMS_MONTH']);
						foreach ($dims_agenda_months as $i=>$d) {
								$nameofColum[$i]=substr($d,0,3);
						}
						$graph->xaxis->SetTickLabels($nameofColum);
						$datedeb = date('Ymd',mktime(0,0,0,1,1,date('Y'))).'000000';
						$maxelem = 12;
						$jdeb = 1;
						$posdeb=4;
						$poslen=2;
						break;
		}

		$sql = "SELECT	id, timestp
				FROM	dims_mod_business_contact_incomplete
				WHERE	timestp > :datedeb
				AND		id_workspace = :idworkspace ";
		$res = $db->query($sql, array(
			':datedeb'		=> $datedeb,
			':idworkspace'	=> $_SESSION['dims']['workspaceid']
		));

		for ($i=1;$i<=$maxelem;$i++)
			$datay[$i] = 0 ;

		while ($data = $db->fetchrow($res)){
			$val = intval(substr($data['timestp'],$posdeb,$poslen))-$jdeb+1;
			$datay[$val]++;
		}

		// construction
		$lineplot=new LinePlot($datay);

		// Adjust fill color
		$lineplot->SetFillColor('#a6b8d5');
		$lineplot->value->Show();
		$lineplot->value->SetFormat('%d');

		$graph->Add($lineplot);

		//$graph->SetFrame(false);
		//$graph->SetBackgroundGradient('blue','navy:0.5',GRAD_HOR,BGRAD_PLOT);

		$graph->xgrid->SetColor('gray@0.5');
		// Setup the titles
		$graph->title->Set('Stats');
		$graph->title->SetFont(FF_VERDANA,FS_BOLD,28);
		$graph->xaxis->title->Set($xtitle);
		$graph->xaxis->title->SetFont(FF_VERDANA,FS_BOLD,14);
		$graph->yaxis->title->Set($_DIMS['cste']['_INCOMPLETE_RECORDS']);

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

		$graph->xaxis->SetTextTickInterval(1);
		// Display the graph
		$graph->Stroke($filegraph);

echo "<p style=\"text-align:center;\"><img src=\"".$webfilegraph."\"></p>";
echo $skin->close_simplebloc();

echo '</div>';
?>
