<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_newsletter_tag
 *
 * @author pat
 */
class newsletter_tag extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter_tag';

    function __construct() {
        parent::dims_data_object(self::TABLE_NAME);
    }
}
