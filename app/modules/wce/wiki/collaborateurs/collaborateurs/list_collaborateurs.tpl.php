<?php
if (!isset($_SESSION['wiki']['collab']['statut'])) $_SESSION['oeuvre']['collab']['statut'] = module_wiki::_WIKI_ADMIN_STATUT_ACTIF;
$_SESSION['wiki']['collab']['statut'] = dims_load_securvalue('statut',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['wiki']['collab']['statut'],$_SESSION['wiki']['collab']['statut']);
if (!isset($_SESSION['wiki']['collab']['name'])) $_SESSION['wiki']['collab']['name'] = '';
if (isset($_POST['name'])) {
	$_SESSION['wiki']['collab']['name'] = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
} else {
	$_SESSION['wiki']['collab']['collab'] = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['wiki']['collab']['name'],$_SESSION['wiki']['collab']['name']);
}

$work = new workspace();
$work->open($_SESSION['dims']['workspaceid']);

$tmstp = dims_createtimestamp();

switch($_SESSION['wiki']['collab']['statut']){
	default:
	case module_wiki::_WIKI_ADMIN_STATUT_ALL:
		$and = '';
		break;
	case module_wiki::_WIKI_ADMIN_STATUT_ACTIF:
		$and = " AND (dims_user.date_expire IS NULL OR dims_user.date_expire = '' OR dims_user.date_expire = '00000000000000' OR dims_user.date_expire >= $tmstp) ";
		break;
	case module_wiki::_WIKI_ADMIN_STATUT_INACTIF:
		$and = " AND dims_user.date_expire IS NOT NULL AND dims_user.date_expire != '' AND dims_user.date_expire > 0 AND dims_user.date_expire < $tmstp ";
		break;
}
$lstUsers = $work->getUsersOpen($_SESSION['wiki']['collab']['name'],$and);

$lstGr = module_wiki::getGrDispo();

?>
<h4><? echo $_SESSION['cste']['_LIST_OF_ASSOCIATES']; ?></h4>
<div class="cadre_article">
	<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C); ?>" name="filtre_user">
		<label>
			<? echo $_SESSION['cste']['_OEUVRE_STATUS']; ?>&nbsp;:&nbsp;
		</label>
		<select name="statut" onchange="javascript:document.filtre_user.submit();">
			<option <? if ($_SESSION['wiki']['collab']['statut'] == module_wiki::_WIKI_ADMIN_STATUT_ALL) echo "selected=true"; ?> value="<? echo module_wiki::_WIKI_ADMIN_STATUT_ALL; ?>">
				<? echo $_SESSION['cste']['_OEUVRE_ALL']; ?>
			</option>
			<option <? if ($_SESSION['wiki']['collab']['statut'] == module_wiki::_WIKI_ADMIN_STATUT_ACTIF) echo "selected=true"; ?> value="<? echo module_wiki::_WIKI_ADMIN_STATUT_ACTIF; ?>">
				<? echo $_SESSION['cste']['_DIMS_LABEL_ACTIVE']; ?>
			</option>
			<option <? if ($_SESSION['wiki']['collab']['statut'] == module_wiki::_WIKI_ADMIN_STATUT_INACTIF) echo "selected=true"; ?> value="<? echo module_wiki::_WIKI_ADMIN_STATUT_INACTIF; ?>">
				<? echo $_SESSION['cste']['_DIMS_LABEL_NO_ACTIVE']; ?>
			</option>
		</select>
		<label style="margin-left:10px;">
			<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>&nbsp;:&nbsp;
		</label>
		<input type="text" value="<? echo $_SESSION['wiki']['collab']['name']; ?>" name="name" />
		<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_FILTER']; ?>" />
		<?
		if (dims_isactionallowed(module_wiki::_ACTION_ADMIN_EDIT_COLLAB)){
		?>
		<a style="float: right;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C."&action=".module_wiki::_ACTION_EDIT_COLLAB); ?>">
			<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_add.png'); ?>" alt="<? echo $_SESSION['cste']['_OEUVRE_CREATE_A_NEW_ACCOUNT']; ?>" title="<? echo $_SESSION['cste']['_OEUVRE_CREATE_A_NEW_ACCOUNT']; ?>" />
			<? echo $_SESSION['cste']['_OEUVRE_CREATE_A_NEW_ACCOUNT']; ?>
		</a>
		<? } ?>
	</form>
</div>
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL_FIRSTNAME']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_PHONE']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_SERVICE']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
		<?
		foreach($lstUsers as $us){
		?>
		<tr>
			<td>
				<? echo $us->fields['firstname']; ?>
			</td>
			<td>
				<? echo $us->fields['lastname']; ?>
			</td>
			<td>
				<? echo $us->fields['email']; ?>
			</td>
			<td>
				<? echo $us->fields['phone']; ?>
			</td>
			<td>
				<ul>
				<?
				$grs = $us->getGroupsLabeled($lstGr,array($_SESSION['dims']['workspaceid']));
				foreach($grs as $gr){
					echo '<li>'.$gr['label'].'</li>';
				}
				?>
				</ul>
			</td>
			<td class="actions">
				<?
				if (dims_isactionallowed(module_wiki::_ACTION_ADMIN_VALID_COLLAB)){
					$linkSwitch = module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C."&action=".module_wiki::_ACTION_SWITCH_COLLAB."&id=".$us->fields['id']);
					if ($us->fields['date_expire'] == '' || $us->fields['date_expire'] == '00000000000000' || $us->fields['date_expire'] >= $tmstp)
						echo '<img title="'.$_SESSION['cste']['_DIMS_LABEL_ACTIVE'].'" alt="'.$_SESSION['cste']['_DIMS_LABEL_ACTIVE'].'" onclick="javascript:document.location.href=\''.$linkSwitch.'\';" src="'.module_wiki::getTemplateWebPath('/gfx/deverouiller16.png').'" />';
					else
						echo '<img title="'.$_SESSION['cste']['_DIMS_LABEL_NO_ACTIVE'].'" alt="'.$_SESSION['cste']['_DIMS_LABEL_NO_ACTIVE'].'" onclick="javascript:document.location.href=\''.$linkSwitch.'\';" src="'.module_wiki::getTemplateWebPath('/gfx/verouiller16.png').'" />';
				}
				if(dims_isactionallowed(module_wiki::_ACTION_ADMIN_EDIT_COLLAB)){
				?>
				<img onclick="document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C."&action=".module_wiki::_ACTION_EDIT_COLLAB."&id=".$us->fields['id']); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
				<? } ?>
			</td>
		</tr>
		<?
		}
		?>
	</table>
</div>
