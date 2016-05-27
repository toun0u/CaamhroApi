<header class="line m-auto">
	<div class="large-hidden pa1">
		<h1><img src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/caahmro.jpg" alt="Caahmro"></h1>
		<div class="toggle"></div>
	</div>
</header>
<nav class="opacity">
	<!--[if lt IE 9]>
	<div class="ienomore">
		<table>
			<tr>
				<td style="width: auto !important;"><strong><i class="icon2-warning"></i></strong></td>
				<td style="width: 400px; !important;"><strong>Vous utilisez un navigateur dépassé depuis 2014.</strong><br />Pour une meilleure expérience web, prenez le temps de mettre à jour votre navigateur.<br />Si vous êtes équipé de windows XP, utilisez un autre navigateur qu'Internet Explorer.</td>
				<td><a href="https://www.mozilla.org/fr/firefox/desktop/" title="Firefox" class="navigateur"><i class="icon2-firefox"></i></a></td>
				<td><a href="http://windows.microsoft.com/fr-fr/internet-explorer/download-ie" title="IE" class="navigateur"><i class="icon2-IE"></i></a></td>
				<td><a href="http://support.apple.com/kb/dl1531" title="Safari" class="navigateur"><i class="icon2-safari"></i></a></td>
				<td><a href="https://www.google.fr/chrome/browser/desktop/" title="Chrome" class="navigateur"><i class="icon2-chrome"></i></a></td>
				<td><a href="http://www.opera.com/fr/computer/linux" title="Opera" class="navigateur"><i class="icon2-opera"></i></a></td>
			</tr>
		</table>
	</div><![endif]-->
	<div class="toggle"></div>
	<div class="line"></div>
	<ul class="mw1280p m-auto">
		<!--li class="width70">
			<a class="width70" href="/accueil.html">Accueil</a>
		</li-->
		{if isset($familles.cata1.famille1)}
			{foreach from=$familles.cata1.famille1 item=fam1}
				<li {if isset($fam1.famille2)}class="has-subnav{if isset($fam1.ISLAST) && $fam1.ISLAST} last-child{/if}"{/if}>
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