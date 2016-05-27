<script type="text/javascript">
	function refreshChangeModule(id){
		dims_xmlhttprequest_todiv('admin.php','dims_op=refreshObjects&id='+id,'','objects');
		dims_xmlhttprequest_todiv('admin.php','dims_op=refreshCategories&id='+id,'','lstCategories');
		if (id == 0)
			document.getElementById('objects').disabled=true;
		else
			document.getElementById('objects').disabled=false;
	}
	function refreshChangeObject(idMod,idObj){
		dims_xmlhttprequest_todiv('admin.php','dims_op=refreshCategories2&id='+idMod+'&obj='+idObj,'','lstCategories');
	}
</script>
<style>
	div.listeCateg{
		border: 1px solid #D1D1D1;
		margin: 10px;
		-moz-border-radius:4px;
		-webkit-border-radius:4px;
		color: #5A5757;
		min-width:700px;
	}
	li.browser{
		cursor:pointer;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
	}
	li.selected{
		background: #F0F0F0;
	}
	div.listeCateg ul{
		border-right: 1px solid #D1D1D1;
		height:350px;
		overflow:auto;
	}
	div.listeCateg div{
		max-width:200px;
	}
</style>
<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CATEGORY'],'100%');
/*require_once DIMS_APP_PATH . '/modules/system/class_module.php';
$sel = "SELECT	id, label, id_module_type, id_workspace
		FROM	dims_module";
$res = $db->query($sel);
while ($r = $db->fetchrow($res)){
	echo 'Module : '.$r["label"].' ('.$r["id"].') > Module type : '.$r["id_module_type"].' | Workspace : '.$r["id_workspace"].'<br />';
	$module = new module();
	$module->open($r['id']);
	$categories = $module->getCategorySystemsAllowed($_SESSION['dims']['userid']);
	foreach($categories as $c){
		$c->initDescendance();
		$c->simpleDraw();
		echo '<br/>-------------<br/>';
	}
}*/
?>
<div style="margin-top:10px;margin-left:10px;margin-right:10px;">
<?
$sel = "SELECT	id, description
		FROM	dims_module_type
		WHERE	system = 0";
$res = $db->query($sel);
$lstModType = array();
$lstModType[0] = "<option value=\"0\">-- Choisissez un module type --</option>";
while ($r = $db->fetchrow($res))
	if (isset($_SESSION['dims']['categFiltre']['module']) && $_SESSION['dims']['categFiltre']['module'] == $r['id'])
		$lstModType[$r['id']] = '<option selected=true value="'.$r['id'].'">'.$r['description'].'</option>';
	else
		$lstModType[$r['id']] = '<option value="'.$r['id'].'">'.$r['description'].'</option>';

echo $_DIMS['cste']['_DIMS_LABEL_MODULES'].' : ';
echo '<select onchange="javascript:refreshChangeModule(this.options[this.selectedIndex].value);" name="moduleType" id="moduleType">'.implode('',$lstModType).'</select>';
echo 'Objets associ&eacute;s à ce module type : ';
echo '<span id="objects">';
echo '<select onchange="javascript:refreshChangeObject(document.getElementById(\'moduleType\').options[document.getElementById(\'moduleType\').selectedIndex].value, this.options[this.selectedIndex].value);" disabled=true name="objects"><option value="0">-- Choisissez un objet Dims --</option></select>';
echo '</span>';

if (isset($_SESSION['dims']['categFiltre']['module']) && $_SESSION['dims']['categFiltre']['module'] > 0){
	?>
		<script type="text/javascript">
			refreshChangeModule(<? echo $_SESSION['dims']['categFiltre']['module']; ?>);
		</script>
	<?
}

?>
</div>
<div id="lstCategories" style="margin-bottom:10px;margin-left:10px;margin-right:10px;">
</div>

<script type="text/javascript" src="/js/contextMenu/jquery.contextMenu.js" ></script>
<script type="text/javascript">
	document.write('<link type="text/css" rel="stylesheet" href="/js/contextMenu/jquery.contextMenu.css" media="screen" />');
	$.contextMenu({
		selector: 'div.listeCateg div[id!="0"] li.browser > img',
		trigger: "left",
		items: {
			edit: {name: "Editer", callback: function(){
														dims_showcenteredpopup('',200,500,'dims_popup');
														dims_xmlhttprequest_todiv('admin.php','dims_op=editCateg&id='+this.parents("li.browser").children("input").val(),'','dims_popup');
														}, icon: "edit"},
			del: {name: "Supprimer", callback: function(){
														document.location.href="<? echo $dims->getScriptEnv().'?op=deleteCateg&id='; ?>"+this.parents("li.browser").children("input").val();
														}, icon: "delete"},
			sep1: "---------",
			up: {name: "Monter", callback: function(){
														var liPrev = this.parents("li.browser").prev("li.browser");
														var liFirst = $("li.browser:first-child",this.parents("ul:first"));
														if (liPrev != null && liFirst.attr("name") != this.parents("li.browser").attr("name")){
															this.parents("li.browser").insertBefore(liPrev);
															dims_xmlhttprequest('admin.php','dims_op=upCateg&id='+this.parents("li.browser").children("input").val(),true,false);
														}
														}, icon: "up"},
			down: {name: "Descendre", callback: function(){
														var liNext = this.parents("li.browser").next("li.browser");
														var liLast = $("li.browser:last-child",this.parents("ul:first"));
														if (liNext != null && liNext.attr("name") != liLast.attr("name")){
															this.parents("li.browser").insertAfter(liNext);
															dims_xmlhttprequest('admin.php','dims_op=downCateg&id='+this.parents("li.browser").children("input").val(),true,false);
														}
														}, icon: "down"},
			sep2: "---------",
			quit: {name: "Fermer", callback: $.noop, icon: "quit"}
		}
	});
	$.contextMenu({
		selector: 'div.listeCateg div[id="0"] li.browser > img',
		trigger: "left",
		items: {
			edit: {name: "Editer", callback: function(){
														dims_showcenteredpopup('',200,500,'dims_popup');
														dims_xmlhttprequest_todiv('admin.php','dims_op=editCateg&id='+this.parents("li.browser").children("input").val(),'','dims_popup');
														}, icon: "edit"},
			del: {name: "Supprimer", callback: function(){
														document.location.href="<? echo $dims->getScriptEnv().'?op=deleteCateg&id='; ?>"+this.parents("li.browser").children("input").val();
														}, icon: "delete"},
			sep1: "---------",
			quit: {name: "Fermer", callback: $.noop, icon: "quit"}
		}
	});
</script>
