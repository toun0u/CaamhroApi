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
			<i class="icon-user title-icon"></i>
			{$smarty.session.cste.CATA_CONNECTION}
		</h1>
	</div>

	<hr class="mt0">
	<div class="pa1">
		<div class="w90 m-auto">
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

			<div class="grid2-1 line">
				<div class="mod">
					<div class="txtcenter secondary-zone">
						<h3 class="orange">{$smarty.session.cste.CATA_ALREADY_CLIENT_CONNECT}</h3>
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
								<span class="field"><input type="text" id="dims_login" name="dims_login"></span>
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
