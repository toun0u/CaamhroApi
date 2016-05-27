<?php
define ('_DOC_ACTION_ADDFOLDER',	1);
define ('_DOC_ACTION_ADDFILE',		2);
define ('_DOC_ACTION_MODIFYFOLDER',	3);
define ('_DOC_ACTION_MODIFYFILE',	4);
define ('_DOC_ACTION_DELETEFOLDER',	5);
define ('_DOC_ACTION_DELETEFILE',	6);
define ('_DOC_ACTION_WORKFLOW_MANAGE',	7);

define('_DOC_OBJECT_FOLDER',		1);
define('_DOC_OBJECT_FILE',			2);
define('_DOC_OBJECT_FILEDRAFT',		3);

global $foldertypes;
global $_DIMS;
$foldertypes = array ('private' => $_DIMS['cste']['_PERSO'], 'shared' => $_DIMS['cste']['_DIMS_LABEL_ISSHARED'], 'public' => $_DIMS['cste']['_DIMS_LABEL_PUBLIC'], 'gallery' => $_DIMS['cste']['_DIMS_EVENT_LABEL_GALLERY'], 'network' => $_DIMS['cste']['_DIMS_LABEL_RESEAU']);

function doc_getpath($id_module = -1, $createpath = false)
{
	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	$path = _DIMS_PATHDATA._DIMS_SEP."doc-{$id_module}";

	if ($createpath) {
		// test for existing _DIMS_PATHDATA path
		if (!is_dir(_DIMS_PATHDATA)) mkdir(_DIMS_PATHDATA);

		if ($path != '' && !is_dir($path)) mkdir($path);
	}

	return($path);
}

function doc_countelements($id_folder)
{
	$db = dims::getInstance()->getDb();

	$c = 0;

	$res=$db->query("SELECT count(id) as c FROM dims_mod_doc_folder WHERE id_folder = :idfolder", array(':idfolder' => $id_folder) );
	if ($row = $db->fetchrow($res)) $c += $row['c'];

	$res=$db->query("SELECT count(id) as c FROM dims_mod_doc_file WHERE id_folder = :idfolder", array(':idfolder' => $id_folder) );
	if ($row = $db->fetchrow($res)) $c += $row['c'];

	return($c);
}

function createRecursiveFiles($src,$dest,$mask,$objfolder) {
	$ok = true;
	$folder=opendir($src);

	//if (!file_exists($dest)) mkdir($dest, $mask);

	while ($file = readdir($folder)) {
		$l = array('.', '..');



		if (!in_array( $file, $l))
		{
			if (is_dir($src.$file))
			{
				// on doit recr��er le dosier contenant les �ventuels fichiers en plus
				$docfolder = new docfolder();
				$docfolder->fields['foldertype']=$objfolder->fields['foldertype'];

				if (mb_check_encoding($file,"UTF-8")) $namefile=utf8_decode($file);
				else $namefile=$file;

				$docfolder->fields['name']=$namefile;
				$docfolder->fields['description']="";

				if ($objfolder->fields['id']==0) $docfolder->fields['parents']=$objfolder->fields['id'];
				else $docfolder->fields['parents']=$objfolder->fields['parents'].",".$objfolder->fields['id'];

				$docfolder->fields['readonly']=0;
				$docfolder->fields['readonly_content']=0;
				$docfolder->fields['timestp_create']=dims_createtimestamp();
				$docfolder->fields['timestp_modify']=dims_createtimestamp();
				$docfolder->fields['published']=$objfolder->fields['published'];
				$docfolder->fields['id_folder']=$objfolder->fields['id'];
				$docfolder->setugm();
				//dims_print_r($docfolder);die();
				$docfolder->save();
				$ok = createRecursiveFiles("$src$file"._DIMS_SEP, "$dest$file"._DIMS_SEP, $mask,$docfolder);

				// maj des �l�ments
				$docfolder->save();
				unset($docfolder);
			}
			else
			{
				// test if writable
				if (!(file_exists("$dest$file") && !is_writable("$dest$file"))) {
					//copy("$src/$file", "$dest/$file");

					$docfile = new docfile();
					$docfile->setugm();

					$docfile->fields['id_folder'] = $objfolder->fields['id'];
					$docfile->fields['size'] = filesize("$src$file");

					if (_DIMS_ENCODING!="UTF-8" && mb_check_encoding($file,"UTF-8")) $namefile=utf8_decode($file);
					else $namefile=$file;

					$docfile->fields['name'] = $namefile;
					$docfile->tmpzipfile = "$src$file";
					$erreur=$docfile->save();
					if ($erreur>0)
					{
						echo $erreur;
						//die();
					}
					unlink($src.$file);
					unset($docfile);
				}
				else $ok = false;
			}
		}
	}
	return $ok;
}

function doc_getfolders($module_id = '') {
	$db = dims::getInstance()->getDb();
	global $_DIMS;
	require_once DIMS_APP_PATH . '/include/functions/shares.php';
	$shares = dims_shares_get($_SESSION['dims']['userid']);

	$list_sharedfolder = array();

	foreach($shares as $sh) {
		if ($sh['id_object'] == _DOC_OBJECT_FOLDER) $list_sharedfolder[] = $sh['id_record'];
	}

	// DISPLAY FOLDERS
	$param = array();
	$where = (!empty($list_sharedfolder)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfolder, 'sharedfolder', $param).')' : '';

	$folders = array('list' => array(), 'tree' => array());

	$docf=new docfolder();
	$docf->fields['id']=0;
	$docf->fields['id_folder']=-1;
	$docf->fields['name']=$_DIMS['cste']['_DOC_ROOT'];
	$docf->fields['parents']=-1;

	$folders['list'][$docf->fields['id']] = $docf->fields;
	$folders['tree'][$docf->fields['id_folder']][] = $docf->fields['id'];

	if ($module_id != '')
		$mod = $module_id ;
	else
		$mod = $_SESSION['dims']['moduleid'];
	$select = "	SELECT		*
				FROM		dims_mod_doc_folder as f
				where		f.published = 1
				AND			f.id_module = :moduleid";
	$param[':moduleid'] = $mod;
	if (!dims_isadmin()) {
		$select.="
				AND			(f.id_user = :userid
							OR (f.foldertype = 'public' AND f.id_workspace IN (".dims_viewworkspaces()."))
							$where
							)";
		$param[':userid'] = $_SESSION['dims']['userid'];
	}
	$select.="			ORDER BY	f.name";

	$result = $db->query($select, $param);
	while ($fields = $db->fetchrow($result)) {
			$folders['list'][$fields['id']] = $fields;
			$folders['tree'][$fields['id_folder']][] = $fields['id'];
	}

	return($folders);
}

/**
* build recursively the whole groups tree
*
*/

function doc_build_tree($folders,$selectedfolder,$from_wid = 0, $from_gid = 0, $str = '',$selectfolders=array()) {
	global $scriptenv;

	$html = '';

	//if ($from_wid == 0) $from_wid = 1543;
	$html = '';
	//echo $from_wid." <br>";
	if (isset($folders['tree'][$from_wid])) {
		$c=0;
		foreach($folders['tree'][$from_wid] as $wid) {
			if (!in_array($wid,$selectfolders)) {
				$folder = $folders['list'][$wid];
				$isworkspacesel = (!empty($folderid) && ($folderid == $wid));

				if (isset($groupsel['parents_workspace']) && isset($foldersel['parents']))
					$gselparents = (isset($foldersel)) ? explode(',',$foldersel['parents']) : explode(',',$groupsel['parents_workspace']);
				else $gselparents=array();

				$testparents = explode(',',$folder['parents']);
				if (isset($foldersel)) $testparents[] = $folder['id'];

				// workspace opened if parents array intersects
				$isworkspaceopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
				$islast = ((!isset($folders['tree'][$from_wid]) || $c == sizeof($folders['tree'][$from_wid])-1) && !isset($groups['workspace_tree'][$from_wid]));
				$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
				$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

				if ($isworkspacesel) $style_sel = 'bold';
				else $style_sel = 'none';

				$new_str = ' '; // decalage pour les noeuds suivants

				if (!empty($str)) {
					if (!$islast) $new_str = $str.'(s)'; // |
					else $new_str = $str.'(b)';  // (vide)

					$last = 'joinbottom';
					if ($islast) $last = 'join';
					if (isset($folders['tree'][$wid])) {
						if ($islast) {
							$last = 'minus';
						}
						else {
							$last = 'minusbottom';
						}
					}
					$decalage .= "<div style=\"float:left;\" id=\"w{$folder['id']}_plus\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></div>";
				}

				$html_rec = doc_build_tree($folders,$selectedfolder, $wid, 0, $new_str,$selectfolders);

				$display = ($html_rec == '') ? 'none' : 'block';



				if (in_array($folder['id'],$selectedfolder))  {
					if ($html_rec!="") {
						$html .="
						<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
							<div style=\"float:left;\">{$decalage}<img src=\"./common/modules/doc/img/ico_folder.png\">&nbsp;</div>";
						$html .= dims_strcut($folder['name'],25);
					}
				}
				else {
					$html .="
						<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
							<div style=\"float:left;\">{$decalage}<img src=\"./common/modules/doc/img/ico_folder.png\">&nbsp;</div>";
					$html .= "<a href=\"#\" onclick=\"document.getElementById('iddestfolder').value=".$folder['id'].";document.listdoc.submit();\">".dims_strcut($folder['name'],25)."</a>";
				}
				$html .= "</div><div style=\"clear:left;display:$display;\" id=\"w{$folder['id']}\" style=\"padding:0px;\">$html_rec</div>
						";
				$c++;
			}
		}
	}

	return $html;
}

/**
* build recursively the whole folder tree
*
*/

function doc_build_maintree($folders, $fromhid = 0, $str = '', $depth = 1, $option = '',$folderid=-1) {
	global $scriptenv;

	switch($option) {
		// used for fckeditor and link redirect on folder
		case 'selectredirect':
		case 'selectlink':
		//dims_print_r($folders);
			$foldersel = $folders['list'][$folders['tree'][0][0]];
		break;

		default:
			if (isset($folders['list'][$folderid])) $foldersel = $folders['list'][$folderid];
			else $foldersel=0;
		break;
	}

	$html = '';
	if (isset($folders['tree'][$fromhid])) {
		$c=0;
		foreach($folders['tree'][$fromhid] as $hid) {

			$folder = $folders['list'][$hid];
			$isfoldersel = ($folderid == $hid && $option == '');

			$hselparents = explode(',',$foldersel['parents']);
			$testparents = explode(',',$folder['parents']);
			$testparents[] = $folder['id'];

			// folder opened if parents array intersects
			$isfolderopened = sizeof(array_intersect ($hselparents, $testparents)) == sizeof($testparents);
			// last node or not ?
			$islast = ((!isset($folders['tree'][$fromhid]) || $c == sizeof($folders['tree'][$fromhid])-1) );

			$decalage = str_replace("(b)", "<img src=\"./common/modules/doc/img/empty.png\" />", $str);
			$decalage = str_replace("(s)", "<img src=\"./common/modules/doc/img/line.png\" />", $decalage);
			$style_sel = ($isfoldersel) ? 'bold' : 'none';

			$icon = 'ico_folder';

			if (isset($folder['foldertype']) && $folder['foldertype']=="network")
				$icon ='folder_network';

			$new_str = ''; // decalage pour les noeuds suivants
			if ($depth == 1 || $folder['id'] == $fromhid) $icon = 'base';
			else {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)
			}

			switch($option) {
				// used for fckeditor and link redirect on folder
				case 'selectredirect':
				case 'selectlink':
					$link = $link_div ="<a name=\"folder{$hid}\" onclick=\"javascript:doc_showfolder('{$hid}','{$new_str}&option={$option}');\" href=\"javascript:void(0);\">";
				break;

				default:
					$link_div ="<a name=\"folder{$hid}\" onclick=\"javascript:doc_showfolder('{$hid}','{$new_str}');\" href=\"javascript:void(0);\">";
					$link = "<a style=\"font-weight:{$style_sel}\" href=\"admin.php?currentfolder={$folder['id']}\">";
				break;
			}

			if ($depth > 1) {
				$last = 'joinbottom';
				if ($islast) $last = 'join';

				if (isset($folders['tree'][$hid]))
				{
					if ($islast) $last = ($isfoldersel || $isfolderopened) ? 'minus' : 'plus';
					else  $last = ($isfoldersel || $isfolderopened) ? 'minusbottom' : 'plusbottom';
				}
				$decalage .= "<div style=\"float:left;\" id=\"{$folder['id']}_plus\">{$link_div}<img src=\"./common/modules/doc/img/{$last}.png\" /></a></div>";
			}

			$html_rec = '';

			if ($isfoldersel || $isfolderopened || $depth == 1 ) $html_rec = doc_build_maintree($folders, $hid, $new_str, $depth+1, $option,$folderid);

			$display = ($isfolderopened || $isfoldersel || $depth == 1) ? 'block' : 'none';

			$html .=	"
						<div class=\"wce_tree_node\">
							{$decalage}<img src=\"./common/modules/doc/img/{$icon}.png\" />
							{$link}".dims_strcut($folder['name'],25)."</a>
						</div>
						<div style=\"clear:left;display:$display;\" id=\"{$folder['id']}\">$html_rec</div>
						";
			$c++;
		}
	}

	return $html;
}

?>
