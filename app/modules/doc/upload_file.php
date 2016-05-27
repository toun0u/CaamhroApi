<?
dims_init_module('doc');
$firstmodule=0;

// on supprime ce qu'il peut y avoir en temporary
$sid = sha1(uniqid(""). MD5(microtime()));
$temp_dir = DIMS_TMP_PATH;
$session_dir = $temp_dir."/".$sid;

if (file_exists($session_dir)) dims_deletedir($session_dir);
dims_makedir($session_dir);

$upload_dir = _DIMS_PATHDATA."/uploads/".$sid."/";
if (!is_dir($upload_dir)) dims_makedir ($upload_dir);

$_SESSION['dims']['uploaded_sid']=$sid;
?>
<table>
<TR CLASS="Titre">
	<TD ALIGN=CENTER COLSPAN=2>
	<script language="JavaScript" type="text/javascript">
	window['activeNewDoc'] = function activeNewDoc() {
		var iddiv=document.getElementById('newdoc');
		iddiv.style.visibility="visible";
		iddiv.style.display="block";
	}
	</script>
	<a href="#" onclick="javascript:activeNewDoc();">
		<img border="0" src="/common/modules/wce/img/ico_add.gif"/>&nbsp;<? echo $_DIMS['cste']['_DIMS_ADD']; ?> un Nouveau Document
	</a>
	</TD>
</TR>
<tr>
	<td>
	<div id="newdoc" style="visibility:hidden;display:none;">
	<form id="docfile_add" name="docfile_add" action="<? echo $scriptenv; ?>" METHOD="post" ENCTYPE="multipart/form-data">
	<table width="100%" height="100%">

		<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="doc_uploadfile">
		<INPUT TYPE="HIDDEN" NAME="currentsel" VALUE="<? echo $currentsel; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_module" id="docfile_id_module" VALUE="<? echo $firstmodule; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_folder" id="docfile_id_folder" VALUE="0">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_user_modify" VALUE="<? echo $_SESSION['dims']['userid']; ?>">
		<TR>
			<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_FILE']; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<div id="ScrollBox" style="overflow:auto;">
					<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
					<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;display:none;" src=""></iframe>
				</div>
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT COLSPAN=2>
				<span id="btn_upload"><INPUT TYPE="button" onclick="javascript:upload();" CLASS="Button" VALUE="<? echo $_DIMS['cste']['_DIMS_ADD']; ?>"></span>
			</TD>
		</TR>
	</table>
	</FORM>
	</div>
	</td>
</tr>
</table>
<script type="text/javascript">
	var uploads = new Array();
	var upload_cell, file_name;
	var count=0;
	var checkCount = 0;
	var check_file_extentions = true;
	var sid = '<? echo $_SESSION['dims']['uploaded_sid']; ?>';
	var page_elements = ["toolbar","page_status_bar"];
	var img_path = "../common/img/";
	var path = "";
	var bg_color = false;
	var status;
	var debug = false;
	var param1=<? echo ($op == 'file_add') ? 'true' : 'false'; ?>;
	var param2=<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
</script>
<script type="text/javascript" src="/common/js/upload/javascript/uploader.js"></script>
<script type="text/javascript">
	createFileInput();
</script>
