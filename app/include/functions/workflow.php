<?

function dims_workflow_initusers($id_object = -1, $id_record = -1, $id_module = -1, $id_action = -1) {
	$db = dims::getInstance()->getDb();

	if (isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action])) {
			unset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]);
	}

	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];
	// security filter
	$sql="";

	if (!(is_numeric($id_record) or is_array($id_record)) || !is_numeric($id_module) || !is_numeric($id_object) || !is_numeric($id_action)) die();

	$params = array();
	$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
	$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
	if ($id_action==-1) {
		if (is_array($id_record) && !empty($id_record)) {
			$sql="SELECT id_workflow,type_workflow FROM dims_workflow WHERE id_object = :idobject AND id_record in (".$db->getParamsFromArray($id_record, 'idrecord', $params).") AND id_module = :idmodule";
		}
		else {
			$sql="SELECT id_workflow,type_workflow FROM dims_workflow WHERE id_object = :idobject AND id_record = :idrecord AND id_module = :idmodule";
			$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);
		}
	}
	else {
		if (is_array($id_record) && !empty($id_record)) {
			$sql="SELECT id_workflow,type_workflow FROM dims_workflow WHERE id_object = :idobject AND id_record in (".$db->getParamsFromArray($id_record, 'idrecord', $params).") AND id_module = :idmodule AND id_action = {$id_action}";
		}
		else {
			$sql="SELECT id_workflow,type_workflow FROM dims_workflow WHERE id_object = :idobject AND id_record = :idrecord AND id_module = :idmodule AND id_action = :idaction";
			$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);
			$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
		}
	}

	$res=$db->query($sql, $params);
	if ($db->numrows($res)) {
		while ($row = $db->fetchrow($res)) {

			if ($row['type_workflow']=='user') {

				//if (!isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action])) $_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]=array();

				$_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action][$row['id_workflow']] = $row['id_workflow'];

			}
			else {
				$_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action][$row['id_workflow']] = $row['id_workflow'];
			}
		}
	}
}

function dims_workflow_selectusers($id_object = -1, $id_record = -1, $id_module = -1, $id_action = -1) {
	$db = dims::getInstance()->getDb();

	dims_workflow_initusers($id_object,$id_record,$id_module,$id_action);

	?>
	<div id="dims_workflow<? echo $id_action; ?>" style="display:block;">
		<div class="dims_workflow_search_form">
			<p class="dims_va">
				<span>Recherche groupes/utilisateurs:&nbsp;</span>
				<input onkeyup="javascript:searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);" type="text" id="dims_workflow_userfilter<? echo $id_action; ?>" class="text">
				<img onmouseover="javascript:this.style.cursor='pointer';" onclick="dims_xmlhttprequest_todiv('index-light.php','dims_op=workflow_search_users&dims_workflow_userfilter='+dims_getelem('dims_workflow_userfilter<? echo $id_action; ?>').value+'&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_search_result<? echo $id_action; ?>');" style="border:0px" src="./common/img/search.png">
			</p>
		</div>
		<div id="div_workflow_search_result<? echo $id_action; ?>"></div>

		<div class="dims_workflow_title">Accr&eacute;ditations :</div>
		<div class="dims_workflow_authorizedlist" id="div_workflow_users_selected<? echo $id_action; ?>">
		<? if (empty($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action])) echo 'Aucune accredidation'; ?>
		</div>
		<?
		if (!empty($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]) || !empty($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action])) {
		?>
			<script type="text/javascript">
				dims_ajaxloader('div_workflow_users_selected<? echo $id_action; ?>');
				dims_xmlhttprequest_todiv('index-light.php','dims_op=workflow_select_user&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');
			</script>
		<?
		}
		?>
		</div>
	<?
}

function dims_workflow_getSelectedUsers($id_object = -1, $id_record = -1, $id_module = -1, $id_action = -1,$admin=true) {
	$db = dims::getInstance()->getDb();
	$cptetempselected=0;
	if (!$admin) {
		dims_workflow_initusers($id_object,$id_record,$id_module,$id_action);
	}

	if (empty($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]) && empty($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action])) echo 'Aucune autorisation';
	else {
		echo "<table width=\"100%\">";

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] ) {
			if (isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]) && !empty($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action])) {
				// construction de la requete de listing
				$actionsparams = array();
				$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action], 'idaction', $actionsparams).") order by lastname, firstname", $actionsparams);
				if ($db->numrows($res)>0) {
					echo "<table width=\"50%\">";
					while ($f=$db->fetchrow($res)) {
						$cptetempselected++;
						//calcul de l'icon
						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
						$icon="";
						if (!file_exists($usericon) || $f['color']=="") $f['color']="#EFEFEF";
						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

						if ($admin) {
							echo "<tr><td width=\"4%\"><a href=\"javascript:void(0);\" onclick=\"dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&id_object=".$id_object."&id_action=".$id_action."&remove_user_id=".$f['id']."','','div_workflow_users_selected".$id_action."');searchUserWorkflow(".$id_object.",".$id_action.");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td>";
						}
						else {
							echo "<tr><td width=\"4%\"></td>";
						}
						echo "<td width=\"6%\">".$icon."</td>
						<td align=\"left\" width=\"90%\">".$f['lastname']." ".strtoupper(substr($f['firstname'],0,1)).". (".$f['login'].")</td></tr>";
					}
					echo "</table>";
				}
			}

			// on s'occupe des groupes
			if (isset($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action]) && !empty($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action])) {
				// construction de la requete de listing
				$actionsparams = array();
				$res=$db->query("select g.* from dims_group as g where id in (".$db->getParamsFromArray($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action], 'idaction', $actionsparams).") order by label", $actionsparams);

				if ($db->numrows($res)>0) {
					echo "<table width=\"50%\">";
					while ($f=$db->fetchrow($res)) {
						$cptetempselected++;
						//calcul de l'icon
						if (!file_exists($usericon) || isset($f['color']) && $f['color']=="") $f['color']="#EFEFEF";
						else $f['color']='';

						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
						$icon="";

						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

						if ($admin) {
							echo "<tr><td width=\"4%\"><a href=\"javascript:void(0);\" onclick=\"dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&id_object=".$id_object."&id_action=".$id_action."&remove_group_id=".$f['id']."','','div_workflow_users_selected".$id_action."');searchUserWorkflow(".$id_object.",".$id_action.");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td>";
						}
						else {
							echo "<tr><td width=\"4%\"></td>";
						}
						echo "<td width=\"6%\"><img src=\"./common/img/icon_group.gif\" alt=\"\"></td>
								<td align=\"left\" width=\"90%\">".$f['label']."</td></tr>";
					}
					echo "</table>";
				}
			}
		}
	}
}

function dims_workflow_save($id_object = -1, $id_record = -1, $id_module = -1,$id_action=-1) {
	require_once DIMS_APP_PATH . '/include/class_workflow.php';
	$db = dims::getInstance()->getDb();

	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];
	// security filter
	if (!is_numeric($id_record) || ! is_numeric($id_module) || !is_numeric($id_object)) die();

	if (isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action]) || isset($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action])) {
		$res=$db->query("DELETE FROM dims_workflow WHERE id_object = :idobject AND id_record = :idrecord AND id_module = :idmodule AND id_action = :idaction", array(
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
		));
	}

	if (!empty($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action])) {
		foreach($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action] as $id_user) {
			$workflow = new workflow();
			$workflow->fields = array(	'id_module' => $id_module,
									'id_record'	=> $id_record,
									'id_object'	=> $id_object,
									'type_workflow' => 'user',
									'id_workflow'	=> $id_user,
									'id_action'	=> $id_action
								);
			$workflow->save();
		}
	}
	// boucle sur les groups
	foreach($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action] as $id_group) {
		$workflow = new workflow();
		$workflow->fields = array(	'id_module' => $id_module,
								'id_record'	=> $id_record,
								'id_object'	=> $id_object,
								'type_workflow'		=> 'group',
								'id_workflow'		=> $id_group,
								'id_action'	=> $id_action
							);
		$workflow->save();
	}
}

function dims_workflow_get($id_object = -1, $id_record = -1,  $id_module = -1, $id_user = -1,$id_action=-1) {
	$db = dims::getInstance()->getDb();

	$workflow = array();

	if ($id_module == -1 || !is_numeric($id_module)) $id_module = $_SESSION['dims']['moduleid'];
	// security filter
	if (!is_numeric($id_record) || !is_numeric($id_user) || !is_numeric($id_object)) die();

	$sql =	"SELECT * FROM dims_workflow WHERE id_module = :idmodule";

	$params = array(':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module));

	if ($id_object != -1) {
		$sql .= " AND id_object = :idobject";
		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
	}

	if ($id_record != -1) {
		$sql .= " AND id_record = :idrecord";
		$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);
	}

	if ($id_user != -1) {
		$sql .= " AND id_workflow = :idworkflow AND type_workflow = 'user'";
		$params[':idworkflow'] = array('type' => PDO::PARAM_INT, 'value' => $id_workflow);
	}

	if ($id_action != -1) {
		$sql .= " AND id_action = :idaction";
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
	}

	$res=$db->query($sql, $params);

	while ($row = $db->fetchrow($res)) {
		$workflow[] = $row;
	}

	return($workflow);
}

function dims_workflow_display($lstresult,$id_object=-1,$id_action=-1) {
	global $_DIMS;
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
				if (isset($work['users']) && !empty($work['users']))
					$nbgroup += sizeof($work['users']);

				foreach ($work['groups'] as $gid => $group) {
					$icon="";
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
					if (!isset($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action][$gid])) {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&group_id=<? echo $gid; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
						<?
						echo "<span><img src=\"./common/img/add.gif\"></span>";
					}
					else {
						?>
						<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&remove_group_id=<? echo $gid; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
						<?
						echo "<span><img src=\"./common/img/delete.png\"></span>";

					}
					echo "<span style=\"margin-left:5px;\"><img src=\"./common/img/icon_group.gif\" alt=\"\">&nbsp;";

					echo $lstresult['groups'][$gid]['label'];
					// test si herite ou non
					if (isset($lstresult['groups'][$gid]['herited'])) {
						// on affiche (herite)
						$gparent=$lstresult['groups'][$gid]['id_group'];
						echo "<font style=\"font-style: italic;\">(".$_DIMS['cste']['_DIMS_HERITED']." ".$_DIMS['cste']['_DIMS_LABEL_OF']." '".$lstresult['groups'][$gparent]['label']."')</font>";
					}

					echo "</span></a>";

					$pos=1;
					if (isset($lstresult['groups'][$gid]['users'])) {
						$nbuser=count($lstresult['groups'][$gid]['users']);

						foreach ($lstresult['groups'][$gid]['users'] as $iduser => $u) {
							if ($pos==$nbuser ) $icon="join.png";
							else $icon="joinbottom.png";
							$user=$lstresult['users'][$iduser];

							echo "<div style=\"clear:left;display: block;\">
									<img src=\"./common/img/".$iconsep."\">
									<img src=\"./common/img/".$icon."\">";

							$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$user['color']).".png";

							if (!file_exists($usericon) || $user['color']=="")	$user['color']="#EFEFEF";
							if (!isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action][$iduser])) {
							?>
							<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&user_id=<? echo $iduser; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
							<?
							echo "<span><img src=\"./common/img/add.gif\"></span>";
							}
							else {
								?>
								<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&remove_user_id=<? echo $iduser; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
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
					$posw++;
				}

				//liste des users rattaches directement
				if (isset($work['users']) && !empty($work['users'])) {
					//$pos=1;
					//$nbuser=sizeof($work['users']);
					$iconsep="empty.png";
					foreach ($work['users'] as $iduser => $u) {
							if ($posw==$nbgroup) $icon="join.png";
							else $icon="joinbottom.png";
							$user=$lstresult['users'][$iduser];

							echo "<div class=\"wce_tree_node\">
									<img src=\"./common/img/".$icon."\">";

							$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$user['color']).".png";

							if (!file_exists($usericon) || $user['color']=="")	$user['color']="#EFEFEF";
							if (!isset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action][$iduser])) {
							?>
							<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&user_id=<? echo $iduser; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
							<?
							echo "<span><img src=\"./common/img/add.gif\"></span>";
							}
							else {
								?>
								<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=workflow_select_user&remove_user_id=<? echo $iduser; ?>&id_object=<? echo $id_object; ?>&id_action=<? echo $id_action; ?>','','div_workflow_users_selected<? echo $id_action; ?>');searchUserWorkflow(<? echo $id_object.",".$id_action; ?>);">
								<?
								echo "<span><img src=\"./common/img/delete.png\"></span>";
							}

							$icon="<img src=\"./data/users/icon_".str_replace("#","",$user['color']).".png\" alt=\"\" border=\"0\">";
							echo "<span style=\"margin-left:5px;\">".$icon."&nbsp;".$user['firstname']." ".$user['lastname']."</span></a>";
							$posw++;
							echo "</div>";
					}
				}
			echo "</div>";

			}
		}
	}
}
?>
