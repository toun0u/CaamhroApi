<?php
function dims_get_nbannotation($id_object, $id_record, $id_user = -1, $id_group = -1, $id_module = -1) {
	$db = dims::getInstance()->getDb();

	if ($id_user == -1) $id_user = $_SESSION['dims']['userid'];
	if ($id_group == -1) $id_group = $_SESSION['dims']['workspaceid'];
	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	$select =	"
		SELECT		count(*) as c
		FROM		dims_annotation a
		WHERE		a.id_record = :idrecord
		AND		a.id_object = :idobject
		AND		a.id_module = :idmodule
		AND		(a.private = 0
		OR		(a.private = 1 AND a.id_user = :iduser)) ";
	$res=$db->query($select, array(
		':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
		':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
	));

	if ($fields = $db->fetchrow($res)) $nbanno = $fields['c'];
	else $nbanno = 0;

	return($nbanno);
}

function dims_annotation($id_object, $id_record, $object_label = '', $id_user = -1, $id_workspace = -1, $id_module = -1,$display=false) {
	global $skin;
	$db = dims::getInstance()->getDb();
	global $dims;
	global $dims_annotation_private;
	global $_DIMS;
	if ($id_user == -1) $id_user = $_SESSION['dims']['userid'];
	if ($id_workspace == -1) $id_workspace = $_SESSION['dims']['workspaceid'];
	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	$mods=$dims->getModules($id_workspace);

	// generate annotation id
	$annotation_id_object = base64_encode("{$id_module}_{$id_object}_".addslashes($id_record));

	$select = "
		SELECT		count(*) as c
		FROM		dims_annotation a
		WHERE		a.id_record = :idrecord
		AND		a.id_object = :idobject
		AND		a.id_module = :idmodule
		AND		(a.private = 0
		OR		(a.private = 1 AND a.id_user = :iduser)) ";
	$rs_anno = $db->query($select, array(
		':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
		':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
	));

	if ($fields = $db->fetchrow($rs_anno)) $nbanno = $fields['c'];
	else $nbanno = 0;

	unset($_SESSION['dims']['annotations']['show'][$annotation_id_object]);
	$annotation_show = (isset($_SESSION['dims']['annotations']['show'][$annotation_id_object]));
	?>
	<a name="annotation_<? echo $annotation_id_object; ?>" style="display:none;"></a>
	<div style="overflow:hidden;">
		<div style="width:60%;text-align:center;float:left;">
			<?
			if ($nbanno<=1) {
				$label=$_SESSION['cste']['_DIMS_LABEL_ANNOTATION'];
			}
			else {
				$label=$_SESSION['cste']['_DIMS_COMMENTS'];
			}
			?>

			<a style="vertical-align: middle" id="annotations_count_<? echo $annotation_id_object; ?>" href="#annotation_<? echo $annotation_id_object; ?>" onclick="javascript:dims_switchdisplay('annotations_list_<? echo $annotation_id_object; ?>'); dims_xmlhttprequest('index-light.php','dims_op=annotation_show&object_id=<? echo $annotation_id_object; ?>');return false;"><img style="vertical-align: middle" border="0" src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/annotation.png"><span>&nbsp;<? echo $nbanno." ".$label; ?> - <? echo $_SESSION['cste']['_DIMS_LABEL_VIEW']." ".strtolower($_SESSION['cste']['_DIMS_COMMENTS']); ?></span></a>
		</div>
		<div style="width:30%;text-align:right;float:left;"><a style="vertical-align: middle;" id="annotations_count_<? echo $annotation_id_object; ?>" href="#annotation_<? echo $annotation_id_object; ?>" onclick="javascript:dims_switchdisplay('annotations_add_<? echo $annotation_id_object; ?>');$('dims_annotation_title').focus();dims_xmlhttprequest('index-light.php','dims_op=annotation_show&object_id=<?php echo $annotation_id_object; ?>');return false;"><img style="vertical-align: middle" src="./common/img/add.gif" style="border:0px;"><span style="vertical-align: middle">&nbsp;<? echo $_SESSION['cste']['_DIMS_ADD']." ".$_SESSION['cste']['_DIMS_LABEL_ANNOTATION']; ?></span></a></div>
		<div style="clear:both;display:<?php echo ($annotation_show) ? 'block' : 'none'; ?>;" id="annotations_add_<? echo $annotation_id_object; ?>">
		<?
		if ($_SESSION['dims']['connected']) {
			//$id_module_type = (isset($mods[$id_module]['viewmode'])) ? $mods[$id_module]['id_module_type'] : 0;
			global $dims;
			$mod=$dims->getModule($id_module,$id_workspace);
			$id_module_type = $mod['id_module_type'];

			$numrow = (!isset($numrow) || $numrow == 2) ? 1 : 2;
			?>
			<form action="" method="post" name="form_annotation_<? echo $id_record; ?>">
			<div style="width:100%" class="dims_annotations_row_<? echo $numrow; ?>">

				<input type="hidden" name="dims_op" value="annotation_save">
				<input type="hidden" name="dims_annotation_id_object" value="<?php echo $id_object; ?>">
				<input type="hidden" name="dims_annotation_id_record" value="<?php echo $id_record; ?>">
				<input type="hidden" name="dims_annotation_id_module" value="<?php echo $id_module; ?>">
				<input type="hidden" name="dims_annotation_id_module_type" value="<?php echo $id_module_type; ?>">
				<input type="hidden" name="dims_annotation_id_user" value="<?php echo $id_user; ?>">
				<input type="hidden" name="dims_annotation_id_workspace" value="<?php echo $id_workspace; ?>">
				<?php
					if ($object_label != "" )
						$annotation_object_label = $object_label;
					elseif ($_SESSION['dims']['current_object']['label'] != "")
						$annotation_object_label = $_SESSION['dims']['current_object']['label'] ;
				?>
				<input type="hidden" name="dims_annotation_object_label" value="<?php echo $annotation_object_label; ?>">

				<div style="padding:2px 4px;"><input type="checkbox" name="dims_annotation_private" value="1"><? echo $_DIMS['cste']['_PRIVATE']; ?></div>
				<div style="padding:2px 4px;"><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?></div>
				<div style="padding:2px 4px;"><input type="text" class="text" style="width:50%;" id="dims_annotation_title" name="dims_annotation_title"></div>
				<?
                                /*<div style="padding:2px 4px;">Tags:</div>
				<div style="padding:2px 4px;"><input type="text" class="text" style="width:50%;" onfocus="dims_tag_init('<?php echo $id_record; ?>')" name="dims_annotationtags" id="dims_annotationtags_<? echo $id_record; ?>" autocomplete="off"></div>
				<div style="padding:2px 4px;" id="tagsfound_<? echo $id_record; ?>"></div>
                                 *
                                 */?>
				<div style="padding:2px 4px;"><? echo $_DIMS['cste']['_DIMS_LABEL_ANNOTATION']; ?></div>
				<div style="padding:2px 4px;"><textarea class="text" style="width:99%;" rows="5" name="dims_annotation_content"></textarea></div>

				<div style="padding:2px 4px;text-align:right;">
                    <?php
                        echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],"cancel","javascript:dims_switchdisplay('annotations_add_".$annotation_id_object."')");
                        echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:forms.form_annotation_".$id_record.".submit();");
                    ?>
				</div>
                                <?
                                /*
				<script type="text/javascript">
					dims_tag_init('<?php echo $id_record; ?>');
				</script>*/
                                ?>
			</div>
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("dims_op","annotation_save");
				$token->field("dims_annotation_id_object",$id_object);
				$token->field("dims_annotation_id_record",$id_record);
				$token->field("dims_annotation_id_module",$id_module);
				$token->field("dims_annotation_id_module_type",$id_module_type);
				$token->field("dims_annotation_id_user",$id_user);
				$token->field("dims_annotation_id_workspace",$id_workspace);
				$token->field("dims_annotation_object_label",$annotation_object_label);
				$token->field("dims_annotation_private");
				$token->field("dims_annotation_title");
				$token->field("dims_annotation_content");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			</form>
			<?php
		}

		?>
		</div>


		<div style="display:<?php echo ($display) ? 'block' : 'none'; ?>;;clear:both;" id="annotations_list_<? echo $annotation_id_object; ?>">

		<?php

		$select =	"
					SELECT		a.*,
							u.firstname,
							u.lastname,
							u.login,
							t.id as idtag,
							t.tag
					FROM		dims_annotation a

					INNER JOIN	dims_user u ON a.id_user = u.id

					LEFT JOIN	dims_annotation_tag at ON a.id = at.id_annotation
					LEFT JOIN	dims_tag t ON t.id = at.id_tag

					WHERE		a.id_record = :idrecord
					AND		a.id_object = :idobject
					AND		a.id_module = :idmodule
					AND		(a.private = 0
					OR		(a.private = 1 AND a.id_user = :iduser))
					ORDER BY	a.date_annotation DESC
					";

		$rs_anno = $db->query($select, array(
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));

		$array_anno = array();
		while ($fields = $db->fetchrow($rs_anno)) {
			$array_anno[$fields['id']]['fields'] = $fields;
			if (!is_null($fields['tag'])) $array_anno[$fields['id']]['tags'][$fields['idtag']] = $fields['tag'];
		}

		if (sizeof($array_anno)==0) {
			echo "<span style='font-weight:bold;margin-top:20px;text-align:center;width:100%;'>".$_SESSION['cste']['_DIMS_LABEL_NO_COMMENT']."</span>";
		}
		else {
			foreach($array_anno as $anno) {
				$fields = $anno['fields'];
				$ldate = dims_timestamp2local($fields['date_annotation']);
				$numrow = (!isset($numrow) || $numrow == 2) ? 1 : 2;

				$private = '';
				if ($fields['private']) $private = $_DIMS['cste']['_PRIVATE'];
				?>
				<div class="dims_annotations_row_<? echo $numrow; ?>" style="clear: both;">
					<div>
						<div style="float:right;padding:2px 4px;"><? echo $_DIMS['cste']['_DIMS_LABEL_BY_USER']; ?> <strong><?php echo "{$fields['firstname']} {$fields['lastname']}"; ?></strong> <? echo $_DIMS['cste']['_AT']; ?> <?php echo $ldate['date']; ?> - <?php echo $ldate['time']; ?> <?php echo $private; ?></div>
						<div style="padding:2px 4px;"><strong><?php echo $fields['title']; ?></strong></div>
					</div>
					<div style="clear:both;padding:2px 4px;"><?php echo dims_make_links(nl2br($fields['content'])); ?></div>
					<div style="clear:both;">
						<?php
						if ($fields['id_user'] == $id_user) {
							?>
							<div style="float:right;padding:2px 4px;">
								<form action="" method="post" name="form_annotation_delete_<? echo $fields['id']; ?>">
								<input type="hidden" name="dims_op" value="annotation_delete">
								<input type="hidden" name="dims_annotation_id" value="<?php echo $fields['id']; ?>">
									<a href="javascript:dims_confirmform(document.form_annotation_delete_<? echo $fields['id']; ?>,'<? echo addslashes($_DIMS['cste']['_DIMS_CONFIRM']); ?>');"><? echo $_DIMS['cste']['_DELETE']; ?></a>
								<?
									// Sécurisation du formulaire par token
									require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
									$token = new FormToken\TokenField;
									$token->field("dims_op",			"annotation_delete");
									$token->field("dims_annotation_id",	$fields['id']);
									$tokenHTML = $token->generate();
									echo $tokenHTML;
								?>
								</form>
							</div>
							<?php
						}
						?>
						<div style="padding:2px 4px;">
						<?php
                                                /*
						if (isset($anno['tags']) && is_array($anno['tags'])) {
							echo "<b>tags :</b>";
							foreach($anno['tags'] as $idtag => $tag) {
								?>
								<a href="#" onclick="javascript:dims_showpopup('','550',event,'click');dims_xmlhttprequest_todiv('index-light.php','dims_op=tags_annotationsearch&id_tag=<?php echo $idtag; ?>','','dims_popup');return false;"><?php echo $tag; ?></a>
								<?php
							}
						}*/
						?>
						</div>
					</div>
				</div>
				<?php
			}
		}
		?>
		</div>
	</div>
	<?php
}
