<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// recuperation des blocks
$blocks=$article->getBlocks(false,$article->fields['id_lang']);
// recuperation des modeles d'affichage
$models=$wce_site->getBlockModels();

$subPages       = false;
$titleMissing   = false;
foreach($blocks as $pos => $block) {
    if (isset($models[$block['id_model']])) {
        if($block['page_break']) $subPages = true;
        if(empty($block['title']) && $pos > 0) $titleMissing = true;
    }
}

$blockcontent='';
$key_subpage = 0;

foreach($blocks as $pos=>$block) {
    if (isset($models[$block['id_model']])) {
        // construction des styles
        $float=(isset($block['float']) &&  $block['float']!='') ? $block['float'] : 'left';
        $width = (isset($block['width']) && $block['width']!='') ? $block['width'] : '100%';
        $height = (isset($block['height']) && $block['height']!='') ? $block['height'] : '';
        $display = (isset($block['display']) && $block['display']!='') ? $block['display'] : 'block';
        $padding = (isset($block['padding']) && $block['padding']!='') ? $block['padding'] : '0px';
        $margin = (isset($block['margin']) && $block['margin']!='') ? $block['margin'] : '10px 0px 0px 0px';
        $fontfamily = (isset($block['font-family']) && $block['font-family']!='') ? 'font-family:'.$block['font-family'] : '';
        $fontsize = (isset($block['font-size']) && $block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'] : '';
        $fontweight = (isset($block['font-weight']) && $block['font-weight']!='') ? 'font-weight:'.$block['font-weight'] : '';
        $color = (isset($block['color']) && $block['color']!='') ? 'color:'.$block['color'] : '';
        $backgroundcolor = (isset($block['background-color']) && $block['background-color']!='') ? 'background-color:'.$block['background-color'] : '';
        $borderstyle = (isset($block['border-style']) && $block['border-style']!='') ? 'border-style:'.$block['border-style'] : '';
        $bordercolor = (isset($block['border-color']) && $block['border-color']!='') ? 'border-color:'.$block['border-color'] : '';
        $bordersize = (isset($block['border-size']) && $block['border-size']!='') ? 'border-size:'.$block['border-size'] : '';

        $blockcontent.='<div style="'.$borderstyle.';'.$bordercolor.';'.$bordersize.';'.$backgroundcolor.';'.$color.';'.$fontfamily.';'.$fontsize.';'.$fontweight.';width:'.$width.';height:'.$height.';display:'.$display.';float:'.$float.';padding:'.$padding.';margin:'.$margin.';">';

        if($subPages && $block['page_break']) {
            $key_subpage++;

            if($titleMissing)
                $subpages['list'][$key_subpage]['title']    = (empty($block['title'])) ? 'Page '.$key_subpage : 'Page '.$key_subpage.' - '.$block['title'];
            else
                $subpages['list'][$key_subpage]['title']    = $block['title'];

            $subpages['list'][$key_subpage]['selected'] = ($key_subpage == $num_subpage) ? 'selected' : '';
            $subpages['list'][$key_subpage]['link']     = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage='.$key_subpage;
        }

        if(!$subPages || $key_subpage == $num_subpage) {
            $contenu=$models[$block['id_model']]['content'];


            /*// test si title
            if ($block['display_title']) {
				$blockcontent.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
            }else{*/
            	$contentsection.="<balise id=\"".$block['id']."\"></balise>";
            //}

            // affichage de la box pour editer, changer de position
            $ischange=false;

            for($j=1;$j<=10;$j++) {
                if ($block['content'.$j]!=$block['draftcontent'.$j]) {
                    $ischange=true;
                }
            }

            // on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
            for($i=1;$i<=10;$i++) {
                // detection de parametres eventuels
                $posstart=strpos($page,"<CONTENT$i>");
                //$dims->getModeOffice()!="web" || ($dims->getModeOffice()=="web" &&
                if ($ischange && $wce_mode!="online" && $adminedit==1) {
                    if ($block['draftcontent'.$i]=='') {
                        $block['draftcontent'.$i]="&nbsp;";
                    }
                    $ctemp=$block['draftcontent'.$i];
                }
                else {
                    if ($block['content'.$i]=='') {
                        $block['content'.$i]="&nbsp;";
                    }
                    $ctemp=$block['content'.$i];
                }

                if ($posstart>=0 ) {
                    $posend=strpos($page,"</CONTENT$i>");

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

            $blockcontent.= $contenu.'</div>';
        }
    }
}

if(!empty($subpages)) {
    $key_subpage = 0;
    $block = $blocks[0];

    $first_bloc['title']    = (empty($block['title'])) ? $article->fields['title'] : $block['title'];
    $first_bloc['selected'] = ($key_subpage == $num_subpage) ? 'selected' : '';
    $first_bloc['link']     = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage='.$key_subpage;
    array_unshift($subpages['list'], $first_bloc);

    $total_subpages = count($subpages['list']);

	$subpages['prev'] = array('link' => '', 'title' => '');
	$subpages['next'] = array('link' => '', 'title' => '');

	if($num_subpage != 0) {
		$subpages['prev']['link']  = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage='.($num_subpage-1);
		$subpages['prev']['title'] = $subpages['list'][$num_subpage-1]['title'];
	}

	if($num_subpage+1 != $total_subpages) {
		$subpages['next']['link']  = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage='.($num_subpage+1);
		$subpages['next']['title'] = $subpages['list'][$num_subpage+1]['title'];
	}

    $subpages['first']  = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage=0';
    $subpages['last']   = '/index.php?headingid='.$article->fields['id_heading'].'&articleid='.$article->fields['id'].'&num_subpage='.($total_subpages-1);
    $subpages['total']  = $total_subpages;
    $subpages['current']= intval($num_subpage);
}
?>
