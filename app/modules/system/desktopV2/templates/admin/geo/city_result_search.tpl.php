<div class="block-city" id="block_city_<?= $this->get('id'); ?>" dims-data-value="<?= $this->get('id'); ?>">
	<?= $this->get('label').(($this->get('cp')!='' && $this->get('cp')>0)?" (".$this->get('cp').")":(($this->get('insee')!='' && $this->get('insee') > 0)?" (".substr($this->get('insee'), 0,2).")":"")); ?>
	<a href="javascript:void(0);" class="add"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" /></a>
</div>
