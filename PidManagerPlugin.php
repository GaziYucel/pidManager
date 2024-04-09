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

use APP\plugins\generic\pidManager\classes\Ror\RorArticleView;
use APP\plugins\generic\pidManager\classes\Ror\RorForm;
use APP\plugins\generic\pidManager\classes\Ror\RorSchema;
use APP\plugins\generic\pidManager\classes\Ror\RorWorkflow;
use Config;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

define('PID_MANAGER_PLUGIN_NAME', basename(__FILE__, '.php'));

class PidManagerPlugin extends GenericPlugin
{
    /** @copydoc Plugin::register */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (parent::register($category, $path, $mainContextId)) {

            if ($this->getEnabled()) {
                /* ROR */
                $rorSchema = new RorSchema();
                $rorForm = new RorForm();
                $rorWorkflow = new RorWorkflow($this);
                $rorArticleView = new RorArticleView($this);
                Hook::add('Schema::get::author', function ($hookName, $args) use ($rorSchema) {
                    $rorSchema->addToSchemaAuthor($hookName, $args);
                });
                Hook::add('Form::config::before', function ($hookName, $args) use ($rorForm) {
                    $rorForm->addFormFields($hookName, $args);
                });
                Hook::add('Template::Workflow::Publication', function ($hookName, $args) use ($rorWorkflow) {
                    $rorWorkflow->execute($hookName, $args);
                });
                Hook::add('ArticleHandler::view', function ($hookName, $args) use ($rorArticleView) {
                    $rorArticleView->submissionView($hookName, $args);
                });
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
