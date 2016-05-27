<?php

/**
 *	Classe rapide de benchmark
 *	@author Baptiste @ Netlor
 */

/**
*	TO DO :
*	- constantes (points d'alerte)
*/

class Dims_Benchmark {

	// Singleton
	private static $self;

	// Points d'arrêts
	private $breakPoints = array();

	// Dernier breeakpoint définit
	private $lastBreakPoint;

	// Start
	private $start;

	/**
	*	Constructeur
	*	Ajoute un point d'arrêt marquant le début du bench
	*/
	public function Dims_Benchmark(){
		$this->start = (float)microtime(true);
		$array['time'] = 0.000000;
		$array['msg']  = "[ 0.000000 ] Initialisation de la classe de benchmark<br />";
		$this->breakPoints[0] = $array;
	}

	/**
	*	Singleton
	*	Retourne une instance de cet objet
	*/
	public static function getInstance(){

		if(is_null(self::$self)){
			self::$self = new Dims_Benchmark();
		}

		return self::$self;
	}

	/**
	*	Définit un point d'arrêt
	*	@param String $name Nom du point d'arrêt
	*/
	public function setBreak($name = ''){

		$duree = (float)microtime(true) - $this->start;

		$array = array();

		if($duree < 0.000001){
			$duree = '0.000000';
		} else {
			$duree = round($duree, 6);
		}

		$array['time'] = $duree;
		$array['msg'] = "[ ".$duree." ] ";
		$array['msg'].= $name."<br />";

		$this->breakPoints[] = $array;
	}

	/**
	*	Définit un commentaire
	*	@param String $comment Commentaire
	*/
	public function setComment($comment = ''){
		$array['time'] = '[ NOTE ]';
		$array['msg'] = '[ '.$comment.' ]<br />';
		$this->breakPoints[] = $array;
	}

	/**
	*	Effectue le calcul du temps écoulé
	*	entre plusieurs points d'arrêt
	*	@param Integer $key Clé à calculer
	*/
	private function getElapsedTime($key){

		for($i = $key; $i > 0; $i--){
			if(is_float($this->breakPoints[$i]['time'])){
				$r = $this->breakPoints[$key]['time'] - $this->breakPoints[$i - 1]['time'];
				if($r > 0.0001){
					return $r;
				} else {
					return 0;
				}
			}
		}
	}


	/**
	*	Affiche la trace de benchmark
	*	@param Boolean $exit Définit si oui ou non le programme doit s'arrêter
	*/
	public function printTrace($exit = false){
		echo '<meta charset="utf-8"></meta>';

		foreach ($this->breakPoints as $key => $break){
			if(!is_float($break['time'])){
				echo $break['time'];
			} else {

				if($this->getElapsedTime($key) == 0){
					echo 'Temps écoulé négligeable<br />';
				} else {
					if($this->getElapsedTime($key) > 0.03){
						echo 'Temps écoulé : <span style="color:red">'.$this->getElapsedTime($key).'</span> <br />';
					} elseif($this->getElapsedTime($key) < 0.03 && $this->getElapsedTime($key) > 0.005){
						echo 'Temps écoulé : <span style="color:orange">'.$this->getElapsedTime($key).'</span> <br />';
					} else {
						echo '[ '.$this->getElapsedTime($key).' ]';
					}
				}
			}
			echo $break['msg'].'<br />';
		}
		if($exit) die();
	}

}

?>