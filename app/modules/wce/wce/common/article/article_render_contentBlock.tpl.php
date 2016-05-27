<?php
//global $article;
$models=$article->getWceSite()->getBlockModels();

$position=0;

$subPages=false; // on desactive pour l'instant DSK
// nous allons boucler sur l'ensemble des objets text et/ou object
$contentsection='';

$section_pagination = new wce_section_pagination($article->fields['id'].'_'.$section['id']);
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
$indicesection=1;
foreach($blocks as $pos=>$block) {
	if (isset($models[$block['id_model']])) {
        if($block['page_break']) {
            $indicesection++;
        }

        // check sur les pages break courants
        if ($currentpagesection==0 || $currentpagesection==$indicesection ) {
            // construction des styles
			$id_bl=($block['id']!='') ? trim($block['id']) : '';
			$class_block=($block['class']!='') ? trim($block['class']) : '';
			$float=(isset($block['float']) &&  $block['float']!='') ? 'float:'.$block['float'].';' : 'float:'.'left;';
			$width = (isset($block['width']) && $block['width']!='') ? 'width:'.$block['width'].';' : 'width:100%;';
			$height = (isset($block['height']) && $block['height']!='') ? 'height:'.$block['height'].';' : '';
			$display = (isset($block['display']) && $block['display']!='') ? 'display:'.$block['display'].';' : '';
			$padding = (isset($block['padding']) && $block['padding']!='') ? 'padding:'.$block['padding'].';' : '';
			$margin = (isset($block['margin']) && $block['margin']!='') ? 'margin:'.$block['margin'].';' : '';
			$fontfamily = (isset($block['font-family']) && $block['font-family']!='') ? 'font-family:'.$block['font-family'].';' : '';
			$fontsize = (isset($block['font-size']) && $block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'].';' : '';
			$fontweight = (isset($block['font-weight']) && $block['font-weight']!='') ? 'font-weight:'.$block['font-weight'].';' : '';
			$color = (isset($block['color']) && $block['color']!='') ? 'color:'.$block['color'].';' : '';
			$backgroundcolor = (isset($block['background-color']) && $block['background-color']!='') ? 'background-color:'.$block['background-color'].';' : '';
			$borderstyle = (isset($block['border-style']) && $block['border-style']!='') ? 'border-style:'.$block['border-style'].';' : '';
			$bordercolor = (isset($block['border-color']) && $block['border-color']!='') ? 'border-color:'.$block['border-color'].';' : '';
			$bordersize = (isset($block['border-size']) && $block['border-size']!='') ? 'border-size:'.$block['border-size'].';' : '';

			$contentsection.='<div id="wce_block_'.$id_bl.'" class="'.$class_block.'" style="'.$borderstyle.$bordercolor.$bordersize.$backgroundcolor.$color.$fontfamily.$fontsize.$fontweight.$width.$height.$display.$float.$padding.$margin.'">';

            $contenu=$models[$block['id_model']]['content'];


            /*// test si title
            if ($block['display_title']) {
                $contentsection.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
            }else{*/
            	$contentsection.="<balise id=\"".$block['id']."\"></balise>";
            //}

            // affichage de la box pour editer, changer de position
            $ischange=false;

            for($j=1;$j<=$article->getNbElements();$j++) {
				if ($block['content'.$j]!=$block['draftcontent'.$j]) {
					$ischange=true;
				}
            }

            // on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
            for($i=1;$i<=$article->getNbElements();$i++) {
                // detection de parametres eventuels
                $posstart=strpos($contenu,"<CONTENT$i>");
                $contblock = "";
                //$dims->getModeOffice()!="web" || ($dims->getModeOffice()=="web" &&
                if ($ischange && isset($wce_mode) && $wce_mode!="online" && isset($adminedit) && $adminedit==1) {
                    if ($block['draftcontent'.$i]=='') {
                        $block['draftcontent'.$i]="&nbsp;";
                    }
					if (dims::getInstance()->getScriptEnv() == 'admin.php')
						$contblock=preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"#\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&articleid=$1$2');\" ",$block['draftcontent'.$i]);
                    else
						$contblock=$block['draftcontent'.$i];
                }
                else {
                    if ($block['content'.$i]=='') {
                        $block['content'.$i]="&nbsp;";
                    }
					if (dims::getInstance()->getScriptEnv() == 'admin.php')
						$contblock=preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"#\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&articleid=$1$2');\" ",$block['content'.$i]);
					else
                        $contblock=$block['content'.$i];
                }
                $ctemp=$contblock;

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
}
// on ajoute le bloc des paginations
$section_pagination->setMaxItem($indicesection);
$section_pagination->setSelectedItem($currentpagesection);

if ($currentpagesection>$section_pagination->getMaxItem()) {
	echo $indicesection;
	die($section_pagination->getMaxItem());
	$currentpagesection=$section_pagination->getMaxItem();
	$_SESSION['dims']['wcecurrentsections'][$article->fields['id']][$section['id']]=$currentpagesection;
}

// on affiche la pagination
if ($currentpagesection>0)
	$contentsection.=$section_pagination->display();
?>
