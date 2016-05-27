<?php
/**
* @author   NETLOR CONCEPT
* @version  1.0
* @package  forms
* @access   public
*/

class reply extends dims_data_object {
	const TABLE_NAME = 'dims_mod_forms_reply';
    /**
    * Class constructor
    *
    * @param int $connection_id
    * @access public
    **/
    function __construct() {
        parent::dims_data_object(self::TABLE_NAME);
    }

    function delete() {
		// delete reply fields
		$replys = reply_field::find_by(array('id_reply'=>$this->get('id'),'id_forms'=>$this->get("id_forms")));
		foreach($replys as $r){
			$r->delete();
		}
		parent::delete();
    }

    public function getFields($obj = true){
    	$rf = reply_field::find_by(array('id_reply'=>$this->get('id'),'id_forms'=>$this->get("id_forms")), " ORDER BY id_field ");
    	$lst = array();
    	if($obj){
	    	foreach($rf as $r){
	    		$lst[$r->get('id_field')] = $r;
	    	}
	    }else{
	    	foreach($rf as $r){
	    		$lst[$r->get('id_field')] = $r->get('value');
	    	}
	    }
    	return $lst;
    }

    public function sendEmail($isNew = true){
    	$form = forms::find_by(array('id'=>$this->get('id_forms')),null,1);
    	if(!empty($form)){
    		$lstValues = $this->getFields();
    		$lstFields = $form->getAllFields();

    		$email_array = array();
			$email_array['Formulaire']['Titre'] = $form->get('label');
    		if($isNew){
				$email_array['Formulaire']['Operation'] = 'Nouvel Enregistrement';
    		}else{
    			$email_array['Formulaire']['Operation'] = 'Modification d\'Enregistrement';
    		}

    		if($form->get('option_displaydate')){
    			$dd = dims_timestp2local($this->get('date_validation'));
				$email_array['Formulaire']['Date'] = $dd['date']." ".$dd['time'];
			}

			if($form->get('option_displayip')){
				$email_array['Formulaire']['Adresse IP'] = $this->get('ip');
			}

			foreach($lstFields as $f){
				if(isset($lstValues[$f->get('id')])){
					if($f->get('type') == 'file'){
						$path = _DIMS_PATHDATA.'forms-'.$form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$this->get('id')._DIMS_SEP;
						if(isset($lstValues[$f->get('id')]) && file_exists($path.$lstValues[$f->get('id')]->get('value'))){
							$lk = "http".(dims::getInstance()->getSsl()?'s':'')."://".$_SERVER['SERVER_NAME']."/data/forms-".$form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$this->get('id')._DIMS_SEP.$lstValues[$f->get('id')]->get('value');
							$email_array['Contenu']["(".$f->get('id').") ".$f->get('name')] = '<a href="'.$lk.'" alt="'.$lstValues[$f->get('id')]->get('value').'" title="'.$lstValues[$f->get('id')]->get('value').'">'.$lstValues[$f->get('id')]->get('value').'</a>';
						}else{
							$email_array['Contenu']["(".$f->get('id').") ".$f->get('name')] = '';
						}
					}else{
						$email_array['Contenu']["(".$f->get('id').") ".$f->get('name')] = nl2br($lstValues[$f->get('id')]->get('value'));
					}
				}
			}

			$from = $to = array();
			$list_email = explode(';',$form->get('email'));
			foreach($list_email as $email){
				$to[] = array('name' => $email, 'address' => $email);
			}
			$sender = $form->get('sender');
			if(empty($sender)){
				$w = workspace::find_by(array('id'=>$form->get('id_workspace')),null,1);
				$from[] = array('name'=>$w->get('email_noreply'),'address'=>$w->get('email_noreply'));
			}else{
				$from[] = array('name'=>$sender,'address'=>$sender);
			}
			dims_send_form(
				$from, 
				$to, 
				$form->get('label'), 
				$email_array
			);
		}
	}
}
