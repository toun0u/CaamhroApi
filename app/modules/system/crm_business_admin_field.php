<?
require_once DIMS_APP_PATH . "/modules/system/include/global.php";
require_once DIMS_APP_PATH . "/modules/system/include/metatype.php";

$metafield = new metafield();

if ($metafield_id>0) {
	$metafield->open($metafield_id);
	$title=$_DIMS['cste']['_FIELDMODIFICATION'];
}
else {
	$metafield->init_description();
	$metafield->fields['option_arrayview'] = 1;
	$metafield->fields['option_exportview'] = 1;

	$rescateg=$db->query("select * from dims_mod_business_meta_categ order by position");
	if ($f=$db->fetchrow($rescateg)) {
		$metafield->fields['id_metacateg'] = $f['id'];
	}
	$title=$_DIMS['cste']['_DIMS_ADD'];
}
?>
<div style="clear:left;width:100%;background:#FFFFFF;">
	<table cellpadding="2" cellspacing="1" width="100%" bgcolor="<? if (array_key_exists('bgline1', $skin->values)) { echo $skin->values['bgline1']; } ?>">
	<tr>
		<td><strong><? echo $title; ?></strong></td>
	</tr>
	</table>

	<table cellpadding="0" cellspacing="0" bgcolor="<? if (array_key_exists('colsec', $skin->values)) { echo $skin->values['colsec']; } ?>" width="100%"><tr><td height="1"></td></tr></table>
	<form name="form_field" action="<? echo 'admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ADMIN.''; ?>" method="post" onsubmit="javascript:return field_validate(this);">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("object_id",		$object_id);
		$token->field("op",				"savemetafield");
		$token->field("field_values",	$metafield->fields['values']);
	?>
	<input type="hidden" name="object_id" value="<? echo $object_id; ?>">
	<input type="hidden" name="op" value="savemetafield">
	<input type="hidden" name="field_values" value="<? echo ($metafield->fields['values']); ?>">
	<table cellpadding="2" cellspacing="1">
	<?
	if (isset($metafield_id) && $metafield_id>0) {
		$_SESSION['dims']['current_metafield_id']=$metafield_id;
	}

	// construction de la liste des champs mbfields deja utilises
	$mbfields_used = array();
	$sql ="select		distinct id_mbfield,label,protected,indexed
			from		dims_mod_business_meta_field
			inner join	dims_mb_field
			on			dims_mb_field.id=dims_mod_business_meta_field.id_mbfield
			and			dims_mod_business_meta_field.id_object = :idobject ";

	$res=$db->query($sql, array(
		':idobject' => $object_id
	));
	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			$mbfields_used[$f['id_mbfield']]=$f;
		}
	}
	?>
	<tr>
		<td valign="top">
			<table cellpadding="2" cellspacing="1">
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_POSITION']; ?></td>
				<td>
				<?
				if (isset($metafield_id) && $metafield_id>0) {
					$respos=$db->query("SELECT max(position) as maxi
										FROM dims_mod_business_meta_field
										WHERE id_object= :idobject
										AND id_metacateg= :idmetacateg ", array(
								':idobject' 	=> $object_id,
								':idmetacateg' 	=> $metafield->fields['id_metacateg']
							));
					if ($db->numrows($respos)>0) {
						$fresu=$db->fetchrow($respos);
						$maxi=$fresu['maxi'];
					}
					else $maxi=1;

					echo "<select id=\"fieldnew_position\" name=\"fieldnew_position\">";
					$token->field("fieldnew_position");

					if ($metafield->fields['position']=="") $metafield->fields['position']=$maxi;

					for ($indi=1;$indi<=$maxi;$indi++) {
						if ($metafield->fields['position']==$indi) $selected="selected";
						else $selected="";

						echo "<option ".$selected." value=\"".$indi."\">".$indi."</option>";
					}

					echo "</select>";
				}
				?>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_CATEGORY']; ?></td>
				<td>
				<select class="select" name="field_id_metacateg" id="field_id_metacateg">
					<?
					$token->field("field_id_metacateg");
					$rescateg=$db->query("select * from dims_mod_business_meta_categ order by position");
					while ($f=$db->fetchrow($rescateg)) {
						$key=$f['id'];
						$value=$f['label'];
						$sel = ($metafield->fields['id_metacateg'] == $key) ? 'selected' : '';
						echo "<option $sel value=\"{$key}\">{$value}</option>";
					}
					?>
				</select>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_GENFIELD']; ?></td>
				<td>
				<?
/*				if (isset($mbfields_used[$metafield->fields['id_mbfield']]['protected']) && $mbfields_used[$metafield->fields['id_mbfield']]['protected']==0) {
					echo "&nbsp;-";
				}
				else {*/
				?>
				<select class="select" name="field_id_mbfield" <? if (isset($select_dis)) echo $select_dis;?>>
					<? $token->field("field_id_mbfield"); ?>
					<option value="0"></option>
					<?
					if ($object_id==dims_const::_SYSTEM_OBJECT_CONTACT) {
						$sql = "SELECT dims_mb_field.* FROM `dims_mb_field` inner join dims_mb_table as t on t.id=dims_mb_field.id_table WHERE t.name LIKE 'dims_mod_business_contact'";
					}
					else {
						$sql = "SELECT dims_mb_field.* FROM `dims_mb_field` inner join dims_mb_table as t on t.id=dims_mb_field.id_table WHERE t.name LIKE 'dims_mod_business_tiers'";
					}

					$rescateg=$db->query($sql);
					while ($f=$db->fetchrow($rescateg)) {
						if (!isset($mbfields_used[$f['id']]) || $f['id']==$metafield->fields['id_mbfield']) {
							$key=$f['id'];
							if (isset($_DIMS['cste'][$f['label']])) $value=$_DIMS['cste'][$f['label']];
							else $value=$f['label'];

							$sel = ($metafield->fields['id_mbfield'] == $key) ? 'selected' : '';
							echo "<option $sel value=\"{$key}\">{$value}</option>";
						}
					}
				//}
					?>
				</select>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_SYSTEM_LABELICON_INDEX']; ?></td>
				<td>
					<?
					if (isset($mbfields_used[$metafield->fields['id_mbfield']]['indexed'])
							&& $mbfields_used[$metafield->fields['id_mbfield']]['indexed']) $valindex="checked";
					else $valindex="";
					?>
					<input type="checkbox" name="is_indexed" <? echo $valindex;?>>
					<? $token->field("is_indexed"); ?>
				</td>
			</tr>
			<?
			//if ($metafield->fields['fieldname'] == '') $metafield->fields['fieldname'] = forms_createphysicalname($metafield->fields['name']);
			?>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_FIELD_FIELDNAME']; ?></td>
				<td>
				<?
				if (isset($metafield->fields['id_mbfield']) && $metafield->fields['id_mbfield']>0 && isset($mbfields_used[$metafield->fields['id_mbfield']]['protected']) && $mbfields_used[$metafield->fields['id_mbfield']]['protected']==1) {
					if (isset($_DIMS['cste'][$mbfields_used[$metafield->fields['id_mbfield']]['label']])) $value=$_DIMS['cste'][$mbfields_used[$metafield->fields['id_mbfield']]['label']];
					else $value=$mbfields_used[$metafield->fields['id_mbfield']]['label'];
					echo '<input type="text" style="color:#ABABAB;" disabled class="text" size="30" id="field_name" name="field_name" value="'.($value).'">';
				}
				else {
					echo '<input type="text" class="text" size="30" id="field_name" name="field_name" value="'.($metafield->fields['name']).'">';
				}
				$token->field("field_name");
				?>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_TYPE']; ?></td>
				<td>
					<select class="select" name="field_type" onchange="javascript:display_fieldvalues();display_fieldformats();display_fieldcols();display_tablelink();">
					<?
					$token->field("field_type");
					foreach($metafield_types as $key => $value) {
						$sel = ($metafield->fields['type'] == $key) ? 'selected' : '';
						echo "<option $sel value=\"{$key}\">{$value}</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<?
			// verification du type de champ, si liste de choix
			if ($metafield->fields['type']=="select") {
			?>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_PREDEFINED']; ?></td>
				<td>
				<select class="select" name="field_enum" id="field_enum">
					<?
					$token->field("field_enum");
					$restype=$db->query("select distinct type from dims_mod_business_enum order by type");
					echo "<option value=\"\"></option>";
					while ($f=$db->fetchrow($restype)) {
						$key=$f['type'];
						$value=$f['type'];
						$sel = ($metafield->fields['enum'] == $key) ? 'selected' : '';
						echo "<option $sel value=\"{$key}\">{$value}</option>";
					}
					?>
				</select>
				</td>
			</tr>
			<?
			}
			?>
			<tr>
				<td align="right" valign="top"><? echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']; ?></td>
				<td><textarea class="text" cols="40" rows="4" name="field_description"><? echo ($metafield->fields['description']); ?></textarea>
				<? $token->field("field_description"); ?>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?></td>
				<td><input type="text" class="text" size="30" name="field_defaultvalue" value="<? echo ($metafield->fields['defaultvalue']); ?>"></td>
				<? $token->field("field_defaultvalue"); ?>
			</tr>
			<tr>
				<td align="right">Options: </td>
				<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="checkbox" name="field_option_needed" value="1" <? if ($metafield->fields['option_needed']) echo 'checked'; ?>></td>
						<? $token->field("field_option_needed"); ?>
						<td align="left"><? echo $_DIMS['cste']['_FIELD_NEEDED']; ?></td>
					</tr><tr>
						<td><input type="checkbox" name="field_option_search" value="1" <? if ($metafield->fields['option_search']) echo 'checked'; ?>></td>
						<? $token->field("field_option_search"); ?>
						<td align="left"><? echo $_DIMS['cste']['_SEARCH']; ?></td>
					</tr><tr>
						<td><input type="checkbox" name="field_option_exportview" value="1" <? if ($metafield->fields['option_exportview']) echo 'checked'; ?>></td>
						<? $token->field("field_option_exportview"); ?>
						<td align="left"><? echo $_DIMS['cste']['_FORMS_FIELD_EXPORTVIEW']; ?></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_DIMS_MODE']; ?></td>
				<td>
					<select class="select" name="field_mode">
					<? $token->field("field_mode"); ?>
					<?
						$sel = ($metafield->fields['mode'] == 0) ? 'selected' : '';
						echo "<option $sel value=\"0\">".$_DIMS['cste']['_DIMS_LABEL_PUBLIC']."</option>";

						$sel = ($metafield->fields['mode'] == 1) ? 'selected' : '';
						echo "<option $sel value=\"1\">".$_DIMS['cste']['_DIMS_LABEL_CAN_SHARE']."</option>";
					?>
					</select>
				</td>
			</tr>

			</table>
		</td>
		<td valign="top">
			<table cellpadding="2" cellspacing="1" id="fieldformats" style="display:block;">
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_FIELD_FORMAT']; ?>: </td>
				<td>
					<select class="select" name="field_format">
					<? $token->field("field_format"); ?>
					<?

					foreach($metafield_formats as $key => $value) {
						$sel = ($metafield->fields['format'] == $key) ? 'selected' : '';
						echo "<option $sel value=\"{$key}\">{$value}</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_FORMS_FIELD_MAXLENGTH']; ?>: </td>
				<td><input type="text" class="text" size="5" name="field_maxlength" value="<? echo $metafield->fields['maxlength']; ?>"></td>
				<? $token->field("field_maxlength"); ?>
			</tr>
			</table>
			<table cellpadding="2" cellspacing="1" id="fieldvalues" style="display:none;">
			<tr>
				<td align="right" valign="top"><? echo $_DIMS['cste']['_FIELD_VALUES']; ?>: </td>
				<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
						<select name="f_values" class="select" size="12" style="width:250px" onclick="document.form_field.newvalue.value=this.value;document.form_field.newvalue.focus();">
						<? $token->field("f_values"); ?>
						<?
						if ($metafield->fields['type'] == 'radio' || $metafield->fields['type'] == 'select' || $metafield->fields['type'] == 'checkbox') {
							foreach(explode('||',$metafield->fields['values']) as $value) {
								if ($value != '') {
									if ($metafield->fields['type'] == 'color') echo "<option value=\"$value\" style=\"background-color:$value;\"></option>";
									else echo "<option value=\"$value\">$value</option>";
								}
							}
						}
						?>
						</select>
						</td>
						<td valign="top">
							<input style="width:25px;margin:5px;" type="button" class="button" value="+" onclick="javascript:move_value(document.form_field.f_values,1)">
							<br />
							<input style="width:25px;margin:5px;" type="button" class="button" value="-" onclick="javascript:move_value(document.form_field.f_values,-1)">
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<input style="width:250px;margin:5px 0px 5px 0px;" name="newvalue" type="text" class="text">
						<? $token->field("newvalue"); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<input type="button" class="button" value="<? echo $_DIMS['cste']['_DIMS_ADD']; ?>" onclick="javascript:add_value(document.form_field.f_values, document.form_field.newvalue)">
						<input type="button" class="button" value="<? echo $_DIMS['cste']['_MODIFY']; ?>" onclick="javascript:modify_value(document.form_field.f_values, document.form_field.newvalue)">
						<input type="button" class="button" value="<? echo $_DIMS['cste']['_DELETE']; ?>" onclick="javascript:delete_value(document.form_field.f_values)">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			<table cellpadding="2" cellspacing="1" id="fieldcols" style="display:none;">
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_FIELD_MULTICOLDISPLAY']; ?>: </td>
				<td>
				<select name="field_cols" class="select">
				<? $token->field("field_cols"); ?>
				<?
				for ($i=1;$i<=5;$i++) {
					$sel = ($i == $metafield->fields['cols']) ? 'selected' : '';
					echo "<option value=\"{$i}\" {$sel}>{$i}</option>";
				}
				?>
				</select>
				</td>
			</tr>
			</table>
			<table cellpadding="2" cellspacing="1" id="tablelink" style="display:none;">
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_FIELD_FORMFIELD']; ?>: </td>
				<td>
				<select class="select" name="f_formfield" style="width:200px;">
				<? $token->field("f_formfield"); ?>
				<?
				$res=$db->query("
							SELECT	forms.label, field.*
							FROM	dims_mod_forms forms,
									dims_mod_forms_field field
							WHERE	forms.id_module = :idmodule
							AND		forms.id = field.id_forms
							AND		field.separator = 0
							ORDER BY label, position
							", array(
						':idmodule' => $_SESSION['dims']['moduleid']
					));

				while ($row = $db->fetchrow($res))
				{
					$sel = ($metafield->fields['values'] == $row['id']) ? 'selected' : '';
					echo "<option $sel value=\"{$row['id']}\">{$row['label']} | {$row['name']}</option>";
				}

				?>
				</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<table cellpadding="0" cellspacing="0" bgcolor="<?= isset($skin->values['colsec'])?$skin->values['colsec']:""; ?>" width="100%"><tr><td height="1"></td></tr></table>

	<table cellpadding="2" cellspacing="1" width="100%" bgcolor="<?= isset($skin->values['bgline1'])?$skin->values['bgline1']:""; ?>">
	<tr>
		<td align="right">
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.location.href='<? echo "$scriptenv?op=modify&forms_id={$object_id}"; ?>'">&nbsp;
			<input type="submit" class="flatbutton" id="button_save" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"></td>
	</tr>
	</table>
	<?
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>

	<table cellpadding="0" cellspacing="0" bgcolor="<?= isset($skin->values['colsec'])?$skin->values['colsec']:""; ?>" width="100%"><tr><td height="1"></td></tr></table>
</div>
<script type="text/javascript">
function display_fieldvalues() {
	t = document.form_field.field_type;
	if (t.value == 'textarea' || t.value == 'text' || t.value == 'file' || t.value == 'autoincrement' || t.value == 'tablelink') document.getElementById('fieldvalues').style.display='none';
	else document.getElementById('fieldvalues').style.display='block';

	verifcolor = (t.value == 'color');
}

function display_fieldformats() {
	t = document.form_field.field_type;
	if (t.value == 'text') document.getElementById('fieldformats').style.display='block';
	else document.getElementById('fieldformats').style.display='none';
}

function display_fieldcols() {
	t = document.form_field.field_type;
	if (t.value == 'textarea' || t.value == 'text' || t.value == 'color' || t.value == 'select' || t.value == 'file' || t.value == 'autoincrement'	|| t.value == 'tablelink') document.getElementById('fieldcols').style.display='none';
	else document.getElementById('fieldcols').style.display='block';
}

function display_tablelink() {
	t = document.form_field.field_type;
	if (t.value == 'tablelink') document.getElementById('tablelink').style.display='block';
	else document.getElementById('tablelink').style.display='none';
}

if (window.attachEvent) {
	window.attachEvent('onload', display_fieldvalues);
	window.attachEvent('onload', display_fieldformats);
	window.attachEvent('onload', display_fieldcols);
	window.attachEvent('onload', display_tablelink);
}
else {
	window.onload = display_fieldvalues();
	window.onload = display_fieldformats();
	window.onload = display_fieldcols();
	window.onload = display_tablelink();
}

dims_getelem('field_name').focus();
</script>


