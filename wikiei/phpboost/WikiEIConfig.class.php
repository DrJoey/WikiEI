<?php

class WikiEIConfig extends AbstractConfigData
{
	
	const EXPORT_PATH = 'export_path';
	
	public function get_default_values()
	{
		return array(
			self::EXPORT_PATH => 'export'
		);
	}
	
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'wikiei', 'config');
	}
	
	public static function save()
	{
		ConfigManager::save('wikiei', self::load(), 'config');
	}
	
	public function get_export_path()
	{
		return $this->get_property(self::EXPORT_PATH);
	}
 
	public function set_export_path($path)
	{
		$this->set_property(self::EXPORT_PATH, $path);
	}
}
