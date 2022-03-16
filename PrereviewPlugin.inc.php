<?php
import('lib.pkp.classes.plugins.GenericPlugin');
//PREREVIEW SITE
define('PREREVIEW_API_CHECK', 'https://prereview.org/api/v2/preprints/');
define('PREREVIEW_URL', 'https://prereview.org/preprints/');

class PrereviewPlugin extends GenericPlugin {
	public function register($category, $path, $mainContextId = NULL) {

		// Register the plugin even when it is not enabled
		$success = parent::register($category, $path);

		if ($success && $this->getEnabled()) {
			$request = Application::get()->getRequest();
			//Include javascript and css
			$url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/prereview.css';
			$urlj = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/prereview.js';
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->addStyleSheet('callbackSharingDisplay', $url);
			$templateMgr->addJavaScript('callbackSharingDisplay', $urlj);

			// Do something when the plugin is enabled
			HookRegistry::register('Schema::get::publication', array($this, 'addToSchema')); //Add schema for radiobuttons
			HookRegistry::register('Templates::Preprint::Details', array($this, 'callbackSharingDisplay')); // Include information in detail view of OPS
			HookRegistry::register('submissionsubmitstep4form::display', array($this, 'handleFormDisplaySubmission'));//Include form to submmisions
			HookRegistry::register('submissionsubmitstep4form::execute', array($this, 'handleFormExecute'));//Save form
			HookRegistry::register('Template::Workflow::Publication', array($this, 'publicationTemplateData'));//Include form to publications
			HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
		}

		return $success;

	}

		/**
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.prereviewPlugin.controllers.grid.PrereviewGridHandler') {
			import($component);
			PrereviewGridHandler::setPlugin($this);
			return true;
		}
		return false;
	}
	

	/**
	 * Provide a name for this plugin
	 *
	 * The name will appear in the Plugin Gallery where editors can
	 * install, enable and disable plugins.
	 */
	public function getDisplayName() {
		return 'PREreview';
	}

	/**
	 * Provide a description for this plugin
	 *
	 * The description will appear in the Plugin Gallery where editors can
	 * install, enable and disable plugins.
	 */
	public function getDescription() {
		return  __('plugins.generic.prereview.description');
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $actionArgs) {
		// Get the existing actions
		$actions = parent::getActions($request, $actionArgs);
		if (!$this->getEnabled()) {
			return $actions;
		}
    // Create a LinkAction that will call the plugin's
    // `manage` method with the `settings` verb.
	$router = $request->getRouter();
	import('lib.pkp.classes.linkAction.request.AjaxModal');
	$linkAction = new LinkAction(
		'settings',
		new AjaxModal(
			$router->url(
				$request,
				null,
				null,
				'manage',
				null,
				array(
					'verb' => 'settings',
					'plugin' => $this->getName(),
					'category' => 'generic'
				)
			),
			$this->getDisplayName()
		),
		__('manager.plugins.settings'),
		null
	);

	// Add the LinkAction to the existing actions.
	// Make it the first action to be consistent with
	// other plugins.
	array_unshift($actions, $linkAction);

	return $actions;
	}

	
	/**
	 * @see Plugin::manage()
	 */
	function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$context = $request->getContext();
				$this->import('PrereviewSettingsForm');
				$form = new PrereviewSettingsForm($this,  $context);
				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						return new JSONMessage(true);
					}
				} else {
					$form->initData();
				}
				return new JSONMessage(true, $form->fetch($request));
		}
		return parent::manage($args, $request);
	}

	function callbackSharingDisplay($hookName, $params) {
		$templateMgr = $params[1];
		$output =& $params[2];
		$request = $this->getRequest();
		$context = $request->getContext();
		$result=array();
		$showRevisions = $this->_getPluginSetting($context, 'showRevisions');
		$idPreprint=$request->getRouter()->getHandler()->preprint->_data['id'];
		$idPreprint=((int) $idPreprint);
		$doi = $this->getDoi($idPreprint);
		$doi_result = "doi-".str_replace("/", "-", strtolower($doi));
		$url= PREREVIEW_API_CHECK . $doi_result;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  

		$result = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);  
		$datos = json_decode($result);

			$fullrev=array();
			$i=0;
			$datos_fullreview=$datos->data[0]->fullReviews;
			$datos_rapidreview=$datos->data[0]->rapidReviews;
			
			foreach($datos_fullreview as $fr){
				$fullrev[$i]=array(
							       'id' =>$i,
								   'name'=> $fr->authors[0]->name,
								   'content' =>$fr->drafts[0]->contents,
							);

				$i++;
			}

		$templateMgr->assign(
			array(
				'status' => $datos->status,
				'url' => PREREVIEW_URL.$doi_result,
				'numFullReviews' => count($datos_fullreview),
				'numRapidReviews' => count($datos_rapidreview),
				'numRequests' => count($datos->data[0]->requests),
				'fullReviews' =>$fullrev,
				'showReviews' =>$showRevisions,
				'showrevisionsLong' =>$showrevisionsLong,
				'rapidReviews' =>$this->getRapidReviews($datos_rapidreview),
				'authorization'=>$this->getPrereviewSetting($idPreprint)->setting_value,
			)
		); 
		$output .= $templateMgr->fetch($this->getTemplateResource('prereview.tpl'));
		return false;
	}
	



	function handleFormDisplaySubmission($hookName, $args) {
		$request = PKPApplication::get()->getRequest();
		$context = $request->getContext();
		$templateMgr = TemplateManager::getManager($request);
		switch ($hookName) {
			case 'submissionsubmitstep4form::display':
				$authorForm =& $args[0];
				$supportedSubmissionLocales = $context->getSupportedSubmissionLocales();
				$localeNames = AppLocale::getAllLocales();
				$locales = array_map(function($localeKey) use ($localeNames) {
					return ['key' => $localeKey, 'label' => $localeNames[$localeKey]];
				}, $supportedSubmissionLocales);

				$templateMgr->registerFilter("output", array($this, 'metadataForm'));

				break;
		}
		return false;

	}

	
	function handleFormExecute($hookName, $params) {
		$props =& $params[0];
		$request = Application::get()->getRequest();
		$this->import('PrereviewPluginDAO');
		$prereview = new PrereviewPluginDAO();
		DAORegistry::registerDAO('PrereviewPluginDAO', $prereview);
		$id=$props->submission->_data['id'];
		$request=$_POST['prereview:authorization'];
		if(empty($request))
		$request='no';
		$preDao = DAORegistry::getDAO('PrereviewPluginDAO'); 	
		$preDao->insert($id, 'prereview:authorization', $request);
		
	}
	
	/**
	 * Output adds prereview authorization form.
	 *
	 * @param $output string
	 * @param $templateMgr TemplateManager
	 * @return string
	 */
	function metadataForm($output, $templateMgr) {
		if (preg_match('/<input[^>]+name="submissionId"[^>]*>/', $output, $matches, PREG_OFFSET_CAPTURE)) {
			$match = $matches[0][0];
			$offset = $matches[0][1];
			$templateMgr->assign('data', $output);
			$newOutput = substr($output, 0, $offset + strlen($match));
			$newOutput .= $templateMgr->fetch($this->getTemplateResource('request.tpl'));
			$newOutput .= substr($output, $offset + strlen($match));
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'metadataForm'));
		}
		return $output;
	}

	/**
	 * @param string $hookname
	 * @param array $args [string, TemplateManager]
	 */
	function publicationTemplateData(string $hookname, array $args): void {
		/**
		 * @var $templateMgr TemplateManager
		 * @var $submission Submission
		 * @var $submissionFileDao SubmissionFileDAO
		 * @var $submissionFile SubmissionFile
		 */

		$templateMgr = $args[1];
		$request = $this->getRequest();
		$context = $request->getContext();
		$submission = $templateMgr->getTemplateVars('submission');
		$latestPublication = $submission->getLatestPublication();
		$latestPublicationApiUrl = $request->getDispatcher()->url($request, ROUTE_API, $context->getData('urlPath'), 'submissions/' . $submission->getId() . '/publications/' . $latestPublication->getId());

		$supportedSubmissionLocales = $context->getSupportedSubmissionLocales();
		$localeNames = AppLocale::getAllLocales();
		$locales = array_map(function($localeKey) use ($localeNames) {
			return ['key' => $localeKey, 'label' => $localeNames[$localeKey]];
		}, $supportedSubmissionLocales);

		$smarty =& $args[1];
		$output =& $args[2];
		$submission = $smarty->get_template_vars('submission');

		$smarty->assign([
			'submissionId' => $submission->getId(),
			'publication' => $this->getPrereviewSetting($submission->getId())->setting_value,
		]);

		$output .= sprintf(
			$smarty->fetch($this->getTemplateResource('workflowPrereview.tpl'))
		);

	}


		function getRapidReviews($rapidReviews){
            for ($l = 0; $l < count($rapidReviews); $l++) {
                $ynNovel[$l] = $rapidReviews[$l]->ynNovel;
                $ynFuture[$l] = $rapidReviews[$l]->ynFuture;
                $ynReproducibility[$l] = $rapidReviews[$l]->ynReproducibility;
                $ynMethods[$l] = $rapidReviews[$l]->ynMethods;
                $ynCoherent[$l] = $rapidReviews[$l]->ynCoherent;
                $ynLimitations[$l] = $rapidReviews[$l]->ynLimitations;
                $ynEthics[$l] = $rapidReviews[$l]->ynEthics;
                $ynNewData[$l] = $rapidReviews[$l]->ynNewData;
                $ynAvailableData[$l] = $rapidReviews[$l]->ynAvailableData;
                $ynAvailableCode[$l] = $rapidReviews[$l]->ynAvailableCode;
                $ynRecommend[$l] = $rapidReviews[$l]->ynRecommend;
                $ynPeerReview[$l] = $rapidReviews[$l]->ynPeerReview;


            }
			$rapidrev=array(
				'ynNovel'=>$this->getValues($ynNovel),
				'ynFuture'=>$this->getValues($ynFuture),
				'ynReproducibility'=>$this->getValues($ynReproducibility),
				'ynMethods'=>$this->getValues($ynMethods),
				'ynCoherent'=>$this->getValues($ynCoherent),
				'ynLimitations'=>$this->getValues($ynLimitations),
				'ynEthics'=>$this->getValues($ynEthics),
				'ynNewData'=>$this->getValues($ynNewData),
				'ynAvailableData'=>$this->getValues($ynAvailableData),
				'ynAvailableCode'=>$this->getValues($ynAvailableCode),
				'ynRecommend'=>$this->getValues($ynRecommend),
				'ynPeerReview'=>$this->getValues($ynPeerReview),

			);

			return $rapidrev;
		}

		function getValues($value) {
			$yes = 0; 
			$no = 0; 
			$unsure = 0;
			$na = 0;
			$result = "";
			$total = (100 / count($value));
	
			for ($v = 0; $v < count($value); $v++) {
				switch ($value[$v]) {
					case "yes":
						$yes = $yes + $total;
						break;
					case "no":
						$no = $no + $total;
						break;
					case "unsure":
						$unsure = $unsure + $total;
						break;
					case "N/A":
						$na = $na + $total;
						break;
				}
			}
			if ($yes != 0) {
				$result = $result . '<div class="yes" style="width:' . $yes . '%;"><p>' . $yes . '%</p></div>';
			}
			if ($unsure != 0) {
				$result = $result . '<div class="unsure" style="width:' . $unsure . '%;"><p>' . $unsure . '%</p></div>';
			}
			if ($na != 0) {
				$result = $result . '<div class="na" style="width:' . $na . '%;"><p>' . $na . '%</p></div>';
			}
			if ($no != 0) {
				$result = $result . '<div class="no" style="width:' . $no . '%;"><p>' . $no . '%</p></div>';
			}
	
	
			return $result;
		}

		public function addToSchema($hookName, $args) {
			$schema = $args[0];
			$propId = '{
				"type": "string",
				"apiSummary": true,
				"validation": [
					"nullable"
				]
			}';
			
			$schema->properties->{'prereview:authorization'} = json_decode($propId);
	  }


	/**
	 * Get context wide setting. If the context or the setting does not exist,
	 * get the site wide setting.
	 * @param $context Context
	 * @param $name Setting name
	 * @return mixed
	 */
	function _getPluginSetting($context, $name) {
		$pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
		if ($context && $pluginSettingsDao->settingExists($context->getId(), $this->getName(), $name)) {
			return $this->getSetting($context->getId(), $name);
		} else {
			return $this->getSetting(CONTEXT_ID_NONE, $name);
		}
	}

	function _getSubmissionSetting($id, $name) {
		$this->import('PrereviewPluginDAO');
		$prereview = new PrereviewPluginDAO();
		DAORegistry::registerDAO('PrereviewPluginDAO', $prereview);
		$preDao = DAORegistry::getDAO('PrereviewPluginDAO'); 	
			$result= $preDao->_getPrereviewData($id, $name);
		return $result;
	}
	function getPrereviewSetting($id) {
		$this->import('PrereviewPluginDAO');
		$prereview = new PrereviewPluginDAO();
		DAORegistry::registerDAO('PrereviewPluginDAO', $prereview);
		$preDao = DAORegistry::getDAO('PrereviewPluginDAO'); 	
			$result= $preDao->getDataPrereview($id);
		return $result;
	}

	function getInstallMigration() {
		$this->import('PrereviewSchemaMigration');
		return new PrereviewSchemaMigration();
	}

	public function getDoi($id) {
		import('classes.submission.Submission');
		$submission = Services::get('submission')->get($id);
		$submission = $submission->getData('publications')[0]->getData('pub-id::doi');
		return $submission;
	} 

	

}
