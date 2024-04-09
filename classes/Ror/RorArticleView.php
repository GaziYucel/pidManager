<?php
/**
 * @file classes/Ror/RorArticleView.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class RorArticleView
 * @brief Ror Article View
 */

namespace APP\plugins\generic\pidManager\classes\Ror;

use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;
use Exception;
use PKP\core\Core;

class RorArticleView
{
    /** @var PidManagerPlugin */
    private PidManagerPlugin $plugin;

    /** @param PidManagerPlugin $plugin */
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * Show Ror ID on front page of article.
     *
     * @param string $hookName
     * @param array $args
     * @return bool
     * @throws Exception
     */
    function submissionView(string $hookName, array $args): bool
    {
        $request = $args[0];
        $templateMgr = TemplateManager::getManager($request);

        $icon = '';
        $iconPath = Core::getBaseDir() . '/' . $this->plugin->getPluginPath() . '/' . RorConstants::$iconPath;

        if (file_exists($iconPath)) $icon = file_get_contents($iconPath);

        $templateMgr->assign([RorConstants::$iconNameInTemplate => $icon]);

        return false;
    }
}
