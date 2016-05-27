<?
///////////////////////////////////////////////////////////////////////////
// LOAD MODULES & GLOBAL PARAMS
///////////////////////////////////////////////////////////////////////////

$_SESSION['dims']['modules'] = Array();

// get modules
$select = '
	SELECT		m.id,
			m.label,
			m.active,
			m.public,
			m.autoconnect,
			m.shared,
			m.viewmode,
			m.transverseview,
			m.id_module_type,
			mt.label as moduletype,
			mt.contenttype as contentype

	FROM		dims_module m

	INNER JOIN	dims_module_type mt ON m.id_module_type = mt.id
	';

$res=$db->query($select);
while ($fields = $db->fetchrow($res)) {
	$_SESSION['dims']['modules'][$fields['id']] = $fields;
}

$listmodules = array_keys($_SESSION['dims']['modules']);
$parammodule = array();

// get default params
$select = '
	SELECT		pd.id_module,
			pt.name,
			pt.label,
			pd.value

	FROM		dims_param_default pd

	INNER JOIN	dims_param_type pt
	ON		pt.name = pd.name
	AND		pt.id_module_type = pd.id_module_type

	WHERE		pd.id_module IN ('.$db->getParamsFromArray($listmodules, 'module', $parammodule).')
	';

$res = $db->query($select, $parammodule);
while ($fields = $db->fetchrow($res)) {
	$_SESSION['dims']['params'][$fields['id_module']]['default'][$fields['name']] = $fields['value'];
}

$select = '
	SELECT		pg.id_module,
			pt.name,
			pt.label,
			pg.value,
			pg.id_group

	FROM		dims_param_group pg

	INNER JOIN	dims_param_type pt
	ON		pt.name = pg.name
	AND		pt.id_module_type = pg.id_module_type

	WHERE		pg.id_module IN ('.$db->getParamsFromArray($listmodules, 'module', $parammodule).')
	';

$res=$db->query($select, $parammodule);
while ($fields = $db->fetchrow($res)) {
	$_SESSION['dims']['params'][$fields['id_module']]['workspace'][$fields['id_group']][$fields['name']] = $fields['value'];
}

// get user params
if (!empty($_SESSION['dims']['userid'])) {
	$select = '
		SELECT		pu.id_module,
				pt.name,
				pt.label,
				pu.value

		FROM		dims_param_user pu

		INNER JOIN	dims_param_type pt
		ON		pt.name = pu.name
		AND		pt.id_module_type = pu.id_module_type

		WHERE		pu.id_module IN ('.$db->getParamsFromArray($listmodules, 'module', $parammodule).')
		AND		pu.id_user = :iduser
		';

	$res=$db->query($select, array_merge(
		$parammodule,
		array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
		)
	));

	while ($fields = $db->fetchrow($res)) {
		$_SESSION['dims']['params'][$fields['id_module']]['user'][$fields['name']] = $fields['value'];
	}
}

$_SESSION['dims']['paramloaded'] = true;
?>
