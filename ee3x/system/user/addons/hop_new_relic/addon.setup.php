<?php

require_once PATH_THIRD."hop_new_relic/config.php";

return array(
	'author'		=> 'Hop Studios',
	'author_url'	=> 'http://www.hopstudios.com',
	'name'			=> HOP_NEW_RELIC_FULL_NAME,
	'description'	=> 'Hop New Relic displays useful and important information about your server given by New Relic',
	'docs_url'		=> 'http://www.hopstudios.com/software/hop_404_reporter/docs',
	'version'		=> HOP_NEW_RELIC_VERSION,
	'namespace'		=> 'HopStudios\HopNewRelic',
	'settings_exist'=> TRUE,
	'models'		=> array('Hnp_settings' => 'Model\Hnp_settings')
);
