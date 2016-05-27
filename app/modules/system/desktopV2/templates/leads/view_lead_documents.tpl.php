<h3>Liste des documents attachés à l'opportunité</h3>

<?php
if (!empty($linkedObjectsIds['distribution']['docs'])) {
	$params = array();
	$rs = $db->query('
		SELECT	*
		FROM	dims_mod_doc_file
		WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['docs']), 'idglobalobject', $params).')');
	if ($db->numrows($rs, $params)) {
		echo '
			<div class="w500p">
			<table class="tabGen">
				<thead>
					<th>Nom</th>
					<th class="w50p">Actions</th>
				</thead>
				<tbody>';
		$i = 0;
		while ($row = $db->fetchrow($rs)) {
			$doc = new docfile();
			$doc->openFromResultSet($row);

			echo '
				<tr class="ligne'.($i % 2 == 0).'">
					<td>'.$doc->fields['name'].'</td>
					<td class="txtcenter">
						<a href="javascript:void(0)" onclick="javascript:preview_docfile(\''.$doc->fields['md5id'].'\');" title="Prévisualiser ce document"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/previsu.png" alt="Prévisualiser ce document" /></a>
						<a href="'.dims_urlencode($dims->getScriptEnv().'?dims_op=doc_file_download&docfile_md5id='.$doc->fields['md5id']).'" title="Télécharger ce document"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/download.png" alt="Télécharger ce document" /></a>
					</td>
				</tr>';
			$i++;
		}
		echo '</tbody></table></div>';
	}
}
else {
	echo 'Il \'y a aucun document rattaché à cette opportunité';
}
