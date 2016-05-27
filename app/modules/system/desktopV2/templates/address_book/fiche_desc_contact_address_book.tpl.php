<table class="contact_fiche" cellspacing="10" cellpadding="0">
    <tbody>
        <tr>
            <td style="width:60px;vertical-align:top;">
				<?
				if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
					echo '<img class="ab_desc_image" src="'.$this->getPhotoWebPath(60).'" border="0" />';
				else
					echo '<img class="ab_desc_image" src="'._DESKTOP_TPL_PATH.'/gfx/common/contact_default_search.png" border="0" />';
				?>
            </td>
            <td style="vertical-align:top;">
				<div class="actions">
					<?php
					if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_DESKTOP) {
						?>
						<div class="favoris" id="ab_favoris">
							<?
							$refreshLst = 'false';
							if(isset($_SESSION['desktopv2']['adress_book']['group']) && $_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_FAVORITES)
								$refreshLst = 'true';
							if ($this->isFavorite()){
								?>
								<img onclick="javascript: addToFavoriteAB(<? echo $this->fields['id_globalobject']; ?>,<? echo $refreshLst; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_plain.png" border="0">
								<?
							}else{
								?>
								<img <? if($this->fields['id_globalobject'] > 0){ ?> onclick="javascript: addToFavoriteAB(<? echo $this->fields['id_globalobject']; ?>,<? echo $refreshLst; ?>);"<? } ?> src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_empty.png" border="0">
								<?
							}
							?>
						</div>
						<?php
					}
					if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_DESKTOP) {
						?>
						<div class="cible">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/monitored.png" border="0">
						</div>
						<?php
					}
					if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_DESKTOP) {
						?>
						<div id="ab_nb_groups" class="groups">
							<a href="Javascript: void(0);" onclick="javascript:displayContactsGroups(event,<? echo $this->fields['id_globalobject']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>);">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe16.png" border="0" style="float:left">
								<?
								$nb_group = ct_group::getNbGroupsForContact($this->fields['id_globalobject'],dims_const::_SYSTEM_OBJECT_CONTACT);
								?>
							<span>(<? echo $nb_group; ?>)</span>
							</a>
						</div>
						<?php
					}
					?>
				</div>
                <div class="puce_title_contact_fiche">
					<?
					global $lstConn;
					$isConn = false;
					if (isset($lstConn))
						foreach($lstConn as $conn)
							if ($conn->fields['id_contact'] == $this->fields['id'] && $conn->fields['diff'] <= 300)
								$isConn = true;
					if ($isConn){
					?>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" border="0" style="float:left">
						<?
					}else{
						?>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/sleep.png" border="0" style="float:left">
						<?
					}
					?>
					<span>
						<a style="font-weight:bold;color:#df1d31;text-decoration:none" href="/admin.php?submenu=1&mode=contact&action=show&id=<?php echo $this->fields['id']; ?>">
							<? echo $this->fields['lastname']." ".$this->fields['firstname']; ?>
						</a>
					</span>
				</div>
				<?php
				if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_CONCEPTS) {
					?>
					<div class="action">
						<a href="?typerech=0&init_contact_search=1">
							<img src="<?php echo _DESKTOP_TPL_PATH ; ?>/gfx/common/icon_back.png" />
							<span>
								<?php echo $_DIMS['cste']['_DIMS_LINK_BACK_LIST']; ?>
							</span>
						</a>
					</div>
					<?php
				}
				$employeur = current($this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
				?>
                <div class="desc_comment_contact_fiche">
					<i><? echo $employeur['function']; ?></i>
				</div>
				<?php
				if(!empty($employeur['intitule'])) {
					?>
					<div class="icon_company">
						<a href="/admin.php?submenu=1&mode=company&action=show&id=<? echo $employeur['id']; ?>">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png" border="0">
							<span>
								<? echo $employeur['intitule']; ?>
							</span>
						</a>
					</div>
					<?php
				}
				?>
            </td>
        </tr>
    </tbody>
</table>
<table cellspacing="0" cellpadding="3" class="contact_fiche_details">
    <tbody>
        <tr>
            <td class="title_contact_fiche_gras">
               <? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?> :
            </td>
            <td class="title_desc_rouge">
                <? echo $this->fields['email']; ?> 
            </td>
        </tr>
        <tr>
            <td class="title_contact_fiche_gras">
                <? echo $_SESSION['cste']['PHONE_NUMBER']; ?> :
            </td>
            <td>
               	<span data-phoneone="" data-callname="<? echo $this->fields['lastname']." ".$this->fields['firstname'];?>" data-phone="<? echo $this->fields['phone'];?>" > 
               	<? echo $this->fields['phone'];?></span>
            </td>
        </tr>
        <tr>
            <td class="title_contact_fiche_gras" style="vertical-align:top;">
                <? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?> :
            </td>
            <td>
				<? echo $this->fields['address']."<br />".$this->fields['postalcode']." ".$this->fields['city']; ?>
            </td>
        </tr>
		<?php
		if ($this->fields['comments'] != '') {
			?>
			<tr>
				<td class="title_contact_fiche_gras">
					<? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?> :
				</td>
				<td>
					<i><? echo $this->fields['comments']; ?></i>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<table cellspacing="0" cellpadding="3" class="link_contact_bas">
	<tbody>
		<tr>
			<?php
			if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_DESKTOP) {
				?>
				<td>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc16.png" border="0" style="float:left;margin-right:5px" />
					<span><?php echo $_SESSION['cste']['ATTACH_NEW_DOCUMENT']; ?></span>
				</td>
				<?php
			}
			?>
			<td>
				<a href="Javascript: void(0);" onclick="javascript:exportVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>);">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" border="0" style="float:left;margin-right:5px" />
					<span><?php echo $_SESSION['cste']['EXPORT_VCARD']; ?></span>
				</a>
			</td>
			<td>
				<a href="Javascript:void(0);" onclick="javascript:sendVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>);" title="<?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?>">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
					<span><?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?></span>
				</a>
			</td>
			<?php
			if($_SESSION['dims']['submainmenu'] == _DESKTOP_V2_CONCEPTS) {
				$focus = "?submenu="._DESKTOP_V2_CONCEPTS."&id=".$this->getId()."&type=".dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1';
				?>
				<td>
					<a href="Javascript: void(0);" onclick="javascript: document.location.href='<? echo $focus; ?>';">
						<span><?php  echo $_SESSION['cste']['_DIMS_FOCUS_ON_ACTIVITY']; ?></span>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" border="0" />
					</a>
				</td>
				<?php
			}
			?>
			<td>
				<a href="Javascript:void(0);" onclick="javascript:chooseCategSelection(<? echo $this->fields['id_globalobject']; ?>);" title="<?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?>">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_add.png" />
					<span><?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?></span>
				</a>
			</td>
        </tr>
    </tbody>
</table>


<script>
	 <? 
	 if( isset($_SESSION['dims']['user']['phone']) && ($_SESSION['dims']['user']['phoneforvoip']) ){
     ?>
     	var account= "<?php echo $_SESSION['dims']['user']['phone'] ?>";
		$("span[data-phoneone]").voip_call(account,'/common/modules/system/desktopV2/templates//gfx/common/tel_sortant16.png');
		<?php
	}
	?>
</script>
