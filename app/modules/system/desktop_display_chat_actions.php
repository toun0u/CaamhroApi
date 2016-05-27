<?php
die();
if (!empty($ferme) && $ferme > 0 ){
	if (isset($_SESSION['dims']['minichat']['haut'][$ferme]))
		unset($_SESSION['dims']['minichat']['haut'][$ferme]);
}

if (!empty($read) && $read > 0){
	unset($_SESSION['dims']['minichat']['read'][$read]);
	foreach($_SESSION['dims']['minichat']['chats'][$read] as $clef => $val){
		$db->query("UPDATE 	dims_chat_users
					SET		isread = 0
					WHERE	id_chat = :idchat
					AND		id_received = :idreceived ", array(
			':idchat'		=> $clef,
			':idreceived'	=> $_SESSION['dims']['userid']
		));
	}
}

if (!empty($haut) && ($haut > 0 || $haut == -1)){
	if (isset($_SESSION['dims']['minichat']['haut'][$haut])){
		unset($_SESSION['dims']['minichat']['haut']);
	}else{
		unset($_SESSION['dims']['minichat']['haut']) ;
		$_SESSION['dims']['minichat']['haut'][$haut] = $margin;
		unset($_SESSION['dims']['minichat']['read'][$haut]);
		foreach($_SESSION['dims']['minichat']['chats'][$haut] as $clef => $val){
			$db->query("UPDATE 	dims_chat_users
						SET		isread = 0
						WHERE	id_chat = :idchat
						AND		id_received = :idreceived ", array(
				':idchat'		=> $clef,
				':idreceived'	=> $_SESSION['dims']['userid']
			));
		}
	}
}

if (!empty($bas) && $bas > 0){
	if (isset($_SESSION['dims']['minichat']['bas'][$bas])){
		unset($_SESSION['dims']['minichat']['bas'][$bas]);
		unset($_SESSION['dims']['minichat']['haut'][$bas]);
		unset($_SESSION['dims']['minichat']['chats'][$bas]);
		//if (!isset($_SESSION['dims']['minichat']['connected'][$bas]))
		unset($_SESSION['dims']['minichat']['user'][$bas]);
		unset($_SESSION['dims']['minichat']['last'][$bas]);
		echo '&nbsp';
	}else{
		//unset($_SESSION['dims']['minichat']['bas']) ;
		$_SESSION['dims']['minichat']['bas'][$bas] = $_SESSION['dims']['minichat']['connected'][$bas];
	}
}

// msg_send_
// TODO : à modifier dans le cas d'un groupe de discussion
if (!empty($send) && $send > 0 && !empty($msg) && $msg != ''){

	// on rafraichit la liste des messages reçus pour cet utilisateur
	$sql_msg = "SELECT		msg, cm.id
				FROM		dims_chat_msg as cm
				INNER JOIN	dims_chat_users as cu
				ON			cu.id_chat = cm.id
				AND			cu.id_received = :idreceived
				AND			cu.isread = 1
				WHERE		cm.id_send = :idsend ";

	$msgs = $db->query($sql_msg, array(
			':idsend'		=> $send,
			':idreceived'	=> $_SESSION['dims']['userid']
		));
	$nb_res = $db->numrows($msgs);
	while ($res = $db->fetchrow($msgs)){
		$name = explode(' ',$_SESSION['dims']['minichat']['connected'][$send]);

		if ($_SESSION['dims']['minichat']['last'][$send] == 1)
			$_SESSION['dims']['minichat']['chats'][$send][] = stripslashes($res['msg']) ;
		else{
			$_SESSION['dims']['minichat']['chats'][$send][] = '<b>'.trim($name[0]).'</b> : '.stripslashes($res['msg']) ;
			$_SESSION['dims']['minichat']['last'][$send] = 1 ;
		}
		$db->query("UPDATE 	dims_chat_users
					SET		isread = 0
					WHERE	id_chat = :idchat
					AND		id_received = :idreceived ", array(
			':idchat'		=> $res['id'],
			':idreceived'	=> $_SESSION['dims']['userid']
		));
	}

	// on enregistre le message
	$sql = "INSERT INTO	dims_chat_msg (id_send, timestp, msg)
			VALUES		( :userid , :time , :msg )";
	$db->query($sql, array(
		':userid'	=> $_SESSION['dims']['userid'],
		':time'		=> dims_createtimestamp(),
		':msg'		=> $msg
	));

	$sql = 'SELECT 		id
			FROM		dims_chat_msg
			WHERE		id_send = :idsend
			ORDER BY	timestp DESC
			LIMIT		1';
	$res = $db->query($sql, array(
		':idsend' => $_SESSION['dims']['userid']
	));
	$id = $db->fetchrow($res);

	$sql = "INSERT INTO	dims_chat_users (id_chat, id_received, isread)
			VALUES		( :id , :send , '1')";
	$db->query($sql, array(
		':id'	=> $id['id'],
		':send'	=> $send
	));

	/*if (strrpos(htmlentities($_SESSION['dims']['minichat']['chats'][$send][count($_SESSION['dims']['minichat']['chats'][$send])-1]), htmlentities('<b>'.$_DIMS['cste']['_DIMS_LABEL_ME'].'</b> :')) !== false)
		$_SESSION['dims']['minichat']['chats'][$send][] = $msg ;
	else
		$_SESSION['dims']['minichat']['chats'][$send][] = '<b>'.$_DIMS['cste']['_DIMS_LABEL_ME'].'</b> : <br>'. $msg ;*/

	if (isset($_SESSION['dims']['minichat']['last'][$send]) && $_SESSION['dims']['minichat']['last'][$send] == -1)
		$_SESSION['dims']['minichat']['chats'][$send][] = stripslashes($msg) ;
	else{
		$_SESSION['dims']['minichat']['chats'][$send][] = '<b>'.$_DIMS['cste']['_DIMS_LABEL_ME'].'</b> : '. stripslashes($msg) ;
		$_SESSION['dims']['minichat']['last'][$send] = -1;
	}


	// on rafraichit le div
	//dims_print_r($_SESSION['dims']['minichat']['chats'][$send]);
	foreach ($_SESSION['dims']['minichat']['chats'][$send] as $val){
		echo $val.'<br>';
	}
}

if (!empty($refresh) && $refresh > 0){
	switch ($refresh){
		case 1 : // on rafraichit les blocs inférieurs
			unset($_SESSION['dims']['minichat']['connected']);
			//unset($_SESSION['dims']['minichat']['user']);
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

			// récupération de tous les messages reçus par un utilisateur en ligne
			$tab_javascript = "" ;
			$nb_connect = 0 ;
			while ($f=$db->fetchrow($res)) {
				$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
				if ($diff<=100 && $f['id'] != $_SESSION['dims']['userid']) {
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
								$_SESSION['dims']['minichat']['chats'][$f['id']][$m['id']] = stripslashes($m['msg']);
							else{
								$_SESSION['dims']['minichat']['last'][$f['id']] = 1 ;
								$_SESSION['dims']['minichat']['chats'][$f['id']][$m['id']] = '<b>'.$f['firstname'].'</b> : '. stripslashes($m['msg']);
							}
						}
					}
				}
			}
			if ($nb_connect > 0)
				$tab_javascript = substr($tab_javascript,0,-1);

			// récupération des messages de personnes hors ligne
			$sql_msg = "SELECT		cm.id, msg, id_send, u.firstname, u.lastname
						FROM		dims_chat_msg as cm
						INNER JOIN	dims_user as u
						ON			cm.id_send = u.id
						INNER JOIN	dims_chat_users as cu
						ON			cu.id_chat = cm.id
						AND			cu.id_received = :idreceived
						AND			cu.isread = 1";

			$msgs = $db->query($sql_msg, array(
				':idreceived' => $_SESSION['dims']['userid']
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

			$res=$db->query($sql);
			while ($f=$db->fetchrow($res)) {

				$diff=dims_diffdate(date("YmdHis"),$f['timestp']);
				if ($diff<=210 && $f['id'] != $_SESSION['dims']['userid']) {
					$_SESSION['dims']['minichat']['user'][$f['id']] = $f['firstname'].' '.$f['lastname'];
					$_SESSION['dims']['minichat']['connected'][$f['id']] = $f['firstname'].' '.$f['lastname'];
				}
			}
			// affichage du bloc "utilisateurs connectés (x)"
			echo '<div id=\'connected\' onclick="javascript:displayListeConnected(\''.$tab_javascript.'\');" style="border-color:#000000;float:right;padding-right:10px;background:url(\'./common/templates/backoffice/dims_lfb/img/sprite.png\') repeat-x scroll left -24px #000000; color: #FFFFFF; height:18px;text-align:center;width:195px;">';
			echo 	'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_CONNECTEDUSERS'].'&nbsp;(<b>'.$nb_connect.'</b>)';
			echo '</div>';

			// chargement des blocs de discussion inférieurs
			foreach ($_SESSION['dims']['minichat']['user'] as $clef => $name){
				if ($name!='') {
					$displ = 'display:none;';
					$back = 'background:url(\'./common/templates/backoffice/dims_lfb/img/sprite.png\') repeat-x scroll left -24px #000000;';
					if (isset($_SESSION['dims']['minichat']['read'][$clef])){
						$displ = 'display:block;';
						$back = 'background: #F07746;';
						$_SESSION['dims']['minichat']['bas'][$clef] = $name ;
					}elseif (isset($_SESSION['dims']['minichat']['bas'][$clef])){
						//dims_print_r($_SESSION['dims']['minichat']);
						$displ = 'display:block;';
					}

					echo '	<div id="chat_'.$clef.'" style="cursor:pointer;'.$displ.'width:175px;border-color:#000000;float:right; '.$back.'color: #FFFFFF; height:18px;text-align:left;">';
					echo 		'<div style="float:left;"><a style="color:#FFFFFF;" onclick="javascript:displayChatOpen(\''.$clef.'\',\''.$tab_javascript.'\');">&nbsp;'.$name.'</a></div>
								 <div style="float:right;cursor:pointer;"><a style="color:#FFFFFF;" onclick="javascript:closeChat(\''.$clef.'\')">X&nbsp;</a></div>';
					echo '	</div>';
				}
			}

			echo '<input type="text" style="display:none;" id="list_used" value="'.$tab_javascript.'">';

			break ;
		case 2 : // on rafraichit les bloc du haut

			$tab_javascript = "" ;
			foreach ($_SESSION['dims']['minichat']['connected'] as $clef => $val)
				$tab_javascript .= $clef.',';
			$tab_javascript = substr($tab_javascript,0,-1);


			// liste des personnes connectées
			$displ = 'display:none;';
			if (isset($_SESSION['dims']['minichat']['haut'][-1]))
				$displ='display:block;';
			//$liste_connect = '<div id="liste_connect" style="border-color:#000000;width:195px;float:right;'.$displ.'padding-right:10px;background:url(\'./common/templates/backoffice/dims_lfb/img/sprite.png\') repeat-x scroll left -24px #000000; color: #FFFFFF; height:18px;text-align:left;">
			$liste_connect	='				<table width="100%" cellspacing="0" cellpadding="0" style="color: #FFFFFF;">';

			foreach ($_SESSION['dims']['minichat']['connected'] as $clef => $val){
				$liste_connect .= '	<tr onclick="javascript:displayChat('.$clef.',\''.$tab_javascript.'\',\'\');displayChatOpen('.$clef.',\''.$tab_javascript.'\');">
										<td>
											&nbsp;'.$val.'
										</td>
									</tr>';
				$displ = 'display:none;';
			}

			if (count($_SESSION['dims']['minichat']['connected']) == 0)
				$list_connect .= '	<tr>
										<td>
											'.$_DIMS['cste']['_DIMS_LABEL_NO_USER_CONNECTED'].'
										</td>
									</tr>';

			$liste_connect .= '		</table>';
								//</div>';



			// liste utilisateurs
			echo $liste_connect ;
			//dims_print_r($_SESSION['dims']['minichat']);

			break ;

		case 3 :
			if (!empty($msgchat) && $msgchat > 0){
				// tous les blocs div de chat des personnes
				$liste_chat = '';
				//foreach ($_SESSION['dims']['minichat']['user'] as $clef => $val){
					if (isset($_SESSION['dims']['minichat']['haut'][$msgchat]) && isset($_SESSION['dims']['minichat']['bas'][$msgchat]))
						$displ = 'display:block; margin-right:'.$_SESSION['dims']['minichat']['haut'][$msgchat].'px;';

					$messages = '';
					if (isset($_SESSION['dims']['minichat']['chats'][$msgchat])){
						foreach($_SESSION['dims']['minichat']['chats'][$msgchat] as $msg){
							$messages .= $msg.'<br>';
						}
					}else
						$messages = '&nbsp;';

					/*$liste_chat .= '<div id="chat_open_'.$clef.'" onclick="javascript:chatFocus(\''.$clef.'\');" style="border-color:#000000;width:200px;float:right;'.$displ.'background: #373737; color: #FFFFFF; height:250px;text-align:left;">
										<div id="chat_msg_'.$clef.'" style="overflow:auto;width:190px; margin-left:5px;margin-right:5px;margin-top:5px;background: #FFFFFF;color: #000000;height:210px;">
											'.$messages.'
										</div>
										<form method="POST" onsubmit="javascript:chat_submit(\''.$clef.'\');return false;">
											<input type="text" style="width:183px; margin-left:5px;margin-right:5px;margin-top:5px;" id="msg_send_'.$clef.'">
										</form>
									</div>';*/
				//}
				echo $messages ;
				// fenetres de chat
				//echo $liste_chat ;
			}

			break ;
	}
}

die();
?>
