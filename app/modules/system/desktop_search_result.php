<?php
if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
	if (isset($_GET['campaignid']) && $_GET['campaignid']>0) {
		$_SESSION['dims']['search']['execute']=true;
	}
	else {
		// on initialise
		unset($_SESSION['dims']['search']['listword']);
		unset($_SESSION['dims']['search']['lengthword']);
		unset($_SESSION['dims']['search']['sqlselectedword']);
		unset($_SESSION['dims']['search']['sqlselectedtag']);

		$_SESSION['dims']['search']['listword']="";
		$_SESSION['dims']['search']['lengthword']="";
		$_SESSION['dims']['search']['sqlselectedword']="";
		$_SESSION['dims']['search']['sqlselectedtag']="";

		$refresh=dims_load_securvalue('refresh',_DIMS_NUM_INPUT,true,false,false);
		$tabword=array();
		$tablength=array();
		// construction de la liste des cls de correspondances
		// traitement des recherches
		$position=0;

		if (!empty($_SESSION['dims']['search']['listselectedword'])) {
			foreach($_SESSION['dims']['search']['listselectedword'] as $id=>$elem) {
				$_SESSION['dims']['search']['listselectedword'][$id]['present']=0;
				if ($_SESSION['dims']['search']['sqlselectedword']=="") $_SESSION['dims']['search']['sqlselectedword']="'".$elem['key']."'";
				else $_SESSION['dims']['search']['sqlselectedword'].=",'".$elem['key']."'";

				$wlength=strlen($elem['word']);

				// construction de la restriction sur la longueur
				if (!in_array($wlength,$tablength)) {
					array_push($tablength,$wlength);
					if ($_SESSION['dims']['search']['lengthword']!="") $_SESSION['dims']['search']['lengthword'].=",";
					$_SESSION['dims']['search']['lengthword'].=$wlength;
				}

				// construction de la liste des mots cles
				if (!isset($_SESSION['dims']['search']['listpositionword'][$elem['key']]))
					$_SESSION['dims']['search']['listpositionword'][$elem['key']]=array();

				array_push($_SESSION['dims']['search']['listpositionword'][$elem['key']],$position);
				$position++;
			}

			if ($_SESSION['dims']['search']['sqlselectedword']!="") $_SESSION['dims']['search']['execute']=true;

			// verify access rule
			if ($_SESSION['dims']['search']['execute']) {
				$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
				if (!$refresh) $dims_searchresult =array();

				// on analyse le résultat, regroupe par type pour construire un tableau d'élements finaux
				$mods=$dims->getModules($_SESSION['dims']['workspaceid']);
				foreach($mods as $idmod => $mod) {
					if($mod['active'] && $mod['visible']) {
						$moduleid=$idmod;
						$modtype=$mod['label'];
						$_GET['moduleid']=$moduleid;

						$blockpath = "./common/modules/".$modtype."/block_portal.php";

						if (file_exists($blockpath)) {
							// on initialise la structure pour ce module id
							if (!$refresh) {
								$dims_searchresult[$moduleid]=array();
								//$mod=$dims->getModule($moduleid);
								//$dims_searchresult[$moduleid]['label']=$mod['instancename'];
								require_once($blockpath);
							}
							// on ajout ce type en résultat
							if (!isset($modtype_tab[$mod['contenttype']])) $modtype_tab[$mod['contenttype']]=array();
							if(!isset($modtype_tab[$mod['contenttype']][$mod['id']])) $modtype_tab[$mod['contenttype']][$mod['id']]=$mod;
						}
					}
				}

				//$tpl_tab[]=array('TITLE' => $_DIMS['cste']['_DIMS_LABEL_SYSTEM'],'IMG'=> $_SESSION['dims']['template_path']."/media/home16.png",'URL' => "");

				if ($currentworkspace['project']) $tpl_tab[]=array('TITLE' => $_DIMS['cste']['_DIMS_LABEL_MYPROJECTS'],'IMG'=> $_SESSION['dims']['template_path']."/media/project16.png",'URL' => "");
				if ($currentworkspace['planning']) $tpl_tab[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_PLANNING'],'IMG'=> $_SESSION['dims']['template_path']."/media/planning16.png",'URL' => "");
				if ($currentworkspace['contact']) $tpl_tab[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_CONTACT'],'IMG'=> $_SESSION['dims']['template_path']."/media/contact16.png",'URL' => "");

				$change_mainmenu=false;
				$typkown=array();

				// traitement des module de docs
				if (isset($modtype_tab[_DIMS_MENU_MODULEDOC]) && !empty($modtype_tab[_DIMS_MENU_MODULEDOC])) $tpl_tab[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_DOCS'],'IMG'=>"./common/modules/doc/img/mod16.png",'URL' => "",'MODULES'=>$modtype_tab[_DIMS_MENU_MODULEDOC]);
				$typkown[_DIMS_MENU_MODULEDOC]=_DIMS_MENU_MODULEDOC;

				// traitement des modules de contenus
				if (isset($modtype_tab[_DIMS_MENU_MODULECONTENT]) && !empty($modtype_tab[_DIMS_MENU_MODULECONTENT])) {
					$tabarray=array();
					if (sizeof($modtype_tab[_DIMS_MENU_MODULECONTENT])>=1) $tabarray=$modtype_tab[_DIMS_MENU_MODULECONTENT];
					$tpl_tab[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_CONTENT'],'IMG'=>"./common/modules/wce/img/mod16.png",'URL' => "",'MODULES'=>$tabarray);
					$typkown[_DIMS_MENU_MODULECONTENT]=_DIMS_MENU_MODULECONTENT;
				}

				// traitement des modules de surveillance de contenus
				if (isset($modtype_tab[_DIMS_MENU_MODULEWATCH]) && !empty($modtype_tab[_DIMS_MENU_MODULEWATCH])) $tpl_tab[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_WATCH'],'IMG'=>$_SESSION['dims']['template_path']."/media/watch16.png",'URL' => "");
				$typkown[_DIMS_MENU_MODULEWATCH]=_DIMS_MENU_MODULEWATCH;
				// traitement des autres modules différents

				// boucle sur les types de contenus
				foreach ($modtype_tab as $mtype => $ctype) {
					if (!in_array($mtype,$typkown)) {
						// on ajoute le nouvel onglet
						if (!empty($ctype)) {
							if (sizeof($ctype)>1) $tabarray=$ctype;
							else $tabarray=array();

							$tpl_tab[]=array('TITLE' =>$mtype, 'URL' => "",'SELECTED' => ($_SESSION['dims']['mainmenu'] == $mtype) ? 'selected' : '','MODULES'=>$tabarray,'IMG'=>"./common/img/".$mtype."/mod16.png");
						}
					}
				}

				if ($refresh && isset($_SESSION['search']['searchresult'])) $dims_searchresult=$_SESSION['search']['searchresult'];
				// on traite maintenant le resultat en affichage
				if (!empty($dims_searchresult)) {
					echo "<div style=\"width:100%;margin:0 auto;\"><table width=\"100%\"><tr>";
					// construction du bloc par type de module avec incone
					//$_DIMS['cste']['_DIMS_LABEL_DOCS']
					$nbtab=sizeof($tpl_tab);
					foreach ($tpl_tab as $tab) {
						echo "<td style=\"width:".(100/$nbtab)."%;text-align:center\" ><img src=\"".$tab['IMG']."\" alt=\"\">&nbsp;".$tab['TITLE']."</td>";
					}
					echo "</tr><tr>";

					// on affiche le resultat
					foreach ($tpl_tab as $tab) {
						echo "<td>";
						// test si module dispo
						if (isset($tab['MODULES']) && !empty($tab['MODULES'])) {
							echo "<table style=\"width:100%;\">";
							foreach ($tab['MODULES'] as $idmoduletype =>$modu) {
								//$mod=$dims->getModule($idmodule);
								$idmodule=$modu['instanceid'];
								echo "<tr><td>";
								// test si resultat ou non
								if (isset($dims_searchresult[$idmodule]) && !empty($dims_searchresult[$idmodule])) {
									// on boucle sur les objets
									foreach ($dims_searchresult[$idmodule] as $idobject=>$object) {
										$mod=$dims->getModule($idmodule);
										echo $mod['instancename']. "/".$object['label']. " ".$object['title'];
									}
								}

								echo "</td></tr>";
							}
							echo "</table>";
						}
						echo "</td>";
					}
					echo "</tr></table></div>";

				}

				// on appelle la fonction d'affichage
				foreach ($tpl_tab as $tab) {
					if (isset($tab['MODULES']) && !empty($tab['MODULES'])) {
						foreach ($tab['MODULES'] as $idmoduletype =>$modu) {
							$idmodule=$modu['instanceid'];
							if (isset($dims_searchresult[$idmodule]) && !empty($dims_searchresult[$idmodule])) {
								// on boucle sur les objets
								foreach ($dims_searchresult[$idmodule] as $idobject=>$object) {
									echo getSearchcontent($object['id_object'],$idmodule,$object['tabid'],$object['tabresult'],"search")."<br>";
								}
							}

						}
					}
				}
				if (!$refresh) $_SESSION['search']['searchresult']=$dims_searchresult;
			}
			else {
				echo "Non trouve";
			}

		}
	}
}
?>
