<script language="javascript">
 var indiceTimer=0;

function executeQueryTimer() {
    $('executeDiv').innerHTML="<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
    dims_xmlhttprequest_todiv('admin-light.php','<? echo "op=".$_SESSION['ejabber']['op']."&action=".$_SESSION['ejabber']['action']; ?>',"",'executeDiv');
    executeQueryTiming();
}

function stopQuery() {
    clearTimeout(timerdisplayresult);
}

function progress() {

}

function executeQueryTiming() {
    clearTimeout(timerdisplayresult);
    timerdisplayresult = setTimeout("executeQueryTimer()", 5000);
}
window.onload = function() {
  executeQueryTimer();
};
</script>

<div id="executeDiv" style="width:100%;height:200px">
</div>

