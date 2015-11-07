<?php

class WikiEICategorie extends WikiEIFileAbstract 
{
	
	public $type = 'cat';
	
	public $fields = array(
		'articles_fields', 'cats_fields', 'contents_fields'
	);
	
	public $tables_to_save = array(
		'wiki_articles' => 'articles_fields',
		'wiki_cats' => 'cats_fields',
		'wiki_contents' => 'contents_fields'
	);
	
	/**
	 * Préparation du chemin à créer
	 */
	protected function before_to_export()
	{
		$this->real_path .= '/' . $this->title;
		
		mkdir($this->real_path);
	}
	
	/**
	 * Le nom du fichier regroupant les infos de la catégorie est indépendant de son nom
	 */
	protected function prepare_title_for_filename()
	{
		$this->filename = '_cat_infos';
	}
	
	
}