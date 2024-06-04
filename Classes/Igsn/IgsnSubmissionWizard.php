<?php
/**
 * @file Classes/Igsn/IgsnSubmissionWizard.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnSubmissionWizard
 * @brief Igsn submission wizard
 */

namespace APP\plugins\generic\pidManager\Classes\Igsn;

use APP\plugins\generic\pidManager\PidManagerPlugin;

class IgsnSubmissionWizard
{
    /**@var PidManagerPlugin */
    public PidManagerPlugin $plugin;

    /** @param PidManagerPlugin $plugin */
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * Igsn metadata on submission wizard page
     *
     * @param string $hookName
     * @param array $args
     * @return void
     */
    public function execute(string $hookName, array $args): void
    {
        $templateMgr = &$args[1];

        $igsnDao = new IgsnDao();
        $request = $this->plugin->getRequest();
        $submission = $templateMgr->getTemplateVars('submission');
        $publication = $submission->getLatestPublication();

        $templateParameters = [
            'location' => 'SubmissionWizard',
            'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets',
            'igsnS' => json_encode($igsnDao->getIgsns($publication))
        ];
        $templateMgr->assign($templateParameters);

        $templateMgr->display($this->plugin->getTemplateResource("igsnBackend.tpl"));
    }
}