<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/config.php';
require_once PATH_THIRD.'hop_new_relic/api/new_relic_api.php';

/**
 * General Helper class for the add-on
 * 
 */
class Hop_new_relic_settings_helper
{
	private static $_settings_table_name = "hop_new_relic_settings";
	private static $_settings;

	/**
	 * Get array with default settings
	 * @return array Settings
	 */
	private static function _get_default_settings()
	{
		return array(
			'nr_api_key'						=> '',
			'nr_apps_list'						=> '', // The apps list associated with the account
			'nr_selected_app_servers'			=> '', // The servers from the selected app
			'user_datasets'						=> '', // Saved user datasets
		);
	}

	/**
	 * Get settings saved into DB; if no settings found, get default ones.
	 * @return array Settings
	 */
	public static function get_settings()
	{
		if (! isset(self::$_settings) || self::$_settings == null)
		{
			$settings = array();
			//Get the actual saved settings
			$query = ee()->db->get(self::$_settings_table_name);
			foreach ($query->result_array() as $row)
			{
				$settings[$row["setting_name"]] = $row["setting_value"];
			}
			self::$_settings = array_merge(self::_get_default_settings(), $settings);
		}
		return self::$_settings;
	}

	/**
	 * Get one unique setting
	 * @param  string $setting_name [description]
	 * @return string|null		  [description]
	 */
	public static function get_setting($setting_name)
	{
		if (! isset(self::$_settings))
		{
			//Load the settings from DB if not already done
			self::get_settings();
		}
		if (array_key_exists($setting_name, self::$_settings))
		{
			return self::$_settings[$setting_name];
		}
		return null;
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
	 * Save Add-on settings into database
	 * @param  array  $settings [description]
	 * @return array			[description]
	 */
	public static function save_settings($settings = array())
	{
		//be sure to save all settings possible
		$_tmp_settings = array_merge(self::_get_default_settings(), $settings);
		//No way to do INSERT IF NOT EXISTS so...
		foreach ($_tmp_settings as $setting_name => $setting_value)
		{
			$query = ee()->db->get_where(self::$_settings_table_name, array('setting_name'=>$setting_name), 1, 0);
			if ($query->num_rows() == 0) {
			  // A record does not exist, insert one.
			  $query = ee()->db->insert(self::$_settings_table_name, array('setting_name' => $setting_name, 'setting_value' => $setting_value));
			} else {
			  // A record does exist, update it.
			  $query = ee()->db->update(self::$_settings_table_name, array('setting_value' => $setting_value), array('setting_name'=>$setting_name));
			}
		}
		self::$_settings = $_tmp_settings;
	}

	/**
	 * Save a single setting into database (will override if exists)
	 * @param  [type] $setting_name  [description]
	 * @param  [type] $setting_value [description]
	 * @return [type]				[description]
	 */
	public static function save_setting($setting_name, $setting_value)
	{
		$query = ee()->db->get_where(self::$_settings_table_name, array('setting_name'=>$setting_name), 1, 0);
		if ($query->num_rows() == 0) {
		  // A record does not exist, insert one.
		  $query = ee()->db->insert(self::$_settings_table_name, array('setting_name' => $setting_name, 'setting_value' => $setting_value));
		} else {
		  // A record does exist, update it.
		  $query = ee()->db->update(self::$_settings_table_name, array('setting_value' => $setting_value), array('setting_name'=>$setting_name));
		}

		//Refresh our local copy of the settings
		self::$_settings = null;
		self::get_settings();
	}

	/**
	 * Helper function to convert bytes into a human readable value
	 * @param  [type]  $bytes	 [description]
	 * @param  integer $precision [description]
	 * @return [type]			 [description]
	 */
	public static function format_bytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('', 'kB', 'MB', 'GB', 'TB');

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}

	/**
	 * Helper function to convert a number or an array of numbers
	 * @param number|array $data      The data to convert
	 * @param number       $divider   The number to divide the origin data by
	 * @param int          $precision {optionnal) The precision to round the number to
	 */
	public static function divider_conversion($data, $divider, $precision = NULL)
	{
		if (is_array($data))
		{
			$results = array();
			foreach($data as $value)
			{
				$result = $value / $divider;
				if ($precision != NULL)
				{
					$result = round($result, $precision);
				}
				$results[] = $result;
			}

			return $results;
		}
		else
		{
			$result = $value / $divider;
			if ($precision != NULL)
			{
				$result = round($result, $precision);
			}
			return $result;
		}
	}

}