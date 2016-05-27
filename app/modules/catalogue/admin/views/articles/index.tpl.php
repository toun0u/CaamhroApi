<?php $view = view::getInstance(); ?>
<a href="<?= get_path('articles', 'index'); ?>" class="a_h1">
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/articles30x20.png'); ?>">
		<?= dims_constant::getVal('_LIST_OF_ARTICLES'); ?>
	</h1>
</a>

<?php
$ad_js = <<< ADDITIONAL_JS
$("select#families").chosen({allow_single_deselect:true});
$("select#published").chosen({allow_single_deselect:true});
$("select#type").chosen({allow_single_deselect:true});

$("#filter_articles select").attr('onchange', 'javascript:document.filter_articles.submit();');

$('#select_all').click(function(){

	if($(this).is(':checked')){
		$('.art_check').attr("checked", "checked");
	}
	else $('.art_check').removeAttr("checked");
});
ADDITIONAL_JS;

$filters = $view->get('filters');
if (sizeof($filters)) {
	foreach ($filters as $filter) {
		$ad_js .= '$("select#filter'.$filter['filter']->get('id').'").chosen({allow_single_deselect:true});';
	}
}

$form = new Dims\form(array(
		'name'				=> 'filter_articles',
		'action'			=> get_path('articles','index'),
		'validation'		=> false,
		'back_name'			=> dims_constant::getVal('REINITIALISER'),
		'back_url'			=> get_path('articles','index', array('filter_init' => 1) ),
		'submit_value'		=> dims_constant::getVal('_DIMS_FILTER'),
		'include_actions'	=> false,
		'additional_js'		=> $ad_js
	));

$form->add_hidden_field(array(
	'name' => 'page',
	'value'=> '0'
	));

$publication = array();
$publication['all'] = dims_constant::getVal('_DIMS_ALLS');
$publication['published'] = dims_constant::getVal('PUBLISHED');
$publication['unpublished'] = dims_constant::getVal('NOT_PUBLISHED');

$selected_publication = $view->get('publication');

$form->add_select_field(array(
		'name'				=> 'published',
		'label'				=> dims_constant::getVal('STATE_OF_PUBLICATION'),
		'options'			=> $publication,
		'value'				=> isset($selected_publication) ? $selected_publication : 'all',
		'classes'			=> 'published_select'
	));

$type = array();
$type['all'] = dims_constant::getVal('_DIMS_ALLS');
$type['kit'] = dims_constant::getVal('KIT');
$type['classical'] = dims_constant::getVal('CLASSICAL');

$selected_type = $view->get('type');
$form->add_select_field(array(
		'name'			=> 'type',
		'label'			=> dims_constant::getVal('_TYPE'),
		'options'		=> $type,
		'value'			=> isset($selected_type) ? $selected_type : 'all',
		'classes'		=> 'type_select'
	));

$selected_family = $view->get('family');
$familles = $view->get('familles');

$form->add_select_field(array(
		'name'			=> 'families',
		'label'			=> dims_constant::getVal('_FAMILY'),
		'options'		=> $familles,
		'value'			=> isset($selected_family) ? $selected_family : 'all',
		'classes'		=> 'family_select'
	));

$keywords = $view->get('keywords');
$form->add_text_field(array(
		'name'			=> 'keywords',
		'label'			=> dims_constant::getVal('_WCE_KEYWORDS_META'),
		'value'			=> isset($keywords) ? $keywords : '',
	));

$form->add_hidden_field(array( #permet de reseter la valeur à 0 sur la case à cocher n'est pas
		'name'			=> 'unattached',
		'id'			=> 'h_unattached',
		'value'			=> 0
	));
$unattached = $view->get('unattached');
$form->add_checkbox_field(array(
		'name'			=> 'unattached',
		'id'			=> 'unattached',
		'label'			=> dims_constant::getVal('UNATTACHED_ARTICLES'),
		'value'			=> 1,
		'checked'		=> ($unattached) ? true : false,
	));

$form->add_hidden_field(array( #permet de reseter la valeur à 0 sur la case à cocher n'est pas
		'name'			=> 'in_clipboard',
		'id'			=> 'h_in_clipboard',
		'value'			=> 0
	));
$in_clipboard = $view->get('in_clipboard');
$form->add_checkbox_field(array(
		'name'			=> 'in_clipboard',
		'id'			=> 'in_clipboard',
		'label'			=> dims_constant::getVal('IN_CLIPBARD'),
		'value'			=> 1,
		'checked'		=> ($in_clipboard) ? true : false,
	));

// Filtres sur les champs libres
if (sizeof($filters)) {
	foreach ($filters as $filter) {
		$form->add_select_field(array(
			'name' 		=> 'filter'.$filter['filter']->get('id'),
			'label' 	=> $filter['filter']->getLibelle(),
			'options' 	=> $filter['values'],
			'classes' 	=> 'type_select',
			'value' 	=> $filter['selected']
			));
	}
}

$default_block = $form->getBlock('default');
//$default_block->setActionnable(true, $form);
$default_block->setForm($form);
$default_block->setTitle( dims_constant::getVal('_FILTERS') );
$default_block->setLayout($view->getTemplatePath('articles/article_filters_block.tpl.php'));
$form->build();
?>

<?php
$articles = $view->get('articles');
if($view->get('total_articles') > 0){
	?>
	<div class="zone_filter">
		<div class="result_criteres">
			<span class="count"><?= $view->get('total_articles'); ?></span>&nbsp;<?= dims_constant::getVal('PRODUCTS_MATCH_YOUR_CRITERIA'); ?>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('ORDER'); ?> :
			<a href="<?= get_path('articles', 'index', array('sort_way' => 'ASC')); ?>" <?php if($view->get('sort_way') == 'ASC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('ASCENDANT'); ?></a>&nbsp;|&nbsp;
			<a href="<?= get_path('articles', 'index', array('sort_way' => 'DESC')); ?>" <?php if($view->get('sort_way') == 'DESC') echo 'class="selected"'; ?> ><?= dims_constant::getVal('DESCENDANT'); ?></a>
		</div>
		<div class="sort_options">
			<?= dims_constant::getVal('SORT_BY'); ?> :
			<a href="<?= get_path('articles', 'index', array('sort_by' => 'ref', 'sort_way' => 'ASC', 'page' => 0)); ?>" <?php if($view->get('sort_by') == 'ref') echo 'class="selected"'; ?>><?= dims_constant::getVal('REFERENCE'); ?></a>&nbsp;|&nbsp;
			<a href="<?= get_path('articles', 'index', array('sort_by' => 'des', 'sort_way' => 'ASC', 'page' => 0)); ?>" <?php if($view->get('sort_by') == 'des') echo 'class="selected"'; ?>><?= dims_constant::getVal('_DESIGNATION'); ?></a>
		</div>
	</div>

	<?php
	$form2 = new Dims\form(array(
		'name'		=> 'lst_articles',
		'action'	=> get_path('articles', 'handle_selection')
		));

	echo $form2->get_header();
	echo $form2->hidden_field(array(
		'name'	=> 'action',
		'id'	=> 'action_list'
		));
	?>
	<table class="tableau">
		<tr>
			<td class="w7 title_tableau">
				<?php
				echo $form2->checkbox_field(array(
							'name'		=> 'select_all',
							'value'		=> 'all',
						));
				?>
			</td>
			<td class="w5 title_tableau">
				&nbsp;
			</td>
			<td class="w5 title_tableau">
				&nbsp;
			</td>
			<td class="w10 title_tableau">
				<?= dims_constant::getVal('REF'); ?>.
			</td>
			<td class="w30 title_tableau">
				<?= dims_constant::getVal('DESIGNATION'); ?>
			</td>
			<td class="w25 title_tableau">
				<?= dims_constant::getVal('FAMILIES'); ?>
			</td>
			<td class="w5 title_tableau">
				<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
			</td>
		</tr>
		<?php
		foreach($articles as $art){
		?>
			<tr>
				<td>
					<?=
						$form2->checkbox_field(array(
							'name'		=> 'selection[]',
							'id'		=> 'cb_'.$art->fields["id"],
							'value'		=> $art->fields['id'],
							'classes'		=> 'art_check'
						));
					?>
					<?php
					if(in_clipboard($art->fields['id']) ){
						?>
						<img class="in_clipboard" src="<?= $view->getTemplateWebPath('gfx/clipboard16.png'); ?>" title="<?= dims_constant::getVal('IN_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('IN_CLIPBOARD'); ?>"/>
						<a href="<?= get_path('articles', 'shift_clipboard', array('id' => $art->fields['id'])); ?>"><img class="in_clipboard" src="<?= $view->getTemplateWebPath('gfx/del16min.png'); ?>" title="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>"/></a>
						<?php
					}
					?>
				</td>
				<td style="text-align:center">
					<?php
					if($art->fields['published']){
						?>
						<img src="<?=  $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="Cet article est publié" alt="Article publié"/>
						<?php
					}
					else{
						?>
						<img src="<?=  $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="Cet article n'est pas publié" alt="Article non publié"/>
						<?php
					}
					?>
				</td>
				<td style="text-align:center">
					<?php
					$thumb = $art->getLightAttribute('thumb'); //Récupération d'un docfile
					$photo = null;
					if(isset($thumb)){
						$photo = $thumb->getThumbnail(20);
					}
					if( ! is_null($photo)){
					?>
						<img src="<?= $photo; ?>" />
					<?php
					}
					?>
				</td>
				<td><?= $art->fields['reference']; ?></td>
				<td><?= $art->fields['label']; ?></td>
				<td>
					<?php
					$familles = $art->getLightAttribute('familles');
					if(isset($familles)){
						$i = 0;
						$total = count($familles);
						foreach($familles as $id => $label){
							?>
							<a href="<?= get_path('familles', 'show', array('id' => $id)); ?>"><?= $label; ?></a>
							<?php
							if($i < $total-1) echo ', ';
							$i++;
						}
					}
					?>
				</td>
				<td class="center">
					<a href="<?= get_path('articles', 'show', array('id' => $art->fields['id']));?>" title="<?= dims_constant::getVal('OPEN_ARTICLE_RECORD');?>"><img src="<?=  $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" /></a>
					<a onclick="javascript:dims_confirmlink('<?= get_path('articles', 'disable', array('id' => $art->fields['id'])); ?>','<?= dims_constant::getVal('SURE_DELETE_ARTICLE'); ?>');" href="javascript:void(0);" title="<?= dims_constant::getVal('DELETE_ARTICLE');?>"><img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a></td>
			</tr>
			<?php
		}
		?>
	</table>
	<div class="tableau_footer">
		<div class="actions_selection">
			<img src="<?=  $view->getTemplateWebPath('gfx/pour_la_selection20.png'); ?>"/><span><?= dims_constant::getVal('FOR_SELECTION'); ?> :</span>
			<a href="javascript:void(0);" onclick="javascript:actionList('copy');"><?= dims_constant::getVal('_COPY'); ?></a>&nbsp;|&nbsp;
			<a href="javascript:void(0);" onclick="javascript:actionList('revert');"><?= dims_constant::getVal('_INVERT_PUBLICATION'); ?></a>
		</div>
		<?= $form2->close_form(); ?>
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
	<div class="div_no_elem"><?= dims_constant::getVal('NO_ARTICLE'); ?></div>
	<?php
}
?>
