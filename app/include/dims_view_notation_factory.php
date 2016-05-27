<?php

/**
 * Description of dims_view_notation_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
class dims_view_notation_factory {
    private static $chemin_img_notation_select = "./common/img/notation/etoile_verte.png";
    private static $chemin_img_notation_empty = "./common/img/notation/etoile_blanche.png";
    private static $chemin_img_notation_hover = "./common/img/notation/etoile_verte_clair.png";
    private static $echelle_notation = 5 ;
    private static $margin_between_stars = "5px" ;

    public static function build_view($notation){
        for ($i=0; $i<$notation; $i++){
            ?>
                <img src="<?echo dims_view_notation_factory::$chemin_img_notation_select;?>"
                     border="0"
                     style="margin-right: <?echo dims_view_notation_factory::$margin_between_stars;?>;"
                 />
            <?
        }
        for ($i=$notation; $i<dims_view_notation_factory::$echelle_notation; $i++){
            ?>
                <img src="<?echo dims_view_notation_factory::$chemin_img_notation_empty;?>"
                     border="0"
                     style="margin-right: <?echo dims_view_notation_factory::$margin_between_stars;?>;"
                 />
            <?
        }
    }

    public static function build_view_editable($notation, $id_object, $dims_op){
        dims_view_notation_factory::buildScript();
        ?><input type="hidden" id="notation" value="<?echo $notation;?>"/><?
        for ($i=0; $i<$notation; $i++){
            ?>
                <img onmouseout="javascript:onMouseOutNotation(
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_select; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation; ?>
                                    );"
                     onmouseover="javascript:onMouseOverNotation(
                                        <?echo $i; ?>,
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_hover; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation; ?>
                                    );"
                     onclick="javascript:onClickNotation(<?echo $i;?>, <?echo $id_object;?>,
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_select; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation; ?>,
                                        '<?echo $dims_op;?>');"
                     id="notation_<?echo $i;?>"
                     src="<?echo dims_view_notation_factory::$chemin_img_notation_select;?>"
                     border="0"
                     style="margin-right: 5px;cursor:pointer;"
                 />
            <?
        }
        for ($i=$notation; $i<dims_view_notation_factory::$echelle_notation; $i++){
            ?>
                <img onmouseout="javascript:onMouseOutNotation(
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_select; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation; ?>
                                    );"
                     onmouseover="javascript:onMouseOverNotation(
                                        <?echo $i; ?>,
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_hover; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation; ?>
                                    );"
                     onclick="javascript:onClickNotation(<?echo $i;?>, <?echo $id_object;?>,
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_select; ?>',
                                        '<?echo dims_view_notation_factory::$chemin_img_notation_empty; ?>',
                                        <?echo dims_view_notation_factory::$echelle_notation;?>,
                                        '<?echo $dims_op;?>');"
                     id="notation_<?echo $i;?>"
                     src="<?echo dims_view_notation_factory::$chemin_img_notation_empty;?>"
                     border="0"
                     style="margin-right: 5px;cursor:pointer;"
                 />
            <?
        }
    }

    private static function buildScript(){
        ?>
        <script language="Javascript">
            function onMouseOutNotation(chemin_note_select, chemin_note_empty, echelle_notation){
                var notation = document.getElementById("notation").getAttribute("value");
                var i ;
                for(i=0; i<notation; i++){
                    document.getElementById("notation_"+i).setAttribute("src", chemin_note_select);
                }
                for(i=notation; i<echelle_notation; i++){
                    document.getElementById("notation_"+i).setAttribute("src", chemin_note_empty);
                }
            }

            function onMouseOverNotation(indice, chemin_note_empty, chemin_note_hover, echelle_notation){
                var i ;
                for(i=0; i<=indice; i++){
                    document.getElementById("notation_"+i).setAttribute("src", chemin_note_hover);
                }
                for(i=indice+1; i<echelle_notation; i++){
                    document.getElementById("notation_"+i).setAttribute("src", chemin_note_empty);
                }
            }

            function onClickNotation(indice_etoile, id_object, chemin_note_select, chemin_note_empty, echelle_notation, dims_op){
                    var new_notation ;
                    new_notation = indice_etoile + 1 ;

                    dims_xmlhttprequest('admin.php','dims_op='+dims_op+'&new_notation='+new_notation+'&id_object='+id_object, true, false);
                    actualiseNotation(new_notation, chemin_note_select, chemin_note_empty, echelle_notation);
            }

            function actualiseNotation(notation, chemin_note_select, chemin_note_empty, echelle_notation){
                document.getElementById("notation").setAttribute("value", notation);
                onMouseOutNotation(chemin_note_select, chemin_note_empty, echelle_notation);
            }
        </script>
        <?
    }

    public static function setCheminImgNotationSelect($chemin){
        if($chemin != null){
            dims_view_notation_factory::$chemin_img_notation_select = $chemin;
        }
    }

    public static function setCheminImgNotationEmpty($chemin){
        if($chemin != null){
            dims_view_notation_factory::$chemin_img_notation_empty = $chemin;
        }
    }

    public static function setCheminImgNotationHover($chemin){
        if($chemin != null){
            dims_view_notation_factory::$chemin_img_notation_hover = $schemin;
        }
    }

    public static function setEchelleNotation($echelle){
        if($echelle != null){
            dims_view_notation_factory::$echelle_notation = $echelle;
        }
    }

    /**
     *
     * @param type $margin - e.g = "5px"
     */
    public static function setMarginBetweenStars($margin){
        if($margin != null){
            dims_view_notation_factory::$margin_between_stars = $margin;
        }
    }
}

?>
