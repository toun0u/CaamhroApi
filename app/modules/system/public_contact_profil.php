<?php

//on recupere l'action
$action = dims_load_securvalue("action_sup",dims_const::_DIMS_CHAR_INPUT,true,true);
$filter="";
$filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true);

// ACTION : VOIR LE PROFIL
//###################################

//Si on veut voir un profil, on recupere l'ID du contact
if($action == "show")
{
    $create = dims_load_securvalue("create",dims_const::_DIMS_CHAR_INPUT,true,true);

    if(!empty($id)) $_SESSION['dims']['contactid'] = $id;

    if($id = dims_load_securvalue("id",dims_const::_DIMS_CHAR_INPUT,true,true)) {
        $cont = new contact();
        $cont->open($id);
    }

    if(!$create) {
        //On affiche les renseignements et le formulaire
        echo '<p class="entete_search" style="margin-left:5px;">Profil de '.$cont->fields['firstname'].' '.$cont->fields['lastname'].'</p>';
    }

    ?>
    <form name="form_modify_user" action="<? echo $scriptenv ?>?action=save_contact&action_sup=show<? if (!empty($id)) echo "&id=$id"; ?>" method="POST" enctype="multipart/form-data">
    <?
    // Sécurisation du formulaire par token
    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
    $token = new FormToken\TokenField;
    $token->field("id_contact", $cont->fields['id']);
    $token->field("contact_lastname");
    $token->field("contact_firstname");
    $token->field("contact_service");
    $token->field("contact_function");
    $token->field("contact_phone");
    $token->field("contact_mobile");
    $token->field("contact_fax");
    $token->field("contact_address");
    $token->field("contact_postalcode");
    $token->field("contact_city");
    $token->field("contact_country");
    $token->field("contact_email");
    $token->field("contact_comments");
    $tokenHTML = $token->generate();
    echo $tokenHTML;
    /*if(!empty($create)) {
        echo '<input type="hidden" name="action" value="save_contact" >';
    }
    else {
        echo '<input type="hidden" name="action" value="modif_contact" >';
    }*/
    ?>
    <input type="hidden" name="id_contact" value="<? echo $cont->fields['id']; ?>">
    <div class="dims_form" style="float:left;width:50%;">
		<div style="padding:2px;">
			<p>
				<label><? echo _SYSTEM_LABEL_LASTNAME; ?>:</label>
				<input type="text" class="text" name="contact_lastname"  value="<? if(!empty($cont->fields['lastname'])) echo htmlentities($cont->fields['lastname']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_FIRSTNAME; ?>:</label>
				<input type="text" class="text" name="contact_firstname"  value="<? if(!empty($cont->fields['firstname'])) echo htmlentities($cont->fields['firstname']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_SERVICE; ?>:</label>
				<input type="text" class="text" name="contact_service"  value="<? if(!empty($cont->fields['service'])) echo htmlentities($cont->fields['service']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_FUNCTION; ?>:</label>
				<input type="text" class="text" name="contact_function"  value="<? if(!empty($cont->fields['function'])) echo htmlentities($cont->fields['function']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_PHONE; ?>:</label>
				<input type="text" class="text" name="contact_phone"  value="<? if(!empty($cont->fields['phone'])) echo htmlentities($cont->fields['phone']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_MOBILE; ?>:</label>
				<input type="text" class="text" name="contact_mobile"  value="<? if(!empty($cont->fields['mobile'])) echo htmlentities($cont->fields['mobile']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_FAX; ?>:</label>
				<input type="text" class="text" name="contact_fax"  value="<? if(!empty($cont->fields['fax'])) echo htmlentities($cont->fields['fax']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ADDRESS; ?>:</label>
				<textarea class="text" name="contact_address"><? if(!empty($cont->fields['address'])) echo htmlentities($cont->fields['address']); ?></textarea>
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_POSTALCODE; ?>:</label>
				<input type="text" class="text" name="contact_postalcode"  value="<? if(!empty($cont->fields['postalcode'])) echo htmlentities($cont->fields['postalcode']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_CITY; ?>:</label>
				<input type="text" class="text" name="contact_city"  value="<? if(!empty($cont->fields['city'])) echo htmlentities($cont->fields['city']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_COUNTRY; ?>:</label>
				<input type="text" class="text" name="contact_country"  value="<? if(!empty($cont->fields['country'])) echo htmlentities($cont->fields['country']); ?>">
			</p>
		</div>
	</div>
	<div style="float:left;width:49%" class="dims_form">
		<div style="padding:2px;">
			<p>
				<label><? echo _SYSTEM_LABEL_EMAIL; ?>:</label>
				<input type="text" class="text" name="contact_email"  value="<? if(!empty($cont->fields['email'])) echo htmlentities($cont->fields['email']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_COMMENTS; ?>:</label>
				<textarea class="text" name="contact_comments"><? if(!empty($cont->fields['comments'])) echo htmlentities($cont->fields['comments']); ?></textarea>
			</p>
		</div>
	</div>
    <div style="clear:both;float:right;padding:4px;width:160px">
    <?php
        echo dims_create_button(_DIMS_SAVE,"./common/img/save.gif","javascript:form_modify_user.submit();","enreg","");
    ?>
    </div>
    </form>
    <form name="form_speed_search" action="<? echo $scriptenv ?>?action=link_cont_to_ent&action_sup=show<? if (!empty($id)) echo "&id=$id"; ?>" method="POST" enctype="multipart/form-data">
    <?
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("q_search");
        $token->field("ent_id_ent");
        $token->field("ent_date_deb");
        $token->field("ent_date_fin");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
    ?>
    <div class="dims_form" style="clear:both;float:left;width:30%;">
        <div style="padding:2px;">
            <p>Liste des entreprises rattach&eacute;es :
            </p>
			<table>
            <?php
            $sql = "SELECT e.id, e.label
                    FROM dims_ent e
                    INNER JOIN dims_ent_contact ec
                    ON ec.id_ent = e.id
                    AND ec.id_contact = :idcontact
                    WHERE e.id_workspace = :workspaceid
                    ";
            if($res = $db->query($sql, array(':idcontact' => $id, ':workspaceid' => $_SESSION['dims']['workspaceid']))){
                while($tab_res = $db->fetchrow($res)){
                    echo '<tr>
                            <td><span style="cursor:pointer" onclick="javascript:show_ent('.$tab_res['id'].',\'\');">'.$tab_res['label'].'</span></td>
                        </tr>';
                }
            }
            ?>
            </table>
		</div>
	</div>
	<div style="float:left;width:69%" class="dims_form">
		<div style="padding:2px;">
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_LABEL; ?>:</label>
				<input type="text" name="q_search" id="q_search" onkeyup="javascript:speed_search_ent();" size="40">
			</p>
            <p id="speed_search">
                <label><? echo _SYSTEM_LABEL_CHOOSE; ?>:</label>
                <select size="5" name="ent_id_ent">
                </select>
            </p>
            <p>
                <label><? echo _SYSTEM_LABEL_LINK_BEGIN_DATE; ?>:</label>
                <input type="text" id="ent_date_deb" name="ent_date_deb" size="40"/>
                <a onclick="javascript:dims_calendar_open('ent_date_deb', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
            </p>
            <p>
                <label><? echo _SYSTEM_LABEL_LINK_END_DATE; ?>:</label>
                <input type="text" id="ent_date_fin" name="ent_date_fin" size="40"/>
                <a onclick="javascript:dims_calendar_open('ent_date_fin', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
            </p>
		</div>
	</div>
     <div style="clear:both;float:right;padding:4px;width:160px">
    <?php
        echo dims_create_button(_DIMS_SAVE,"./common/img/save.gif","javascript:form_speed_search.submit();","enreg","");
    ?>
    </div>

    </form>
    <?


    /*echo '<div id="infos_address">&nbsp;</div>
        <table style="margin-left:10px;margin-bottom:10px;">
            <tr>
                <td style="text-align:right;font-weight:bold;">Sexe :</td>
                <td>'.$sex.'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Age :</td>
                <td>'.$age.' ans</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Situation :</td>
                <td>'.$data['family_situation'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Inscrit le :</td>
                <td>'.$date['date'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">m&egrave;l :</td>
                <td>'.$data['email'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Phone :</td>
                <td>'.$data['phone'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">FAX :</td>
                <td>'.$data['fax'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Mobile :</td>
                <td>'.$data['mobile'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;vertical-align:top;">Addresse :</td>
                <td>'.$data['address_1'].'<br/>'.$data['address_2'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Localit&eacute; :</td>
                <td>'.$data['localite'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Service :</td>
                <td>'.$data['service'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Fonction :</td>
                <td>'.$data['function'].'</td>
            </tr>
        </table>
        <table style="margin-left:20px;">
            <tr>
                <td><img src="./common/templates/frontofficesitecom/gfx/lien_ami.jpg" alt="(A)" title="Relation amicale"/> Relation amicale</td>
                <td><input type="checkbox" name="address_ami" id="address_ami" '.$ami.' id="search_button"/></td>
            </tr>
            <tr>
                <td><img src="./common/templates/frontofficesitecom/gfx/lien_loisirs.jpg" alt="(L)" title="Relation &eacute;v&eacute;nementielle"/> Relation &eacute;v&eacute;nementielle</td>
                <td><input type="checkbox" name="address_loisirs" id="address_loisirs" '.$loisirs.' id="search_button"/></td>
            </tr>
            <tr>
                 <td><img src="./common/templates/frontofficesitecom/gfx/lien_pro.jpg" alt="(P)" title="Relation professionnelle"/> Relation professionnelle</td>
                <td><input type="checkbox" name="address_pro" id="address_pro" '.$pro.' id="search_button"/></td>
            </tr>
        </table>';
    echo '<input type="button" onclick="javascript:del_profil('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');" value="SUPPRIMER CET ADRESSE" style="margin-left:10px;margin-top:20px;" id="search_button"/> <input type="button" onclick="javascript:update_profil('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');" value="MODIFIER" style="margin-left:5px;margin-top:20px;" id="search_button"/> <input type="button" id="search_button" value="PLUS" style="margin-left:5px;margin-top:20px;" onclick="javascript:show_profil_plus('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');"/>';*/
}

// ACTION : VOIR PLUS DU PROFIL
//###################################

//Si on veut voir un profil, on recupere l'ID du contact
if($action == "showplus")
{
    $id=dims_load_securvalue("id",dims_const::_DIMS_CHAR_INPUT,true,true);

    //On recupere les infos du contact
    $sql = "SELECT du.lastname, du.firstname, du.presentation, dmpc.note
            FROM dims_user du
            INNER JOIN dims_mod_private_user_contact dmpc
            ON dmpc.id_contact = du.id
            WHERE du.id = :id AND dmpc.id_user = :userid ";
    $res = $db->query($sql, array(
        ':id'       => $id,
        ':userid'   => $_SESSION['dims']['userid']
    ));
    $data = $db->fetchrow($res);

    //On affiche les renseignements et le formulaire
    echo '<p class="entete_search" style="margin-left:5px;">Profil de '.$data['firstname'].' '.$data['lastname'].'</p>';
    echo '<div id="infos_address">&nbsp;</div>
        <table style="margin-left:5px;margin-bottom:10px;">
            <tr>
                <td style="text-align:right;font-weight:bold;width:120px;vertical-align:top;">Caract&eacute;ristiques :</td>
                <td>';
                $sql = "
                SELECT dc.label_car
                FROM dims_car dc
                INNER JOIN dims_user_car duc
                ON duc.id_car = dc.id
                WHERE duc.id_users = :id ";
                $car = $db->query($sql, array(
                    ':id'   => $id
                ));
                while($car_data = $db->fetchrow($car))
                {
                    echo $car_data['label_car'].'<br>';
                }

    echo '      </td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;width:120px;vertical-align:top;">Int&eacute;r&ecirc;ts :</td>
                <td>';
                $sql = "SELECT di.nom_interest
                FROM dims_interest di
                INNER JOIN dims_user_interest dui
                ON dua.id_int = di.id
                WHERE dui.id_user = :id ";
                $int = $db->query($sql, array(
                    ':id'   => $id
                ));
                while($int_data = $db->fetchrow($int))
                {
                    echo $int_data['nom_interest'].'<br>';
                }
    echo '      </td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;width:120px;vertical-align:top;">Pr&eacute;sentation :</td>
                <td>'.$data['presentation'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;width:120px;vertical-align:top;">Notes :</td>
                <td>
                    <textarea id="note_perso" cols="20" rows="3">'.$data['note'].'</textarea>
                </td>
            </tr>
        </table>';
    echo '<input type="button" onclick="javascript:del_profil('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');" value="SUPPRIMER CET ADRESSE" style="margin-left:5px;margin-top:20px;" id="search_button"/> <input type="button" onclick="javascript:update_note('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');" value="MODIFIER" style="margin-left:5px;margin-top:20px;" id="search_button"/> <input type="button" id="search_button" value="RETOUR" style="margin-left:5px;margin-top:20px;" onclick="javascript:show_profil('.$_SESSION['dims']['currentheadingid'].','.$id.',\''.$filter.'\');"/>';
}


// ACTION : MODIFIER LIENS PROFIL
//###################################

//Si on veut modifier une adresse
if($action == "update")
{
    $id=dims_load_securvalue("update",dims_const::_DIMS_CHAR_INPUT,true,true);
    if($_GET['data'] != ""){//Si ce sont les liens qui doivent etre modifier
        //On recupere les données a modifier
        $modif_ami_loisirs_pro = dims_load_securvalue("data",dims_const::_DIMS_CHAR_INPUT,true,true);
        $data_explode = explode('-',$modif_ami_loisirs_pro);

        //On genere la requete SQL a effectuer
        $sql = "UPDATE dims_mod_private_user_contact
                SET ami = :ami , loisirs = :loisirs , pro= :pro
                WHERE id_user = :userid
                AND id_contact = :id ";
        if($res = $db->query($sql, array(
            ':ami'      => $data_explode[0],
            ':loisirs'  => $data_explode[1],
            ':pro'      => $data_explode[2],
            ':userid'   => $_SESSION['dims']['userid'],
            ':idcontact'=> $id
        ))){echo 1;}
        else{echo 0;}
    }

    if($_GET['data_note'] != ""){//Si c'est les notes perso qui doivent etre modifier
        $modif_note = dims_load_securvalue("data_note",dims_const::_DIMS_CHAR_INPUT,true,true);

        //On genere la requete SQL a effectuer
        $sql = "UPDATE dims_mod_private_user_contact SET note= :note WHERE id_user = :userid AND id_contact = :id ";
        if($res = $db->query($sql, array(
            ':note'         => $modif_note,
            ':userid'       => $_SESSION['dims']['userid'],
            ':id'           => $id
        ))){echo 1;}
        else{echo 0;}
    }
}


// ACTION : SUPPRIMER UNE ADRESSE
//###################################

//Si on veut supprimer une adresse
if($action == "del")
{
    $id = dims_load_securvalue("id",dims_const::_DIMS_CHAR_INPUT,true,true);
    //On genere la requete SQL a effectuer
    $sql = "DELETE FROM dims_mod_private_user_contact WHERE id_user = :userid AND id_contact = :id ";
    if($res = $db->query($sql, array(
        ':userid'   => $_SESSION['dims']['userid'],
        ':id'       => $id
    ))){echo 1;}
    else{echo 0;}
}

// ACTION : AJOUTER UNE ADDRESSE
//###################################
if($action == "add"){
    $id = dims_load_securvalue("id",dims_const::_DIMS_CHAR_INPUT,true,true);
    $sql = "INSERT INTO dims_mod_private_user_contact VALUES( :userid ,  :id ,0,0,0,'')";
    if($res = $db->query($sql, array(
        ':userid'   => $_SESSION['dims']['userid'],
        ':id'       => $id
    ))){echo 1;}
    else{echo 0;}
}
?>