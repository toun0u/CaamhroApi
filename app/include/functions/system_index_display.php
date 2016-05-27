<?
$nb_elem_page = dims_const::_DIMS_NB_ELEM_PAGE;
$numrows = sizeof($tabresdoc);

$nbpage = ($numrows - $numrows % $nb_elem_page) / $nb_elem_page + ($numrows % $nb_elem_page > 0);
$page=dims_load_securvalue("page",dims_const::_DIMS_NUM_INPUT,true,false);
if ($page>0) {
	$_SESSION['dims']['search']['page'][$moduleid]=$page;
}
else {
	if (isset($_SESSION['dims']['search']['page'][$moduleid])) $page=$_SESSION['dims']['search']['page'][$moduleid];
	else {
		$page = 1;
		$_SESSION['dims']['search']['page'][$moduleid]=$page;
	}
}
if ($nbpage>0) {
?>
	<div style="float:right;">
		<div style="float:left;">page :&nbsp;</div>
		<?
		for ($p = 1; $p <= $nbpage; $p++) {
			?>
			<a class="system_page<? if ($p==$page) echo '_sel'; ?>" href="javascript:void(0)" onclick="refreshDesktopPage(<? echo $moduleid.",'".$dims_op."',".$p; ?>);"><? echo $p; ?></a>
			<?
		}
		?>
	</div>
	<?
}
?>

<div style="clear:both;">

</div>
