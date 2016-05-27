<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(DIMS_APP_PATH . '/include/class_documentsfolder.php');
require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');

if (isset($_POST['currentfolder']))
	$currentfolder = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
elseif (isset($_GET['currentfolder']))
	$currentfolder = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);

if (empty($currentfolder)) { // on va chercher la racine
	$res = $db->query('
		SELECT id
		FROM dims_documents_folder
		WHERE id_folder = 0
		AND :idobject
		AND :idrecord',
		array (
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['documents']['id_object']),
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['documents']['id_record']),
		));

	if ($row = $db->fetchrow($res))
		$currentfolder = $row['id'];
	else { // racine inexistante, il faut la créer
		$documentsfolder = new documentsfolder();
		$documentsfolder->fields['name'] = 'Racine';
		$documentsfolder->fields['id_folder'] = 0;
		$documentsfolder->fields['id_object'] = $_SESSION['documents']['id_object'];
		$documentsfolder->fields['id_record'] = $_SESSION['documents']['id_record'];
		$documentsfolder->fields['id_module'] = $_SESSION['documents']['id_module'];
		$documentsfolder->fields['id_user'] = $_SESSION['documents']['id_user'];
		$documentsfolder->fields['id_workspace'] = $_SESSION['documents']['id_workspace'];
		$currentfolder = $documentsfolder->save();
	}
}
?>

<div class="documents_browser">

<div class="documents_path">
<?
// voir pour une optimisation de cette partie car on ouvre un docfolder sans doute pour rien
$documentsfolder = new documentsfolder();

if (!empty($currentfolder))
	$documentsfolder->open($currentfolder);
?>

<a title="Rechercher un Fichier" href="javascript:void(0);" style="float:right;"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/documents/ico_search.png"></a>
<a title="Crer un nouveau fichier" href="javascript:void(0);" style="float:right;" onclick="javascript:dims_documents_openfile('<? echo $currentfolder; ?>','',event);"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/documents/ico_newfile.png"></a>
<a title="Crer un nouveau Dossier" href="javascript:void(0);" style="float:right;" onclick="javascript:dims_documents_openfolder('<? echo $currentfolder; ?>','',event);"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/documents/ico_newfolder.png"></a>
<a title="Aller au Dossier Racine" href="javascript:void(0);" style="float:right;" onclick="javascript:dims_documents_browser('','<? echo $_SESSION['documents']['documents_id']; ?>');"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/documents/ico_home.png"></a>

<div>Emplacement :</div>
<?
if ($currentfolder != 0) {
	$documentsfolder = new documentsfolder();
	$documentsfolder->open($currentfolder);

	$res = $db->query("SELECT id, name FROM dims_documents_folder WHERE id in (".implode(',', array_fill(0, count($documentsfolder->fields['parents'])+1, '?')).")" ,array_merge(
		$documentsfolder->fields['parents'],
		array($currentfolder)
	));

	while ($row = $db->fetchrow($res)) {
?>
			<a <? if ($currentfolder == $row['id'])
			echo 'class="doc_pathselected"'; ?> href="javascript:void(0);" onclick="javascript:dims_documents_browser('<? echo $row['id']; ?>','<? echo $_SESSION['documents']['documents_id']; ?>');">
				<p class="dims_va">
					<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/documents/ico_folder.png" />
					<span><? echo $row['name']; ?></span>
				</p>
			</a>
<?
	}
}
?>
</div>
<?
$documents_columns['left'][0] = array('label' => '&nbsp;', 'width' => '23');
$documents_columns['auto'][1] = array('label' => 'Nom');
$documents_columns['right'][4] = array('label' => 'Date', 'width' => '130');
$documents_columns['right'][3] = array('label' => 'Type', 'width' => '55');
$documents_columns['right'][2] = array('label' => 'Taille', 'width' => '90');
$documents_columns['actions_right'][5] = array('label' => 'Actions', 'width' => '85');

$documents_values = array();


$sql = "
	SELECT		f.*,
				u.login
	FROM		dims_documents_folder f
	LEFT JOIN	dims_user u
	ON			f.id_user = u.id
	WHERE		f.id_folder = :folderid
	ORDER BY	f.name
	";

$res = $db->query($sql, array(
	':folderid' => array('type' => PDO::PARAM_INT, 'value' => $currentfolder),
));

$i = 0;
while ($row = $db->fetchrow($res)) {
	$ldate = dims_timestamp2local($row['timestp_modify']);

	$documents_values[$i]['values'][0] = array('label' => "<img style=\"display:block;\" src=\"{$_SESSION['dims']['template_path']}/img/documents/ico_folder.png\" />", 'style' => '');
	$documents_values[$i]['values'][1] = array('label' => $row['name'], 'style' => '');
	$documents_values[$i]['values'][2] = array('label' => "{$row['nbelements']} element(s)", 'style' => '');
	$documents_values[$i]['values'][3] = array('label' => 'Dossier', 'style' => '');
	$documents_values[$i]['values'][4] = array('label' => "{$ldate['date']} {$ldate['time']}", 'style' => '');
	$documents_values[$i]['values'][5] = array('label' => '	<a title="Supprimer" style="display:block;float:right;" href="javascript:void(0);" onclick="javascript:if (confirm(\'Attention, cette action va supprimer dfinitivement le dossier et son contenu\')) dims_documents_deletefolder(\'' . $currentfolder . '\',\'' . $_SESSION['documents']['documents_id'] . '\',\'' . $row['id'] . '\');"><img src="' . $_SESSION['dims']['template_path'] . './common/img/documents/ico_trash.png" /></a>
														<a title="Modifier" style="display:block;float:right;" href="javascript:void(0);" onclick="javascript:dims_documents_openfolder(\'' . $currentfolder . '\',\'' . $row['id'] . '\',event);"><img src="' . $_SESSION['dims']['template_path'] . './common/img/documents/ico_modify.png" /></a>', 'style' => ''
	);
	$documents_values[$i]['description'] = '';
	$documents_values[$i]['link'] = 'javascript:void(0);';
	$documents_values[$i]['option'] = 'onclick="javascript:dims_documents_browser(\'' . $row['id'] . '\',\'' . $_SESSION['documents']['documents_id'] . '\');"';
	$documents_values[$i]['style'] = '';

	$i++;
}

// DISPLAY FILES

$sql = "
	SELECT		f.*,
				u.login,
				e.filetype

	FROM		dims_documents_file f

	LEFT JOIN	dims_user u
	ON			f.id_user = u.id

	LEFT JOIN	dims_documents_ext e
	ON			e.ext = f.extension

	WHERE		f.id_folder = :folderid

	ORDER BY	f.name
	";

$res = $db->query($sql, array(
	':folderid' => array('type' => PDO::PARAM_INT, 'value' => $currentfolder),
));

while ($row = $db->fetchrow($res)) {
	$ksize = sprintf("%.02f", $row['size'] / 1024);
	$ldate = dims_timestamp2local($row['timestp_modify']);
	$ico = (file_exists("{$_SESSION['dims']['template_path']}/img/documents/mimetypes/ico_{$row['filetype']}.png")) ? "ico_{$row['filetype']}.png" : 'ico_default.png';

	$documents_values[$i]['values'][0] = array('label' => "<img src=\"{$_SESSION['dims']['template_path']}/img/documents/mimetypes/{$ico}\" />", 'style' => '');
	$documents_values[$i]['values'][1] = array('label' => $row['name'], 'style' => '');
	$documents_values[$i]['values'][2] = array('label' => "{$ksize} ko", 'style' => '');
	$documents_values[$i]['values'][3] = array('label' => 'Fichier', 'style' => '');
	$documents_values[$i]['values'][4] = array('label' => "{$ldate['date']} {$ldate['time']}", 'style' => '');
	$documents_values[$i]['values'][5] = array('label' => '	<a title="Supprimer" style="display:block;float:right;" href="javascript:if (confirm(\'Attention, cette action va supprimer dfinitivement le fichier\')) dims_documents_deletefile(\'' . $currentfolder . '\',\'' . $_SESSION['documents']['documents_id'] . '\',\'' . $row['id'] . '\');"><img src="' . $_SESSION['dims']['template_path'] . './common/img/documents/ico_trash.png" /></a>
														', 'style' => '');
	$documents_values[$i]['description'] = '';
	$documents_values[$i]['link'] = dims_urlencode("admin-light.php?dims_op=documents_downloadfile&documentsfile_id={$row['id']}");
	$documents_values[$i]['style'] = '';

	$i++;
}

$skin->display_array($documents_columns, $documents_values);
?>
</div>
<?
die();
?>
