<?php
/***************************************/
/*********** GERER LE FILTRE ***********/
/***************************************/
global $contextual;
$contextual=array();
$dims=dims::getInstance();
$contextual[$_SESSION['cste']['ADD_DOCUMENT']]['label']     = $_SESSION['cste']['ADD_DOCUMENT'];
$contextual[$_SESSION['cste']['ADD_DOCUMENT']]['image']     = _DESKTOP_TPL_PATH.'/gfx/common/deposer_document.png';
$contextual[$_SESSION['cste']['ADD_DOCUMENT']]['href']      = $dims->getScriptEnv().'?more='.dims_const_desktopv2::_DOCUMENTS_EDIT_FILE;

$contextual[$_SESSION['cste']['_ADD_FOLDER']]['label']     = $_SESSION['cste']['_ADD_FOLDER'];
$contextual[$_SESSION['cste']['_ADD_FOLDER']]['image']     = _DESKTOP_TPL_PATH.'/gfx/common/ajouter_dossier.png';
$contextual[$_SESSION['cste']['_ADD_FOLDER']]['href']      = $dims->getScriptEnv().'?more='.dims_const_desktopv2::_DOCUMENTS_ADD_FOLDER;

$level=dims_load_securvalue('level',dims_const::_DIMS_NUM_INPUT,true,true);
$item=dims_load_securvalue('selitem',dims_const::_DIMS_CHAR_INPUT,true,true);

if(isset($level) && $level != '' && !empty($item)){
	$_SESSION['dims']['gedfinder'][$level] = $item;
	$i=$level + 1;
	while(isset($_SESSION['dims']['gedfinder'][$i])){
		unset($_SESSION['dims']['gedfinder'][$i]);
		$i++;
	}
}elseif(!isset($_SESSION['dims']['gedfinder']))
	$_SESSION['dims']['gedfinder'] = array();

$this->initFolder(); // TODO : à enlever une fois que la création des folders sera effectué lors de la création
$h3 = array();
$h3[] = '<a href="admin.php?level=0&selitem=FOLD_'.$this->fields['id_folder'].'">'.$_SESSION['cste']['_DOC_ROOT'].'</a>';

require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
require_once DIMS_APP_PATH.'modules/doc/include/global.php';
require_once DIMS_APP_PATH.'modules/system/class_dims_browser.php';

$browser = new dims_browser();
$browser->setSpecificNodeTPL(_DESKTOP_TPL_LOCAL_PATH.'/concepts/finder/finder_node.tpl.php');
$continue = true;

$current_folder = $this->fields['id_folder'];
$current_node = $browser;
$current_level = 0;


while($continue){
	$elements = docfolder::getElements($current_folder, ' ORDER BY name ASC', ' ORDER BY name DESC');

	$sel = null;
	foreach($elements[0] as $subfolder){
		$user = new user();
		$user->open($subfolder['id_user']);
		$create = dims_timestamp2local($subfolder['timestp_create']);
		$modify = dims_timestamp2local($subfolder['timestp_modify']);
		$data = array(
		'key'			=> 'FOLD_'.$subfolder['id'],
		'libelle'		=> (isset($_SESSION['cste'][$subfolder['name']])) ? $_SESSION['cste'][$subfolder['name']] : $subfolder['name'],
		'type'			=> 'folder',
		'auteur'		=> $user->fields['firstname'].' '.$user->fields['lastname'],
		'creation'		=> $create['date'].' à '.$create['time'],
		'modification'	=> $modify['date'].' à '.$modify['time'],
		'taille'		=> $subfolder['nbelements']
		);

		if (in_array('FOLD_'.$subfolder['id'],$_SESSION['dims']['gedfinder']))
			$h3[] = '<a href="admin.php?level='.$current_node->getDepth().'&selitem='.$data['key'].'">'.((isset($_SESSION['cste'][$subfolder['name']])) ? $_SESSION['cste'][$subfolder['name']] : $subfolder['name']).'</a>';

		$child = $current_node->addChild($data);
		$child->setSpecificNodeTPL(_DESKTOP_TPL_LOCAL_PATH.'/concepts/finder/finder_node.tpl.php');
		if(isset($_SESSION['dims']['gedfinder'][$current_level]) && $_SESSION['dims']['gedfinder'][$current_level] == $data['key'])
		{
			$sel = $child;
			$child->setSelected(true);
			$sel_folder = $subfolder['id'];
		}

		if($subfolder['nbelements'] > 0){
			$data2 = array(
				'key'       => 'GHOST'
			);
			$child->addChild($data2);
		}
	}

	foreach($elements[1] as $subfile){
		//dims_print_r($subfile);
		$user = new user();
		$user->open($subfile['id_user']);
		$create = dims_timestamp2local($subfile['timestp_create']);
		$modify = dims_timestamp2local($subfile['timestp_modify']);

		$data = array(
		'key'       	=> 'FILE_'.$subfile['id'],
		'libelle'   	=> $subfile['name'],
		'type'			=> 'file',
		'extension'		=> $subfile['extension'],
		'auteur'		=> $user->fields['firstname'].' '.$user->fields['lastname'],
		'creation'		=> $create['date'].' à '.$create['time'],
		'modification'	=> $modify['date'].' à '.$modify['time'],
		'taille'		=> sprintf("%.02f",$subfile['size']/1024),
		'version'		=> $subfile['version'],
		'file_path'		=> _DIMS_WEBPATHDATA."doc-{$subfile['id_module']}"._DIMS_SEP.substr($subfile['timestp_create'],0,8)._DIMS_SEP."{$subfile['id']}_{$subfile['version']}.{$subfile['extension']}",
		'md5id'			=> $subfile['md5id']
				);

		$child = $current_node->addChild($data);
		$child->setSpecificLeafTPL(_DESKTOP_TPL_LOCAL_PATH.'/concepts/finder/finder_leaf.tpl.php');
		if(isset($_SESSION['dims']['gedfinder'][$current_level]) && $_SESSION['dims']['gedfinder'][$current_level] == $data['key'])
		{
			$child->setSelected(true);
			$sel = $child;
			if (in_array('FILE_'.$subfile['id'],$_SESSION['dims']['gedfinder']))
				$h3[] = (isset($_SESSION['cste'][$subfile['name']])) ? $_SESSION['cste'][$subfile['name']] : $subfile['name'];
		}
		//$child->setSpecificLeafTPL(null);//la feuille ne sera pas gérée par le finder mais en dehors
	}

	if(!is_null($sel) && substr($_SESSION['dims']['gedfinder'][$current_level], 0, 4) == 'FOLD'){
		$current_folder = $sel_folder;
		$continue = true;
		$current_level ++;
		$current_node = $sel;
	}
	else $continue = false;
}

$more = dims_load_securvalue('more',dims_const::_DIMS_CHAR_INPUT,true,true,false);
switch($more){
	case dims_const_desktopv2::_DOCUMENTS_EDIT_FOLDER:
		$fold = new docfolder();
		unset($_SESSION['dims']['gedfinder']);
		if ($current_folder != '' && $current_folder > 0 && $fold->open($current_folder)){
			?>
			<form method="POST" action="<? echo dims::getInstance()->getScriptEnv(); ?>">
                <?
                    // Sécurisation du formulaire par token
                    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                    $token = new FormToken\TokenField;
                    $token->field("action",		"add_folder");
                    $token->field("id_folder",	$fold->fields['id_parent']);
                    $token->field("editId",		$current_folder);
                    $token->field("fold_name");
                    $token->field("fold_description");
                    $tokenHTML = $token->generate();
                    echo $tokenHTML;
                ?>
				<input type="hidden" value="add_folder" name="action" />
				<input type="hidden" value="<? echo $fold->fields['id_parent']; ?>" name="id_folder" />
				<input type="hidden" value="<? echo $current_folder; ?>" name="editId" />
				<table style="width: 95%;margin-left:20px;" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label">
							<? echo ucfirst($_SESSION['cste']['_DOC_FOLDER']); ?>&nbsp;:
						</td>
						<td>
							<input type="text" name="fold_name" value="<? echo $fold->fields['name']?>" />
						</td>
					</tr>
					<tr>
						<td class="label" style="vertical-align: top;">
							<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>&nbsp;:
						</td>
						<td>
							<textarea style="width: 250px;height: 75px;" name="fold_description"><? echo $fold->fields['description']?></textarea>
						</td>
					</tr>
				</table>
				<input type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv(); ?>';" />
				<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
			</form>
			<?
		}
		break;
	case dims_const_desktopv2::_DOCUMENTS_ADD_FOLDER:
		//	unset($_SESSION['dims']['gedfinder']);
		?>
		<form method="POST" action="<? echo dims::getInstance()->getScriptEnv(); ?>">
            <?
                // Sécurisation du formulaire par token
                require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                $token = new FormToken\TokenField;
                $token->field("action",		"add_folder");
                $token->field("id_folder",	$current_folder);
                $token->field("name");
                $token->field("description");
                $tokenHTML = $token->generate();
                echo $tokenHTML;
            ?>
			<input type="hidden" value="add_folder" name="action" />
			<input type="hidden" value="<? echo $current_folder; ?>" name="id_folder" />
			<table style="width: 95%;margin-left:20px;" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label">
						<? echo ucfirst($_SESSION['cste']['_DOC_FOLDER']); ?>&nbsp;:
					</td>
					<td>
						<input type="text" name="name" />
					</td>
				</tr>
				<tr>
					<td class="label" style="vertical-align: top;">
						<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>&nbsp;:
					</td>
					<td>
						<textarea style="width: 250px;height: 75px;" name="description"></textarea>
					</td>
				</tr>
			</table>
			<input type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv(); ?>';" />
			<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		</form>
		<?
		break;
	case dims_const_desktopv2::_DOCUMENTS_EDIT_FILE:

		//unset($_SESSION['dims']['gedfinder']);
		?>
		<form method="POST" action="<? echo dims::getInstance()->getScriptEnv(); ?>" enctype="multipart/form-data">
            <?
                // Sécurisation du formulaire par token
                require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                $token = new FormToken\TokenField;
                $token->field("action",		"add_file");
                $token->field("id_folder",	"$current_folder");
                $token->field("file");
                $token->field("description");
                $tokenHTML = $token->generate();
                echo $tokenHTML;
            ?>
			<input type="hidden" value="add_file" name="action" />
			<input type="hidden" value="<? echo $current_folder; ?>" name="id_folder" />
			<table style="width: 95%;margin-left:20px;" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label">
						<? echo ucfirst($_SESSION['cste']['DOCUMENT']); ?>&nbsp;:
					</td>
					<td>
						<input type="file" name="file" />
					</td>
				</tr>
				<tr>
					<td class="label" style="vertical-align: top;">
						<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>&nbsp;:
					</td>
					<td>
						<textarea style="width: 250px;height: 75px;" name="description"></textarea>
					</td>
				</tr>
			</table>
			<input type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv(); ?>';" />
			<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		</form>
		<?
		break;
}

?>
<h4 style="margin-left:30px;">
	<? echo implode(' > ',$h3); ?>
</h4>
<?

include _DESKTOP_TPL_LOCAL_PATH.'/concepts/finder/finder.tpl.php';

?>
