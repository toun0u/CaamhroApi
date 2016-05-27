<?php

/**
 * Description of view_todo_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_todo_factory {

    public static function buildViewPopupListeTodos(array $liste_todo, array $liste_user, $id_popup) {
        ?>
        <div id="popup_liste_todo">
            <div class="actions">
                <a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
                    <img src="modules/assurance/templates/backoffice/img/icon_close.gif" />
                </a>
            </div>
            <h2>
                <?php echo $_SESSION['cste']['_DIMS_LABEL_TODO_DETAILLED_LIST_ALL_TODO']; ?>
            </h2>
            <div>
                <?php
                $elements = array();
                $data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_TODO_DATE'];
                $data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_TODO_CONTENT'];
                $data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_TODO_SENDER'];
                $data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_TODO_STATUS'];

                foreach ($liste_todo as $todo) {
                    $elem[0] = $todo->getDate();
                    $elem[1] = $todo->getContent();
                    if(isset($liste_user[$todo->getUserFrom()])){
                        $elem[2] = $liste_user[$todo->getUserFrom()]->getFirstname()." ".$liste_user[$todo->getUserFrom()]->getLastname();
                    }else{
                        $elem[2] = $_SESSION['cste']['_DIMS_LABEL_TODO_UNKNOW_USER'];
                    }
                    if($todo->getState() == 1){
                       if(isset($liste_user[$todo->getUserBy()])){
                           $validator = $liste_user[$todo->getUserBy()]->getFirstname()." ".$liste_user[$todo->getUserBy()]->getLastname();
                       }else{
                           $validator = $_SESSION['cste']['_DIMS_LABEL_TODO_UNKNOW_USER'];
                       }
                       $elem[4] = $_SESSION['cste']['_DIMS_LABEL_TODO_REALIZED']."
                           <img title='".$_SESSION['cste']['_DIMS_LABEL_TODO_VALIDATED']." ".dims_nicetime($todo->getDateValidation())." ".$_SESSION['cste']['_DIMS_LABEL_TODO_VALIDATED_BY']." ".$validator." - ".$todo->getCommentBy()."' src='./common/img/interrogation.png'/>";
                    }else{
                       $elem[4] = $_SESSION['cste']['_DIMS_LABEL_TODO_STAND_BY'];
                    }

                    $elements[] = $elem;
                }

                $data['data']['elements'] = $elements;
                global $skin;
                echo $skin->displayArray($data);
                ?>
            </div>
            <div>
                <?
                echo dims_create_button($_SESSION['cste']['_DIMS_CLOSE'],'close','Javascript: count -- ;dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin:10px;');
                ?>
            </div>
        </div>
        <?php
    }

}
?>
