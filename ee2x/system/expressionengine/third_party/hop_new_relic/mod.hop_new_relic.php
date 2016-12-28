<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/helper.php';

class Hop_new_relic {

	public $return_data	= '';
	
	private $cache_ttl = 300; //cache time to live in seconds, 5 minutes
	
	public function __construct()
	{
		$this->cache_ttl = intval(ee()->TMPL->fetch_param('ttl'));
		// For speed and API purposes, limit the cache to 30sec minimum
		if ($this->cache_ttl < 30)
		{
			$this->cache_ttl = 30;
		}
	}
	
	/**
	 * Output app data
	 * @return string What's displayed in the template
	 */
	public function app_data()
	{
		$serialized_selected_app = Hop_new_relic_helper::get_setting('nr_selected_app');
		$api_key =  Hop_new_relic_helper::get_setting('nr_api_key');
		if ($serialized_selected_app != null && $api_key != null)
		{
			$selected_app = unserialize($serialized_selected_app);
			
			//Is it cached ?
			$app_summary = ee()->cache->get('/'.HOP_NEW_RELIC_NAME.'/app_sum_'.$selected_app->id.'_'.$this->cache_ttl);
			if (!$app_summary)
			{
				$new_relic_api = new New_Relic_Api($api_key);
				//Get NC data from selected app
				
				$app_summary = $new_relic_api->get_app_summary($selected_app->id);
				
				if ($app_summary != null && isset($app_summary->application_summary))
				{
					ee()->cache->save('/'.HOP_NEW_RELIC_NAME.'/app_sum_'.$selected_app->id.'_'.$this->cache_ttl, $app_summary, $this->cache_ttl);
				}
			}
			
			//Process data to display
			if ($app_summary == null || !isset($app_summary->application_summary))
			{
				return "";
			}
			else
			{
				
				$text = '<span>'.$app_summary->application_summary->response_time.' ms</span> <span>'.$app_summary->application_summary->throughput.' rpm</span> <span>'.$app_summary->application_summary->error_rate.' err%</span> <span>apdex '.$app_summary->application_summary->apdex_score.'</span>';
				return $text;
			}
			
		}
		
		return "";
	}
	
	/**
	 * Retrieve and display server data from New Relic API
	 * @return string What's displayed in the template
	 */
	public function server_data()
	{
		$serialized_selected_app = Hop_new_relic_helper::get_setting('nr_selected_app');
		$api_key =  Hop_new_relic_helper::get_setting('nr_api_key');
		if ($serialized_selected_app != null && $api_key != null)
		{
			$selected_app = unserialize($serialized_selected_app);
			
			$server_summary = ee()->cache->get('/'.HOP_NEW_RELIC_NAME.'/server_sum_'.$selected_app->id.'_'.$this->cache_ttl);
			if(!$server_summary)
			{
				$new_relic_api = new New_Relic_Api($api_key);
				$server_summary = $new_relic_api->get_server_summary($selected_app->links->servers[0]);
				
				ee()->cache->save('/'.HOP_NEW_RELIC_NAME.'/server_sum_'.$selected_app->id.'_'.$this->cache_ttl, $server_summary, $this->cache_ttl);
			}
			
			if ($server_summary != null && !isset($server_summary->error))
			{
				
				$text = '<span>'.$server_summary->summary->cpu.' cpu%';
				if($server_summary->summary->cpu_stolen > 0)
				{
					$text .= ' ('.$server->summary->cpu_stolen.' cpu stolen%)';
				}
				$text .= '</span> <span>'.$server_summary->summary->memory.' mem%';
				
				if (isset($server_summary->summary->memory_used) && isset($server_summary->summary->memory_total))
				{
					$text .= ' ('.Hop_new_relic_helper::format_bytes($server_summary->summary->memory_used, 0).'/'.Hop_new_relic_helper::format_bytes($server_summary->summary->memory_total, 0).')';
				}
				
				$text .= '</span> <span>'.$server_summary->summary->fullest_disk.' disk%</span>';
				return $text;
			}
			else
			{
				return "";
			}
		}
		else
		{
			return "";
		}
	}
	
	/**
	 * Retrieve and display end-user data from New Relic API
	 * @return string What's displayed in the template
	 */
	public function enduser_data()
	{
		$serialized_selected_app = Hop_new_relic_helper::get_setting('nr_selected_app');
		$api_key =  Hop_new_relic_helper::get_setting('nr_api_key');
		if ($serialized_selected_app != null && $api_key != null)
		{
			$selected_app = unserialize($serialized_selected_app);
			
			//Is it cached ?
			$app_summary = ee()->cache->get('/'.HOP_NEW_RELIC_NAME.'/enduser_'.$selected_app->id.'_'.$this->cache_ttl);
			if (!$app_summary)
			{
				$new_relic_api = new New_Relic_Api($api_key);
				//Get NC data from selected app
				
				$app_summary = $new_relic_api->get_app_summary($selected_app->id);
				
				if ($app_summary != null && isset($app_summary->end_user_summary))
				{
					ee()->cache->save('/'.HOP_NEW_RELIC_NAME.'/enduser_'.$selected_app->id.'_'.$this->cache_ttl, $app_summary, $this->cache_ttl);
				}
			}
			
			//Process data to display
			if ($app_summary == null || !isset($app_summary->end_user_summary))
			{
				return "";
			}
			else
			{
				$text = '<span>'.$app_summary->end_user_summary->response_time.' s</span> <span>'.$app_summary->end_user_summary->throughput.' rpm</span> <span>apdex '.$app_summary->end_user_summary->apdex_score.'</span>';
				return $text;
			}
			
		}
		
		return "";
	}
}
