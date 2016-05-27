<table style="width:100%">
	<tr>
		<td style="width:20%;height:30px;">Id</td>
		<td>
			<input type="text" disabled size="1" value='<? echo $this->fields['id']; ?>' id="wce_block_id" name="wce_block_id">
		</td>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_WIDTH']; ?> :</td>
		<td>
			<input type="text" size="8" value="<? echo $this->fields['width']; ?>" id="wce_block_width" name="wce_block_width">
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_POSITION']; ?></td>
		<td>
			<select id="wce_block_position" name="wce_block_position">
				<?
				foreach($positionB as $elemp) {
					$selp='';
					if ($this->fields['position']==$elemp) {
						$selp='selected="selected"';
					}
					echo '<option value="'.$elemp.'" '.$selp.'>'.$elemp.'</option>';
				}
				?>
			</select>
		</td>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_HEIGHT']; ?> :</td>
		<td>
			<input type="text" size="8" value='<? echo $this->fields['height']; ?>' id="wce_block_height" name="wce_block_height">
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_DISPLAY']; ?></td>
		<td>
			<select id="wce_block_display" name="wce_block_display">
				<?
				foreach($displayB as $elemp) {
					$selp='';
					if ($this->fields['display']==$elemp) {
						$selp='selected="selected"';
					}
					echo '<option value="'.$elemp.'" '.$selp.'>'.$elemp.'</option>';
				}
				?>
			</select>
		</td>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_MARGIN']; ?> :</td>
		<td>
			<input type="text" size="16" value='<? echo $this->fields['margin']; ?>' id="wce_block_margin" name="wce_block_margin">
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;">Float :</td>
		<td>
			<select id="wce_block_float" name="wce_block_float">
			<?
			foreach($floatB as $elemp) {
				$selp='';
				if ($this->fields['float']==$elemp) {
					$selp='selected="selected"';
				}
				echo '<option value="'.$elemp.'" '.$selp.'>'.$elemp.'</option>';
			}
			?>
            </select>
		</td>
		<td style="width:20%;height:30px;">Padding :</td>
		<td>
			<input type="text" size="16" value='<? echo $this->fields['padding']; ?>' id="wce_block_padding" name="wce_block_padding">
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;">Class :</td>
		<td colspan="3">
			<input type="text" size="72" value='<? echo $this->fields['class']; ?>' id="wce_block_class" name="wce_block_class">
		</td>
	</tr>
</table>
