<?php

class AdminWikiEIConfigController extends AdminModuleController
{

	private $view;
	
	private $form;

	private $submit_button;
	
	private $lang;

	/**
	 * @var \WikiEIConfig
	 */
	private $config;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->build_form();
		
		$this->view = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		$this->view->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('message.success.config', 'status-messages-common'), E_USER_SUCCESS, 5));
		}
		
		$this->view->put('FORM', $this->form->display());
		
		$this->view = new AdminDisplayResponse($this->view);
		$this->view->get_graphical_environment()->set_page_title($this->lang['module_title']);	
		
		return $this->view;
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'wikiei');
		$this->config = WikiEIConfig::load();
	}
	
	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('config', LangLoader::get_message('configuration', 'admin'));
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('export_path', $this->lang['path_to_folder_export'], $this->config->get_export_path()));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function save()
	{
		$this->config->set_export_path($this->form->get_value('export_path'));
		WikiEIConfig::save();
	}
}
?>