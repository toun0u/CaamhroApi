<?php
$quotations = $this->get('quotations');

$view = view::getInstance();

$ad_js = <<< ADDITIONAL_JS
$("select#client").chosen({allow_single_deselect:true});
$("#filter_commandes select").attr('onchange', 'javascript:document.filter_commandes.submit();');


ADDITIONAL_JS;


$form = new Dims\form(array(
	'name'              => 'filter_commandes',
	'action'            => $this->get('action_path'),
	'validation'        => false,
	'back_name'         => dims_constant::getVal('REINITIALISER'),
	'back_url'          => $this->get('back_path'),
	'submit_value'      => dims_constant::getVal('_DIMS_FILTER'),
	'include_actions'   => false,
	'additional_js'     => $ad_js
));

$form->add_hidden_field(array(
	'name' => 'page',
	'value'=> 0
));

$form->add_text_field(array(
	'name'              => 'date_deb',
	'label'             => dims_constant::getVal('DATE_FROM'),
	'value'             => $this->get('date_deb'),
	'classes'           => 'dates'
));

$form->add_text_field(array(
	'name'              => 'date_fin',
	'label'             => dims_constant::getVal('DATE_TO_THE'),
	'value'             => $this->get('date_fin'),
	'classes'           => 'dates'
));

$states = $this->get('states');
$form->add_select_field(array(
	'name'              => 'state',
	'label'             => dims_constant::getVal('_STATE'),
	'options'           => $states,
	'value'             => $this->get('state')
));

$form->add_select_field(array(
	'name'              => 'payment',
	'label'             => dims_constant::getVal('_PAYMENT_MEAN'),
	'options'           => $this->get('paiements'),
	'value'             => $this->get('payment')
));

$form->add_select_field(array(
	'name'              => 'client',
	'label'             => dims_constant::getVal('CLIENT'),
	'options'           => $this->get('lst_clients'),
	'value'             => $this->get('client'),
	'additionnal_attributes' => 'style="width:300px;"'
));

$form->add_hidden_field(array(
	'name'      => 'sort_way',
	'id'        => 'sort_way',
	'value'     => $this->get('sort_way')
));

$form->add_hidden_field(array(
	'name'      => 'sort_by',
	'id'        => 'sort_by',
	'value'     => $this->get('sort_by')
));

$default_block = $form->getBlock('default');
$default_block->setForm($form);
$default_block->setTitle( dims_constant::getVal('_FILTERS') );
$default_block->setLayout($view->getTemplatePath('commandes/commande_filters_block.tpl.php'));
$form->build();

?>
<h3>
	<?= dims_constant::getVal('QUOTATION'); ?>
</h3>
<table class="tableau">
	<tr>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_STATE'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('CATA_CLIENTS'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_DATE'); ?></td>
		<td class="w50 title_tableau"><?= dims_constant::getVal('_DIMS_LABEL'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DISCOUNT'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></td>
	</tr>
	<?php
	if(!empty($quotations)) {
		foreach($quotations as $quotation) {
			$localdate = array('date' => '', 'time' => '');
			if($quotation->get('date_cree') > 0) {
				$localdate = dims_timestamp2local($quotation->get('date_cree'));
			}
			?>
			<tr>
				<td>
					<img src="<?= cata_facture::getstatepicture($quotation->fields['state']); ?>" alt="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" title="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" />
				</td>
				<td><?= $quotation->getclient()->get('code_client'); ?> - <?= $quotation->getclient()->get('nom'); ?></td>
				<td><?= (!empty($localdate['date']) ? $localdate['date'] : '<em>n/a</em>'); ?></td>
				<td><?= $quotation->fields['libelle']; ?></td>
				<td><?= $quotation->fields['discount']; ?>&nbsp;%</td>
				<td>
					<a href="<?= get_path('clients', 'show', array('id' => $quotation->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->getId())); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>">
						<img src="<?=  $this->getTemplateWebPath('gfx/ouvrir16.png'); ?>" />
					</a>
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="6">
				<?= dims_constant::getVal('NO_QUOTATION'); ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<div class="pagination">
	<?php
	if(count($view->get('pagination')) > 1) {
		echo dims_constant::getVal('_DIMS_LABEL_PAGE').' : ';
		foreach($view->get('pagination') as $page){
			$class = '';
			if( empty($page['url']) ){ #on est sur la page courrante
				$class = "selected";
			}
			?>
			<a href="<?= $page['url']; ?>" title="<?= $page['title']; ?>" class="link_pag <?= $class; ?>"><?= $page['label']; ?></a>
			<?php
		}
	}
	?>
</div>
