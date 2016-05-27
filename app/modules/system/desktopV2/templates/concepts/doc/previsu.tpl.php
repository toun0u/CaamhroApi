<link type="text/css" rel="stylesheet" href="/common<?php echo module_desktopv2::getTemplateWebPath('/concepts/doc/styles.css'); ?>">
<div id="fiche_content" class="infos_general">
	<div class="fiche_tech elem">
		<h4 style="margin:5px">
			<?
			echo $_SESSION['cste']['_PREVIEW']." : ".$this->fields['name'];
			?>
			<a style="float:right;font-size:12px;" href="<? echo dims::getInstance()->getScriptEnv()."?concepts_op=documents"; ?>">
				<img src="<? echo module_desktopv2::getTemplateWebPath('/gfx/common/icon_back.png'); ?>" />
			</a>
		</h4>
		<?
		if (in_array($this->fields['extension'],array("mp4","mkv","avi","mpeg","mpg"))){
			?>
			<div style="width: 100%; height: 870px;text-align:center;">
				<? echo $this->getPreview(true,null); ?>
			</div>
			<?
		}else{
			?>
			<div style="width: 100%; height: 870px;">
				<?
				echo $this->getPreview(true,null,module_desktopv2::getTemplatePath('/concepts/doc/preview_doc.tpl.php'));
				?>
			</div>
		<? } ?>
	</div>
</div>