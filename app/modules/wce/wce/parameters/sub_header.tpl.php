<div class="title_h2">
	<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_param.png'); ?>">
	<h2>
		<? echo $_SESSION['cste']['_GENERAL_SETTINGS']; ?>
	</h2>
</div>
<div class="sous_rubrique">
	<ul>
		<li>
			<a <? echo ($sub == module_wce::_PARAM_INFOS)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_INFOS_LABEL'], 'UTF-8'); ?>
			</a>
		</li>
		<li>
			<a <? echo ($sub == module_wce::_PARAM_CONF)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_ADVANCED_CONFIGURATION'], 'UTF-8'); ?>
			</a>
		</li>
		<!-- TODO : à activer quand la page sera en place (Outils)
		<li>
			<a <? echo ($sub == module_wce::_PARAM_TOOLS)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_TOOLS; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_DIMS_LABEL_TOOLS'], 'UTF-8'); ?>
			</a>
		</li>
		-->
		<li>
			<a <? echo ($sub == module_wce::_PARAM_EXECUTE)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_DIMS_LABEL_TOOLS'], 'UTF-8'); ?>
			</a>
		</li>
	</ul>
</div>