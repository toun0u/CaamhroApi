<?php

include_once DIMS_APP_PATH.'modules/system/class_module.php';

class module_cata extends module {
    public static function getInstance(){
        $module = new module_cata();
        $module->open($_SESSION['dims']['moduleid']);
        return $module;
    }

    public function getRootGroup(){
        include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
        $elem = new cata_group();
        if($this->fields['id_group_def'] != '' && $this->fields['id_group_def'] > 0){
            include_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
            if(is_null($elem = cata_param::getRootGroup())){
                $elem = new cata_group();
                $elem->open($this->fields['id_group_def']);
            }
        }else{
            include_once DIMS_APP_PATH.'modules/system/class_workspace.php';
            $work = new workspace();
            $work->open($_SESSION['dims']['workspaceid']);

            $elem->init_description();
            $elem->fields['system'] = 1;
            $elem->fields['protected'] = 1;
            $elem->fields['depth'] = 1;
            $elem->fields['parents'] = 0;
            $elem->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
            $elem->fields['label'] = "Catalogue : ".$work->fields['label'];
            $elem->save();

            $this->fields['id_group_def'] = $elem->fields['id'];
            $this->save();
        }
        return $elem;
    }

    public function defineNewRootgroup($id){
        $oldRoot = $this->getRootGroup();
        if($oldRoot->fields['id'] != $id){
            $oldRoot->moveChildsTo($id);
        }
    }

}
?>
