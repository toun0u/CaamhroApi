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
<link rel="icon" href="{$site.TEMPLATE_PATH}/img/favicon.png" type="image/png" />
<!--
<link type="text/css" rel="stylesheet" href="{$site.TEMPLATE_PATH}/css/main.css" media="screen" />
<link type="text/css" rel="stylesheet" href="{$site.TEMPLATE_PATH}/css/jquery-ui/{$site.CSS_FILE}/jquery-ui.css" media="screen" />
<link type="text/css" rel="stylesheet" href="{$site.TEMPLATE_PATH}/css/dt_page.css" media="screen" />
<link type="text/css" rel="stylesheet" href="{$site.TEMPLATE_PATH}/css/dt_table_jui.css" media="screen" />
-->
{$site.DIMS_NS_CSS}
{$site.ADDITIONAL_HEAD}
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
<script type="text/javascript" src="{$site.ROOT_PATH}/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="{$site.ROOT_PATH}/js/jquery-ui-1.8.11.custom.min.js"></script>
<script type="text/javascript" src="{$site.ROOT_PATH}/js/mootools-1.2.4-core-yc.js"></script>
<script type="text/javascript" src="{$site.ROOT_PATH}/js/portal_v5.js"></script>
<script type="text/javascript" src="{$site.ROOT_PATH}/js/dims_chat.js"></script>
<script type="text/javascript" src="{$site.ROOT_PATH}/js/jquery.dataTables.min.js"></script>

<div id="container" {if (isset($switch_user_logged_out))}style="width:500px;position:relative;margin:150px auto;"{else} class="ui-widget ui-widget-content" {/if}>
	{if (isset($switch_user_logged_out))}
            <div style="padding:8px;">
                <div id="dims_connexion" style="display:{if (isset($pass_forgotten))}none{else}bloc{/if};" class="ui-widget ui-widget-content ui-corner-all" >
                    <form name="formlogin" action="admin.php" method="post" onsubmit="document.formlogin.submit();">
                    {if (isset($dims_form_token))}{$dims_form_token}{/if}
                        <div class="ui-widget-header ui-corner-top">
                            <img height="60" width="120" alt="Dims logo" id="dims_logo" src="{$site.TEMPLATE_PATH}/img/logo_dims_v2.png">
                           <h1 style="margin-left:150px;font-size:14px;">Bienvenue sur Dims</h1>
                        </div>
                        <div id="loginbackground" class="" style="border:0px;height:35Opx;">
			    <table style="width:50%">
				<tr style="text-align:right;">
					<td><label style="text-align:right;">Login</label></td>
					<td><input type="text" id="dims_login" name="dims_login" class="ui-autocomplete" style="position:relative;" size="16"/></td>
				</tr>
				<tr style="text-align:right;">
					<td><label>Mot de passe</label></td>
					<td><input type="password" id="dims_password" name="dims_password" class="ui-autocomplete" style="position:relative;" size="16"/></td>
				</tr>
			    </table>
                            {if (isset($switch_dimserrormsg))}
                                <div style="float:left;width:100%;text-align:center;clear:both;">
                                    <img src="{$site.TEMPLATE_PATH}/img/system/attention.png"><span class="error">&nbsp;{$site.DIMS_ERROR}</span>
                                </div>
                            {/if}
                            <div style="width:100%;clear:both;margin:10px 0px 20px 0px;height:24px;text-align:center;">
                                <input type="submit" value="Connexion" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false"/>
                                <input type="button" onclick="javascript:document.getElementById('dims_connexion').style.display='none';document.getElementById('lostpass').style.display='block';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="Mot de passe perdu"/>
                            </div>

                            <div style="float:left;font-size:10px;margin-top:10px;margin-bottom:5px;text-align:center;width:100%;">
                                <a href="http://www.dims.fr">Powered by DIMS<font style="color:#adbc2d">Portal&nbsp;<img style="border:0px" src="{$site.TEMPLATE_PATH}/img/dims_mini.png"/></font></a>
                            </div>
                            <div style="font-size:10px;margin:0px 0px 20px 0px;text-align:center;">
                                    {$site.DIMS_VERSION}&nbsp;|&nbsp;<a class="whitelink" href="http://www.mozilla-europe.org/fr/products/firefox/">Get Firefox</a>
                                    &nbsp;| render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)
                            </div>
                        </div>
                    </form>
                </div>
                <div id="lostpass" style="display:{if (isset($pass_forgotten))}bloc{else}none{/if};" class="ui-widget ui-widget-content ui-corner-all">
                    <form name="formpassword" action="admin.php?dims_op=forgot_password" method="post" onsubmit="document.formpassword.submit();">
                        <div class="ui-widget-header ui-corner-top">
                            <img height="60" width="120" alt="Dims logo" id="dims_logo" src="{$site.TEMPLATE_PATH}/img/logo_dims_v2.png">

                            <h1 style="margin-left: 150px;font-size:14px;">Bienvenue sur Dims</span></h1>

                        </div>
                        <div id="loginbackground" class="" style="border:0px;">
                            <div style="width:100%;clear:both;margin:4px 0px;text-align:center;">
                                {if (isset($pass_forgotten))}
                                    <div style="margin:10px 0px;text-align:center;width:100%;color:#ff0000;font-size:12px;">{$pass_forgotten}</div>
                                {/if}
                                <div style="margin:10px 0px;text-align:center;width:100%;color:#000000;margin-top:10px;">Merci de remplir le formulaire suivant à l'aide de votre adresse mel : </div>
                                <span style="margin:15px 0px;text-align:right;width:24%;color:#000000">Adresse mel</span>

                                <span style="margin:10px 10px;">
                                        <input type="text" style="border:1px solid #333333;width:150px;" id="dims_email" name="dims_email" size="16" tabindex="1"/>
                                </span>
                                <div style="width:100%;clear:both;margin:10px 0px 12px 0px;height:24px;text-align:center;">
                                    <input type="button" class="ui-button ui-state-default ui-corner-all activebutton" onclick="javascript:document.formpassword.submit();" aria-disabled="false" value="Envoi"/>
                                    <input type="button" onclick="javascript:window.location.href='./admin.php';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="Annuler"/>
                                </div>

                            </div>
                            <div style="float:left;font-size:0.9em;margin-top:10px;text-align:center;width:100%;">
                                <a href="http://www.dims.fr"><font style="color:#000000">Powered by DIMS</font> <font style="color:#adbc2d">Portal&nbsp;<img style="border:0px" src="{$site.TEMPLATE_PATH}/img/dims_mini.png"/></font></a>
                            </div>
                            <div style="font-size:0.9em;margin:0px 0px 20px 0px;text-align:center;color:#000000;">
                                    {$site.DIMS_VERSION}&nbsp;|&nbsp;<a class="whitelink" href="http://www.mozilla-europe.org/fr/products/firefox/">Get Firefox</a>
                                    &nbsp;| render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)
                            </div>
                            <? if (isset($pass_forgotten)) unset($pass_forgotten); ?>
                        </div>
                    </form>
                </div>
            </div>
  	{/if}

  	{if (isset($switch_user_logged_in))}
    <div id="top">
        <span style="float:left;margin-top:3px;"><a style="padding:0px;" href="/admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1&init_desktop=1"><img src="./common/img/home.gif" style="border:0px;"></a></span>
        <span class="separator"></span>
        <span class="dropdown">
            <span>Dashboard</span>
            <div id="menu_dashboard" class="menu" style="display:none;">
                <ul>
                    {foreach from=$tabs key=t item=tab}
                        <li><a href="{$tab.URL}">
                            <img style="vertical-align:middle;" border="0" src="{$tab.IMG}"/>
                            <span style="vertical-align:middle;">{$tab.TITLE}</span></a>
                        </li>
                        {if isset($tab.MODULES) && $tab.MODULES|@sizeof>1}
                        {foreach from=$tab.MODULES key=m item=module}
                                                    <li>
                                                        <a class="whitelink" href="{$module.URL}" title="{$module.DESC}">
                            {$module.EXT}<span style="vertical-align:middle;">{$module.TITLE}</span>
                            </a>
                        </li>
                        {/foreach}
                        {/if}
                    {/foreach}
                </ul>
            </div>
		</span>
        <span class="separator"></span>
		{if ((isset($user.ACCESGROUPMANAGER) && $user.ACCESGROUPMANAGER!='') || (isset($user.ACCESWORKSPACES) && $user.ACCESWORKSPACES!=''))}
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
            <span class="separator"></span>
		{/if}
        <span style="float:left;">{$smarty.session.cste._WORKSPACE}</span>
        <span style="float:left;margin:4px 2px 0px 2px">
            <a href="#" onclick="javascript:displayMapWorkspaces(event);"><img src="./common/img/arbo.gif" border="0" alt="Visualisation des espaces" title="Visualisation des espaces"></a>
			</span>
        <span style="float:left;margin:0px 2px">
            <select class="select" style="width:200px;" onchange="javascript:document.location.href=this.value;">
		{foreach from=$workspaces key=w item=workspace}
			<option value="{$workspace.URL}" onchange="" {$workspace.SELECTED}>{$workspace.TITLE}</option>
		{/foreach}
		</select>
        </span>

        <div style="float:right;">
                        <span style="float:right;margin:3px 1px 0px 2px">
                <a href="{$site.USER_DECONNECT}"><img src="{$site.TEMPLATE_PATH}/img/logout.png" border="0" alt="D&eacute;connexion"></a>
            </span>
		{if (isset($site.MAINMENU_SHOWPROFILE_URL) && $site.MAINMENU_SHOWPROFILE_URL!="")}
            <span style="float:right;margin:2px 0px 0px 0px">
                <a href="{$site.MAINMENU_SHOWPROFILE_URL}" title="{$smarty.session.cste._PROFIL}">
                <img src="./common/img/user.png" /></a>
            </span>
		{/if}
        {if (isset($smarty.session.dims.constantizer) && $smarty.session.dims.constantizer)}
			<span style="float:right;margin:3px 1px 0px 2px">
				<a class="a_switch_cstz" href="javascript:void(0);"><img src="./common/img/configure.png"/></a>
			</span>
			<div class="constantizer">
				<div class="close"><a href="javascript:void(0);" onclick="javascript:$('div.constantizer').toggle();"> <img src="./common/img/close.png" title="close"/></a></div>
				<div class="search">
					<p>
						<label for="cstz_text">Expression : </label><input type="text" name="cstz_text" id="cstz_text" />
					</p>
				</div>
				<div class="results">
				</div>
				<div class="footer">
				</div>
			</div>
		{/if}
            <span style="float:right;"><a class="whitelink" style="font-weight:bold;" href="{$site.MAINMENU_SHOWPROFILE_URL}">{$user.FIRSTNAME}&nbsp;{$user.LASTNAME}</a></span>
            <span style="float:right;">{$smarty.session.cste._WELCOME}</span>
	</div>

	{/if}
</div>
{if (isset($switch_user_logged_in))}

<div id="page_content" style="width:100%;float:left;display:block;">{$site.PAGE_CONTENT}</div>

<div  style="width: 100%; clear: both; float:left;display: block;">

    <div style="margin: 0pt auto;visibility: visible; width: 300px;">
        <table cellspacing="0" cellpadding="0" border="0" summary="#">
            <tr>
                <td class="bgb32"></td>
                <td class="midb32">
                <div style="float:left;font-size:0.9em;margin-top:0px;text-align:center;width:260px;">
                    <a href="http://www.dims.fr" target="_blank"><font style="color:#ffffff">Powered by DIMS</font> <font style="color:#adbc2d">Portal &nbsp;{$site.DIMS_VERSION}</font></a>

		</div>
                <div style="float:left;clear:both;width:260px;font-size:0.9em;margin-top:0px;text-align:center;color:#FFFFFF;">
			-&nbsp;render {$site.DIMS_EXEC_TIME} ms - sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)&nbsp;-
		</div>
	</div>
                </td>
                <td class="bdb32"></td>
            </tr>
        </table>

</div>
</div>
{/if}
{if (isset($user.REAL_LANGUAGE) && $user.REAL_LANGUAGE != 'en')}
    <script type="text/javascript" src="{$site.ROOT_PATH}/js/datepicker_lang/jquery.ui.datepicker-{$user.REAL_LANGUAGE}.js"></script>
    {literal}
    <script type="text/javascript">
        $(document).ready(function(){
            $.datepicker.setDefaults( $.datepicker.regional["{/literal}{$user.REAL_LANGUAGE}{literal}"] );
        });
    </script>
    {/literal}
{/if}
{foreach from=$modules_js key=mc item=module_js}
<script type="text/javascript" src="{$module_js.PATH}"></script>
{/foreach}

<!--[if IE]><script type="text/javascript" src="/js/excanvas.js"></script><![endif]-->

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

$$("#top .dropdown").addEvents({
    mouseenter:function(){
        if(jQuery.browser.msie&&jQuery.browser.version<=7){
            this.addClass("hover")
        }
        this.getElement(".menu").setStyle("display","")
    }
    ,mouseleave:function(){
        if(jQuery.browser.msie&&jQuery.browser.version<=7){
        this.removeClass("hover")}this.getElement(".menu").setStyle("display","none")
        }
});

$(document).ready(function() {
    //redirectConnexion();
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

$("a.a_switch_cstz").click(function(){
	$('div.constantizer').toggle();
});

selected = 0;

$('input#cstz_text').keyup(function() {
	var value = $(this).val();
	if(value.length >= 2) {
		$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'constantizer',
				'value': value
			},
			dataType: "json",
			async: true,
			success: function(data){
				if(data.length > 0){
					var list ='';
					for(var i=0;i<data.length;i++){
						list += '<div id="cste_'+data[i]['id']+'"><a class="constante" href="javascript:void(0);" onclick="javascript:displayConstante('+data[i]['id']+', \''+data[i]['phpvalue']+'\');">'+data[i]['value']+'</a></div>';
					}

					$('div.constantizer div.results').html(list);
				}
				else $('div.constantizer div.results').html('<span style="font-style:italic">Aucune constante ne correspond à cette recherche</span>');

			},
			error: function(data){
			}
		});
	}
	else{
		$('div.constantizer div.results').html('');
		$('div.constantizer div.footer').text('');
	}
});

function displayConstante(id, constante){
	if(selected != 0) $('div#cste_'+selected+' a').removeClass('selected');
	selected = id;
	//$('div.constantizer div.footer').html(constante);
//<a href="javascript:void(0);" onclick="javascript:copy(document.getElementById(\'cste_selected\'));" title="Copier dans le presse-papier"><img src="./common/img/btn_edit.png"/></a>
	$('div.constantizer div.footer').html('<p>$_SESSION[\'cste\'][\''+constante+'\']</p>');
	$('div#cste_'+id+' a.constante').addClass('selected');
}
</script>
{/literal}
</body>
</html>
