<?php
// recuperation des modeles d'affichage
$models=$article->getWceSite()->getBlockModels();

$position=0;

if (isset($section['title']) && $section['title']!= '') {
    $label = '<span style="font-weight:bold;">'.$section['title'].'</span>';
}
else {
    $label = '<span style="font-weight:bold;">';
}

$contentsection='<div style="font-size:12px;color:#424242;border: 1px dashed grey;overflow:hidden;padding:5px;">';

if ($section['title']!='') {
    $blockcontent.='<div style="float:left;font-size:13px;margin-left:5px;">
			'.$label.'
		</div>';
}

// test si editable ou non
if (isset($section['properties']['edit']) && $section['properties']['edit']=='true') {
    $contentsection.='<a title="'.$_SESSION['cste']['_ADD_SECTION'].'" href="javascript:void(0);" onclick="javascript:window.parent.wceAddBlock('.$idsection.')">
			<img title="'.$_SESSION['cste']['_ADD_SECTION'].'" alt="'.$_SESSION['cste']['_ADD_SECTION'].'" src="'.module_wiki::getTemplateWebPath('/gfx/icon_add.png').'" border="0">
		</a>';
}

if ($section['title']!='') {
    $contentsection.='</div>';
}

// on genère le code avec template
if (isset($section['properties']['edit']) && $section['properties']['edit']=='true') {
    if (isset($blocks[$idsection][1]['content1'])) $contentobject=$blocks[$idsection][1]['content1'];

    if (file_exists(_WCE_MODELS_PATH."/objects/".$contentobject)) {
            ob_start();
            $smarty->display('file:'._WCE_MODELS_PATH."/objects/".$contentobject);
            $contentsection = ob_get_contents();
            ob_end_clean();
    }
}
else {
    //  version non editable
    if (isset($section['properties']['path'])) {
        $pathmodelobject=_WCE_MODELS_PATH."/objects/".str_replace("..", "", $section['properties']['path']);
        if (file_exists($pathmodelobject)) {
            ob_start();
            $smarty->display('file:'.$pathmodelobject);
            $contentsection .= ob_get_contents();

            ob_end_clean();
        }
    }
}

// on ferme le bloc d'edition
$contentsection.='</div>';
?>
