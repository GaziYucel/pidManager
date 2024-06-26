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

require_once(PidManagerPlugin::autoloadFile());

use APP\core\Application;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleDetails;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchemaMigration;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnPublicationTab;
use Exception;
use PKP\config\Config;
use PKP\install\Installer;
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
                Hook::add('Template::Workflow::Publication', [$igsnWorkflowTab, 'execute']);
                Hook::add('Templates::Article::Main', [$igsnArticleView, 'execute']);

                $igsnSubmissionWizard = new IgsnSubmissionWizard($this);
//                Hook::add('LoadComponentHandler', [$igsnSubmissionWizard, 'setupGridHandler']);
                Hook::add('TemplateManager::display', [$igsnSubmissionWizard, 'addToSubmissionWizardSteps']);
                Hook::add('Template::SubmissionWizard::Section', [$igsnSubmissionWizard, 'addToSubmissionWizardTemplate']);
                Hook::add('Template::SubmissionWizard::Section::Review', [$igsnSubmissionWizard, 'addToSubmissionWizardReviewTemplate']);

                Hook::add('Installer::postInstall', [$this, 'updateSchema']);
//                Hook::add('Installer::updateVersion', [$this, 'updateSchema']);
//                $migration = new IgsnSchemaMigration();
//                $migration->up();
            }

            return true;
        }

        return false;
    }

    /**
     * @copydoc Plugin::updateSchema()
     */
    public function updateSchema($hookName, $args): void
    {
        error_log('updateSchema');

        $installer = $args[0];
        $result = &$args[1];

        $migration = new IgsnSchemaMigration();
        try {
            $migration->up();
        } catch (Exception $e) {
            $installer->setError(Installer::INSTALLER_ERROR_DB, __('installer.installMigrationError', ['class' => get_class($migration), 'message' => $e->getMessage()]));
            $result = false;
        }
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

    /** @copydoc Plugin::getInstallMigration() */
//    function getInstallMigration()
//    {
//        error_log('getInstallMigration');
//        $this->updateSchema();
//        return new IgsnSchemaMigration();
//    }

    /** @return bool Get isDebugMode from config, return false if setting not present */
    public static function isDebugMode(): bool
    {
        $config_value = Config::getVar(PID_MANAGER_PLUGIN_NAME, 'isDebugMode');

        if (!empty($config_value)
            && (strtolower($config_value) === 'true' || (string)$config_value === '1')
        ) {
            return true;
        }

        return false;
    }

    /** @return bool Get isTestMode from config, return false if setting not present */
    public static function isTestMode(): bool
    {
        $config_value = Config::getVar(PID_MANAGER_PLUGIN_NAME, 'isTestMode');

        if (!empty($config_value)
            && (strtolower($config_value) === 'true' || (string)$config_value === '1')
        ) {
            return true;
        }

        return false;
    }

    /** @return string Return composer autoload file path */
    public static function autoloadFile(): string
    {
        if (self::isTestMode()) return __DIR__ . '/tests/vendor/autoload.php';
        return __DIR__ . '/vendor/autoload.php';
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
