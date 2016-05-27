<?
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
$docfolder_readonly_content = false;
$moduleid=$obj['module_id'];

if (!empty($folder_id)) {
	$docfolder= new docfolder();
	$docfolder->open($folder_id);

	$docfolder_readonly_content = ($docfolder->fields['readonly_content'] && $docfolder->fields['id_user'] != $_SESSION['dims']['userid']);
}

if (!empty($currentfolder)) {
	?>
	<div class="doc_folderannotations">
	<?
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
		dims_annotation(_DOC_OBJECT_FOLDER, $docfolder->fields['id'], $docfolder->fields['name']);
		?>
	</div>
	<?
}
?>
<style>
.table-hover > tbody > tr:hover > td{
	cursor:pointer;
}
</style>
<div class="container table-responsive">
	<table class="table table-bordered table-hover">
		<thead>
			<tr class="active">
				<th class="w5"></th>
				<th>
					<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
				</th>
				<th style="width:100px;">
					<? echo $_SESSION['cste']['_SIZE']; ?>
				</th>
				<th style="width:100px;">
					<? echo $_SESSION['cste']['_TYPE']; ?>
				</th>
				<th style="width:150px;">
					<? echo $_SESSION['cste']['_DIMS_DATE']; ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?
			// DISPLAY FILES<
			require_once DIMS_APP_PATH.'include/functions/shares.php';
			$shares = dims_shares_get($_SESSION['dims']['userid']);

			$list_sharedfolder = array();
			$list_sharedfile = array();
			foreach($shares as $sh)
			{
				if ($sh['id_object'] == _DOC_OBJECT_FOLDER) $list_sharedfolder[] = $sh['id_record'];
				if ($sh['id_object'] == _DOC_OBJECT_FILE) $list_sharedfile[$sh['id_record']] = $sh['id_record'];
			}
			$param = array();
			$where = (!empty($list_sharedfile)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfile, 'sharedfile', $param).')' : '';
			$sql =	"
					SELECT		f.*,
								w.label,
								e.filetype

					FROM		dims_mod_doc_file f

					LEFT JOIN	dims_workspace w
					ON			f.id_workspace = w.id

					LEFT JOIN	dims_mod_doc_ext e
					ON			e.ext = f.extension

					WHERE		f.id_folder = :folderid
					AND			f.id_module = :moduleid";
			$param[':folderid'] = $folder_id;
			$param[':moduleid'] = $moduleid;
			if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0){
				$sql .= "	AND		((f.id_user = :userid AND f.id_folder = 0) OR f.id_folder!=0 {$where})";
				$param[':userid'] = $_SESSION['dims']['userid'];
			} else {
				$sql .= "	AND		f.id_folder!=0 {$where}";
			}
			$sql .=		"	ORDER BY	f.name";

			$res=$db->query($sql, $param);

			while ($row = $db->fetchrow($res)) {
				// test si fichier existe
				$doc= new docfile();
				$doc->openFromResultSet($row);

				if (file_exists($doc->getfilepath())) {
					$ksize = sprintf("%.02f",$row['size']/1024);
					$ldate = dims_timestamp2local($row['timestp_modify']);

					$color = (!isset($color) || $color == 2) ? 1 : 2;
					$min = $doc->getThumbnail(16);
					if(empty($min)){
						$min = $doc->getFileIcon(16);
					}
					?>
					<tr onclick="javascript:document.location.href='<? echo dims_urlencode(dims::getInstance()->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id={$row['md5id']}"); ?>';">
						<td class="txtcenter">
							<img src="<?= $min; ?>" />
						</td>
						<td>
							<? echo $row['name']; ?>
						</td>
						<td class="txtcenter">
							<? echo $ksize; ?> ko
						</td>
						<td class="txtcenter">
							<? echo $_SESSION['cste']['_DIMS_LABEL_FILE']; ?>
						</td>
						<td class="txtcenter">
							<? echo $ldate['date']; ?> <? echo $ldate['time']; ?>
						</td>
					</tr>
					<?
				}
			}
			?>
		</tbody>
	</table>
</div>
