<?php $view = view::getInstance(); ?>
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/clients50x30.png'); ?>">
		<?= dims_constant::getVal('CATA_CLIENTS'); ?>
	</h1>

<?php
$ad_js = <<< ADDITIONAL_JS
$("select#status").chosen({allow_single_deselect:true});
ADDITIONAL_JS;


$a_status = array();
$a_status[-1] = dims_constant::getVal('_DIMS_ALLS');
$a_status[client::STATUS_OK] = dims_constant::getVal('CATA_STATUS_ACTIVES');
$a_status[client::STATUS_BLOCKED] = dims_constant::getVal('CATA_STATUS_BLOCKEDS');

$form = new Dims\form(array(
		'name' 				=> 'clients_filters',
		'action'			=> get_path('clients','index'),
		'validation'		=> false,
		'back_name'			=> dims_constant::getVal('REINITIALISER'),
		'back_url'			=> get_path('clients', 'index', array('filter_init' => 1) ),
		'submit_value'		=> dims_constant::getVal('_DIMS_FILTER'),
		'include_actions' 	=> false,
		'additional_js'		=> $ad_js
	));

$filters = $form->addBlock ('clients_filters', '', $view->getTemplatePath('clients/client_filters_block.tpl.php'));
$filters->setForm($form);

$selected_type = $view->get('status');
$form->add_select_field(array(
		'name' 			=> 'status',
		'block'			=> 'clients_filters',
		'label'			=> dims_constant::getVal('STATUS'),
		'options'		=> $a_status,
		'value'			=> isset($selected_type) ? $selected_type : -1,
		'classes'		=> 'statut_select'
	));

$keywords = $view->get('keywords');
$form->add_text_field(array(
		'name'			=> 'keywords',
		'block'			=> 'clients_filters',
		'label'			=> dims_constant::getVal('_WCE_KEYWORDS_META'),
		'value'			=> isset($keywords) ? $keywords : '',
	));

$form->build();


// liste des clients
$clients = $view->get('clients');

if (sizeof($clients)) {
	?>
	<div class="zone_filter">
		<div class="result_criteres">
			<span class="count"><?= $view->get('total_clients'); ?></span>&nbsp;<?= dims_constant::getVal('CLIENTS_MATCH_YOUR_CRITERIA'); ?>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('ORDER'); ?> :
			<a href="<?= get_path('clients', 'index', array('sort_way' => 'ASC')); ?>" <?php if($view->get('sort_way') == 'ASC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('ASCENDANT'); ?></a>&nbsp;|&nbsp;
			<a href="<?= get_path('clients', 'index', array('sort_way' => 'DESC')); ?>" <?php if($view->get('sort_way') == 'DESC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('DESCENDANT'); ?></a>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('SORT_BY'); ?> :
			<a href="<?= get_path('clients', 'index', array('sort_by' => 'code', 'sort_way' => 'ASC', 'page' => 0)); ?>" <?php if($view->get('sort_by') == 'code') echo 'class="selected"'; ?>><?= dims_constant::getVal('CATA_CODE_CLIENT'); ?></a>&nbsp;|&nbsp;
			<a href="<?= get_path('clients', 'index', array('sort_by' => 'nom', 'sort_way' => 'ASC', 'page' => 0)); ?>" <?php if($view->get('sort_by') == 'nom') echo 'class="selected"'; ?>><?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?></a>
		</div>
	</div>

	<table class="tableau">
		<tr>
			<td class="w2 title_tableau">
				&nbsp;
			</td>
			<td class="w2 title_tableau">
				&nbsp;
			</td>
			<td class="w25 title_tableau">
				<?= dims_constant::getVal('CATA_CODE_CLIENT'); ?>
			</td>
			<td class="w30 title_tableau">
				<?= dims_constant::getVal('_DESIGNATION'); ?>
			</td>
			<td class="w2 title_tableau">
				<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
			</td>
		</tr>
		<?php
		foreach ($clients as $client) {
			?>
			<tr>
				<td class="center">
					<?php
					if ($client->isBlocked()) {
						echo '<img src="'.$view->getTemplateWebPath('gfx/pastille_rouge12.png').'" />';
					}
					else {
						echo '<img src="'.$view->getTemplateWebPath('gfx/pastille_verte12.png').'" />';
					}
					?>
				</td>
				<td class="center">
					<!-- Photo de l'entreprise -->
				</td>
				<td>
					<?= $client->getCode(); ?>
				</td>
				<td>
					<?= $client->getName(); ?>
				</td>
				<td class="center">
					<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'))); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>"><img src="<?=  $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" /></a>
					<?php
					// Désactivé pour la caahmro
					/*
					if ($client->isBlocked()) {
						?><a href="<?= get_path('clients', 'unblock', array('id' => $client->get('id_client'))); ?>" title="<?= dims_constant::getVal('CATA_UNBLOCK'); ?>"><img src="<?=  $view->getTemplateWebPath('gfx/main.png'); ?>" /></a><?php
					}
					else {
						?><a href="<?= get_path('clients', 'block', array('id' => $client->get('id_client'))); ?>" title="<?= dims_constant::getVal('CATA_BLOCK'); ?>"><img src="<?=  $view->getTemplateWebPath('gfx/main.png'); ?>" /></a><?php
					}
					*/
					?>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<div class="tableau_footer">
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

	<?php
}
else{
	?>
	<div class="div_no_elem"><?= dims_constant::getVal('CATA_NO_CLIENT'); ?></div>
	<?php
}

