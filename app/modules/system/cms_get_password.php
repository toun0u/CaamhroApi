<?php

    $http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

    require_once DIMS_APP_PATH.'modules/system/class_forgot_password.php';

    $mail = dims_load_securvalue('mail', dims_const::_DIMS_CHAR_INPUT, true, true);
    $id_ask = dims_load_securvalue('id_ask', dims_const::_DIMS_NUM_INPUT, true, true);

    if(isset($id_ask) && $id_ask != 0 && isset($mail) && !empty($mail)) {
        //on verifie la confirmation et envoie de mail/newPass

        $sql_ask = 'SELECT id
                    FROM dims_user_ask_password
                    WHERE validated = 0
                    AND mail like :mail
                    AND id_ask = :idask
                    LIMIT 1';

        $ress_ask = $db->query($sql_ask, array(
            ':mail'     => $mail ,
            ':idask'    => $id_ask
        ));

        if($db->numrows($ress_ask) > 0) {
            $sql_user = 'SELECT id FROM dims_user WHERE email like :mail LIMIT 1';

            $ress_user = $db->query($sql_user, array(
                ':mail' => $mail ,
            ));

            if($db->numrows($ress_user) > 0) {
                $result_ask = $db->fetchrow($ress_ask);
                $result_user= $db->fetchrow($ress_user);

                $user = new user();
                $user->open($result_user['id']);

                $forgot_password = new forgot_password();
                $forgot_password->open($result_ask['id']);

                $forgot_password->fields['validated'] = 1;

                $forgot_password->save();

                $password = '';
                $hash_pwd = '';

                $char_list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $size_list  = strlen($char_list)-1;

                for($i = 0; $i < 8; $i++)
                {
                    $rand_nb    = mt_rand(0, $size_list);
                    $password  .= $char_list[$rand_nb];
                }

                //$hash_pwd = dims_getPasswordHash($password);
                //$user->fields['password'] = $hash_pwd;
                $dims->getPasswordHash($password,$user->fields['password'],$user->fields['salt']);
                $user->save();

                //mail
                $from   = array();
                $to     = array();
                $subject= '';

                $to[0]['name']     = $user->fields['lastname'].' '.$user->fields['firstname'];
                $to[0]['address']  = $user->fields['email'];

				$work = new workspace();
				$work->open($_SESSION['dims']['workspaceid']);
				$email = $work->fields['email'];
				if ($email=="") $email=_DIMS_ADMINMAIL;

				$from[0]['name'] = '';
				$from[0]['address'] = $email;

                $subject = 'Your password has been changed';

				$rootpath="";
				$rootpath="https://";
				$rootpath.=$_SERVER['HTTP_HOST'];

                $message = 'Dear '.$user->fields['firstname'].' '.$user->fields['lastname'].',<br /><br />';
                $message.= 'Your password has now been changed.<br /><br />';
                $message.= 'From now on you can access your personal microsite using your new password.<br />';
                $message.= 'Link : <a href="'.dims_urlencode($rootpath.'/index.php',true).'">Link</a><br /><br />';
                $message.= 'Login : <b>'.$user->fields['login'].'</b><br />';
                $message.= 'Password : <b>'.$password.'</b><br /><br />';
                $message .= 'Best regards,';
				$message.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

                dims_send_mail($from,$to, $subject, $message);

                ?>
                <div>
                    <h4 style="font-weight:normal;font-size:13px;"><?php echo $_DIMS['cste']['_DIMS_TEXT_FORGOT_PASSWORD2']; ?></h4>
                </div>
                <?php
            }
            else
                echo '';
        }
        else {
           echo $_DIMS['cste']['_DIMS_LABEL_REQUEST_ALREADY_TREATED'];
        }
    }
    elseif(isset($mail) && !empty($mail)) {

        //Demande de new pass on enregistre une nouvelle demande +
        //mail de confirm
        $forgot_password = new forgot_password();

        $uniqId = mt_rand();

        $forgot_password->fields['timestp_create'] = date('YmdHis');
        $forgot_password->fields['mail'] = $mail;
        $forgot_password->fields['id_ask'] = $uniqId;

		$rootpath="";
		$rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];

        $forgot_password->fields['link'] = dims_urlencode($rootpath.'/index.php?action=getPwd&id_ask='.$uniqId.'&mail='.$mail);
        $forgot_password->fields['validated'] = 0;

        $forgot_password->save();

        //mail
        $from   = array();
        $to     = array();
        $subject= '';

        $to[0]['name'] = '';
        $to[0]['address']  = $mail;

		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$email = $work->fields['email'];
		if ($email=="") $email=_DIMS_ADMINMAIL;

		$from[0]['name'] = '';
		$from[0]['address'] = $email;

        //$subject = 'Demande de mot de passe';
        $subject = 'You have asked for a new password';

        $message = 'Hello, <br /><br />';
        $message.= 'This is an automatically generated e-mail, please do not reply.<br /><br />';
        $message.= 'Thank you for requesting a new password, please confirm your request by clicking on the link below :<br />';
        $message.= '<a href="'.$forgot_password->fields['link'].'">'.$forgot_password->fields['link'].'</a><br /><br />';
        $message.= 'In case you have not made request for a new password, we kindly ask you to ignore this e-mail<br /><br />';
        $message .= 'Best regards,';
		$message.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

        /*$message = 'Bonjour <br /><br />';
        $message.= 'Vous avez &eacute;ffectu&eacute; une demande de renouvellement de mot de passe.<br />';
        $message.= 'Pour recevoir votre mot de passe vous devez confirmer votre demande en cliquant sur le lien suivant :<br />';
        $message.= '<a href="'.$forgot_password->fields['link'].'">'.$forgot_password->fields['link'].'</a><br /><br />';
        $message.= 'Si vous n\'avez pas effectu&eacute; cette demande, merci d\'ignorer cet e-mail<br /><br />';
        $message.= 'Cordialement,<br />L\'&eacute;quipe d\'organisation';*/

        dims_send_mail($from,$to, $subject, $message);

        ?>
        <div>
            <h4 style="font-weight:normal;font-size:13px;"><?php echo $_DIMS['cste']['_DIMS_TEXT_FORGOT_PASSWORD1']; ?></h4>
        </div>
        <?php

    }
    else {
        //Nouvelle page, nouvelle demande ?
        ?>
        <div style="width:400px;margin: 0 30px;">
            <h4 style="font-weight:normal;font-size:13px;"><?php echo $_DIMS['cste']['_DIMS_TEXT_FORGOT_PASSWORD']; ?></h4>
            <form name="forgotPass" action="?action=getPwd" method="post">
                <?
                // Sécurisation du formulaire par token
                require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                $token = new FormToken\TokenField;
                $token->field("mail");
                $tokenHTML = $token->generate();
                echo $tokenHTML;
                ?>
                <input type="text" name="mail" tabindex="1"/>
                <input type="submit" value="<?php echo $_DIMS['cste']['_DIMS_LABEL_GO']; ?> >" class="submit" />
            </form>
        </div>
        <?php
    }
?>
