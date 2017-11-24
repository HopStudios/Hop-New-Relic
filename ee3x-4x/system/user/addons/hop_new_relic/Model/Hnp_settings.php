<?php

namespace HopStudios\HopNewRelic\Model;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Service\Model\Model;

class Hnp_settings extends Model {

	protected static $_primary_key = 'id';
	protected static $_table_name = 'hop_new_relic_settings';

	protected static $_validation_rules = array(
		'name'	=> 'required|unique',
	);

	protected static $_typed_columns = array(
		'id'	=> 'int',
		'name'	=> 'string',
		'value'	=> 'string',
	);

	protected static $_events = array(
		'beforeSave',
	);

	protected $id;
	protected $name;
	protected $value;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Hard check the setting name before saving it for the first time.
	 * We never want 2 settings with the same name
	 */
	public function onBeforeSave()
	{
		if ($this->id == NULL && ee('Model')->get('hop_new_relic:Hnp_settings')->filter('name', $this->name)->count() != 0)
		{
			throw new \Exception('Hnp_settings cannot be saved, a setting with that name already exists.');
		}
	}

	public function __toString()
	{
		return __CLASS__.': Name = '.$this->name."\n";
	}

}