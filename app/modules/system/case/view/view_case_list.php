<?php

/**
 * Description of view_case_list
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class view_case_list {

    public static function buildViewListBox(array $liste_cases = null, $displayFav = false,$light=false) {
        global $skin;
        $data = array();
        $elements = array();

        if (!$light) {
            $data['headers'][0] = 'R&eacute;f&eacute;rence';
            $data['headers'][1] = 'Client';
            $data['headers'][2] = 'Cat&eacute;gorie';
                    $data['headers'][3] = 'Resp.';
                    $data['headers'][4] = '';
            if (!empty($liste_cases)) {
                            require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
                foreach ($liste_cases as $case) {
                    if ($case->getId()>0)
                        $elements[] = self::buildLigneCaseBox($case, $displayFav);
                }
            }
            $data['data']['elements'] = $elements;
            echo '<div>' . $skin->displayArray($data) . '</div>';
        }
        else {
            if (!empty($liste_cases)) {
                            require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
                foreach ($liste_cases as $case) {
                    if ($case->getId()>0)
                        $elements[] = self::buildLigneCaseBox($case, $displayFav,$light);
                }
            }

            return $elements;
        }


    }

    private static function buildLigneCaseBox(dims_case $case, $displayFav = false,$light=false) {
		require_once DIMS_APP_PATH.'/modules/system/case/class_case.php';
		require_once DIMS_APP_PATH.'/modules/system/class_category.php';
                $elems = array();

                if (!$light) {
                    $fav = new favorite();
                    $fav->open($case->fields['id_globalobject']);
                    if ($fav->isNew() || $fav->fields['status'] == favorite::NotFavorite){
                            $fav->fields['status'] = favorite::NotFavorite;
                            $lvlFav = favorite::Favorite;
                            $star = "modules/notaire/img/star.png";
                    }else{
                            $lvlFav = favorite::NotFavorite;
                            $star = "modules/notaire/img/star_yellow.png";
                    }

                    $elems[0] = $case->getLabel();
                    if($ct = $case->getObjectContactAttach())
                            $elems[1] = $ct->fields['firstname'].'&nbsp;'.$ct->fields['lastname'];
                    elseif($ct = $case->getObjectTierAttach())
                            $elems[1] = $ct->fields['intitule'];
                    else
                            $elems[1] = '';
                    $lst = $case->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
                    $categ = new category();
                    $categ->openWithGB(current($lst));
                    $elems[2] = $categ->getAriane();
                    $user = $case->getUser();
                    $elems[3] = $user->fields['firstname'].'&nbsp;'.$user->fields['lastname'];
                    $elems[4] = '';
                    if ($displayFav)
                            $elems[4] = '<img style="cursor:pointer;" src="'.$star.'" onclick="javascript:changeCaseFavoriteList('.$case->fields['id_globalobject'].','.$lvlFav.',this,'.$fav->fields['status'].');" onMouseOut="javascript:$(this).attr(\'src\',\''.$star.'\');" onMouseOver="javascript:$(this).attr(\'src\',\'modules/notaire/img/star_yellow.png\');" />';
                    $elems[4] .= '&nbsp;<img style="cursor:pointer;"  src="img/view.png" style="cursor:pointer;" onclick="javascript:showCase(' . $case->getId() . ', ' . _OP_VIEW_CASE . ');" />';
                    $elems[4] .= '&nbsp;<img style="cursor:pointer;"  src="img/delete.gif" style="cursor:pointer;" onclick="javascript:deleteCase(' . $case->getId() . ');" />';
                }
                else {

                    $elems[0] = $case->getLabel();
                    $lst = $case->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
                    $categ = new category();
                    $categ->openWithGB(current($lst));
                    $elems[1] = $categ->getAriane();
                    $elems[2] = $case->getId();
                    //$elems[3] .= '&nbsp;<img style="cursor:pointer;"  src="img/view.png" style="cursor:pointer;" onclick="javascript:selectCase(' . $case->getId() . ', ' . _OP_VIEW_CASE . ');" />';
                }
                return $elems;
    }

}

?>
