<?
echo $skin->open_simplebloc('', 'width:100%;');
?>
<div>
	<div class="system_group_icons">
		<div class="system_group_icons_padding">
			<?
			$toolbar_group=array();
			$x=0;
			$toolbar_group[] = array(
									'title' 	=> str_replace('<LABEL>','<br /><b>'.$childgroup.'</b>', $_DIMS['cste']['_DIMS_LABEL_CREATE_CHILD']),
									'url'		=> "$scriptenv?op=child&groupid=$groupid",
									'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_child.png"
								);

			// test if root of tree
			if ($group->fields['depth']>2) {
				$toolbar_group[] = array(
									'title' 	=> str_replace('<LABEL>','<br /><b>'.$currentgroup.'</b>', $_DIMS['cste']['_DIMS_LABEL_CREATE_CLONE']),
									'url'		=> "$scriptenv?op=clone&groupid=$groupid",
									'icon'		=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_copy.png"
								);
			}
			$sizeof_groups = sizeof($group->getgroupchildrenlite());
			$sizeof_users = $group->getNbUsers();

			// delete button if group not protected and no children
			// if (!$group->fields['protected'] && !$sizeof_groups && !$sizeof_users)
			if (!$sizeof_groups && !$sizeof_users && !$group->fields['protected']) {
				$toolbar_group[] = array(
										'title' 	=> str_replace('<LABEL>','<br /><b>'.$currentgroup.'</b>', $_DIMS['cste']['_DIMS_LABEL_DELETE_GROUP']),
										'url'		=> "$scriptenv?op=delete&groupid=$groupid",
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_delete.png",
										'confirm'	=> $_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDELETE']
									);
			}
			else {
				if ($sizeof_groups || $sizeof_users) {
					$msg = '';
					if ($sizeof_groups) $msg = $_DIMS['cste']['_SYSTEM_MSG_INFODELETE_GROUPS'];
					elseif ($sizeof_users) $msg = $_DIMS['cste']['_SYSTEM_MSG_INFODELETE_USERS'];

					$toolbar_group[] = array(
											'title' 	=> str_replace('<LABEL>','<br /><b>'.$currentgroup.'</b>', $_DIMS['cste']['_DIMS_LABEL_DELETE_GROUP']),
											'url'		=> $scriptenv,
											'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_delete_gray.png",
											'confirm'	=> $msg
										);
				}
				else
				{
					$msg = $_DIMS['cste']['_SYSTEM_MSG_PROTECTED_GROUPS'];
					$toolbar_group[] = array(
											'title' 	=> str_replace('<LABEL>','<br /><b>'.$currentgroup.'</b>', $_DIMS['cste']['_DIMS_LABEL_DELETE_GROUP']),
											'url'		=> $scriptenv,
											'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_delete_gray.png",
											'confirm'	=> $msg
										);
				}
			}

			echo $skin->create_menu($toolbar_group, $x, false, true);
			?>
		</div>
	</div>

	<div class="system_group_main ui-widget ui-widget-content">
		<?
		if ($father = $group->getfather()) {
			$parentlabel = $father->fields['label'];
			$parentid = $father->fields['id'];
		}
		else {
			$parentlabel = 'Racine';
			$parentid = '';
		}

		//$users = $group->getusers();
		$nbusers = $group->getNbUsers();

		$groups = $group->getgroupchildren(1);

		$grouplist = '';
		foreach ($groups as $childid => $fields)
		{
			if ($grouplist!='') $grouplist .= ' &#149; ';
			$grouplist .= $fields['label'];
		}

		$templatelist_back = dims_getavailabletemplates('backoffice');
		$templatelist_front = dims_getavailabletemplates('frontoffice');

		$groups_parents = system_getallgroups($groupid);

		$token = new FormToken\TokenField;
        $token->field("op",			"save_group");
        $token->field("group_id",	$group->fields['id']);
        $token->field("group_label");
        $token->field("group_reference");
        $token->field("group_shared");
		?>
		<form name="form_group" action="<? echo $scriptenv; ?>" method="POST" onsubmit="javascript:return system_group_validate(this);">
		<input type="hidden" name="op" value="save_group">
		<?php echo $token->generate(); ?>
		<input type="hidden" name="group_id" value="<? echo $group->fields['id']; ?>">

			<div class="dims_form_title">
				<? echo $group->fields['label']; ?> &raquo;
				<?
					echo $_DIMS['cste']['_DIMS_LABEL_GROUP_MODIFY'];
				?>
			</div>
			<div class="dims_form" style="clear:both;padding:2px">
				<p>
					<label><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:</label>
					<input type="text" class="text" name="group_label"  value="<? echo $group->fields['label']; ?>">
				</p>
                                <p>
					<label><? echo $_DIMS['cste']['_WCE_ARTICLE_REFERENCE']; ?>:</label>
					<input type="text" class="text" name="group_reference"  value="<? echo $group->fields['reference']; ?>">
				</p>
				<p>
					<label><? echo $_DIMS['cste']['_SHARE']; ?>:</label>
					<input style="width:16px;" type="checkbox" name="group_shared" <? if($group->fields['shared']) echo "checked"; ?> value="1">(disponible pour les sous-espaces)
				</p>
			</div>
			<div style="clear:both;float:right;padding:4px;">
                <?php
                    echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:forms.form_group.submit();");
                ?>
			</div>
		</form>
	</div>
</div>
<?
echo $skin->close_simplebloc();

echo $skin->open_simplebloc('', 'width:auto;');
require_once DIMS_APP_PATH.'include/functions/annotations.php';
dims_annotation(dims_const::_SYSTEM_OBJECT_GROUP, $group->fields['id'], $group->fields['label']);
echo $skin->close_simplebloc();
?>
