<?php

/**
 * @file classes/Base/Schema.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Schema
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Schema
 */

namespace APP\plugins\generic\pidManager\classes\Base;

abstract class Schema
{
    public string $fieldName = '';

    public function addToSchemaPublication(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        $schema->properties->{$this->fieldName} = (object)[
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable']
        ];

        return false;
    }
}
