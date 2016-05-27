<?php
class gescom_workflow_step extends dims_data_object{
	const TABLE_NAME = 'dims_gescom_workflow_step';

	const _STATE_DISABLED = 0;
	const _STATE_ENABLED = 1;

	const _TYPE_WAITING = 0;
	const _TYPE_FINISHED = 1;
	const _TYPE_CANCELLED = 2;

	public function __construct(){
		parent::setMatriceStandalone(true);
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function save($light = false){
		if($this->isNew()){
			$nbElem = count(self::find_by(array('id_workflow'=>$this->get('id_workflow'))))+1;
			if($this->get('position') <= 0 || $this->get('position') > $nbElem){
				$this->set('position',$nbElem);
			}else{
				$db = dims::getInstance()->getDb();
				$sel = "SELECT 		*
						FROM 		".self::TABLE_NAME."
						WHERE 		id_workflow = :idw
						AND 		position >= :pos
						ORDER BY 	position";
				$params = array(
					':idw' => $this->get('id_workflow'),
					':pos' => $this->get('position'),
				);
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$gw = new self();
					$gw->openFromResultSet($r);
					$gw->fields['position'] ++;
					$gw->save(true);
				}
			}
		}elseif(!$light){
			$changes = $this->changes();
			if(!empty($changes['position'])){
				$before = $changes['position']['before'];
				$after = $changes['position']['after'];
				$db = dims::getInstance()->getDb();
				if($before < $after){
					$sel = "SELECT 		*
							FROM 		".self::TABLE_NAME."
							WHERE 		id_workflow = :idw
							AND 		position > :pos1
							AND 		position <= :pos2
							ORDER BY 	position";
					$params = array(
						':idw' => $this->get('id_workflow'),
						':pos1' => $before,
						':pos2' => $after,
					);
					$res = $db->query($sel,$params);
					while($r = $db->fetchrow($res)){
						$gw = new self();
						$gw->openFromResultSet($r);
						$gw->fields['position'] --;
						$gw->save(true);
					}
				}elseif($before > $after){
					$sel = "SELECT 		*
							FROM 		".self::TABLE_NAME."
							WHERE 		id_workflow = :idw
							AND 		position < :pos1
							AND 		position >= :pos2
							ORDER BY 	position";
					$params = array(
						':idw' => $this->get('id_workflow'),
						':pos1' => $before,
						':pos2' => $after,
					);
					$res = $db->query($sel,$params);
					while($r = $db->fetchrow($res)){
						$gw = new self();
						$gw->openFromResultSet($r);
						$gw->fields['position'] ++;
						$gw->save(true);
					}
				}
			}
		}
		return parent::save();
	}

	public function delete(){
		// TODO : tester si cette étape est utilisée

		$this->set('state',self::_STATE_DISABLED);
		$this->save();
	}
}
