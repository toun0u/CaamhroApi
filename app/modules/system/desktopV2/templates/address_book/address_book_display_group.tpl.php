<?
switch($_SESSION['desktopv2']['adress_book']['type']){
	case dims_const::_SYSTEM_OBJECT_CONTACT :
		$res = $this->getContactsGroup(dims_const::_SYSTEM_OBJECT_CONTACT);
		break;
	case dims_const::_SYSTEM_OBJECT_TIERS :
		$res = $this->getContactsGroup(dims_const::_SYSTEM_OBJECT_TIERS);
		break;
	default:
		$res = array_merge($this->getContactsGroup(dims_const::_SYSTEM_OBJECT_CONTACT),$this->getContactsGroup(dims_const::_SYSTEM_OBJECT_TIERS));
		usort($res,'sortCtTiers');
		break;
}
$this->contacts = $res;
?>
<tr <? if ($_SESSION['desktopv2']['adress_book']['group'] == $this->fields['id']) echo 'class="ligne1_sel"'; else echo 'class="ligne1" onclick="javascipt:document.location.href=\'/admin.php?group='.$this->fields['id'].'\';"'; ?>>
	<td class="icon_contacts_AB">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0">
	</td>
	<td class="text_nav_AB">
		<span><? echo $this->fields['label']; ?> (<? echo count($this->contacts); ?>)</span>
	</td>
	<td class="icon_nav_AB">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($_SESSION['desktopv2']['adress_book']['group'] == $this->fields['id']) echo 'selected_'; ?>item_fleche.png" border="0">
	</td>
</tr>