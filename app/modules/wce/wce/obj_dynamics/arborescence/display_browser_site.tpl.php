<?php
$lstHeadings = wce_heading::getAllHeadings();
$articleid = 0;
if ($this->fields['id_heading'] != '' && $this->fields['id_heading'] > 0){
	$heading = new wce_heading();
	$heading->open($this->fields['id_heading']);
}elseif($this->fields['id_article'] != '' && $this->fields['id_article'] > 0){
	$article = new wce_article();
	$article->open($this->fields['id_article']);
	$articleid = $this->fields['id_article'];
	$heading = new wce_heading();
	$heading->open($article->fields['id_heading']);
}else{
	$heading = current($lstHeadings);
}
$lstOpenHeading = explode(';',$heading->fields['parents']);
$lstOpenHeading[] = $heading->fields['id'];
?>
<div class="arborescence" style="width: 250px;border-left:1px dashed #9E9E9E;height: 480px;">
	<h3>
		<? echo $_SESSION['cste']['_SITE_TREE']; ?>
	</h3>
	<div style="overflow:auto;height: 429px;">
		<div style="float:left;margin-left:5px;">
			<?
			foreach($lstHeadings as $heading){
				$heading->displayArbo(module_wce::getTemplatePath("obj_dynamics/arborescence/display_browser_site_root.tpl.php"),
									  module_wce::getTemplatePath("obj_dynamics/arborescence/display_browser_site_rubrique.tpl.php"),
									  module_wce::getTemplatePath("obj_dynamics/arborescence/display_browser_site_article.tpl.php"),
									  $lstOpenHeading,
									  $articleid);
			}
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('div.arborescence a.show').click(function(){
			var src = $('img',$(this)).attr('src');
			if(src == "./common/modules/wce/img/plus.png" || src == "./common/modules/wce/img/plusbottom.png"){ // on affiche le contenu
				if(src == "./common/modules/wce/img/plus.png")
					$('img',$(this)).attr('src',"./common/modules/wce/img/minus.png");
				else
					$('img',$(this)).attr('src',"./common/modules/wce/img/minusbottom.png");
			}else{ // on cache le contenu
				if(src == "./common/modules/wce/img/minus.png")
					$('img',$(this)).attr('src',"./common/modules/wce/img/plus.png");
				else
					$('img',$(this)).attr('src',"./common/modules/wce/img/plusbottom.png");
				var src2 = $('div.arborescence div.rub'+$(this).attr('name')+" a.show img").attr('src');
				if(src2 == "./common/modules/wce/img/minus.png" || src2 == "./common/modules/wce/img/minusbottom.png")
					$('div.arborescence div.rub'+$(this).attr('name')+" a.show").click();
			}
			$('div.arborescence div.rub'+$(this).attr('name')).fadeToggle();
			$('div.arborescence div.art'+$(this).attr('name')).fadeToggle();
		});
		$('div.arborescence a.article').click(function(){
			$('input#id_linkedheading').val(0);
			$('input#linkedheading_displayed').val("");

			$('input#id_article_link').val($(this).attr('id'));
			$('input#linkedpage_displayed').val($(this).attr('title'));
		});
		$('div.arborescence a.heading').click(function(){
			$('input#id_linkedheading').val($(this).attr('id'));
			$('input#linkedheading_displayed').val($(this).attr('title'));

			$('input#id_article_link').val(0);
			$('input#linkedpage_displayed').val("");
		});
	});
</script>
