<?
//header('Expires: ' . gmdate("D, d M Y H:i:s") . " GMT");
//header('Last-Modified: ' . gmdate("D, d M Y H:i:s"));
//header('Cache-Control: no-cache, must-revalidate');
//header('Pragma: no-cache');
header('Content-type: text/html; charset=iso-8859-1');

if (isset($_SERVER['SCRIPT_NAME'])) {
    if (isset($_SERVER['SERVER_PROTOCOL']) && substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") {
        $urlpath = "http://".$_SERVER['HTTP_HOST']."/".basename($_SERVER['SCRIPT_NAME']);
    }
    else
        $urlpath = "https://".$_SERVER['HTTP_HOST']."/".basename($_SERVER['SCRIPT_NAME']);
}
else $urlpath ="";

$scriptenv = basename($_SERVER['SCRIPT_FILENAME']);

if (substr(PHP_OS, 0, 3) == 'WIN') define ('_DIMS_SERVER_OSTYPE', 'windows');
else define ('_DIMS_SERVER_OSTYPE', 'unix');

switch(_DIMS_SERVER_OSTYPE) {
	default:
	case 'unix':
		define ('_DIMS_SEP', '/');
	break;

	case 'windows':
		define ('_DIMS_SEP', '\\');
	break;
}

?>
