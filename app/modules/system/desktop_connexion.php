<div style="position:relative;display:block;min-height:100px;">
	<div class="ui-widget-header ui-helper-clearfix">
		<span class="title action" style="float:right;">
			<?
			$arrayChoice = array();
			$arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_LASTCONNECTION'];
			$arrayChoice[1]=$_DIMS['cste']['_DIMS_LABEL_RECENTLY_VIEWED'];
			$arrayChoice[2]=$_DIMS['cste']['_DIMS_LABEL_GRAPHS'];

			$taille=sizeof($arrayChoice);
			foreach($arrayChoice as $i=>$val) {
			if ($i==$desktop_view_connexion) $sel="ui-state-active ui-state-hover";
			else $sel="";
			if($i==0) $corner="ui-corner-left";
			else if($i==($taille-1)) $corner="ui-corner-right";
			else $corner = "";

			echo '<a class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$sel.'" href="/admin.php?desktop_view_connexion='.$i.'">
					<span class="ui-button-text">'.$val.'</span>
				</a>';
			}
			?>
		</span>
		<span class="ui-icon ui-icon-person" style="float:left;"></span>
		<div>
			<?
			switch ($desktop_view_connexion) {
				case 0:
					echo $_DIMS['cste']['_DIMS_LABEL_LASTCONNECTION'];
					break;
				case 1:
					echo $_DIMS['cste']['_DIMS_LABEL_RECENTLY_SEARCH'];
					break;
				case 2:
					echo $_DIMS['cste']['_DIMS_LABEL_GRAPHS'];
					break;
			}
			?>
		</div>
	</div>
	<h2 style="padding-left:22px">
		<img class="icons fil_ct"></img>
		<?
		switch ($desktop_view_connexion) {
			case 0:
					echo $_DIMS['cste']['_DIMS_LABEL_LASTCONNECTION'];
					break;
			case 1:
					echo $_DIMS['cste']['_DIMS_LABEL_RECENTLY_SEARCH'];
					break;
			case 2:
					echo $_DIMS['cste']['_DIMS_LABEL_GRAPHS'];
					break;
		}
		?>
	</h2>
<div>
	<?
	switch ($desktop_view_connexion) {
			case 1:
					echo $dims->getHistoryObject();
					break;
			case 2:
					if (!isset($_SESSION['dims']['desktop_date_connexion'])) $_SESSION['dims']['desktop_date_connexion']='month';
					$desktop_date_connexion=dims_load_securvalue('desktop_date_connexion',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_date_connexion'],1);

					$arrayChoice = array();
					$arrayChoice[0]=$_DIMS['cste']['_DIMS_WEEK'];
					$arrayChoice[1]=$_DIMS['cste']['_DIMS_MONTH'];
					$arrayChoice[2]=$_DIMS['cste']['_DIMS_LABEL_YEAR'];

					echo "<div style='width:100%;text-align:center;'>";

					$taille=sizeof($arrayChoice);
					foreach($arrayChoice as $i=>$val) {
			if ($i==$desktop_date_connexion) $sel="ui-state-active ui-state-hover";
			else $sel="";
			if($i==0) $corner="ui-corner-left";
			else if($i==($taille-1)) $corner="ui-corner-right";
			else $corner = "";

			echo '<a class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$sel.'" href="/admin.php?desktop_date_connexion='.$i.'">
					<span class="ui-button-text">'.$val.'</span>
				</a>';
					}
					echo "</div>";

					$datemodif=0;
					// generation du fichier contenant la langue
					$id_lang= $_SESSION['dims']['currentlang'];
					$filegraph = DIMS_TMP_PATH . '/graph_connexion_'.$desktop_date_connexion.'_'.$id_lang.'.png';
					$webfilegraph = DIMS_TMP_PATH . '/graph_connexion_'.$desktop_date_connexion.'_'.$id_lang.'.png';

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
					// on a voir modifier si besoin
					//$datemodif=0;

					if ($datemodif==0){
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
											break;
									case 2:
											$xtitle=ucfirst($_DIMS['cste']['_DIMS_MONTH']);
											foreach ($dims_agenda_months as $i=>$d) {
													$nameofColum[$i]=substr($d,0,3);
											}
											$graph->xaxis->SetTickLabels($nameofColum);
											break;
							}
							$datay=$dims->getConnexionStats($desktop_date_connexion);

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
							$graph->yaxis->title->Set($_DIMS['cste']['_DIMS_LABEL_NUMBER_CONNECTION']);

							$graph->title->SetFont(FF_FONT1,FS_BOLD);
							$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
							$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

							$graph->xaxis->SetTextTickInterval(1);
							// Display the graph
							$graph->Stroke($filegraph);
					}

					echo "<p style=\"text-align:center;\"><img src=\"".$webfilegraph."\"></p>";
					break;
			default:
					$datedeb_timestp = mktime(date('H'),date('i'),date('s'),date('n'),date('j')-21,date('Y'));
					$datedeb_timestp= date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$datedeb_timestp);
					$params = array();
					$sql="	SELECT		distinct u.firstname,u.lastname,u.id,max(cu.timestp) as timestp
							FROM		dims_user as u
							INNER JOIN	dims_connecteduser as cu
							ON			cu.user_id=u.id
							AND			cu.timestp> :datedebtimestp ";
					$params[':datedeb_timestp'] = $datedeb_timestp;

					if ($desktop_view_type==0) {
							$sql.= " AND	workspace_id= :idworkspace ";
							$params[':idworkspace'] = $_SESSION['dims']['workspaceid'];
					}
					elseif ($desktop_view_connexion==1) {
							$sql.= " AND	workspace_id in (".$db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")";
					}
					else {
							$sql.= " AND	workspace_id in (".$db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")";
					}
					$sql.= " GROUP BY	u.id order by timestp desc";

					$res=$db->query($sql, $params);
					$tot=$db->numrows($res);
					if ($tot>0) {
							while ($f=$db->fetchrow($res)) {
									echo '<div style="padding:6px;float:left;">
						<a href="/admin.php?view_user_id='.$f['id'].'">'.$f['firstname']." ".$f['lastname'].'</a>';
									$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
									if ($diff>300) {
											echo dims_nicetime($f['timestp']);
									}
									else {
											echo '<span style="color:#8dd286;"> ('.$_DIMS['cste']['_DIMS_LABEL_IS_CONNECTED'].')</span>';
									}
									echo '</div>';

							}
					}
			break;
	}
	?>
<?
//<div style="position:relative;height:10px;display:block;width:150px;border-bottom: 1px solid #DEDEDE;margin:0 auto;">
?>
</div>
