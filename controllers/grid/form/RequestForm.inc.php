<?php

import('lib.pkp.classes.form.Form');

class RequestForm extends Form {
	/** @var int Context ID */
	var $contextId;

	/** @var int Submission ID */
	var $submissionId;

	/** @var PrereviewPlugin */
	var $plugin;

	/**
	 * Constructor
	 */
	function __construct($prereviewPlugin, $contextId, $submissionId, $result, $latestRequest) {
		parent::__construct($prereviewPlugin->getTemplateResource('workflowPrereview.tpl'));
		$this->contextId = $contextId;
		$this->submissionId = $submissionId;
		$this->result = $result;
		$this->plugin = $prereviewPlugin;
		$this->latestRequest = $latestRequest;

		// Add form checks
		$this->addCheck(new FormValidator($this, 'prereview:authorization', 'required', 'plugins.generic.prereview.publication.success'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

	}

	/**
	 * @copydoc Form::initData()
	 */
	function initData() {
		$this->setData('publication_id', $this->submissionId);
		$this->setData('setting_name', 'prereview:authorization');
		$this->setData('setting_value', $this->result);
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(['prereview:authorization']);
	}

	/**
	 * @copydoc Form::fetch
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		
		$templateMgr->assign('publication_id', $this->submissionId);
		$templateMgr->assign('setting_value', $this->result);
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save form values into the database
	 */
	function execute(...$functionArgs) {
		import('plugins.generic.prereviewPlugin.PrereviewPluginDAO');
		$prereview = new PrereviewPluginDAO();
		DAORegistry::registerDAO('PrereviewPluginDAO', $prereview);
		$preDao = DAORegistry::getDAO('PrereviewPluginDAO');
		$latestRequest = $this->latestRequest;
		$id=$this->submissionId;
		$requestResult=$this->result;

		if(empty($latestRequest))
		{
			$data = $preDao->insert($id, 'prereview:authorization', $requestResult);
		}else{
			$data = $preDao->updateObject($id, 'prereview:authorization', $requestResult);
		}
		return $data;
		
	}
}
