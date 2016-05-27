<?php
ob_start();
$nav = 'Liste scolaire';

$search_code = trim(dims_load_securvalue('search_code', dims_const::_DIMS_CHAR_INPUT, true, true, true));


$sql = 'SELECT * FROM dims_mod_cata_panierstypes WHERE type = '._CATA_PANIER_TYPE_LIST_SCOLAIRE.' AND code_liste like "'.$search_code.'" LIMIT 1';

$res = $db->query($sql);

if($db->numrows($res)) {
    $paniertype = new paniertype();
    $paniertype->openFromResultSet($db->fetchrow($res));
    if(empty($_POST['sel'])) {
        $arts = $paniertype->getarticles();
    }
    else {
        foreach($_POST['sel'] as $key => $val) {
            $art['id'] = $key;
            $art['ref_article'] = dims_load_securvalue('ref'.$key, dims_const::_DIMS_CHAR_INPUT, true, true);
            $art['qte'] = dims_load_securvalue('qte'.$key, dims_const::_DIMS_NUM_INPUT, true, true);
            $art['selection'] = 0;
            $arts[]=$art;
        }
    }

    ?>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr bgcolor="#8BCE44">
        <td class="WebNavTitle">&nbsp;<?php echo $nav; ?> : <?php echo $paniertype->fields['libelle']; ?></td>
    </tr>
    <tr bgcolor="#dddddd" height="1"><td></td></tr>
    <tr>
        <td>
            <table cellpadding="6" cellspacing="0" width="100%">
            <tr>
                <td colspan="3">
                    <?php
                    echo "
                        <form name=\"form_paniertype\" action=\"$scriptenv\" method=\"Post\">
                        <input type=\"Hidden\" name=\"op\" value=\"\">
                        <input type=\"Hidden\" name=\"nbarticles\" value=\"0\">
                        <input type=\"Hidden\" name=\"search_code\" value=\"".$search_code."\">
                        <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border:". _CMD_COLOR_BORDER ." 1px solid\">
                            <tr bgcolor=\"#f8f8f8\">
                                <td width=\"10\">&nbsp;</td>
                                <td width=\"50\">Ref.</td>
                                <td width=\"302\">Désignation</td>";
                                    if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
                                        if ($_SESSION['catalogue']['ttc']) {
                                            echo "<td width=\"60\" align=\"right\">Prix Net TTC</td>";
                                        }
                                        else {
                                            echo "<td width=\"60\" align=\"right\">Prix Net HT</td>";
                                        }
                                    }
                                    echo "<td width=\"60\" align=\"right\">Quantité</td>
                                    <td width=\"10\" align=\"right\">Total</td>
                                    <td width=\"10\">&nbsp;</td>";
                                    if(isset($_SESSION['session_adminlevel']) && $_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP && $_SESSION['catalogue']['utiliser_selection']) echo "<td width=\"10\" align=\"center\">Sel.&nbsp;</td>";
                                    echo "</tr>";

                                    $prix_total_raw = 0;

                                    foreach ($arts as $row) {
                                        // On ajuste le nombre d'articles passé en paramètre
                                        echo "
                                            <script language=\"JavaScript\">
                                                document.form_paniertype.nbarticles.value = ". $db->numrows($rs) .";
                                            </script>";

                                        $id = 0;
                                        $bandeau = false;
                                        $id++;

                                        if ((isset($row['selection']) && $row['selection'] != 1) || (!isset($row['selection'])) && $bandeau == false) {
                                            if ($id > 1) {
                                                // On insère un bandeau pour séparer 'La sélection' du reste
                                                echo "<tr><td colspan=\"10\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
                                                echo "<tr><td colspan=\"10\" bgcolor=\"#f8f8f8\">&nbsp;</td></tr>";
                                            }

                                            $bandeau = true;
                                        }

                                        $ref = $row['ref_article'];
                                        $qte = $row['qte'];
                                        $id = $row['id'];

                                        $article = new article();
                                        $article->findByRef($ref);

                                        if ($article->fields['reference']) {
                                            $prix_raw = catalogue_getprixarticle($article, $qte);
                                            $prix = catalogue_formateprix($prix_raw);

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

                                            $desc = str_replace(' - ','_',$article->fields['label']);
                                            $desc = str_replace('+','_',$desc);
                                            $desc = urlencode(dims_convertaccents($desc));
                                            $onclick = "";

                                            ($row['selection'] == 1) ? $selection = "<img src='./front/gfx/selection.gif' alt='La sélection'>" : $selection = "";

                                            $degressif = "";
                                            if ($article->fields['degressif']) {
                                                $degressif = "<br>&nbsp;<a href=\"#\" onMouseOver=\"javascript:get_degressif('{$article->fields['reference']}');\" onMouseOut=\"javascript:hide_degressifbox();\" onClick=\"javascript:return false;\"><img src=\"./front/gfx/degressif.gif\" border=\"0\"></a>&nbsp;";
                                            }

                                            echo "
                                                <tr><td colspan=\"10\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>
                                                <tr><td colspan=\"10\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>
                                                <tr><td colspan=\"10\" height=\"5\"></td></tr>
                                                <tr>
                                                    <td valign=\"top\" width=\"20\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\"><input type=\"checkbox\" name=\"sel[$id]\" checked=\"checked\" />&nbsp;$selection</td>
                                                    <td valign=\"top\" width=\"50\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">$ref</td>
                                                    <td valign=\"top\" width=\"202\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\"><a>{$article->fields['label']}</a></td>";
                                            if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) echo "<td valign=\"top\" width=\"60\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"right\"><nobr>{$prix}</nobr>&nbsp;&euro;&nbsp;$degressif</td>";

                                            echo "
                                                <input type=\"Hidden\" name=\"ref{$id}\" value=\"$ref\">

                                                <td valign=\"top\" width=\"60\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" align=\"right\">
                                                    <table cellpadding=\"0\" cellspacing=\"0\">
                                                    <tr>
                                                        <td rowspan=\"2\"><input type=\"text\" name=\"qte{$id}\" class=\"WebInput\" size=\"5\" value=\"".$qte."\"></td>
                                                        <td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:document.form_paniertype.qte{$id}.value++;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_plus.gif\"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:if (document.form_paniertype.qte{$id}.value>0) document.form_paniertype.qte{$id}.value--;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_moins.gif\"></td>
                                                    </tr>
                                                    </table>
                                                </td>";

                                            if(isset($_SESSION['session_adminlevel']) && $_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP && $_SESSION['catalogue']['utiliser_selection']) {
                                                if ($row['selection'] == 1) {
                                                    echo "<td valign=\"top\" align=\"center\" width=\"10\"><a href=\"$scriptenv&op=selection_moins&ref_article={$article->fields['reference']}&redir=panierstype&id_panier=$id_panier\"><img src='./common/modules/catalogue/img/selection_moins.gif' border=0 alt=\"Enlever de 'La sélection'\"></a>&nbsp;</td>";
                                                }
                                                else {
                                                    echo "<td valign=\"top\" align=\"center\" width=\"10\"><a href=\"$scriptenv&op=selection_plus&ref_article={$article->fields['reference']}&redir=panierstype&id_panier=$id_panier\"><img src='./common/modules/catalogue/img/selection_plus.gif' border=0 alt=\"Ajouter à 'La sélection'\"></a>&nbsp;</td>";
                                                }
                                            }

                                            echo "<td valign=\"top\" width=\"20\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"right\"><nobr>".catalogue_formateprix($qte*$prix_raw)."</nobr>&nbsp;&euro;</td>";

                                            echo "
                                                </tr>
                                                <tr>
                                                    <td colspan=\"2\"></td>
                                                    <td colspan=\"8\" valign=\"top\">
                                                        <div id=\"detail{$id}\" style=\"display:none;\">
                                                            <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td valign=\"top\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">$detail_produit</td>
                                                                <td valign=\"top\" width=\"100\" align=\"right\">$logo</td>
                                                                $photo_detail
                                                            </tr>
                                                            <tr OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">
                                                                <td colspan=\"2\">
                                                                    <br>
                                                                    <table cellpadding=\"0\" cellspacing=\"0\">
                                                                    <tr>
                                                                        <td valign=\"middle\">refermer</td>
                                                                        <td valign=\"middle\"><img src=\"./front/gfx/detail_moins.gif\"></td>
                                                                    </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>";
                                        }
                                        else {
                                            $prix = catalogue_getprixarticle($article, $qte);
                                            $prix = catalogue_formateprix($prix);

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

                                            ($row['selection'] == 1) ? $selection = "<img src='./front/gfx/selection.gif' alt='La sélection'>" : $selection = "";

                                            $degressif = "";
                                            if ($article->fields['degressif']) {
                                                $degressif = "<br>&nbsp;<a href=\"#\" onMouseOver=\"javascript:get_degressif({$article->fields['reference']});\" onMouseOut=\"javascript:hide_degressifbox();\" onClick=\"javascript:return false;\"><u>Dégressif</u></a>&nbsp;";
                                            }

                                            echo "
                                                <tr><td colspan=\"10\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>
                                                <tr><td colspan=\"10\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>
                                                <tr><td colspan=\"10\" height=\"5\"></td></tr>
                                                <tr>
                                                    <td valign=\"top\" width=\"20\">&nbsp;</td>
                                                    <td valign=\"top\" width=\"50\">$ref</td>
                                                    <td valign=\"top\" colspan=\"5\"><font style=\"color:#cc0000\"><b>Cet article n'existe plus</b></font></td>";
                                            echo "</tr>";
                                        }
                                        $prix_total_raw += $prix_raw*$qte;
                                    }
                                    ?>
                                    <tr><td colspan="10" height="1" bgcolor="#dddddd"></td></tr>
                                    <tr>
                                        <td colspan="5">&nbsp;</td>
                                        <td align="right"><?php echo catalogue_formateprix($prix_total_raw); ?>&nbsp;&euro;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
					<a href="?op=home">
						<img src="<?= $template_web_path; ?>gfx/bt_retour_catalogue.png" />
					</a>
				</td>
                <td align="right">
					<a href="javascript:void(0);" onclick="javascript:document.form_paniertype.op.value='search_schoollist'; document.form_paniertype.submit();">
						<img src="<?= $template_web_path; ?>gfx/bt_recalculer.png" /></a>
					<a href="javascript:void(0);" onclick="javascript:document.form_paniertype.op.value='add_schoollist'; document.form_paniertype.submit();">
						<img src="<?= $template_web_path; ?>gfx/bt_ajt_panier.png" /></a>
				</td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
    <?php
}
else {
    ?>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr bgcolor="#8BCE44">
        <td class="WebNavTitle">&nbsp;<?php echo $nav; ?></td>
    </tr>
    <tr bgcolor="#dddddd" height="1"><td></td></tr>
    <tr>
        <td>
            <table cellpadding="6" cellspacing="0">
            <tr>
                <td>
                    <?php echo _DESC_PANIERSTYPES_LISTSCOLAIRE_NOTFIND; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <form action="/index.php" method="post" name="search">
                        <input type="hidden" name="op" value="search_schoollist" />
                        <div class="form_scolaire">
                            <span>
                                <input border="0" type="text" class="" name="search_code" onkeyup="javascript:searchTags(this.value);">
                                <input border="0" type="submit" value="Rechercher"  name="button_search"  onclick="javascript:if (document.getElementById('desktop_editbox_search').value != '') document.desktop_editbox_search.submit; else return false;">
                            </span>
                        </div>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
					<a href="?op=home">
						<img src="<?= $template_web_path; ?>gfx/bt_retour_catalogue.png" />
					</a>
				</td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
    <?php
}
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

$page['TITLE'] = 'Liste scolaire';
$page['META_DESCRIPTION'] = 'Votre liste scolaire';
$page['META_KEYWORDS'] = 'Panier type, liste scolaire, ajouter, créer';
$page['CONTENT'] = '';

ob_end_clean();
