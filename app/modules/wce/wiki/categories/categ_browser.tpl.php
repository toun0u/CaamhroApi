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
$complement = (($id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true)) != '' && $id > 0) ? "&id=$id" : "";
?>

<div class="root_categ" onclick="document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_EDIT_CATEG.'&id_categ='.$this->fields['id'].$complement); ?>';">
	<? echo $_SESSION['cste']['_DOC_ROOT']; ?>
</div>
<div class="browser_categ">
	<?
	$this->display(module_wiki::getTemplatePath('/categories/categ_browser_lvl.tpl.php'));
	?>
	<p style="clear: both; height: 1px;"></p>
</div>
<input type="hidden" value="<? echo $num; ?>" name="categ_id_parent" id="id_parent" />
<script type="text/javascript">
	$(document).ready(function(){
		$('div.browser_categ li').click(function(){
			document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_EDIT_CATEG.'&id_categ='); ?>'+$(this).attr('ref')+"<? echo $complement; ?>";
		});
	});
</script>