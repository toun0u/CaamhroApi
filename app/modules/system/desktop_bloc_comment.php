<?
if (!isset($_SESSION['dims']['ct_filter'])  || (isset($_GET['ct_filter']) && $_GET['ct_filter']=='')) $_SESSION['dims']['ct_filter']='';
$ct_filter = dims_load_securvalue('ct_filter', dims_const::_DIMS_CHAR_INPUT, true, true,false,&$_SESSION['dims']['ct_filter']);

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject($_SESSION['dims']['current_object']['id_object']);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

?>
<div>
<?
echo $skin->open_widgetbloc($_SESSION['cste']['_DIMS_LABEL_ANNOTATION'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','26px', '26px', '-12px', '-5px', '', '', '');
require_once DIMS_APP_PATH.'include/functions/annotations.php';
dims_annotation($_SESSION['dims']['current_object']['id_object'],$_SESSION['dims']['current_object']['id_record'], "",-1,-1,$_SESSION['dims']['current_object']['id_module'],true);
echo $skin->close_widgetbloc();
?>
</div>
<script type="text/javascript" language="text/javascript">
function charger() {
	//$('ct_filter').focus();
}

charger();
</script>