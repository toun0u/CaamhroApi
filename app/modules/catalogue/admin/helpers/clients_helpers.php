<?php
/* --------------------- Gestion des dernières commandes ----*/
function store_lastclient($id, $nb_elems){
    $last_clients = &get_sessparam($_SESSION['cata']['clients']['last_clients'], array() );
    if(in_array($id,$last_clients)){
        unset($last_clients[array_search($id, $last_clients)]);
    }elseif(count($last_clients) >= $nb_elems){
        array_splice($last_clients,$nb_elems-1);
    }
    array_unshift($last_clients,$id);
}

function get_lastclients(){
    $last = &get_sessparam($_SESSION['cata']['clients']['last_clients'], array() );;
    return $last;

}
?>