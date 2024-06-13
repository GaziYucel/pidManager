<?php
/**
 * @file classes/Igsn/IgsnSubmissionWizard.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnSubmissionWizard
 * @brief Igsn submission wizard
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\core\Application;
use APP\pages\submission\SubmissionHandler;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\submission\Submission;
use APP\template\TemplateManager;

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
     * Add section to the details step of the submission wizard
     *
     * @param $hookName
     * @param $params
     * @return bool
     */
    function addToSubmissionWizardSteps($hookName, $params): bool
    {
        $request = Application::get()->getRequest();

        if ($request->getRequestedPage() !== 'submission') return false;
        if ($request->getRequestedOp() === 'saved') return false;

        $submission = $request
            ->getRouter()
            ->getHandler()
            ->getAuthorizedContextObject(Application::ASSOC_TYPE_SUBMISSION);

        if (!$submission || !$submission->getData('submissionProgress')) return false;

        $igsn = [];

        /** @var TemplateManager $templateMgr */
        $templateMgr = $params[0];

        $steps = $templateMgr->getState('steps');
        $steps = array_map(function ($step) {
            if ($step['id'] === 'details') {
                $step['sections'][] = [
                    'id' => 'igsn',
                    'name' => __('plugins.generic.pidManager.igsn.label'),
                    'description' => __('plugins.generic.pidManager.igsn.workflow.description'),
                    'type' => SubmissionHandler::SECTION_TYPE_TEMPLATE,
                ];
            }
            return $step;
        }, $steps);

        $templateMgr->setState([
            'igsn' => $igsn,
            'steps' => $steps,
        ]);

        return false;
    }

    /**
     * Insert template to display grid in submission wizard
     *
     * @param string $hookName
     * @param array $args
     * @return bool
     */
    public function addToSubmissionWizardTemplate(string $hookName, array $args): bool
    {
        $smarty = $args[1];
        $output = &$args[2];

        $output .= sprintf(
            '<template v-else-if="section.id === \'igsn\'">%s</template>',
            $smarty->fetch($this->plugin->getTemplateResource('igsnTest.tpl'))
        );

        return false;
    }

    /**
     * Insert template to review in the submission wizard before completing the submission
     *
     * @param $hookName
     * @param $params
     * @return bool
     */
    function addToSubmissionWizardReviewTemplate($hookName, $params): bool
    {
        $submission = $params[0]['submission'];
        /** @var Submission $submission */
        $step = $params[0]['step'];
        /** @var string $step */
        $templateMgr = $params[1];
        /** @var TemplateManager $templateMgr */
        $output =& $params[2];

        if ($step === 'igsn') {
            $output .= $templateMgr->fetch($this->plugin->getTemplateResource('igsnReview.tpl'));
        }

        return false;
    }
}