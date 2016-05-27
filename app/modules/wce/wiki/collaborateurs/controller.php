<?php
$subsub = dims_load_securvalue('subsub',dims_const::_DIMS_CHAR_INPUT,true,true);
if (empty($subsub))
	$subsub = module_wiki::_SUB_SUB_COLLAB_LIST_C;
?>
<ul class="sub_menu">
	<li>
		<a href="<? echo module_wiki::getScriptEnv('sub='.$sub."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C); ?>" <? if($subsub == module_wiki::_SUB_SUB_COLLAB_LIST_C) echo 'class="selected"'; ?>>
			<? echo $_SESSION['cste']['_LIST_OF_ASSOCIATES']; ?>
		</a>
	</li>
	<li>
		<a href="<? echo module_wiki::getScriptEnv('sub='.$sub."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R); ?>" <? if($subsub == module_wiki::_SUB_SUB_COLLAB_LIST_R) echo 'class="selected"'; ?>>
			<? echo $_SESSION['cste']['_SYSTEM_LABELICON_ROLES']; ?>
		</a>
	</li>
	<li>
		<a href="<? echo module_wiki::getScriptEnv('sub='.$sub."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S); ?>" <? if($subsub == module_wiki::_SUB_SUB_COLLAB_LIST_S) echo 'class="selected"'; ?>>
			<? echo $_SESSION['cste']['_SERVICE']; ?>
		</a>
	</li>
</ul>
<?
switch($subsub){
	default:
	case module_wiki::_SUB_SUB_COLLAB_LIST_C:
		require_once module_wiki::getTemplatePath('/collaborateurs/collaborateurs/controller.php');
		break;
	case module_wiki::_SUB_SUB_COLLAB_LIST_R:
		require_once module_wiki::getTemplatePath('/collaborateurs/roles/controller.php');
		break;
	case module_wiki::_SUB_SUB_COLLAB_LIST_S:
		require_once module_wiki::getTemplatePath('/collaborateurs/services/controller.php');
		break;
}
?>