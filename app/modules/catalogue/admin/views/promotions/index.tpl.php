<?php $view = view::getInstance(); ?>
<a href="<?= get_path('promotions', 'index'); ?>" class="a_h1">
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/promos50x30.png'); ?>">
		<?= dims_constant::getVal('CATA_PROMOTIONS_MANAGEMENT'); ?>
	</h1>
</a>

<?php
$ad_js = <<< ADDITIONAL_JS
$("select#families").chosen({allow_single_deselect:true});
$("select#status").chosen({allow_single_deselect:true});
$("select#type").chosen({allow_single_deselect:true});

$("#filter_articles select").attr('onchange', 'javascript:document.filter_articles.submit();');

$('#select_all').click(function(){
	if($(this).is(':checked')){
		$('.art_check').attr("checked", "checked");
	}
	else $('.art_check').removeAttr("checked");
});
ADDITIONAL_JS;

$a_status = array();


$form = new Dims\form(array(
		'name' 			=> 'promotions',
		'action'		=> get_path('promotions','index'),
		'validation'		=> false,
		'back_name'		=> dims_constant::getVal('REINITIALISER'),
		'back_url'		=> get_path('promotions','index', array('filter_init' => 1) ),
		'submit_value'		=> dims_constant::getVal('_DIMS_FILTER'),
		'include_actions' 	=> false,
		'additional_js'		=> $ad_js
	));

$filters = $form->addBlock ('promotions', '', $view->getTemplatePath('promotions/promotions_filters_block.tpl.php'));
$filters->setForm($form);

$selected_type = $view->get('status');
$form->add_select_field(array(
		'name' 			=> 'status',
		'block'			=> 'promotions',
		'label'			=> dims_constant::getVal('STATUS'),
		'value'			=> isset($selected_type) ? $selected_type : -1,
		'classes'		=> 'statut_select'
	));

$keywords = $view->get('keywords');
$form->add_text_field(array(
		'name'			=> 'keywords',
		'block'			=> 'promotions',
		'label'			=> dims_constant::getVal('_WCE_KEYWORDS_META'),
		'value'			=> isset($keywords) ? $keywords : '',
	));

$selected_promotions = $view->get('type');
$form->add_select_field(array(
		'name' 			=> 'type',
		'block'			=> 'promotions',
		'label'			=> dims_constant::getVal('_TYPE'),
		'value'			=> isset($selected_promotions) ? $selected_promotions : -1,
	));

$form->build();

$promotions = $view->get('promotions');

// TODO Gérer les promos comme dans le PDF
if ($view->get('total_promotions') > 0) {
	?>
	<div class="zone_filter">
		<div class="result_criteres">
			<span class="count"><?= $view->get('total_promotions'); ?></span> <?= dims_constant::getVal('PROMOS_MATCH_YOUR_CRITERIA'); ?>
		</div>
	</div>
	<table class="tableau">
		<tr>
			<td>&nbsp;</td>
			<td><?= dims_constant::getVal('_BUSINESS_FIELD_NAME'); ?></td>
			<td><?= dims_constant::getVal('PAGINATION_FIRST'); ?></td>
			<td><?= dims_constant::getVal('_END'); ?></td>
			<td><?= dims_constant::getVal('ACTIVATION_CODE'); ?></td>
			<td><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></td>
		</tr>
		<?php
		foreach ($promotions as $promo) {
			?>
			<tr>
				<td class="center">
					<?php
					if ($promo->isActive()) {
						?>
						<a href="<?= get_path('promotions', 'deactivate', array('id' => $promo->get('id'))); ?>" title="<?= dims_constant::getVal('DEACTIVATE_THIS_PROMO'); ?>">
							<img src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" alt="<?= dims_constant::getVal('PROMO_ACTIVATED'); ?>" />
						</a>
						<?php
					}
					else {
						?>
						<a href="<?= get_path('promotions', 'activate', array('id' => $promo->get('id'))); ?>" title="<?= dims_constant::getVal('ACTIVATE_THIS_PROMO'); ?>">
							<img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" alt="<?= dims_constant::getVal('PROMO_NOT_ACTIVATED'); ?>" />
						</a>
						<?php
					}
					?>
				</td>
				<td><?= $promo->getLibelle(); ?></td>
				<td class="center"><?= $promo->getDateDebutFormatted(); ?></td>
				<td class="center"><?= $promo->getDateFinFormatted(); ?></td>
				<td class="center"><?= $promo->getCode(); ?></td>
				<td class="center">
					<a href="<?= get_path('promotions', 'edit', array('id' => $promo->get('id'))); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>">
						<img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" /></a>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= get_path('promotions', 'delete', array('id' => $promo->get('id'))); ?>', '<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>');" title="<?= dims_constant::getVal('DELETE_THIS_PROMO'); ?>">
						<img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}
else{
	?>
	<div class="div_no_elem"><?= dims_constant::getVal('NO_RESULT'); ?></div>
	<?php
}
