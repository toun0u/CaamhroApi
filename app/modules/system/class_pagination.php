<?php
/**
* @author 	NETLOR CONCEPT
* @version  1.0 by Sylvain 2008-12-05
* @package  news
* @access  	public
*/

class pagination extends dims_data_object  {

	public $limite_key	= 30;			//Nombre d'�l�ments par page
	public $maxpagevisibles = 10;		//définit le nombre de pages visibles dans le show nb_pages - doit être pair

	public $nombre_key;
	public $nombre_page;
	public $sql_debut;
	public $sql_fin;
	public $page_courant;

	//Variable d'affichage
	public $nom_get;
	public $url_return_defaut	= false;
	public $rewrite				= false;
	public $name_get_page		= "page";
	public $precedent			= "&lt; ";
	public $suivant				= "&gt;";
	public $debut			= "&lt;&lt; ";
	public $fin				= "&gt;&gt;";
	public $title				= "Page [PAGE]";
	public $force_zero			= true;

	private $nombre_page_array	= array();

	public $isPageLimited		= false;

	//Structure la pagination
	public function liste_page($nombre_key){
		$this->nombre_key	= $nombre_key;
		$this->nombre_page	= ceil($this->nombre_key / $this->limite_key) - 1;

		//Cr�er la clef de d�but et de fin pour l'affichage des stages
		if($this->nombre_key > $this->limite_key){
			if($this->page_courant == $this->nombre_page){
				$this->sql_debut	= round($this->limite_key * $this->page_courant);
				$this->sql_fin		= $this->nombre_key;
			}else{
				if($this->page_courant > $this->nombre_page){
					$this->page_courant = $this->nombre_page;
				}
				$this->sql_debut	= round($this->limite_key * $this->page_courant);
				$this->sql_fin		= round($this->limite_key * $this->page_courant) + $this->limite_key;
			}
			/*else{
				$this->sql_debut	= 0;
				$this->sql_fin		= $this->limite_key;
			}*/
		}else{
			$this->sql_debut	= 0;
			$this->sql_fin		= $this->nombre_key;
		}

		for($i=0; $i <= $this->nombre_page; $i++){
			$this->nombre_page_array[]	= $i+1;
		}
	}

	/*
	 * Fonction qui permet de setter les params de pagination
	*/
	public function setPaginationParams($limit_elem=5, $maxpages = 10, $zero=true, $deb='&lt;&lt;', $end='&gt;&gt;', $prec='&lt;', $suiv='&gt;', $page_name='page')
	{
		//$this->name_get_page 	= 'page_'.$this->fields['id'];
		$this->name_get_page	= $page_name;
		$this->limite_key 		= $limit_elem;
		$this->maxpagevisibles 	= $maxpages;
		$this->precedent		= $prec;
		$this->suivant			= $suiv;
		$this->debut			= $deb;
		$this->fin				= $end;
		$this->force_zero		= $zero;
		$this->title			= "Page [PAGE]";
	}

	/*
	 * Limite ou non la gestion de la pagination
	*/
	public function setPageLimited($limit)
	{
		$this->isPageLimited = $limit;
	}


	//!\\ Ancien version en prod pour pap et module partenaire //!\\
	//Permet d'afficher dans l'url la pagination si la page est diff�rente de 0
	public function url_pagination($num_page, $nom_get="page", $url_return_defaut=false, $rewrite=false){
		if ($this->force_zero && $num_page == 0) {
			return "&".$nom_get."=".$num_page;//$url_return_defaut;
		}else{
			if ($rewrite == true) {
				return $nom_get.$num_page;
			}else{
				//die($nom_get);
				return "&".$nom_get."=".$num_page;
			}
		}
	}
	//Fin

	//Permet d'afficher dans l'url la pagination si la page est diff�rente de 0
	public function url_paginations($num_page){
		if ($num_page == 0) {
			return "&".$this->nom_get."=".$num_page;//$this->url_return_defaut;
		}else{
			if ($this->rewrite == true) {
				return $this->nom_get.$num_page;
			}else{
				return "&".$this->nom_get."=".$num_page;
			}
		}
	}

	//Affichage de la pagination en mode graphique
	public function show_pagination(){
		$url_pagination	= htmlspecialchars(courante_page($this->name_get_page));
		$c=0;
		if (strpos($url_pagination,"?")==0) $url_pagination.="?1=1";

		if (count($this->nombre_page_array) > 1){
			echo '<div class="pagination">';
			if (!$this->page_courant) {

				foreach($this->nombre_page_array as $key => $value){
					$c++;
					if ($c<=$this->maxpagevisibles) {
						if($key != $this->page_courant){
							echo '<a href="'.$url_pagination.$this->url_pagination($key, $this->name_get_page).'" title="'.$this->affiche_title($key+1).'">'.$value.'</a>';
						}else{
							echo '<span>'.$value.'</span>';
						}
					}
				}

				echo '<a href="'.$url_pagination.$this->url_pagination($this->page_courant+1, $this->name_get_page).'" title="'.$this->affiche_title($this->page_courant+2).'">'.$this->suivant.'</a>';
				if(!empty($this->fin))echo '<a href="'.$url_pagination.$this->url_pagination($this->nombre_page, $this->name_get_page).'" title="'.$this->affiche_title($this->nombre_page+1).'">'.$this->fin.' ('.($this->nombre_page+1).') </a>';

			} elseif ($this->page_courant == count($this->nombre_page_array)-1) {
				// cas on est � la fin
				if(!empty($this->debut)) echo '<a href="'.$url_pagination.$this->url_pagination(0, $this->name_get_page).'" title="'.$this->affiche_title(1).'">'.$this->debut.' </a>';
				echo '<a href="'.$url_pagination.$this->url_pagination($this->page_courant-1, $this->name_get_page).'" title="'.$this->affiche_title($this->page_courant-1+1).'">'.$this->precedent.' </a>';
				$valmin=$this->page_courant-$this->maxpagevisibles;

				foreach($this->nombre_page_array as $key => $value){
					if ($c>=$valmin) {
						if ($key != $this->page_courant){
							echo '<a href="'.$url_pagination.$this->url_pagination($key, $this->name_get_page).'" title="'.$this->affiche_title($key+1).'">'.$value.'</a>';
						}else{
							echo '<span>'.$value.'</span>';
						}
					}
					$c++;
				}

			}
			else {
				if(!empty($this->debut)) echo '<a href="'.$url_pagination.$this->url_pagination(0, $this->name_get_page).'" title="'.$this->affiche_title(1).'">'.$this->debut.' </a>';
				echo '<a href="'.$url_pagination.$this->url_pagination($this->page_courant-1, $this->name_get_page).'" title="'.$this->affiche_title($this->page_courant).'">'.$this->precedent.'</a>';
				if ($this->nombre_page_array>$this->maxpagevisibles) {
					$valmin=$this->page_courant-($this->maxpagevisibles/2);
					$valmax=$this->page_courant+($this->maxpagevisibles/2);
				}
				else {
					$valmin=$this->page_courant;
					$valmax=$this->nombre_page_array;
				}

				foreach($this->nombre_page_array as $key => $value){
					if ($c>=$valmin && $c<=$valmax) {
						if($key != $this->page_courant){
							echo '<a href="'.$url_pagination.$this->url_pagination($key, $this->name_get_page).'" title="'.$this->affiche_title($key+1).'">'.$value.'</a>';
						}else{
							echo '<span>'.$value.'</span>';
						}
					}
					$c++;
				}
				echo '<a href="'.$url_pagination.$this->url_pagination($this->page_courant+1, $this->name_get_page).'" title="'.$this->affiche_title($this->page_courant+2).'">'.$this->suivant.'</a>';
				if(!empty($this->fin))echo '<a href="'.$url_pagination.$this->url_pagination($this->nombre_page, $this->name_get_page).'" title="'.$this->affiche_title($this->nombre_page+1).'">'.$this->fin.' ('.($this->nombre_page+1).') </a>';
			}
			echo '</div>';
		}
	}

	//retourne un tableau des pages, liens associés et les titles pour pouvoir smartiser le tout
	public function getPagination(){
		$url_pagination	= courante_page($this->name_get_page);
		$c=0;
		$cpt=0;//permet l'index du tableau de pages
		$retval = '';
		if (strpos($url_pagination,"?")==0) $url_pagination.="?1=1";

		$prepared_smarties = array();
		if (count($this->nombre_page_array) > 1){
			if (!$this->page_courant) {
				foreach($this->nombre_page_array as $key => $value){
					$c++;
					if ($c<=$this->maxpagevisibles) {
						if($key != $this->page_courant){
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($key, $this->name_get_page);
							$prepared_smarties[$cpt]['title'] = $this->affiche_title($key+1);
						}else{
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = '';
							$prepared_smarties[$cpt]['title'] = '';
						}
					}
					$cpt++;
				}

				$prepared_smarties[$cpt]['label'] = $this->suivant;
				$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->page_courant+1, $this->name_get_page);
				$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->page_courant+2);

				if(!empty($this->fin)){
					$cpt++;
					$prepared_smarties[$cpt]['label'] = $this->fin.' ('.($this->nombre_page+1).')';
					$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->nombre_page, $this->name_get_page);
					$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->nombre_page+1);
				}
			}
			elseif ($this->page_courant == count($this->nombre_page_array)-1) {
				// cas on est � la fin
				if(!empty($this->debut))
				{
					$prepared_smarties[$cpt]['label'] = $this->debut;
					$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination(0, $this->name_get_page);
					$prepared_smarties[$cpt]['title'] = $this->affiche_title(1);
					$cpt++;//si on est dans le if il faut que le suivant passe à 1, mais si non, il faut que le suivant démarre à 0
				}
				$prepared_smarties[$cpt]['label'] = $this->precedent;
				$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->page_courant-1, $this->name_get_page);
				$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->page_courant);
				$valmin=$this->page_courant-$this->maxpagevisibles;
				$cpt++;
				foreach($this->nombre_page_array as $key => $value){
					if ($c>=$valmin) {
						if ($key != $this->page_courant) {
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($key, $this->name_get_page);
							$prepared_smarties[$cpt]['title'] = $this->affiche_title($key+1);
						}else{
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = '';
							$prepared_smarties[$cpt]['title'] = '';
						}
					}
					$c++;
					$cpt++;
				}
			}
			else {
				if(!empty($this->debut))
				{
					$prepared_smarties[$cpt]['label'] = $this->debut;
					$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination(0, $this->name_get_page);
					$prepared_smarties[$cpt]['title'] = $this->affiche_title(1);
					$cpt++;//si on est dans le if il faut que le suivant passe à 1, mais si non, il faut que le suivant démarre à 0
				}
				$prepared_smarties[$cpt]['label'] = $this->precedent;
				$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->page_courant-1, $this->name_get_page);
				$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->page_courant);
				if ($this->nombre_page_array>$this->maxpagevisibles) {
					$valmin=$this->page_courant - ($this->maxpagevisibles/2);
					$valmax=$this->page_courant + ($this->maxpagevisibles/2);
				}
				else {
					$valmin=$this->page_courant;
					$valmax=$this->nombre_page_array;
				}
				$cpt++;
				foreach($this->nombre_page_array as $key => $value){
					if ($c>=$valmin && $c<=$valmax) {
						if($key != $this->page_courant){
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($key, $this->name_get_page);
							$prepared_smarties[$cpt]['title'] = $this->affiche_title($key+1);
						}else{
							$prepared_smarties[$cpt]['label'] = $value;
							$prepared_smarties[$cpt]['url'] = '';
							$prepared_smarties[$cpt]['title'] = '';
						}
					}
					$c++;
					$cpt++;
				}
				$prepared_smarties[$cpt]['label'] = $this->suivant;
				$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->page_courant+1, $this->name_get_page);
				$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->page_courant+2);
				if(!empty($this->fin)){
					$cpt++;
					$prepared_smarties[$cpt]['label'] = $this->fin.' ('.($this->nombre_page+1).')';
					$prepared_smarties[$cpt]['url'] = $url_pagination.$this->url_pagination($this->nombre_page, $this->name_get_page);
					$prepared_smarties[$cpt]['title'] = $this->affiche_title($this->nombre_page+1);
				}
			}
		}
		return $prepared_smarties;
	}

	private function affiche_title($page){
		return str_replace("[PAGE]", $page, $this->title);
	}
}
?>
