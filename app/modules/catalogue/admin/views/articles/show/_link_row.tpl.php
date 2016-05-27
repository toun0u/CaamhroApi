<?php
$view = view::getInstance();
$art = $object;#Hérité de la fonction d'appel
?>
<tr>
	<td>
		<?php
		$photo = $art->getVignette(20);
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
		if($art->getLightAttribute('sym_link')) $pastille = 'verte12';
		else $pastille = 'rouge12';
		?>
		<img src="<?=  $view->getTemplateWebPath('gfx/pastille_'.$pastille.'.png'); ?>" />
	</td>
	<td>
		<a href="<?= get_path('articles', 'show', array('id' => $art->get('id'))); ?>" title="<?= dims_constant::getVal('OPEN_ARTICLE_RECORD'); ?>"><img src="<?=  $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" /></a>
		<a href="<?= get_path('articles', 'show', array('sc' => 'links', 'sa' => 'edit', 'id' => $view->get('article')->get('id'), 'id_link' => $art->getLightAttribute('link_id') ) ); ?>" title="<?= dims_constant::getVal('EDIT_LINK');?>"><img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" /></a>
		<a href="<?= get_path('articles', 'show', array('sc' => 'links', 'sa' => 'detach', 'id' => $view->get('article')->get('id'), 'id_link' => $art->getLightAttribute('link_id') ) ); ?>" title="<?= dims_constant::getVal('_BUSINESS_LEGEND_CUT');?>"><img src="<?=  $view->getTemplateWebPath('gfx/rompre_lien16.png'); ?>" /></a>
	</td>
</tr>