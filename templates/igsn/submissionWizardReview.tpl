{**
 * templates/igsn/igsnSubmissionWizardReview.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Submission Wizard Review
 *}

<div class="submissionWizard__reviewPanel">
    <div class="submissionWizard__reviewPanel__header h-34]">
        <h3>{translate key="plugins.generic.pidManager.{$pidName}.label"}</h3>
    </div>
    <div class="submissionWizard__reviewPanel__body">
        <div class="submissionWizard__reviewPanel__item">
            <p class="description" style="text-align: justify;">
                <i>{translate key="plugins.generic.pidManager.{$pidName}.generalDescription"}
                    {translate key="plugins.generic.pidManager.{$pidName}.submission.instructions"}</i>
            </p>
        </div>
    </div>
</div>
