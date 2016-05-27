<?php

class block_user extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function block_user() {
		parent::dims_data_object('dims_param_block_user','id_user','id_module','id_workspace');
	}

	function open2($iduser,$idmodule,$idworkspace) {
		// verification de l'existance d'une date dans la base
		$db = dims::getInstance()->getDb();
		$select ="SELECT * from dims_param_block_user where id_user= :iduser and id_module= :idmodule and id_workspace= :idworkspace";
		$res=$db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $iduser),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $idworkspace),
		));

		if ($db->numrows($res)==0)  {
			// on crée la ligne
			$bu = new block_user();
			$bu->fields['id_user']=$iduser;
			$bu->fields['id_module']=$idmodule;
			$bu->fields['id_workspace']=$idworkspace;
			$bu->fields['date_lastvalidate']=dims_createtimestamp();
			$bu->fields['id_column']=1;
			$bu->fields['position']=1;
			$bu->fields['state']=1;
			$bu->save();
		}
		//parent::open($iduser,$idmodule,$idworkspace);
	}

	function save2($idcolumn=0,$position=0) {
		$db = dims::getInstance()->getDb();

		if (!$this->new) {
			if ($idcolumn>0 and $position>0) {
				// process diff column and position
				if ($this->fields["idcolumn"]!=$idcolumn) {
					// update old colummn
					$select="UPDATE dims_param_block_user set position=position-1 where id_user=iduser and id_workspace= :idworkspace and idcolumn= :idcolumn and position > :position";
					$res=$db->query($select, array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
						':idcolumn' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['idcolumn']),
						':position' => array('type' => PDO::PARAM_INT, 'value' => $i),
					));

					// update new column
					$select="UPDATE dims_param_block_user set position=position+1 where id_user= :iduser and id_workspace= :idworkspace and idcolumn= :idcolumn and position >= :position";
					$res=$db->query($select, array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
						':idcolumn' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['idcolumn']),
						':position' => array('type' => PDO::PARAM_INT, 'value' => $i),
					));
				}
				else
				{
					if ($this->fields["position"]!=$position)
					{
						$dif=$position-$this->fields["position"];
						if ($this->fields["position"]!=0)
						{
							$pas=$pas/abs($pas);
							$dec=$pas*(-1);

							for ($i=$ancien_pos+$pas;$i!=$page->fields['position']+$pas;$i+=$pas)
							{
								$select="UPDATE dims_param_block_user set position=position+( :dec ) where id_user= :iduser and id_workspace= :idworkspace and idcolumn= :idcolumn and position= :position";
								$res=$db->query($select, array(
									':dec' => array('type' => PDO::PARAM_INT, 'value' => $dec),
									':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
									':idcolumn' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['idcolumn']),
									':position' => array('type' => PDO::PARAM_INT, 'value' => $i),
								));
							}
						}
					}
				}
				$this->fields["idcolumn"]=$idcolumn;
				$this->fields["position"]=$position;
			}
		}
		return(parent::save());
	}

	function delete($id_object="") {
		$db = dims::getInstance()->getDb();

		// update higher position modules
		$sql="UPDATE dims_param_block_user set position=position-1
				where id_workspace= :idworkspace
				and id_user= :iduser
				and idcolumn= :idcolumn
				and position> :position";

		$res=$db->query($sql, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
				':idcolumn' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_column']),
				':position' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
			));

		parent::delete();

	}

}
