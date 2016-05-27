<?php
require_once(DIMS_APP_PATH . "/modules/wce/include/global.php");
require_once(DIMS_APP_PATH . "/modules/wce/include/classes/class_article.php");

$article = new wce_article();

if (!file_exists(_WCE_TEMPLATES_PATH."/$template_name")) $template_name = 'default';

$template_path = _WCE_TEMPLATES_PATH."/$template_name";

$_SESSION['dims']['front_template_name']=$template_name;
$_SESSION['dims']['front_template_path']=$template_path;
//$template_body = new Template($template_path);
$smarty->template_dir = $template_path;

if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/", 0777, true);

$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";

if (file_exists("{$template_path}/config.php")) require_once "{$template_path}/config.php";

// construction de la racine du site
$root_path=$article->getRootPath();
$root_path="https://i-net.luxembourgforbusiness.lu";

$tpl_site=array(
	'TEMPLATE_PATH'				=> $template_path,
	'TEMPLATE_ROOT_PATH'		=> $root_path.'/common'.str_replace("./","/",$template_path),
	'TEMPLATE_ROOT_PATH_FCK'	=> $root_path.'/common'.str_replace("./","/",$template_path).$customfck.'/fckeditorarea.css',
	'ROOT_PATH'					=> $root_path.'/common',
	'EDITO'						=> $edito,
	'ADDITIONAL_JAVASCRIPT'		=> $additional_javascript,
	'ADDITIONAL_CSS'			=> $additional_css,
	'CONNECTEDUSERS'			=> (isset($_SESSION['dims']['connectedusers'])) ? $_SESSION['dims']['connectedusers'] : "",
	'TITLE'						=> ($title),
	'WORKSPACE_ID'				=> $_SESSION['dims']['workspaceid'],
	'META_DESCRIPTION'			=> $metadesc,
	'META_KEYWORDS'				=> $metakeywords,
	'META_AUTHOR'				=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
	'META_COPYRIGHT'			=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
	'META_ROBOTS'				=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
	'SITE_TITLE'				=> ($_SESSION['dims']['currentworkspace']['title']),
	'WORKSPACE_META_DESCRIPTION'=> $metadesc,
	'WORKSPACE_META_KEYWORDS'	=> $metakeywords,
	'WORKSPACE_META_AUTHOR'		=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
	'WORKSPACE_META_COPYRIGHT'	=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
	'WORKSPACE_META_ROBOTS'		=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",

	'SITE_CONNECTEDUSERS'		=> (isset($_SESSION['dims']['connectedusers'])) ?  $_SESSION['dims']['connectedusers'] : 0,
	'PAGE_QUERYSTRING'			=> $query_string,
	'NAV'						=> $nav,
	'HOST'						=> $_SERVER['HTTP_HOST'],
	'DATE_DAY'					=> date('d'),
	'DATE_MONTH'				=> date('m'),
	'DATE_YEAR'					=> date('Y'),
	'LASTUPDATE_DATE'			=> $lastupdate['date'],
	'LASTUPDATE_TIME'			=> $lastupdate['time'],
	'DIMS_PAGE_SIZE'			=> sprintf("%.02f",$dims_stats['pagesize']/1024),
	'DIMS_EXEC_TIME'			=> $dims_stats['total_exectime'],
	'DIMS_PHP_P100'				=> $dims_stats['php_ratiotime'],
	'DIMS_SQL_P100'				=> $dims_stats['sql_ratiotime'],
	'DIMS_NUMQUERIES'			=> $dims_stats['numqueries']);

	$smarty->assign('site',$tpl_site);

// on assigne le contenu de la newsletter
$tpl_page=array(
		'ID'						=> '',
		'REFERENCE'					=> '',
		'TITLE'						=> $env->fields['subject'],
		'TITLE_FAVORITES'			=> '',
		'AUTHOR'					=> '',
		'VERSION'					=> '',
		'DATE'						=> '',
		'LASTUPDATE_DATE'			=> '',
		'LASTUPDATE_TIME'			=> '',
		'LASTUPDATE_USER_LASTNAME'	=> '',
		'LASTUPDATE_USER_FIRSTNAME'	=> '',
		'LASTUPDATE_USER_LOGIN'		=> '',
		'TOP_CONTENT'				=> '',
		'LEFT_CONTENT'				=> '',
		'RIGHT_CONTENT'				=> '',
		'BOTTOM_CONTENT'			=> '',
		'META_DESCRIPTION'			=> '',
		'META_KEYWORDS'				=> '',
		'CONTENT'					=> $env->fields['content']);


$smarty->assign('page', $tpl_page);

// buffer flushing
ob_start();
$smarty->display('index.tpl');
$content = ob_get_contents();
ob_end_clean();


// on remplace maintenant les liens internes pour valider l'urlrewrite
$rep_fromdeb=array();
$rep_fromdeb[]= "'index.php";
$rep_fromdeb[]= "\"index.php";

$rep_todeb=array();
$rep_todeb[]="'/index.php";
$rep_todeb[]="\"/index.php";

$content= str_replace($rep_fromdeb,$rep_todeb,$content);

$rep_from[]= "./index.php";
$rep_from[]= "./index-quick.php";
$rep_from[]= "./data/";

$rep_to[]=$root_path."/index.php";
$rep_to[]=$root_path."/index-quick.php";
$rep_to[]=$root_path."/data/";

$url = $article->getAllUrls($content, false);

$tab_src = array();

foreach($url['src'] as $key => $src) {
	$tab_src[] = $src;
}

$tab_src = array_unique($tab_src);

$tab_css = array();
$tab_href = array();

foreach($url["href"] as $key => $href) {
	if (!ereg("css$",$href)) $tab_href[] = $href;
	else $tab_css[] = $href;
}

$tab_css = array_unique($tab_css);
$tab_href = array_unique($tab_href);

$tab_background = array();

foreach($url["background"] as $key => $bg) {
	if ( !in_array($bg, $tab_src))	$tab_background[] = $bg;
}

foreach($url["url"] as $key => $bg) {
	if (ereg("jpg$",$bg) || ereg("png$",$bg) || ereg("gif$",$bg) || ereg("jpeg$",$bg)) {
		if ( !in_array($bg, $tab_src))	$tab_background[] = $bg;
	}
}

$tab_background = array_unique($tab_background);
/*
foreach($tab_href as $key => $href) {
	if (!ereg("(^http)|(^www)",$href))	$content = str_replace($href, "http://".$domain."/".dims_urlencode($href), $content);
}

foreach($tab_background as $key => $bg) {
	if (!ereg("^http|(^www)",$bg)) $content = str_replace($bg, "http://".$domain."/".$bg, $content);
}


$style_css = "<style type=\"text/css\">";

foreach($tab_css as $key => $css) {
	if (substr($css,0,2) == "..")	$css = str_replace("..", ".", $css);

	$chemin = eregi_replace("[a-z0-9]*.css","",$css);
	$style = file_get_contents("http://".$domain."/".$css);
	$style = str_replace(array("'", "\""), "", $style);
	$style = str_replace("url(", "url(http://".$domain."/".$chemin."/", $style);
	$style_css .= $style;
}

$style_css .= "</style>";
$content = str_replace("<head>", "<head>".$style_css, $content);

foreach($tab_src as $key => $src) {
	if (substr($src,0,1) == "/")
		$src2 = ".".$src;
	else
		$src2 = $src;

	if (!ereg("(^http)|(^www)|(^\.\.)",$src2)) {
		echo "src = $src2 <br/>";
		$content = str_replace($src, "http://".$domain."/".$src2, $content);
	} else
	{
		echo "mauvais src = $src2 <br/>";
		$content = str_replace($src, $src2, $content);
	}
}
*/

$content= str_replace($rep_from,$rep_to,$content);

/*

$content = str_replace(">", ">\r\n", $content);

//----------------------------------
// Construction de l'ent�te
//----------------------------------

$delimiteur = "-----=".md5(uniqid(rand()));

$entete = "MIME-Version: 1.0\r\n";
$entete .= "Content-Type: multipart/related; boundary=\"$delimiteur\"\r\n";
$entete .= "\r\n";


$msg = "\r\n";
$msg .= $content;
$msg .= "\r\n";
$msg .= "\r\n";
*/
?>
