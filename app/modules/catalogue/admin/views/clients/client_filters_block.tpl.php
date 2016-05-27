<?php $view = view::getInstance(); ?>
<div class="sub_bloc">
	<div class="sub_header">
		<?php
		$title = $this->getTitle();
		if (!empty($title)) {
			?>
			<h2><?php echo $title; ?></h2>
			<?php
		}
		?>
		<a class="mask_filters link_img" href="javascript:void(0);" onclick="javascript:switch_filters();">
			<?php
			$mode = $view->get('filters_mode');
			if( $mode == 'show' ){
				?>
				<img src="<?= $view->getTemplateWebPath('gfx/masquer16.png'); ?>" />
				<span><?= dims_constant::getVal('_MASK_FILTERS'); ?></span>
				<?php
			}
			else{
				?>
				<img src="<?= $view->getTemplateWebPath('gfx/afficher16.png'); ?>" />
				<span><?= dims_constant::getVal('_SHOW_FILTERS'); ?></span>
				<?php
			}
		?>
		</a>
	</div>
	<div id="filters" class="sub_bloc_form" <?php if ($mode!= 'show') echo 'style="display:none;"'; ?>>
		<?= $this->get_field_html('page'); #permet de réinialiser à 0 la page après submit du formulaire ?>
		<table>
			<tr>
				<td class="label_field"><label for="status"><?= $this->get_field_label('status'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('status'); ?></td>
				<td class="label_field"><label for="keywords"><?= $this->get_field_label('keywords'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('keywords'); ?></td>
			</tr>
		</table>
		<?php
		$this->getForm()->displayActionsBlock();
		?>
	</div>
</div>

<script type="text/javascript">
	function switch_filters(){
		var mode = '';
		if($('#filters').is(':visible')){
			$('#filters').hide();
			$('a.mask_filters span').text('<?= dims_constant::getVal('_SHOW_FILTERS');?>');
			$('a.mask_filters img').attr('src', '<?= $view->getTemplateWebPath('gfx/afficher16.png'); ?>');
			mode = 'mask';
		}
		else{
			$('#filters').show();
			$('a.mask_filters span').text('<?= dims_constant::getVal('_MASK_FILTERS');?>');
			$('a.mask_filters img').attr('src', '<?= $view->getTemplateWebPath('gfx/masquer16.png'); ?>');
			mode = 'show';
		}
		//Appel ajax pour stocker ça en session
		$.ajax({
                type: "POST",
                url: "<?= get_path('clients', 'switch_filters'); ?>",
                data: {
                	'mode': mode
                }
            });
	}
</script>
