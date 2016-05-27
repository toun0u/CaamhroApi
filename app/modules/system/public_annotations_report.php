<?
if (!isset($action)) $action = '';

switch ($action) {
	case 'result':
		echo $skin->create_pagetitle('Annotations - Rapport d\'activit&eacute; - S&eacute;l&eacute;ction des annotations','100%');
		echo $skin->open_simplebloc('','100%');

			echo "<form name=\"f_result\" action=\"".$scriptenv."\" method=\"Post\">";
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",		"report");
			$token->field("action",	"export");
			$token->field("selanno");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
			?>
			<input type="hidden" name="op" value="report" />
			<input type="hidden" name="action" value="export" />

			<div id="system_annotations_tags">
				<div id="system_annotations_titlebar">
					<b>Mes tags : </b><a href="<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS); ?>">voir tous les tags</a>
				</div>
				<?
				$select = "
					SELECT	t.*,
							COUNT(*) AS c
					FROM	dims_tag t

					INNER JOIN	dims_annotation_tag at
					ON			t.id = at.id_tag

					INNER JOIN	dims_annotation a
					ON			a.id = at.id_annotation
					AND			a.id_workspace = :workspaceid

					GROUP BY t.tag
					ORDER BY t.tag
					";

				$rs = $db->query($select, array(
					':workspaceid'	=> $_SESSION['dims']['workspaceid']
				));
				$tags = array();
				$max_c = 0;
				while ($row = $db->fetchrow($rs))
				{
					if (!empty($row['c']) && $row['c'] > $max_c) $max_c = $row['c'];
					$tags[$row['id']] = $row;
				}

				$maxsize = 10;
				$minsize = 10;
				foreach($tags as $idtag => $tag)
				{
					$size = $minsize + $maxsize * $tag['c'] / $max_c;
					$color = (isset($_POST['seltag']) && in_array($tag['tag'], $_POST['seltag'])) ? $color = "background-color:{$skin->values['bgline1']};" : '';
					?>
					<a title="utilis&eacute; <? echo $tag['c']; ?> fois" class="system_annotations_tag" style="font-size: <? echo $size; ?>px;<? echo $color; ?>;" href="<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS."&idtag={$idtag}"); ?>"><? echo $tag['tag']; ?><span style="vertical-align: 4px; font-size: 7px"> <? echo $tag['c']; ?></span></a>
					<?
				}
				?>
			</div>

			<div id="system_annotations_list">
				<?
				$timestp_start	= (empty($_POST['timestp_start'])) ? '00000000000000' : dims_local2timestamp(dims_load_securvalue('timestp_start', dims_const::_DIMS_CHAR_INPUT, true, true, true));
				$timestp_end	= (empty($_POST['timestp_end'])) ? '99999999999999' : dims_local2timestamp(dims_load_securvalue('timestp_end', dims_const::_DIMS_CHAR_INPUT, true, true, true),'23:59:59');
				$sel_users = dims_load_securvalue('sel_users', dims_const::_DIMS_NUM_INPUT, true, true, true);

				// Enregistrement de la saisie en session pour export
				$_SESSION['dims']['annotation_report']['sel_users']	= $sel_users;
				$_SESSION['dims']['annotation_report']['date_start']	= dims_load_securvalue('timestp_start', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$_SESSION['dims']['annotation_report']['date_end']	= dims_load_securvalue('timestp_end', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$_SESSION['dims']['annotation_report']['timestp_start']	= $timestp_start;
				$_SESSION['dims']['annotation_report']['timestp_end']	= $timestp_end;
				if (isset($_POST['seltag'])) {
					$_SESSION['dims']['annotation_report']['seltag'] = dims_load_securvalue('seltag', dims_const::_DIMS_NUM_INPUT, true, true, true);
				}

				$params = array();
				$select = "
					SELECT		a.*,
							t.id as tagid,
							t.tag,
							m.label as module_name,
							o.label as object_name,
							o.script,
							u.firstname,
							u.lastname

					FROM		dims_annotation a

					LEFT JOIN	dims_annotation_tag at
					ON		at.id_annotation = a.id

					LEFT JOIN	dims_tag t
					ON		t.id = at.id_tag
					";
				if (!empty($_SESSION['dims']['annotation_report']['seltag'])) {
					$select .= "
						INNER JOIN	dims_annotation_tag at2
						ON		at2.id_annotation = a.id

						INNER JOIN	dims_tag t2
						ON		t2.id = at2.id_tag
						AND		t2.tag IN (".$db->getParamsFromArray($_SESSION['dims']['annotation_report']['seltag'], 'tags', $params).")";
				}
				$select .= "
					LEFT JOIN	dims_module m
					ON		a.id_module = m.id

					LEFT JOIN	dims_mb_object o
					ON		a.id_object = o.id
					AND		m.id_module_type = o.id_module_type

					INNER JOIN	dims_user u
					ON		u.id = a.id_user
					AND		u.id IN (".$db->getParamsFromArray($sel_users, 'iduser', $params).")

					WHERE		a.id_workspace = :idworkspace
					AND		a.date_annotation BETWEEN :timestampstart AND :timestampend

					ORDER BY	a.date_annotation DESC
					";
				$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
				$params[':timestampstart'] = array('type' => PDO::PARAM_INT, 'value' => $timestp_start);
				$params[':timestampend'] = array('type' => PDO::PARAM_INT, 'value' => $timestp_end);

				$rs = $db->query($select, $params);
				$annotations = array();
				while ($row = $db->fetchrow($rs))
				{
					if (!isset($annotations[$row['id']])) $annotations[$row['id']] = $row;
					if (!is_null($row['tag'])) $annotations[$row['id']]['tags'][$row['tagid']] = $row['tag'];
				}
				?>

				<div id="system_annotations_titlebar">
					<div style="float:left">
						<input class="button" type="button" value="Lancer la g&eacute;n&eacute;ration du rapport d'activit&eacute;" onclick="javascript:document.f_result.submit();" />
						<input class="button" type="button" value="Retour" onclick="javascript:history.go(-1);" />
					</div>

					<?
					$nb_anno_page = 10;
					$numrows = sizeof($annotations);
					$nbpage = ($numrows - $numrows % $nb_anno_page) / $nb_anno_page + ($numrows % $nb_anno_page > 0);
					if (isset($_GET['page'])) $page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true);
					else $page = 1;

					if ($nbpage>0)
					{
						?>
						<div style="float:right;">
							<div style="float:left;">page :&nbsp;</div>
						<?
						for ($p = 1; $p <= $nbpage; $p++)
						{
							?>
							<a class="system_annotations_page<? if ($p==$page) echo '_sel'; ?>" href="<? echo "{$scriptenv}?page={$p}&idtag={$idtag}"; ?>"><? echo $p; ?></a>
							<?
						}
						?>
						</div>
						<?
					}
					?>
				</div>

				<?
				// on se positionne sur le bon enregistrement
				for ($i=0; $i<($page-1)*$nb_anno_page; $i++) next($annotations);

				$annotation = current($annotations);
				for  ($i=0; $i<$nb_anno_page && !empty($annotation); $i++)
				{
					$object_script = str_replace('<IDRECORD>',$annotation['id_record'],$annotation['script']);
					$object_script = str_replace('<IDMODULE>',$annotation['id_module'],$object_script);
					$ldate = dims_timestamp2local($annotation['date_annotation']);
					$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
					?>
					<div class="system_annotations_row" style="background-color:<? echo $color; ?>">
						<div class="system_annotations_title">
							<input class="checkbox" type="checkbox" name="selanno[]" value="<? echo $annotation['id']; ?>" checked />
							<? echo $annotation['title']; ?>
						</div>
						<div class="system_annotations_date">
							le <? echo $ldate['date']; ?> &agrave; <? echo $ldate['time']; ?>
						</div>
						<div class="system_annotations_content"><? echo dims_make_links(dims_nl2br($annotation['content'])); ?></div>
						<div class="system_annotations_user">par <? echo $annotation['firstname'].' '.$annotation['lastname']; ?></div>
						<div class="system_annotations_taglist">
						<?
						if (isset($annotation['tags']) && sizeof($annotation['tags'])>0)
						{
							?>
							tags:
							<?
							foreach($annotation['tags'] as $idtag => $tag)
							{
								?>
								<a href="<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS."&idtag={$idtag}"); ?>"><? echo $tag; ?></a>
								<?
							}
						}
						?>
						</div>
						<div class="system_annotations_module">
							<a href="<? echo "{$scriptenv}?dims_workspaceid={$annotation['id_workspace']}&dims_mainmenu=1&{$object_script}"; ?>"><b><? echo $annotation['module_name']; ?></b>	/ <? echo $annotation['object_name']; ?> / <? echo $annotation['object_label']; ?></a>
						</div>
					</div>
					<?
					next($annotations);
					$annotation = current($annotations);
				}
				?>
			</div>

			<?
			echo "</form>";
		echo $skin->close_simplebloc('','100%');
		break;

	case 'export':
		$format = (!empty($_POST['format'])) ? dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, true, true, true) : 'html';
		$params = array();

		$selectedannotation = dims_load_securvalue('selanno', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$select = "
			SELECT		a.*,
					t.id as tagid,
					t.tag,
					m.label as module_name,
					o.label as object_name,
					o.script,
					u.firstname,
					u.lastname

			FROM		dims_annotation a

			LEFT JOIN	dims_annotation_tag at
			ON		at.id_annotation = a.id

			LEFT JOIN	dims_tag t
			ON		t.id = at.id_tag
			";
		if (isset($_SESSION['dims']['annotation_report']['seltag'])) {
			$select .= '
				INNER JOIN	dims_annotation_tag at2
				ON		at2.id_annotation = a.id

				INNER JOIN	dims_tag t2
				ON		t2.id = at2.id_tag
				AND		t2.tag IN ('.$db->getParamsFromArray($_SESSION['dims']['annotation_report']['seltag'], 'tags', $params).')';
		}
		$select .= "
			LEFT JOIN	dims_module m
			ON		a.id_module = m.id

			LEFT JOIN	dims_mb_object o
			ON		a.id_object = o.id
			AND		m.id_module_type = o.id_module_type

			INNER JOIN	dims_user u
			ON		u.id = a.id_user
			AND		u.id IN (".$db->getParamsFromArray(explode(',', $_SESSION['dims']['annotation_report']['sel_users']), 'iduser', $params).")

			WHERE		a.id_workspace = :idworkspace
			AND		a.date_annotation BETWEEN :timestampstart AND :timestampend
			AND		a.id IN (".$db->getParamsFromArray($selectedannotation, 'idannotation', $params).")

			ORDER BY	a.date_annotation DESC
			";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':timestampstart'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['annotation_report']['timestp_start']);
		$params[':timestampend'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['annotation_report']['timestp_end']);
		$rs = $db->query($select);

		switch ($format) {
			case 'html':
				// Preparation des donnees
				$annotations = array();
				while ($row = $db->fetchrow($rs)) {
					if (!isset($annotations[$row['id']])) $annotations[$row['id']] = $row;
					if (!is_null($row['tag'])) $annotations[$row['id']]['tags'][$row['tagid']] = $row['tag'];
				}
				if ($_SESSION['dims']['annotation_report']['date_start'] != '') {
					$periode = " du {$_SESSION['dims']['annotation_report']['date_start']}";
				}
				if ($_SESSION['dims']['annotation_report']['date_end'] != '') {
					$periode .= " au {$_SESSION['dims']['annotation_report']['date_end']}";
				}

				// Ecriture du fichier
				while (@ob_end_clean());

				header("Cache-control: private");
				header("Content-type: text/html");
				header("Content-Disposition: attachment; filename=export.html");
				header("Pragma: public");
				?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Rapport d'activit&eacute;</title>

<style type="text/css">
body {
	margin:0;
	margin-left:auto;
	margin-right:auto;
	width:1000px;
}
body, form, table, td, input, select, textarea {
	font: 11px tahoma, verdana, sans-serif;
}
.report_header_title {
	margin: 10px auto 10px auto;
	text-align:center;
	font-size:24px;
	font-weight:bold;
}
.system_annotations_row {
	clear:both;
	overflow:auto;
	padding:2px;
	border-bottom:1px solid #d0d0d0;
}
.system_annotations_title {
	font-weight:bold;
	font-size:1.1em;
	float:left;
	padding:2px 2px 4px 2px;
}
.system_annotations_date {
	float:right;
	padding:2px;
}
.system_annotations_content {
	clear:left;
	float:left;
	padding:2px;
}
.system_annotations_user {
	float:right;
	padding:2px;
}
.system_annotations_taglist {
	clear:both;
	float:left;
	padding:2px;
}
.system_annotations_module {
	float:right;
}
</style>
</head>
<body style="position:relative;">
	<div class="report_header_title">
		Rapport d'activit&eacute;<? echo $periode; ?>
	</div>

	<?
	foreach ($annotations as $annotation) {
		$ldate = dims_timestamp2local($annotation['date_annotation']);
		$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
		?>
		<div class="system_annotations_row" style="background-color:<? echo $color; ?>">
			<div class="system_annotations_title">
				<? echo $annotation['title']; ?>
			</div>
			<div class="system_annotations_date">
				le <? echo $ldate['date']; ?> &agrave; <? echo $ldate['time']; ?>
			</div>
			<div class="system_annotations_content"><? echo dims_make_links(dims_nl2br($annotation['content'])); ?></div>
			<div class="system_annotations_user">par <? echo $annotation['firstname'].' '.$annotation['lastname']; ?></div>
			<div class="system_annotations_taglist">
			<?
			if (isset($annotation['tags']) && sizeof($annotation['tags']) > 0) {
				?>
				tags:
				<?
				foreach($annotation['tags'] as $idtag => $tag) {
					echo "$tag&nbsp;";
				}
			}
			?>
			</div>
			<div class="system_annotations_module">
				<b><? echo $annotation['module_name']; ?></b>  / <? echo $annotation['object_name']; ?> / <? echo $annotation['object_label']; ?>
			</div>
		</div>
		<?
	}
	?>
</body>
</html>
				<?
				die();
				break;
		}
		break;

	default:
		$_SESSION['dims']['annotation_report'] = array();

			echo $skin->create_pagetitle('Annotations - Rapport d\'activit&eacute;','100%');
			echo $skin->open_simplebloc('','100%');

			echo "<form name=\"f_report\" action=\"".$scriptenv."\" method=\"Post\">";
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",		"report");
			$token->field("action",	"result");
			$token->field("seltag");
			$token->field("timestp_start");
			$token->field("timestp_end");
			$token->field("sel_users");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
			?>
			<input type="hidden" name="op" value="report" />
			<input type="hidden" name="action" value="result" />

			<div id="system_annotations_tags">
				<div id="system_annotations_titlebar">
					<b>S&eacute;l&eacute;ctionner les tags : </b>
				</div>
				<?
				$select = "
					SELECT	t.*,
							COUNT(*) AS c,
							u.firstname,
							u.lastname,
							u.login
					FROM	dims_tag t

					LEFT JOIN	dims_annotation_tag at
					ON			t.id = at.id_tag

					LEFT JOIN	dims_annotation a
					ON			a.id = at.id_annotation
					AND			a.id_workspace = :workspaceid

					INNER JOIN	dims_user u
					ON			u.id = t.id_user

					GROUP BY t.tag
					ORDER BY t.tag
					";

				$rs = $db->query($select, array(
					':workspaceid'	=> $_SESSION['dims']['workspaceid']
				));
				$tags = array();
				$users = array();
				$max_c = 0;
				while ($row = $db->fetchrow($rs)) {
					if (!empty($row['c']) && $row['c'] > $max_c) $max_c = $row['c'];
					$tags[$row['id']] = $row;

					if (!in_array($row['id_user'],array_keys($users))) {
						$users[$row['id_user']] = "{$row['firstname']} {$row['lastname']} ({$row['login']})";
					}
				}

				$maxsize = 10;
				$minsize = 10;

				// Checkbox correspondant aux tags
				?>
				<div style="display:none;visibility:hidden;">
					<?
					foreach($tags as $idtag => $tag) {
						?>
						<input type="checkbox" id="seltag_<? echo $tag['id']; ?>" name="seltag[]" value="<? echo $tag['tag']; ?>" />
						<?
					}
					?>
				</div>
				<?

				// Affichage des tags
				foreach($tags as $idtag => $tag) {
					$size = $minsize + $maxsize * $tag['c'] / $max_c;
					?>
					<a id="tag_<? echo $tag['id']; ?>" title="utilis&eacute; <? echo $tag['c']; ?> fois" class="system_annotations_tag" style="font-size: <? echo $size; ?>px;<? echo $color; ?>;" href="#" onclick="javascript:system_report_switch_style('<? echo $tag['id']; ?>', '<? echo $skin->values['bgline1']; ?>');">
						<? echo $tag['tag']; ?>
						<span style="vertical-align: 4px; font-size: 7px"> <? echo $tag['c']; ?></span>
					</a>
					<?
				}
				?>
			</div>

			<div id="system_report_period">
				Sur la p&eacute;riode du :
				<input style="width:100px;" class="text" type="text" name="timestp_start" id="timestp_start" value="" />
				<a href="#" onclick="javascript:dims_calendar_open('timestp_start', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
				au :
				<input style="width:100px;" class="text" type="text" name="timestp_end" id="timestp_end" value="<? echo date("d/m/Y"); ?>" />
				<a href="#" onclick="javascript:dims_calendar_open('timestp_end', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>

				<input class="button" type="submit" value="G&eacute;n&eacute;rer le rapport d'activit&eacute;" />
				<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS) ?>';" />
			</div>
			<div id="system_report_users">
				<div id="system_report_users_titlebar">
					Pour les utilisateurs suivants : (<a href="#" onclick="javascript:dims_checkall(document.f_report, 'report_users', true);">Tous</a> / <a href="#" onclick="javascript:dims_checkall(document.f_report, 'report_users', false);">Aucun</a>)
				</div>
				<?
				foreach ($users as $id_user => $username) {
					$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
					?>
					<div class="system_report_users_row" style="background-color:<? echo $color; ?>;">
						<div style="float:left">
							<input class="checkbox" type="checkbox" name="sel_users[]" value="<? echo $id_user; ?>" checked />
						</div>
						<div style="overflow:auto;"><? echo $username; ?></div>
					</div>
					<?
				}
				?>
			</div>


			<?
			echo "</form>";
			echo $skin->close_simplebloc('','100%');
		break;
}
?>
