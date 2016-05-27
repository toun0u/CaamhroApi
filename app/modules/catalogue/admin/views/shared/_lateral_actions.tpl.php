<?php $view = view::getInstance();
$actions = $view->get('actions');
if( isset($actions) ){
?>
<h3 class="h3_underline"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></h3>
<?php
	foreach($actions as $action){
		if(isset($action['confirm']) && $action['confirm'] && isset($action['txt_confirm'])){
			?>
			<p>
				<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= $action['link']; ?>','<?= str_replace("'","\'",$action['txt_confirm']); ?>');">
					<img src="<?=  $view->getTemplateWebPath($action['picto']); ?>" />
					<?= $action['text']; ?>
				</a>
			</p>
			<?php
		}else{
		  ?>
			<p>
				<a href="<?= $action['link']; ?>">
					<img src="<?=  $view->getTemplateWebPath($action['picto']); ?>" />
					<?= $action['text']; ?>
				</a>
			</p>
		  <?php
		}
	}
}
?>
