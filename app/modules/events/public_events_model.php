<?php

//Recherche des modeles d'events
$sql = 'SELECT
            m.*
        FROM
            dims_mod_business_event_model as m
        LEFT JOIN
            dims_user u
            ON
            u.id = m.id_user
        ORDER BY label';

$res = $db->query($sql);
?>
<div style="margin-top:15px;">
    <table width="100%">
        <tr>
            <td>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left" width="60%">&nbsp;
						</td>
						<td align="right">
							<?php
								echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_ADD'], './common/img/icon_add.gif', "location.href='admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=add_model_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL."';");
							?>
						</td>
					</tr>
				</table>

            </td>
        </tr>
    </table>
    <table width="100%" style="border-collapse: collapse;">
        <tr class="trl1">
            <th style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?></th>
			<th style="width: 20%;"><?php echo $_DIMS['cste']['_MODIFY']." / ".$_DIMS['cste']['_DELETE']; ?></th>
        </tr>
        <?php
            $class='trl2';

			while ($f=$db->fetchrow($res)) {
				echo '<tr class="'.$class.'"  style="text-align: center" >';
				echo '<td style="text-align: left;width:70%" >';
				echo $f['label'];
				echo '</td><td>';
				echo '<a title="'.$_DIMS['cste']['_DIMS_MILESTONE'].'" href="admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu='._DIMS_ADMIN_EVENTS_MODEL.'&id_evt_model='.$f['id'].'">';
				echo '<img src="./common/img/go-next.png" alt="'.$_DIMS['cste']['_DIMS_MILESTONE'].'" />';
				echo '</a> ';

				echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" href="admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=open_model_admin_events&ssubmenu='._DIMS_ADMIN_EVENTS_MODEL.'&id_evt_model='.$f['id'].'">';
				echo '<img src="./common/img/edit.gif" alt="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" />';
				echo '</a> ';
				$chg_state = '<img src="./common/img/delete.gif" alt="'.$_DIMS['cste']['_DELETE'].'" />';
				echo '<a  title="'.$_DIMS['cste']['_DELETE'].'"href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=delete_model_admin_events&ssubmenu='._DIMS_ADMIN_EVENTS_MODEL.'&id_evt_model='.$f['id'].'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_state.'</a>';
				echo '</a>';
				echo '</tr>';
				$class = ($class == 'trl2') ? 'trl1' : 'trl2';
			}
        ?>
    </table>
</div>

