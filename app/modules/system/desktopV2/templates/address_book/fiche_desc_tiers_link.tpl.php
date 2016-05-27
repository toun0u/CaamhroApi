<table class="contact_fiche" cellspacing="10" cellpadding="0">
    <tbody>
        <tr>
            <td style="width:60px;vertical-align:top;">
				<?
				if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
					echo '<img class="ab_desc_image" src="'.$this->getPhotoWebPath(60).'" border="0" />';
				else
					echo '<img class="ab_desc_image" src="'._DESKTOP_TPL_PATH.'/gfx/common/company_default_search.png" border="0" />';
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
							if($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_FAVORITES)
								$refreshLst = 'true';
							if ($this->isFavorite()){
								?>
								<img onclick="javascript: addToFavoriteAB(<? echo $this->fields['id_globalobject']; ?>,<? echo $refreshLst; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_plain.png" border="0">
								<?
							}else{
								?>
								<img <? if($this->fields['id_globalobject'] > 0){ ?>onclick="javascript: addToFavoriteAB(<? echo $this->fields['id_globalobject']; ?>,<? echo $refreshLst; ?>);"<? } ?> src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_empty.png" border="0">
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
							<a href="Javascript: void(0);" onclick="javascript:displayContactsGroups(event,<? echo $this->fields['id_globalobject']; ?>,<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>);">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe16.png" border="0">
								<?
								$nb_group = ct_group::getNbGroupsForContact($this->fields['id_globalobject'],dims_const::_SYSTEM_OBJECT_TIERS);
								?>
								<span>(<? echo $nb_group; ?>)</span>
							</a>
						</div>
						<?php
					}
					?>
				</div>
                <div class="puce_title_contact_fiche">
					<a style="font-weight:bold;color:#df1d31;text-decoration:none" href="?submenu=1&mode=company&action=show&id=<?php echo $_SESSION['desktopv2']['adress_book']['sel_id']; ?>">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png" border="0">
						<span style="font-weight:bold;color:#df1d31">
							<? echo $this->fields['intitule']; ?>
						</span>
					</a>
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
				?>
				<div class="desc_comment_contact_fiche">
				</div>
                <div class="icon_company">
				</div>
            </td>
        </tr>
    </tbody>
</table>
<?php
// ouverture du lien, recuperation des infos
include_once(DIMS_APP_PATH."modules/system/class_tiers_contact.php");
$idlink = dims_load_securvalue('idlink',dims_const::_DIMS_NUM_INPUT,true,true,true);
//$idlink = dims_load_securvalue('id_link',dims_const::_DIMS_CHAR_INPUT,true,true);
$sql = "SELECT tc.*,t.intitule FROM dims_mod_business_tiers_contact as tc
		INNER JOIN dims_mod_business_tiers as t on t.id=tc.id_tiers AND tc.id = :id ";

$res = $this->db->query($sql, array(
	':id' => $idlink
));
$tab_ct = $this->db->fetchrow($res);

if(!empty($tab_ct['date_deb'])) {
	$date_deb_y = substr($tab_ct['date_deb'], 0, 4);
	$date_deb_m = substr($tab_ct['date_deb'], 4, 2);
	$date_deb_d = substr($tab_ct['date_deb'], 6, 2);
}
else {
	$date_deb_y = date('Y');
	$date_deb_m = date('m');
	$date_deb_d = date('d');
}
if(!empty($tab_ct['date_fin'])) {
	$date_fin_y = substr($tab_ct['date_fin'], 0, 4);
	$date_fin_m = substr($tab_ct['date_fin'], 4, 2);
	$date_fin_d = substr($tab_ct['date_fin'], 6, 2);
}
else {
	$date_fin_y = "";
	$date_fin_m = "";
	$date_fin_d = "";
}

$id_ent=$tab_ct['id_tiers'];

echo '<form method="POST" action="admin.php?dims_op=desktopv2&action=saveupdatelink" >';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("id_link",	$idlink);
$token->field("id_ent",		$id_ent);
$token->field("type_link");
$token->field("date_fin_day");
$token->field("date_fin_month");
$token->field("date_fin_year");
$token->field("commentaire");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '<input type="hidden" name="id_link" value="'.$idlink.'"/>';
echo '<input type="hidden" name="id_ent" value="'.$id_ent.'"/>';

?>
<table cellspacing="0" cellpadding="2" class="contact_fiche_details">
    <tbody>
        <tr>
            <td class="title_contact_fiche_gras">
               <? echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?> :
            </td>
            <td class="title_desc_rouge">
                <? echo $tab_ct['intitule']; ?>
            </td>
        </tr>
        <tr>
            <td class="title_contact_fiche_gras">
                <? echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?> :
            </td>
            <td>
                <?php
				echo '<select id="type_link" name="type_link" style="width:205px;"><option ';

						if($tab_ct['type_lien'] == strtolower($_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'])) echo 'selected="selected"';
						echo ' value="'.$_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'].'">'.ucfirst($_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS']).'</option>
							<option ';

						if($tab_ct['type_lien'] == strtolower($_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'])) echo 'selected="selected"';
						echo ' value="'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'">'.ucfirst($_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']).'</option>';

				?>
            </td>
        </tr>
        <tr>
            <td class="title_contact_fiche_gras" style="vertical-align:top;">
                <? echo $_DIMS['cste']['_END']; ?> :
            </td>
            <td><span style="float:left">
				<?php
				echo '
								<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="'.$date_fin_d.'" style="width:30px;"/>&nbsp;/&nbsp;

								<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="'.$date_fin_m.'" style="width:30px;"/>&nbsp;/&nbsp;

								<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="'.$date_fin_y.'" style="width:30px;"/>
					';
				?></span>
				<span style="float:left"><a href="javascript:void(0);" onclick="javascript:$('#date_fin_day').val('');$('#date_fin_month').val('');$('#date_fin_year').val('');"><img src="./common/img/delete.png" style="border:0px;"></a>&nbsp; </span>
            </td>
        </tr>
		<tr>
			<td class="title_contact_fiche_gras">
				<? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?> :
			</td>
			<td>
				<?php
				echo '<textarea id="commentaire" name="commentaire" style="width:250px;">'.$tab_ct['commentaire'].'</textarea>';
				?>
			</td>
		</tr>
		<tr>
			<td class="title_contact_fiche_gras">&nbsp;</td>
			<td>
			<?php
			echo '<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].'"/>';
			?>
			</td>
		</tr>
	</tbody>
</table>
