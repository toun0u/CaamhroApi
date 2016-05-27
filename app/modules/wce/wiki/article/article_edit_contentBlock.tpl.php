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

$contentsection='<div style="font-size:12px;color:#424242;border: 1px dashed grey;overflow:hidden;padding:5px;">
        <div style="background:#F9F9F9;width:100%;clear:both;display:block;padding-bottom:2px;float:right;text-align:right;padding-top:5px;padding-left:5px;">';

if ($section['title']!='') {
    $contentsection.='<div style="float:left;font-size:13px;margin-left:5px;">
			'.$label.'
		</div>';
}

$contentsection.='<a title="'.$_SESSION['cste']['_ADD_SECTION'].'" href="javascript:void(0);" onclick="javascript:window.parent.wceAddBlock('.$idsection.','.$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'].')">
			<img title="'.$_SESSION['cste']['_ADD_SECTION'].'" alt="'.$_SESSION['cste']['_ADD_SECTION'].'" src="'.module_wce::getTemplateWebPath('/gfx/icon_add.png').'" border="0">
		</a>
	</div>';

$indicesection=1;
$section_pagination = new wce_section_pagination($article->fields['id'].'_'.$section['id']);
$nblevel=wce_article::NB_LEVEL;

// valeur courante
// $_SESSION['dims']['wcecurrentsections'][$this->fields['id']][$id]

$PageBreak=$article->GetPageBreakBlocks();
$ispageBreak=(isset($PageBreak[$section['id']])) ? $PageBreak[$section['id']] : 0;

if ($ispageBreak) {
    $currentpagesection=dims_load_securvalue('WCE_section_'.$article->fields['id'].'_'.$section['id'],dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']],$_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]);

    // page break par defaut le premier si non defini
    if (!isset($_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]) || $_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]<=0) {
        $currentpagesection=1;
        $_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]=1;
    }
    else
        $currentpagesection=$_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']];
}
else {
    // pas de page break;
    $currentpagesection=0;
}

// variable stockant la difference
$diffpos=0;
$levelblock=0;
foreach($blocks as $pos=>$block) {

        if($block['page_break']) {
            $indicesection++;
        }

        $position++;

	// verification de la position des blocs
	if ($block['position']!=$position) {
		$bblock = new wce_block();
		$bblock->open($block['id'],$block['id_lang']);
		$bblock->fields['position']=$position;

                //dims_print_r($position);
                //die();
                $bblock->save();
                // on actualise la structure
                $blocks[$pos]['position']=$position;
                $block['position']=$position;
	}

        $oldlevel=$levelblock;
        $levelblock=$block['level'];

        // check sur les pages break courants
        if ($currentpagesection==0 || $currentpagesection==$indicesection ) {

            if (isset($models[$block['id_model']])) {
                $contenu=$models[$block['id_model']]['content'];
                $maxblock=0;
//dims_print_r($block['uptodate']);die();
                // affichage de la box pour editer, changer de position
                if ($block['uptodate']) $ischange=0;
                else $ischange=1;

                for($j=1;$j<=$article->getNbElements();$j++) {
                        if ($block['content'.$j]!=$block['draftcontent'.$j]) {
                                //$ischange=true;
                        }

                        if (!(strpos($contenu,"<CONTENT".$j.">")===false)) {
                                $maxblock=$j;
                        }
                }

                // construction des styles
				$id_bl=($block['id']!='') ? trim($block['id']) : '';
				$class_block=($block['class']!='') ? trim($block['class']) : '';
				$float=(isset($block['float']) &&  $block['float']!='') ? 'float:'.$block['float'].';' : 'float:'.'left;';
				$width = (isset($block['width']) && $block['width']!='') ? 'width:'.$block['width'].';' : 'width:100%;';
				$height = (isset($block['height']) && $block['height']!='') ? 'height:'.$block['height'].';' : '';
				$display = (isset($block['display']) && $block['display']!='') ? 'display:'.$block['display'].';' : '';
				$padding = (isset($block['padding']) && $block['padding']!='') ? 'padding:'.$block['padding'].';' : '';
				$margin = (isset($block['margin']) && $block['margin']!='') ? 'margin:'.$block['margin'].';' : 'margin:5px 0px 0px 0px;';
				$fontfamily = (isset($block['font-family']) && $block['font-family']!='') ? 'font-family:'.$block['font-family'].';' : '';
				$fontsize = (isset($block['font-size']) && $block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'].';' : '';
				$fontweight = (isset($block['font-weight']) && $block['font-weight']!='') ? 'font-weight:'.$block['font-weight'].';' : '';
				$color = (isset($block['color']) && $block['color']!='') ? 'color:'.$block['color'].';' : '';
				$backgroundcolor = (isset($block['background-color']) && $block['background-color']!='') ? 'background-color:'.$block['background-color'].';' : '';
				$borderstyle = (isset($block['border-style']) && $block['border-style']!='') ? 'border-style:'.$block['border-style'].';' : '';
				$bordercolor = (isset($block['border-color']) && $block['border-color']!='') ? 'border-color:'.$block['border-color'].';' : '';
				$bordersize = (isset($block['border-size']) && $block['border-size']!='') ? 'border-size:'.$block['border-size'].';' : '';

				$contentsection.='<div id="wce_block_'.$id_bl.'" class="'.$class_block.'" style="border: dashed 1px #AAAAAA;'.$borderstyle.$bordercolor.$bordersize.$backgroundcolor.$color.$fontfamily.$fontsize.$fontweight.$width.$height.$display.$float.$padding.$margin.'">';

                $contentsection.='<div style="background:#F9F9F9;padding-top:5px;padding-bottom:5px;clear:both;display:block;float:left;font-size:12px;width:100%;">';

                // on place le bouton edition + status

                if ($ischange) {
                        $contentsection.='<span style="float:left;margin-right:5px;"><img id="icon_valid_'.$block['id'].'" src="/common/modules/wce/img/ico_wait.png" "'.$_SESSION['cste']['_MODIFY'].'"></span>';
                }
                else {
                        $contentsection.='<span style="float:left;margin-right:5px;"><img id="icon_valid_'.$block['id'].'" src="/common/modules/wce/img/ico_yes.png"></span>';
                }



                //$titleblock='<span style="font-weight:bold;float:left;margin-top:3px">'.$_SESSION['cste']['_SECTION_LEVEL'].' '.$levelblock.' : </span>';
                $titleblock='<span style="font-weight:bold;float:left;margin-left:5px;margin-right:5px;">';
                $indiceblock='';

                for ($k=1;$k<=$levelblock;$k++) {
                    if ($k>1) $indiceblock.='.';
                    $indiceblock.=$block['l'.$k];
                }
                if ($levelblock==1) $indiceblock.='.'; // on ajoute le point de premier element
                $titleblock.=$indiceblock.'</span>';

                // on construit le title + zone d'édition
                $titleblock.='<span id="zoneedit_dims_block'.$block['id'].'" style="color:#FA652B;cursor:pointer;"><span onclick="javascript:wceEditLittleBlock('.$block['id'].','.$block['id_lang'].',\'wce_block_title\');">'.$block['title'].'</span></span>';

                // on peut supprimer l'article
                $contentsection.=$titleblock."
                <a style=\"text-decoration:none;float:right;margin-left:5px;\" href=\"javascript:void(0);\" onclick=\"javascript:window.parent.dims_confirmlink('".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_DEL_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang'])."','".$_SESSION['cste']['_CONFIRM_DELETE_BLOCK']."')\">
                    <img border=\"0\" src=\"".module_wiki::getTemplateWebPath('/gfx/icon_mini_suppr.png')."\"/>
                </a>";

                // édition du contenu

                /*$contentsection .= '
                <a style="text-decoration:none;float:right;margin-left:5px;" href="javascript:void(0);" onclick="window.parent.wceModifBlockContent('.$block['id'].');">
                        <img src="'.module_wiki::getTemplateWebPath('/gfx/icon_edit.png').'" border="0">
                </a>';*/

                // on ajoute le bouton de modification
                $contentsection.='
                <a style="text-decoration:none;float:right;margin-left:5px;" href="javascript:void(0);" onclick="window.parent.wceModifBlock('.$block['id'].','.$block['id_lang'].');">
                        <img src="'.module_wiki::getTemplateWebPath('/gfx/icon_mini_param.png').'" border="0">
                </a>';

                // calcul de la difference
                $diffpos=($levelblock-$oldlevel);
                // on va ajouter les commandes pour composer les niveaux
                // test non premiere ligne
                //echo $levelblock." - ".$oldlevel." - ".$nblevel. " ".$diffpos;
                if ($levelblock<=$nblevel && $block['position']>1 && $diffpos<1) {
                    // on peut ajouter un niveau au bloc
                    $contentsection.='
                    <a style="text-decoration:none;float:right;margin-left:5px;" href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_UP_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang']).'\'">
                            <img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-right.png').'" border="0">
                    </a>';
                }
                else {
                    $contentsection.='<span style="float:right;margin-left:5px;"><img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-right-dis.png').'" border="0"></span>';
                }

                // on gère le cote gauche
                if ($levelblock>1) {
                    // on peut ajouter un niveau au bloc
                    $contentsection.='
                    <a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_DOWN_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang']).'\'">
                            <img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-left.png').'" border="0">
                    </a>';
                }
                else {
                    $contentsection.='<span style="float:right;margin-left:5px;"><img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-left-dis.png').'" border="0"></span>';
                }

                // on gère le haut
                if ($block['position']>1) {
                    // on peut ajouter un niveau au bloc
                    $contentsection.='
                    <a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_POSITION_UP_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang']).'\'">
                            <img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-up.png').'" border="0">
                    </a>';
                }
                else {
                    $contentsection.='<span style="float:right;margin-left:5px;"><img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-up-dis.png').'" border="0"></span>';
                }

                // on gère le bas
                if ($block['position']<sizeof($blocks)) {
                    // on peut ajouter un niveau au bloc
                    $contentsection.='
                    <a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_POSITION_DOWN_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang']).'\'">
                            <img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-bottom.png').'" border="0">
                    </a>';
                }
                else {
                    $contentsection.='<span style="float:right;margin-left:5px;"><img src="'.module_wiki::getTemplateWebPath('/gfx/arrow-bottom-dis.png').'" border="0"></span>';
                }

                // on gère le bas

                if($block['page_break'])
                    $contentsection .= '<span style="font-weight: bold; color: #7D7D7D;"> -- PAGE BREAK -- </span>';

                $contentsection.='</div>';

                if ($block['display_title']) {
                    $contentsection.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
                }

                // on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
                for($i=1;$i<=10;$i++) {
                        // detection de parametres eventuels
                        $posstart=strpos($contenu,"<CONTENT$i>");

                        $ctemp='<div style="clear:both;overflow:auto;"><div style="float:left;">';

                        // on regarde la position pour eventuellement inverser les contenus

                        $ctemp='<div style="font-size:12px;color:#424242;border: 1px dashed grey;overflow:hidden;padding:5px;">
                                <div id="wikiedit'.$block['id'].'_'.$i.'" style="float:left;clear:both;"><a href="javascript:void(0);" onclick="wceModifBlockContentCkeditor('.$block['id'].','.$block['id_lang'].','.$i.');$(this).unbind();"><img src="./common/img/edit.png" border="0"></a>';

                        if ($i>1) {
                                $ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(0,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_left.gif" border="0"></a>';
                        }

                        if ($i<$maxblock) {
                                $ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(1,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_right.gif" border="0"></a>';
                        }


                        $chdiv='<form onsubmit="javascript:saveBloc('.$block['id'].','.$block['id_article'].','.$block['id_lang'].',\''.module_wiki::getTemplateWebPath('/gfx/puce_orange.png').'\',\''.$_SESSION['cste']['NOT_UP_TO_DATE'].'\');" class="ajaxForm" name="form_wce_block'.$block['id'].'_'.$i.'" id="form_wce_block'.$block['id'].'_'.$i.'" style="margin:0;" action="'.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_SAVE_BLOC_C_AJAX.'&id='.$article->getId()).'&block_id='.$block['id'].'&lang='.$block['id_lang'].'&content_id='.$i.'" method="post" enctype="multipart/form-data"><textarea style="display:none;visibility:hidden" name="fck_contentBlockReturn'.$block['id'].'_'.$i.'" id="fck_contentBlockReturn'.$block['id'].'_'.$i.'" value="dsd"></textarea><div style="clear:both;" class="ckeditoriz" id="block'.$block['id'].'_'.$i.'">';
                        $chdivend='</div></form>';

                        if (isset($block['contentversion'])) {
                            //die();
                            // on a un contenu venant d'un version antérieure
                            if (isset($block['contentversion']['content'.$i]) && $block['contentversion']['content'.$i]=='') {
                                $block['contentversion']['content'.$i]="&nbsp;";
                            }
                            $ctemp.='</div>'.$chdiv.$block['contentversion']['content'.$i].$chdivend.'</div>';
                        }
                        else {
                            $contblock='';

                            if ($ischange) {
                                if ($block['draftcontent'.$i]=='') {
                                    $block['draftcontent'.$i]="&nbsp;";
                                }
                                if (dims::getInstance()->getScriptEnv() == 'admin.php'){
                                    $contblock=preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"javascript:void(0);\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=$1&wce_mode=edit&readonly=0&adminedit=1$2")."');\" ",$block['draftcontent'.$i]);
									//echo $contblock;
                                }
                                else
                                    $contBlock = $block['draftcontent'.$i];
                            }
                            else {
                                if ($block['content'.$i]=='') {
                                    $block['content'.$i]="&nbsp;";
                                }
                                if (dims::getInstance()->getScriptEnv() == 'admin.php')
                                    $contblock=preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"javascript:void(0);\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=$1&wce_mode=edit&readonly=0&adminedit=1$2")."');\" ",$block['content'.$i]);
                                else
                                    $contBlock = $block['content'.$i];
                            }
                            // on cumule
                            $ctemp.='</div>'.$chdiv.$contblock.$chdivend.'</div>';
                        }

                        if ($posstart>=0 ) {
                                $posend=strpos($contenu,"</CONTENT$i>");

                                if (($posstart+strlen("<CONTENT$i>"))==$posend || ($posend==0))	{
                                        // cas ou pas de params en plus ou </content inexistant

                                        $contenu = str_replace("<CONTENT$i>",$ctemp, $contenu);
                                        $contenu = str_replace("</CONTENT$i>", "", $contenu);
                                }
                                else {
                                        // on  a qq chose
                                        $chparams=substr($page,$posstart+strlen("<CONTENT$i>"),$posend-($posstart+strlen("<CONTENT$i>")));
                                        $contenu = str_replace("<CONTENT$i>$chparams</CONTENT$i>", $ctemp, $contenu);
                                        $contenu = str_replace("</CONTENT$i>", "", $contenu);
                                }

                        }
                }

                $contentsection.= $contenu.'</div>';
            }
        }
        else {

        }
}
// on ajoute le bloc des paginations
$section_pagination->setMaxItem($indicesection);
$section_pagination->setSelectedItem($currentpagesection);

if ($currentpagesection>$section_pagination->getMaxItem()) {
    $currentpagesection=$section_pagination->getMaxItem();
    $_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]=$currentpagesection;
}

// on affiche la pagination
if ($currentpagesection>0)
    $contentsection.=$section_pagination->display();

$contentsection.='</div>';
$contentsection.='<script type="text/javascript"> window[\'saveBloc\'] = function saveBloc(id,idArt,idLang,ico,label){
	var elem = window.parent.document.getElementById(\'global_article_puce\')
        if (elem!=null) {
            elem.src=ico;
            elem.title=label;
            elem.alt=label;
            var elem2 = window.parent.document.getElementById(\'icon_valid_article\');
            elem2.setAttribute(\'onclick\',"javascript:document.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE).'&articleid="+idArt+"&lang="+idLang+"&action='.module_wiki::_ACTION_VALID_ARTICLE.'\';");
            elem2.src = \'./common/modules/wce/wiki/gfx/icon_validation.png\';
            $(\'img#icon_valid_\'+id).attr(\'src\',ico);
        }
} </script>';
?>
