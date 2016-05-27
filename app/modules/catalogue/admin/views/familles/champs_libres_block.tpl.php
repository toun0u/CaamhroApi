<?php
$view = view::getInstance();
$famille = $view->get('famille');
$id_lang = substr($this->getId(), strpos($this->getId(),'_')+1);
$champs = $view->get('sel_champs');
?>

<div id="<?= $this->getId();?>" class="sub_bloc <?= $this->getClasses(); ?> champs_<?= $id_lang; ?>">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3>
			<?php echo $title; ?>
		</h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<div class="legend"><?= dims_constant::getVal('EMPTY_FREE_FIELDS'); ?></div>
		<div class="families_scope">
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
						$view->partial($view->getTemplatePath('familles/_field.tpl.php'), array('scope' => 'family', 'id_lang' => $id_lang, 'id_chp' => $id_chp, 'field' => $field, 'form' => $this->getForm()));
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
	</div>

</div>
