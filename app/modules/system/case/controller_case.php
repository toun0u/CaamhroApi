<?php

/**
 * Description of controller_case
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class controller_case {

	public static function accueilCaseBOPublicView($id_gb, $displayFav = false,$light=false) {
		$gb = new dims_globalobject();
		$gb->open($id_gb);
		$lst = $gb->searchLink(dims_const::_SYSTEM_OBJECT_CASE);

		$lst_elems = array();
		foreach($lst as $c){
			$case = new dims_case();
			$case->openWithGB($c);
			$lst_elems[] = $case;
		}
                $elements=view_case_list::buildViewListBox($lst_elems, $displayFav,$light);
		if (isset($_SESSION['dims']['case']['reopen']) && $_SESSION['dims']['case']['reopen'] > 0 && !$light){
			?>
				<script type="text/javascript">
					$(document).ready(function(){
						showCase(<? echo $_SESSION['dims']['case']['reopen']; ?>,<? echo _OP_VIEW_CASE; ?>);
					});
				</script>
			<?
			unset($_SESSION['dims']['case']['reopen']);
		}
                return $elements;
    }

	public static function viewCaseBOPublic($id_case,$id_popup = 0){
		$case = new dims_case();
		$case->open($id_case);
		if (isset($_SESSION['dims']['personalViewCaseFile'])){
			require_once($_SESSION['dims']['personalViewCaseFile']);
			$url = explode('/',$_SESSION['dims']['personalViewCaseFile']);
			$name = explode('.',$url[count($url)-1]);
			$view_case = new $name[0];
			$view_case::buildViewCase($case,$id_popup);
		}else
			view_case::buildViewCase($case,$id_popup);
	}

    public static function editCaseBOPublic($id_case){
		view_case_edit::buildViewCase($id_case);
	}
}

?>
