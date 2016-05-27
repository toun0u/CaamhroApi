<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="_your description goes here_" />
<meta name="keywords" content="_your,keywords,goes,here_" />
<meta name="author" content="_your name goes here_  / Original design: Andreas Viklund - http://andreasviklund.com/" />
<link rel="icon" href="{TEMPLATE_PATH}icons/16/web.png" type="image/png" />
<link type="text/css" rel="stylesheet" href="{TEMPLATE_PATH}pda.css" media="screen" title="styles" />
<title>{HEADINGS_TITLE}</title>
<script type="text/javascript">
//<!--

var lstmsg = new Array();
lstmsg[0] = "L'adresse mèl n'est pas valide.\nIl n'y a pas de caractère @\nUne adresse mèl valide est du type \"adresse@domaine.com\"";
lstmsg[1] = "L'adresse mèl n'est pas valide.\nIl ne peut pas y avoir un point (.) juste après @\nUne adresse mèl valide est du type \"adresse@domaine.com\"";
lstmsg[2] = "L'adresse mèl n'est pas valide.\nL'adresse mèl ne peut pas finir par un point (.)\nUne adresse mèl valide est du type \"adresse@domaine.com\"";
lstmsg[3] = "L'adresse mèl n'est pas valide.\nL'adresse mèl ne peut pas contenir 2 points (.) qui se suivent.\nUne adresse mèl valide est du type \"adresse@domaine.com\"";
lstmsg[4] = "Le champ '<FIELD_LABEL>' ne doit pas être vide";
lstmsg[5] = "Le champ '<FIELD_LABEL>' doit être un nombre entier valide";
lstmsg[6] = "Le champ '<FIELD_LABEL>' doit être un nombre réel valide";
lstmsg[7] = "Le champ '<FIELD_LABEL>' doit être une date valide";
lstmsg[8] = "Le champ '<FIELD_LABEL>' doit être une heure valide";
lstmsg[9] = "Vous devez sélectionner une valeur pour le champ '<FIELD_LABEL>'";
error_bgcolor = "#FCE6D6";

//-->
</script>

</head>

<body>
<!-- BEGIN switch_user_logged_out -->
<div id="login" style="font-size:8px;">
	<p style="text-align:center"><img src="{TEMPLATE_PATH}/img/logosmall.png" alt="Netlor"/></p>
	<form name="formlogin" action="admin.php" method="post" {DIMS_TEST_MAC}>
	<table style="font-size:8px;">
		<tr>
			<td width="50%"><label for="dims_login">Identifiant</label>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="dims_login" name="dims_login" size="10" /></td>
		</tr>
		<tr>
			<td width="50%"><label for="dims_password">Mot de passe</label>:
			<input type="password" id="dims_password" name="dims_password" size="10" /></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="connexion"  class="button" />
			</td>
		</tr>
	</table>
	</form>
	</br>
</div>
<!-- END switch_user_logged_out -->
<div id="mainmenu" style="font-size:8px;padding: 0px;background-color:#FFEEEE;">

<!-- BEGIN switch_user_logged_in -->
	<b>{USER_FIRSTNAME} {USER_LASTNAME}</b>
	<a class="HOMELINK" href="{MAINMENU_SHOWSEARCH_URL}"><img src="{TEMPLATE_PATH}/icons/16/home.png" border="0" alt="Mes tickets"></a>
	|<a class="{MAINMENU_SHOWTICKETS_SEL}" href="{MAINMENU_SHOWTICKETS_URL}&viewmode=pda"><img border="0" src="{TEMPLATE_PATH}/icons/16/mail.png" alt="Mes tickets"></a>
	|<a class="{MAINMENU_SHOWANNOTATIONS_SEL}" href="{MAINMENU_SHOWANNOTATIONS_URL}"><img border="0" src="{TEMPLATE_PATH}/icons/16/annotations.png" alt="Mes annotations"></a>
	|<a class="{MAINMENU_SHOWPROFILE_SEL}" href="{MAINMENU_SHOWPROFILE_URL}&viewmode=pda"><img border="0" src="{TEMPLATE_PATH}/icons/16/personal.png" alt="Profil"></a>
	|<a href="{USER_DECONNECT}"><img src="{TEMPLATE_PATH}/icons/16/stop.png" border="0" alt="se déconnecter"></a>
	<br/>
	Esp.<select class="select" style="font-size:8px;width:70px;" onchange="javascript:document.location.href=this.value;">
		<!-- BEGIN workspace -->
		        <option value="{switch_user_logged_in.workspace.URL}" {switch_user_logged_in.workspace.SELECTED}>{switch_user_logged_in.workspace.TITLE}</option>
		<!-- END workspace -->
	</select>
<!-- END switch_user_logged_in -->

<!-- BEGIN switch_blockmenu -->
Mod.<select class="select" style="font-size:8px;width:100px;" onchange="javascript:document.location.href=this.value;">
		<!-- BEGIN block -->
		<option value="{switch_blockmenu.block.URLPDA}" {switch_blockmenu.block.SELECTED}>{switch_blockmenu.block.TITLE}</option>
		<!-- END block -->
</select>
</div>
<!-- END switch_blockmenu -->
<!-- BEGIN switch_blockportal -->
	Mod.<select class="select" style="font-size:8px;width:100px;" onchange="javascript:document.location.href=this.value;">
	<!-- BEGIN column -->
		<!-- BEGIN block -->
			<option value="{switch_blockportal.column.block.URLPDA}" {switch_blockportal.column.block.SELECTED}>{switch_blockportal.column.block.TITLE}</option>
		<!-- END block -->
	<!-- END column -->
	</select>
<!-- END switch_blockportal -->
</div>
<div>{PAGE_CONTENT}</div>


<div id="footer" style="text-align:center;font-size:8px">DIMS Mobility Business | <DIMS_PAGE_SIZE> kB | <DIMS_EXEC_TIME> ms</div>
</body>
</html>
