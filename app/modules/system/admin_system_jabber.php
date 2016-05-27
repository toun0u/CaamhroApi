<? echo $skin->open_simplebloc("Admin Jabber",'100%'); ?>

<?
$res=$db->query("select * from dims_intercom");
if ($db->numrows($res)>0) {
	echo "<table style=\"width:100%\"><tr><td>IP</td><td>Host</td><td>port</td><td>Dims</td><td>Statut</td></tr>";
	while ($f=$db->fetchrow($res)) {
		echo "<td>".$f['ip']."</td>";
		echo "<td>".$f['host']."</td>";
		echo "<td>".$f['port']."</td>";
		echo "<td>".$f['dims']."</td>";
		echo "<td><div id=\"".$f['host']."_".$f['dims']."\"></div></td></tr>";
	}
	echo "</table>";
}
?>
<? echo $skin->close_simplebloc(); ?>
<br>
<? echo $skin->open_simplebloc("New Jabber",'100%'); ?>
<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="initDims">

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR CLASS="title">
	<TD COLSPAN="2" ALIGN="LEFT"></TD>
</TR>
<TR>
		<td>
			<label>IP de l'Host</label>
		</td>
	<TD>
			<input type="text" name="host_ip" value="213.251.149.247">
	</TD>
</tr>
<TR>
		<td>
			<label>Port de l'Host</label>
		</td>
	<TD>
			<input type="text" name="host_port"  value="8255">
	</TD>
</tr>
<TR>
		<td>
			<label>Nom de l'Host</label>
		</td>
	<TD>
			<input type="text" name="host_name" value="Netlor">
	</TD>
</tr>
<TR>
		<td>
			<label>Nom du Dims pour l'Host</label>
		</td>
	<TD>
			<input type="text" name="dims_name"  value="dims">
	</TD>
</tr>

<TR>
		<td>
			<label>Cle de securite Host</label>
		</td>
	<TD>
			<input type="text" name="host_securitykey"	value="+skaur1897a15m85">
	</TD>
</tr>
<TR>
		<td>
			<label>Cle de securite Dims</label>
		</td>
	<TD>
			<?
			if (isset($_SESSION['ejabber']['clefSecurite'])) {
				echo $_SESSION['ejabber']['clefSecurite'];
			}
			?>
	</TD>
</tr>
<TR>
	<TD ALIGN="CENTER" colspan="2">
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
			<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_EXECUTE']; ?></span>
		</button>
	</TD>
</TR>
</TABLE>
<?
	// Sécurisation du formulaire
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "initDims");
	$token->field("host_ip");
	$token->field("host_port");
	$token->field("host_name");
	$token->field("dims_name");
	$token->field("host_securitykey");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
</FORM>

<?

if (isset($_SESSION['ejabber']['error']) && $_SESSION['ejabber']['error']!='') {
	switch ($_SESSION['ejabber']['error']) {
		case 100:
			// Dims deja utilise
			echo "Dims deja utilise";
			break;
		case 101:
			// Host name dont exist
			echo "Host name dont exist";
			break;
		case 102:
			// IP non autorisee
			echo "IP non autorisee";
			break;
		case 103:
			// Depassement du nb de Dims autorise pour cette IP
			echo "Depassement du nb de Dims autorise pour cette IP";
			break;
		case 190:
			// cle invalide
			echo "cle invalide";
			break;
	}
}
else {
	if (isset($_SESSION['ejabber']['success']) && $_SESSION['ejabber']['success']!='') {
		echo "Autorise !!! avec cle : ".$_SESSION['ejabber']['clefSecurite'];
	}
}
?>
<? echo $skin->close_simplebloc(); ?>
