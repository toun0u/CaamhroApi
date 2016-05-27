<form name="form" action="/index.php" method="post">
<input type="hidden" name="op" value="depot_save">
<input type="hidden" name="id" value="{$depot.id}">

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
		<h3 class="titleh2_orange"><i class="orange icon2-location title-icon"></i>&nbsp;Adresse de livraison</h3>
		<div class="group-form">
			<label class="form-label" for="fact_nom">{$smarty.session.cste._DIMS_LABEL_NAME} :</label>
			<input class="WebText" id="fact_nom" type="text" name="user_lastname" value="{$infos.user.lastname}" />
		</div>
		<div class="group-form">
			<label class="form-label" for="liv_pays">{$smarty.session.cste._DIMS_LABEL_COUNTRY} :</label>
			<select id="liv_pays" name="liv_pays">
				<option value="0">{$smarty.session.cste.SELECT_A_COUNTRY}</option>
				{foreach from=$a_countries item=country}
					<option value="{$country->fields.id}"{if isset($inscription.values.liv_pays) && $inscription.values.liv_pays == $country->fields.id} selected="selected"{/if}>{$country->fields.printable_name}</option>
				{/foreach}
			</select>
		</div>
		<div class="group-form">
			<label class="form-label" for="liv_adresse">{$smarty.session.cste._DIMS_LABEL_ADDRESS} :</label>
			<textarea id="liv_adresse" name="liv_adresse" rows="3">{if isset($inscription.values.liv_adresse)}{$inscription.values.liv_adresse}{/if}</textarea>
		</div>
		<div class="group-form">
			<label class="form-label" for="liv_cp">{$smarty.session.cste._DIMS_LABEL_CP} *</label>
			<input type="text" id="liv_cp" name="liv_cp" value="{if isset($inscription.values.liv_cp)}{$inscription.values.liv_cp}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="liv_ville">{$smarty.session.cste._DIMS_LABEL_CITY} *</label>
			<input type="text" id="liv_ville" name="liv_ville" value="{if isset($inscription.values.liv_ville)}{$inscription.values.liv_ville}{/if}">
		</div>
		<br/>
		<div class="txtcenter pa1">
			<div class="btn center">{$buttons.btn_back}</div>
			<div class="btn btn-primary center txtwhite">{$buttons.btn_save}</div>
		</div>
	</div>
</div>
</form>

<script type="text/javascript">
	<!--//<![CDATA[
	// Pays sur lesquels il faut le code postal (so colissimo)
	var countries = new Array();
	{foreach from=$a_pays_so_colissimo key=code item=libelle}
	countries[{$code}] = '{$libelle}';
	{/foreach}

	{literal}
	// Affichage ou non du code postal
	$('#depot_id_pays').change(function() {
		if (countries[$('#depot_id_pays option:selected').val()] != undefined) {
			$('#ligne_cp').show();
			$('#cp_ville_label').html('{/literal}{$smarty.session.cste._DIMS_LABEL_CITY} :{literal}');
		}
		else {
			$('#ligne_cp').hide();
			$('#cp_ville_label').html('{/literal}{$smarty.session.cste.POSTAL_CODE_AND_CITY} :{literal}');
		}
	});

	function validForm() {
		if (countries[$('#depot_id_pays option:selected').val()] != undefined) {
			if ($('#depot_cp').val() == '') {
				alert("{/literal}{$smarty.session.cste.THE_DELIVERY_ZIP_CODE_IS_NOT_SPECIFIED}{literal}");
				$('#depot_cp').focus();
				return false;
			}
		}

		return true;
	}

	{/literal}
	//]]>-->
</script>
