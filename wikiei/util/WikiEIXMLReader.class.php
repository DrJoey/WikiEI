<?php

class WikiEIXMLReader
{

	private $file;
	
	private $content;

	const PREFIX = 'wiki_';
	
	public function set_file(\WikiEIFileAbstract $file)
	{
		$this->file = $file;
		
		$this->read_file();
	}
	
	private function read_file()
	{
		$handle = fopen($this->file->real_path, 'r');

		$this->content = "";
		$line = fgets($handle);
		while ($line)
		{
			// On remplace les '<' et '>' sinon le navigateur ne nous donne pas acc�s aux balises XML
			$line = str_replace("<", "&lt;", $line);
			$line = str_replace(">", "&gt;", $line);
			$this->content .= $line;
			$line = fgets($handle);
		}
	}

	public function fill_fields_content()
	{
		foreach ($this->file->fields as $cat_field)
		{
			foreach ($this->file->{$cat_field} as $field)
			{
				preg_match('#&lt;' . self::PREFIX . $field . '&gt;(.*)&lt;/' . self::PREFIX . $field . '&gt;#isU', $this->content, $matches);
				$this->file->{$field} = $matches[1];
				$this->file->{$field} = str_replace("&lt;", "<", $this->file->{$field});
				$this->file->{$field} = str_replace("&gt;", ">", $this->file->{$field});
			}
		}
	}

}
