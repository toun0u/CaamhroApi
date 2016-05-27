<link type="text/css" rel="stylesheet" href="./common/modules/doc/include/styles.css" media="screen" />
<div class="doc_fileform">
<?php
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

// on supprime ce qu'il peut y avoir en temporary
$sid = session_id();
$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
$session_dir = $temp_dir.$sid;
$upload_size_file = $session_dir."/upload_size";
$upload_finished_file = $session_dir."/upload_finished";

if (file_exists($upload_size_file)) unlink($upload_size_file);
if (file_exists($upload_finished_file)) unlink($upload_finished_file);

$docfile = new docfile();
$docfile->init_description();
?>
<form id="docfile_add" name="docfile_add" action="<?php echo $dims->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",	"save_sharefile");
	$token->field("addfile");
	$token->field("uploadForm");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_sharefile">

<div class="doc_fileform_main">
	<script type="text/javascript">
		var uploads = new Array();
		var upload_cell, file_name;
		var count=0;
		var checkCount = 0;
		var check_file_extentions = true;
		var sid = '<?php echo session_id() ; ?>';
		var page_elements = ["toolbar","page_status_bar"];
		var img_path = "../common/img/";
		var path = "";
		var bg_color = false;
		var status;
		var debug = false;
		var param1=<?php echo ($op == 'file_add') ? 'true' : 'false'; ?>;
		var param2=<?php echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
	</script>
	<script type="text/javascript" src="/js/upload/javascript/uploader.js"></script>

	<div class="dims_form" style="padding:2px;">
		<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<?php echo $_DIMS['cste']['_DOC_LABEL_ADD_OTHER_FILE']; ?>">

		<div id="ScrollBox" style="overflow:auto;">
			<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
			<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;" src=""></iframe>
		</div>
	</div>
</div>
<div id="sharefile_button" style="padding-top:20px;clear:both;float:left;width:100%;">
	<span style="width:50%;display:block;float:left;text-align:right;"><a style="text-decoration:none;padding-right:50px;" href="<?php echo dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=2"); ?>"><img style="border:0px;" src="./common/modules/sharefile/img/back.png" alt="<?php echo $_DIMS['cste']['_DIMS_PREVIOUS']; ?>"></a></span>
	<span id="btn_upload" style="width:50%;display:block;float:left;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:upload();"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<?php echo $_DIMS['cste']['_DIMS_SAVE']; ?>"></a></span>
</div>
</form>
<?php
global $dims;

$rootpath=$dims->getProtocol().$http_host;
echo "<script type=\"text/javascript\">status = document.getElementById(\"status\");setVariables(\"$rootpath\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']."\",\"".$_DIMS['cste']['_DOC_MSG_COPY_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']."\");createFileInput(path);</script>";
?>
