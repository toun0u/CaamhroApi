<?
$show_options = (	!empty($_GET['doc_search_name']) ||
			!empty($_GET['doc_search_filetype']) ||
			!empty($_GET['doc_search_user']) ||
			!empty($_GET['doc_search_workspace']) ||
			!empty($_GET['doc_search_date1']) ||
			!empty($_GET['doc_search_date2'])
				);
?>

<form action="<? echo $scriptenv; ?>" method="get">
<input type="hidden" name="op" value="search_next">
<div class="doc_folderinfo">
	<div style="float:left;height:40px;">
		<p style="margin:0;padding:4px 0px 4px 8px;">
			<img src="./common/modules/doc/img/search.png" />
		</p>
	</div>
	<div style="float:left;height:40px;">
			<p style="margin:0;padding:4px;">
			<strong>Recherche</strong>
			<br />d'un Fichier
		</p>
	</div>
	<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
			<strong>Nom / Mots Cl�s</strong>:
			<br /><input type="text" class="text" style="width:140px;" name="doc_search_keywords" value="<? if (!empty($_GET['doc_search_keywords'])) echo dims_load_securvalue('doc_search_keywords', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>">
		</p>
	</div>
	<div style="display:<? echo ($show_options) ? 'block' : 'none'; ?>;" id="doc_search_options">
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
				<strong>Type</strong>:
				<br />
				<?
				$select = "SELECT distinct(filetype) FROM dims_mod_doc_ext ORDER BY filetype";
				$res=$db->query($select);
				?>
				<select class="select" style="width:100px;" name="doc_search_filetype">
					<option value="">(tout)</option>
					<?
					while ($row = $db->fetchrow($res))
					{
						?>
						<option value="<? echo $row['filetype']; ?>" <? if (!empty($_GET['doc_search_filetype']) && $_GET['doc_search_filetype'] == $row['filetype']) echo 'selected'; ?>><? echo $row['filetype']; ?></option>
						<?
					}
					?>
				</select>
			</p>
		</div>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
				<strong>Propri�taire</strong>:
				<br /><input type="text" class="text"  style="width:90px;" value="<? if (!empty($_GET['doc_search_user'])) echo dims_load_securvalue('doc_search_user', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>" name="doc_search_user">
			</p>
		</div>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
				<strong>Espace</strong>:
				<br /><input type="text" class="text" style="width:90px;" value="<? if (!empty($_GET['doc_search_workspace'])) echo dims_load_securvalue('doc_search_workspace', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>" name="doc_search_workspace">
			</p>
		</div>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
				<strong>Date (du)</strong>:
				<br />
				<input type="text" class="text" style="width:70px;" value="<? if (!empty($_GET['doc_search_date1'])) echo dims_load_securvalue('doc_search_date1', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>" name="doc_search_date1" id="doc_search_date1">
				<a href="#" onclick="javascript:dims_calendar_open('doc_search_date1', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</p>
		</div>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
				<strong>Date (au)</strong>:
				<br />
				<input type="text" class="text" style="width:70px;" value="<? if (!empty($_GET['doc_search_date2'])) echo dims_load_securvalue('doc_search_date2', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>" name="doc_search_date2" id="doc_search_date2">
				<a href="#" onclick="javascript:dims_calendar_open('doc_search_date2', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</p>
		</div>
	</div>
	<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
			<strong>Lancer la recherche</strong>:
			<br /><input type="submit" class="flatbutton" value="Rechercher">
		</p>
	</div>
	<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px;">
			<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay('doc_search_options');"><strong>Afficher<br />plus d'options</strong></a>
		</p>
	</div>
</div>
</form>

<?
if ($op == 'search_next') {
	require_once DIMS_APP_PATH . '/include/functions/shares.php';
	$shares = dims_shares_get($_SESSION['dims']['userid']);

	$list_sharedfolder = array();
	$list_sharedfile = array();
	foreach($shares as $sh)
	{
		if ($sh['id_object'] == _DOC_OBJECT_FOLDER) $list_sharedfolder[] = $sh['id_record'];
		if ($sh['id_object'] == _DOC_OBJECT_FILE) $list_sharedfile[] = $sh['id_record'];
	}

	$docfolder_readonly_content = false;

	$params = array();
	$where = (!empty($list_sharedfile)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfile, 'sharedfile', $params).')' : '';

	$search = '';

	if (!empty($_GET['doc_search_keywords']))
	{
		$search_name = $search_desc = $search_cont = array();
		$docsearchkeywors = split(' ',trim(dims_load_securvalue('doc_search_keywords', dims_const::_DIMS_CHAR_INPUT, true, true, true)));
		$compteur_fe = 0;
		foreach($docsearchkeywors as $k)
		{
			$search_name[] = "f.name LIKE ".':like'.$compteur_fe." ";
			$search_desc[] = "f.description LIKE ".':like'.$compteur_fe." ";
			$search_cont[] = "f.content LIKE ".':like'.$compteur_fe." ";
			$params[':like'.$compteur_fe] = "%".$k."%";
			$compteur_fe++;
		}
		$search .= ' AND (('.implode(' AND ',$search_name). ') OR ('.implode(' AND ',$search_desc).')  OR ('.implode(' AND ',$search_cont).')) ';
	}

	if (!empty($_GET['doc_search_name'])) {
		$search .= " AND f.name LIKE :name ";
		$params[':name'] = "%".trim(dims_load_securvalue('doc_search_name', dims_const::_DIMS_CHAR_INPUT, true, true, true))."%";
	}
	if (!empty($_GET['doc_search_filetype'])) {
		$search .= " AND e.filetype LIKE :filetype ";
		$params[':filetype'] = "%".dims_load_securvalue('doc_search_filetype', dims_const::_DIMS_CHAR_INPUT, true, true, true)."%" ;
	}
	if (!empty($_GET['doc_search_user'])) {
		$search .= " AND u.login LIKE :login ";
		$params[':login'] = "%".trim(dims_load_securvalue('doc_search_user', dims_const::_DIMS_CHAR_INPUT, true, true, true))."%" ;
	}
	if (!empty($_GET['doc_search_workspace'])) {
		$search .= " AND g.label LIKE :label ";
		$params[':label'] = "%".trim(dims_load_securvalue('doc_search_workspace', dims_const::_DIMS_CHAR_INPUT, true, true, true))."%" ;
	}


	if (!empty($_GET['doc_search_date1']) && !empty($_GET['doc_search_date2'])) {
		$search .= " AND f.timestp_modify BETWEEN :date1 AND :date2 ";
		$params[':date1'] = dims_local2timestamp(dims_load_securvalue('doc_search_date1', dims_const::_DIMS_CHAR_INPUT, true, true, true));
		$params[':date2'] = dims_timestamp_add(dims_local2timestampdims_load_securvalue('doc_search_date2'),0,0,0,0,1);
	} else {
		if (!empty($_GET['doc_search_date1'])) {
			$search .= " AND f.timestp_modify >= :time ";
			$params[':time'] = dims_local2timestamp(dims_load_securvalue('doc_search_date1', dims_const::_DIMS_CHAR_INPUT, true, true, true));
		}
		if (!empty($_GET['doc_search_date2'])) {
			$search .= " AND f.timestp_modify < :time ";
			$params[':time'] = dims_timestamp_add(dims_local2timestamp(dims_load_securvalue('doc_search_date2', dims_const::_DIMS_CHAR_INPUT, true, true, true)),0,0,0,0,1);
		}
	}

	$sql =	"
			SELECT		f.*,
						u.login,
						w.label,
						e.filetype,
						fd.foldertype,
						fd.readonly,
						fd.readonly_content,
						fd.id_user as fd_id_user,
						fd.name as fd_name

			FROM		dims_mod_doc_file f

			LEFT JOIN	dims_mod_doc_folder fd
			ON			fd.id = f.id_folder

			LEFT JOIN	dims_user u
			ON			f.id_user = u.id

			LEFT JOIN	dims_workspace w
			ON			f.id_workspace = w.id

			LEFT JOIN	dims_mod_doc_ext e
			ON			e.ext = f.extension

			WHERE		f.id_module = :idmodule
			AND			((f.id_user = :userid AND f.id_folder = 0) OR f.id_folder!=0 {$where})
			$search
			ORDER BY	f.name
			";
	$params[':idmodule'] = $_SESSION['dims']['moduleid'];
	$params[':userid'] = $_SESSION['dims']['userid'];

	$res=$db->query($sql, $params );
	?>

	<div class="doc_explorer_main">

		<?
		if ($db->numrows())
		{
			?>
			<div class="doc_explorer_maintitle"><? echo $db->numrows(); ?> fichier(s) trouv�(s)</div>
			<?
		}
		?>

		<div style="right:90px;" class="doc_explorer_column"></div>
		<div style="right:220px;" class="doc_explorer_column"></div>
		<div style="right:350px;" class="doc_explorer_column"></div>
		<div style="right:450px;" class="doc_explorer_column"></div>
		<div style="right:570px;" class="doc_explorer_column"></div>
		<div style="right:660px;" class="doc_explorer_column"></div>

		<div style="position:relative;">
			<div class="doc_explorer_title">
				<a href="" style="width:90px;float:right;" class="doc_explorer_element"><p>Actions</p></a>
				<a href="" style="width:130px;float:right;" class="doc_explorer_element"><p>Date</p></a>
				<a href="" style="width:130px;float:right;" class="doc_explorer_element"><p>Espace</p></a>
				<a href="" style="width:100px;float:right;" class="doc_explorer_element"><p>Propri�taire</p></a>
				<a href="" style="width:120px;float:right;" class="doc_explorer_element"><p>Dossier</p></a>
				<a href="" style="width:90px;float:right;" class="doc_explorer_element"><p>Taille</p></a>

				<a href="" style="overflow:auto;" class="doc_explorer_element"><p>Nom</p></a>
			</div>

			<?
			// DISPLAY FILES
			while ($row = $db->fetchrow($res))
			{
				if ($row['id_folder'] == 0) $row['fd_name'] = 'Racine';

				$ksize = sprintf("%.02f",$row['size']/1024);
				$ldate = dims_timestamp2local($row['timestp_modify']);

				$color = (!isset($color) || $color == 2) ? 1 : 2;

				$docfolder_readonly_content = (!empty($row['id_folder'])) ? ($row['readonly_content'] && $row['fd_id_user'] != $_SESSION['dims']['userid']) : false;

				?>
				<div class="doc_explorer_line">
					<div class="doc_explorer_tools">
						<?
						if (dims_isactionallowed(_DOC_ACTION_DELETEFILE) && (!$docfolder_readonly_content || $row['id_user'] == $_SESSION['dims']['userid']))
						{
							?>
							<a title="Supprimer" style="display:block;float:right;" href="javascript:dims_confirmlink('<? echo dims_urlencode("{$scriptenv}?op=file_delete&currentfolder={$currentfolder}&docfile_id={$row['id']}"); ?>','<? echo $_DIMS['cste']['_DIMS_CONFIRM']; ?>');"><img src="./common/modules/doc/img/ico_trash.png" /></a>
							<?
						}
						else
						{
							?>
							<a title="Supprimer" style="display:block;float:right;" href="#" onclick="javascript:alert('Vous ne disposez pas des autorisations n�cessaires pour supprimer ce fichier');"><img src="./common/modules/doc/img/ico_trash_grey.png" /></a>
							<?
						}
						?>
						<a title="Modifier" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=file_modify&currentfolder={$row['id_folder']}&docfile_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_modify.png" /></a>
						<a title="T�l�charger" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=file_download&docfile_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_download.png" /></a>
						<a title="T�l�charger (ZIP)" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=file_download_zip&docfile_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_download_zip.png" /></a>
					</div>
					<a class="doc_explorer_link_<? echo $color; ?>" title="<? echo ($row['description']); ?>" href="<? echo dims_urlencode("{$scriptenv}?op=file_download&docfile_id={$row['id']}"); ?>">
						<div style="width:130px;float:right;" class="doc_explorer_element"><p><? echo $ldate['date']; ?> <? echo $ldate['time']; ?></p></div>
						<div style="width:130px;float:right;" class="doc_explorer_element"><p><? echo $row['label']; ?></p></div>
						<div style="width:100px;float:right;" class="doc_explorer_element"><p><? echo $row['login']; ?></p></div>
						<div style="width:100px;float:right;" class="doc_explorer_element"><p><? echo $row['fd_name']; ?></p></div>
						<div style="width:20px;float:right;" class="doc_explorer_element">
							<p>
								<img src="./common/modules/doc/img/ico_folder<? if ($row['foldertype'] == 'shared') echo '_shared'; ?><? if ($row['foldertype'] == 'public') echo '_public'; ?><? if ($row['readonly']) echo '_locked'; ?>.png" />
							</p>
						</div>
						<div style="width:90px;float:right;text-align:right;" class="doc_explorer_element"><p><? echo $ksize; ?> ko</p></div>

						<div style="float:left;width:20px;" class="doc_explorer_element">
							<p><?
								if (file_exists('./common/modules/doc/img/mimetypes/ico_'.$row['filetype'].'.png'))
								{
									?><img src="./common/modules/doc/img/mimetypes/ico_<? echo $row['filetype']; ?>.png" /><?
								}
								else
								{
									?><img src="./common/modules/doc/img/mimetypes/ico_default.png" /><?
								}
								?></p>
						</div>
						<div style="overflow:auto;" class="doc_explorer_element"><p><? echo $row['name']; ?></p></div>
					</a>
				</div>
				<?
			}
			?>
		</div>
	</div>
	<?
}
?>

<? require_once DIMS_APP_PATH . '/modules/doc/public_legend.php'; ?>
