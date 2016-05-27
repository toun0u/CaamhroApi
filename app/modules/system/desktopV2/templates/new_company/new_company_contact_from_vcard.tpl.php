<div class="new_company_contact_from_vcard">
    <div class="zone_new_company_contact_from_vcard">
        <span class="title_new_company_contact">
			<? echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT']; ?>
		</span>
		<span class="add_vcard" onclick="javascript:activityCtSwitchForm();">
			<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
			Add from vCard
		</span>
        <table cellspacing="10" cellpadding="0" style="width: 100%;clear:both;float:right;">
            <tbody>
                <tr>
                    <td style="width: 180px;">
                        <input onclick="javascript:activityDisplaySearchVcard(this.value);" value="computer" style="float: left;" type="radio" name="radio" class="button_radio" checked=true />
						<span style="float:left; height: 17px;line-height: 17px;">vCard on your Computer</span>
                    </td>
                    <td id="computer_vcard">
						<?
						include(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_from_computer_vcard.tpl.php');
						?>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <input onclick="javascript:activityDisplaySearchVcard(this.value);" value="existing" style="float: left;" type="radio" name="radio" class="button_radio" />
						<span style="float:left; height: 17px;line-height: 17px;">or use an existing vCard</span>
                    </td>
                </tr>
				<tr>
					<td colspan=2 id="display_vcard_search">
					</td>
				</tr>
            </tbody
        </table>
    </div>
</div>
