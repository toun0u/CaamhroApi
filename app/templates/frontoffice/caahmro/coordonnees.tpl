<div class="content-zone">
	{include file="_mobile_menu.tpl"}
	<h3 class="titleh2_orange"><i class="orange icon2-cart title-icon"></i>&nbsp;{$smarty.session.cste.CATA_YOUR_ORDER}</h1>
	<hr class="mt0">

	{if $missingaddr}
		<div class="flash info ma1">Vous devez sélectionner une adresse.</div>
	{/if}
	{if $missingemail}
		<div class="flash info ma1">Vous devez renseigner une adresse email.</div>
	{/if}

	<form name="f_coordonnees" action="/index.php" method="post" class="pa2">
		<input type="hidden" name="op" value="{$catalogue.op}" />
		<input type="hidden" name="etape" value="2" />
		{if isset($catalogue.id_cmd)}
		<input type="hidden" name="id_cmd" value="{$catalogue.id_cmd}" />
		{/if}

		<div class="grid2 line pa2">
			<div class="mod borderright">
				<h2>Adresse email de contact</h2>

				<p>Une adresse email est obligatoire pour pouvoir passer commande. Elle ne servira qu'à vous faire parvenir vos devis et confirmations de commande.</p>
				<p>Votre adresse email : <input class="text" type="text" name="user_email" value="{$catalogue.client.EMAIL}" maxlength="255" /></p>


				<h2>{$smarty.session.cste._BILLING_ADDRESS}</h2>

				<p class="nomargintop"><em>{$smarty.session.cste.CATA_INFOS_MODIFIABLES_DANS_ESPACE_PERSO}</em></p>

				<p class="nopadding"><strong>{$smarty.session.cste._COMPANY_CT} / {$smarty.session.cste._DIMS_LABEL_LASTNAME} {$smarty.session.cste._DIMS_LABEL_FIRSTNAME} :</strong> {$catalogue.client.NOM}</p>
				<p class="nopadding">
					<strong>{$smarty.session.cste._DIMS_LABEL_ADDRESS} :</strong><br>
					{$catalogue.client.ADR1}
					{if ($catalogue.client.ADR2 != '')}
						<br>{$catalogue.client.ADR2}
					{/if}
					{if ($catalogue.client.ADR3 != '')}
						<br>{$catalogue.client.ADR3}
					{/if}
					<br>{$catalogue.client.CP} {$catalogue.client.VILLE}
					<br>{$catalogue.client.PAYS}
				</p>
			</div>
			<div class="mod pl1">
				{if $retrait_magasin}
				<h2>Drive (enlèvement sur site au service logistique)</h2>

				<p class="nopadding">
					<input type="radio" id="magasin" name="numdepot" value="-1">
					<label for="magasin">
						Si la commande est validée avant 13h, vous pouvez demander un enlèvement<br>à partir du lendemain matin 8h00, sinon, un jour plus tard ;<br><br>
						Merci de sélectionner dans le calendrier la date souhaitée pour l'enlèvement<br>dans les 2 semaines à venir et d'indiquer dans le champ ci-dessous<br>la plage horaire d’enlèvement souhaitée
					</label>
				</p>
				<p class="nopadding">
					{assign var=curH value=$smarty.now|date_format:'%H'}
					{assign var=nexD value="+1 days"}
					{assign var=nexD2 value="+2 days"}
					{if $smarty.now|date_format:'%u' == 5}
						{assign var=nexD value="+3 days"}
						{assign var=nexD2 value="+4 days"}
					{elseif $smarty.now|date_format:'%u' == 6}
						{assign var=nexD value="+2 days"}
					{/if}

					Sélectionnez une date de récupération
					<input type="text" name="date_enlevement" id="date_enlevement" value="{if $curH > 13}{strftime($nexD2)|date_format:'%d/%m/%Y'}{else}{strftime($nexD)|date_format:'%d/%m/%Y'}{/if}" /><br />
					Sélectionnez un horaire de récupération
					<select name="heure_enlevement" id="heure_enlevement">
						<option value="8h00 - 8h30">8h00 - 8h30</option>
						{for $h=8 to 16}
							{if $h < 11 or $h > 12}
								<option value="{$h}h30 - {$h+1}h00">{$h}h30 - {$h+1}h00</option>
								<option value="{$h+1}h00 - {$h+1}h30">{$h+1}h00 - {$h+1}h30</option>
							{elseif $h == 11}
								<option value="{$h+1}h30 - {$h+1}h00">{$h}h30 - {$h+1}h00</option>
							{/if}
						{/for}
					</select>
				</p>
				{/if}

				<h2>Livraison</h2>

				<p class="nopadding">
					Votre commande sera expédiée de nos locaux dans un délai de 24 à 48 h, merci de sélectionner dans le calendrier la date souhaitée pour l’expédition dans les 2 semaines à venir
				</p>

				{foreach from=$catalogue.client.depots item=depot}
					<p class="nopadding">
						<input type="radio" id="numdepot{$depot.NUMDEPOT}" name="numdepot" value="{$depot.NUMDEPOT}" />
						<label for="numdepot{$depot.NUMDEPOT}">
							{if !empty($depot.NOM)}<strong>{$depot.NOM}</strong>&nbsp;<a href="/index.php?op=depot_edit&id={$depot.ID}&return={base64_encode("/index.php?op=valider_panier")}"><i class="icon2-pencil"></i></a><br/>{/if}
							{$depot.ADR}{if empty($depot.NOM)}&nbsp;<a href="/index.php?op=depot_edit&id={$depot.ID}&return={base64_encode("/index.php?op=valider_panier")}"><i class="icon2-pencil"></i></a>{/if}<br/>
							{$depot.CP} {$depot.VILLE}<br/>
							{$depot.PAYS}
						</label>
					</p>
				{/foreach}

				Sélectionnez une date de livraison
				<input type="text" name="date_livraison" id="date_livraison" value="{if $curH > 13}{strftime($nexD2)|date_format:'%d/%m/%Y'}{else}{strftime($nexD)|date_format:'%d/%m/%Y'}{/if}" /><br />

				<br /><a href="/index.php?op=depot_edit&return={base64_encode("/index.php?op=valider_panier")}"><i class="icon2-plus"></i>&nbsp;{$smarty.session.cste._ADD_DELIVERY_ADDRESS}</a>
			</div>
		</div>
		<div class="txtright">
			{if isset($catalogue.id_cmd)}
				<input type="button" class="btn" value="{$smarty.session.cste._DIMS_BACK}" onclick="javascript:document.location.href='/index.php?op=commandes';">
			{else}
				<input type="button" class="btn" value="{$smarty.session.cste._DIMS_BACK}" onclick="javascript:document.location.href='/index.php?op=panier';">
			{/if}
			<input type="submit" class="btn btn-primary" value="{$smarty.session.cste.SUBMIT_THESE_INFORMATIONS}">
		</div>
	</form>

</div>

<script type="text/javascript">
$(document).ready(function() {
	// $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );

	$('#date_enlevement').datepicker({
		minDate: $('#date_enlevement').val(),
		beforeShowDay: function(date) {
			// pas le samedi et le dimanche
			var day = date.getDay();
			return [(day != 0 && day != 6), ''];
		}
	});

	$('#date_livraison').datepicker({
		minDate: $('#date_livraison').val(),
		beforeShowDay: function(date) {
			// pas le samedi et le dimanche
			var day = date.getDay();
			return [(day != 0 && day != 6), ''];
		}
	});
});
</script>
