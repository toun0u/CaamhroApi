<?php

function dims_tickets_selectusers($show_message = false, $userlist = null, $width = 500) {
	if (isset($_SESSION['dims']['tickets']['users_selected'])) unset($_SESSION['dims']['tickets']['users_selected']);
	if (isset($_SESSION['dims']['tickets']['workspaces_selected'])) unset($_SESSION['dims']['tickets']['workspaces_selected']);
	if (isset($_SESSION['dims']['tickets']['groups_selected'])) unset($_SESSION['dims']['tickets']['groups_selected']);
	?>
	<table cellpadding="0" cellspacing="0" style="width:<? echo $width; ?>;">
	<?
	if ($show_message)
	{
		?>
		<tr>
			<td><textarea id="dims_ticket_message" name="dims_ticket_message" class="text" style="width:<? echo $width-10; ?>px;height:50px"></textarea></td>
		</tr>
		<?
	}
	if (is_null($userlist))
	{
		?>
		<tr>
			<td>
				Destinataires qui vont recevoir un message :
				<div id="div_ticket_users_selected" style="padding:2px 0 0 0;">
				</div>
			</td>
		</tr>
		<tr>
			<td>
			<table style="padding:2px 0 0 0" cellspacing="0">
				<tr>
					<td>Recherche destinataires:&nbsp;&nbsp;</td>
					<td><input type="text" id="dims_ticket_userfilter" class="text">&nbsp;</td>
					<td><img onmouseover="javascript:this.style.cursor='pointer';" onclick="dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_search_users&dims_ticket_userfilter='+dims_getelem('dims_ticket_userfilter').value,'','div_ticket_search_result');" style="border:0px" src="./common/img/icon_loupe.png"></td>
				</tr>
			</table>
			</td>
		</tr>
		<?
	}
	else
	{
		foreach($userlist as $userid)
		{
			$_SESSION['dims']['tickets']['users_selected'][$userid] = $userid;
		}
	}
	?>
	</table>
	<?
	if (is_null($userlist))
	{
		?>
		<div id="div_ticket_search_result" style="padding:2px 0 6px 0;">
		</div>
		<?
	}
}

function dims_tickets_send($title, $message, $needed_validation = 0, $timestmp_validation = 0, $delivery_notification = 0, $id_object = '', $id_record = '', $object_label = '',$systemsend=false)
{
	$db = dims::getInstance()->getDb();
	global $scriptenv;
	//global $urlpath;
	if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") {
		$urlpath = "http://".$_SERVER['HTTP_HOST']."/admin.php";
	}
	else
		$urlpath = "https://".$_SERVER['HTTP_HOST']."/admin.php";

	require_once DIMS_APP_PATH . '/modules/system/class_user.php';
	require_once DIMS_APP_PATH . '/modules/system/class_ticket.php';
	require_once DIMS_APP_PATH . '/modules/system/class_ticket_dest.php';
	require_once DIMS_APP_PATH . '/modules/system/class_ticket_watch.php';

	if ($message == '') {
		$message = dims_load_securvalue('dims_ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true, true);
	}

	$id_user		= $_SESSION['dims']['userid'];
	$id_workspace	= $_SESSION['dims']['workspaceid'];
	$id_module		= $_SESSION['dims']['moduleid'];
	global $dims;
	$mod=$dims->getModule($id_module,$id_workspace);
	$id_module_type = $mod['id_module_type'];
	$module_name	= $mod['instancename'];

	if (isset($_SESSION['dims']['tickets']['users_selected']) || isset($_SESSION['dims']['tickets']['workspaces_selected']) || isset($_SESSION['dims']['tickets']['groups_selected'])) {
		$ticket = new ticket();
		$ticket->fields['id_object'] = $id_object;
		$ticket->fields['id_record'] = $id_record;
		$ticket->fields['id_module'] = $id_module;
				$ticket->fields['id_workspace'] = $id_workspace;
		$ticket->fields['id_user'] = $id_user;
		//$ticket->fields['id_user_dest'] = $user_id;
		$ticket->fields['object_label'] = $object_label;
		$ticket->fields['title'] = $title;
		$ticket->fields['message'] = $message;
		$ticket->fields['needed_validation'] = $needed_validation;
				$ticket->fields['time_limit'] = $timestmp_validation;
		$ticket->fields['delivery_notification'] = $delivery_notification;
		$ticket->fields['timestp'] = dims_createtimestamp();
		$ticket->fields['lastreply_timestp'] = $ticket->fields['timestp'];
		$id_ticket = $ticket->save();

		$email_subject = "Vous avez recu un nouveau ticket : {$title}";
		$email_message = $message;

		// construction du lien vers l'objet :
		if ($id_object!="" && $id_record>0) {
			// recherche du script d'acc�s � l'objet
			$select =
			"SELECT		script
			FROM		dims_mb_object
			WHERE		id=:idobject and id_module_type=:idmoduletype";

			$answer = $db->query($select, array(':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object), ':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $id_module_type)));
			if ($fields = $db->fetchrow($answer)) {
				$object_script = str_replace('<IDRECORD>',$id_record,$fields['script']);
				$object_script = str_replace('<IDMODULE>',$id_module,$object_script);

				$email_message ="<html><body>".$email_message."<br><span><a href=\"".dims_urlencode($urlpath."?dims_mainmenu=1&dims_workspaceid=".$id_workspace."&".$object_script)."\"><img src=\""."http://".$_SERVER['HTTP_HOST']."/".$_SESSION['dims']['template_path']."./common/img/system/link.png\">Lien vers le site ".$_SERVER['HTTP_HOST']." : module ".$module_name." / ".$object_label."</a></body></html>";
			}
		}


		if (!empty($_SESSION['dims']['user']['email']) && !$systemsend)
		{
			$email_from[0] = array(	'address'	=> $_SESSION['dims']['user']['email'],
								'name'	=> "{$_SESSION['dims']['user']['firstname']} {$_SESSION['dims']['user']['lastname']}"
							);
		}
		else
		{
			$email_from[0] = array(	'address'	=> _DIMS_ADMINMAIL,
								'name'	=> _DIMS_ADMINMAIL
							);
		}

		$users = array(); // destinataires
		if (isset($_SESSION['dims']['tickets']['users_selected'])) $users = $_SESSION['dims']['tickets']['users_selected'];
		if (isset($_SESSION['dims']['tickets']['workspaces_selected'])) {
			foreach($_SESSION['dims']['tickets']['workspaces_selected'] as $workspace_id)
			{
				$workspace = new workspace();
				$workspace->open($workspace_id);
				$w_users = $workspace->getusers();
				foreach($w_users as $id_user => $user)
				{
					$users[$id_user] = $id_user;
				}
			}
		}
		if (isset($_SESSION['dims']['tickets']['groups_selected'])) {
			foreach($_SESSION['dims']['tickets']['groups_selected'] as $group_id) {
				$group = new group();
				$group->open($group_id);
				$g_users = $group->getusers();
				foreach($g_users as $id_user => $user) {
					$users[$id_user] = $id_user;
				}
			}
		}

		foreach($users as $user_id) {
			echo "ticket send to $user_id<br/>";
			$user = new user();
			$user->open($user_id);
			if ($user->fields['ticketsbyemail'] == 1 && !empty($user->fields['email']))  {
				$email_to[0] = array(	'address'	=> $user->fields['email'],
									'name'	=> "{$user->fields['firstname']} {$user->fields['lastname']}"
								);

				require_once DIMS_APP_PATH . "/include/functions/mail.php";
				dims_send_mail($email_from, $email_to, $email_subject, $email_message);
				echo "mail send to $user_id<br/>";
			}
			//$ticket->fields['id_user_dest'] = $user_id;
			$ticket_dest = new ticket_dest();
			$ticket_dest->fields['id_user'] = $user_id;
			$ticket_dest->fields['id_ticket'] = $id_ticket;
			$ticket_dest->save();

			$ticket_watch = new ticket_watch();
			$ticket_watch->fields['id_user'] = $user_id;
			$ticket_watch->fields['id_ticket'] = $id_ticket;
			$ticket_watch->fields['notify'] = 1;
			$ticket_watch->save();
		}

		unset($_SESSION['dims']['tickets']['users_selected']);
		unset($_SESSION['dims']['tickets']['workpaces_selected']);
		unset($_SESSION['dims']['tickets']['groups_selected']);
	}

}

function dims_tickets_new($id_object = '', $id_record = '', $object_label = '')
{
	return('<a href="#" onclick="javascript:dims_tickets_new(event, '.$id_object.',\''.addslashes($id_record).'\',\''.addslashes(addslashes($object_label)).'\');"><img style="border:0px;" src="'.$_SESSION['dims']['template_path'].'./common/img/system/email_read.png"></a>');
}

?>
