<table style="width:100%">
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_STYLE']; ?></td>
		<td>
			<select id="wce_block_border-style" name="wce_block_border-style">
			<?
			  foreach($borderB as $elemp) {
				  $selp='';
				  if ($this->fields['border-style']==$elemp) {
					  $selp='selected="selected"';
				  }
				  echo '<option value="'.$elemp.'" '.$selp.'>'.$elemp.'</option>';
			  }
			 ?>
			 </select>
		</td>
		<td style="width:20%;height:30px;">Size</td>
		<td>
			<select id="wce_block_border-size" name="wce_block_border-size">
			<?
			 for($id=0;$id<=20;$id++) {
				 $selp='';
				 if ($this->fields['border-size']==$id) {
					 $selp='selected="selected"';
				 }
				 echo '<option value="'.$id.'px" '.$selp.'>'.$id.'</option>';
			 }
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:20%;height:30px;"><? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?></td>
		<td>
		 <?
			$txtcolor='';

			if ($this->fields['border-color']=='') {
				$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
			}
			else {
				if ($this->fields['border-color']=='#FFFFFF' || $this->fields['border-color']=='FFFFFF') {
					$txtcolor='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);';
				}
				elseif ($this->fields['border-color']=='#00000' || $this->fields['border-color']=='000000') {
					$txtcolor='background-color: rgb(0, 0, 0); color: rgb(255, 255, 255);';
				}
				else {
					$txtcolor='background-color: '.$this->fields['border-color'].'; color: rgb(0, 0, 0);';
				}
			}
			?>
			<input type="text" value="<? echo $this->fields['border-color']; ?>" id="wce_block_border-color" name="wce_block_border-color" style="<? echo $txtcolor; ?>">
		</td>
		<td style="width:20%;height:30px;"></td>
		<td>
		</td>
	</tr>
</table>
