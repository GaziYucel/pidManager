<?php

/**
 * @file classes/Components/Forms/PidinstForm.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PidinstForm
 * @brief A preset form for setting a publication's pidinsts
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Constants;
use DAORegistry;
use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;
use PublicationDAO;

class PidinstForm extends FormComponent
{
    /** @copydoc FormComponent::__construct */
    public function __construct(string $id, string $method, string $action, array $locales)
    {
        parent::__construct($id, $method, $action, $locales);

        $publicationDao = DAORegistry::getDAO('PublicationDAO');
        /** @var PublicationDAO $publicationDao */
        $publication = $publicationDao->getById(array_reverse(explode('/', $action))[0]);

        $this->addField(new FieldText(
            Constants::pidinst, [
            'label' => '',
            'description' => '',
            'isMultilingual' => false,
            'value' => $publication->getData(Constants::pidinst)
        ]));
    }
}
