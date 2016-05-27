<?php

/**
 * Description of controller_ticket
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class controller_ticket {
    const TYPE_MESSAGE_FRONT_OFFICE = 1 ;
    const TYPE_MESSAGE_BACK_OFFICE_INTERNE = 2;
    const TYPE_MESSAGE_BACK_OFFICE_PUBLIC_VIEW = 3;

    public static function accueilMessagerieBOIntern() {
        $id_user = dims::getInstance()->getUserId();
        ?>
        <div id="ticket_manager">
            <div id="box_selecter">
                <?
                view_box_selecter_factory::buildViewSelecter(view_box_selecter_factory::inbox, $id_user, self::TYPE_MESSAGE_BACK_OFFICE_INTERNE);
                ?>
            </div>
            <div id="message_box" style="text-align: center;">
                <?
                view_message_box_factory::buildViewMessageBox();
                ?>
            </div>
            <div id="action_panel">
                <?
                view_panel_actions_factory::buildViewPanelAction(view_panel_actions_factory::BO_BASIC_INBOX, $id_user);
                ?>
            </div>
        </div>
        <script type="text/javascript" language="javascript" src="/include/upload/javascript/uploader.js"></script>
        <script language="Javascript">
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
            //Chargement asynchrone de la boite de réception
            dims_xmlhttprequest_todiv('admin.php', 'dims_op=ticket_manager&ticket_op=<? echo _OP_TICKET_VIEW_INBOX_BOI; ?>&id_user=<? echo $id_user; ?>','','message_box');
        </script>
        <?
    }

    public static function accueilMessagerieBOPublicView($id_user) {
        ?>
        <div id="ticket_manager">
            <div id="box_selecter">
                <?
                view_box_selecter_factory::buildViewSelecter(view_box_selecter_factory::inbox, $id_user, self::TYPE_MESSAGE_BACK_OFFICE_PUBLIC_VIEW);
                ?>
            </div>
            <div id="message_box" style="text-align: center;">
                <?
                view_message_box_factory::buildViewMessageBox();
                ?>
            </div>
            <div id="action_panel">
                <?
                view_panel_actions_factory::buildViewPanelAction(view_panel_actions_factory::BOP_BASIC_INBOX, $id_user);
                ?>
            </div>
            <!--<div id="message_viewer">
                <?
                //view_message_factory::buildViewMessage();
                ?>
            </div>-->
        </div>
		<script type="text/javascript" language="javascript" src="/include/upload/javascript/uploader.js"></script>
        <script language="Javascript">
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
            //Chargement asynchrone de la boite de réception
            dims_xmlhttprequest_todiv('admin.php', 'dims_op=ticket_manager&ticket_op=<? echo _OP_TICKET_VIEW_INBOX_BOPV; ?>&id_user=<? echo $id_user; ?>','','message_box');
        </script>
        <?
        //$id_user
    }

    public static function accueilMessagerieFrontOffice($id_user) {
        ?>
        <div id="ticket_manager">
            <div id="box_selecter">
                <?
                view_box_selecter_factory::buildViewSelecter(view_box_selecter_factory::inbox, $id_user, self::TYPE_MESSAGE_FRONT_OFFICE);
                ?>
            </div>
            <div id="message_box" style="text-align: center;">
                <?
                view_message_box_factory::buildViewMessageBoxFront();
                ?>
            </div>
            <div id="action_panel">
                <?
                view_panel_actions_factory::buildViewPanelAction(view_panel_actions_factory::FO_BASIC_INBOX, $id_user);
                ?>
            </div>
            <!--<div id="message_viewer">
                <?
                //view_message_factory::buildViewMessage();
                ?>
            </div>-->
        </div>
		<script type="text/javascript" language="javascript" src="/include/upload/javascript/uploader.js"></script>
        <script language="Javascript">
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
            //Chargement asynchrone de la boite de réception
            dims_xmlhttprequest_todiv('index.php', 'dims_op=ticket_manager&ticket_op=<? echo _OP_TICKET_VIEW_INBOX_BOPV; ?>&id_user=<? echo $id_user; ?>','','message_box');
        </script>
        <?
        //$id_user
    }

    public static function controlleMessageBox_BOPV($id_user, $status){

        if($id_user > 0){
            $id_collaborateur = dims::getInstance()->getUserId();

            $liste_messages = ticket_status::getTicketsForDestAndSender($id_collaborateur, $id_user, $status);
            view_message_box_factory::buildViewMessageBox($liste_messages);
        }else{
            view_message_box_factory::buildViewMessageBox();
        }
    }

}

?>
