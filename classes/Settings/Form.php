<?php

/**
 * @file classes/Settings/Form.php
 *
 * Copyright (c) 2024+ TIB Hannover
 * Copyright (c) 2024+ Gazi Yücel
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Form
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Form for journal managers to configure the plugin
 */

namespace APP\plugins\generic\pidManager\classes\Settings;

use APP\core\Application;
use APP\notification\Notification;
use APP\notification\NotificationManager;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;
use PKP\form\Form as PKPForm;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;

class Form extends PKPForm
{
    public PidManagerPlugin $plugin;

    private array $settings = [
        Constants::settingEnableIgsn,
        Constants::settingEnablePidinst
    ];

    public function __construct(PidManagerPlugin &$plugin)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));

        $this->plugin = &$plugin;

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    public function initData(): void
    {
        $context = Application::get()->getRequest()->getContext();

        $contextId = $context ? $context->getId() : Application::CONTEXT_SITE;

        foreach ($this->settings as $key) {
            $this->setData($key, $this->plugin->getSetting($contextId, $key));
        }

        parent::initData();
    }

    public function readInputData(): void
    {
        foreach ($this->settings as $key) {
            $this->readUserVars([$key]);
        }

        parent::readInputData();
    }

    public function fetch($request, $template = null, $display = false): ?string
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());

        return parent::fetch($request, $template, $display);
    }

    public function execute(...$functionArgs): mixed
    {
        $context = Application::get()->getRequest()->getContext();

        $contextId = $context ? $context->getId() : Application::CONTEXT_SITE;

        foreach ($this->settings as $key) {
            $this->plugin->updateSetting($contextId, $key, $this->getData($key));
        }

        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            Notification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );

        return parent::execute();
    }
}
