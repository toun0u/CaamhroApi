<?php
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture.php';
$factures = cata_facture::find_by(array('id_client'=>$this->get('id_client'))," ORDER BY date_cree ");
?>
<h2 class="contact">
	<balise id="factures">
		<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/icon_documents.png" style="width:16px;" />
		<span>
			<?= $_SESSION['cste']['_INVOICES']; ?>
		</span>
	</balise>
</h2>
<div id="linked_suivi" class="bloc_contact">
	<?php
	if(empty($factures)){
		echo $_SESSION['cste']['NO_COMMERCIAL_DOCUMENT_FOR_NOW'];
	}else{
		?>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="txtleft w10 colorwhite">
						<?= $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?>
					</th>
					<th class="txtcenter colorwhite">
						<?= $_SESSION['cste']['QUOTATION']; ?>
					</th>
					<th class="txtright w10 colorwhite">
						<?= $_SESSION['cste']['_TOTAL_DUTY_FREE_AMOUNT']; ?>
					</th>
					<th class="txtright w10 colorwhite">
						<?= $_SESSION['cste']['CATA_TOTAL_TTC']; ?>
					</th>
					<th class="w5">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($factures as $f){
					?>
					<tr>
						<td>
							<?php
							$dd = dims_timestamp2local($f->get('timestp_create'));
							echo $dd['date'];
							?>
						</td>
						<td>
							<?= $f->get('libelle'); ?>
						</td>
						<td class="txtright">
							<?= $f->get('total_ht'); ?>
						</td>
						<td class="txtright">
							<?= $f->get('total_ttc'); ?>
						</td>
						<td class="txtcenter">
							<a href="<?= dims::getInstance()->getScriptEnv(); ?>?dims_mainmenu=catalogue&dims_moduleid=<?= $f->get('id_module'); ?>&dims_desktop=block&dims_action=public&c=clients&a=show&id=<?= $f->get('id_client'); ?>&sc=quotations&sa=show&quotationid=<?= $f->get('id'); ?>"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/open_record16.png" /></a>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
	?>
</div>
