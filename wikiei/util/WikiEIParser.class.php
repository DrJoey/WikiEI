<?php

class WikiEIParser
{
	protected $file;
	
	protected $parser;
	
	public function __construct(\WikiEIFileAbstract $file)
	{
		$this->file = $file;
		$content_manager = AppContext::get_content_formatting_service()->get_default_factory();
		$this->parser = $content_manager->get_parser();
	}
	
	public function parse($make_absolute_mages_url = false)
	{
		if (MAGIC_QUOTES)
		{
			$this->file->content = stripslashes($this->file->content);
		}
			
		$this->parser->set_content($this->file->title);
		$this->parser->parse();
		$this->file->title = $this->parser->get_content();
		
		$this->parser->set_content($this->file->content);
		$this->parser->parse();

		$trans = array("'" => "''");
		$this->file->title = strtr($this->file->title, $trans);
		$this->file->menu = strtr($this->file->menu, $trans);
		
		$this->file->content = preg_replace('`\[link=([a-z0-9+#-_]+)\](.+)\[/link\]`isU', '<a href="/wiki/$1">$2</a>', addslashes($this->parser->get_content()));

		if ($make_absolute_mages_url) $this->make_absolutes_images_url();
	}
	
	protected function make_absolutes_images_url()
	{
		$this->file->content = preg_replace('`\[img\]images/`isU', "[img]http://resources.phpboost.com/documentation/", $this->file->content);
	}
}
