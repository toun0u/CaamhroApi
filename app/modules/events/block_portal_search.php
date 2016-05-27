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

// test if available in this workspace
if ($workspace['planning']) {
	$ok=false;
	$param = array();
	switch($dims_op) {
		case 'searchfavorites':
			$sql="	select		a.*,
								u.lastname,u.firstname
					FROM		dims_favorite as f
					inner join	dims_user as u
					on			u.id=f.id_user_from
					and			id_user= :userid
					and			id_module= :moduleid";
			$param[':userid'] = $_SESSION['dims']['userid'];
			$param[':moduleid'] = $moduleid;

			if	($_SESSION['dims']['desktop_view_workspace']==1) {
				$sql.=" AND			f.id_workspace IN ($workspaces)";
			}
			else {
				$sql.=" AND			f.id_workspace = :idworkspace";
				$param[':idworkspace'] = $_SESSION['dims']['workspaceid'];
			}

			$sql.="
					and			f.type= :favorites
					and			f.id_object= :idobject
					INNER JOIN	dims_mod_business_action as a
					ON			a.id=f.id_record
					GROUP BY	f.id_module, f.id_object, f.id_record";
			$param[':favorites'] = $_SESSION['dims']['favorites'];
			$param[':idobject'] = dims_const::_SYSTEM_OBJECT_ACTION;
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
					$sql.=" ON			a.id_workspace = w.id AND a.id_workspace AND w.id = :workspaceid";
					$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];
				}

				$sql.= " AND a.id_parent=0
						AND	a.id_module= :moduleid
						AND ((a.personnel=0 AND a.type=1)
						OR (a.personnel=1 AND a.id_user= :userid1 AND a.type=1)
						OR (a.type=2 AND a.id_workspace = :idworkspace ))";
				$param[':moduleid'] = $moduleid;
				$param[':userid1'] = $_SESSION['dims']['userid'];
				$param[':idworkspace'] = $_SESSION['dims']['workspaceid'];

				if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
					$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
							and ti.id_module=1 and ti.id_record=a.id
							and ti.id_tag in (".$lsttag.")";
					$param[':idobject'] = dims_const::_SYSTEM_OBJECT_ACTION;
				}
				else {
					if ($datelastvalidate>0) $sql.=" AND a.timestp_modify> :datelastvalidate";
					$param[':datelastvalidate'] = $datelastvalidate;
				}

				if ($_SESSION['dims']['adminlevel']<dims_const::_DIMS_ID_LEVEL_GROUPADMIN){
					$sql .="
						LEFT JOIN	dims_mod_business_action_utilisateur as au
						ON			au.action_id=a.id AND au.user_id= :userid2
						AND			a.type=1
						AND			a.id_parent=0";
					$param[':userid2'] = $_SESSION['dims']['userid'];
				}

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
				/*}
				else {
						$sql.=" INNER JOIN	dims_workspace w ON a.id_workspace = w.id AND w.id = ".$_SESSION['dims']['workspaceid'];
				}*/

				$sql.= "
						AND			a.id_parent=0
						AND			a.id in (".$db->getParamsFromArray($tabcorresp, 'corresp', $param).")
						AND			a.id_module= :moduleid
						AND 		(a.personnel=0
										OR (a.personnel=1 AND a.id_user= :userid1 and a.type=1)
										OR (a.type=2 AND a.id_workspace = :workspaceid )
									)";
				$param[':moduleid'] = $moduleid;
				$param[':userid1'] = $_SESSION['dims']['userid'];
				$param[':workspaceid'] = $_SESSION['dims']['workspaceid'];

				if ($_SESSION['dims']['adminlevel']<dims_const::_DIMS_ID_LEVEL_GROUPADMIN){
					$sql .="
						LEFT JOIN	dims_mod_business_action_utilisateur as au
						ON			au.action_id=a.id AND au.user_id= :userid2
						AND			a.type=1
						AND			a.id_parent=0";
					$param[':userid2'] = $_SESSION['dims']['userid'];
				}

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
		$res=$db->query($sql, $param);

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
			/*
			if ($lastchange!=0) {
				if ($lastuser!="") {
					$firstpart.=" <font class=\"fontgray\"> - ".dims_getLastModify($lastchange);
					$firstpart.=" ".$_DIMS['cste']['_DIMS_LABEL_FROM']." ".$lastuser;
				}
				$firstpart.="</font><br>";
			}*/

			//$secondpart.= getSearchcontent(dims_const::_SYSTEM_OBJECT_ACTION,$moduleid,$tabcorresp,$tabresult,$dims_op=='searchnews',$dims_op);

		//$secondpart.="</div>";
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['tabid']=$tabid;
		$dims_searchresult[$moduleid][dims_const::_SYSTEM_OBJECT_ACTION]['tabresult']=$tabresult;
	}
}

?>
