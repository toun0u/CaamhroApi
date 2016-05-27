<?php

define('AUTHORIZED_ENTRY_POINT', true);
///////////////////////////////////////////////////////////////////////////
// START DIMS ENGINE
///////////////////////////////////////////////////////////////////////////
if (substr($_SERVER["DOCUMENT_ROOT"],strlen($_SERVER["DOCUMENT_ROOT"])-1,1)!="/") $_SERVER["DOCUMENT_ROOT"].="/";

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

require_once DIMS_APP_PATH . 'include/start.php';

$op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true, false);

if (isset($op)) {
	switch($op) {
		case "download_doc":

		if (file_exists(DIMS_APP_PATH . '/modules/doc/class_doc.php')) {

			dims_init_module('doc');
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
			if (isset($doc_id))
			{
				$docfile = new docfile();
				$docfile->open($doc_id);
				dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
				die();
			}
		}
		break;
		case 'doc_file_download':
		case 'doc_image_get':
			if (file_exists(DIMS_APP_PATH . '/modules/doc/class_docfile.php')) {

				require_once(DIMS_APP_PATH . '/modules/doc/include/global.php');
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');

				if (!empty($_GET['docfile_md5id']))
				{
					$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5id ", array(
						':md5id' => $_GET['docfile_md5id']
					));
					if ($fields = $db->fetchrow($res))
					{
						$docfile = new docfile();
						$docfile->open($fields['id']);
						if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
						else if (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
						//dims_resizeimage($docfile->getfilepath());//, $coef = 0, $wmax = 0, $hmax = 0, $format = '', $nbcolor = 0, $filename = '')
					}
				}
			}
			die();
			break;

		case 'get_captcha':
			ob_clean();
			if(isset($_SESSION['dims']['captcha']))
				echo $_SESSION['dims']['captcha'];
			die();
			break;
	}
}

if ($_SESSION['dims']['mode'] == 'admin') {
	if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
		//require_once(DIMS_APP_PATH . '/include/class_template.php');
		require_once DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php";

		$skin = new skin();
		//$template_body = new Template($_SESSION['dims']['template_path']);
		//$template_body->set_filenames(array('body' => "popup.tpl"));

		$template_name=$_SESSION['dims']['template_name'];
		$template_path=$_SESSION['dims']['template_path'];
		//$template_body = new Template($template_path);
		$smarty->template_dir = $template_path;

		if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/",0777,true);
		$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";

		if (file_exists("{$template_path}/config.php")) require_once "{$template_path}/config.php";

		// GET MODULE ADDITIONAL JS
		ob_start();
		include(DIMS_APP_PATH . 'include/javascript.php');
		if (file_exists(DIMS_APP_PATH . "modules/{$_SESSION['dims']['moduletype']}/include/javascript.php")) include(DIMS_APP_PATH . "modules/{$_SESSION['dims']['moduletype']}/include/javascript.php");
		$additional_javascript = ob_get_contents();
		@ob_end_clean();

		ob_start();
		$dims_op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true, true);

		if (isset($dims_op) && $dims_op !== "") {
			require_once DIMS_APP_PATH.'include/op.php';
		}

		$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true);
		//if ($_SESSION['dims']['workspaceid'] != '' && $_SESSION['dims']['moduletype'] != '')
		if ($_SESSION['dims']['moduletype'] != '') {
			// Comment� pour cause de filtrage inutile sur des r�gles qui ont chang� :
			//if ($_SESSION['dims']['action']=='admin' && dims_moduleadmin())
			if ($_SESSION['dims']['action'] == 'admin') {
				if (file_exists(DIMS_APP_PATH . "modules/".$_SESSION['dims']['moduletype']."/admin.php")) require_once(DIMS_APP_PATH . "modules/".$_SESSION['dims']['moduletype']."/admin.php");
			}
			else {
				if (file_exists(DIMS_APP_PATH . "modules/".$_SESSION['dims']['moduletype']."/public.php")) require_once(DIMS_APP_PATH . "modules/".$_SESSION['dims']['moduletype']."/public.php");
			}

		}
		$main_content = ob_get_contents();
		@ob_end_clean();

		$dims_ns_css="";

		// GET ADDITIONAL CSS FROM NS
		if (file_exists($_SESSION['dims']['template_path']."/NSTools.php")) {
			ob_start();
			require($_SESSION['dims']['template_path']."/NSTools.php");
			echo NS_CSS_SEGMENT($_SESSION['dims']['template_path']."/");
			$dims_ns_css = ob_get_contents();
			@ob_end_clean();
		}
		/*
		$template_body->assign_vars(array(
			'TEMPLATE_PATH'			=> $_SESSION['dims']['template_path'],
			'ADDITIONAL_JAVASCRIPT' => $additional_javascript,
			'PAGE_CONTENT'			=> $main_content
			)
		);
		$template_body->pparse('body');
		*/
		if (empty($workspace)) {
			$workspace = new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$metadesc=($workspace->fields['meta_description']);
			$metakeywords=($workspace->fields['meta_keywords']);
			$title=($workspace->fields['title']);
		}
		else {
			$metadesc="";
			$metakeywords="";
			$title="";
		}

		$tpl_site=array(
			'TEMPLATE_PATH'				=> $_SESSION['dims']['template_path'],
			'DIMS_NS_CSS'				=> $dims_ns_css,
			'SCRIPT_ENV'				=> $scriptenv,
			'PAGE_CONTENT'				=> $main_content,
			'TITLE'						=> $title,
			'META_DESCRIPTION'			=> $metadesc,
			'META_KEYWORDS'				=> $metakeywords,
			'META_AUTHOR'				=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
			'META_COPYRIGHT'			=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
			'META_ROBOTS'				=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
			'SITE_TITLE'				=> (isset($_SESSION['dims']['currentworkspace']['title'])?$_SESSION['dims']['currentworkspace']['title']:$_SESSION['dims']['currentworkspace']['label']),
			'WORKSPACE_META_DESCRIPTION'=> $metadesc,
			'WORKSPACE_META_KEYWORDS'	=> $metakeywords,
			'WORKSPACE_META_AUTHOR'		=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
			'WORKSPACE_META_COPYRIGHT'	=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
			'WORKSPACE_META_ROBOTS'		=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",

			'ADDITIONAL_JAVASCRIPT'			=> $additional_javascript);

		$smarty->assign('site',$tpl_site);
		$smarty->display('popup.tpl');
	}
	else dims_redirect("admin.php");
}

//require_once DIMS_APP_PATH . '/include/stats.php';

if ($dims_errors_level && _DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '') echo mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");
if (defined('_DIMS_ACTIVELOG') && _DIMS_ACTIVELOG)	include DIMS_APP_PATH . '/modules/system/hit.php';

$db->close();
?>
