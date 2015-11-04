<?php

class WikiEIArticle extends WikiEIFileAbstract 
{
	public $type = 'article';
	
	public $fields = array(
		'id','id_contents','title','encoded_title','hits','id_cat','is_cat',
		'defined_status','undefined_status','redirect','auth',
		'con_id_contents','con_id_article','menu','activ','user_id',
		'user_ip','timestamp','content'
	);
	
	protected function before_to_export()
	{
		;
	}
}