<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/config.php';
require_once PATH_THIRD.'hop_new_relic/api/new_relic_api.php';

/**
 * Helper class for handling the add-on settings
 */
class Hop_new_relic_settings_helper
{
	/**
	 * Get array with default settings
	 * @return array Settings
	 */
	private static function _get_default_settings()
	{
		return array(
			'nr_api_key'						=> '', // The API Key from New Relic
			'nr_apps_list'						=> '', // The apps list associated with the account
			'nr_selected_app_servers'			=> '', // The servers from the selected app
			'user_datasets'						=> '', // Saved user datasets
		);
	}

	/**
	 * Get settings saved into DB
	 * @return Collection Settings
	 */
	public static function get_settings()
	{
		$settings = ee('Model')->get('hop_new_relic:Hnp_settings')->all();
		return $settings;
	}

	/**
	 * Get settings saved into DB as an array[setting_name] = setting_value
	 * @return array settings
	 */
	public static function get_settings_as_array()
	{
		$settings = self::get_settings();
		$_settings = array();

		foreach ($settings as $setting)
		{
			$_settings[$setting->name] = $setting->value;
		}

		return $_settings;
	}

	/**
	 * Get one unique setting
	 * @param  string $setting_name [description]
	 * @return string|null		  [description]
	 */
	public static function get_setting($setting_name)
	{
		$setting = ee('Model')->get('hop_new_relic:Hnp_settings')->filter('name', $setting_name)->first();
		if ($setting)
		{
			return $setting->value;
		}
		return NULL;
	}

	/**
	 * Get the selected New Relic App
	 */
	public static function get_selected_app()
	{
		$serialized_selected_app = self::get_setting('nr_selected_app');
		if ($serialized_selected_app != NULL && $serialized_selected_app != '')
		{
			return unserialize($serialized_selected_app);
		}
		return NULL;
	}

	/**
	 * Get the selected New Relic Server
	 */
	public static function get_selected_server()
	{
		$serialized_selected_server = self::get_setting('nr_selected_app_selected_server');
		if ($serialized_selected_server != NULL && $serialized_selected_server != '')
		{
			return unserialize($serialized_selected_server);
		}
		return NULL;
	}

	public static function get_user_datasets()
	{
		$user_datasets_ser = self::get_setting('user_datasets');
		if ($user_datasets_ser != NULL && $user_datasets_ser != '')
		{
			return unserialize($user_datasets_ser);
		}
		return NULL;
	}

	public static function get_user_dataset($id)
	{
		$user_datasets_ser = self::get_setting('user_datasets');
		if ($user_datasets_ser != NULL && $user_datasets_ser != '')
		{
			return unserialize($user_datasets_ser);
		}
		return NULL;
	}

	/**
	 * Add a new dataset and save it
	 * @param array $dataset array correctly structured containing the new dataset
	 * @return id of the newly added dataset
	 */
	public static function add_user_dataset($dataset)
	{
		$user_datasets = self::get_user_datasets();
		if ($user_datasets == NULL)
		{
			$array = array($dataset);
			self::save_setting('user_datasets', serialize($array));
			return 0; // return index/id
		}
		else
		{
			$user_datasets[] = $dataset;
			self::save_setting('user_datasets', serialize($user_datasets));
			end($user_datasets); // move the internal pointer to the end of the array
			$key = key($user_datasets);
			return $key;
		}
	}

	/**
	 * Remove a user custom dataset from saved params
	 * @param int $id The id of the dataset to delete
	 * @return bool TRUE if dataset deleted, FALSE if not (dataset doesn't exist)
	 */
	public static function remove_user_dataset($id)
	{
		$user_datasets = self::get_user_datasets();
		if ($user_datasets != NULL)
		{
			if (array_key_exists($id, $user_datasets))
			{
				unset($user_datasets[$id]);
				self::save_setting('user_datasets', serialize($user_datasets));
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	/**
	 * Create default settings objects and save them into db
	 * If the setting already exists, it won't be saved
	 * @return void
	 */
	public static function save_default_settings()
	{
		foreach (self::_get_default_settings() as $setting_name => $setting_value)
		{
			$setting = ee('Model')->make('hop_new_relic:Hnp_settings', array('name' => $setting_name));
			$result = $setting->validate();
			if ($result->isValid())
			{
				$setting->save();
			}
		}
	}

	/**
	 * Save Add-on settings into database
	 * @param  array  $settings [description]
	 * @return array			[description]
	 */
	public static function save_settings($settings = array())
	{
		foreach ($settings as $setting_name => $setting_value)
		{
			self::save_setting($setting_name, $setting_value);
		}
	}

	/**
	 * Save a single setting into database (will override if exists)
	 * @param  string	$setting_name	The setting name to save
	 * @param  string	$setting_value	The setting value
	 * @return bool						True if setting saved, false otherwise
	 */
	public static function save_setting($setting_name, $setting_value)
	{
		$setting = ee('Model')->get('hop_new_relic:Hnp_settings')->filter('name', $setting_name)->first();
		if ($setting)
		{
			$setting->value = $setting_value;
		}
		else
		{
			$setting = ee('Model')->make('hop_new_relic:Hnp_settings', array('name' => $setting_name, 'value' => $setting_value));
		}

		$result = $setting->validate();
		if ($result->isValid())
		{
			$setting->save();
			return TRUE;
		}
		return FALSE;
	}

}