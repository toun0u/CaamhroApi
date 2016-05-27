<?
$search = '';
$workspaceid=$_SESSION['dims']['workspaceid'];
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');
$blockuser = new block_user();
$blockuser->open($_SESSION['dims']['userid'],$moduleid,$workspaceid);
$datelastvalidate=$blockuser->fields['date_lastvalidate'];
$module=$dims->getModule($moduleid);
$moduletype=$module['label'];

$ok=false;

$param = array();

switch($dims_op) {
	case 'searchfavorites':
		$sql="	select		fi.*,
							e.filetype,
							fd.foldertype,
							fd.id_user as fd_id_user,
							fd.name as fd_name,
							u.lastname,u.firstname
				FROM		dims_favorite as f
				LEFT join	dims_user as u
				on			u.id=f.id_user_from
				and			id_user= :userid1

				INNER JOIN	dims_mod_doc_file as fi
				ON			fi.id=f.id_record
				left JOIN	dims_mod_doc_folder fd
				ON			fd.id = fi.id_folder
				AND			((fd.id_user = :userid2 AND fd.foldertype = \"private\") OR fd.foldertype <> \"private\" OR fi.id_folder=0)
				left JOIN	dims_mod_doc_ext e
				ON			e.ext = fi.extension
								WHERE		id_module= :moduleid
				and			id_workspace= :workspaceid
				and			f.type= :favorites
				and			f.id_object= :idobject
				GROUP BY	f.id_module, f.id_object, f.id_record";
		$param[':userid1'] = $_SESSION['dims']['userid'];
		$param[':userid2'] = $_SESSION['dims']['userid'];
		$param[':moduleid'] = $moduleid;
		$param[':workspaceid'] = $workspaceid;
		$param[':favorites'] = $_SESSION['dims']['favorites'];
		$param[':idobject'] = _DOC_OBJECT_FILE;

		$ok=true;
		break;
	case 'searchannot':
	case 'searchnewscontent':
	case 'searchnews':

			// recherche
			$sql =	"
					SELECT		distinct f.*,
								e.filetype,
								fd.foldertype,
								fd.id_user as fd_id_user,
								fd.name as fd_name,
								u.lastname,u.firstname
					FROM		dims_mod_doc_file f
					left JOIN	dims_mod_doc_folder fd
					ON			fd.id = f.id_folder
					AND			((fd.id_user = :userid1 AND fd.foldertype = \"private\") OR fd.foldertype <> \"private\" OR f.id_folder=0)
					INNER JOIN	dims_workspace w
					ON			f.id_workspace = w.id AND f.id_workspace AND w.id IN ( :workspaces )
					AND			f.id_module= :moduleid";
			$param[':userid1'] = $_SESSION['dims']['userid'];
			$param[':moduleid'] = $moduleid;
			$param[':workspaces'] = $workspaces;

			if ($datelastvalidate>0) $sql.=" AND f.timestp_modify>$datelastvalidate";

			$sql .= "
					LEFT JOIN	dims_user u
					ON			f.id_user = u.id
					left JOIN	dims_mod_doc_ext e
					ON			e.ext = f.extension
					";

			if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
				$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
						and ti.id_module=1 and ti.id_record=f.id
						and ti.id_tag in (".$db->getParamsFromArray($lsttag, 'tags', $param).")";
				$param[':idobject'] = _DOC_OBJECT_FILE;
			}

			$sql.="
					group by	f.id
					ORDER BY	f.timestp_modify desc
					limit 0,20";

			$ok=true;

		break;
	default:
			$tabcorresp=getSearchReponse(_DOC_OBJECT_FILE,$moduleid);

			if (sizeof($tabcorresp)>0) $ok=true;
			// recherche
			$sql =	"
					SELECT		distinct f.*,
								e.filetype,
								fd.foldertype,
								fd.id_user as fd_id_user,
								fd.name as fd_name,
								u.lastname,u.firstname
					FROM		dims_mod_doc_file f
					left JOIN	dims_mod_doc_folder fd
					ON			fd.id = f.id_folder
					AND			((fd.id_user = :userid1 AND fd.foldertype = \"private\") OR fd.foldertype <> \"private\" OR f.id_folder=0)
					LEFT JOIN	dims_user u	ON f.id_user = u.id

					INNER JOIN	dims_workspace w ON f.id_workspace = w.id AND w.id IN ( :workspaces )";
			$param[':userid1'] = $_SESSION['dims']['userid'];
			$param[':workspaces'] = $workspaces;

			if (isset($_SESSION['dims']['search']['listselectedtag']) && !empty($_SESSION['dims']['search']['listselectedtag'])) {
				$sql.= " inner join dims_tag_index as ti on ti.id_object= :idobject
						and ti.id_module=1 and ti.id_record=f.id
						and ti.id_tag in (".$db->getParamsFromArray($lsttag, 'tags', $param).")";
				$param[':idobject'] = _DOC_OBJECT_FILE;
			}

			$sql.="
					LEFT JOIN	dims_mod_doc_ext e ON e.ext = f.extension
					WHERE		f.id in (".$db->getParamsFromArray($tabcorresp, 'corresp', $param).")
					ORDER BY	f.name
					";
		break;
}

$tabresdoc=array();

$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['id_object'] =_DOC_OBJECT_FILE;
$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['label'] =$_DIMS['cste']['_DIMS_LABEL_FILE'];
$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['tabid']=array();
$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['tabresult']=array();

if ($ok) {
	$res=$db->query($sql, $param);

	// test si fichier existe ou non
	while ($row=$db->fetchrow($res)) {
		$doc= new docfile();
			$doc->fields['id']=$row['id'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['id_module']=$row['id_module'];
			$doc->fields['version']=$row['version'];
			$doc->fields['extension']=$row['extension'];

			if (file_exists($doc->getfilepath())) {
				$tabresdoc[]=$row;
			}

	}

	if (sizeof($tabresdoc)==0) {
		$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['title'] = "<font class=\"fontgray\">".$_DIMS['cste']['_DIMS_LABEL_NOFOUND']."</font>";
		$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['nb'] = 0;
	}
	else {
		if (sizeof($tabresdoc)>1) {
			$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['title'] = sizeof($tabresdoc)." ".$_DIMS['cste']['_DIMS_LABEL_FOUNDS'];
			$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['nb'] = sizeof($tabresdoc);
		}
		else {
			$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['title'] = "1 ".$_DIMS['cste']['_DIMS_LABEL_FOUND'];
			$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['nb'] = 1;
		}
	}

	$tabresult=array();
	$tabid=array();
	$lastchange=0;
	$lastuser="";

	//if($page>1) $tabresdoc=array_slice($tabresdoc,($page-1)*$nb_elem_page);
	$cpte=0;

	foreach($tabresdoc as $row) {
		// test si non d�passement
	//	if ($cpte<$nb_elem_page) {
			if ($row['id_folder'] == 0) $row['fd_name'] = 'Racine';
			$tabid[$row['id']]=$row['id'];
			$elem=array();
			$elem['id']=$row['id'];
			$elem['title']=$row['name'];
			$elem['titlelink']=$row['name'];
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$row['id_module']}&dims_desktop=block&dims_action=public&op=file_download&docfile_id={$row['id']}");
			//if ($dims_op!="searchnews" && $dims_op!="searchnewscontent")
				$elem['content']=dims_strcut($row['content'],175);
			//else
			//	$elem['content']="";

			$elem['author']=strtoupper(substr($row['firstname'],0,1)).". ".$row['lastname'];
			$elem['timestp_modify']=$row['timestp_modify'];
			$elem['annot']=0;
			$tabresult[$elem['id']]=$elem;

			if($lastchange<$row['timestp_modify']) {
				$lastchange=$row['timestp_modify'];
				$lastuser=$elem['author'];
			}
			$cpte++;
	//	}
	}

	$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['tabid']=$tabid;
	$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['tabresult']=$tabresult;

	// calcul de la derni�re maj
	/*if ($lastchange!=0 && $dims_op=='searchnews') {
		$firstpart.=" <font class=\"fontgray\"> - ".dims_getLastModify($lastchange);
		if ($lastuser!="") $firstpart.=" ".$_DIMS['cste']['_DIMS_LABEL_FROM']." ".$lastuser;
		$firstpart.="</font>";
	}*/

	//echo getSearchcontent(_DOC_OBJECT_FILE,$moduleid,$tabcorresp,$tabresult,$dims_op=='searchnews',$dims_op);
}
else {
	$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['title'] = $_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
	$dims_searchresult[$moduleid][_DOC_OBJECT_FILE]['nb'] = 0;
}
?>
