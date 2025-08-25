<?php

/**
 * @file classes/Pidinst/PidinstRepo.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PidinstRepo
 * @brief Pidinst Repo
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Constants;
use Publication;
use ReflectionClass;
use ReflectionProperty;

class PidinstRepo
{
    /**
     * This method retrieves the pidinsts for a publication from the publication object.
     * After this, the method returns an array of PidinstDataModels.
     * If no pidinsts are found, the method returns an empty array.
     *
     * @param Publication|null $publication
     * @return array
     */
    public function getPidinsts(Publication|null $publication): array
    {
        if (empty($publication)) return [];

        $pidinstsIn = json_decode($publication->getData(Constants::pidinst), true);

        if (empty($pidinstsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

        $pidinstsOut = [];

        foreach ($pidinstsIn as $pidinst) {
            if (!empty($pidinst) && (is_object($pidinst) || is_array($pidinst))) {
                $pidinstDataModel = new PidinstDataModel();
                $reflect = new ReflectionClass(new PidinstDataModel());
                $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
                foreach ($properties as $property) {
                    if (!empty($pidinst[$property->getName()])) {
                        $pidinstDataModel->{$property->getName()} = $pidinst[$property->getName()];
                    }
                }
                $pidinstsOut[] = $pidinstDataModel;
            }
        }

        return $pidinstsOut;
    }
}
