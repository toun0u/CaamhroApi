<div class="similar-address">
	<div class="infos">
		<?= $_SESSION['cste']['_SYSTEM_DETECT_SIMILAR_ADDRESS']; ?>
		<div style="font-weight: bold;margin-bottom: 10px;margin-top: 10px;">
			<?php
			echo $this->get('address')."<br />";
			if($this->get('address2') != '')
				echo $this->get('address2')."<br />";
			if($this->get('address3') != '')
				echo $this->get('address3')."<br />";
			echo $this->get('postalcode');
			$city = $this->getCity();
			echo " ".$city->get('label');
			$country = $this->getCountry();
			echo " (".$country->get('printable_name').")";
			// TODO CEDEX
			?>
		</div>
		<?= $_SESSION['cste']['_THIS_ADDRESS_ASSOCIATED_WITH']; ?>
		<table style="margin-bottom: 10px;margin-top: 10px;">
			<tr>
				<?php
				$linkedObj = $this->getLinkedObject();
				if(!empty($linkedObj)){
					$tooltip = "";
					$nbCt = 0;
					if(isset($linkedObj[contact::MY_GLOBALOBJECT_CODE]) && !empty($linkedObj[contact::MY_GLOBALOBJECT_CODE])){
						$lstGo = array();
						foreach($linkedObj[contact::MY_GLOBALOBJECT_CODE] as $go){
							$lstGo[] = $go->get('id');
						}
						$lstCt = contact::find_by(array('id_globalobject'=>$lstGo),' ORDER BY firstname, lastname');
						foreach($lstCt as $ct){
							if($nbCt == 0){
								?>
								<td style="width:30px;">
								<?php
								$file = $ct->getPhotoPath(20);//real_path
								if(file_exists($file)){
									?>
									<img class="picture" src="<?php echo $ct->getPhotoWebPath(20); ?>">
									<?php
								}
								else{
									?>
									<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human20.png">
									<?php
								}
								?>
								</td>
								<td>
								<span>
									<?= (($ct->get("civilite")!='')?$ct->get("civilite")." ":"").$ct->get("firstname")." ".$ct->get("lastname"); ?>
									(<a href="mailto:<?= $ct->get('email'); ?>">
										<?= $ct->get('email'); ?>
									</a>)
								</span>
								<?php
							}else{
								$tooltip .= '<li class=\'human\'>'.$ct->get('firstname')." ".$ct->get('lastname').'</li>';
							}
							$nbCt++;
						}
					}
						
					if(isset($linkedObj[tiers::MY_GLOBALOBJECT_CODE]) && !empty($linkedObj[tiers::MY_GLOBALOBJECT_CODE])){
						$lstGo = array();
						foreach($linkedObj[tiers::MY_GLOBALOBJECT_CODE] as $go){
							$lstGo[] = $go->get('id');
						}
						$lstCt = tiers::find_by(array('id_globalobject'=>$lstGo),' ORDER BY intitule');
						foreach($lstCt as $ct){
							if($nbCt == 0){
								?>
								<td style="width:30px;">
								<?php
								$file = $ct->getPhotoPath(20);//real_path
								if(file_exists($file)){
									?>
									<img class="picture" src="<?php echo $ct->getPhotoWebPath(20); ?>">
									<?php
								}
								else{
									?>
									<img style="width:20px;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company40.png">
									<?php
								}
								?>
								</td>
								<td>
								<span>
									<?= $ct->get("intitule"); ?>
									(<a href="mailto:<?= $ct->get('mel'); ?>">
										<?= $ct->get('mel'); ?>
									</a>)
								</span>
								<?php
							}else{
								$tooltip .= '<li class=\'entreprise\'>'.$ct->get('intitule').'</li>';
							}
							$nbCt++;
						}
					}
					if($nbCt > 1){
						echo $_SESSION['cste']['_DIMS_LABEL_AND']." ";
						?>
						<a href="javascript:void(0);" tooltip="<ul><?= $tooltip; ?></ul>" class="tooltips">
							<?= ($nbCt-1)." ".strtolower(((($nbCt-1)==1)?$_SESSION['cste']['_ENTITY']:$_SESSION['cste']['_ENTITIES'])); ?>
						</a>
						</td>
						<?php
					}elseif($nbCt > 0){
						?>
						</td>
						<?php
					}
				}
				?>
			</tr>
		</table>
		<?= str_replace('{DIMS_TEXT}', $this->getLightAttribute('obj_to_link'), $_SESSION['cste']['_CHOICE_LINK_TEXT_TO_ADDRESS_OR_NEW']); ?>
	</div>
	<table style="margin-left:15px;">
		<tr>
			<td style="width: 10px;">
				<input type="radio" name="chose_action" value="<?= $this->get('id'); ?>" id="merge-<?= $this->get('id'); ?>" <?= ($this->getLightAttribute('adr_id') == $this->get('id'))?"checked=true":""; ?> />
			</td>
			<td>
				<label for="merge-<?= $this->get('id'); ?>">
					<?= $_SESSION['cste']['_ATTACH_TO_EXISTING_ADDRESS']; ?>
				</label>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		if($('body div#loading_ajax').length > 1)
			$('body div#loading_ajax').remove();
	});
</script>
