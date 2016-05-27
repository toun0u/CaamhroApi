<div class="tag">
	<?php
	$from_desktop = $this->getLightAttribute('from_desktop');
	if(isset($from_desktop) && $from_desktop) $fd = '&from_desktop=1';
	else $fd = '';
	if ($this->getLightAttribute('from_concept')) {
		?>
		<a href="admin.php?dims_op=desktopv2&action=selectConceptTag&tag=<?php echo $this->getId().$fd; ?>" >
			<span><? echo $this->fields['tag']; ?></span>
		<?php

		if (isset($_SESSION['desktop']['concept']['tags']) && in_array($this->getId(),$_SESSION['desktop']['concept']['tags'])){
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_plein.png" />
			<?
		}else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_vide.png" />
			<?
		}
	}
	elseif ($this->getLightAttribute('from_fiche')) {
		?>
		<a href="admin.php?dims_op=desktopv2&action=attachTag&tag=<?php echo $this->getId().$fd; ?>&id_fiche=<?php echo $this->getLightAttribute('id_fiche'); ?>&type_fiche=<?php echo $this->getLightAttribute('type_fiche'); ?>" >
			<span><? echo $this->fields['tag']; ?></span>
		<?php

		if (isset($_SESSION['desktop']['concept']['tags']) && in_array($this->getId(),$_SESSION['desktop']['concept']['tags'])){
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_plein.png" />
			<?
		}else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_vide.png" />
			<?
		}
	}
	else {
		?>
		<a href="admin.php?dims_op=desktopv2&action=selectTag&tag=<?php echo $this->getId().$fd; ?>" >
			<span><? echo $this->fields['tag']; ?></span>
		<?php

		if (isset($_SESSION['desktop']['search']['tags']) && in_array($this->getId(),$_SESSION['desktop']['search']['tags'])){
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_plein.png" />
			<?
		}else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/tag_vide.png" />
			<?
		}
	}
	?>
	</a>
	<?php
	if($this->getLightAttribute('delete_button')){
		?>
		<a href="admin.php?dims_op=desktopv2&action=selectTag&tag=<?php echo $this->getId(); ?>" >
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/remove_tag.png" />
		</a>
		<?php
	}
	elseif($this->getLightAttribute('detach_button')) {
		?>
		<a href="admin.php?dims_op=desktopv2&action=detachTag&tag=<?php echo $this->getId(); ?>&id_fiche=<?php echo $this->getLightAttribute('id_fiche'); ?>&type_fiche=<?php echo $this->getLightAttribute('type_fiche'); ?>" >
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/remove_tag.png" />
		</a>
		<?php
	}
	?>
</div>
