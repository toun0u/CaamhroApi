<?php
$typeObj = 0;
switch ($this->getLightAttribute('mode')) {
	case 'contact':
		$typeObj = contact::MY_GLOBALOBJECT_CODE;
		break;
	case 'company':
		$typeObj = tiers::MY_GLOBALOBJECT_CODE;
		break;
}
?>
<div class="bloc_info" dims-data-value="<?= $this->get('id'); ?>" id="bloc_info_adr_<?= $this->get('id'); ?>">
	<table>
		<tr>
			<td rowspan="2" style="font-weight:bold;">
				<?php
				echo $this->get('address')."<br />";
				if($this->get('address2') != '')
					echo $this->get('address2')."<br />";
				if($this->get('address3') != '')
					echo $this->get('address3')."<br />";
				echo $this->get('postalcode');
				$city = $this->getCity();
				echo " ".$city->get('label');
				$cityTags = $city->getMyTags(tag::TYPE_GEO);
				if($this->get('bp') != ''){
					echo " ".$this->get('bp');
				}
				$country = $this->getCountry();
				echo " (".$country->get('printable_name').")";
				// TODO CEDEX
				?>
			</td>
			<td style="vertical-align:top;">
				<?php
				$lk = $this->getLinkCt($this->getLightAttribute('go_parent'));
				if(!empty($lk)){
					$infos = array();
					if($lk->get('email') != '')
						$infos[] = '<a href="mailto:'.$lk->get('email').'">'.$lk->get('email').'</a>';
					if($lk->get('phone') != '')
						// XXX : Merge dev/Master & cata-kernel - TO confirm
						// originated from cata-kernel
						// $infos[] = $lk->get('phone')." (Tél)";
						//  orginated from dev/master
						$infos[] ="<span data-phone=".$lk->get('phone')." data-callname=&nbsp;>".$lk->get('phone')."(Tél)</span>";
						// XXX : Merge dev/Master & cata-kernel **
					if($lk->get('fax') != '')
						$infos[] = $lk->get('fax')." (Fax)";
					print implode(' - ',$infos);
				}
				?>
				<?= $this->get('phone'); ?>
			</td>
			<td style="vertical-align:top;" rowspan="2">
				<?php
				$linkedObj = $this->getLinkedObject($this->getLightAttribute('go_parent'));
				if(!empty($linkedObj)){
					?>
					<ul>
						<?php
						$tooltip = "";
						$nbCt = 0;
						if(isset($linkedObj[contact::MY_GLOBALOBJECT_CODE]) && !empty($linkedObj[contact::MY_GLOBALOBJECT_CODE])){
							$lstGo = array();
							foreach($linkedObj[contact::MY_GLOBALOBJECT_CODE] as $go){
								$lstGo[] = $go->get('id');
							}
							$lstCt = contact::find_by(array('id_globalobject'=>$lstGo),' ORDER BY firstname, lastname');
							foreach($lstCt as $ct){
								$tooltip .= '<li class=\'human\'>'.$ct->get('firstname')." ".$ct->get('lastname').'</li>';
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
								$tooltip .= '<li class=\'entreprise\'>'.$ct->get('intitule').'</li>';
								$nbCt++;
							}
						}
						if($nbCt > 0){
							?>
							<li>
								<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/humans.png" />
								<a style="vertical-align:top;" href="javascript:void(0);" tooltip="<ul><?= $tooltip; ?></ul>" class="tooltips">
									<?= $nbCt." ".strtolower(((($nbCt)==1)?$_SESSION['cste']['_ENTITY']:$_SESSION['cste']['_ENTITIES'])); ?>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				?>
			</td>
			<td style="vertical-align:top;">
				<a href="javascript:void(0);" class="edit"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/pencil.png" /></a>
				<a href="javascript:void(0);" class="remove"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<?= $this->get('mail'); ?>
			</td>
			<td></td>
		</tr>
		<?php if(count($cityTags)){ ?>
			<tr>
				<td colspan="4">
					<?php
					foreach($cityTags as $t){
						?>
						<span class="tag" dims-data-value="<?= $t->get('id'); ?>">
							<?= $t->get('tag'); ?>
						</span>
						<?php
					}
					?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="4" style="color:#8A8A8A;padding-top:10px;">
				<?php
				$lk = $this->getLinkCt($this->getLightAttribute('go_parent'));
				if(!empty($lk)){
					$type = new address_type();
					$type->open($lk->get('id_type'));
					echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']." <b>".$type->getLabel()."</b>";
				}
				if($this->get('user_create') == $_SESSION['dims']['userid']){
					echo " ".$_SESSION['cste']['_CREEEE_PAR'].' <a href="'.dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$_SESSION['dims']['user']['id_contact'].'">'.$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF']."</a>";
					echo " ".$_SESSION['cste']['SINGLE_THE']." ".$this->getPrintableCreatedDate();
				}else{
					$user = $this->getCreatedBy();
					if(!$user->isNew()){
						echo " ".$_SESSION['cste']['_CREEEE_PAR'].' <a href="'.dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$user->get('id_contact').'">'.$user->get('firstname')." ".$user->get('lastname')."</a>";
						echo " ".$_SESSION['cste']['SINGLE_THE']." ".$this->getPrintableCreatedDate();
					}
				}
				?>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		$(document).ready(function(){
			$('div.bloc_info#bloc_info_adr_<?= $this->get('id'); ?> a.edit').click(function(){
				$.ajax({
					type: "POST",
			        url: "<?= dims::getInstance()->getScriptEnv(); ?>",
			        data: {
			            'submenu': '1',
			            'mode': 'address',
			            'action' : 'view_edit',
			            'id': <?= $this->get('id'); ?>,
			            'id_ct' : '<?= $this->getLightAttribute('id_ct'); ?>',
			            'type': <?= $typeObj; ?>,
			        },
			        dataType: "html",
			        async: false,
			        success: function(data){
						$('div.bloc_info#bloc_info_adr_<?= $this->get('id'); ?>').replaceWith(data);
			        },
			        error: function(data){}
				});
			});
			$('div.bloc_info#bloc_info_adr_<?= $this->get('id'); ?> a.remove').click(function(){
				dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=address&action=del_link&id=<?= $this->get('id'); ?>&id_ct=<?= $this->getLightAttribute('id_ct'); ?>&type=<?= $typeObj; ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');
			});
		});
	</script>
</div>
