<?
$url = "https://preview.cases.lu/piwikadmin/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite=1&period=week&date=yesterday";
//$url = "https://preview.cases.lu/piwikadmin/index.php?module=Widgetize&action=iframe&moduleToWidgetize=MultiSites&actionToWidgetize=standalone&idSite=1&period=week&date=yesterday";
$token = "&token_auth=7baa53ab49644b92c9a2898c84e1417d"
?>
<div class="title_h3">
    <h3><? echo $_SESSION['cste']['STATISTIQUES']; ?> / <? echo $_SESSION['cste']['_TRACKERS']; ?></h3>
</div>
<div class="lien_modification">
    <a href="https://preview.cases.lu/piwikadmin/index.php">
		<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_WSITE']; ?>
	</a>
</div>
<div class="form_object_block">
	<iframe src="<? echo $url.$token; ?>" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="1300px"></iframe>
</div>