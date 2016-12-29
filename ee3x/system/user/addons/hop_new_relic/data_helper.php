<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Specific data helper for New Relic data/api
 */
class Hop_new_relic_data_helper
{
	
	var $selected_app = null;
	var $new_relic_api_key = null;
	var $new_relic_api = null;

	var $predefined_data_sets = null;
	var $predfined_time_ranges = null;
	var $user_defined_datasets = null;
	
	public function __construct($api_key = null, $selected_app = null, $selected_server = null)
	{
		$this->new_relic_api_key = $api_key;
		$this->selected_app = $selected_app;
		$this->selected_server = $selected_server;
		$this->new_relic_api = new New_Relic_Api($api_key);

		// Please order those by Source, app first and then server
		$this->predefined_data_sets = array(
			'app_apdex' => array(
				'names'			=> array('Apdex'),	// Query param
				'values'		=> array('score'),	// Query param
				'unit'			=> '',				// Unit name displayed in graph
				'convert'		=> false,			// Try to convert value in a more readable format
				'title'			=> 'App Apdex',		// Nice title to display on the front end
				'source'		=> 'app',			// server or app (endpoints are different in New Relic API)
			),
			'app_enduser_networktime' => array(
				'names'			=> array('EndUser'),
				'values'		=> array('average_network_time'),
				'unit'			=> '',
				'convert'		=> false,
				'title'			=> 'Enduser Network Time',
				'source'		=> 'app',
			),
			'app_average_throughput' => array(
				'names'			=> array('HttpDispatcher'),
				'values'		=> array('requests_per_minute'),
				'unit'			=> 'req/min',
				'convert'		=> false,
				'title'			=> 'App Throughput',
				'source'		=> 'app',
			),
			'server_system_cpu_usage' => array(
				'names'		=> array('System/CPU/System/percent'),
				'values'	=> array('average_value'),
				'unit'		=> '%',
				'convert'	=> false,
				'title'		=> 'Server System CPU Usage',
				'source'	=> 'server',
			),
			'server_user_cpu_usage' => array(
				'names'		=> array('System/CPU/User/percent'),
				'values'	=> array('average_value'),
				'unit'		=> '%',
				'convert'	=> false,
				'title'		=> 'Server User CPU Usage',
				'source'	=> 'server',
			),
			'server_physical_memory' => array(
				'names'		=> array('System/Memory/Used/bytes'),
				'values'	=> array('average_value'),
				'unit'		=> 'bytes',
				'convert'	=> true,
				'title'		=> 'Server Physical Memory Use',
				'source'	=> 'server',
			),
			'server_load_average' => array(
				'names'		=> array('System/Load'),
				'values'	=> array('average_value'),
				'unit'		=> '',
				'convert'	=> false,
				'title'		=> 'Average Server Load',
				'source'	=> 'server',
			),
			'server_disk_input' => array(
				'names'		=> array('System/Disk/All/Utilization/percent'),
				'values'	=> array('average_value'),
				'unit'		=> '%',
				'convert'	=> false,
				'title'		=> 'Server Disk I/O Utilization',
				'source'	=> 'server',
			),
			'server_network_input' => array(
				'names'		=> array('System/Network/All/Received/bytes/sec'),
				'values'	=> array('per_second'),
				'unit'		=> 'bytes/sec',
				'convert'	=> true,
				'title'		=> 'Server Network Input',
				'source'	=> 'server',
			),
			'server_network_output' => array(
				'names'		=> array('System/Network/All/Transmitted/bytes/sec'),
				'values'	=> array('per_second'),
				'unit'		=> 'bytes/sec',
				'convert'	=> true,
				'title'		=> 'Server Network Output',
				'source'	=> 'server',
			)
		);

		$this->predefined_time_ranges = array(
			'1800' => array(
				'title'		=> 'Last 30 minutes',	// Nice name for the user
				'period'	=> 60, 					// the number of seconds you want each time period to report (= granularity) 
			),
			'3600' => array(
				'title'		=> 'Last hour',
				'period'	=> 60
			),
			'10800' => array(
				'title'		=> 'Last 3 hours',
				'period'	=> 60
			),
			'21600' => array(
				'title'		=> 'Last 6 hours',
				'period'	=> 120 // 2 min
			),
			'43200' => array(
				'title'		=> 'Last 12 hours',
				'period'	=> 300 // 5 min
			),
			'86400' => array(
				'title'		=> 'Last 24 hours',
				'period'	=> 600 // 10 min
			)
		);

		$serialized_user_datasets = Hop_new_relic_settings_helper::get_setting('user_datasets');
		if ($serialized_user_datasets != NULL && $serialized_user_datasets != '')
		{
			$this->user_defined_datasets = unserialize($serialized_user_datasets);
		}
		else
		{
			$this->user_defined_datasets = array();
		}
	}
	
	/**
	 * Returns all predefined relevant data sets
	 */
	public function get_all_predefined_data_details()
	{
		return $this->predefined_data_sets;
	}

	/**
	 * We're defining relevant data sets for the user to choose on the control panel
	 * This methods gives the details of a predefined set
	 * @param string $predefined_data_name The unique name of a predefined data set
	 */
	public function get_predefined_data_details($predefined_data_name)
	{
		if (array_key_exists($predefined_data_name, $this->predefined_data_sets))
		{
			return $this->predefined_data_sets[$predefined_data_name];
		}
		return NULL;
	}

	/**
	 * Returns all predefined time ranges
	 */
	public function get_all_predefined_time_ranges()
	{
		return $this->predefined_time_ranges;
	}

	/**
	 * We're defining relevant time ranges for the user to choose on the control panel
	 * This methods gives details of a predefined time range
	 */
	public function get_predefined_time_range_details($time_range)
	{
		if (array_key_exists($time_range, $this->predefined_time_ranges))
		{
			return $this->predefined_time_ranges[$time_range];
		}
		return NULL;
	}

	/**
	 * Return all user saved custom datasets
	 */
	public function get_user_custom_datasets()
	{
		return $this->user_defined_datasets;
	}

	/**
	 * Get a single user defined dataset
	 */
	public function get_user_custom_dataset($id)
	{
		$sets = $this->get_user_custom_datasets();
		if (array_key_exists($id, $sets))
		{
			return $sets[$id];
		}
		return NULL;
	}

	/**
	 * Get data from New Relic
	 * @param  [type] $nr_data_name       A specific keyword to determine what metric name/metric data to retrieve
	 * @param  [type] $nr_data_time_range A time range in seconds
	 * @return [type]                     [description]
	 */
	public function get_nr_data($nr_data_name, $nr_data_time_range)
	{
		// Time range and period
		// New Relic requires a sepcific format &from=2014-08-11T14:42:00-02:00&to=2014-08-11T15:12:00-02:00
		// What's cool is that it accepts timezones
		// What's less cool is that it sends back times in UTC, so we have to change that
		// 
		// Period :
		// "Sometimes the output data's granularity may be too fine, or the time period for the data returned may be too short. To control this, include the period= parameter in the query command as the number of seconds you want each time period to report."
		// We need to customize the period depending on the time range selected otherwise the graph will be unreadable

		$date_now = new \DateTime();
		$date_now->setTimestamp(ee()->localize->now);
		$date_sub = new \DateTime();
		$date_sub->setTimestamp(ee()->localize->now);
		// echo $date_now->format('c');

		$date_sub->sub(new \DateInterval('PT'.$nr_data_time_range.'S'));
		// echo $date_now->format('c');

		$predefined_data_details = $this->get_predefined_data_details($nr_data_name);

		if ($predefined_data_details == NULL)
		{
			// Might be a user custom dataset
			if (substr($nr_data_name, 0, 7) == 'custom_')
			{
				$id = intval(str_replace('custom_', '', $nr_data_name));
				$predefined_data_details = $this->get_user_custom_dataset($id);
			}
		}

		$predefined_time_range_details = $this->get_predefined_time_range_details($nr_data_time_range);

		if ($predefined_data_details == NULL)
		{
			return NULL;
		}

		if (count($predefined_data_details['names']) == 1)
		{
			$metric_details_names = $predefined_data_details['names'][0];
		}
		else
		{
			// TODO : use that properly, nothing is setup for it right now
			// $metric_details_names = $predefined_data_details['names'];
		}

		if (count($predefined_data_details['values']))
		{
			$metric_details_values = $predefined_data_details['values'][0];
		}
		else
		{
			// TODO : use that properly, nothing is setup for it right now
			// $metric_details_values = $predefined_data_details['values'];
		}

		$data_source = $predefined_data_details['source'];

		$period = 60;
		if ($predefined_time_range_details != NULL)
		{
			$period = $predefined_time_range_details['period'];
		}

		switch ($data_source)
		{
			case 'app':
				$metrics = $this->new_relic_api->get_app_metric_value(
					$this->selected_app->id,
					$metric_details_names,
					$metric_details_values,
					array('period' => $period, 'from' => $date_sub->format('c'), 'to' => $date_now->format('c'))
				);
				break;

			case 'server':
				$metrics = $this->new_relic_api->get_server_metric_value(
					$this->selected_server->id,
					$metric_details_names,
					$metric_details_values,
					array('period' => $period, 'from' => $date_sub->format('c'), 'to' => $date_now->format('c'))
				);
		}
		
		return $metrics;
	}

	/**
	 * Process metrics data received from New Relic and create a dataset that would work for our JS graph library
	 * @param string $predefined_data_name Name of the predefined setting used to retrieve the data
	 * @param [type] $data                 Data received from New Relic
	 */
	public function create_graph_dataset($predefined_data_name, $data)
	{
		// print_r($data);

		$data_set = new DataSet();

		$predefined_data_details = $this->get_predefined_data_details($predefined_data_name);
		if ($predefined_data_details == NULL)
		{
			// Maybe it's a user custom dataset
			if (substr($predefined_data_name, 0, 7) == 'custom_')
			{
				$id = intval(str_replace('custom_', '', $predefined_data_name));
				$predefined_data_details = $this->get_user_custom_dataset($id);
			}
		}

		// $data_set->label = $data->name;
		$data_set->label = $predefined_data_details['title'];
		$data_set->lineTension = 0;

		$data_value_name = $predefined_data_details['values'][0];

		$data_graph = new DataGraph();
		$data_graph->datasets = array();
		
		$labels = array();
		
		$data_set->data = array();

		foreach ($data->timeslices as $slice)
		{
			$val_label = $slice->from;
			// Calculate difference to know what granularity label to display
			// Convert time from UTC to local server timezone
			$dt_from = new DateTime($slice->from);
			$dt_to = new DateTime($slice->to);
			$interval = $dt_from->diff($dt_to);
			if ($interval->d != 0)
			{
				// Display date
				$val_label = ee()->localize->format_date('%d %M', $dt_from->getTimestamp());
			}
			else if ($interval->h != 0 || $interval->i != 0)
			{
				// Display time only
				$val_label = ee()->localize->format_date('%H:%i', $dt_from->getTimestamp());
			}

			$value = NULL;
			if (isset($slice->values->$data_value_name))
			{
				$value = $slice->values->$data_value_name;
			}
			else
			{
				$value = 0;
			}

			$data_set->data[] = $value;

			$labels[] = $val_label;
		}

		if ($predefined_data_details['convert'] == TRUE)
		{
			// Try to convert the value to a nice human-readable value
			if ($predefined_data_details['unit'] == 'bytes' || $predefined_data_details['unit'] == 'byte' || $predefined_data_details['unit'] == 'bytes/sec')
			{
				// Get the smallest value of all
				$smallest = PHP_INT_MAX;
				foreach ($data_set->data as $data_value)
				{
					if ($smallest > $data_value)
					{
						$smallest = $data_value;
					}
				}

				if ($smallest > 1073741824)
				{
					// Convert all in Gb
					$data_set->data = self::divider_conversion($data_set->data, 1073741824, 2);
					if ($predefined_data_details['unit'] == 'bytes' || $predefined_data_details['unit'] == 'byte')
					{
						$data_set->label .= ' (Gb)';
					}
					else if ($predefined_data_details['unit'] == 'bytes/sec')
					{
						$data_set->label .= ' (Gb/sec)';
					}
					
				}
				else if ($smallest > 1048576)
				{
					// Convert all in Mb
					$data_set->data = self::divider_conversion($data_set->data, 1048576, 2);
					if ($predefined_data_details['unit'] == 'bytes' || $predefined_data_details['unit'] == 'byte')
					{
						$data_set->label .= ' (Mb)';
					}
					else if ($predefined_data_details['unit'] == 'bytes/sec')
					{
						$data_set->label .= ' (Mb/sec)';
					}
				}
				else if ($smallest > 1024)
				{
					// Convert all in Kb
					$data_set->data = self::divider_conversion($data_set->data, 1024, 2);
					if ($predefined_data_details['unit'] == 'bytes' || $predefined_data_details['unit'] == 'byte')
					{
						$data_set->label .= ' (Kb)';
					}
					else if ($predefined_data_details['unit'] == 'bytes/sec')
					{
						$data_set->label .= ' (Kb/sec)';
					}
				}
			}
			
		}

		$data_graph->datasets[] = $data_set;
		$data_graph->labels = $labels;
		return $data_graph;
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

class DataGraph
{
	public $labels;
	public $datasets;
}

class DataSet
{
	public $label;
	public $data;
	public $lineTension;
	public $borderColor = "rgba(12, 135, 204, 0.6)";
	public $backgroundColor = "rgba(79, 111, 175, 0.6)";
	public $pointBorderColor = "rgba(12, 135, 204, 0.9)";
	public $pointBackgroundColor = "rgba(43, 155, 190, 0.7)";

}