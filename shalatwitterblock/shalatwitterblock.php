<?php

if(!defined('_PS_VERSION_'))
	exit;

class shalatwitterblock extends Module
{

	private $_html = '';

	public function __construct()
	{
		$this->name = 'shalatwitterblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Enis Shala';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

		$this->need_instance = 0;
		$this->bootstrap = true;

		$this->displayName = $this->l('Shala Twitter block');
		$this->description = $this->l('Display latest twitter posts in left column block.');
		parent::__construct();
	}	

	public function install()
	{
		if(
			!parent::install() OR
			!$this->registerHook('displayHeader') OR
			!$this->registerHook('displayLeftColumn')
			)
			return false;
		return true;
	}

	public function uninstall()
	{
		if(!parent::uninstall() 
			OR !Configuration::deleteByName('shalatwitterblock_twitter_username')
			OR !Configuration::deleteByName('shalatwitterblock_widget_height')
			OR !Configuration::deleteByName('shalatwitterblock_dark_theme')
			)
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html;
	}


	private function _postProcess()
	{
		if(Tools::isSubmit('submitUpdate'))
		{

			foreach ($this->context->controller->getLanguages() as $lang)
				$text[$lang['id_lang']] = Tools::getValue('twitter_username_'.$lang['id_lang']);
			Configuration::updateValue('shalatwitterblock_twitter_username', $text, true);

			foreach ($this->context->controller->getLanguages() as $lang)
				$text[$lang['id_lang']] = Tools::getValue('widget_height_'.$lang['id_lang']);
			Configuration::updateValue('shalatwitterblock_widget_height', $text, true);

			Configuration::updateValue('shalatwitterblock_dark_theme', Tools::getValue('dark_theme'));

			if($error)
				$this->_html .= $this->displayError($error);
			else
				$this->_html .= $this->displayConfirmation($this->l('Settings Updated'));

		} 
	}

	public function _displayForm()
	{

		$this->_html .= $this->_generateForm();

	}

	private function _generateForm()
	{
		$inputs = array();

		$inputs[] = array(
			'type' => 'text',
			'label' => $this->l('Twitter Username'),
			'name' => 'twitter_username',
			'desc' => 'Enter your twitter username without "@".',
			'prefix' => '@',
			'lang' => true
			);

		$inputs[] = array(
			'type' => 'text',
			'label' => $this->l('Twitter Widget Height'),
			'name' => 'widget_height',
			'desc' => 'Enter twitter widget height in (px)',
			'suffix' => 'pixels',
			'lang' => true
			);

		$inputs[] = array(
			'type' => 'switch',
			'label' => $this->l('Enable Dark Theme'),
			'name' => 'dark_theme',
			'desc' => 'Enable dark theme of twitter widget.',
			'values' => array(
				array(
					'id' => 'active_on',
					'value' => 1,
					'label' => $this->l('Enabled')
					),
				array(
					'id' => 'active_ff',
					'value' => 0,
					'label' => $this->l('Disabled')
					)
				)
			);
		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
					),
				'input' => $inputs,
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right',
					'name' => 'submitUpdate'
					)
				)
			);

		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper = new HelperForm();
		$helper->default_form_language = $lang->id;
		// $helper->submit_action = 'submitUpdate';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules',false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		// name => value
		return array(
			'twitter_username' => Configuration::getInt('shalatwitterblock_twitter_username'),
			'widget_height' => Configuration::getInt('shalatwitterblock_widget_height'),
			'dark_theme' => Configuration::get('shalatwitterblock_dark_theme')
			);
	}

	public function hookDisplayHeader($params)
	{

		// $this->context->controller->addCSS($this->_path . 'views/css/shalatwitterblock.css');
		// $this->context->controller->addJS($this->_path . 'views/js/shalatwitterblock.js');
		
	}

	public function hookDisplayLeftColumn($params)
	{

			$this->context->smarty->assign(array(
				'twitter_username' => Configuration::get('shalatwitterblock_twitter_username', $this->context->language->id),
				'widget_height' => Configuration::get('shalatwitterblock_widget_height', $this->context->language->id),
				'dark_theme' => Configuration::get('shalatwitterblock_dark_theme')
				));
		
			return $this->display(__FILE__, 'leftColumn.tpl');	

	}

}