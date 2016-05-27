<?
dims_init_module('doc');
?>
<table>
<TR CLASS="Titre">
	<TD ALIGN=CENTER COLSPAN=2>
	<script language="JavaScript" type="text/javascript">
	function activeNewDoc()
	{
		var iddiv=document.getElementById('newdoc');
		iddiv.style.visibility="visible";
		iddiv.style.display="block";
	}
	</script>
	<a href="#" onclick="javascript:activeNewDoc();"><img border="0" src="/common/modules/wce/img/ico_add.gif"/>&nbsp;<? echo $_DIMS['cste']['_DIMS_ADD']; ?> un Nouveau Document</a>
	</TD>
</TR>
<tr>
	<td>
	<div id="newdoc" style="visibility:hidden;display:none;">
		<FORM NAME="form_upload" ACTION="<? echo $scriptenv; ?>" METHOD="post" ENCTYPE="multipart/form-data">
		<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="doc_uploadfile">
		<INPUT TYPE="HIDDEN" NAME="img" VALUE="<? echo $img; ?>">
		<INPUT TYPE="HIDDEN" NAME="doc_id_module" id="doc_id_module" VALUE="<? echo $firstmodule; ?>">
		<INPUT TYPE="HIDDEN" NAME="doc_id_docfolder" id="doc_id_docfolder" VALUE="0">
		<INPUT TYPE="HIDDEN" NAME="doc_id_user_create" VALUE="<? echo $_SESSION['session_userid']; ?>">
		<INPUT TYPE="HIDDEN" NAME="doc_id_user_modify" VALUE="<? echo $_SESSION['session_userid']; ?>">
		<table width="100%" height="100%">
		<TR>
			<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_FILE']; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<INPUT CLASS="Text" TYPE="file" SIZE=30 MAXLENGTH=255 NAME="fileform">
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT COLSPAN=2>
				<INPUT TYPE="Submit" CLASS="Button" VALUE="<? echo $_DIMS['cste']['_DIMS_ADD']; ?>">
			</TD>
		</TR>
		</table>
		</FORM>
	</div>
	</td>
</tr>
</table>