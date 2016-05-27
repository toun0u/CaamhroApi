<div class="wce_tree_node rub<? echo $this->fields['id_heading']; ?>" style="<? echo ($this->getLightAttribute('opened'))?"font-weight:bold;":"font-weight:normal;"; ?><? echo ($this->getLightAttribute('displayed'))?"":"display:none;"; ?>">
	<?
	$depth = count(explode(';',$this->fields['parents']))-2;
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
	if (count($this->getLstRubriques()) > 0 || count($this->getLstArticles()) > 0){
		?>
		<a href="javascript:void(0);" class="show" name="<? echo $this->fields['id']; ?>">
			<?
			if ($this->getLightAttribute('childOpened')){
				if ($this->getLightAttribute('isLast')){
					?>
					<img src="./common/modules/wce/img/minus.png">
					<?
				}else{
					?>
					<img src="./common/modules/wce/img/minusbottom.png">
					<?
				}
			}else{
				if ($this->getLightAttribute('isLast')){
					?>
					<img src="./common/modules/wce/img/plus.png">
					<?
				}else{
					?>
					<img src="./common/modules/wce/img/plusbottom.png">
					<?
				}
			}
			?>
		</a>
		<?
	}else{
		if ($this->getLightAttribute('isLast')){
			?>
			<img src="./common/modules/wce/img/join.png">
			<?
		}else{
			?>
			<img src="./common/modules/wce/img/joinbottom.png">
			<?
		}
	}
	?>
	<img style="margin-right: 4px;" src="<? echo module_wce::getTemplateWebPath('gfx/dossier16.png'); ?>" />
	<a title="<? echo $this->fields['label']; ?>" href="javascript:void(0);" class="heading" id="<? echo $this->fields['id']?>">
		<? echo dims_strcut($this->fields['label'],45-($depth*8)); ?>
	</a>
</div>
