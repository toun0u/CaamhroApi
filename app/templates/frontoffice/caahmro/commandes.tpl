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

	<h3 class="titleh2_orange">
		<i class="orange icon2-signup title-icon"></i>
		{$title}
	</h3>

	{if (isset($catalogue.etats) && sizeof($catalogue.etats))}
		<section id="no-more-tables">
			<table class="table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr class="bgblue txtwhite">
						<th class="txtcenter">Statut</th>
						<th class="txtcenter">Bon de Cde</th>
						<th class="txtcenter">Référence</th>
						<th class="txtcenter">Date</th>
						<th class="txtright">Montant HT</th>
						<th class="txtright">Montant TTC</th>
						<th class="txtright">Actions</th>
					</tr>
				</thead>
				{foreach from=$catalogue.etats item=etat}
					{foreach from=$etat.group item=group}
						{foreach from=$group.commandes item=commande}
							<tr>
								<td class="txtcenter" data-title="Statut">{if $commande.VALIDABLE}En attente de validation{else}En attente de chiffrage{/if}</td>
								<td class="txtcenter" data-title="Bon de Cde">{$commande.ID}</td>
								<td class="txtcenter" data-title="Référence">{$commande.LIBELLE}</td>
								<td class="txtcenter" data-title="Date">{$commande.DATE}</td>
								<td class="txtright" data-title="Montant HT">{$commande.TOTAL_HT} € HT</td>
								<td class="txtright" data-title="Montant TTC">{$commande.TOTAL_TTC} € TTC</td>
								<td class="txtright" data-title="Actions">
									<a class="colblack nounderline detail_cde_btn" href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('/index.php', '{$commande.VIEWLINK}', '|', 'msgboxcontent');" title="Voir le détail"><i class="icon2-search orange title-icon"></i> Voir le détail</a><br/>
									<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:dims_openwin('/index.php?op=historique&action=print&id_cde={$commande.ID}', 800, 500);" title="Imprimer"><i class="icon2-print orange title-icon"></i> Imprimer</a><br/>
									{if $commande.VALIDABLE}
										{if $commande.REQUIRE_COSTING}
											<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:document.location.href='/index.php?op=reprendre_commande&id_cmd={$commande.ID}';" title="Valider ce panier"><i class="icon2-checkmark orange title-icon"></i> Valider ce panier</a><br/>
										{else}
											<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:document.location.href='/index.php?op=valider_commande&etape=3&id_cmd={$commande.ID}';" title="Valider ce panier"><i class="icon2-checkmark orange title-icon"></i> Valider ce panier</a><br/>
										{/if}
									{else}
										<i class="icon2-checkmark title-icon" title="{$commande.STATE_LABEL}"></i> Valider ce panier<br/>
									{/if}
									{if $commande.MODIFIABLE}
										{if $panier.nb_art == 0}
											<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:document.location.href='/index.php?op=reprendre_commande&id_cmd={$commande.ID}';" title="Modifier ce panier"><i class="icon2-pencil orange title-icon"></i> Modifier ce panier</a><br/>
										{else}
											<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/index.php?op=reprendre_commande&id_cmd={$commande.ID}', 'Attention !\nVous allez perdre le contenu de votre panier en cours.\nÊtes-vous sûr(e) de vouloir faire cela ?\n\nSinon, retournez dans votre panier en cours\net cliquez sur « Mettre mon panier en attente »');" title="Modifier ce panier"><i class="icon2-pencil orange title-icon"></i> Modifier ce panier</a><br/>
										{/if}
										<a class="colblack nounderline" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/index.php?op=supprimer_commande&id_cmd={$commande.ID}', 'Êtes-vous sûr(e) de vouloir supprimer ce panier ?');" title="Supprimer ce panier"><i class="icon2-close orange title-icon"></i> Supprimer ce panier</a><br/>
									{else}
										<i class="icon2-pencil title-icon" title="{$commande.STATE_LABEL}"></i> Modifier ce panier<br/>
										<i class="icon2-close title-icon" title="{$commande.STATE_LABEL}"></i> Supprimer ce panier<br/>
									{/if}
								</td>
							</tr>
						{/foreach}
					{/foreach}
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
		<div style="padding:10px;">Il n'y a aucun panier.</div>
	{/if}
</div>
