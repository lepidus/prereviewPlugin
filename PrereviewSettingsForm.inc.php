<?php

/**
 * @file PrereviewSettingsForm.inc.php
 *
 * @class PrereviewSettingsForm
 * @ingroup plugins.generic.prereview
 *
 * @brief Form for adding/editing the settings for the PREreview plugin
 */

import('lib.pkp.classes.form.Form');

class PrereviewSettingsForm extends Form
{
    /** @var PrereviewSettingsForm  */
    public $plugin;

    public function __construct($plugin)
    {

        // Define the settings template and store a copy of the plugin object
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->plugin = $plugin;

        // Always add POST and CSRF validation to secure your form.
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Load the settings already saved in the database
     *
     * The settings are stored together with the general settings of the plugin.
     * can have different settings.
     */
    public function initData()
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $this->setData('prereviewApp', $this->plugin->getSetting($contextId, 'prereviewApp'));
        $this->setData('prereviewkey', $this->plugin->getSetting($contextId, 'prereviewkey'));
        $this->setData('showRevisions', $this->plugin->getSetting($contextId, 'showRevisions'));
        parent::initData();
    }

    /**
     * Load data that was submitted with the form
     */
    public function readInputData()
    {
        $this->readUserVars(['prereviewApp']);
        $this->readUserVars(['prereviewkey']);
        $this->readUserVars(['showRevisions']);
        parent::readInputData();
    }

    /**
     * Fetch any additional data needed for your form.
     *
     * Data assigned to the form using $this->setData() during the
     * initData() or readInputData() methods will be passed to the
     * template.
     */
    public function fetch($request, $template = null, $display = false)
    {

        // Pass the plugin name to the template so that it can be
        // used in the URL that the form is submitted to
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());

        return parent::fetch($request, $template, $display);
    }
    /**
     * Save the settings
     */
    public function execute(...$functionArgs)
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $this->plugin->updateSetting($contextId, 'prereviewApp', $this->getData('prereviewApp'));
        $this->plugin->updateSetting($contextId, 'prereviewkey', $this->getData('prereviewkey'));
        $this->plugin->updateSetting($contextId, 'showRevisions', $this->getData('showRevisions'));
        // Tell the user that the save was successful.
        import('classes.notification.NotificationManager');
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );
        return parent::execute(...$functionArgs);
    }

}
