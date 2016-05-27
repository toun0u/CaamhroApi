<?
if (!isset($_SESSION['desktopv2']['opportunity']['ct_added'])) $_SESSION['desktopv2']['opportunity']['ct_added'] = array();
?>

<div class="title_description">
	<h2>
		<? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 6 - <?php echo $_SESSION['cste']['CONTACT___COMPANIES_CONCERNED_BY_THE_OPPORTUNITY']; ?>
	</h2>
	<!--<input type="file" id="opp_import_excel" />-->
</div>
<div class="zone_contact_opportunity_content">
	<div class="zone_contact_opportunity_gauche">
		<div class="zone_search_contact">

<!--			<div class="searchform">
				<span>
					<input id="button_image_search_ct" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" style="float:left;" />
					<input autocomplete="off" onkeyup="javascript:searchOpportunityCt(this.value);" type="text" class="editbox_search" id="editbox_search_contact" maxlength="80" value="<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>" onfocus="Javascript:if (this.value=='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>')this.value='';" onblur="Javascript:if (this.value=='') this.value='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>';" />
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
				</span>
			</div>
 -->
		</div>
		<!--<div id="div_list_search" style="max-height:300px;overflow-x: auto;"></div>-->
		<div id="div_list_added" style="max-height:600px;overflow-x: hidden;overflow-y: auto;">
			<?php
			global $desktop;
			$added_contact = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
			foreach ($added_contact as $tiers) {
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			}
			?>
		</div>
	</div>
	<div class="zone_contact_opportunity_droite">
		<?php
		include _DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company.tpl.php';
		?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		desactiveEnterSubmit('editbox_search_contact');
		desactiveClicSubmit('button_image_search_ct');
	});
</script>
