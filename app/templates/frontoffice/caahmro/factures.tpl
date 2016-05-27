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
	<h3 class="titleh2_orange"><i class="orange icon2-file-pdf title-icon"></i>&nbsp;{$title}</h3>
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
