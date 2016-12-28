<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(
	//Required for MODULES page
	'hop_new_relic_module_name'			=> 'Hop New Relic',
	'hop_new_relic_module_description'		=> 'Display New Relic data on your Control Panel',

	//General Wording
	'preference'						=> 'Preference',
	'save'								=> 'Save',
	'saving'							=> 'Saving...',
	'setting'							=> 'Setting',
	'settings'							=> 'Settings',
	
	//Index page
	'server_data'						=> 'Server Data',
	'app'								=> 'App',
	'no_app_sum_received'				=> 'No application summary data received from New Relic',
	'end_user'							=> 'End-User',
	'no_enduser_sum_received'			=> 'No End-User data received from New Relic',
	'server'							=> 'Server',
	'no_server_found_for_app'			=> 'No server found for this application.',
	'please_select_app'					=> 'Please select an app from the app list in the settings',

	// Settings
	'settings_saved_success'			=> 'Settings have been saved',
	'apps_list_refresh_failed_no_key'	=> 'Couldn\'t refresh the Apps list, the API key is missing',
	'apps_list_refresh_failed'			=> 'Refreshing the Apps list has failed',
	'apps_list_refresh_success'			=> 'Apps list has been updated',
	'apps_no_servers_list'				=> 'The New Relic app isn\'t providing any server linked to it',
	'apps_servers_received_empty_list'	=> 'We received an empty server list for that app',
	'app_servers_list_refresh_success'	=> 'Servers list has been updated',
	'servers_list_refresh_failed_no_key'=> 'Couldn\'t refresh the Severs list, the API key is missing',
	'servers_list_refresh_no_app'		=> 'Please select an app before refreshing the servers list',
	'error_refreshing_apps_list'		=> 'Error when refreshing the apps list',
	'error_refreshing_servers_list'		=> 'Error when refreshing the servers list',
	'success_alert_title'				=> 'Success',
	'new_relic_api_key'					=> 'New Relic API Key',
	'to_get_api_key_instructions'		=> 'To get that API key, follow <a target="_blank" href="%s">instructions on New Relic website</a>',
	'new_relic_app'						=> 'New Relic App',
	'new_relic_server'					=> 'New Relic Server',
	'choose_app_instructions'			=> 'Choose the app corresponding to this EE install. <a href="%s">Refresh app list</a>',
	'choose_server_instructions'		=> 'Choose the server corresponding to this EE install. <a href="%s">Refresh server list</a>',
	'inactive'							=> 'inactive',
	'active'							=> 'active',
	'not_healthy'						=> 'not healthy',
	
	// Custom Datasets
	'custom_datasets'					=> 'Custom Datasets',
	'metric_name'						=> 'Metric name',
	'metric_value'						=> 'Metric value',
	'get_metric_names_app_error'		=> 'Couldn\'t retrieve the different metrics available for that app',
	'get_metric_names_server_error'		=> 'Couldn\'t retrieve the different metrics available for that server',
	'delete_selected'					=> 'Delete selected',

	'new_custom_datasets'				=> 'New Custom Dataset',
	'custom_dataset_name'				=> 'Custom Dataset Name',
	'custom_dataset_name_desc'			=> 'Give a name to your preset',
	'custom_dataset_type'				=> 'Metric Type',
	'custom_dataset_type_desc'			=> 'Retrieve data about the app or data about the server',
	'app_metric_data'					=> 'App Metric Data',
	'app_metric_data_desc'				=> 'Choose what app data to retrieve',
	'server_metric_data'				=> 'Server Metric Data',
	'server_metric_data_desc'			=> 'Choose what server data to retrieve',
	'error_no_metric_type_selected'		=> 'No Metric type selected',
);
