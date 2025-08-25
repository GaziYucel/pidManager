<?php

/**
 * @file classes/Pidinst/Repo.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Repo
 * @brief Pidinst Repo
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Constants;
use Publication;
use ReflectionClass;
use ReflectionProperty;

class Repo
{
    /**
     * This method retrieves the pids for a publication from the publication object.
     * After this, the method returns an array of pid DataModels.
     * If no pids are found, the method returns an empty array.
     *
     * @param Publication|null $publication
     * @return array[DataModel]
     */
    public function getByPublication(Publication|null $publication): array
    {
        if (empty($publication)) return [];

        $itemsIn = json_decode($publication->getData(Constants::pidinst), true);

        if (empty($itemsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

        $itemsOut = [];

        foreach ($itemsIn as $item) {
            if (!empty($item) && (is_object($item) || is_array($item))) {
                $dataModel = new DataModel();
                $reflect = new ReflectionClass(new DataModel());
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
