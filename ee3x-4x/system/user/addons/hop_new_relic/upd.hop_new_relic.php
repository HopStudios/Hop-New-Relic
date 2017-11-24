<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/settings_helper.php';

class Hop_new_relic_upd {

	var $version = HOP_NEW_RELIC_VERSION;

	/**
	 * Install the module, create DB tables
	 * @return [type] [description]
	 */
	function install()
	{
		ee()->load->dbforge();

		$data = array(
			'module_name' =>  ucfirst(HOP_NEW_RELIC_NAME) ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		ee()->db->insert('modules', $data);

		//Create our tables

		$fields = array(
			'id'	=> array('type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name'	=> array('type' => 'VARCHAR', 'constraint' => '100'),
			'value'	=> array('type' => 'TEXT')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('name');

		ee()->dbforge->create_table(HOP_NEW_RELIC_NAME.'_settings');

		unset($fields);

		Hop_new_relic_settings_helper::save_default_settings();

		return TRUE;
	}

	/**
	 * Uninstall the module and delete its tables
	 * @return [type] [description]
	 */
	function uninstall()
	{
		ee()->load->dbforge();

		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' =>  ucfirst(HOP_NEW_RELIC_NAME)));

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		ee()->db->where('module_name', HOP_NEW_RELIC_NAME);
		ee()->db->delete('modules');

		ee()->db->where('class', HOP_NEW_RELIC_NAME);
		ee()->db->delete('actions');

		//Uninstall our tables
		ee()->dbforge->drop_table(HOP_NEW_RELIC_NAME.'_settings');

		return TRUE;
	}

	function update($current = '')
	{
		ee()->load->dbforge();

		if (version_compare($current, $this->version, '='))
		{
			return FALSE;
		}

		/*
		if (version_compare($current, '2.0', '<'))
		{
			// Do your update code here
		}
		*/

		return TRUE;
	}

}
