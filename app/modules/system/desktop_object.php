<?
$type=dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,false,false);
$subtype=dims_load_securvalue('subtype',dims_const::_DIMS_CHAR_INPUT,true,true,false);
$id=dims_load_securvalue('elem_id',dims_const::_DIMS_NUM_INPUT,true,false,false);
$auto=dims_load_securvalue('auto',dims_const::_DIMS_NUM_INPUT,true,false,false);

if (!isset($_SESSION['dims']['current_typetag'])) $_SESSION['dims']['current_typetag']=0; // generic

$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, true);

if ($typetag>0) {
	$_SESSION['dims']['current_typetag']=$typetag;
}
elseif ((isset($_GET['typetag']) || isset($_POST['typetag'])) && $_SESSION['dims']['current_typetag']!=0) {
	$_SESSION['dims']['current_typetag']=0;
}

if (isset($_SESSION['dims']['current_object'])) {
	$moduleid=$_SESSION['dims']['current_object']['id_module'];
	$objectid=$_SESSION['dims']['current_object']['id_object'];
	$recordid=$_SESSION['dims']['current_object']['id_record'];
	$idrecord=$recordid;

	if (!isset( $_SESSION['dims']['submenuobject'][$moduleid][$objectid]) || $_SESSION['dims']['submenuobject'][$moduleid][$objectid]=="" || $subtype==dims_const::_DIMS_SUBMENU_DETAIL)  {
	   $_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_DETAIL;
	}

	// on switche sur l'element couramment s�lectionne
	$desktopObject = array();
	$idDesktopObject=0;

	// D�tails
	if ($type!="activities") {
		$elem = array();
		$elem['name'] = "<img src='./common/img/data_view.png' border='0'>&nbsp;".$_DIMS['cste']['_EVENT_DETAILS'];
		$elem['id'] = dims_const::_DIMS_SUBMENU_DETAIL;
		$elem['link']= "javascript:viewPropertiesObject(".$objectid.",".$recordid.",".$moduleid.",\'subtype=".dims_const::_DIMS_SUBMENU_DETAIL."\');";
		$desktopObject[$idDesktopObject++]=$elem;

		$elem = array();
		$elem['name'] = "<img src='./common/img/annot.gif' border='0'>&nbsp;Comments";
		$elem['id'] = dims_const::_DIMS_SUBMENU_COMMENT_GEN;
		$elem['link']= "javascript:desktopViewComment();";
		$desktopObject[$idDesktopObject++]=$elem;

		$elem = array();
		$elem['name'] =  "<img src='./common/img/tag.png' border='0'>&nbsp;Tags";
		$elem['id'] = dims_const::_DIMS_SUBMENU_TAGS;
		$elem['link']= "javascript:desktopViewTag();";
		$desktopObject[$idDesktopObject++]=$elem;

		//if ($moduleid==1) { // contact or tiers or action
		$elem = array();
		$elem['name'] =  "<img src='./common/img/mod.png' border='0'>&nbsp;".$_DIMS['cste']['_DOCS'];
		$elem['id'] = dims_const::_DIMS_SUBMENU_DOCS;
		$elem['link']= "javascript:desktopViewDoc();";
		$desktopObject[$idDesktopObject++]=$elem;
		//}

		/*if ($moduleid==1) { // contact or tiers or action
			$elem = array();
			$elem['name'] =  "<img src='./common/img/mod.png' border='0'>&nbsp;Liste des biens";
			$elem['id'] = dims_const::_DIMS_SUBMENU_IMMO;
			$elem['link']= "javascript:desktopViewBiens();";
			$desktopObject[$idDesktopObject++]=$elem;
		}*/

		if ($moduleid==1 && ($objectid==dims_const::_SYSTEM_OBJECT_ACTION || $objectid==dims_const::_SYSTEM_OBJECT_EVENT)) { // contact or tiers or action
			$elem = array();
			$elem['name'] =  "<img src='./common/img/social.png' border='0'>&nbsp;".$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'];
			$elem['id'] = dims_const::_DIMS_SUBMENU_EVENTCONTACT;
			$elem['link']= "javascript:desktopViewEventContact();";
			$desktopObject[$idDesktopObject++]=$elem;
		}
	}

	// traitement des cas specifiques
	if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
		require_once (DIMS_APP_PATH . "/modules/system/".$_SESSION['dims']['_PREFIX']."/desktop_object.php");
	}

	$fileinclude='';
	$title='';
	$detailobject_description='';

	// pr�paration des onglets en fonction de l'objet que l'on ouvre
	switch($type) {
		case 'activities':
			switch ($_SESSION['dims']['desktop_view_date']) {
				case 0:
					$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
					$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
					break;
				case 1:
					$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
					$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
					break;
				case 2:
					$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
					$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
					break;
				case 3:
					$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
					$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
					break;
			}
			$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true, false,$_SESSION['dims']['action_activities']);
			if (isset($_GET['tagfilter']) && $action=="events") $action='contact_all';
			switch($action) {
				case 'tags':
					$title=$_DIMS['cste']['_DIMS_LABEL_TAGS'];
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb_desktop_tags_watch.php';
					break;
				case 'contact_new':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_contact.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']." ".strtolower($_DIMS['cste']['_DIMS_LABEL_SINCE'])." ".$date_since;
					break;
				case 'contact_modify':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_contact.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_MODIFIED_SHEET_SINCE']." ".strtolower($_DIMS['cste']['_DIMS_LABEL_SINCE'])." ".$date_since;
					break;
				case 'contact_veille':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_contact.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_WATCH_CONT'];
					break;
				case 'ent_new':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_ent.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']." ".strtolower($_DIMS['cste']['_DIMS_LABEL_SINCE'])." ".$date_since;
					break;
				case 'ent_modify':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_ent.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_MODIFIED_SHEET_SINCE']." ".strtolower($_DIMS['cste']['_DIMS_LABEL_SINCE'])." ".$date_since;
					break;
				case 'ent_veille':
					$fileinclude=DIMS_APP_PATH . '/modules/system/lfb/lfb_desktop_activities_ent.php';
					$title=$_DIMS['cste']['_DIMS_LABEL_WATCH_ENT'];
					break;
			}
			$elem = array();
			$elem['name'] = $title;
			$elem['id'] = dims_const::_DIMS_SUBMENU_DETAIL;
			$desktopObject[$idDesktopObject++]=$elem;
			break;
		case 'event':
			$id_evt = dims_load_securvalue('elem_id', dims_const::_DIMS_NUM_INPUT, true);

			$elem['name']=$_DIMS['cste']['_PREVIEW'];
			$elem['id']=dims_const::_DIMS_SUBMENU_PREVIEW;
			$elem['src']="./common/img/view.png";
			$elem['width']= "width:115px";
			$elem['link']= "javascript:desktopViewPreview();";
			// structure decrite dans /modules/system/desktop_object.php
			$desktopObject[$idDesktopObject++]=$elem;
			break;
		case 'contact':
			$contact_id = dims_load_securvalue('elem_id', dims_const::_DIMS_NUM_INPUT, true);
			// voir la fiche compl�te
			$elem = array();
			$elem['name'] = $_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'];
			$elem['id'] = 100;
			$elem['link'] = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.
								'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.
								'&contact_id='.$contact_id;
			$desktopObject[$idDesktopObject++]=$elem;
			break;
		case 'vcard':
		case  'preview':
		case 'comments':
		case 'viewtag':
		case 'object':
		case 'viewdoc':
		case 'vieweventcontact':
		case 'viewimmo':

			switch ($type) {
				case  'preview':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_PREVIEW;break;
				case  'comments':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_COMMENT_GEN;break;
				case  'viewtag':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_TAGS;break;
				case  'viewdoc':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_DOCS;break;
				case  'viewimmo':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_IMMO;break;
				case 'vieweventcontact':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_EVENTCONTACT;break;
				case 'vcard':$_SESSION['dims']['submenuobject'][$moduleid][$objectid]=dims_const::_DIMS_SUBMENU_VCARD;break;
			}
			global $obj;

			$_SESSION['dims']['current_object']['cmd']=array();
			if (isset($_SESSION['dims']['current_object'])) {

				$mod=$dims->getModule($moduleid);

				if (($dims->isModuleEnabled($moduleid) || $mod['contenttype']=='doc')  || $moduleid==1) {

					$res=$db->query("SELECT m.active,mt.label from dims_module_type as mt inner join dims_module as m on m.id_module_type=mt.id and m.id= :moduleid ",array(
						':moduleid' => $moduleid
					));

					if ($db->numrows($res)>0 || $mod['contenttype']=='doc' || $moduleid!=1) {
						$mod=$db->fetchrow($res);
					}

					if ($mod['active'] || $mod['contenttype']=='doc' || $moduleid==1) {
						$dims_mod_opfile = DIMS_APP_PATH . "/modules/{$mod['label']}/op.php";

						if (file_exists($dims_mod_opfile)) {
							//echo $dims_mod_opfile;
							include $dims_mod_opfile;
						}
					}

				}

				// object EVENT
				/*$elem= array();
				$elem['name']=$_DIMS['cste']['_PREVIEW'];
				$elem['id']=dims_const::_DIMS_SUBMENU_PREVIEW;
				$elem['src']="./common/img/view.png";
				$elem['width']= "width:115px";
				$elem['link']= "javascript:desktopViewPreview();";
				$desktopObject[$idDesktopObject++]=$elem;*/
				// Add todo
				$elem= array();
				$elem['name']=$_DIMS['cste']['_ADDTO_DO'];
				$elem['src']="./common/img/add.gif";
				$elem['link']= "";
				$elem['width']= "";
				$elem['script']="displayAddTodo(event,".$objectid.",".$moduleid.",".$recordid.");";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;

				$workspaceid=$_SESSION['dims']['workspaceid'];
				$moduletype=$mod['label'];

				require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
				$dims_user = new user();
				$dims_user->open($_SESSION['dims']['userid']);

				// construction des favoris
				$favorites=$dims_user->getFavorites($moduleid);

								if (isset($_SESSION['dims']['current_object']['label'])) {
									$label=$_SESSION['dims']['current_object']['label'];
								}
								else {
									$label='';
								}

				$title=str_replace("<OBJECT>","",$_DIMS['cste']['_DIMS_OBJECT_PROPERTIES']);
			}

			$value=0;
			$elem=array();
			// gestion des fonctions standards
			// on regarde maintenant les favoris / en veille
			if (isset($favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]) && $favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type']>0) {
				$idfav=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['id'];
				$value=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type'];
			}
			else {
				$idfav=0;
				$value=0;
			}

			if (isset($_SESSION['dims']['desktop_collab']) && ($_SESSION['dims']['desktop_collab']== dims_const::_DIMS_CSTE_FAVORITE || $_SESSION['dims']['desktop_collab']==dims_const::_DIMS_CSTE_SURVEY))
				$refresh=1;
			else
				$refresh=0;

			if (!isset($obj->fields['id_user']) || $obj->fields['id_user']<0) {
				$obj->fields['id_user']=0;
			}
			// on traite le en veille
			if ($value!=2) {
				// add favor
				$elem['name']=$_DIMS['cste']['_ADDTO_FAVORITES'];
				$elem['src']="./common/img/fav1.png";
				$elem['link']= "";
				$elem['width']= "";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",2,".$obj->fields['id_user'].",0,".$refresh.");";
				$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;

				if ($value==0) {
					// add wait
					$elem['name']=$_DIMS['cste']['_DIMS_ADDTO_SURVEY'];
					$elem['src']="./common/img/view.png";
					$elem['link']= "";
					$elem['width']= "";
					$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",1,".$obj->fields['id_user'].",0,".$refresh.");";
					$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
					$_SESSION['dims']['current_object']['cmd'][]=$elem;
				}
				else {
					// remove from wait
					$elem['name']=$_DIMS['cste']['_DIMS_REMOVEFROM_SURVEY'];
					$elem['src']="./common/img/delete.png";
					$elem['link']= "";
					$elem['width']= "";
					$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$obj->fields['id_user'].",0,".$refresh.");";
					$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
					$_SESSION['dims']['current_object']['cmd'][]=$elem;
				}
			}
			else {
				// on peut annuler le favoris
				$elem['name']=$_DIMS['cste']['_REMOVEFROM_FAVORITES'];
				$elem['src']="./common/img/delete.png";
				$elem['link']= "";
				$elem['width']= "";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$obj->fields['id_user'].",0,".$refresh.");";
				$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;
			}

						/*if ($moduleid==1 && $objectid== dims_const::_SYSTEM_OBJECT_TIERS) {
							$obj=new tiers();
							$obj->open($idrecord);
							$elem['name']="Bar code";
							$elem['src']="./common/img/barcode.png";
							$elem['link']= "";
							$elem['width']= "";
							$elem['script']= "printBarcode();";
							$_SESSION['dims']['current_object']['cmd'][]=$elem;
						}*/

			break;
	}

	$elem = array();
	$elem['name'] = "<img src='./common/img/close.png' border='0'>&nbsp;".$_DIMS['cste']['_DIMS_CLOSE'];
	$elem['id'] = -1;
	$elem['link']= "javascript:closePropertiesObject(".dims_const::_DIMS_MENU_HOME.",0);";
	$desktopObject[$idDesktopObject++]=$elem;
	// affichage
	echo $skin->displayOnglet($desktopObject,'object_tab',1,$_SESSION['dims']['submenuobject'][$moduleid][$objectid]);

	// separateur de contenus
	echo '||';

	// check for auto refresh : must check if current tab is another value
	if ($auto==1 && isset($_SESSION['dims']['submenuobject'][$moduleid][$objectid])) {
		$type=$_SESSION['dims']['submenuobject'][$moduleid][$objectid];
	}

	// traitement des cas sp�cifiques
	if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
		require_once (DIMS_APP_PATH . "/modules/system/".$_SESSION['dims']['_PREFIX']."/desktop_object_detail.php");
	}

	switch($type) {
		case 'activities':
			require_once(DIMS_APP_PATH . '/modules/system/desktop_detail_activities.php');
			break;
		case 'event':
			require_once(DIMS_APP_PATH . '/modules/system/desktop_detail_event.php');
			break;
		case 'contact':
			require_once(DIMS_APP_PATH . '/modules/system/desktop_detail_contact.php');
			break;
		case dims_const::_DIMS_SUBMENU_IMMO:
		case 'viewimmo':
			require_once(DIMS_APP_PATH . '/modules/immo/desktop_bloc_object.php');
			break;
		case 'viewdoc':
		case dims_const::_DIMS_SUBMENU_DOCS:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_object_detail.php');
			require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_doc.php');
			break;
		case 'vieweventcontact':
		case dims_const::_DIMS_SUBMENU_EVENTCONTACT:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_object_detail.php');
			$_GET['id_evt']=$recordid;
			require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_eventcontacts.php');
			break;
		case 'comments':
		case 'viewcomment':
		case dims_const::_DIMS_SUBMENU_COMMENT_GEN:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_object_detail.php');
			require_once(DIMS_APP_PATH . '/modules/system/desktop_detail_comments.php');
			break;
		case 'preview':
		case dims_const::_DIMS_SUBMENU_PREVIEW:

				$idmodule=$_SESSION['dims']['current_object']['id_module'];
				$idobject=$_SESSION['dims']['current_object']['id_object'];
				$idrecord=$_SESSION['dims']['current_object']['id_record'];

				//	catch detail level from object
				if ($idrecord>0 && $idobject>0 && $idmodule>0) {
					//{$_SESSION['dims']['modules'][$idmodule]['moduletype']}
					$mod=$dims->getModule($idmodule);

					if(isset($mod['label'])){
						$modtype=$mod['label'];
						$_GET['dims_op']='preview';
						$dims_mod_opfile = DIMS_APP_PATH . "/modules/{$modtype}/op.php";

						if (file_exists($dims_mod_opfile)) include $dims_mod_opfile;

					}else{
						echo $_DIMS['cste']['_DIMS_TICKET_NO_OBJECT'];
					}
				}
			break;
		case 'vcard':
		case dims_const::_DIMS_SUBMENU_VCARD:
			echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_VCARD'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
			$docfile = new docfile();
			$docfile->open($_SESSION['dims']['current_object']['id_record']);

			$sql2 = "SELECT	name_vcard, id_contact, num, date_modify
					 FROM	dims_mod_vcard
					 WHERE	id_docfile = :iddocfile ";
			$res2 = $db->query($sql2, array(
				':iddocfile' => $_SESSION['dims']['current_object']['id_record']
			));
			if ($db->numrows($res2) == 0){
				if (file_exists($docfile->getfilepath())) {
					$num = 1 ;
					$content = fopen($docfile->getwebpath(), 'r');
					while($ligne = fgets($content)) {
						if (substr($ligne,0,5) == "BEGIN"){

							while(($ligne = fgets($content)) && (substr($ligne,0,3) != "END")){

								if (substr($ligne,0,2) == "FN"){
									$fn = substr($ligne,3);
									$tabfn = explode(" ",$fn);

									$sql3 = "INSERT INTO	dims_mod_vcard
											 VALUES			( :docfile , :fn , '0', :num ,'')";
									$db->query($sql3, array(
										':docfile'	=> $docfile->fields['id'],
										':fn'		=> trim($fn),
										':num'		=> $num
									));
									$num ++ ;
								}
							}
						}
					}
					fclose($content);
				}
				$res2 = $db->query($sql2, array(
					':iddocfile' => $_SESSION['dims']['current_object']['id_record']
				));
			}

			$compt = 0 ;

			$cont = '' ;
			$class = 'trl2';
			while($etat = $db->fetchrow($res2)){
				$class = ($class == 'trl1') ? 'trl2' : 'trl1';
				if ($etat['id_contact'] > 0){
					// afficher les contacts liés à la vcard
					$sql3 = "SELECT	lastname, firstname
							 FROM	dims_mod_business_contact
							 WHERE	id = :id ";
					if ($res3 = $db->query($sql3, array(':id' => $etat['id_contact']))){
						$contact = $db->fetchrow($res3);
						$cont .=	'<tr class="'.$class.'">
										<td style="text-align:left; color: #333;">';
						$cont .=			'<a target=_BLANK href="./admin.php?dims_mainmenu=9&cat=0&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$etat['id_contact'].'">';
						$cont .=				$contact['firstname']." ".$contact['lastname'].
											'</a>';
						$cont .=		'</td>';
						$dateLocal = dims_timestamp2local($etat['date_modify']);
						$cont .=		'<td>'.
											$_DIMS['cste']['_DIMS_LABEL_IMPORT_DATE'].' '.$dateLocal['date'].
										'</td>';
						$cont .= '</tr>';
						$compt ++ ;
					}
				}else{
					$cont .=		'<tr class="'.$class.'">
										<td style="text-align:left; color: #333;">';
					$cont .=				$etat['name_vcard'];
					$cont .=			'</td>
									</tr>';
				}
			}

			echo '<table style="width:100%;" cellspacing="0" cellpadding="1">'
					.$cont.
				  '</table>';

			echo $skin->close_simplebloc();
			break;
		case 'viewtag':
		case dims_const::_DIMS_SUBMENU_TAGS:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_object_detail.php');
			echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
			require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
			echo $skin->close_simplebloc();
			break;
		case 'object':
		case dims_const::_DIMS_SUBMENU_DETAIL:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_object_detail.php');

			/*echo "<div style='clear:both;width:100%;float:left'>";
			// Affichage de quelques contenus
			$sql= "select			distinct s.*,ku.id_sentence,mf.label
					from			dims_keywords_sentence as s
					inner join		dims_keywords_usercache as ku
					on				ku.id_module=".$moduleid."
					and				ku.id_object=".$objectid."
					and				ku.id_record=".$recordid."
					and				s.id=ku.id_sentence
					inner join		dims_mb_field as mf
					on				mf.id=s.id_metafield
					order by		s.id_metafield";

			$typecontent='';

			$res=$db->query($sql);
			if ($db->numrows($res)) {
				echo $skin->open_backgroundbloc($_DIMS['cste']['_DIMS_LABEL_CONTENT'],'100%');
				echo "<div style=\"width:100%;float:left;height:200px;overflow:auto;\">";
				while ($f=$db->fetchrow($res)) {
					if ($typecontent!=$f['label']) {
						if (isset($_DIMS['cste'][$f['label']])) {
							$typecontent=$_DIMS['cste'][$f['label']];
						}
						else {
							$typecontent=$f['label'];
						}
						echo "</ul><span style=\"float:left;font-weight:bold;width:100%;\"><img src=\"./common/img/bullet_sel.png\">".$typecontent."</span><ul>";
					}

					if (!empty($_SESSION['dims']['search']['listselectedword'])) {
						foreach($_SESSION['dims']['search']['listselectedword'] as $id=>$elem) {
							$f['content']= str_replace($elem['word'],"<b>".$elem['word']."</b>",$f['content']);
						}
					}
					echo "<li>- ".$f['content']."</li>";
				}
				echo "</ul></div>";
				echo $skin->close_backgroundbloc();
			}
			echo "</div>";*/
			// on affiche l'historique des modifications effectuees sur le document
			echo $skin->open_simplebloc($_DIMS['cste']['_ACTIONHISTORY'],'100%');
			echo "<div style=\"width:100%;float:left;height:200px;overflow:auto;\">";
			?>
			<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
			<TR BGCOLOR=<? echo $skin->values['bgline1']; ?>>
				<TD><B><? echo $_DIMS['cste']['_DIMS_DATE']; ?></B></TD>
				<TD><B><? echo $_DIMS['cste']['_DIMS_LABEL_USER']; ?></B></TD>
				<TD><B><? echo $_DIMS['cste']['_LABEL_ACTION']; ?></B></TD>
			</TR>
			<?
			$user_action = dims_get_user_action_log($recordid,$objectid,$moduleid);

			$color = $skin->values['bgline1'];

			foreach($user_action as $key => $value) {
				if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
				else $color=$skin->values['bgline2'];

				$localdate = dims_timestamp2local($value['timestp']);

				?>
				<TR BGCOLOR="<? echo $color; ?>">
					<TD><? echo "$localdate[date] $localdate[time]"; ?></TD>
					<TD><? echo $value['user_name']; ?></TD>
					<TD><? echo $value['action_label']; ?></TD>
				</TR>
				<?
			}
			echo "</table></div>";
			echo $skin->close_simplebloc();
			break;
	}
}
else {
	require_once(DIMS_APP_PATH . '/modules/system/lfb_widget_desktop_tags.php');
}
?>
