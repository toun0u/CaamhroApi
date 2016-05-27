<?
if (!isset($colorfield)) $colorfield='';
$style = 'style="'.$colorfield.'"';
$style_sel = 'style="'.$colorfield.'"';

if (!is_array($fields['values'])) {
	$values = explode('||',$fields['values']);
}
if (!isset($ind_categ) || $ind_categ=='') $ind_categ=0;
if (!isset($ind_field) || $ind_field=='') $ind_field=0;

if(!isset($op)) $op = '';
switch($fields['type']) {
	case 'textarea':
		if ($op == 'display') {
			if (isset($replies[$fields['id']][0])) echo dims_nl2br($replies[$fields['id']][0]);
		}
		else {
			?>
			<textarea class="dynamic_field_textarea" <? echo $style; ?> onkeypress="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)"  name="fck_field<? echo $fields['id']; ?>" id="field<? echo $fields['id']; ?>" class="text" rows="5"><? if (isset($replies[$fields['id']][0])) echo $replies[$fields['id']][0]; ?></textarea>
			<?
			$token->field("fck_field".$fields['id']);
		}

	break;

	case 'select':
		if ($op == 'display') {
			if (isset($replies[$fields['id']][0])) echo $replies[$fields['id']][0];
		}
		else {
			?>
			<select class="dynamic_field_select" <? echo $style_sel; ?> name="fck_field<? echo $fields['id']; ?>" onchange="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)" id="field<? echo $fields['id']; ?>" class="select">
			<? $token->field("fck_field".$fields['id']); ?>
			<option></option>
			<?
			if (!is_array($fields['values'])) {
				foreach($values as $value) {
					$selected = (isset($replies[$fields['id']]) && $value == $replies[$fields['id']][0])? 'selected' : '';
					?>
					<option <? echo $selected; ?> value="<? echo $value; ?>"><? echo $value; ?></option>
					<?
				}
			}
			else {
				// afichage specifique pour code nace, pays, etc.
				foreach($values as $k=> $elem) {
					if (isset($elem['value']) && isset($elem['key'])) {
						$value=$elem['value'];
						$key=$elem['key'];
						$selected = (isset($replies[$fields['id']]) && $key == $replies[$fields['id']][0])? 'selected' : '';
						?>
						<option <? echo $selected; ?> value="<? echo $key; ?>"><? echo $value; ?></option>
						<?
					}
				}
			}
			?>
			</select>
			<?
		}
	break;

	case 'color':
		if ($op == 'display') {
			if (isset($replies[$fields['id']][0])) echo $replies[$fields['id']][0];
		}
		else {
			?>
			<select class="select" <? echo $style; ?> id="fck_field<? echo $fields['id']; ?>" onkeypress="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)"  name="field<? echo $fields['id']; ?>" class="select" onchange="this.style.backgroundColor=this.value;" style="background-color:<? echo $replies[$fields['id']][0]; ?>">
			<? $token->field("field".$fields['id']); ?>
			<option></option>
			<?
			foreach($values as $value) {
				$selected = (isset($replies[$fields['id']]) && $value == $replies[$fields['id']][0])? 'selected' : '';
				?>
				<option <? echo $selected; ?> value="<? echo $value; ?>" style="background-color:<? echo $value; ?>;color:<? echo $value; ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
				<?
			}
			?>
			</select>
			<?
		}
	break;

	case 'checkbox':
		?>
		<table cellpadding="0" cellspacing="0">
		<tr>
		<?
		$s = sizeof($values);
		$v = 0;
		for ($c=$fields['cols'];$c>=1;$c--) {// for each column
			$d = (($s-($s%$c))/$c) + ($s%$c>0); // nb element to place in current column
			echo "<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\">";
			for($i=$v;$i<$v+$d;$i++) {
				$value = $values[$i];
				$checked = (isset($replies[$fields['id']]) && in_array($value, $replies[$fields['id']]))? 'checked=\'checked\'' : '';
				echo "<tr>";
				if ($op == 'display') {
					if ($checked == 'checked') {
						?>
						<td><img src="./common/modules/forms/img/checked.gif"></td>
						<?
					}
					else {
						?>
						<td><img src="./common/modules/forms/img/unchecked.gif"></td>
						<?
					}
				}
				else {
					?>
					<td>
						<input style="width:32px;"  type="checkbox" onclick="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)"  id="field<? echo $fields['id']."_".$i; ?>" name="field<? echo $fields['id']; ?>[]" value="<? echo $value; ?>" "<? echo $checked; ?>">
						<? $token->field("field".$fields['id']); ?>
					</td>
					<?
				}
				?>
				<td><? echo $value; ?></td>

				<?
				echo "</tr>";
			}
			echo "</table></td>";
			$v += $d;
			$s -= $d; // element to place for next columns
		}
		?>
		</tr>
		</table>
		<?
	break;

	case 'radio':
		?>
		<table cellpadding="0" cellspacing="0">
		<tr>
		<?
		$s = sizeof($values);

		$v = 0;
		for ($c=$fields['cols'];$c>=1;$c--) // for each column
		{
			$d = (($s-($s%$c))/$c) + ($s%$c>0); // nb element to place in current column
			echo "<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\">";
			for($i=$v;$i<$v+$d;$i++)
			{
				$value = $values[$i];
				$checked = (isset($replies[$fields['id']]) && is_array($replies[$fields['id']]) && in_array($value, $replies[$fields['id']]))? 'checked' : '';
				echo "<tr>";
				?>

					<?
					if ($op == 'display')
					{
						if ($checked == 'checked')
						{
							?>
							<td><img src="./common/modules/forms/img/checked.gif"></td>
							<?
						}
						else
						{
							?>
							<td><img src="./common/modules/forms/img/unchecked.gif"></td>
							<?
						}
					}
					else
					{
						?>
						<td><input <? echo $checked; ?> style="width:32px;" type="radio" onclick="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)"  id="field<? echo $fields['id']; ?>[]" name="field<? echo $fields['id']; ?>[]" value="<? echo $value; ?>"></td>
						<?
						$token->field("field".$fields['id']);
					}
					?>
					<td><? echo $value; ?></td>

				<?
				echo "</tr>";
			}
			echo "</table></td>";
			$v += $d;
			$s -= $d; // element to place for next columns
		}
		?>
		</tr>
		</table>
		<?
	break;

	case 'file':
		if ($op == 'display') {
			if (isset($replies[$fields['id']][0])) echo $replies[$fields['id']][0];
		}
		else {
			if (isset($fields['protected']) && $fields['protected']==1 && $fields['namefield']=="photo") {
				echo "<input type=\"file\" id=\"photo\" name=\"photo\" class=\"dynamic_field_photo\">";
				$token->field("photo");
			}
			else {
				echo "<input type=\"file\" id=\"field".$fields['id']."\" name=\"field".$fields['id']."\" class=\"dynamic_field_photo\">";
				$token->field("field".$fields['id']);
				if (isset($replies[$fields['id']][0]) && $replies[$fields['id']][0] != '') {
					echo $replies[$fields['id']][0].'<a href="'."{$scriptenv}?op=download_file&replyfield_id={$replies[$fields['id']][0]}".'"><img style="border:0px" src="./common/modules/forms/img/link.gif"></a>';
				}
			}
		}
	break;

	break;

	case 'autoincrement':
		if (isset($replies[$fields['id']][0]) && $replies[$fields['id']][0] != '') {
			echo $replies[$fields['id']][0];
		}
		else {
			$select = "SELECT max(value*1) as maxinc FROM dims_mod_forms_reply_field WHERE id_forms = :idforms AND id_field = :idfield ";
			$rs_maxinc = $db->query($select, array(
				':idforms' => $forms_id,
				':idfield' => $fields['id']
			));
			$fields = $db->fetchrow($rs_maxinc);
			$maxinc = ($fields['maxinc'] == '' || $fields['maxinc'] == 0) ? 1 : $fields['maxinc']+1;
			echo "$maxinc (À valider)";
		}
	break;

	default:
	case 'text':
		if ($op == 'display') {
			if (isset($replies[$fields['id']][0])) echo "<span id=\"field".$fields['id']."\" class=\"text\">".$replies[$fields['id']][0]."</span>";
		}
		else {
			$maxlength = ($fields['maxlength'] > 0 && $fields['maxlength'] != '') ? $fields['maxlength'] : '50';

			if ($fields['format'] == 'date') {
				?>
				<span style="float:left;">
					<input type="text" <? echo $style; ?> id="field<? echo $fields['id']; ?>" onkeypress="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)" name="fck_field<? echo $fields['id']; ?>" id="field_<? echo $fields['id']; ?>
					" value="<? if (isset($replies[$fields['id']][0])){
							$date = dims_timestamp2local($replies[$fields['id']][0]);
							echo $date['date'];
						    }
						 ?>" class="text" maxlength="<? echo $maxlength; ?>"
				</span>
				<? $token->field("fck_field".$fields['id']); ?>
				<span style="float:left;">
					<a href="javascript:calend.select(document.form_ct.field<? echo $fields['id']; ?>,'field<? echo $fields['id']; ?>link','dd/MM/yyyy');" name="field<? echo $fields['id']; ?>link" id="field<? echo $fields['id']; ?>link">
					<? $token->field("field".$fields['id']); ?>
						<img src="./common/modules/system/img/calendar.gif" width="31" height="18" align="top" border="0" style="position:relative">
					</a>
				</span>
				<?
			}
			else {
				?>
				<input  <? echo $style; ?>  style="<? echo $colorfield;?>" onkeypress="javascript:updateLine(<? echo $fields['id'].",".$ind_categ.",".$ind_field; ?>)" type="text" <? echo $style; ?> id="field<? echo $fields['id']; ?>" name="fck_field<? echo $fields['id']; ?>" value="<? if (isset($replies[$fields['id']][0])) echo $replies[$fields['id']][0]; ?>" class="dynamic_field_text" maxlength="<? echo $maxlength; ?>">
				<? $token->field("fck_field".$fields['id']); ?>
				<?
			}
		}
	break;
}

if (isset($field_formats[$fields['format']]) && $fields['type'] == 'text') {
		switch ($fields['format']) {
			case 'date':
				echo '(jj/mm/aaaa)';
			break;

			case 'heure':
				echo '(hh:mm)';
			break;
		}
}

?>
