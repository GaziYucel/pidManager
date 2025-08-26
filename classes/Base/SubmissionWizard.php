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
use APP\submission\Submission;
use APP\template\TemplateManager;

class SubmissionWizard
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';

    public function __construct(PidManagerPlugin &$plugin, string $fieldName)
    {
        $this->plugin = &$plugin;
        $this->fieldName = $fieldName;
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
                    'id' => 'igsn',
                    'name' => __('plugins.generic.pidManager.igsn.label'),
                    'description' => '',
                    'type' => SubmissionHandler::SECTION_TYPE_TEMPLATE,
                ];
            }
            return $step;
        }, $steps);

        $templateMgr->setState([
            'igsn' => [],
            'steps' => $steps,
        ]);

        return false;
    }

    /**
     * Insert template to display grid in submission wizard.
     */
    public function addToSubmissionWizardTemplate(string $hookName, array $args): bool
    {
        $smarty = $args[1];
        $output = &$args[2];

        $output .= sprintf(
            '<template v-else-if="section.id === \'igsn\'">%s</template>',
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
        /** @var Submission $submission */
        /** @var string $step */
        /** @var TemplateManager $templateMgr */
        $submission = $params[0]['submission'];
        $step = $params[0]['step'];
        $templateMgr = $params[1];
        $output =& $params[2];

        $templateParameters = [
            'pidName' => $this->fieldName
        ];
        $templateMgr->assign($templateParameters);

        if ($step === 'details') {
            $output .= $templateMgr->fetch(
                $this->plugin->getTemplateResource(
                    $this->fieldName . '/SubmissionWizardReview.tpl'));
        }

        return false;
    }
}
