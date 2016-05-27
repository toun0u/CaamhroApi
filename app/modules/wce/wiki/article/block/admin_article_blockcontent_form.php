<form name="form_wce_block" style="margin:0;" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_SAVE_BLOC_C."&id=".$this->fields['id_article']); ?>" method="post" enctype="multipart/form-data">
<div style="padding:0px;overflow:auto;clear:both;width:100%;height:500px;background-color:#FFFFFF;">
	<input type="hidden" name="block_id" value="<? echo $this->fields['id']; ?>">
	<input type="hidden" name="content_id" value="<? echo $this->fields['section']; ?>">
<?
require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php') ;
$jour=date("j/m/Y");

$template_path_fck=$_SESSION['dims']['front_template_path'];

$heading = module_wiki::getRootHeading();

if ($heading->fields['fckeditor']=='' && isset($headings['list'][$heading->fields['id']]['fckeditor']) && $headings['list'][$heading->fields['id']]['fckeditor']!='') {
	$heading->fields['fckeditor']=$headings['list'][$heading->fields['id']]['fckeditor'];
}
if ($heading->fields['fckeditor']!='') {
	$template_path_fck.='/fckstyles/'.$heading->fields['fckeditor'];
	$customfck='/fckstyles/'.$heading->fields['fckeditor'];
}

$oFCKeditor = new FCKeditor("fck_wce_article_draftcontent") ;

$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
if ($basepath == '/') $basepath = '';

$oFCKeditor->BasePath = "{$basepath}/FCKeditor/";
$oFCKeditor->Width='98%';
$oFCKeditor->Height='450';
// default value
$oFCKeditor->Value= $this->fields['draftcontent'.$this->fields['section']];

//$oFCKeditor->Config['ToolbarLocation'] = 'Out:parent(xToolbar)' ;
$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/wce/fckeditor/skins/default/" ;

if (file_exists("{$template_path_fck}/fckstyles.xml")) $oFCKeditor->Config['StylesXmlPath'] = $basepath . substr($template_path_fck,1) . '/fckstyles.xml';

if (file_exists("{$template_path_fck}/fckeditorarea.css")) {
	$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/wce/fckeditor/fckconfigrestrict.js";
	$oFCKeditor->Config['EditorAreaCSS'] = $basepath . substr($template_path_fck,1) . '/fckeditorarea.css';
}
else {
	$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/wce/fckeditor/fckconfig.js"	;
}

$oFCKeditor->Config['BaseHref'] = "{$basepath}/";

$oFCKeditor->PluginsPath = "{$basepath}/FCKeditor/editor/plugins/" ;
$oFCKeditor->Config['PluginsPath'] = "{$basepath}/FCKeditor/editor/plugins/" ;

$oFCKeditor->Config['DimsUser']="{$_SESSION['dims']['user']['firstname']} {$_SESSION['dims']['user']['lastname']} a ecrit le $jour :";

$tab_editor = $oFCKeditor->CreateHTML("FCKeditor") ;

echo $tab_editor;
?>
</div>
<div style="padding:0px;overflow:auto;clear:both;width:98%;background-color:#FFFFFF;text-align:right">
	<input type="button" onclick="javascript:dims_hidepopup();" value="Fermer"/>
	<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
</div>
</form>
