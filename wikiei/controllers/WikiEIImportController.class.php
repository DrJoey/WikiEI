<?php

class WikiEIImportController  extends ModuleController
{
	
	private static $articles_table;
	private static $cats_table;
	private static $contents_table;
	
	/** @var	string	Dossier d'export */
	private static $export_folder = 'export';

	public function execute(\HTTPRequestCustom $request)
	{
		$this->explorer(self::$export_folder);
		
	}
	
	private function recursive_explorer($path)
	{
		if (is_dir($path))
		{
			$dir = opendir($path);
			while ($child = readdir($dir) )
			{
				if( $child != '.' && $child != '..' )
				{
					$this->explorer( $path . DIRECTORY_SEPARATOR . $child );
				}
			}
		}
	}
	
	/**
	 * Initialisation des tables de la DB
	 */
	private function init_tables()
	{
		self::$articles_table = PREFIX . 'wiki_articles';
		self::$cats_table = PREFIX . 'wiki_cats';
		self::$contents_table = PREFIX . 'wiki_contents';
	}
	
	
	
	
}