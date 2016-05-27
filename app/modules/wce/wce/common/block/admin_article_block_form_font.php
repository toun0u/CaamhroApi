<table style="width:100%">
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_FAMILY']; ?></td>
		<td>
			<input type="text" size="18" value="<? echo $this->fields['font-family']; ?>" id="wce_block_font-family" name="wce_block_font-family">
		</td>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_WEIGHT']; ?></td>
		<td>
			<select id="wce_block_font-weight" name="wce_block_font-weight">
				<option selected="selected" value=""></option>
				<option value="normal"><? echo $_SESSION['cste']['_DIMS_LABEL_CONT_VIP_N']; ?></option>
				<option value="bold"><? echo $_SESSION['cste']['_BOLD']; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_SIZE']; ?></td>
		<td>
		<input type="text" size="6" value="" id="wce_block_font-size" name="wce_block_font-size">
		</td>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?></td>
		<td>
		<?
			$txtcolor='';
			if ($this->fields['color']=='') {
				$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
			}
			else {
				if ($this->fields['color']=='#FFFFFF' || $this->fields['color']=='FFFFFF') {
					$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
				}
				elseif ($this->fields['color']=='#00000' || $this->fields['color']=='000000') {
					$txtcolor='background-color: rgb(0, 0, 0); color: rgb(255, 255, 255);';
				}
				else {
					$txtcolor='background-color: '.$this->fields['color'].'; color: rgb(0, 0, 0);';
				}
			}
			?>
			<input type="text" value="<? echo $this->fields['color']; ?>" id="wce_block_font-color" name="wce_block_color" style="<? echo $txtcolor; ?>">
		</td>
	</tr>
</table>
