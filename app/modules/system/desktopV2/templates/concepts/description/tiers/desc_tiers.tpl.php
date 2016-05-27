<div class="desc_picture_mini">
	<?
	if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
		echo '<img class="conc_img_tiers" src="'.$this->getPhotoWebPath(60).'" border="0" />';
	else
		echo '<img class="conc_img_tiers" src="'._DESKTOP_TPL_PATH.'/gfx/common/company_default_search.png" border="0" />';
	?>
</div>
<div class="desc_content">
    <div class="desc_content_title">
        <h1><? echo $this->fields['intitule']; ?></h1>
    </div>
    <div class="desc_content_link">
		<div>
			<a href="<?php echo dims::getInstance()->getScriptEnv(); ?>?mode=edit">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
				<span><? echo $_SESSION['cste']['_MODIFY']; ?></span>
			</a>
		</div>
        <?php
        require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
        $isLinked = tiersct::isLinked($this->fields['id'], $_SESSION['dims']['user']['id_contact']);

        if ($isLinked) {
            ?>
            <div>
				<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?op=desktopv2&action=remove_from_ab&id_go=<?php echo $this->fields['id_globalobject']; ?>&type=<?php echo dims_const::_SYSTEM_OBJECT_TIERS; ?>', '<?php echo addslashes($_SESSION['cste']['CONFIRM_REMOVE_FROM_ADDRESS_BOOK']); ?>');" title="<?php echo $_SESSION['cste']['REMOVE_FROM_MY_ADDRESS_BOOK']; ?>">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" />
					<span><?php echo $_SESSION['cste']['REMOVE_FROM_MY_ADDRESS_BOOK']; ?></span>
				</a>
            </div>
            <?php
        }
        else {
            // si il y a des groupes dans le carnet d'adresses,
            // on ouvre un popup pour les sélectionner
            // if ($_SESSION['desktopv2']['ab_groups']) {
            //     $onclick='displayContactsGroups(event,'.$this->fields['id_globalobject'].','.dims_const::_SYSTEM_OBJECT_CONTACT.')';
            // }
            // sinon, on ajoute simplement le contact
            // else {
                $onclick = 'document.location.href=\'/admin.php?op=desktopv2&action=add_to_ab&id_go='.$this->fields['id_globalobject'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'\';';
            // }
            ?>
            <div>
				<a href="javascript:void(0);" onclick="javascript:<?php echo $onclick; ?>" title="<?php echo $_SESSION['cste']['ADD_TO_MY_ADDRESS_BOOK']; ?>">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/attach.png" />
					<span><?php echo $_SESSION['cste']['ADD_TO_MY_ADDRESS_BOOK']; ?></span>
				</a>
            </div>
            <?php
        }
        ?>
		<div>
			<a href="Javascript:void(0);" onclick="javascript:exportVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>);" title="<?php echo $_SESSION['cste']['EXPORT_VCARD']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
				<span><?php echo $_SESSION['cste']['EXPORT_VCARD']; ?></span>
			</a>
		</div>
		<div>
			<a href="Javascript:void(0);" onclick="javascript:sendVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>);" title="<?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
				<span><?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?></span>
			</a>
		</div>
<!--         <div style="cursor: pointer;" onclick="javascript:exportVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>);">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
			<span>Export vCard</span>
		</div>
 -->
		<div>
			<a href="Javascript:void(0);" onclick="javascript:chooseCategSelection(<?php echo $this->fields['id_globalobject']; ?>);" title="<?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_add.png" />
				<span><?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?></span>
			</a>
		</div>
		<div>
			<a href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo addslashes($_SESSION['cste']['SURE_DELETE_COMPANY']);?>')) document.location.href = '?dims_op=desktopv2&action=delete_concept&type=tiers&go=<?php echo $this->fields['id_globalobject']; ?>&from=concept&desktop=1';">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" border="0" />
				<span><?php echo $_SESSION['cste']['DELETE_COMPANY']; ?></span>
			</a>
		</div>
	</div>
    <!--<table cellspacing="0" cellpadding="3">
        <tbody>
            <tr>
                <td class="title_desc">
                    <? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?> :
                </td>
                <td class="title_desc_rouge">
                    <? echo $this->fields['mel']; ?>
                </td>
            </tr>
            <tr>
                <td class="title_desc">
                    Assistant :
                </td>
                <td>
                    Anne-Catherine Lammar
                </td>
            </tr>
            <tr>
                <td class="title_desc">
                    Phone assist. :
                </td>
                <td>
                    (352) 24 78 41 06
                </td>
            </tr>
            <tr>
                <td class="title_desc">
                    Address :
                </td>
                <td>
                    <? echo $this->fields['adresse']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    <? echo $this->fields['codepostal']." ".$this->fields['ville']; ?>
                </td>
            </tr>
        </tbody>
    </table>-->
</div>
