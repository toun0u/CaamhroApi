<?
echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_ACTIONHISTORY'],'100%');
$filter_date = dims_load_securvalue('filter_date',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_date2 = dims_load_securvalue('filter_date2',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_user = dims_load_securvalue('filter_user',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_module = dims_load_securvalue('filter_module',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_action = dims_load_securvalue('filter_action',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_record = dims_load_securvalue('filter_record',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$filter_ip = dims_load_securvalue('filter_ip',dims_const::_DIMS_CHAR_INPUT,true,true,true);

$where = '';
$params = array();
if ($filter_date!='') {
	$where .= " AND dims_user_action_log.timestp >= :timestp1 ";
	$params[':timestp1'] = dims_local2timestamp($filter_date);
}
if ($filter_date2!='') {
	$where .= " AND dims_user_action_log.timestp <= :timestp2 ";
	$params[':timestp2'] = dims_timestamp_add(dims_local2timestamp($filter_date2),0,0,0,0,1);
}
if ($filter_user!='') {
	$where .= " AND login LIKE :login ";
	$params[':login'] = "%".$filter_user."%";
}
if ($filter_module!='') {
	$where .= " AND dims_module.label LIKE :filtermodule ";
	$params[':filtermodule'] = "%".$filter_module."%";
}
if ($filter_action!='') {
	$where .= " AND dims_mb_action.label LIKE :filteraction ";
	$params[':filteraction'] = "%".$filter_action."%";
}
if ($filter_record!='') {
	$where .= " AND dims_user_action_log.id_record LIKE :filterrecord ";
	$params[':filterrecord'] = "%".$filter_record."%";
}
if ($filter_ip!='') {
	$where .= " AND dims_user_action_log.ip LIKE :ip ";
	$params[':ip'] = $filter_ip."%";
}

//echo $where;

if (!empty($_POST['exportcsv']))
{
	@ob_end_clean();

	$sql = "SELECT          dims_user_action_log.*,
							dims_user.login,
							dims_user.firstname,
							dims_user.lastname,
							dims_module.label as label_module,
							dims_mb_action.label as label_action
			FROM            dims_user_action_log
			LEFT JOIN       dims_user ON dims_user_action_log.id_user = dims_user.id
			LEFT JOIN       dims_module ON dims_user_action_log.id_module = dims_module.id
			LEFT JOIN       dims_mb_action
			ON 				dims_user_action_log.id_action = dims_mb_action.id_action
			AND				dims_mb_action.id_module_type = dims_module.id_module_type
			WHERE			1
			$where
			ORDER BY        dims_user_action_log.timestp DESC
			";

	$res=$db->query($sql, $params);

	header("Cache-control: private");
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=actionlog.csv");
	header("Pragma: public");

	echo	"\"timestamp\";\"ip\";\"id_user\";\"login\";\"id_module\";\"module\";\"id_action\";\"action\";\"record\"\n";
	while($row = $db->fetchrow($res))
	{
		$login = (is_null($row['login'])) ? "supprime ({$row['id_user']})" : $row['login'];
		$module = (is_null($row['label_module'])) ? "supprime ({$row['id_module']})" : $row['label_module'];
		$action = (is_null($row['label_action'])) ? "supprimee ({$row['id_action']})" : $row['label_action'];

		echo	"\"{$row['timestp']}\";\"{$row['ip']}\";\"{$row['id_user']}\";\"{$login}\";\"{$row['id_module']}\";\"{$module}\";\"{$row['id_action']}\";\"{$action}\";\"".addslashes($row['id_record'])."\"\n";
	}

	die();
}

$sql = "SELECT          dims_user_action_log.*,
						dims_user.login, dims_user.firstname, dims_user.lastname,
						dims_module.label as label_module,
						dims_mb_action.label as label_action
		FROM            dims_user_action_log
		LEFT JOIN       dims_user ON dims_user_action_log.id_user = dims_user.id
		LEFT JOIN       dims_module ON dims_user_action_log.id_module = dims_module.id
		LEFT JOIN       dims_mb_action
		ON 				dims_user_action_log.id_action = dims_mb_action.id_action
		AND				dims_mb_action.id_module_type = dims_module.id_module_type
		WHERE			1
		$where
		ORDER BY        dims_user_action_log.timestp DESC
		LIMIT			0,100
		";

$res=$db->query($sql, $params);

?>
<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "actionhistory");
	$token->field("filter_date");
	$token->field("filter_date2");
	$token->field("filter_user");
	$token->field("filter_module");
	$token->field("filter_action");
	$token->field("filter_record");
	$token->field("filter_ip");
	$token->field("exportcsv");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="actionhistory">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD>Date (jj/mm/aaaa):&nbsp;</TD>
	<TD>de&nbsp;<INPUT TYPE="TEXT" class="text" SIZE="9" NAME="filter_date" id="filter_date" VALUE="<? echo $filter_date; ?>"><a href="#" onclick="javascript:dims_calendar_open('filter_date', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
	&nbsp;&agrave;&nbsp;<INPUT TYPE="TEXT" class="text" SIZE="9" NAME="filter_date2" id="filter_date2" VALUE="<? echo $filter_date2; ?>"><a href="#" onclick="javascript:dims_calendar_open('filter_date2', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></TD>
	<TD>Utilisateur:&nbsp;</TD>
	<TD><INPUT TYPE="TEXT" class="text" SIZE="35" NAME="filter_user" VALUE="<? echo $filter_user; ?>"></TD>
</TR>
<TR>
	<TD>Module:&nbsp;</TD>
	<TD><INPUT TYPE="TEXT" class="text" SIZE="35" NAME="filter_module" VALUE="<? echo $filter_module; ?>"></TD>
	<TD>Action:&nbsp;</TD>
	<TD><INPUT TYPE="TEXT" class="text" SIZE="35" NAME="filter_action" VALUE="<? echo $filter_action; ?>"></TD>
</TR>
<TR>
	<TD>Enregistrement:&nbsp;</TD>
	<TD><INPUT TYPE="TEXT" class="text" SIZE="35" NAME="filter_record" VALUE="<? echo $filter_record; ?>"></TD>
	<TD>IP:&nbsp;</TD>
	<TD><INPUT TYPE="TEXT" class="text" SIZE="35" NAME="filter_ip" VALUE="<? echo $filter_ip; ?>"></TD>
</TR>
<TR>
	<TD COLSPAN="4" ALIGN="RIGHT">
		<INPUT TYPE="BUTTON" CLASS="flatbutton" VALUE="Effacer tous les logs" OnClick="javascript:dims_confirmlink('<? echo "$scriptenv?op=delete_logs"; ?>','<? echo $_DIMS['cste']['_SYSTEM_MSG_CONFIRMLOGDELETE']; ?>')">&nbsp;
		<INPUT TYPE="SUBMIT" CLASS="flatbutton" VALUE="Export CSV" NAME="exportcsv">
		<INPUT TYPE="SUBMIT" CLASS="flatbutton" VALUE="Filtrer">
	</TD>
</TR>
</TABLE>
</FORM>

<TABLE CELLPADDING="4" CELLSPACING="1" WIDTH="100%">
<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
  <TD><B>Date/Heure</B></TD>
  <TD><B>IP</B></TD>
  <TD><B>Utilisateur</B></TD>
  <TD><B>Module</B></TD>
  <TD><B>Action</B></TD>
  <TD><B>Enregistrement</B></TD>
</TR>

<?
$color = '';
while($row = $db->fetchrow($res)) {
	if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
	else $color=$skin->values['bgline2'];

	$date = dims_timestamp2local($row['timestp']);

	$login = (is_null($row['login'])) ? "<i>supprim&eacute;</i> ({$row['id_user']})" : $row['login'];
	$module = (is_null($row['label_module'])) ? "<i>supprim&eacute;</i> ({$row['id_module']})" : $row['label_module'];
	$action = (is_null($row['label_action'])) ? "<i>supprim&eacute;e</i> ({$row['id_action']})" : $row['label_action'];

	echo	"
			<TR BGCOLOR=\"$color\">
				<TD>{$date['date']} {$date['time']}</TD>
				<TD>{$row['ip']}</TD>
				<TD>$login</TD>
				<TD>$module</TD>
				<TD>$action</TD>
				<TD>{$row['id_record']}</TD>
			</TR>
			";
}

echo "
  </TD>
</TR>
</TABLE>";

echo $skin->close_simplebloc();
?>
