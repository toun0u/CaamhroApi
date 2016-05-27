<div class="dims_form" style="padding-top:40px;width:100%;text-align:center;height:100px;" id="progressbar">
	Extraction en cours
</div>

<script type="text/javascript">
function finishExtract() {
	document.location.href='<? echo $dims->getScriptEnv(); ?>?op=browser&currentfolder=<? echo $currentfolder; ?>';
}

function initExtract() {
	dims_xmlhttprequest('index-light.php','op=file_extract_init',true);
}

function refreshExtract() {
	clearTimeout(timerdisplayresult);
	dims_xmlhttprequest_todiv('admin.php','op=file_extract_ajax','','progressbar');
	timerdisplayresult = setTimeout("refreshExtract()", 5000);
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
	initExtract();
	refreshExtract();
}
</script>
