<?php
$num = 0;
$opened = array();
if(is_null($par = $this->getLightAttribute('parent'))){
	$num = $this->fields['id'];
}else{
	$num = $par->fields['id'];
	$opened = explode(';',$par->fields['parents']);
	$opened[] = $par->fields['id'];
}
$this->setLightAttribute('opened',$opened);
$this->setLightAttribute('current',$num);
?>

<div class="root_categ" onclick="javascript:selectCateg(<? echo $this->fields['id']; ?>);">
	<? echo $_SESSION['cste']['DON_T_USE_CATEGORY']; ?>
</div>
<div class="browser_categ" id="browser_categ_id">
	<?
	$this->display(module_wiki::getTemplatePath('/categories/ajax_categ_browser_lvl.tpl.php'));
	?>
	<p style="clear: both; height: 1px;"></p>
</div>
<input type="hidden" value="<? echo $num; ?>" name="id_categ" id="id_parent" />
<script type="text/javascript">
	$(document).ready(function(){
		$('div.browser_categ li').live('click',function(){
			selectCateg($(this).attr('ref'));
		});
	});
	window['selectCateg'] = function selectCateg(id){
		$('input#id_parent').val(id);
		dims_xmlhttprequest_todiv('<? echo dims::getInstance()->getScriptEnv(); ?>','dims_op=wiki&op_wiki=article_refresh_categ&id='+id,'','browser_categ_id');
	}
</script>