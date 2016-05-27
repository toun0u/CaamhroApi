<div class="content-zone">
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

		<h1 class="txtcenter line">
			{$smarty.session.cste.CATA_YOUR_ORDER}
		</h1>
	</div>

	<hr class="mt0">

	<form name="f_recap" action="/index.php" method="post" onsubmit="javascript:return valider_commande(this);" class="mt3 m-auto pa1">
		<input type="hidden" name="op" value="{$catalogue.op}" />
		<input type="hidden" name="etape" value="4.1" />
		<section id="no-more-tables">
			<table class="table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr class="bgblue txtwhite">
						<th class="txtleft w10">{$smarty.session.cste.REFERENCE}</th>
						<th class="txtleft">{$smarty.session.cste._DESIGNATION}</th>
						<th class="txtcenter w15">{$smarty.session.cste.QUANTITY}</th>
						<th class="txtright w15">{$smarty.session.cste.CATA_UNIT_PRICE_HT}</th>
						<th class="txtright w15">{$smarty.session.cste.CATA_SUM_HT}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$catalogue.articles item=article}
						<tr class="{$article.class}">
							<td data-title="{$smarty.session.cste.REFERENCE}">{$article.reference}</td>
							<td data-title="{$smarty.session.cste._DESIGNATION}"><strong>{$article.designation}</strong></td>
							<td data-title="{$smarty.session.cste.QUANTITY}" class="txtcenter">{$article.qte_cde}</td>
							<td data-title="{$smarty.session.cste.CATA_UNIT_PRICE_HT}" class="txtright">{$article.pu_net_ht} € HT</td>
							<td data-title="{$smarty.session.cste.CATA_SUM_HT}" class="txtright">{$article.total_ligne} € HT</td>
						</tr>
					{/foreach}
				</tbody>
				<tfoot>
					<tr class="bgwhite">
						<td colspan="3">&nbsp;</td>
						<td class="txtright">{$smarty.session.cste.CATA_SS_TOTAL_HT}</td>
						<td class="txtright">{$catalogue.commande.ss_total_ht} € HT</td>
					</tr>
					<tr class="bgwhite">
						<td colspan="3">&nbsp;</td>
						<td class="txtright">{$smarty.session.cste.CATA_FRAIS_PORT_HT}</td>
						<td class="txtright">{$catalogue.commande.mt_port_ht} € HT</td>
					</tr>
					<tr class="bgwhite">
						<td colspan="3">&nbsp;</td>
						<td class="txtright">{$smarty.session.cste._TOTAL_DUTY_FREE_AMOUNT}</td>
						<td class="txtright">{$catalogue.commande.total_ht} € HT</td>
					</tr>
					<tr class="bgwhite">
						<td colspan="3">&nbsp;</td>
						<td class="txtright">{$smarty.session.cste.CATA_TOTAL_TVA}</td>
						<td class="txtright">{$catalogue.commande.total_tva} €</td>
					</tr>
					<tr class="bgwhite">
						<td colspan="3">&nbsp;</td>
						<td class="txtright"><strong style="font-size: 16px;">{$smarty.session.cste.CATA_TOTAL_TTC}</strong></td>
						<td class="txtright"><strong style="font-size: 16px;">{$catalogue.commande.total_ttc} € TTC</strong></td>
					</tr>
				</tfoot>
			</table>
		</section>

		<div class="line pa1">
			<div class="mod content-zone">
				<h4 class="bgblue txtwhite pa1">{$smarty.session.cste.SELECT_YOUR_PAYMENT_METHOD}</h4>
				{if sizeof($catalogue.modes_paiement)}
				<div class="grid2">
					<div class="mod">
						<p class="txtcenter"><img src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/img_paiement.jpg" alt="Paiement"></p>

					</div>
					<div class="mod">
						<p class="">
							{foreach from=$catalogue.modes_paiement item=mp}
								<li><input type="radio" id="mode_paiement_{$mp.value}" name="mode_paiement" class="mode_paiement" value="{$mp.value}"{if $mp.checked} checked="checked"{/if}>
								<label for="mode_paiement_{$mp.value}" class="big">{$mp.label}</label></li>
							{/foreach}
						</p>
						<p id="rgt_message" class="notice" style="display: none;"></p>
					</div>
				</div>
				{else}
					<p class="error">{$smarty.session.cste.YOU_DO_NOT_HAVE_PAYMENT_METHOD_AVAILABLE_CONTACT_YOU_BUSINESS_SUPPORT}</p>
				{/if}
			</div>
		</div>
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
		<div class="grid2 pa1 overflowH">
			<div class="mod">
				{$smarty.session.cste.TELL_US_HERE_YOUR_SPECIFIC_DELIVERY_INFORMATION}
			</div>
			<div class="mod">
				<textarea id="commentaire" class="nomargin w100" name="commentaire" cols="50" rows="4" onkeydown="javascript:verifLength(this, 210);">{$catalogue.commande.commentaire}</textarea>
				<p>{$smarty.session.cste.210_CHARACTERS_MAX} : <span id="nbChars">210</span> {$smarty.session.cste.CHARACTERS_LEFT}</p>
			</div>
		</div>

		<p class="txtcenter">
			<input class="checkbox" type="checkbox" id="cgv" name="cgv" value="1" />
			<label for="cgv" class="gras colorange"><strong>{$smarty.session.cste.I_ACCEPT_THE_GENERAL_TERMS_OF_SALE_URL|replace:'URL':'/cgv.html'}</strong></label>
		</p>

		<p class="txtcenter">
			<input type="button" class="btn" value="{$smarty.session.cste._DIMS_BACK}" onclick="javascript:document.location.href='/index.php?op={$catalogue.op}&etape=1';"></input>
			{if $catalogue.bouton_pause}
			<input class="btn" type="button" value="{$smarty.session.cste.PAUSE_MY_ORDER}" onclick="javascript:document.f_recap.etape.value='4.2';document.f_recap.submit();" />
			{/if}
			<input class="btn btn-primary" id="valid" type="submit" value="{$smarty.session.cste.CONFIRM_ORDER}" />
		</p>
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
	//]]>-->
	{/literal}
</script>
