<?php
/**
 * @file classes/Ror/RorForm.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class RorForm
 * @brief Ror Form
 */

namespace APP\plugins\generic\pidManager\classes\Ror;

use PKP\components\forms\FieldText;
use PKP\components\forms\publication\ContributorForm;
use PKP\plugins\Hook;

class RorForm
{
    /**
     * Add extra form fields to Contributor form.
     *
     * @param string $hookName
     * @param object $form
     * @return bool
     */
    public function addFields(string $hookName, object $form): bool
    {
        if (!$form instanceof ContributorForm)
            return Hook::CONTINUE;

        $form->removeField(RorConstants::$idName);
        $form->addField(new FieldText(RorConstants::$idName, [
            'label' => __('plugins.generic.pidManager.ror.rorId'),
            'tooltip' => __('plugins.generic.pidManager.ror.description'),
            'groupId' => 'affiliation',
        ]), [FIELD_POSITION_AFTER, 'affiliation']);

        return Hook::CONTINUE;
    }
}
