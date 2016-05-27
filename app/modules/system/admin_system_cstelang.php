<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_TRADUCTION']);

echo "<form name='formlang' action='' method='get'>";

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("admin_lang");
$tokenHTML = $token->generate();
echo $tokenHTML;
// construction du choix de la langue de travail
//echo "<span style='float:left;width:45%;'> ".$_DIMS['cste']['_DIMS_LABEL_LANG'];
//
//echo "<select name='admin_lang' onchange='document.formlang.submit();'>";
//
//$res_lg = $db->query("SELECT id, label FROM dims_lang");
//while($tab_l = $db->fetchrow($res_lg)) {
//	if($_SESSION['dims']['current_adminlang'] == $tab_l['id']) $sel = "selected=\"selected\"";
//	else $sel = "";
//	echo '<option value="'.$tab_l['id'].'" '.$sel.'>'.ucfirst($tab_l['label']).'</option>';
//}
//echo "</select></span>";

echo '<div style="margin:10px;">';
echo "<span>".$_DIMS['cste']['_DIMS_LABEL_LANG'];
echo "<select name='admin_lang' onchange='document.formlang.submit();'>";
$res_lg = $db->query("SELECT id, label, isactive FROM dims_lang");
$activeLang = 0;
while($tab_l = $db->fetchrow($res_lg)) {
	if($_SESSION['dims']['current_adminlang'] == $tab_l['id']) $sel = "selected=\"selected\""; else $sel = "";
	echo '<option value="'.$tab_l['id'].'" '.$sel.'>'.ucfirst($tab_l['label']).'</option>';
}
echo "</select>";

require_once DIMS_APP_PATH.'modules/system/class_lang.php';
$lang = new lang();
$lang->open($_SESSION['dims']['current_adminlang']);
$lang->fields['isactive'] = !$lang->fields['isactive'];
echo '&nbsp;Active : <input type="checkbox" ';
if (!$lang->fields['isactive']) echo 'checked';
echo ' onclick="javascript:document.location.href=\''.$dims->getScriptEnv().'?op=active_cstelang\'">';

echo '&nbsp;Flag : ';
if ($urlFlag = $lang->getFlag())
	echo '<img src="'.$urlFlag.'">';
else
	echo 'No flag available !';
echo "</span>";
echo '<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" href="/admin.php?op=needed_traduction" style="float:right;">
		<span class="ui-button-text">Checked traduction</span>
	</a>
	</div>';

echo "<div style='clear:both;'>";
$sql = 'SELECT
           *
        FROM
            dims_constant where id_lang=? limit 0,100';

$ressource = $db->query($sql,array(array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['current_adminlang'])));

if($db->numrows($ressource) > 0) {
    $columns = array();
    $columns['auto'][0] = array('label' => '#');
    $columns['right'][3] = array('label' => '&nbsp;', 'width' => '100');
    $columns['right'][2] = array('label' => $_DIMS['cste']['_CONSTANT'], 'width' => '350');
    $columns['right'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_LABEL'], 'width' => '350');

    $c = 0;
    $values = array();

    while($result = $db->fetchrow($ressource)) {

        $open   = $scriptenv.'?op=modify_cstelang&id_cstelang='.$result['id'];

        $action = '
        <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$open.'" title="'.$_DIMS['cste']['_MODIFY'].'">
		<span class="ui-button-icon ui-icon ui-icon-wrench"></span>
		<span class="ui-button-text">'.$_DIMS['cste']['_MODIFY'].'</span>
	</a>';

        $values[$c]['values'][0] = array('label' => $result['id'], 'style' => '');
        $values[$c]['values'][1] = array('label' => $result['phpvalue'], 'style' => 'text-align:center');
        $values[$c]['values'][2] = array('label' => dims_strcut($result['value'],80), 'style' => 'text-align:center');
	$values[$c]['values'][3] = array('label' => $action, 'style' => 'text-align:center');
        $values[$c]['link'] = '';
        $values[$c]['style']= '';

        $c++;
    }

    $skin->display_array($columns, $values);
}
echo "</div></form>";
echo $skin->close_simplebloc();
?>
