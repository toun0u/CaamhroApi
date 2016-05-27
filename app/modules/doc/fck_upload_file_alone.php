<?
dims_init_module('doc');
$firstmodule=0;
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
		<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="doc_uploadfile_alone">
		<INPUT TYPE="HIDDEN" NAME="urlreturn" VALUE="<?= (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''); ?>">
		<INPUT TYPE="HIDDEN" NAME="currentsel" VALUE="<? echo $currentsel; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_module" id="docfile_id_module" VALUE="<? echo $firstmodule; ?>">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_folder" id="docfile_id_folder" VALUE="0">
		<INPUT TYPE="HIDDEN" NAME="docfile_id_user_modify" VALUE="<? echo $_SESSION['dims']['userid']; ?>">
		<table width="100%" height="100%">
			<TR>
				<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_FILE']; ?>:&nbsp;</TD>
				<TD ALIGN=LEFT>
					<div id="ScrollBox" style="overflow:auto;">
						<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
						<input type="file" name="file_upload" />
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
