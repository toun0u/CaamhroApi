<?
class enum_liens extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_action_detail';
	function enum_liens()
	{
		parent::dims_data_object(self::TABLE_NAME,'type_parent','type_enfant');
	}
}
