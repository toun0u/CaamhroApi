<div class="content-zone pa1">
	<div class="pa1 nopbottom desk-hidden" style="overflow:hidden">
		<a href="/index.php?op=panier"  class="mod right fondbuttonnoir pa1 rounded-pic mb1">
			<i class="icon-cart"></i>
			<span id="nbArtPanier">
				{if $panier.nb_art == 0}
					Votre panier (vide)
				{else}
					{$panier.nb_art}
					{if $panier.nb_art > 1}
						articles
					{else}
						article
					{/if}
				{/if}
			</span>
		</a>
		{if (isset($switch_user_logged_out))}
			<a href="/index.php?op=connexion" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
				<i class="icon2-enter title-icon"></i>
				<span>
					Connexion
				</span>
			</a>
		{else}
			<a href="/index.php?op=compte" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
				<i class="icon2-user3 title-icon"></i>
				<span>
					Mon compte
				</span>
			</a>
		{/if}
		<a href="/accueil.html" class="mod right fondbuttonnoir pa1 rounded-pic mr2">
			<i class="icon2-home title-icon"></i>
			<span>
				Accueil
			</span>
		</a>
		<div class="arianne">
			Vous êtes ici :
			{foreach from=$arianne item=i name=it}

				{if not $smarty.foreach.it.last}
					<a href="{$i.link}">
				{/if}

				{$i.label}

				{if not $smarty.foreach.it.last}
					</a>
				{/if}

				{if not $smarty.foreach.it.last} > {/if}
			{/foreach}
		</div>
	</div>
	<h3 class="titleh2_orange"><i class="orange icon2-cart title-icon"></i>&nbsp;Panier</h3>
	{if isset($commande)}
		<form name="f_cart" action="/index.php" method="post" class="m-auto pa1">
			<input type="hidden" name="op" value="">
			<input type="hidden" name="etape" value="" />

			<section id="no-more-tables">
				<table class="table-bordered table-striped table-condensed cf">
					<thead class="cf">
						<tr class="bgblue txtwhite">
							<th class="txtleft w10">{$smarty.session.cste.REFERENCE}</th>
							<th class="txtleft">{$smarty.session.cste._DESIGNATION}</th>
							<th class="txtcenter w15">{$smarty.session.cste.QUANTITY}</th>
							<th class="txtright w15">{$smarty.session.cste.CATA_UNIT_PRICE_HT}</th>
							<th class="txtright w15">{$smarty.session.cste.CATA_SUM_HT}</th>
							<th class="w5">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$commande.articles item=article}
						<input type="hidden" name="id[]" value="{$article.id}">
						<input type="hidden" name="ref{$article.id}" value="{$article.reference}">
						<tr class="mt1">
							<td data-title="{$smarty.session.cste.REFERENCE}"><a href="/article/{$article.urlrewrite}.html" title="Voir la fiche du produit {$article.label}">{$article.reference}</a></td>
							<td data-title="{$smarty.session.cste._DESIGNATION}"><strong><a href="/article/{$article.urlrewrite}.html" title="Voir la fiche du produit {$article.label}">{$article.label}</a></strong></td>
							<td data-title="{$smarty.session.cste.QUANTITY}" class="pa1">
								<div class="grid3-1">
									<div class="mod">
										<input type="text" id="qte{$article.id}" name="qte{$article.id}" value="{$article.qte_cde}" onblur="javascript:constraintFieldsUvente(this, {$article.uvente});" class="left">
									</div>
									<div class="mod">
										<div class="btn-spec-align txtcenter">

											<div class="grid2">
												<div class="mod">
													<a href="javascript:void(0);" onclick="javascript:modifyQte(dims_getelem('qte{$article.id}'), {$article.uvente});" title="{$smarty.session.cste.CATA_AUGMENT_QTY}">
														+
													</a>
												</div>
												<div class="mod">
													<a href="javascript:void(0);" onclick="javascript:modifyQte(dims_getelem('qte{$article.id}'), -{$article.uvente});" title="{$smarty.session.cste.CATA_REDUCE_QTY}">
														-
													</a>
												</div>
											</div>
											<div class="line"></div>
										</div>
									</div>
								</div>
							</td>
							<td data-title="{$smarty.session.cste.CATA_UNIT_PRICE_HT}" class="txtright">{$article.pu_ht} € HT</td>
							<td data-title="{$smarty.session.cste.CATA_SUM_HT}" class="txtright">{$article.somme_ht} € HT</td>
							<td data-title="{$smarty.session.cste.CATA_DROP_THE_LINE}" class="txtcenter">
								<a href="javascript:void(0);" onclick="javascript:dropArticle('{$article.reference}');" title="{$smarty.session.cste.CATA_DROP_THE_LINE}">
									<i class="red icon2-cancel-circle"></i>
								</a>
							</td>
						</tr>
						{/foreach}
					</tbody>
					<tfooter>
						<tr class="bgwhite">
							<td colspan="3">&nbsp;</td>
							<td class="txtright">{$smarty.session.cste._TOTAL_DUTY_FREE_AMOUNT}</td>
							<td class="txtright">{$commande.total_ht} € HT</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="bgwhite">
							<td colspan="3">&nbsp;</td>
							<td class="txtright"><strong>{$smarty.session.cste.CATA_TOTAL_TTC}</strong></td>
							<td class="txtright"><strong>{$commande.total_ttc} € TTC</strong></td>
							<td>&nbsp;</td>
						</tr>
					</tfooter>
				</table>
				<div class="txtright">
					<input type="button" class="btn" value="{$smarty.session.cste.CATA_CONTINUE_SHOPPING}" onclick="{$achats_action}" />
					<input type="button" class="btn" value="{$smarty.session.cste.CATA_RECALCULATE_MY_CART}" onclick="javascript:document.f_cart.op.value='modifier_panier'; document.f_cart.submit();" />
					{if $cata_account_commandes_cours}
						<input type="button" class="btn" value="{$smarty.session.cste.PAUSE_MY_ORDER}" onclick="javascript:document.f_cart.op.value='valider_panier'; document.f_cart.etape.value='4.2'; document.f_cart.submit();" />
					{/if}
					<input type="button" class="btn btn-primary" value="{$smarty.session.cste.CATA_PROCEED_TO_CHECKOUT}" onclick="javascript:document.f_cart.op.value='valider_panier'; document.f_cart.submit();" />
				</div>
			</form>
		</section>
	{else}
		<div class="w70 m-auto pa1">
			<div class="flash info">
				{$smarty.session.cste.CATA_YOUR_CART_IS_EMPTY}
			</div>
		</div>
	{/if}
</div>
