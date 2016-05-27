<table style="width:100%">
	<tr>
		<td style="width:20%;height:30px;">Colour</td>
		<td>
		 <?
			$txtcolor='';

			if ($this->fields['background-color']=='') {
				$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
			}
			else {
				if ($this->fields['background-color']=='#FFFFFF' || $this->fields['background-color']=='FFFFFF') {
					$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
				}
				elseif ($this->fields['background-color']=='#00000' || $this->fields['background-color']=='000000') {
					$txtcolor='background-color: rgb(0, 0, 0); color: rgb(255, 255, 255);';
				}
				else {
					$txtcolor='background-color: '.$this->fields['background-color'].'; color: rgb(0, 0, 0);';
				}
			}
			?>
			<input type="text" value="<? echo $this->fields['background-color']; ?>" id="wce_block_border-color" name="wce_block_background-color" style="<? echo $txtcolor; ?>">
		</td>
		<td style="width:20%;height:30px;"></td>
		<td>
		</td>
	</tr>
</table>
