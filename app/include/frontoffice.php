<?php defined('AUTHORIZED_ENTRY_POINT') or exit;
dims_init_module('wce');
// pour etre sur que l'on recalcule bien
if (!isset($_SESSION['test_ab_permanent'])) {
	unset($_SESSION['templateab']);
	unset($_SESSION['test_ab_permanent']);
	unset ($_SESSION['test_ab_reference']);
}

$into_cata = false;

if (isset($_GET['urlrewrite']) && isset($_GET['pathrewrite'])) {
	// verification du referred si page existe ou non

	$buildpath=realpath("./".dims_load_securvalue('pathrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true)."/".dims_load_securvalue('urlrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true).".html");
	$buildphysicpath=realpath(".")."/".dims_load_securvalue('pathrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true)."/".dims_load_securvalue('urlrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true).".html";

	if (file_exists($buildphysicpath) && strpos($buildphysicpath, "..")===FALSE) {
		include($buildphysicpath);
	} else {

		$url=dims_load_securvalue("urlrewrite",dims_const::_DIMS_CHAR_INPUT,true,false,true);
		$pathrewrite=dims_load_securvalue("pathrewrite",dims_const::_DIMS_CHAR_INPUT,true,false,true);
		if (isset($url) && $url!='') {

			$foundredirect=false;

			if (isset($_SERVER['REQUEST_URI']) && file_exists(DIMS_APP_PATH . '/modules/catalogue/include/global.php') && $dims->isModuleTypeEnabled('catalogue')) {
				// on recherche si rubrique ou article
				if (!isset($_SESSION['urlrewrite'])) {
					require_once(DIMS_APP_PATH . '/modules/catalogue/include/global.php');
					require_once(DIMS_APP_PATH . '/modules/catalogue/include/functions.php');
					cata_getfamilys();
				}

				$urlarticle='';
				$urlpath=$_SERVER['REQUEST_URI'];
				// bug sur certains caracteres
				$urlpath=  urldecode($urlpath);
				//dims_print_r($_GET);
				$posit=strpos($urlpath,".html?");

				if ($posit!==false) {
					$urlpath=substr($urlpath,0,$posit+5);
				}

				//dims_print_r($_SERVER);
				//dims_print_r($urlpath);

				$urlpath=str_replace(".html", "",$urlpath);
				if (substr($urlpath,0,1)=="/") {
					$urlpath=substr($urlpath,1);
				}

				// test sur les familles d'articles
				if (isset($_SESSION['urlrewrite'][$urlpath])) {
					$_GET['op']='catalogue';
					$into_cata = true;
					if (!isset($_GET['param'])) {
						$_GET['param']=$_SESSION['urlrewrite'][$urlpath];
					}
					//$_SESSION['param']=$_GET['param'];
					$foundredirect=true;
				}

				// test sur les articles
				if (substr($urlpath,0,8)=="article/") {

					// on a un article
					$res=$db->query("select id from dims_mod_cata_article where urlrewrite like :url", array(
						':url' => array('type' => PDO::PARAM_STR, 'value' => utf8_decode($url)),
					));

					if ($f=$db->fetchrow($res)) {
						if (!isset($_GET['op'])) {
							$op="fiche_article";
							$_GET['op']=$op;
							$into_cata = true;
						}
						$artid=$f['id'];
						$_GET['artid']=$f['id'];
						$foundredirect=true;
					}
				}
			}

			if (!$foundredirect) {
				$id_workspace=0;
				$displayContent=false;

				// traitement du rewriting auto des pages et rubriques
				$select = "	SELECT distinct	wd.id_workspace
						FROM		dims_workspace_domain as wd
						inner join	dims_domain as d
						on		d.id=wd.id_domain
						and		(d.domain =:domain OR d.domain = '*') and (wd.access=1 or wd.access=2)";

				$res=$db->query($select, array(':domain' => array('type' => PDO::PARAM_STR, 'value' => $_SERVER['HTTP_HOST'])));

				if ($fields = $db->fetchrow($res)) {
					$id_workspace=$fields['id_workspace'];
				}

				require_once(DIMS_APP_PATH."modules/wce/include/classes/class_article.php");
				$art = new wce_article();

				if ($id_workspace>0) {

					// recherche de la page ayant le nom passe en parametre
					$select = "	SELECT	id,urlrewriteold,urlrewrite, id_lang
							FROM	dims_mod_wce_article
							WHERE	id_workspace = :idworkspace
							AND	(urlrewrite != 'error'
							AND	urlrewrite != 'erreur')
							AND	(urlrewrite = :urlrewrite
							OR	urlrewriteold = :urlrewrite)";

					$res=$db->query($select, array(
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
						':urlrewrite' => array('type' => PDO::PARAM_STR, 'value' => $url),
					));
					$path="";

					// test si url correspond ou pas si bonne page mais sous contenu inexact
					// ex : www.monsite.com/mauvaiserubrique/pagequiexiste.html
					if ($_GET['pathrewrite']!="")
						$buildpath=$art->getRootPath()."/".dims_load_securvalue('pathrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true)."/".dims_load_securvalue('urlrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true).".html";
					else
						$buildpath=$art->getRootPath()."/".dims_load_securvalue('urlrewrite', dims_const::_DIMS_CHAR_INPUT, true, true, true).".html";


					// on test le nombre de résultat
					if ($db->numrows($res)>0) {
						//if ($db->numrows($res)>1) {
							// on recherche celui qui correspond le mieux
							// on recupere la liste des ip, on genere le path et on teste
							while ($fields = $db->fetchrow($res)) {
								/*if((!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) || $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] != $fields['id_lang']){
									$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $fields['id_lang'];
								}*/
								$art->open($fields['id'],$fields['id_lang']);
								$path_tmp=$art->getUrlRewriting();


								if ($path_tmp==$buildpath) {
									$path=$path_tmp;
									$id_article=$fields['id'];
									$_GET['articleid']=$id_article;
									$_GET['lang'] = $fields['id_lang'];
								}
							}
						/*}
						else {
							$fields = $db->fetchrow($res);
							$id_article=$fields['id'];
							$_GET['articleid']=$id_article;
							$art->open($id_article,$fields['id_lang']);
							$path=$art->getUrlRewriting();
							if((!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) || $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] != $fields['id_lang']){
								$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $fields['id_lang'];
							}
						}*/

						if ($path!=$buildpath) {
							dims_redirect($path);

						} else {
							unset($_GET['urlrewrite']);
							$smarty->assign('into_cata', $into_cata);
							$view->assign('into_cata', $into_cata);
							$_SESSION['dims']['catalogue_mode']=$into_cata;
							include DIMS_APP_PATH . '/modules/wce/display.php';
							$displayContent=true;
						}
					} else {
						// on regarde les pages alternatives
						$select = "SELECT	ab.*
							FROM		dims_mod_wce_article_ab as ab
							INNER JOIN	dims_mod_wce_article as a
							ON		a.id=ab.id_article
							AND		a.id_workspace=:idworkspace
							AND		ab.urlrewrite = :urlrewrite";

						$res=$db->query($select, array(
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
							':urlrewrite' => array('type' => PDO::PARAM_STR, 'value' => $url),
						));
						if ($db->numrows($res)>0) {
							$found=false;
							while ($f=$db->fetchrow($res)) {
								// ouverture de l'article
								$artid=$f['id_article'];
								if ($artid>0 && !$found) {
									$art = new wce_article();
									$art->open($artid);
									$pathart=$art->getUrlRewriting($f['urlrewrite']);

									if ($pathart==$buildpath) {
										$found=true;

										if ($f['enable']==true) {
											if (is_dir(realpath('.')."./common/templates/frontoffice".$f['template'])) {
												$_SESSION['test_ab_template']=$f['template'];
											}

											$_SESSION['test_ab_index']=$f['index'];
											if ($f['permanent']) {
												$_SESSION['test_ab_permanent']=1; // on met en permanent le lien
											}
											$_SESSION['test_ab_reference']=$f['reference'];
											$id_article=$f['id_article'];
											$_GET['articleid']=$id_article;
											unset($_GET['urlrewrite']);
											$smarty->assign('into_cata', $into_cata);
											$view->assign('into_cata', $into_cata);
											$_SESSION['dims']['catalogue_mode']=$into_cata;
											include DIMS_APP_PATH . '/modules/wce/display.php';
											}
										else {
											// on reprend la redirection par défaut de l'article car test ab non active
											$pathart=$art->getUrlRewriting();
											dims_redirect($pathart);
										}
									}
								}
							}
						} else {
							$path_tmp=$art->getUrlRewriting();

							$chheadings=explode("/",$pathrewrite);
							$id_heading=0;
							foreach ($chheadings as $el => $h) {

								if ($h!='') {
									$select = "SELECT	id,urlrewrite
										FROM		dims_mod_wce_heading
										where		id_workspace=:idworkspace
										AND		(urlrewrite=:urlrewrite)";

									$res=$db->query($select, array(
										':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
										':urlrewrite' => array('type' => PDO::PARAM_STR, 'value' => $h),
									));

									if ($db->numrows($res)>0) {
										while ($hed=$db->fetchrow($res)) {
												$id_heading=$hed['id'];
										}
									}
								}
							}
							if ($id_heading>0) {
								$_GET['headingid']=$id_heading;
								unset($_GET['urlrewrite']);
								$smarty->assign('into_cata', $into_cata);
								$view->assign('into_cata', $into_cata);
								$_SESSION['dims']['catalogue_mode']=$into_cata;
								include DIMS_APP_PATH . '/modules/wce/display.php';
								$displayContent=true;
							}
							else {
								ob_end_clean();
								header("HTTP/1.0 404 Not Found");

								// recherche du template par dï¿½faut
								$select = "SELECT	template
								FROM	dims_workspace_template
								where	id_workspace=:idworkspace";

								$res=$db->query($select, array(':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace)));

								if ($fields = $db->fetchrow($res)) {
									$template=DIMS_APP_PATH . "./common/templates/frontoffice".$fields['template'];
									if (file_exists(realpath($template."/404.php"))) require_once(realpath($template."/404.php"));
									elseif (file_exists(realpath($template."/error.html"))) require_once(realpath($template."/error.html"));
									elseif (file_exists(realpath($template."/errors.html"))) require_once(realpath($template."/errors.html"));
									elseif (file_exists(realpath($template."/error.php"))) require_once(realpath($template."/error.php"));
									elseif (file_exists(realpath($template."/errors.php"))) require_once(realpath($template."/errors.php"));
									else {
										dims_404();
										// $path= DIMS_APP_PATH."error.html";
										// if (file_exists($path)) require_once($path);
									}
								}
								else {
									dims_404();
									// $path= DIMS_APP_PATH."error.html";
									// if (file_exists($path)) require_once($path);
								}
							}
						}
					}

					// test si une page n'est pas en error ou erreur .html rewrite
					// traitement des fichiers htm ou html
					if (!isset($_SERVER['SCRIPT_URI']) && isset($_SERVER['REQUEST_URI'])) {
						$_SERVER['SCRIPT_URI']=$_SERVER['REQUEST_URI'];
					}

					// on echappe les referrer pour eviter les appels de fichiers manquants rediriges
					if (!$displayContent && !file_exists($path) && !isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['SCRIPT_URI'])
						&& ((substr($_SERVER['SCRIPT_URI'],-4)==html || substr($_SERVER['SCRIPT_URI'],-3)==htm)
						|| (strpos('.',$_SERVER['SCRIPT_URI'])===false))) {

						ob_end_clean();
						header("HTTP/1.0 404 Not Found");
						$select = "SELECT	id,urlrewriteold,urlrewrite
							FROM	dims_mod_wce_article
							where	id_workspace=:idworkspace
							and		(urlrewrite='error'
							OR		urlrewrite='erreur')";

						$res=$db->query($select, array(':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace)));
						$id_article=0;
						if ($db->numrows($res)>0) {
							// on recherche celui qui correspond le mieux
							// on recupere la liste des ip, on genere le path et on teste
							while ($fields = $db->fetchrow($res)) {
								$id_article=$fields['id'];
								$_GET['articleid']=$id_article;
							}
							if ($id_article>0) {
								$smarty->assign('into_cata', $into_cata);
								$view->assign('into_cata', $into_cata);
								$_SESSION['dims']['catalogue_mode']=$into_cata;
								include DIMS_APP_PATH . 'modules/wce/display.php';
							}
						}
					}
				}
				else {
					dims_404();
					// $path= DIMS_APP_PATH."error.html";
					// if (file_exists($path)) require_once($path);
				}
			}
			else {
				$smarty->assign('into_cata', $into_cata);
				$view->assign('into_cata', $into_cata);
				$_SESSION['dims']['catalogue_mode']=$into_cata;
				include DIMS_APP_PATH . 'modules/wce/display.php';
			}
		}
	}
}
else {
	$smarty->assign('into_cata', $into_cata);
	$view->assign('into_cata', $into_cata);
	$_SESSION['dims']['catalogue_mode']=$into_cata;
	include DIMS_APP_PATH . 'modules/wce/display.php';
}

$db->close();
?>
