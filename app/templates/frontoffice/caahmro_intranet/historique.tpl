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
		<a href="/index.php?op=compte" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
			<i class="icon2-user3 title-icon"></i>
			<span>
				Mon compte
			</span>
		</a>
		<a href="/accueil.html" class="mod right fondbuttonnoir pa1 rounded-pic mr2">
			<i class="icon2-home title-icon"></i>
			<span>
				Accueil
			</span>
		</a>
	</div>
	<h3 class="titleh2_orange"><i class="orange icon2-calendar title-icon"></i>&nbsp;Historique de vos commandes</h3>
	{if isset($catalogue.commandes) && sizeof($catalogue.commandes)}
		<section id="no-more-tables">
			<table class="table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr class="bgblue txtwhite">
						<th class="txtcenter">Bon de Cde</th>
						<th class="txtcenter">Date</th>
						<th class="txtright">Montant HT</th>
						<th class="txtright">Montant TTC</th>
						<th class="txtright">Actions</th>
					</tr>
				</thead>
				{foreach from=$catalogue.commandes item=commande}
					<tr>
						<td class="txtcenter" data-title="Bon de Cde">{$commande.ID}</td>
						<td class="txtcenter" data-title="Date">{$commande.DATE}</td>
						<td class="txtright" data-title="Montant HT">{$commande.TOTAL_HT} € HT</td>
						<td class="txtright" data-title="Montant TTC">{$commande.TOTAL_TTC} € TTC</td>
						<td class="txtright" data-title="Actions">
							<a class="colblack detail_cde_btn" href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('/index.php', '{$commande.VIEWLINK}', '|', 'msgboxcontent');" title="Voir le détail"><i class="icon2-search orange title-icon"></i> Voir le détail</a><br/>
							<a class="colblack" href="javascript:void(0);" onclick="javascript:dims_openwin('/index.php?op=historique&action=print&id_cde={$commande.ID}', 800, 500);" title="Imprimer"><i class="icon2-print orange title-icon"></i> Imprimer</a><br/>
							<a class="colblack" href="javascript:void(0);" onclick="javascript:dims_confirmlink('{$commande.RENEWLINK}', 'Attention ! Vous êtes sur le point d\'écraser votre panier.\nEtes-vous sûr(e) de vouloir faire cela ?');" title="Renouveler la commande"><i class="icon2-loop2 title-icon orange"></i> Renouveler la commande</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</section>

		<script type="text/javascript">
		{literal}
		<!--//<![CDATA[
		jQuery(document).ready(function ($) {
			$('.detail_cde_btn').click(function(){
				$('#msgboxcontent').empty();
				$('#msgbox').css({ 'width': '800px', 'left': ($(window).width() - 800) / 2 });
				$('#overlay').fadeIn('fast', function(){
					$('#msgbox').animate({'top': '100px'}, 200);
				});
			});
		});
		//]]>-->
		{/literal}
		</script>
	{else}
		<div style="padding:10px;">Il n'y a aucune commande.</div>
	{/if}
</div>
