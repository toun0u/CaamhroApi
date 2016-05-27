<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$list_adresses = $this->getLightAttribute("list_adresses");
?>
<div id="add_address"><a href="javascript:void(0);" onclick="addAddress();"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />&nbsp;<? echo $_SESSION['cste']['ADD_ADDRESS'];?></a></div>
<div id="add_address_content">
	<?php
	if (empty($list_adresses['perso']) && empty($list_adresses['pro']) || isset($_SESSION['dims']['form_scratch']['contacts']['success']) && !$_SESSION['dims']['form_scratch']['contacts']['success']) {
		echo '<script>addAddress();</script>';
	}
	?>
</div>
<div id="list_addresses">
<?php

if (!empty($list_adresses)) {
	foreach ($list_adresses as $type) {
		if(isset($type['add']) && !empty($type['add'])){
			$t = $type['obj'];
			?>
			<h3>
				<?= $t->getLabel(); ?>
			</h3>
			<?php
			foreach($type['add'] as $address){
				$address->setLightAttribute('type',$t->get('id'));
				$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/address.tpl.php');
			}
		}
	}
}
?>
</div>