<div class="content-zone pa1">
	{include file="_mobile_menu.tpl"}
	<div class="pa1">
		<div id="catalogue_content" class="phone-hidden">
			<div class="arianne">
				Vous êtes ici :
				{foreach from=$ariane item=i name=it}
					<a href="{$i.link}">
						{$i.label}
					</a>
					{if not $smarty.foreach.it.last} > {/if}
				{/foreach}
			</div>
		</div>
	</div>
	<h3 class="titleh2_orange"><i class="orange icon2-cart title-icon"></i>&nbsp;Panier</h3>
	{if isset($commande)}
		<form name="f_cart" action="/index.php" method="post" class="m-auto pa1">
			<input type="hidden" name="op" value="">
			<input type="hidden" name="etape" value="" />
			<input type="hidden" name="ask_costing" value="" />
			<input type="hidden" name="waiting_panier" value="0" />

			{if isset($errors)}
				<div class="mod flash error">
					{foreach from=$errors item=error}
						{$error}<br>
					{/foreach}
				</div>
				<br>
			{/if}

			<section id="no-more-tables">
				<table class="table-bordered table-striped table-condensed cf">
					<thead class="cf">
						<tr class="bgblue txtwhite">
							<th class="txtleft w10">{$smarty.session.cste.REFERENCE}</th>
							<th class="txtleft">{$smarty.session.cste._DESIGNATION}</th>
							<th class="txtcenter w15">{$smarty.session.cste.QUANTITY}</th>
							<th class="txtright w15">{$smarty.session.cste.CATA_UNIT_PRICE_HT}</th>
							<th class="txtright w15">{$smarty.session.cste.RATE_TVA}</th>
							<th class="txtright w15">{$smarty.session.cste._VAT_AMOUNT}</th>
							<th class="txtright w15">{$smarty.session.cste.CATA_SUM_HT}</th>
							<th class="w5">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$commande.articles item=article}
						<input type="hidden" name="id[]" value="{$article.id}">
						<input type="hidden" id="ref{$article.id}" name="ref{$article.id}" value="{$article.reference}">
						<tr class="mt1">
							<td data-title="{$smarty.session.cste.REFERENCE}">
							    {if $display_vrp_prices}
							        <a class="nounderline" href="/article/{$article.urlrewrite}.html" title="PRCS : {$article.prcs|catalogue_formateprix} &euro;. &#10;DPA : {$article.dpa|catalogue_formateprix} &euro;. &#10;Marge : {$article.margin}.">{$article.reference}</a>
                                {else}
							        <a class="nounderline" href="/article/{$article.urlrewrite}.html" title="Voir la fiche du produit {$article.label}">{$article.reference}</a>
                                {/if}
							</td>
							<td data-title="{$smarty.session.cste._DESIGNATION}"><strong><a class="nounderline" href="/article/{$article.urlrewrite}.html" title="Voir la fiche du produit {$article.label}">{$article.label}</a></strong></td>
							<td data-title="{$smarty.session.cste.QUANTITY}" class="pa1">
								<div class="grid3-1">
									<div class="mod">
										<input type="text" id="qte{$article.id}" name="qte{$article.id}" value="{$article.qte_cde}" onblur="javascript:constraintFieldsUvente(this, {$article.uvente});" class="left" onchange="javascript:recalculeFocus();">
									</div>
									<div class="mod">
										<div class="btn-spec-align txtcenter">

											<div class="grid2">
												<div class="mod">
													<a href="javascript:void(0);" onclick="javascript:modifyQte(dims_getelem('qte{$article.id}'), {$article.uvente});$('#qte{$article.id}').change();" title="{$smarty.session.cste.CATA_AUGMENT_QTY}">
														+
													</a>
												</div>
												<div class="mod">
													<a href="javascript:void(0);" onclick="javascript:modifyQte(dims_getelem('qte{$article.id}'), -{$article.uvente});$('#qte{$article.id}').change();" title="{$smarty.session.cste.CATA_REDUCE_QTY}">
														-
													</a>
												</div>
											</div>
											<div class="line"></div>
										</div>
									</div>
								</div>
							</td>
							<td data-title="{$smarty.session.cste.CATA_UNIT_PRICE_HT}" class="txtright" id="cell{$article.id}">
								{$article.pu_ht} &euro;
								{if $editables_prices}
									<a class="nounderline" href="javascript:void(0);" onclick="javascript:editPrice({$article.id}, '{$article.pu_ht}');">
										<i class="icon2-pencil orange pl1"></i>
									</a>
								{/if}
							</td>

							<td data-title="{$smarty.session.cste.RATE_TVA}" class="txtright">{$article.tva} &#37;</td>
							<td data-title="{$smarty.session.cste._VAT_AMOUNT}" class="txtright">{$article.tot_tva} &euro;</td>
							<td data-title="{$smarty.session.cste.CATA_SUM_HT}" class="txtright">{$article.somme_ht} &euro;</td>
							<td data-title="{$smarty.session.cste.CATA_DROP_THE_LINE}" class="txtcenter">
								<a href="javascript:void(0);" onclick="javascript:dropArticle('{$article.reference}');" title="{$smarty.session.cste.CATA_DROP_THE_LINE}" class="nounderline">
									<i class="red icon2-cancel-circle"></i>
								</a>
							</td>
						</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr class="bgwhite">
							<td class="pa0" colspan="5" rowspan="{if isset($commande.total_taxe_phyto) && $commande.total_taxe_phyto > 0}6{else}5{/if}">
							<table class="table-bordered table-striped table-condensed cf">
								<thead class="cf">
									<tr class="bgblue txtwhite">
										<th colspan="3">
											{$smarty.session.cste.TAXES_DETAIL}
										</th>
									</tr>
									<tr class="bgblue txtwhite">
										<th class="txtleft w20">{$smarty.session.cste.DESIGNATION}</th>
										<th class="txtright w5">{$smarty.session.cste.RATE_TVA}</th>
										<th class="txtright w5">{$smarty.session.cste._VAT_AMOUNT}</th>
									</tr>
								</thead>
								<tbody>
								{foreach $commande.VATs as $VAT}
									<tr>
										<td data-title="{$smarty.session.cste.DESIGNATION}" class="txtleft">{$VAT.label}</td>
										<td data-title="{$smarty.session.cste.RATE_TVA}" class="txtright">{$VAT.rate} %</td>
										<td data-title="{$smarty.session.cste._VAT_AMOUNT}" class="txtright">{$VAT.value} €</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
							</td>
						</tr>
						<tr class="bgwhite">
							<td class="txtright">{$smarty.session.cste._TOTAL_DUTY_FREE_AMOUNT}</td>
							<td class="txtright">{$commande.total_ht} &euro;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="bgwhite">
							<td class="txtright">{$smarty.session.cste.CATA_TOTAL_TVA}</td>
							<td class="txtright">{$commande.total_tva} &euro;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="bgwhite">
							<td class="txtright"><strong>{$smarty.session.cste.CATA_TOTAL_TTC}</strong></td>
							<td class="txtright"><strong>{$commande.total_ttc} &euro;</strong></td>
							<td>&nbsp;</td>
						</tr>
						{if isset($commande.total_taxe_phyto) && $commande.total_taxe_phyto > 0}
							<tr class="bgwhite">
								<td class="txtright">{$smarty.session.cste.INCLUDING_TAXE_PHYTO}</td>
								<td class="txtright">{$commande.total_taxe_phyto} &euro;</td>
								<td>&nbsp;</td>
							</tr>
						{/if}
					</tfoot>
				</table>
				<div class="txtright">
					<input type="button" class="btn" value="{$smarty.session.cste.CATA_CONTINUE_SHOPPING}" onclick="{$achats_action}" />
					<input type="button" class="btn panier-reacalculate" value="{$smarty.session.cste.CATA_RECALCULATE_MY_CART}" onclick="javascript:document.f_cart.op.value='modifier_panier'; document.f_cart.submit();" />
					{if $cata_account_commandes_cours}
						<input type="button" class="btn" value="{$smarty.session.cste.PAUSE_MY_ORDER}" onclick="javascript:document.f_cart.waiting_panier.value='1'; document.f_cart.op.value='valider_panier'; document.f_cart.etape.value='4.2'; document.f_cart.submit();" />
					{/if}
					<input type="button" class="btn btn-primary panier-submit" value="{$smarty.session.cste.CATA_PROCEED_TO_CHECKOUT}" onclick="javascript:document.f_cart.op.value='valider_panier'; document.f_cart.submit();" />
				</div>
			</form>
		</section>
		<script type="text/javascript">
			function recalculeFocus(){
				$('.panier-submit').attr('onclick','').addClass('disable').removeClass('btn-primary');
				$('.panier-reacalculate').addClass('btn-primary')
			}

			{if $editables_prices}
				/*
				 * Fonction d'édition des prix dans le panier
				 */
				function editPrice(articleId, articlePrice) {
					var html = '<input type="text" id="price' + articleId + '" name="price' + articleId + '" value="' + articlePrice + '" class="txtright"> &euro;' +
						'<a href="javascript:void(0);" onclick="javascript:confirmEditPrice(\'' + articleId + '\');" title="Confirmer"><i class="icon2-checkmark orange pl1"></i></a>' +
						'<a href="javascript:void(0);" onclick="javascript:closeEditPrice(\'' + articleId + '\', \'' + articlePrice + '\');" title="Annuler"><i class="icon2-close orange pl1"></i></a>';
					$('#cell' + articleId).html(html);
				}

				function confirmEditPrice(articleId) {
					document.f_cart.op.value = 'modifier_panier';
					document.f_cart.submit();
				}

				function closeEditPrice(articleId, articlePrice) {
					$('#cell' + articleId).html(articlePrice + ' &euro;<a href="javascript:void(0);" onclick="javascript:editPrice(\'' + articleId + '\', \'' + articlePrice + '\');"><i class="icon2-pencil orange pl1"></i></a>');
				}
			{/if}
		</script>
	{else}
		<div class="w70 m-auto pa1">
			<div class="flash info">
				{$smarty.session.cste.CATA_YOUR_CART_IS_EMPTY}
			</div>
		</div>
	{/if}
</div>
