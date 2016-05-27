<?php
$infos = array();
if($this->get('email') != '')
	$infos[] = '<a href="mailto:'.$this->get('email').'">'.$this->get('email').'</a>';
if($this->get('phone') != '')
	$infos[] ="<span data-phone=".$this->get('phone')." data-callname=".$this->get('firstname')."&nbsp;".$this->get('lastname').">".$this->get('phone')."(Tél)</span>";
if($this->get('fax') != '')
	$infos[] = $this->get('fax')." (Fax)";

$labelAddr = "";
$addresses = address::getAddressesFromGo($this->get('id_globalobject'),' ORDER BY timestp_create DESC');
if(!empty($addresses)){
	$addr = current($addresses);
	$labelAddr .= $addr->get('address')."<br />";
	if($addr->get('address2') != '')
		$labelAddr .= $addr->get('address2')."<br />";
	if($addr->get('address3') != '')
		$labelAddr .= $addr->get('address3')."<br />";
	$labelAddr .= $addr->get('postalcode');
	$city = $addr->getCity();
	$labelAddr .= " ".$city->get('label');
	$country = $addr->getCountry();
	$labelAddr .= " (".$country->get('printable_name').")";
	// TODO CEDEX
}

$nb2 = 2;
if(count($infos)){
	$nb2 ++;
}
if($labelAddr != ''){
	$nb2 ++;
}
$timestamp = dims_createtimestamp();
?>

<div class="bloc_info <?= ($this->getLightAttribute('date_fin') != '' && $this->getLightAttribute('date_fin') > 0 && $this->getLightAttribute('date_fin') <= $timestamp)?"disabled":""; ?>">
	<table style="width:100%;">
		<tr>
			<td style="width:70px;vertical-align:top;" rowspan="<?= $nb2; ?>">
				<?php
				$file = $this->getPhotoPath(60);//real_path
				if(file_exists($file)){
					?>
					<img class="picture" src="<?php echo $this->getPhotoWebPath(60); ?>">
					<?php
				}
				else{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human60.png">
					<?php
				}
				?>
			</td>
			<th style="font-size:14px;height:20px;border:0px;text-align:left;vertical-align:top;">
				<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=show&id=<?= $this->get('id'); ?>"><?= $this->get("firstname")." ".$this->get("lastname"); ?></a>
				<?php
				if($this->getLightAttribute('mode') == 'company'){
					$dateFin = date("d/m/Y");
					if($this->getLightAttribute('date_fin') != '' && $this->getLightAttribute('date_fin')){
						$dateFin = substr($this->getLightAttribute('date_fin'), 6,2)."/".substr($this->getLightAttribute('date_fin'), 4,2)."/".substr($this->getLightAttribute('date_fin'), 0,4);
					}
					?>
					<a href="javascript:void(0);" id="edit-link-<?= $this->get('id'); ?>" style="margin-left:5px;"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/pencil.png" alt="<?= $_SESSION['cste']['_DIMS_LABEL_LFB_MOD_LINK']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_LFB_MOD_LINK']; ?>" /></a>
					<a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=remove_link&id=<?= $this->getLightAttribute('id_tiers'); ?>&id_ct=<?= $this->get('id'); ?>','<?= $_SESSION['cste']['_CONFIRM_DELETE_LINK']; ?>');" href="javascript:void(0);" style="margin-left:5px;"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" alt="<?= $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>" title="<?= $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>" /></a>
					<?
				}
				?>
			</th>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<?php if ($this->getLightAttribute('function') != ''){ ?>
					<?= $_SESSION['cste']['AS_A']." : ".$this->getLightAttribute('function'); ?>
				<?php }else{ ?>
					<?= $_SESSION['cste']['_NO_FUNCTION_DEFINED']; ?>
				<?php } ?>
			</td>
		</tr>
		<?php if($labelAddr != ''){ ?>
		<tr>
			<td style="vertical-align:top;">
				<?= $labelAddr; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if(count($infos)){ ?>
		<tr>
			<td style="height:15px;">
				<?= implode(' - ',$infos); ?>
			</td>
		</tr>
		<?php } ?>
		<?php
		$myTags = $this->getMyTags(tag::TYPE_DEFAULT);
		if(count($myTags)){
		?>
		<tr>
			<td colspan="2">
				<?php
				foreach($myTags as $t){
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
			<td colspan="2" style="color:#8A8A8A;">
				<?php
				if($this->getLightAttribute('id_ct_user_create') == $_SESSION['dims']['user']['id_contact']){
					$url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$_SESSION['dims']['user']['id_contact'];
					echo $_SESSION['cste']['_ATTACHED_BY'].' <a href="'.$url.'">'.$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF']."</a> ";
				}else{
					$ct = contact::find_by(array('id'=>$this->getLightAttribute('id_ct_user_create'), 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($ct)){
						$url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$ct->get('id');
						echo $_SESSION['cste']['_ATTACHED_BY'].' <a href="'.$url.'">'.$ct->get('firstname')." ".$ct->get('lastname')."</a> ";
					}
				}
				$dd = dims_timestamp2local($this->getLightAttribute('date_create'));
				echo $_SESSION['cste']['SINGLE_THE']." ".$dd['date'];
				if($this->getLightAttribute('date_fin') != '' && $this->getLightAttribute('date_fin') > 0 && $this->getLightAttribute('date_fin') <= $timestamp){
					$dd = dims_timestamp2local($this->getLightAttribute('date_fin'));
					echo " - ".$_SESSION['cste']['_COLLAB_ENDED_THE']." ".$dd['date'];
				}
				?>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
$(document).ready(function(){
	var idPopup_<?= $this->get('id'); ?> = dims_getUniqId();
	$('a#edit-link-<?= $this->get('id'); ?>').click(function(event){
		$.ajax({
			type: "POST",
			url: "<?= dims::getInstance()->getScriptEnv(); ?>",
			data: {
				'submenu': '1',
				'mode': 'company',
				'action' : 'edit_link',
				'id': '<?= $this->getLightAttribute('id_tiers'); ?>',
				'id_ct' : '<?= $this->get('id'); ?>',
			},
			dataType: "html",
			async: false,
			success: function(data){
				var popup = '	<div style="display: none;width: auto;" class="dims-link-popup" id="'+idPopup_<?= $this->get('id'); ?>+'">\
									<div>\
										<h3>\
											<?= $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>\
											<a href="javascript:void(0);" onclick="javascript:$(this).parents(\'div#'+idPopup_<?= $this->get('id'); ?>+':first\').hide();">\
												<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />\
											</a>\
										</h3>\
										<div>\
											'+data+'\
										</div>\
									</div>\
								</div>';
				if($("div#popup_container div#"+idPopup_<?= $this->get('id'); ?>).length){
					$("div#popup_container div#"+idPopup_<?= $this->get('id'); ?>).replaceWith(popup);
				}else{
					$('div#popup_container').append(popup);
				}
				$('div#popup_container div#'+idPopup_<?= $this->get('id'); ?>).css({'visibility':'visible','display':'block','top':event.pageY,'left':event.pageX});
			},
			error: function(data){}
		});
	});
});
</script>
