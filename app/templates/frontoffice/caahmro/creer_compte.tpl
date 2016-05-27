<div class="content-zone">
	{include file="_mobile_menu.tpl"}
	<h1 class="txtcenter line">
		{$smarty.session.cste.CATA_CREATION_OF_YOUR_ACCOUNT}
	</h1>
	<hr class="mt0">
	{if isset($inscription.errors)}
		<h2 class="titleh2_orange">{$smarty.session.cste._ERROR} : {$smarty.session.cste.CATA_IMPOSSIBLE_INSCRIPTION_FOR_THE_FOLLOWING_REASONS} :</h2>
		<ul>
			{foreach from=$inscription.errors item=error}
				<li>
					{$error}
				</li>
			{/foreach}
		</ul>
	{/if}
	<form name="f_inscription" action="/index.php" method="post" onsubmit="javascript: return verif_fields(this);" class="pa1">
		<input type="hidden" name="op" value="creer_compte">

		<h2 class="titleh2_orange">{$smarty.session.cste.CATA_YOUR_LOGIN_ID}</h2>

		<p>{$smarty.session.cste.CATA_CHOOSE_LOGIN_AND_PASSWORD_TO_CONNECT}</p>

		<div class="group-form">
			<label class="form-label" for="login">{$smarty.session.cste.CATA_YOUR_LOGIN_ID} *</label>
			<input type="text" id="login" name="login" value="{if isset($inscription.values.login)}{$inscription.values.login}{/if}">
			<input type="hidden" id="loginDispoBool firstname" name="loginDispoBool" value="0">
			<span id="loginDispoImg"></span>
		</div>
		<div class="group-form">
			<label class="form-label" for="password1">{$smarty.session.cste.CATA_YOUR_PASSWORD} *</label>
			<input type="password" name="password1">
		</div>
		<div class="group-form">
			<label class="form-label" for="password1">{$smarty.session.cste.CATA_CONFIRM_YOUR_PASSWORD} *</label>
			<input type="password" name="password2">
		</div>

		<h2 class="titleh2_orange">{$smarty.session.cste.CATA_YOUR_PERSONAL_INFORMATIONS}</h2>

		<h3 style="margin-top:0px;">{$smarty.session.cste.BILLING_ADDRESS}</h3>

		<div class="group-form">
			<label class="form-label" for="fact_nom">{$smarty.session.cste._DIMS_LABEL_NAME} *</label>
			<input type="text" id="fact_nom" name="fact_nom" value="{if isset($inscription.values.fact_nom)}{$inscription.values.fact_nom}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_prenom">{$smarty.session.cste._DIMS_LABEL_FIRSTNAME} *</label>
			<input type="text" id="fact_prenom" name="fact_prenom" value="{if isset($inscription.values.fact_prenom)}{$inscription.values.fact_prenom}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_raisoc">{$smarty.session.cste._DIMS_LABEL_ENT_NAME}</label>
			<input type="text" id="fact_raisoc" name="fact_raisoc" value="{if isset($inscription.values.fact_raisoc)}{$inscription.values.fact_raisoc}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_pays">{$smarty.session.cste._DIMS_LABEL_COUNTRY} *</label>
			<select id="fact_pays" name="fact_pays">
				<option value="0">{$smarty.session.cste.SELECT_A_COUNTRY}</option>
				{foreach from=$a_countries item=country}
					<option value="{$country->fields.id}"{if isset($inscription.values.fact_pays) && $inscription.values.fact_pays == $country->fields.id} selected="selected"{/if}>{$country->fields.printable_name}</option>
				{/foreach}
			</select>
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_adresse">{$smarty.session.cste._DIMS_LABEL_ADDRESS} *</label>
			<textarea id="fact_adresse" name="fact_adresse" rows="3">{if isset($inscription.values.fact_adresse)}{$inscription.values.fact_adresse}{/if}</textarea>
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_cp">{$smarty.session.cste._DIMS_LABEL_CP} *</label>
			<input type="text" id="fact_cp" name="fact_cp" value="{if isset($inscription.values.fact_cp)}{$inscription.values.fact_cp}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_ville">{$smarty.session.cste._DIMS_LABEL_CITY} *</label>
			<input type="text" id="fact_ville" name="fact_ville" value="{if isset($inscription.values.fact_ville)}{$inscription.values.fact_ville}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_tel">{$smarty.session.cste._DIRECTORY_PHONE}</label>
			<input type="text" id="fact_tel" name="fact_tel" value="{if isset($inscription.values.fact_tel)}{$inscription.values.fact_tel}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_fax">{$smarty.session.cste._DIMS_LABEL_FAX}</label>
			<input type="text" id="fact_fax" name="fact_fax" value="{if isset($inscription.values.fact_fax)}{$inscription.values.fact_fax}{/if}">
		</div>
		<div class="group-form">
			<label class="form-label" for="fact_email">{$smarty.session.cste._DIMS_LABEL_EMAIL} *</label>
			<input type="text" id="fact_email" name="fact_email" value="{if isset($inscription.values.fact_email)}{$inscription.values.fact_email}{/if}">
		</div>

		<hr class="mt0">

		<h3>{$smarty.session.cste._DELIVERY_ADDRESS}</h3>

		<div class="group-form">
			<input type="checkbox" id="idem" name="idem" value="1" {if !isset($inscription.values) || isset($inscription.values.idem)} checked="checked"{/if}>
			<label class="dinline" for="idem">{$smarty.session.cste.CATA_USE_SAME_INFOS_AS_BILLING_ADDRESS}</label>
		</div>
		<div id="adrLiv"{if !isset($inscription.values) || isset($inscription.values.idem)} style="display: none;"{/if}>
			<div class="group-form">
				<label class="form-label" for="liv_nom">{$smarty.session.cste._DIMS_LABEL_NAME} *</label>
				<input type="text" id="liv_nom" name="liv_nom" value="{if isset($inscription.values.liv_nom)}{$inscription.values.liv_nom}{/if}">
			</div>
			<div class="group-form">
				<label class="form-label" for="liv_prenom">{$smarty.session.cste._DIMS_LABEL_FIRSTNAME} *</label>
				<input type="text" id="liv_prenom" name="liv_prenom" value="{if isset($inscription.values.liv_prenom)}{$inscription.values.liv_prenom}{/if}">
			</div>
			<div class="group-form">
				<label class="form-label" for="liv_raisoc">{$smarty.session.cste._DIMS_LABEL_ENT_NAME}</label>
				<input type="text" id="liv_raisoc" name="liv_raisoc" value="{if isset($inscription.values.liv_raisoc)}{$inscription.values.liv_raisoc}{/if}">
			</div>
			<div class="group-form">
				<label class="form-label" for="liv_pays">{$smarty.session.cste._DIMS_LABEL_COUNTRY} *</label>
				<select id="liv_pays" name="liv_pays">
					<option value="0">{$smarty.session.cste.SELECT_A_COUNTRY}</option>
					{foreach from=$a_countries item=country}
						<option value="{$country->fields.id}"{if isset($inscription.values.liv_pays) && $inscription.values.liv_pays == $country->fields.id} selected="selected"{/if}>{$country->fields.printable_name}</option>
					{/foreach}
				</select>
			</div>
			<div class="group-form">
				<label class="form-label" for="liv_adresse">{$smarty.session.cste._DIMS_LABEL_ADDRESS} *</label>
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
		</div>

		<p>
			<input type="submit" class="btn btn-primary" value="{$smarty.session.cste.CATA_CREATE_MY_ACCOUNT}">&nbsp;ou&nbsp;<a class="liendecoration" href="/index.php?op=connexion">Retourner à la connexion</a>
		</p>
	</form>

</div>

<script type="text/javascript">
<!--//<![CDATA[
	{literal}
	function verif_fields(form) {
		return ($('#loginDispoBool').val() == 1);
	}

	function verifLoginDispo() {
		$.ajax({
			type: 'GET',
			url: '/index.php',
			data: {
				op: 'verif_loginDispo',
				login: $('#login').val()
			},
			dataType: 'json',
			success: function(data) {
				$('#loginDispoBool').val(data.response);
				if (data.response == 1) {
					$('#loginDispoImg').html('<i class="iconegreen biggest icon-ok-sign"></i>');
				}
				else {
					$('#loginDispoImg').html('<i class="iconegreen biggest icon-remove-sign"></i>');
				}
			}
		});
	}

	$('document').ready(function() {
		$('#login').blur(function() {
			verifLoginDispo();
		});

		$('#idem').click(function() {
			if ($(this).attr('checked') == 'checked') {
				$('#adrLiv').hide();
			}
			else {
				$('#adrLiv').show();
			}
		});

		{/literal}
		{if (isset($inscription.values.login))}
			verifLoginDispo();
		{/if}
		{literal}
	});
	{/literal}
//]]>-->
</script>
