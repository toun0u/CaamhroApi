<?php
if ($_SESSION['dims']['connected']) {
    ob_start();

    $id_panier = dims_load_securvalue('id_panier', dims_const::_DIMS_NUM_INPUT, true, false);
    $order = dims_load_securvalue('order', dims_const::_DIMS_CHAR_INPUT, true, false);
    $reverse = dims_load_securvalue('reverse', dims_const::_DIMS_NUM_INPUT, true, false);

    // pour le fil d'ariane
    $rubriques = $id_panier;

    // TRI DES COLONNES
    if ($order == '') $order = 'libelle';
    if ($reverse === -1) $reverse = !$reverse; // On inverse le sens

    // On met en session la combinaison op / order / reverse
    $_SESSION['catalogue']['oporder']['op'] = $op;
    $_SESSION['catalogue']['oporder']['order'] = $order;
    $_SESSION['catalogue']['oporder']['reverse'] = $reverse;
    // FIN - TRI DES COLONNES

    $navElem = array();
    if($oCatalogue->getParams('panier_type')) $navElem[] = _LABEL_MESPANIERSTYPES;
    if($oCatalogue->getParams('school_lists')) $navElem[] = _LABEL_MESLISTESSCOLAIRE;
    $nav = implode(' / ', $navElem);

    $paniers = array();
    $sql = "
        SELECT  DISTINCT(pt.id),
                pt.libelle,
                pt.type,
                pt.code_liste,
                COUNT(*) AS qte
        FROM    dims_mod_cata_panierstypes pt

        LEFT JOIN   dims_mod_cata_panierstypes_details ptd
        ON          ptd.id_paniertype = pt.id

        WHERE   pt.id_user = {$_SESSION['dims']['userid']}
        GROUP BY pt.id
        ORDER BY pt.libelle";
    $rs_panier = $db->query($sql);
    while ($row = $db->fetchrow($rs_panier)) {
        $paniers[$row[$order]][] = $row;
        if (isset($id_panier) && $id_panier == $row['id']) $nav .= "&nbsp;&raquo;&nbsp;{$row['libelle']}";
    }

    ksort($paniers);
    if (isset($reverse) && $reverse) $paniers = array_reverse($paniers);

    $uventeField = 'uvente';
    if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
        include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
        $obj_cli_cplmt = new cata_client_cplmt();
        if ($obj_cli_cplmt->open($_SESSION['catalogue']['client_id'])) {
            if ($obj_cli_cplmt->fields['soldeur'] == 'Oui') {
                $uventeField = 'uventesolde';
            }
        }
    }
    ?>

    <script type="text/javascript">
    	<!--//<![CDATA[
    	jQuery(document).ready(function ($) {
    		// popup tarif degressif
    		$('.degressif_img').hover(function() {
    			box = $('#div_degressifbox');
    			box.show();
    		}, function() {
    			box.hide();
    		}).mousemove(function(e) {
    			var mousex = e.pageX + 20;
    			var mousey = e.pageY + 20;
    			var boxWidth = box.width();
    			var boxHeight = box.height();

    			// Distance of element from the right edge of viewport
    			var boxVisX = $(window).width() - ( mousex + boxWidth );
    			// Distance of element from the bottom edge of viewport
    			var boxVisY = $(window).height() - ( mousey + boxHeight );

    			// If box exceeds the X coordinate of viewport
    			if (boxVisX < 20) {
    				mousex = e.pageX - boxWidth - 20;
    			}
    			// If box exceeds the Y coordinate of viewport
    			if (boxVisY < 20) {
    				mousey = e.pageY - boxHeight - 20;
    			}

    			box.css({ top: mousey, left: mousex });
    		});
    	});

        function get_degressif(pref) {
            dims_getxmlhttp('/index-light.php','get_degressif','&dims_moduleid=<?= $catalogue_moduleid; ?>&pref='+pref,'div_degressif');
        }
    	//]]>-->
    </script>

    <table width="100%" cellpadding="0" cellspacing="0">
    <tr bgcolor="#0097D9">
        <td>
            <table cellpadding="0" cellspacing="0" width="100%" height="10%" >
                <tr>
                    <td class="image_panier" colspan="0"><img border="0" alt="Mes Paniers Types" src="/modules/catalogue/img/paniers_types.png"></td>
                    <td colspan="3" class="WebNavTitle">&nbsp;<? echo $nav; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr bgcolor="#dddddd" height="1"><td colspan="3"></td></tr>
    <tr>
        <td>
            <table cellpadding="7" cellspacing="0" width="100%">
            <tr>
                <td>
                    <?php
                    if (sizeof($paniers)) {
                        ?>
                        <table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #ececec;">
                        <tr><td colspan="10" height="1"></td></tr>
                        <tr>
                            <td class="panier_titre" width="250" height="20"><b><? echo catalogue_makelink($scriptenv, 'Panier', 'libelle', $reverse, $order, $op); ?></b></td>
                            <td class="panier_titre" width="90" height="20"><b><? echo catalogue_makelink($scriptenv, 'Type', 'type', $reverse, $order, $op); ?></b></td>
                            <td class="panier_titre" width="90" height="20"><b><? echo catalogue_makelink($scriptenv, 'Code', 'code_liste', $reverse, $order, $op); ?></b></td>
                            <td class="panier_titre" width="90" height="20"><b><? echo catalogue_makelink($scriptenv, 'Nb Article', 'qte', $reverse, $order, $op); ?></b></td>
                            <td class="panier_titre" colspan="8" width="350" height="20"></td>
                        <tr>
                        <?php
                        foreach ($paniers as $panier) {
                            foreach ($panier as $row) {
                                $idpanier = $row['id'];
                                if (isset($id_panier) && $id_panier == $row['id']) {
                                    $bgcolor = "bgcolor=\"". _CMD_COLOR_ENTETE ."\"";
                                    $button = catalogue_makegfxbutton('Fermer','<img src="./common/modules/catalogue/img/supprimer.png">',"document.location.href='$scriptenv?op=panierstype'",'*');
                                    $link = "$scriptenv?op=panierstype";
                                }
                                else {
                                    $bgcolor = "";
                                    $button = catalogue_makegfxbutton('Ouvrir','<img src="./common/modules/catalogue/img/voir.png">',"document.location.href='$scriptenv?op=panierstype&id_panier={$row['id']}'",'*');
                                    $link = "$scriptenv?op=panierstype&id_panier={$row['id']}";
                                }

                                echo "
                                    <form name=\"form{$idpanier}\" action=\"$scriptenv\" method=\"Post\">
                                    <input type=\"Hidden\" name=\"op\" value=\"\">
                                    <input type=\"Hidden\" name=\"id_panier\" value=\"{$row['id']}\">
                                    <input type=\"Hidden\" name=\"nbarticles\" value=\"0\">";
                                echo "<tr><td colspan=\"10\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>";
                                echo "<tr><td colspan=\"10\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>";
                                echo "<tr $bgcolor><td colspan=\"10\" height=\"5\"></td></tr>";

                                echo "
                                    <tr $bgcolor>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\">&nbsp;<b><a>{$row['libelle']}</a></b></td>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\"><a>".(($row['type'] == _CATA_PANIER_TYPE_LIST_SCOLAIRE) ? _CATALOGUE_PANIER_TYPE_LIST_SCOLAIRE : _CATALOGUE_PANIER_TYPE_LIST_CLASSIQUE)."</a></td>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\" class='typewriter'><a>{$row['code_liste']}</a></td>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\"><a>{$row['qte']}</a></td>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\" colspan=\"5\" align=\"right\">".$button."</td>
                                        <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$link'\" width=\"2\">&nbsp;</td>
                                    </tr>";

                                echo "<tr $bgcolor><td colspan=\"8\" height=\"2\"></td></tr>";
                                if (isset($id_panier) && $id_panier == $row['id']) {
                                    echo "
                                        <tr $bgcolor><td colspan=\"8\" height=\"2\"></td></tr>
                                        <tr>
                                            <td colspan=\"8\">
                                                <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border:". _CMD_COLOR_BORDER ." 1px solid\">
                                                <tr bgcolor=\"#f8f8f8\">
                                                    <td width=\"50\">&nbsp;</td>
                                                    <td width=\"10\">&nbsp;</td>
                                                    <td width=\"50\">Ref.</td>
                                                    <td width=\"262\">Désignation</td>
                                                    <td width=\"40\" align=\"center\">Cdt</td>
                                                    <td width=\"10\" align=\"center\">Stock</td>
                                                    <td width=\"60\" align=\"center\">&nbsp;&nbsp;Unité&nbsp;&nbsp;<br>&nbsp;&nbsp;de Vente&nbsp;&nbsp;</td>";
                                    if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
                                        if ($oCatalogue->getParams('cata_base_ttc')) {
                                            echo "<td width=\"60\" align=\"right\">Prix Net TTC</td>";
                                        }
                                        else {
                                            echo "<td width=\"60\" align=\"right\">Prix Net HT</td>";
                                        }
                                    }
                                    echo "
                                        <td width=\"60\" align=\"right\">Quantité</td>
                                        <td width=\"30\" align=\"center\">&nbsp;Ajouter&nbsp;</td>
                                        <td width=\"30\" align=\"center\">&nbsp;Effacer&nbsp;</td>";

                                    if($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP && $_SESSION['catalogue']['utiliser_selection']) echo "<td width=\"10\" align=\"center\">Sel.&nbsp;</td>";
                                    echo "</tr>";
                                    $sql = "
                                        SELECT  ptd.ref_article,
                                                ptd.*,
                                                sel.selection

                                        FROM    dims_mod_cata_panierstypes pt

                                        INNER JOIN  dims_mod_cata_panierstypes_details ptd
                                        ON          pt.id = ptd.id_paniertype

                                        INNER JOIN  dims_mod_cata_article art
                                        ON          ptd.ref_article = art.reference

                                        LEFT JOIN   dims_mod_vpc_selection sel
                                        ON          sel.ref_article = ptd.ref_article
                                        AND         sel.ref_client = '{$_SESSION['catalogue']['code_client']}'

                                        WHERE   pt.id = $id_panier
                                        AND     pt.id_user = {$_SESSION['dims']['userid']}

                                        GROUP BY ptd.ref_article
                                        ORDER BY art.label ASC";
                                    $rs = $db->query($sql);

                                    // On ajuste le nombre d'articles passé en paramètre
                                    echo "
                                        <script language=\"JavaScript\">
                                            document.form{$idpanier}.nbarticles.value = ". $db->numrows($rs) .";
                                        </script>";

                                    $id = 0;
                                    $bandeau = false;
                                    while ($rowl = $db->fetchrow($rs)) {
                                        $id++;

                                        if ((isset($rowl['selection']) && $rowl['selection'] != 1) || (!isset($rowl['selection'])) && $bandeau == false) {
                                            if ($id > 1) {
                                                // On insère un bandeau pour séparer 'La sélection' du reste
                                                echo "<tr><td colspan=\"12\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
                                                echo "<tr><td colspan=\"12\" bgcolor=\"#f8f8f8\">&nbsp;</td></tr>";
                                            }

                                            $bandeau = true;
                                        }

                                        $ref = $rowl['ref_article'];
                                        $qte = $rowl['qte'];

                                        $article = new article();
                                        $article->findByRef($ref);

                                        if ($article->fields['reference']) {
                                            $prix = catalogue_getprixarticle($article, $qte);

                                            $prixaff = $prix;

                                            $prix = catalogue_formateprix($prix);
                                            $prixaff = catalogue_formateprix($prixaff);

                                            $detail_produit = catalogue_detailproduit($article->fields);
                                            $logo = catalogue_logoproduit($article->fields);

                                            $desc = str_replace(' - ','_',$article->fields['label']);
                                            $desc = str_replace('+','_',$desc);
                                            $desc = urlencode(dims_convertaccents($desc));
                                            $onclick = "javascript:document.location.href='/article/".$article->fields['urlrewrite'].".html';";

                                            ($rowl['selection'] == 1) ? $selection = "<img src='./front/gfx/selection.gif' alt='La sélection'>" : $selection = "";

                                            $degressif = "";
                                            if ($article->fields['degressif']) {
                                                $degressif = "<br>&nbsp;<a href=\"#\" onMouseOver=\"javascript:get_degressif('{$article->fields['reference']}');\" onMouseOut=\"javascript:hide_degressifbox();\" onClick=\"javascript:return false;\"><img src=\"./front/gfx/degressif.gif\" border=\"0\"></a>&nbsp;";
                                            }


                                            // on s'assure de la presence de la photo
                                            $photo = $template_web_path.'gfx/empty_100x100.png';
                                            $vignette = $article->getVignette(100);
                                            if ($vignette != null) {
                                                $photo = $vignette;
                                            }

                                            // stock
                                            if ($article->fields['qte'] <= 0) {
                                                $stock_img = $template_web_path.'/gfx/puce_rouge.png';
                                            }
                                            elseif ($article->fields['qte'] <= $article->fields['qte_mini']) {
                                                $stock_img = $template_web_path.'/gfx/puce_orange.png';
                                            }
                                            else {
                                                $stock_img = $template_web_path.'/gfx/puce_verte.png';
                                            }

                                            // Quantité > uniquement si liste scolaire. Sinon 0
                                            $artQte = ($row['type'] == _CATA_PANIER_TYPE_LIST_SCOLAIRE) ? $qte : 0;

                                            echo "
                                                <tr><td colspan=\"12\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>
                                                <tr><td colspan=\"12\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>
                                                <tr><td colspan=\"12\" height=\"5\"></td></tr>
                                                <tr>
                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">&nbsp;<img src=\"$photo\" alt=\"{$article->fields['label']}\" /></td>
                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">&nbsp;$selection</td>
                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">$ref</td>
                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\"><a>{$article->fields['label']}</a></td>

                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"center\">
                                                        <img src=\"$template_web_path/gfx/carton24.png\" alt=\"Vendu par\" style=\"vertical-align: middle;\"/> ".intval($article->fields['cond'])."
                                                    </td>

                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"center\"><a><img src=\"".$stock_img."\" /></a></td>
                                                    <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"center\">".$article->fields[$uventeField]."</td>";
                                            if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) echo "<td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"right\"><nobr>{$prixaff}</nobr>&nbsp;&euro;&nbsp;$degressif</td>";

                                            echo "
                                                <input type=\"Hidden\" name=\"ref{$id}\" value=\"$ref\">

                                                <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" align=\"right\">
                                                    <table cellpadding=\"0\" cellspacing=\"0\">
                                                    <tr>
                                                        <td rowspan=\"2\"><input type=\"text\" name=\"qte{$id}\" id=\"qte{$id}\" class=\"WebInput\" size=\"5\" value=\"".$artQte."\" onblur=\"Javascript: constraintFieldsUvente(this, ".$article->fields[$uventeField].");\"></td>
                                                        <td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:modifyQte(dims_getelem('qte".$id."'), ".$article->fields[$uventeField].");\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_plus.gif\"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:modifyQte(dims_getelem('qte".$id."'), -".$article->fields[$uventeField].");\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_moins.gif\"></td>
                                                    </tr>
                                                    </table>
                                                </td>
                                                <td valign=\"top\" align=\"center\"><a href=\"javascript:void(0);\" onclick=\"javascript:ajouter_panierart(".$article->get('id').", $('#qte".$id."').val());\"><img src=\"./common/modules/catalogue/img/panier_24.png\" alt=\"Ajouter cet article\" border=\"0\"></a></td>

                                                <td valign=\"top\" align=\"center\"><a href=\"javascript:dims_confirmlink('/index.php?op=effacer_pt_article&id_panier=$id_panier&pref={$article->fields['reference']}','". _CONFIRM_PT_DEL_ART ."');\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Effacer cet article\" border=\"0\"></a></td>";

                                            if ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP && $_SESSION['catalogue']['utiliser_selection']) {
                                                if ($rowl['selection'] == 1) {
                                                    echo "<td valign=\"top\" align=\"center\" <a href=\"$scriptenv&op=selection_moins&ref_article={$article->fields['reference']}&redir=panierstype&id_panier=$id_panier\"><img src='./common/modules/catalogue/img/selection_moins.gif' border=0 alt=\"Enlever de 'La sélection'\"></a>&nbsp;</td>";
                                                }
                                                else {
                                                    echo "<td valign=\"top\" align=\"center\" <a href=\"$scriptenv&op=selection_plus&ref_article={$article->fields['reference']}&redir=panierstype&id_panier=$id_panier\"><img src='./common/modules/catalogue/img/selection_plus.gif' border=0 alt=\"Ajouter à 'La sélection'\"></a>&nbsp;</td>";
                                                }
                                            }

                                            echo "</tr>";
                                        }
                                        else {
                                            $prix = catalogue_getprixarticle($article, $qte);
                                            $prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

                                            $prix = catalogue_formateprix($prix);
                                            $prixaff = catalogue_formateprix($prixaff);

                                            $photo = '';
                                            $photo_detail = '';
                                            if ($article->fields['image']) $refimage = substr($article->fields['image'],0,strlen($article->fields['image'])-4);
                                            $imagefile = "./photos/$refimage.jpg";

                                            if (file_exists($imagefile)) {
                                                $photo = "<img border=\"0\" src=\"./modules/catalogue/miniature.php?ref=$refimage&size=30\">";
                                                $photo_detail = "<td valign=\"top\" width=\"100\" height=\"100\" align=\"right\" style=\"background-image:url('./modules/catalogue/miniature.php?ref=$refimage&size=100');background-repeat:no-repeat\"><a href=\"javascript:dims_openwin('./modules/catalogue/popup_photo.php?img=$refimage',420,410);\"><img border=\"0\" src=\"./common/modules/catalogue/img/agrandir.gif\"></a></td>";
                                            }

                                            $detail_produit = catalogue_detailproduit($article->fields);
                                            $logo = catalogue_logoproduit($article->fields);

                                            $onclick = "javascript:document.location.href='$scriptenv?op=fiche_article&pref=$ref';";

                                            ($rowl['selection'] == 1) ? $selection = "<img src='./front/gfx/selection.gif' alt='La sélection'>" : $selection = "";

                                            $degressif = "";
                                            if ($article->fields['degressif']) {
                                                $degressif = "<br>&nbsp;<a href=\"#\" onMouseOver=\"javascript:get_degressif({$article->fields['reference']});\" onMouseOut=\"javascript:hide_degressifbox();\" onClick=\"javascript:return false;\"><u>Dégressif</u></a>&nbsp;";
                                            }

                                            echo "
                                                <tr><td colspan=\"12\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>
                                                <tr><td colspan=\"12\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>
                                                <tr><td colspan=\"12\" height=\"5\"></td></tr>
                                                <tr>
                                                    <td valign=\"top\" width=\"20\">&nbsp;</td>
                                                    <td valign=\"top\" width=\"50\">$ref</td>
                                                    <td valign=\"top\" colspan=\"6\"><font style=\"color:#cc0000\"><b>Cet article n'existe plus</b></font></td>";
                                            echo "<td valign=\"top\" align=\"center\"><a href=\"javascript:dims_confirmlink('$scriptenv?op=effacer_pt_article&id_panier=$id_panier&pref=$ref','". _CONFIRM_PT_DEL_ART ."');\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Effacer cet article\" border=\"0\"></a></td>";
                                            echo "</tr>";
                                        }
                                    }

                                    echo "
                                                <tr>
                                                    <td colspan=\"12\" align=\"right\">
                                                        <table>
                                                        <tr>
                                                            <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$scriptenv?op=panierstype&id_panier={$idpanier}'\"><b><a>{$row['libelle']}</a></b></td>
                                                            <td OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" OnClick=\"javascript:document.location.href='$scriptenv?op=panierstype&id_panier={$idpanier}'\" ><a>{$row['qte']}</a></td>
                                                            <td>".catalogue_makegfxbutton('Supprimer','<img src="./common/modules/catalogue/img/supprimer.png">',"document.location.href='$scriptenv?op=supprimer_panier&id_panier={$idpanier}'",'*',true)."</td>
                                                        </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </table>
                                            </td>
                                        </tr>";
                                }
                                echo "</form>";
                            }
                        }
                        ?>
                        <tr><td colspan="10" height="1" bgcolor="#dddddd"></td></tr>
                        </table>
                    </td>
                </tr>
                <?php
            }
            else {
                echo _NO_MESPANIERSTYPES;
            }
            ?>
            </table>
        </td>
    </tr>
    </table>
    <div id="raccourci">
        <table width="100%" cellpadding="20" cellspacing="0">
            <tr>
                <td id="espace_raccourci">
                    <a href="/index.php?op=saisierapide"><img border="0" alt="Saisie rapide" src="/modules/catalogue/img/saisie_rapide.png" /><p>&nbsp;Saisie rapide</p></a>
                </td>
                <td id="espace_raccourci">
                    <a href="/index.php?op=panier"><img border="0" alt="Panier" src="/modules/catalogue/img/panier.png" /><p>&nbsp;Panier</p></a>
                </td>
                <td id="espace_raccourci">
                    <a href="/index.php?op=commandes"><img border="0" alt="Commandes" src="/modules/catalogue/img/commandes.png" /><p>&nbsp;Commandes en cours</p></a>
                </td>
                <td id="espace_raccourci">
                    <a href="/index.php?op=historique"><img border="0" alt="Historique" src="/modules/catalogue/img/historique.png" /><p>&nbsp;Historique</p></a>
                </td>
    			<?php if ($oCatalogue->getParams('exceptional_orders')): ?>
                <td id="espace_raccourci">
                    <a href="/index.php?op=hors_catalogue"><img border="0" alt="Hors Catalogue" src="/modules/catalogue/img/hors_catalogue.png" /><p>&nbsp;Commandes exceptionnelles</p></a>
                </td>
    			<?php endif; ?>
            </tr>
        </table>
    </div>
    <?php
    $smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

    $page['TITLE'] = 'Panier type';
    $page['META_DESCRIPTION'] = 'Visualiser vos panier type';
    $page['META_KEYWORDS'] = 'panier type, commande, rapide';
    $page['CONTENT'] = '';

    ob_end_clean();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
