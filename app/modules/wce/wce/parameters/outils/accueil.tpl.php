<table class="outils" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_impor.png'); ?>">
		</td>
		<td>
			<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_export.png'); ?>">
		</td>
		<td>
			<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_actualiser.png'); ?>">
		</td>
	</tr>
	<tr>
		<td>
			<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_TOOLS."&action=".module_wce::_PARAM_TOOLS_IMPORT; ?>">
				<? echo $_SESSION['cste']['_IMPORT_SITE']; ?>
			</a>
		</td>
		<td>
			<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_TOOLS."&action=".module_wce::_PARAM_TOOLS_EXPORT; ?>">
				<? echo $_SESSION['cste']['_EXPORT_SITE']; ?>
			</a>
		</td>
		<td>
			<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_TOOLS."&action=".module_wce::_PARAM_TOOLS_SITEMAP; ?>">
				<? echo $_SESSION['cste']['_UPDATE_SITEMAP']; ?>
			</a>
		</td>
	</tr>
</table>