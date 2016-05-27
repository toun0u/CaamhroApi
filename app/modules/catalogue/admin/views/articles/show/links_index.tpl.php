<?php
$view = view::getInstance();
$article = $view->get('article');
$link_types = $view->get('link_types');

foreach ($link_types as $link_type):
	?>
	<h3><?= $link_type->get('label'); ?></h3>
	<a href="<?= get_path('articles', 'show', array('id' => $article->get('id'), 'sc' => 'links', 'sa' => 'new', 'type' => $link_type->get('id')));?>" class="link_img"><img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?> "/><span><?= dims_constant::getVal('ADD_AN_ARTICLE_FOR_THIS_LINK'); ?></span></a>
	<?php
	$link_article = $view->get('links');
	if( !empty($link_article[$link_type->get('id')])){
		?>
		<table class="tableau">
			<tr>
				<td class="w5 title_tableau">
					&nbsp
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('REF'); ?>.
				</td>
				<td class="w30 title_tableau">
					<?= dims_constant::getVal('DESIGNATION'); ?>
				</td>
				<td class="w5 title_tableau">
					<?= dims_constant::getVal('SYMMETRIC'); ?>
				</td>
				<td class="w2 title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>
			<?php
			foreach($link_article[$link_type->get('id')] as $id => $art){
				$view->partial($view->getTemplatePath('articles/show/_link_row.tpl.php'), $art);
			}
		?>
		</table>
		<?php
	}
	else{
		?>
		<div class="div_no_elem"><?= dims_constant::getVal('NOT_ANY_ARTICLE_FOR_THIS_LINK');?></div>
		<?php
	}
endforeach;
