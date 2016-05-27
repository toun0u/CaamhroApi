<?php
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DOMAIN_ADD']);
?>
<FORM NAME="form_domain" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op");
	$token->field("domain_id");
	$token->field("domain_domain");
	$token->field("domain_webmail_http_code");
	$token->field("domain_ssl");
	$token->field("domain_mobile");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_domain">
<?php
   if ($domain->fields['id']==-1)
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"domain_id\" VALUE=\"?>\">";
   else
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"domain_id\" VALUE=\"".$domain->fields['id']."\">";
?>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_DOMAIN']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT type="Text" size="30" id="domain_domain" name="domain_domain" VALUE="<? echo $domain->fields['domain']; ?>">
	<?
		if (isset($_GET['error'])) echo $_DIMS['cste']['_DIMS_LABEL_DOMAIN_ALREADYEXISTS'];
	?>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_ACTIVATED_HTTPEMAIL_KEY']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT type="Text" size="30" id="domain_webmail_http_code" name="domain_webmail_http_code" VALUE="<? echo $domain->fields['webmail_http_code']; ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT>SSL :&nbsp;</TD>
	<TD ALIGN=LEFT>
		<input type="checkbox" name="domain_ssl" <?php if($domain->fields['ssl']) echo "checked"; ?> value="1">
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT>Mobile :&nbsp;</TD>
	<TD ALIGN=LEFT>
		<input type="checkbox" name="domain_mobile" <?php if($domain->fields['mobile']) echo "checked"; ?> value="1">
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT COLSPAN=2>
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
			<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></span>
		</button>
	</TD>
</TR>
</TABLE>
</FORM>

<script language="javascript">
document.getElementById('domain_domain').focus();
</script>

<?php
	echo $skin->close_simplebloc();
?>
