<?php

abstract class WikiEIImporterAbstract
{
	
	// Champs des articles
	public $wiki_articles = array(
		'`id`','`id_contents`','`title`','`encoded_title`','`hits`','`id_cat`',
		'`is_cat`','`defined_status`','`undefined_status`','`redirect`','`auth`'
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
	
	public function add_row($table, $values = array())
	{
		$line = 'INSERT INTO `' . PREFIX . $table .'` ( ';
		$line .= implode(',', $this->{$table});
		$line .= ' ) VALUES ( ';
		$line .= implode(',', $values);
		$line .= ' );';
		
		$this->treatment_row($line, $table);
	}
	
	abstract protected function treatment_row($line, $table);
}

