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
		Mot de passe oublié - Email Envoyé
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<div class="m-auto">
			<div class="line">
				<div class="mod">
					<div class="secondary-zone">
						<p>
							Votre adresse Email a été reconnue par notre système. Dans quelques instants, vous allez recevoir un email vous permettant de générer un nouveau mot de passe.<br />
							Cet email contient un lien sur lequel vous devez cliquer pour accéder à la page de modification de votre mot de passe.
						</p>
						<p>
							Toute l'équipe de la société Caahmro
						</p>
						<p>
							<a href="/accueil.html">Retour à l'accueil</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
