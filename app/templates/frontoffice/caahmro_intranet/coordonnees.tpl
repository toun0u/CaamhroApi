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

	<form name="f_coordonnees" action="/index.php" method="post" class="pa2">
		<input type="hidden" name="op" value="{$catalogue.op}" />
		<input type="hidden" name="etape" value="2" />
		{if isset($catalogue.id_cmd)}
		<input type="hidden" name="id_cmd" value="{$catalogue.id_cmd}" />
		{/if}
		<div class="grid2 line pa2">
			<div class="mod borderright">
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
			<div class="mod">
				<h2>{$smarty.session.cste.DELIVERY_ADDRESS}</h2>

				{foreach from=$catalogue.client.depots item=depot}
					<p class="nopadding">
						<input type="radio" id="numdepot{$depot.NUMDEPOT}" name="numdepot" value="{$depot.NUMDEPOT}" {$depot.CHECKED}>
						<label for="numdepot{$depot.NUMDEPOT}">
							<strong>{$depot.NOM}</strong><br/>
							{$depot.ADR}<br/>
							{$depot.CP} {$depot.VILLE}<br/>
							{$depot.PAYS}
						</label>
					</p>
				{/foreach}
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
