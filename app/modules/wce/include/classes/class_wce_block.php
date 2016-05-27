<?
/*
 *      Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

class wce_block extends dims_data_object {
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_WCE_ARTICLE_BLOCK;
	const TABLE_NAME = 'dims_mod_wce_article_block';
	private $wce_site = null;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id','id_lang');
		$this->nbelement = 19;//dépend beaucoup
	}

	public function open() {
		$id=0;
		$id_lang=0;
		foreach(func_get_args() as $i => $val){
			switch($i){
				case 0:
					$id = $val;
					break;
				case 1:
					$id_lang = $val;
					break;
			}
		}
		if ($id_lang=='' || $id_lang<=0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
			if (!is_object($this->wce_site)){
				if (!isset($_SESSION['dims']['moduleid']) || (!(isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0))){
					$lstwcemods=dims::getInstance()->getWceModules(true);
					foreach($lstwcemods as $wce){
						$this->wce_site= new wce_site ($this->db,$wce);
						$id_lang = $this->wce_site->getDefaultLanguage();
						parent::open($id,$id_lang);
						if(isset($this->fields['id_module']))
							break;
					}
				}
				else{
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
					$id_lang = $this->wce_site->getDefaultLanguage();
					parent::open($id,$id_lang);
				}
			}else{
				$id_lang = $this->wce_site->getDefaultLanguage();
				parent::open($id,$id_lang);
			}
		}else
			parent::open($id,$id_lang);
	}

	function save() {
		if ( empty($this->fields['id_lang']) || !is_numeric($this->fields['id_lang']) || $this->fields['id_lang'] <= 0 || $this->fields['id_lang'] == '') {
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
            $this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
			$this->fields['id_lang'] = $this->wce_site->getDefaultLanguage();
			//echo $this->wce_site->getDefaultLanguage();
		}

		// Il faut publier les images si présentes dans le content
		$regexp = '/src="('.str_replace(array('.','/'),array('\.','\/'),_DIMS_WEBPATHDATA).'(doc-[0-9]*\/[0-9]{8}\/)([0-9]+_[0-9]*\.[a-zA-Z]{2,3}))"/';
		$lstDoc = array();
		for($i=1;$i<=19;$i++){
			if (isset($this->fields["content$i"])) {
				if(preg_match_all($regexp, $this->fields["content$i"], $matches) !== false){
					for($o=0;$o<count($matches[0]);$o++){
						$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
														'path'=>$matches[2][$o],
														'filename'=>$matches[3][$o]);
					}
				}
			}

			if (isset($this->fields["draftcontent$i"])) {
				if(preg_match_all($regexp, $this->fields["draftcontent$i"], $matches) !== false){
					for($o=0;$o<count($matches[0]);$o++){
						$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
														'path'=>$matches[2][$o],
														'filename'=>$matches[3][$o]);
					}
				}
			}
		}
		foreach($lstDoc as $doc){
			if(!file_exists(DIMS_ROOT_PATH."www/data/".$doc['path']))
				dims_makedir(DIMS_ROOT_PATH."www/data/".$doc['path']);
			if(file_exists(DIMS_ROOT_PATH."data/".$doc['path'].$doc['filename']) && !file_exists(DIMS_ROOT_PATH."www/data/".$doc['path'].$doc['filename']))
				copy(DIMS_ROOT_PATH."data/".$doc['path'].$doc['filename'],DIMS_ROOT_PATH."www/data/".$doc['path'].$doc['filename']);
		}

		parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}
	function settitle(){
		$this->title = $this->fields['title'];
	}

	function delete() {

		$params=array();
		$params[':position']['value']=$this->fields['position'];
		$params[':position']['type']=PDO::PARAM_INT;
		$params[':id_article']['value']=$this->fields['id_article'];
		$params[':id_article']['type']=PDO::PARAM_INT;
		$params[':id_lang']['value']=$this->fields['id_lang'];
		$params[':id_lang']['type']=PDO::PARAM_INT;

		$res=$this->db->query("UPDATE {$this->tablename} SET position = position - 1 WHERE position > :position AND id_article = :id_article AND id_lang = :id_lang",$params);

		$params=array();
		$params[':blockid']['value']=$this->fields['id'];
		$params[':blockid']['type']=PDO::PARAM_INT;
		$params[':id_lang']['value']=$this->fields['id_lang'];
		$params[':id_lang']['type']=PDO::PARAM_INT;
		$rver=$this->db->query("delete from dims_mod_wce_article_block_version where blockid=:blockid AND id_lang = :id_lang",$params);

		parent::delete();
	}

	public function isModify(){
		$result = false;
		for ($i = 1; $i <= $this->nbelement; $i++) {
			if ($this->fields['draftcontent' . $i] != $this->fields['content' . $i]) {
				$result = true;
				break;
			}
		}
		return $result;
	}

	public function setUpToDate($val){
		$this->fields['uptodate'] = $val;
	}

	public function isUptodate(){
		return $this->fields['uptodate'];
	}

	function move($sens,$position) {
		if ($position>1 && $sens==0) {
			// on va a gauche
			$temp=$this->fields['draftcontent'.($position-1)];
			$this->fields['draftcontent'.($position-1)]=$this->fields['draftcontent'.$position];
			$this->fields['draftcontent'.$position]=$temp;
			$this->save();
		}
		elseif ($sens) {
			$temp=$this->fields['draftcontent'.($position+1)];
			$this->fields['draftcontent'.($position+1)]=$this->fields['draftcontent'.$position];
			$this->fields['draftcontent'.$position]=$temp;
			$this->save();
		}
	}

	public function valide(){
		for($i=1; $i <= 19;$i++){
			$this->fields["content$i"] = $this->fields["draftcontent$i"];
		}
		$this->fields['uptodate'] = 1;
		$this->save();
                //dims_print_r($this->fields);die();
	}

	public function getInternalLinks(){
		$lst = array();
		for ($i=1; $i<=19;$i++){
			$matches = array();
			if(preg_match_all('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i',$this->fields["content$i"],$matches)){
				if (isset($matches[1]))
					foreach($matches[1] as $match)
						if (isset($match))
							$lst[$match] = $match;
			}
			$matches = array();
			if(preg_match_all('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i',$this->fields["draftcontent$i"],$matches)){
				if (isset($matches[1]))
					foreach($matches[1] as $match)
						if (isset($match))
							$lst[$match] = $match;
			}
		}
		return $lst;
	}

	public function replaceLinks($from, $to){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = preg_replace('/( href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=)'.$from.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\'])/i',"$1<DIMS_TO_REPLACE>$2$5",$this->fields["content$i"]);
			$this->fields["content$i"] = str_replace("<DIMS_TO_REPLACE>",$to,$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = preg_replace('/( href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=)'.$from.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\'])/i',"$1<DIMS_TO_REPLACE>$2$5",$this->fields["draftcontent$i"]);
			$this->fields["draftcontent$i"] = str_replace("<DIMS_TO_REPLACE>",$to,$this->fields["draftcontent$i"]);
		}
		$this->save();
	}

	public function deleteLinks($to){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = preg_replace('/<a.* href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid='.$to.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\']).*>(.*)<\/a>/iU',"$5",$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = preg_replace('/<a.* href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid='.$to.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\']).*>(.*)<\/a>/iU',"$5",$this->fields["draftcontent$i"]);
		}
		$this->save();
	}

	public function str_replace($search,$replace){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = str_replace($search,$replace,$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = str_replace($search,$replace,$this->fields["draftcontent$i"]);
		}
		$this->save();
	}
}
?>
