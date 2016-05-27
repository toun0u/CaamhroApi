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

    if(!empty($id)) $_SESSION['dims']['entid'] = $id;

    if($id = dims_load_securvalue("id",dims_const::_DIMS_CHAR_INPUT,true,true)) {
        $ent = new ent();
        $ent->open($id);
    }

    if(!$create) {
        //On affiche les renseignements et le formulaire
        echo '<p class="entete_search" style="margin-left:5px;">Entreprise '.$ent->fields['label'].'</p>';
    }

    ?>
    <form name="form_modify_user" action="<? echo $scriptenv ?>?action=save_ent&action_sup=show<? if (!empty($id)) echo "&id=$id"; ?>" method="POST" enctype="multipart/form-data">
    <?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_contact", $ent->fields['id']);
		$token->field("ent_label");
		$token->field("ent_address");
		$token->field("ent_tel1");
		$token->field("ent_tel2");
		$token->field("ent_tel3");
		$token->field("ent_fax1");
		$token->field("ent_fax2");
		$token->field("ent_siret");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
    ?>
    <input type="hidden" name="id_contact" value="<? echo $ent->fields['id']; ?>">
    <div class="dims_form" style="float:left;width:50%;">
		<div style="padding:2px;">
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_LABEL; ?>:</label>
				<input type="text" class="text" name="ent_label"  value="<? if(!empty($ent->fields['label'])) echo htmlentities($ent->fields['label']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_ADDRESS; ?>:</label>
				<input type="text" class="text" name="ent_address"  value="<? if(!empty($ent->fields['address'])) echo htmlentities($ent->fields['address']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_TEL; ?> 1 :</label>
				<input type="text" class="text" name="ent_tel1"  value="<? if(!empty($ent->fields['tel1'])) echo htmlentities($ent->fields['tel1']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_TEL; ?> 2 :</label>
				<input type="text" class="text" name="ent_tel2"  value="<? if(!empty($ent->fields['tel2'])) echo htmlentities($ent->fields['tel2']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_TEL; ?> 3 :</label>
				<input type="text" class="text" name="ent_tel3"  value="<? if(!empty($ent->fields['tel3'])) echo htmlentities($ent->fields['tel3']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_FAX; ?> 1 :</label>
				<input type="text" class="text" name="ent_fax1"  value="<? if(!empty($ent->fields['fax1'])) echo htmlentities($ent->fields['fax1']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_FAX; ?> 2 :</label>
				<input type="text" class="text" name="ent_fax2"  value="<? if(!empty($ent->fields['fax2'])) echo htmlentities($ent->fields['fax2']); ?>">
			</p>
			<p>
				<label><? echo _SYSTEM_LABEL_ENT_SIRET; ?>:</label>
				<input type="text" class="text" name="ent_siret"  value="<? if(!empty($ent->fields['siret'])) echo htmlentities($ent->fields['siret']); ?>">
			</p>
		</div>
	</div>
    <div style="clear:both;float:right;padding:4px;width:160px">
    <?php
        echo dims_create_button(_DIMS_SAVE,"./common/img/save.gif","javascript:form_modify_user.submit();","enreg","");
    ?>
    </div>
    </form>
    <?
}
?>