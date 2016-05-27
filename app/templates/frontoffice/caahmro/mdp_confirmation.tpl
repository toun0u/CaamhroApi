<div id="nv_client">
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
		Mot de passe oublié - Confirmation
	</h1>
	<hr class="mt0">
	<div class="pa1">
		<div class="m-auto">
			<div class="line">
				<div class="mod">
					<div class="secondary-zone">
						<p>
							Votre mot de passe a bien été modifié. Vous pouvez désormais l'utiliser pour vous connecter.
						</p>
						<p>
							<a href="/?op=connexion">Retour au formulaire de connexion</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
