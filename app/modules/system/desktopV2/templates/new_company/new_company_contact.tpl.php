<?php
global $desktop;
$tmpPhotoComp = DIMS_TMP_PATH . '/uploadsjquery/'.session_id();

// déselection de l'entreprise si on crée un contact qui n'est pas rattaché à une entreprise
$reset_company = dims_load_securvalue('reset_company', dims_const::_DIMS_CHAR_INPUT, true, false);
if ($reset_company == 'true' && isset($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
	unset($_SESSION['desktopv2']['opportunity']['tiers_selected']);
}
?>

<div>
	<span id="title_new_company_contact" class="title_new_company_contact">
		<?php
		echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'];

		$tiers = new tiers();
		$tiers->init_description();
		if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] != '' && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
			$tiers = new tiers();
			$tiers->open($_SESSION['desktopv2']['opportunity']['tiers_selected']);
			echo " ".$_SESSION['cste']['_FROM']." : ".'<span style="color:#DF1D31;">'.dims_strcut($tiers->fields['intitule'],25)."</span>";
		}
		?>
	</span>
</div>
<div style="clear:both" class="searchform">
	<span>
		<input id="button_image_search_ct" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" style="float:left;" />
		<input autocomplete="off" onkeyup="javascript:searchLinkCtOpportunity(this.value);" type="text" class="editbox_search" id="editbox_search_contact" maxlength="80" value="<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>" onfocus="Javascript:if (this.value=='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>')this.value=''; $(this).addClass('working');" onblur="Javascript:if (this.value=='') { $(this).removeClass('working'); this.value='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>'; }" />
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
	</span>
</div>
<div id="div_list_search" style="max-height:300px;overflow-x: auto; clear: both;"></div>
<div class="otherAction">

	<a href="Javascript: void(0);" onclick="javascript:$('#new_company_contact').show('fast');">
		<?php echo $_DIMS['cste']['_DIMS_LABEL_NO_CONTACT_CREATE_ONE']; ?>
	</a>
</div>

<div id="new_company_contact" class="new_company_contact">
	<div class="zone_new_company_contact">
		<?php
		if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
			?><input type="hidden" name="tiers_selected" value="<?php echo $_SESSION['desktopv2']['opportunity']['tiers_selected']; ?>" /><?php
		}
		?>
		<span class="add_vcard" onclick="javascript:opportunityCtSwitchVcard();">
			<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />Add from vCard
		</span>
		<table cellspacing="10" cellpadding="0">
			<tbody>
				<tr>
					<td>
						<span style="float:right;"><? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></span>
					</td>
					<td>
						<select style="width: 240px;" name="title" class="title_new_contact">
							<option value=""></option>
							<option value="M.">M.</option>
							<option value="Mme">Mme</option>
							<option value="Melle">Melle</option>
						</select>
					</td>
					<!--td rowspan="4">
						<div class="cadre_add_picture" id="cadre_new_contact">
							<input type="file" id="opp_contact_photo" />
							<input type="hidden" id="photo_path_contact" name="photo_path_contact" value="" />
						</div>
						<span style="float: right; width: 100%;font-size:10px;font-weight:normal;">
							<i>60x60px</i>
						</span>
					</td-->
				</tr>
				<tr>
					<td class="text" name="lastname">
						<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
					</td>
					<td>
						<input type="text" name="lastname" style="width: 235px;" />
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_FIRSTNAME']; ?>
					</td>
					<td>
						<input type="text" name="firstname" style="width: 235px;" />
					</td>
				</tr>
				<!--<tr>
					<td class="text">
						Nickname
					</td>
					<td>
						<input type="text" name="nickname" style="width: 235px;" />
					</td>
				</tr>-->
			   <?php
				/*
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_LINK_TYPE']; ?>
					</td>
					<td colspan="2">
						<select style="width: 100%;" name="type_lien" class="function">
							<option value="<? echo $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']; ?>"><? echo ucfirst($_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']); ?></option>
							<option value="<? echo $_SESSION['cste']['_DIMS_LABEL_ASSOCIE']; ?>"><? echo ucfirst($_SESSION['cste']['_DIMS_LABEL_ASSOCIE']); ?></option>
							<option value="<? echo stripslashes($_SESSION['cste']['_DIMS_LABEL_CONSADMIN']); ?>"><? echo ucfirst(stripslashes($_SESSION['cste']['_DIMS_LABEL_CONSADMIN'])); ?></option>
							<option value="<? echo $_SESSION['cste']['_DIMS_LABEL_OTHER']; ?>"><? echo ucfirst($_SESSION['cste']['_DIMS_LABEL_OTHER']); ?></option>
						</select>
					</td>
				</tr>
				*/
				?>
				<!--<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_PHONE']; ?>
					</td>
					<td>
						<input type="text" class="email" name="phone" style="width: 98%;" value="<? echo $tiers->fields['telephone']; ?>"/>
					</td>
				</tr>-->
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_MOBILE']; ?>
					</td>
					<td>
						<input type="text" class="email" name="mobile" style="width: 98%;" value="<? echo $tiers->fields['telmobile']; ?>"/>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_FAX_WORK']; ?>
					</td>
					<td>
						<input type="text" class="email" name="fax" style="width: 98%;" value="<? echo $tiers->fields['telecopie']; ?>"/>
					</td>
				</tr>
				<!--<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
					</td>
					<td colspan="2">
						<input style="width: 98%;" type="text" name="function" />
					</td>
				</tr>-->
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
					</td>
					<td colspan="2">
						<input type="text" class="email" name="email" style="width: 98%;" value="<? echo $tiers->fields['mel']; ?>" />
					</td>
				</tr>
				<!--<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
					</td>
					<td>
						<input type="text"	name="address" style="width: 98%;" value="<? echo $tiers->fields['adresse']; ?>"/>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
					</td>
					<td>
						<input type="text"	name="postalcode" style="width: 98%;" value="<? echo $tiers->fields['codepostal']; ?>"/>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
					</td>
					<td>
						<input type="text"	name="city" style="width: 98%;" value="<? echo $tiers->fields['ville']; ?>"/>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
					</td>
					<td>
						<input type="text"	name="country" style="width: 98%;" value="<? echo $tiers->fields['pays']; ?>"/>
					</td>
				</tr>-->
				<tr>
					<td class="text" style="vertical-align:top;">
						<? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?>
					</td>
					<td colspan="2">
						<textarea id="field_22" name="comment" class="form_textarea" style="float: left; width: 98%;height:60px;"><? echo $tiers->fields['commentaire']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?>
					</td>
					<td colspan="2" style="float: left; width: 100%;" class="tag_for_contact_opp">
						<select multiple="" style="width: 400px;" name="tags" class="tags">
							<option value=""></option>
							<?
							$lstCateg = tag_category::getForObject(contact::MY_GLOBALOBJECT_CODE);
							foreach($lstCateg as $cat){
								$lstTag = $cat->getTagLink();
								if(count($lstTag)){
									?>
									<optgroup label="<?= $cat->get('label'); ?>">
									<?php
									foreach($lstTag as $tag){
										?>
										<option value="<?= $tag->get('id'); ?>"><?= $tag->get('tag'); ?></option>
										<?
									}
									?>
									</optgroup>
									<?php
								}
							}
							$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0, 'type'=>tag::TYPE_DEFAULT),' ORDER BY tag ');
							if(count($lstTag)){
								?>
								<optgroup label="<?= $_SESSION['cste']['_UNCATEGORIZED']; ?>">
								<?php
								foreach($lstTag as $tag){
									?>
									<option value="<?= $tag->get('id'); ?>"><?= $tag->get('tag'); ?></option>
									<?
								}
								?>
								</optgroup>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<!--<tr>
					<td>
						&nbsp;
					</td>
					<td class="checkbox">
						<input type="checkbox" name="vip_flag" class="checkbox"/>
						<span>Flag as VIP</span>
					</td>
				</tr>-->
				<tr>
					<td colspan="3">
						<div class="zone_contact_opportunity_enregistrement">
							<input type="button" value="<?= $_SESSION['cste']['_DIMS_CANCEL']; ?>" onclick="javascript:hide_new_company_form();" />
							<span> <?= $_SESSION['cste']['_DIMS_OR']; ?> </span>
							<input onclick="javascript:saveOppNewContact();" type="button" value="<?= $_SESSION['cste']['_ADD_IT_TO_THE_LIST']; ?>" name="enregistrement" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		/*createFileLoader();*/
		$("td.tag_for_contact_opp select.tags").chosen({no_results_text: "<div onclick=\"javascript:addNewTag('tag_for_contact_opp');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"})
	});
</script>
