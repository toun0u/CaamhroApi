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
	<form id="docfile_add" name="docfile_add" action="<?= dims::getInstance()->getScriptEnv(); ?>" METHOD="post" ENCTYPE="multipart/form-data">
	<input type="hidden" name="CKEditorFuncNum" value="<?= dims_load_securvalue('CKEditorFuncNum',dims_const::_DIMS_NUM_INPUT,true,true,true); ?>" />
	<table width="100%" height="100%">

		<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="doc_uploadfile">
		<INPUT TYPE="HIDDEN" NAME="currentsel" VALUE="<? echo $currentsel; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_module" id="docfile_id_module" VALUE="<? echo $firstmodule; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_folder" id="docfile_id_folder" VALUE="0">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_user_modify" VALUE="<? echo $_SESSION['dims']['userid']; ?>">
		<TR>
			<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_FILE']; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<div id="ScrollBox" style="overflow:none;">
					<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
					<input type="file" name="file_upload" />
				</div>
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT COLSPAN=2>
				<span id="btn_upload"><INPUT TYPE="submit" CLASS="Button" VALUE="<? echo $_DIMS['cste']['_DIMS_ADD']; ?>"></span>
			</TD>
		</TR>
	</table>
	</FORM>
	</div>
	</td>
</tr>
</table>
