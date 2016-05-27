<?
if (!isset($op)) $op = '';
switch($op)
{
	case 'report':
		require_once 'public_annotations_report.php';
		break;

	default:
		//require_once(DIMS_APP_PATH . '/modules/system/class_favorite_heading.php');
		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ANNOTATION'],'100%');
			?>

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
					':workspaceid' => $_SESSION['dims']['workspaceid']
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

					if (isset($_GET['tag']) && $_GET['tag'] == $tag['tag']) $color = "background-color:{$skin->values['bgline1']};";
					else $color = '';

					?>
					<a title="utilis&eacute; <? echo $tag['c']; ?> fois" class="system_annotations_tag" style="font-size: <? echo $size; ?>px;<? echo $color; ?>;" href="<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS."&tag={$tag['tag']}"); ?>"><? echo $tag['tag']; ?><span style="vertical-align: 4px; font-size: 7px"> <? echo $tag['c']; ?></span></a>
					<?
				}
				?>
			</div>

			<div id="system_annotations_list">
				<?
				$tag = '';

				if (!empty($_GET['tag']))
				{
					$tag = dims_load_securvalue('tag', dims_const::_DIMS_CHAR_INPUT, true, true, true);

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
						ON			at.id_annotation = a.id

						LEFT JOIN	dims_tag t
						ON			t.id = at.id_tag

						INNER JOIN	dims_annotation_tag at2
						ON			a.id = at2.id_annotation

						INNER JOIN	dims_tag t2
						ON			t2.id = at2.id_tag
						AND			t2.tag = :tag

						LEFT JOIN	dims_module m
						ON			a.id_module = m.id

						LEFT JOIN	dims_mb_object o
						ON			a.id_object = o.id
						AND			m.id_module_type = o.id_module_type

						INNER JOIN	dims_user u
						ON			u.id = a.id_user

						WHERE		a.id_workspace = :idworkspace

						ORDER BY	a.date_annotation DESC
						";
					$params[':tag'] = array('type' => PDO::PARAM_STR, 'value' => $tag);
				} else {
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
						ON			at.id_annotation = a.id

						LEFT JOIN	dims_tag t
						ON			t.id = at.id_tag

						LEFT JOIN	dims_module m
						ON			a.id_module = m.id

						LEFT JOIN	dims_mb_object o
						ON			a.id_object = o.id
						AND			m.id_module_type = o.id_module_type

						INNER JOIN	dims_user u
						ON			u.id = a.id_user

						WHERE		a.id_workspace = :idworkspace

						ORDER BY	a.date_annotation DESC
						";
				}
				$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);

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
						<input class="button" type="button" value="G&eacute;n&eacute;rer un rapport d'activit&eacute;" onclick="javascript:document.location.href='<? echo dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_ANNOTATIONS."&op=report"); ?>';" />
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
							<a class="system_annotations_page<? if ($p==$page) echo '_sel'; ?>" href="<? echo "{$scriptenv}?page={$p}&tag={$tag}"; ?>"><? echo $p; ?></a>
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
		echo $skin->close_simplebloc('','100%');
	break;
}
?>
