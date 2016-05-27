<?
if ($_SESSION['dims']['tmp_event_model']==0)
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_ADD']);
else
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_MODIFY']);
?>
	<FORM NAME="form_event_model" ACTION="<? echo "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=save_model_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL; ?>" METHOD="POST">
	<TABLE CELLPADDING="2" CELLSPACING="1">
	<TR>
		<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?>:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<INPUT CLASS="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="event_model_label" VALUE="<? echo ($eventm->fields['label']); ?>">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT COLSPAN=2>
			<INPUT TYPE="Submit" CLASS="button" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
		</TD>
	</TR>
	</TABLE>
	</FORM>
<? echo $skin->close_simplebloc(); ?>
