<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	forms
* @access  	public
*/

class field extends dims_data_object {
	const TABLE_NAME = 'dims_mod_forms_field';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function save() {
		if ($this->fields['fieldname'] == '') $this->fields['fieldname'] = $this->fields['name'];
		$this->fields['fieldname'] = forms_createphysicalname($this->fields['fieldname']);
		if($this->isNew()){
			if($this->fields['position'] <= 0){
				$this->fields['position'] = count(field::find_by(array('id_forms'=>$this->get('id_forms'))))+1;
			}
			$db = dims::getInstance()->getDb();
			$sql = "SELECT 	*
					FROM 	".self::TABLE_NAME."
					WHERE 	position >= :pos
					AND 	id_forms = :idf";
			$params = array(
				':pos' => $this->get('position'),
				':idf' => $this->get('id_forms'),
			);
			$res = $db->query($sql,$params);
			while($r = $db->fetchrow($res)){
				$f = new field();
				$f->openFromResultSet($r);
				$f->fields['position'] ++;
				$f->save();
			}
		}
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		//update position
		$sql = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	position > :pos
				AND 	id_forms = :idf";
		$params = array(
			':pos' => $this->get('position'),
			':idf' => $this->get('id_forms'),
		);
		$res = $db->query($sql,$params);
		while($r = $db->fetchrow($res)){
			$f = new field();
			$f->openFromResultSet($r);
			$f->fields['position'] --;
			$f->save();
		}
		// DELETE REPLY
		$replys = reply_field::find_by(array('id_field'=>$this->get('id'),'id_forms'=>$this->get("id_forms")));
		foreach($replys as $r){
			$r->delete();
		}
		parent::delete();
	}

	public function upField(){
		$f = field::find_by(array('id_forms'=>$this->get('id_forms'), 'position'=>($this->get('position')+1)),null,1);
		if(!empty($f)){
			$f->fields['position']--;
			$f->save();
			$this->fields['position']++;
			$this->save();
		}
	}

	public function downField(){
		$f = field::find_by(array('id_forms'=>$this->get('id_forms'), 'position'=>($this->get('position')-1)),null,1);
		if(!empty($f)){
			$f->fields['position']++;
			$f->save();
			$this->fields['position']--;
			$this->save();
		}
	}

	public function getLabel($classes = "col-sm-2 control-label"){
		return '<label for="field_'.$this->get('id').'" class="'.$classes.'">'.$this->get('name').($this->get('option_needed')?" <span class=\"mandatory\">*</span>":"").'</label>';
	}
	public function getFields(Dims\form $form, $value = null, $reply_id = null){
		if(is_null($value)) $value = $this->get('defaultvalue');
		switch ($this->get('type')) {
			default:
			case 'text':
				$dom_extension = null;
				$style = "";
				switch ($this->get('format')) {
					case 'integer':
					case 'float':
						$revision = "number";
						break;
					case 'date':
						$style = 'style="width: 110px;"';
						$revision = "date_jj/mm/yyyy";
						break;
					case 'time':
						$revision = "heure_hh:mm";
						break;
					case 'email':
						$revision = "email";
						break;
					case 'color':
						$revision = "color";
						$style = 'style="width:95px;display:inline-block;" onchange="javascript:$(\'a\',$(this).parent()).css(\'color\',$(this).val());"';
						$dom_extension = '<a style="margin-top: -5px;margin-left:5px;'.(empty($value)?'':('color:'.$value.';')).'" href="javascript:void(0);" onclick="javascript:dims_colorpicker_open(\'field_'.$this->get('id').'\', event);" class="btn btn-small btn-default"><span class="glyphicon glyphicon-tint"></span></a>';
						break;
					case 'url':
					case 'string':
					default:
						$revision = "";
						break;
				}
				return $form->text_field(array(
					'name'						=> 'field_'.$this->get('id'),
					'id'						=> 'field_'.$this->get('id'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'" '.$style,
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
					'revision'					=> $revision,
					'dom_extension'				=> $dom_extension,
				));
				break;
			case 'textarea':
				return $form->textarea_field(array(
					'name'						=>  'field_'.$this->get('id'),
					'id'						=> 'field_'.$this->get('id'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
				));
				break;
			case 'checkbox':
				$checkboxes = array();
				$options = explode('||',$this->get('values'));
				$value = explode('||', $value);
				$xs = round(12/$this->get('cols'));
				foreach($options as $k => $o){
					$checkboxes[] = $form->checkbox_field(array(
						'name'						=>  'field_'.$this->get('id')."[]",
						'id'						=> 'field_'.$this->get('id')."_$k",
						'classes'					=> '',
						'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
						'value'						=> $o,
						'mandatory'					=> $this->get('option_needed'),
						'checked'					=> in_array($o, $value),
					)).$o;
				}
				return '<div class="row"><div class="col-xs-'.$xs.'"><div class="checkbox"><label>'.implode('</label></div></div><div class="col-xs-'.$xs.'"><div class="checkbox"><label>',$checkboxes).'</label></div></div></div>'.(!empty($this->fields['description'])?'<span class="help-block">'.nl2br($this->get('description')).'</span>':"");
				break;
			case 'radio':
				$checkboxes = array();
				$options = explode('||',$this->get('values'));
				$value = explode('||', $value);
				$xs = round(12/$this->get('cols'));
				foreach($options as $k => $o){
					$checkboxes[] = $form->radio_field(array(
						'name'						=>  'field_'.$this->get('id'),
						'id'						=> 'field_'.$this->get('id')."_$k",
						'classes'					=> '',
						'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
						'value'						=> $o,
						'mandatory'					=> $this->get('option_needed'),
						'checked'					=> in_array($o, $value),
					)).$o.((($k+1)%$this->get('cols') == 0)?'<br />':'');
				}
				return '<div class="row"><div class="col-xs-'.$xs.'"><div class="radio"><label>'.implode('</label></div></div><div class="col-xs-'.$xs.'"><div class="radio"><label>',$checkboxes).'</label></div></div></div>'.(!empty($this->fields['description'])?'<span class="help-block">'.nl2br($this->get('description')).'</span>':"");
				break;
			case 'select':
				$options = explode('||',$this->get('values'));
				array_unshift($options,"");
				return $form->select_field(array(
					'name'						=>  'field_'.$this->get('id'),
					'id'						=> 'field_'.$this->get('id'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
					'options'					=> array_combine($options, $options),
				)).(!empty($this->fields['description'])?'<span class="help-block">'.nl2br($this->get('description')).'</span>':"");
				break;
			case 'tablelink':
				return ""; /*$form->text_field(array(
					'name'						=> $this->get('fieldname'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
				));*/
				break;
			case 'file':
				$path = null;
				if(!empty($value)){
					$f = Dims\forms::find_by(array('id'=>$this->get('id_forms')),null,1);
					$path = _DIMS_PATHDATA.'forms-'.$f->get('id_module')._DIMS_SEP.$f->get('id')._DIMS_SEP.$reply_id._DIMS_SEP;
				}
				return $form->file_field(array(
					'name'						=>  'field_'.$this->get('id'),
					'id'						=> 'field_'.$this->get('id'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
				)).(!empty($this->fields['description'])?'<span class="help-block">'.nl2br($this->get('description')).'</span>':"").((!empty($value) && file_exists($path.$value))?(' <a href="'.'/data/forms-'.$f->get('id_module')._DIMS_SEP.$f->get('id')._DIMS_SEP.$reply_id._DIMS_SEP.$value.'" title="'.$value.'" alt="'.$value.'">'.$value.'</a>'):'');
				break;
			case 'autoincrement':
				return ""; /*$form->text_field(array(
					'name'						=> $this->get('fieldname'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
				));*/
				break;
			case 'color':
				return ""; /*$form->text_field(array(
					'name'						=> $this->get('fieldname'),
					'classes'					=> 'form-control',
					'additionnal_attributes' 	=> 'placeholder="'.str_replace('"','\\"',$this->get('description')).'"',
					'value'						=> $value,
					'mandatory'					=> $this->get('option_needed'),
				));*/
				break;
		}
	}
}
