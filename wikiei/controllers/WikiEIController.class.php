<?php

class WikiEIController  extends ModuleController
{
	
	private static $articles_table;
	private static $cats_table;
	private static $contents_table;
	
	/**
	 * @var \WikiEIConfig
	 */
	private static $config;
	
	/** @var	string	Dossier d'export */
	private static $export_folder;
	
	private static $redirects_export_folder;
	
	public function execute(\HTTPRequestCustom $request)
	{
		$this->init_config();
		$this->init_directories();
		$this->init_tables();
		
		$this->recursive_search(0, self::$export_folder);
		
		$this->create_redirects();
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
	}
	
	/**
	 * Test de l'existence du dossier d'export cible, si non on le cr��
	 */
	private function init_directories()
	{
		self::$export_folder = self::$config->get_export_path();
		self::$redirects_export_folder = self::$export_folder . DIRECTORY_SEPARATOR . 'wiki_redirects';
		
		if (!file_exists(self::$export_folder))
		{
			mkdir(self::$export_folder);
		}
		
		if (!file_exists(self::$redirects_export_folder))
		{
			mkdir(self::$redirects_export_folder);
		}
	}
	
	/**
	 * Recherche recursive des articles et cat�gories pour l'export
	 * 
	 * @param type $id_cat
	 * @param type $curr_dir
	 */
	private function recursive_search($id_cat, $curr_dir)
	{
		// R�cup�ration des articles actifs de la cat�gorie en cours
		$files_result = PersistenceContext::get_querier()->select($this->get_articles_query(), array(
			'id' => $id_cat
		), SelectQueryResult::FETCH_ASSOC);
			
		while($rowfiles = $files_result->fetch())
		{
			$file = new WikiEIArticle($curr_dir);
			$file->hydrate_by_sql($rowfiles);
			$file->export();
		}
		
		// R�cup�ration des cat�gories contenues dans la cat�gorie en cours
		$result = PersistenceContext::get_querier()->select($this->get_cats_query(), array(
			'id' => $id_cat
		), SelectQueryResult::FETCH_ASSOC);
		
		while($row = $result->fetch())
		{
			$cat = new WikiEICategorie($curr_dir);
			$cat->hydrate_by_sql($row);
			$cat->export();
			
			// Pour chaque cat�gorie, on relance la recherche
			$this->recursive_search($cat->id_cat, $cat->real_path);
		}
		$result->dispose();
	}
	
	private function create_redirects()
	{
		// R�cup�ration des redirections de la cat�gorie en cours
		$redirects_result = PersistenceContext::get_querier()->select($this->get_redirects_query(), array(
		), SelectQueryResult::FETCH_ASSOC);
			
		while($rowredirects = $redirects_result->fetch())
		{
			$redirect = new WikiEIRedirect(self::$redirects_export_folder);
			$redirect->hydrate_by_sql($rowredirects);
			$redirect->export();
		}
	}
	
	private function get_cats_query()
	{
		return $this->get_general_query() . ' WHERE cat.id_parent = :id'
			. ' AND con.activ = 1 AND art.is_cat = 1'
			. ' ORDER BY cat.id_parent ASC , art.id_cat ASC';
	}
	
	private function get_articles_query()
	{
		return $this->get_general_query() . ' WHERE art.id_cat = :id AND con.activ = 1 AND art.is_cat = 0 AND art.redirect = 0';
	}
	
	private function get_redirects_query()
	{
		return 'SELECT * FROM ' . self::$articles_table . ' WHERE redirect != 0';
	}

	private function get_general_query()
	{
		return 'SELECT art.id, art.id_contents, art.title, art.encoded_title,'
			. ' art.hits, art.id_cat, art.is_cat, art.defined_status, art.undefined_status,'
			. ' art.redirect, art.auth, cat.id cat_id, cat.id_parent cat_id_parent,'
			. ' cat.article_id cat_article_id, con.id_contents con_id_contents,'
			. ' con.id_article con_id_article, con.menu, con.content, con.activ,'
			. ' con.user_id, con.user_ip, con.timestamp'
			. ' FROM ' . self::$articles_table . ' art'
			. ' LEFT OUTER JOIN ' . self::$cats_table . ' cat ON art.id_cat = cat.id'
			. ' RIGHT OUTER JOIN ' . self::$contents_table . ' con ON art.id = con.id_article';
	}
	
	
}