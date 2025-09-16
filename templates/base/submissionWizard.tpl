{**
 * templates/base/submissionWizard.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Submission Wizard
 *}

<div>
    <label class="label">
        {translate key="plugins.generic.pidManager.{$pidName}.label"}
    </label>
    <p class="description align-justify">
        <i>
            {translate key="plugins.generic.pidManager.{$pidName}.generalDescription"}
            {translate key="plugins.generic.pidManager.{$pidName}.submission.instructions"}
        </i>
    </p>
</div>
