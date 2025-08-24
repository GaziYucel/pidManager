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

require_once(PidManagerPlugin::autoloadFile());

import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.site.VersionCheck');
import('lib.pkp.classes.handler.APIHandler');
import('lib.pkp.classes.linkAction.request.AjaxAction');

use APP\plugins\generic\pidManager\classes\Settings\Actions;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchemaMigration;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnPublicationTab;
use APP\plugins\generic\pidManager\classes\Settings\Manage;

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
        $igsnArticleView = new IgsnArticleDetails($this);
        HookRegistry::register('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
        HookRegistry::register('Template::Workflow::Publication', [$igsnWorkflowTab, 'execute']);
        HookRegistry::register('Templates::Article::Main', [$igsnArticleView, 'execute']);

        $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
        // Hook::add('LoadComponentHandler', [$igsnSubmissionWizard, 'setupGridHandler']);
        HookRegistry::register('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
        HookRegistry::register('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
        HookRegistry::register('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);

        // PIDINST
        // $pidinstSchema = new PidinstSchema();
        // HookRegistry::register('Schema::get::publication', [$pidinstSchema, 'addToSchemaPublication']);
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

  /** @copydoc Plugin::getInstallMigration() */
  function getInstallMigration(): IgsnSchemaMigration
  {
    return new IgsnSchemaMigration();
  }

  /**
   * Return composer autoload file path
   *
   * @return string
   */
  public static function autoloadFile(): string
  {
    return __DIR__ . '/vendor/autoload.php';
  }
}
