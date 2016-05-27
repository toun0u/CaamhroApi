<?php
if (!isset($_SESSION['wiki']['articleid'])) $_SESSION['wiki']['articleid'] = 0;
$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);

if (!isset($_SESSION['wce_expand_tree'])) $_SESSION['wce_expand_tree']=0;
$_SESSION['wce_expand_tree']=dims_load_securvalue('wce_expand_tree',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['wce_expand_tree'],$_SESSION['wce_expand_tree']);

if (!isset($_SESSION['wce_display_tree'])) $_SESSION['wce_display_tree']=true;

$lstOpenHeading = explode(';',$heading->fields['parents']);
$lstOpenHeading[] = $heading->fields['id'];

?>
<div class="arborescence" <? if(!$_SESSION['wce_display_tree']) echo 'style="display:none;"'; ?>>
	<h3>
		<? echo $_SESSION['cste']['_SITE_TREE']; ?>
		<!--<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&wce_expand_tree=".($_SESSION['wce_expand_tree']?0:1); ?>">
			<img src="./common/img/zoomouput.png" style="border:0px" />
		</a>-->
	</h3>
	<div style="overflow:auto;height:518px;">
		<div style="float:left;width:99%;">
			<?
			foreach($lstHeadings as $heading){
				$heading->displayArbo(module_wce::getTemplatePath("common/display_browser_site_root.tpl.php"),
									  module_wce::getTemplatePath("common/display_browser_site_rubrique.tpl.php"),
									  module_wce::getTemplatePath("common/display_browser_site_article.tpl.php"),
									  $lstOpenHeading,
									  $articleid);
			}
			//echo wce_build_tree($headings, $articles,0, '', 1, '', $headingid,$articleid,array(),array(),$_SESSION['wce_expand_tree']);
			?>
		</div>
	</div>
</div>
<div class="arbo_repli <? if(!$_SESSION['wce_display_tree']) echo 'close_arbo'; ?>">
	<a href="javascript:void(0);">
		<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_'.(($_SESSION['wce_display_tree'])?'r':'d').'eplier_v.png'); ?>" />
	</a>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("div.arbo_repli a").click(function(){
			if ($('div.arborescence').is(":visible")){
				$('div.arborescence').animate({"width": "toggle"},{duration: "slow"});
				$('img',$(this)).attr("src","<? echo module_wce::getTemplateWebPath("/gfx/icon_deplier_v.png"); ?>");
				$("div.arbo_repli").addClass('close_arbo');
				$('div.content_arbo').animate({'width':'98%'},{duration: "slow"});
			}else{
				$('div.arborescence').animate({"width": "toggle"},{duration: "slow"});
				$('img',$(this)).attr("src","<? echo module_wce::getTemplateWebPath("/gfx/icon_replier_v.png"); ?>");
				$("div.arbo_repli").removeClass('close_arbo');
				$('div.content_arbo').animate({'width':'78%'},{duration: "slow"});
			}
			dims_xmlhttprequest('admin.php','dims_op=switch_display_arborescence');
		});
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
	});
</script>
