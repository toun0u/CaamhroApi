
<script language="javascript">
function checkAllTables(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("sel_synctable_"+i).checked = true;
}

function uncheckAllTables(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("sel_synctable_"+i).checked = false;
}
</script>

<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_SYNCHRO']);

echo "<div style=\"width:60%;text-align:center;margin:0 auto;\">";
echo "<form name=\"synchro\" action=\"/admin.php?op=save_synchro\" method=\"post\">";

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("sel_synctable");
$tokenHTML = $token->generate();
echo $tokenHTML;

// ajout du bouton d'ajout
//echo "<div style=\"display:block;width:100%;text-align:right;margin-right:10px;\"><a href=\"$scriptenv?op=add_shareobject\">".$_DIMS['cste']['_DIMS_ADD']."&nbsp;<img src=\"./common/img/icon_add.gif\" border=\"0\" alt=\"\"></a>&nbsp;</div>";

echo "<table style=\"width:80%;text-align:left;\"><tr>";
echo "<td style=\"width:10%\">".$_DIMS['cste']['_LABEL_SYNCHRONIZE']."</td>";
echo "<td>".$_DIMS['cste']['_FORMS_TABLENAME']."</td></tr>";

$tables = $db->listtables();

$num_tables = $db->numrows($tables);
$i=0;

// FIXME : Switching to pdo - Should directly fetch over PDOStatement $tables
while($i < $num_tables) {
	$color=($color=='trl2') ? 'trl1' : 'trl2';

        $tablename = $db->tablename($tables, $i);
	if (isset($_SESSION['dims']['synchro']['tables']) && isset($_SESSION['dims']['synchro']['tables'][$tablename])) {
		$selected='checked';
	}
	else {
		$selected='';
	}

	echo "<tr class=\"".$color."\"><td><input value=\"".$tablename."\" type=\"checkbox\" id=\"sel_synctable_".$i."\" name=\"sel_synctable[]\" ".$selected."></td><td>".$tablename."</td></tr>";
	$i++;
}

echo "</table>";
?>
<div style="float:left;"><img src="./common/img/arrow_ltr.png" border="0" alt="0"></div>
	<div style="float:left;margin-top:4px;"><a href="#" onclick="checkAllTables(<? echo $num_tables; ?>);"><? echo $_DIMS['cste']['_ALLCHECK']; ?></a>
	&nbsp;/&nbsp;<a href="#" onclick="uncheckAllTables(<? echo $num_tables; ?>);"><? echo $_DIMS['cste']['_ALLUNCHECK']; ?></a></div>
<?php
echo '</div><div style="clear:both;text-align:right;padding:0px;height:40px;padding:10px 5px 0px 0px;">';
echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.synchro.submit();");
echo "</div>";

echo $skin->close_simplebloc();
?>
