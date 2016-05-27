<?php
$deb = explode('-',$this->fields['datejour']);
$datejour = '';
if($deb[2] != 0){
	$datejour .= $deb[2].'/';
}
if($deb[1] != 0){
	$datejour .= $deb[1].'/';
}
if($deb[0] != 0){
	$datejour .= $deb[0];
}
?>

<table style="width: 100%;">
	<tbody>
		<tr>

			<td style="width:70%">
				<!-- <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" /> -->
				<span><? echo $this->fields['libelle']; ?></span>
			</td>
			<td>
				<span><? echo $datejour; ?></span>
			</td>
			<td>
				<img onclick="javascript:$('#link').val(<?php echo $this->fields['id']; ?>);addEventInActivity(<?php echo $this->fields['id']; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />
				<!-- <img onclick="javascript:addEventInActivity(<? echo $this->fields['id']; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" /> -->
			</td>
	</tbody>
</table>
