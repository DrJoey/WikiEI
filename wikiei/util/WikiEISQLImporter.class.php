<?php

class WikiEISQLImporter extends WikiEIImporterAbstract
{
	private $articles_content;
	
	private $cats_content;
	
	private $contents_content;
	
	private $filename;
	
	public function __construct($path)
	{
		$this->init_articles_content();
		$this->init_cats_content();
		$this->init_contents_content();

		$this->filename = $path . '/import-' . date("d-m-y") . '.sql';
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
		if (file_exists($this->filename)) unlink($this->filename);
		
		$file = fopen($this->filename ,'a');
		fputs($file, $this->articles_content);
		fputs($file, $this->cats_content);
		fputs($file, $this->contents_content);
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
	
	private function init_cats_content()
	{
		$this->cats_content = "DROP TABLE IF EXISTS `" . PREFIX . "wiki_cats`;
CREATE TABLE `" . PREFIX . "wiki_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL DEFAULT '0',
  `article_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;\n";
	}
	
	private function init_contents_content()
	{
		$this->contents_content = "DROP TABLE IF EXISTS `" . PREFIX . "wiki_contents`;
CREATE TABLE `" . PREFIX . "wiki_contents` (
  `id_contents` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL DEFAULT '0',
  `menu` text,
  `content` text,
  `activ` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `user_ip` varchar(50) DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contents`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=1442 DEFAULT CHARSET=latin1;\n";
	}
}
