<div>
	<h2>
		<?
		echo $_DIMS['cste']['_DIMS_LABEL_TEMPLATEWORKSPACE_LIST']." : ".$_SESSION['dims']['currentworkspace']['label'];
		?>
	</h2>
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_PARAM); ?>" name="save_tpl">
		<input type="hidden" name="sub" value="<? echo module_wce::_PARAM_CONF; ?>" />
		<input type="hidden" name="action" value="<? echo module_wce::_PARAM_CONF_SAVE_TEMPL; ?>" />
		<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 98%; border-collapse: collapse;">
			<?
			$db = dims::getInstance()->db;
			$availabletpl = dims_getavailabletemplates();
			$sql = "SELECT 	*
					FROM 	dims_workspace_template
					WHERE 	id_workspace=:id_workspace";
			$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
			while ($f = $db->fetchrow($res)) {
				if (in_array($f['template'], $availabletpl)) {
					$lsttplcurrent[] = $f['template'];
				}
			}
			$trl = 'class="table_ligne1"';
			foreach ($availabletpl as $tpl) {
				$check = (in_array($tpl, $lsttplcurrent))?"checked=true":"";
				?>
				<tr <? echo $trl; ?>>
					<td>
						<input type="checkbox" name="seltpl[]" <? echo $check; ?> value="<? echo $tpl; ?>">
					</td>
					<td>
						<? echo $tpl; ?>
					</td>
				</tr>
				<?
				$trl = ($trl == '')?'class="table_ligne1"':'';
			}
			?>
			<tr>
				<td colspan="2">
					<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					<? echo $_SESSION['cste']['_DIMS_OR']; ?>
					<a href="javascript:void(0);" onclick="javascript:dims_hidepopup();">
						<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
					</a>
				</td>
			</tr>
		</table>
	</form>
</div>