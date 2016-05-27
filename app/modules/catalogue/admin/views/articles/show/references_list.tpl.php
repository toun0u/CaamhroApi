<?php
$view = view::getInstance();
$article = $view->get('article');
$references = $view->get('references');

?>
<div class="actions" style="text-align: right;">
	<a href="<?= get_path('articles', 'show', array('id' => $article->get('id'), 'sc' => 'references', 'sa' => 'new')); ?>">
		<?= dims_constant::getVal('ADD_REFERENCE'); ?>
	</a>
</div>

<?php
if (empty($references)) {
	?>
	<div class="flash-bag info clear">
		<?= dims_constant::getVal('NO_REFERENCE'); ?>
	</div>
	<?php
} else {
	?>
	<table class="tableau">
		<tr>
			<td class="w5 title_tableau">
				<?= dims_constant::getVal('NAME'); ?>
			</td>
			<td class="w25 title_tableau">
				<?= dims_constant::getVal('TYPE'); ?>
			</td>
			<td class="w30 title_tableau">
				<?= dims_constant::getVal('URL'); ?>
			</td>
			<td class="w2 title_tableau">
				<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
			</td>
		</tr>
		<?php
		foreach ($references as $reference) {
			?>
			<tr>
				<td class="w5 title_tableau">
					<?= $reference->fields['name']; ?>
				</td>
				<td class="w25 title_tableau">
					<?= article_reference::getTypeLabel($reference->fields['type']); ?>
				</td>
				<td class="w30 title_tableau">
					<a href="<?= $reference->getLink(); ?>">
						<?= $reference->getLink(); ?>
					</a>
				</td>
				<td class="w2 title_tableau">
					<a href="<?= get_path('articles', 'show', array('id' => $article->get('id'), 'sc' => 'references', 'sa' => 'edit', 'id_reference' => $reference->fields['id'])); ?>">
						<img src="<?= $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" alt="<?= dims_constant::getVal('MODIFY'); ?>" title="<?= dims_constant::getVal('MODIFY'); ?>" />
					</a>
					<a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?= get_path('articles', 'show', array('id' => $article->get('id'), 'sc' => 'references', 'sa' => 'delete', 'id_reference' => $reference->fields['id'])); ?>', '<?= dims_constant::getVal('_DIMS_LABEL_CONFIRM_DELETE'); ?>');">
						<img src="<?= $view->getTemplateWebPath('gfx/poubelle20.png'); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
					</a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}
