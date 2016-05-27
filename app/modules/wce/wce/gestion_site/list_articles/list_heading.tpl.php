<?php
$pos = $this->getLightAttribute('pos');
$class = $this->getLightAttribute('class');
$varPix = 12;
foreach($this->getAllRubriques() as $head){
	?>
	<tr <? echo $class; ?>>
		<!--<td style="width: 20px;">
			<? echo $head->fields['position']; ?>
		</td>-->
		<td style="width: 20px;">
			<input type="checkbox" class="input_art" name="head[]" value="<? echo $head->fields['id']; ?>" />
		</td>
		<td colspan="2">
			<img style="margin-top:4px;margin-left:<? echo ($pos*$varPix); ?>px;float: left;" src="<? echo module_wce::getTemplateWebPath('gfx/dossier16.png'); ?>" />
			<div style="float: left;margin-top:3px;margin-left:5px;">
				<? echo $head->fields['label']; ?>
			</div>
		</td>
	</tr>
	<?
	$head->setLightAttribute('pos',$pos+1);
	$class = ($class == '')?'class="table_ligne1"':'';
	$head->setLightAttribute('class',$class);
	$head->display(module_wce::getTemplatePath("gestion_site/list_articles/list_heading.tpl.php"));
	$class = $head->getLightAttribute('class',$class);
}
foreach($this->getAllArticles() as $article){
	?>
	<tr <? echo $class; ?>>
		<!--<td style="width: 20px;">
			<? echo $article->fields['position']; ?>
		</td>-->
		<td style="width: 20px;">
			<input type="checkbox" class="input_art" name="art[]" value="<? echo $article->fields['id']; ?>" />
		</td>
		<td>
			<?
			if ($article->fields['uptodate']){
				?>
				<img style="margin-top:4px;margin-left:<? echo ($pos*$varPix); ?>px;float: left;" src="/common/modules/wce/img/doc0.png" />
				<?
			}else{
				?>
				<img style="margin-top:4px;margin-left:<? echo ($pos*$varPix); ?>px;float: left;" src="/common/modules/wce/img/doc1.png" />
				<?
			}
			?>
			<div style="float: left;margin-top:3px;margin-left:3px;">
				<? echo $article->fields['title']; ?>
			</div>
		</td>
		<td style="width: 100px;text-align: right;">
			<? echo $article->fields['author']; ?>
		</td>
	</tr>
	<?
	$class = ($class == '')?'class="table_ligne1"':'';
}
$this->setLightAttribute('class',$class);
?>