<div class="main small-heading">
	<header class="heading background_header header_default">
		<header class="line m-auto">
			<div class="large-hidden pa1">
				<h1><img src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/caahmro.jpg" alt="Caahmro"></h1>
				<div class="toggle"></div>
			</div>
		</header>
		<nav class="opacity">
			<div class="toggle"></div>
			<div class="line"></div>
			<ul class="m-auto">
				<!--li class="width70">
					<a class="width70" href="/accueil.html">Accueil</a>
				</li-->
				{if isset($familles.cata1.famille1)}
					{foreach from=$familles.cata1.famille1 item=fam1}
						<li {if isset($fam1.famille2)}class="has-subnav"{/if}>
							<a href="{$fam1.LINK}" title="{$fam1.LABEL}">{$fam1.LABEL}</a>
							{if isset($fam1.famille2)}
							<ul>
								{foreach from=$fam1.famille2 item=fam2}
									<li {if isset($fam2.famille3)}class="third-level"{/if}>
										<a href="{$fam2.LINK}" title="{$fam2.LABEL}">{$fam2.LABEL}</a>
										{if isset($fam2.famille3)}
											<ul>
												{foreach from=$fam2.famille3 item=fam3}
													<li>
														<a href="{$fam3.LINK}" title="{$fam3.LABEL}">{$fam3.LABEL}</a>
													</li>
												{/foreach}
											</ul>
										{/if}
									</li>
								{/foreach}
							</ul>
							{/if}
						</li>
					{/foreach}
				{/if}
			</ul>
		</nav>
	</header>
	<div class="action_nav phone-hidden">
		<div class="bloc_action">
			<a href="/accueil.html" class="home">
				<i class="icon2-home title-icon"></i>
				<span>
					Accueil
				</span>
			</a>
			{if (isset($switch_user_logged_out))}
				<a href="/index.php?op=connexion" class="phone-hidden">
					<i class="icon2-enter title-icon"></i>
					<span>
						Connexion
					</span>
				</a>
			{else}
	 			<a href="/index.php?op=compte" class="phone-hidden">
					<i class="icon2-user3 title-icon"></i>
					<span>
						Mon compte
					</span>
				</a>
				<a title="Me déconnecter" href="/index.php?dims_logout=1">
					<i class="icon2-exit title-icon"></i>
					Me déconnecter
				</a>
				<hr class="bgwhite">
				<a href="/index.php?op=panier"  class="phone-hidden">
					<i class="icon-cart"></i>
					<span id="nbArtPanier">
						{if isset($panier)}
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
						{/if}
					</span>
				</a>
	        {/if}
		</div>
	</div>
	<div id="recherche" class="line m-auto mt1 txtcenter">
		<form name="f_search" action="/index.php" method="get">
			<input type="hidden" name="op" value="recherche">
			<input type="text" name="motscles" placeholder="Rechercher un produit ..." class="ma0 w30">
			<input class="btn" type="submit" value="Recherche">
		</form>
	</div>
	<div class="m-auto">
		{if (!empty($tpl_name))}
			{include file="$tpl_name.tpl"}
		{else}
			{$page.CONTENT}
		{/if}
	</div>

	<div class="m-auto">
		<footer class="txtcenter content-zone">
			<a href="#" class="right scrollup"><i class="icon2-arrow-up"></i></a>
			<div class="menu_footer">
	            {if isset($headings.root2.heading1)}
	                {foreach from=$headings.root2.heading1 key=idh1 item=menuprincipal}
	                    <li class="border-left" id="home{$menuprincipal.POSITION}">
	                        {if $menuprincipal.SEL == "selected"}
	                            <a class="selected" title="{$menuprincipal.LABEL}" href="{$menuprincipal.LINK}">{$menuprincipal.LABEL}</a>
	                        {else}
	                            <a title="{$menuprincipal.LABEL}" href="{$menuprincipal.LINK}">{$menuprincipal.LABEL}</a>
	                        {/if}
	                    </li>
	                {/foreach}
	            {/if}
	        </div>
			<div class="pa2 right">
				&copy; 2015 CAAHMRO.fr - Tous Droits Réservés - Powered by Dims
			</div>
		</footer>
	</div><!-- !wrap1280p/m-auto -->
</div>

<div id="overlay" class="overlay" style="display: none;"></div>
<div id="msgbox" class="msgbox">
	<a id="msgboxclose" class="msgboxclose"></a>
	<div id="msgboxcontent"></div>
</div>
