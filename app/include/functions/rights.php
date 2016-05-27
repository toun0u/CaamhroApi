<?
###############################################################################
#
# module/group management
#
###############################################################################

/**
* ! description !
*
* @param int group ID
* @return bool
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*/
function dims_isadmin($workspaceid = -1) {
	global $dims;
	if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
	//return ($workspaceid != -1 && !empty($_SESSION['dims']['workspaces'][$workspaceid]['adminlevel']) && $_SESSION['dims']['workspaces'][$workspaceid]['adminlevel'] == dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN);
	return ($workspaceid != -1 && $dims->isAdmin($workspaceid));
}

/**
* ! description !
*
* @param int group ID
* @return bool
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*/
function dims_ismanager($workspaceid = -1) {
	global $dims;
	if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
	//return ($workspaceid != -1 && !empty($_SESSION['dims']['workspaces'][$workspaceid]['adminlevel']) && $_SESSION['dims']['workspaces'][$workspaceid]['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER);
	return ($workspaceid != -1 && $dims->isManager($workspaceid));
}

/**
* ! description !
*
* @return bool !!
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*
* @uses dims_isadmin()
*/
function dims_iscontentmanager($workspaceid = -1) {
	global $dims;

	if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
	$contentmanager = FALSE;
	return ($contentmanager || $dims->isContentManager($workspaceid));
	/*
	if (isset($_SESSION['dims']['actions'][$workspaceid]))
	{
		foreach($_SESSION['dims']['actions'][$workspaceid] as $moduleid => $action)
		{
			$contentmanager |= dims_ismodulemanager($workspaceid, $moduleid);
		}
	}
	return(dims_isadmin($workspaceid) || $contentmanager);
	 */
}

/**
* ! description !
*
* @param int module ID
* @return bool !!
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*/
function dims_ismodulemanager($workspaceid = -1, $moduleid = -1) {
	global $dims;
	if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
	if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined

	return ($dims->isModuleManager($workspaceid,$moduleid));
}

/**
* ! description !
*
* @param int action ID
* @param int group ID
* @param int module ID
* @return bool !!
*
* @version 2.09
* @since 0.1
*
* @category module/group management
*
* @uses dims_isadmin()
*/
function dims_isactionallowed($actionid = -1, $workspaceid = -1, $moduleid = -1) {
	global $dims;
	if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
	if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined

	return $dims->isActionAllowed($actionid, $workspaceid, $moduleid);
	//if ($actionid == -1) return (dims_isadmin($workspaceid) || isset($_SESSION['dims']['actions'][$workspaceid][$moduleid]));
	//else return (dims_isadmin($workspaceid) || isset($_SESSION['dims']['actions'][$workspaceid][$moduleid][$actionid]));
}

function dims_isroot() {
	return ($_SESSION['dims']['login'] == 'root');
}

?>
