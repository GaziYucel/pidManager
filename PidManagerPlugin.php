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
use APP\plugins\generic\pidManager\classes\Igsn\DataModel as IgsnDataModel;
use APP\plugins\generic\pidManager\classes\Igsn\Schema as IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\SubmissionWizard as IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\Workflow as IgsnWorkflow;
use APP\plugins\generic\pidManager\classes\Pidinst\ArticleDetails as PidinstArticleDetails;
use APP\plugins\generic\pidManager\classes\Pidinst\DataModel as PidinstDataModel;
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

                /** IGSN */
                if ($this->getSetting($contextId, Constants::settingEnableIgsn)) {
                    $igsnSchema = new IgsnSchema(Constants::igsn);
                    $igsnWorkflow = new IgsnWorkflow($this, Constants::igsn, new IgsnDataModel());
                    $igsnArticleDetails = new IgsnArticleDetails($this, Constants::igsn, new IgsnDataModel());
                    $igsnSubmissionWizard = new IgsnSubmissionWizard($this, Constants::igsn);

                    HookRegistry::register('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
                    HookRegistry::register('Template::Workflow::Publication', [$igsnWorkflow, 'execute']);
                    HookRegistry::register('Templates::Article::Main', [$igsnArticleDetails, 'execute']);
                    HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', [$igsnSubmissionWizard, 'execute']);
                }

                /** PIDINST */
                if ($this->getSetting($contextId, Constants::settingEnablePidinst)) {
                    $pidinstSchema = new PidinstSchema(Constants::pidinst);
                    $pidinstWorkflow = new PidinstWorkflow($this, Constants::pidinst, new PidinstDataModel());
                    $pidinstArticleDetails = new PidinstArticleDetails($this, Constants::pidinst, new PidinstDataModel());
                    $pidinstSubmissionWizard = new PidinstSubmissionWizard($this, Constants::pidinst);

                    HookRegistry::register('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);
                    HookRegistry::register('Template::Workflow::Publication', [$pidinstWorkflow, 'execute']);
                    HookRegistry::register('Templates::Article::Main', [$pidinstArticleDetails, 'execute']);
                    HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', [$pidinstSubmissionWizard, 'execute']);
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
}

class_alias('\PidManagerPlugin', '\APP\plugins\generic\pidManager\PidManagerPlugin');
