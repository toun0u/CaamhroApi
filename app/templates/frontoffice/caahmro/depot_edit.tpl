<form name="form" action="/index.php" method="post">
<input type="hidden" name="op" value="depot_save">
<input type="hidden" name="id" value="{$depot.id}">
<input type="hidden" name="return" value="{$return}">

<div class="content-zone">
	{include file="_mobile_menu.tpl"}
	<h1 class="txtcenter line">
		{$smarty.session.cste.CATA_YOUR_CART}
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<h3 class="titleh2_orange"><i class="orange icon2-location title-icon"></i>&nbsp;Adresse de livraison</h3>
		<div class="group-form">
			<label class="form-label" for="depot_adr1">{$smarty.session.cste._DIMS_LABEL_ADDRESS} :</label>
			<textarea id="depot_adr1" name="depot_adr1" rows="3">{$depot.adr1}</textarea>
		</div>
		<div class="group-form">
			<label class="form-label" for="depot_cp">{$smarty.session.cste._DIMS_LABEL_CP}</label>
			<input type="text" id="depot_cp" name="depot_cp" value="{$depot.cp}">
		</div>
		<div class="group-form">
			<label class="form-label" for="depot_ville">{$smarty.session.cste._DIMS_LABEL_CITY}</label>
			<input type="text" id="depot_ville" name="depot_ville" value="{$depot.ville}">
		</div>
		<div class="group-form">
			<label class="form-label" for="depot_country">{$smarty.session.cste._DIMS_LABEL_COUNTRY} :</label>
			<select id="depot_country" name="depot_country">
				<option value="0">{$smarty.session.cste.SELECT_A_COUNTRY}</option>
				{foreach from=$a_countries item=country}
					<option value="{$country->fields.id}"{if $country->fields.id == $depot.id_country} selected="selected"{/if}>{$country->fields.printable_name}</option>
				{/foreach}
			</select>
		</div>
		<br>
		<div class="txtcenter pa1">
			{if empty($return)}
				<div class="btn center">{$buttons.btn_back}</div>
			{else}
				<a href="{base64_decode($return)}" class="btn center"><i class="txtwhite icon2-reply orange title-icon"></i>&nbsp;{$smarty.session.cste._DIMS_BACK}</a>
			{/if}
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
