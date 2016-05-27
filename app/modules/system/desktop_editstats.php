<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// check admin view
if (dims_isadmin ()) {
	// inclusion des feuilles de styles

	echo '<div class="dims_form_title">';
	echo '<font style=\"font-weight:bold\">'.$_DIMS['cste']['_DIMS_GRAPHIC_WORKSPACE_VIEW'].'</font>';
	echo '</div>';

	// on genere l'image des workspaces
	echo "<script type=\"text/javascript\" src=\"/js/functions.js\"></script>";
	echo " <!--[if IE]><script type=\"text/javascript\" src=\"/js/excanvas.js\"></script><![endif]-->";
	echo "<div id=\"containerworkspace\" style=\"position:relative;background:#FFFFFF;\">";
	get_mapview(true,80);
	echo "</div>";

	echo '<div class="dims_form_title">';
	echo '<font style=\"font-weight:bold\">'.$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE'].'</font>';
	echo '</div>';

	$datedeb_timestp = mktime(date('H'),date('i'),date('s'),date('n'),date('j')-500,date('Y'));
	$datedeb_timestp= date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$datedeb_timestp);
	$params = array();
	$sql="	SELECT		distinct u.firstname,u.lastname,u.id,max(cu.timestp) as timestp
			FROM		dims_user as u
			INNER JOIN	dims_connecteduser as cu
			ON			cu.user_id=u.id
			AND			cu.timestp> :timestp ";
	$params[':timestp'] = $datedeb_timestp;


	$sql.= " AND	workspace_id in (".$db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")";
	$sql.= " GROUP BY	u.id order by timestp desc";

	$res=$db->query($sql, $params);
	$tot=$db->numrows($res);
	$activities=array();

	while ($f=$db->fetchrow($res)) {
		$activities[$f['id']]=$f;
	}

	// on contruit le tableau des groupes avec les utilisateurs et les workspaces correspondants
	// calcul

	$ws = system_getavailabledworkspaces();

	$allgroups=array();
	echo system_build_tree_stats($ws);
	echo "<br>";

	if (sizeof($ws['list'])>0) {
		echo "<table style=\"width:100%\" cellspacing=\"2\" cellpadding=\"2\">";
		echo "<tr style=\"font-weight:bold;text-align:center;\"><td style=\"width:15%;\">".$_DIMS['cste']['_WORKSPACE']."</td>";
		echo "<td style=\"width:15%;\">".$_DIMS['cste']['_DIMS_LINKED_GROUP']."</td>";
		echo "<td style=\"width:22%;\">".$_DIMS['cste']['_DIMS_LINKED_USER']."</td>";
		echo "<td style=\"width:18%;\">".$_DIMS['cste']['_DIMS_LABEL_LASTCONNECTION']."</td>";
		echo "<td style=\"width:15%;\">".$_DIMS['cste']['_DIMS_LABEL_GROUP_WEBDOMAINLIST']."</td>";
		echo "<td style=\"width:5%;\">".$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']."</td>";
		echo "<td style=\"width:5%;\">".$_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF']."</td>";
		echo "<td style=\"width:5%;\">".$_DIMS['cste']['_DIMS_LABEL_CONTACT']."</td>";
		$i=0;

		foreach ($ws['list'] as $k => $w) {
			$currentwork = new workspace();
			$currentwork->open($w['id']);
			$groups=$currentwork->getgroups();
			$users=$currentwork->getusers();
			$style=($i%2) ? '#FFFFFF' : '#EEEEDD';
			echo "<tr style=\"background-color:".$style."\"><td>".$w['label']."</td>";
			echo "<td>";
			if (!empty($groups)) {
				$k=0;
			 foreach ($groups as $ii => $g) {
				 if ($k>0) echo ", ";
				echo "<span>'".$g['label']."'</span>";
				$k++;

				if (!isset($allgroups[$g['id']])) {
					$allgroups[$g['id']]=$g['label'];
				}
			 }
			}
			echo "</td><td>";
			$acti=array();
			$usr='';
			$admin='';
			if (!empty($users)) {
				$k=0;
				foreach ($users as $ii => $u) {
					$textuser= "<span>".ucfirst($u['firstname'])." ".$u['lastname']."</span>";
					if ($u['adminlevel']<99) {
						if ($k>0) $usr.= ", ";

						$usr.= $textuser;
						$k++;

						if (isset($activities[$u['id']]) && $u['adminlevel']<99) {
							$acti[$activities[$u['id']]['timestp']]=$textuser;
						}
					}
					else {
						if ($admin=='') {
							$admin=$textuser;
						}
						else {
							$admin.=", ".$textuser;
						}
					}
				}
				echo $usr;
				if ($admin!='') {
					if ($usr!='') echo "<br><br>";
					echo "<font style=\"font-weight:bold;\">Admin. : </font>";
					echo $admin;

				}

			}
			echo "</td><td>";

			// on affiche les dernières activités
			if (!empty($acti)) {
				krsort($acti);
				$k=1;

				foreach ($acti as $idate=>$el) {
					if ($k<6) {
						echo $el." : ";
						$diff=dims_diffdate(date("YmdHis"),$idate);
						if ($diff>300) {
							echo dims_nicetime($idate);
						}
						else {
							echo "<font style=\"color:#8dd286;\"> (".$_DIMS['cste']['_DIMS_LABEL_IS_CONNECTED'].")</font>";
						}
						echo "<br>";
						$k++;
					}// on limite l'affichage
				}
			}
			echo "</td><td>";
			// domains
			$isdomain=false;
			$domains=$currentwork->getFrontDomains();
			foreach ($domains as $k=>$dom) {
				echo $dom['domain']."<br>";
				$isdomain=true;
			}
			echo "</td><td style=\"text-align:center;\">";

			if ($currentwork->fields['newsletter'] && $isdomain) {
				echo "<img src=\"./common/img/checkdo.png\">";
			}
			else {
				echo "<img src=\"./common/img/bullet.png\">";
			}

			echo "</td><td style=\"text-align:center;\">";

			if ($currentwork->isEnabledModuleByType('events') && $isdomain) {
				echo "<img src=\"./common/img/checkdo.png\">";
			}
			else {
				echo "<img src=\"./common/img/bullet.png\">";
			}

			echo "</td><td style=\"text-align:center;\">";

			if ($currentwork->fields['contact'] && $isdomain) {
				echo "<img src=\"./common/img/checkdo.png\">";
			}
			else {
				echo "<img src=\"./common/img/bullet.png\">";
			}

			echo "</td></tr>";
			$i++;
		}

		echo "</table>";
	}

	// chargement des pays
	$countries=array();
	$sql="	SELECT		distinct iso, printable_name
			FROM		dims_country";

	$res=$db->query($sql);

	while ($f=$db->fetchrow($res)) {
		$countries[$f['iso']]=$f['printable_name'];
	}

	// newsletters
	echo '<br><br><div class="dims_form_title">';
	echo '<font style=\"font-weight:bold\">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'].'</font>';
	echo '</div>';
	if (sizeof($ws['list'])>0) {
		echo "<table style=\"width:100%\" cellspacing=\"2\" cellpadding=\"2\">";
		echo "<tr style=\"font-weight:bold;text-align:center;\"><td style=\"width:15%;\">".$_DIMS['cste']['_WORKSPACE']."</td>";
		echo "<td style=\"width:85%;\">".$_DIMS['cste']['_EVENT_DETAILS']."</td></tr>";

		$i=0;
		$origin=array();
		foreach ($ws['list'] as $k => $w) {
			$currentwork = new workspace();
			$currentwork->open($w['id']);
			$isdomain=false;
			$domains=$currentwork->getFrontDomains();
			foreach ($domains as $k=>$dom) {
				$isdomain=true;
			}

			if ($currentwork->fields['newsletter'] && $isdomain) {
				$style=($i%2) ? '#FFFFFF' : '#EEEEDD';
				echo "<tr style=\"background-color:".$style."\"><td>".$w['label']."</td>";

				// on compte le nombre de newsletter
				$listworkspace_nl = '0';

				$sql_in = "	SELECT	id_to
										FROM	dims_workspace_share
										WHERE	id_from = :idfrom
										AND		active = 1
										AND		id_object = :idobject ";

				$res_in = $db->query($sql_in, array(
					':idfrom'	=> $w['id'],
					':idobject'	=> dims_const::_SYSTEM_OBJECT_NEWSLETTER
				));

				if($db->numrows($res_in) >= 1) {
						while($tabw = $db->fetchrow($res_in)) {
								$listworkspace_nl .= ", ".$tabw['id_to'];
						}
						$listworkspace_nl .= ", ".$w['id']; // on ajoute le workspace courant sinon il sera exclu des recherches
				}
				else {
						$listworkspace_nl = $w['id'];
				}

				$params = array();
				$sql = 'SELECT		ml.*,mn.id_newsletter,n.label as labelnewsletter
						FROM		dims_mod_newsletter_mailing_list as ml
						INNER JOIN	dims_mod_newsletter_mailing_news as mn
						ON			ml.id_workspace in ('.$db->getParamsFromArray(explode(',', $listworkspace_nl ), 'listworkspace', $params).")
						AND			ml.id=mn.id_mailing
						INNER JOIN	dims_mod_newsletter as n
						ON			n.id=mn.id_newsletter
						";

				$res = $db->query($sql, $params);
				echo "<td>";

				$tab_mailing=array();
				while ($tab_mail = $db->fetchrow($res)) {

					$tab_mailing[$tab_mail['id_newsletter']]['label']=$tab_mail['labelnewsletter'];
					$tab_mailing[$tab_mail['id_newsletter']]['lists'][$tab_mail['id']] = $tab_mail;
					//on compte le nbr de mails dans la liste
					if(!isset($tab_mailing[$tab_mail['id_newsletter']][$tab_mail['id']]['nb_mail'])) {
						$sqlct = 'SELECT	id,email
								  FROM		dims_mod_newsletter_mailing_ct
								  WHERE		actif =1
								  AND		id_mailing = :idmailing ';
						$resct = $db->query($sqlct, array(
							':idmailing' => $tab_mail['id']
						));
						$tab_mailing[$tab_mail['id_newsletter']]['lists'][$tab_mail['id']]['nb_mail'] = $db->numrows($resct);

						$origin[$tab_mail['id_newsletter']]=array();
						// on boucle sur les racines
						while($tabins = $db->fetchrow($resct)) {
							$sExtension = substr( $tabins['email'], ( strrpos($tabins['email'], '.') + 1 ) ) ;
							if (!isset($origin[$tab_mail['id_newsletter']][$sExtension])) {
								$origin[$tab_mail['id_newsletter']][$sExtension]=1;
							}
							else {
								$origin[$tab_mail['id_newsletter']][$sExtension]++;
							}
						}

						// nettoyage
						foreach($origin[$tab_mail['id_newsletter']] as $ko=>$elem) {
							if (strlen($ko)>2) {
								unset($origin[$tab_mail['id_newsletter']][$ko]);
							}
						}
						arsort($origin[$tab_mail['id_newsletter']],true);
					}


					//on compte le nbr de news rattachees a la liste
					if(!isset($tab_mailing[$tab_mail['id']]['nb_news'])) {
						$sqlnews = 'SELECT	id
									FROM	dims_mod_newsletter_mailing_news
									WHERE	id_mailing = :idmailing ';
						$resnews = $db->query($sqlnews, array(
							':idmailing' => $tab_mail['id']
						));
						$tab_mailing[$tab_mail['id_newsletter']]['lists'][$tab_mail['id']]['nb_news'] = $db->numrows($resnews);
					}

					// on compte les enregistrements directs
					$sqlsub = '    SELECT		ns.*,
												c.*
								FROM			dims_mod_newsletter_subscribed ns
								INNER JOIN		dims_mod_business_contact c
								ON				c.id = ns.id_contact
								WHERE			id_newsletter = :idnewsletter ';

					$ressub = $db->query($sqlsub, array(
							':idnewsletter' => $tab_mail['id_newsletter']
						));
					$cptedirect=0;
					while($tab_insc = $db->fetchrow($ressub)) {
						$cptedirect++;
					}
					$tab_mailing[$tab_mail['id_newsletter']]['direct_insc']=$cptedirect;
				}

				// construction de la liste des origines
				//$sql=" select count(c.id) as cpte from dims_mod_business_contact as c inner join dims_mod_newsletter_subscribed as s on s.id_contact=c.id and s.id_newsletter=".
				//dims_mod_newsletter_subscribed

				if (!empty($tab_mailing)) {
					echo "<table style=\"width:100%\"><tr style=\"font-weight:bold;text-align:center;\"><td style=\"width:20%;\">".$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']."</td>";
					echo "<td style=\"width:15%;\">".$_DIMS['cste']['_DIMS_LABEL_MAILINGLIST']."</td>";
					echo "<td style=\"width:24%;\">".$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBDMDINSC']." (".$_DIMS['cste']['_DIMS_LABEL_ENT_WSITE'].")</td>";
					echo "<td style=\"width:46%;\">".$_DIMS['cste']['_DIMS_LABEL_COUNTRY']."</td>";
					$ii=0;
					foreach ($tab_mailing as $k => $newsletter) {
						$style2=($ii%2) ? '#e4edff' : '#c3d7ff';
						echo "<tr style=\"background-color:".$style2."\"><td>".$newsletter['label']."</td>";
						echo "<td >";

						if (isset($newsletter['lists'])) {
							foreach ($newsletter['lists'] as $o => $mailing) {
								echo $mailing['label']." : ".$mailing['nb_mail'];
							}
						}
						echo "</td>";
						echo "<td style=\"text-align:center;\">".$newsletter['direct_insc']."</td><td style=\"font-size:9px;\">";

						// origin des inscriptions
						if (isset($origin[$k])) {
							$jj=0;
							foreach ($origin[$k] as $ind =>$nb) {

								if (isset($countries[strtoupper($ind)]) && $jj<15) {
									echo $countries[strtoupper($ind)]." : ".$nb."<br>";
									$jj++;
								}
							}
						}
						echo "</td></tr>";
						$ii++;
					}
					echo "</table>";
				}
				echo "</td></tr>";
				$i++;
			}
		}

		echo "</table>";
	}

	echo '<br><br><div class="dims_form_title">';
	echo '<font style=\"font-weight:bold\">'.$_DIMS['cste']['_GROUP_SETTING'].'</font>';
	echo '</div>';

	if (sizeof($allgroups)>0) {
		echo "<table style=\"width:100%\" cellspacing=\"2\" cellpadding=\"2\">";
		echo "<tr style=\"font-weight:bold;text-align:center;\"><td style=\"width:20%;\">".$_DIMS['cste']['_DIMS_LABEL_GROUP']."</td>";
		echo "<td style=\"width:80%;\">".$_DIMS['cste']['_DIMS_LINKED_USER']."</td></tr>";
		$i=0;

		foreach ($allgroups as $idg => $g) {
			$currentg = new group();
			$currentg->open($idg);

			$users=$currentg->getusers();
			$style=($i%2) ? '#FFFFFF' : '#EEEEDD';
			echo "<tr style=\"background-color:".$style."\"><td >".$g."</td>";
			echo "<td>";


			if (!empty($users)) {
				$k=0;
				foreach ($users as $ii => $u) {
					 if ($k>0) echo ", ";
					$textuser= "<span>".ucfirst($u['firstname'])." ".$u['lastname']."</span>";
					echo $textuser;
					$k++;

					if (isset($activities[$u['id']])) {
						$acti[$activities[$u['id']]['timestp']]=$textuser;
					}
				}
			}


			echo "</td>";
			echo "</tr>";
			$i++;
		}

		echo "</table>";
	}

	echo '<br><br><div class="dims_form_title">';
	echo '<font style=\"font-weight:bold\">'.$_DIMS['cste']['_DIMS_LABEL_NUMBER_CONNECTION'].'</font>';
	echo '</div>';

	require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph.php');
	require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_bar.php');
	require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_line.php');

	$datemodif=0;
		// generation du fichier contenant la langue
	for ($j=1;$j<=2;$j++) {
		$id_lang= $_SESSION['dims']['currentlang'];
		$filegraph = DIMS_TMP_PATH . '/graph_connexion_'.$j.'_'.$id_lang.'.png';
		$webfilegraph = DIMS_TMP_PATH . '/graph_connexion_'.$j.'_'.$id_lang.'.png';


		// on a voir modifier si besoin
		$datemodif=0;

		if ($datemodif==0){
				require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph.php');
				require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_bar.php');
				require_once (DIMS_APP_PATH . '/scripts/jpgraph/jpgraph_line.php');

				$xtitle='';
				$graph = new Graph(500,200);
				$graph->SetScale('textlin');


				$nameofColum=array();

				$jdeb='';
				$datedisplay='';
				switch ($j) {
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

								$datedisplay=$dims_agenda_months[date("n")]." ".date("Y");
								break;
						case 2:
								$xtitle=ucfirst($_DIMS['cste']['_DIMS_MONTH']);
								$nameofColum[0]='';
								foreach ($dims_agenda_months as $i=>$d) {
										$nameofColum[$i]=substr($d,0,3);
								}

								$graph->xaxis->SetTickLabels($nameofColum);
								$datedisplay=date("Y");
								break;
				}
				$datay=$dims->getConnexionStats($j);

				// construction
				$lineplot=new LinePlot($datay);

				// Adjust fill color
				$lineplot->SetFillColor('#a6b8d5');
				$lineplot->value->Show();
				$lineplot->value->SetFormat('%d');

				$graph->Add($lineplot);

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

		echo "<div style=\"float:left;width:49%\"><p style=\"text-align:center;\"><img src=\"".$webfilegraph."\"><br>".$datedisplay."</p></div>";
	}

	echo '<br><br><div class="dims_form_title">';
	echo '<b>'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS']." - ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</b>';
	echo '</div>';

	// on construit le tableau des liens entreprises, contacts avec les personnes
	$linkcontacts=array();
	$linktiers=array();
	$linkevents=array();

	$sql="	SELECT u.id,count(id_contact2) as cpte FROM dims_mod_business_ct_link as ctl inner join dims_user as u on u.id_contact=ctl.id_contact1 group by ctl.id_contact1;";
	$res=$db->query($sql);
	$tot=$db->numrows($res);
	if ($tot>0) {
		while ($f=$db->fetchrow($res)) {
			$linkcontacts[$f['id']]=$f['cpte'];
		}
	}

	$sql="	SELECT u.id,count(id_contact1) as cpte FROM dims_mod_business_ct_link as ctl inner join dims_user as u on u.id_contact=ctl.id_contact2 group by ctl.id_contact2;";
	$res=$db->query($sql);
	$tot=$db->numrows($res);
	if ($tot>0) {
		while ($f=$db->fetchrow($res)) {
			if (!isset($linkcontacts[$f['id']])) {
				$linkcontacts[$f['id']]=$f['cpte'];
			}
			else {
				$linkcontacts[$f['id']]+=$f['cpte'];
			}
		}
	}

	// tableau de link avec les entreprises
	$sql="SELECT u.id,count(id_tiers) as cpte FROM dims_mod_business_tiers_contact as tc inner join dims_user as u on u.id_contact=tc.id_contact group by tc.id_contact;";
	$res=$db->query($sql);
	$tot=$db->numrows($res);
	if ($tot>0) {
		while ($f=$db->fetchrow($res)) {
			if (!isset($linktiers[$f['id']])) {
				$linktiers[$f['id']]=$f['cpte'];
			}
			else {
				$linktiers[$f['id']]+=$f['cpte'];
			}
		}
	}

	// tableau de link avec les events
	$sql="SELECT u.id,count(id_action) as cpte FROM dims_mod_business_event_inscription as tc inner join dims_user as u on u.id_contact=tc.id_contact group by tc.id_contact;";
	$res=$db->query($sql);
	$tot=$db->numrows($res);
	if ($tot>0) {
		while ($f=$db->fetchrow($res)) {
			$linkevents[$f['id']]=$f['cpte'];
		}
	}


	if ($tot>0) {
		echo "<table style=\"width:100%\" cellspacing=\"2\" cellpadding=\"2\">";
		echo "<tr style=\"font-weight:bold;text-align:center;\"><td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_USER']."</td>";
		echo "<td style=\"width:20%;\">".$_DIMS['cste']['_DIMS_LABEL_LINK_CONT']."</td>";
		echo "<td style=\"width:20%;\">".$_DIMS['cste']['_DIMS_LABEL_LINK_ENT']."</td>";
		echo "<td style=\"width:20%;\">".$_DIMS['cste']['_DIMS_LABEL_LINK_EVT']."</td>";
		echo "<td style=\"width:15%;\">".$_DIMS['cste']['_DIMS_LABEL_LASTCONNECTION']."</td>";
		$i=0;
		//while ($f=$db->fetchrow($res)) {
		foreach ($activities as $f) {
			$style=($i%2) ? '#FFFFFF' : '#EEEEDD';
			echo "<tr style=\"background-color:".$style.";text-align:center;\">";
			echo '<td>'.$f['firstname']." ".$f['lastname'].'</td><td>';
			if (isset($linkcontacts	[$f['id']])) {
				echo $linkcontacts[$f['id']];
			}
			else {
				echo "0";
			}
			echo "</td><td>";

			if (isset($linktiers[$f['id']])) {
				echo $linktiers[$f['id']];
			}
			else {
				echo "0";
			}
			echo "</td><td>";

			if (isset($linkevents[$f['id']])) {
				echo $linkevents[$f['id']];
			}
			else {
				echo "0";
			}
			echo "</td><td>";
			$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
			if ($diff>300) {
					echo dims_nicetime($f['timestp']);
			}
			else {
					echo "<font style=\"color:#8dd286;\"> (".$_DIMS['cste']['_DIMS_LABEL_IS_CONNECTED'].")</font>";
			}
			echo "</tr>";
			$i++;
		}
	}

}
?>
