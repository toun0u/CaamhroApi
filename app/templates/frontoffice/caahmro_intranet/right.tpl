    {if (isset($global_filter_label))}
    <div class="mod content-zone-green w300p">
        <article class="mod txtcenter mod-separator">
            <div class="pa1">
                <div id="global_filter_info" class="mw1280p m-auto txtblack">
                    Vous êtes actuellement dans l'espace "{$global_filter_label}"
                    <a class="btn btn-primary" style="color: black !important;" href="{$returnURI}">Retourner au site complet</a>
                </div>
            </div>
        </article>
    </div>
    {/if}

    <div class="mod content-zone w300p">
        <article class="mod txtcenter mod-separator">
            <div class="pa1">
                <header>
                    <h1 class="txtcenter line">
                        <i class="icon-user title-icon"></i>
                        Mon compte
                    </h1>
                </header>
                <section>
                    <div id="connexion">
                        <div id="account" class="{if (isset($switch_user_logged_out))}logged_out{else}logged_in{/if}">
                            {if (isset($switch_user_logged_out))}
                                <form action="/index.php" method="post" class="navbar-search pull-right">
                                    <div class="collapse-group" style="float: left; margin-left: 40px;">
                                        <input style="width: 90% ! important;" type="text" name="dims_login" placeholder="Identifiant...">
                                    </div>
                                    <div class="collapse-group" style="float: left; margin-left: 40px;">
                                        <input style="width: 78% ! important;" type="password" name="dims_password" placeholder="Mot de passe...">
                                        <input type="submit" value="" class="password-btn">
                                    </div>
                                    <!--a style="float:left;" class="password" href="/index.php?op=mdp_perdu">Mot de passe perdu <img style="padding-left: 5px;" border="0" src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/icon-perdu.png"></a-->
                                </form>
                            {else}
                                <div class="on" id="account_logged">
                                    <div class="user">
                                        <b style="color:#2E2E2E">Bienvenue,</b><font style="color:#2E2E2E"> {$smarty.session.dims.user.firstname} {$smarty.session.dims.user.lastname}</font>
                                    </div>
                                    <div class="espace_client" style="float: left; width: 100%;">
                                        <span class="icon"><a class="btn btn-primary btn-small" href="/index.php?op=compte">Mon espace perso</a></span>
                                        <span style="float:right;" class="logout"><a class="btn btn-primary btn-small" title="Me déconnecter" href="/index.php?dims_logout=1">Me déconnecter</a></span>
                                    </div>

                                </div>
                            {/if}
                        </div>
                    </div>
                </section>
            </div>
        </article>
    </div>
    <div class="mod content-zone w300p">
        <article class="mod txtcenter mod-separator">
            <div class="pa1">
                <header>
                    <h1 class="txtcenter line">
                        <i class="icon-cart title-icon"></i>
                        Votre panier
                    </h1>
                </header>
                <section>
                    <div id="divpanier" class="right-box-middle"></div>
                    <a class="btn btn-primary" href="/index.php?op=panier">Voir mon panier</a>
                </section>
            </div>
        </article>
    </div>
    <div class="mod content-zone w300p">
        <article class="mod txtcenter mod-separator">
            <div class="pa1">
                <header>
                    <h1 class="txtcenter line">
                        Infos utiles
                    </h1>
                </header>
                <section>
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
                </section>
            </div>
        </article>
    </div>
</section>
