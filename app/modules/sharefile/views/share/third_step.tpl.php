<link type="text/css" rel="stylesheet" href="./common/modules/doc/include/styles.css" media="screen" />
<?php
$files = $this->get('files');
if (!empty($files)) {
	// mode duplication
		echo "<div style=\"100%;\"><span style=\"float:left;width:100%;font-weight:bold;\">Fichiers attach&eacute;s à l'envoi pr&eacute;c&eacute;dent</span>";
		foreach ($files as $file) {
			echo "<div style=\"float:left;width:140px;cursor: default;padding-left:10px;text-align:center;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
					<a style=\"text-decoration:none;\" href=".$file['downloadlink']." title=\"Voir le document.\">";

			switch ($file['extension']) {
				case "ppt":
					$namefile="ppt32.png";
					break;
				case "pdf":
					$namefile="pdf32.png";
					break;
				case "wav":
					$namefile="sound32.png";
					break;
				case "doc":
				case "docx":
				case "odt":
					$namefile="doc32.png";
					break;
				case "xls":
					$namefile="xls32.png";
					break;
				case "jpg":
				case "png":
				case "bmp":
				case "gif":
				case "xcf":
				case "psd":
					$namefile="img32.png";
					break;
				case "avi":
				case "mpg":
				case "mpeg":
				case "mpeg4":
					$namefile="video32.png";
					break;
				default :
					$namefile="file32.png";
					break;
			}

			if (strlen($file['name'])>15) {
				$title=dims_strcut($file['name'],15);
				$title.=" .".$file['extension'];
			}
			else $title=$file['name'];

			echo "<img style=\"\" src=\"./common/modules/sharefile/img/".$namefile."\" border=\"0\">
				<br><font style=\"font-size:11px;\">".$title."<br />".sprintf("%.02f",$file['size']/1048576)." Mo</font></a>
				&nbsp; <a style=\"font-size:11px;\" href=\"".dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'delete_file', 'id_doc' => $file['id'])))."\"><img border=\"0\" src=\"./common/img/delete.png\" alt=\"Enlever\" title=\"Enlever\">&nbsp;Enlever</a></div>";
		}
		echo "</div>";

}
?>
<div style="padding:2px;overflow:hidden">
	<span style="width:12%;display:block;float:left;">
		<img src="/common/modules/sharefile/img/icon_upload.png">
	</span>
	<span style="width:80%;display:block;float:left;font-size:20px;color:#424242;font-weight:bold; margin-left: 20px; line-height:63px;">
		Upload du fichier
	</span>
</div>
<div class="doc_fileform" style="clear:both;margin-top:10px;float:left;width:100%;">
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
<form id="docfile_add" name="docfile_add" action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'save_files'))); ?>" method="post" enctype="multipart/form-data">
</form>
<div class="doc_fileform_main">
	<script type="text/javascript">
		var uploads = new Array();
		var upload_cell, file_name;
		var count=0;
		var checkCount = 0;
		var check_file_extentions = true;
		var sid = '<?= session_id() ; ?>';
		var page_elements = ["toolbar","page_status_bar"];
		var img_path = "../common/img/";
		var path = "";
		var bg_color = false;
		var status;
		var debug = false;
		var param1= false;
		var param2=<?= (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
	</script>
	<script type="text/javascript" src="./include/upload_share/javascript/uploader.js"></script>

	<div class="dims_form" style="padding-top:2px;">
		<!--<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<?= dims_constant::getVal('_DOC_LABEL_ADD_OTHER_FILE'); ?>">
		-->
		<div id="ScrollBox" style="overflow:auto;">
			<form id="frmUpload_0" name="frmUpload_0" target="uploadForm" method="post" enctype="multipart/form-data" action="/cgi-bin/upload.cgi?sid=<?= session_id(); ?>">

			<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody>
			</tbody></table>
			</form>
			<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;width:800px;height:100px;overflow:auto;" src=""></iframe>
		</div>
		</form>
	</div>
</div>
<div id="sharefile_button" style="padding-top:20px;clear:both;float:left;width:100%;">
	<span style="width:50%;display:block;float:left;text-align:right;"><a style="text-decoration:none;padding-right:50px;float:left" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'second_step'))); ?>"><span style="float:left;margin-left:10px;line-height:63px;margin-right:10px">Cliquez pour retourner a l'étape précédente</span><img style="border:0px;" src="./common/modules/sharefile/img/retour_etape2.png" alt="<?= dims_constant::getVal('_DIMS_PREVIOUS'); ?>"></a></span>
<?
if (isset($_SESSION['share']['duplicate']) && $_SESSION['share']['duplicate']==1) $display="visible;";
else $display="hidden";
?>
<span id="btn_upload" style="width:24%;display:block;float:right;visibility:<?= $display;?>;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:upload();"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/bouton_valider.png" alt="<?= dims_constant::getVal('_DIMS_SAVE'); ?>"></a></span>
</div>

<?
$http_host = dims::getInstance()->getProtocol().((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '');

echo "<script type=\"text/javascript\">status = document.getElementById(\"status\");setVariables(\"$http_host\",\"".dims_constant::getVal('_DOC_MSG_UPLOAD_FILE')."\",\"".dims_constant::getVal('_DOC_MSG_UPLOAD_WAITING')."\",\"".dims_constant::getVal('_DOC_MSG_COPY_FILE')."\",\"".dims_constant::getVal('_DOC_MSG_UPLOAD_ERROR')."\",\"".dims_constant::getVal('_DOC_MSG_UPLOAD_ERROREXT')."\");
createFileInput();</script>";
?>
