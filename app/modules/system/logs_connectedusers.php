<?
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONNECTEDUSERS'],'100%');
?>

<?
if (isset($monitoring))
{
  ?>
  <META HTTP-EQUIV=Refresh CONTENT="<? echo $monitoring; ?>; URL=<? echo "$scriptenv?op=connectedusers&monitoring=$monitoring"; ?>">
  <?
}
?>
<FORM NAME="monitoring">
<?
  // Sécurisation du formulaire par token
  require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
  $token = new FormToken\TokenField;
  $token->field("monitoring");
  $tokenHTML = $token->generate();
  echo $tokenHTML;
?>
<TABLE CELLPADDING="4" CELLSPACING="1" WIDTH="100%">
<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
  <TD><B>ID Session</B></TD>
  <TD><B>IP Client</B></TD>
  <TD><B>Domaine</B></TD>
  <TD><B>login</B></TD>
  <TD><B>Prénom</B></TD>
  <TD><B>Nom</B></TD>
  <TD><B>Groupe</B></TD>
  <TD><B>Module</B></TD>
  <TD><B>Date/Heure</B></TD>
</TR>

<?
$sql =  " SELECT          dims_connecteduser.*,
                          dims_user.login, dims_user.firstname, dims_user.lastname,
                          dims_workspace.label as workspacelabel,
                          dims_module.label as modulelabel
          FROM            dims_connecteduser
          LEFT JOIN       dims_user ON dims_connecteduser.user_id = dims_user.id
          LEFT JOIN       dims_workspace ON dims_connecteduser.workspace_id = dims_workspace.id
          LEFT JOIN       dims_module ON dims_connecteduser.module_id = dims_module.id
          ORDER BY        dims_user.login
          ";

  $db->query($sql);

  $color = '';
  while($row = $db->fetchrow($res))
  {
    if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
    else $color=$skin->values['bgline2'];

    $date = dims_timestamp2local($row['timestp']);

    if (is_null($row['login']))
    {
      echo        "
                  <TR BGCOLOR=\"$color\">
                    <TD>{$row['sid']}</TD>
                    <TD>{$row['ip']}</TD>
                    <TD>{$row['domain']}</TD>
                    <TD COLSPAN=\"4\"><i>anonymous</i></TD>
                    <TD>{$row['modulelabel']}</TD>
                    <TD>{$date['date']} {$date['time']}</TD>
                  </TR>
                  ";
    }
    else
    {
      echo        "
                  <TR BGCOLOR=\"$color\">
                    <TD>{$row['sid']}</TD>
                    <TD>{$row['ip']}</TD>
                    <TD>{$row['domain']}</TD>
                    <TD>{$row['login']}</TD>
                    <TD>{$row['firstname']}</TD>
                    <TD>{$row['lastname']}</TD>
                    <TD>{$row['workspacelabel']}</TD>
                    <TD>{$row['modulelabel']}</TD>
                    <TD>{$date['date']} {$date['time']}</TD>
                  </TR>
                  ";
    }
  }

  ?>

<TR>
  <?
  if (isset($monitoring))
  {
  ?>
    <TD COLSPAN="9" ALIGN="RIGHT"><INPUT TYPE="Button" class="flatbutton" OnClick="document.location.href='<? echo "$scriptenv?op=connectedusers"; ?>'" VALUE="Stop Monitoring"></TD>
  <?
  }
  else
  {
  ?>
    <TD COLSPAN="9" ALIGN="RIGHT"><INPUT TYPE="Text" class="text" VALUE="2" SIZE="2" NAME="monitoring">&nbsp;<INPUT TYPE="Button" class="flatbutton" OnClick="document.location.href='<? echo "$scriptenv"; ?>?op=connectedusers&monitoring='+document.monitoring.monitoring.value;" VALUE="Monitoring"></TD>
  <?
  }
  ?>
</TR>
</TABLE>
</FORM>
<?
echo $skin->close_simplebloc();
?>