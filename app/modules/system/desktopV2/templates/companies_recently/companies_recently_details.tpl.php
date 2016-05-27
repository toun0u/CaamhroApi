<?
$us = new user();
//if ($this->fields['timestp_modify'] == $this->fields['date_creation'])
if (isset($this->fields['id_user_create']) && $this->fields['id_user_create']>0 && $this->fields['timestp_modify'] == $this->fields['date_creation'] ) {
		$us->open($this->fields['id_user_create']);
}
else if(isset($this->fields['id_user']) && $this->fields['id_user'] > 0){
		$us->open($this->fields['id_user']);
}
?>
<div class="zone_companies_recently">
    <table class="zone_companies_recently">
        <tbody>
            <tr>
                <td rowspan="2">
                    <?
                    $pathPhoto = $this->getPhotoPath(40);
                    if (file_exists($pathPhoto))
                        echo '<img src="'.$this->getPhotoWebPath(40).'" />';
                    else
                        echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/company40.png" />';
                    ?>
                </td>
                <td class="ro_zone_details">
                    <div class="ro_title <?php if(isset($this->fields['crm_type'])) {
							switch($this->fields['crm_type']) {
								case tiers::CRM_TYPE_CLIENT:
									echo " client";
									break;

								case tiers::CRM_TYPE_FOURNISSEUR:
									echo " fournisseur";
									break;
							}
						}
						?>">
                        <p style="cursor: pointer;" onclick="javascript:document.location.href='/admin.php?submenu=1&mode=company&action=show&id=<? echo $this->fields['id']; ?>';">
							<?
							echo $this->fields['intitule'];
							if (isset($this->fields['id_country']) && $this->fields['id_country'] != '' && $this->fields['id_country'] > 0){
								require_once DIMS_APP_PATH."modules/system/class_country.php";
								$country = new country();
								$country->open($this->fields['id_country']);
								$imgC = $country->getFlag();
								if ($imgC != ''){
									?>
									<img src="<? echo $imgC; ?>" title="<? echo $country->fields['printable_name']; ?>" alt="<? echo $country->fields['printable_name']; ?>">
									<?
								}
							}
							?>
						</p>
                    </div>
                </td>
            </tr>
			<tr>
				<td colspan="2" class="footer">
	                <?
					if(!$us->new){

						$contact = new contact();
						$contact->open($us->fields['id_contact']);
						$a_open = '';
						$a_close = '';
						if(!$contact->isNew()){
							$a_open = '<a href="'.dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$contact->fields['id'].'">';
							$a_close = '</a>';
						}

						if ($this->fields['timestp_modify'] == $this->fields['date_creation']){
							if(isset($this->fields['date_creation']) && !empty($this->fields['date_creation'])){
								$date = ' ';
								$d = dims_timestamp2local($this->fields['date_creation']);
								$date .= ' '.$d['date'];
							}
							echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED'].$date." ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM'])." ".$a_open.'<strong>'.ucfirst(substr($us->fields['firstname'],0,1)).". ".$us->fields['lastname'].'</strong>'.$a_close;
						}
						else{
							if(isset($this->fields['timestp_modify']) && !empty($this->fields['timestp_modify'])){
								$date = ' '.$_SESSION['cste']['SINGLE_THE'];
								$d = dims_timestamp2local($this->fields['timestp_modify']);
								$date .= ' '.$d['date'];
							}
							echo $_SESSION['cste']['_DIMS_LABEL_MOD_SHEET_IMP'].$date." ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM'])." ".$a_open.'<strong>'.ucfirst(substr($us->fields['firstname'],0,1)).". ".$us->fields['lastname'].'</strong>'.$a_close;
						}
					}
	                ?>
	            </td>
			</tr>
        </tbody>
    </table>
</div>
