<?php echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_MYDATAS'],'100%'); ?>

<TABLE CELLPADDING="2" CELLSPACING="1">
<?php
$select = "SELECT * FROM dims_mb_action";
$db->query($select);
while ($fields = $db->fetchrow($res)) $actions[$fields['id_module_type']][$fields['id_action']] = $fields;

foreach ($_SESSION['dims']['workspaces'] as $group)
{
	if (!empty($group['adminlevel']) && $group['id'] != _DIMS_SYSTEMGROUP)
	{
		?>
		<TR bgcolor="<?php echo $skin->values['bgline2']; ?>">
			<TD COLSPAN="2"><b>Espace a <?php echo $group['label']; ?> </b></TD>
		</TR>
		<TR bgcolor="<?php echo $skin->values['bgline1']; ?>">
			<TD>Niveau Utilisateur :</TD>
			<TD><?php echo $dims_system_levels[$group['adminlevel']]; ?></TD>
		</TR>

		<?php
		if (isset($group['modules']))
		foreach ($group['modules'] as $moduleid)
		{
			?>
			<TR bgcolor="<?php echo $skin->values['bgline1']; ?>">
				<TD VALIGN="top">Module a <? echo $_SESSION['dims']['modules'][$moduleid]['label']; ?> </TD>
				<TD VALIGN="top">
					<TABLE CELLPADDING="0" CELLSPACING="1">

						<?php
						$red = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\">";
						$green = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\">";

						if (!empty($actions[$_SESSION['dims']['modules'][$moduleid]['id_module_type']]))
							foreach($actions[$_SESSION['dims']['modules'][$moduleid]['id_module_type']] as $id => $action)
							{
								$puce = dims_isactionallowed($id, $group['id'], $moduleid) ? $green : $red;
								echo 	"<tr>
											<td>{$puce}</td>
											<td>{$action['label']}</td>
										</tr>";
							}
						?>
					</TABLE>
				</TD>
			</TR>
			<?
		}
	}
}
?>
</TABLE>
<?php //dims_print_r($_SESSION); ?>
<?php echo $skin->close_simplebloc(); ?>
