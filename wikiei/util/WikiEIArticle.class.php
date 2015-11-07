<?php

class WikiEIArticle extends WikiEIFileAbstract 
{
	public $type = 'article';
	
	public $fields = array(
		'articles_fields', 'contents_fields'
	);
	
	public $tables_to_save = array(
		'wiki_articles' => 'articles_fields',
		'wiki_contents' => 'contents_fields'
	);
	
	protected function before_to_export()
	{
		
	}
}