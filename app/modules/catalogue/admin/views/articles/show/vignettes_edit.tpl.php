<?php
$view = view::getInstance();
$elem = $view->get('article');
show_guide(dims_constant::getVal('THUMBNAIL_PICTURE_MAIN_CORRESPONDS_TO_POSITION_1'));
?>

<div class="add_vignette">
	<a href="<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$elem->get('id')."&sa=add"; ?>">
		<img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?> "/>
        <?= dims_constant::getVal('ADD_THUMBNAIL'); ?>
	</a>
</div>
<div id="vignette_added">
	<?php
	$lstThumb = $view->get('lst_thumbnails');
	$nbThumb = count($lstThumb);
	foreach($lstThumb as $thumb){
		$doc = $thumb->getDocfile();
		?>
		<div class="cadre_vignette_added">
			<div class="actions_vignette">
				<!--<a href="#">
					<img src="<?=  $view->getTemplateWebPath('gfx/edit16.png'); ?> "/>
				</a>-->
				<a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$elem->get('id')."&doc=".$thumb->fields['id_doc']."&sa=delete"; ?>','<?= dims_constant::getVal('_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE'); ?>');" href="javascript:void(0);">
                    <img src="<?php echo $this->getTemplateWebPath("/gfx/poubelle16.png"); ?>" />
                </a>
				<?
                if($thumb->fields['position'] == 1){
                    ?>
                    <img src="<?php echo $this->getTemplateWebPath("/gfx/gauche16_ns.png"); ?>" />
                    <?
                }else{
                    ?>
                    <a href="<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$elem->get('id')."&doc=".$thumb->fields['id_doc']."&sa=down"; ?>">
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/gauche16_s.png"); ?>" />
                    </a>
                    <?
                }
                if($thumb->fields['position'] == $nbThumb){
                    ?>
                    <img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_ns.png"); ?>" />
                    <?
                }else{
                    ?>
                    <a href="<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$elem->get('id')."&doc=".$thumb->fields['id_doc']."&sa=up"; ?>">
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_s.png"); ?>" />
                    </a>
                    <?
                }
                ?>
			</div>
			<div class="picture_vignette">
				<img src="<?= $doc->getThumbnail(150); ?>" title="article" alt="article" />
			</div>
			<div class="status_vignette_added">
				<img src="<?= $view->getTemplateWebPath('gfx/pastille_verte16.png'); ?> "/>
				<span><?= $doc->fields['name']; ?></span>
			</div>
		</div>
		<?php
	}
	?>
</div>