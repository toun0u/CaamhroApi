<?php
$parent = null;
if($this->get('id_tiers') != '' && $this->get('id_tiers') >0){
	$parent = tiers::find_by(array('id'=>$this->get('id_tiers'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
}
?>
<div class="result_tiers">
	<table style="width: 100%;">
		<tr>
			<td rowspan="<?= (!empty($parent))?3:2; ?>" style="width:70px;vertical-align:top;">
				<?php
				$file = $this->getPhotoPath(60);//real_path
				if(file_exists($file)){
					?>
					<img class="picture" src="<?php echo $this->getPhotoWebPath(60); ?>" />
					<?php
				}
				else{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_default_search.png" />
					<?php
				}
				?>
			</td>
			<td style="vertical-align:top;font-size: 14px;font-weight:bold;">
				<?= $this->get('intitule'); ?>
			</td>
			<?php
			if(!empty($parent)){
				?>
				<tr>
					<td style="vertical-align:top;font-size: 13px;">
						<?= $_SESSION['cste']['_UNDER_SERVICE_OF']." : ".$parent->get('intitule'); ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tr>
		<tr>
			<td>
				<a href="javascript:void(0);" class="addCompanyFromSearch" dims-data-value="<?= $this->get('id'); ?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/check16.png" /></a>
				<span>
					<a href="javascript:void(0);" class="addCompanyFromSearch" dims-data-value="<?= $this->get('id'); ?>">
						<?= str_replace('{DIMS_TEXT}', $this->getLightAttribute('text_attach'), $_SESSION['cste']['_ATTACH_STRUCTURE_TO_XXX']); ?>
					</a>
				</span>
			</td>
			<!--<td>
				<?php
				$resAdr = $this->getDefaultAddress();
				if(!empty($resAdr)){
					$typeAdr = current($resAdr);
					if(isset($typeAdr['add']) && !empty($typeAdr['add'])){
						$adr = current($typeAdr['add']);
						$city = new city();
						$city->open($adr->get('id_city'));
						$country = new country();
						$country->open($adr->get('id_country'));
						echo $adr->get('address')."<br />".$adr->get('postalcode')." ".$city->get('label')." ".$country->get('printable_name');
					}
				}
				?>
			</td>-->
		</tr>
	</table>
</div>