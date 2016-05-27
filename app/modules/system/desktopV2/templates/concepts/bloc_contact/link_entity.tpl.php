<?php
unset($_SESSION['desktopv2']['opportunity']['ct_added']);
unset($_SESSION['desktopv2']['opportunity']['tiers_added']);
unset($_SESSION['desktopv2']['opportunity']['tiers_tolink']);
?>
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>result_search/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>recent_opportunities/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>opportunity/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>companies_recently/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>new_company/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>advanced_search/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>recent_connexions/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>address_book/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>shortcuts/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>newsletters/css/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>styles.css" media="screen" />
<script type="text/javascript" src="./common/js/base64.js"></script>

<div style="clear:both;float:right;" class="action switcher">
    <span><a href="javascript:void(0)" onclick="javascript:$('div.getLinkAdditionalContent').slideToggle('fast',flip_flop($('div.getLinkAdditionalContent'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));"><? echo $_SESSION['cste']['_DIMS_LABEL_ADDLINK']; ?></a></span>
    <img style="cursor:pointer;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" border="0" onclick="javascript:$('div.getLinkAdditionalContent').slideToggle('fast',flip_flop($('div.getLinkAdditionalContent'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
</div>


<div id="getLinkAdditionalContent" class="getLinkAdditionalContent" style="clear:both;display:none;">
    <div class="title_description">
            <h2>
                    <?php echo $_SESSION['cste']['_ADDING_CONTACT_COMPANIES']; ?>
            </h2>
    </div>
    <div class="zone_contact_opportunity_content">
            <div class="zone_contact_opportunity_gauche">
                    <div class="zone_search_contact">
                    </div>
                    <div id="div_list_added" style="max-height:600px;overflow-x: hidden;overflow-y: auto;">
                            <?php
                            global $desktop;
                            if (!isset($_SESSION['desktopv2']['opportunity']['ct_added'])) $_SESSION['desktopv2']['opportunity']['ct_added']=array();
                            $added_contact = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
                            foreach ($added_contact as $tiers) {
                                    $tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
                            }
                            ?>
                    </div>
            </div>
            <div class="zone_contact_opportunity_droite">
                    <?php
                    include _DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company.tpl.php';
                    ?>
            </div>
            <div style="clear: both;float:right;">
                    <input type="button" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>" onclick="Javascript: link_entity(<?php echo $this->fields['id_globalobject']; ?>); " >
            </div>
    </div>
</div>
