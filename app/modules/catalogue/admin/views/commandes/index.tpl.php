<?php
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

$form->add_text_field(array(
	'name'              => 'keywords',
	'label'             => dims_constant::getVal('_WCE_KEYWORDS_META'),
	'value'             => $this->get('keywords')
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
//$default_block->setActionnable(true, $form);
$default_block->setForm($form);
$default_block->setTitle( dims_constant::getVal('_FILTERS') );
$default_block->setLayout($view->getTemplatePath('commandes/commande_filters_block.tpl.php'));
$form->build();

$commandes = $view->get('commandes');
if($view->get('total_commandes') > 0){
	?>
	<div class="zone_filter">
		<div class="result_criteres">
			<span class="count"><?= $view->get('total_commandes'); ?></span>&nbsp;<?= dims_constant::getVal('_ORDER_S'); ?>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('ORDER'); ?> :
			<a ref="ASC" onclick="javascript:$('input#sort_way').val($(this).attr('ref')); document.filter_commandes.submit();" href="javascript:void(0);" <?php if($view->get('sort_way') == 'ASC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('ASCENDANT'); ?></a>&nbsp;|&nbsp;
			<a ref="DESC" onclick="javascript:$('input#sort_way').val($(this).attr('ref')); document.filter_commandes.submit();" href="javascript:void(0);" <?php if($view->get('sort_way') == 'DESC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('DESCENDANT'); ?></a>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('SORT_BY'); ?> :
			<a ref="date_cree" onclick="javascript:$('input#sort_by').val($(this).attr('ref')); document.filter_commandes.submit();" href="javascript:void(0);" <?php if($view->get('sort_by') == 'date_cree') echo 'class="selected"'; ?>><?= dims_constant::getVal('_DIMS_DATE'); ?></a>&nbsp;|&nbsp;
			<a ref="client" onclick="javascript:$('input#sort_by').val($(this).attr('ref')); document.filter_commandes.submit();" href="javascript:void(0);" <?php if($view->get('sort_by') == 'client') echo 'class="selected"'; ?>><?= dims_constant::getVal('CLIENT'); ?></a>&nbsp;|&nbsp;
			<a ref="numcde" onclick="javascript:$('input#sort_by').val($(this).attr('ref')); document.filter_commandes.submit();" href="javascript:void(0);" <?php if($view->get('sort_by') == 'numcde') echo 'class="selected"'; ?>><?= dims_constant::getVal('_NUMBER'); ?></a>
		</div>
	</div>
	<table class="tableau">
		<tr>
			<td class="title_tableau">
				<?= dims_constant::getVal('_STATE'); ?>
			</td>
			<td class="title_tableau">
				HC
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('_PAYMENT_MEAN'); ?>
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('_NUMBER'); ?>
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('CATA_CODE_CLIENT'); ?>
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('CLIENT'); ?>
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('_DIMS_DATE'); ?>
			</td>
			<td class="title_tableau">
				<?= dims_constant::getVal('_DUTY_FREE_AMOUNT'); ?> (&euro;)
			</td>
			<td class="w5 title_tableau">
				<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
			</td>
		</tr>
		<?php
		foreach($commandes as $cde){
			$cli = $cde->getClient();
			?>
			<tr>
				<td class="center">
					<?= commande::getStateIcon($cde->fields['etat'], $cde->fields['hors_cata']); ?>
				</td>
				<td class="center">
					<?php
					if($cde->fields['hors_cata']){
						?>
						<img src="<?= $view->getTemplateWebPath('gfx/pastille_verte16.png'); ?>" />
						<?php
					}else{
						?>
						-
						<?php
					}
					?>
				</td>
				<td>
					<?= moyen_paiement::getTypeLabel($cde->fields['mode_paiement']); ?>
				</td>
				<td class="txtright">
					<?= $cde->get('id_cde'); ?>
				</td>
				<td>
					<?= $cli->fields['code_client']; ?>
				</td>
				<td>
					<?= $cli->fields['nom']; ?>
				</td>
				<td>
					<?php
					$d = dims_timestamp2local($cde->fields['date_validation']);
					echo $d['date'].' - '.substr($d['time'], 0, 5);
					?>
				</td>
				<td class="txtright">
					<?= $cde->fields['total_ht']; ?>
				</td>
				<td class="center">
					<a href="<?= get_path('commandes', 'show', array('id' => $cde->fields['id_cde']));?>" title="<?= dims_constant::getVal('OPEN_THE_ORDER_SHEET'); ?>">
						<img src="<?= $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" />
					</a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<div class="tableau_footer">
		<div class="actions_selection" style="margin-top: 10px;width: auto;">
			<?= commande::getStateIcon(commande::_STATUS_AWAITING_COSTING); ?>
			<span><?= dims_constant::getVal('AWAITING_COSTING'); ?></span>
			<?= commande::getStateIcon(commande::_STATUS_PROGRESS); ?>
			<span><?= dims_constant::getVal('_DIMS_CONFIRM_WAIT'); ?></span>
			<?= commande::getStateIcon(commande::_STATUS_VALIDATED); ?>
			<span><?= dims_constant::getVal('_VALIDATED_F'); ?></span>
			<?= commande::getStateIcon(commande::_STATUS_REFUSED); ?>
			<span><?= dims_constant::getVal('_CANCELED_F'); ?></span>
		</div>
		<div class="pagination">
			<?php
			if(count($view->get('pagination')) > 1){
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
	</div>

	<script type="text/javascript">
		function actionList(mode){
			$('#action_list').val(mode);
			document.lst_articles.submit();
		}
	</script>
	<?php
}
else{
	?>
	<div class="div_no_elem"><?= dims_constant::getVal('NO_RESULT'); ?></div>
	<?php
}
?>
