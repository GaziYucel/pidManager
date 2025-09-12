<?php

/**
 * @file PidManagerPlugin.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PidManagerPlugin
 * @brief Plugin for managing Persistent Identifiers (PIDs) and depositing to external services.
 */

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

require_once(__DIR__ . '/vendor/autoload.php');

import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.site.VersionCheck');
import('lib.pkp.classes.handler.APIHandler');
import('lib.pkp.classes.linkAction.request.AjaxAction');

use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\classes\Settings\Actions;
use APP\plugins\generic\pidManager\classes\Settings\Manage;
use APP\plugins\generic\pidManager\classes\Igsn\ArticleDetails as IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\Schema as IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\SubmissionWizard as IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\Workflow as IgsnWorkflow;
use APP\plugins\generic\pidManager\classes\Pidinst\ArticleDetails as PidinstArticleDetails;
use APP\plugins\generic\pidManager\classes\Pidinst\Schema as PidinstSchema;
use APP\plugins\generic\pidManager\classes\Pidinst\SubmissionWizard as PidinstSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Pidinst\Workflow as PidinstWorkflow;

class PidManagerPlugin extends GenericPlugin
{
    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {
            if ($this->getEnabled()) {
                $contextId = ($mainContextId === null) ? $this->getCurrentContextId() : $mainContextId;
                $request = Application::get()->getRequest();
                $templateMgr = TemplateManager::getManager($request);

                /** IGSN */
                if ($this->getSetting($contextId, Constants::settingEnableIgsn)) {
                    $igsnSchema = new IgsnSchema();
                    $igsnWorkflow = new IgsnWorkflow($this);
                    $igsnArticleDetails = new IgsnArticleDetails($this);
                    $igsnSubmissionWizard = new IgsnSubmissionWizard($this);

                    HookRegistry::register('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
                    HookRegistry::register('Template::Workflow::Publication', [$igsnWorkflow, 'execute']);
                    HookRegistry::register('Templates::Article::Main', [$igsnArticleDetails, 'execute']);
                    HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', [$igsnSubmissionWizard, 'execute']);
                }

                /** PIDINST */
                if ($this->getSetting($contextId, Constants::settingEnablePidinst)) {
                    $pidinstSchema = new PidinstSchema();
                    $pidinstWorkflow = new PidinstWorkflow($this);
                    $pidinstArticleDetails = new PidinstArticleDetails($this);
                    $pidinstSubmissionWizard = new PidinstSubmissionWizard($this);

                    HookRegistry::register('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);
                    HookRegistry::register('Template::Workflow::Publication', [$pidinstWorkflow, 'execute']);
                    HookRegistry::register('Templates::Article::Main', [$pidinstArticleDetails, 'execute']);
                    HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', [$pidinstSubmissionWizard, 'execute']);
                }

                if ($this->getSetting($contextId, Constants::settingEnableIgsn) ||
                    $this->getSetting($contextId, Constants::settingEnablePidinst)
                ) {
                    $this->addStyleSheet($request, $templateMgr);
                }
            }
            return true;
        }
        return false;
    }

    /** @copydoc PKPPlugin::getDescription */
    public function getDescription(): string
    {
        return __('plugins.generic.pidManager.description');
    }

    /** @copydoc PKPPlugin::getDisplayName */
    public function getDisplayName(): string
    {
        return __('plugins.generic.pidManager.displayName');
    }

    /** @copydoc Plugin::getActions() */
    public function getActions($request, $actionArgs): array
    {
        $actions = new Actions($this);
        return $actions->execute($request, $actionArgs, parent::getActions($request, $actionArgs));
    }

    /** @copydoc Plugin::manage() */
    public function manage($args, $request): JSONMessage
    {
        $manage = new Manage($this);
        return $manage->execute($args, $request);
    }

    protected function addStyleSheet(Request $request, TemplateManager $templateMgr): void
    {
        $templateMgr->addStyleSheet("pidManagerStylesBackend",
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/assets/css/backend.css", [
                'inline' => false,
                'contexts' => ['backend']
            ]);

        $templateMgr->addStyleSheet("pidManagerStylesFrontend",
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/assets/css/frontend.css", [
                'inline' => false,
                'contexts' => ['frontend']
            ]);
    }
}

class_alias('\PidManagerPlugin', '\APP\plugins\generic\pidManager\PidManagerPlugin');
