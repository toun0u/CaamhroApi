<SCRIPT LANGUAGE="javascript">

var ns4 = (document.layers);
var ie4 = (document.all && !document.getElementById);
var ie5 = (document.all && document.getElementById);
var ns6 = (!document.all && document.getElementById);

function Init()
{
 var contenu;

 var objs=document.form_modify_rule['rule_id_type'];
 var id=objs.selectedIndex;
 var i=objs.options[id].value*1;

 nObjet='div_id_profile';
obj = (document.getElementById) ? document.getElementById(nObjet) : eval("document.all['nObjet']");

	switch(i)
	{
		case 1 :
			if(ns4) obj.visibility = "hide";
 			else obj.style.visibility = "hidden";
			break;
		case 2 :
			if(ns4) obj.visibility = "show";
 			else obj.style.visibility = "visible";
			break;
	}

}

</script>

<FORM NAME="form_modify_rule" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",				"save_rule");
	$token->field("rule_id_group",	$rule->fields['id_group']);
	$token->field("rule_id"			$rule->fields['id']);
	$token->field("rule_label");
	$token->field("rule_id_type");
	$token->field("rule_field");
	$token->field("rule_operator");
	$token->field("rule_value");
	$token->field("rule_id_profile");
	$tokenHTML = $token->generate();
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_rule">
<INPUT TYPE="HIDDEN" NAME="rule_id_group" VALUE="<? echo $rule->fields['id_group']; ?>">
<INPUT TYPE="HIDDEN" NAME="rule_id" VALUE="<? echo $rule->fields['id']; ?>">

<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="rule_label" VALUE="<? echo $rule->fields['label']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_TYPE']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><SELECT class="select" NAME="rule_id_type" onClick="javascript:Init()">
	<?
	$resuser = $db->query("select * from dims_rule_type order by id");

	while ($ltype = $db->fetchrow($resuser))
	{
		$sel = ($rule->fields['id_type'] == $ltype['id']) ? 'selected' : '';
		echo "<option $sel value=\"".$ltype['id']."\">".$ltype['label']."</option>";
	}// fin du while

	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_RULEFIELD']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
	<SELECT class="select" NAME="rule_field">
	<?
	$resuser = $db->query("select * from dims_user where id= :user_id", array(':user_id' => $user_id));

	while ($fuser = $db->fetchrow($resuser))
	{
		foreach ($fuser as $uname => $uvalue)
		{
			$sel = ($rule->fields['field'] == $uname) ? 'selected' : '';
			echo "<option $sel value=\"$uname\">$uname</option>";
		}
	}// fin du while

	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_RULEOPERATOR']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
		<SELECT class="select" NAME="rule_operator">
		<option value="=" selected="<? ($rule->fields['operator'] == '=') ? 'selected' : ''; ?>" >= </option>
		<option value="!=" selected="<? ($rule->fields['operator'] == '!=') ? 'selected' : ''; ?>" >!= </option>
		<option value="<" selected="<? ($rule->fields['operator'] == '<') ? 'selected' : ''; ?>"> < </option>
		<option value=">"> selected="<? ($rule->fields['operator'] == '>') ? 'selected' : ''; ?>"> > </option>
		<option value="<=" selected="<? ($rule->fields['operator'] == '<=') ? 'selected' : ''; ?>"> <= </option>
		<option value=">=" selected="<? ($rule->fields['operator'] == '>=') ? 'selected' : ''; ?>"> >= </option>
		<option value="like" selected="<? ($rule->fields['operator'] == 'like') ? 'selected' : ''; ?>">like</option>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_RULEVALUE']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="rule_value" VALUE="<? echo $rule->fields['value']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_PROFIL']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><div id="div_id_profile">
	<?

	$res="<SELECT class=\"select\" NAME=\"rule_id_profile\">";

			$resuser = $db->query("select * from dims_profile where id_group=:id_group", array(':id_group' => $rule->fields['id_group']));

			if ($rule->fields['id_profile']==0)
				$res.="<option ".$sel." value=\"0\">Pas de profil</option>";
			else
				$res.="<option ".$sel." value=\"0\">Pas de profil</option>";

			while ($lprofil = $db->fetchrow($resuser))
			{
				$sel = ($rule->fields['id_profile'] == $lprofil['id']) ? 'selected' : '';
				$res.="<option ".$sel." value=\"".$lprofil['id']."\">".$lprofil['label']."</option>";
			}// fin du while

			$res.="</SELECT>";
		echo $res;
	?>

	</div></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT COLSPAN=2>
	<SCRIPT LANGUAGE="javascript">Init();</SCRIPT>
        <?php
        echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'disk','Javascript: forms.form_modify_rule.submit();');
        ?>
	</TD>
</TR>
</TABLE>
<? echo $tokenHTML; ?>
</FORM>
