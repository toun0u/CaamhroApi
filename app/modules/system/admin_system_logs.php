<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LOGS'],'100%'); ?>
<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "connectedusers");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="connectedusers">

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_CONNECTEDUSERS']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_CONNECTEDUSERS']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
			<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
		</button>
	</TD>
</TR>
</TABLE>
</FORM>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "actionhistory");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="actionhistory">
<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_ACTIONHISTORY']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_ACTIONHISTORY']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
			<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
		</button>
	</TD>
</TR>
</TABLE>
</FORM>
<? echo $skin->close_simplebloc(); ?>
