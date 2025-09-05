<?php

/**
 * @file classes/Base/WorkflowTab.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Workflow
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Workflow
 */

namespace APP\plugins\generic\pidManager\classes\Base;

use APP\core\Application;
use APP\plugins\generic\pidManager\classes\Repo;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;

class Workflow
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';
    public object $dataModel;

    public function __construct(PidManagerPlugin &$plugin, string $fieldName, object $dataModel)
    {
        $this->plugin = &$plugin;
        $this->fieldName = $fieldName;
        $this->dataModel = $dataModel;
    }

    public function execute(string $hookName, array $args): void
    {
        /* @var TemplateManager $templateMgr */
        $templateMgr = &$args[1];

        $repo = new Repo($this->fieldName, $this->dataModel);
        $request = $this->plugin->getRequest();
        $context = $request->getContext();
        $submission = $templateMgr->getTemplateVars('submission');
        $submissionId = $submission->getId();
        $publication = $submission->getLatestPublication();
        $publicationId = $publication->getId();

        $apiBaseUrl = $request->getDispatcher()->url(
            $request,
            Application::ROUTE_API,
            $context->getData('urlPath'),
            '');

        $locales = $context->getSupportedLocaleNames();
        $locales = array_map(
            fn(string $locale, string $name) => ['key' => $publication->getData('locale'), 'label' => $name],
            array_keys($locales), $locales);

        $form = new Form(
            $this->fieldName,
            'PUT',
            $apiBaseUrl . 'submissions/' . $submissionId . '/publications/' . $publicationId,
            $locales,
            $this->fieldName
        );

        $state = $templateMgr->getTemplateVars('state');
        $state['components'][$this->fieldName] = $form->getConfig();
        $templateMgr->assign('state', $state);

        $templateParameters = [
            'pidName' => $this->fieldName,
            'dataModel' => json_encode(get_class_vars(get_class(new DataModel()))),
            'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets',
            'apiBaseUrl' => $apiBaseUrl,
            'items' => json_encode($repo->getPidsByPublication($publication))
        ];
        $templateMgr->assign($templateParameters);

        $templateMgr->display($this->plugin->getTemplateResource(
            $this->fieldName . '/workflow.tpl'));
    }
}
