<div class="wce_tree_node art<? echo $this->fields['id_heading']; ?>" style="<? echo ($this->getLightAttribute('opened'))?"font-weight:bold;":"font-weight:normal;"; ?><? echo ($this->getLightAttribute('displayed'))?"":"display:none;"; ?>">
	<?
	$depth = $this->getLightAttribute('depth');
	$prevLast = $this->getLightAttribute('previousLast');
	for($i=0;$i<$depth;$i++){
		if ($prevLast[$i]){
			?>
			<img src="./common/modules/wce/img/empty.png"/>
			<?
		}else{
			?>
			<img src="./common/modules/wce/img/line.png"/>
			<?
		}
	}
	if ($this->getLightAttribute('isLast')){
		?>
		<img src="./common/modules/wce/img/join.png">
		<?
	}else{
		?>
		<img src="./common/modules/wce/img/joinbottom.png">
		<?
	}
	if ($this->fields['uptodate']){
		?>
		<img src="./common/modules/wce/img/doc0.png"/>
		<?
	}else{
		?>
		<img src="./common/modules/wce/img/doc1.png"/>
		<?
	}
	?>
	<a href="javascript:void(0);" class="article" id="<? echo $this->fields['id']?>" title="<? echo $this->fields['title']; ?>">
		<? echo dims_strcut($this->fields['title'],45-($depth*8)); ?>
	</a>
</div>
