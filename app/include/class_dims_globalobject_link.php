<?php
require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';

/**
 * Description of dims_globalobject_link
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
class dims_globalobject_link extends dims_data_object {

	function __construct() {
		parent::dims_data_object('dims_globalobject_link');
	}

	public static $GET_FROM_TO = 0 ;
	public static $GET_TO_FROM = 1 ;

	/**
	 * Le résultat est triés en fonction de l'id globalobject de l'objet lié à
	 * l'objet origine et à l'action ayant lié les objets dans un tableau à double entrée
	 * mon_tab[id_globalobject][id_action]
	 * @param type $tab_globalobject
	 * @param type $id_globalobject_origine
	 * @param type $getting_type : Variable de classe statique.
	 * @param type $db
	 * @return dims_globalobject_link
	 */
	public static function getLinkFromListGO($tab_globalobject, $id_globalobject_origine, $getting_type ,$db){

		$tab_link = array();
		if(!empty($tab_globalobject)){
			$params = array();
			if($getting_type === dims_globalobject_link::$GET_TO_FROM){
				$sql_globalobject_link = "SELECT *
					FROM	dims_globalobject_link
					WHERE	id_globalobject_from IN (".$db->getParamsFromArray($tab_globalobject, 'idglobalobjectto', $params).")
					AND id_globalobject_to = :idglobalobjectorigin
					";
			}else{
				$sql_globalobject_link = "SELECT *
					FROM	dims_globalobject_link
					WHERE	id_globalobject_to IN (".$db->getParamsFromArray($tab_globalobject, 'idglobalobjectto', $params).")
						AND id_globalobject_from = :idglobalobjectorigin
					";
			}
			$params[':idglobalobjectorigin'] = array('type' => PDO::PARAM_INT, 'value' => $id_globalobject_origine);

			$res_link = $db->query($sql_globalobject_link, $params);
			while($row_link = $db->fetchrow($res_link)){
				$link = new dims_globalobject_link();
				$link->fields = $row_link;
				if($getting_type === dims_globalobject_link::$GET_TO_FROM){
					$tab_link[$link->getId_globalobject_from()][$link->getIdAction()] = $link ;
				}else{
					$tab_link[$link->getId_globalobject_to()][$link->getIdAction()] = $link ;
				}
			}
		}
		return $tab_link ;
   }

   public static function addLink($id_module_type_from,
		   $id_globalobject_from,
		   $id_workspace_from,
		   $link_type,
		   $id_action,
		   $id_module_type_to,
		   $id_globalobject_to,
		   $id_workspace_to,
		   $timestp = 0){

	   if($id_globalobject_from == null
					|| $id_module_type_from == null
					|| $id_workspace_from == null
					|| $link_type == null
					|| $id_globalobject_to == null
					|| $id_module_type_to == null
					|| $id_workspace_to == null
					|| $id_action == null
					){
				//TODO ERROR insertion impossible mauvais parametre
		}else{
		   $link = new dims_globalobject_link();
		   $link->setIdModuleTypeFrom($id_module_type_from);
		   $link->setIdGlobalobjectFrom($id_globalobject_from);
		   $link->setIdWorkspaceFrom($id_workspace_from);
		   $link->setLinkType($link_type);
		   $link->setIdAction($id_action);
		   $link->setIdModuleTypeTo($id_module_type_to);
		   $link->setIdGlobalobjectTo($id_globalobject_to);
		   $link->setIdWorkspaceTo($id_workspace_to);
		   $link->setTimestp($timestp);
		   $link->save();
		}
   }

   public function getId_globalobject_from(){
	   if(isset($this->fields['id_globalobject_from'])){
		   return $this->fields['id_globalobject_from'];
	   }else{
		   //todo error
		   return null;
	   }
   }

   public function getId_globalobject_to(){
	   if(isset($this->fields['id_globalobject_to'])){
		   return $this->fields['id_globalobject_to'];
	   }else{
		   //todo error
		   return null;
	   }
   }

   public function getType(){
	   if(isset($this->fields['link_type'])){
		   return $this->fields['link_type'];
	   }else{
		   //todo error
		   return null;
	   }
   }

   public function getIdAction(){
	   if(isset($this->fields['id_action'])){
		   return $this->fields['id_action'];
	   }else{
		   //todo error
		   return null;
	   }
   }

   private function setIdGlobalobjectFrom($id_globalobject_from){
	   $this->fields['id_globalobject_from'] = $id_globalobject_from;
   }

   private function setIdModuleTypeFrom($id_module_type_from){
	   $this->fields['id_module_type_from'] = $id_module_type_from;
   }


   private function setIdWorkspaceFrom($id_workspace_from){
	   $this->fields['id_workspace_from'] = $id_workspace_from;
   }


   private function setLinkType($link_type){
	   $this->fields['link_type'] = $link_type;
   }


   private function setIdAction($id_action){
	   $this->fields['id_action'] = $id_action;
   }


   private function setIdModuleTypeTo($id_module_type_to){
	   $this->fields['id_module_type_to'] = $id_module_type_to;
   }


   private function setIdGlobalobjectTo($id_globalobject_to){
	   $this->fields['id_globalobject_to'] = $id_globalobject_to;
   }


   private function setIdWorkspaceTo($id_workspace_to){
	   $this->fields['id_workspace_to'] = $id_workspace_to;
   }


   private function setTimestp($timestp = 0){
	   if($timestp != 0){
		   $this->fields['timestp'] = $timestp;
	   }else{
		   $this->fields['timestp'] = dims_createtimestamp();
	   }
   }
}
