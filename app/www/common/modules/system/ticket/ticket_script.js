/*
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */


//javascript:dims_showcenteredpopup('', 500, 500, 'dims_popup');
//dims_xmlhttprequest_todiv('admin.php','dims_op=add_action_comment&id_action=<? echo $action->getActionId(); ?>','','dims_popup');

function showTicket(id_ticket, ticket_operation,scriptenv){
    if (scriptenv == null)
        scriptenv = "admin.php"
    dims_showcenteredpopup("", 500, 500, 'dims_popup');
    dims_xmlhttprequest_todiv(scriptenv, 'dims_op=ticket_manager&ticket_op='+ticket_operation+'&id_ticket='+id_ticket, '', 'dims_popup');
}

function changeStatusTicket(id_ticket, status, ticket_operation){
    //todo
}

function forwardTicket(id_ticket, ticket_operation){
    //todo
}

function showLinkedObject(id_ticket, ticket_operation, div){
    //todo
}

function changeSelector(destination, id_user, operation,scriptenv){
    var source ;
    source = dims_getelem('bouton_source').value ;
    dims_getelem(source).disabled = false;
    dims_getelem(destination).disabled = true;
    dims_getelem('bouton_source').value = destination;
    dims_xmlhttprequest_todiv(scriptenv, 'dims_op=ticket_manager&ticket_op='+operation+'&id_user='+id_user,'','message_box');

}
function createTicket(ticket_operation,id_dest,scriptenv){
    if (scriptenv == null)
        scriptenv = "admin.php"
    dims_showcenteredpopup("", 500, 500, 'dims_popup');
    dims_xmlhttprequest_todiv(scriptenv, 'dims_op=ticket_manager&ticket_op='+ticket_operation+'&id_dest='+id_dest, '', 'dims_popup');
}
var currentList = null;
// fonction d'autocomplete prenant comme valeurs une liste de type [id : val, label : label, type : type_sender]
function autocompletion(idDivSearch,idDivData,lstValues,initDest){
    currentList = lstValues;

    $("input#"+idDivSearch)
    .wrap('<span class="divAutocomplete" onclick="javascript:$(this).children(\'input#'+idDivSearch+'\').focus();"></span>')
    .ready(function(){
        if (initDest != null && initDest.length > 0){
            $('<span id="complet_'+initDest[0].id+'-'+initDest[0].type+'" class="selectedCompletion"><span>'+initDest[0].label+'</span><img onclick="javascript:deleteAutocompletion(this,\''+initDest[0].id+'-'+initDest[0].type+'\',\''+idDivData+'\',\''+idDivSearch+'\');" src="./common/img/delete.png" /></span>').insertBefore($("input#"+idDivSearch));
            $("input#"+idDivData).val(initDest[0].id+'-'+initDest[0].type);
        }
    })
    .bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ) {
            event.preventDefault();
        }else if (event.keyCode == $.ui.keyCode.BACKSPACE && this.value == ''){
            var destFor = $("input#"+idDivData).val().split(/;\s*/);

            var lastElem = destFor[destFor.length-1].split(/-/);
            currentList[currentList.length] = { 'id' : lastElem[0], 'label' : $(this).parents("span.divAutocomplete").children("span.selectedCompletion:last span").html(), 'type' : lastElem[1]};

            $(this).parents("span.divAutocomplete").children("span.selectedCompletion:last").remove();

            destFor.pop();
            $("input#"+idDivData).val(destFor.join(";"));

            $(this).autocomplete("option", "source", function (request, response){ response($.ui.autocomplete.filter(currentList,request.term.split(/,\s*/).pop())); });
        }
    })
    .autocomplete({
                    source: function (request, response){
                            response($.ui.autocomplete.filter(currentList,request.term.split(/,\s*/).pop()));
                        },
                    select: function(event, ui){
                            var destFor = $("input#"+idDivData).val().split(/;\s*/);
                            destFor.push(ui.item.id+"-"+ui.item.type);
                            $("input#"+idDivData).val(destFor.join(";"));
                            this.value = '';
                            for(i = 0; i < currentList.length; i++){
                                if (currentList[i].id == ui.item.id && ui.item.type == currentList[i].type){
                                    var tabFirst = currentList.slice(0,i);
                                    var tabLast = currentList.slice(i+1,lstValues.length-1);
                                    currentList = tabFirst.concat(tabLast);
                                }
                            }
                            $('<span id="complet_'+ui.item.id+'-'+ui.item.type+'" class="selectedCompletion"><span>'+ui.item.value+'</span><img onclick="javascript:deleteAutocompletion(this,\''+ui.item.id+'-'+ui.item.type+'\',\''+idDivData+'\',\''+idDivSearch+'\');" src="./common/img/delete.png" /></span>').insertBefore(this);
                            $(this).autocomplete("option", "source", function (request, response){ response($.ui.autocomplete.filter(currentList,request.term.split(/,\s*/).pop())); });
                            return false;
                        },
                    focus: function(event, ui){
                            //this.value = ui.item.label;
                            return false;
                        }
    })
    .data( "autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( "<a>" + item.label + "</a>" )
            .appendTo( ul );
    };
}
function deleteAutocompletion(elem,id,idDivData,idDivSearch){
    var destFor = $("input#"+idDivData).val().split(/;\s*/);
    var tmp = Array();
    for (i = 0; i < destFor.length; i++){
        if (destFor[i] != id && destFor[i] != '')
            tmp.push(destFor[i]);
    }

    var lastElem = id.split(/-/);
    currentList[currentList.length] = { 'id' : lastElem[0], 'label' : $(elem).parents("span.selectedCompletion[id='complet_"+id+"']").children("span").html(), 'type' : lastElem[1]};
    $(elem).parents("span.divAutocomplete").children("span.selectedCompletion[id='complet_"+id+"']").remove();

    $("input#"+idDivSearch).autocomplete("option", "source", function (request, response){ response($.ui.autocomplete.filter(currentList,request.term.split(/,\s*/).pop())); });

    $("input#"+idDivData).val(tmp.join(";"));
}
