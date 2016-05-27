<?php
require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
require_once(DIMS_APP_PATH . '/modules/system/class_ticket_dest.php');

if (isset($_GET['filtertype'])) $_SESSION['tickets']['filtertype'] = dims_load_securvalue('filtertype', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (!isset($_SESSION['tickets']['filtertype'])) $_SESSION['tickets']['filtertype'] = 'all';
$filtertype = $_SESSION['tickets']['filtertype'];

if (isset($_GET['sort'])) $_SESSION['tickets']['sort'] = dims_load_securvalue('sort', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (!isset($_SESSION['tickets']['sort'])) $_SESSION['tickets']['sort'] = 'datereply';
$sort = $_SESSION['tickets']['sort'];
if (!isset($op)) $op = '';
switch($op) {
	case 'eraseticket':
		$where = '';
		$params = array();
		if ($_SESSION['dims']['desktop_ticket']==dims_const::_DIMS_CSTE_TOVALID) {
			$where = " AND t.id_user <> :iduser AND t.needed_validation > 0 AND t.status < :status ";
			$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_DIMS_TICKETS_DONE);
		}
		$sql = " SELECT		t.id
			FROM		dims_ticket t
			INNER JOIN	dims_ticket_dest td
			ON		td.id_ticket = t.id
			AND		td.id_user= :iduser
			AND		t.deleted=0 AND td.deleted=0
			WHERE		(td.id_user = :iduser AND td.deleted = 0 AND t.deleted=0)
			$where";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		$rs = $db->query($sql, $params);
		if ($db->numrows($rs)) {
			while ($elem=$db->fetchrow($rs)) {
				$tdest = new ticket_dest();

				if ($tdest->open($_SESSION['dims']['userid'],$elem['id'])) {
					$tdest->fields['deleted'] = 1;
					$tdest->save();
				}
			}
		}
		break;

	case 'eraseticketsent':
		$where = '';
		$params = array();
		if ($_SESSION['dims']['desktop_ticket']==dims_const::_DIMS_CSTE_TOCONFIRM) {
			$where = " AND t.id_user = :iduser AND t.needed_validation > 0 AND t.status < :status ";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
			$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_DIMS_TICKETS_DONE);
		}
		$sql = " SELECT		t.id
			FROM		dims_ticket t
			INNER JOIN	dims_ticket_dest td
			ON		td.id_ticket = t.id
			AND		t.deleted=0 AND td.deleted=0
			WHERE		(td.deleted = 0 AND t.deleted=0)
			$where";

		$rs = $db->query($sql, $params);
		if ($db->numrows($rs)) {
			while ($elem=$db->fetchrow($rs)) {
				$sql = "UPDATE dims_ticket_dest SET deleted=1 WHERE id_ticket = :idticket";
				$res=$db->query($sql, array(
					':idticket' => array('type' => PDO::PARAM_INT, 'value' => $elem['id']),
				));
				$ticket = new ticket();
				$ticket->open($elem['id']);
				$ticket->fields['deleted']=1;
				$ticket->save();
			}
		}
		break;

	case "deleteselticket":
		$lst=dims_load_securvalue('lst',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$alst=explode(",",$lst);

		if (!empty($alst)) {
			foreach($alst as $id=>$elem) {
				$tdest = new ticket_dest();

				if ($tdest->open($_SESSION['dims']['userid'],$elem)) {
					$tdest->fields['deleted'] = 1;
					$tdest->save();
				}
			}
		}
		die();
		break;

	case 'deleteselsentticket':
		$lst=dims_load_securvalue('lst',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$alst=explode(",",$lst);

		if (!empty($alst)) {
			foreach($alst as $id) {
				$ticket = new ticket();
				$ticket->open($id);
				if(($ticket->fields['status']>=1)&&($ticket->fields['id_user']==$_SESSION['dims']['userid'])){
					$sql_del_ticket = "UPDATE dims_ticket_dest SET deleted = 1 WHERE id_ticket = :idticket";
					$res=$db->query($sql_del_ticket, array(
						':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id),
					));
					$ticket->fields['deleted']=1;
					$ticket->save();
				}
			}
		}
		die();
		break;

	case 'ticket_delete':


		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$ticket = new ticket();
		if ($ticketid>0 && $ticket->open($ticketid)) {
			if ($_SESSION['dims']['userid'] == $ticket->fields['id_user']) {
				$ticket->fields['deleted'] = 1;
				$ticket->save();
				unset($_SESSION['dims']['current_ticket']);
				unset($_SESSION['dims']['current_object']);
			}
		}

		$ticket_dest = new ticket_dest();
		if ($ticket_dest->open($_SESSION['dims']['userid'], $ticketid)) {
			$ticket_dest->fields['deleted'] = 1;
			$ticket_dest->save();
		}

		// on regarde pour positionner le prochain &eacute;l&eacute;ment
		dims_redirect($scriptenv);
	break;

	case 'ticket_send':
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket_dest.php');
		require_once DIMS_APP_PATH . '/include/functions/tickets.php';
		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($ticketid>0) {
			$ticket = new ticket();
			$ticket->open($ticketid);

			$root_ticket = new ticket();
			$root_ticket->open($ticket->fields['root_id']);
			$root_ticket->fields['count_replies']++;
			$root_ticket->fields['lastreply_timestp'] = dims_createtimestamp();

			$response = new ticket();
			$response->fields = $ticket->fields;
			$response->fields['id'] = '';
			$response->fields['title'] = dims_load_securvalue('ticket_title', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$response->fields['message'] = dims_load_securvalue('ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$response->fields['id_user'] = $_SESSION['dims']['userid'];
			$response->fields['timestp'] = dims_createtimestamp();
			$response->fields['lastreply_timestp'] = $response->fields['timestp'];
			$response->fields['parent_id'] = $ticketid;
			$response->fields['root_id'] = $ticket->fields['root_id'];
			$id_resp = $response->save();

			$root_ticket->save();

			$res=$db->query("DELETE FROM dims_ticket_watch WHERE id_ticket = :idticket AND id_user <> :iduser", array(
				':idticket'	=> array('type' => PDO::PARAM_INT, 'value' => $ticket->fields['root_id']),
				':iduser'	=> array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid'])
			));

			if(isset($_POST['ticket_limit_validation'])) {
				$ticketdatevalidation = dims_load_securvalue('ticket_date_validation', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$tab_date = explode('/', $ticketdatevalidation);
				$timestmp_date_validation = $tab_date[2].$tab_date[1].$tab_date[0]."000000";
			} else {
				$timestmp_date_validation = 0;
			}

			$tickettitle = dims_load_securvalue('ticket_title', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$ticketmessage = dims_load_securvalue('ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			dims_tickets_send($tickettitle, $ticketmessage, (int)isset($_POST['ticket_needed_validation']), $timestmp_date_validation , (int)isset($_POST['ticket_delivery_notification']));

		}else{
			if(isset($_POST['ticket_limit_validation'])) {
				$ticketdatevalidation = dims_load_securvalue('ticket_date_validation', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$tab_date = explode('/', $ticketdatevalidation);
				$timestmp_date_validation = $tab_date[2].$tab_date[1].$tab_date[0]."000000";
			} else {
				$timestmp_date_validation = 0;
			}

			$tickettitle = dims_load_securvalue('ticket_title', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$ticketmessage = dims_load_securvalue('ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			dims_tickets_send($tickettitle, $ticketmessage, (int)isset($_POST['ticket_needed_validation']), $timestmp_date_validation , (int)isset($_POST['ticket_delivery_notification']));

		}

		dims_redirect($scriptenv);
	break;

	case 'ticket_modify_next':
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket_dest.php');
		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($ticketid>0) {
			$ticket = new ticket();
			$ticket->open($ticketid);
			$ticket->setvalues($_POST, 'ticket_');
			$ticket->save();
		}
		dims_redirect($scriptenv);
	break;

	case 'ticket_replyto':
	case 'ticket_modify':
		ob_end_clean();
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
		$ticket = new ticket();
		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$ticket->open($ticketid);

		if ($op == 'ticket_replyto') {
			$ticket->fields['title'] = "RE: {$ticket->fields['title']}";
			$nextop = 'ticket_send';
			$button_value = 'Envoyer';
			if (!isset($quoted)) $ticket->fields['message'] = '';
		}
		else {
			$nextop = 'ticket_modify_next';
			$button_value = 'Modifier';
		}
		?>
		<div id="tickets_new">
			<form method="post" style="padding:0;margin:0;">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("op",			$nextop);
				$token->field("ticket_id",	$ticketid);
				$token->field("ticket_title");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="op" value="<? echo $nextop; ?>">
			<input type="hidden" name="ticket_id" value="<? echo $ticketid; ?>">
			<table cellpadding="2" cellspacing="0" style="width:100%">
			<tr><td style="font-weight:bold;">Titre</td></tr>
			<tr>
				<td><input type="text" name="ticket_title" class="text" value="<? echo ($ticket->fields['title']); ?>" style="width:380px"></td>
			</tr>
			<tr><td style="font-weight:bold;">Message</td></tr>
			<tr>
				<td>
				<?php
				require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php') ;

				$oFCKeditor = new FCKeditor('fck_ticket_message') ;

				$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
				if ($basepath == '/') $basepath = '';

				$oFCKeditor->BasePath = "{$basepath}/FCKeditor/";

				// default value
				$oFCKeditor->Value = $ticket->fields['message'];

				// width & height
				$oFCKeditor->Width='100%';
				$oFCKeditor->Height='200';

				$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js"  ;
				//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
				$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
				$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
				$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
				$oFCKeditor->Create('FCKeditor_1') ;
				?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
					<input type="submit" class="flatbutton" value="<? echo $button_value; ?>" style="font-weight:bold;">
					<input type="button" class="flatbutton" value="Annuler" onclick="dims_getelem('dims_popup').style.visibility='hidden';">
				</td>
			</tr>
			</table>
			<?
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			</form>
		</div>
		<?
		die();
	break;

	case 'ticket_open':
		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($ticketid>0) {
			require_once(DIMS_APP_PATH . '/modules/system/class_ticket_watch.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_ticket_status.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');

			$ticket_status = new ticket_status();

			if (!$ticket_status->open($ticketid, $_SESSION['dims']['userid'], dims_const::_DIMS_TICKETS_OPENED)) {
				$ticket_status->fields['id_ticket'] = $ticketid;
				$ticket_status->fields['id_user'] = $_SESSION['dims']['userid'];
				$ticket_status->fields['status'] = dims_const::_DIMS_TICKETS_OPENED;
				$ticket_status->save();
			}

			$ticket_watch = new ticket_watch();
			$ticket_watch->open($ticketid, $_SESSION['dims']['userid']);
			$ticket_watch->fields['id_ticket'] = $ticketid;
			$ticket_watch->fields['id_user'] = $_SESSION['dims']['userid'];
			$ticket_watch->fields['notify'] = 0;
			$ticket_watch->save();

			$ticket = new ticket();
			$ticket->open($ticketid);
			$ticket->fields['count_read']++;
			$ticket->save();
		}
		die();
	break;

	case 'ticket_open_responses':
		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($ticketid>0) {
			ob_end_clean();
			$rootid = addslashes($ticketid);

			$sql = " SELECT		t.id,
							t.title,
							t.message,
							t.timestp,
							t.id_module,
							t.parent_id,
							t.root_id,
							t.id_user as sender_uid,
							ts.status,
							u.login,
							u.firstname,
							u.lastname

				FROM		dims_ticket t

				INNER JOIN	dims_user u
				ON			t.id_user = u.id

				LEFT JOIN	dims_ticket_status ts
				ON			ts.id_ticket = t.id
				AND		ts.id_user = :iduser

				WHERE		t.root_id = :idroot
				AND			t.id <> :idroot

				ORDER BY	t.timestp DESC
				";

			$tickets = array();
			$parents = array();

			$rs = $db->query($sql, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				':idroot' => array('type' => PDO::PARAM_INT, 'value' => $ticketid),
			));

			while ($fields = $db->fetchrow($rs)) {
				if (!isset($tickets[$fields['id']])) {
					$tickets[$fields['id']] = $fields;
					$parents[$fields['parent_id']][] = $fields['id'];
				}

			}

			if (!empty($tickets)) system_tickets_displayresponses($parents, $tickets,$ticketid);
		}
		die();
	break;


	case 'ticket_validate':
		require_once(DIMS_APP_PATH . '/modules/system/class_ticket_status.php');
		$ticket_status = new ticket_status();

		$ticketid=dims_load_securvalue('ticket_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if (!$ticket_status->open($ticketid, $_SESSION['dims']['userid'], dims_const::_DIMS_TICKETS_DONE)) {
			$ticket_status->fields['id_ticket'] = $ticketid;
			$ticket_status->fields['id_user'] = $_SESSION['dims']['userid'];
			$ticket_status->fields['status'] = dims_const::_DIMS_TICKETS_DONE;
			$ticket_status->save();
		}
		dims_redirect($scriptenv);
	break;

	case 'ticket_change_status':
		if (isset($_POST['ticket_delete'])) {
			foreach($ticket_delete as $ticket_id) {
				require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
				$ticket = new ticket();
				$ticket->open($ticket_id);
				switch($_POST['status']) {
					case 'done':
						if (!$ticket->fields['done']) {
							$ticket->fields['done'] = 1;
							$ticket->fields['done_timestp'] = dims_createtimestamp();
						}
					break;

					case 'deleted':
						if (!$ticket->fields['deleted']) {
							$ticket->fields['deleted'] = 1;
							$ticket->fields['deleted_timestp'] = dims_createtimestamp();
						}
					break;
				}
				$ticket->save();
			}
		}
		dims_redirect($scriptenv);
	break;

	case 'ticket_dest_change_status':
		if (isset($ticket_delete)) {
			foreach($ticket_delete as $ticket_id) {
				require_once(DIMS_APP_PATH . '/modules/system/class_ticket_dest.php');
				$ticket_dest = new ticket_dest();
				$ticket_dest->open($_SESSION['dims']['userid'], $ticket_id);
				switch($_POST['status']) {
					case 'done':
						if (!$ticket_dest->fields['done']) {
							$ticket_dest->fields['done'] = 1;
							$ticket_dest->fields['done_timestp'] = dims_createtimestamp();
						}
					break;

					case 'deleted':
						if (!$ticket_dest->fields['deleted']) {
							$ticket_dest->fields['deleted'] = 1;
							$ticket_dest->fields['deleted_timestp'] = dims_createtimestamp();
						}
					break;
				}
				$ticket_dest->save();
			}
		}
		dims_redirect($scriptenv);
	break;

	case 'ticket_new':
		$_SESSION['dims']['desktop_ticket'] = dims_const::_DIMS_CSTE_CREATE;

		unset($_SESSION['dims']['tickets']['workspaces_selected']);
		unset($_SESSION['dims']['tickets']['groups_selected']);
		unset($_SESSION['dims']['tickets']['users_selected']);

		$nextop = 'ticket_send';
		?>
		<div id="tickets_new">
			<form method="post" style="padding:0;margin:0;" id="formNewTicket">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("op", $nextop);
				$token->field("ticket_search");
				$token->field("ticket_title");
				$token->field("ticket_date_validation");
				$token->field("ticket_needed_validation");
			?>
			<input type="hidden" name="op" value="<?php echo $nextop; ?>">
			<table cellpadding="0" cellspacing="0" style="width:100%;color:#000000;">
				<tr>
				<td style="width:60%;vertical-align:top;">
					<table	cellpadding="2" cellspacing="0" style="width:100%;color:#000000;">
										<tr>
											<td style="width:2%"><img src="./common/img/contact.png" alt=""></td>
											<td style="width:98%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_DESTS'];?></td>
										</tr>
					<tr>
						<td colspan="2">
												<span style="float:left;"><input type="text" name="ticket_search" id="ticket_search" class="text" style="width:200px" onkeyup="javascript:dims_tickets_search_users();"></span>
												<span  style="margin-left:10px;float:left;"><img src="./common/img/search.png" alt="">
											</td>
					</tr>
										<tr>
						<td style="height:40px;" colspan="2"><div id="div_ticket_users_selected"></div></td>
					</tr>
					<tr>
											<td colspan="2" style="font-weight:bold;"><?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE'];?></td>
										</tr>
					<tr>
						<td colspan="2" valign="top" style="height:30px;"><input type="text" name="ticket_title" class="text" style="width:400px"></td>
					</tr>
					<tr>
											<td colspan="2" style="height:20px;font-weight:bold;"><?php echo $_DIMS['cste']['_DIMS_LABEL_MESSAGE'];?></td>
										</tr>
					<tr>
											<td colspan="2">
											<?php
											require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php') ;

											$oFCKeditor = new FCKeditor('fck_ticket_message') ;

											$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
											if ($basepath == '/') $basepath = '';

											$oFCKeditor->BasePath = "{$basepath}/FCKeditor/";

											// width & height
											$oFCKeditor->Width='100%';
											$oFCKeditor->Height='200';

											$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js"  ;
											//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
											$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
											$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
											$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
											$oFCKeditor->Create('FCKeditor_1') ;
											?>
											</td>
					</tr>
					<tr>
											<td colspan="2">
												<div style="width:50%;float:left;">
													<span style="height:20px;float:left;"><img src="./common/img/checkdo.png" alt=""></span>
													<span style="height:20px;float:left;font-weight:bold;margin-left:10px;"><?php echo $_DIMS['cste']['_DIMS_LABEL_VALIDATION'];?></span>
													<span style="clear:both;float:left;margin-top:5px;"><?php echo $_DIMS['cste']['_TICKET_ENABLE_VALIDATION'];?></span>
													<span style="float:left;margin-left:10px;margin-top:5px;"><input type="checkbox" name="ticket_needed_validation" value="1"/></span>
												</div>
												<div style="width:50%;float:left;">
													<span style="height:20px;float:left;"><img src="./common/img/date.gif" alt=""></span>
													<span style="height:20px;float:left;font-weight:bold;margin-left:10px;"><?php echo $_DIMS['cste']['_TICKET_LIMIT_TIME_VALIDATION'];?></span>
													<span style="clear:both;float:left;margin-top:5px;"><input type="text" name="ticket_date_validation" id="ticket_date_validation"/></span>
													<span style="float:left;margin-top:5px;"><a href="javascript:void(0);" onclick="javascript:dims_calendar_open('ticket_date_validation', event);"><img src="./common/img/calendar/calendar.gif"/></a></span>
												</div>
											</td>
										<tr>
											<td style="text-align:right;" colspan="2">
												<?php
													echo dims_create_button($_SESSION['cste']['_DIMS_SEND'], "./common/img/public.png", "dims_getelem('formNewTicket').submit();", "submitTicket", '', "javascript:void(0);");
												?>
											</td>
					</tr>
					</table>
				</td>
								<td valign="top">
									<div id='div_ticket_search_result'></div>
								</td>
				</tr>
			</table>
			<script language="javascript">
				$('ticket_search').focus();
			</script>
			<?
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			</form>
		</div>
		<?
	break;

	default:
		$select = "";
		$inner = "";
		$where = "";
		switch($_SESSION['dims']['desktop_ticket']) {
			case dims_const::_DIMS_CSTE_TOVIEW:
				$select = "";
				$inner = "";
				$where = " AND td.id_user = :iduser";
				$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
			break;

			case dims_const::_DIMS_CSTE_TOVALID:
				$select = "";
				$inner = "";
				$where = " AND td.id_user = :iduser AND t.id_user <> :iduser AND t.needed_validation = 1";
				$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
			break;

			case dims_const::_DIMS_CSTE_TOCONFIRM:
				$select = " du.firstname AS dest_firstname,
						du.lastname AS dest_lastname, ";
				$inner = " AND td.id_user <> t.id_user
						 INNER JOIN dims_user du ON du.id = td.id_user";
				$where = " AND t.id_user = :iduser AND t.needed_validation = 1
					   AND (t.status < :status )";
				$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
				$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_DIMS_TICKETS_DONE);
				break;

			case dims_const::_DIMS_CSTE_SEND:
				$select = " du.firstname AS dest_firstname, du.lastname AS dest_lastname, ";
				$inner = " INNER JOIN dims_user du ON du.id = td.id_user AND td.id_user <> t.id_user ";
				$where = " AND t.id_user = :iduser
					   AND (t.status = 0)";
				$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
				break;
		};

		$orderby = ' ORDER BY status_null DESC';
		switch($sort) {
			case 'dateticket':
				$orderby .= ", t.timestp DESC ";
			break;

			case 'datereply':
				$orderby .= ", t.lastreply_timestp DESC, t.timestp DESC ";
			break;
		}

		// v&eacute;rification du droit de visualisation des personnes concern&eacute;es
		require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
		$usr=new user();

		$usr->open($_SESSION['dims']['userid']);

		// liste des users visibles par le user courant
		//$lstusers=$usr->getusersgroup();

		// liste des espaces de travail rattach&eacute;s
		$lstworkspace=array_keys($usr->getworkspaces());


		$sql = "SELECT		t.id, t.title,
					t.message,
					t.status AS status_ticket,
					t.needed_validation,
					t.delivery_notification,
					t.timestp,
					t.lastreply_timestp,
					t.id_record,
					t.id_object,
					t.id_module,
					t.id_workspace,
					t.parent_id,
					t.root_id,
					t.count_read,
					t.count_replies,
					t.id_user AS sender_uid,

					u.login,
					u.firstname,
					u.lastname,

					tw.notify,

					ts.status IS NULL AS status_null,
					ts.status,

					$select

					td.id_user

			FROM		dims_ticket t

			INNER JOIN	dims_ticket_dest td
			ON		td.id_ticket = t.id

			$inner

			LEFT JOIN	dims_ticket_watch tw
			ON		tw.id_ticket = t.id
			AND		tw.id_user = td.id_user

			LEFT JOIN	dims_user u
			ON		u.id = t.id_user

			LEFT JOIN	dims_ticket_status ts
			ON		ts.id_ticket = t.id
			AND		ts.id_user = td.id_user

			WHERE		td.deleted = 0

			$where

			GROUP BY t.id

			$orderby";

		$rs = $db->query($sql, $params);
		$tickets = array();

		while ($fields = $db->fetchrow($rs)) {
			if($fields['status'] == null){$fields['status']=0;}
			//$fields['status'] = dims_const::_DIMS_TICKETS_DONE;
			if (!isset($tickets[$fields['id']])) $tickets[$fields['id']] = $fields;
		}

		switch($_SESSION['dims']['desktop_ticket']) {
			case dims_const::_DIMS_CSTE_TOVALID:
				$sql = "SELECT		t.id
					FROM		dims_ticket t
					INNER JOIN	dims_ticket_dest td
					ON		td.id_ticket = t.id
					AND		td.id_user = :userid
					INNER JOIN	dims_ticket_status ts
					ON		ts.id_ticket = t.id
					AND		ts.id_user = :userid
					AND		ts.status = :status ";
				//echo $sql;
				$res_diff = $db->query($sql, array(
					':userid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':status' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_DIMS_TICKETS_DONE)
				));
				$tickets_diff = array();
				while ($data = $db->fetchrow($res_diff)) {
					if (!isset($tickets_diff[$data['id']])) $tickets_diff[$data['id']] = $data;
				}

				$tickets = array_diff_assoc($tickets,$tickets_diff);
			break;
		}

		//dims_print_r($tickets);

		$ticket_list = implode(',',array_keys($tickets));

		if ($ticket_list != "") {
			// get dest users & state for all tickets
			$params = array();
			$sql = " SELECT		td.id_ticket,
						ts.status,
						ts.timestp,
						u.id,
						u.login,
						u.firstname,
						u.lastname,
						t.id_user as sender_ui,
						mbo.script
				FROM		dims_ticket_dest td
				INNER JOIN	dims_ticket as t
				ON		t.id=td.id_ticket
				LEFT JOIN	dims_ticket_status ts
				ON		ts.id_ticket = td.id_ticket
				AND		ts.id_user = td.id_user

				LEFT JOIN	dims_user u
				ON		td.id_user = u.id
				LEFT JOIN	dims_mb_object as mbo
				ON		mbo.id=t.id_object and mbo.id_module_type=t.id_module_type
				WHERE		td.id_ticket IN (".$db->getParamsFromArray(array_keys($tickets), 'tickets', $params).")
				";

			$rs = $db->query($sql, $params);

			// open current user for filter
			$usr= new user();
			$usr->open($_SESSION['dims']['userid']);
			//$listusersallowed=$usr->getusersgroup();
			$listusersallowed=array();

			while ($fields = $db->fetchrow($rs)) {
				if (in_array($fields['id'],$listusersallowed)) {
					if (!isset($tickets[$fields['id_ticket']]['dest'][$fields['id']])) {
						$tickets[$fields['id_ticket']]['dest'][$fields['id']] = array( 'login' => $fields['login'], 'firstname' => $fields['firstname'], 'lastname' => $fields['lastname'], 'id_user' => $fields['sender_ui']);
					}

					$tickets[$fields['id_ticket']]['dest'][$fields['id']]['status'][$fields['status']] = $fields['timestp'];
					if (empty($fields['status'])) $fields['status'] = dims_const::_DIMS_TICKETS_NONE;
					if (empty($tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'])) $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'] = dims_const::_DIMS_TICKETS_NONE;
					if ($fields['status'] > $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status']) $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'] = $fields['status'];
				}
			}

			foreach($tickets as $ticket) {
				if (isset($ticket['dest'])) {
					foreach($ticket['dest'] as $dest) {
						if ($dest['final_status'] < $tickets[$ticket['id']]['status']) $tickets[$ticket['id']]['status'] = $dest['final_status'];
					}
				}
			}
		}

		$tab_destticket=array();
		// construction de la liste des destinataires regroupe par ticket
		if ($ticket_list != "") {
			if(($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM)||($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND)){
				$params = array();
				$sql = "SELECT id_ticket,count(id_user) as cpte
						FROM dims_ticket_dest
						WHERE id_ticket IN (".$db->getParamsFromArray(explode(',', $ticket_list), 'tickets', $params).")
						GROUP BY id_ticket";

				$res = $db->query($sql, $params);
				if($db->numrows($res)>1){
					while ($resu=$db->fetchrow($res)) {
						$tab_destticket[$resu['id_ticket']]=$resu['cpte'];
					}
				}
			}
		}else{
			$tab_destticket = 0;
		}
		?>
		<div id="system_tickets_titlebar">
			<?
			$nb_tickets_page = 10;
			$numrows = sizeof($tickets);
			$nbpage = ($numrows - $numrows % $nb_tickets_page) / $nb_tickets_page + ($numrows % $nb_tickets_page > 0);
			if (isset($_GET['page'])) {
				$page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$_SESSION['dims']['page_ticket']=$page;
			}
			else {
				if (isset($_SESSION['dims']['page_ticket'])) $page=$_SESSION['dims']['page_ticket'];
				else {
					$page = 1;
					$_SESSION['dims']['page_ticket']=$page;
				}
			}

			if ($nbpage>0) {
				?>

				<div style="float:right;">
					<div style="float:left;"><? echo $_DIMS['cste']['_DIMS_LABEL_PAGE']; ?> :&nbsp;</div>
					<?
					for ($p = 1; $p <= $nbpage; $p++)
					{
						?>
						<a class="system_page<? if ($p==$page) echo '_sel'; ?>" href="javascript:void(0)" onclick="ticketsRefresh(<? echo "$p"; ?>);"><? echo $p; ?></a>
						<?
					}
					?>
				</div>
				<?
			}
			?>
		</div>
			<table style="width:100%;margin-top:2px;margin-bottom:2px;" cellpadding="0" cellspacing="0">
			<tr class="fontgray" style="font-size:12px;">
				<td style="width:4%"></td>
				<td style="width:41%"><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?></td>
			<?php
			if(($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM)||($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND)){
			?>	<td style="width:24%;"><? echo $_DIMS['cste']['_DIMS_DEST']; ?></td><?php
			}else{
			?>	<td style="width:24%;"><? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_EMETTEUR']; ?></td><?php
			}
			?>
				<td style="width:25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_DPOST']; ?></td>
				<td style="width:3%;"><? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_NBREP']; ?></td>
				<td style="width:3%;"></td>
			</tr>
			<?
			$todaydate = dims_timestamp2local(dims_createtimestamp());
			if (!sizeof($tickets)) {
				$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
				//$color = (!isset($color) || $color == "#738CAD") ? "#B5CFF1;" : "#738CAD";
				?>
				<tr class="lr" style="background-color:<? echo $color; ?>;text-align:center;font-weight:bold;">
				<td colspan="4" height="150"><? echo $_DIMS['cste']['_DIMS_LABEL_NO_TICKET']; ?></td>
				</tr>
				<?
			}

			// positionnement de l'objet courant
			if (isset($_SESSION['dims']['current_object'])) {
				$selmoduleid=$_SESSION['dims']['current_object']['id_module'];
				$selobjectid=$_SESSION['dims']['current_object']['id_object'];
				$selrecordid=$_SESSION['dims']['current_object']['id_record'];

			}
			else {
				$selmoduleid=0;
				$selobjectid=0;
				$selrecordid=0;
			}

			// test si on ouvre le premier ticket ou non
			$isnewticket=false;
			$ticketcur=array();
			$nbticketscheck=0;
			//dims_print_r($tickets);
			reset($tickets);
			if(count($tickets != 1)){
				for ($i=0; $i<($page-1)*$nb_tickets_page; $i++) next($tickets);
			}
			$ticket = current($tickets);
			$color=1;

			for  ($i=0; $i<$nb_tickets_page && !empty($ticket); $i++) {
				$fields = $ticket;
				if (!isset($_SESSION['dims']['current_ticket'])) $_SESSION['dims']['current_ticket']['id_ticket']=$fields['id'];

				/*if ($i % 2 == 1) $seli="1";
				else $seli="";*/
				$color = (!isset($color) || $color == 2) ? 1 : 2;

				//check selected ticket
				if (isset($_SESSION['dims']['current_ticket']) && $_SESSION['dims']['current_ticket']['id_ticket']==$fields['id']) {
					//$seli="2";
					$seli="style=\"background-color:#bfcde1;\"";
					$fields['status']=1;
					$selectedobj=true;
				}
				else {
					$selectedobj=false;
					$seli="";
				}

		if (!isset($fields['script'])) $fields['script']="";
				$object_script = str_replace('<IDRECORD>',$fields['id_record'],$fields['script']);
				$object_script = str_replace('<IDMODULE>',$fields['id_module'],$object_script);

				$timestp = dims_timestamp2local($fields['timestp']);
				$timestp['date'] = ($todaydate['date'] == $timestp['date'])  ? "Aujourd'hui" : $timestp['date'];
				$fields['lastreply_timestp'];
				$lastreply_timestp = dims_timestamp2local($fields['lastreply_timestp']);
				$lastreply_timestp['date'] = ($todaydate['date'] == $lastreply_timestp['date'])  ? "Aujourd'hui" : $lastreply_timestp['date'];

				if ($fields['id_record']=="") $fields['id_record']=0;

				$td_gras="";
				if($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOVALID){
					$td_gras = 'style="font-weight:bold;"';
				}else{
					if ($fields['status_null'] == 1) {
						$td_gras = 'style="font-weight:bold;"';
					}
				}
				?>

				<tr class="trl<? echo $color; ?>"<? echo $seli;?>>
					<td class="system_tickets_user_puce" id="watch_notify_<? echo $fields['id']; ?>">
					<?
						echo "<input type=\"checkbox\" id=\"selticket".$nbticketscheck."\" name=\"selticket[]\" value=\"".$fields['id']."\">";
						$token->field("selticket");
							$nbticketscheck++;
					?>
					</td>
					<td id="tickets_title_<? echo $fields['id']; ?>" <? echo $td_gras;?> onclick="javascript:viewPropertiesTicket(<? echo $fields['id']; ?>,<? echo $fields['id_record']; ?>,<? echo $fields['id']; ?>,<? echo $fields['id_module']; ?>);" onmouseover="javascript:this.style.cursor='pointer';" onmouseout="javascript:this.style.cursor='default';">
						<? echo dims_strcut(trim($fields['title']),30);
			?>
					</td>
					<td id="tickets_dest_<? echo $fields['id']; ?>" <? echo $td_gras;?> onclick="javascript:viewPropertiesTicket(<? echo $fields['id']; ?>,<? echo $fields['id_record']; ?>,<? echo $fields['id']; ?>,<? echo $fields['id_module']; ?>);" onmouseover="javascript:this.style.cursor='pointer';" onmouseout="javascript:this.style.cursor='default';">

						<?php
						if(($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM)||($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND)){
							if (isset($tab_destticket[$fields['id']]) && $tab_destticket[$fields['id']]>0) {
								$nb_dest = $tab_destticket[$fields['id']];
								echo "{$nb_dest} destinataires";

							}else{
								echo strtoupper(substr($fields['dest_firstname'],0,1)).". ".$fields['dest_lastname'];
							}
						}else{
							echo strtoupper(substr($fields['firstname'],0,1)).". ".$fields['lastname'];
						}
						?>
					</td>
					<td onclick="javascript:viewPropertiesTicket(<? echo $fields['id']; ?>,<? echo $fields['id_record']; ?>,<? echo $fields['id']; ?>,<? echo $fields['id_module']; ?>);" onmouseover="javascript:this.style.cursor='pointer';" onmouseout="javascript:this.style.cursor='default';"><? echo $timestp['date']; ?> &agrave; <? echo substr($timestp['time'],0,5); ?></td>
					<td onclick="javascript:viewPropertiesTicket(<? echo $fields['id']; ?>,<? echo $fields['id_record']; ?>,<? echo $fields['id']; ?>,<? echo $fields['id_module']; ?>);" onmouseover="javascript:this.style.cursor='pointer';" onmouseout="javascript:this.style.cursor='default';"><? echo $fields['count_replies']; ?></td>
					<td onclick="javascript:viewPropertiesTicket(<? echo $fields['id']; ?>,<? echo $fields['id_record']; ?>,<? echo $fields['id']; ?>,<? echo $fields['id_module']; ?>);" onmouseover="javascript:this.style.cursor='pointer';" onmouseout="javascript:this.style.cursor='default';">
					<?
					//acces &agrave; l'objet
					/*if($selectedobj) {
						echo "<img id=\"img_".$fields['id_object']."_".$fields['id']."_".$_SESSION['dims']['moduleid']."\"src=\"./common/img/arrow-blue-right.gif\"";
					}
					else
						echo "<img id=\"img_".$fields['id_object']."_".$fields['id']."_".$_SESSION['dims']['moduleid']."\" src=\"./common/img/arrow-right.gif\"";
					*/?>
					</td>
				</tr>
				<?
				next($tickets);
				$ticket = current($tickets);
			}

		echo "</table>";
		if ($nbticketscheck>0) {
			?>
			<div style="float:left;"><img src="./common/img/arrow_ltr.png" border="0" alt="0"></div>
			<div style="float:left;margin-top:4px;"><a href="#" style="color:#738CAD;" onclick="checkAllTickets(<? echo $nbticketscheck; ?>);"><? echo $_DIMS['cste']['_ALLCHECK']; ?></a>
			&nbsp;/&nbsp;
			<a href="#" style="color:#738CAD;" onclick="uncheckAllTickets(<? echo $nbticketscheck; ?>);"><? echo $_DIMS['cste']['_ALLUNCHECK']; ?></a></div>
			<div style="float:right">
			<?
			if(($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM)||($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND))$sent="Sent";
			else{$sent="";}
			echo dims_create_button($_DIMS['cste']['_DIMS_ALLDELETE'],"./common/img/delete.png","javascript:if (confirm('".$_DIMS['cste']['_DIMS_CONFIRM']."')) eraseTickets".$sent."();","","");
			echo dims_create_button($_DIMS['cste']['_DELETE'],"./common/img/delete.png","javascript:if (confirm('".$_DIMS['cste']['_DIMS_CONFIRM']."')) deleteSel".$sent."Tickets(".$nbticketscheck.");","","");
			?>
			</div>
		<?
		}
	break;
}
?>
