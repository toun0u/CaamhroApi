<?php
function _constructFamilleArborescence($id = 0,$inputId = ""){
    $sel = "SELECT      *
            FROM        ".cata_famille::TABLE_NAME."
            WHERE       id_module = ".$_SESSION['dims']['moduleid']."
            AND         id_workspace = ".$_SESSION['dims']['workspaceid']."
            ".(($id > 0 && $id != '')?"AND id != $id":"")."";
    $db = dims::getInstance()->getDb();
    $res = $db->query($sel);
    $lstFamilles = array();
    $root = new cata_famille();
    $root->init_description();
    while($r = $db->fetchrow($res)){
        $lstFamilles[] = $r['id'];
        if($r['id_parent'] == 0)
            $root->openFromResultSet($r);
    }
    $root->getChildrens($lstFamilles);
    ?>
    <div class="arbo_fam">
        <div class="root" ref="<?= $root->fields['id']; ?>">
            <?= dims_constant::getVal('_DOC_ROOT'); ?>
        </div>
        <?
        _constructFamilleArboElem($root,$lstFamilles);
        ?>
    </div>
    <p style="clear: both; height: 1px;"></p>
    <script type="text/javascript">
        $(document).ready(function(){
            $("div.arbo_fam div.puce.nav").click(function(){
                $('div.arbo_fam ul[rel="'+$(this).attr('ref')+'"]').slideToggle('fast');
                if($(this).hasClass('plus'))
                    $(this).removeClass('plus').addClass('minus');
                else if($(this).hasClass('plusbottom'))
                    $(this).removeClass('plusbottom').addClass('minusbottom');
                else if($(this).hasClass('minus'))
                    $(this).removeClass('minus').addClass('plus');
                else if($(this).hasClass('minusbottom'))
                    $(this).removeClass('minusbottom').addClass('plusbottom');
            });
            <?
            if($inputId != ""){
                ?>
                $("div.arbo_fam a").click(function(){
                    $("#<? echo $inputId; ?>_hidden").val($(this).attr('ref')).trigger('change');
                    $("#<? echo $inputId; ?>_display").val(jQuery.trim($(this).text()));
                });
                $("div.arbo_fam div.root").click(function(){
                    $("#<? echo $inputId; ?>_hidden").val($(this).attr('ref')).trigger('change');
                    $("#<? echo $inputId; ?>_display").val(jQuery.trim($(this).text()));
                });
                <?
            }
            ?>
        });
    </script>
    <?
}

function _constructFamilleArboElem(cata_famille $elem,$lstDipo = array(), $prev = array()){
    if(isset($elem->childrens) && !is_null($elem->childrens)){
        $nbChild = count($elem->childrens);
        ?>
        <ul rel="<?= $elem->fields['id']; ?>" class="<? echo (($elem->fields['id_parent'] == 0)?"first ":"").(($elem->fields['depth'] > 1)?'hidden':""); ?>">
        <?
        foreach($elem->childrens as $i => $child){
            if(in_array($child->fields['id'],$lstDipo)){
                $prev2 = $prev;
                $testSuite = isset($child->childrens) && !is_null($child->childrens) && count($child->childrens);
                ?>
                <li>
                    <?
                    foreach($prev as $puce){
                        ?>
                        <div class="puce <?= $puce; ?>"></div>
                        <?
                    }
                    ?>
                    <div ref="<?= $child->fields['id']; ?>" class="puce nav <? echo ($testSuite)?(($nbChild-1 == $i)?"plus":"plusbottom"):(($nbChild-1 == $i)?"join":"joinbottom"); ?>"></div>
                    <a href="javascript:void(0);" ref="<?= $child->fields['id']; ?>">
                        <?= $child->getLabel(); ?>
                    </a>
                    <?
                    if($testSuite){
                        $prev2[] = ($nbChild-1 == $i)?"blank":"line";
                        _constructFamilleArboElem($child,$lstDipo,$prev2);
                    }
                    ?>
                </li>
                <?
            }
        }
        ?>
        </ul>
        <?
    }
}

/* --------------------- Gestion des dernières familles ----*/
function store_lastfamille($id, $nb_elems){
    $last_familles = &get_sessparam($_SESSION['cata']['familles']['last_familles'], array() );
    if(in_array($id,$last_familles)){
        unset($last_familles[array_search($id, $last_familles)]);
    }elseif(count($last_familles) >= $nb_elems){
        array_splice($last_familles,$nb_elems-1);
    }
    array_unshift($last_familles,$id);
}

function get_lastfamilles(){
    $last = &get_sessparam($_SESSION['cata']['familles']['last_familles'], array() );;
    return $last;
}

function unstore_famille($id){
    $last_familles = &get_sessparam($_SESSION['cata']['familles']['last_familles'], array() );
    if(($pos=in_array($id,$last_familles)) !== false)
        unset($last_familles[$pos]);
}
?>
