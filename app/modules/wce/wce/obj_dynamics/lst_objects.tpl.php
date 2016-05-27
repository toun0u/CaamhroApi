<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
	<tr>
		<td class="title_table_accueil">
			<? echo $_SESSION['cste']['_OBJECT_NAME']; ?>
		</td>
		<td class="title_table_accueil">
			<? echo $_SESSION['cste']['_TEMPLATE']; ?>
		</td>
		<td class="title_table">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$lstobj=$wce_site->getDynamicObjects();
	if (empty($lstobj)){
		?>
		<tr>
			<td colspan="3">
				<? echo $_SESSION['cste']['_NO_OBJECT_PRESENT']; ?>
			</td>
		</tr>
		<?
	}else{
		$class = 'class="table_ligne1"';
		foreach($lstobj as $obj){
			?>
			<tr <? echo $class; ?>>
				<td>
					<? echo $obj['label']; ?>
				</td>
				<td>
					<? echo $obj['template']; ?>
				</td>
				<td class="actions">
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT."&id=".$obj['id']; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_modif.png'); ?>" />
					</a>
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$obj['id']; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_voir.png'); ?>" />
					</a>
					<a onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_DEL."&id=".$obj['id']; ?>','<? echo $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');" href="javascript:void(0);">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_suppr.png'); ?>" />
					</a>
				</td>
			</tr>
			<?
			$class = ($class == '')?'class="table_ligne1"':'';
		}
	}
	?>
</table>
