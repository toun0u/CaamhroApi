<?php

/**
* Clears Varnish cache for the provided domain
*
* @param domain The domain to clear (e.g. http://www.frimaudeau.fr)
* @return TRUE if the operation succeeded, FALSE otherwise.
*/
function dims_clearcache($domain) {
	$purge = http_request_method_register("PURGE");
	$result = http_request($purge, $domain);
	if (strpos($result, '200 OK') !== FALSE) {
		return true;
	} else {
		return false;
	}
}

/**
* PHP "print_r" encapsulation for HTML debugging
*
* @param mixed var to show
* @return string PHP print_r() output HTMLized
*
* @version 2.09
* @since 0.1
*
* @category debugging / error management
*/
function dims_print_r($var) {
	print "\n<div align=\"left\">\n<pre>";
	print_r ($var);
	print "\n</pre>\n</div>";
}

/**
 * PHP "print_r" encapsulation for file debugging
 * Write in a file with file_put_contents (file will be rewritted)
 * @param mixed $var : var to show
 * @param string $filename : filename of the debug file (optionnal)
 * @param string $path	: pathname of the debug file (optionnal)
 * @example dims_print_r_file($dims_op, 'file.txt', './debug/', true);
 * @author Aurélien Tisserand
 */
function dims_print_r_file($var, $filename = 'file.txt', $path = './debug/', $append = false){
	$message = "\n<div align=\"left\">\n<pre>";
	$print_r = print_r($var, true);
	$message .= $print_r ;
	$message .= "\n</pre>\n</div>";
	if($append == false){
		file_put_contents($path.''.$filename, $message);
	}else{
		file_put_contents($path.''.$filename, $message, FILE_APPEND);
	}

}

function dims_cleanexit($statuscode = 0) {
	ob_clean();
	exit($statuscode);
}

function dims_flushexit($statuscode = 0) {
	ob_flush();
	exit($statuscode);
}

//cyril --> pratique pour le debug pour afficher le contenu d'un variable et passer à la ligne derrière
function dims_echobr($chaine) {
	print $chaine."<br/>";
}

/**
* ! description !
*
* @param string module type
* @return void
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*/
function dims_init_module($moduletype) {

	if (!defined("_DIMS_INITMODULE_$moduletype")) {
		define("_DIMS_INITMODULE_$moduletype",	1);

		global $dims_help;

		$defaultlanguagefile = DIMS_APP_PATH . "/modules/$moduletype/lang/french.php";
		//$languagefile = DIMS_APP_PATH . "/modules/$moduletype/lang/{$_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_language']}.php";
		$globalfile = DIMS_APP_PATH . "/modules/$moduletype/include/global.php";
		//$helpfile = DIMS_APP_PATH . "/modules/$moduletype/help/{$_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_language']}.php";

		/*
		if (file_exists($defaultlanguagefile)) {
			require_once($defaultlanguagefile);
		}

		if ($languagefile != 'french' && file_exists($languagefile)) {
			require_once($languagefile);
		}
		 */
		if (file_exists($globalfile)) {
			require_once($globalfile);
		}

		/*
		if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['site_contextualhelp'] && file_exists($helpfile))
		{
			require_once $helpfile;
			$dims_help = array_merge($dims_help, $help);
		}
		*/
	}
}

/**
* listing groups IDs for a given module instance depending on it's view policy (PRIVATE/DESC/ASC/GLOBAL)
*
* @param int module instance id
* @return string comma serparated list of group IDs
*
* @version 2.10
* @since 0.1
*
* @category module/group management
*/
function dims_viewworkspaces($moduleid = -1, $mode = '', $viewmode = null) {
	return dims::getInstance()->getViewWorkspaces($moduleid, $mode, $viewmode);
}

/**
* listing groups IDs for a given module instance depending on it's view policy (PRIVATE/DESC/ASC/GLOBAL)
*
* @param int module instance id
* @return string comma serparated list of group IDs
*
* @version 2.10
* @since 0.1
*
* @category module/group management
*/
function dims_viewworkspaces_rec($moduleid = -1, $mode = '') {
	global $dims;

	if ($mode == 'web') {
		$current_workspaceid = $_SESSION['dims']['webworkspaceid'];
	}
	else {
		if ($_SESSION['dims']['workspaceid'] == '') $current_workspaceid = dims_const::_DIMS_SYSTEMGROUP; // HOME PAGE / NO GROUP;
		else $current_workspaceid = $_SESSION['dims']['workspaceid'];
	}
	$workspaces = '';

	if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined
	$work = new workspace();
	$work->open($current_workspaceid);
	$mods=$dims->getModules($current_workspaceid);

	switch($mods[$moduleid]['viewmode']) {
		default:
		case dims_const::_DIMS_VIEWMODE_PRIVATE:
			$workspaces = $current_workspaceid;
		break;

		case dims_const::_DIMS_VIEWMODE_DESC:
			$lst=$work->getworkspacechildren();
			foreach($lst as $i => $w) {
				if ($workspaces!='') $workspaces.=',';
				$workspaces .= $w['id'];
			}

			if ($workspaces!='') $workspaces.=',';
			$workspaces .= $current_workspaceid;
		break;

		case dims_const::_DIMS_VIEWMODE_ASC:
			$lst=$work->getparents();
			foreach($lst as $i => $w) {
				if ($workspaces!='') $workspaces.=',';
				$workspaces .= $w['id'];
			}

			if ($workspaces!='') $workspaces.=',';
			$workspaces .= $current_workspaceid;
		break;

		case dims_const::_DIMS_VIEWMODE_GLOBAL:
			$workspaces = dims_getAllWorkspaces(); //$_SESSION['dims']['allworkspaces'];
		break;
	}

	if ($mode == 'web') {
		$array_workspaces = explode(',',$workspaces);
		// filtrer sur les groupes web public !!!! (pas fait)
		$workspaces = implode(',',$array_workspaces);
	}
	return $workspaces;
}

// return an array with ip in values
function dims_getip($wan_only = false) {
	$ip = '';

	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
	else $ip = "UNKNOW";
	return $ip;
}

function dims_htpasswd($pass) {
	return (crypt(trim($pass),CRYPT_STD_DES));
}

/**
* set a value to a specific var in current web session only if var exist
*
* @param string value name
* @param mixed value to be set
* @return bool TRUE if var exist, FALSE otherwise
*
* @version 2.09
* @since 0.1
*
* @category session management
*/
function dims_set_flag($var,$value) {
	if (!isset($_SESSION[$var])) $_SESSION[$var]='';
	if (!strstr($_SESSION[$var],"[$value]")) {
		$_SESSION[$var].="[$value]";
		return(true);
	}
	else return(false);
}

/**
* HTTP redirection encapsulation
*
* @param string link to be redirected
* @return void
*
* @version 2.09
* @since 0.1
*
* @package DIMS
* @subpackage global functions
* @category
*
* @license http://www.netlorconcept.com
*/

function dims_redirect($link, $urlencode = false) {
	if (_DIMS_DEBUGMODE) {
		$backtrace = debug_backtrace();
		$lastcall = $backtrace[0];

		header(sprintf('X-Dims-Redirect-From: %s:%s', $lastcall['file'], $lastcall['line']));
	}

	if ($urlencode || _DIMS_URL_ENCODE) $link = dims_urlencode($link,true);

	if (ob_get_length() > 0) ob_end_clean();
	if (!headers_sent()) {
		header("Location: $link");
		exit();
	}
}

function dims_404() {
	header("HTTP/1.0 404 Not Found");
	if (file_exists(realpath('.').'/404.html'))
		echo file_get_contents(realpath('.').'/404.html');
}

function dims_error($msg) {
	global $_DIMS;
	if (_DIMS_DEBUGMODE) {
		global $skin;
		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_SECURITY_ERROR']);
		echo "<p align=\"center\"><b><br>$msg<br><br></b></p>";
		echo $skin->close_simplebloc();
	}
}

/**
* list available skins
*
* @return array skin(s) list
*
* @version 2.09
* @since 0.1
*
* @category HTML styles management
*/
function dims_getavailableskins() {
	clearstatcache();

	$skins = array();
	$ptrDir = @opendir(DIMS_APP_PATH . '/skins');

	while ($skin = @readdir($ptrDir))
	{
		if ($skin != '.' && $skin != '..' && is_dir(DIMS_APP_PATH . "/skins/$skin"))
		{
			$skins[] = $skin;
		}
	}

	return($skins);
}

/**
* list available templates
*
* @return array template(s) list
*
* @version 1.00
* @since 0.1
*
* @category HTML styles management
*/
function dims_getavailabletemplates($type = 'frontoffice') {
	$templates = array();
	$basepath = DIMS_APP_PATH.'templates'._DIMS_SEP.$type;
	$p = @opendir(realpath($basepath));

	while ($template = @readdir($p)) {
		if($template == '.' || $template == '..') {
			// Exit current iteration
			continue;
		}

		$tplpath = realpath($basepath._DIMS_SEP.$template);

		if (is_dir($tplpath) && (file_exists($tplpath._DIMS_SEP.'index.tpl') || file_exists($tplpath._DIMS_SEP.'index.tpl.php'))) {
			$templates[strtolower ($template)] = $template;
		}
	}

	ksort($templates);
	return($templates);
}


function dims_workspace_sort($a,$b) {
	return (intval($_SESSION['dims']['workspaces'][$b]['depth'])<intval($_SESSION['dims']['workspaces'][$a]['depth']));
}

function dims_index_drop($id_record,$id_object,$id_module,$typecontent="") {
	$db = dims::getInstance()->getDb();
	$tabsentence=array();

	if(!empty($id_record) && !empty($id_object) && !empty($id_module)) {
		// construction de la liste des sentences
		$params = array(
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
		);
		if ($typecontent!="") {
			$sql=	"SELECT DISTINCT id_sentence
					FROM 			dims_keywords_index
					INNER JOIN 		dims_keywords_sentence
					ON 				dims_keywords_index.id_sentence=dims_keywords_sentence.id
					WHERE 			id_record=:idrecord
					AND 			id_object=:idobject
					AND 			id_module=:idmodule
					AND 			typecontent =:typecontent";
			$params[':typecontent'] = array('type' => PDO::PARAM_INT, 'value' => $typecontent);
		}
		else
			$sql=	"SELECT DISTINCT id_sentence
					FROM 			dims_keywords_index
					WHERE 			id_record=:idrecord
					AND 			id_object=:idobject
					AND 			id_module=:idmodule";

		$rs=$db->query($sql, $params);
		if ($db->numrows($rs)>0) {
			while ($fields = $db->fetchrow($rs)) {
				$tabsentence[]=$fields['id_sentence'];
			}
		}
	}

	// on met à jour corresp index et sentence
	if (sizeof($tabsentence)>0) {
		$params = array();
		$res=$db->query("DELETE FROM dims_keywords_index WHERE id_sentence IN (".$db->getParamsFromArray($tabsentence, 'idsentence', $params).")", $params);
		$params = array();
		$res=$db->query("DELETE FROM dims_keywords_corresp WHERE id_sentence IN (".$db->getParamsFromArray($tabsentence, 'idsentence', $params).")", $params);
		$params = array();
		$res=$db->query("DELETE FROM dims_keywords_sentence WHERE id IN (".$db->getParamsFromArray($tabsentence, 'idsentence', $params).")", $params);
	}
}

function getSearchTags() {
	global $_DIMS;
	$result="";

	if (!isset($_SESSION['dims']['search']['listselectedtag'])) {
		$result="<font class=\"noresponsetags\">".$_DIMS['cste']['_DIMS_NO_TAGS_SEARCH']."</font>";
	}
	else {
		$c=0;
		$tot=sizeof($_SESSION['dims']['search']['listselectedtag']);
		$result.="&nbsp;";
		foreach ($_SESSION['dims']['search']['listselectedtag'] as $key=>$elem) {
			$result.="<a href=\"#\" onclick=\"deleteSelectedTag($key)\">".$elem."</a>";
			$c++;
			if ($c<$tot) $result.="&nbsp;|&nbsp;";

		}
		$result.="&nbsp;";
	}
	return $result;
}

function getSearchLastTags() {
	global $_DIMS;
	$db = dims::getInstance()->getDb();
	$result="";

	unset($_SESSION['dims']['search']['lastlistselectedtag']);
	unset($_SESSION['dims']['search']['lastlistselectedtagindex']);

	//if (!isset($_SESSION['dims']['search']['lastlistselectedtag'])) {
		// on verifie si on a pas d'information en bd

		$rs =$db->query("SELECT DISTINCT 	st.*,t.tag
						FROM 				dims_user_search_tag AS st
						INNER JOIN 			dims_tag as t
						ON 					t.id=st.id_tag
						AND 				st.id_user=:iduser
						ORDER BY 			position",
						array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
						)
						);
		$pos=1;
		if ($db->numrows($rs)>0) {
			while ($fields = $db->fetchrow($rs)) {
				if (isset($_DIMS['cste'][$fields['tag']])) {
					$fields['tag']=$_DIMS['cste'][$fields['tag']];
				}
				$_SESSION['dims']['search']['lastlistselectedtag'][$fields['id_tag']]=$fields['tag'];
				$_SESSION['dims']['search']['lastlistselectedtagindex'][$pos]=$fields['id_tag'];
				$pos++;
			}
		}
	//}

	if (isset($_SESSION['dims']['search']['lastlistselectedtag'])) {
		$arraytag=$_SESSION['dims']['search']['lastlistselectedtag'];
		$tot=sizeof($arraytag);

		$c=1;
		foreach ($arraytag as $key=>$elem) {
			// test si on affiche deja en tag used
			if (!isset($_SESSION['dims']['search']['listselectedtag'][$key])) {
				$result.='<a style="color:#5E5E5E;" href="javascript:addSelectedTag(\''.$key.'\');prepareWord();">'.$elem.'</a>&nbsp;';

				if ($c<$tot) $result.="&nbsp;|&nbsp;";
			}
			$c++;
		}

	}
	else $result="<font class=\"noresponsetags\">".$_DIMS['cste']['_DIMS_NO_TAGS_SEARCH']."</font>";
	return $result;
}


function getSearchExpression($link=true) {
	global $_DIMS;
	$result="";
	if (isset($_SESSION['dims']['search']['listselectedword']) && sizeof($_SESSION['dims']['search']['listselectedword'])>0) {
		$c=0;
		$result.="&nbsp;";
		foreach ($_SESSION['dims']['search']['listselectedword'] as $key=>$elem) {
			if ($link) $result.=" ".str_repeat('(',$elem['('])."<a href=\"#\" onclick=\"deleteSelectedWord($key)\">";
			else $result.=" ".str_repeat('(',$elem['(']);

			if (strpos($elem['word']," ")===false) $result.=$elem['word'];
			else $result.="\"".$elem['word']."\"";

			if ($link) $result.="</a>".str_repeat(')',$elem[')']);
			else $result.=str_repeat(')',$elem[')']);

			$c++;
			if ($c<sizeof($_SESSION['dims']['search']['listselectedword'])) {
				if ($link) {
					if ($elem['op']=="AND") $result.= " <a href=\"#\" onclick=\"updateOperator($key,0)\">&</a> ";
					else $result.= " <a href=\"#\" onclick=\"updateOperator($key,1)\">U</a> ";
				}
				else {
					if ($elem['op']=="AND") $result.= " &";
					else $result.=" U";
				}
			}
		}
		$result.="&nbsp;";
	}
	else {
		if ($link) $result="<font class=\"noresponsewords\">".$_DIMS['cste']['_DIMS_NO_WORDS_SEARCH']."</font>";
		else $result="";
	}
	return $result;
}

function getSearchCampaign($campaignid) {
	$result="";
	$db = dims::getInstance()->getDb();
	$c=0;
	if (isset($campaignid) && is_numeric($campaignid)) {
		$sql = "SELECT		*
			FROM		dims_campaign_keyword
			WHERE		id_campaign= :idcampaign
			ORDER BY	position";
		$res=$db->query($sql, array(':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => $campaignid)));
		$nbelement=$db->numrows($res);
		if ($nbelement>0) {
			while ($elem=$db->fetchrow($res)) {

				$result.=" ".str_repeat('(',$elem['(']);

				if (strpos($elem['word']," ")===false) $result.=$elem['word'];
				else $result.="\"".$elem['word']."\"";

				$result.=str_repeat(')',$elem[')']);
				$c++;
				if ($c<$nbelement) {
					if ($elem['op']=="AND") $result.= " & ";
					else $result.= " U ";
				}
			}
		}
		$result.="&nbsp;";
	}
	else $result="<font class=\"noresponsewords\">".$_DIMS['cste']['_DIMS_NO_WORDS_SEARCH']."</font>";
	return $result;
}

function getSearchReponse($id_object,$id_module) {
	if (isset($_SESSION['dims']['search']['result'][$id_module][$id_object])) {
		return(array_unique(array_keys($_SESSION['dims']['search']['result'][$id_module][$id_object])));
	}
	else return (array());
}

function getSearchcontent($idobject,$idmodule,$tabid,$tabresult,$dims_op) {
	$db = dims::getInstance()->getDb();
	global $dims;
	global $_DIMS;

	$result="";

	if ($idmodule>0 && !empty($tabid) && !empty($tabid)) {
		// recherche des contenus
		$params = array();
		$sql = "SELECT		distinct parag,content,id_sentence,id_record
			FROM		dims_keywords_usercache
			INNER JOIN	dims_keywords_sentence
			ON		dims_keywords_sentence.id=dims_keywords_usercache.id_sentence
			AND		id_module= :idmodule
			AND		id_object= :idobject
			AND		id_record in (".$db->getParamsFromArray($tabid, 'idrecord', $params).")
			ORDER BY	parag,count desc";

		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $idobject);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $idmodule);

		$rs=$db->query($sql, $params);

		if ($db->numrows($rs)>0) {
			while ($f=$db->fetchrow($rs)) {
				if (isset($f['typecontent']) && ( substr($f['typecontent'],0,7)=="content" || $f['typecontent']=="description")) {
					if ($tabresult[$f['id_record']]['content']!="") $tabresult[$f['id_record']]['content'].="...";
					$tabresult[$f['id_record']]['content'].=$f['content'];
				}
			}
		}

		// recherche des annotations
		$params = array();
		$sql = "SELECT		an.id_record,count(an.id) as cpteannot
			FROM		dims_annotation an
			WHERE		an.id_record in (".$db->getParamsFromArray($tabid, 'idrecord', $params).")
			AND		an.id_object= :idobject
			AND		an.id_module= :idmodule
			GROUP BY	an.id_record";

		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $idobject);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $idmodule);

		$rs=$db->query($sql, $params);

		if ($db->numrows($rs)>0) {
			while ($f=$db->fetchrow($rs)) {
				if (isset($tabresult[$f['id_record']])) $tabresult[$f['id_record']]['annot']=$f['cpteannot'];
			}
		}
	}

	if (true) {
		// init preview content
		$previewcontent=1;
		$objmod=$dims->getModule($idmodule);
		$moduletype=$objmod['label'];
		if ($moduletype==1) $previewcontent=0;
		// display current result
		$c=0;

		if (isset($_SESSION['dims']['current_object']['id_module']) && isset($_SESSION['dims']['current_object']['id_record'])) {
			$selmoduleid=$_SESSION['dims']['current_object']['id_module'];
			$selobjectid=$_SESSION['dims']['current_object']['id_object'];
			$selrecordid=$_SESSION['dims']['current_object']['id_record'];
		}
		else {
			$selmoduleid=0;
			$selobjectid=0;
			$selrecordid=0;
		}

		$pos=0;
		$bcourant=1;
		$chdate='';
		$nbelem=sizeof($tabresult);
		$limit=80;
		$nbblock=0;

		if ($nbelem>$limit) $nbelem=$limit;

		// on ajoute le code pour faire plusieurs onglets sinon ne tient pas
		$maxelem=12;

		if ($nbelem>$maxelem) {
			$result.=	"<div style=\"width:100%;text-align:center;margin-top:4px;\">";
			$nbblock=$nbelem/$maxelem;
			if ($nbelem%$maxelem>0) $nbblock++;
		}

		// on boucle sur les blocs pour afficher le multi page
		for($b=1;$b<=$nbblock;$b++) {
			$result.=  "<a style=\"border:dotted 1px #6E6E6E;padding:1px;\" href=\"javascript:void(0);\" onclick=\"javascript:switchDiv('block_".$idobject."_".$idmodule."_',".$b.",".$nbblock.");\">".$b."</a>&nbsp;";
		}

		if ($nbelem>$maxelem) {
			$result.=  "</div>";
		}

				if (isset($tabresult) && !empty($tabresult)) {
					foreach ($tabresult as $id=>$elem) {
						if ($pos<$limit) {
							if ($pos%$maxelem==0) {
									if ($pos>0) $result.=  '</div>';

									$select=($pos==0) ? 'display:block;visibility:visible;' : 'display:none;visibility:hidden;';
									$result.=  '<div id="block_'.$idobject.'_'.$idmodule.'_'.$bcourant.'" style="'.$select.'">';
									$bcourant++;
							}
							//dims_print_r($elem);
							//if ($elem['id']=="") dims_print_r($elem);
							if (isset($tabresult[$elem['id']]['annot'])) {
									$elem['annot']=$tabresult[$elem['id']]['annot'];
							}
							$src='';
							if (isset($dims_op) && ($dims_op!="searchannot" || ($dims_op=="searchannot" && $elem['annot']>0))) {
									$bgcolor=($c%2) ? "1" : "";
									if($selmoduleid==$idmodule && $selobjectid=$idobject && $selrecordid==$elem['id']) $selectedobj=true;
									else $selectedobj=false;

									//if($selectedobj) $style="style=\"background-color:#CADDFF;\"";
									$style="";

									$datvar=dims_timestamp2local($elem['timestp_modify']);
									$chdate=$datvar['date'];

									$result.= '<div class="item" onclick="javascript:viewPropertiesObject('.$idobject.','.$elem['id'].','.$idmodule.','.$previewcontent.')" id="obj_'.$idobject.'_'.$elem['id'].'_'.$idmodule.'" $style>';
									//$result.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tbody>";
									$result.= '<h3>'.$elem['title'].'</h3>';
									$result.= '<p class="creation-date">'.$_DIMS['cste']['_ADD_ON'].' '.$chdate.'</p>';


									$result.= '<p class="preview">';

												if (isset($elem['photo'])){
													$real_path = $elem['photo'];
													if (substr($elem['photo'],0,1) != '.')
														$real_path = '.'.$elem['photo'];

					   if (file_exists($real_path)) {
						$src=' src="'.$real_path.'"';
					 }
					  else {
					   $src='';
						}
												}
									$result.= '<img '.$src.' alt="" class="thumb"></p>

									<p class="visits">'.$_DIMS['cste']['_DIMS_COMMENTS'].'</p>';
									//<p class="rating"><span style="width: 40%;">0</span></p>
									//$result.= '
									//<p class="tags"><img alt="" src="./common/img/tags-icon.png">&nbsp;Tags: <a href="/widgets/tag/entrepreneurship">entrepreneurship</a>, <a href="/widgets/tag/rails">rails</a>, <a href="/widgets/tag/software%2Bdevelopment">software development</a>
									//</p>
									$result.= '</div>';
									/*
									if (isset($_SESSION['dims']['selectedsearch'][$idmodule][$idobject][$idrecord])) {
											$sel_search="checked=\"checked\"";
									}
									else {
											$sel_search="";
									}
									//$result.= "<tr><td><input type=\"checkbox\" id=\"selsearch".$_SESSION['dims']['nbselectedsearch']."\" onclick=\"javascript:selSearch('".$idobject."_".$elem['id']."_".$idmodule."');\" name=\"selsearch[]\" $sel_search><span class=\"searchtitle\" onclick=\"viewPropertiesObject(".$idobject.",".$elem['id'].",".$idmodule.",".$previewcontent.");\">".$elem['title']."</span></td>";
									$result.= "<tr><td><span class=\"\" style=\"52%\">".$elem['title']."</span></td>";
									$_SESSION['dims']['nbselectedsearch']++;

									$result.="<td style=\"width:36%\" valign=\"middle\">";
									$result.="<span class=\"\">".$_DIMS['cste']['_AUTHOR']." : ".$elem['author']."&nbsp;".$chdate;

									$result.= "</td><td style=\"width:10%\"><img alt=\"".$_DIMS['cste']['_DIMS_COMMENTS']."\" title=\"".$_DIMS['cste']['_DIMS_COMMENTS']."\" border=\"0\" src=\"./common/img/annot.gif\"/>(".$elem['annot'].")</td><td>";
									// lien
									$result.="<td style=\"width:2%\" valin=\"middle\">";

									if($selectedobj) {
											$result.="<img id=\"img_".$idobject."_".$elem['id']."_".$idmodule."\"src=\"./common/img/arrow-green-right.gif\"";
									}
									else
											$result.="<img id=\"img_".$idobject."_".$elem['id']."_".$idmodule."\" src=\"./common/img/arrow-right.gif\"";

									$result.="alt=\"".$_DIMS['cste']['_DIMS_OBJECT_DISPLAY']."\" border=\"0\">";
									*/
									/*
									$result.= "<tr><td class=\"j\"	onclick=\"viewPropertiesObject(".$idobject.",".$elem['id'].",".$idmodule.",".$previewcontent.");\">";

									if ($elem['content']!="") {
											//$result.= "<div style=\"font-size:9px\" >";

											if (isset($dims_op) && $dims_op=="search") {
													$elem['content']=trim(dims_strcut($elem['content'],400));
													// on parcourt les sentences afin de les afficher
													if (isset($_SESSION['dims']['search']['listselectedword'])) {
															foreach ($_SESSION['dims']['search']['listselectedword'] as $elemw) {
																	$tabword=explode(" ",$elemw['word']);

																	foreach($tabword as $word) {
																			$elem['content']= str_replace($word, "<font style=\"font-size:12px;font-weight:bold;\">".$word."</font>", $elem['content']);
																	}
															}
													}
											}
											$result.= $elem['content']."</div>";

									}
									$result.= "</td></tr>";

									if (isset($elem['icon']) && file_exists($elem['icon'])) {
											$title="<img src=\"".$elem['icon']."\" alt=\"\">".$elem['titlelink'];
									}
									else $title=$elem['titlelink'];

									$datvar=dims_timestamp2local($elem['timestp_modify']);
									$chdate=" - ".$datvar['date'];

									$result.= "
													<tr><td  onclick=\"viewPropertiesObject(".$idobject.",".$elem['id'].",".$idmodule.",".$previewcontent.");\">
															<span class=\"searchlink\">".$_DIMS['cste']['_AUTHOR']." : ".$elem['author']."&nbsp;".$chdate;
															*//*
															</span></div>
																	</td>
																	<td align=\"right\" width=\"50%\"><table><tr>";*/

									//if ($elem['author']!="") $result.= "<td><img alt=\"\" src=\"./common/img/user.gif\"/></td><td><font class=\"fontgray\">".$elem['author']."</font></td>";

									//$result.= "<td>&nbsp;<img alt=\"".$_DIMS['cste']['_DIMS_COMMENTS']."\" title=\"".$_DIMS['cste']['_DIMS_COMMENTS']."\" border=\"0\" src=\"./common/img/annot.gif\"/></td><td>";
									//$result.= "</td><td align=\"right\" width=\"20px\"><a href=\"#\" onclick=\"viewPropertiesObject(".$idobject.",".$elem['id'].",".$idmodule.");\"><img src=\"./common/img/arrow-right.gif\" alt=\"".$_DIMS['cste']['_DIMS_OBJECT_DISPLAY']."\" border=\"0\"></a></td>";
									//$result.= "<a href=\"#\" onclick=\"addTags(event,'".dims_urlencode("dims_op=addtags&id_object=".$idobject."&id_record=".$elem['id']."&moduleid=".$idmodule)."');\">";
									//$result.= "<font class=\"fontgray\">".$elem['annot']." ".$_DIMS['cste']['_DIMS_LABEL_A']NNOTATION']."</font></a></td>";</table>
									/*
									$result.= "</td>
															</tr>
															</tbody>
															</table>
													</div>";*/
									$c++;
									$pos++;
							}
						}
					}
				}
				if ($pos>0) $result.=  '</div>';
		//echo "</div>";
	}
	else {
		$result.= "<table width=\"100%\">";

		// display current result
		foreach ($tabresult as $id=>$elem) {

			$color = (!isset($color) || $color == 2) ? 1 : 2;
			$idobject=$elem['id_object'];

			if (isset($tabresult[$elem['id']]['annot'])) {
				$elem['annot']=$tabresult[$elem['id']]['annot'];
			}

			$result.= "<tr class=\"trl$color\">";

			if ($elem['titlelink']=="") $elem['titlelink']=$elem['title'];

			$result.= "<td><a href=\"".$elem['link']."\" onmouseover=\"this.mousepointer\">".$elem['titlelink']."</a></td>";
			if ($elem['view']!="") $result.= "<td style=\"width:120px\"><img alt=\"\" src=\"./common/img/view.png\"/>&nbsp;".$elem['view']."</td>";
			if ($elem['author']!="") $result.= "<td style=\"width:120px\"><img alt=\"\" src=\"./common/img/user.gif\"/>".dims_strcut($elem['author'],60)."</td>";
			$result.= "<td style=\"width:40px\"><a href=\"#\" onclick=\"addTags(event,'".dims_urlencode("dims_op=addtags&id_object=".$idobject."&id_record=".$elem['id']."&moduleid=".$idmodule)."');\">";
			$result.= "<img alt=\"\" src=\"./common/img/annot.gif\"/>(".$elem['annot'].")</a></td></tr>";
		}
		$result.= "</table>";
	}
	return $result;
}

function getCampaigns() {
	$db = dims::getInstance()->getDb();

	$sql=	"SELECT 	dims_campaign.*,dims_user.login
			FROM		dims_campaign
			INNER JOIN	dims_user
			ON 			dims_user.id=dims_campaign.id_user
			WHERE		dims_campaign.id_workspace=:idworkspace
			AND			(dims_campaign.id_user=:iduser OR dims_campaign.share=2)";

	$res=$db->query($sql, array(
		':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
	));

	$tabcampaign=array();

	if ($db->numrows($res)>0)
	{

		while ($f=$db->fetchrow($res))
		{
			$tabcampaign[]=$f;
		}
	}
	return $tabcampaign;
}

function addWordSearch($word) {
	$db = dims::getInstance()->getDb();
	require_once(DIMS_APP_PATH . "/include/functions/string.php");
	$insert=false;
	$word=str_replace("\"","",$word);
	$tabword=explode(" ",$word);
	if (!isset($_SESSION['dims']['search'])) {
		$_SESSION['dims']['search']=array();
		$_SESSION['dims']['search']['listselectedword']=array();
		$_SESSION['dims']['search']['listselectedtag']=array();
		$_SESSION['dims']['search']['listuniqueword']=array();
		$_SESSION['dims']['search']['cacheresult']=array();
	}

	if (!isset($_SESSION['dims']['search']['listselectedword'])) $_SESSION['dims']['search']['listselectedword']=array();
	if (!isset($_SESSION['dims']['search']['listuniqueword'])) $_SESSION['dims']['search']['listuniqueword']=array();

	if (sizeof($tabword)==1) {
		// on reinitialise l'ensemble

		$rs=$db->query("select distinct id,word from dims_keywords where ucase(word) like :word", array(
			':word' => array('type' => PDO::PARAM_STR, 'value' => dims_convertaccents($word)),
		));
		$key="";
		if ($db->numrows($rs)>0) {
			if ($f=$db->fetchrow($rs)) $key=$f['id'];

			// verification de l'ajout du mot cle dans la recherche
			if ((!in_array($word,$_SESSION['dims']['search']['listselectedword'])) && $key!="") {
				// on définit une nouvelle structure de mots
				$elem['(']=0;
				$elem['op']="AND";
				$elem['word']=$word;
				$elem['key']=$key;
				$elem[')']=0;
				$elem['id_campaign']=0;
				$elem['present']=0;

				// on recerche si ce mot existe deja ou non
				$posi=false;

				$posi=array_search($word,$_SESSION['dims']['search']['listuniqueword']);

				if ($posi===false) {
					$posi=sizeof($_SESSION['dims']['search']['listuniqueword'])+1;
					$_SESSION['dims']['search']['listuniqueword'][$posi]=$word;
					$insert=true;
				}
				$elem['posword']=$posi;

				$_SESSION['dims']['search']['listselectedword'][]=$elem;
			}
		}
	}
	else {
		$elem['(']=0;
		$elem['op']="AND";
		$elem['word']=$word;
		$elem['key']=0;
		$elem[')']=0;
		$elem['id_campaign']=0;
		$elem['present']=0;

		// on recerche si ce mot existe deja ou non
		$posi=array_search($word,$_SESSION['dims']['search']['listuniqueword']);
		if ($posi===false) {
			$posi=sizeof($_SESSION['dims']['search']['listuniqueword'])+1;
			$_SESSION['dims']['search']['listuniqueword'][$posi]=$word;
			$insert=true;
		}
		$elem['posword']=$posi;

		$_SESSION['dims']['search']['listselectedword'][]=$elem;
	}

	return($insert);
}

function recursiveInterpretQuery($idcour,$idfin,&$tabelem) {

	while($idcour<$idfin) {
		$elemdeb=$tabelem[$idcour];

		if($elemdeb['(']>0) {
			// recherche de l'élement qui correspond
			$pouvrante=$elemdeb['('];
			$pfermante=0;
			$i=$idcour+1;

			while ($pouvrante!=$pfermante &&  $i<=$idfin && $i<sizeof($tabelem)) {
				$pouvrante+=$tabelem[$i]['('];
				$pfermante+=$tabelem[$i][')'];
				if ($pouvrante!=$pfermante) $i++;
			}

			if ($pouvrante==$pfermante) {
				//echo "deb :".$idcour." - fin :".$i."<br>";
				$_SESSION['dims']['search']['matrix'][$idcour]['corresp'][$i]=1;
				$_SESSION['dims']['search']['matrix'][$i]['link']=$idcour;
				$tabelem[$idcour]['(']--;
				$tabelem[$i][')']--;
				recursiveInterpretQuery($idcour,$i,$tabelem);
			}
		}
		$idcour++;
	}
}

function recursiveExecuteQuery($deb,$limit,&$result,$workspaceid) {
	//echo "Commencement sur ".($deb)." et ".$limit."<br><br>";
	//dims_print_r($_SESSION['dims']['search']['matrix']);
	$elem=array();
	$elemprec=array();
	for($i=$deb;$i<=$limit;$i++) {
		if (sizeof($elem)>0) $elemprec=$elem;
		$elem=$_SESSION['dims']['search']['matrix'][$i];

		// test si deja utilisé ou non
		if (!$elem['used']) {
			// test si elements de parenthèses à traiter ou non
			if (sizeof($elem['corresp'])>0) {
			$resultemp=array();
			// on va reboucler sur les éléments et renvoyer un tableau en résultat
			foreach ($elem['corresp'] as $indice=>$subcorresp) {
				//echo "appel sur ".($i)." et ".$indice."<br>";
				$resultemp= recursiveExecuteQuery($i+1,$indice,$result,$workspaceid);
				// on fusionne le résultat
				if (sizeof($result)==0) {
					$result=$resultemp;
				}
				else {
					if ($elem['op']=="AND") mergeResult($result,$resultemp,"intersec");
					else mergeResult($result,$resultemp,"merge");
				}
				//echo "calcul contenu sur ".($i)." et ".$indice."<br>";
				$_SESSION['dims']['search']['matrix'][$i]['used']=true;
			}

			}
			else {
			$resultemp=array();

			if ((isset($_SESSION['dims']['search']['cacheresult'][$elem['key']][$workspaceid])))
				$resultemp=$_SESSION['dims']['search']['cacheresult'][$elem['key']][$workspaceid];

			//echo "calcul standard sur ".$i." : ".$elem['key']." wkspace : ".$workspaceid." de taille ".sizeof($resulttemp);
			//ob_flush();
			//dims_print_r($resulttemp);ob_flush();
			// on fusionne le résultat
			if (sizeof($result)==0) $result=$resultemp;
			else {
				if (isset($elemprec['op']) && $elemprec['op']=="AND") mergeResult($result,$resultemp,"intersec");
				else mergeResult($result,$resultemp,"merge");
			}
			$_SESSION['dims']['search']['matrix'][$i]['used']=true;

			}
		}
	}
	return $result;
}

function mergeResult(&$result,$resultemp,$op)  {
	if ($op=="intersec") {

		foreach($resultemp as $idmod=>$data) {
			foreach($resultemp[$idmod] as $id_object=>$data2) {
				// on fusionne sur la base du résultat
				if (isset($result[$idmod][$id_object]))
					$result[$idmod][$id_object]=array_intersect_key($data2,$result[$idmod][$id_object]);
				//else
				//unset($result[$idmod][$id_object]);
			}
		}
	}
	else	{
		foreach($resultemp as $idmod=>$data) {
			foreach($resultemp[$idmod] as $id_object=>$data2) {
				// on fusionne sur la base du résultat
				if (isset($result[$idmod][$id_object]))
					$result[$idmod][$id_object]=$data2+$result[$idmod][$id_object];
				else
					$result[$idmod][$id_object]=$data2;

			}
		}
	}
}

function dims_getContent($moduleid,$idobject,$idrecord,$obj,$label) {
	$result="";
	$id_module_type=$_SESSION['dims']['modules'][$moduleid]['id_module_type'];
	$namefields=$_SESSION['dims']['index'][$id_module_type][$idobject]['fields'];

	$fields=array();

	$result="<table style=\"width:100%;border:0px;background:#FFFFFF;\" cellpadding=\"0\" cellspacing=\"0\">";
	$result.="<tr class=\"trtitle\"><td colspan=\"2\" align=\"center\">".str_replace("<OBJECT>","'".$label."'",$_DIMS['cste']['_DIMS_OBJECT_PROPERTIES'])."</td></tr>";
	// on boucle sur les propriétés à afficher
	$i=0;
	foreach($namefields as $nom) {
		if ($i%2==0) $i=1;
		else $i=2;

		if (substr($obj->fields[$nom],0,17)=="[dimscontentfile]") {
			$fileindex=realpath(substr($content,17));

			if (file_exists($fileindex)) {
					$content="";
					$total=filesize($fileindex);
					$fh = fopen($fileindex, "r");
					while (!feof($fh)) {
						$content .= fgets($fh);
					}
			}
		}
		if (strlen($obj->fields[$nom])<400) $content=$obj->fields[$nom];
		else {
			$content="<font style=\"font-weight:bold;\">".$_DIMS['cste']['_DIMS_OBJECT_RESUME']." :</font><br>".dims_strcut($obj->fields[$nom],400);
			$content.="<div id=\"detaildisplay$moduleid-$idrecord\" style=\"visibility:visible;display:block\"><a href=\"#\" onclick=\"dims_getelem('detailcontent$moduleid-$idrecord').style.visibility='visible';dims_getelem('detailcontent$moduleid-$idrecord').style.display='block';dims_getelem('detaildisplay$moduleid-$idrecord').style.visibility='hidden';dims_getelem('detaildisplay$moduleid-$idrecord').style.display='none';dims_getelem('detailhide$moduleid-$idrecord').style.visibility='visible';dims_getelem('detailhide$moduleid-$idrecord').style.display='block';\">".$_DIMS['cste']['_DIMS_OBJECT_DISPLAY']."</a></div>";
			$content.="<div id=\"detailhide$moduleid-$idrecord\"  style=\"visibility:hidden;display:none\"><a href=\"#\" onclick=\"dims_getelem('detailcontent$moduleid-$idrecord').style.visibility='hidden';dims_getelem('detailcontent$moduleid-$idrecord').style.display='none';dims_getelem('detaildisplay$moduleid-$idrecord').style.visibility='visible';dims_getelem('detaildisplay$moduleid-$idrecord').style.display='block';dims_getelem('detailhide$moduleid-$idrecord').style.visibility='hidden';dims_getelem('detailhide$moduleid-$idrecord').style.display='none';\">".$_DIMS['cste']['_DIMS_OBJECT_HIDE']."</a></div>";
			$content.="<div id=\"detailcontent$moduleid-$idrecord\" style=\"text-align:justify;padding:10px;visibility:hidden;display:none\"><font style=\"font-weight:bold;\">".$_DIMS['cste']['_DIMS_OBJECT_COMPLETECONTENT']." :</font><br>".$obj->fields[$nom]."</div>";

			// construction de la zone de detail en plus
		}
		$result.= "<tr class=\"trdetaill$i\"><td>".$nom."</td><td><p style=\"text-align:justify;padding:2px;\">".$content."</p></td></tr>";
	}
	$result.= "</table>";
	$result.= "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
		<input type=\"button\" onclick=\"dims_getelem('dims_popup2').style.visibility='hidden';dims_getelem('dims_popup2').style.display='none';\" value=\"Fermer\" class=\"flatbutton\"/></div>";
	return ($result);
}

function dims_getAllWorkspaces() {
	$db = dims::getInstance()->getDb();
	global $dims;
	// get domain from url
	//$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
	$http_host = $dims->getHttpHost();

	$select = "SELECT id_workspace as id,domain,dims_workspace_domain.access FROM dims_workspace_domain inner join dims_domain on dims_domain.id=dims_workspace_domain.id_domain and (dims_domain.domain like :domain or dims_domain.domain like '*')";
	$res=$db->query($select, array(':domain' => array('type' => PDO::PARAM_STR, 'value' => $http_host)));

	if ($db->numrows($res)>0) {
		$result="";
		while ($f=$db->fetchrow($res)) {
			if ($result!="") $result.=",";
			$result.=intval($f['id']);
		}
		return $result;
	}
	else return "0";
}

function calcul_block($tree,$list, $index,$blockwidth,$blockheight,$pasw,$pash) {
	$block = array();
	$block['id'] = $index;
	if (isset($list[$index]['label'])) $block['label'] = $list[$index]['label'];
	else $block['label'] ="";

	if (isset($list[$index]['code'])) $block['code'] = $list[$index]['code'];
	else $block['code'] ="";
	$block['x'] = 0;
	$block['y'] = 0;
	$block['w'] = 0;
	$block['h'] = 0;

	$block['childs'] = array();

	if (isset($tree[$index]) && sizeof($tree[$index]) > 0) {
		foreach ($tree[$index] as $k=>$v) {
			$block['childs'][] = calcul_block($tree,$list, $v,$blockwidth,$blockheight,$pasw,$pash);
		}
		$w=0;
		$h=0;
		// on recalcule la taille avec maintenant les enfants dans child
		foreach ($block['childs'] as $k=>$bl) {
			$w+=$bl['w'];
			$h=($h<$bl['h']) ? $bl['h']:$h;
		}

		$block['w'] =$w+(sizeof($block['childs'])-1)*$pasw;
		$block['h'] =$h+$pash+$blockheight;
	}
	else {
		$block['w'] = $blockwidth;
		$block['h'] = $blockheight;
	}

	return $block;
}

function verif_AccessWorkspaces($allws,$ws,$level,$idparent,$parents) {

}

function get_mapview($auto=false,$blockwidth=0) {
	if ($blockwidth==0) $blockwidth = 100;
	$blockheight = 14;
	$pasw=8;
	$pash=70;
	$tabcanvas=array();
	require_once DIMS_APP_PATH . '/modules/system/include/functions.php';

	$ws = system_getavailabledworkspaces();

	$blocks = calcul_block($ws['tree'],$ws['list'], 1,$blockwidth,$blockheight,$pasw,$pash);
	// begin graph
	echo "<div id=\"workspacearea\" style=\"margin:0 auto;width:".$blocks['w']."px;height:".$blocks['h']."px;position:relative;\">";
	displayBlock($blocks,0,0,$blockwidth,$blockheight,$pasw,$pash,$tabcanvas);
	echo "</div>";

	// construction du script de generation des canvas
	echo "<script language=\"javascript\">";
	echo 'setTimeout("execCanvas()", 400);';


	echo "window['execCanvas'] = function execCanvas() { var containerworkspace=document.getElementById('containerworkspace');";
	if (!$auto) {
		echo "if( window.innerWidth) {
				containerworkspace.style.width=(((window.innerWidth *4)/ 5)-30)+\"px\";
			}
			else {
				containerworkspace.style.width=(((document.body.offsetWidth *4)/ 5)-30)+\"px\";

			}
			containerworkspace.style.height=(".$blocks['h']."+30)+\"px\";
			";
	}

	foreach($tabcanvas as $id=>$canvas) {
		echo "addElement(\"workspacearea\",\"canvas\",\"canvas".$id,"\",\"\",".$canvas['xdeb'].",".($canvas['ydeb']+2).",".$canvas['xfin'].",".$canvas['yfin'].");";
	}

	echo "}";
	echo "</script>";


}

function displayBlock($block,$x,$y,$blockwidth,$blockheight,$pasw,$pash,&$tabcanvas) {
	// calcul du positionnement de l'�l�ment courant
	// construction du div portant l'�l�ment racine
	global $scriptenv;
	global $_DIMS;
	$xcour=$x+($block['w']/2)-($blockwidth/2);
	$ycour=$y;

	if($block['code']!="") $code=$block['label']." / ".$block['code'];
	else $code=$block['label'];

	if ($block['id']==1) {
		echo "<div id=\"bl".$block['id']."\" class=\"mapworkspace\" style=\"background-color:#dddddd;border:1px dotted;width:".
		$blockwidth."px;height:".$blockheight.";position:absolute;top:".$ycour."px;left:".$xcour."px;text-align:center;\">".$_DIMS['cste']['_DIMS_ENABLED_WORKSPACE'] ."</div>";
	}
	else  {
		echo "<div id=\"bl".$block['id']."\" class=\"mapworkspace\" style=\"z-index:10;border:1px dotted;width:".
		$blockwidth."px;height:".$blockheight.";position:absolute;top:".$ycour."px;left:".$xcour."px;text-align:center;\"><a href=\"$scriptenv?dims_workspaceid=".$block['id']."&dims_mainmenu=0&dims_desktop=block&dims_action=public\" title=\"".$code."\" alt=\"".$code."\">".dims_strcut($block['label'],25) ."</a></div>";
	}

	$xdeb=$x+($block['w']/2);
	$ydeb=$y+$blockheight;

	foreach ($block['childs'] as $c=>$bl) {
		// on garde les deux points en m�moire
		$elemtabcanvas=array();
		$elemtabcanvas['xdeb']=$xdeb;
		$elemtabcanvas['ydeb']=$ydeb;
		$elemtabcanvas['xfin']=$x+($bl['w']/2);
		$elemtabcanvas['yfin']=$y+$pash;
		$tabcanvas[]=$elemtabcanvas;

		displayBlock($bl,$x,$y+$pash,$blockwidth,$blockheight,$pasw,$pash,$tabcanvas);
		$x+=$bl['w']+$pasw;
	}
}



function calcul_servicesBlock($tree, $list, $index, $blockwidth, $blockheight, $pasw, $pash) {
	$block = array();
	$block['id'] = $index;
	$block['label'] = $list[$index]['label'];
	$block['x'] = 0;
	$block['y'] = 0;
	$block['w'] = 0;
	$block['h'] = 0;

	$block['childs'] = array();

	if (sizeof($tree[$index]) > 0) {
		foreach ($tree[$index] as $k=>$v) {
			$block['childs'][] = calcul_servicesBlock($tree,$list, $v,$blockwidth,$blockheight,$pasw,$pash);
		}
		$w=0;
		$h=0;
		// on recalcule la taille avec maintenant les enfants dans child
		foreach ($block['childs'] as $k=>$bl) {
			$w+=$bl['w'];
			$h=($h<$bl['h']) ? $bl['h']:$h;
		}

		$block['w'] =$w+(sizeof($block['childs'])-1)*$pasw;
		$block['h'] =$h+$pash+$blockheight;
	}
	else {
		$block['w'] = $blockwidth;
		$block['h'] = $blockheight;
	}

	return $block;
}

function get_servicesMapview($id_ent) {
	$blockwidth = 140;
	$blockheight = 14;
	$pasw=8;
	$pash=70;
	$tabcanvas=array();
	require_once DIMS_APP_PATH . '/modules/system/include/functions.php';

	$ws = system_getServices($id_ent);
	$blocks = calcul_servicesBlock($ws['tree'],$ws['list'], 1,$blockwidth,$blockheight,$pasw,$pash);
	// begin graph
	echo "<div id=\"workspacearea\" style=\"margin:0 auto;width:".$blocks['w']."px;height:".$blocks['h']."px;position:relative;background:#FFFFFF\">";
	displayServicesBlock($blocks,0,0,$blockwidth,$blockheight,$pasw,$pash,$tabcanvas);
	echo "</div>";

	// construction du script de g�n�ration des canvas
	echo "<script language=\"javascript\">";
	echo "var containerServices=document.getElementById('containerServices');";
	echo "if( window.innerWidth) {
			containerServices.style.width=(((window.innerWidth *4)/ 6)-30)+\"px\";
		}
		else {
			containerServices.style.width=(((document.body.offsetWidth *4)/ 6)-30)+\"px\";

		}
		containerServices.style.height=(".$blocks['h']."+30)+\"px\";
		";

	foreach($tabcanvas as $id=>$canvas) {
		echo "addElement(\"workspacearea\",\"canvas\",\"canvas".$id,"\",\"\",".$canvas['xdeb'].",".($canvas['ydeb']+2).",".$canvas['xfin'].",".$canvas['yfin'].");";
	}
	echo "</script>";


}

function displayServicesBlock($block,$x,$y,$blockwidth,$blockheight,$pasw,$pash,&$tabcanvas) {
	// calcul du positionnement de l'�l�ment courant
	// construction du div portant l'�l�ment racine
	$xcour=$x+($block['w']/2)-($blockwidth/2);
	$ycour=$y;

	echo "<div id=\"bl".$block['id']."\" class=\"mapworkspace\" style=\"border:1px dotted;width:".
		$blockwidth."px;height:".$blockheight.";position:absolute;top:".$ycour."px;left:".$xcour."px;text-align:center;\">
			<a href=\"javascript: void(0);\" onclick=\"javascript: dims_xmlhttprequest_todiv('admin.php','action=display_serviceContacts&id_service={$block['id']}','','service_contacts');\">".dims_strcut(strtolower($block['label']),20) ."</a>
			<a href=\"javascript: void(0);\" onclick=\"javascript: displayNewServices(event, {$block['id']});\"><img src=\"./common/img/icon_add.gif\" alt=\"Ajouter un service\" border=\"0\" /></a>
			<a href=\"javascript: void(0);\" onclick=\"javascript: dims_confirmlink('$scriptenv?action=drop_service&id_service={$block['id']}', 'Etes-vous s&ucirc;r(e) ?');\"><img src=\"./common/img/del.png\" alt=\"Supprimer le service\" border=\"0\" /></a>
		</div>";

	$xdeb=$x+($block['w']/2);
	$ydeb=$y+$blockheight;

	foreach ($block['childs'] as $c=>$bl) {
		// on garde les deux points en m�moire
		$elemtabcanvas=array();
		$elemtabcanvas['xdeb']=$xdeb;
		$elemtabcanvas['ydeb']=$ydeb;
		$elemtabcanvas['xfin']=$x+($bl['w']/2);
		$elemtabcanvas['yfin']=$y+$pash;
		$tabcanvas[]=$elemtabcanvas;

		displayServicesBlock($bl,$x,$y+$pash,$blockwidth,$blockheight,$pasw,$pash,$tabcanvas);
		$x+=$bl['w']+$pasw;
	}
}



function dims_initOptions($id_module) {
	unset($_SESSION['dims']['options'][$id_module]);
}

function dims_createOptions($id_workspace,$id_module,$id_object,$id_record,$label,$id_userfrom=0,$option=0) {
	$options=array();

	$options['id_workspace']=$id_workspace;
	$options['id_module']=$id_module;
	$options['id_object']=$id_object;
	$options['id_record']=$id_record;
	$options['id_userfrom']=$id_userfrom;
	$options['label']=$label;
	$options['option']=0;
	/*
	// favoris
	if (isset($favorites['access'][$id_workspace][$id_module][$id_object][$id_record]) && $favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['type']>0) {
		$idfav=$favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['id'];
		$value=$favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['type'];
		$options['favorite']=$value;
		// $options[]= array("","refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$id_module.",".$id_workspace.",".$id_object.",".$id_record.",$valsuiv,$id_user_from,'".str_replace("'","\'",$label)."');\"
	}
	*/
	// attribution en session de l'object courant ajout�
	unset($_SESSION['dims']['options'][$id_module][$id_object][$id_record]);
	$_SESSION['dims']['options'][$id_module][$id_object][$id_record]=$options;
}

function dims_addOptions($id_workspace,$id_module,$id_object,$id_record,$href="",$link="",$label,$type="",$icon="") {
	if(isset($_SESSION['dims']['options'][$id_module][$id_object][$id_record])) {
		$elem=array();
		$elem['href']=$href;
		$elem['link']=$link;
		$elem['label']=$label;
		$elem['type']=$type; // add, modify, sup, comment
		$elem['icon']=$icon;
		$_SESSION['dims']['options'][$id_module][$id_object][$id_record]['elements'][]=$elem;
	}
}

function dims_displayOptions($_DIMS,$id_workspace,$id_module,$id_object,$id_record,$decal=0) {
	//echo "<a href=\"#\" onclick=\"javascript:displayOptions(event,".$id_workspace.",".$id_module.",".$id_object.",".$id_record.")\")>".$_DIMS['cste']['_DIMS_ACTIONS']."</a>";
	return dims_create_button($_DIMS['cste']['_DIMS_ACTIONS'],'',"javascript:displayOptions(event,".$id_workspace.",".$id_module.",".$id_object.",".$id_record.",".$decal.");",'','','','',true);
}

function dims_getOptions($id_workspace,$id_module,$id_object,$id_record,$fav=true) {
	global $_DIMS;
	if (isset($_SESSION['dims']['options'][$id_module][$id_object][$id_record])) {
		$options=$_SESSION['dims']['options'][$id_module][$id_object][$id_record];

		echo "<table style=\"width:100%\">";
		// traitement des options
		if (sizeof($options['elements'])>0) {
			foreach ($options['elements'] as $elem) {
				switch ($elem['type']) {
					case 'modify':
						if ($elem['href']!='') $link="<a href=\"".$elem['href']."\">";
						elseif ($elem['link']!='') $link="<a href=\"#\" onclick=\"".$elem['link']."\">";

						echo "<tr class=\"\trsep\"><td style=\"width:16px;\">$link<img border=\"0\" src=\"./common/img/edit.gif\"></a></td>";
						echo "<td>".$link.$_DIMS['cste']['_MODIFY']."</a></td></tr>";
						break;

					case 'add':
						break;

					case 'delete':
						if ($elem['href']!='') $link="<a href=\"".$elem['href']."\">";
						elseif ($elem['link']!='') $link="<a href=\"#\" onclick=\"".$elem['link']."\">";

						echo "<tr class=\"\trsep\"><td style=\"width:16px;\">$link<img border=\"0\" src=\"./common/img/del.png\"></a></td>";
						echo "<td>$link".$_DIMS['cste']['_DELETE']."</a></td></tr>";
						break;

					default :
						if ($elem['href']!='') $link="<a href=\"".$elem['href']."\">";
						elseif ($elem['link']!='') $link="<a href=\"javascript:void(0);\" onclick=\"".$elem['link']."\">";

						echo "<tr class=\"\trsep\"><td style=\"width:16px;\">$link<img border=\"0\" src=\"".$elem['icon']."\"></a></td>";
						echo "<td>$link".$elem['label']."</a></td></tr>";
						break;
				}
			}
		}

		$_SESSION['dims']['current_object']['cmd']=array();

		if ($fav) {
			// construction des favoris
			require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
			$dims_user = new user();
			$dims_user->open($_SESSION['dims']['userid']);
			$favorites=$dims_user->getFavorites($id_module);
			$elem=array();
			$workspaceid=$id_workspace;
			$moduleid=$id_module;
			$objectid=$id_object;
			$recordid=$id_record;

			if (isset($favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]) && $favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type']>0) {
				$idfav=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['id'];
				$value=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type'];
			}
			else {
				$idfav=0;
				$value=0;
			}

			$refresh=0;

			// on traite le en veille
			if ($value!=2) {
				// add favor
				$elem['name']=$_DIMS['cste']['_ADDTO_FAVORITES'];
				$elem['src']="./common/img/fav1.png";
				$elem['link']= "";
				$elem['width']= "width:140px";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",2,".$_SESSION['dims']['userid'].",0,".$refresh.");";
				$elem['script'].="displayOptionsRefresh(".$workspaceid.",".$moduleid.",".$objectid.",".$recordid.");";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;

				if ($value==0) {
					// add wait
					$elem['name']=$_DIMS['cste']['_DIMS_ADDTO_SURVEY'];
					$elem['src']="./common/img/view.png";
					$elem['link']= "";
					$elem['width']= "width:140px";
					$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",1,".$_SESSION['dims']['userid'].",0,".$refresh.");";
					$elem['script'].="displayOptionsRefresh(".$workspaceid.",".$moduleid.",".$objectid.",".$recordid.");";
					$_SESSION['dims']['current_object']['cmd'][]=$elem;
				}
				else {
					// remove from wait
					$elem['name']=$_DIMS['cste']['_DIMS_REMOVEFROM_SURVEY'];
					$elem['src']="./common/img/delete.png";
					$elem['link']= "";
					$elem['width']= "width:160px";
					$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$_SESSION['dims']['userid'].",0,".$refresh.");";
					$elem['script'].="displayOptionsRefresh(".$workspaceid.",".$moduleid.",".$objectid.",".$recordid.");";
					$_SESSION['dims']['current_object']['cmd'][]=$elem;
				}
			}
			else {
				// on peut annuler le favoris
				$elem['name']=$_DIMS['cste']['_REMOVEFROM_FAVORITES'];
				$elem['src']="./common/img/delete.png";
				$elem['link']= "";
				$elem['width']= "width:160px";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$_SESSION['dims']['userid'].",0,".$refresh.");";
				$elem['script'].="displayOptionsRefresh(".$workspaceid.",".$moduleid.",".$objectid.",".$recordid.");";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;
			}
		}

		if (isset($_SESSION['dims']['current_object']['cmd'])) {
			foreach($_SESSION['dims']['current_object']['cmd'] as $elem) {
				$link="<a href=\"#\" onclick=\"".$elem['script']."\" alt=\"".$elem['name']."\">";
				echo "<tr><td style=\"width:16px;\">".$link."<img border=\"0\" src=\"".$elem['src']."\"></a></td>";
				echo "<td>$link".$elem['name']."</a></td></tr>";
			};
		}
		echo "</table>";
	}
}

function dims_createTplColumns($array_modules,&$tpl_columns,&$sumvalidate) {

	// GET CURRENT USER DISPLAY
	require_once(DIMS_APP_PATH . '/modules/system/class_workspace_user.php');

	$wuser= new workspace_user();
	$wuser->open($_SESSION['dims']['workspaceid'],$_SESSION['dims']['userid']);

	// collecte des informations relatives au param�trage des modules utilis�s
	$tabcolumns=$wuser->getConfigBlocks($array_modules,$_SESSION['dims']['moduleid']);

	unset($_SESSION['dims']['search']['listmodules']);
	$_SESSION['dims']['search']['listmodules']=array();

	for ($idcolumn=1;$idcolumn<=$_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SITE]['site_blockcolumn_number'];$idcolumn++) {

		$tpl_blocks=array();
		// retrieve current modules for this column
		if (isset($tabcolumns[($idcolumn-1)])) {
			foreach($tabcolumns[($idcolumn-1)] as $idmod => $mod) {
										// Add validate state for current module
				if (!isset($mod['date_lastvalidate'])) $mod['date_lastvalidate']=0;

				$sumvalidate+=$mod['date_lastvalidate'];
				$urlactivezoom="";
				// build title
				if (isset($mod['admin']) && $mod['admin']) {
					$refadmin="<a href=\"".dims_urlencode("{$scriptenv}?dims_moduleid=$idmod&dims_action=admin")."\"><img src=\"./common/modules/system/img/tools.gif\"></a>";
				}
				else
					$refadmin="";

				$idmodcour=($idmod =="admin") ? dims_const::_DIMS_MODULE_SYSTEM : $idmod;

				if ($idmodcour==dims_const::_DIMS_MODULE_SYSTEM ) {
					if ($mod['date_lastvalidate']==0)
						$urlvalidate="<img src=\"./common/img/check.png\"/></a>";
					else
						$urlvalidate="<a href=\"#\" title=\"Valider ces nouvelles informations comme lues\" onclick=\"updateValidate(".$idmodcour.");\"><img src=\"./common/img/checkdo.png\"/></a>";

					$urladmin="";
					$urlpublic="";
					$urlzoom="";
				}
				else {

					if ($mod['date_lastvalidate']==0)
						$urlvalidate="<img src=\"./common/img/check.png\"/></a>";
					else
						$urlvalidate="<a href=\"#\" title=\"Valider ces nouvelles informations comme lues\" onclick=\"updateValidate(".$idmodcour.");\"><img src=\"./common/img/checkdo.png\"/></a>";

					// test pour l'administration du module minotie
					if (dims_isactionallowed(-1,$_SESSION['dims']['workspaceid'],$idmodcour) && file_exists(DIMS_APP_PATH . "/modules/".$mod['type']."/admin.php")) {
						$urladmin="<a href=\"#\" title=\"Acces en administration\" onclick=\"document.location.href='".dims_urlencode("admin.php?dims_moduleid=".$idmodcour."&dims_desktop=block&dims_action=admin")."'\"><img src=\"./common/img/configure.png\"/></a>";
					}
					else
						$urladmin="";


					$urlpublic="<a href=\"#\" title=\"Acces public\" onclick=\"document.location.href='".dims_urlencode("admin.php?dims_moduleid=".$idmodcour."&dims_desktop=block&dims_action=public")."'\"><img src=\"./common/img/public.png\"/></a>";
					$urlzoom="<a href=\"#\" title=\"Zoom\" onclick=\"zoomBlock(".$idmodcour.");\"><img src=\"./common/img/zoom.png\"/></a>";
					$urlactivezoom="<a href=\"#\" onclick=\"viewActiveZoom(".$idmodcour.");\"><img src=\"./common/img/zoom.png\"/></a>";
				}

				if ($mod['state']==1)
						$urlstate="<a href=\"#\" title=\"R�duire/agrandir\" onclick=\"updateState(".$idmodcour.",1);\"><img id=\"bkimg".$idmodcour."\" src=\"./common/img/minimize.gif\"/></a>";
					else
						$urlstate="<a href=\"#\" title=\"R�duire/agrandir\" onclick=\"updateState(".$idmodcour.",0);\"><img id=\"bkimg".$idmodcour."\" src=\"./common/img/maximize.gif\"/></a>";

				$urlmove="<img border=\"0\" src=\"./common/img/move.png\"/>";
				if (isset($array_modules[$_SESSION['dims']['modules'][$idmod]['moduletype']][$idmod]['visible']) && $array_modules[$_SESSION['dims']['modules'][$idmod]['moduletype']][$idmod]['visible']) $statevisible="display:block;visibility:visible;";
				else $statevisible="display:none;visibility:hidden;";

				if (isset($mod['id_workspace']) && $mod['id_workspace']!=$_SESSION['dims']['workspaceid']) $iconmod="puceshare.png";
				else $iconmod="puce.png";

				if (!isset($mod['icontoolbar'])) $mod['icontoolbar']="";
				if (!isset($mod['icon'])) $mod['icon']="";
				if (!isset($mod['url'])) $mod['url']="";

				if (!$_SESSION['dims']['browser']['pda'] || ($_SESSION['dims']['browser']['pda'] && $mod['blockpda'])) {
					$tpl_blocks[]=array(
						'ID' =>($idmod =="admin") ? dims_const::_DIMS_MODULE_SYSTEM : $idmod ,
						'TYPE' =>$mod['type'],
						'ID_MODULE' =>$idmod,
						'SHORT_TITLE' =>substr($mod['title'],0,25),
						'TITLE' =>$mod['title'],
						'URLPDA' =>dims_urlencode("admin.php?dims_moduleid=".$idmodcour."&moduleid=".$idmodcour."&dims_desktop=block&dims_action=public&op=viewpda"),
						'SELECTED' => ($idmod == $_SESSION['dims']['moduleid']) ? 'selected' : '',
						'ICONMOD' => $iconmod,
						'STATE' => $mod['state'],
						'STATEVISIBLE' => $statevisible,
						'URLVALIDATE' => $urlvalidate,
						'URLSTATE' => $urlstate,
						'URLADMIN' => $urladmin,
						'URLPUBLIC' => $urlpublic,
						'URLZOOM' => $urlzoom,
						'MOVE'=>$urlmove,
						'ICON' => $mod['icon'],
						'ICONTOOLBAR' => $mod['icontoolbar'],
						'URL' => dims_urlencode($mod['url']),
						'DESCRIPTION' => '',
						'CONTENT' => (isset($mod['content'])) ? $mod['content'] : '',
						'CLASS' => ($idmod == $_SESSION['dims']['moduleid']) ? 'sel' : 'notsel'
					);
				}
			} // end foreach

			// define if must activate or not
			if ($sumvalidate==0) { // not activated
				$urlallvalidate="<div style=\"padding: 2px 0px; height: 16px; display: block; float: left;\"><a href=\"#\" onclick=\"updateAllValidate();window.location.reload(true);\">".$_DIMS['cste']['_DIMS_ACTIVATE_CHECK']."</div><div><img src=\"./common/img/check.png\"/></div>";
			}
			else {
				$urlallvalidate="<div style=\"padding: 2px 0px; height: 16px; display: block; float: left;\"><a href=\"#\" onclick=\"updateAllValidate();window.location.reload(true);\">".$_DIMS['cste']['_DIMS_VALIDATE_CHECK']."</a></div><div><img src=\"./common/img/checkdo.png\"/></div>";
			}
		}

		$tpl_columns[]=array(
			'ID'		=> $idcolumn,
			'TITLE'		=> 'Espace '.$idcolumn,
			'CONTENT'	=> '',
			'blocks'	=> $tpl_blocks);
	}
}

function dims_getBlocks(&$array_modules,$scriptenv,$_DIMS) {
	if ($_SESSION["dims"]["connected"] && $_SESSION['dims']['workspaceid']>0) {

		require_once DIMS_APP_PATH . "/include/class_block.php";
		// left menu
		// admin menu always on the left menu
		//if ($_SESSION['dims']['workspaces'][$_SESSION['dims']['grouptabid']]['system'])

		if (dims_ismanager() && $_SESSION['dims']['desktop']=="portal") {
			if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER) {
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['title'] = $_DIMS['cste']['_GENERAL_ADMINISTRATION'];
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['icon'] = _DIMS_DESK_ICON_ADMIN_SYSTEM;
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['icontoolbar'] = _DIMS_DESKTOOLBAR_ICON_ADMIN_SYSTEM;
				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
					$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['url'] = "$scriptenv?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM.'&dims_action=admin&system_level=work';
				}
				else {
					$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['url'] = "$scriptenv?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM.'&dims_action=admin&system_level='.dims_const::_SYSTEM_WORKSPACES;
				}

				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['description'] = 'Installation des Modules, Param�trage, Monitoring';
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['admin'] = true;

				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
					$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['menu'][] = array(	'label' => $_DIMS['cste']['_DIMS_LABEL_SYSTEM'],
													'url' => dims_urlencode("{$scriptenv}?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_action=admin&system_level=work")
													);
				}

				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['menu'][] = array(	'label' => $_DIMS['cste']['_DIMS_ADMIN_WORKSPACES'],
															'url' => dims_urlencode("{$scriptenv}?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_action=admin&system_level=".dims_const::_SYSTEM_WORKSPACES)
															);

				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['state'] = 0;
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['visible'] = 1;
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['type'] = "system";
				$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['id_workspace']=$_SESSION['dims']['workspaceid'];
			}
		}
		else {
			$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['title'] = $_DIMS['cste']['_WORKSPACE']." ".$_SESSION['dims']['currentworkspace']['label'];
			$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['type'] = "system";
			$array_modules['system'][dims_const::_DIMS_MODULE_SYSTEM]['id_workspace']=$_SESSION['dims']['workspaceid'];
		}

		// Search displayable modules for the current group & menu (left/right)
		if (isset($_SESSION['dims']['currentworkspace']['modules'])) {
			$modules = $_SESSION['dims']['currentworkspace']['modules'];
			$worksp=new workspace();
			$worksp->open($_SESSION['dims']['workspaceid']);
			$modulevisibility=$worksp->getmodulesVisibility();

			foreach($modules as $key => $struct) {
				$menu_moduleid=$struct['instanceid'];
				// TEST IF SHOWABLE BLOCK

				if (isset($_SESSION["dims"]["modules"][$menu_moduleid]['showblock'])) {

					if ($_SESSION['dims']['modules'][$menu_moduleid]['showblock']
						&&	$_SESSION['dims']['modules'][$menu_moduleid]['active']) {
						$modtype = $_SESSION['dims']['modules'][$menu_moduleid]['moduletype'];

					}
				}
			}
		}
	}
}

function dims_getLastModify($date) {
	$annee = substr($date, 0, 4); // on r�cup�re le jour
	$mois = substr($date, 4, 2); // puis le mois
	$jour = substr($date, 6, 2);

	$timestamp = mktime(0, 0, 0, $mois, $jour, $annee);
	$maintenant=time();

	$ecart_secondes = $maintenant - $timestamp;
	$ecart=floor($ecart_secondes / (60*60*24));

	if ($ecart==0) $result =$_DIMS['cste']['_DIMS_LABEL_DAY'];
	elseif ($ecart==1) $result =$_DIMS['cste']['_DIMS_LABEL_LASTDAY'];
	else {
		$result =$_DIMS['cste']['_DIMS_LABEL_THEREIS']." ".$ecart." ".$_DIMS['cste']['_DIMS_LABEL_DAYS']." ";
	}

	return	$result;
}

function getSearchReplace($content) {
	$structfrom=array();
	$structto=array();
	$c=0;
	$limit=-1;
	$cpte=0;
	foreach($_SESSION['dims']['search']['listselectedword'] as $elem) {
		// remplacement standard
		$structfrom[$c]=$elem['word'];
		$structto[$c++]="<font style=\"font-weight:bold;background-color:#ffffee;\">".$elem['word']."</font>";

		$content=preg_replace("/(>[^<]*?)(".$elem['word'].")([^>]*?<)/si","\\1<span style=\"font-weight:bold;background-color:#ffffee;\">\\2</span>\\3",$content,$limit,$cpte);
		// remplacement 1er majuscule
		$structfrom[$c]=ucfirst($elem['word']);
		$structto[$c++]="<font style=\"font-weight:bold;background-color:#ffffee;\">".ucfirst($elem['word'])."</font>";

		$content=preg_replace("/(>[^<]*?)(".ucfirst($elem['word']).")([^>]*?<)/si","\\1<font style=\"font-weight:bold;background-color:#ffffee;\">\\2</font>\\3",$content,$limit,$cpte);
		// majuscule
		$structfrom[$c]=strtoupper($elem['word']);
		$structto[$c++]="<font style=\"font-weight:bold;background-color:#ffffee;\">".strtoupper($elem['word'])."</font>";
		$content=preg_replace("/(>[^<]*?)(".strtoupper($elem['word']).")([^>]*?<)/si","\\1<span style=\"font-weight:bold;background-color:#ffffee;\">\\2</span>\\3",$content,$limit,$cpte);
	}

	if ($cpte==0) {// pas de code html
		//	on remplace dans content
		foreach($structfrom as $c=>$elem) {
			$content=dims_str_replace_once($elem,$structto[$c],$content);
		}
	}
	return $content;
}

function dims_getTags($dims,$id_module,$id_object,$id_record) {
	$db = $dims->getDb();

	$sql= "SELECT		t.*,ti.id AS idtagindex
			FROM		dims_tag AS t
			INNER JOIN	dims_tag_index AS ti
			ON			t.id = ti.id_tag
			AND			ti.id_module=:idmodule
			AND			ti.id_object=:idobject
			AND			ti.id_record=:idworkspace
			AND			(t.private=0 AND ti.id_workspace=:idworkspace) OR (t.private=1 AND ti.id_user=:iduser)";

	$params = array(
		':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
		':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
		':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
	);


	$res=$db->query($sql, $params);
	$result=array();
	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			$result[$f['id']]=$f;
		}
		return $result;
	} else {
		return false;
	}
}

function dims_getTagsTemp() {
	if (isset($_SESSION['dims']['tag_temp'])) return $_SESSION['dims']['tag_temp'];
	else return false;
}

function dims_createAddTagLink($id_module,$id_object,$id_record) {
	global $_DIMS;
	$_SESSION['dims']['uploadfile']=array();
	$_SESSION['dims']['uploadfile']['id_module']=$id_module;
	$_SESSION['dims']['uploadfile']['id_object']=$id_object;
	$_SESSION['dims']['uploadfile']['id_record']=$id_record;
	$_SESSION['dims']['uploadfile']['url']=$_SERVER['SCRIPT_URI']."?".$_SERVER['QUERY_STRING'];
	echo "<a href=\"javascript:void(0);\" onclick=\"displayAddTags(event,600);\">".$_DIMS['cste']['_DIMS_LABEL_NEWTAG']."&nbsp;<img src=\"./common/img/add.gif\" alt=\"\"></a>";
}

function dims_getBlockTag($dims,$_DIMS,$moduleid,$objectid,$recordid) {
	//supprime liste temp
		$db = $dims->getDb();
		$result= "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:2px;margin-bottom:2px;\"><tbody>
		<tr>
				<td><input type=\"text\" style=\"width:120px;\" id=\"searchtag\" onkeyup=\"javascript:searchTag(".$moduleid.",".$objectid.",".$recordid.");\"></td>
		<tr>
				<td>
						<div id=\"blockresulttags\" style=\"width:100%;\">&nbsp;</div>
				</td>
		</tr>
		<tr><td>";
		// collecte des fichiers deja ins�r�s
		$lsttags=dims_getTags($dims,$moduleid,$objectid,$recordid);
		$lsttagsTemp=dims_getTagsTemp($dims);

		if (!empty($lsttags)) {
				foreach ($lsttags as $idtag=>$t) {
									if (isset($_DIMS['cste'][$t['tag']])) {
											$t['tag'] = $_DIMS['cste'][$t['tag']];
									}
									$result.= "<a href=\"javascript:void(0);\" onclick=\"javascript:removeTagObject(".$t['idtagindex'].",".$moduleid.",".$objectid.",".$recordid.",'".addslashes($_DIMS['cste']['_DIMS_CONFIRM'])."');\">".$t['tag']."<img src=\"./common/img/delete.png\" alt=\"\"></a>&nbsp;&nbsp;";
				}
		}
		elseif (!empty($lsttagsTemp)) {
				foreach ($lsttagsTemp as $idtag=>$t) {
									if (isset($_DIMS['cste'][$t['tag']])) {
											$t['tag'] = $_DIMS['cste'][$t['tag']];
									}
									$result.= "<a href=\"javascript:void(0);\" onclick=\"javascript:removeTagObjectTemp(".$t['id'].");\">".$t['tag']."<img src=\"./common/img/delete.png\" alt=\"\"></a>&nbsp;&nbsp;";
				}
		}
		else {
				$result.= $_DIMS['cste']['_DIMS_NO_TAGS_SEARCH'];
		}
		$result.= "</table>";
		return $result;
}

function dimsSearch() {

}

function dims_Excel($namefile,$headers,$datas) {
	// remplacement eventuel dans le nom
	$namefile=str_replace(".xls","",$namefile);

	$dataswriter = " <?xml version='1.0' encoding='utf-8' ?>
	<?mso-application progid='Excel.Sheet' ?>
	<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office-excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'>
	<Worksheet ss:Name='Emp x Depto'>
	<Table>
	<Row>";

	// headers
	foreach ($headers as $i => $head){
		$dataswriter .= "<Cell><Data ss:Type='String'>".utf8_encode($head)."</Data></Cell>";
	}
	$dataswriter .= "</Row>";

	//display data
	foreach ($datas as $data) {
		$dataswriter .= "<Row>";
		foreach($data as $elem){
			$dataswriter .= "<Cell><data ss:Type='String'>".utf8_encode($elem)."</Data></Cell>";
		}
		$dataswriter .= "</Row>";
	}
	$dataswriter .=  "</Table></Worksheet></Workbook>";

	header("Content-type: aplication/octet-stream");
	header("Content-Type: application/ms-excel");
	header("Content-Disposition: attachment; filename=".$namefile.".xls;");
	header("Pragma: no-cache");
	header("Expires: 0");

	//print all xml+data
	echo $dataswriter;
	//while (@ob_end_flush());
	die();
}


/**
 * Save Excel to CSV files
 * may want to clean up the worksheet title used for filename???
 *
 * @param	string		$xlsfile (full file path/name)
 * @param	boolean		$precalculate
 * @param	array		$use_states (array of states to save to file)
 * @throws	Exception
 */
function xls_to_csv_files($xlsfile,$precalculate=true,$use_states=array('visible')){
	require_once DIMS_APP_PATH . '/lib/PHPExcel/PHPExcel.php';
	require_once DIMS_APP_PATH.'/lib/PHPExcel/PHPExcel/IOFactory.php';
	$excel = PHPExcel_IOFactory::load($xlsfile);
	$writer = PHPExcel_IOFactory::createWriter($excel, 'CSV');
	$writer->setDelimiter(";");
	$writer->setEnclosure("\"");

	$output=str_replace(array('.xlsx','.xls'), '.csv',$xlsfile);
	$writer->save($output);

	return $output;
}

function dims_stacktrace(){
	$traces = debug_backtrace();
	for($i = count($traces) - 1 ; $i > 0 ; $i--){
		echo 'in '.$traces[$i]['file']. ' line '.$traces[$i]['line'] .' with '.$traces[$i]['function'].'<br/>';
	}
}
