<?php

/**
 * Description of controller_op_ticket : Functionnement identique à un op normal
 * Les valeurs devront êtres récupérée via le dims load secure_value.
 * Remarques les méthodes appelées sont celles de controller_tickets=
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class controller_op_ticket {

    public static function op_ticket($ticket_op){
        switch($ticket_op){
            case _OP_TICKET_SEND :
                $id_sender = $_SESSION['dims']['userid'];

                $string_liste_destinataire_for = dims_load_securvalue('liste_destinataire_for',dims_const::_DIMS_CHAR_INPUT, true, true, true);
                $string_liste_destinataire_cc = dims_load_securvalue('liste_destinataire_cc',dims_const::_DIMS_CHAR_INPUT, true, true, true);
                $string_liste_destinataire_cci = dims_load_securvalue('liste_destinataire_cci',dims_const::_DIMS_CHAR_INPUT, true, true, true);
                $label_objet = dims_load_securvalue('label_objet',dims_const::_DIMS_CHAR_INPUT, true, true, true);
                $label_corps_message = dims_load_securvalue('label_corps_message',dims_const::_DIMS_CHAR_INPUT, true, true, true);
                $notifying = dims_load_securvalue('notifying',dims_const::_DIMS_NUM_INPUT, true, true, true);
                $watching = dims_load_securvalue('watching',dims_const::_DIMS_NUM_INPUT, true, true, true);
                $parent_id = dims_load_securvalue('parent_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
                $root_id = dims_load_securvalue('root_id', dims_const::_DIMS_NUM_INPUT, true, true, true);

                $draft_save = dims_load_securvalue('draft_save', dims_const::_DIMS_NUM_INPUT, true, true, true);

                // todo : il serait mieux qu'il y ai 3 listes de type_sender contenant dans l'ordre les différents types des destinataires
                if($type_sender == ticket::TYPE_SENDER_GROUP){
                    $id_groupe = $id_sender ;
                    $id_user = dims::getInstance()->getUserId();
                }else{
                    $id_groupe = 0 ;
                    $id_user = $id_sender;
                }

                //todo comment joindre un objet?
                $liste_objets_joints = array () ;
				$sid = session_id();
				$upload_dir = realpath('./data/uploads/'.$sid).'/';
				if (is_dir( realpath('./data/uploads/'.$sid)) && is_dir($upload_dir)) {
					if ($dh = opendir($upload_dir)) {
						while (($filename = readdir($dh)) !== false) {
							if ($filename!="." && $filename!="..") {
								$docfile = new docfile();
								$docfile->init_description();
								$docfile->setugm();

								$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
								$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$docfile->fields['id_folder'] = -1;
								$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
								$docfile->tmpuploadedfile = $upload_dir.$filename;
								$docfile->fields['name'] = $filename;
								$docfile->fields['size'] = filesize($upload_dir.$filename);
								$docfile->fields['version'] = 0;
								$docfile->save();

								$liste_objets_joints[] = $docfile->getIdGlobalobject();
							}
						}
					}
					closedir($dh);
				}
				rmdir($upload_dir);

				$liste_destinataire_for_gr = array();
				$liste_destinataire_for_user = array();
				foreach(explode(';', $string_liste_destinataire_for) as $val){
					$typeVal = explode('-',$val);
					switch($typeVal[1]){
						case ticket::TYPE_SENDER_GROUP :
							$liste_destinataire_for_gr[] = $typeVal[0];
							break;
						case ticket::TYPE_SENDER_USER :
							$liste_destinataire_for_user[] = $typeVal[0];
							break;
					}
				}
				$liste_destinataire_cc_gr = array();
				$liste_destinataire_cc_user = array();
				foreach(explode(';', $string_liste_destinataire_cc) as $val){
					$typeVal = explode('-',$val);
					switch($typeVal[1]){
						case ticket::TYPE_SENDER_GROUP :
							$liste_destinataire_cc_gr[] = $typeVal[0];
							break;
						case ticket::TYPE_SENDER_USER :
							$liste_destinataire_cc_user[] = $typeVal[0];
							break;
					}
				}
				$liste_destinataire_cci_gr = array();
				$liste_destinataire_cci_user = array();
				foreach(explode(';', $string_liste_destinataire_cci) as $val){
					$typeVal = explode('-',$val);
					switch($typeVal[1]){
						case ticket::TYPE_SENDER_GROUP :
							$liste_destinataire_cci_gr[] = $typeVal[0];
							break;
						case ticket::TYPE_SENDER_USER :
							$liste_destinataire_cci_user[] = $typeVal[0];
							break;
					}
				}
                if($draft_save == 1){
                    ticket::sendMessage($label_objet, $label_corps_message,
                        $liste_destinataire_cc_gr, $liste_destinataire_cc_user,
                        $liste_destinataire_for_gr, $liste_destinataire_for_user,
                        $liste_destinataire_cci_gr, $liste_destinataire_cci_user,
                        $liste_objets_joints,
                        $notifiying, $id_user, $id_group, $parent_id, $root_id, $watching, true);
                }else{
                    ticket::sendMessage($label_objet, $label_corps_message,
                        $liste_destinataire_cc_gr, $liste_destinataire_cc_user,
                        $liste_destinataire_for_gr, $liste_destinataire_for_user,
                        $liste_destinataire_cci_gr, $liste_destinataire_cci_user,
                        $liste_objets_joints,
                        $notifiying, $id_user, $id_group, $parent_id, $root_id, $watching);
                }

                dims_redirect(dims::getInstance()->getScriptEnv());
                break;
            case _OP_TICKET_READ :
                break;
            case _OP_TICKET_RESPOND :
                break;
            case _OP_TICKET_VIEW_INBOX_BOPV :
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                controller_ticket::controlleMessageBox_BOPV($id_user, ticket_status::_INBOX);
                break;
            case _OP_TICKET_VIEW_INBOX_BOI :
            case _OP_TICKET_VIEW_INBOX :
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                if($id_user > 0){
                    $liste_messages = ticket_status::getTicketsForUser($id_user, ticket_status::_INBOX);
                    view_message_box_factory::buildViewMessageBox($liste_messages);
                }
                break;
            case _OP_TICKET_VIEW_OUTBOX :
            case _OP_TICKET_VIEW_OUTBOX_BOI :
                $id_user = dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                if($id_user > 0){
                    $liste_messages = ticket::getTicketsBySender($id_user);
                    view_message_box_factory::buildViewMessageBox($liste_messages);
                }
                break;
            case _OP_TICKET_VIEW_OUTBOX_BOPV :
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                if($id_user > 0){
                    $id_collaborateur = dims::getInstance()->getUserId();

                    //$liste_messages = ticket_status::getTicketsForDestAndSender($id_collaborateur, $id_user, ticket_status::_);
                    view_message_box_factory::buildViewMessageBox($liste_messages);
                }
                break;
            case _OP_TICKET_VIEW_JUNK :
            case _OP_TICKET_VIEW_JUNK_BOI :
                break;
            case _OP_TICKET_VIEW_JUNK_BOPV :
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                controller_ticket::controlleMessageBox_BOPV($id_user, ticket_status::_JUNK);
                break;
            case _OP_TICKET_VIEW_ARCHIVES:
            case _OP_TICKET_VIEW_ARCHIVES_BOI:
                break;
            case _OP_TICKET_VIEW_ARCHIVES_BOPV:
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                controller_ticket::controlleMessageBox_BOPV($id_user, ticket_status::_ARCHIVES);
                break;
            case _OP_TICKET_VIEW_DRAFTS_FO :
            case _OP_TICKET_VIEW_DRAFTS_BOI :
                break;
            case _OP_TICKET_VIEW_DRAFTS_BOPV :
                $id_user =   dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true);
                if($id_user > 0){
                    $id_collaborateur = dims::getInstance()->getUserId();

                 //   $liste_messages = ticket_status::getTicketsForDestAndSender($id_collaborateur, $id_user, ticket_status::_ARCHIVES);
                    view_message_box_factory::buildViewMessageBox($liste_messages);
                }
                break;
            case _OP_TICKET_VIEW_FOLLOW_FO :
            case _OP_TICKET_VIEW_FOLLOW_BOI :
                break;
            case  _OP_TICKET_VIEW_FOLLOW_BOPV :
                break;
            case _OP_TICKET_VIEW_TICKET :
                $id_ticket = dims_load_securvalue('id_ticket', dims_const::_DIMS_NUM_INPUT, true, true) ;

                if($id_ticket > 0){
                    //todo charger le ticket de facon exhaustive
                    $ticket = ticket::getTicketInformationsById($id_ticket);
                    $ticket->incrementReadingCount();
                    view_ticket_factory::buildViewTicket($ticket);
                }else{
                    ?>
                    <div id="popup_user" style="width:400px; height: 200px;">
                        <div class="bann">
                            <div style="margin-left:20px;float:left;"><? echo "" ; ?></div>
                            <img style="float:right; margin-right: 2px; margin-top:2px;cursor:pointer;" onclick="javascript:dims_hidepopup();" src="./common/modules/immo/gfx/close.png" />
                        </div>
                        <div>
                            <span style='width:399px; text-align: center; padding-bottom: 10px;'>
                            <?
                                echo "todo : aucun message a afficher";
                            ?>
                            </span>
                        </div>
                    </div>
                    <?
                }
                break;
            case _OP_TICKET_VIEW_RESPOND :
                break;
            case _OP_TICKET_VIEW_WRITE_TICKET :
                $id_dest = dims_load_securvalue('id_dest', dims_const::_DIMS_NUM_INPUT, true, true) ;
                view_send_ticket_factory::buildWriteTicket($id_dest);
                break;
            case _OP_TICKET_VIEW_WRITE_TICKET_IN_BOPV :

                break;
            case _OP_TICKET_CHANGE_STATUT_TICKET :
                $id_ticket = dims_load_securvalue('id_ticket', dims_const::_DIMS_NUM_INPUT, true, true) ;
                $type_statut = dims_load_securvalue('type_statut', dims_const::_DIMS_NUM_INPUT, true, true) ;

                //todo
                echo "todo = OP_TICKET_CHANGE_STATUT_TICKET";

                break;
            case _OP_TICKET_FORWARD_TICKET :
                //todo
                echo "todo OP_TICKET_FORWARD_TICKET";
                break;
            default :
                //Not implemented
        }
    }
}


?>
