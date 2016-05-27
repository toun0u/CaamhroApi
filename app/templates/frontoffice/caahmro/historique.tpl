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
	<h3 class="titleh2_orange"><i class="orange icon2-calendar title-icon"></i>&nbsp;Historique de vos commandes</h3>
	{if isset($catalogue.commandes) && sizeof($catalogue.commandes)}
		<section id="no-more-tables">
			<table class="table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr class="bgblue txtwhite">
						<th class="txtcenter">Bon de Cde</th>
						<th class="txtcenter">Référence</th>
						<th class="txtcenter">Date</th>
						<th class="txtright">Montant HT</th>
						<th class="txtright">Montant TTC</th>
						<th class="txtright">Actions</th>
					</tr>
				</thead>
				{foreach from=$catalogue.commandes item=commande}
					<tr>
						<td class="txtcenter" data-title="Bon de Cde">{$commande.ID}</td>
						<td class="txtcenter" data-title="Référence">{$commande.LIBELLE}</td>
						<td class="txtcenter" data-title="Date">{$commande.DATE}</td>
						<td class="txtright" data-title="Montant HT">{$commande.TOTAL_HT} € HT</td>
						<td class="txtright" data-title="Montant TTC">{$commande.TOTAL_TTC} € TTC</td>
						<td class="txtright" data-title="Actions">
							<a class="colblack nounderline detail_cde_btn" href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('/index.php', '{$commande.VIEWLINK}', '|', 'msgboxcontent');" title="Voir le détail"><i class="icon2-search orange title-icon"></i> Voir le détail</a><br/>
							<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:dims_openwin('/index.php?op=historique&action=print&id_cde={$commande.ID}', 800, 500);" title="Imprimer"><i class="icon2-print orange title-icon"></i> Imprimer</a><br/>
							<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:dims_confirmlink('{$commande.RENEWLINK}', 'Attention ! Vous êtes sur le point d\'écraser votre panier.\nEtes-vous sûr(e) de vouloir faire cela ?');" title="Renouveler la commande"><i class="icon2-loop2 title-icon orange"></i> Renouveler la commande</a>
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
