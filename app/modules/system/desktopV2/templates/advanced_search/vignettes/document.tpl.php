<?php
$mode = $this->getLightAttribute('mode');
?>

<div class="bloc item">
	<div class="as_picto">
	<?php
	switch($this->getSearchableType()){
		case search::RESULT_TYPE_DOCUMENT:
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60.png"/>
			<?php
			break;
		case search::RESULT_TYPE_PICTURE:
			if(file_exists($this->getPicturePath(60))){
					?>
					<img src="<?php echo $this->getPictureWebPath(60);?>" />
					<?php
				}
			else
			{
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60picture.png"/>
				<?php
			}
			break;
		case search::RESULT_TYPE_MOVIE:
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60movie.png"/>
			<?php
			break;
	}
	?>
	</div>
	<div>
		<span class="label">
			<?php
			if ($mode == 'concept') {
				$filter_type = $this->getLightAttribute('filter_type');
				?>
				<a class="remove_item" href="/admin.php?action=drop_filter&filter_type=doc&filter_value=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			else {
				?>
				<a class="remove_item" href="/admin.php?dims_op=desktopv2&action=as_managefilter&faction=del&type=document&val=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			?>
				<span><?php echo dims_strcut($this->fields['name'],15); ?></span>
			</a>
		</span>
	</div>
</div>
