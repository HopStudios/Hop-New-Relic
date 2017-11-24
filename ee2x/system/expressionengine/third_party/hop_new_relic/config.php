<?php if ( ! defined('EXT')) exit('No direct script access allowed');
/**
 * Hop New Relic - Config
 *
 * NSM Addon Updater config file.
 *
 * @package		Hop Studios:New Relic
 * @author		Louis Dekeister (Hop Studios)
 * @copyright	Copyright (c) 2015, Hop Studios, Inc.
 * @link		http://www.hopstudios.com/software
 * @version		1.0.1
 * @filesource	hop_new_relic/config.php
 */
$config['name']='Hop New Relic';
$config['version']='1.0.1';
$config['nsm_addon_updater']['versions_xml']='http://www.hopstudios.com/software/versions/hop_404_reporter';

// Version constant
if (!defined("HOP_NEW_RELIC_VERSION")) {
	define('HOP_NEW_RELIC_VERSION', $config['version']);
}

//Clean name constant
if (!defined("HOP_NEW_RELIC_NAME")) {
	define('HOP_NEW_RELIC_NAME', 'hop_new_relic');
}

//Clean name constant
if (!defined("HOP_NEW_RELIC_FULL_NAME")) {
	define('HOP_NEW_RELIC_FULL_NAME', $config['name']);
}