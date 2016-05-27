<?php
$sel = "SELECT		*
	FROM		".dims_import::TABLE_NAME."
	WHERE		id_user = :iduser
	ORDER BY	timestp_create ASC";
$db = dims::getInstance()->db;
$res = $db->query($sel, array(
	':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
));
?>
<div style="padding-top:10px;">
	<table cellpadding="0" cellspacing="0" style="width: 100%;border-top:1px solid #D6D6D6;border-left:1px solid #D6D6D6;">
		<tr>
			<th style="padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_DIMS_DATE']; ?>
			</th>
			<th style="padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_NB_ELEMENTS']; ?>
			</th>
			<th style="padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_NB_IMPORTED']; ?>
			</th>
			<th style="padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_NB_ERRORS']; ?>
			</th>
			<th style="padding:5px;width: 75px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
		<?
		while($r = $db->fetchrow($res)){
			?>
			<tr>
				<td style="padding-left:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<?
					$d = dims_timestamp2local($r['timestp_create']);
					echo $d['date'];
					?>
				</td>
				<td style="text-align: center;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<?
					$sql = "SELECT	COUNT(id) as nb_ok
						FROM	".$r['ref_tmp_table'];
					$res2 = $db->query($sql);
					$nbelems = 0;
					if ($r2 = $db->fetchrow($res2))
						$nbelems = $r2['nb_ok'];
					echo $nbelems;

					?>
				</td>
				<td style="text-align: center;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<?
					$sql = "SELECT	COUNT(id) as nb_ok
						FROM	".$r['ref_tmp_table']."
						WHERE	status >= :status ";
					$res2 = $db->query($sql, array(
						':status' => _STATUS_IMPORT_OK_CT
					));
					$nbOk = 0;
					if ($r2 = $db->fetchrow($res2))
						$nbOk = $r2['nb_ok'];
					echo $nbOk;
					?>
				</td>
				<td style="text-align: center;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<? echo $nbelems-$nbOk; ?>
				</td>
				<td style="text-align: center;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<img style="cursor: pointer;" onclick="javascript:document.location.href='<?echo dims::getInstance()->getScriptEnv()."?import_op="._OP_MERGE_IMPORT."&id_import=".$r['id']; ?>';" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_edit_petit.png" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
				</td>
			</tr>
			<?
		}
		?>
	</table>
	<input style="float:right;margin-top:10px;" type="button" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_DEFAULT_IMPORT; ?>';" value="<? echo $_SESSION['cste']['_DIMS_BACK']; ?>" />
</div>
