<?php

require_once DIMS_APP_PATH.'modules/system/class_mailinglist_attach.php';

$wce_object = new wce_object();
$wce_object->open($id_object);

$erreur = '';
$inscript = false;

if(isset($_POST['mailing_mail']) && !empty($_POST['mailing_mail'])) {
    $email = dims_load_securvalue('mailing_mail', dims_const::_DIMS_CHAR_INPUT, true, true, true);
    $exp = "/^[A-Za-z\'0-9]+([+._-][A-Za-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/";
    if (!preg_match($exp, $email)) $erreur = 'E-mail incorrect.';

    $select = ' SELECT  id
                FROM    dims_mailinglist_attach
                WHERE   id_mailinglist = :id_mailinglist
                AND     email LIKE :email';
    $params = array();
    $params[':id_mailinglist'] = array('value'=>$wce_object->fields['id_maillinglist'],'type'=>PDO::PARAM_INT);
    $params[':email'] = array('value'=>$email,'type'=>PDO::PARAM_STR);

    $res_attached = $db->query($select,$params);

    if($db->numrows($res_attached) > 0) $erreur = 'E-mail d&eacute;j&agrave; inscrit.';

    if(empty($erreur)) {
        $mailing_attach = new mailinglist_attach();
        $mailing_attach->init_description();
        $mailing_attach->fields['id_mailinglist'] = $wce_object->fields['id_maillinglist'];
        $mailing_attach->fields['email'] = $email;

        $mailing_attach->save();
        $inscript = true;
    }
}

if($inscript) {
    ?>
    <div>
        <h4>Inscription &agrave; la liste de diffusion : <?php echo $wce_object->fields['label']; ?></h4>
        <div>
            Votre inscription &agrave; notre liste de diffusion &agrave; bien &eacute;t&eacute; pris en compte.
        </div>
    </div>
    <?php
}
else {
    ?>
    <div>
        <h4>Inscription &agrave; la liste de diffusion : <?php echo $wce_object->fields['label']; ?></h4>
        <?php
        if(!empty($erreur)) {
            echo '<div class="error">'.$erreur.'</div>';
        }
        ?>
        <form method="post" action="">
            <label for="mailing_mail">Renseignez votre adresse e-mail :</label>
            <input type="text" name="mailing_mail" id="mailing_mail" />
            <input type="submit" value="Enregistrer" />
        </form>
    </div>
    <?php
}
?>
