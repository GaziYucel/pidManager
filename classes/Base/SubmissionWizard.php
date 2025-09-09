<?php

/**
 * @file classes/Base/SubmissionWizard.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionWizard
 * @ingroup plugins_generic_pidmanager
 *
 * @brief SubmissionWizard
 */

namespace APP\plugins\generic\pidManager\classes\Base;

use PidManagerPlugin;

abstract class SubmissionWizard
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';

    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    public function execute(string $hookName, array $args): void
    {
        $templateMgr = &$args[1];

        $request = $this->plugin->getRequest();

        $templateParameters = [
            'pidName' => $this->fieldName,
            'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets'
        ];
        $templateMgr->assign($templateParameters);

        $templateMgr->display($this->plugin->getTemplateResource(
            $this->fieldName . '/submissionWizard.tpl'
        ));
    }
}
