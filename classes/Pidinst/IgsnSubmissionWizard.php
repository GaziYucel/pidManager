<?php

/**
 * @file classes/Pidinst/PidinstSubmissionWizard.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PidinstSubmissionWizard
 * @brief Pidinst submission wizard
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use PidManagerPlugin;

class PidinstSubmissionWizard
{
    /**@var PidManagerPlugin */
    public PidManagerPlugin $plugin;

    /** @param PidManagerPlugin $plugin */
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    public function execute(string $hookName, array $args): void
    {
        $templateMgr = &$args[1];

        $request = $this->plugin->getRequest();

        $templateParameters = [
            'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets'
        ];
        $templateMgr->assign($templateParameters);

        $templateMgr->display(
            $this->plugin->getTemplateResource("pidinst/pidinstSubmissionWizard.tpl")
        );
    }
}
