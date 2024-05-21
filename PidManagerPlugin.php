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

require_once(PidManagerPlugin::autoloadFile());

use APP\core\Application;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnArticleView;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchema;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnWorkflowTab;
use Config;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

class PidManagerPlugin extends GenericPlugin
{
    /** @var string Key for igsn saved in publications */
    public const IGSN = 'igsn';

    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {
            if (Application::isUnderMaintenance()) return true;

            if ($this->getEnabled()) {
                // IGSN
                $igsnSchema = new IgsnSchema();
                $igsnWorkflowTab = new IgsnWorkflowTab($this);
                $igsnArticleView = new IgsnArticleView($this);
                Hook::add('Schema::get::publication', [$igsnSchema, 'addToSchemaPublication']);
                Hook::add('Template::Workflow::Publication', [$igsnWorkflowTab, 'execute']);
                Hook::add('Templates::Article::Main', [$igsnArticleView, 'execute']);
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

    /**
     * Get isDebugMode from config, return false if setting not present
     *
     * @return bool
     */
    public static function isDebugMode(): bool
    {
        $config_value = \PKP\config\Config::getVar(CITATION_MANAGER_PLUGIN_NAME, 'isDebugMode');

        if (!empty($config_value)
            && (strtolower($config_value) === 'true' || (string)$config_value === '1')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get isTestMode from config, return false if setting not present
     *
     * @return bool
     */
    public static function isTestMode(): bool
    {
        $config_value = Config::getVar(CITATION_MANAGER_PLUGIN_NAME, 'isTestMode');

        if (!empty($config_value)
            && (strtolower($config_value) === 'true' || (string)$config_value === '1')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return composer autoload file path
     *
     * @return string
     */
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
