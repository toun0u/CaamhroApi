 <style>
	#progressbar .ui-progressbar-value {
	background-color: #ccc;
}
</style>
 <script>
$(function() {
	$( "#progressbar" ).progressbar({
		value: false
	});
});
</script>
<div style="width:100%;text-align:center;height:20px;" id="progressbar">

</div>

<script type="text/javascript">
window['finishExtract'] = function finishExtract() {
	alert("on a fini");
	document.location.href='<? echo $dims->getScriptEnv(); ?>';
}

window['initExtract'] = function initExtract() {
	dims_xmlhttprequest('index-light.php','action=file_extract_init',true);
}

window['refreshExtract'] = function refreshExtract() {
	clearTimeout(timerdisplayresult);
	dims_xmlhttprequest_todiv('admin.php','action=file_extract_ajax',refreshExtractSuite);
	timerdisplayresult = setTimeout("refreshExtract()", 1000);
}

function refreshProgressBar(){
	$.ajax({
		type: "GET",
		url: '/admin.php?action=file_extract_ajax',
		async: false,
		dataType: "text",
		success: function(data){
			var d = parseInt(data);
			if(d != '' && d > 0) {
				if (d>=100) {
					finishExtract();
				}
				else {
					$("div#progressbar").progressbar("option", "value", d);
				}
			}
		}
	});
}


function build_HttpRequest() {
	xmlhttp = dims_gethttpobject();
	return xmlhttp;
}

function makeRequest(url,do_function) {
	http_request = build_HttpRequest();
	var time = new Date();
	if (url.indexOf('?')>0){ url = url + '&time='+time.getTime(); } else { url = url + '?time='+time.getTime();}
	http_request.onreadystatechange = do_function;
	http_request.open('GET', url, true);
	http_request.send(null);
}
var http_path="";
var sendmsgupload="";
var sendmsgwaiting="";
var sendmsgcopy="";
var sendmsgerror="";
var checkCount=0;

function setVariables(hpath,supload,swaiting,scopy,serror,serrorext) {
	http_path=hpath;
	sendmsgupload=supload;
	sendmsgwaiting=swaiting;
	sendmsgcopy=scopy;
	sendmsgerror=serror;
	sendmsgerrorext=serrorext;
}
<?
global $dims;
$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
$rootpath=$dims->getProtocol().$http_host;

echo "setVariables(\"$rootpath\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']."\",\"".$_DIMS['cste']['_DOC_MSG_COPY_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']."\")";
?>

window.onload = function() {
	$("div#progressbar").progressbar({value: 0});
	initExtract();
	refreshProgressBar();
	setInterval(refreshProgressBar,1000);
}
</script>
