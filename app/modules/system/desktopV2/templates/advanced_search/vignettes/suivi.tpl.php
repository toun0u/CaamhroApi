<?php
$mode = $this->getLightAttribute('mode');
?>

<div class="bloc item">
	<div class="as_picto">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60.png">
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
				<a class="remove_item" href="/admin.php?dims_op=desktopv2&action=as_managefilter&faction=del&type=suivi&val=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			?>
				<span><?php echo dims_strcut(strtoupper($this->fields['libelle']), 15); ?></span>
			</a>
		</span>
	</div>
</div>