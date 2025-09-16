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

use APP\core\Application;
use APP\core\Request;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\classes\Igsn\ArticleDetails as IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\PluginApiHandler as IgsnPluginApiHandler;
use APP\plugins\generic\pidManager\classes\Igsn\Schema as IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\SubmissionWizard as IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Pidinst\ArticleDetails as PidinstArticleDetails;
use APP\plugins\generic\pidManager\classes\Pidinst\PluginApiHandler as PidinstPluginApiHandler;
use APP\plugins\generic\pidManager\classes\Pidinst\Schema as PidinstSchema;
use APP\plugins\generic\pidManager\classes\Pidinst\SubmissionWizard as PidinstSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Settings\Actions;
use APP\plugins\generic\pidManager\classes\Settings\Manage;
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
                    Hook::add('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);

                    $apiHandler = new IgsnPluginApiHandler($this);
                    Hook::add('APIHandler::endpoints::submissions', [$apiHandler, 'addRoute']);

                    $igsnArticleDetails = new IgsnArticleDetails($this);
                    Hook::add('Templates::Article::Main', [$igsnArticleDetails, 'execute']);

                    $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
                    Hook::add('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
                    Hook::add('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
                    Hook::add('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);

                    $this->addJavascript(Constants::igsn, $request, $templateMgr);
                    $this->addStyleSheet(Constants::igsn, $request, $templateMgr);
                }

                /** PIDINST */
                if ($this->getSetting($contextId, Constants::settingEnablePidinst)) {
                    $pidinstSchema = new PidinstSchema();
                    Hook::add('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);

                    $apiHandler = new PidinstPluginApiHandler($this);
                    Hook::add('APIHandler::endpoints::submissions', [$apiHandler, 'addRoute']);

                    $pidinstArticleDetails = new PidinstArticleDetails($this);
                    Hook::add('Templates::Article::Main', [$pidinstArticleDetails, 'execute']);

                    $pidinstSubmissionWizard = new PidinstSubmissionWizard($this);
                    Hook::add('TemplateManager::display', [$pidinstSubmissionWizard, 'addToSubmissionWizardSteps']);
                    Hook::add('Template::SubmissionWizard::Section', [$pidinstSubmissionWizard, 'addToSubmissionWizardTemplate']);
                    Hook::add('Template::SubmissionWizard::Section::Review', [$pidinstSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);

                    $this->addJavascript(Constants::pidinst, $request, $templateMgr);
                    $this->addStyleSheet(Constants::pidinst, $request, $templateMgr);
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

    protected function addJavascript(string $pidName, Request $request, TemplateManager $templateMgr): void
    {
        $templateMgr->addJavaScript(
            "pidManager$pidName",
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/build-$pidName.iife.js",
            [
                'inline' => false,
                'contexts' => ['backend'],
                'priority' => TemplateManager::STYLE_SEQUENCE_LAST
            ]
        );
    }

    protected function addStyleSheet(string $pidName, Request $request, TemplateManager $templateMgr): void
    {
        $templateMgr->addStyleSheet("pidManagerStyle$pidName",
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/assets/css/frontend.css",
            [
                'inline' => false,
                'contexts' => ['frontend']
            ]
        );

        $templateMgr->addStyleSheet("pidManagerStyle$pidName",
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/build-$pidName.css",
            [
                'inline' => false,
                'contexts' => ['backend']
            ]
        );
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
