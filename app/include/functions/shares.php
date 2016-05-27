<?
function dims_shares_selectusers($id_object = -1, $id_record = -1, $id_module = -1) {
	$db = dims::getInstance()->getDb();
	global $_DIMS;
	if (isset($_SESSION['dims']['shares']['users_selected'])) unset($_SESSION['dims']['shares']['users_selected']);
	if (isset($_SESSION['dims']['shares']['groups_selected'])) unset($_SESSION['dims']['shares']['groups_selected']);

	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	$res=$db->query('SELECT id_share,type_share FROM dims_share WHERE id_object = ? AND id_record = ? AND id_module = ?', array($id_object, $id_record, $id_module));
	while ($row = $db->fetchrow($res)) {
		switch($row['type_share']) {
			case 'group':
				$_SESSION['dims']['shares']['groups_selected'][$row['id_share']] = $row['id_share'];
				break;
			default:
				$_SESSION['dims']['shares']['users_selected'][$row['id_share']] = $row['id_share'];
				break;
		}
	}

	?>
	<a class="dims_shares_title" href="#" onclick="javascript:dims_switchdisplay('dims_shares');">
		<p class="dims_va">
			<img src="<? echo "{$_SESSION['dims']['template_path']}/img/shares/shares.png"; ?>">
			<span><? echo $_DIMS['cste']['_DIMS_SHARE']; ?></span>
		</p>
	</a>
	<div id="dims_shares" style="display:block;">
		<div class="dims_shares_search_form">
			<p class="dims_va">
				<span><? echo $_DIMS['cste']['_SEARCH']; ?></span>
				<input onkeyup="javascript:searchUserShare();" type="text" id="dims_shares_userfilter" class="text">
				<img onmouseover="javascript:this.style.cursor='pointer';" onclick="javascript:searchUserShare();" style="border:0px" src="./common/img/search.png">
			</p>
		</div>
		<div style="width:100%;display:block;" id="div_shares_search_result"></div>
		<div style="width:100%;display:block;clear:both;">Autorisations :</div>
		<div style="width:100%;display:block;" id="div_shares_users_selected">
			<?
			dims_shares_getSelectedUsers();
			?>
		</div>
	</div>
	<?
}

function dims_shares_getSelectedUsers($id_object = -1, $id_record = -1, $id_module = -1) {
	$db = dims::getInstance()->getDb();
	if (empty($_SESSION['dims']['shares']['users_selected']) && empty($_SESSION['dims']['shares']['groups_selected'])) echo 'Aucune autorisation';
	else {
		echo "<table width=\"100%\">";
		$cptetempselected=0;
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] ) {
			if (isset($_SESSION['dims']['shares']['users_selected']) && !empty($_SESSION['dims']['shares']['users_selected'])) {
				// construction de la requete de listing
				$params = array();
				$res=$db->query('SELECT 	u.*
								 FROM 		dims_user as u
								 WHERE 		id in ('.$db->getParamsFromArray($_SESSION['dims']['shares']['users_selected'], 'user', $params).')', $params);

				if ($db->numrows($res)>0) {
					echo "<table width=\"50%\">";
					while ($f=$db->fetchrow($res)) {
						$cptetempselected++;
						//calcul de l'icon
						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
						$icon="";
						if (!file_exists($usericon) || $f['color']=="") $f['color']="#EFEFEF";
						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

						echo "<tr><td width=\"4%\"><a href=\"javascript:void(0);\" onclick=\"dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&remove_user_id=".$f['id']."','','div_shares_users_selected');\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td>";
						echo "<td width=\"6%\">".$icon."</td>
						<td align=\"left\" width=\"90%\">".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']." (".$f['login'].")</td></tr>";
					}
					echo "</table>";
				}
			}

			// on s'occupe des groupes
			if (isset($_SESSION['dims']['shares']['groups_selected']) && !empty($_SESSION['dims']['shares']['groups_selected'])) {
				// construction de la requete de listing
				$params = array();
				$res=$db->query("SELECT 	g.*
								 FROM 		dims_group as g
								 WHERE 		id in (".$db->getParamsFromArray($_SESSION['dims']['shares']['groups_selected'], 'group', $params).')', $params);

				if ($db->numrows($res)>0) {
					echo "<table width=\"50%\">";
					while ($f=$db->fetchrow($res)) {
						$cptetempselected++;
						//calcul de l'icon
						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
						$icon="";
						if (!file_exists($usericon) || $f['color']=="") $f['color']="#EFEFEF";
						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

						echo "<tr><td width=\"4%\"><a href=\"javascript:void(0);\" onclick=\"dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&remove_group_id=".$f['id']."','','div_shares_users_selected');\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td>";
						echo "<td width=\"6%\"><img src=\"./common/img/icon_group.gif\" alt=\"\"></td>
								<td align=\"left\" width=\"90%\">".$f['label']."</td></tr>";
					}
					echo "</table>";
				}
			}
		}
	}
}

function dims_shares_save($id_object = -1, $id_record = -1, $id_module = -1,$fromnew=false) {
	$db = dims::getInstance()->getDb();

	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	$res=$db->query("DELETE FROM dims_share WHERE id_object = {$id_object} AND id_record = '".addslashes($id_record)."' AND id_module = {$id_module}");

	// boucle sur les users
	if (isset($_SESSION['dims']['shares'])) {
			foreach($_SESSION['dims']['shares']['users_selected'] as $id_user) {
				$share = new share();
				$share->fields = array(	'id_module' 	=> $id_module,
										'id_record' 	=> $id_record,
										'id_object' 	=> $id_object,
										'type_share' 	=> 'user',
										'id_share' 		=> $id_user
									);
				$share->save();
			}

			// boucle sur les groups
			foreach($_SESSION['dims']['shares']['groups_selected'] as $id_group) {
				$share = new share();
				$share->fields = array(	'id_module' 	=> $id_module,
										'id_record' 	=> $id_record,
										'id_object' 	=> $id_object,
										'type_share' 	=> 'group',
										'id_share' 		=> $id_group
									);
				$share->save();
			}
	}
}

function dims_shares_get($id_user = -1, $id_object = -1, $id_record = -1,  $id_module = -1,$id_group=-1) {
	$db = dims::getInstance()->getDb();

	$shares = array();

	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];
    if ($id_user==-1) $id_user=$_SESSION['dims']['userid'];

    // groupes auxquels le user appartient
    $groupuser=array();
    // test si user connecte ou non
    if ($id_user>0) {
        $user=new user();
        $user->open($id_user);
        $groupuser=$user->getgroups();
    }

	$sql =	"SELECT * FROM dims_share WHERE 1";

	$params = array();
	if ($id_module != -1) {
		$sql .= " AND id_module = :idmodule ";
		$params[':idmodule'] = $id_module;
	}
	if ($id_object != -1) {
		$sql .= " AND id_object = :idobject ";
		$params[':idobject'] = $id_object;
	}
	if ($id_record != -1) {
		$sql .= " AND id_record = :idrecord ";
		$params[':idrecord'] = $id_record;
	}
    if (!empty($groupuser)) {
        $sql .= " AND (
        				(id_share = :iduser AND type_share = 'user')
        					OR
        				(type_share = 'group' AND id_share in (".$db->getParamsFromArray($groupuser, 'groupuser', $params)."))
        			  ) ";
		$params[':iduser'] = $id_user;
    }
    else {
		if ($id_user >0) {
			$sql .= " AND id_share = :iduser AND type_share = 'user'";
			$params[':iduser'] = $id_user;
		}
		if ($id_group >0) {
			$sql .= " AND id_share = :idgroup AND type_share = 'group'";
			$params[':idgroup'] = $id_group;
		}
    }

	$res=$db->query($sql, $params);

 	while ($row = $db->fetchrow($res)) {
		$shares[] = $row;
	}

	return($shares);
}


function dims_share_display($lstresult) {
	if (!isset($lstresult)) $lstresult=array();
	if (!empty($lstresult['works'])) {

		foreach ($lstresult['works'] as $idwork =>$work) {
			if (isset($work['groups']) && sizeof($work['groups'])>=1 || isset($work['users']) && sizeof($work['users'])>=1) {
				echo "<div class=\"wce_tree_node\"><img src=\"./common/img/workspace.png\" alt=\"\">".$work['label']."</div>";
				echo "<div style=\"clear: left; display: block;\">";
				// on regarde maintenant les groupes et users
				//test si vide on ou non
				$posw=1;
				$nbgroup=sizeof($work['groups']);

				foreach ($work['groups'] as $gid => $group) {
					if ($posw==$nbgroup) {
						if (!empty($group['users'])) $icon="join.png";
						else $icon="minus.png";
					}
					else {
						if (!empty($group['users'])) $icon="joinbottom.png";
						else $icon="minusbottom.png";
					}

					if ($posw==$nbgroup) $iconsep="empty.png";
					else $iconsep="line.png";

					echo "<div class=\"wce_tree_node\"><img src=\"./common/img/".$icon."\">";
					//// link for add element
					//echo "<span><img src=\"./common/img/add.gif\"></span><span style=\"margin-left:5px;\">";
					if (!isset($_SESSION['dims']['shares']['groups_selected'][$gid])) {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&group_id=<? echo $gid; ?>','','div_shares_users_selected');searchUserShare();">
						<?
						echo "<span><img src=\"./common/img/add.gif\"></span>";
					}
					else {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&remove_group_id=<? echo $gid; ?>','','div_shares_users_selected');searchUserShare();">
						<?
						echo "<span><img src=\"./common/img/delete.png\"></span>";

					}
					echo "<span style=\"margin-left:5px;\"><img src=\"./common/img/icon_group.gif\" alt=\"\">&nbsp;";

					echo $lstresult['groups'][$gid]['label'];
					// test si herite ou non
					if (isset($lstresult['groups'][$gid]['herited'])) {
						// on affiche (herite)
						$gparent=$lstresult['groups'][$gid]['id_group'];
						echo "<font style=\"font-style: italic;\">(".$_DIMS['cste']['_DIMS_HERITED']." ".$_DIMS['cste']['_FROM']." '".$lstresult['groups'][$gparent]['label']."')</font>";
					}

					echo "</span></a>";

					$pos=1;
					$nbuser=sizeof($lstresult['groups'][$gid]['users']);

					foreach ($lstresult['groups'][$gid]['users'] as $iduser => $u) {
						if ($pos==$nbuser ) $icon="join.png";
						else $icon="joinbottom.png";
						$user=$lstresult['users'][$iduser];

						echo "<div style=\"clear:left;display: block;\">
								<img src=\"./common/img/".$iconsep."\">
								<img src=\"./common/img/".$icon."\">";

						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$user['color']).".png";

						if (!file_exists($usericon) || $user['color']=="")  $user['color']="#EFEFEF";
						if (!isset($_SESSION['dims']['shares']['users_selected'][$iduser])) {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&user_id=<? echo $iduser; ?>','','div_shares_users_selected');searchUserShare();">
						<?
						echo "<span><img src=\"./common/img/add.gif\"></span>";
						}
						else {
							?>
							<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&remove_user_id=<? echo $iduser; ?>','','div_shares_users_selected');searchUserShare();">
							<?
							echo "<span><img src=\"./common/img/delete.png\"></span>";
						}

						$icon="<img src=\"./data/users/icon_".str_replace("#","",$user['color']).".png\" alt=\"\" border=\"0\">";
						echo "<span style=\"margin-left:5px;\">".$icon."&nbsp;".$user['firstname']." ".$user['lastname']."</span></a>";
						$pos++;
						echo "</div>";
					}
					echo "</div>";
					$posw++;
				}

				// liste des groupes h�rit�s
				if (isset($work['users']) && sizeof($work['users'])>=1) {
					$nbuser=sizeof($work['users']);
					$pos=1;
					$iconsep="empty.png";
					foreach ($work['users'] as $iduser => $u) {
						if ($pos==$nbuser ) $icon="join.png";
						else $icon="joinbottom.png";
						$user=$lstresult['users'][$iduser];

						echo "<div style=\"clear:left;display: block;\">
								<img src=\"./common/img/".$iconsep."\">
								<img src=\"./common/img/".$icon."\">";

						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$user['color']).".png";

						if (!file_exists($usericon) || $user['color']=="")  $user['color']="#EFEFEF";
						if (!isset($_SESSION['dims']['shares']['users_selected'][$iduser])) {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&user_id=<? echo $iduser; ?>','','div_shares_users_selected');searchUserShare();">
						<?
						echo "<span><img src=\"./common/img/add.gif\"></span>";
						}
						else {
							?>
							<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=shares_select_user&remove_user_id=<? echo $iduser; ?>','','div_shares_users_selected');searchUserShare();">
							<?
							echo "<span><img src=\"./common/img/delete.png\"></span>";
						}

						$icon="<img src=\"./data/users/icon_".str_replace("#","",$user['color']).".png\" alt=\"\" border=\"0\">";
						echo "<span style=\"margin-left:5px;\">".$icon."&nbsp;".$user['firstname']." ".$user['lastname']."</span></a>";
						$pos++;
						echo "</div>";
					}
				}
				echo "</div>";
			}
		}
	}
}
?>
