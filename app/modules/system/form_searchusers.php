
<div id="<? echo $element;?>block1" style="padding-top:5px;clear:both;float:left;width:100%;">
<?
//// zone de recherche
echo "<div style=\"float:left;width:49%;display:block;\">";
?>

<span style="width: 80%;display:block;float:left;font-size:14px;color:#BABABA;font-weight:bold;">
		<? echo $_DIMS['cste']['_FORM_SELECTION']; ?>
</span>
<span style="clear:both;width:100%;display:block;float:left;font-size:14px;color:#BABABA;font-weight:bold;">
<? //echo $_DIMS['cste']['_DIMS_LABEL_INTERNAL_SOURCES'];
?>
</span>
<?
if (isset($_SESSION[$element]['currentsearch'])) $nomsearch=$_SESSION[$element]['currentsearch'];
else $nomsearch='';
?>
<input value="<? echo $nomsearch; ?>" type="text" onkeyup="javascript:object_searchUser(<? echo $element; ?>);" id="nomsearch<? echo $element;?>" name="nomsearch<? echo $element;?>" size="16">
<img style="cursor: pointer;" onclick="javascript:object_updateGroupActionFromSelected(<? echo $element;?>);" src="./common/img/search.png" border="0">
&nbsp;<img style="cursor: pointer;" onclick="javascript:object_searchInitSearch(<? echo $element;?>);" src="./common/img/delete.png" border="0">
<div id="lst_tempuser<? echo $element;?>" style="width:90%;overflow:auto;display:block;float:left;"></div>
</div>
<?
// personne sélectionnée
echo "<div id=\"lstselectedusers".$element."\" style=\"float:left;width:49%;display:block;\">";
echo "</div>";
?>
</div>

<script type="text/javascript">
$("nomsearch<? echo $element;?>").focus();
object_searchUserExec('',<? echo $element;?>);
</script>
