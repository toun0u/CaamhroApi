<div class="title_h3">
    <h3><? echo $_SESSION['cste']['_DIMS_LABEL_TOOLS']; ?></h3>
</div>
<form name="replace" method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_STR_REPLACE; ?>">
	<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;">
		<tr class="table_ligne1">
			<td colspan="4">
				Publier l'ensemble des articles
			</td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_PUBLISH_ALL; ?>','<? echo $_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION']; ?>');">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_param.png'); ?>" alt="" title="" />
				</a>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				Générer la réécriture d'url
			</td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_GENERATE_URL; ?>','<? echo $_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION']; ?>');">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_param.png'); ?>" alt="" title="" />
				</a>
			</td>
		</tr>
		<tr class="table_ligne1">
			<td>
				Remplacer par
			</td>
			<td style="width:155px;">
				<input type="text" name="search" />
			</td>
			<td class="actions">
				>
			</td>
			<td style="width:155px;">
				<input type="text" name="replace" />
			</td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="javascript:dims_confirmform(document.replace,'<? echo $_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION']; ?>');">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_param.png'); ?>" alt="" title="" />
				</a>
			</td>
		</tr>
	</table>
</form>