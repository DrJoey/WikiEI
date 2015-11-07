<?php

class WikiEISQLImporter extends WikiEIImporterAbstract
{
	private $articles_content;
	
	private $cats_content;
	
	private $contents_content;
	
	private $content;
	
	private $filename;
	
	private $file;
	
	public function __construct()
	{
		$this->init_articles_content();
		mkdir('export');
		$this->filename = 'export/p.sql';
	}
	
	protected function treatment_row($line, $table)
	{
		$line .= "\n";
		if ($table === 'wiki_articles')
		{
			$this->articles_content .= $line;
		}
		elseif ($table === 'wiki_cats')
		{
			$this->cats_content .= $line;
		}
		elseif ($table === 'wiki_contents')
		{
			$this->contents_content .= $line;
		}
	}

	
	public function save()
	{
		$file = fopen($this->filename ,'a');
		fputs($file, $this->articles_content);
		fclose($file);
	}
	
	private function init_articles_content()
	{
		$this->articles_content = "DROP TABLE IF EXISTS `" . PREFIX . "wiki_articles`;
CREATE TABLE `" . PREFIX . "wiki_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contents` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT '',
  `encoded_title` varchar(250) DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT '0',
  `id_cat` int(11) NOT NULL DEFAULT '0',
  `is_cat` tinyint(1) NOT NULL DEFAULT '0',
  `defined_status` varchar(6) NOT NULL DEFAULT '0',
  `undefined_status` text,
  `redirect` int(11) NOT NULL DEFAULT '0',
  `auth` text,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=315 DEFAULT CHARSET=latin1;\n";
	}
}
