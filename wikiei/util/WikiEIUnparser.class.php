<?php

class WikiEIUnparser
{
	protected $file;
	
	protected $content;
	
	protected $unparser;
	
	public function __construct(\WikiEIFileAbstract $file)
	{
		$this->file = $file;
		$content_manager = AppContext::get_content_formatting_service()->get_default_factory();
		$this->unparser = $content_manager->get_unparser();
	}

	public function unparse()
	{
		$this->build_original_paragraph();
		
		//Unparse de la balise link
		$this->file->content = preg_replace('`<a href="/wiki/([a-z0-9+#-_]+)">(.*)</a>`sU', "[link=$1]$2[/link]", $this->file->content);

		//On force le langage de formatage à BBCode
		
		$this->unparser->set_content($this->file->content);
		$this->unparser->parse();
		$this->file->content = $this->unparser->get_content();
		
		$this->make_relatives_images_url();
	}

	protected function build_original_paragraph()
	{
		$string_regex = '-';
		for ($i = 1; $i <= 5; $i++)
		{
			$string_regex .= '-';
			$this->file->content = preg_replace('`[\r\n]+<(?:div|h[1-5]) class="wiki_paragraph' .  $i . '" id=".+">(.+)</(?:div|h[1-5])><br />[\r\n]+`sU', "\n" . $string_regex . ' $1 '. $string_regex, "\n" . $this->file->content . "\n");
		}
		$this->file->content = trim($this->file->content);
	}
	
	protected function make_relatives_images_url()
	{
		$this->file->content = preg_replace('`\[img\]http://resources\.phpboost\.com/documentation/`isU', "[img]images/", $this->file->content);
	}
	
	
}

