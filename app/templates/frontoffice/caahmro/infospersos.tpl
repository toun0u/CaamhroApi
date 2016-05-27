<script src="/assets/javascripts/common/dims_validForm.js" type="text/javascript"></script>
<form name="form" id="form_info_perso" action="/index.php" method="post">
<input type="hidden" name="op" value="save_infospersos">

<div class="content-zone">
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
	<div class="pa1">
		<h3 class="titleh2_orange"><i class="orange icon2-address-book title-icon"></i>&nbsp;Informations personnelles</h3>
		<div class="global_message error_message flash error" {if empty($error)}style="display: none;"{/if}>{$error}</div>
		<div class="group-form">
			<label class="form-label" for="fact_nom">&nbsp;Nom :&nbsp;</label>
			<input class="WebText" id="fact_nom" type="text" name="user_lastname" value="{$infos.user.lastname}" rel="requis" />
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_prenom">&nbsp;Prénom :&nbsp;</label>
			<input class="WebText" id="fact_prenom" type="text" name="user_firstname" value="{$infos.user.firstname}" rel="requis" />
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_email">&nbsp;Email :&nbsp;</label>
			<input class="WebText" id="fact_email" type="text" name="user_email" value="{$infos.user.email}" rel="requis" rev="email" />
		</div>
		<div class="group-form">
			<label class="form-label" for="password">&nbsp;Mot de passe :&nbsp;</label>
			<input class="WebText" type="password" id="password" name="password" value="" maxlength="100" autocomplete="off" rev="dims_pwd" />
		</div>
		<div class="group-form">
			<label class="form-label" for="passconf">&nbsp;Confirmation :&nbsp;</label>
			<input class="WebText" type="password" id="passconf" name="passconf" value="" maxlength="100" autocomplete="off" rev="dims_pwd_confirm" />
		</div>
		<hr class="mt0">

		{if $infos_persos_editables}
			<h3 class="titleh2_orange"><i class="orange icon2-location title-icon"></i>&nbsp;Adresse de facturation</h3>
			<div class="group-form">
				<label class="form-label" for="client_nom">&nbsp;Nom :&nbsp;</label>
				<input class="WebText" type="text" id="client_nom" name="client_nom" value="{$infos.client.name}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="client_adr1">&nbsp;Adresse :&nbsp;</label>
				<input class="WebText" type="text" id="client_adr1" name="client_adr1" value="{$infos.client.adr1}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="client_cp">&nbsp;Code postal :&nbsp;</label>
				<input class="WebText" type="text" id="client_cp" name="client_cp" value="{$infos.client.cp}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="client_ville">&nbsp;Ville :&nbsp;</label>
				<input class="WebText" type="text" id="client_ville" name="client_ville" value="{$infos.client.city}" maxlength="100" {if !$infos_persos_editables} disabled="disabled"{/if} />
			</div>
			<div class="group-form">
				<label class="form-label" for="fact_nom">&nbsp;Pays :&nbsp;</label>
				<select id="fact_pays" name="client_id_pays" {if !$infos_persos_editables} disabled="disabled"{/if}>
				<option value="0">{$smarty.session.cste.SELECT_A_COUNTRY}</option>
				{foreach from=$a_countries item=country}
					<option value="{$country->fields.id}"{if $country->fields.id == $infos.client.id_country} selected="selected"{/if}>{$country->fields.printable_name}</option>
				{/foreach}
			</select>
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
						<strong>{$depot.nomlivr}</strong><br>
						{$depot.adr1}<br>
						{if $depot.adr2 != ''}{$depot.adr2}<br>{/if}
						{$depot.cp} {$depot.ville}
						<br>
						{if $infos_persos_editables}
						<a class="btn" style="color: #7F6C5A;" href="/index.php?op=depot_edit&id={$depot.id}">Modifier</a> /
						<a class="btn btn-error" style="color: #7F6C5A;" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/index.php?op=depot_delete&id={$depot.id}', 'Etes-vous sûr(e) de vouloir supprimer cette adresse de livraison ?');">Supprimer</a>
						{/if}
					</p>
				</div>
				{/foreach}
		</div>
	{/if}

	<br>
	<div class="pa1 txtcenter">
		<div class="btn nounderline">{$infos.buttons.btn_back}</div>
		<div class="btn btn-primary nounderline"><font class="txtwhite">{$infos.buttons.btn_save}</font></div>
	</div>
</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$("#form_info_perso").dims_validForm({
		displayMessages: true,
		refId: 'def',
		globalId: 'global_message',
	});
});
</script>
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
