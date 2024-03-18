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

require_once(__DIR__ . '/vendor/autoload.php');

use APP\plugins\generic\pidManager\classes\Db\PluginSchema;
use Config;
use PKP\core\JSONMessage;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

class PidManagerPlugin extends GenericPlugin
{
    /** @var string Key for the journal metadata saved in journal */
    public const PID_MANAGER_PIDs_JOURNAL = ['openalex_id', 'wikidata_id'];
    /** @var string Key for the publication metadata saved in publication */
    public const PID_MANAGER_PIDs_PUBLICATION = ['openalex_id', 'wikidata_id'];

    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {

            if ($this->getEnabled()) {
                $pluginSchema = new PluginSchema();
                Hook::add('Schema::get::context', function ($hookName, $args) use ($pluginSchema) {
                    $pluginSchema->addToSchemaContext($hookName, $args);
                });
                Hook::add('Schema::get::publication', function ($hookName, $args) use ($pluginSchema) {
                    $pluginSchema->addToSchemaPublication($hookName, $args);
                });
            }

            return true;
        }

        return false;
    }

    /** @copydoc Plugin::getActions() */
    public function getActions($request, $actionArgs): array
    {
        if (!$this->getEnabled()) return parent::getActions($request, $actionArgs);

        return parent::getActions($request, $actionArgs);
    }

    /** @copydoc Plugin::manage() */
    public function manage($args, $request): JSONMessage
    {
        return new JSONMessage(false);
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
     * @return bool
     */
    public static function isDebugMode(): bool
    {
        $config_value = Config::getVar(CITATION_MANAGER_PLUGIN_NAME, 'isDebugMode');

        if (!empty($config_value)
            && (strtolower($config_value) === 'true' || (string)$config_value === '1')
        ) {
            return true;
        }

        return false;
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pidManager\PidManagerPlugin', '\PidManagerPlugin');
}
