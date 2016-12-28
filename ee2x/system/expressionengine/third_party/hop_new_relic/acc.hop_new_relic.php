<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'hop_new_relic/helper.php';

class Hop_new_relic_acc
{
    var $name       = 'New RelEEk Data';
    var $id         = __CLASS__;
    var $version    = '1.0';
    var $description= 'Accessory for Hop New Relic add-on';
    var $sections   = array();

    /**
     * Set Sections
     *
     * Set content for the accessory
     *
     * @access  public
     * @return  void
     */
    function set_sections()
    {
        // Get general data about selected app and its server

        //Load our magnificent CSS
        ee()->cp->add_to_head('<link rel="stylesheet" href="'.URL_THIRD_THEMES.HOP_NEW_RELIC_NAME.'/css/style.css" type="text/css" media="screen">');

        $vars = array('app' => null, 'server' => null);
        $app_name = "Application Data";

        $serialized_selected_app = Hop_new_relic_helper::get_setting('nr_selected_app');
        $api_key =  Hop_new_relic_helper::get_setting('nr_api_key');
        if ($serialized_selected_app != null && $api_key != null)
        {
            $selected_app = unserialize($serialized_selected_app);
            $new_relic_api = new New_Relic_Api($api_key);

            $cache_key = md5('app_summary_'.$selected_app->id);
            if ($app_summary_cache = ee()->cache->get('/'.__CLASS__.'/'.$cache_key))
            {
                //Cache found
                $vars['app'] = $app_summary_cache;
                $app_name = $app_summary_cache->name;
            }
            else
            {
                $app_summary = $new_relic_api->get_app_summary($selected_app->id);
                if ($app_summary == null)
                {

                }
                else
                {
                    $vars['app'] = $app_summary;
                    // Save in cache for 2 min
                    ee()->cache->save('/'.__CLASS__.'/'.$cache_key, $app_summary, 2*60);
                    $app_name = $app_summary->name;
                }
            }


            if (isset($selected_app->links->servers) && count($selected_app->links->servers) > 0)
            {
                $cache_key = md5('server_summary_'.$selected_app->links->servers[0]);
                if ($server_summary = ee()->cache->get('/'.__CLASS__.'/'.$cache_key))
                {
                    //Cache found
                }
                else
                {
                    //Get data from NR
                    $server_summary = $new_relic_api->get_server_summary($selected_app->links->servers[0]);

                }

                if ($server_summary != null && !isset($server_summary->error))
                {
                    $vars['server'] = $server_summary;
                    if (isset($server_summary->summary->memory_used))
                    {
                        $vars['server_memory_used'] = Hop_new_relic_helper::format_bytes($server_summary->summary->memory_used, 0);
                    }
                    if (isset($server_summary->summary->memory_total))
                    {
                        $vars['server_memory_total'] = Hop_new_relic_helper::format_bytes($server_summary->summary->memory_total, 0);
                    }
                    // Save in cache for 2 min
                    ee()->cache->save('/'.__CLASS__.'/'.$cache_key, $server_summary, 2*60);
                }
                else
                {
                    if (isset($server_summary->error))
                    {
                        $vars['errors'][] = $server_summary->error->title;
                    }
                }
            }
            else
            {
                // We don't have a server attached this app
                $vars['server'] = -1;
            }
        }

        $this->sections[$app_name] = ee()->load->view('accessory_tab', $vars, TRUE);;
    }
}
