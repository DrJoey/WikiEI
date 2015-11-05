<?php

class WikiEIRedirect extends WikiEIFileAbstract
{
	public $type = 'redirect';
	
	public $fields = array(
		'articles_fields'
	);
	
	public $articles_fields = array(
		'id','id_contents','title','encoded_title','hits','id_cat','is_cat',
		'defined_status','undefined_status','redirect','auth'
	);
	
	protected function before_to_export()
	{
		
	}
}

