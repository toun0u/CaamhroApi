<?php
require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');
$ticket = new ticket();

// ouverture du ticket
$ticketid=$_SESSION['dims']['current_ticket']['id_ticket'];
if ($ticketid==0) die();

//dims_print_r($_SESSION['dims']);

if(!isset($_SESSION['dims']['current_object']['id_module']))
	$_SESSION['dims']['current_object']['id_module'] = '';
if(!isset($_SESSION['dims']['current_object']['id_object']))
	$_SESSION['dims']['current_object']['id_object'] = '';
if(!isset($_SESSION['dims']['current_object']['id_record']))
	$_SESSION['dims']['current_object']['id_record'] = '';

$moduleid=$_SESSION['dims']['current_object']['id_module'];
$objectid=$_SESSION['dims']['current_object']['id_object'];
$recordid=$_SESSION['dims']['current_object']['id_record'];

// ouverture du ticket
$where = '';
$orderby = '';
// v�rification du droit de visualisation des personnes concern�es
	require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
	$usr=new user();
	$usr->open($_SESSION['dims']['userid']);
	// liste des users visibles par le user courant
	//$lstusers=$usr->getusersgroup();
	// liste des espaces de travail rattach�s
	$lstworkspace=array_keys($usr->getworkspaces());

	$sql =	"
			SELECT		t.id, t.title,
					t.message,
					t.needed_validation,
					t.delivery_notification,
					t.timestp,
					t.lastreply_timestp,
					t.object_label,
					t.id_object,
					t.id_record,
					t.id_module,
					t.id_workspace,
					t.parent_id,
					t.root_id,
					t.count_read,
					t.count_replies,
					t.id_user AS sender_uid,
					t.status,
					u.login,
					u.firstname,
					u.lastname,

					tw.notify,

					td.id_user,

					m.label as module_name,

					o.label as object_name,
					o.script

			FROM		dims_ticket t

			INNER JOIN	dims_ticket_dest td
			ON			td.id_ticket = t.id

			LEFT JOIN	dims_ticket_watch tw
			ON			tw.id_ticket = t.id
			AND		tw.id_user = :iduser

			LEFT JOIN	dims_user u
			ON			u.id = t.id_user

			LEFT JOIN	dims_module m
			ON			t.id_module = m.id

			LEFT JOIN	dims_mb_object o
			ON			t.id_object = o.id
			AND		m.id_module_type = o.id_module_type

			WHERE	t.id= :idticket";

$rs = $db->query($sql, array(
	':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
	':idticket' => array('type' => PDO::PARAM_INT, 'value' => $ticketid),
));

if ($ticket = $db->fetchrow($rs)) {
		// get dest users & state for all tickets
		$sql =	"
				SELECT		td.id_ticket,
							ts.status,
							ts.timestp,
							u.id,
							u.login,
							u.firstname,
							u.lastname

				FROM		dims_ticket_dest td
				INNER JOIN	dims_user u
				ON			td.id_user = u.id
				LEFT JOIN	dims_ticket_status ts
				ON			ts.id_ticket = td.id_ticket
				AND			ts.id_user = td.id_user

				WHERE		td.id_ticket = :idticket";

		$rs2 = $db->query($sql, array(
			':idticket' => array('type' => PDO::PARAM_INT, 'value' => $ticket['id']),
		));

		// open current user for filter
		$usr= new user();
		$usr->open($_SESSION['dims']['userid']);
		$listusersallowed=$usr->getusersgroup();
		while ($fields = $db->fetchrow($rs2)) {

			if (in_array($fields['id'],$listusersallowed)) {
				if (!isset($tickets[$fields['id_ticket']]['dest'][$fields['id']])) {
					$tickets[$fields['id_ticket']]['dest'][$fields['id']] = array( 'login' => $fields['login'], 'firstname' => $fields['firstname'], 'lastname' => $fields['lastname']);
				}

				$tickets[$fields['id_ticket']]['dest'][$fields['id']]['status'][$fields['status']] = $fields['timestp'];

				if (empty($fields['status'])) $fields['status'] = dims_const::_DIMS_TICKETS_NONE;

				if (empty($tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'])) $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'] = dims_const::_DIMS_TICKETS_NONE;

				if ($fields['status'] > $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status']) $tickets[$fields['id_ticket']]['dest'][$fields['id']]['final_status'] = $fields['status'];
			}
		}

		foreach($tickets as $tck) {
			if (isset($tck['dest'])) {
				foreach($tck['dest'] as $dest) {
					if ($dest['final_status'] < $ticket['status']) $ticket['status'] = $dest['final_status'];
				}
			}
		}

$label=$ticket['title'];
?>
 <div id="vertical_container">
	<h3 class="accordion_toggle">
		<table style="width:100%;">
			<tr>
				<td align="left" width="30%">&nbsp;</td>
				<td align="left" width="30%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						<td class="midb20">
							<?php
							echo $_DIMS['cste']['_DIMS_PROPERTIES'];
							?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:30%;text-align:right">&nbsp;</td>
			</tr>
		</table>
	</h3>
	<div class="accordion_content">
		<div class="system_tickets_detail_content">
			<?
			if (isset($tickets[$ticket['id']]['dest'])) {
			?>
				<div class="system_tickets_user">
					<b>Destinataires:</b>
					<?
					foreach ($tickets[$ticket['id']]['dest'] as $iddest => $dest) {
						$puce = '';
						$strdate = '';

						if ($done = isset($tickets[$ticket['id']]['dest'][$iddest]['status'][dims_const::_DIMS_TICKETS_DONE]))
						{
							$ldate = dims_timestamp2local($tickets[$ticket['id']]['dest'][$iddest]['status'][dims_const::_DIMS_TICKETS_DONE]);
							$strdate = "<br />(valid� le {$ldate['date']} � {$ldate['time']})";
							$puce = 'green';
						}
						elseif ($opened = isset($tickets[$ticket['id']]['dest'][$iddest]['status'][dims_const::_DIMS_TICKETS_OPENED]))
						{
							$ldate = dims_timestamp2local($tickets[$ticket['id']]['dest'][$iddest]['status'][dims_const::_DIMS_TICKETS_OPENED]);
							$strdate = "<br />(lu le {$ldate['date']} � {$ldate['time']})";
							$puce = 'blue';
						}
						else
						{
							$puce = 'red';
							$strdate = '';
						}

						?>
							<div class="system_tickets_user_detail">
								<div style="clear:both;float:left;"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/p_<? echo $puce; ?>.png"></div>
								<div style="float:left;"><? echo "{$dest['firstname']} {$dest['lastname']}{$strdate}"; ?></div>
							</div>
						<?
					}
					?>
				</div>
				<?
			}
			?>
			<div class="system_tickets_head" style="margin-top: 4px;width:90%;">
			<? echo $_DIMS['cste']['_DIMS_LABEL_FROM']." <strong>";

			$usr= new user();
			$usr->open($ticket['id_user']);
				if (isset($usr->fields['firstname'])) {
					echo $usr->fields['firstname']." ".$usr->fields['lastname'];
				}
			echo "</strong> ".$_DIMS['cste']['_AT']." <strong>";

			$datvar=dims_timestamp2local($ticket['timestp']);
				$chdate=$datvar['date'];
			echo $chdate."</strong> ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$datvar['time'];

			echo "<br>".dims_make_links(dims_nl2br($ticket['message']));
			?>

			</div>
			<?
			if ($ticket['needed_validation'] > 0 && in_array($_SESSION['dims']['userid'],$tickets[$ticket['id']]) && !isset($tickets[$ticket['id']]['dest'][$_SESSION['dims']['userid']]['status'][dims_const::_DIMS_TICKETS_DONE])) {
				?>
				<div class="system_tickets_tovalidate">
					<div class="system_tickets_tovalidate_msg">
						<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/attention.png">
						<span>L'exp�diteur vous demande de valider ce message</span>
					</div>
					<div class="system_tickets_tovalidate_button">
						<p class="dims_va">
						<a href="<? echo "{$scriptenv}?op=ticket_validate&ticket_id={$ticket['id']}"; ?>"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/email_validate.png">Valider</a>
						</p>
					</div>
				</div>
				<?
			}
			?>
		</div>
		<?
		if ($ticket['id_record'] != '') {
			?>
			<div class="system_tickets_buttons">
				<p class="dims_va">
					<?
					// construction du script
					$object_script = $dims->getScriptAccessObject($ticket['id_module'], $ticket['id_object'], $ticket['id_record']);

					?>
					<span><strong>Objet li&eacute;</strong>: </span><a href="<? echo "{$object_script}"; ?>"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/link.png"><? echo "{$ticket['module_name']} / {$ticket['object_name']} <b>\"{$ticket['object_label']}\"</b>"; ?></a>
				</p>
			</div>
			<?
		}
		?>

		<div class="system_tickets_buttons">
			<?
			//if (!($ticket['needed_validation'] > 0 && $ticket['sender_uid'] != $_SESSION['dims']['userid'] && !isset($tickets[$ticket['id']]['dest'][$_SESSION['dims']['userid']]['status'][dims_const::_DIMS_TICKETS_DONE]))) {
				echo dims_create_button($_DIMS['cste']['_DELETE'],'./common/img/delete.png',"javascript:dims_confirmlink('admin.php?op=ticket_delete&ticket_id={$ticket['id']}','�tes-vous certain de vouloir supprimer ce ticket ?');",'sup','');
			//}
			?>
		</div>
		<div id="tickets_responses_<? echo $ticket['id'];?>"></div>
	</div>
	<?
		// test si r�ponse � afficher
		if ($ticket['count_replies']>0) {
		?>
		<h3 class="accordion_toggle">
			<table style="width:100%;">
			<tr>
				<td align="left" width="30%">&nbsp;</td>
				<td align="left" width="30%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						<td class="midb20" onclick="javascript:ticketOpenResponses(<? echo $ticket['id']; ?>);">
							<?php
							echo $_DIMS['cste']['_DIMS_ANSWER']." (".$ticket['count_replies'].")";
							?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:30%;text-align:right">&nbsp;</td>
			</tr>
			</table>
		</h3>
		 <div class="accordion_content">
			<div id="ticket_responses_<? echo $ticket['id'];?>">
				<table width="100%" height="300"><tr><td valign="center" align="center"><img src="./common/img/loading.gif" alt=""></td></tr></table>
			</div>
		</div>
		<?
		}
		?>
	<h3 class="accordion_toggle" >
		<table style="width:100%;">
			<tr>
				<td align="left" width="30%">&nbsp;</td>
				<td align="left" width="30%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						<td class="midb20" onclick="javascript:ticketOpenResponse(<? echo $ticket['id']; ?>);">
							<?php
							echo $_DIMS['cste']['_DIMS_REPLY'];
							?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:30%;text-align:right">&nbsp;</td>
			</tr>
		</table>
	</h3>
	<div class="accordion_content">
		<div id="ticket_response_<? echo $ticket['id'];?>">
			<table width="100%" height="300"><tr><td valign="center" align="center"><img src="./common/img/loading.gif" alt=""></td></tr></table>
		</div>
	</div>

	<?
	// on regarde si on a un objet pour visualiser les commentaires
	if ($moduleid>0 && $recordid>0 && $objectid>0) {
	?>
		<h3 class="accordion_toggle">
				<table style="width:100%;">
				<tr>
					<td align="left" width="30%">&nbsp;</td>
					<td align="left" width="30%">
						<table style="width:100%;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							<td class="midb20">
								<?php
								echo $_DIMS['cste']['_DIMS_VIEW_CONTENT'];
								?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
					</td>
					<td  style="width:30%;text-align:right">&nbsp;</td>
				</tr>
			</table>
		</h3>
		 <div class="accordion_content" id="desktop_detail_object_content">
		</div>

		<h3 class="accordion_toggle">
			<table style="width:100%;">
			<tr>
				<td align="left" width="30%">&nbsp;</td>
				<td align="left" width="30%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						<td class="midb20">
							<?php
							echo $_DIMS['cste']['_DIMS_COMMENTS'];
							?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:30%;text-align:right">&nbsp;</td>
			</tr>
			</table>
		</h3>
		 <div class="accordion_content">
			<?
						require_once DIMS_APP_PATH.'include/functions/annotations.php';
			dims_annotation($objectid,$recordid,'',-1,-1,-1,true);
			?>
		</div>
	<?
	}
	?>
</div>
<?
}
?>
