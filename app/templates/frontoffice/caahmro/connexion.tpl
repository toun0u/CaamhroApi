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
	<h1 class="txtcenter line">
		<i class="icon-user title-icon"></i>
		{$smarty.session.cste.CATA_CONNECTION}
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<div class="m-auto">
			{if $connexion_page.error > 0}
				<div class="flash error">
					{if $connexion_page.error == 1}
						{$smarty.session.cste.CATA_TRY_ACCESS_PRIVATE_SPACE}.
						{$smarty.session.cste.CATA_TY_CONNECT_OR_SUBSCRIBE_TO_CONTINUE}.
					{elseif $connexion_page.error == 2}
						{$smarty.session.cste._ERROR} : {$smarty.session.cste.CATA_IMPOSSIBLE_CONNECTION}.
						{$smarty.session.cste.CATA_LOGIN_OR_PASSWORD_INVALID}.
						{$smarty.session.cste.CATA_PLEASE_RETRY}.
					{/if}
				</div>
			{/if}

			<div class="line">
				<div class="mod">
					<div class="txtcenter secondary-zone">
						<h3 class="orange">Vous avez déjà vos identifiants client ? Connectez-vous</h3>
						<form name="f_inscription" action="/index.php{if isset($query_string)}{$query_string}{/if}" method="post">
							{if isset($hidden_fields)}
								{foreach from=$hidden_fields key=key item=value}
									{if is_array($value)}
										{foreach from=$value item=val}
											<input type="hidden" name="{$key}[]" value="{$val}" />
										{/foreach}
									{else}
										<input type="hidden" name="{$key}" value="{$value}" />
									{/if}
								{/foreach}
							{/if}

							<div class="form_row">
								<span class="label"><label for="dims_login">{$smarty.session.cste._WEBSITE_WATCHER_LABEL_LOGINLABEL} :</label></span>
								<span class="field"><input type="text" id="dims_login" name="dims_login" placeholder="Ex : 000999 votre n° client à 6 chiffres" ></span>
							</div>
							<div class="form_row">
								<span class="label"><label for="dims_password">{$smarty.session.cste._DIMS_LABEL_PASSWORD} :</label></span>
								<span class="field"><input type="password" id="dims_password" name="dims_password"></span>
							</div>
							<div class="form_row_validate">
								<input type="submit" class="btn btn-primary" value="{$smarty.session.cste.CATA_CONNECTION}">&nbsp;&nbsp;&nbsp;<a class="underline small" href="/index.php?op=mdp_perdu" title="{$smarty.session.cste.CATA_LOST_PASSWORD}">{$smarty.session.cste.CATA_LOST_PASSWORD} ?</a>
							</div>
						</form>
					</div>
				</div>
				{if $cata_mode_B2C}
					<div class="mod">
						<div class="txtcenter">
							<h3 class="orange">{$smarty.session.cste.CATA_I_CREATE_MY_ACCOUNT}</h3>
							<input type="button" class="btn" value="{$smarty.session.cste.CATA_CREATE_MY_ACCOUNT}" onclick="javascript: document.location.href='/index.php?op=creer_compte';">
						</div>
					</div>
				{/if}
			</div>
			<div class="grid2 mt2 line mb3">
				<div class="mod secondary-zone pb1">
					<div class="txtcenter">
						<h3 class="orange">Je suis déjà client Caahmro<br/> mais je n'ai pas d'identifiant</h3>
						<input type="button" class="btn" value="Formulaire de demande" onclick="javascript: document.location.href='/deja-client.html';">
					</div>
				</div>
				<div class="mod secondary-zone pb1">
					<div class="txtcenter">
						<h3 class="orange">Je ne suis pas encore client <br/>et je souhaite ouvrir un compte</h3>
						<input type="button" class="btn" value="{$smarty.session.cste.CATA_CREATE_MY_ACCOUNT}" onclick="javascript: document.location.href='/nouveau-client.html';">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{literal}
<script type="text/javascript">
window.onload = function() {
	document.f_inscription.login.focus();
}
</script>
{/literal}
