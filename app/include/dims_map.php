<?php

/**
 * Description of dims_map
 * Cette classe a pour but de simuler une map.
 * Elle stocke des valeurs/objets dans un tableau en fonction d'un indice.
 * L'indice peut être numérique ou sous la forme d'une chaîne de caractère.
 * Il faut noter qu'un indice numérique permettra un fonctionnement plus rapide
 * des méthodes d'accès.
 * Cette classe implémente l'interface itérator ce qui permet de parcourir les
 * éléments de la map à travers un foreach.
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 * @see Iterator
 */
class dims_map implements Iterator{
    private $map ;
    private $nbObjet = 0;
    private $cpt_iteration = 0;

    /**
     * Constructeur qui initialise la map.
     */
    public function __construct() {
        $map = array();
        $this->nbObjet = 0;
    }

    /**
     * Méthode qui ajoute une valeur $valeur dans la map à l'indice $indice.
     * La méthode va tester si l'indice et la valeur ne sont pas null avant de
     * tenter un ajout dans la map.
     * @param type $indice - indice de la valeur dans la map
     * @param type $valeur - valeur/objet à enregistrer dans la map.
     */
    public function put($indice, $valeur){
        if($indice != null && $valeur != null){
            $this->map[$indice] = $valeur;
            $this->nbObjet ++;
        }else{
            //TODO EXCEPTION
        }
    }

    /**
     * Méthode qui retourne la valeur (ou l'objet) associé à l'indice dans la map.
     * Attention la méthode renvoi null si l'indice n'existe pas dans la map.
     * @param type $indice - Indice de la valeur que l'on souhaite obtenir.
     * @return type : Valeur ou objet.
     */
    public function get($indice){
        if(isset($this->map[$indice])){
            return $this->map[$indice] ;
        }else{
            return null ;
        }
    }

    /**
     * Cette fonction retire un objet identifié par son $indice de la map.
     * @param type $indice - indice de la valeur (ou de l'objet) que l'on
     * souhaite obtenir.
     */
    public function remove($indice){
        unset($this->map[$indice]) ;
        $this->nbObjet --;
    }

    /**
     * Cette fonction teste si la map est vide ou non. Cette méthode lit une
     * valeur entière qui détermine le nombre d'objet dans la map. Elle ne
     * parcourt pas la map. Elle est donc très rapide et peut être utilisé dans
     * une itération.
     * @return boolean - true si vide false sinon.
     */
    public function isEmpty(){
        if($this->nbObjet == 0){
            return true ;
        }else{
            return false;
        }
    }

    /**
     * Cette fonction retourne le nombre d'objet présent dans la map.
     * @return int - nombre d'objet dans la map. Cette méthode lit une
     * valeur entière qui détermine le nombre d'objet dans la map. Elle ne
     * parcourt pas la map. Elle est donc très rapide et peut être utilisé dans
     * une itération.
     */
    public function sizeOf(){
        return $this->nbObjet ;
    }

    /**
     * Cette fonction va tester si la map contient ou non l'indice passé en paramètre.
     * @param type $indice - Indice dont on souhaite tester l'existence.
     * @return boolean - true si elle contient l'indice false sinon
     */
    public function containsKey($indice){
        return isset($this->map[$indice]) ;
    }

    /**
     * Cette fonction va tester si la map contient ou non l'objet passé en paramètre.
     * Attention cette fonction effectue une itération sur toute la map pour tester
     * la présence de l'objet. Son usage doit être exceptionnel.
     * @param type $objet - Objet dont l'on souhaite tester l'existence
     * @return boolean - true si elle contient l'objet false sinon
     */
    public function containsValue($objet){
        foreach ($this->map as $obj) {
            if($obj === $objet){
                return true;
            }
        }
        return false ;
    }

    /**
     * Cette fonction vide la map et la réinitialise.
     */
    public function clean(){
        unset($this->map) ;
        $this->map = array();
        $this->nbObjet = 0;
    }



    public function rewind() {
       reset($this->map);
       $this->cpt_iteration = 0 ;
    }

    public function next() {
        next($this->map);
        $this->cpt_iteration++ ;
    }

    public function key() {
       return key($this->map);
    }

    public function current() {
        return current($this->map);
    }

    public function valid() {
        if($this->cpt_iteration < $this->nbObjet){
            return true;
        }
        return false;
    }
}

?>

