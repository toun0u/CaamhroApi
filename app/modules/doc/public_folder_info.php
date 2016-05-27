<div class="doc_folderinfo">
<?
require_once DIMS_APP_PATH . '/include/functions/shares.php';
require_once DIMS_APP_PATH . '/include/functions/workflow.php';
$wf_validator = false;

if (!empty($currentfolder)) {
	//if (dims_isactionallowed(_DOC_ACTION_MODIFYFOLDER) && (!$docfolder->fields['readonly'] || $_SESSION['dims']['userid'] == $docfolder->fields['id_user']))
	//{
		?>
		<div style="float:right;height:40px;width:150px;">
			<p style="margin:0;padding:4px 8px;">
			<?
			echo dims_create_button($_DIMS['cste']['_MODIFY'],'./common/img/edit.gif','','','',dims_urlencode("/admin.php?op=folder_modify&currentfolder={$currentfolder}"));
			?>
			</p>
		</div>
		<?
	//}
	?>
	<div style="float:left;height:40px;">
		<p style="margin:0;padding:4px 0px 4px 8px;">
			<img src="./common/modules/doc/img/folder<? if ($docfolder->fields['foldertype'] == 'shared') echo '_shared'; ?><? if ($docfolder->fields['foldertype'] == 'public') echo '_public'; ?><? if ($docfolder->fields['readonly']) echo '_locked'; ?>.png" />
		</p>
	</div>
	<div style="float:left;height:40px;">
		<p style="margin:0;padding:4px 8px;">
			<strong><? echo $docfolder->fields['name']; ?></strong>
			<br />Dossier <? echo $foldertypes[$docfolder->fields['foldertype']]; ?><? if ($docfolder->fields['readonly']) echo ' en lecture seule'; ?>
		</p>
	</div>
	<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
		<p style="margin:0;padding:4px 8px;">
			<strong><? echo $_DIMS['cste']['_DIMS_OWNER']; ?></strong>:
			<br />
			<?
			require_once DIMS_APP_PATH . '/modules/system/class_user.php';
			$user = new user();
			$user->open($docfolder->fields['id_user']);
			echo $user->fields['login'];
			?>
		</p>
	</div>
	<?
	if ($docfolder->fields['foldertype'] == 'shared')
	{
		?>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px 8px;">
				<strong>Partages</strong>:
				<br />
				<?
				$shusers = array();
				foreach(dims_shares_get(-1, _DOC_OBJECT_FOLDER, $currentfolder) as $value) $shusers[] = $value['id_share'];

				$users = array();
				if (!empty($shusers))
				{
					$params = array();
					$sql = "SELECT id,login,lastname,firstname FROM dims_user WHERE id in (".$db->getParamsFromArray($shusers, 'shusers', $params).") ORDER BY lastname, firstname";
					$res=$db->query($sql, $params);
					while ($row = $db->fetchrow($res)) $users[$row['id']] = $row;

					if (sizeof($users))
					{
						$c=1;
						foreach($users as $user)
						{
							echo "{$user['login']}";
							if ($c++<sizeof($users)) echo ', ';
						}
					}
					else echo "Aucun partage";
				}
				else echo "Aucun partage";
				?>
			</p>
		</div>
		<?
	}

	if ($docfolder->fields['foldertype'] != 'private')
	{
		?>
		<div style="float:left;height:40px;border-left:1px solid #e0e0e0;">
			<p style="margin:0;padding:4px 8px;">
				<strong>Validateurs</strong>:
				<br />
				<?
				$wfusers = array();
				foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];

				$users = array();
				if (!empty($wfusers))
				{
					$params = array();
					$sql = "SELECT id,login,lastname,firstname FROM dims_user WHERE id in (".$db->getParamsFromArray($wfusers, 'wfusers', $params).") ORDER BY lastname, firstname";
					$res=$db->query($sql, $params);
					while ($row = $db->fetchrow($res)) $users[$row['id']] = $row;

					if (!empty($users))
					{
						$c=1;
						foreach($users as $user)
						{
							echo "{$user['login']}";
							if ($c++<sizeof($users)) echo ', ';

							if ($user['id'] == $_SESSION['dims']['userid']) $wf_validator = true;
						}
					}
					else echo $_DIMS['cste']['_DIMS_LABEL_NONE'];
				}
				else echo $_DIMS['cste']['_DIMS_LABEL_NONE'];
				?>
			</p>
		</div>
		<?
	}
}
else
{
	?>
	<div style="float:left;height:40px;">
		<p style="margin:0;padding:4px 0px 4px 8px;">
			<img src="./common/modules/doc/img/folder_home.png" />
		</p>
	</div>
	<div style="float:left;height:40px;">
		<p style="margin:0;padding:4px 8px;">
			<strong>Racine</strong>
			<br />Dossier Personnel
		</p>
	</div>
	<?
}
?>
</div>
