<script type="text/javascript">
	chatRefreshAll();
</script>

	<?php
		$_SESSION['dims']['minichat']['connected'] = array();
		$datedeb_timestp = mktime(date('H'),date('i'),date('s'),date('n'),date('j')-21,date('Y'));
		$datedeb_timestp= date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$datedeb_timestp);
		//AND			cu.timestp>".$datedeb_timestp."
		$sql="	SELECT		distinct u.firstname,u.lastname,u.id,max(cu.timestp) as timestp
				FROM		dims_user as u
				INNER JOIN	dims_connecteduser as cu
				ON			cu.user_id=u.id
				AND			workspace_id= :workspaceid
				GROUP BY	u.id order by timestp desc";

		$res=$db->query($sql, array(
			':workspaceid' => $_SESSION['dims']['workspaceid']
		));
		$nb_connect = 0 ;
		$liste_connect='';
		$displ = 'display:none;';
		if (isset($_SESSION['dims']['minichat']['haut'][-1]))
			$displ='display:block;';
		$liste_connect = '<div id="liste_connect" style="border-color:#000000;width:195px;float:right;'.$displ.'padding-right:10px;background: #373737; color: #FFFFFF; text-align:left;">
							<table width="100%" cellspacing="0" cellpadding="0" style="color: #FFFFFF;">';

		$tab_javascript = "" ;
		while ($f=$db->fetchrow($res)) {

			$_SESSION['dims']['minichat']['user'][$f['id']] = $f['firstname'].' '.$f['lastname'];

			$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
			if ($diff<=100 && $f['id'] != $_SESSION['dims']['userid']) {
				$_SESSION['dims']['minichat']['connected'][$f['id']] = $f['firstname'].' '.$f['lastname'];

				$nb_connect ++ ;
				$tab_javascript .= $f['id'].',';

				$sql_msg = "SELECT		cm.id, msg
							FROM		dims_chat_msg cm
							INNER JOIN	dims_chat_users cu
							ON			cu.id_chat = cm.id
							AND			cu.id_received = :idreceived
							AND			cu.isread = 1
							WHERE		cm.id_send = :idsend ";
				$msgs = $db->query($sql_msg, array(
					':idreceived'	=> $_SESSION['dims']['userid'],
					':idsend'		=> $f['id']
				));
				$nb_res = $db->numrows($msgs);
				if ($nb_res > 0){
					$_SESSION['dims']['minichat']['read'][$f['id']] = $db->numrows($msgs);
					while ($m = $db->fetchrow($msgs)){

						if (isset($_SESSION['dims']['minichat']['last'][$f['id']]) && $_SESSION['dims']['minichat']['last'][$f['id']] == 1)
							$_SESSION['dims']['minichat']['chats'][$f['id']][$f['id']] = stripslashes($m['msg']);
						else{
							$_SESSION['dims']['minichat']['last'][$f['id']] = 1 ;
							$_SESSION['dims']['minichat']['chats'][$f['id']][$f['id']] = '<b>'.$f['firstname'].'</b> : '. stripslashes($m['msg']);
						}
					}
				}

			}
		}
		if ($nb_connect > 0)
			$tab_javascript = substr($tab_javascript,0,-1);

		// récupération des messages de personnes hors ligne
		$sql_msg = "SELECT		cm.id, cm.msg, cm.id_send, u.firstname, u.lastname
					FROM		dims_chat_msg as cm
					INNER JOIN	dims_user as u
					ON			cm.id_send = u.id
					INNER JOIN	dims_chat_users as cu
					ON			cu.id_chat = cm.id
					AND			cu.id_received = :idreceived
					AND			cu.isread = 1";

		$msgs = $db->query($sql_msg, array(
					':idreceived'	=> $_SESSION['dims']['userid']
				));
		$nb_res = $db->numrows($msgs);
		while ($m = $db->fetchrow($msgs)){
			if (!isset($_SESSION['dims']['minichat']['chats'][$m['id_send']][$m['id']])){
				$_SESSION['dims']['minichat']['chats']['user'][$m['id']] = $m['firstname'].' '.$m['lastname'];

				if ($_SESSION['dims']['minichat']['last'][$m['id_send']] == 1)
					$_SESSION['dims']['minichat']['chats'][$m['id_send']][$m['id']] = stripslashes($m['msg']);
				else{
					$_SESSION['dims']['minichat']['last'][$m['id_send']] = 1 ;
					$_SESSION['dims']['minichat']['chats'][$m['id_send']][$m['id']] =  '<b>'.$m['firstname'].'</b> : '. stripslashes($m['msg']);
				}

				if (!isset($_SESSION['dims']['minichat']['read'][$m['id_send']]))
					$_SESSION['dims']['minichat']['read'][$m['id_send']] = 0 ;
				$_SESSION['dims']['minichat']['read'][$m['id_send']] ++ ;
			}
		}

		$liste_chat = '';
		unset($_SESSION['dims']['minichat']['connected']);

		$res=$db->query($sql, array(
			':workspaceid' => $_SESSION['dims']['workspaceid']
		));

		while ($f=$db->fetchrow($res)) {

			$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
			if ($diff<=210 && $f['id'] != $_SESSION['dims']['userid']) {
				$liste_connect .= '	<tr onclick="javascript:displayChat('.$f['id'].',\''.$tab_javascript.'\',\'\');displayChatOpen('.$f['id'].',\''.$tab_javascript.'\');">
										<td>
											&nbsp;'.$f['firstname'].' '.$f['lastname'].'
										</td>
									</tr>';

				$displ = 'display:none;';
				if (isset($_SESSION['dims']['minichat']['haut'][$f['id']]) && isset($_SESSION['dims']['minichat']['bas'][$f['id']]))
					$displ = 'display:block; margin-right:'.$_SESSION['dims']['minichat']['haut'][$f['id']].'px;';

				$messages = '';
				if (isset($_SESSION['dims']['minichat']['chats'][$f['id']])){
					foreach($_SESSION['dims']['minichat']['chats'][$f['id']] as $msg){
						$messages .= $msg.'<br>';
					}
				}else
					$messages = '&nbsp;';

				$liste_chat .= '<div id="chat_open_'.$f['id'].'" onclick="javascript:chatFocus(\''.$f['id'].'\');" style="cursor:pointer;border-color:#000000;width:350px;float:right;'.$displ.'background: #373737; color: #FFFFFF; height:250px;text-align:left;">
									<div id="chat_msg_'.$f['id'].'" style="overflow:auto;margin-left:5px;margin-right:5px;margin-top:5px;background: #FFFFFF;color: #000000;height:210px;">
										'.$messages.'
									</div>
									<form method="POST" onsubmit="javascript:chat_submit(\''.$f['id'].'\');return false;">
										<input type="text" style="width:335px; margin-left:5px;margin-right:5px;margin-top:5px;" id="msg_send_'.$f['id'].'">
									</form>
								</div>';
			}else{
				$liste_chat .= '<div id="chat_open_'.$f['id'].'" onclick="javascript:chatFocus(\''.$f['id'].'\');" style="cursor:pointer;border-color:#000000;width:350px;float:right;display:none;background: #373737; color: #FFFFFF; height:250px;text-align:left;">
									<div id="chat_msg_'.$f['id'].'" style="overflow:auto; margin-left:5px;margin-right:5px;margin-top:5px;background: #FFFFFF;color: #000000;height:210px;">
										&nbsp;
									</div>
									<form method="POST" onsubmit="javascript:chat_submit(\''.$f['id'].'\');return false;">
										<input type="text" style="width:335px; margin-left:5px;margin-right:5px;margin-top:5px;" id="msg_send_'.$f['id'].'">
									</form>
								</div>';
			}
		}

		//echo '<input type="text" style="display:none;" value="">';

		if ($nb_connect == 0)
			$liste_connect .= '	<tr>
									<td>
										'.$_DIMS['cste']['_DIMS_LABEL_NO_USER_CONNECTED'].'
									</td>
								</tr>';

		$liste_connect .= '		</table>
							</div>';

	?>
<div id='chat_sup' style="width:100%;position:fixed;bottom:18px;">
	<?

		// liste utilisateurs
		echo $liste_connect ;

		// fenetres de chat
		echo $liste_chat ;
		//dims_print_r($_SESSION['dims']['minichat']);
	?>
</div>

<div id='chat_inf' style="width:100%;position:fixed;bottom:0;">

	<div id='connected' onclick="javascript:displayListeConnected('<? echo $tab_javascript; ?>');" style="border-color:#000000;float:right;padding-right:10px;background:url('./common/templates/backoffice/dims_lfb/img/sprite.png') repeat-x scroll left -24px #000000; color: #FFFFFF; height:18px;text-align:center;width:195px;">
		<?
			echo '&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_CONNECTEDUSERS'].'&nbsp;(<b>'.$nb_connect.'</b>)';
		?>
	</div>

	<?
	if (isset($_SESSION['dims']['minichat']['connected'] )) {
		foreach ($_SESSION['dims']['minichat']['connected'] as $clef => $name){
			if ($name!='') {
				$displ = 'display:none;';
				$back = 'background:url(\'./common/templates/backoffice/dims_lfb/img/sprite.png\') repeat-x scroll left -24px #000000;';
				if (isset($_SESSION['dims']['minichat']['read'][$clef])){
					$displ = 'display:block;';
					$back = 'background: #F07746;';
				}elseif (isset($_SESSION['dims']['minichat']['bas'][$clef]))
					$displ = 'display:block;';
				echo '	<div id="chat_'.$clef.'" onclick="" style="'.$displ.'width:175px;border-color:#000000;float:right; '.$back.'color: #FFFFFF; height:18px;text-align:left;"';
				echo 		'<div style="float:left;cursor:pointer;"><a style="color:#FFFFFF;" onclick="javascript:displayChatOpen(\''.$clef.'\',\''.$tab_javascript.'\');">&nbsp;'.$name.'</a></div>
							 <div style="float:right;cursor:pointer;"><a style="color:#FFFFFF;" onclick="javascript:closeChat(\''.$clef.'\')">X&nbsp;</a></div>';
				echo '	</div>';
			}
		}
	}
	?>
	<input type="text" style="display:none;" id="list_used" value="<? echo $tab_javascript ; ?>">
</div>
