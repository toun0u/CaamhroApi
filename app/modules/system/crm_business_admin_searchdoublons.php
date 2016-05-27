<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// traitement et recherche des doublons
$sql = "select      c.id,ucase(c.firstname) as firstname,ucase(c.lastname) as lastname
        from        dims_mod_business_contact as c
        left join   dims_user as u
        on          u.id_contact=c.id
        order by    lastname, firstname
        ";
$res=$db->query($sql);
$resultat=array();

// première lettre du nom
while ($f=$db->fetchrow($res)) {

    // controle direct
    $key=$f['lastname']." ".$f['firstname'];

    if (isset($resultat[$key])) {
        $resultat[$key]++;
    }
    else {
        $resultat[$key]=1;
    }
}

echo $_DIMS['cste']['_DIMS_LABEL_CONTACTS']." : ".$db->numrows($res)."<br>";

// on parcourt chaque debut de noms

// on affiche le resultat
if (!empty($resultat)) {
    $total=0;
    echo "<table width=\"100%\"><tr><td>".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td><td>Nb</td><td>".$_DIMS['cste']['_DIMS_LABEL_VIEW']."</td><tr>";
    foreach($resultat as $k=>$nb) {
        if ($nb>1) {
            echo "<tr><td><a href=\"javascript:void(0);\" onclick=\"javascript:viewDetailDuplicate('".$k."')\">".$k."</a></td><td>".$nb."</td><td><a href=\"javascript:void(0);\" onclick=\"javascript:viewDetailDuplicate('".$k."')\"><img src=\"./common/img/view.png\"></a></tr>";
            $total++;
        }
    }
    echo "<tr><td colspan=\"3\" align=\"right\">".$total."</td></tr>";
    echo "</table>";
}
?>
