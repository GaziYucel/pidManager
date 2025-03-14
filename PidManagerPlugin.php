<?php
/**
 * @file PidManagerPlugin.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PidManagerPlugin
 * @brief Plugin for managing Persistent Identifiers (PIDs) and depositing to external services.
 */

namespace APP\plugins\generic\pidManager;

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

use APP\core\Application;
use APP\plugins\generic\pidManager\classes\Settings\Actions;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchemaMigration;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnPublicationTab;
use APP\plugins\generic\pidManager\classes\Settings\Manage;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\core\Registry;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class PidManagerPlugin extends GenericPlugin
{
    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {
            if (Application::isUnderMaintenance()) return true;

            if ($this->getEnabled()) {
                // IGSN
                $igsnSchema = new IgsnSchema();
                $igsnWorkflowTab = new IgsnPublicationTab($this);
                $igsnArticleView = new IgsnArticleDetails($this);
                Hook::add('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
//                Hook::add('Template::Workflow::Publication', [$igsnWorkflowTab, 'execute']);
                Hook::add('Templates::Article::Main', [$igsnArticleView, 'execute']);
                Hook::add('TemplateManager::display', $this->registerJS(...));


                $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
                // Hook::add('LoadComponentHandler', [$igsnSubmissionWizard, 'setupGridHandler']);
                Hook::add('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
                Hook::add('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
                Hook::add('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);

                // PIDINST
                // $pidinstSchema = new PidinstSchema();
                // Hook::add('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);
            }

            return true;
        }

        return false;
    }

    /**
     * Register the TinyMCE JavaScript file
     *
     * Hooked to the the `display` callback in TemplateManager
     */
    public function registerJS(string $hookName, array $args): bool
    {
        $request = &Registry::get('request');
        /** @var TemplateManager $templateManager */
        $templateManager = &$args[0];

        $templateManager->addJavaScript(
            'pidManager',
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build.js",
            [
                'contexts' => 'backend',
            ]
        );

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

    /** @copydoc Plugin::getInstallMigration() */
    function getInstallMigration(): IgsnSchemaMigration
    {
        return new IgsnSchemaMigration();
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
