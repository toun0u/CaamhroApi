<table style="width:100%">
	<tr>
		<td style="width:20%;height:30px;">Family</td>
		<td>
			<input type="text" size="18" value="<? echo $this->fields['font-family']; ?>" id="wce_block_font-family" name="wce_block_font-family">
		</td>
		<td style="width:20%;height:30px;">Weight</td>
		<td>
			<select id="wce_block_font-weight" name="wce_block_font-weight">
				<option selected="selected" value=""></option>
				<option value="normal">normal</option>
				<option value="bold">bold</option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;">Size</td>
		<td>
		<input type="text" size="6" value="" id="wce_block_font-size" name="wce_block_font-size">
		</td>
		<td style="width:20%;height:30px;">Color</td>
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
