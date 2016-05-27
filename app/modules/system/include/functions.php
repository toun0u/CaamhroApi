<?php
global $_DIMS;
$planning_mois[1]=$_DIMS['cste']['_JANUARY'];
$planning_mois[2]=$_DIMS['cste']['_FEBRUARY'];
$planning_mois[3]=$_DIMS['cste']['_MARCH'];
$planning_mois[4]=$_DIMS['cste']['_APRIL'];
$planning_mois[5]=$_DIMS['cste']['_MAY'];
$planning_mois[6]=$_DIMS['cste']['_JUNE'];
$planning_mois[7]=$_DIMS['cste']['_JULY'];
$planning_mois[8]=$_DIMS['cste']['_AUGUST'];
$planning_mois[9]=$_DIMS['cste']['_SEPTEMBER'];
$planning_mois[10]=$_DIMS['cste']['_OCTOBER'];
$planning_mois[11]=$_DIMS['cste']['_NOVEMBER'];
$planning_mois[12]=$_DIMS['cste']['_DECEMBER'];

// OPTIMISATION
// fonction a optimiser car mal programm�....
function system_getgroups() {
	$db = dims::getInstance()->getDb();
	global $groupid;
	global $workspaces;

	$groups = array('list' => array(), 'tree' => array(), 'workspace_tree' => array());

	$select = "SELECT * FROM dims_group WHERE system = 0 ORDER BY depth,label";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result)) {
		$fields['parents_workspace'] = '';
		$fields['groups'] = array();
		$groups['list'][$fields['id']] = $fields;
		$groups['tree'][$fields['id_group']][] = $fields['id'];
		if (!empty($fields['id_workspace']) && isset($workspaces['list'][$fields['id_workspace']]))
		{
			$groups['workspace_tree'][$fields['id_workspace']][] = $fields['id'];
			$workspaces['list'][$fields['id_workspace']]['groups'][] = $fields['id'];
			if ($groups['list'][$fields['id']]['shared']) $workspaces['list'][$fields['id_workspace']]['groups_shared'][] = $fields['id'];
		}
	}

	foreach($groups['workspace_tree'] as $idw => $list_idg) {
		foreach($list_idg as $idg)
		{
			if (isset($workspaces['list'][$idw])) $groups['list'][$idg]['parents_workspace'] = $workspaces['list'][$idw]['parents'];
		}
	}

	// application de l'h�ritage du lien de parent� entre un groupe et un espace aux sous groupes
	foreach($groups['tree'] as $idg => $list_idg) {
		foreach($list_idg as $idg_child) {
			if (isset($groups['list'][$idg])) {
				$groups['list'][$idg_child]['parents_workspace'] = $groups['list'][$idg]['parents_workspace'];
			}
		}
	}

	foreach($workspaces['list'] as $idw => $workspace) {
		// r�cup�ration des sous-groupes
		foreach($workspace['groups'] as $idg) {
			if (isset($groups['tree'][$idg])) {
				foreach($groups['tree'][$idg] as $idg2) {
					$workspaces['list'][$idw]['groups'][] = $idg2;
					if ($groups['list'][$idg2]['shared']) $workspaces['list'][$idw]['groups_shared'][] = $idg2;
				}
			}
		}

		// h�ritage des partages
		if (isset($workspaces['tree'][$idw]) && !empty($workspaces['list'][$idw]['groups']))
		{
			foreach($workspaces['tree'][$idw] as $idw2)
			{
				$workspaces['list'][$idw2]['groups_shared'] = array_merge($workspaces['list'][$idw2]['groups_shared'], $workspaces['list'][$idw]['groups_shared']);
				$workspaces['list'][$idw2]['groups'] = array_merge($workspaces['list'][$idw2]['groups'], $workspaces['list'][$idw2]['groups_shared']);
			}
		}

		// application des partages de groupes aux groupes
		$workspace = $workspaces['list'][$idw];
		foreach($workspace['groups'] as $idg)
		{
			$groups['list'][$idg]['groups'] = array_unique(array_merge($groups['list'][$idg]['groups'], $workspace['groups']));
		}
	}
	return($groups);
}

function system_getavailabledworkspaces() {
	$db = dims::getInstance()->getDb();
	global $workspaceid;
	global $dims;
	$workspaces = array('list' => array(), 'tree' => array());

	//$select = "SELECT * FROM dims_workspace WHERE system = 0 ORDER BY depth,label";
	$user = new user();
	$user->open($_SESSION['dims']['userid']);
	/*
	$lstworkspacesenabled=$user->getworkspaces();
	$taballow=array();

	foreach($dims->getWorkspaces() as $w=>$vwork) {
		$taballow[$w]=$w;
	}

	// fusion entre cette liste et celle li�e au nom de domaine
	$lstworkspacesenabled=array_intersect_key($lstworkspacesenabled, $taballow);
	 */
	if (dims_isadmin() ) {
		$lstworkspacesenabled=$dims->getAllWorkspaces();
	}
	else {
		$lstworkspacesenabled=$dims->getAdminWorkspaces();
	}
	/*
	foreach($lstworkspacesenabled as $w) {
		echo $w['id']." ".$w['label']."<br>";
	}*/

	$lstcurrent =array();

	$select = "SELECT * FROM dims_workspace ORDER BY depth,label";
	$result = $db->query($select);

	while ($fields = $db->fetchrow($result)) {

	//foreach ($lstworkspaces as $id => $fields) {
		$add = true;

		if (!isset($lstworkspacesenabled[$fields['id']])) {
			$add=false;
		}

		/*
		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && $_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN)
		{
			// get allowed only groups
			$array_parents = explode(';',$fields['parents']);
			if (!($fields['id'] == $_SESSION['dims']['workspaceid'] || in_array($_SESSION['dims']['workspaceid'],$array_parents))) $add = false;
		}
		*/

		if ($add) {
			$fields['groups'] = array();
			$fields['groups_shared'] = array();
			$workspaces['list'][$fields['id']] = $fields;

			// verification de l'existence du p�re  :
			$array_parents = explode(';',$fields['parents']);
			//if ($fields['id']==64) dims_print_r($array_parents);
			$array_parents= array_reverse($array_parents);

			$find=false;
			$i=0;
			$taille=sizeof($array_parents);
			//if ($fields['id']==64) dims_print_r($array_parents);
			foreach($array_parents as $idw => $w) {
				if (!$find) {
					if (isset($lstworkspacesenabled[$w])) {
						$workspaces['tree'][$w][] = $fields['id'];
						$find=true;
					}
				}
			}

			// si pas trouve on rattache � syst�me
			if (!$find) {
				$workspaces['tree'][1][] = $fields['id'];
			}
		}
	}

	return($workspaces);
}

function system_getworkspaces() {
	$db = dims::getInstance()->getDb();
	global $workspaceid;

	$workspaces = array('list' => array(), 'tree' => array());

	$select = "SELECT * FROM dims_workspace WHERE system = 0 ORDER BY depth,label";

	$select = "SELECT * FROM dims_workspace ORDER BY depth,label";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result)) {
	//dims_print_r($lstworkspaces);
		$add = true;
		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && $_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN)
		{
			// get allowed only groups
			$array_parents = explode(';',$fields['parents']);
			if (!($fields['id'] == $_SESSION['dims']['workspaceid'] || in_array($_SESSION['dims']['workspaceid'],$array_parents))) $add = false;
		}

		if ($add)
		{
			$fields['groups'] = array();
			$fields['groups_shared'] = array();
			$workspaces['list'][$fields['id']] = $fields;
			$workspaces['tree'][$fields['id_workspace']][] = $fields['id'];
		}
	}

	return($workspaces);
}


/**
* build recursively the whole groups tree
*
*/

function system_build_tree($typetree,$workspaces,$groups, $from_wid = 0, $from_gid = 0, $str = '') {
	global $scriptenv;
	//global $workspaces;
	//global $groups;
	global $workspaceid;
	global $groupid;

	$html = '';
	if (!empty($workspaceid) && $workspaceid>0) $workspacesel = $workspaces['list'][$workspaceid];
	if (!empty($groupid) && $groupid>0) {
		$groupsel = $groups['list'][$groupid];
		$groupworksel=$groupsel['id_workspace'];
	}
	else $groupworksel=0;

	switch($typetree) {
		case 'workspaces':

			if ($from_wid == 0) $from_wid = 1;

			$html = '';

			if (isset($workspaces['tree'][$from_wid])) {
				$c=0;
				foreach($workspaces['tree'][$from_wid] as $wid) {
					$workspace = $workspaces['list'][$wid];
					$isworkspacesel = (!empty($workspaceid) && ($workspaceid == $wid));
					$gselparents=array();
					if (isset($workspacesel)) {
						if (isset($workspacesel['parents'])) $gselparents=explode(';',$workspacesel['parents']);
					}
					else {
						if (isset($groupsel['parents_workspace'])) {
							$gselparents=explode(';',$groupsel['parents_workspace']);
						}
					}

					$testparents = explode(';',$workspace['parents']);
					if (isset($workspacesel)) $testparents[] = $workspace['id'];

					// workspace opened if parents array intersects
					$isworkspaceopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
					// test si dernier element = workspacecourant
					if ($isworkspaceopened) {
						$islastworkspacesel=($gselparents[sizeof($gselparents)-1]==$from_wid);
						if ($islastworkspacesel) $isworkspaceopened=false;

						if  ($groupworksel==$wid) $isworkspaceopened=true;
					}
					else $islastworkspacesel=false;


					//$islast = (!isset($workspaces['tree'][$from_wid]) || $c == sizeof($workspaces['tree'][$from_wid])-1);
					$islast = ((!isset($workspaces['tree'][$from_wid]) || $c == sizeof($workspaces['tree'][$from_wid])-1) && !isset($groups['workspace_tree'][$from_wid]));

					$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
					$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

					if ($isworkspacesel) $style_sel = 'bold';
					else $style_sel = 'none';

					$icon = ($workspace['web']) ? 'workspace-web' : 'workspace';
					$new_str = ' '; // decalage pour les noeuds suivants

					if (!empty($str)) {
						if (!$islast) $new_str = $str.'(s)'; // |
						else $new_str = $str.'(b)';  // (vide)

						$link_div ="<a onclick=\"javascript:system_showgroup('workspaces', '{$wid}', '{$new_str}');\" href=\"#\">";

						$last = 'joinbottom';
						if ($islast) $last = 'join';
						if (isset($workspaces['tree'][$wid])) {
							if ($islast) {
								if ($isworkspacesel || $isworkspaceopened) $last = 'minus';
								else $last = 'plus';
							}
							else {
								if ($isworkspacesel || $isworkspaceopened) $last = 'minusbottom';
								else $last = 'plusbottom';
							}
						}
						$decalage .= "<div style=\"float:left;\" id=\"w{$workspace['id']}_plus\">{$link_div}<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></a></div>";
					}

					$link = "<a style=\"font-weight:{$style_sel}\" href=\"admin.php?workspaceid={$workspace['id']}\">";

					$html_rec = '';
					if ($isworkspacesel || $isworkspaceopened || $workspace['depth'] == 2)  $html_rec .= system_build_tree('workspaces',$workspaces,$groups, $wid, 0, $new_str);

					$display = ($html_rec == '') ? 'none' : 'block';

					$html .=	"
								<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
									<div style=\"float:left;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;</div>
									{$link}".dims_strcut($workspace['label'],40)."</a>
								</div>
								<div style=\"clear:left;display:$display;\" id=\"w{$workspace['id']}\" style=\"padding:0px;\">$html_rec</div>
								";

					$c++;
				}
			}

			if (isset($groups['workspace_tree'][$from_wid])) {
				$c=0;
				foreach($groups['workspace_tree'][$from_wid] as $gid) {
					$group = $groups['list'][$gid];

					$isgroupsel = (!empty($groupid) && ($groupid == $gid));

					$gselparents = (isset($groupsel)) ? explode(';',$groupsel['parents']) : array();
					$testparents = explode(';',$group['parents']);
					$testparents[] = $group['id'];

					// group opened if parents array intersects
					$isgroupopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
					$islast = (!isset($groups['workspace_tree'][$from_wid]) || $c == sizeof($groups['workspace_tree'][$from_wid])-1);

					$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
					$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

					if ($isgroupsel) $style_sel = 'bold';
					else $style_sel = 'none';

					$icon = 'group';
					$new_str = ' '; // decalage pour les noeuds suivants

					if (!empty($str)) {
						if (!$islast) $new_str = $str.'(s)'; // |
						else $new_str = $str.'(b)';  // (vide)

						$link_div ="<a onclick=\"javascript:system_showgroup('groups', '{$gid}','{$new_str}');\" href=\"#\">";

						$last = 'joinbottom';
						if ($islast) $last = 'join';
						if (isset($groups['tree'][$gid])) {
							if ($islast) {
								if ($isgroupsel || $isgroupopened) $last = 'minus';
								else $last = 'plus';
							}
							else {
								if ($isgroupsel || $isgroupopened) $last = 'minusbottom';
								else $last = 'plusbottom';
							}
						}
						$decalage .= "<div style=\"float:left;\" id=\"g{$group['id']}_plus\">{$link_div}<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></a></div>";

					}

					$link = "<a style=\"font-weight:{$style_sel}\" href=\"admin.php?groupid={$group['id']}\">";

					$html_rec = '';
					if ($isgroupsel || $isgroupopened || $group['depth'] == 2) $html_rec = system_build_tree('groups',$workspaces,$groups, 0, $gid, $new_str);

					$display = ($html_rec == '') ? 'none' : 'block';

					$html .=	"
								<div style=\"clear:left;\" style=\"padding:0px;line-height:16px;\">
									<div style=\"float:left;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;</div>
									{$link}".dims_strcut($group['label'],40)."</a>
								</div>
								<div style=\"clear:left;display:$display;\" id=\"g{$group['id']}\" style=\"padding:0px;\">$html_rec</div>
								";
					$c++;
				}
			}

		break;

		case 'groups':
			if ($from_gid == 0) $from_gid = 1;

			if (!empty($groupid)) $groupsel = $groups['list'][$groupid];

			if (isset($groups['tree'][$from_gid])) {
				$c=0;
				foreach($groups['tree'][$from_gid] as $gid) {
					$group = $groups['list'][$gid];
					if (!$group['id_workspace']) {
						$isgroupsel = (!empty($groupid) && ($groupid == $gid));

						$gselparents = (isset($groupsel)) ? explode(';',$groupsel['parents']) : array();
						$testparents = explode(';',$group['parents']);
						$testparents[] = $group['id'];

						// group opened if parents array intersects
						$isgroupopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
						$islast = (!isset($groups['tree'][$from_gid]) || $c == sizeof($groups['tree'][$from_gid])-1);

						$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
						$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

						if ($isgroupsel) $style_sel = 'bold';
						else $style_sel = 'none';

						$icon = 'group';
						$new_str = ' '; // decalage pour les noeuds suivants

						if (!empty($str)) {
							if (!$islast) $new_str = $str.'(s)'; // |
							else $new_str = $str.'(b)';  // (vide)

							$link_div ="<a onclick=\"javascript:system_showgroup('groups', '{$gid}', '{$new_str}');\" href=\"#\">";

							$last = 'joinbottom';
							if ($islast) $last = 'join';
							if (isset($groups['tree'][$gid])) {
								if ($islast) {
									if ($isgroupsel || $isgroupopened) $last = 'minus';
									else $last = 'plus';
								}
								else {
									if ($isgroupsel || $isgroupopened) $last = 'minusbottom';
									else $last = 'plusbottom';
								}
							}
							$decalage .= "<div style=\"float:left;\" id=\"g{$group['id']}_plus\">{$link_div}<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></a></div>";

						}

						$link = "<a style=\"font-weight:{$style_sel}\" href=\"".dims_urlencode("admin.php?groupid=".$group['id'])."\">";

						$html_rec = '';
						if ($isgroupsel || $isgroupopened || $group['depth'] == 2) $html_rec = system_build_tree('groups',$workspaces,$groups, 0, $gid, $new_str);

						$display = ($html_rec == '') ? 'none' : 'block';

						$html .=	"
									<div style=\"clear:left;\" style=\"padding:0px;line-height:16px;\">
										<div style=\"float:left;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;</div>
										{$link}".dims_strcut($group['label'],40)."</a>
									</div>
									<div style=\"clear:left;display:$display;\" id=\"g{$group['id']}\" style=\"padding:0px;\">$html_rec</div>
									";
						$c++;
					}
				}
			}
		break;
	}

	return $html;
}

function system_getallgroups($idgrouptop = '')
{
	$db = dims::getInstance()->getDb();
	$groups = array();

	$select = "SELECT * FROM dims_group WHERE system = 0 ORDER BY label";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result))
	{
		$groups[$fields['id_group']][$fields['id']] = $fields;
	}

	$ar = array();
	$depth = system_getallgroupsrec($ar, $groups, dims_const::_DIMS_SYSTEMGROUP, 0, $idgrouptop);
	return($ar);
}



function system_getallworkspaces($idworkspacetop = '')
{
	$db = dims::getInstance()->getDb();
	$workspaces = array();

	$select = "SELECT * FROM dims_workspace WHERE system = 0 ORDER BY label";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result))
	{
		$workspaces[$fields['id_workspace']][$fields['id']] = $fields;
	}

	$ar = array();
	$depth = system_getallworkspacesrec($ar, $workspaces, dims_const::_DIMS_SYSTEMGROUP, 0, $idworkspacetop);
	return($ar);
}

function system_getallgroupsrec(&$ar, $groups, $idgroup, $depthlimit, $idgrouptop, $depth=1, $line=0, $fullpath='')
{
	$depthmax = $depth;

	if (array_key_exists($idgroup,$groups))
	{
		foreach($groups[$idgroup] as $fields)
		{
			if ($fields['id'] != $idgroup)
			{
				if (!$idgrouptop || $fields['id'] != $idgrouptop)
				{
					$c=count($ar);

					$parents = $fields['parents'];

					$fullpath_group = '';
					if ($fullpath=='') $fullpath_group = dims_strcut($fields['label'], 20);
					else $fullpath_group = $fullpath.' | '. dims_strcut($fields['label'], 20);

					$ar[$c] = array(
					'depth'=>$depth,
					'idparent'=>$idgroup,
					'id'=>$fields['id'],
					'label'=>$fields['label'],
					'fullpath'=>$fullpath_group,
					'line'=>++$line,
					'parents'=>$parents
					);

					if ($depth < $depthlimit || !$depthlimit)
					{
						$depthgroup = system_getallgroupsrec($ar, $groups, $fields['id'], $depthlimit, $idgrouptop, $depth+1, $line, $ar[$c]['fullpath']);
						if ($depthgroup > $depthmax) $depthmax = $depthgroup;
					}
				}
			}
		}
	}

	return $depthmax;
}

function system_getallgroupsreclite(&$ar, $groups, $idgroup, $depthlimit, $idgrouptop, $depth=1)
{
	$db = dims::getInstance()->getDb();

	$depthmax = $depth;

	if (array_key_exists($idgroup,$groups))
	{
		foreach($groups[$idgroup] as $fields)
		{
			if ($fields['id'] != $idgroup)
			{
				if (!$idgrouptop || $fields['id'] != $idgrouptop)
				{
					$ar[] = $fields['id'];

					if ($depth < $depthlimit || !$depthlimit)
					{
						$depthgroup = system_getallgroupsreclite($ar, $groups, $fields['id'], $depthlimit, $idgrouptop, $depth+1);
						if ($depthgroup > $depthmax) $depthmax = $depthgroup;
					}
				}
			}
		}
	}
	return $depthmax;
}


/* new functions for workspaces */

function system_getallworkspacesrec(&$ar, $workspaces, $idworkspace, $depthlimit, $idworkspacetop, $depth=1, $line=0, $fullpath='')
{
	$depthmax = $depth;

	if (array_key_exists($idworkspace,$workspaces))
	{
		foreach($workspaces[$idworkspace] as $fields)
		{
			if ($fields['id'] != $idworkspace)
			{
				if (!$idworkspacetop || $fields['id'] != $idworkspacetop)
				{
					$c=count($ar);

					$parents = $fields['parents'];

					$fullpath_workspace = '';
					if ($fullpath=='') $fullpath_workspace = dims_strcut($fields['label'], 20);
					else $fullpath_workspace = $fullpath.' | '. dims_strcut($fields['label'], 20);

					$ar[$c] = array(
					'depth'=>$depth,
					'idparent'=>$idworkspace,
					'id'=>$fields['id'],
					'label'=>$fields['label'],
					'fullpath'=>$fullpath_workspace,
					'line'=>++$line,
					'parents'=>$parents
					);

					if ($depth < $depthlimit || !$depthlimit)
					{
						$depthworkspace = system_getallworkspacesrec($ar, $workspaces, $fields['id'], $depthlimit, $idworkspacetop, $depth+1, $line, $ar[$c]['fullpath']);
						if ($depthworkspace > $depthmax) $depthmax = $depthworkspace;
					}
				}
			}
		}
	}

	return $depthmax;
}


function system_getallworkspacesreclite(&$ar, $workspaces, $idworkspace, $depthlimit, $idworkspacetop, $depth=1)
{
	$db = dims::getInstance()->getDb();

	$depthmax = $depth;

	if (array_key_exists($idworkspace,$workspaces))
	{
		foreach($workspaces[$idworkspace] as $fields)
		{
			if ($fields['id'] != $idworkspace)
			{
				if (!$idworkspacetop || $fields['id'] != $idworkspacetop)
				{
					$ar[] = $fields['id'];

					if ($depth < $depthlimit || !$depthlimit)
					{
						$depthworkspace = system_getallworkspacesreclite($ar, $workspaces, $fields['id'], $depthlimit, $idworkspacetop, $depth+1);
						if ($depthworkspace > $depthmax) $depthmax = $depthworkspace;
					}
				}
			}
		}
	}
	return $depthmax;
}

function system_getNballworkspacesreclite(&$nb, $workspaces, $idworkspace, $depthlimit, $idworkspacetop, $depth=1) {
	$db = dims::getInstance()->getDb();

	if (array_key_exists($idworkspace,$workspaces)) {
		foreach($workspaces[$idworkspace] as $fields) {
			if ($fields['id'] != $idworkspace) {
				if (!$idworkspacetop || $fields['id'] != $idworkspacetop) {
					$nb++;
					if ($depth < $depthlimit || !$depthlimit) {
						$depthworkspace = system_getNballworkspacesreclite($nb, $workspaces, $fields['id'], $depthlimit, $idworkspacetop, $depth+1);
						if ($depthworkspace > $depthmax) $depthmax = $depthworkspace;
					}
				}
			}
		}
	}
}


/********************************/


function system_updateparents($idgroup=0,$parents='',$depth=1)
{
	$db = dims::getInstance()->getDb();

	$select = "SELECT * FROM dims_group WHERE id_group = :idgroup AND id <> :idgroup";
	$result = $db->query($select, array(
		':idgroup' => $idgroup
	));

	if ($parents!='') $parents .= ';';
	$parents .= $idgroup;

	while ($fields = $db->fetchrow($result))
	{
		$update = "UPDATE dims_group SET parents = :parents , depth = :depth WHERE id = :id ";
		$res=$db->query($update, array(
			':parents'	=> $parents,
			':depth'	=> $depth,
			':id'		=> $fields['id']
		));
		system_updateparents($fields['id'],$parents,$depth+1);
	}
}

function system_getinstalledmodules()
{
	$db = dims::getInstance()->getDb();

	$modules = array();

	$select = 	"
				SELECT 		*
				FROM 		dims_module_type
				WHERE		system != 1
				ORDER BY 	label
				";

	$result = $db->query($select);

	$i = 0;

	while ($moduletype = $db->fetchrow($result))
	{
		$modules[$moduletype['id']] = $moduletype;
	}

	return $modules;
}



function system_generate_htpasswd($login, $pass, $delete = false)
{
	$content = '';
	$res = '';

	if (file_exists('.htpasswd') && is_readable('.htpasswd'))
	{
		if ($handle = fopen('.htpasswd', 'r'))
		{
			while (!feof($handle)) $content .= fgets($handle, 4096);
			fclose($handle);
		}
	}

	if (is_writable('.'))
	{
		$handle = fopen('.htpasswd', 'w');

		$array_content = split("\r\n", $content);

		$array_pass = array();
		foreach($array_content as $line_content)
		{
			if (trim($line_content) != '')
			{
				list($ht_login, $ht_pass) = split(":", $line_content);
				$array_pass[$ht_login] = $ht_pass;
			}
		}

		if ($delete && isset($array_pass[$login])) unset($array_pass[$login]);
		else $array_pass[$login] = dims_htpasswd($pass);

		$c = 0;
		foreach($array_pass as $ht_login => $ht_pass)
		{
			if ($c++) $res .= "\r\n";
			$res .= "$ht_login:$ht_pass";
		}

		fwrite($handle, $res);
	}
}


// fonction permettant la v�rification de l'ensemble des fils d'un group passe en parametre
function system_verifyuser_groupsrules($objuser,$id_group,$groups,$usergroup,$listrules)
{
	$db = dims::getInstance()->getDb();

	// on s�lectionne que les fils du group pere $id_group
	//$result = $db->query("select * from dims_group where id_group=".$id_group);

	//while ($group = $db->fetchrow($result))
	foreach ($groups as $group)
	{
		// v�rification de l'appartenance de id_user dans le groupe id_group

		//$usergroup = "SELECT * FROM dims_group_user WHERE id_group =".$group['id']." and id_user=".$objuser->fields['id'];

		$continue=1;

		//$resusergroup = $db->query($usergroup);
		//if ($rule = $db->fetchrow($resusergroup))

		if (isset($usergroup[$group['id']][$objuser->fields['id']]))
		{
		 $isnew=0;
		 $continue=system_verifyusergroup($objuser,$group['id'],0,$listrules);
		}
		else
		{
		 $isnew=1;
		 $continue=system_verifyusergroup($objuser,$group['id'],0,$listrules);
		}
		//echo "group: ".$group['id']." - continue ".$continue." isnew ".$isnew;

		if ($continue)
		{
			//echo $group['id']."<br>";
			// on verifie l'attribution d'un profil au user courant

			//$id_profile=system_verifyusergroupprofile($objuser,$group['id'],!$isnew,$listrules);
			$id_profile=0;
			//echo " - profil ".$id_profile;
			//on continue, les r�gles sont valides, on v�rifie d�ja l'appartenance
			if ($isnew)
			{
			 	// il n'existe pas d'attachement, on le cree
			 	$objuser->attachtogroup($group['id'],$id_profile,0);
			}
			else
			{
				$res=$db->query("UPDATE dims_group_user set id_profile= :idprofile where id_group= :idgroup and id_user= :iduser ", array(
					':idprofile'	=> $id_profile,
					':idgroup'		=> $group['id'],
					':iduser'		=> $objuser->fields['id']
				));
			}


			// on rappelle la fonction sur ce groupe courant pour traiter les fils
			//system_verifyuser_groupsrules($objuser,$group['id'],$usergroup,$listrules);

		} // fin de $continue
		else
		{
			// on detache si il etait attache
			if (!$isnew)
				$objuser->detachfromgroup($group['id']);
		}

		//echo "<br>";
	}// fin de la boucle sur les groupes fils de id_group

}// fin de system_verifyuser_groupsrules



function system_verifyusergroup($objuser,$id_group,$persistent,$listrules)
{
	$db = dims::getInstance()->getDb();
	$continue=1;
	$valreturn=0;

	// on recupere l'ensemble des r�gles appliqu�es � ce groupe
	//$select = "SELECT * FROM dims_rule WHERE id_group = $id_group and id_type=1  order by position";
	//$result = $db->query($select);

	//if ($db->numrows()==0)
	if (!isset($listrules[1][$id_group]))
	{
	 return($continue);
	}

	//while (($rule = $db->fetchrow($result)))

	foreach ($listrules[1][$id_group] as $rule)
	{
		// on va v�rifier l'ensemble des r�gles du groupe

		//$valreturn=system_verifyuser_rule($objuser,$rule);
		$continue=$continue & system_verifyuser_rule($objuser,$rule);
		//echo "rule:".$rule['id']." -> ".$valreturn."<Br>";
		//if ($valreturn) $continue=1;
	}

	return $continue;
}


// fonction permettant l'attribution automatique d'un profil � une personne
function system_verifyusergroupprofile($objuser,$id_group,$persistent,$listrules)
{
	$db = dims::getInstance()->getDb();
	$continue=0;
	$id_profile=0;


	// on recupere l'ensemble des r�gles appliqu�es � ce groupe
	//$select = "SELECT * FROM dims_rule WHERE id_group = $id_group and id_type=2 order by position";
	//$result = $db->query($select);

	//while ($rule = $db->fetchrow($result))
	foreach ($listrules[2][$id_group] as $rule)
	{
		// on va v�rifier l'ensemble des r�gles du groupe
		$continue=system_verifyuser_rule($objuser,$rule);
		//echo "rule:".$rule['id']." profile:".$rule['id_profile']." -> ".$continue."<Br>";
		if ($continue) $id_profile=$rule['id_profile'];
	}

	return $id_profile;
}


// fonction permettant la v�rification d'une r�gle de gestion sur le user courant
function system_verifyuser_rule($objuser,$rule)
{
	$res=0;

	switch($rule['operator'])
	{
		case '=':
			if ($objuser->fields[$rule['field']]==$rule['value']) $res=1;
			break;
		case '!=':
			if ($objuser->fields[$rule['field']]!=$rule['value']) $res=1;
			break;
		case '>=':
			if ($objuser->fields[$rule['field']]>=$rule['value']) $res=1;
			break;
		case '<=':
			if ($objuser->fields[$rule['field']]<=$rule['value']) $res=1;
			break;
		case '>':
			if ($objuser->fields[$rule['field']]>$rule['value']) $res=1;
			break;
		case '<':
			if ($objuser->fields[$rule['field']]<$rule['value']) $res=1;
			break;
	}// end switch

	return $res;
}



function system_getsharevalue(&$tabshare,$moduleid,$type_object="",$idgroup="",&$listworkspacegroup,&$listworkspacegroupauth)
{
	$db = dims::getInstance()->getDb();

	if ($type_object=="")
		$res=$db->query("SELECT * from dims_share where id_module= :moduleid ", array(
			':moduleid' => $moduleid
		));
	else
		$res=$db->query("SELECT * from dims_share where id_module= :moduleid and id_object= :typeobject ", array(
			':moduleid'		=> $moduleid,
			':typeobject'	=> $typeobject
		));


	if ($idgroup=="") $idgroup=$_SESSION["dims"]["groupid"];

	while ($fields=$db->fetchrow($res))
	{
		$tabshare[$fields['id_object']][$fields['id_record']][$fields['type_share']."_".$fields['id_share']]=1;
	}

	// construction de la liste d'appartenance des groupes
	// 01/07/2006 00H05
	// bug corrige car pas seulement sur la liste des groupes contenant le user courant mais tous ceux du groupe de travail

	$sql="SELECT id_org from dims_workspace_group as o,dims_group_user as u where o.id_group= :idgroup
		 and u.id_group=o.id_org";

	$res=$db->query($sql, array(
		':idgroup' => $idgroup
	));

	while ($fields=$db->fetchrow($res))
	{
		$listworkspacegroup[$fields['id_org']]=$fields['id_org'];
	}

	$sql="SELECT id_org from dims_workspace_group as o,dims_group_user as u where o.id_group= :idgroup
		 and u.id_group=o.id_org and id_user= :userid ";

	$resu=$db->query($sql, array(
		':idgroup'	=> $_SESSION["dims"]["groupid"],
		':userid'	=> $_SESSION["dims"]["userid"]
	));

	while ($fieldsu=$db->fetchrow($resu))
	{
		$listworkspacegroupauth[$fieldsu['id_org']]=$fieldsu['id_org'];
	}

}

function system_verifshare($tabshare,$idobject,$idrecord,$listworkspacegroupauth)
{
 $res=0;
 $iduser=$_SESSION["dims"]["userid"];
 $idgroup=$_SESSION["dims"]["groupid"];

/*
if ($idrecord==301)
{
 dims_print_r($tabshare[$idobject][$idrecord]);
 dims_print_r($listworkspacegroupauth);
echo $idgroup;
}
*/
 // v�rification de l'existence de droit sur l'element courant
 if (isset($tabshare[$idobject][$idrecord]))
 {

	$elem=$tabshare[$idobject][$idrecord];

	// test si droit sur user
	if (isset($elem['user'."_".$iduser])) $res=1;

	// test de droit sur all
	if (isset($elem['all'."_".$idgroup])) $res=2;

	// test group de travail $_SESSION["dims"]["groupid"]
	if (isset($elem['work'."_".$_SESSION["dims"]["groupid"]])) $res=3;

	// test sur le droit du groupe d'organisation autoris�

	if (isset($listworkspacegroupauth))
	{
		foreach($listworkspacegroupauth as $numorg)
		{
			if (isset($elem['org'."_".$numorg])) $res=4;

			if (isset($elem['all'."_".$numorg])) $res=4;

		}
	}
 }
 else
	$res=1;


 return $res;
}

function system_tickets_displayresponses($parents, $tickets, $rootid) {
	global $skin;
	global $scriptenv;
	global $dims;
	global $_DIMS;
	sort($parents[$rootid]);

	$todaydate = dims_timestamp2local(dims_createtimestamp());

	foreach($parents[$rootid] as $ticketid) {
		$fields = $tickets[$ticketid];
		$localdate = dims_timestamp2local($fields['timestp']);
		$localdate['date'] = ($todaydate['date'] == $localdate['date'])  ? "Aujourd'hui" : "le {$localdate['date']}";

		$puce = '#ff2020';
		/*
		if (!$fields['opened']) $puce = '#ff2020';
		elseif (!$fields['done']) $puce = '#2020ff';
		else $puce = '#20ff20';
		*/
		?>

		<div class="system_tickets_head" style="margin-top: 4px;width:90%;">
		    <?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']." <strong>";

				echo $fields['firstname']." ".$fields['lastname'];

		    	echo "</strong> ".$_DIMS['cste']['_AT']." <strong>";

		    	echo $localdate['date']."</strong> ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$localdate['time'];

		    	echo "<br>".dims_make_links(dims_nl2br($fields['message']));
		    ?>

		    </div>

			<div>
			<?php
				if (isset($parents[$ticketid])) system_tickets_displayresponses($parents, $tickets, $ticketid);
			?>
			</div>
		</div>
		<?php
	}
}

function CreateSubstArrays($subststrings) {
$tabEncode = array();

foreach($subststrings as $subststring) {
    $tempArray = explode(',',$subststring);

    $tabEncode['str'] = '';
    $tabEncode['tr'] = '';
    $tabEncode['char'] = array();
    $tabEncode['ereg'] = array();

    foreach ($tempArray as $tempSubstitution)
    {
         $chrs = explode(':',$tempSubstitution);
         $tabEncode['char'][strtolower($chrs[0])] = strtolower($chrs[0]);
         for($i=0; $i < strlen($chrs[1]); $i++)
         {
             $tabEncode['str'] .= $chrs[1][$i];
             $tabEncode['tr']  .= $chrs[0];
         }
    }
}
return $tabEncode;
}

/**
* build recursively the whole groups tree
*
*/


function system_build_tree_domain($workspaces,$selectedworkspaces,$from_wid = 0, $from_gid = 0, $str = '') {
	global $scriptenv;

	$html = '';

	if ($from_wid == 0) $from_wid = 1;

	$html = '';

	if (isset($workspaces['tree'][$from_wid])) {
		$c=0;
		foreach($workspaces['tree'][$from_wid] as $wid) {
			$workspace = $workspaces['list'][$wid];
			$isworkspacesel = (!empty($workspaceid) && ($workspaceid == $wid));
			$gselparents = array();

			if (isset($workspacesel)) {
				if (isset($workspacesel['parents'])) $gselparents=$workspacesel['parents'];
			}
			else {
				if (isset($groupsel['parents_workspace'])) $gselparents=$groupsel['parents_workspace'];
			}

			$testparents = explode(';',$workspace['parents']);
			if (isset($workspacesel)) $testparents[] = $workspace['id'];

			// workspace opened if parents array intersects
			$isworkspaceopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
			$islast = ((!isset($workspaces['tree'][$from_wid]) || $c == sizeof($workspaces['tree'][$from_wid])-1) && !isset($groups['workspace_tree'][$from_wid]));
			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

			if ($isworkspacesel) $style_sel = 'bold';
			else $style_sel = 'none';

			$icon = ($workspace['web']) ? 'workspace-web' : 'workspace';
			$new_str = ' '; // decalage pour les noeuds suivants

			if (!empty($str)) {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)

				$link_div ="<a onclick=\"javascript:system_showgroup('workspaces', '{$wid}', '{$new_str}');\" href=\"#\">";

				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($workspaces['tree'][$wid])) {
					if ($islast) {
						$last = 'minus';
					}
					else {
						$last = 'minusbottom';
					}
				}
				$decalage .= "<div style=\"float:left;\" id=\"w{$workspace['id']}_plus\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></div>";
			}

			$html_rec = system_build_tree_domain($workspaces,$selectedworkspaces, $wid, 0, $new_str);

			$display = ($html_rec == '') ? 'none' : 'block';

			if (in_array($wid,$selectedworkspaces)) $check="checked";
			else $check="";

			$selectedw="<input type=\"checkbox\" name=\"selwork[]\" $check value=\"".$wid."\">&nbsp;";

			$html .=	"
						<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
							<div style=\"float:left;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;
							".$selectedw.dims_strcut($workspace['label'],25)."</div>
						</div>
						<div style=\"clear:left;display:$display;\" id=\"w{$workspace['id']}\" style=\"padding:0px;\">$html_rec</div>
						";
			$c++;
		}
	}


	return $html;
}

function system_build_tree_stats($workspaces,$selectedworkspaces,$from_wid = 0, $from_gid = 0, $str = '') {
	global $scriptenv;
	global $_DIMS;
	$html = '';

	if ($from_wid == 0) $from_wid = 1;

	$html = '';

	if (isset($workspaces['tree'][$from_wid])) {
		$c=0;
		foreach($workspaces['tree'][$from_wid] as $wid) {
			$workspace = $workspaces['list'][$wid];
			$isworkspacesel = (!empty($workspaceid) && ($workspaceid == $wid));
			$gselparents = array();

			if (isset($workspacesel)) {
				if (isset($workspacesel['parents'])) $gselparents=$workspacesel['parents'];
			}
			else {
				if (isset($groupsel['parents_workspace'])) $gselparents=$groupsel['parents_workspace'];
			}

			$testparents = explode(';',$workspace['parents']);
			if (isset($workspacesel)) $testparents[] = $workspace['id'];

			// workspace opened if parents array intersects
			$isworkspaceopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
			$islast = ((!isset($workspaces['tree'][$from_wid]) || $c == sizeof($workspaces['tree'][$from_wid])-1) && !isset($groups['workspace_tree'][$from_wid]));
			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

			if ($isworkspacesel) $style_sel = 'bold';
			else $style_sel = 'none';

			$icon = ($workspace['web']) ? 'workspace-web' : 'workspace';
			$new_str = ' '; // decalage pour les noeuds suivants

			if (!empty($str)) {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)

				$link_div ="<a onclick=\"javascript:system_showgroup('workspaces', '{$wid}', '{$new_str}');\" href=\"#\">";

				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($workspaces['tree'][$wid])) {
					if ($islast) {
						$last = 'minus';
					}
					else {
						$last = 'minusbottom';
					}
				}
				$decalage .= "<div style=\"float:left;\" id=\"w{$workspace['id']}_plus\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></div>";
			}

			$html_rec = system_build_tree_stats($workspaces,$selectedworkspaces, $wid, 0, $new_str);

			$display = ($html_rec == '') ? 'none' : 'block';

			if (in_array($wid,$selectedworkspaces)) $check="checked";
			else $check="";

			$selectedw="&nbsp;";

			$html .=	"
						<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
							<div style=\"float:left;width:33%;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;
							".$selectedw.dims_strcut($workspace['label'],45)."</div>
						</div>";

			$currentwork = new workspace();
			$currentwork->open($workspace['id']);
			$groups=$currentwork->getgroups();
			$users=$currentwork->getusers();

			$style=($i%2) ? 'trl1' : 'trl2';
			$nbgroup=sizeof($groups);

			$html .=	"<div style=\"float:left;width:20%;\">";

			if ($nbgroup<=1) $html.= "&nbsp;".$nbgroup." ".$_DIMS['cste']['_GROUP'];
			else $html .= $nbgroup." ".$_DIMS['cste']['_GROUP']."s";

			$html.="</div><div>";

			$nbuser=sizeof($users);

			if ($nbuser<=1) $html.= "&nbsp;".$nbuser." ".$_DIMS['cste']['_DIMS_LABEL_USER'];
			else $html .= $nbuser." ".$_DIMS['cste']['_USERS'];

			$html.="</div>";

			$html .=	"</table></div>
						<div style=\"clear:left;display:$display;\" id=\"w{$workspace['id']}\" style=\"padding:0px;\">$html_rec</div>
						";
			$c++;
		}
	}


	return $html;
}


function system_build_tree_workspace_share($workspaces,$selectedworkspaces,$needworkspaces,$notneedworkspaces,$id_workspace,$id_object,$from_wid = 0, $from_gid = 0, $str = '') {
	global $scriptenv;

	$html = '';

	if ($from_wid == 0) $from_wid = 1;

	$html = '';

	if (isset($workspaces['tree'][$from_wid])) {
		$c=0;
		foreach($workspaces['tree'][$from_wid] as $wid) {
			$workspace = $workspaces['list'][$wid];
			$isworkspacesel = (!empty($workspaceid) && ($workspaceid == $wid));
			$gselparents = array();

			if (isset($workspacesel)) {
				if (isset($workspacesel['parents'])) $gselparents=$workspacesel['parents'];
			}
			else {
				if (isset($groupsel['parents_workspace'])) $gselparents=$groupsel['parents_workspace'];
			}

			$testparents = explode(';',$workspace['parents']);
			if (isset($workspacesel)) $testparents[] = $workspace['id'];

			// workspace opened if parents array intersects
			$isworkspaceopened = sizeof(array_intersect($gselparents, $testparents)) == sizeof($testparents);
			$islast = ((!isset($workspaces['tree'][$from_wid]) || $c == sizeof($workspaces['tree'][$from_wid])-1) && !isset($groups['workspace_tree'][$from_wid]));
			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/empty.png\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/line.png\"></div>", $decalage);

			if ($isworkspacesel) $style_sel = 'bold';
			else $style_sel = 'none';

			$icon = ($workspace['web']) ? 'workspace-web' : 'workspace';
			$new_str = ' '; // decalage pour les noeuds suivants

			if (!empty($str)) {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)

				$link_div ="<a onclick=\"javascript:system_showgroup('workspaces', '{$wid}', '{$new_str}');\" href=\"#\">";

				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($workspaces['tree'][$wid])) {
					if ($islast) {
						$last = 'minus';
					}
					else {
						$last = 'minusbottom';
					}
				}
				$decalage .= "<div style=\"float:left;\" id=\"w{$workspace['id']}_plus\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$last}.png\"></div>";
			}

			$html_rec = system_build_tree_workspace_share($workspaces, $selectedworkspaces, $needworkspaces,$notneedworkspaces,$id_workspace,$id_object, $wid, 0, $new_str);

			$display = ($html_rec == '') ? 'none' : 'block';

			if (in_array($wid,$selectedworkspaces)) {
				$check="checked";
				$value=0;
			}
			else {
				$check="";
				$value=1;
			}

			if (isset($notneedworkspaces[$wid])) $disa=" disabled ";
			else $disa="";
			$selectedw="<input type=\"checkbox\" $disa name=\"selwork[]\" $check value=\"".$wid."\" onclick=\"javascript:updateWorkspaceShareObject(".$id_workspace.",".$id_object.",".$wid.",".$value.",0);\">&nbsp;";

			if (isset($needworkspaces[$wid])) $selectedw.="<img src=\"./common/img/checkdo.png\" alt=\"\">";

			$html .=	"
						<div style=\"clear:left;\" style=\"padding:0px;height:16px;line-height:16px;\">
							<div style=\"float:left;\">{$decalage}<img src=\"{$_SESSION['dims']['template_path']}/img/system/treeview/{$icon}.png\">&nbsp;
							".$selectedw.dims_strcut($workspace['label'],25)."</div>
						</div>
						<div style=\"clear:left;display:$display;\" id=\"w{$workspace['id']}\" style=\"padding:0px;\">$html_rec</div>
						";
			$c++;
		}
	}


	return $html;
}

function CleanHtml($text) {
//global $htmlspec;

//replace blank characters by spaces
//$text = ereg_replace("[\r\n\t]+"," ",$text);
$text = ereg_replace("[*{}()\"\r\n\t_\-]+"," ",$text);

//extracts title
if (preg_match('/< *title *>(.*?)< *\/ *title *>/is',$text,$regs)) {
    $title = trim($regs[1]);
}
else {
    $title = "";
}

//delete content of head, script, and style tags
$text = ereg_replace("<head[^>]*>.*</head>"," ",$text);
//$text = eregi_replace("<script[^>]*>.*</script>"," ",$text); // more conservative
$text = preg_replace("/<script[^>]*?>.*?<\/script>/is"," ",$text); // less conservative
$text = ereg_replace("<style[^>]*>.*</style>"," ",$text);
// clean tags
$text = preg_replace("/<[\/\!]*?[^<>]*?>/is"," ",$text);

/*
// first case-sensitive and then case-insensitive
//tries to replace  by ascii equivalent
foreach ($htmlspec as $entity => $char) {
      $text = ereg_replace ($entity."[;]?",$char,$text);
      $title = ereg_replace ($entity."[;]?",$char,$title);
}
//tries to replace  by ascii equivalent
foreach ($htmlspec as $entity => $char) {
      $text = eregi_replace ($entity."[;]?",$char,$text);
      $title = eregi_replace ($entity."[;]?",$char,$title);
}
*/
/*
while (eregi('&#([0-9]{3});',$text,$reg)) {
    $text = str_replace($reg[0],chr($reg[1]),$text);
}
while (eregi('&#x([a-f0-9]{2});',$text,$reg)) {
    $text = str_replace($reg[0],chr(base_convert($reg[1],16,10)),$text);
}
*/
//replace foo characters by space
//$text = ereg_replace("[*{}()\"\r\n\t_]+"," ",$text);
$text = ereg_replace("<[^>]*>"," ",$text);
$text = ereg_replace("(\r|\n|\r\n)"," ",$text);

// replace any stranglers by space
$text = ereg_replace("( -> | <- | > | < )"," ",$text);

//strip characters used in highlighting with no space
//$text = str_replace("^#_","",str_replace("_#^","",$text));
$text = str_replace("@@@","",str_replace("@#@","",$text));

$text = ereg_replace("[[:space:]]+"," ",$text);

return $text." ".$title;
}


function EpureText($text,$min_word_length=2) {
global $words_chars;
global $tabencoding;

$text=strtr(strtolower($text),$tabencoding['str'],$tabencoding['tr']);
$text = strtr( $text,'��','��');

$text = ereg_replace('[^'.$words_chars.' \'._~@#$:&%/;,=-]+',' ',$text);
$text = ereg_replace('(['.$words_chars.'])[\'._~@#$:&%/;,=-]+($|[[:space:]]$|[[:space:]]['.$words_chars.'])','\1 \2',$text);

// the next two repeated lines needed
if ($min_word_length >= 1) {
  $text = ereg_replace('[[:space:]][^ ]{1,'.$min_word_length.'}[[:space:]]',' ',' '.$text.' ');
  $text = ereg_replace('[[:space:]][^ ]{1,'.$min_word_length.'}[[:space:]]',' ',' '.$text.' ');
}

$text = ereg_replace('\.{2,}',' ',$text);
$text = ereg_replace('^[[:space:]]*\.+',' ',$text);

return trim(ereg_replace("[[:space:]]+"," ",$text));
}

function dims_getProjets() {
	$db = dims::getInstance()->getDb();
	global $scriptenv;

	$tabprojets=array();

	$res=$db->query("SELECT * from dims_project where id_workspace= :workspaceid ", array(
		':workspaceid' => $_SESSION['dims']['workspaceid']
	));

	if ($db->numrows($res)) {
		while ($fields=$db->fetchrow($res)) {
			$elem=array();
			$elem['title']=$fields['label'];
			$elem['selected']=(isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']==$fields['id']) ? "selected" : "";
			$elem['url']=$scriptenv;
			$tabprojets[]=$elem;
		}
	}
	return $tabprojets;
}

function system_getServices($id_ent) {
	$db = dims::getInstance()->getDb();

	$services = array('list' => array(), 'tree' => array());

	$select = "SELECT * FROM dims_ent_services where id_ent= :ident ORDER BY depth,label";

	$result = $db->query($select, array(
		':ident' => $id_ent
	));
	while ($fields = $db->fetchrow($result)) {
		$add = true;
		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && $_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
			// get allowed only groups
			$array_parents = explode(';',$fields['parents']);
			if (!($fields['id'] == $_SESSION['dims']['workspaceid'] || in_array($_SESSION['dims']['workspaceid'], $array_parents))) $add = false;
		}

		if ($add) {
			$fields['groups'] = array();
			$fields['groups_shared'] = array();
			$services['list'][$fields['id']] = $fields;
			$services['tree'][$fields['id_service']][] = $fields['id'];
		}
	}

	return($services);
}

function system_getServiceContacts($id_service) {
	$db = dims::getInstance()->getDb();

	$contacts = array();
	$rs = $db->query("
		SELECT	c.id, c.lastname, c.firstname
		FROM 	dims_ent_contact ec

		INNER JOIN	dims_contact c
		ON			c.id = ec.id_contact

		WHERE	ec.id_service = :idservice
		", array(
		':idservice' => $id_service
	));
	while ($row = $db->fetchrow($rs)) {
		$contacts[$row['id']] = $row;
	}
	return $contacts;
}

function system_createphysicalname($name)
{
	$chars = array("�" => "Y", "�" => "u", "�" => "A", "�" => "A",
					"�" => "A", "�" => "A", "�" => "A", "�" => "A",
					"�" => "A", "�" => "C", "�" => "E", "�" => "E",
					"�" => "E", "�" => "E", "�" => "I", "�" => "I",
					"�" => "I", "�" => "I", "�" => "D", "�" => "N",
					"�" => "O", "�" => "O", "�" => "O", "�" => "O",
					"�" => "O", "�" => "O", "�" => "U", "�" => "U",
					"�" => "U", "�" => "U", "�" => "Y", "�" => "s",
					"�" => "a", "�" => "a", "�" => "a", "�" => "a",
					"�" => "a", "�" => "a", "�" => "a", "�" => "c",
					"�" => "e", "�" => "e", "�" => "e", "�" => "e",
					"�" => "i", "�" => "i", "�" => "i", "�" => "i",
					"�" => "o", "�" => "n", "�" => "o", "�" => "o",
					"�" => "o", "�" => "o", "�" => "o", "�" => "o",
					"�" => "u", "�" => "u", "�" => "u", "�" => "u",
					"�" => "y", "�" => "y", " " => "_", "-" => "_");

	$name = ereg_replace("([^[:alnum:]|_]+)", "", strtr(strtolower(trim($name)), $chars));
	if (strlen($name) && is_numeric($name{0})) $name  = "_$name";

	return(substr($name,0,32));
}

function analyzeEmailsExpression($content) {
        $content=strtolower(dims_convertaccents(html_entity_decode(($content))));

        $len=strlen($content);
        $word="";
        $wc=0; // nb de car courant
        $nbwords=0;
        $cpteglobal=0;
        $wordcour="";
        $wordouble="";
        $key="";
        $sentencecontent="";
        $email=false;
        $issentence=false;
        $isword=false;
        $cour=0;
        $idparag=1;
        $cptepoint=0;
        $anca=0;
        $a=0;
        $linesentence="";
        $tabwordscur=array();
        $type=0;
        $lstemails=array();

        for($i=0;$i<=$len;$i++) {
                if ($i==$len) $car="\n";
                else $car=$content[$i];

                $anca=$a;
                $a=ord($car);

                if ($a>=48 && $a <=57 || $a>=97 && $a <=122 || $a==64) {
                        // digits or caracteres
                        $word.=$car;
                        $wc++;
                }
                else {
                        // cas spécifique : \r \n ! ?
                        if ($a==10 || $a==13 || $a==33 || $a==63) {
                                //if ($nbwords>0) {
                                $issentence=true;
                                $isword=true;
                                $type=0;
                                if (is_numeric($word)) {
                                        $type=1;
                                        //echo "\nInt : ".$word;ob_flush();
                                }
                                //else echo "\nTexte : ".$word;ob_flush();
                                //}
                        }
                        /***************************************************************/
                        elseif ($a==46 || ($a==32 && $wc>0 && is_numeric($word))) {// gestion du point ou espace pour telephone
                                if ($wc>0 && $anca!=46 && $anca!=32) { // on verifie que l'on a bien un mot en cour, sinon on coupe
                                        // on préserve les points uniquement pour verifier le mot complet
                                        // verifions si pas un email
                                        $j=$i+1;
                                        $ssword=substr($content,$cour,$i-$cour);
                                        if ($a==46) {
                                                $cptepoint=1;
                                                $aspoint=true;
                                                $asspace=false;
                                        }
                                        else {
                                                $cptespace=0;
                                                $aspoint=false;
                                                $asspace=true;
                                        }

                                        $continue=true;
                                        $email=(strpos($ssword,"@")>0);
                                        $tabword=array();
                                        $tabword[0]=$ssword;
                                        $courw="";

                                        $isnum=is_numeric($ssword);

                                        while ($j<$len && $continue) {
                                                $a2=ord($content[$j]);
                                                if ($a2==64) {
                                                        $email=true;
                                                        $isnum=false;
                                                }
                                                else {
                                                        $continue=($a2>=48 && $a2 <=57 || $a2>=97 && $a2 <=122 || $a2==46 || $a2==32);
                                                        if ($continue) {
                                                                if ($a2==46 || $a2==32) {
                                                                        if ($a2==46) { // on gere le point
                                                                                $cptepoint++; // on a plusieurs points
                                                                                if ($cptepoint==1) {
                                                                                        $tabword[]=$courw;
                                                                                        $courw="";
                                                                                }
                                                                                // test si deja un espace, on arrete
                                                                                if ($asspace) {
                                                                                        $continue=false; // on ne peut avoir des . et espaces en meme temps
                                                                                        $isnum=false;
                                                                                }
                                                                        }
                                                                        else { // on gere l'espace
                                                                                if ($aspoint) {
                                                                                        $continue=false;// on ne peut avoir des . et espaces en meme temps
                                                                                        if (is_numeric($courw)) $tabword[]=$courw;
                                                                                        if (sizeof($tabword)<5) $isnum=false;
                                                                                }
                                                                                elseif ($email) {
                                                                                        $continue=false;
                                                                                        $isnum=false;
                                                                                }
                                                                                else {
                                                                                        $cptespace++;
                                                                                        if ($cptespace==1) { // on complete la structure pour construire un numero de tel
                                                                                                $tabword[]=$courw;
                                                                                                $isnum=$isnum && is_numeric($courw);
                                                                                                // on a un tel on arrete
                                                                                                if (sizeof($tabword)==5 && $isnum) $continue=false;
                                                                                                $courw="";
                                                                                        }
                                                                                        else {
                                                                                                // on arrete avec les espaces ce n'est pas un tel
                                                                                                $isnum=false;
                                                                                                $continue=false;
                                                                                        }
                                                                                }
                                                                        }
                                                                }
                                                                elseif ($cptepoint>1) {
                                                                        $continue=false; // on a repris un nouveau mot
                                                                        $isnum=false;
                                                                        //$j-=$cptepoint;
                                                                }
                                                                else {
                                                                        $cptespace=0;
                                                                        $cptepoint=0; // on a autre chose
                                                                        $courw.=$content[$j];
                                                                }
                                                        }

                                                }
                                                if ($continue) $j++;
                                        }
                                        if ($j==$len && $courw!="") {
                                                $isnum=$isnum && is_numeric($courw);
                                                $tabword[]=$courw;
                                        }

                                        if ($isnum && sizeof($tabword)==5) { // test si numéro de telephone ok
                                                $ssword=substr($content,$cour,$j-$cour);
                                                $isword=true;
                                                $i=$j-1;
                                                $anca=ord($content[$i]);
                                                $word=$ssword;
                                                $type=4; // tel
                                                //echo "\nTel : ".$word;ob_flush();
                                        }
                                        else {
                                                //echo substr($content,$cour,$j-$cour). " ".$cptepoint."\n";ob_flush();
                                                $ssword=substr($content,$cour,$j-$cour-$cptepoint);

                                                // verifions si on a un email ou numérique
                                                if ($email || is_numeric($ssword)) {
                                                        $isword=true;
                                                        $i=$j-1;
                                                        $anca=ord($content[$i]);
                                                        $word=$ssword;
                                                        if ($email) {
                                                                $type=3;
                                                                //echo "\nEmail : ".$word;ob_flush();
                                                        }
                                                        else {
                                                                if (strpos($word,".")>0) {
                                                                        // a t on qq chose apres le point, si non alors point comme gin de phrase
                                                                        $type=2;
                                                                        //echo "\nFloat : ".$word;ob_flush();
                                                                }
                                                                else {
                                                                        $type=1;
                                                                        //echo "\nInt : ".$word;ob_flush();
                                                                }

                                                        }
                                                }
                                                else {
                                                        // on fait le mot
                                                        $isword=true;
                                                        //$i+=strlen($word);
                                                        if (isset($content[$i])) $anca=ord($content[$i]);
                                                        $issentence=true;

                                                        if (is_numeric($word)) {
                                                                if ($cptepoint>0) $type=2;
                                                                else $type=1;
                                                        }
                                                        else $type=0;
                                                        /*if ($type==0) {echo "\nTexte ".$word;ob_flush();}
                                                        else {
                                                                if ($type==2) {echo "\nFloat : ".$word;ob_flush();}
                                                                else {echo "\nInt $type ".$word;ob_flush();}
                                                        }*/
                                                }// fin du cas autre que email ou nombre
                                        } // fin de test sur numero de tel

                                }
                        }
                        elseif ($a==44) {// virgule, gestion des nombres
                                $ssword=substr($content,$cour,$i-$cour);
                                if ($wc>0 && is_numeric($ssword)) {
                                        // on check la deuxième partie
                                        $j=$i+1;
                                        $continue=true;
                                        while ($j<$len && $continue) {
                                                $c2=$content[$j];
                                                $a2=ord($c2);
                                                if ($a2==64) {
                                                        $email=true;
                                                }
                                                else {
                                                        $continue=($a2>=48 && $a2 <=57);
                                                        if ($continue) $ssword.=$c2;
                                                }
                                                if ($continue) $j++;
                                        }
                                        // on a un chiffre de type 2343,34
                                        if (is_numeric($ssword)) {
                                                if ($j>($i+1)) {
                                                        $word=substr($content,$cour,$j-$cour);
                                                        $isword=true;
                                                        $type=2; // float
                                                        $i=$j-1;
                                                        //echo "\nFloat ".$word;ob_flush();
                                                }
                                                else {
                                                        // on a un entier, la virgule etait une separation standard
                                                        $isword=true;
                                                        $type=1;
                                                        //echo "\nNum ".$word;ob_flush();
                                                }
                                        }
                                }
                                else {
                                        $type=0;
                                        $isword=true;
                                }
                        }
                        /***************************************************************/
                        elseif (($a==47 || $a==45) && $wc>0 && is_numeric($word) && strlen($word)<=4) {// gestion du / pour les dates
                                $is4len=strlen($word)==4;
                                $j=$i+1;
                                $continue=true;
                                $cpteslash=1;
                                $courw="";
                                $tabword=array();
                                $tabword[0]=substr($content,$cour,$i-$cour);
                                $nbslash=1;

                                // on avance pour analyse de la suite
                                while ($j<$len && $continue) {
                                        $a2=ord($content[$j]);

                                        $continue=($a2>=48 && $a2 <=57 || $a2>=97 && $a2 <=122 || $a2==47 || $a2==45); // 45 => -
                                        if ($continue) {
                                                if ($a2==47 || $a2==45) {
                                                        $nbslash++;

                                                        if ($cpteslash>0) $continue=false;
                                                        else $cpteslash=1;

                                                        // test si entier
                                                        if (is_numeric($courw)) {
                                                                // on a un entier, vérifions si pas deja eu un chiffre de 4 num.
                                                                if (strlen($courw)==4) {
                                                                        if (!$is4len) $is4len=true;
                                                                        else $continue=false; // on ne peut avoir 2 series de 4 chiffres
                                                                }
                                                                elseif (strlen($courw)>4) $continue=false;

                                                                if ($continue) {// on enregistre
                                                                        $tabword[]=$courw;
                                                                        $courw="";
                                                                }
                                                        }
                                                        else {
                                                                $continue=false;
                                                        }
                                                        if ($nbslash==3) {
                                                                $continue=false;
                                                        }
                                                }
                                                else {
                                                        $cpteslash=0;
                                                        $courw.=$content[$j];
                                                }

                                                if ($continue) $j++; // on avance
                                        }
                                        else {
                                                // on sort avec un dernier bloc
                                                if (is_numeric($courw)) {
                                                        // on a un entier, vérifions si pas deja eu un chiffre de 4 num.
                                                        if (strlen($courw)==4) {
                                                                if (!$is4len) $tabword[]=$courw;
                                                        }
                                                        else $tabword[]=$courw;
                                                }
                                                else {
                                                        $continue=false;
                                                }
                                        }
                                }

                                if ($j==$len && $courw!="") {
                                                //$isnum=$isnum && is_numeric($courw);
                                                $tabword[]=$courw;
                                }
                                // on examine le tableau $taword
                                if (sizeof($tabword)==3) {
                                        $ssword=substr($content,$cour,$j-$cour);
                                        $isword=true;
                                        $i=$j-1;
                                        $anca=ord($content[$i]);
                                        $word=$ssword;
                                        $type=5; // date
                                        //echo "\nDate : ".$word;ob_flush();
                                        $courw="";
                                }
                                else {
                                        $isword=true;
                                        $type=0;
                                        $courw="";
                                        //echo "\nTexte : ".$word;ob_flush();
                                }
                        }
                        else {
                                if ($wc>0) {
                                        $isword=true;
                                        $type=0;

                                        // test si on a un entier eventuellement
                                        if (is_numeric($word)) {
                                                $type=1;
                                                //echo "\nInt : ".$word;ob_flush();
                                        }
                                        //else echo "\nTexte : ".$word;ob_flush();
                                }
                        }
                }

                /********************************************************************************/
                /* Traitement du mot cle a inserer												*/
                /********************************************************************************/
                if ($isword) {
                        // ajout du mot et son type
                        $cour=$i+1;
                        $isword=false;
                        $word=trim($word);
                        if ($word!="") {
                                if (!$email && strpos($word,"@")>0) $type=3; // modification pour debut de recherche sur email

                                if ($type==3) {// on a bien un email
                                    $lstemails[$word]=$word;
                                }
                        }
                        $word="";
                }
        }
        return ($lstemails);
}

?>
