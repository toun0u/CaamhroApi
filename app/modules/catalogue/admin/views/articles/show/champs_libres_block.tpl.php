<?php
$view = view::getInstance();
$article = $view->get('article');
$id_lang = substr($this->getId(), strpos($this->getId(),'_')+1);
$champs = $view->get('champs_libres');
$full = $view->get('all_fields');
?>
<div id="<?= $this->getId();?>" class="sub_bloc <?= $this->getClasses(); ?>">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<div class="scope_selection">
			<a class="full <?= ( $article->isFullScope() ) ? 'selected' : ''; ?>" href="javascript:void(0);" onclick="javascript:selectScope('full');"><?= dims_constant::getVal('ALL_FIELDS'); ?></a>
			&nbsp;&nbsp;|&nbsp;&nbsp;<a class="family <?= ( $article->isFullScope() ) ? '' : 'selected'; ?>" href="javascript:void(0);" onclick="javascript:selectScope('family');"><?= dims_constant::getVal('FAMILY_SCOPE'); ?></a>
		</div>
		<h3>
			<?php echo $title; ?>
		</h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<div class="legend"><?= dims_constant::getVal('EMPTY_FREE_FIELDS'); ?></div>
		<div class="families_scope"  <?= ( $article->isFullScope() ) ? 'style="display:none"' : ''; ?>>
			<?php
			foreach($champs as $categ => $lst){
				?>
				<h4><?= $categ; ?></h4>
				<table class="tableau">
					<tr>
						<th class="empty_th"></th>
						<th class="w100p"><?= dims_constant::getVal('_TECHNICAL');?></th>
						<th class="w100p"><?= dims_constant::getVal('_FORMS_FILTER');?></th>
						<th class="w300p"><?= dims_constant::getVal('_DIMS_LABEL_RULEVALUE');?></th>
					</tr>
					<?php
					foreach($lst as $id_chp => $field){
						$view->partial($view->getTemplatePath('articles/show/_field.tpl.php'), array('scope' => 'family', 'id_lang' => $id_lang, 'id_chp' => $id_chp, 'field' => $field, 'form' => $this->getForm()));
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<div class="full_scope" <?= ( $article->isFullScope() ) ? '' : 'style="display:none"'; ?>>
			<?php
			foreach($full as $categ => $lst){
				?>
				<h4><?= $categ; ?></h4>
				<table class="tableau">
					<tr>
						<th class="empty_th"></th>
						<th class="w100p"><?= dims_constant::getVal('_TECHNICAL');?></th>
						<th class="w100p"><?= dims_constant::getVal('_FORMS_FILTER');?></th>
						<th class="w300p"><?= dims_constant::getVal('_DIMS_LABEL_RULEVALUE');?></th>
					</tr>
					<?php
					foreach($lst as $id_chp => $field){
						$view->partial($view->getTemplatePath('articles/show/_field.tpl.php'), array('scope' => 'full', 'id_lang' => $id_lang, 'id_chp' => $id_chp, 'field' => $field, 'form' => $this->getForm()));
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
	</div>

</div>
