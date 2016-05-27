<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// traitement et recherche des doublons
$keysearch= dims_load_securvalue("key",dims_const::_DIMS_CHAR_INPUT,true,true,false);

$sql = "select      c.id,c.email,
                    ucase(c.firstname) as firstname,
                    ucase(c.lastname) as lastname,
                    c.address,
                    c.postalcode,
                    c.city,
                    u.id as iduser
        from        dims_mod_business_contact as c
        left join   dims_user as u
        on          u.id_contact=c.id
        order by    lastname, firstname
        ";
$res=$db->query($sql);
$resultat=array();
$listid='';
// première lettre du nom
while ($f=$db->fetchrow($res)) {

    // controle direct
    $key=$f['lastname']." ".$f['firstname'];

    if ($key==$keysearch) {
        $f['nblink']=0;
        $f['nblinkcompany']=0;
        $resultat[$f['id']]=$f;
        if ($listid=='') $listid=$f['id'];
        else $listid.=",".$f['id'];
    }
}

// on affiche le resultat
if (!empty($resultat)) {
    // on calcul les liens de chacun et l'origine de la création
    $sql = "select  id_contact1,id_contact2,count(id) as cpte
                    from dims_mod_business_ct_link as link
                    where id_contact1 in (".$listid.")
                    OR id_contact2 in (".$listid.")  group by id_contact1";

    $res = $db->query($sql);

    while ($f=$db->fetchrow($res)) {
        if (isset($resultat[$f['id_contact1']])) {
            $idpers=$f['id_contact1'];
        }
        else {
            $idpers=$f['id_contact2'];
        }
        $resultat[$idpers]['nblink']=$f['cpte'];
    }

    // on calcul les liens de chacun et l'origine de la création
    $sql = "select  id_contact,count(id) as cpte
                    from dims_mod_business_tiers_contact as tc
                    where id_contact in (".$listid.")
                     group by id_contact ";

    $res = $db->query($sql);

    while ($f=$db->fetchrow($res)) {
        if (isset($resultat[$f['id_contact']])) {
            $idpers=$f['id_contact'];
            $resultat[$idpers]['nblinkcompany']=$f['cpte'];
        }
    }

    $isuser=0;
    foreach($resultat as $k=>$f) {
        if ($f['iduser']>0) {
            $isuser=true;
        }
    }
    // Sécurisation du formulaire par token
    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
    $token = new FormToken\TokenField;
    echo "<form name=\"formdoublon\" method=\"post\" action=\"/admin.php?action="._BUSINESS_TAB_MANAGE_DOUBLONS."&op=mergeContact\">
           <table width=\"100%\"><tr><td>".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td><td>Infos</td><td>Email</td><td>Contact Links</td><td>Company Links</td><td>".$_DIMS['cste']['_DIMS_LABEL_USER']."</td><td>".$_DIMS['cste']['_DIMS_ACTIONS']."</td><td>".$_DIMS['cste']['_FROM']."</td><td>".$_DIMS['cste']['_DIMS_DEST']."</td><tr>";
    foreach($resultat as $k=>$f) {
        echo "<tr><td>".$f['lastname']." ".$f['firstname']."</td>";
        echo "<td>".$f['address']." ".$f['postalcode']." ".$f['city']."</td>";
        echo "<td>".$f['email']."</td><td>";
        echo $resultat[$f['id']]['nblink'];
        echo "</td><td>";
        echo $resultat[$f['id']]['nblinkcompany'];
        echo "</td><td>";

        if ($f['iduser']>0) {
            echo "<img src=\"./common/img/check.png\">";
        }
        else {
            echo "&nbsp;";
        }

        echo "</td><td><a href=\"/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action=301&contact_id=".$f['id']."\" target=\"_blank\"><img src=\"./common/img/view.png\"></a>";

        //onclick=\"javascript:viewDetailDuplicate('".$k."')\">";

        if ($resultat[$f['id']]['nblink']==0 && $f['iduser']==0) {
            echo "<a href=\"\"\"><img src=\"./common/img/delete.png\" border=\"0\"></a>";
        }
        echo "</td>";

        // on affiche le from et to
        echo "<td><input type=\"checkbox\" name=\"doublon_from[]\" value=\"".$f['id']."\"";
        $token->field("doublon_from");
        if ($f['iduser']>0) {
            echo " disabled ";
        }
        else {
            if ($isuser) {
                echo " checked ";
            }
        }
        echo "></td>";

        echo "<td><input type=\"radio\" name=\"doublon_to\" value=\"".$f['id']."\"";
        $token->field("doublon_to");
        // on regarde si on a un user on selectionne
        if ($f['iduser']>0) {
            echo " checked ";
        }
        else {
            if ($isuser) {
                echo " disabled ";
            }
        }
        echo "></td></tr>";

    }

    echo "<tr><td colspan=\"9\" style=\"text-align:right;\">
    <input type=\"submit\"></td>";

    $tokenHTML = $token->generate();
    echo $tokenHTML;

    echo "</form></table>";
}
?>
