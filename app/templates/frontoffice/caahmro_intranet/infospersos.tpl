<form name="form" action="/index.php" method="post">
<input type="hidden" name="op" value="save_infospersos">

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
			{$smarty.session.cste.CATA_YOUR_CART}
		</h1>
	</div>
	<div class="pa1">
		<h3 class="titleh2_orange"><i class="orange icon2-address-book title-icon"></i>&nbsp;Informations personnelles</h3>
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Nom :&nbsp;</label>
			<input class="WebText" id="fact_nom" type="text" name="user_lastname" value="{$infos.user.lastname}" />
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Prénom :&nbsp;</label>
			<input class="WebText" id="fact_prenom" type="text" name="user_firstname" value="{$infos.user.firstname}" />
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Email :&nbsp;</label>
			<input class="WebText" id="fact_email" type="text" name="user_lastname" value="{$infos.user.lastname}" />
		</div>
		{if isset($error)}
			<div>&nbsp;<font class="error">{$error}</font>&nbsp;</div>
		{/if}	
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Mot de passe :&nbsp;</label>
			<input class="WebText" type="password" name="password" value="" maxlength="100" autocomplete="off" />
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Confirmation :&nbsp;</label>
			<input class="WebText" type="password" name="passconf" value="" maxlength="100" autocomplete="off" />
		</div>
		<hr class="mt0">

		{if $infos_persos_editables}
			<h3 class="titleh2_orange"><i class="orange icon2-location title-icon"></i>&nbsp;Adresse de facturation</h3>
			<div class="group-form">
				<label class="form-label" for="fact_nom">&nbsp;Nom :&nbsp;</label>
				<input class="WebText" type="text" id="fact_nom" name="client_nom" value="{$infos.client.name}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>	
			<div class="group-form">
				<label class="form-label" for="fact_nom">&nbsp;Adresse :&nbsp;</label>
				<input class="WebText" type="text" id="liv_adresse" name="client_adr1" value="{$infos.client.adr1}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="fact_nom">&nbsp;Code postal :&nbsp;</label>
				<input class="WebText" type="text" id="fact_cp" name="client_cp" value="{$infos.client.cp}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="fact_nom">&nbsp;Ville :&nbsp;</label>
				<input class="WebText" type="text" id="fact_ville" name="client_ville" value="{$infos.client.city}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>				
		{/if}			
	</div>
	<hr class="mt0">
	{if $infos_persos_editables}
		<div class="pa1">
			<h3 class="titleh2_orange"><i class="orange icon2-location title-icon"></i>&nbsp;Adresses de livraison</h3>
				{if $infos_persos_editables}
				<a class="orange" href="/index.php?op=depot_edit">
					<i class="orange icon2-plus title-icon"></i>
					Ajouter une adresse de livraison
				</a>
				<div style="clear: both;"></div>
				{/if}

				{foreach from=$infos.depots item=depot}
				<div class="liv_adresse">
					<p>
						<strong>{$depot.nomlivr}</strong><br/>
						{if $infos_persos_editables}
						<a class="btn" style="color: #7F6C5A;" href="/index.php?op=depot_edit&id={$depot.id}">Modifier</a> /
						<a class="btn btn-error" style="color: #7F6C5A;" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/index.php?op=depot_delete&id={$depot.id}', 'Etes-vous sûr(e) de vouloir supprimer cette adresse de livraison ?');">Supprimer</a>
						{/if}
					</p>
					<p>
						{$depot.adr1}<br/>
						{$depot.adr2}<br/>
						{$depot.cp} {$depot.ville}
					</p>
				</div>
				{/foreach}
		</div>
	{/if}

	<br/>
	<div class="pa1 txtcenter">
		<div class="btn">{$infos.buttons.btn_back}</div>
		<div class="btn btn-primary"><font class="txtwhite">{$infos.buttons.btn_save}</font></div>
	</div>
</div>
</form>

<!--div id="raccourci">
	<table width="100%" cellpadding="20" cellspacing="0">
	<tr>
		<td id="espace_raccourci">
			<a href="/index.php?op=saisierapide"><i class="orange enormous icon2-stopwatch title-icon"></i>&nbsp;Saisie rapide</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=panier"><i class="orange enormous icon2-cart title-icon"></i>&nbsp;Panier</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=panierstype"><i class="orange enormous icon2-heart title-icon"></i>&nbsp;Paniers types</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=commandes"><i class="orange enormous icon2-signup title-icon"></i>&nbsp;Commandes en cours</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=historique"><i class="orange enormous icon2-calendar title-icon"></i>&nbsp;Historique</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=factures"><i class="orange enormous icon2-file-pdf title-icon"></i>&nbsp;Factures</a>
		</td>
		<td id="espace_raccourci">
			<a href="/index.php?op=promotions"><i class="orange enormous icon2-coin title-icon"></i>&nbsp;Promotions</a>
		</td>
	</tr>
	</table>
</div-->
