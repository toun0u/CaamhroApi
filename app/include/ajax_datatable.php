<?php

/**
 * Description of ajax_datatable
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2012
 */
interface ajax_datatable {

    /**
     * Retour le paramètre sTable.
     * Celui-ci doit définir le FROM de la requête alimentant le tableau
     */
    public function get_sTable();
    public function get_aColumns();
    public function get_sIndexColumn();
    public function get_sWhere();
    public function get_aaData($res_query);


}

?>
