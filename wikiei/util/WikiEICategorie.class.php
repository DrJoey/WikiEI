<?php

class WikiEICategorie extends WikiEIFileAbstract 
{
	public $type = 'cat';
	
	public $fields = array(
		'articles_fields', 'cats_fields', 'contents_fields'
	);
	
	public $articles_fields = array(
		'id','id_contents','title','encoded_title','hits','id_cat','is_cat',
		'defined_status','undefined_status','redirect','auth'
	);
	
	public $cats_fields = array(
		'cat_id','cat_id_parent','cat_article_id'
	);
	
	public $contents_fields = array(
		'con_id_contents','con_id_article','menu','content','activ','user_id',
		'user_ip','timestamp'
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