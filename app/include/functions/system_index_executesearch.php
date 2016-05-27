<?
ini_set('memory_limit','512M');
    // on execute maintenant la recherche en commencant par charger en mémoire les éléments
    // structure : id_module => id_object => liste id_record
    // on a tout en mémoire, maintenant on interprète
    // doit tenir compte des parenthèses et des contraintes sur les ET et OU
    if (isset($_SESSION['dims']['search']['listselectedwordtemp'])) unset($_SESSION['dims']['search']['listselectedwordtemp']);
    $_SESSION['dims']['search']['listselectedwordtemp']=array();

    $i=0;
    foreach ($_SESSION['dims']['search']['listselectedword'] as $elem) {
        $_SESSION['dims']['search']['listselectedwordtemp'][$i]=$elem;
        $i++;
    }

    if (isset($_SESSION['dims']['search']['matrix'])) unset($_SESSION['dims']['search']['matrix']);
    $_SESSION['dims']['search']['matrix']=array();

    for($i=0;$i<sizeof($_SESSION['dims']['search']['listselectedwordtemp']);$i++) {
        $element=array();
        $element['used']=false;
        $element['link']=0;
        $element['word']=$_SESSION['dims']['search']['listselectedwordtemp'][$i]['word'];
        $element['key']=array_search($element['word'],$_SESSION['dims']['search']['listuniqueword']);
        $element['op']=$_SESSION['dims']['search']['listselectedwordtemp'][$i]['op'];
        $element['corresp']=array();

        $_SESSION['dims']['search']['matrix'][$i]=$element;
    }

    recursiveInterpretQuery(0,sizeof($_SESSION['dims']['search']['listselectedwordtemp'])-1,$_SESSION['dims']['search']['listselectedwordtemp']);

    // on tri les colonnes de chaque élément
    for($i=0;$i<sizeof($_SESSION['dims']['search']['listselectedwordtemp']);$i++) {
        $element=array();
        ksort($_SESSION['dims']['search']['matrix'][$i]['corresp']);
    }

    // on lance le calcul final
    $result=array();
    unset($_SESSION['dims']['search']['result']);
    recursiveExecuteQuery(0,sizeof($_SESSION['dims']['search']['listselectedwordtemp'])-1,$_SESSION['dims']['search']['result'],$_SESSION['dims']['workspaceid']);

?>
