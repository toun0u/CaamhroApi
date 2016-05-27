<?php
$lst = $this->getDirectChilds();
if (count($lst) > 0){
	$sub2 = null;
	?>
	<div class="bloc_categ">
		<ul id="browser_categ_<? echo $this->fields['id']; ?>">
			<?
			foreach($lst as $sub){
				?>
				<li ref="<? echo $sub->fields['id']; ?>" <? if (in_array($sub->fields['id'],$this->getLightAttribute('opened'))){ $sub2 = $sub; echo 'class="selected"'; } ?>>
					<div class="category_item">
						<?php
						if($sub->hasSubCategories()){
							?>
							<div class="puce_children">
								<img src="<?= module_wiki::getTemplateWebPath('/gfx/puce_categ.png'); ?>" />
							</div>
							<?php
						}
						?>
						<div class="label_categ"><? echo $sub->fields['label']; ?></div>
					</div>
				</li>
				<?
			}
			?>
		</ul>
	</div>
	<?
	if (!is_null($sub2)){
		$sub2->setLightAttribute('opened',$this->getLightAttribute('opened'));
		$sub2->setLightAttribute('current',$this->getLightAttribute('current'));
		$sub2->display(module_wiki::getTemplatePath('/categories/categ_browser_lvl.tpl.php'));
	}
}
?>

