<?php
function familles_aplat($root){
	$alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$res = array();
	$res['dims_nan'] = dims_constant::getVal('PLEASE_SELECT_A_FAMILY');
	$in_progress = array();
	if( isset($root) ){
		$in_progress[] = $root;
		while( ! empty($in_progress) ){
			$fam = array_shift($in_progress);
			$text = '';

			// Dans la liste des familles, on rajoute des lettres pour le 1er niveau de profondeur
			// puis des chiffres pour les suivants
			$parent = $root;
			$parents = explode(';', $fam->fields['parents']);

			if ($fam->fields['depth'] == 2) {
				$text .= $alpha[($fam->fields['position']-1)].' - ';
			}
			foreach ($parents as $id_parent) {
				if ($id_parent > 1) {
					if (isset($parent->descendance[$id_parent])) {
						if ($parent->descendance[$id_parent]->fields['depth'] == 2) {
							$text .= $alpha[($parent->descendance[$id_parent]->fields['position']-1)];
						}
						else {
							$text .= ' - '.$parent->descendance[$id_parent]->fields['position'];
						}
					}
					if (isset($parent->descendance[$id_parent])) {
						$parent = $parent->descendance[$id_parent];
					}
				}
			}
			if ($fam->fields['depth'] > 2) {
				$text .= ' - '.$fam->fields['position'].' - ';
			}

			$res[$fam->fields['id']] =  $text.$fam->fields['label'];
			if(isset($fam->descendance)){
				$children = array_reverse($fam->descendance, true);
				foreach($children as $child){
					array_unshift($in_progress, $child);
				}
			}
		}
	}

	return $res;
}

function copy_articles($lst, $idFam = null){
	$clipboard = &get_sessparam($_SESSION['cata']['articles']['clipboard'], array() );
	if(!empty($lst)){
		$cut = &get_sessparam($_SESSION['cata']['articles']['clipboard_cut'], array());
		if(!is_null($idFam) && $idFam > 0){
			foreach($lst as $id){
				$cut[$id] = $idFam;
				$clipboard[$id] = $id; #comme ça il ne peut y avoir qu'une seule fois un id article
			}
		}else{
			foreach($lst as $id){
				$clipboard[$id] = $id;#comme ça il ne peut y avoir qu'une seule fois un id article
				if(isset($cut[$id]))
					unset($cut[$id]); // dans le cas ou on à couper et ensuite coller le mm article
			}
		}
	}
	return $clipboard;
}

function get_clipboard(){
	$clipboard = &get_sessparam($_SESSION['cata']['articles']['clipboard'], array() );
	return $clipboard;
}

function in_clipboard($id){
	$clipboard = &get_sessparam($_SESSION['cata']['articles']['clipboard'], array() );
	return isset($clipboard[$id]);
}

function del_clipboard_article($id){
	$clipboard = &get_sessparam($_SESSION['cata']['articles']['clipboard'], array() );
	if( isset($clipboard[$id]) ){
		unset($clipboard[$id]);
		$cut = &get_sessparam($_SESSION['cata']['articles']['clipboard_cut'], array());
		if(isset($cut[$id]))
			unset($cut[$id]);
		return true;
	}
	return false;
}

function isCutClipboard($id){
	return isset($_SESSION['cata']['articles']['clipboard_cut'][$id]) && $_SESSION['cata']['articles']['clipboard_cut'][$id] > 0;
}

function getCutClipboard($id){
	if(isCutClipboard($id))
		return $_SESSION['cata']['articles']['clipboard_cut'][$id];
	else
		return false;
}
function delCutClipboard(){
	unset($_SESSION['cata']['articles']['clipboard_cut']);
}
function empty_clipboard(){
	unset($_SESSION['cata']['articles']['clipboard']);
	delCutClipboard();
}


/* --------------------- Gestion des derniers articles ----*/
function store_lastarticle($id, $nb_elems){
	$last_articles = &get_sessparam($_SESSION['cata']['articles']['last_articles'], array() );
	if(in_array($id,$last_articles)){
		unset($last_articles[array_search($id, $last_articles)]);
	}elseif(count($last_articles) >= $nb_elems){
		array_splice($last_articles,$nb_elems-1);
	}
	array_unshift($last_articles,$id);
}

function get_lastarticles(){
	$last = &get_sessparam($_SESSION['cata']['articles']['last_articles'], array() );
	return $last;
}

function del_from_lastarticles($id){
	$last = &get_sessparam($_SESSION['cata']['articles']['last_articles'], array() );
	if( in_array($id, $last) ){
		unset($last[array_search($id, $last)]);
	}

}
