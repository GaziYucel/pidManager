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

namespace APP\plugins\generic\pidManager;

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

use APP\core\Application;
use APP\core\Request;
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
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

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

                    Hook::add('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
                    Hook::add('Template::Workflow::Publication', [$igsnWorkflow, 'execute']);
                    Hook::add('Templates::Article::Main', [$igsnArticleDetails, 'execute']);
                    Hook::add('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
                    Hook::add('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
                    Hook::add('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);
                }

                /** PIDINST */
                if ($this->getSetting($contextId, Constants::settingEnablePidinst)) {
                    $pidinstSchema = new PidinstSchema();
                    $pidinstWorkflow = new PidinstWorkflow($this,);
                    $pidinstArticleDetails = new PidinstArticleDetails($this);
                    $pidinstSubmissionWizard = new PidinstSubmissionWizard($this);

                    Hook::add('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);
                    Hook::add('Template::Workflow::Publication', [$pidinstWorkflow, 'execute']);
                    Hook::add('Templates::Article::Main', [$pidinstArticleDetails, 'execute']);
                    Hook::add('TemplateManager::display', [$pidinstSubmissionWizard, 'addToSubmissionWizardSteps']);
                    Hook::add('Template::SubmissionWizard::Section', [$pidinstSubmissionWizard, 'addToSubmissionWizardTemplate']);
                    Hook::add('Template::SubmissionWizard::Section::Review', [$pidinstSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);
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

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
