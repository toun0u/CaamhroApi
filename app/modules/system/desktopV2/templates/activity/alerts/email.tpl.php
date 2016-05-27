<?php
$obj = $this->getNotifObject();

// recherche de l'expediteur
$expeditor = '';
$workspace = new workspace();
$workspace->open($obj->fields['id_workspace']);
if ($workspace->fields['email_noreply'] != '') {
	$expeditor = $workspace->fields['email_noreply'];
}

// responsable
if ($obj->fields['id_responsible'] == $id_dest) {
	$responsible = 'Vous-même';
}
else {
	$responsible = $resp->fields['firstname'].' '.$resp->fields['lastname'];
}

// participants
$participants = '';
if (!empty($linkedObjectsIds['distribution']['contacts'])) {
	$rs = $db->query('
		SELECT	*
		FROM	dims_mod_business_contact
		WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')', $params);
	if ($db->numrows($rs)) {
		while ($row = $db->fetchrow($rs)) {
			$participants .= '- '.$row['firstname'].' '.$row['lastname'].' ('.$row['email'].')<br/>';
		}
	}
}

// opportunités liées
$opportunities = '';
if (!empty($linkedObjectsIds['distribution']['opportunities'])) {
	$params = array();
	$rs = $db->query('
		SELECT *
		FROM dims_mod_business_action
		WHERE id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['opportunities']), 'idglobalobject', $params).')', $params);
	if ($db->numrows($rs)) {
		while ($row = $db->fetchrow($rs)) {
			$opportunities .= '- '.stripslashes($row['libelle']).'<br/>';
		}
	}
}


// sujet
$subject = 'RAPPEL: '.$obj->getTitle();

// contenu du message
$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
		<title>'.$subject.'</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="title" content="'.$subject.'">
		<meta name="description" content="'.$subject.'">
	</head>
	<body>
		<table width="100%">
		<tr>
			<td>
			<!-- HEADER -->
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="770px">
				<tr>
				<td height="80px" width="770px"><img name="IC_Mail_Banner.png" src="'.dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/modules/system/desktopV2/templates/activity/alerts/gfx/dims_banner_alert.png" width="770" height="80" alt="Bannière Dims" style="display: block;" border="0"></td>
				</tr>
			</table><!-- FIN HEADER -->
			<!-- CONTENT -->
			<table cellspacing="0" align="center" width="770" bgcolor="#F1F1F1">
				<tr>
				<td valign="top">
					<table cellpadding="40" cellspacing="0" align="center" width="600">
					<tr>
						<td valign="top" align="justigy"><font face="Arial" size="4" color="#434343">
											<strong>'.$subject.'</strong><br/><br/>

											<strong>Responsable :</strong> '.$responsible.'<br/><br/>';

if ($participants != '') {
	$message .= '<strong>Participants :</strong><br/>'.$participants.'<br/>';
}

if ($opportunities != '') {
	$message .= '<strong>Opportunités liées :</strong><br/>'.$opportunities.'<br/>';
}

$message .= '
											<strong>Localisation :</strong> '.$obj->fields['address'].' '.$obj->fields['cp'].' '.$obj->fields['lieu'].'<br/><br/>

											Vous pouvez consulter la fiche de l\'activité en suivant ce lien :<br/>
											<a href="'.$this->getProtocol().$this->getDomain().$obj->getLink().'">Consulter la fiche de l\'activité</a>
						</td>
					</tr>
					</table>
				</td>
				</tr>
			</table><!-- FIN CONTENT -->
			<!-- FOOTER -->
			<table cellspacing="0" align="center" width="770">
				<tr bgcolor="#D9DADB">
				<td valign="top">
					<table cellpadding="20" cellspacing="0" align="center" width="500">
					<tr>
						<td valign="top" align="center"><font face="Arial" size="1" color="#5F5F5F">Dims Portal v5</font></td>
					</tr>
					</table>
				</td>
				</tr>
			</table><!-- FIN FOOTER -->
			</td>
		</tr>
		</table>
	</body>
	</html>';
