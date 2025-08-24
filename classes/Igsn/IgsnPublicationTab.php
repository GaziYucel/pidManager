<?php
/**
 * @file classes/Workflow/WorkflowTab.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class WorkflowTab
 * @brief Workflow Publication Tab
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\publication\Publication;
use APP\template\TemplateManager;
use Exception;
use PKP\core\PKPApplication;


class IgsnPublicationTab
{
    /** @var PidManagerPlugin */
    public PidManagerPlugin $plugin;

    /** @param PidManagerPlugin $plugin */
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * Show tab under Publications
     *
     * @param string $hookName
     * @param array $args [string, TemplateManager]
     * @return void
     * @throws Exception
     */
    public function execute(string $hookName, array $args): void
    {
        /* @var Publication $publication */
        /* @var TemplateManager $templateMgr */
        $templateMgr = &$args[1];

        $igsnDao = new IgsnRepo();
        $request = $this->plugin->getRequest();
        $context = $request->getContext();
        $submission = $templateMgr->getTemplateVars('submission');
        $submissionId = $submission->getId();
        $publication = $submission->getLatestPublication();
        $publicationId = $publication->getId();
        $locale = $publication->getData('locale');

        $apiBaseUrl = $request->getDispatcher()->url(
            $request,
            PKPApplication::ROUTE_API,
            $context->getData('urlPath'),
            '');

        $locales = $context->getSupportedLocaleNames();
        $locales = array_map(
            fn(string $locale, string $name) => ['key' => $locale, 'label' => $name],
            array_keys($locales), $locales);

        $form = new IgsnForm(
            IgsnConstants::igsn,
            'PUT',
            $apiBaseUrl . 'submissions/' . $submissionId . '/publications/' . $publicationId,
            $locales);

        $state = $templateMgr->getTemplateVars('state');
        $state['components'][IgsnConstants::igsn] = $form->getConfig();
        $templateMgr->assign('state', $state);

        $templateParameters = [
            'location' => 'PublicationTab',
            'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets',
            'apiBaseUrl' => $apiBaseUrl,
            'igsns' => json_encode($igsnDao->getIgsns($publication))
        ];
        $templateMgr->assign($templateParameters);

        $templateMgr->display($this->plugin->getTemplateResource("igsnBackend.tpl"));
    }
}
