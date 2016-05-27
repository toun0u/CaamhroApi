<?
/*
 *      Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */
//if (isset($adminedit) && $adminedit ) {
?>

function updateCompleteArticle(param) {
    window.parent.document.location.href=param;
}

function changeViewEditArticle(idarticle,idlang) {
    var versionid=$("#selectVersionArticle").val();
    window.location.href="<? echo module_wiki::getScriptEnv('action=show_article&sub='.module_wiki::_SUB_NEW_ARTICLE);?>&articleid="+idarticle+"&wce_mode=edit&versionid="+versionid+"&lang="+idlang;
}

window['refreshWceIframe'] =function refreshWceIframe() {
    //$( '#wce_frame_editor' ).attr( 'src', function ( i, val ) { return val; });
    document.getElementById('wce_frame_editor').contentWindow.location.reload();
}

window['editArticleProperties'] = function editArticleProperties(id) {
	dims_showcenteredpopup("",700,150,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=properties_article&id_article='+id,'','dims_popup');
}


window['activeEditHeading'] = function activeEditHeading(event,headingid) {
	event= (!event) ? window.event : event;
	dims_showpopup('',90,event,'click','dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_showcmd','','dims_popup');
}

window['autofitIframe'] = function autofitIframe(){

    try {
		var F = parent.document.getElementById("wce_frame_editor");
		if(F.contentDocument) {
			F.height = F.contentDocument.documentElement.scrollHeight+100; //FF 3.0.11, Opera 9.63, and Chrome
		} else {
			F.height = F.contentWindow.document.body.scrollHeight+100; //IE6, IE7 and Chrome
		}
		hauteur=F.height;

		if (hauteur<650) hauteur=650;
		parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
	}
	catch (e) {
		hauteur =this.document.body.offsetHeight + 100;
		if (hauteur<650) hauteur=650;
		parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
	}
}

var currentmode="";
var current_editobject='';
var current_hover_editobject='';
var timerselectwceobject=0;
var opacity = 0;
var arrayObject= new Array();
var arraySelectObject = Array();
var boxedit=null;


window['updateStateWce'] = function updateStateWce(object,type,id,value) {
    dims_xmlhttprequest_todiv('admin.php','dims_op=switch_value_wce&object='+object+'&id='+id+'&type='+type+'&value='+value,'','wce_'+object+'_'+type+'_'+id);
}

window['wceRefreshMenuLeft'] = function wceRefreshMenuLeft() {
    valcour=dims_getelem('wce_tree').style.display;

    if (valcour=='block') {
        dims_switchdisplay('wce_tree');
        document.getElementById('main_wce_content').style.width='100%';
        dims_xmlhttprequest('admin-light.php', 'op=xml_switchdisplay&wceautoresize=true&display='+valcour, true);
    }
}

window['removeEditObject'] = function removeEditObject(id) {
    for (key in arrayObject) {
        if (arrayObject[key] == id) {
            arrayObject.splice(key, 1);
        }
    }
    $("#"+id).removeClass('divselectedblocfilled');
}

window['moveScroller'] = function moveScroller() {
    var a = function() {
    var b = $(window).scrollTop();
    var d = $("#scroller-anchor").offset({scroll:false}).top;
    var c=$("#scroller");
    if (b>d) {
      c.css({position:"fixed",top:"0px"})
    } else {
      if (b<=d) {
        c.css({position:"relative",top:""})
      }
    }
    };
    $(window).scroll(a);a()
}

window['activeDimsBloc'] = function activeDimsBloc() {
    $("div.activedimsbloc").addClass('divselectedbloc');
    var $editbox = $('<div id="dims_editbox"></div>')
    .html('')
    .click(function(event){
        $(this).css('z-index',1000);
    })
    .dialog({
        autoOpen: false,
        title: '<? echo addslashes($_DIMS['cste']['_DIMS_PROPERTIES']);?>',
                    width:250,
                    height:460,
                    padding:'0.2em 0.2em',
        closeOnEscape: true,
        position:['right',0]
    });

    $("div.activedimsbloc").hover(
        function(event) {
            id_bloc=$(this).attr('id');
            arrayObject.push(id_bloc);

            for (key in arrayObject) {
                if (arrayObject[key] != id_bloc) {
                    removeEditObject(arrayObject[key]);
                }
            }

            current_hover_editobject=id_bloc;
            $(this).addClass('divselectedblocfilled');
        },
        function() {
            id_bloc=$(this).attr('id');
            if (current_editobject!=id_bloc) {
                removeEditObject(id_bloc);
            }
        }
    );

    $("div.activedimsbloc").click(
        function(event){
            id_bloc=$(this).attr('id');
            arraySelectObject.push(id_bloc);
            clearTimeout(timerselectwceobject);

            if (!$editbox.dialog( "isOpen" )) {
                boxedit=$editbox.dialog('open');
                Obj=boxedit;//$("#dims_editbox");
                var cal = $("#dims_editbox");
            }
            else {
                $editbox.dialog({ show: 'slide' });
            }
            var pos = $("#dims_editbox").offset();
            linkOffset = $("#"+id_bloc).position();
            linkLeft = $("#dims_editbox").position();
            linkHeight = $("#dims_editbox").height();
            scrolltop = $(window).scrollTop();
            $("#dims_editbox").dialog("option", "position", [(pos.left ), linkOffset.top + linkHeight - scrolltop]);

            timerselectwceobject = setTimeout("initCurrentEditObject('"+id_bloc+"')",100);
        });
        <?
        if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['selectedobject'])) {
            $obj=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['selectedobject'];
            echo '$("#'.$obj.'").click();$("#'.$obj.'").addClass("divselectedblocfilled");';
            unset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['selectedobject']);
        }
        ?>

}

window['initCurrentEditObject'] = function initCurrentEditObject(id_bloc) {
    var select="";
    for (key in arraySelectObject) {
        if (select=='') {
            if (current_editobject!='') {
                removeEditObject(current_editobject);
            }
            current_editobject=arraySelectObject[key];
            select=current_editobject;
        }
        else {
            removeEditObject(arraySelectObject[key]);
        }
    }

    $("#"+current_editobject).addClass('divselectedblocfilled');
    dims_xmlhttprequest_todiv('admin.php','dims_op=wce_edit_bloc&id_bloc='+current_editobject,'','dims_editbox');
    clearTimeout(timerselectwceobject);
    arraySelectObject = Array();
}

window['refreshTreeView'] = function refreshTreeView() {
    dims_xmlhttprequest_todiv('admin.php','dims_op=refreshTreeView','','wce_tree');
}
window['wceAddBlock'] = function wceAddBlock(section,lang) {
    if( arguments[0] == null) section='';
    if( arguments[1] == null) lang=0;

    dims_showcenteredpopup("",700,150,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=add_block&section='+section+"&lang="+lang,'','dims_popup');
}


window['wceModifBlock'] = function wceModifBlock(blockid,lang) {
    dims_showcenteredpopup("",700,150,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=modify_block&block_id='+blockid+"&lang="+lang,'','dims_popup');
}

window['wceModifDescription'] = function wceModifDescription(articleid) {
    dims_showcenteredpopup("",700,550,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=modify_desc_article&article_id='+articleid,'','dims_popup');
}

window['wceMoveBlockContent'] = function wceMoveBlockContent(sens,blockid,contentid) {
    dims_xmlhttprequest('admin.php','dims_op=move_block&block_id='+blockid+'&sens='+sens+"&contentid="+contentid);
    var f=document.getElementById('wce_frame_editor');
    f.contentWindow.location.reload(true);
}

window['wceModifBlockContent'] = function wceModifBlockContent(blockid) {
    dims_showcenteredpopup("",900,530,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=modify_blockcontent&block_id='+blockid,'','dims_popup');
}

function wceModifBlockContentCkeditor(blockid,contentid) {

    var instance = CKEDITOR.instances['block'+blockid+'_'+contentid];
    if(instance){
        CKEDITOR.remove(instance); //if existed then remove it
    }

    CKEDITOR.replace( 'block'+blockid+'_'+contentid,
    {
        customConfig : '/modules/wce/ckeditor/ckeditor_config_fr.js'
    });


}

window['activeWceDescription'] = function activeWceDescription (articleid) {
    var elem=document.getElementById('div_editproperties');
    if (elem.innerHTML=="") {
        elem.style.visibility='visible';
        elem.style.display='block';
        dims_xmlhttprequest_todiv('admin.php','op=article_edit_properties&articleid='+articleid,'','div_editproperties');
    }
}

window['activeWceButton'] = function activeWceButton(active) {
    if (active) {
        if (document.getElementById('edit')!=null) {
            document.getElementById('xToolbar').style.visibility='visible';
            document.getElementById('xToolbar').style.display='block';
            document.getElementById('edit').style.display='none';
        }

        if (document.getElementById('enreg')!=null) {
            document.getElementById('enreg').style.visibility='visible';
            document.getElementById('enreg').style.display='';
        }

        if (document.getElementById('enreg1')!=null) {
            document.getElementById('enreg1').style.visibility='visible';
            document.getElementById('enreg1').style.display='';
        }

        if (document.getElementById('back')!=null) {
            document.getElementById('back').style.visibility='visible';
            document.getElementById('back').style.display='';
        }

        if (document.getElementById('back1')!=null) {
            document.getElementById('back1').style.visibility='visible';
            document.getElementById('back1').style.display='';
        }
        currentmode="edit";

    }
    else {
        if (document.getElementById('onlineversion')!=null) {
            document.getElementById('onlineversion').style.visibility='visible';
            document.getElementById('onlineversion').style.display='';
        }
        if (document.getElementById('edit')!=null) {
            document.getElementById('edit').style.visibility='visible';
            document.getElementById('edit').style.display='';
        }
        if (document.getElementById('enreg')!=null) {
            document.getElementById('enreg').style.visibility='hidden';
            document.getElementById('enreg').style.display='none';
        }
        if (document.getElementById('enreg1')!=null) {
            document.getElementById('enreg1').style.visibility='hidden';
            document.getElementById('enreg1').style.display='none';
        }
    }
}

window['wce_refreshedit_article'] = function wce_refreshedit_article() {
    if (currentmode=="edit") {
        if (document.getElementById('edit')!=null) {
            document.getElementById('edit').style.visibility='visible';
            document.getElementById('edit').style.display='';
            activeWceButton(true);
        }
    }
}
window['wce_getcontent_article'] = function wce_getcontent_article(articleid,wce_mode,headingid) {
    var version=0;
    var cible="contentarticle";

    if (wce_mode=="edit" || wce_mode=="offline") {
        if (document.getElementById('onlineversion')!=null) {
            document.getElementById('offlineversion').style.visibility='hidden';
            document.getElementById('offlineversion').style.display='none';
        }
        if (document.getElementById('onlineversion')!=null) {
            document.getElementById('onlineversion').style.visibility='visible';
            document.getElementById('onlineversion').style.display='';
        }

        if ( wce_mode=="offline") {
            if (currentmode!="edit") {
                if (document.getElementById('publish')!=null) {
                    document.getElementById('publish').style.visibility='visible';
                    document.getElementById('publish').style.display='';
                    document.getElementById('publish1').style.visibility='visible';
                    document.getElementById('publish1').style.display='';
                }

                if (document.getElementById('edit')!=null) {
                    document.getElementById('edit').style.visibility='visible';
                    document.getElementById('edit').style.display='';
                }
            }
            else {
                if (document.getElementById('enreg')!=null) {
                    document.getElementById('enreg').style.visibility='visible';
                    document.getElementById('enreg').style.display='';
                    document.getElementById('enreg1').style.visibility='visible';
                    document.getElementById('enreg1').style.display='';
                }
            }
        }
        else {
            if (currentmode!="edit" && currentmode!="") {
                if (document.getElementById('edit')!=null) {
                    document.getElementById('edit').style.visibility='visible';
                    document.getElementById('edit').style.display='';
                }
                if (document.getElementById('publish')!=null) {
                    document.getElementById('publish').style.visibility='visible';
                    document.getElementById('publish').style.display='';
                    document.getElementById('publish1').style.visibility='visible';
                    document.getElementById('publish1').style.display='';
                }
            }
            else {

                document.getElementById('enreg').style.visibility='hidden';
                document.getElementById('enreg').style.display='none';
                document.getElementById('enreg1').style.visibility='hidden';
                document.getElementById('enreg1').style.display='none';
                document.getElementById('edit').style.visibility='hidden';
                document.getElementById('edit').style.display='none';

            }
        }
        document.getElementById('contentarticle').style.visibility='visible';
        document.getElementById('contentarticle').style.display='block';
        document.getElementById('contentarticleonline').style.visibility='hidden';
        document.getElementById('contentarticleonline').style.display='none';
        document.getElementById('contentarticleonline').innerHTML="";
    }
    if (wce_mode=="online") {
        document.getElementById('onlineversion').style.visibility='hidden';
        document.getElementById('onlineversion').style.display='none';
        document.getElementById('edit').style.visibility='hidden';
        document.getElementById('edit').style.display='none';
        document.getElementById('enreg').style.visibility='hidden';
        document.getElementById('enreg').style.display='none';
        document.getElementById('publish').style.visibility='hidden';
        document.getElementById('publish').style.display='none';
        document.getElementById('enreg1').style.visibility='hidden';
        document.getElementById('enreg1').style.display='none';
        document.getElementById('publish1').style.visibility='hidden';
        document.getElementById('publish1').style.display='none';
        document.getElementById('offlineversion').style.visibility='visible';
        document.getElementById('offlineversion').style.display='';
        document.getElementById('contentarticle').style.visibility='hidden';
        document.getElementById('contentarticle').style.display='none';
        document.getElementById('contentarticleonline').style.visibility='visible';
        document.getElementById('contentarticleonline').style.display='block';
        cible="contentarticleonline";
        adminedit="";
    }
    else {
        adminedit="&adminedit=1";
    }

    if (wce_mode!="offline") {
        var elt = document.getElementById(cible);
        elt.innerHTML="<div style=\"width:100%;height:280px;background:#FFFFFF url(./modules/wce/img/loading_text.gif) no-repeat scroll center bottom;\"><p align=\"center\" style=\"padding-top:120px\"><img src=\"./common/modules/wce/img/loading.gif\" alt=\"\">&nbsp;Chargement en cours...</p></div>";

        if (articleid>0) {
            if(document.getElementById('version')!=null) {
                version=document.getElementById('version').options[document.getElementById('version').selectedIndex].value;
            }
            dims_xmlhttprequest_todiv('admin.php','op=show_content&articleid='+articleid+'&wce_mode='+wce_mode+'&version='+version+adminedit,'',cible);
        }
        else {
            dims_xmlhttprequest_todiv('admin.php','op=show_content&headingid='+headingid+'&wce_mode='+wce_mode+adminedit,'',cible);
        }
    }
}

window['wce_showheading'] = function wce_showheading(hid,str) {
    elt = document.getElementById(hid+'_plus');
    if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
    else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
    else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
    else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');


    if (elt = document.getElementById(hid)) {
        if (elt.style.display == 'none') {
            if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('<? echo dims::getInstance()->getScriptEnv(); ?>','op=xml_detail_heading&hid='+hid+'&str='+str,'',hid);
            document.getElementById(hid).style.display='block';
        }
        else {
            document.getElementById(hid).style.display='none';
        }
    }
}

window['wce_heading_validate'] = function wce_heading_validate(form) {
    if (dims_validatefield('Libellé', form.wce_heading_label, 'string'))
        return true;

    return false;
}

window['wce_article_validate'] = function wce_article_validate(form, article_type, article_status, validator) {
    next = true;
    valid=false;
    if (article_type == 'draft') {
        if ( !validator) {
            // confirm sending tickets on waiting validation
            next = confirm('Cette action va envoyer\nune demande de publication\naux validateurs de cette rubrique\n\nÊtes-vous certain de vouloir continuer ?');
        }
    }

    if (next) {
        // get fckeditor content (iframe)
        isempty=true;

        if (document.getElementById('wce_frame_editor')!=null) {
            for (i=1;i<=19;i++) {
                res = document.getElementById('wce_frame_editor').contentWindow.wce_getcontent(i);

                if (res!=false && document.getElementById('fck_wce_article_draftcontent'+i)!=null) {
                    document.getElementById('fck_wce_article_draftcontent'+i).value=res;
                }
            }
        }
        valid=true;
    }

    if (valid) form.submit();

}

var timershowarbo;

window['wce_showarbo'] = function wce_showarbo(event,data) {
    dims_showpopup('',400, event);
    timershowarbo = setTimeout("execshowArbo('"+data+"')", 100);
}

window['execshowArbo'] = function execshowArbo(data) {
    clearTimeout(timershowarbo);
    dims_xmlhttprequest_todiv("admin-light.php",data,'',"dims_popup");
}

window['showArticleDescription'] = function showArticleDescription(id_article) {
    dims_showcenteredpopup("",950,650,'dims_popup');
    dims_xmlhttprequest_todiv('admin-light.php','op=article_description&id_article='+id_article,'','dims_popup');
}

window['GetScrollPage'] = function GetScrollPage(){
    var Left;
    var Top;
    var DocRef;

    if( window.innerWidth){
        with( window){
            Left   = pageXOffset;
            Top    = pageYOffset;
        }
    }
    else{ // Cas Explorer a part
        if( document.documentElement && document.documentElement.clientWidth)
            DocRef = document.documentElement;
        else
            DocRef = document.body;

        with( DocRef){
            Left   = scrollLeft;
            Top    = scrollTop;
        }
    }
    return({top:Top, left:Left});
}

window['ObjGetPosition'] = function ObjGetPosition(obj_){
    var PosX = 0;
    var PosY = 0;

    if( typeof(obj_)=='object')
        var Obj  = obj_;
    else
        var Obj  = document.getElementById( obj_);

    if( Obj){
        //-- Recup. Position Objet
        PosX = Obj.offsetLeft;
        PosY = Obj.offsetTop;

        /*
        if( Obj.offsetParent){
        //-- Tant qu'un parent existe
        while( Obj = Obj.offsetParent){
            if( Obj.offsetParent){ // on ne prend pas le BODY
                //-- Ajout position Parent
                PosX += Obj.offsetLeft;
                PosY += Obj.offsetTop;
            }
        }
        }*/
    }
    //-- Retour des positions
    return({left:PosX, top:PosY});
}

var IdTimer_1;
var IdTimer_2;
var O_DivScroll;
var Rapport = 1.0/4.0;  // On divise par 20
var Mini = 2 * Rapport;
//-----------------------
window['DIV_Scroll'] = function DIV_Scroll( id_){
    var Obj = document.getElementById( id_);
    this.Obj = Obj;
    if( Obj){
        Obj.style.position = "absolute"; // IMPERATIF
        //-- Recup position de depart
        var Pos   = ObjGetPosition( id_);
        this.PosX = Pos.left;
        this.PosY = Pos.top;
        this.DebX = this.PosX;
        this.DebY = this.PosY;
        this.NewX = 0;
        this.NewY = 0;
        this.Move = DIV_Deplace;
    }
}
//---------------------------
window['DIV_Deplace'] = function DIV_Deplace( x_, y_){
    if( arguments[0] != null){
        this.PosX = x_;
        this.Obj.style.left = parseInt(x_) +"px";
    }
    if( arguments[1] != null){
        this.PosY = y_;
        this.Obj.style.top  = parseInt(y_) +"px";
    }
}
//---------------------------
window['DIV_Replace'] = function DIV_Replace( x_, y_){
    //-- Calcul Delta deplacement
    var Delta_X = (x_ -O_DivScroll.PosX) *Rapport;
    var Delta_Y = (y_ -O_DivScroll.PosY) *Rapport;
    //-- Test si fin deplacement
    if((( Delta_Y < Mini)&&( Delta_Y > -Mini))&&
        (( Delta_X < Mini)&&( Delta_X > -Mini))){
       clearInterval( IdTimer_1);
       O_DivScroll.Move( x_, y_);
    }
    else{
        O_DivScroll.Move( O_DivScroll.PosX +Delta_X, O_DivScroll.PosY +Delta_Y);
    }
}


window['DIV_CheckScroll'] = function DIV_CheckScroll(){
    var Scroll  = GetScrollPage();
    //-- New position  du menu

    O_DivScroll.NewX = Scroll.left+O_DivScroll.DebX;

    //if (Scroll.top>O_DivScroll.DebY/2) O_DivScroll.NewY = Scroll.top;
    O_DivScroll.NewY = Scroll.top+35;
    //else O_DivScroll.NewY = Scroll.top  +O_DivScroll.DebY;

    //-- Si pas la bonne Position
    if(( O_DivScroll.PosY != O_DivScroll.NewY)||( O_DivScroll.PosX != O_DivScroll.NewX)){
        //-- Clear l'encours
        clearInterval( IdTimer_1);
        IdTimer_1 = setInterval("DIV_Replace(" + O_DivScroll.NewX +"," + O_DivScroll.NewY +")", 25);
    }
    return( true);
}
//-----------------------
window['DIV_InitScroll'] = function DIV_InitScroll(namediv){
    //-- Recup position Objet
    if (namediv ==null) namediv = 'xToolbar';
    O_DivScroll  = new DIV_Scroll(namediv);

    //-- Lance inspection si existe

    if( O_DivScroll.Obj)
        IdTimer_2 = setInterval('DIV_CheckScroll()',50);
}

window['checkAllArticles'] = function checkAllArticles(nbfiles) {
    for (i = 0; i < nbfiles; i++)
        document.getElementById("selart"+i).checked = true;
}

window['uncheckAllArticles'] = function uncheckAllArticles(nbfiles) {
    for (i = 0; i < nbfiles; i++)
        document.getElementById("selart"+i).checked = false;
}

window['validCommandArt'] = function validCommandArt(event,currentheading,nbfiles) {
    var cpte=0;
    var selectheadings="";

    for (i = 0; i < nbfiles; i++) {
        if (document.getElementById("selart"+i).checked) cpte++;
    }

    if (cpte==0) {
        alert("<? echo addslashes($_DIMS['cste']['_DIMS_MSG_MUSTSELECTEDELEMENT']);?>");
        document.getElementById('op').selectedIndex=0;
    }
    else {
        var elem=document.getElementById('op').selectedIndex;
        if (elem>0) {
            switch(elem) {
                case 1:
                    displayHeadingChoice(event,currentheading,selectheadings);
                break;

                case 2:
                    if (confirm('<? echo addslashes($_DIMS['cste']['_DIMS_CONFIRM']);?>')) {
                        document.listart.submit();
                    }
                break;
            }
        }
    }
}
window['displayHeadingChoice'] = function displayHeadingChoice(event,currentheading,selectheadings) {
    dims_showcenteredpopup("",600,600,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=choice_heading&currentheading='+currentheading+"&selectheadings="+selectheadings,'','dims_popup');
}

<?php
//}

?>
