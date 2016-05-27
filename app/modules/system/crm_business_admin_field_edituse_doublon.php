<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$metafield = new metafield();
$metafield->open($metafield_id);

$workspace = new workspace();
$lstworkspace=$workspace->getAllWorkspace();

echo $skin->open_simplebloc("Duplicate layer content <b>".$metafield->fields['name']."</b>");

$tabcontact_layer=array();

if ($metafield->fields['id_mbfield']>0) {
    $mbf = new mb_field();
    $mbf->open($metafield->fields['id_mbfield']);

    $namefield=$mbf->fields['name'];

    $sql_sa = "SELECT id,".$namefield.",type_layer,id_layer FROM dims_mod_business_contact_layer order by id";

    $res_sa = $db->query($sql_sa);
    while($tab = $db->fetchrow($res_sa)) {
        if (isset($tab[$namefield]) && trim($tab[$namefield])!='') {
            if (!isset($tabcontact_layer[$tab['id']])) $tabcontact_layer[$tab['id']]=array();

            $tabcontact_layer[$tab['id']][$tab['type_layer']][$tab['id_layer']]=trim($tab[$namefield]);
        }
    }

    // construction du resultat
    $sql_sa = "SELECT id,".$namefield.",firstname,lastname FROM dims_mod_business_contact order by id";

    echo "<table style=\"width:100%;\"><tr><td width=\"15%\">Contact</td><td width=\"5%\">&nbsp;</td><td width=\"70%\">Others values</td></tr>";
    $res_sa = $db->query($sql_sa);
    $c=0;
    while($tab = $db->fetchrow($res_sa)) {

        if (isset($tab[$namefield]) && $tab[$namefield]=='' && isset($tabcontact_layer[$tab['id']]) && !empty($tabcontact_layer[$tab['id']])) {
            $c= $c%2;
            echo "<tr class=\"trl".($c+1)."\"><td>".$tab['firstname']." ".$tab['lastname']."</td>";


            $tabres='';
            $icon='';
            $curvalue='';
            $ismultivalue=false;

            foreach ($tabcontact_layer[$tab['id']] as $type_layer => $elems) {

                switch($type_layer) {
                    case 1:
                        $icon='<img src="./common/img/users.png"> ';
                        break;

                    case 2:
                        $icon='<img src="./common/img/user.png"> ';
                        break;
                }

                foreach ($elems as $id_layer => $value) {
                    $tabres.=$icon." ";
                    // on prend la valeur du workspace + valeur réelle
                    if ($type_layer==1 && isset($lstworkspace[$id_layer])) $tabres.=$lstworkspace[$id_layer]." ";

                    $tabres.= " : ".trim($value)."<br>";

                    if ($curvalue=='') $curvalue=trim($value);
                    else {
                        // on compare
                        if ($curvalue!=$value) {
                            $ismultivalue=true;
                        }
                    }
                }


            }

            echo "<td>";
            if ($ismultivalue) echo "<img src=\"./common/img/warning.png\">";

            echo "</td><td>";
            echo $tabres."</td></tr>";

            $c++;
        }
    }

    echo "</table>";
}



echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
<input type=\"button\" onclick=\"dims_hidepopup();\" value=\"Fermer\" class=\"flatbutton\"/>";

echo $skin->close_simplebloc();
?>
