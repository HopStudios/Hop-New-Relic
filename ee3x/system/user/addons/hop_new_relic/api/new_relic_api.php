<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class New_Relic_Api
{
	private $api_key;

	const RPM_URL = 'https://rpm.newrelic.com';
	const API_URL = 'https://api.newrelic.com/v2';

	function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	/**
	 * Get all applications attached to the account
	 * @return [type] [description]
	 */
	public function get_applications()
	{
		$data = $this->call_api('applications.json');

		if ($data != null)
		{
			$data = json_decode($data);
			//print_r($data);

			return $data->applications;
		}
		return null;
	}

	/**
	 * Get summary data for an application
	 * @param  [type] $app_id [description]
	 * @return [type]		 [description]
	 */
	public function get_app_summary($app_id)
	{
		if ($app_id == null || $app_id == "")
		{
			return null;
		}

		$data = $this->call_api('applications/'.$app_id.'.json');

		if ($data != null)
		{
			$data = json_decode($data);
			//print_r($data);

			if (isset($data->error))
			{
				return $data;
			}

			return $data->application;
		}
		return null;
	}
	
	/**
	 * Get the list of available Metrics for an app
	 * @param string $app_id The app id from which to get the available metrics
	 */
	public function get_app_metric_names($app_id)
	{
		if ($app_id == null || $app_id == "")
		{
			return null;
		}
		
		$data = $this->call_api('applications/'.$app_id.'/metrics.json');

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);

			return $data->metrics;
		}
		return null;
	}
	
	/**
	 * Get app data for a single value of a metric, through time
	 * @param  [type] $app_id                 [description]
	 * @param  [type] $metric_name            [description]
	 * @param  [type] $metric_value           [description]
	 * @param  array  $additionnal_parameters Additional parameters you want to add to the request (see New Relic doc)
	 * @return [type]                         [description]
	 */
	public function get_app_metric_value($app_id, $metric_name, $metric_value, $additionnal_parameters = array())
	{
		if ($app_id == null || $app_id == "" || $metric_name == null || $metric_name == "")
		{
			return null;
		}

		$parameters = array('names' => array($metric_name), 'values' => array($metric_value)) + $additionnal_parameters;

		$data = $this->call_api('applications/'.$app_id.'/metrics/data.json', $parameters);

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);
			if (isset($data->error))
			{
				return $data;
			}
			if (isset($data->metrics))
			{
				return $data->metrics;
			}
			if (isset($data->metric_data))
			{
				return $data->metric_data;
			}
		}
		return null;
	}

	/**
	 * Get a list of servers
	 */
	public function get_servers($server_ids = array())
	{
		$server_ids_list = "";
		if (count($server_ids) > 0)
		{
			foreach ($server_ids as $server_id)
			{
				$server_ids_list .= $server_id.',';
			}
		}

		if ($server_ids_list != "")
		{
			// Retrieve data only for specific servers
			$data = $this->call_api('servers.json', array('filter[ids]' => $server_ids_list));
		}
		else
		{
			// Retrieve data for all servers
			$data = $this->call_api('servers.json');
		}

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);

			if (isset($data->error))
			{
				return $data;
			}

			return $data->servers;
		}
		return null;
	}

	/**
	 * Get summary data of a server
	 * @param  [type] $server_id [description]
	 * @return [type]			[description]
	 */
	public function get_server_summary($server_id)
	{
		if ($server_id == null || $server_id == "")
		{
			return null;
		}

		$data = $this->call_api('servers/'.$server_id.'.json');

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);

			if (isset($data->error))
			{
				return $data;
			}

			return $data->server;
		}
		return null;
	}

	/**
	 * Retrieve a list of available server metrics and values
	 * @param  [type] $server_id [description]
	 * @return [type]			[description]
	 */
	public function get_server_metric_names($server_id)
	{
		if ($server_id == null || $server_id == "")
		{
			return null;
		}

		$data = $this->call_api('servers/'.$server_id.'/metrics.json');

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);

			return $data->metrics;
		}
		return null;
	}

	/**
	 * Get values of a specific server metric
	 * @param  string $server_id   The unique NR server id
	 * @param  string $metric_name The metric name you want to retrieve the values of
	 * @return [type]			  [description]
	 */
	public function get_server_metric_values($server_id, $metric_name)
	{
		if ($server_id == null || $server_id == "" || $metric_name == null || $metric_name == "")
		{
			return null;
		}

		$parameters = array('name' => $metric_name);

		$data = $this->call_api('servers/'.$server_id.'/metrics.json', $parameters);

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);

			return $data->metrics;
		}
		return null;
	}

	/**
	 * Retrieve specific value of a server metric
	 * @param  [type] $server_id	[description]
	 * @param  [type] $metric_name  [description]
	 * @param  [type] $metric_value [description]
	 * @param  [type] $additionnal_parameters [description]
	 * @return [type]			   [description]
	 */
	public function get_server_metric_value($server_id, $metric_name, $metric_value, $additionnal_parameters = array())
	{
		if ($server_id == null || $server_id == "" || $metric_name == null || $metric_name == "")
		{
			return null;
		}

		$parameters = array('names' => array($metric_name), 'values' => array($metric_value)) + $additionnal_parameters;

		$data = $this->call_api('servers/'.$server_id.'/metrics/data.json', $parameters);

		if ($data != null)
		{
			$data = json_decode($data);
			// print_r($data);
			if (isset($data->error))
			{
				return $data;
			}
			if (isset($data->metrics))
			{
				return $data->metrics;
			}
			if (isset($data->metric_data))
			{
				return $data->metric_data;
			}
		}
		return null;
	}

	private function call_api($end_point, $parameters = array())
	{
		$call_url = self::API_URL.'/'.$end_point;

		if (!empty($parameters))
		{
			$query = http_build_query($parameters);
			//This generates wrong URL parameters -> param[0]=1&param[1]=2 should be param[]=1&param[]=2 instead.
			$query = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);
			$call_url .= '?' . $query;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Api-Key: ' . $this->api_key));
		curl_setopt($ch, CURLOPT_URL, $call_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		/* Execute cURL, Return Data */
		$data = curl_exec($ch);

		/* Check HTTP Code */
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		if ($status == 200)
		{
			//We got our data, YAY !
			return $data;
		}
		else
		{
			//Error
			// echo '<h2>Error API</h2>';
			// print_r($data);
			return $data;
		}
	}
}
