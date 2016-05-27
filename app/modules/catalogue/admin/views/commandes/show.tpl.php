<?php
$view = view::getInstance();
$cde = $view->get('commande');
$states = $view->get('states');
$cli = $cde->getClient();
$user = $view->get('user_create');
$oCatalogue = $view->get('oCatalogue');
$isTTC = $oCatalogue->getParams('cata_base_ttc');
?>
<div>
	<div class="cde_left">
		<table>
			<tr>
				<td>
					<?php
					$title = (isset($states[$cde->fields['etat']]))?$states[$cde->fields['etat']]:"";
					switch ($cde->fields['etat']) {
						case commande::_STATUS_VALIDATED:
							?>
							<img alt="<?= $title; ?>" title="<?= $title; ?>" src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" />
							<?php
							break;
						case commande::_STATUS_REFUSED:
							?>
							<img alt="<?= $title; ?>" title="<?= $title; ?>" src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" />
							<?php
							break;
						case commande::_STATUS_PROGRESS:
						case commande::_STATUS_WAIT_PAYMENT:
						case commande::_STATUS_AWAITING_VALIDATION1:
						case commande::_STATUS_AWAITING_VALIDATION2:
						case commande::_STATUS_AWAITING_VALIDATION3:
							if($cde->fields['hors_cata']){
								?>
								<img alt="<?= $title; ?>" title="<?= $title; ?>" src="<?= $view->getTemplateWebPath('gfx/pastille_jaune12.png'); ?>" />
								<?php
							}else{
								?>
								<img alt="<?= $title; ?>" title="<?= $title; ?>" src="<?= $view->getTemplateWebPath('gfx/pastille_orange12.png'); ?>" />
								<?php
							}
							break;
					}
					?>
				</td>
				<td style="font-size:16px;font-weight:bold;">
					<?= dims_constant::getVal('_ORDER_NO'); ?>
					<span style="color:#FD661F;">
						<?= $cde->get('id_cde'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php
					$dc = dims_timestamp2local($cde->fields['date_cree']);
					echo dims_constant::getVal('_CREATED_FEM')."&nbsp;".$dc['date']."&nbsp;".strtolower(dims_constant::getVal('_AT'))."&nbsp;".substr($dc['time'],0,5)."&nbsp;".strtolower(dims_constant::getVal('_DIMS_LABEL_FROM'))."&nbsp;".$user->fields['firstname']."&nbsp;".$user->fields['lastname'];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div style="width:90px;float:left;text-align: center;">
						<?= (($img = $cli->getLogo(80)) != '')?$img:'<img style="margin-top:20px;" src="'.$view->getTemplateWebPath('gfx/clients50x30.png').'" />'; ?>
					</div>
					<div style="float:right;line-height:15px">
						<div>
							<?= dims_constant::getVal('_CLIENT_ACCOUNT'); ?> :&nbsp;
							<a href="<?= get_path('clients', 'show', array('id'=>$cli->get('id_client'))); ?>">
								<?= $cli->fields['code_client']." - ".$cli->fields['nom']; ?>
								<img style="float: right;padding-left: 5px;" src="<?= $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" alt="<?= $cli->fields['code_client']." - ".$cli->fields['nom']; ?>" title="<?= $cli->fields['code_client']." - ".$cli->fields['nom']; ?>" />
							</a>
						</div>
						<div>
							<?= dims_constant::getVal('_PAYMENT_MEAN'); ?> :&nbsp;
							<span style="color:#FD661F;">
								<?= moyen_paiement::getTypeLabel($cde->fields['mode_paiement']); ?>
							</span>
						</div>
						<div>
							<?= dims_constant::getVal('_BILLING_ADDRESS'); ?> :&nbsp;
							<div style="font-weight:bold;margin-top:5px;">
								<?php
								$country = new country();
								$country->open($cde->fields['cli_id_pays']);
								$pays = (isset($country->fields['name']))?" (".$country->fields['name'].")":"";
								?>
								<?= $cde->fields['cli_adr1']; ?><br />
								<?= $cde->fields['cli_cp']." ".$cde->fields['cli_ville'].$pays; ?>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:14px;padding-top:15px;">
					<?php
					if(trim($cde->fields['commentaire']) != ''){
						?>
						<span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
						<?= $cde->fields['commentaire']; ?>
						<span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
	</div>
	<?php $portHt = $cde->fields['port']; ?>
	<div class="cde_right">
		<table class="cde_table">
			<tr>
				<td style="width:80%;">
					<?= ($isTTC?dims_constant::getVal('_SUBTOTAL_TTC'):dims_constant::getVal('_SUBTOTAL_HT')); ?> :
				</td>
				<td>
					<?= money_format('%n',($isTTC?$cde->fields['total_ttc']:$cde->fields['total_ht'])); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?= $isTTC?dims_constant::getVal('_SHIPPING_INC_VAT'):dims_constant::getVal('_SHIPPING'); ?> :
				</td>
				<td>
					<?php if($cde->fields['cli_liv_cp'] == -1){
						echo "-";
					}else{
						?>
						<form method="post" name="edit_head" action="<?= get_path('commandes', 'edit_head', array('id_cde' => $cde->get('id_cde'))); ?>">
							<input type="text" name="cde_port" value="<?= $cde->fields['port']; ?>" size="10" /> &euro;
							<a href="Javascript: void(0);" onclick="Javascript: document.forms.edit_head.submit();">
								<?= dims_constant::getVal('_DIMS_VALID'); ?>
							</a>
						</form>
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
			 <?php
            $affTb = false; // affichage du tableau des details tva
            if (count($this->get('lst_bases'))>1){
                $affTb=true;
            }
            if ($affTb){
                ?>
				<td colspan="2">
					<table class="cde_base" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<?= ($isTTC?dims_constant::getVal('_TTC_BASIS'):dims_constant::getVal('_HT_BASIS'));; ?>
							</td>
							<td>
								<?= dims_constant::getVal('_POURC_VAT'); ?>
							</td>
							<td>
								<?= dims_constant::getVal('_VAT_AMOUNT'); ?>
							</td>
						</tr>
				<?php
			}
			$total_ht = $total_tva = 0;
			$ttc_ht = $isTTC?'ttc':'ht';
			foreach($this->get('lst_bases') as $bas){
				$basHtnet = $bas['ht'];
				$tva = $isTTC ? ($basHtnet * $bas['tx_tva'] / (100 + $bas['tx_tva'])) : ($basHtnet * $bas['tx_tva'] / 100);
				$total_tva += $tva;
				$total_ht += $basHtnet;

				if ($affTb){
            		?>
						<tr>
							<td>
								<?= money_format('%n',$bas['ht']); ?>
							</td>
							<td>
								<?= number_format($bas['tx_tva'],2,',', ' '); ?>
							</td>
							<td>
								<?= money_format('%n',$tva); ?>
							</td>
						</tr>
					<?php
				}
			}

			$tva = $isTTC ? $cde->fields['port'] * $cde->fields['port_tx_tva'] / (100 + $cde->fields['port_tx_tva']) : $cde->fields['port'] * $cde->fields['port_tx_tva'] / 100;
			$total_tva += $tva;
			$total_ht += $portHt;

			if ($affTb){
						?>
						<tr>
							<td>
								<?= money_format('%n',($isTTC?$cde->fields['port']:$portHt)); ?>
							</td>
							<td>
								<?= number_format($cde->fields['port_tx_tva'],2,',', ' '); ?>
							</td>
							<td>
								<?= money_format('%n',$tva); ?>
							</td>
						</tr>
					</table>
				</td>
				<?php
			}
			?>
			</tr>
			<tr>
				<td>
					<?= dims_constant::getVal('_TOTAL_DUTY_FREE_AMOUNT'); ?> :
				</td>
				<td>
					<?= money_format('%n',$cde->fields['total_ht'] + $cde->fields['port']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?= dims_constant::getVal('_TOTAL_VAT'); ?> :
				</td>
				<td>
					<?= money_format('%n',$cde->fields['total_tva']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?= dims_constant::getVal('_TOTAL_TAX'); ?> :
				</td>
				<td>
					<?= money_format('%n',$cde->fields['total_ht'] + $cde->fields['port'] + $cde->fields['total_tva']); ?>
				</td>
			</tr>
		</table>
	</div>
<div>
