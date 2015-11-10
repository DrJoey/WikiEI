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
		if ($make_absolute_mages_url) $this->make_absolutes_images_url();
		
		if (MAGIC_QUOTES)
		{
			$this->file->content = stripslashes($this->file->content);
		}
			
		$this->parser->set_content($this->file->title);
		$this->parser->parse();
		$this->file->title = $this->parser->get_content();
		
		$this->parser->set_content($this->file->content);
		$this->parser->parse();
		$this->file->content =$this->parser->get_content();
		
		$this->file->menu = $this->display_menu($this->explode_menu($this->file->content));
		
		$trans = array("'" => "''");
		$this->file->title = strtr($this->file->title, $trans);
		$this->file->menu = addslashes($this->file->menu);
		
		$this->file->content = preg_replace('`\[link=([a-z0-9+#-_]+)\](.+)\[/link\]`isU', '<a href="/wiki/$1">$2</a>',  addslashes($this->file->content));

		
	}
	
	protected function make_absolutes_images_url()
	{
		$this->file->content = preg_replace('`\[img\]images/`isU', "[img]http://resources.phpboost.com/documentation/", $this->file->content);
	}
	
	protected function explode_menu(&$content)
	{
		$lines = explode("\n", $content);
		$num_lines = count($lines);
		$max_level_expected = 2;

		$list = array();

		//We read the text line by line
		$i = 0;
		while ($i < $num_lines)
		{
			for ($level = 2; $level <= $max_level_expected; $level++)
			{
				$matches = array();

				//If the line contains a title
				if (preg_match('`^\s*[\-]{' . $level . '}[\s]+(.+)[\s]+[\-]{' . $level . '}(?:<br />)?\s*$`', $lines[$i], $matches))
				{
					$title_name = strip_tags(TextHelper::html_entity_decode($matches[1]));

					//We add it to the list
					$list[] = array($level - 1, $title_name);
					//Now we wait one of its children or its brother
					$max_level_expected = min($level + 1, 5 + 1);

					//Réinsertion
					$class_level = $level - 1;
					$lines[$i] = '<h' . $class_level . ' class="wiki_paragraph' .  $class_level . '" id="paragraph_' . Url::encode_rewrite($title_name) . '">' . TextHelper::htmlspecialchars($title_name) .'</h' . $class_level . '><br />' . "\n";
				}
			}
			$i++;
		}

		$content = implode("\n", $lines);

		return $list;
	}
	
	protected function display_menu($menu_list)
	{
		if (count($menu_list) == 0) //Aucun titre de paragraphe
		{
			return '';
		}

		$menu = '';
		$last_level = 0;

		foreach ($menu_list as $title)
		{
			$current_level = $title[0];

			$title_name = stripslashes($title[1]);		
			$title_link = '<a href="#paragraph_' . Url::encode_rewrite($title_name) . '">' . TextHelper::htmlspecialchars($title_name) . '</a>';

			if ($current_level > $last_level)
			{
				$menu .= '<ol class="wiki_list_' . $current_level . '"><li>' . $title_link;
			}
			elseif ($current_level == $last_level)
			{
				$menu .= '</li><li>' . $title_link;
			}
			else
			{
				if (substr($menu, strlen($menu) - 4, 4) == '<li>')
				{
					$menu = substr($menu, 0, strlen($menu) - 4);
				}
				$menu .= str_repeat('</li></ol>', $last_level - $current_level) . '</li><li>' . $title_link;
			}
			$last_level = $title[0];
		}

		//End
		if (substr($menu, strlen($menu) - 4, 4) == '<li>')
		{
			$menu = substr($menu, 0, strlen($menu) - 4);
		}
		$menu .= str_repeat('</li></ol>', $last_level);

		return $menu;
	}
}
