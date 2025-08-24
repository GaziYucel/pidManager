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

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

require_once(__DIR__ . '/vendor/autoload.php');

import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.site.VersionCheck');
import('lib.pkp.classes.handler.APIHandler');
import('lib.pkp.classes.linkAction.request.AjaxAction');

use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnPublicationTab;

class PidManagerPlugin extends GenericPlugin
{
  /** @copydoc Plugin::register */
  public function register($category, $path, $mainContextId = null): bool
  {
    if (parent::register($category, $path, $mainContextId)) {
      if ($this->getEnabled()) {

        // IGSN
        $igsnSchema = new IgsnSchema();
        $igsnWorkflowTab = new IgsnPublicationTab($this);
        $igsnArticleDetails = new IgsnArticleDetails($this);
        HookRegistry::register('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
        HookRegistry::register('Template::Workflow::Publication', [$igsnWorkflowTab, 'execute']);
        HookRegistry::register('Templates::Article::Main', [$igsnArticleDetails, 'execute']);

        $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
        HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', [$igsnSubmissionWizard, 'execute']);
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
