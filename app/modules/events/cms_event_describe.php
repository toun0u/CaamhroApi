<div class="head" style="margin:10px;">
    <h2><?php echo ucfirst(strtolower($_DIMS['cste'][$evt->fields['typeaction']])); ?></h2>
    <h3>Agenda <?php echo date("Y")-1; ?> - <?php echo date("Y") ?></h3>
</div>
<div id="describe_event"  style="margin:10px;overflow:hidden">
    <?php
    if(isset($evt->fields['banner_path']) && !empty($evt->fields['banner_path'])) {
        ?>
        <div class="banner">
            <img width="620" height="190" src="<?php echo $evt->fields['banner_path']; ?>" alt="<?php echo $evt->fields['libelle']; ?>" />
        </div>
        <?php
    }

    $date_jour  = explode('-', $evt->fields['datejour']);

    if($evt->fields['datefin'] == '0000-00-00')
        $evt->fields['datefin'] = $evt->fields['datejour'];

    $date_fin   = explode('-', $evt->fields['datefin']);

    $same_date = 0;

    if($evt->fields['datefin'] == $evt->fields['datejour'])
        $same_date = 1;
    ?>
    <div id="left-side">
        <div class="date">
            <span class="day">
                <?php echo $date_jour[2]; ?>
            </span>
            <span class="month">
                <?php echo $_SESSION['cste'][getMonthCste(intval($date_jour[1]))]; ?>,
            </span>
            <span class="year">
                <?php echo $date_jour[0]; ?>
            </span>
        </div>
        <?php
            if(!$same_date) {
        ?>
        <div class="date-separator">
            -
        </div>
        <div class="date">
            <span class="day">
                <?php echo $date_fin[2]; ?>
            </span>
            <span class="month">
                <?php echo $_SESSION['cste'][getMonthCste(intval($date_fin[1]))]; ?>,
            </span>
            <span class="year">
                <?php echo $date_fin[0]; ?>
            </span>
        </div>
        <?php
            }
        ?>
        <div id="actions">
            <div class="button">
            <?php
            if($evt->fields['close'] == 0 && $evt->fields['allow_fo'] == 1) {
                //if($evt->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') $url_reg = "?id_event=".$evt->fields['id']."&action=form_niv1";
                //else $url_reg = "?id_event=".$evt->fields['id']."&action=form_niv1_fair";
                $url_reg='/index.php?op=fairs&action=sub_eventinscription&id_event='.$evt->fields['id'].'&id_contact='.$_SESSION['dims']['user']['id_contact'];
                ?>
                <a href="<?php echo $url_reg; ?>">
                    <?php echo $_DIMS['cste']['_DIMS_LABEL_REGISTER']; ?> >
                </a>
                <?php
            }
            else {
                $responsable = new contact();
                $responsable->open($evt->fields['id_responsible']);

                echo '<a href="mailto:'.$responsable->fields['email'].'?subject='.$evt->fields['libelle'].'">';
                echo $_DIMS['cste']['_DIMS_CONTACT_US'];
                echo '</a>';
            }
            ?>
            </div>
            <div class="button">

                <a href="/index.php?article_id=<? echo $_SESSION['dims']['currentarticleid']; ?>&submenu=coming_events">
                    <?php echo $_DIMS['cste']['_DIMS_BACK']; ?>
                </a>
            </div>
        </div>
        <div id="booklet">
            <?php
            if($idBooklet = $evt->getIdDocBooklet()) {
                $booklet = new docfile();
                $booklet->open($idBooklet);

                ?>
                <a href="<?php echo $booklet->getwebpath(); ?>">
                <?php
                if(isset($evt->fields['preview_path']) && !empty($evt->fields['preview_path'])) {
                    ?>
                    <img width="160" height="174" src="<?php echo $evt->fields['preview_path']; ?>" alt="<?php echo $_DIMS['cste']['_DIMS_LABEL_BOOKLET']; ?>"/><br />
                    <?php
                }
                else {
                    ?>
                    <img src="./common/modules/system/img/pdf.png" alt="<?php echo $_DIMS['cste']['_DIMS_LABEL_BOOKLET']; ?>"/><br />
                    <?php
                }
                echo $_DIMS['cste']['_DIMS_LABEL_BOOKLET'];
                ?>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
    <div id="description">
        <h3>
            <?php echo $evt->fields['libelle']; ?>
        </h3>
        <div>
            <?php echo $evt->fields['description']; ?>
        </div>
    </div>
    <div id="gallery">
    <?php
    $idFiles = $evt->getFilesGallery();
    $imgGal = new docfile();
    foreach($idFiles as $idFile) {
        $imgGal->open($idFile);

        $mouseover  = 'onmouseover="javascript:this.style.cursor=\'pointer\';"';
        $onmouseout = 'onmouseout="javascript:this.style.cursor=\'default\';"';
        $popup=$imgGal->getwebpath();
       /* $mouseclick = "onclick=\"javascript:if(document.getElementById('infos".$idFile."').style.display=='block') document.getElementById('infos".$idFile."').style.display='none'
				else document.getElementById('infos".$idFile."').style.display='block'\"";*/

        /*echo '<div id="infos"'*/
        echo "<a href=\"./".$popup."\" rel=\"clearbox[gal".$evt->fields['id']."]\" >";
        echo '<img '.$mouseover.' '.$onmouseout.' src="'.$imgGal->getwebpath().'" alt="'.$imgGal->fields['name'].'" />';
        echo '</a>';
    }
    ?>
    </div>
</div>
