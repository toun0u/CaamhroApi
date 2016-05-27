<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_INSTALLEDMODULES'],'100%');

$tabmoduletype_installed = array();
$tabmoduletype_install = array();

// get all modules in install folder
if ($dir = @opendir(DIMS_APP_PATH . "/install/")) {
	require_once DIMS_APP_PATH . '/include/class_dims_xml2array.php';

	while($file = readdir($dir)) {
		if (is_dir(DIMS_APP_PATH . "/install/$file") && !preg_match( "/([.]{1,2})/", $file)) {// read folders in install
			$descfile = DIMS_APP_PATH . "/install/$file/description.xml";
			if (file_exists($descfile)) {
				$xml2array = new dims_xml2array();
				$xmlarray = $xml2array->parseFile($descfile);
				$moduleinfo = &$xmlarray['root']['dims'][0]['moduletype'][0];
				if (!isset($moduleinfo['contenttype'][0])) $moduleinfo['contenttype'][0]='';

				$tabmoduletype_install[$moduleinfo['label'][0]] = array(
					'label' => $moduleinfo['label'][0],
					'version' => $moduleinfo['version'][0],
					'date' => $moduleinfo['date'][0],
					'author' => $moduleinfo['author'][0],
					'description' => $moduleinfo['description'][0],
					'contenttype' => $moduleinfo['contenttype'][0]
				);
			}
		}
	}
	closedir($dir);
}
ksort($tabmoduletype_install);

$columns = array();
$values = array();
$c = 0;

$columns['auto'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']);
$columns['right'][9] = array('label' => '&nbsp;', 'width' => '80');
$columns['right'][8] = array('label' => $_DIMS['cste']['_DIMS_LABEL_WCEOBJECTS'], 'width' => '100');
$columns['right'][7] = array('label' => $_DIMS['cste']['_DIMS_LABEL_METABASE'], 'width' => '120');
$columns['right'][6] = array('label' => $_DIMS['cste']['_DIMS_ACTIONS'], 'width' => '60');
$columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_DATE'], 'width' => '80');
$columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_LABEL_VERSION'], 'width' => '70');
$columns['right'][3] = array('label' => $_DIMS['cste']['_AUTHOR'], 'width' => '130');
$columns['left'][2] = array('label' => $_DIMS['cste']['_TYPE'], 'width' => '80');

// get all modules installed in a table
$select =	"
			SELECT	*
			FROM	dims_module_type
			WHERE	system != 1
			ORDER	BY label
			";

$result = $db->query($select);

while ($fields = $db->fetchrow($result))
{
	$select = "SELECT * FROM dims_mb_action WHERE id_module_type = {$fields['id']}";
	$res=$db->query($select);
	if ($db->numrows($res)) $has_actions = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
	else $has_actions = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$select = "SELECT m.id FROM dims_mb_table as m inner join dims_mb_classes as c on c.id_table=m.id inner join dims_mb_object as o on o.id_class=c.id and o.id_module_type = {$fields['id']}";
	$res=$db->query($select);
	if ($db->numrows($res)) $has_mb = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
	else $has_mb = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$select = "SELECT * FROM dims_mb_wce_object WHERE id_module_type = {$fields['id']}";
	$res=$db->query($select);
	if ($db->numrows()) $has_cmsop = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
	else $has_cmsop = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$ldate = dims_timestamp2local($fields['date']);

	$values[$c]['values'][1] = array('label' => $fields['description'], 'style' => '');
	$values[$c]['values'][2] = array('label' => $fields['label'], 'style' => '');
	$values[$c]['values'][3] = array('label' => $fields['author'], 'style' => '');
	$values[$c]['values'][4] = array('label' => $fields['version'], 'style' => '');
	$values[$c]['values'][5] = array('label' => $ldate['date'], 'style' => '');
	$values[$c]['values'][6] = array('label' => $has_actions, 'style' => 'text-align:center');
	$values[$c]['values'][7] = array('label' => "$has_mb <a href=\"javascript:dims_confirmlink('$scriptenv?op=updatemb&moduletype=$fields[label]&idmoduletype=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMBUPDATE']."')\">".$_DIMS['cste']['_DIMS_UPDATE']."</a>", 'style' => 'text-align:center');
	$values[$c]['values'][8] = array('label' => $has_cmsop, 'style' => 'text-align:center');
	$values[$c]['values'][9] = array('label' => "<a href=\"javascript:dims_confirmlink('$scriptenv?op=uninstall&uninstallidmoduletype=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMODULEUNINSTAL']."')\">".$_DIMS['cste']['_DIMS_LABEL_UNINSTALL']."</a>", 'style' => '');

	$values[$c]['description'] = $fields['description'];
	$values[$c]['link'] = '';
	$values[$c]['style'] = '';
	$c++;

	$tabmoduletype_installed[$fields['label']]['version'] = $fields['version'];
	$tabmoduletype_installed[$fields['label']]['id'] = $fields['id'];
}

$skin->display_array($columns, $values);

echo $skin->close_simplebloc();

$columns = array();
$values = array();
$c = 0;

$columns['auto'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']);
$columns['right'][6] = array('label' => '&nbsp;', 'width' => '80');
$columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_DATE'], 'width' => '80');
$columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_LABEL_VERSION'], 'width' => '70');
$columns['right'][3] = array('label' => $_DIMS['cste']['_AUTHOR'], 'width' => '130');
$columns['left'][2] = array('label' => $_DIMS['cste']['_TYPE'], 'width' => '80');

foreach($tabmoduletype_install as $label => $fields)
{
	if (isset($tabmoduletype_installed[$label])) // new module version if already defined in installed module and greater version
	{
		if ($tabmoduletype_install[$label]['version'] > $tabmoduletype_installed[$label]['version'])
		{
			$ldate = dims_timestamp2local($fields['date']);

			$values[$c]['values'][1] = array('label' => $fields['description'], 'style' => '');
			$values[$c]['values'][2] = array('label' => $fields['label'], 'style' => '');
			$values[$c]['values'][3] = array('label' => $fields['author'], 'style' => '');
			$values[$c]['values'][4] = array('label' => $fields['version'], 'style' => '');
			$values[$c]['values'][5] = array('label' => $ldate['date'], 'style' => '');
			$values[$c]['values'][6] = array('label' => "<a href=\"{$scriptenv}?op=update&idmoduletype={$tabmoduletype_installed[$label]['id']}&installmoduletype={$tabmoduletype_install[$label]['label']}&updatefrom={$tabmoduletype_installed[$label]['version']}&updateto={$fields['version']}\">".$_DIMS['cste']['_DIMS_LABEL_UPDATE']."</a>", 'style' => '');
			$c++;
		}
	}
}


echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWMODULEVERSIONS'],'100%');
$skin->display_array($columns, $values);
echo $skin->close_simplebloc();

$columns = array();
$values = array();
$c = 0;

$columns['auto'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']);
$columns['right'][6] = array('label' => '&nbsp;', 'width' => '80');
$columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_DATE'], 'width' => '80');
$columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_LABEL_VERSION'], 'width' => '70');
$columns['right'][3] = array('label' => $_DIMS['cste']['_AUTHOR'], 'width' => '130');
$columns['left'][2] = array('label' => $_DIMS['cste']['_TYPE'], 'width' => '80');


foreach($tabmoduletype_install as $label => $fields)
{
	if (!isset($tabmoduletype_installed[$label])) // module is new if not defined in installed module
	{
		$ldate = dims_timestamp2local($fields['date']);

		$values[$c]['values'][1] = array('label' => $fields['description'], 'style' => '');
		$values[$c]['values'][2] = array('label' => $fields['label'], 'style' => '');
		$values[$c]['values'][3] = array('label' => $fields['author'], 'style' => '');
		$values[$c]['values'][4] = array('label' => $fields['version'], 'style' => '');
		$values[$c]['values'][5] = array('label' => $ldate['date'], 'style' => '');
		$values[$c]['values'][6] = array('label' => "<a href=\"$scriptenv?op=install&installmoduletype=$fields[label]\">".$_DIMS['cste']['_DIMS_LABEL_INSTALL']."</a>", 'style' => '');
		$c++;
	}
}

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_UNINSTALLEDMODULES'],'100%');
$skin->display_array($columns, $values);
echo $skin->close_simplebloc();
?>
