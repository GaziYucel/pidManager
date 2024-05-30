<?php
/**
 * @file classes/Components/Forms/WorkflowForm.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class WorkflowForm
 * @brief A preset form for setting a publication's parsed citations
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\facades\Repo;
use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;

class IgsnWorkflowForm extends FormComponent
{
    /** @copydoc FormComponent::__construct */
    public function __construct(string $id, string $method, string $action, array $locales)
    {
        parent::__construct($id, $method, $action, $locales);

        $publication = Repo::publication()->get(
            array_reverse(explode('/', $action))[0]);

        $igsnDao = new IgsnDao();

        $this->addField(new FieldText(
            IgsnConstants::igsn, [
            'label' => '',
            'description' => '',
            'isMultilingual' => false,
            'value' => $publication->getData(IgsnConstants::igsn)
        ]));
    }
}
