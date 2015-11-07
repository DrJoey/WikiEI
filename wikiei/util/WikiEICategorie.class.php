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
	 * Pr�paration du chemin � cr�er
	 */
	protected function before_to_export()
	{
		$this->real_path .= '/' . $this->title;
		
		mkdir($this->real_path);
	}
	
	/**
	 * Le nom du fichier regroupant les infos de la cat�gorie est ind�pendant de son nom
	 */
	protected function prepare_title_for_filename()
	{
		$this->filename = '_cat_infos';
	}
	
	
}