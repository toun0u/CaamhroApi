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
				<td class="label_field"><label for="published"><?= $this->get_field_label('published'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('published'); ?></td>
				<td class="label_field"><label for="families"><?= $this->get_field_label('families'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('families'); ?></td>
			</tr>

			<tr>
				<td class="label_field"><label for="type"><?= $this->get_field_label('type'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('type'); ?></td>
				<td class="label_field"><label for="unattached"><?= $this->get_field_label('unattached', 1); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('unattached',0); ?><?= $this->get_field_html('unattached', 1); ?></td>
			</tr>

			<tr>
				<td class="label_field"><label for="in_clipboard"><?= $this->get_field_label('in_clipboard',1); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('in_clipboard',0); ?><?= $this->get_field_html('in_clipboard',1); ?></td>
				<td class="label_field"><label for="keywords"><?= $this->get_field_label('keywords'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('keywords'); ?></td>
			</tr>

			<?php
			$filters = $view->get('filters');
			if (sizeof($filters)) {
				?>
				<tr><td colspan="4">&nbsp;</td></tr>
				<tr>
					<?php
					$i = 0;
					foreach ($filters as $filter) {
						?>
						<td class="label_field"><label for=""><?= $this->get_field_label('filter'.$filter['filter']->get('id')); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('filter'.$filter['filter']->get('id')); ?></td>
						<?php
						$i++;
						if ($i % 2 == 0) {
							echo '</tr><tr>';
						}
					}
					?>
				</tr>
				<?php
			}
			?>
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
                url: "<?= get_path('articles', 'switch_filters'); ?>",
                data: {
                	'mode': mode
                }
            });
	}
</script>
