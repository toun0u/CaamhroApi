<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset={$site.ENCODING}" />
<meta name="description" content="{$site.WORKSPACE_META_DESCRIPTION}" />
<meta name="keywords" content="{$site.WORKSPACE_META_KEYWORDS}" />
<meta name="author" content="{$site.WORKSPACE_META_AUTHOR}" />
<meta name="copyright" content="{$site.WORKSPACE_META_COPYRIGHT}" />
<meta name="robots" content="{$site.WORKSPACE_META_ROBOTS}" />
<title>{$site.WORKSPACE_TITLE}</title>
<link rel="icon" href="{$site.TEMPLATE_IMG_PATH}/img/favicon.png" type="image/png" />

<!-- Style manager -->
{$styles}

{$site.DIMS_NS_CSS}
{$site.ADDITIONAL_HEAD}
{$scripts}
</head>

{if (isset($switch_user_logged_out))}
<body class="bodyconnect" {if (isset($user.BACKGROUND) && $user.BACKGROUND!="")} style="background:url('data/users/$user.BACKGROUND') left top repeat;"{/if} {if (isset($site.BACKGROUND) && $site.BACKGROUND!="")} style="background:url('data/workspaces/{$site.BACKGROUND}')  left top repeat;"{/if}>
{/if}

{if (isset($switch_user_logged_in))}
<body class="bodyconnected" {if (isset($user.BACKGROUND) && $user.BACKGROUND!="")} style="background:url('data/users/{$user.BACKGROUND}') left top repeat;"{/if} {if (isset($site.BACKGROUND) && $site.BACKGROUND!="")} style="background:url('data/workspaces/{$site.BACKGROUND}')  left top repeat;"{/if}>
{/if}
<div id="toppage"></div>
<div id="popup_container">
	<div id="dims_popup" class="dims_popup ui-dialog ui-widget ui-widget-content ui-corner-all" style="display:none;"></div>
	<div id="dims_popup2" class="dims_popup"></div>
	<div id="overlay" class="overlay"></div>
	<div id="overlay2" class="overlay"></div>
</div>
<div id="dims_clipboard"></div>
<div id="calendardiv"></div>

<script type="text/javascript" src="{$site.SCRIPTS_COMMON_PATH}voip_call.js"></script>
<!-- FIXME: 404 & should use real cdn, not svn trunk project -->
<!--script type="text/javascript" src="http://closure-library.googlecode.com/svn/trunk/closure/goog/base.js"></script-->
<script type="text/javascript">
	goog.require('goog.proto2.Message');
</script>
<script type="text/javascript" src="/common/js/telephony/libphonenumber.js"></script>

<div id="container" {if ( ! isset($switch_user_logged_out))}class="ui-widget ui-widget-content" {/if}>
	{if (isset($switch_user_logged_out))}
			<div >
				<div id="dims_connexion" style="display:{if (isset($pass_forgotten))}none{else}block{/if};" class="prompt_connexion" >
					<form name="formlogin" action="admin.php" method="post" onsubmit="document.formlogin.submit();">
						{if (isset($dims_form_token))}{$dims_form_token}{/if}
						<input type="hidden" name="rfid-auth" id="rfid-auth" value="" />
						<div class="">
						   <h1>Bienvenue sur Dims v<span style="color: {$connexion_color};">6</span></h1>
						</div>
						<div>
							<div class="login-fields">
								<p>
									<input type="text" id="dims_login" name="dims_login" class="" placeholder="Identifiant" /></td>
								</p>
								<p>
									<input type="password" id="dims_password" name="dims_password" class="" placeholder="Mot de passe" /></td>
								</p>
							</div>
							{if (isset($switch_dimserrormsg))}
							<div class="error">
								Mauvais login ou mot de passe
							</div>
							{/if}
							<div class="actions_bloc">
								<input type="button" onclick="javascript:document.getElementById('dims_connexion').style.display='none';document.getElementById('lostpass').style.display='block';" class="normal" value="Mot de passe perdu ?"/>
								<input type="submit" value="Connexion" class="go"/>
							</div>

							<div class="login-footer">
								<div>
									Powered by <a href="http://www.dims.fr" style="color: {$connexion_color};">DIMS Portal</a>
								</div>
								<div>
									{$site.DIMS_VERSION}&nbsp;|&nbsp;<a class="whitelink" href="https://www.mozilla.org/fr/" target="_blank" style="color: {$connexion_color};">Get Firefox</a>
									&nbsp;| render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)
								</div>
							</div>
						</div>
					</form>
				</div>
				<div id="lostpass" style="display:{if (isset($pass_forgotten))}block{else}none{/if};" class="prompt_connexion">
					<form name="formpassword" action="admin.php?dims_op=forgot_password" method="post" onsubmit="document.formpassword.submit();">
						{if (isset($dims_form_token_pass))}{$dims_form_token_pass}{/if}
						<div class="">
							<h1>Bienvenue sur Dims v<span style="color: {$connexion_color};">6</span></h1>
						</div>
						<div>
							<div>Merci de remplir le formulaire suivant à l'aide de votre adresse email : </div>
							<div>
								<div class="login-fields">
									<p>
										<input type="text" id="dims_email" name="dims_email" class="" placeholder="Adresse email" /></td>
									</p>
								</div>
								{if (isset($pass_forgotten))}
									<div class="{if (! $pass_renouv) }error { else } green bigger_message{/if}">{$pass_forgotten}</div>
								{/if}
								<div class="actions_bloc">
									<input type="button" class="normal" onclick="javascript:window.location.href='./admin.php';" value="Annuler" />
									<input type="button" onclick="javascript:document.formpassword.submit();" value="Envoi" class="go"/>
								</div>
								<div class="login-footer">
									<div>
										Powered by <a href="http://www.dims.fr" style="color: {$connexion_color};">DIMS Portal</a>
									</div>
									<div>
										{$site.DIMS_VERSION}&nbsp;|&nbsp;<a class="whitelink" href="https://www.mozilla.org/fr/" target="_blank" style="color: {$connexion_color};">Get Firefox</a>
										&nbsp;| render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
	{/if}
	{if ((isset($switch_user_logged_in) && (((isset($dashboard_enabled) && $dashboard_enabled)) || !isset($dashboard_enabled))) || isset($switch_user_logged_in))}
		<div id="top">
			<span style="float:left;margin-top:3px;"><a style="padding:0px;" href="/admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1&init_desktop=1"><img src="{$site.IMAGES_COMMON_PATH}img/home.gif" style="border:0px;"></a></span>
			<span class="separator"></span>
			<span class="dropdown">
				<span>Applications</span>
				<div id="menu_dashboard" class="menu" style="display:none;">
					<ul>
						{foreach from=$tabs key=t item=tab}
							{if isset($tab.MODULES)}
								{if $tab.MODULES|@sizeof>1 || $tab.MODULES|@sizeof==0}
									<li><a href="{$tab.URL}">
										<img style="vertical-align:middle;" border="0" src="{$tab.IMG}"/>
										<span style="vertical-align:middle;">{$tab.TITLE}</span></a>
									</li>
									{foreach from=$tab.MODULES key=m item=module}
										<li>
											<a class="whitelink" href="{$module.URL}" title="{$module.DESC}">
												<img style="vertical-align:middle;" border="0" src="{$module.EXT}"/><span style="vertical-align:middle;">{$module.TITLE}</span>
											</a>
										</li>
									{/foreach}
								{else}
									{if $tab.MODULES|@sizeof==1}
										<li>
											<a class="whitelink" href="{$tab.MODULES[0].URL}" title="{$tab.MODULES[0].DESC}">
												<img style="vertical-align:middle;" border="0" src="{$tab.MODULES[0].EXT32}"/><span style="vertical-align:middle;">{$tab.MODULES[0].TITLE}</span>
											</a>
										</li>
									{/if}
								{/if}
							{else}
								<li><a href="{$tab.URL}">
									<img style="vertical-align:middle;" border="0" src="{$tab.IMG}"/>
									<span style="vertical-align:middle;">{$tab.TITLE}</span></a>
								</li>
							{/if}
						{/foreach}
					</ul>
				</div>
			</span>
			{if ((isset($user.ACCESGROUPMANAGER) && $user.ACCESGROUPMANAGER!='') || (isset($user.ACCESWORKSPACES) && $user.ACCESWORKSPACES!=''))}
				<span class="separator"></span>
				<span class="dropdown">
					<span>Administration</span>
					<div id="menu_admin" class="menu" style="display:none;">
						<ul>
							{if ((isset($user.ACCESGROUPMANAGER) && $user.ACCESGROUPMANAGER!=''))}
								<li>{$user.GROUPMANAGER_WITHLABEL}</li>
							{/if}
							{if ((isset($user.ACCESWORKSPACES) && $user.ACCESWORKSPACES!=''))}
								<li>{$user.ACCESWORKSPACES_WITHLABEL}</li>
							{/if}
						</ul>
					</div>
				</span>
			{/if}
			{if $workspaces|@count > 1}
				<span class="separator"></span>
				<span style="float:left;">{$smarty.session.cste._WORKSPACE}</span>

				<span style="float:left;margin:0px 2px">
					<select class="select" style="width:200px;" onchange="javascript:document.location.href=this.value;">
						{foreach from=$workspaces key=w item=workspace}
							<option value="{$workspace.URL}" onchange="" {$workspace.SELECTED}>{$workspace.TITLE}</option>
						{/foreach}
					</select>
				</span>
			{else}
				{if $workspaces|@count > 0}
					<span class="separator"></span>
					<span style="float:left;">{$smarty.session.cste._WORKSPACE} : {foreach from=$workspaces key=w item=workspace}{$workspace.TITLE}{/foreach}</span>
				{/if}
			{/if}

			<div style="float:right;">
				<span style="float:right;margin:3px 1px 0px 2px">
					<a href="{$site.USER_DECONNECT}"><img src="{$site.TEMPLATE_IMG_PATH}/img/logout.png" border="0" alt="D&eacute;connexion"></a>
				</span>

				{if (isset($site.MAINMENU_SHOWPROFILE_URL) && $site.MAINMENU_SHOWPROFILE_URL!="")}
					<span style="float:right;margin:2px 0px 0px 0px">
						<a href="{$site.MAINMENU_SHOWPROFILE_URL}" title="{$smarty.session.cste._PROFIL}">
						<img src="{$site.IMAGES_COMMON_PATH}img/user.png" /></a>
					</span>
				{/if}
				{if (isset($smarty.session.dims.constantizer) && $smarty.session.dims.constantizer)}
					<span style="float:right;margin:3px 1px 0px 2px">
						<a id="display_constantizer" href="javascript:void(0);"><img src="{$site.IMAGES_COMMON_PATH}img/configure.png"/></a>
					</span>
				{/if}

				<span style="float:right;"><a class="whitelink" style="font-weight:bold;" href="{$site.MAINMENU_SHOWPROFILE_URL}">{$user.FIRSTNAME}&nbsp;{$user.LASTNAME}</a></span>
				<span style="float:right;">{$smarty.session.cste._WELCOME}</span>
			</div>
			{if (isset($dims_switchusers))}
			<span style="float:right;margin:0px 2px">
				{$smarty.session.cste._SWITCH_ON}
				<select class="select" style="width:200px;" onchange="javascript:document.location.href=this.value;">
					{foreach from=$dims_switchusers key=w item=switchuser}
					<option value="/admin.php?dims_switchuserfromid={$switchuser.id}" {$switchuser.SELECTED}>{$switchuser.firstname} {$switchuser.lastname}</option>
					{/foreach}
				</select>
			</span>
		{/if}
		</div>
	{/if}

{if (isset($switch_user_logged_in))}
	<div id="page_content" style="width:100%;float:left;display:block;">{$site.PAGE_CONTENT}</div>
	</div>

	{if ($user.REAL_LANGUAGE != 'en')}
		<script type="text/javascript" src="{$site.ROOT_PATH}/js/datepicker_lang/jquery.ui.datepicker-{$user.REAL_LANGUAGE}.js"></script>
		{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$.datepicker.setDefaults( $.datepicker.regional["{/literal}{$user.REAL_LANGUAGE}{literal}"] );
			});
		</script>
		{/literal}
	{/if}
{else}
	<div id=""></div>
{/if}

{foreach from=$modules_js key=mc item=module_js}
	<script type="text/javascript" src="{$module_js.PATH}"></script>
{/foreach}

<script language="javascript">
{$site.ADDITIONAL_JAVASCRIPT}
//-->
{literal}
function dimsOnMouseUp() {
	detectSelectedText();
	updateTimerDesktop();
}

if(navigator.appName.indexOf("Netscape") != -1){
	document.captureEvents(Event.MOUSEDOWN);
	document.onmousedown =detectOpenPopup ;
	document.onmouseup=dimsOnMouseUp;
	document.onkeypress=updateTimerDesktop;
}
else if(navigator.appName.indexOf("Microsoft") != -1){
	document.onmousedown =detectOpenPopupIE;
	document.onmouseup=dimsOnMouseUp;
	document.onkeypress=updateTimerDesktop;
}
else {
	document.body.onmouseup=detectOpenPopup;
	document.body.onmouseup=dimsOnMouseUp;
	document.body.onkeypress=updateTimerDesktop;
}

$("#top .dropdown").mouseenter(function(){
	if(jQuery.browser.msie&&jQuery.browser.version<=7){
		$(this).addClass("hover")
	}
	$(this).find(".menu").css("display","block")
});

$("#top .dropdown").mouseleave(function(){
	if(jQuery.browser.msie&&jQuery.browser.version<=7){
		$(this).removeClass("hover")
	}
		$(this).find(".menu").css("display","none")
});

$(document).ready(function() {
	{/literal}
	{if !empty($user.PHONEVOIP)}
		{literal}
			if({/literal}{$user.PHONEVOIP}{literal} != "null"){
				//appel du script d'affichage voip_call qui affiche les icones de téléphonies
				$.each($("span[data-phone]:not([data-phoneone])"),function(){
					$(this).voip_call({/literal}{$user.PHONEVOIP}{literal},'/common/modules/system/desktopV2/templates//gfx/common/tel_sortant16.png');
				});
			}
		{/literal}
	{/if}
	{literal}

	$('a').click(function(){
		this.blur();
	});

	keepConnexion();

	$("input.activebutton").hover(
			function() { $(this).addClass('ui-state-hover'); },
			function() { $(this).removeClass('ui-state-hover'); }
	);

	if (document.getElementById('dims_login')!=null) {
		document.getElementById('dims_login').focus();
	}
});
</script>
{/literal}

{if (isset($smarty.session.dims.constantizer) && $smarty.session.dims.constantizer)}
<script type="text/javascript">
{literal}
// ------------------- init constantizer
	$('document').ready(function(){
		initConstantizer($('a#display_constantizer'));
	});
</script>
{/literal}
{/if}


</body>
</html>
