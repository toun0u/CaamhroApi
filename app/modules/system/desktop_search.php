<?
require_once(DIMS_APP_PATH . "/modules/system/class_search.php");

if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
	$dims_op="searchnews";
	$_SESSION['dims']['nbselectedsearch']=0;

	$reset_filter=dims_load_securvalue('reset_filter',dims_const::_DIMS_NUM_INPUT,true,true,false);

	if (!isset($_SESSION['dims']['selectedfilternews']) || $reset_filter) {
		$_SESSION['dims']['selectedfilternews']=array();
	}

	$id_module_filter=dims_load_securvalue('id_module',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$id_object_filter=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$active_filter=dims_load_securvalue('active_filter',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$nbresult=0;

	if ($active_filter==2) {
		// on supprime
		if (isset($_SESSION['dims']['selectedfilternews'][$id_module_filter][$id_object_filter])) {
			unset($_SESSION['dims']['selectedfilternews'][$id_module_filter][$id_object_filter]);
			if (empty($_SESSION['dims']['selectedfilternews'][$id_module_filter])) {
				unset($_SESSION['dims']['selectedfilternews'][$id_module_filter]);
			}
		}
	}
	else {
		if ($id_module_filter>0 && $id_object_filter>0) {
			unset($_SESSION['dims']['selectedfilternews'][$id_module_filter]);
			$_SESSION['dims']['selectedfilternews'][$id_module_filter][$id_object_filter]=1;
		}
	}

	$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
	$modtype_tabsearch=array();
	$tpl_tabsearch=array();
	if (isset($refresh) && !$refresh || !isset($refresh)) $dims_searchresult =array();

	// on analyse le resultat, regroupe par type pour construire un tableau d'elements finaux
	$modssearch=$dims->getModules($_SESSION['dims']['workspaceid']);
	if (isset($_SESSION['dims']['search']['result']) && !empty($_SESSION['dims']['search']['result'])) {
		$_GET['dims_op']="search";
		$dims_op="search";
		if (!isset($dimsearch)) {
			$dimsearch					= new search($dims);

			$dimsearch->updateSearchObject();
			$_SESSION['dims']['search']['result']=$dimsearch->tabresultat;
		}
		//dims_print_r($_SESSION['dims']['search']['result']);
		//$dims_searchresult=$dimsearch->tabresultat;
	}

	foreach($modssearch as $idmod => $mod) {

		if(($mod['active'] && $mod['visible']) || $mod['id_module_type']==1) {
			$moduleid=$idmod;
			$modtype=$mod['label'];
			$_GET['moduleid']=$moduleid;
			$blockpath = DIMS_APP_PATH . "/modules/".$modtype."/block_portal.php";
			if (file_exists($blockpath)) {
				// on initialise la structure pour ce module id
				if (isset($refresh) && !$refresh || !isset($refresh)) {

					$dims_searchresult[$moduleid]=array();
					//$mod=$dims->getModule($moduleid);
					//$dims_searchresult[$moduleid]['label']=$mod['instancename'];
					//echo $blockpath."<br>";
					include($blockpath);

					foreach ($dims_searchresult[$moduleid] as $obj=>$res) {
						$nbresult+=$res['nb'];
					}
				}

				// on ajout ce type en resultat
				if (!isset($modtype_tabsearch[$mod['contenttype']])) $modtype_tabsearch[$mod['contenttype']]=array();
				if(!isset($modtype_tabsearch[$mod['contenttype']][$idmod])) $modtype_tabsearch[$mod['contenttype']][$idmod]=$mod;
			}
		}

	}

	//if ($currentworkspace['project']) $tpl_tabsearch[]=array('TITLE' => $_DIMS['cste']['_LABEL_PROJECTS'],'IMG'=> $_SESSION['dims']['template_path']."/media/project16.png",'URL' => "",'MODULES'=>$modtype_tabsearch['system']);
	//fusion
	$title="";
	$img="";
	$img2="";
	$title2='';

	if ($currentworkspace['planning']) {
		//$title=$_DIMS['cste']['_PLANNING'];
		$img=$_SESSION['dims']['template_path']."/media/planning16.png";
		//$tpl_tabsearch[]=array('TITLE' =>$_DIMS['cste']['_PLANNING'],'IMG'=> $_SESSION['dims']['template_path']."/media/planning16.png",'URL' => "",'MODULES'=>$modtype_tabsearch['system']);
	}
	if ($currentworkspace['contact']) {
		if ($title!="") {
			$title2=$_DIMS['cste']['_DIMS_LABEL_CONTACT'];
			$img2=$_SESSION['dims']['template_path']."/media/contact16.png";
		}
		else {
			$title=$_DIMS['cste']['_DIMS_LABEL_CONTACT'];
			$img=$_SESSION['dims']['template_path']."/media/contact16.png";
		}
		//$tpl_tabsearch[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_CONTACT'],'IMG'=> $_SESSION['dims']['template_path']."/media/contact16.png",'URL' => "",'MODULES'=>$modtype_tabsearch['system']);
	}

	// system part
	$tpl_tabsearch[]=array('TITLE' =>$title,'IMG'=> $img,'TITLE2' =>$title2,'IMG2'=> $img2,'URL' => "",'MODULES'=>$modtype_tabsearch['system']);

	$change_mainmenu=false;
	$typkownsearch=array();

	// traitement des module de docs
	if (isset($modtype_tabsearch[dims_const::_DIMS_MENU_MODULEDOC]) && !empty($modtype_tabsearch[dims_const::_DIMS_MENU_MODULEDOC])) {
		$tpl_tabsearch[]=array('TITLE' =>$_DIMS['cste']['_DOCS'],'IMG'=>"./common/modules/doc/img/mod16.png",'URL' => "",'MODULES'=>$modtype_tabsearch[dims_const::_DIMS_MENU_MODULEDOC]);
	}
	$typkownsearch[dims_const::_DIMS_MENU_MODULEDOC]=dims_const::_DIMS_MENU_MODULEDOC;

	// traitement des modules de contenus
	if (isset($modtype_tabsearch[dims_const::_DIMS_MENU_MODULECONTENT]) && !empty($modtype_tabsearch[dims_const::_DIMS_MENU_MODULECONTENT])) {
		$tabarray=array();
		if (sizeof($modtype_tabsearch[dims_const::_DIMS_MENU_MODULECONTENT])>=1) $tabarray=$modtype_tabsearch[dims_const::_DIMS_MENU_MODULECONTENT];
		$tpl_tabsearch[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_CONTENT'],'IMG'=>"./common/modules/wce/img/mod16.png",'URL' => "",'MODULES'=>$tabarray);
		$typkownsearch[dims_const::_DIMS_MENU_MODULECONTENT]=dims_const::_DIMS_MENU_MODULECONTENT;
	}

	// traitement des modules de surveillance de contenus
	if (isset($modtype_tabsearch[dims_const::_DIMS_MENU_MODULEWATCH]) && !empty($modtype_tabsearch[dims_const::_DIMS_MENU_MODULEWATCH])) $tpl_tabsearch[]=array('TITLE' =>$_DIMS['cste']['_DIMS_LABEL_WATCH'],'IMG'=>$_SESSION['dims']['template_path']."/media/watch16.png",'URL' => "");
	$typkownsearch[dims_const::_DIMS_MENU_MODULEWATCH]=dims_const::_DIMS_MENU_MODULEWATCH;
	// traitement des autres modules differents

	// boucle sur les types de contenus
	foreach ($modtype_tabsearch as $mtype => $ctype) {
		if (!in_array($mtype,$typkownsearch)) {
			// on ajoute le nouvel onglet
			if (!empty($ctype)) {
				if (sizeof($ctype)>=1) $tabarray=$ctype;
				else $tabarray=array();

				if ($mtype!='system')
					$tpl_tabsearch[]=array('TITLE' =>$mtype, 'URL' => "",'SELECTED' => ($_SESSION['dims']['mainmenu'] == $mtype) ? 'selected' : '','MODULES'=>$tabarray,'IMG'=>"/modules/".$mtype."./common/img/mod16.png");
			}
		}
	}

	//	if (isset($refresh) && $refresh && isset($_SESSION['search']['searchresult'])) $dims_searchresult=$_SESSION['search']['searchresult'];

	// on traite maintenant le resultat en affichage
	//if (isset($_SESSION['dims']['search']['result']) && !empty($_SESSION['dims']['search']['result'])) {
		require_once(DIMS_APP_PATH . '/modules/system/desktop_search_words_result.php');
	//}
	echo "<div class=\"item-list\" style=\"display:block;width:100%;float:left;clear:both;\">";

	// traitement d'affichage des r�sultats
	$nbtab=sizeof($tpl_tabsearch);

	foreach ($tpl_tabsearch as $tab) {
		echo "<div style=\"clear:both;width:100%;margin:0 auto;\">";
		// title
		echo "<span style=\"float:left;width:99%;margin-left:5px;margin-top:10px;border-bottom:1px solid #CFCFCF;\">";
		$title=(isset($tab['TITLE2']) && $tab['TITLE2']!='') ? $tab['TITLE2']." / ".$tab['TITLE'] : $tab['TITLE'];
		echo "<span style=\"float:left;font-weight:bold;\"><img src=\"".$tab['IMG']."\" alt=\"\">&nbsp;".$title."</span>";

		// link option
		echo "<span style=\"float:right;\">";

		$checkedall=0;

		$blocklink='';
		if (isset($tab['MODULES']) && !empty($tab['MODULES'])) {
			foreach ($tab['MODULES'] as $idmoduletype =>$modu) {
				$idmodule=$modu['instanceid'];

				// on boucle sur les objets
				foreach ($dims_searchresult[$idmodule] as $idobject=>$object) {
					if (isset($dims_searchresult[$idmodule][$idobject]) && !empty($dims_searchresult[$idmodule][$idobject])) {
						$selec='';
						$mod=$dims->getModule($idmodule);
						if (isset($_SESSION['dims']['selectedfilternews'][$idmodule][$idobject])) {
							$style="font-weight:bold;";
							$unselec="&active_filter=2";
						}
						else {
							$style="";
							$unselec="";
						}
						// nouveau test
						if ($idmodule==1) {
							if ($object['nb']>0) {
								if (empty($_SESSION['dims']['selectedfilternews'][$idmodule])) {
									$_SESSION['dims']['selectedfilternews'][$idmodule][$idobject]=1;
									$style="font-weight:bold;";
									$unselec="&active_filter=2";
								}

								$checkedall++;
								if ($blocklink!='') $blocklink.= " | ";

								if ($idobject==dims_const::_SYSTEM_OBJECT_ACTION) {
									$blocklink.= "<a style=\"cursor:pointer;\" onclick=\"refreshDesktop(0,2,'&id_module=1&id_object=".dims_const::_SYSTEM_OBJECT_ACTION."$unselec');\"><font style=\"".$style."\">Action&nbsp;$selec&nbsp;(".$object['nb'].")</font></a>";
								}
								elseif ($idobject==dims_const::_SYSTEM_OBJECT_CONTACT) {
									$blocklink.= "<a style=\"cursor:pointer;\" onclick=\"refreshDesktop(0,2,'&id_module=1&id_object=".dims_const::_SYSTEM_OBJECT_CONTACT."$unselec');\"><font style=\"".$style."\">Contact&nbsp;$selec(".$object['nb'].")</font></a>";

									if ($object['nb']>0) {
										$blocklink.="&nbsp;<a href=\"/admin.php?dims_op=exportContactFromSearch\"><img src=\"./common/img/export.png\"></a>";
									}

									require_once DIMS_APP_PATH . '/modules/system/desktop_search_exportEmail.php';
								}
								elseif ($idobject==dims_const::_SYSTEM_OBJECT_TIERS) {
									$blocklink.= "<a style=\"cursor:pointer;\" onclick=\"refreshDesktop(0,2,'&id_module=1&id_object=".dims_const::_SYSTEM_OBJECT_TIERS."$unselec');\"><font style=\"".$style."\">".$_DIMS['cste']['_DIMS_LABEL_COMPANY']."&nbsp;$selec(".$object['nb'].")</font></a>";
									if ($object['nb']>0) {
										$blocklink.="&nbsp;<a href=\"/admin.php?dims_op=exportEntFromSearch\"><img src=\"./common/img/export.png\"></a>";
									}
								}
							}
						}
						else {
							if ($mod['instancename']!='' && $object['nb']>0) {
								$checkedall++;
								if ($blocklink!='') $blocklink.= " | ";
								$blocklink.= "<a style=\"cursor:pointer;\" onclick=\"refreshDesktop(0,2,'&id_module=".$idmodule."&id_object=".$idobject."$unselec');\"><font style=\"".$style."\">".$mod['instancename']."&nbsp;$selec(".$object['nb'].")</font></a>";
							}
						}
					}

				}
			}
		}

		// display all select
		//if ($checkedall>1) {
		//	echo $_DIMS['cste']['_DIMS_ALLS']." | ";
		//}
		echo $blocklink;

		echo "</span></span></div>";
		echo "<div style=\"float:left;width:96%;margin-left:0px;clear:both;\">";

		// on affiche le contenu de chaque moduletype
		if (isset($tab['MODULES']) && !empty($tab['MODULES'])) {
			foreach ($tab['MODULES'] as $idmoduletype =>$modu) {
				$idmodule=$modu['instanceid'];

				foreach ($dims_searchresult[$idmodule] as $idobject=>$object) {
					if (!isset($_SESSION['dims']['selectedfilternews']) || empty($_SESSION['dims']['selectedfilternews'][$idmodule]) || isset($_SESSION['dims']['selectedfilternews'][$idmodule][$idobject])) {
						if (!empty($object['tabresult'])) {
							echo "<font style=\"font-weight:bold;\">".$object['label']."</font><br>";
						}
						// nouveau test
						// quel est l'interêt de ces tests ? on utilise la même fonction et les mêmes paramètres à chaque fois
						/*if ($idmodule==1) {

							if ($idobject==dims_const::_SYSTEM_OBJECT_ACTION) {
								echo getSearchcontent($object['id_object'],$idmodule,$object['tabid'],$object['tabresult'],"search");
							}
							elseif ($idobject==dims_const::_SYSTEM_OBJECT_CONTACT) {
								echo getSearchcontent($object['id_object'],$idmodule,$object['tabid'],$object['tabresult'],"search");
							}
							else {
								echo getSearchcontent($object['id_object'],$idmodule,$object['tabid'],$object['tabresult'],"search");
							}
						}
						else {*/

											echo getSearchcontent($object['id_object'],$idmodule,$object['tabid'],$object['tabresult'],"search");
						//}
					}
				}
			}
		}

		echo "</div>";
	}

	echo "</div>";
	?>
<div class="doc_explorer_main" style="display:none;visibility:hidden;">
	<div style="float:left;"><img src="./common/img/arrow_ltr.png" border="0" alt="0"></div>
	<div style="float:left;margin-top:4px;"><a href="#" onclick="checkAllSearch(<? echo $_SESSION['dims']['nbselectedsearch'];	?>);"><? echo $_DIMS['cste']['_ALLCHECK']; ?></a>
	&nbsp;/&nbsp;<a href="#" onclick="uncheckAllSearch(<? echo $_SESSION['dims']['nbselectedsearch']; ?>);"><? echo $_DIMS['cste']['_ALLUNCHECK']; ?></a></div>
	<div style="float:left;margin:0px 0px 0px 10px;"><? echo $_DIMS['cste']['_DOC_LABEL_OPERATION'];?>&nbsp;
		<select name="op" id="op" onchange="validCommand(event);">
			<option value=""></option>
			<option value="tag"><? echo $_DIMS['cste']['_DIMS_LABEL_TAGS'];?></option>
		</select>
		<input type="hidden" id="iddestfolder" name="iddestfolder" value="0">
	</div>
</div>

	<?
		if (isset($_SESSION['dims']['current_object']['id_module']) && $_SESSION['dims']['current_object']['id_module']>0 ) {
		   $idm=$_SESSION['dims']['current_object']['id_module'];
		   $ido=$_SESSION['dims']['current_object']['id_object'];
		   $idr=$_SESSION['dims']['current_object']['id_record'];
			if (isset($dims_searchresult[$idm][$ido]['tabid'][$idr])) {
				$_SESSION['dims']['current_object']['mustview']=true;
			}
		}

		//
	/*
	// selection de l'objet courant
	if (isset($_SESSION['dims']['current_object']) && isset($_SESSION['dims']['current_object']['id_module'])) {
		$cur_moduleid=$_SESSION['dims']['current_object']['id_module'];
		$cur_objectid=$_SESSION['dims']['current_object']['id_object'];
		$cur_recordid=$_SESSION['dims']['current_object']['id_record'];
		echo "<script language=\"javascript\">";
		echo "window.onload=viewPropertiesObject(".$cur_objectid.",".$cur_recordid.",".$cur_moduleid.",0,1);resizeHome();";
		echo "</script>";
	}
	else {

	}*/
}
?>
