<?php

$article = $this->get('article');
$reference = $this->get('reference');

$form = new Dims\form(array(
	'name' 			=> 'ref_'.$reference->fields['id'],
	'object'		=> $reference,
	'action'		=> get_path('articles', 'show', array('sc' => 'references', 'sa' => 'save', 'id' => $article->get('id'))),
	'submit_value' 	=> dims_constant::getVal('_DIMS_SAVE'),
	'back_url'		=> get_path('articles', 'show', array('sc' => 'references', 'sa' => 'list', 'id' => $article->get('id'))),
));

$id_docfileselected = dims_load_securvalue('id_docfileselected', dims_const::_DIMS_NUM_INPUT, true, true);

if (!empty($id_docfileselected)) {
	$reference->fields['id_doc'] = $id_docfileselected;
	$reference->fields['type'] = article_reference::TYPE_DOC;
}

$delDocLk = "";
if ($reference->get('id_doc') > 0) {
	$doc = docfile::find_by(array('id'=>$reference->get('id_doc'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);

	if (!empty($doc)) {
		$lab = trim($reference->get('name'));

		if (empty($lab)) {
			$reference->fields['name'] = $doc->get('name');
		}

		$delDocLk .= $doc->get('name')."&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:initDocLink();\"><img src=\"./common/img/delete.png\"></a>";
	}
}

if (!$reference->isNew()) {
	$form->add_hidden_field(array(
		'name' 		=> 'id_reference',
		'db_field'	=> 'id',
	));
}

$form->add_hidden_field(array(
	'name'      => 'ref_id_doc',
	'db_field'  => 'id_doc',
));

$form->add_text_field(array(
	'name' 		=> 'ref_name',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_LABEL'],
	'db_field' 	=> 'name',
	'mandatory'	=> true,
));

$types = array();
foreach (article_reference::getTypeList() as $type) {
	$types[$type] = ucfirst(article_reference::getTypeLabel($type));
}

$form->add_select_field(array(
	'name' 		=> 'ref_type',
	'label'		=> $_SESSION['cste']['_DIMS_SELECT_TYPE'],
	'db_field' 	=> 'type',
	'mandatory'	=> true,
	'options'	=> $types,
));

$maxPosition = article_reference::getMaxPosition($article->fields['id']);

if ($reference->isNew()) {
	$maxPosition++;
    // update Pat pour mettre à la suite des références
    $reference->fields['position']=$maxPosition;
}

$poss = array();
for ($i = 1; $i <= $maxPosition; $i++) {
	$poss[$i] = $i;
}

$form->add_select_field(array(
	'name' 		=> 'ref_position',
	'label'		=> $_SESSION['cste']['_POSITION'],
	'db_field' 	=> 'position',
	'mandatory'	=> true,
	'options'	=> $poss,
));

$form->add_text_field(array(
	'name' 		=> 'ref_url',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_URL'],
	'db_field' 	=> 'url',
	'mandatory'	=> true,
));

$form->add_simple_text(array(
	'name' 		=> 'link_link',
	'label'		=> $_SESSION['cste']['_DOCS'],
	'value'		=> '<span id="descFileRef">'.$delDocLk.'</span>&nbsp;<a href="javascript:void(0);" class="file-select">Choisir</a>',
));

$form->build();
// on supprime ce qu'il peut y avoir en temporary
$sid = sha1(uniqid(""). MD5(microtime()));
$temp_dir = DIMS_TMP_PATH;
$session_dir = $temp_dir."/".$sid;

if (file_exists($session_dir)) dims_deletedir($session_dir);
dims_makedir($session_dir);

$upload_dir = _DIMS_PATHDATA."/uploads/".$sid."/";
if (!is_dir($upload_dir)) dims_makedir ($upload_dir);

$_SESSION['dims']['uploaded_sid']=$sid;

$upload_size_file = $session_dir."/upload_size";
$upload_finished_file = $session_dir."/upload_finished";

if (file_exists($upload_size_file)) unlink($upload_size_file);
if (file_exists($upload_finished_file)) unlink($upload_finished_file);
?>
<script type="text/javascript" src="/assets/javascripts/common/upload/javascript/uploader.js"></script>
<script type="text/javascript">
var uploads = new Array();
var upload_cell, file_name;
var count=0;
var checkCount = 0;
var check_file_extentions = true;
var sid = '<? echo $_SESSION['dims']['uploaded_sid'] ; ?>';
var page_elements = ["toolbar","page_status_bar"];
var img_path = "../common/img/";
var path = "";
var bg_color = false;
var status;
var debug = false;

function setDocUrl(id_doc,name,id_popup) {
	dims_closeOverlayedPopup(id_popup);
	$("#ref_<?= $reference->get('id'); ?> #ref_id_doc").val(id_doc);
	var ext=name.indexOf(".", 0);
	if (ext==0) $("#ref_<?= $reference->get('id'); ?> #ref_label").val(name.substring(0));
	else $("#ref_<?= $reference->get('id'); ?> #ref_label").val(name.substring(0,ext));

	$("#ref_<?= $reference->get('id'); ?> #descFileRef").html(name+"&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:initDocLink();\"><img src=\"./common/img/delete.png\"></a>");
}

function initDocLink() {
	$("#ref_<?= $reference->get('id'); ?> #ref_id_doc").val(0);
	$("#ref_<?= $reference->get('id'); ?> #descFileRef").html('');
}

$(document).ready(function(){
	$('#ref_<?= $reference->get('id'); ?> #ref_type').change(function(){
		if($(this).val() != <?= article_reference::TYPE_DOC; ?>){
			$('#ref_<?= $reference->get('id'); ?> #ref_url').parents('tr:first').show();
			$('#ref_<?= $reference->get('id'); ?> #link_link').parents('tr:first').hide();
			$('#ref_<?= $reference->get('id'); ?> #ref_url').attr('rel','requis');
			$('#ref_<?= $reference->get('id'); ?> #ref_id_doc').attr('rel','');
		}else{
			$('#ref_<?= $reference->get('id'); ?> #link_link').parents('tr:first').show();
			$('#ref_<?= $reference->get('id'); ?> #ref_url').parents('tr:first').hide();
			$('#ref_<?= $reference->get('id'); ?> #ref_url').attr('rel','');
			$('#ref_<?= $reference->get('id'); ?> #ref_id_doc').attr('rel','requis');
		}
	});
	$('#ref_<?= $reference->get('id'); ?> .file-select').click(function(){
		var id_popup = dims_openOverlayedPopup(900,700);
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', 'dims_op=doc_selectfile&mode=simple&id_popup='+id_popup,'','p'+id_popup);
	});
	$('#ref_<?= $reference->get('id'); ?> #ref_type').change();
});
</script>

