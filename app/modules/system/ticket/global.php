<?php

/**
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */

require_once 'class_ticket.php' ;
require_once 'class_ticket_newsletter.php' ;
require_once 'class_ticket_dest.php' ;
require_once 'class_ticket_object.php' ;
require_once 'class_ticket_status.php' ;
require_once 'class_ticket_watch.php' ;
require_once 'controller_ticket.php' ;
require_once 'controller_op_ticket.php' ;

require_once 'view/view_box_selecter_factory.php' ;
require_once 'view/view_message_box_factory.php' ;
require_once 'view/view_message_factory.php' ;
require_once 'view/view_send_ticket_factory.php' ;
require_once 'view/view_panel_actions_factory.php' ;
require_once 'view/view_ticket_factory.php' ;

require_once 'class_op_ticket.php' ;

?>
<script type="text/javascript" src="modules/system/ticket/ticket_script.js"></script>
<?php

/*Liste des différents types de statut pour les tickets*/
define('_TICKET_STATUT_DRAFT', 1);
define('_TICKET_STATUT_UNREAD', 2);
define('_TICKET_STATUT_READ', 3);
define('_TICKET_STATUT_STARED', 4);
define('_TICKET_STATUT_ARCHIVED', 5);
define('_TICKET_STATUT_DELETED', 6);

/*Liste des différents types de ticket*/
define('_TICKET_TYPE_DEFAULT', 0);
define('_TICKET_TYPE_NEWSLETTER', 1);

/*Liste des différents destinataire/sender*/
define('_TICKET_GROUP', 1);
define('_TICKET_USER', 2);

/*Liste des opérations pour les tickets*/
define('_OP_TICKET_SEND', 1);
define('_OP_TICKET_DELETE', 2);
define('_OP_TICKET_READ', 3);
define('_OP_TICKET_RESPOND', 4);
define('_OP_TICKET_VIEW_INBOX', 5);
define('_OP_TICKET_VIEW_INBOX_BOI', 51);
define('_OP_TICKET_VIEW_INBOX_BOPV', 52);
define('_OP_TICKET_VIEW_OUTBOX', 6);
define('_OP_TICKET_VIEW_OUTBOX_BOI', 61);
define('_OP_TICKET_VIEW_OUTBOX_BOPV', 62);
define('_OP_TICKET_VIEW_JUNK', 7);
define('_OP_TICKET_VIEW_JUNK_BOI', 71);
define('_OP_TICKET_VIEW_JUNK_BOPV', 72);
define('_OP_TICKET_VIEW_ARCHIVES', 8);
define('_OP_TICKET_VIEW_ARCHIVES_BOI', 81);
define('_OP_TICKET_VIEW_ARCHIVES_BOPV', 82);
define('_OP_TICKET_VIEW_TICKET', 9);
define('_OP_TICKET_VIEW_RESPOND', 10);
define('_OP_TICKET_VIEW_WRITE_TICKET', 11);
define('_OP_TICKET_VIEW_WRITE_TICKET_IN_BOPV', 111);
define('_OP_TICKET_CHANGE_STATUT_TICKET', 12);
define('_OP_TICKET_FORWARD_TICKET', 13);
define('_OP_TICKET_SHOW_LINKED_OBJECT', 14);
define('_OP_TICKET_VIEW_DRAFTS_FO', 1501);
define('_OP_TICKET_VIEW_DRAFTS_BOI', 1502);
define('_OP_TICKET_VIEW_DRAFTS_BOPV', 1503);
define('_OP_TICKET_VIEW_FOLLOW_FO', 1601);
define('_OP_TICKET_VIEW_FOLLOW_BOI', 1602);
define('_OP_TICKET_VIEW_FOLLOW_BOPV', 1603);

?>
<script type="text/javascript" language="javascript" src="/include/upload/javascript/uploader.js"></script>
<script type="text/javascript" language="javascript">
	var uploads = new Array();
	var upload_cell, file_name;
	var count=0;
	var checkCount = 0;
	var check_file_extentions = true;
	var sid = '<? echo session_id() ; ?>';
	var page_elements = ["toolbar","page_status_bar"];
	var img_path = "../common/img/";
	var path = "";
	var bg_color = false;
	var status;
	var debug = false;
	var param1=<? echo ($op == 'file_add') ? 'true' : 'false'; ?>;
	var param2=<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
</script>
