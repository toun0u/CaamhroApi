<div class="content-zone">
	{include file="_mobile_menu.tpl"}
	<h3 class="titleh2_orange"><i class="orange icon2-cart title-icon"></i>&nbsp;{$smarty.session.cste.CATA_YOUR_ORDER}</h3>
	<hr class="mt0">

	<form name="f_recap" action="/index.php" method="post" onsubmit="javascript:return valider_commande(this);" class="mt3 m-auto pa1">
		<input type="hidden" name="op" value="{$catalogue.op}" />
		<input type="hidden" name="etape" value="4.1" />
		<input type="hidden" name="ask_costing" value="0" />

		{if sizeof($catalogue.modes_paiement) && sizeof($catalogue.modes_paiement) == 1}
			{foreach from=$catalogue.modes_paiement item=mp}
				<input type="hidden" name="mode_paiement" value="{$mp.value}" checked="checked">
			{/foreach}
		{/if}

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
					</tr>
				</thead>
				<tbody>
					{foreach from=$catalogue.articles item=article}
						<tr class="{$article.class}">
							<td data-title="{$smarty.session.cste.REFERENCE}">{$article.reference}</td>
							<td data-title="{$smarty.session.cste._DESIGNATION}"><strong>{$article.designation}</strong></td>
							<td data-title="{$smarty.session.cste.QUANTITY}" class="txtcenter">{$article.qte_cde}</td>
							<td data-title="{$smarty.session.cste.CATA_UNIT_PRICE_HT}" class="txtright">{$article.pu_net_ht} &euro; HT</td>
							<td data-title="{$smarty.session.cste.RATE_TVA}" class="txtright">{$article.tva} &#37;</td>
							<td data-title="{$smarty.session.cste._VAT_AMOUNT}" class="txtright">{$article.tot_tva} &euro;</td>
							<td data-title="{$smarty.session.cste.CATA_SUM_HT}" class="txtright">{$article.total_ligne} &euro; HT</td>
						</tr>
					{/foreach}
				</tbody>
				<tfoot>
					<tr class="bgwhite">
						<td class="pa0" colspan="5" rowspan="{if isset($catalogue.commande.total_taxe_phyto) && $catalogue.commande.total_taxe_phyto > 0}7{else}6{/if}">
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
							{foreach $catalogue.commande.VATs as $VAT}
								{if $VAT.value > 0}
									<tr>
										<td data-title="{$smarty.session.cste.DESIGNATION}" class="txtleft">{$VAT.label}</td>
										<td data-title="{$smarty.session.cste.RATE_TVA}" class="txtright">{if !empty($VAT.rate)}{$VAT.rate} %{/if}</td>
										<td data-title="{$smarty.session.cste._VAT_AMOUNT}" class="txtright">{$VAT.value} €</td>
									</tr>
								{/if}
							{/foreach}
							</tbody>
						</table>
						</td>
					</tr>
					{if $catalogue.require_costing || $catalogue.commande.mt_port_ht > 0}
						<tr class="bgwhite">
							<td class="txtright">{$smarty.session.cste.CATA_SS_TOTAL_HT}</td>
							<td class="txtright">{$catalogue.commande.ss_total_ht} &euro; HT</td>
						</tr>
						<tr class="bgwhite">
							<td class="txtright">{$smarty.session.cste.CATA_FRAIS_PORT_HT}</td>
							<td class="txtright" id="cellfp">
						        {if $editables_prices}
							        <a class="nounderline" href="javascript:void(0);" onclick="javascript:editPrice('fp', '{$catalogue.commande.mt_port_ht}');">
								        <i class="icon2-pencil orange pl1"></i>
							        </a>
						        {/if}
                                {if $catalogue.require_costing}
                                    {$smarty.session.cste._TO_BE_CALCULATED}
                                {else}
									{$catalogue.commande.mt_port_ht} &euro; HT
                                {/if}
                            </td>
						</tr>
					{/if}
					<tr class="bgwhite">
						<td class="txtright">{$smarty.session.cste._TOTAL_DUTY_FREE_AMOUNT}</td>
						<td class="txtright">{$catalogue.commande.total_ht} &euro; HT</td>
					</tr>
					<tr class="bgwhite">
						<td class="txtright">{$smarty.session.cste.CATA_TOTAL_TVA}</td>
						<td class="txtright">{$catalogue.commande.total_tva} &euro;</td>
					</tr>
					<tr class="bgwhite">
						<td class="txtright"><strong style="font-size: 16px;">{$smarty.session.cste.CATA_TOTAL_TTC}</strong></td>
						<td class="txtright"><strong style="font-size: 16px;">{$catalogue.commande.total_ttc} &euro; TTC</strong></td>
					</tr>
					{if isset($catalogue.commande.total_taxe_phyto) && $catalogue.commande.total_taxe_phyto > 0}
						<tr class="bgwhite">
							<td class="txtright">{$smarty.session.cste.INCLUDING_TAXE_PHYTO}</td>
							<td class="txtright">{$catalogue.commande.total_taxe_phyto} &euro;</td>
						</tr>
					{/if}
				</tfoot>
			</table>
		</section>

		<div class="grid2 pa1">
			<div class="mod content-zone minheight">
				<h4 class="bgblue txtwhite pa1">{$smarty.session.cste.BILLING_ADDRESS}</h4>
				<table class="nomargin">
					<tbody>
						<tr class="bgwhite">
							<td>{$catalogue.commande.adr_fact}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="mod content-zone minheight">
				<h4 class="bgblue txtwhite pa1">{$smarty.session.cste.DELIVERY_ADDRESS}</h4>
				<table class="nomargin">
					<tbody>
						<tr class="bgwhite">
							<td>{$catalogue.commande.adr_liv}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="pas ptl mtl">
			<h5>Référence de document (facultatif) :</h5>
			<p>
				<input type="text" name="cde_libelle" maxlength="255" value="{$catalogue.delivery_conditions.libelle}" />
			</p>
		</div>

		{if $catalogue.commande.delivery && ($catalogue.require_costing || $catalogue.auto_costing)}
			<div class="grid1 pa1">
				<div class="mod content-zone w100">
					<h4 class="bgblue txtwhite pa1">Conditions de livraison</h4>

					<div class="pa1">
						<h5>Camion</h5>
						<p>
							Livraison semi-remorque possible ? *<br>
							<input class="mlm" type="radio" id="semi_remorque_possible_0" name="semi_remorque_possible" value="0" {if $catalogue.delivery_conditions.semi_remorque_possible === 0}checked="checked"{/if}>
							<label for="semi_remorque_possible_0">Non</label>
							<input class="mlm" type="radio" id="semi_remorque_possible_1" name="semi_remorque_possible" value="1" {if $catalogue.delivery_conditions.semi_remorque_possible === 1}checked="checked"{/if}>
							<label for="semi_remorque_possible_1">Oui</label>
						</p>
						<p>
							Si hauteur maximale pour camion, l’indiquer :<br>
							<input type="text" name="hauteur_maxi" maxlength="8" value="{$catalogue.delivery_conditions.hauteur_maxi}" />
						</p>
						<p>
							Hayon nécessaire (supplément de {$catalogue.supplement_hayon} € HT) ? *<br>
							<input class="mlm hayon_necessaire" type="radio" id="hayon_necessaire_0" name="hayon_necessaire" value="0" {if $catalogue.delivery_conditions.hayon_necessaire === 0}checked="checked"{/if}>
							<label for="hayon_necessaire_0">Non</label>
							<input class="mlm hayon_necessaire" type="radio" id="hayon_necessaire_1" name="hayon_necessaire" value="1" {if $catalogue.delivery_conditions.hayon_necessaire === 1}checked="checked"{/if}>
							<label for="hayon_necessaire_1">Oui</label>
						</p>
						<p>
							Tire-palette nécessaire ? *<br>
							<input class="mlm" type="radio" id="tire_pal_necessaire_0" name="tire_pal_necessaire" value="0" {if $catalogue.delivery_conditions.tire_pal_necessaire === 0}checked="checked"{/if}>
							<label for="tire_pal_necessaire_0">Non</label>
							<input class="mlm" type="radio" id="tire_pal_necessaire_1" name="tire_pal_necessaire" value="1" {if $catalogue.delivery_conditions.tire_pal_necessaire === 1}checked="checked"{/if}>
							<label for="tire_pal_necessaire_1">Oui</label>
						</p>
						<p>
							Autre ?<br>
							<input type="text" name="camion_autre" maxlength="150" value="{$catalogue.delivery_conditions.camion_autre}" />
						</p>

						<h5>Horaires/jours livraison</h5>
						<p>
							Préciser les éventuelles impossibilités de réception (ex : pas le vendredi après-midi) :<br>
							<input type="text" name="impossibilites_lirvaison" maxlength="50" value="{$catalogue.delivery_conditions.impossibilites_lirvaison}" />
						</p>

						<h5>Personne à contacter</h5>
						<p>
							Nom *<br>
							<input type="text" name="contact_nom" maxlength="25" required value="{$catalogue.delivery_conditions.contact_nom}" />
						</p>
						<p>
							Prénom *<br>
							<input type="text" name="contact_prenom" maxlength="25" required value="{$catalogue.delivery_conditions.contact_prenom}" />
						</p>
						<p>
							Tél. portable *<br>
							<input type="text" name="contact_tel" maxlength="15" required value="{$catalogue.delivery_conditions.contact_tel}" />
						</p>
						<p>
							Autre ?<br>
							<input type="text" name="contact_autre" maxlength="50" value="{$catalogue.delivery_conditions.contact_autre}" />
						</p>

						<h5>Autres informations importantes ?</h5>
						<textarea id="commentaire" name="commentaire" cols="50" rows="4">{$catalogue.delivery_conditions.commentaire}</textarea>

						<p class="small">
							* Champs obligatoires
						</p>
					</div>
				</div>
			</div>
		{/if}

		{if !$catalogue.require_costing}
			<p class="txtcenter">
				<input class="checkbox" type="checkbox" id="cgv" name="cgv" value="1" />
				<label for="cgv" class="gras colorange"><strong>{$smarty.session.cste.I_ACCEPT_THE_GENERAL_TERMS_OF_SALE_URL|replace:'URL':'/cgv.html'}</strong></label>
			</p>

			<p class="txtcenter">
				<input type="button" class="btn" value="{$smarty.session.cste._DIMS_BACK}" onclick="javascript:document.location.href='/index.php?op={$catalogue.op}&etape=1';" />
				{if $catalogue.bouton_pause}
				<input class="btn" type="button" value="{$smarty.session.cste.PAUSE_MY_ORDER}" onclick="javascript:document.f_recap.etape.value='4.2';document.f_recap.submit();" />
				{/if}
				<input class="btn btn-primary" id="valid" type="submit" value="{$smarty.session.cste.CONFIRM_ORDER}" />
			</p>
		{else}
			<p class="txtcenter">
				<input type="button" class="btn" value="{$smarty.session.cste._DIMS_BACK}" onclick="javascript:document.location.href='/index.php?op={$catalogue.op}&etape=1';" />
				<input class="btn btn-primary" type="button" value="{$smarty.session.cste.ASK_FOR_SHIPPING_COST}" onclick="javascript:document.f_recap.etape.value='4.2';document.f_recap.ask_costing.value='1';document.f_recap.submit();" />
			</p>
		{/if}
	</form>

</div>

<script type="text/javascript">
	<!--//<![CDATA[

	{if sizeof($catalogue.modes_paiement)}
		var aRgtMessages = new Array;

		{foreach from=$catalogue.modes_paiement item=mp}
			{if !empty($mp.message)}
				aRgtMessages['{$mp.value}'] = "{$mp.message}";
			{/if}
		{/foreach}
	{/if}

	{if $editables_prices}
		/*
		 * Fonction d'édition des prix
		 */
		function editPrice(articleId, articlePrice) {
			var html = '<input type="text" id="price' + articleId + '" name="price' + articleId + '" value="' + articlePrice + '" class="txtright"> &euro;' +
				'<a href="javascript:void(0);" onclick="javascript:confirmEditPrice(\'' + articleId + '\');" title="Confirmer"><i class="icon2-checkmark orange pl1"></i></a>' +
				'<a href="javascript:void(0);" onclick="javascript:closeEditPrice(\'' + articleId + '\', \'' + articlePrice + '\');" title="Annuler"><i class="icon2-close orange pl1"></i></a>';
			$('#cell' + articleId).html(html);
		}

		function confirmEditPrice(articleId) {
			document.f_recap.etape.value = '3';
			document.f_recap.submit();
		}

		function closeEditPrice(articleId, articlePrice) {
			$('#cell' + articleId).html('<a href="javascript:void(0);" onclick="javascript:editPrice(\'' + articleId + '\', \'' + articlePrice + '\');"><i class="icon2-pencil orange pl1"></i></a> ' + articlePrice + ' &euro; HT');
		}
	{/if}

	{literal}
	function verifLength(field, maxLength) {
		var nbChars = maxLength - field.value.length;
		if (nbChars < 0) nbChars = 0;
		document.getElementById('nbChars').innerHTML = nbChars;

		if (field.value.length > maxLength) {
			document.getElementById('commentaire_erreur').style.display = 'block';
			field.style = 'background-color: #FFB4B4';
			field.value = field.value.substring(0, maxLength);
		}
	}

	function imposeMaxLength(Event, field, MaxLen) {
		return (field.value.length <= MaxLen)||(Event.keyCode == 8 ||Event.keyCode==46||(Event.keyCode>=35&&Event.keyCode<=40))
	}

	function valider_commande(form) {
		if (form_valid_modepaiement(form) && form_valid_cgv(form)) {
			document.getElementById('valid').disabled = true;
			document.f_recap.submit();
			return true;
		}
		return false;
	}
	function form_valid_cgv(form) {
		if (!form.cgv.checked) {
			alert('{/literal}{$smarty.session.cste.YOU_MUST_ACCEPT_THE_TERMS_AND_CONDITIONS_OF_SALE}{literal}');
		}
		return (form.cgv.checked);
	}
	function form_valid_modepaiement(form) {
		mode_paiement = $(form).find('.mode_paiement');
		mode_paiement_selected = $(form).find('.mode_paiement:checked');
		if(mode_paiement.length == 0 || mode_paiement_selected.length == 1) {
			return true;
		}
		alert('{/literal}{$smarty.session.cste.YOU_MUST_CHOOSE_A_PAYMENT_METHOD}{literal}');
		return false;
	}

	$('input[name=mode_paiement]').click(function() {
		var mp_sel = $('input[name=mode_paiement]:checked').val();
		if (aRgtMessages[mp_sel] != undefined) {
			$('#rgt_message').html(aRgtMessages[mp_sel]);
			$('#rgt_message').show();
		}
		else {
			$('#rgt_message').hide();
		}
	});

	$('.hayon_necessaire').click(function() {
		document.f_recap.etape.value = '3.5';

		$('#flashpopup').html('Veuillez patienter pendant la mise à jour des totaux');
		$('#flashpopup').fadeIn('fast', function(){
			timeFlashPopup = setTimeout(function() {
				$('#flashpopup').fadeOut('fast');
			}, 1500);
		});

		document.f_recap.submit();
	});

	//]]>-->
	{/literal}
</script>
