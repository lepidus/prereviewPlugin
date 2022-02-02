<?php

/**
 * @file plugins/generic/prereviewPlugin/controllers/grid/PrereviewGridHandler.inc.php
 */

import('lib.pkp.classes.controllers.grid.GridHandler');

class PrereviewGridHandler extends GridHandler {
	static $plugin;

	/** @var boolean */
	var $_readOnly;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR),
			array('updateRequest')
		);
	}


	/**
	 * Set the plugin.
	 * @param $plugin 
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Get the submission associated with this grid.
	 * @return Submission
	 */
	function getSubmission() {
		return $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
	}

	/**
	 * Get whether or not this grid should be 'read only'
	 * @return boolean
	 */
	function getReadOnly() {
		return $this->_readOnly;
	}

	/**
	 * Set the boolean for 'read only' status
	 * @param boolean
	 */
	function setReadOnly($readOnly) {
		$this->_readOnly = $readOnly;
	}

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.SubmissionAccessPolicy');
		$this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}




	/**
	 * @copydoc GridHandler::getJSHandler()
	 */
	public function getJSHandler() {
		return '$.pkp.plugins.generic.prereviewPlugin.PrereviewGridHandler';
	}


	/**
	 * Update a request
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateRequest($args, $request) {
		$result = $request->getUserVar('prereview:authorization');
		$context = $request->getContext();
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();
		$latestRequest=$this->getPrereviewSetting($submission->getId())->setting_value;
		$this->setupTemplate($request);
		// Create and populate the form
		import('plugins.generic.prereviewPlugin.controllers.grid.form.RequestForm');
		$prereviewForm = new RequestForm(self::$plugin, $context->getId(), $submissionId, $result, $latestRequest);
		$prereviewForm->readInputData();
		$save = $prereviewForm->execute();
		import('classes.notification.NotificationManager');
		$notificationMgr = new NotificationManager();
		
		if($save==true)
		{			
			$notificationMgr->createTrivialNotification(
			Application::get()->getRequest()->getUser()->getId(),
			NOTIFICATION_TYPE_SUCCESS,
			['contents' => __('common.changesSaved')]
			);			

		}else{
			$notificationMgr->createTrivialNotification(
				Application::get()->getRequest()->getUser()->getId(),
				NOTIFICATION_TYPE_ERROR,
				['contents' => __('common.error')]);
		}
		$path = getenv('HTTP_REFERER').'#publication/prereviewTab';
		header('Location: '.$path);
		
	}


	function getPrereviewSetting($id) {
		import('plugins.generic.prereviewPlugin.PrereviewPluginDAO');
		$prereview = new PrereviewPluginDAO();
		DAORegistry::registerDAO('PrereviewPluginDAO', $prereview);
		$preDao = DAORegistry::getDAO('PrereviewPluginDAO'); 	
			$result= $preDao->getDataPrereview($id);
		return $result;
	}
}
