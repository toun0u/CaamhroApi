<?php $view = view::getInstance(); ?>
<?php
$view->partial($view->getTemplatePath('shared/_lateral_search_block.tpl.php'));
?>
<h3 class="h3_underline"><?= dims_constant::getVal('LAST_CONSULTED_ARTICLES'); ?></h3>
<?php
$last_articles = $view->get('last_articles');
if( ! empty($last_articles) ){
?>
	<table>
		<?php
		foreach($last_articles as $art_id){
			$article = new article();
			$article->open($art_id);
			if( ! $article->isNew() ){
				$photo = $article->getWebPhoto(50);
				?>
				<tr>
					<td>
						<?php
						if( ! is_null ($photo) ){
							?>
							<a href="<?= get_path('articles', 'show', array('id' => $article->fields['id']));?>">
								<img src="<?= $photo; ?>" />
							</a>
							<?php
						}
						?>
					</td>
					<td><a href="<?= get_path('articles', 'show', array('id' => $article->fields['id']));?>"><?= $article->fields['label']; ?> - <?= $article->fields['reference']; ?></a></td>
				</tr>
				<?php
				}
			}
		?>
	</table>
<?php
}
else{
	?>
	<span class="no_elem"><?= dims_constant::getVal('NO_ARTICLE_CONSULTED'); ?></span>
	<?php
}
$view->partial($view->getTemplatePath('shared/_lateral_actions.tpl.php'));

?>
