<?php

class WikiEIRedirect extends WikiEIFileAbstract
{
	public $type = 'redirect';
	
	public $fields = array(
		'articles_fields'
	);
	
	public $tables_to_save = array(
		'wiki_articles' => 'articles_fields'
	);
	
	protected function before_to_export()
	{
		
	}
}

