<?
$search = '';
$firstpart="";
$secondpart="";
$label="";
//$workspace = new workspace();
$workspace = $dims->getWorkspaces($_SESSION['dims']['workspaceid']);
//$workspace->open($_SESSION['dims']['workspaceid']);
//$workspaceid=$_SESSION['dims']['workspaceid'];

//require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');
//$blockuser = new block_user();
//$blockuser->open($_SESSION['dims']['userid'],$moduleid,$workspaceid);
//$datelastvalidate=$blockuser->fields['date_lastvalidate'];

$module=$dims->getModule($moduleid);
$moduletype=$module['label'];

$lsttag="0";

if (isset($_SESSION['dims']['search']['listselectedtag'])) {
	foreach($_SESSION['dims']['search']['listselectedtag'] as $k=>$v) {
		if ($lsttag=="") {
			$lsttag=$k;
		}
		else {
			$lsttag.=",".$k;
		}
	}
}

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////// rdv /////////////////////////////////////////////////////////////////////////////////*/
/*
// test if available in this workspace
if ($workspace['planning']) {
	$ok=false;
	switch($dims_op) {
		case 'searchfavorites':
			$sql="	select		a.*,
								u.lastname,u.firstname
					FROM		dims_favorite as f
					inner join	dims_user as u
					on			u.id=f.id_user_from
					and			id_user=".$_SESSION['dims']['userid']."
					and			id_module=".$moduleid;

			if	($_SESSION['dims']['desktop_view_workspace']==1) {
					$sql.=" AND			f.id_workspace IN ($workspaces)";
			}
			else {
					$sql.=" AND			f.id_workspace = ".$_SESSION['dims']['workspaceid'];
			}

			$sql.="
					and			f.type=".$_SESSION['dims']['favorites']."
					and			f.id_object=".dims_const::_SYSTEM_OBJECT_ACTION."
					INNER JOIN	dims_mod_business_action as a
					ON			a.id=f.id_record
					GROUP BY	f.id_module, f.id_object, f.id_record";
			$ok=true;
		break;

		case 'searchannot':
		case 'searchnewscontent':
		case 'searchnews':
				// recherche dans les contacts
				$sql =	"
						SELECT		distinct a.*,
									u.lastname,u.firstname
						FROM		dims_mod_business_action a
						INNER JOIN	dims_workspace w";


				if	($_SESSION['dims']['desktop_view_workspace']==1) {
						$sql.=" ON			a.id_workspace = w.id AND a.id_workspace AND w.id IN ($workspaces)";
				}
				else {
						$sql.=" ON			a.id_workspace = w.id AND a.id_workspace AND w.id = ".$_SESSION['dims']['workspaceid'];
				}

				$sql.= " AND			a.id_parent=0
						AND			a.id_module=".$moduleid." AND ((a.personnel=0 AND a.type=1) Or (a.personnel=1 AND a.id_user=".$_SESSION['dims']['userid']." AND a.type=1) OR (a.type=2 AND	a.id_workspace = ".$_SESSION['dims']['workspaceid']."))";

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object=".dims_const::_SYSTEM_OBJECT_ACTION."
							and ti.id_module=1 and ti.id_record=a.id
							and ti.id_tag in (".$lsttag.")";
				}
				else {
					if ($datelastvalidate>0) $sql.=" AND a.timestp_modify>$datelastvalidate";
				}

				if ($_SESSION['dims']['adminlevel']<dims_const::_DIMS_ID_LEVEL_GROUPADMIN)
					$sql .="
						LEFT JOIN	dims_mod_business_action_utilisateur as au
						ON			au.action_id=a.id AND au.user_id=".$_SESSION['dims']['userid']."
						AND			a.type=1
						AND			a.id_parent=0";

					$sql.="
						INNER JOIN	dims_user u
						ON			a.id_user = u.id
						";

				// on doit filtrer sur les acteurs pour qu'il y soit

				$sql.="
						ORDER BY	a.timestp_modify desc";

				$ok=true;
			break;
		default:
				$tabcorresp=getSearchReponse(dims_const::_SYSTEM_OBJECT_ACTION,$moduleid);

				if (sizeof($tabcorresp)>0)	$ok=true;
				// recherche
				$sql =	"
						SELECT		distinct a.*,
									u.lastname,u.firstname
						FROM		dims_mod_business_action a";

				//if  ($_SESSION['dims']['desktop_view_workspace']==1) {
						$sql.=" INNER JOIN	dims_workspace w ON a.id_workspace = w.id AND w.id IN ($workspaces)";


				$sql.= "
						AND			a.id_parent=0
						AND			a.id in (".implode(',',$tabcorresp).")
						AND			a.id_module=".$moduleid." AND (a.personnel=0 Or (a.personnel=1 AND a.id_user=".$_SESSION['dims']['userid']." and a.type=1) OR (a.type=2 AND	a.id_workspace = ".$_SESSION['dims']['workspaceid']."))";

				if ($_SESSION['dims']['adminlevel']<dims_const::_DIMS_ID_LEVEL_GROUPADMIN)
					$sql .="
						LEFT JOIN	dims_mod_business_action_utilisateur as au
						ON			au.action_id=a.id AND au.user_id=".$_SESSION['dims']['userid']."
						AND			a.type=1
						AND			a.id_parent=0";

					$sql.="
						INNER JOIN	dims_user u
						ON			a.id_user = u.id
						ORDER BY	a.libelle
						";
				//$ok=true;
				//echo $sql;
			break;
	}

	if ($ok) {
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['id_object'] =dims_const::_SYSTEM_OBJECT_ACTION;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['label']=$_DIMS['cste']['_BUSINESS_ACTION'];
		$res=$db->query($sql);

		if ($db->numrows($res)==1) {
				 $dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['title']="1 ".$_DIMS['cste']['_BUSINESS_ACTION'];
				 $dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['nb'] = 1;
		 }
		 else {
			if ($db->numrows($res)>0) {
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['title']=$db->numrows($res)." ".$_DIMS['cste']['_BUSINESS_ACTION'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['nb'] = $db->numrows($res);
			 }
			 else {
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['title']=$_DIMS['cste']['_BUSINESS_NO_ACTION'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['nb'] = 0;
			 }
		}

		$tabresult=array();
		$tabid=array();
		$lastchange=0;
		$lastuser="";

			while ($row = $db->fetchrow($res)) {

				$tabid[$row['id']]=$row['id'];
				$elem=array();
				$elem['id']=$row['id'];

				//calcul de diff�rence de jour
				$annee = substr($row['datejour'], 0, 4); // on r�cup�re le jour
				$mois = substr($row['datejour'], 5, 2); // puis le mois
				$jour = substr($row['datejour'], 8, 2);

				if (DIMS_DATEFORMAT==dims_const::DIMS_DATEFORMAT_FR)
					$datecumul=$jour."/".$mois."/".$annee;
				else
					$datecumul=$annee."/".$mois."/".$jour;

				$timestamp = mktime(0, 0, 0, $mois, $jour, $annee);
				$maintenant=time();
				$ecart_secondes = $timestamp-$maintenant;
				$ecart=floor($ecart_secondes / (60*60*24));

				$elem['title']=$row['libelle']." pour le ".$datecumul;
				$elem['titlelink']=$row['libelle'];
				$elem['link']= dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&cat=-1&dayadd=".$ecart."&actionid=".$row['id']);
				$elem['content']=dims_strcut($row['description'],175);
				$elem['author']=strtoupper(substr($row['firstname'],0,1)).". ".$row['lastname'];
				$elem['timestp_modify']=$row['timestp_modify'];
				$elem['annot']=0;
				$tabresult[$elem['id']]=$elem;

				if($lastchange<$row['timestp_modify']) {
					$lastchange=$row['timestp_modify'];
					$lastuser=$elem['author'];
				}
			}

			if (!isset($tabcorresp)) $tabcorresp=$tabid;

			// calcul de la derni�re maj


			//$secondpart.= getSearchcontent(dims_const::_SYSTEM_OBJECT_ACTION,$moduleid,$tabcorresp,$tabresult,$dims_op=='searchnews',$dims_op);

		//$secondpart.="</div>";
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['tabid']=$tabid;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['tabresult']=$tabresult;
	}
}

 */

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////// contacts /////////////////////////////////////////////////////////////////////////////////*/
if ($workspace['contact']) {
	$label="";
	$ok=false;
	$param = array();
	switch($dims_op) {
		case 'searchfavorites':
			$sql="	select					c.*,
														u.lastname as author_name,
														u.firstname as author_firstname,
														u.id_contact as id_author
					FROM		dims_favorite as f
					inner join	dims_user as u
					on		u.id=f.id_user_from
					and			id_user= :iduser
					and			id_module= :moduleid ";
			$param[':iduser'] = $_SESSION['dims']['userid'];
			$param[':moduleid'] = $moduleid;
			if	($_SESSION['dims']['desktop_view_workspace']==1) {
				$sql.=" and f.id_workspace IN ($workspaces)";
			}
			else {
				$sql.=" and f.id_workspace = :workspaceid ";
				$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
			}

			$sql.="
					and			f.type= :type
					and			f.id_object= :idobject
					INNER JOIN	dims_mod_business_contact as c
					ON			c.id=f.id_record
					GROUP BY	f.id_module, f.id_object, f.id_record";
			$param[':type'] = $_SESSION['dims']['favorites'];
			$param[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;
			$ok=true;
		break;
		case 'searchannot':
		case 'searchnewscontent':
		case 'searchnews':
				//$datelastvalidate=$_SESSION['dims']['workspaces'][$workspaceid]['modules'][$moduleid]['date_lastvalidate'];

				// recherche dans les contacts
				$sql =	"
						SELECT		distinct c.*,
								u.lastname as author_name,
								u.firstname as author_firstname,
								u.id_contact as id_author
						FROM		dims_mod_business_contact as c";

				$sql.=	" INNER JOIN dims_user u ON u.id_contact = c.id_user_create
						AND			";

				if	($_SESSION['dims']['desktop_view_workspace']==1) {
						$sql.=" c.id_workspace IN ($workspaces)";
				}
				else {
						$sql.=" c.id_workspace = :workspaceid ";
						$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
				}

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
							and ti.id_module=1 and ti.id_record=c.id
							and ti.id_tag in (".$lsttag.")";
					$param[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;
				}
				else {
					if ($datelastvalidate>0) $sql.=" AND c.timestp_modify> :datelastvalidate ";
					$param[':datelastvalidate'] = $datelastvalidate;
				}

				$sql.="
						group by	c.id
						ORDER BY	c.timestp_modify desc
						";

				$ok=true;

			break;
		default:
				$tabcorresp=getSearchReponse(dims_const::_SYSTEM_OBJECT_CONTACT,$moduleid);

				if (sizeof($tabcorresp)>0) $ok=true;
				else $tabcorresp[]=0;
				// recherche
				$sql =	"
						SELECT		distinct c.*,
									u.lastname as author_name,
									u.firstname as author_firstname,
									u.id_contact as id_author
						FROM		dims_mod_business_contact c";

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
							and ti.id_module=1 and ti.id_record=c.id
							and ti.id_tag in (".$lsttag.")";
					$param[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;
				}

				$sql.=	" LEFT JOIN dims_user as u ON u.id_contact = c.id_user_create

						WHERE	c.id in (".implode(',',$tabcorresp).")";

				//if  ($_SESSION['dims']['desktop_view_workspace']==1) {
				//		$sql.=" and c.id_workspace IN ($workspaces)";
				/*}
				else {
						$sql.=" and c.id_workspace = ".$_SESSION['dims']['workspaceid'];
				}*/

				$sql.="	ORDER BY	c.lastname";

			break;
	}

	if ($ok) {
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['id_object'] =dims_const::_SYSTEM_OBJECT_CONTACT;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['label']=$_DIMS['cste']['_DIMS_LABEL_CONTACTS'];

		$res=$db->query($sql, $param);

		if ($db->numrows($res)==1) {
			$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['title']=$db->numrows($res)." ".$_DIMS['cste']['_DIMS_LABEL_FOUND'];
			$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['nb'] = $db->numrows($res);
		}
		else {
			if ($db->numrows($res)>0) {
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['title']=$db->numrows($res)." ".$_DIMS['cste']['_DIMS_LABEL_FOUNDS'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['nb'] = $db->numrows($res);
			}
			else {
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['title']=$_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['nb'] = 0;
			}
		}

	$tabresult=array();
	$tabid=array();
	$lastchange=0;
	$lastuser="";

		while ($row = $db->fetchrow($res)) {
			$tabid[$row['id']]=$row['id'];
			$elem=array();
			$elem['id']=$row['id'];
			$elem['title']=$row['lastname']." ".$row['firstname'];
			$elem['titlelink']=$row['lastname']." ".$row['firstname'];

			if ($row['photo']!='') {
				$filephoto=DIMS_WEB_PATH . 'data/photo_cts/contact_'.$row['id'].'/photo60'.$row['photo'].'.png';
				if (file_exists($filephoto)) {
					$photo= _DIMS_WEBPATHDATA.'photo_cts/contact_'.$row['id'].'/photo60'.$row['photo'].'.png';
				}
				else {
					$photo="./common/img/contact.gif";
				}
			}
			else {
				$photo="./common/img/contact.gif";
			}
			$elem['photo']=$photo;

			$elem['link']= dims_urlencode("admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action=301&contact_id=".$row['id']);
			$elem['content']="";
			$elem['author']=strtoupper(substr($row['author_firstname'],0,1)).". ".$row['author_name'];
			$elem['timestp_modify']=$row['timestp_modify'];
			$elem['annot']=0;
			$tabresult[$elem['id']]=$elem;

			if($lastchange<$row['timestp_modify']) {
				$lastchange=$row['timestp_modify'];
				$lastuser=$elem['author'];
			}
		}

		if (!isset($tabcorresp)) $tabcorresp=$tabid;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['tabid']=$tabid;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_CONTACT]['tabresult']=$tabresult;
		// calcul de la derni�re maj
		/*
		if ($lastchange!=0) {
			$firstpart.=" <font class=\"fontgray\"> - ".dims_getLastModify($lastchange);
			if ($lastuser!="") $firstpart.=" ".$_DIMS['cste']['_DIMS_LABEL_FROM']." ".$lastuser;
			$firstpart.="</font><br>";
		}*/
		//$secondpart.= getSearchcontent(dims_const::_SYSTEM_OBJECT_CONTACT,$moduleid,$tabcorresp,$tabresult,$dims_op=='searchnews',$dims_op);
		//$secondpart.="</div>";
	}


	/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////// entreprises /////////////////////////////////////////////////////////////////////////////////*/

	// on s'occupe maintenant des entreprises
	$label="";
	$ok=false;
	$param = array();
	switch($dims_op) {
		case 'searchfavorites':
			$sql="	select		t.*,
								u.lastname,u.firstname
					FROM		dims_favorite as f
					inner join	dims_user as u
					on			u.id=f.id_user_from
					and			id_user= :iduser
					and			id_module= :moduleid ";
			$param[':iduser'] = $_SESSION['dims']['userid'];
			$param[':moduleid'] = $moduleid;

			if	($_SESSION['dims']['desktop_view_workspace']==1) {
				$sql.=" AND			f.id_workspace IN ($workspaces)";
			}
			else {
				$sql.=" AND			f.id_workspace = :workspaceid ";
				$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
			}

			$sql.="
					and			f.type= :type
					and			f.id_object= :idobject
					INNER JOIN	dims_mod_business_tiers as t
					ON			t.id=f.id_record
					GROUP BY	f.id_module, f.id_object, f.id_record";
			$param[':type'] = $_SESSION['dims']['favorites'];
			$param[':idobject'] = dims_const::_SYSTEM_OBJECT_TIERS;
			$ok=true;
		break;
		case 'searchannot':
		case 'searchnewscontent':
		case 'searchnews':
				//$datelastvalidate=$_SESSION['dims']['workspaces'][$workspaceid]['modules'][$moduleid]['date_lastvalidate'];

				// recherche dans les entreprises
				$sql =	"
						SELECT		distinct t.*,
									u.lastname,u.firstname
						FROM		dims_mod_business_tiers t
						INNER JOIN	dims_workspace w";

				if	($_SESSION['dims']['desktop_view_workspace']==1) {
					$sql.=" ON			t.id_workspace = w.id AND t.id_workspace AND w.id IN ($workspaces)";
				}
				else {
					$sql.=" ON			t.id_workspace = w.id AND t.id_workspace AND w.id = :workspaceid";
					$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
				}

				$sql.="
						AND			t.id_module= :moduleid
						INNER JOIN	dims_user u
						ON			t.id_user = u.id
						";
				$param[':moduleid'] = $moduleid ;

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
							and ti.id_module=1 and ti.id_record=t.id
							and ti.id_tag in (".$lsttag.")";
					$param[':idobject'] = dims_const::_SYSTEM_OBJECT_TIERS ;
				}
				else {
					if ($datelastvalidate>0) $sql.=" AND t.timestp_modify> :datelastvalidate ";
					$param[':moduleid'] = $datelastvalidate ;
				}

				$sql.="
						group by	t.id
						ORDER BY	t.timestp_modify desc
						";
				$ok=true;
			break;
			default:
				$tabcorresp=getSearchReponse(dims_const::_SYSTEM_OBJECT_TIERS,$moduleid);

				if (sizeof($tabcorresp)>0) $ok=true;
				// recherche
				$sql =	"
						SELECT		distinct t.*,
									u.lastname,u.firstname
						FROM		dims_mod_business_tiers t
												";
				//		INNER JOIN	dims_workspace w";

				//if  ($_SESSION['dims']['desktop_view_workspace']==1) {
				//		$sql.=" ON			t.id_workspace = w.id AND t.id_workspace AND w.id IN ($workspaces)";
				/*}
				else {
						$sql.=" ON			t.id_workspace = w.id AND t.id_workspace AND w.id = ".$_SESSION['dims']['workspaceid'];
				}*/

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
							and ti.id_module=1 and ti.id_record=t.id
							and ti.id_tag in (".$lsttag.")";
					$param[':idobject'] = dims_const::_SYSTEM_OBJECT_TIERS;
				}

				$sql.="	INNER JOIN	dims_user u	ON t.id_user = u.id
										WHERE			t.id in (".implode(',',$tabcorresp).")

						ORDER BY	t.intitule
						";
				//$ok=true;
			break;
	}

	if ($ok) {
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['id_object'] =dims_const::_SYSTEM_OBJECT_TIERS;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['label']=$_DIMS['cste']['_DIMS_LABEL_ENTERPRISES'];

		$res=$db->query($sql, $param);

		if ($db->numrows($res)==1) {
			//$label = "1 ".$_DIMS['cste']['_BUSINESS_TIER'];
			//$firstpart.= $label;
			$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['title']=$db->numrows($res)." ".$_DIMS['cste']['_DIMS_LABEL_FOUND'];
			$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['nb'] = $db->numrows($res);
		}
		else {
			if ($db->numrows($res)>0) {
				//$label = $db->numrows($res)." ".$_DIMS['cste']['_BUSINESS_TIERS'];
				//$firstpart.= $db->numrows($res)." ".$_DIMS['cste']['_BUSINESS_TIERS'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['title']=$db->numrows($res)." ".$_DIMS['cste']['_DIMS_LABEL_FOUNDS'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['nb'] = $db->numrows($res);
			}
			else {
				//$label = $db->numrows($res)." ".$_DIMS['cste']['_BUSINESS_NO_TIER'];
				//$firstpart.= "<font class=\"fontgray\">".$_DIMS['cste']['_BUSINESS_NO_TIER']."</font>";
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['title']=$_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
				$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['nb'] = 0;
			}
		}

		$tabresult=array();
		$tabid=array();
		$lastchange=0;
		$lastuser="";

		while ($row = $db->fetchrow($res)) {
			$tabid[$row['id']]=$row['id'];
			$elem=array();
			$elem['id']=$row['id'];
			$elem['title']=$row['intitule'];
			$elem['titlelink']=$row['intitule'];
			//$elem['link']= dims_urlencode("admin.php?dims_moduleid={$row['id_module']}&dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat="._BUSINESS_CAT_TIERS."&op=tiersr_ouvrir&tiers_id={$row['id']}&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS);
			$elem['link']= dims_urlencode("admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action=401&id_ent=".$row['id']);

			$elem['content']='';
			$elem['author']=strtoupper(substr($row['firstname'],0,1)).". ".$row['lastname'];
			$elem['timestp_modify']=$row['timestp_modify'];
			$elem['annot']=0;
			$tabresult[$elem['id']]=$elem;

			if($lastchange<$row['timestp_modify']) {
				$lastchange=$row['timestp_modify'];
				$lastuser=$elem['author'];
			}
		}

		if (!isset($tabcorresp)) $tabcorresp=$tabid;

		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['tabid']=$tabid;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_TIERS]['tabresult']=$tabresult;
		// calcul de la derni�re maj
		/*
		if ($lastchange!=0) {
			$firstpart.=" <font class=\"fontgray\"> - ".dims_getLastModify($lastchange);
			if ($lastuser!="") $firstpart.=" ".$_DIMS['cste']['_DIMS_LABEL_FROM']." ".$lastuser;
			$firstpart.="</font>";
		}
		*/
	}
}


?>
