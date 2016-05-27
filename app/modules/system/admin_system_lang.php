<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CODE_OF_CONDUCT']);



$sql = 'SELECT
           *
        FROM
            dims_lang';
//echo $sql;
$ressource = $db->query($sql);

if($db->numrows($ressource) > 0) {
    $columns = array();
    $columns['auto'][0] = array('label' => '#');
    $columns['right'][3] = array('label' => '&nbsp;', 'width' => '100');
    $columns['right'][2] = array('label' => $_DIMS['cste']['_DIMS_LABEL_CODE_OF_CONDUCT'], 'width' => '300');
    $columns['right'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_LABEL'], 'width' => '150');

    $c = 0;
    $values = array();

    while($result = $db->fetchrow($ressource)) {

        $open   = $scriptenv.'?op=modify_lang&id_lang='.$result['id'];

	$action = '
	<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$open.'" title="'.$_DIMS['cste']['_MODIFY'].'">
	    <span class="ui-button-icon ui-icon ui-icon-wrench"></span>
	    <span class="ui-button-text">'.$_DIMS['cste']['_MODIFY'].'</span>
	</a>';

        $values[$c]['values'][0] = array('label' => $result['id'], 'style' => '');
        $values[$c]['values'][1] = array('label' => ucfirst($result['label']), 'style' => 'text-align:center');
        $values[$c]['values'][2] = array('label' => dims_strcut($result['code_of_conduct'],80), 'style' => 'text-align:center');
	$values[$c]['values'][3] = array('label' => $action, 'style' => 'text-align:center');
        $values[$c]['link'] = '';
        $values[$c]['style']= '';

        $c++;
    }

    $skin->display_array($columns, $values);
}
echo $skin->close_simplebloc();
?>
