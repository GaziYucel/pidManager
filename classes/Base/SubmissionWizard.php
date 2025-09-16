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

use APP\core\Application;
use APP\pages\submission\SubmissionHandler;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;

abstract class SubmissionWizard
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';

    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * Add section to the details step of the submission wizard.
     */
    function addToSubmissionWizardSteps(string $hookName, array $params): bool
    {
        $request = Application::get()->getRequest();

        if ($request->getRequestedPage() !== 'submission') return false;
        if ($request->getRequestedOp() === 'saved') return false;

        $submission = $request
            ->getRouter()
            ->getHandler()
            ->getAuthorizedContextObject(Application::ASSOC_TYPE_SUBMISSION);

        if (!$submission || !$submission->getData('submissionProgress')) return false;

        /** @var TemplateManager $templateMgr */
        $templateMgr = $params[0];

        $steps = $templateMgr->getState('steps');
        $steps = array_map(function ($step) {
            if ($step['id'] === 'details') {
                $step['sections'][] = [
                    'id' => $this->fieldName,
                    'name' => __('plugins.generic.pidManager.' . $this->fieldName . '.label'),
                    'description' => '',
                    'type' => SubmissionHandler::SECTION_TYPE_TEMPLATE,
                ];
            }
            return $step;
        }, $steps);

        $templateMgr->setState([
            $this->fieldName => [],
            'steps' => $steps,
        ]);

        return false;
    }

    /**
     * Insert template to display grid in submission wizard.
     */
    public function addToSubmissionWizardTemplate(string $hookName, array $params): bool
    {
        $smarty = $params[1];
        $output = &$params[2];

        $output .= sprintf(
            '<template v-else-if="section.id === \'' . $this->fieldName . '\'">%s</template>',
            $smarty->fetch(
                $this->plugin->getTemplateResource(
                    $this->fieldName . '/submissionWizard.tpl'))
        );

        return false;
    }

    /**
     * Insert template to review in the submission wizard before completing the submission.
     */
    function addToSubmissionWizardReviewTemplate(string $hookName, array $params): bool
    {
        /** @var string $step */
        /** @var TemplateManager $templateMgr */
        $step = $params[0]['step'];
        $templateMgr = $params[1];
        $output =& $params[2];

        if ($step === 'details') {
            $output .= $templateMgr->fetch(
                $this->plugin->getTemplateResource(
                    $this->fieldName . '/submissionWizardReview.tpl'));
        }

        return false;
    }
}
