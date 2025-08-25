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
use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnWorkflow;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class PidManagerPlugin extends GenericPlugin
{
    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {
            if ($this->getEnabled()) {

                // IGSN
                $igsnSchema = new IgsnSchema();
                $igsnWorkflow = new IgsnWorkflow($this);
                $igsnArticleView = new IgsnArticleDetails($this);
                Hook::add('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
                Hook::add('Template::Workflow::Publication', [$igsnWorkflow, 'execute']);
                Hook::add('Templates::Article::Main', [$igsnArticleView, 'execute']);

                $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
                Hook::add('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
                Hook::add('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
                Hook::add('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);
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
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
