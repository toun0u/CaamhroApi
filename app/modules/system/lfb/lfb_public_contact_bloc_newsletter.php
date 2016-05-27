<script language="JavaScript">
    function newsletter_desinscr(id_news, id_ct) {
        if(confirm('<?php echo $_DIMS['cste']['_DIMS_CONFIRM']; ?>')) {
            dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_NEWSLETTER ?>&cat=0&dims_desktop=block&dims_action=public&action=<?php echo _NEWSLETTER_DELETE_INSC; ?>&id_news="+id_news+"&id_contact="+id_ct+"&from=1","");
        }
        document.location.reload();
    }
</script>
<?php

    $sql = "SELECT      n.*, s.*
            FROM        dims_mod_newsletter n
            INNER JOIN  dims_mod_newsletter_subscribed s
            ON          s.id_newsletter = n.id
            AND         s.id_contact = :idcontact
            AND         s.etat = 1
            WHERE       n.etat = 1";
    $res = $db->query($sql, array(
    	':idcontact' => $contact_id
    ));

	//recherche des mailing listes disponibles pour le rattachement
	//on verifiera que le contact n'est pas deja rattache a la news
	$n_occ  = 0;
	$nb_cpt = 0;
	$opt_listtor = '';
	$sql_mr = '
				SELECT 	DISTINCT		*
				FROM 			dims_mod_newsletter_mailing_list ml
				INNER JOIN 		dims_mod_newsletter_mailing_news mn
				ON 				mn.id_mailing = ml.id
				WHERE 			ml.id_user_create = :userid ';
	$res_mr = $db->query($sql_mr, array(
		':userid' => $_SESSION['dims']['userid']
	));
//echo $sql_mr;
	$nb_occ = $db->numrows($res_mr);

	if($nb_occ > 0) {
		while($tab_list = $db->fetchrow($res_mr)) {
			if(isset($tab_list['id_contact']) && $tab_list['id_contact'] != $contact_id) {
				$opt_listtor .='<option value="'.$tab_list['id_mailing'].'">'.$tab_list['label'].'</option>';
			}
		}
	}

	echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '');
?>
    <form method="POST" action="" name="add_ct_to_mailing">
    <?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$tokenHTML = $token->generate();
		echo $tokenHTML;
    ?>
	<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
        <tr class="trl1">
            <td width="100%"><?php echo $_DIMS['cste']['_DIMS_LABEL_YOUR_NEWSLETTERS']; ?></td>
        </tr>
        <?php
            $class = 'trl1';
            if($db->numrows($res) > 0) {
                while($tab_news = $db->fetchrow($res)) {

                    if($class == "trl1") $class = "trl2";
                    else $class = 'trl1';

                    $label = '';
                    $label .= $tab_news['label'];

                    if($_SESSION['dims']['user']['id_contact'] == $contact_id) {
                        $label .= '<a href="javascript:void(0);" onclick="javascript:newsletter_desinscr(\''.$tab_news['id_newsletter'].'\',\''.$contact_id.'\');"><img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_DESABONNE'].'"/></a>';
                    }

                    echo '  <tr class="'.$class.'">
                                <td>'.$label.'<td>
                            </tr>';
                }
            }
            else {
                echo '<tr class="'.$class.'"><td>'.$_DIMS['cste']['_DIMS_LABEL_NO_NEWSLETTER_ABONN'].'</td></tr>';
            }
			if($opt_listtor != '') {
        ?>
		<tr>
			<td align="left"><?php echo $_DIMS['cste']['_DIMS_LABEL_ADDCTTONEWS']; ?> : </td>
		</tr>
		<tr>
			<td align="center">
			<?php
				echo '<select id="list_toadd">
						<option value="">--</option>
						'.$opt_listtor.'
					</select>&nbsp;';
				//on regarde si le contact courant a une adresse email
				$do= "";
				$ct = new contact();
				$ct->open($id_contact);
				if($ct->fields['id_contact'] != '') $do = "javascript:document.add_ct_to_mailing.submit();";
				else {
					$sql = "SELECT email FROM dims_mod_business_contact_layer WHERE id = :idcontact AND id_layer = :idlayer ";
					$res = $db->query($sql, array(
						':idcontact'	=> $id_contact,
						':idlayer'		=> $_SESSION['dims']['workspaceid']
					));
					$email_ok = 0;
					if($db->numrows($res) > 0) {
						while($tab_ct = $db->fetchrow($res)) {
							if($tab_ct['email'] != '') {
								$email_ok = 1;
							}
						}
						if($email_ok == 1) $do = "javascript:document.add_ct_to_mailing.submit();";
						else $do = "javascript:alert('".$_DIMS['cste']['_DIMS_ALERT_NO_EMAIL']."');";
					}
					else $do = "javascript:alert('".$_DIMS['cste']['_DIMS_ALERT_NO_EMAIL']."');";
				}

				echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"",$do,"","");
			?>
			</td>
		</tr>
		<?php } ?>
    </table>
</form>
<?php
    echo $skin->close_widgetbloc();
?>
