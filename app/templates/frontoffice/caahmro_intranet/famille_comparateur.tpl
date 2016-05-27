<!-- Partie dynamique : -->
<div class="content-zone">

	<div class="pa1">
		<div class="nopbottom desk-hidden" style="overflow:hidden">
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
		</div>

		<h1 class="txtcenter pa1">
			Quel gamme vous faut-il ? <br>
			<small>Quel que soient vos besoins vous trouverez le boxe qu'il vous faut</small>
		</h1>

		{if isset($fam1.famille2)}
			<div class="quick-familly-head">

				{foreach from=$fam1.famille2 item=fam2}
					<a href="{$fam2.LINK}" title="{$fam2.LABEL}" {if $fam2.SEL == 'selected'}class="active"{/if}>{$fam2.LABEL}</a>
				{/foreach}

			</div>
		{/if}
	</div>

	<hr class="mb2 mt1">

	<div class="pa1">
		<table class="alternate-light">
			<tr>
				<th class="w20"></th>
				{foreach from=$families item=family}
					<th class="txtcenter">
						{if $family.photo != ''}
							<img src="{$family.photo}" alt="{$family.label}" class="rounded-pic w300p">
						{/if}
						<div class="pa1">
							<span class="ultima biggest">{$family.label}</span>
							<br>
							<a href="" class="">Plus d'informations</a>
						</div>
					</th>
				{/foreach}
			</tr>
			{foreach from=$fields item=field}
				<tr>
					<td><strong>{$field.label}</strong></td>
					{foreach from=$families item=family}
						{assign var=id_family value=$family.id}
						<td class="txtcenter pa1">{$field.values.$id_family}</td>
					{/foreach}
				</tr>
			{/foreach}
			<tr>
				<td></td>
				{foreach from=$families item=family}
					<td class="txtcenter pa2"><a href="{$webasklink}&o={$family.id}" class="btn btn-primary">Demander un devis</a></td>
				{/foreach}
			</tr>
		</table>
	</div>
</div>
<!-- Fin de la partie dynamique -->
