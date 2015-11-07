<?php

class WikiEIImportController  extends ModuleController
{
	
	private static $articles_table;
	private static $cats_table;
	private static $contents_table;
	
	/**
	 * @var \WikiEIConfig
	 */
	private static $config;
	
	private $lang;
	
	/** @var	string	Dossier d'export */
	private static $export_folder;
	
	private static $redirects_export_folder;
	
	private $importer;

	public function execute(\HTTPRequestCustom $request)
	{
		$this->init_config();
		$this->init_tables();
		if (!$this->init_directories())
		{
			$tpl = new StringTemplate(sprintf($this->lang['export_path_not_exist'], self::$export_folder));
			return $this->build_response($tpl);
		}
		
		$this->importer = new WikiEISQLImporter();
		$this->recursive_explorer(self::$export_folder);
		
		$this->importer->save();
		
	}
	
	private function recursive_explorer($path)
	{
		if (is_dir($path))
		{
			// dossier
			$dir = opendir($path);
			while ($child = readdir($dir) )
			{
				if( ($child != '.' && $child != '..' && $child != 'images') || ($child == 'images' && $path != self::$export_folder))
				{
					$this->recursive_explorer( $path . DIRECTORY_SEPARATOR . $child );
				}
			}
		}
		else
		{
			// fichier
			$folder_parent = dirname($path);
			if ($folder_parent === self::$redirects_export_folder)
			{
				// redirection
				$redirect = new WikiEIRedirect($path);
				$redirect->hydrate_by_xml(new WikiEIXMLReader());
				
				$redirect->add_rows_to_importer($this->importer);

			}
			elseif (basename($path, '.xml') === '_cat_infos')
			{
				// catégorie
				$cat = new WikiEICategorie($path);
				$cat->hydrate_by_xml(new WikiEIXMLReader());
				
				$cat->add_rows_to_importer($this->importer);
			}
			else
			{
				// article
				$article = new WikiEIArticle($path);
				$article->hydrate_by_xml(new WikiEIXMLReader());
				
				$article->add_rows_to_importer($this->importer);
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
	
	/**
	 * Initialisation de la configuration
	 */
	private function init_config()
	{
		self::$config = WikiEIConfig::load();
		$this->lang = LangLoader::get('common', 'wikiei');
	}
	
	private function init_directories()
	{
		self::$export_folder = self::$config->get_export_path();
		self::$redirects_export_folder = self::$export_folder . DIRECTORY_SEPARATOR . 'wiki_redirects';
		
		if (!file_exists(self::$export_folder))
		{
			return false;
		}
		return true;
	}
	
	private function build_response(View $view)
	{
		$response = new SiteDisplayResponse($view);
		$response->get_graphical_environment()->set_page_title($this->lang['import']);
		return $response;
	}
	
	
	
	
}