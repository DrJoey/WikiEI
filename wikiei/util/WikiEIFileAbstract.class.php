<?php

abstract class WikiEIFileAbstract
{	
	// **** Hydratation ****
	
	/** @var array		Champs pour l'hydratation */
	public $fields = array();
	
	/** Champs du noeud articles_fields */
	public $id;
	public $id_contents;
	public $title;
	public $encoded_title;
	public $hits;
	public $id_cat;
	public $is_cat;
	public $defined_status;
	public $undefined_status;
	public $redirect;
	public $auth;
	public $articles_fields = array(
		'id','id_contents','title','encoded_title','hits','id_cat','is_cat',
		'defined_status','undefined_status','redirect','auth'
	);
	
	/** Champs du noeud cats_fields */
	public $cat_id;
	public $cat_id_parent;
	public $cat_article_id;
	public $cats_fields = array(
		'cat_id','cat_id_parent','cat_article_id'
	);
	
	/** Champs du noeud contents_infos */
	public $con_id_contents;
	public $con_id_article;
	public $menu;
	public $content;
	public $activ;
	public $user_id;
	public $user_ip;
	public $timestamp;
	public $contents_fields = array(
		'con_id_contents','con_id_article','menu','content','activ','user_id',
		'user_ip','timestamp'
	);
	
	
	// **** Sauvegarde en DB ****
	
	/** @var array		Tables à sauvegarder en DB */
	public $tables_to_save = array();
	
	
	// **** Système de fichiers ****
	
	/** @var string		Chemin du fichier */
	public $real_path;
	
	/** @var string		Nom du fichier à exporter sans extension */
	public $filename;
	
	/** @var string		Type de fichier (cat, article, redirect) */
	public $type;

	public function __construct($dir)
	{
		$this->real_path = $dir;
	}
	
	public function hydrate_by_sql($row)
	{
		foreach ($this->fields as $cat_field)
		{
			foreach ($this->{$cat_field} as $field)
			{
				$this->{$field} = $row[$field];
			}
		}
	}
	
	public function hydrate_by_xml(\WikiEIXMLReader $xml_importer)
	{
		$xml_importer->set_file($this);
		$xml_importer->fill_fields_content();
	}
	
	final public function export()
	{
		$this->prepare_title_for_filename();
		$this->unparse_content();
		
		if (method_exists($this, 'before_to_export'))
		{
			$this->before_to_export();
		}
		
		$writer = new WikiEIXMLWriter($this);
		
		foreach ($this->fields as $cat_field)
		{
			$writer->open_fields_cat($cat_field);
			foreach ($this->{$cat_field} as $field)
			{
				$writer->add_field($field, $this->{$field});
			}
			$writer->close_fields_cat($cat_field);
		}
		$writer->save();
	}
	
	public function add_rows_to_importer(\WikiEIImporterAbstract $importer)
	{
		$parse_img = (get_class($importer) === 'WikiEISQLImporter') ? true : false;
		$this->parse_content($parse_img);
		foreach ($this->tables_to_save as $key => $value)
		{
			$array_fields = array();
			foreach ($this->{$value} as $field)
			{
				if (!is_numeric($this->{$field}))
				{
					$this->{$field} = "'" . $this->{$field} . "'";
				}
				$array_fields[] = $this->{$field};
			}
			$importer->add_row($key, $array_fields);
		}
	}

	protected function parse_content($parse_img = false)
	{
		
			$parser = new WikiEIParser($this);
		
			$parser->parse($parse_img);
	}
	
	/**
	 * Preparation du titre du fichier
	 */
	protected function prepare_title_for_filename()
	{
		// Suppression des caractères non autorisés
		$forbid = array(">", "<",  ":", "*", "/", "|", "?", '"', '<', '>', "'");
		$this->filename = str_replace($forbid, " ", $this->title);
		// Suppression des espaces en trop
		$this->filename = preg_replace("# +#", " ", $this->filename);
		$this->filename = trim($this->filename);
	}
	
	/**
	 * Unparsage du contenu du fichier
	 */
	protected function unparse_content()
	{
		$unparser = new WikiEIUnparser($this);
		$unparser->unparse();
	}
}