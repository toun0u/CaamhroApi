<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_TOOLS'],'100%'); ?>
// Sécurisation du formulaire par token
<? require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php'; ?>
$token = new FormToken\TokenField;
$token->field("");
$tokenHTML = $token->generate();
echo $tokenHTML;
<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>

<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_PHPINFO']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_PHPINFO']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op", "phpinfo");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="phpinfo">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button
		</FORM>
	</TD>
</TR>
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_DIAGNOSTIC']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_DIAGNOSTIC']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","diagnostic");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="diagnostic">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button>
		</FORM>
	</TD>
</TR>

<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_SQLDUMP']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_SQLDUMP']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","sqldump");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="sqldump">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button>
		</FORM>
	</TD>
</TR>

<?
if (_DIMS_SERVER_OSTYPE == 'unix')
{
	?>
	<TR CLASS="title">
		<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_BACKUP']; ?></TD>
	</TR>
	<TR>
		<TD>
			<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_BACKUP']; ?>
		</TD>
		<TD ALIGN="CENTER">
			<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op""backup);
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
				<INPUT TYPE="HIDDEN" NAME="op" VALUE="backup">
				<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
					<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
				</button>
			</FORM>
		</TD>
	</TR>
	<?
}
?>

<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_CLEANDB']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_CLEANDB']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","cleandb");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="cleandb">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button>
		</FORM>
	</TD>
</TR>

<!-- ajouter les constantes -->
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_UPDATE']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_UPDATE']; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","updateaction");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="updateaction">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button
		</FORM>
	</TD>
</TR>
<!-- ajouter les constantes -->
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"><? echo $_DIMS['cste']['_DIMS_LABEL_UPDATE_ACTION']; ?></TD>
</TR>
<TR>
	<TD>
		<? echo $_DIMS['cste']['_DIMS_LABEL_UPDATE_ACTION']. " tag"; ?>
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","updateactiontag");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="updateactiontag">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button>
		</FORM>
	</TD>
</TR>

<!-- update user depuis le module urml -->
<?php
	$sel = "SELECT	id
			FROM	dims_module
			WHERE	label = 'urml'";
	$res = $db->query($sel);
	if ($r = $db->fetchrow($res)){
		if ($dims->isModuleEnabled($r['id'])){
?>
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT">URML</TD>
</TR>
<TR>
	<TD>
		Update des utilisateurs depuis la base contact URML
	</TD>
	<TD ALIGN="CENTER">
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
			<?
				// Sécurisation du formulaire par token
				$token = new FormToken\TokenField;
				$token->field("op","updateurml");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="updateurml">
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
			</button
		</FORM>
	</TD>
</TR>

</TABLE>
<?
		}
	}
?>
<? echo $skin->close_simplebloc(); ?>
