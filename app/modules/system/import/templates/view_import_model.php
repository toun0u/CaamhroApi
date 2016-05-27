<?php

/**
 * Description of view_import_model
 *
 * @author Patrick Nourrissier
 * @copyright Netlor 2012
 */
class view_import_model {


    /**
     * Construit la vue permettant de lister les modèles de fichiers assureurs
     * @param nothing
     */
    public static function buildViewFiles() {
        global $_DIMS;
        ?>
        <div>
            <h1>
                <img src="/common/modules/system/import/img/icon_gclients.png" />
                <? echo $_DIMS['cste']['_DIMS_LABEL_MANAGE_MODEL']; ?>
            </h1>
        </div>
        <?
        echo '<div style="float:left;width:32%;text-align:center;margin:2px auto;">
                <img src="'.$_SESSION['dims']['template_path'].'/media/goback32.png">
            <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?op=return&import_op="._OP_DEFAULT_IMPORT).'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_BACK'].'"/>
        </div>';
        // add model
        echo '<div style="float:left;width:32%;text-align:center;margin:2px auto;">
                <img src="'.$_SESSION['dims']['template_path'].'/media/add_table32.png">
            <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?op=addNewModel").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_ADD_MODEL'].'"/>
        </div>';
      // add model
        echo '<div style="float:left;width:32%;text-align:center;margin:2px auto;">
                <img src="'.$_SESSION['dims']['template_path'].'/media/table32.png">
            <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?op=listModelFields").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_FORMS_FIELDLIST'].'"/>
        </div>';


    }

    public static function buildViewReturn() {
        global $_DIMS;
        // back
        echo '<div style="float:left;width:99%;text-align:center;margin:2px auto;">
                <img src="'.$_SESSION['dims']['template_path'].'/media/goback32.png">
            <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?op=return&import_op="._OP_DEFAULT_IMPORT).'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_BACK'].'"/>
        </div>';
    }

    public static function buildListModeles() {
        echo '<div style="float:left;clear:both;width:99%;margin:2px auto;">';

        global $_DIMS;
        global $skin;
        // construction du tableau de présentation des champs
        $data =array();
        $elements=array();

        // collecte des données de la base
        $fields=import_fichier_modele::getFichiersModeles();// getModelesFilesAssureurs();

        // headers

        $data['headers'][]=$_DIMS['cste']['_LABEL_MODEL'];
        $data['headers'][]=$_DIMS['cste']['_DIMS_ACTIONS'];

        // construction des données à afficher
        foreach ($fields as $f) {
           $elem=array();

           foreach ($f as $id_modele => $dobject) {
               $globalob= $dobject->getGlobalobjectConcerned();

               $elem[0]=$globalob->fields['title'];
               $elem[1]=$dobject->fields['libelle'];
               $elem[2]='<a href="/admin.php?op=displayModelFieldsCorrespRh&id_globalobject='.$globalob->fields['id'].'&id_modele_fichier='.$id_modele.'"><img src="./common/img/go-next.png"></a>';
           }

           //nbelements
           $elements[]=$elem;
        }

        //elements of table
        $data['data']['elements']=$elements;
        echo $skin->displayArray($data);
        echo '</div>';
    }

    /**
     * Construit la vue permettant d'afficher les champs dynamiques (referentiel)
     * @param nothing
     */
    public static function buildViewModelFields() {
        global $_DIMS;
        global $skin;
        // construction du tableau de présentation des champs
        $data =array();
        $elements=array();

        // collecte des données de la base
        $fields=import_fichier_modele::getModelFields();// import_fichier_assureur::getModelFields();

        // headers
        $data['headers'][]=$_DIMS['cste']['_DIMS_LABEL'];
        $data['headers'][]=$_DIMS['cste']['_TYPE'];
        $data['headers'][]=$_DIMS['cste']['_FIELD_NEEDED'];
        $data['headers'][]=$_DIMS['cste']['_DIMS_COMMENTS'];

        // construction des données à afficher
        foreach ($fields as $f) {
            $elem=array();
            // traduction libelle
           if (isset($_DIMS['cste'][$f['libelle']])) {
               $elem[0]=$_DIMS['cste'][$f['libelle']];
           }
           else {
               $elem[0]=$f['libelle'];
           }

           // traduction libelletype
           if (isset($_DIMS['cste'][$f['libelletype']])) {
               $elem[1]=$_DIMS['cste'][$f['libelletype']];
           }
           else {
               $elem[1]=$f['libelletype'];
           }

           if ($f['obligatoire']==1) {
               $elem[2]='<img src ="./common/img/publish.png">';
           }
           else {
               $elem[2]='&nbsp;';
           }

           // traduction help_constant
           if (isset($_DIMS['cste'][$f['help_constant']])) {
               $elem[3]=$_DIMS['cste'][$f['help_constant']];
           }
           else {
               $elem[3]=$f['help_constant'];
           }

           $elements[]=$elem;
        }

        //elements of table
        $data['data']['elements']=$elements;
        echo '<div style="margin-top:10px;clear:both;float:left;width:100%;">'.$skin->displayArray($data).'</div>';
    }

    /**
     * Construit la vue permettant de lister les modèles de fichiers assureurs
     * @param nothing
     */
    public static function buildViewImportFileStep1() {
        global $_DIMS;
        // collecte des données de la base
        $tiers=import_fichier_modele::getTiers();

        ?>
        <form name="form_etape1" method="post" action="<? echo dims_urlencode("/admin.php?op=addNewModelFile"); ?>" method="post" enctype="multipart/form-data">
        <?
          // Sécurisation du formulaire par token
          require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
          $token = new FormToken\TokenField;
          $token->field("import_title");
          $token->field("file_import");
          $tokenHTML = $token->generate();
          echo $tokenHTML;
        ?>
        <div class="dims_form" style="float:left; width:80%;padding-top:20px;">
                <div style="padding:2px;">
                        <span style="width:10%;display:block;float:left;">
                            <? echo '<img src="'.$_SESSION['dims']['template_path'].'/media/properties32.png">'; ?>
                        </span>
                        <span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
                                <? echo $_DIMS['cste']['_DIMS_LABEL_STEP']." 1 : ".$_DIMS['cste']['_DIMS_LABEL_FILE']; ?>
                        </span>
                </div>
                <div style="padding:2px;clear:both;float:left;width:100%;">
                    <p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?></label>
			<input class="text" type="text" onkeyup="javascript:importFileCheck();" style="width:350px;" id="import_title" name="import_title" value="<? echo $_SESSION['dims']['import']['import']['import_label']; ?>" tabindex="1" />
                    </p>

                    <p>
                        <label><? echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?></label>
                        <input type="file" name="file_import" id="file_import" class="text" tabindex="1" onchange="javascript:importFileCheck();">
                    </p>

                <?
                $error =  dims_load_securvalue('error', _DIMS_NUM_INPUT,true,false);
                if ($error>0) {
                    echo "<p><label><img src=\"./common/img/warning.png\"></label>";
                    switch ($error) {
                        case _ASSUR_STATUT_FILE_NOT_CORRECT: // extension non correcte
                            echo $_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'];
                            break;
                    }
                    echo "</p>";
                }
                ?>
                </div>
                <div id="import_button" style="padding:2px;clear:both;float:left;width:100%;display:none;">
                        <span style="width:50%;display:block;float:left;">&nbsp;</span>
                        <span style="width:50%;display:block;float:left;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etape1.submit();"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_DIMS['cste']['_DIMS_NEXT']; ?>"></a></span>
                </div>
        </div>
        </form>
        <script language="JavaScript" type="text/JavaScript">

        function importFileCheck() {

            if ($('#import_title').val()!="" && $('#file_import').val()!="" ) $('#import_button').css('display','block');
            else $('#import_button').css('display', 'none');
        }
        $("#import_title").focus();
        importFileCheck();
        </script>

        <?
    }

    /**
     * Construit la vue permettant de créer un nouveau fichier assureur
     * @param tiers $tiers
     * @param array $liste_type_champs : tableau qui contient les différents
     * types de champs (@see import_type_champs_fichier_assureur). Chaque type
     * dans le tableau a pour indice son id.
     * @param array $liste_champs  : tableau qui contient les différents champs
     * (@see import_champs_fichier_assureur). Chaque champs dans le tableau est
     * stocké de la sorte : $liste_champs[id_type_champs][id_champs]
     */
    public static function buildAddNewModelFieldsCorresp(tiers $tiers,
            array $liste_type_champs, array $liste_champs,$readonly=false){

        global $_DIMS;

        $data['headers'][]=$_DIMS['cste']['_DIMS_LABEL'];
        $data['headers'][]=$_DIMS['cste']['_TYPE'];
        $data['headers'][]=$_DIMS['cste']['_FIELD_NEEDED'];
        $data['headers'][]=$_DIMS['cste']['_DIMS_LABEL_RELATION'];

        // construction de ce qui est déja pris
        $array_used = array();

        if (isset($_SESSION['dims']['import']['corresp'])) {
            foreach ($_SESSION['dims']['import']['corresp'] as  $corresp => $column) {
                $array_used[$column]=$corresp;
            }
        }

        // permet de connaitre si des champs n'ont pas de correspondances
        $displaySaveButton=true;

        ?>

        <div class="dims_form" style="float:left; width:95%;padding-top:20px;">
                <div style="padding:2px;">
                        <span style="width:10%;display:block;float:left;">
                            <? echo '<img src="'.$_SESSION['dims']['template_path'].'/media/table_relation32.png">'; ?>
                        </span>
                        <span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
                                <? if (!$readonly) {
                                    echo $_DIMS['cste']['_DIMS_LABEL_STEP']." 2 : ";
                                }

                                echo $_DIMS['cste']['_DIMS_LABEL_RELATION']."(s)"; ?>
                        </span>
                </div>
                <div style="padding:2px;clear:both;float:left;width:100%;">
                    <table style="width:100%;" class="display" cellspacing="0" cellpadding="0" border="0">
                        <thead><tr>
                            <?
                            foreach ($data['headers'] as $head) {
                                echo "<th>".$head."</th>";
                            }
                            ?>
                        </tr></thead>

                        <?
                        // boucle sur les types de champs
                        foreach ($liste_type_champs as $column => $f) {
                            $elem=array();
                            // traduction libelle
                           if (isset($_DIMS['cste'][$f['libelle']])) {
                               $elem[0]=$_DIMS['cste'][$f['libelle']];
                           }
                           else {
                               $elem[0]=$f['libelle'];
                           }

                           // traduction libelletype
                           if (isset($_DIMS['cste'][$f['libelletype']])) {
                               $elem[1]=$_DIMS['cste'][$f['libelletype']];
                           }
                           else {
                               $elem[1]=$f['libelletype'];
                           }

                           if ($f['obligatoire']==1) {
                               $elem[2]='<img src ="./common/img/publish.png">';
                           }
                           else {
                               $elem[2]='&nbsp;';
                           }

                           if($col%2) {
                               $class="gradeX event odd";
                           }
                           else {
                               $class="gradeX event even";
                           }
                           echo "<tr class=\"".$class."\"><td>".$elem[0]."</td><td>".$elem[1]."</td><td>".$elem[2]."</td>";

                           $selec='<form name="form_col'.$column.'" method="post" action="'.dims_urlencode("/admin.php?op=addNewModelFieldsCorresp&id_corresp=".$column).'" method="post">';
                            // Sécurisation du formulaire par token
                            require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                            $token = new FormToken\TokenField;
                            $token->field("");
                           // construction du select
                           if ($readonly) {
                               $selec.= $liste_champs[$_SESSION['dims']['import']['corresp'][$column]];

                           }
                           else {
                               $selec.="<select onchange=\"document.form_col".$column.".submit();\" name=\"column".$column."\" style=\"width:250px\">";
                               $token->field("column".$column);
                               $selec.="<option value=\"\"></option>";

                               // permet de savoir si on a une correspondance ou non
                               $foundcolumn=false;

                               foreach ($liste_champs as $col => $ch) {
                                   // test si soit deja utilise ou egal a celui utilise
                                   if ((isset($_SESSION['dims']['import']['corresp'][$column]) && $_SESSION['dims']['import']['corresp'][$column]==$col)
                                           || !isset($array_used[$col])) {

                                       if (!isset($array_used[$col])) $selected='';
                                       else {
                                           $selected=' selected ';
                                           $foundcolumn=true;
                                       }
                                       $selec.="<option ".$selected." value=\"".$col."\">".$ch."</option>";
                                   }
                               }
                               $selec.= "</select>";

                            $tokenHTML = $token->generate();
                            $selec .= $tokenHTML;
                            $selec .= "</form>";
                           }
                           echo "<td>".$selec."</td></tr>";

                           // test si obligatoire et non trouve
                           if ($f['obligatoire']==1 && !$foundcolumn) {
                               $displaySaveButton=false;
                           }
                        }
                        ?>

                    </table>

                </div>

                <?
                if ($displaySaveButton) {
                ?>
                <form name="form_etape1" method="post" action="<? echo dims_urlencode("/admin.php?op=addNewModelFieldsCorrespSave"); ?>" method="post">
                  <?
                    // Sécurisation du formulaire par token
                    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                    $token = new FormToken\TokenField;
                    $token->field("");
                    $tokenHTML = $token->generate();
                    echo $tokenHTML;
                  ?>
                    <div id="import_button" style="padding:2px;clear:both;float:left;width:100%;display:block;">
                        <span style="width:50%;display:block;float:left;">&nbsp;</span>
                        <span style="width:50%;display:block;float:left;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etape1.submit();"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_DIMS['cste']['_DIMS_NEXT']; ?>"></a></span>
                    </div>
                </form>
                <?
                }
                ?>

        </div>

        <script language="JavaScript" type="text/JavaScript">

        function importFileCheck() {

            if ($('#import_title').val()!="" && $('#file_import').val()!="" && $('#id_assureur').val()>0) $('#import_button').css('display','block');
            else $('#import_button').css('display', 'none');
        }
        $("#import_title").focus();

        </script>

        <?

    }

}

?>
