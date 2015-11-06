<?php

abstract class WikiEIExporterAbstract
{
	// Tables
	public $fields = array(
		'wiki_articles', 'wiki_cats', 'wiki_contents'
	);
	
	// Champs des articles
	public $wiki_articles = array(
		'id','id_contents','title','encoded_title','hits','id_cat','is_cat',
		'defined_status','undefined_status','redirect','auth'
	);
	
	// Champs des catégories
	public $wiki_cats = array(
		'id','id_parent','article_id'
	);
	
	// Champs des contenus
	public $wiki_contents = array(
		'con_id_contents','con_id_article','menu','content','activ','user_id',
		'user_ip','timestamp'
	);
}

