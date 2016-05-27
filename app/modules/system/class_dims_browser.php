<?php
class dims_browser {

	private $children;	// tableau des fils du noeud courant
	private $data;		// données du noeud courant
	private $selected;	// indique si le noeud est selectionné parmi ceux du niveau courant
	private $depth;		// profondeur

	private $tpl_node; //tpl pour le noeud qui a des fils
	private $tpl_leaf; //tpl pour le noeud qui est une feuille de l'arborescence

	public function __construct() {
		$this->children = array();
		$this->data = array();
		$this->selected = false;
		$this->depth = 0;
	}

	// ajoute un fils a l'arborescence
	public function addChild($data) {
		$child = new dims_browser();
		$child->setData($data);
		$child->setDepth($this->getDepth() + 1);
		$this->children[$data['key']] = $child;
		return $child;
	}

	// supprime un fils du noeud courant
	public function removeChild($key) {
		unset($this->children[$key]);
	}

	// indique si le noeud est selectionné
	public function isSelected() {
		return $this->selected;
	}

	// indique si le noeud est selectionné
	public function setSelected($sel) {
		return $this->selected = $sel;
	}

	// renvoie le noeud selectionné
	public function getSelectedChild() {
		foreach ($this->children as $child) {
			if ($child->isSelected()) {
				return $child;
			}
		}
		return null;
	}

	// renvoie les données du noeud selectionné
	public function getSelectedChildData() {
		foreach ($this->getChildren() as $child) {
			if ($child->isSelected()) {
				return $child->getData();
			}
		}
		return null;
	}

	// indique si le noeud courant est la racine ou pas
	public function isRoot() {
		return $this->getDepth() == 0;
	}


	public function getData() {
		return $this->data;
	}
	public function setData($data) {
		$this->data = $data;
	}

	public function getChildren() {
		return $this->children;
	}
	// indique si le noeud a des fils ou pas
	public function hasChildren() {
		return sizeof($this->getChildren()) > 0;
	}
	public function getDepth() {
		return $this->depth;
	}
	public function setDepth($depth) {
		$this->depth = $depth;
	}

	/*
	 * Fonction récursive de rotation du browser afin d'envoyer en colonnes le browser au template
	 */
	private function recursiveDataRotation($brw, &$tab){
		foreach($brw->getChildren() as $child){
			$data = $child->getData();
			$tab[$child->getDepth()-1][$data['key']] = array(
				'data' => $child->getData() ,
				'selected' => $child->isSelected() ,
				'has_children' => $child->hasChildren()
				);
			if($child->hasChildren() && $child->isSelected()){
				$child->recursiveDataRotation($child, $tab);
			}
		}
	}

	public function setSpecificNodeTPL($tpl){
		$this->tpl_node = $tpl;
	}

	public function setSpecificLeafTPL($tpl){
		$this->tpl_leaf = $tpl;
	}

	public function getNodeTPL(){
		return $this->tpl_node;
	}

	public function getLeafTPL(){
		return $this->tpl_leaf;
	}


	public function display(){
		$browser = $this;

		if($this->hasChildren() && !is_null($this->getNodeTPL())) include $this->getNodeTPL();
		else if(!is_null($this->getLeafTPL())){
			include $this->getLeafTPL();
		}
	}

	/*
	 * Fonction calculant la profondeur max de l'arbre
	 */
	public static function getMaxDepth(dims_browser $browser, $cur_depth){
		foreach($browser->getChildren() as $child){
			if($child->hasChildren()) $cur_depth = dims_browser::getMaxDepth($child, $child->getDepth());
			else $cur_depth = $child->getDepth();
		}
		return $cur_depth;
	}
}
