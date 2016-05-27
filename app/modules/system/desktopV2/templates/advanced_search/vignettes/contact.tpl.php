<?php
$mode = $this->getLightAttribute('mode');
?>

<div class="bloc item">
	<div class="as_picto">
	<?php
	$file = $this->getPhotoPath(60);//real_path
	if(file_exists($file)){
		?>
		<img src="<?php echo $this->getPhotoWebPath(60); ?>">
		<?php
	}
	else{
		?>
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/contact_default_search.png">
		<?php
	}
	?>
	</div>
	<div>
		<span class="label">
			<?php
			if ($mode == 'concept') {
				$filter_type = $this->getLightAttribute('filter_type');
				?>
				<a class="remove_item" href="/admin.php?action=drop_filter&filter_type=<?php echo $filter_type; ?>&filter_value=<?php echo $this->fields['id_globalobject']; ?>" title="delete this filter">
				<?php
			}
			else {
				?>
				<a class="remove_item" href="/admin.php?dims_op=desktopv2&action=as_managefilter&faction=del&type=contact&val=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			?>
				<span><?php echo dims_strcut(strtoupper(substr($this->fields['firstname'],0,1)).'. '.$this->fields['lastname'], 15); ?></span>
			</a>
		</span>
	</div>
</div>
