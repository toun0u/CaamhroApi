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
		Mot de passe oublié
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<div class="m-auto">
			<div class="line">
				<div class="mod">
					<div class="secondary-zone">
						<p>
							Vous avez oublié votre mot de passe ? Aucun souci, nous vous permettons
							de le retrouver facilement sur la base de votre adresse email.
						</p>
						<p>
							Après avoir renseigné votre adresse email dans le champ ci-dessous, vous
							allez recevoir un email vous permettant de générer un nouveau mot de
							passe. Cet email contient un lien qui, lorsque vous aurez cliquez
							dessus, nous permettra de vous reconnaître.
						</p>
						{if $mdp_perdu.errno > 0}
							<div class="flash error">
								<h2>Erreur - {$mdp_perdu.errno}</h2>
								<p>
									{$mdp_perdu.msg}
								</p>
							</div>
						{/if}
						<form name="f_inscription" action="/index.php" method="post" onsubmit="javascript: return verif_fields(this);">
							<input type="hidden" name="op" value="mdp_perdu" />
							<div class="form_row">
								<span class="label"><label for="email_mdp_perdu">Votre adresse email :</label></span>
								<span class="field"><input type="text" id="email_mdp_perdu" name="email" {if $mdp_perdu.errno == 2}class="fieldWithErrors"{/if} /></span>
							</div>
							<div class="form_row_validate">
								<button type="submit" class="btn btn-primary">Valider</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
