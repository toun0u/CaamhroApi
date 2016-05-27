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
		Mot de passe oublié - Génération d'un nouveau mot de passe
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<div class="m-auto">
			<div class="line">
				<div class="mod">
					<div class="secondary-zone">
						<p>
							Bienvenue. Le formulaire ci-dessous vous permet de générer un nouveau
							mot de passe :
						</p>
						{if $mdp_perdu.errno > 0}
							<div class="errorExplanation">
								<h2>Erreur - {$mdp_perdu.errno}</h2>
								<p>
									{$mdp_perdu.msg}
								</p>
							</div>
						{/if}
						<form name="f_inscription" action="/index.php" method="post">
							<input type="hidden" name="op" value="mdp_perdu" />
							<input type="hidden" name="email" value="{$pwd_email}" />
							<input type="hidden" name="key" value="{$pwd_key}" />
							<div class="form_row">
								<span class="label"><label for="password1">Votre nouveau mot de passe * :</label></span>
								<span class="field"><input type="password" name="password1" id="password1" {if $mdp_perdu.errno == 3}class="fieldWithErrors"{/if} /></span>
							</div>
							<div class="form_row">
								<span class="label"><label for="password2">Confirmez votre nouveau mot de passe * :</label></span>
								<span class="field"><input type="password" name="password2" id="password2" {if $mdp_perdu.errno == 3}class="fieldWithErrors"{/if} /></span>
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
