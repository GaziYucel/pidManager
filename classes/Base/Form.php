<?php

/**
 * @file classes/Base/Form.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Form
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Form
 */

namespace APP\plugins\generic\pidManager\classes\Base;

use DAORegistry;
use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;
use PublicationDAO;

abstract class Form extends FormComponent
{
    public string $fieldName = '';

    public function __construct(string $id, string $method, string $action, array $locales)
    {
        parent::__construct($id, $method, $action, $locales);

        $publicationDao = DAORegistry::getDAO('PublicationDAO');
        /** @var PublicationDAO $publicationDao */
        $publication = $publicationDao->getById(array_reverse(explode('/', $action))[0]);

        $this->addField(new FieldText(
            $this->fieldName, [
            'label' => '',
            'description' => '',
            'isMultilingual' => false,
            'value' => $publication->getData($this->fieldName)
        ]));
    }
}
