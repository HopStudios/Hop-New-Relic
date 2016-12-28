<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/settings_helper.php';
require_once PATH_THIRD.'hop_new_relic/data_helper.php';

class Hop_new_relic_mcp
{
	/**
	 * Build the navigation menu for the module
	*/
	function build_nav()
	{
		ee()->cp->set_right_nav(array(
			lang('hop_new_relic_module_name')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME,
			lang('settings')					=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=settings',
		));

		//Load our magnificent CSS
		ee()->cp->add_to_head('<link rel="stylesheet" href="'.URL_THIRD_THEMES.HOP_NEW_RELIC_NAME.'/css/style.css" type="text/css" media="screen">');

		//Load JS
		ee()->cp->add_to_head('<script type="text/javascript" src="'.URL_THIRD_THEMES.HOP_NEW_RELIC_NAME.'/javascript/Chart.min.js"></script>');
		ee()->cp->add_to_head('<script type="text/javascript" src="'.URL_THIRD_THEMES.HOP_NEW_RELIC_NAME.'/javascript/script.js"></script>');
	}

	/**
	 * Display homepage of the addon
	 * We're retrieving some data from New Relic to display to the user
	 * @return [type] Page view
	 */
	function index()
	{
		$this->build_nav();
		ee()->view->cp_page_title = lang('hop_new_relic_module_name');
		$vars = array();
		$vars['chart_form_action'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=get_nr_metrics';
		$vars['datasets'] = array();
		$vars['errors'] = array();

		$selected_app = Hop_new_relic_settings_helper::get_selected_app();
		$api_key =  Hop_new_relic_settings_helper::get_setting('nr_api_key');
		if ($selected_app != null && $api_key != null)
		{
			$vars['selected_app'] = $selected_app;
			$new_relic_api = new New_Relic_Api($api_key);

			// Get Summary data
			// TODO: Cache results ?
			$app_summary = $new_relic_api->get_app_summary($selected_app->id);
			if ($app_summary == null) { $vars['app'] = -1; } else { $vars['app'] = $app_summary; }

			$selected_server = Hop_new_relic_settings_helper::get_selected_server();
			if ($selected_server != null)
			{
				// TODO: Cache results ?
				$server_summary = $new_relic_api->get_server_summary($selected_server->id);
				if ($server_summary != null && !isset($server_summary->error))
				{
					$vars['server'] = $server_summary;
					if (isset($server_summary->summary->memory_used))
					{
						$vars['server_memory_used'] = Hop_new_relic_settings_helper::format_bytes($server_summary->summary->memory_used, 0);
					}
					if (isset($server_summary->summary->memory_total))
					{
						$vars['server_memory_total'] = Hop_new_relic_settings_helper::format_bytes($server_summary->summary->memory_total, 0);
					}
				}
				else
				{
					$vars['server'] = -1;
					if (isset($server_summary->error))
					{
						$vars['errors'][] = $server_summary->error->title;
					}
				}

				$nr_helper = new Hop_new_relic_data_helper($api_key, $selected_app, $selected_server);

				$vars['predefined_datasets'] = $nr_helper->get_all_predefined_data_details();
				$vars['user_predefined_datasets'] = $nr_helper->get_user_custom_datasets();
				$vars['predefined_time_ranges'] = $nr_helper->get_all_predefined_time_ranges();
			}

		}

		return ee()->load->view('index', $vars, TRUE);
	}

	/**
	 * Display settings view
	 * @return [type] [description]
	 */
	function settings()
	{
		$this->build_nav();
		ee()->view->cp_page_title = lang('settings');
		$vars = array();

		$nr_api_key = Hop_new_relic_settings_helper::get_setting('nr_api_key');
		$nr_apps_list = Hop_new_relic_settings_helper::get_setting('nr_apps_list');
		$nr_app_servers_list = Hop_new_relic_settings_helper::get_setting('nr_selected_app_servers');

		//Get Apps from NewRelic if we don't have them yet
		if ($nr_api_key != null && $nr_api_key != "" && $nr_apps_list == null)
		{
			$nr_api = new New_Relic_Api($nr_api_key);
			$apps = $nr_api->get_applications();
			if (!empty($apps))
			{
				$apps_list = array();
				//Use the app id as index for our array
				foreach ($apps as $app)
				{
					$apps_list[$app->id] = $app;
				}

				//Saving the list of apps into DB
				Hop_new_relic_settings_helper::save_setting('nr_apps_list', serialize($apps_list));
			}
		}

		$settings_db = Hop_new_relic_settings_helper::get_settings();

		if (ee()->input->post('action') == "save_settings")
		{
			$settings = array();
			$form_is_valid = TRUE;

			//Validate our fields here
			// if (ee()->input->post('nr_api_key') == "" )
			// {
			//     $settings["nr_api_key"] = ee()->input->post('nr_api_key');
			//     $form_is_valid = FALSE;
			//     $vars["form_error_cache"] = 'api key not valid';
			// }

			$settings['nr_api_key'] = ee()->input->post('nr_api_key');

			//Save the selected app into DB
			if (array_key_exists('nr_apps_list', $settings_db) && $settings_db['nr_apps_list'] != '')
			{
				$apps_list = unserialize($settings_db['nr_apps_list']);
				$selected_app_id = ee()->input->post('nr_selected_app_id');
				if ($selected_app_id != "" && array_key_exists($selected_app_id, $apps_list))
				{
					// If we select a different app, delete the selected server from the settings
					$serialized_selected_app = Hop_new_relic_settings_helper::get_setting('nr_selected_app');
					if ($serialized_selected_app != null)
					{
						$selected_app = unserialize($serialized_selected_app);
						if ($selected_app_id != $selected_app->id)
						{
							Hop_new_relic_settings_helper::save_setting('nr_selected_app_selected_server', '');
							unset($settings_db['nr_selected_app_selected_server']);
							unset($settings['nr_selected_app_selected_server']);
							unset($settings_db['nr_selected_app_servers']);
						}
					}

					//Save the complete data of the selected app
					$settings['nr_selected_app'] = serialize($apps_list[$selected_app_id]);
				}

				// Save selected server into DB
				if (array_key_exists('nr_selected_app_servers', $settings_db) && $settings_db['nr_selected_app_servers'] != '')
				{
					$servers_list = unserialize($settings_db['nr_selected_app_servers']);
					$selected_server_id = ee()->input->post('nr_selected_app_selected_server_id');
					if ($selected_server_id != "" && array_key_exists($selected_server_id, $servers_list))
					{
						// Save the complete data of the selected server
						$settings['nr_selected_app_selected_server'] = serialize($servers_list[$selected_server_id]);
					}
				}
			}

			if ($form_is_valid)
			{
				Hop_new_relic_settings_helper::save_settings($settings);
				
				ee()->session->set_flashdata('message_success', lang('settings_saved_success'));
				
				ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=settings');
			}
			else
			{
				$vars["settings"] = $settings;
			}
		}

		// No data received, means we'll load saved settings
		if (!isset($form_is_valid))
		{
			$vars["settings"] = $settings_db;
		}
		$vars['refresh_app_list_link'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=refresh_apps_list';
		$vars['refresh_server_list_link'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=refresh_servers_list';
		$vars['action_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=settings';
		$vars['form_hidden'] = array('action' => 'save_settings');
		if (array_key_exists('nr_selected_app', $vars['settings']) )
		{
			$settings = $vars['settings'];
			$selected_app = unserialize($settings['nr_selected_app']);
			$vars['nr_selected_app_id'] = $selected_app->id;
			// If we have a selected app, but no list of servers for the app, or if the list is empty
			if ( (!array_key_exists('nr_selected_app_servers', $vars['settings']) || $vars['settings']['nr_selected_app_servers'] == "" ) 
				&& $nr_api_key != null && $nr_api_key != ""
				&& isset($selected_app->links) && isset($selected_app->links->servers)
				&& count($selected_app->links->servers) > 0)
			{
				$nr_api = new New_Relic_Api($nr_api_key);

				$servers = $nr_api->get_servers($selected_app->links->servers);

				$servers_list = array();
				//Use the app id as index for our array
				foreach ($servers as $server)
				{
					$servers_list[$server->id] = $server;
				}

				//Saving the list of servers into DB
				Hop_new_relic_settings_helper::save_setting('nr_selected_app_servers', serialize($servers_list));

				// Add the new list to settings that we'll pass to the template
				$vars['settings']['nr_selected_app_servers'] = serialize($servers_list);
			}
		}

		// Load saved selected server id
		if (array_key_exists('nr_selected_app_selected_server', $vars['settings']) && $vars['settings']['nr_selected_app_selected_server'] != '')
		{
			$settings = $vars['settings'];
			$selected_server = unserialize($settings['nr_selected_app_selected_server']);
			$vars['nr_selected_app_selected_server_id'] = $selected_server->id;
		}

		return ee()->load->view('settings', $vars, TRUE);
	}

	/**
	 * Refresh the Apps list
	 * @return [type] [description]
	 */
	function refresh_apps_list()
	{
		$nr_api_key = Hop_new_relic_settings_helper::get_setting('nr_api_key');
		if ($nr_api_key != null && $nr_api_key != "")
		{
			$nr_api = new New_Relic_Api($nr_api_key);
			$apps = $nr_api->get_applications();
			if (!empty($apps))
			{
				$apps_list = array();
				//Use the app id as index for our array
				foreach ($apps as $app)
				{
					$apps_list[$app->id] = $app;
				}

				//Saving the list of apps into DB
				Hop_new_relic_settings_helper::save_setting('nr_apps_list', serialize($apps_list));

				ee()->session->set_flashdata('message_success', lang('apps_list_refresh_success'));
			}
			else
			{
				//Tried to get the data but we don't have them
				ee()->session->set_flashdata('message_error', lang('apps_list_refresh_failed'));
			}
		}
		else
		{
			//No API Key set
			ee()->session->set_flashdata('message_error', lang('apps_list_refresh_failed_no_key'));
		}

		ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=settings');
	}

	/**
	 * Refresh the Servers list (servers where the app is deployed)
	 */
	function refresh_servers_list()
	{
		// nr_selected_app_servers
		$nr_api_key = Hop_new_relic_settings_helper::get_setting('nr_api_key');
		$selected_app = Hop_new_relic_settings_helper::get_selected_app();
		if ($selected_app != null && $nr_api_key != null && $nr_api_key != "")
		{
			if (isset($selected_app->links) && is_array($selected_app->links->servers))
			{
				$nr_api = new New_Relic_Api($nr_api_key);
				$servers_result = $nr_api->get_servers($selected_app->links->servers);

				// print_r($servers_result);
				if (!empty($servers_result))
				{
					$servers_list = array();
					//Use the server id as index for our array
					foreach ($servers_result as $server)
					{
						$servers_list[$server->id] = $server;
					}

					//Saving the list of servers into DB
					Hop_new_relic_settings_helper::save_setting('nr_selected_app_servers', serialize($servers_list));
					
					ee()->session->set_flashdata('message_success', lang('app_servers_list_refresh_success'));
				}
				else
				{
					ee()->session->set_flashdata('message_error', lang('apps_servers_received_empty_list'));
				}
			}
			else
			{
				ee()->session->set_flashdata('message_error', lang('apps_no_servers_list'));
			}
			
		}
		else if ($serialized_selected_app == null || $serialized_selected_app == "")
		{
			ee()->session->set_flashdata('message_error', lang('servers_list_refresh_no_app'));
		}
		else if ($nr_api_key == null || $nr_api_key == "")
		{
			ee()->session->set_flashdata('message_error', lang('servers_list_refresh_failed_no_key'));
		}
		ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HOP_NEW_RELIC_NAME.AMP.'method=settings');
	}

	/**
	 * Get New Relic metrics
	 * Used by the index page to display charts (AJAX call)
	 * @return [type] [description]
	 */
	function get_nr_metrics()
	{
		$selected_app = Hop_new_relic_settings_helper::get_selected_app();
		$selected_server = Hop_new_relic_settings_helper::get_selected_server();
		$nr_api_key =  Hop_new_relic_settings_helper::get_setting('nr_api_key');

		if ($selected_app != null
			&& $selected_server != null
			&& $nr_api_key != null && $nr_api_key != '')
		{
			$nr_helper = new Hop_new_relic_data_helper($nr_api_key, $selected_app, $selected_server);

			$dataset_name = ee()->input->get('metric_simple_name');

			$server_metrics = $nr_helper->get_nr_data(
				$dataset_name,
				intval(ee()->input->get('timerange'))
			);

			// print_r($server_metrics);

			// Control for errors
			if (isset($server_metrics->error))
			{
				$vars['errors'][] = $server_metrics->error->title;
				$structured_data = array('error' => true, 'message' => $server_metrics->error->title);
			}
			else if (count($server_metrics->metrics_not_found) > 0)
			{
				// We retrieving one metric at a time, if there's one which is not found,
				// that means we received no data
				$structured_data = array('error' => true, 'message' => "That metric is not found/not available.");
			}
			else
			{
				$structured_data = $nr_helper->create_graph_dataset(
					ee()->input->get('metric_simple_name'), 
					$server_metrics->metrics[0]
					);
			}

			ee()->output->send_ajax_response($structured_data);
		}
	}

	/**
	 * List and display form for custom data sets
	 */
	function custom_datasets()
	{
		$this->build_nav();
		$vars = array();

		$data_helper = new Hop_new_relic_data_helper();
		$user_datasets = $data_helper->get_user_custom_datasets();

		$vars['user_datasets'] = $user_datasets;
		$vars['cp_page_title'] = lang('custom_datasets');
		$vars['base_url'] = ee('CP/URL', 'addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets')->compile();
		$vars['save_btn_text'] = lang('save');
		$vars['save_btn_text_working'] = lang('saving');
		$vars['form_url'] = ee('CP/URL')->make('addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets_remove');

		// Specify other options
		$table = ee('CP/Table', array('sortable' => FALSE));
		$table->setColumns(
			array(
				'id',
				'name',
				lang('metric_name'),
				lang('metric_value'),
				array(
					'type'  => Table::COL_CHECKBOX
				)
			)
		);

		$data = array();
		foreach ($user_datasets as $key => $user_dataset)
		{
			$data[] = array(
				$key,
				$user_dataset['title'],
				$user_dataset['names'][0],
				$user_dataset['values'][0],
				array(
					'name' => 'datasets[]',
					'value' => $key,
					'data'  => array(
						'confirm' => lang('dataset') . ': <b>' . htmlentities($user_dataset['title'], ENT_QUOTES) . '</b>'
					)
				)
			);
		}

		$table->setData($data);

		$vars['table'] = $table->viewData(ee('CP/URL', 'addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets'));

		$modal_vars = array(
			'name'		=> 'modal-confirm-remove',
			'form_url'	=> ee('CP/URL')->make('addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets_remove')
		);
		$modal_html = ee('View')->make('ee:_shared/modal_confirm_remove')->render($modal_vars);
		ee('CP/Modal')->addModal('remove', $modal_html);

		ee()->cp->add_js_script(array(
			'file' => array('cp/confirm_remove'),
		));

		ee()->javascript->compile();

		return array(
			'heading'			=> lang('custom_datasets'),
			'body'				=> ee('View')->make(HOP_NEW_RELIC_NAME.':custom_datasets')->render($vars),
			'breadcrumb'		=> array(
				ee('CP/URL', 'addons/settings/'.HOP_NEW_RELIC_NAME)->compile() => lang('hop_new_relic_module_name')
			),
		);
	}

	function new_custom_dataset()
	{
		$this->build_nav();
		$vars = array();

		$vars['cp_page_title'] = lang('new_custom_datasets');
		$vars['base_url'] = ee('CP/URL', 'addons/settings/'.HOP_NEW_RELIC_NAME.'/save_custom_dataset')->compile();
		$vars['save_btn_text'] = lang('save');
		$vars['save_btn_text_working'] = lang('saving');

		$selected_app = Hop_new_relic_settings_helper::get_selected_app();
		$selected_server = Hop_new_relic_settings_helper::get_selected_server();
		$nr_api_key =  Hop_new_relic_settings_helper::get_setting('nr_api_key');
		
		// Get the metric names available for the selected app
		if ($selected_app != null
			&& $nr_api_key != null && $nr_api_key != '')
		{
			$choices = array();
			$choices_app = array();
			$choices_app_details = array();
			$choices_server = array();
			$choices_server_details = array();
			$nr_api = new New_Relic_Api($nr_api_key);
			$metric_names = $nr_api->get_app_metric_names($selected_app->id);

			if ($metric_names != NULL)
			{
				$vars['metric_names_app'] = $metric_names;

				// Populate choices
				foreach ($metric_names as $metric_name)
				{
					$name = $metric_name->name;
					$choices_app[$name] = $name;
					$choices_app_details[$name] = array();
					foreach ($metric_name->values as $value)
					{
						// $choices['app::'.$name.'::'.$value] = $name . ' ' . $value;
						$choices_app_details[$name][] = $value;
					}
				}

				ksort($choices_app);
				$vars['metric_values_app'] = $choices_app_details;
			}
			else
			{
				$vars['metric_names_app_error'] = lang('get_metric_names_app_error');
			}

			if ($selected_server != NULL)
			{
				// Get the metric names available for the selected server
				$metric_names_serv = $nr_api->get_server_metric_names($selected_server->id);

				if ($metric_names_serv != NULL)
				{
					$vars['metric_names_server'] = $metric_names_serv;

					foreach ($metric_names_serv as $metric_name_serv)
					{
						$name_s = $metric_name_serv->name;
						$choices_server[$name_s] = $name_s;
						$choices_server_details[$name_s] = array();
						foreach ($metric_name_serv->values as $value)
						{
							$choices_server_details[$name_s][] = $value;
						}
					}

					ksort($choices_server);
					$vars['metric_values_server'] = $choices_server_details;
				}
				else
				{
					$vars['metric_names_server_error'] = lang('get_metric_names_server_error');
				}
			}

			// If we get at least one list of metrics
			if (array_key_exists('metric_names_app', $vars) || array_key_exists())
			{
				// EE Settings form
				// The form to select metric data (for app or server) has 2 dropdowns, one to select the dataset metric name, 
				//   the other to select the dataset metric value (which depends on the metric name selected)
				//   so we're adding some js behind the scene to populate the 2nd dropdown field

				// Load JS for field toggling
				ee()->cp->add_js_script(array(
					'file' => array('cp/form_group'),
				));

				$vars['sections'] = array(
					array(
						array(
							'title' => 'custom_dataset_name',
							'desc' => 'custom_dataset_name_desc',
							'fields' => array(
								'custom_dataset_name' => array('type' => 'text', 'value' => '')
							)
						),
						array(
							'title' => 'custom_dataset_type',
							'desc' => 'custom_dataset_type_desc',
							'fields' => array(
								'metric_type' => array(
									'type' => 'select',
									'choices' => array(
										'app' => lang('app'),
										'server' => lang('server')
									),
									'group_toggle' => array(
										'app' => 'app_options',
										'server' => 'server_options'
									),
								)
							)
						),
						array(
							'title' => 'app_metric_data',
							'desc' => 'app_metric_data_desc',
							'group' => 'app_options',
							'wide' => TRUE,
							'fields' => array(
								'custom_dataset_app_metric_name' => array('type' => 'select', 'choices' => $choices_app),
								'custom_dataset_app_metric_value' => array('type' => 'select', 'choices' => array())
							)
						),
						array(
							'title' => 'server_metric_data',
							'desc' => 'server_metric_data_desc',
							'group' => 'server_options',
							'wide' => TRUE,
							'fields' => array(
								'custom_dataset_server_metric_name' => array('type' => 'select', 'choices' => $choices_server),
								'custom_dataset_server_metric_value' => array('type' => 'select', 'choices' => array())
							)
						),
						// array(
						// 	'title' => 'custom_dataset_unit',
						// 	'desc' => 'twilio_from_number_desc',
						// 	'fields' => array(
						// 		'custom_dataset_unit' => array('type' => 'text', 'value' => '')
						// 	)
						// ),
					),
				);
			}
			
		}

		ee()->javascript->compile();

		return array(
			'heading'			=> lang('new_custom_datasets'),
			'body'				=> ee('View')->make(HOP_NEW_RELIC_NAME.':new_custom_dataset')->render($vars),
			'breadcrumb'		=> array(
				ee('CP/URL', 'addons/settings/'.HOP_NEW_RELIC_NAME)->compile() => lang('hop_new_relic_module_name')
			),
		);
	}

	function save_custom_dataset()
	{
		$metric_type = ee()->input->post('metric_type');
		$custom_name = ee()->input->post('custom_dataset_name');
		if ($metric_type == 'app')
		{
			$metric_name = ee()->input->post('custom_dataset_app_metric_name');
			$metric_value = ee()->input->post('custom_dataset_app_metric_value');
		}
		else if ($metric_type == 'server')
		{
			$metric_name = ee()->input->post('custom_dataset_server_metric_name');
			$metric_value = ee()->input->post('custom_dataset_server_metric_value');
		}
		else
		{

		}

		$custom_set = array(
			'names'			=> array($metric_name),
			'values'		=> array($metric_value),
			'unit'			=> '',
			'convert'		=> false,
			'title'			=> $custom_name,
			'source'		=> $metric_type
		);

		Hop_new_relic_settings_helper::add_user_dataset($custom_set);

		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets'));
	}

	function custom_datasets_remove()
	{
		$datasets_to_remove = ee()->input->post('datasets');
		if (is_array($datasets_to_remove))
		{
			foreach ($datasets_to_remove as $dataset_id)
			{
				Hop_new_relic_settings_helper::remove_user_dataset($dataset_id);
			}
		}

		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/'.HOP_NEW_RELIC_NAME.'/custom_datasets'));
	}
}
