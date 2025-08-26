<?php

/**
 * @file classes/Repo.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Repo
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Repo
 */

namespace APP\plugins\generic\pidManager\classes;

use Publication;
use ReflectionClass;
use ReflectionProperty;

class Repo
{
    public string $fieldName = '';
    public object $dataModel;

    public function __construct(string $fieldName, object $dataModel)
    {
        $this->fieldName = $fieldName;
        $this->dataModel = $dataModel;
    }

    /**
     * This method retrieves the pids for a publication and returns an array of pid DataModels.
     * If no pids are found, the method returns an empty array.
     */
    public function getPidsByPublication(Publication|null $publication): array
    {
        if (empty($publication)) return [];

        $itemsIn = json_decode($publication->getData($this->fieldName), true);

        if (empty($itemsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

        $itemsOut = [];

        foreach ($itemsIn as $item) {
            if (!empty($item) && (is_object($item) || is_array($item))) {
                $dataModel = new $this->dataModel();
                $reflect = new ReflectionClass(new $this->dataModel());
                $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
                foreach ($properties as $property) {
                    if (!empty($item[$property->getName()])) {
                        $dataModel->{$property->getName()} = $item[$property->getName()];
                    }
                }
                $itemsOut[] = $dataModel;
            }
        }

        return $itemsOut;
    }
}
