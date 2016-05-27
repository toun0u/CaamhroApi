<?php
$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);

$versioncomp='';
if ($versionid>0) $versioncomp='&versionid='.$versionid;

$lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
if (!empty($lang) && $lang > 0) $versioncomp .= "&lang=$lang";

foreach($_GET as $key => $val){
	if (substr($key,0,12) == "WCE_section_")
		$versioncomp .= "&$key=".dims_load_securvalue($key,dims_const::_DIMS_NUM_INPUT,true,true,false);
}

// on regarde le mode d'affichage
$url = module_wiki::getScriptEnv("sub=".module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_EDIT_ARTICLE."&articleid=".$this->fields['id']."&wce_mode=".$this->getLightAttribute('wce_mode')."&readonly=0&adminedit=1".$versioncomp."&lang=".$this->fields['id_lang']);

?>
<script type="text/javascript">
<?
include module_wiki::getTemplatePath('/include/javascript.php');
?>
</script>

<iframe name="wce_frame_editor" id="wce_frame_editor" style="border:0;width:100%;height:750px;margin:0;padding:0;" src="<? echo $url; ?>"></iframe>

<script type="text/javascript">
	$(document).ready(function(){
		$("#historic").load('admin.php?dims_op=wiki&op_wiki=get_historic');
	});
</script>