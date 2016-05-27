<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
	<tbody>
		<tr>
			<td style="width:24px;">
				<?
				if (file_exists($this->getPhotoPath(24)))
					echo '<img class="activity_img_tiers2" src="'.$this->getPhotoWebPath(24).'" />';
				else
					echo '<img class="activity_img_tiers2" style="width:24px;height:24px;" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" />';
				?>
			</td>
			<td style="padding-top: 13px; padding-left: 5px; font-size: 14px; color: #df1d31;">
				<? echo $this->fields['intitule']; ?>
			</td>
                        <td style="padding-top:9px;width:26px;">
				<a class="previsu progressiveActive" href="javascript:void(0);" onclick="javascript:detailCompanyOpp(<? echo $this->fields['id']; ?>); " />
			</td>
			<td style="padding-top:9px;width:26px;">
				<a class="close progressiveActive" href="javascript:void(0);" onclick="javascript:unselCompanyOpp(<? echo $this->fields['id']; ?>); searchActivityTiersContact('<?php echo addslashes($_SESSION['cste']['LOOKING_EXISTING_CONTACT']); ?>', '<?php echo _DESKTOP_TPL_PATH; ?>');" />
			</td>
		</tr>
	</tbody>
</table>

<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
		<tr>
			<td colspan="3" align="right" height="30px">
				<a class="activity_new_contact" href="javascript:void(0);" onclick="javascript:show_new_contact_form(false);" title="<?php echo $_SESSION['cste']['_ADD_CT']; ?>">
					<span><?php echo $_SESSION['cste']['_ADD_CT']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" alt="<?php echo $_SESSION['cste']['_ADD_CT']; ?>" />
				</a>
			</td>
		</tr>
</table>


<?php
foreach ($this->getLightAttribute('employees') as $employee) {
	if (!isset($_SESSION['desktopv2']['activity']['ct_added'][$employee->fields['id']])){
		$employee->setLightAttribute('idtiers',$this->fields['id']);
		$employee->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/search_contact.tpl.php');
	}
}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$("div#new_company_result table:last").css("border-bottom","none");
	});
</script>
