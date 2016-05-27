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
	</div>
	<h3 class="titleh2_orange"><i class="orange icon2-file-pdf title-icon"></i>&nbsp;Historique de vos factures</h3>
	{if isset($catalogue.documents) && sizeof($catalogue.documents)}
		<section id="no-more-tables">
			<table class="table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr class="bgblue txtwhite">
						<th>Numéro</th>
						<th class="txtcenter">Date</th>
						<th class="txtright">Montant HT</th>
						<th class="txtright">Montant TTC</th>
						<th class="txtright">&nbsp;</th>
					</tr>
				</thead>
				{foreach from=$catalogue.documents item=document}
					<tr>
						<td data-title="Bon de Cde">{$document.NUM}</td>
						<td class="txtcenter" data-title="Date">{$document.DATE}</td>
						<td class="txtright" data-title="Montant HT">{$document.TOTAL_HT} € HT</td>
						<td class="txtright" data-title="Montant TTC">{$document.TOTAL_TTC} € TTC</td>
						<td class="txtright" data-title="Imprimer">
							<a class="colblack" href="{$document.PRINTLINK}" title="Imprimer"><i class="icon2-print orange title-icon"></i> Imprimer</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</section>
	{else}
		<div style="padding:10px;">Il n'y a aucun document.</div>
	{/if}
</div>
