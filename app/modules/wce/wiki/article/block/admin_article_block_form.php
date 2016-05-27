<?php
// TODO : Utiliser des constantes de langue Dims
?>
<form name="form_wce_block" style="margin:0;" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_SAVE_BLOC."&id=".$this->fields['id_article']); ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="block_id" value="<? echo $this->fields['id']; ?>">
<input type="hidden" name="section" value="<? echo $this->fields['section']; ?>">
<input type="hidden" name="wce_block_id_article" value="<? echo $this->fields['id_article']; ?>">
<input type="hidden" name="wce_block_id_lang" value="<? echo $this->fields['id_lang']; ?>">

<?
// construction des proprietes des blocs
$toolbar=array();
$wce_block_properties = dims_load_securvalue('wce_block_properties', dims_const::_DIMS_NUM_INPUT, true, true);

if ($wce_block_properties>=0) {
    $_SESSION['dims']['wce_block_properties']=$wce_block_properties;
}
if (!isset($_SESSION['dims']['wce_block_properties'])) $_SESSION['dims']['wce_block_properties']=0; // generic

$typetag=$_SESSION['dims']['wce_block_properties'];
$arrayproperties=array();
$i=1;
$arrayproperties[$i]['label']="General";
$arrayproperties[$i]['selected']=($_SESSION['dims']['wce_block_properties']==0) ? true : false;
$arrayproperties[$i]['icon']='./common/img/tag.png';
$i++;
$arrayproperties[$i]['label']="Position";
$arrayproperties[$i]['selected']=($_SESSION['dims']['wce_block_properties']==0) ? true : false;
$arrayproperties[$i]['icon']='./common/img/tag.png';
$i++;
$arrayproperties[$i]['label']="Font";
$arrayproperties[$i]['selected']=($_SESSION['dims']['wce_block_properties']==0) ? true : false;
$arrayproperties[$i]['icon']='./common/img/tag.png';
$i++;
$arrayproperties[$i]['label']="Border";
$arrayproperties[$i]['selected']=($_SESSION['dims']['wce_block_properties']==0) ? true : false;
$arrayproperties[$i]['icon']='./common/img/tag.png';
$i++;
$arrayproperties[$i]['label']="Background";
$arrayproperties[$i]['selected']=($_SESSION['dims']['wce_block_properties']==0) ? true : false;
$arrayproperties[$i]['icon']='./common/img/tag.png';

$ind=  rand(1, 1000);

echo '<script language="javascript" type="text/javascript">

        $( "#wce_tabs'.$ind.'" ).tabs();
	</script>';

	echo "<div id=\"wce_tabs".$ind."\"><ul>";
	foreach ($arrayproperties as $id => $value) {
		echo '<li><a href="#tabs-'.$id.'">'.$value['label'].'</a></li>';
	}
echo "</ul>";

$positionB = array('','relative','absolute','static','inherit');
$displayB = array('','block','inline','none','inline-block','table','run-in','list-item');
$borderB = array('','none','hidden','dotted','dashed','solid','double','groove','ridge','inset','outset');
$floatB = array('','left','right','top','bottom');

?>
	<div id="tabs-1">
	<?
	require_once module_wiki::getTemplatePath('/article/block/admin_article_block_form_general.php');
	?>
	</div>
	<div id="tabs-2">
	<?
	require_once module_wiki::getTemplatePath('/article/block/admin_article_block_form_position.php');
	?>
	</div>
	<div id="tabs-3">
	<?
	require_once module_wiki::getTemplatePath('/article/block/admin_article_block_form_font.php');
	?>
	</div>
	<div id="tabs-4">
	<?
	require_once module_wiki::getTemplatePath('/article/block/admin_article_block_form_border.php');
	?>
	</div>
	<div id="tabs-5">
	<?
	require_once module_wiki::getTemplatePath('/article/block/admin_article_block_form_background.php');
	?>
	</div>

</div>
<div style="clear:both;text-align:right;float:left;padding:5px;height:30px;width:95%;">
	<?
	echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"","javascript:dims_hidepopup();");
	echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.form_wce_block.submit();",'','');
	?>
</div>
</form>
<script language="javascript">
$(document).ready(function(){
    $('#wce_block_font-color, #wce_block_border-color').ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) {
            $(el).val('#'+hex);
            $(el).css('background-color','#'+hex);
            $(el).ColorPickerHide();
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor(this.value);
        }
    })
    .bind('keyup', function(){
        $(this).ColorPickerSetColor(this.value);
    });
});
</script>
