<?php

/**
 * @file classes/Components/Forms/IgsnForm.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnForm
 * @brief A preset form for setting a publication's igsns
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\facades\Repo;
use APP\plugins\generic\pidManager\classes\Constants;
use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;

class IgsnForm extends FormComponent
{
    /** @copydoc FormComponent::__construct */
    public function __construct(string $id, string $method, string $action, array $locales)
    {
        parent::__construct($id, $method, $action, $locales);

        $publication = Repo::publication()->get(array_reverse(explode('/', $action))[0]);

        $this->addField(new FieldText(
            Constants::igsn, [
            'label' => '',
            'description' => '',
            'isMultilingual' => false,
            'value' => $publication->getData(Constants::igsn)
        ]));
    }
}
