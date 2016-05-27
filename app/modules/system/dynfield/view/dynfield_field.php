<?php
require_once DIMS_APP_PATH . "/modules/system/include/global.php";
require_once DIMS_APP_PATH . "/modules/system/include/metatype.php";

$db = dims::getInstance()->getDB();
global $skin;

$metafield_id = $metafield->getID();

$object_id = $this->getIdObject();
$module_type_id = $this->getIdModuleType();

if ($metafield_id>0) {
	$title=$_DIMS['cste']['_FIELDMODIFICATION'];
}
else {
	$metafield->fields['option_arrayview'] = 1;
	$metafield->fields['option_exportview'] = 1;

	$title=$_DIMS['cste']['_DIMS_ADD'];
}
?>
<div style="clear:left;width:100%;background:#FFFFFF;">
	<table cellpadding="2" cellspacing="1" width="100%" bgcolor="<? echo $skin->values['bgline1']; ?>">
	<tr>
		<td><strong><? echo $title; ?></strong></td>
	</tr>
	</table>

	<table cellpadding="0" cellspacing="0" bgcolor="<? echo $skin->values['colsec']; ?>" width="100%"><tr><td height="1"></td></tr></table>
	<form name="form_field" action="admin.php?<?php echo $this->getMetierEnvList(); ?>" method="post" onsubmit="javascript:return field_validate(this);">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("object_id",		$object_id);
		$token->field("module_type_id",	$module_type_id);
		$token->field("dims_op",		"dynfield_manager");
		$token->field("dynfield_op",	"savemetafield");
		$token->field("field_values",	$metafield->fields['values']);
		$token->field("tablename",		$tablename);
		$token->field("metafield_id",	$metafield_id);
		$token->field("fieldnew_position");
		$token->field("is_indexed");
		$token->field("field_name");
		$token->field("field_type");
		$token->field("field_enum");
		$token->field("field_description");
		$token->field("field_defaultvalue");
		$token->field("field_option_needed");
		$token->field("field_option_search");
		$token->field("field_option_exportview");
		$token->field("field_mode");
		$token->field("field_format");
		$token->field("field_maxlength");
		$token->field("f_values");
		$token->field("newvalue");
		$token->field("field_cols");
		$token->field("f_formfield");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="object_id" value="<? echo $object_id; ?>">
	<input type="hidden" name="module_type_id" value="<? echo $module_type_id; ?>">
	<input type="hidden" name="dims_op" value="dynfield_manager">
	<input type="hidden" name="dynfield_op" value="savemetafield">
	<input type="hidden" name="field_values" value="<? echo $metafield->fields['values']; ?>">
	<input type="hidden" name="tablename" value="<? echo $tablename; ?>">
	<input type="hidden" name="metafield_id" value="<? echo $metafield_id; ?>">
	<table cellpadding="2" cellspacing="1">
	<?
	if (isset($metafield_id) && $metafield_id>0) {
		$_SESSION['dims']['current_meta_field_id']=$metafield_id;
	}

	// construction de la liste des champs mbfields deja utilises
	$mbfields_used = array();
	$sql =" SELECT		distinct id_mbfield,label,protected,indexed
			from		dims_meta_field
			inner join	dims_mb_field
			on		dims_mb_field.id=dims_meta_field.id_mbfield
			and		dims_meta_field.id_object = :objectid
			and		dims_meta_field.id_module_type = :moduletypeid ";

	$res=$db->query($sql, array(
		':objectid'		=> $object_id,
		':moduletypeid'	=> $module_type_id
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
					$respos=$db->query("select max(position) as maxi from dims_meta_field where id_object=".$object_id);
					if ($db->numrows($respos)>0) {
						$fresu=$db->fetchrow($respos);
						$maxi=$fresu['maxi'];
					}
					else $maxi=1;

					echo "<select id=\"fieldnew_position\" name=\"fieldnew_position\">";

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
				<td align="right"><? echo $_DIMS['cste']['_SYSTEM_LABELICON_INDEX']; ?></td>
				<td>
					<?
					if (isset($mbfields_used[$metafield->fields['id_mbfield']]['indexed'])
							&& $mbfields_used[$metafield->fields['id_mbfield']]['indexed']) $valindex="checked";
					else $valindex="";
					?>
					<input type="checkbox" name="is_indexed" <? echo $valindex;?>>
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
				?>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_TYPE']; ?></td>
				<td>
					<select class="select" name="field_type" onchange="javascript:display_fieldvalues();display_fieldformats();display_fieldcols();display_tablelink();">
					<?
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
					$restype=$db->query("SELECT distinct type from dims_mod_business_enum order by type");
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
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?></td>
				<td><input type="text" class="text" size="30" name="field_defaultvalue" value="<? echo ($metafield->fields['defaultvalue']); ?>"></td>
			</tr>
			<tr>
				<td align="right">Options: </td>
				<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="checkbox" name="field_option_needed" value="1" <? if ($metafield->fields['option_needed']) echo 'checked'; ?>></td>
						<td align="left"><? echo $_DIMS['cste']['_FIELD_NEEDED']; ?></td>
					</tr><tr>
						<td><input type="checkbox" name="field_option_search" value="1" <? if ($metafield->fields['option_search']) echo 'checked'; ?>></td>
						<td align="left"><? echo $_DIMS['cste']['_SEARCH']; ?></td>
					</tr><tr>
						<td><input type="checkbox" name="field_option_exportview" value="1" <? if ($metafield->fields['option_exportview']) echo 'checked'; ?>></td>
						<td align="left"><? echo $_DIMS['cste']['_FORMS_FIELD_EXPORTVIEW']; ?></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="right"><? echo $_DIMS['cste']['_DIMS_MODE']; ?></td>
				<td>
					<select class="select" name="field_mode">
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
				<?
				$res=$db->query("
							SELECT	forms.label, field.*
							FROM	dims_mod_forms forms,
									dims_mod_forms_field field
							WHERE	forms.id_module = :moduleid
							AND		forms.id = field.id_forms
							AND		field.separator = 0
							ORDER BY label, position
							", array(
					':moduleid' => $_SESSION['dims']['moduleid']
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

	<table cellpadding="0" cellspacing="0" bgcolor="<? echo $skin->values['colsec']; ?>" width="100%"><tr><td height="1"></td></tr></table>

	<table cellpadding="2" cellspacing="1" width="100%" bgcolor="<? echo $skin->values['bgline1']; ?>">
	<tr>
		<td align="right">
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv().'?'.$this->getMetierEnvList(); ?>'">&nbsp;
			<input type="submit" class="flatbutton" id="button_save" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"></td>
	</tr>
	</table>
	</form>

	<table cellpadding="0" cellspacing="0" bgcolor="<? echo $skin->values['colsec']; ?>" width="100%"><tr><td height="1"></td></tr></table>
</div>
<script type="text/javascript">
<?php include 'modules/system/include/javascript.php'; ?>
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


