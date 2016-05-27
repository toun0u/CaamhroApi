<?php
$view = view::getInstance();
$current_article = $view->get('article');
$familles = $view->get('familles');

?>
<a href="<?= get_path('articles', 'index'); ?>" class="a_h1">
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/articles30x20.png'); ?>">
		<?= dims_constant::getVal('_LIST_OF_ARTICLES'); ?>
	</h1>
</a>
<div class="bloc_current_article">
	<h2>
		<?php
		if($current_article->fields['published'] == article::ARTICLE_PUBLISHED){
			?>
			<img src="<?= $view->getTemplateWebPath('gfx/pastille_verte16.png'); ?>" title="Cet article est publié" />
			<?php
		}
		else{
			?>
			<img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge16.png'); ?>" title="Cet article n'est pas publié" />
			<?php
		}
		?>
		<span class="orange"><?= $current_article->fields['label']; ?></span> - <?= $current_article->fields['reference']; ?>
	</h2>
	<?php
	if( count($familles) ) {
		?>
		<div class="details">
			<?= dims_constant::getVal('FAMILY_IES').' : '; ?>
			<?php
			$i = 0;
			$total = count($familles);
			foreach($familles as $id => $f){
				?>
				<a href="<?= get_path('familles', 'show', array('id' => $id)); ?>"><?= $f->fields['label']; ?></a>
				<?php
				if($i < $total - 1) echo ', ';
				$i++;
			}
			?>
		</div>
		<?php
	}
	?>
	<div class="totaux">
		<table>
			<tr><td><label><?= dims_constant::getVal('PU_HT'); ?> :</label></td><td class="value orange"><?= money_format('%n', $current_article->getPUHT()); ?></td></tr>
			<tr><td><label><?= dims_constant::getVal('HT_DISCOUNTED_PRICE'); ?> :</label></td><td class="value orange"><?= money_format('%n',$current_article->calculate_PUHTRemise()); ?></td></tr>
			<tr><td><label><?= dims_constant::getVal('PRICE_TTC'); ?> :</label></td><td class="value orange"><?= money_format('%n', $current_article->calculate_PUTTC()); ?></td></tr>
		</table>
	</div>
	<div class="heading_blocks">
		<div class="picto">
			<?php
			$path = $current_article->getVignette();
			if( empty($path) ) $path = $view->getTemplateWebPath('gfx/no_picture.png');
			?>
			<img src="<?= $path; ?>" />
		</div>
		<?php if( ! empty($current_article->fields['description'])){ ?>
		<div class="description">
			<?= $current_article->fields['description']; ?>
		</div>
		<?php } ?>
	</div>
</div>
