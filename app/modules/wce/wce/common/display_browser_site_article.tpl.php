<div class="wce_tree_node art<? echo $this->fields['id_heading']; ?>" style="<? echo ($this->getLightAttribute('opened'))?"font-weight:bold;":"font-weight:normal;"; ?><? echo ($this->getLightAttribute('displayed'))?"":"display:none;"; ?>">
	<?
	$depth = $this->getLightAttribute('depth');

	$prevLast = $this->getLightAttribute('previousLast');
	for($i=0;$i<$depth;$i++){
		if (isset($prevLast[$i]) && $prevLast[$i]){
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
	<a href="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub']).((isset($sub))?"&sub=$sub".((isset($action))?"&action=$action":""):"")."&headingid=".$this->fields['id_heading']."&articleid=".$this->fields['id']; ?>">
		<? echo dims_strcut($this->fields['title'],40-($depth*12)); ?>
	</a>
	<div style="float:right;margin-left:5px;">
		<?
		foreach($this->getListArticleLangVersionWCE() as $lang){
			?>
			<img src="<? echo $lang->getFlag(); ?>" alt="<? echo $lang->getLabel(); ?>" title="<? echo $lang->getLabel(); ?>" />
			<?
		}
		?>
	</div>
</div>
