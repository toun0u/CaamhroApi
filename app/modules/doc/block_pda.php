<?
dims_init_module('doc');
$search = '';

/*
			$shares = dims_shares_get($_SESSION['dims']['userid']);

			$list_sharedfolder = array();
			$list_sharedfile = array();
			foreach($shares as $sh)
			{
				if ($sh['id_object'] == _DOC_OBJECT_FOLDER) $list_sharedfolder[] = $sh['id_record'];
				if ($sh['id_object'] == _DOC_OBJECT_FILE) $list_sharedfile[] = $sh['id_record'];
			}

			$docfolder_readonly_content = false;


			$where = (!empty($list_sharedfile)) ? ' OR f.id IN ('.implode(',', $list_sharedfile).')' : '';
			*/

			/*
			if (!empty($_GET['doc_search_keywords']))
			{
				$search_name = $search_desc = $search_cont = array();
				foreach(split(' ',trim($_GET['doc_search_keywords'])) as $k)
				{
					$search_name[] = "f.name LIKE '%".addslashes($k)."%'";
					$search_desc[] = "f.description LIKE '%".addslashes($k)."%'";
					$search_cont[] = "f.content LIKE '%".addslashes($k)."%'";
				}
				$search .= ' AND (('.implode(' AND ',$search_name). ') OR ('.implode(' AND ',$search_desc).')  OR ('.implode(' AND ',$search_cont).')) ';
			}
			*/

$params = array();
switch($dims_op)
{
	case 'searchnews':
			// recherche
			$sql =	"
					SELECT		distinct f.*,
								e.filetype,
								fd.foldertype,
								fd.id_user as fd_id_user,
								fd.name as fd_name

					FROM		dims_mod_doc_file f
					left JOIN	dims_mod_doc_folder fd ON fd.id = f.id_folder
					INNER JOIN	dims_user u
					ON			f.id_user = u.id
					INNER JOIN	dims_workspace w
					ON			f.id_workspace = w.id AND f.id_workspace= :workspaceid
					AND			f.id_module= :moduleid
					INNER JOIN	dims_mod_doc_ext e
					ON			e.ext = f.extension
					WHERE		((f.id_user = :userid AND f.id_folder = 0) OR f.id_folder!=0)
					AND			left(f.timestp_modify,10)>='".date("Ymd")."'
					ORDER BY	f.timestp_modify desc
					limit 0,40;
					";
			$params = array(
				':userid' 		=> $_SESSION['dims']['userid'],
				':workspaceid'	=> $_SESSION['dims']['workspaceid'],
				':moduleid'		=> $_SESSION['dims']['moduleid']
			);
		break;
	default:
			// recherche
			$sql =	"
					SELECT		distinct f.*,
								e.filetype,
								fd.foldertype,
								fd.id_user as fd_id_user,
								fd.name as fd_name

					FROM		dims_mod_doc_file f
					INNER JOIN	dims_keywords_index on id_object= :idobject and f.id_module= :idmodule
					AND			id_record=f.id and id_keyword in (".$_SESSION['dims']['search']['listword'].")
					left JOIN	dims_mod_doc_folder fd
					ON			fd.id = f.id_folder
					INNER JOIN	dims_user u
					ON			f.id_user = u.id
					INNER JOIN	dims_workspace w
					ON			f.id_workspace = w.id

					INNER JOIN	dims_mod_doc_ext e
					ON			e.ext = f.extension
					WHERE		((f.id_user = :userid AND f.id_folder = 0) OR f.id_folder!=0 )

					ORDER BY	f.name
					limit 0,40;
					";
			$params = array(
				':idobject' 	=> _DOC_OBJECT_FILE,
				':idmodule' 	=> dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true),
				':userid' 		=> $_SESSION['dims']['userid']
			);
		break;
}

$db->query($sql, $params);

if ($db->numrows())
{
	echo "<font style=\"font-size:8px\">".$db->numrows()."&nbsp;".$_DIMS['cste']['_DOC_LABEL_FILESFOUND']."</font>";
}

?>
<div class="search_explorer_main">

	<div style="position:relative;font-size:8px">
		<p style="width:40px;float:right;">Actions</p>
		<p style="width:70px;float:right;">Dossier</p>
		<p style="overflow:auto;float:left;">Nom</p>
	</div>

	<?
	while ($row = $db->fetchrow($res))
	{
		if ($row['id_folder'] == 0) $row['fd_name'] = 'Racine';

		$color = (!isset($color) || $color == "ffffff") ? "f0f0f0" : "ffffff";

		//$docfolder_readonly_content = (!empty($row['id_folder'])) ? ($row['readonly_content'] && $row['fd_id_user'] != $_SESSION['dims']['userid']) : false;

		?>
		<div style="clear:both;height:12px;width:100%;background-color:<? echo $color; ?>;font-size:8px">
			<p style="width:40px;float:right;"><a title="" class="slink" href="<? echo dims_urlencode("admin.php?op=file_modify&currentfolder={$row['id_folder']}&docfile_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_modify.png" alt=""/></a></p>
			<p style="width:70px;float:right;"><? echo dims_strcut($row['fd_name'],12); ?></p>
			<p style=overflow:auto;float:left;"><a href="<? echo dims_urlencode("admin.php?dims_moduleid={$row['id_module']}&dims_desktop=block&dims_action=public&op=file_download&docfile_id={$row['id']}"); ?>"><? echo dims_strcut($row['name'],22); ?></a></p>
		</div>
		<?
	}
	?>
</div>


