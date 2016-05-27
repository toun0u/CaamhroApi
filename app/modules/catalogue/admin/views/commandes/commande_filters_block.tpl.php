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
			if( $mode = $view->get('filters_mode') ){
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
	<div id="filters" class="sub_bloc_form" <?php if (!$mode) echo 'style="display:none;"'; ?>>
		<?= $this->get_field_html('page'); #permet de réinialiser à 0 la page après submit du formulaire ?>
		<?= $this->get_field_html('sort_way'); ?>
		<?= $this->get_field_html('sort_by'); ?>
		<table>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('date_deb'); ?>"><?= $this->get_field_label('date_deb'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('date_deb'); ?>
					<img style="vertical-align:middle;cursor:pointer;" src="<?= $view->getTemplateWebPath('gfx/calendrier16.png'); ?>" alt="<?= $this->get_field_label('date_deb'); ?>" onclick="javascript:dims_calendar_open('<?= $this->get_field_id('date_deb'); ?>', event);" />
				</td>
				<td class="label_field">
					<label for="<?= $this->get_field_id('date_deb'); ?>"><?= $this->get_field_label('date_fin'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('date_fin'); ?>
					<img style="vertical-align:middle;cursor:pointer;" src="<?= $view->getTemplateWebPath('gfx/calendrier16.png'); ?>" alt="<?= $this->get_field_label('date_fin'); ?>" onclick="javascript:dims_calendar_open('<?= $this->get_field_id('date_fin'); ?>', event);" />
				</td>
				<td class="label_field">
					<label for="<?= $this->get_field_id('client'); ?>"><?= $this->get_field_label('client'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('client'); ?>
				</td>
			</tr>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('state'); ?>"><?= $this->get_field_label('state'); ?></label>
				</td>
				<td class="value_field" colspan="3">
					<?= $this->get_field_html('state'); ?>
				</td>
				<td class="label_field">
					<label for="<?= $this->get_field_id('keywords'); ?>"><?= $this->get_field_label('keywords'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('keywords'); ?>
				</td>
			</tr>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('payment'); ?>"><?= $this->get_field_label('payment'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('payment'); ?>
				</td>
				<td class="label_field"></td>
				<td class="value_field"></td>
			</tr>
		</table>
		<?php
		$this->getForm()->displayActionsBlock();
		?>
	</div>
</div>

<script type="text/javascript">
	function switch_filters(){
		if($('#filters').is(':visible')){
			$('#filters').hide();
			$('a.mask_filters span').text('<?= dims_constant::getVal('_SHOW_FILTERS');?>');
			$('a.mask_filters img').attr('src', '<?= $view->getTemplateWebPath('gfx/afficher16.png'); ?>');
		}
		else{
			$('#filters').show();
			$('a.mask_filters span').text('<?= dims_constant::getVal('_MASK_FILTERS');?>');
			$('a.mask_filters img').attr('src', '<?= $view->getTemplateWebPath('gfx/masquer16.png'); ?>');
		}
		//Appel ajax pour stocker ça en session
		$.ajax({
				type: "POST",
				url: "<?= get_path('commandes', 'switch_filters'); ?>"
			});
	}
</script>
